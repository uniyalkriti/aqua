<?php
class misc
{
	function __construct()
	{
		
	}
	function add($table,$inarray = array()) /* ------   Function to ADD VENDOR - STARTS HERE   ------- */
	{
		global $dbc;
		if(is_array($inarray))
		{
			if(count($inarray) == 0)
				return NULL;
				
			$str1 = "INSERT INTO `$table` (";
			$str2 = " VALUES (";
			
			foreach($inarray as $key => $value)
			{
				$str1 .= "`".$key."`,";
				$str2 .= "'".$value."',";
			}
			$str1 = rtrim($str1,',').")";
			$str2 = rtrim($str2,',').")";
			
			$q = $str1.$str2;
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				return mysqli_insert_id($dbc);
			}
			return NULL;
		}
		else
			return NULL;
	}
	function delete($table,$key,$id)
	{
		global $dbc;
		echo $q = "DELETE FROM `$table` WHERE `$key` IN ($id)";
		$r = mysqli_query($dbc,$q);
		return $r;
	}
}
?>