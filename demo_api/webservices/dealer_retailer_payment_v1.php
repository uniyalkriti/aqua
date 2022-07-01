<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$dealer_sale = array();
//$date = date("Y-m-d");
$i = 0;
     $q = "SELECT challan_order.id as cid,ch_no, ch_retailer_id,retailer.name as name FROM `challan_order` INNER JOIN  `retailer` ON challan_order.ch_retailer_id = retailer.id  WHERE ch_dealer_id = $dealer_id AND DATE_FORMAT(ch_date,'%Y-%m-%d') = '$date' AND challan_order.payment_status='0'";
//echo $q;
	$rs = mysqli_query($dbc,$q);
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['cid'];
	   $out['cid'] = $row['cid'];
	   $out['ch_no'] = $row['ch_no'];
           $out['retailer_id'] = $row['ch_retailer_id'];
           $out['name'] = $row['name'];
	   $q1 = mysqli_query($dbc,"SELECT sum(taxable_amt) as amt FROM `challan_order_details` WHERE `ch_id`= $id");
	   $row1 = mysqli_fetch_assoc($q1);
           $out['amt'] = $row1['amt'];
 	   $retailer[$i] = $out;
$i++;
        }

//$retailer[] = $out;

$final_array = array("result" => $retailer);
$data = json_encode($final_array);
echo $data;



?>
