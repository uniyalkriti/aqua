<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

// if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
// if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
if(isset($_GET['date'])) $date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $s_date = 0;
if(isset($_GET['userid'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid']))); else $user_id = 0;
if(isset($_GET['beatid'])) $beatid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['beatid']))); else $beatid = 0;


$q1 = "SELECT `id` from person where id = '$user_id'";
$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
$q_row = mysqli_fetch_assoc($q_res);

$person_id = $q_row['id'];

$data = array();
$final = array();
 $q = "SELECT retailer_id ,retailer.name as retailer_name, SUM(total_sale_value) as sale FROM user_sales_order INNER JOIN retailer ON retailer.id = user_sales_order.retailer_id WHERE 
 `date` = '$date' AND `user_id`  = '$user_id' AND user_sales_order.location_id = '$beatid' GROUP BY retailer_id "; 
// h1($q);
$sale_res = mysqli_query($dbc, $q);
while($sale_row = mysqli_fetch_array($sale_res))
{
$beat_id=$sale_row['beat_id'];
$data["retailer_id"] = $sale_row['retailer_id'];
$data["retailer_name"] = $sale_row['retailer_name'];
$data["sale"] = $sale_row['sale'];
$final[] = $data;
}

$final_array = array("result"=>$final);	
$data = json_encode($final_array);
echo  $data;


?>          