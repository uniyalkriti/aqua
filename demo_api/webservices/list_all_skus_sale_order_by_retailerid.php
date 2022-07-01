<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

// $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailerid'])));
$report_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['report_date'])));
global $dbc;
$query = "SELECT usod.product_id,cp.`name`,usod.quantity,SUM(rate*quantity) as sale_value FROM retailer 
    INNER JOIN user_sales_order uso ON retailer.id=uso.retailer_id 
    INNER JOIN user_sales_order_details usod ON uso.order_id=usod.order_id 
    INNER JOIN catalog_product cp ON cp.id=usod.product_id  
    WHERE uso.`date`='$report_date' AND uso.retailer_id = '$retailer_id' GROUP BY usod.product_id";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
