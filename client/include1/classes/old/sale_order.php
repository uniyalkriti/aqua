<?php
class saleorder
 {
	private static $sid = NULL;
	private static $sdetails = NULL;
	private static $rosid = NULL;
	
	function  __construct()
	{
		
	}
	
	function add_sale_order($inarray=array(),$inarray1=array())
	{
		
		global $dbc;
		$strk="INSERT INTO `sale_order`(";
		$strv="values(";
		if(is_array($inarray))
		 {
			foreach($inarray as $key=>$value)
			{
				$strk.="`".$key."`,";
				$strv.= "'".$value."',";
			}
			$strk .= '`created`)';
			$strv .= 'NOW())';
		    $q= $strk.$strv;
			$r = mysqli_query($dbc,$q);
			if($r)
			{
			   $d = mysqli_insert_id($dbc);
			   $this->sale_items($d,$inarray1);
				self::$sid = $d;
				return $d;
			}else
				return NULL;
		 }
	}
	
function sale_items($d,$inarr=array())
{
	global $dbc;
	$count=count($inarr);
	$no_key=0;
	$strk="";
	$strv="";
	
		foreach($inarr as $key1 => $val1)
		{
			
			if(is_array($inarr[$key1]))
			{
				$flag = 1;
				$strv .="(";
				foreach($inarr[$key1] as $k => $v)
				{
					if($no_key==0)
					{
						$strk.="`".$k."`,";
					}
					
					$strv.= "'".$v."',";
				}
				if($no_key == 0)
					$strk .= "`soId`,";
					
				$no_key++;
				$strv .="'$d'),";
			}
			else
			{
				$flag = 2;
				$strk .= "`".$key1."`,";	
				$strv .= "'".$val1."',";			
			}
		}
		$strk = rtrim($strk,',');
		$strv = rtrim($strv,',');
		if($flag == 2)
		{
			$strk .= ",`soId`";
			$strv = "(".$strv.",'$d')";
			
		}
	  echo $q="INSERT INTO `sale_order_items`($strk) values $strv";
	   $r = mysqli_query($dbc,$q);
		if($r)
		{
			$si=mysqli_insert_id($dbc);
			self::$sid = $d;
			return $d;
			self::$sdetails=NULL;
		}

}
	function show_sale_order($sid = '',$inarray=array())
	{
		
		global $dbc;
		if(empty($sid) || $sid == '0' || is_null($sid) || !is_numeric($sid))
		{
			
			$sid = self::$sid;
			if(is_null($sid))
			return NULL;
		}

		if($sid != self::$sid || is_null(self::$sdetails))
		{
		    $q = "SELECT * FROM sale_order WHERE soId = '$sid' LIMIT 1";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				$d = mysqli_fetch_assoc($r);
				self::$sdetails = $d;
				self::$sid = $sid;
				
				if(count($inarray)>0)
				{
					$outarray = array();
					foreach($inarray as $key)
					{
						$outarray[$key] = $d[$key];
					}
					return $outarray;
				}
				else
				return $d;
			}
			else
				return NULL;
		}
		
			return self::$sdetails;
	}
	
	
	
	 function sale_order_edit($sid = '',$inarray = array(),$item=array())
	{
		global $dbc;
		if(empty($sid) || $sid == '0' || is_null($sid) || !is_numeric($sid))
		{
			$sid = self::$sid;
			if(is_null($sid))
			return NULL;
		}
		
		if(count($inarray) == 0)
			return NULL;
			
		$q = "UPDATE `sale_order` SET ";
		foreach($inarray as $key => $value)
		{
			$q .= "`{$key}` = '{$value}',";
		}
	  	$q .= "modified = NOW() WHERE soId = '$sid'";
		$r = mysqli_query($dbc,$q);
		$this->sale_item_edit($sid,$item);
		self::$sid = $sid;
		self::$sdetails = NULL;
		
		return $r;
	}
	function sale_item_edit($sid,$item1=array())
	{
		global $dbc;
		echo $q="update sale_order_items set edit_status='1' where soId='$sid'";
		$r=mysqli_query($dbc,$q);
		$this->sale_items($sid,$item1);
		if($r)
		{
		 self::$sid = $sid;
		 self::$sdetails = NULL;
		 return $r;
		}
		
	}
	function  sale_order_delete($sid = '')
	{
		global $dbc;
		
		if(empty($sid) || $sid == '0' || is_null($sid) || !is_numeric($sid))
		{
			$sid = self::$sid;
			if(is_null($sid))
			return NULL;
		}
		
		$q = "DELETE FROM sale_order WHERE soId = '$sid'";
		$r = mysqli_query($dbc,$q);
		
		if($sid == self::$sid)
		{
			 self::$sid = NULL;
			 self::$sdetails = NULL;
		}
		
		return $r;
	}
	
	function show_sale_bwdate($rosid = '',$inarray=array(),$date1)
	{
		
		global $dbc;
		if(empty($rosid) || $sid == '0' || is_null($rosid) || !is_numeric($rosid))
		{
			
			$rosid = self::$rosid;
			if(is_null($rosid))
			return NULL;
		}

		if($rosid != self::$rosid || is_null(self::$rosdetails))
		{
		    $q = "SELECT * FROM sale_order WHERE created BETWEEN $date1 AND NOW() AND soId='$sid'";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				$d = mysqli_fetch_assoc($r);
				self::$sdetails = $d;
				self::$sid = $sid;
				
				if(count($inarray)>0)
				{
					$outarray = array();
					foreach($inarray as $key)
					{
						$outarray[$key] = $d[$key];
					}
					return $outarray;
				}
				else
				return $d;
			}
			else
				return NULL;
		}
		
			return self::$sdetails;
	}
	
	
	function pre($show)
	{
	echo '<pre>';
	print_r($show);
	echo '</pre>';	
	}
	
}
?>

