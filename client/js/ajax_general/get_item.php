<?php
 require_once('../../include/conectdb.php');
 $id = $_GET['option'];
 $id = explode('#' , $id);
 $q = "SELECT id, batch_no FROM user_primary_sales_order_details WHERE product_id = '$id[0]'";

$r = mysqli_query($dbc,$q);
if($r)
{
	if(mysqli_num_rows($r)>0)
	{
		$str = '';
		while($d = mysqli_fetch_assoc($r))
		{
			$str .= $d['id'].'@'.$d['batch_no'].'<$>';
		}
		$str = rtrim($str,'<$>');
                
		echo $str;
	}
	else
	echo '1';
}
else
{
	echo '0';
}
?>