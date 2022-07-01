<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['start_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_date']))); else $s_date = 0;
if(isset($_GET['end_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_date']))); else $e_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['role_id'])) $role_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['role_id']))); else $role_id = 0;
if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;



$stock = array();
$array=array();

$q1 = "SELECT product_id,catalog_product.name,sum(stock_qty) AS stock,sum(stock_qty*pcs_mrp) AS stock_value FROM dealer_balance_stock INNER JOIN catalog_product ON  dealer_balance_stock.product_id=catalog_product.id WHERE user_id='$user_id' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')>='$s_date' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')<='$e_date' AND dealer_id='$dealer_id' GROUP BY user_id,dealer_id,product_id";

// $q1 = "SELECT product_id,catalog_product.name,sum(quantity) AS stock,sum(stock_qty*pcs_mrp) AS stock_value FROM retailer_stock INNER JOIN retailer_stock_details ON retailer_stock.order_id=retailer_stock_details.order_id INNER JOIN catalog_product ON  retailer_stock_details.product_id=catalog_product.id WHERE user_id='$user_id' AND DATE_FORMAT(`date`,'%Y-%m-%d')>='$s_date' AND DATE_FORMAT(`date`,'%Y-%m-%d')<='$e_date' AND dealer_id='$dealer_id' GROUP BY user_id,dealer_id,product_id";
//h1($q1);
$r1 = mysqli_query($dbc, $q1);
while($row1 = mysqli_fetch_array($r1)){
$stock['product_id'] =$row1['product_id']; 
$stock['product_name'] =$row1['name'];
$stock['stock'] =$row1['stock'];
$stock['stock_value'] =$row1['stock_value'];
$array[]=$stock;
}
$f = array("result"=>$array);
$data = json_encode($f);
echo  $data;

?>          