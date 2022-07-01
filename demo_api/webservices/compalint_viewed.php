<?php
//http://192.168.1.12/msell/webservices/circular_view.php?imei=78754389499394992&id=1
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
$id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['id'])));
$q= "SELECT id FROM user_complaint WHERE person_id = '$user_id' AND  `order_id`='$id'";
//echo $q;die;
$r = mysqli_query($dbc, $q);
if($r && mysqli_num_rows($r) > 0)
{
	$update_qry = "UPDATE user_complaint SET is_view ='1' WHERE person_id = '$user_id' AND  `order_id`='$id'";
   $update = mysqli_query($dbc,$update_qry); 
   if($update){
   	echo 'Y';
   }
}else{
	echo 'N';
}
?>