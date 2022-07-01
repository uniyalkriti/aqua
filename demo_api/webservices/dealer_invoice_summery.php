<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
require_once('../client/include/classes/primary_sale.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
global $dbc;
//$out = array();
//$dealer_sale = array();
//$date = date("Y-m-d");

$data = array();
$q = "SELECT challan_order.id as id,ch_no,amount ,ch_retailer_id,r.name as retailer_name, DATE_FORMAT(dispatch_date, '%d/%m/%Y') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order left join retailer r on challan_order.ch_retailer_id=r.id WHERE DATE_FORMAT(ch_date,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(ch_date,'%Y-%m-%d') <= '$todate' AND ch_dealer_id = '$dealer_id' ORDER BY challan_order.ch_no DESC";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['ch_no'] = $row['ch_no'];
            $out['ch_date'] = $row['ch_date'];
            $out['retailer_name'] = $row['retailer_name'];
		$out['amount'] = $row['amount'];
		$id = $row['id'];

          
           // $out['scheme'] = $row['scheme'];
           // $out['cases'] = $row['cases'];
	   		
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
