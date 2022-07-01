<?php
class item
{
	private static $id = NULL;
	public static $idetails = NULL;
	private static $isd = NULL;
	public static $isdetails = NULL;
	
	
	function __construct()
	{
		//echo "Creation of Object Of Class ".__CLASS__;
	}
	function add_status($name)  /* ------   Function to ADD ITEM SOURCE - STARTS HERE   ------- */
	{
		global $dbc;
		if(!empty($name))
		{
			$q = "INSERT INTO ref_item_status VALUES(NULL,'$name')";
			$r = mysqli_query($dbc,$q);
			self::$isd = mysqli_insert_id($dbc);
			self::$isdetails = array(self::$isd,$name);
			return $r;
		}
		else
		return NULL;
	}
	
	function get_status($id = '') /* ------   Function to SEARCH ITEM SOURCE for a particular id  STARTS HERE   ------- */
	{
		global $dbc;
		if(empty($id) || $id == '0' || is_null($id) || !is_numeric($id))
		{
			$id = self::$isd;
			if(is_null($id))
			return NULL;
		}

		if($id != self::$isd || is_null(self::$isdetails))
		{
			$q = "SELECT * FROM ref_item_status WHERE risId = '$id' LIMIT 1";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				$d = mysqli_fetch_assoc($r);
				self::$isdetails = $d;
				self::$isd = $id;
				
				return $d;
			}
			else
				return NULL;
		}
		
		return self::$sourcedetails;
	}
	
	function edit_status($id = '',$name)
	{
		global $dbc;
		if(trim($name) == '')
			return NULL;
			
		if(empty($id) || is_null($id) || $id == '' || $id == '0')
		{
			$id = self::$isd;
			if(is_null($id))
				return NULL;
		}
		
		$q = "UPDATE ref_item_status SET ris_name = '$name' WHERE risId = '$id'";
		$r = mysqli_query($dbc,$q);
		
		self::$isd = $id;
		self::$isdetails = $name;
		
		return $r;
	}
	
	function add_item($inarray = array())
	{
		global $dbc;
		if(is_array($inarray))
		{
			if(count($inarray) == 0)
				return NULL;
				
			$str1 = "INSERT INTO `items` (";
			$str2 = " VALUES (";
			
			foreach($inarray as $key => $value)
			{
				$str1 .= "`".$key."`,";
				$str2 .= "'".$value."',";
			}
			$str1 .= '`created`)';
			$str2 .= 'NOW())';
			
			$q = $str1.$str2;
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				self::$id = mysqli_insert_id($dbc);
				return self::$id;
			}
			return NULL;
		}
		else
			return NULL;
	}
	
	function show_item($id = '',$inarray = array())  /* ------   Function to SEARCH ITEM for a particular id  STARTS HERE   ------- */
	{
		global $dbc;
		global $outarray;
		if(empty($id) || $id == '0' || is_null($id) || !is_numeric($id))
		{
			$id = self::$id;
			if(is_null($id))
			return NULL;
		}

		if($id != self::$id || is_null(self::$idetails))
		{
			$q = "SELECT I.*,RIS.ris_name,ISV.*,V.*,C.*,DATE_FORMAT(I.created,'%d-%b-%Y AT %r') AS fcreated,DATE_FORMAT(I.modified,'%d-%b-%Y AT %r') AS fmodified FROM `items` AS I INNER JOIN `ref_item_status` AS RIS USING(risId) INNER JOIN item_source_vendor AS ISV USING(isvId) INNER JOIN variations AS V USING(varId) INNER JOIN catalogue AS C USING(clId) WHERE itemId = '$id' LIMIT 1";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				$d = mysqli_fetch_assoc($r);
				self::$idetails = $d;
				self::$id = $id;
				
				if(count($inarray)>0)
				{
					$i = 0;
					foreach($inarray as $key)
					{
						$outarray[$i++] = $d[$key];
					}
					
					if(count($inarray)== 1)
						return $outarray[0];
					
					return $outarray;
				}
				else
					return self::$idetails;
			}
			else
				return NULL;
		}
		
		if(count($inarray)>0)
		{
			$i = 0;
			foreach($inarray as $key)
			{
				$outarray[$i++] = self::$idetails[$key];
			}
			
			if(count($inarray)== 1)
				return $outarray[0];
			
			return $outarray;
		}

			return self::$idetails;
	}					 /* ------   Function to SEARCH ITEM for a particular id  ENDS HERE   ------- */
	
	function edit_item($id = '',$inarray = array())
	{
		global $dbc;
		
		if(empty($id) || $id == '0' || is_null($id) || !is_numeric($id))
		{
			$id = self::$id;
			if(is_null($id))
			return NULL;
		}
		
		if(count($inarray) == 0)
			return NULL;
			
		$q = "UPDATE `items` SET ";
		foreach($inarray as $key => $value)
		{
			$q .= "`{$key}` = '{$value}',";
		}
		$q .= "modified = NOW() WHERE itemId = '$id'";
		$r = mysqli_query($dbc,$q);
		
		self::$id = $id;
		self::$idetails = NULL;
		
		return $r;
	}
	
	function total_item($date1 = '',$date2 = '')
	{
		global $dbc,$condi;
		if($date1 != '')
		{
			$date1 = date('Y-m-d',strtotime($date1));
			if($date2 == '')
				$date2 = date('Y-m-d');
			else
				$date2 = date('Y-m-d',strtotime($date2));
			
			if($date1 > $date2)
			{
				$condi = $date1;
				$date1 = $date2;
				$date2 = $condi;
			}
			
			$condi = " WHERE `created` >= '{$date1}' AND `created` <= '{$date2}' ";
			
		}
		
		$q = "SELECT COUNT(*) AS c FROM `items`".$condi;
		$r = mysqli_query($dbc,$q);
		
		if($r)
		{
			$d = mysqli_fetch_assoc($r);
			return $d['c'];
		}
	}
}
?>