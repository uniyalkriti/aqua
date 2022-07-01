<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new dealer_sale();
//$retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id'])));
$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
//$dealer_sale = array();
//$sale_array = array();
$sale_order = array();
//$deal = "SELECT state_id FROM `dealer_person_login` WHERE dpId=$dealer_id";
//$deal_r = mysqli_query($dbc, $deal);
//$deal_row = mysqli_fetch_assoc($deal_r);
//$state = $deal_row['state_id'];

      

/////////////////////////////////////////??SALE ORDER //////////////////////////////////////////////////////

$usd = "SELECT * FROM `user_sales_order` WHERE dealer_id = '$dealer_id' AND date = '$date' AND order_status='0'";
//echo $usd;
$usdm = mysqli_query($dbc,$usd);
$ussd = array();
$usdc = 0;
while($row_sale = mysqli_fetch_assoc($usdm))
{
$ussd['order_id'] = $row_sale['order_id'];
$ussd['dealer_id'] = $row_sale['dealer_id'];
$ussd['user_id'] = $row_sale['user_id'];
$ussd['retailer_id'] = $row_sale['retailer_id'];
$ussd['location_id'] = $row_sale['location_id'];
$ussd['total_sale_value'] = $row_sale['total_sale_value'];
$ussd['discount'] = $row_sale['discount'];
$ussd['amount'] = $row_sale['amount'];
$ussd['order_id'] = $row_sale['order_id'];
  $sale_order[$usdc] = $ussd;
$usdc++;
}
//print_r($row_sale);
	
    //$sale_array[] = array(
	     //      "sale_order" => $sale_order
		 //,"sale_order_details" => $dealer_sale
                
		//	);    


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $sale_order);
$data = json_encode($final_array);
echo $data;



?>
