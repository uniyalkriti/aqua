<?php
//http://192.168.1.12/msell/webservices/circular_view.php?imei=78754389499394992&id=1
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['id'])));
$q= "SELECT id FROM person WHERE imei_number = $imei ";
//echo $q;die;
$r = mysqli_query($dbc, $q);
if($r && mysqli_num_rows($r) > 0)
{
	$row = mysqli_fetch_assoc($r);
	$user_id = $row['id'];

	$chk_qry ="select * from circular_view where circular_id='$id' AND user_id='$user_id'";
//echo $chk_qry;die;
	$qchk = mysqli_query($dbc , $chk_qry);
	//echo mysqli_num_rows($qchk);die;
	if(mysqli_num_rows($qchk)<=0){
			
			$q = "INSERT INTO circular_view (`circular_id`, `user_id`) VALUES ('$id', '$user_id')";
			$r = mysqli_query($dbc , $q);
			if($r)
	  		echo "N";


	}else{
		echo "N";
	}
    
}
?>