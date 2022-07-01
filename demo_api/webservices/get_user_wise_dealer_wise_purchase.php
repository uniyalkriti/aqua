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



$sale = array();
$array=array();

$q1 = "SELECT product_id,catalog_product.name,sum(pcs) AS stock,sum(pcs*rate) AS stock_value FROM user_primary_sales_order INNER JOIN user_primary_sales_order_details ON user_primary_sales_order.order_id=user_primary_sales_order_details.order_id INNER JOIN catalog_product ON  user_primary_sales_order_details.product_id=catalog_product.id WHERE created_person_id='$user_id' AND DATE_FORMAT(`created_date`,'%Y-%m-%d')>='$s_date' AND DATE_FORMAT(`created_date`,'%Y-%m-%d')<='$e_date' GROUP BY created_person_id,dealer_id,product_id";
//h1($q1);
$r1 = mysqli_query($dbc, $q1);
while($row1 = mysqli_fetch_array($r1)){
$sale['product_id'] =$row1['product_id']; 
$sale['product_name'] =$row1['name'];
$sale['stock'] =$row1['stock'];
$sale['stock_value'] =$row1['stock_value'];
$array[]=$sale;
}
$f = array("result"=>$array);
$data = json_encode($f);
echo  $data;

?>          