<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealerid'])));
$company_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['company_id'])));
//$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));

//$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
//$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
//dms_stock.rate,
global $dbc;
$query = "SELECT cp.name as productname,cp.id as product_id,
dealer_balance_stock.stock_qty as qty,dealer_balance_stock.dealer_id as dealer_id FROM `dealer_balance_stock`
INNER JOIN catalog_product as cp ON cp.id=dealer_balance_stock.product_id WHERE  dealer_balance_stock.dealer_id='$dealer_id' AND dealer_balance_stock.company_id = '$company_id' AND stock_qty != 0 ";
 // h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}

    if(empty($data)){
        $final_data = Array ( 'result' => Array ('0' =>Array ( 'product_id' => '0', 'productname' => 'NA', 'cases' => '0' )));
    }else{
        $final_data = array('result'=>$data);
    }

$result = json_encode($final_data);
echo $result;
