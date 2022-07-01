<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
if(isset($_GET['retailer_id'])) $retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id']))); else $retailer_id = 0;
if(isset($_GET['s_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['s_date']))); else $e_date = 0;
if(isset($_GET['e_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['e_date']))); else $s_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;

$q1 = "SELECT `id` from person where id = '$user_id'";
$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
$q_row = mysqli_fetch_assoc($q_res);

$person_id = $q_row['id'];


$sale_value = 0;
$q = "select SUM(rate * quantity)as sale, SUM(quantity) as quantity from user_sales_order_details "
        . "INNER JOIN user_sales_order USING (order_id) where retailer_id  = '$retailer_id' AND date >='$s_date' "
        . "AND date<= '$e_date' AND user_id  = '$person_id' ";
//h1($q);
$sale_res = mysqli_query($dbc, $q);
$sale_row = mysqli_fetch_array($sale_res);

if($sale_row['sale'] != 0){
$sale_value = my2digit($sale_row['sale']);
}  

 $q = "select catalog_product.id ,catalog_product.name from user_sales_order "
        . "INNER JOIN user_sales_order_details USING (order_id) INNER JOIN catalog_product ON catalog_product.id = user_sales_order_details.product_id where retailer_id  = '$retailer_id' AND date >='$s_date' "
        . "AND date<= '$e_date' AND user_id  = '$person_id' GROUP BY catalog_product.id ";
$sku_res = mysqli_query($dbc, $q);
$sku = array();

$final_sku_array = array();
while($sku_row = mysqli_fetch_array($sku_res))
{
    $id = $sku_row['id'];
    $sku['id'] = $sku_row['id'];
    $sku['name'] = $sku_row['name'];
    $final_sku_array[] = $sku;
}

$final_array[] = array("sku"=>$final_sku_array,"total_sale_value"=>$sale_value);
//pre($final_array);
$f = array("result"=>$final_array);
$data = json_encode($f);
echo  $data;

?>          