<?php
include('../conectdb.php');
$arr1 = array('rpmId'=>"5",'rpsId'=>"6",'rosId'=>"8",'custId'=>"11",'order_source'=>"sale",'date_shipped'=>"2012-11-22",'total_amount_incl_tax'=>"2000",'total_amount_ex_tax'=>"23000",'discount_amount'=>"20",'tax_amount'=>"10",'shipping_charge'=>"4000",'wrapping_charge'=>"6000",'customername'=>"sohan",'shipped_address'=>"H-75 delhi",'order_note_admin'=>"1023",'order_note_customer'=>"12233333");

$arr2=array(array('itemId'=>"26",'qty'=>"30",'qty_complete'=>"20",'balance_qty'=>"10"),array('itemId'=>"27",'qty'=>"20",'qty_complete'=>"20",'balance_qty'=>"0"));
$array=array("soId","created","tax_amount","modified");
$arr3=array('itemId'=>"24",'qty'=>"30",'qty_complete'=>"20",'balance_qty'=>"10");
$obj=new saleorder;


##------This code is used to Insert sale order details------------##
##--------------------------------------------------------------##
/*if($obj->add_sale_order($arr1,$arr2))
{
	echo 'created<br>';
}
else
{
	echo 'error<br>';
}*/
#-------End HERE slae Details--------##
#-------End HERE sale Details--------##


##------This code is used to display  sale order details------------##
##--------------------------------------------------------------##
/*if($list=$obj->show_sale_order('1',$array))
{
	$obj-> pre($list);
}
else
{
	echo 'error<br>';
}*/

#-------End HERE sale order display Details--------##
#------End HERE sale order display Details--------##

##------This code is used to display  sale order details------------##
##--------------------------------------------------------------##

##------This code is used to Edit sale order details------------##
##--------------------------------------------------------------##
/*if($list=$obj->sale_order_edit('1',$arr1,$arr2))
{
	 $obj->pre($list);
}
else
{
	echo 'error occuer<br>';
}*/
##--------End Here----------##

##------This code is used to delete sale order details------------##
##--------------------------------------------------------------##

/*if($list=$obj->sale_order_delete('1'))
{
	 $obj->pre($list);
}
else
{
	echo 'error occuer<br>';
}*/
#---------End Here----------------------#

/*if($list=$obj->show_sale_bwdate('2',$array,'2012-11-08'))
{
	 $obj->pre($list);
}
else
{
	echo 'error occuer<br>';
}
*/
?>