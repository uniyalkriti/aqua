<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$report_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['report_date'])));
global $dbc;
$query = "SELECT retailer.id as id,retailer.`name` as fullname,l5.`name` as beat ,round(SUM(rate*quantity),2) as sale FROM retailer 
    INNER JOIN user_sales_order uso ON retailer.id=uso.retailer_id INNER JOIN user_sales_order_details usod ON usod.order_id=uso.order_id
    INNER JOIN location_5 l5 on l5.id=uso.location_id 
    WHERE uso.`date`='$report_date' AND uso.user_id = '$user_id' GROUP BY uso.retailer_id";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
    $rows['status'] = ($rows['sale']<1)?'N':'P';
    $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
