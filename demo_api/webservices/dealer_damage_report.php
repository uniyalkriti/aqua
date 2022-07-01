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
$q = "SELECT id, ch_retailer_id,r.name as rname, DATE_FORMAT(`ch_date`,'%d-%b-%Y') as ch_date, actual_amount, complaint_id FROM  `damage_order` LEFT JOIN retailer r ON r.id = ch_retailer_id WHERE DATE_FORMAT(`ch_date`,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(`ch_date`,'%Y-%m-%d') <= '$todate'  AND ch_dealer_id = '$dealer_id'";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['orderid'] = $row['id'];
            $out['date'] = $row['ch_date'];
	    $out['rname'] = $row['rname'];
            $out['amount'] = $row['actual_amount'];
           $comp = $row['complaint_id'];
                    $qn = mysqli_query($dbc,"SELECT name FROM `complaint_type` WHERE id=$comp");
                    $qf = mysqli_fetch_assoc($qn);
                    $out['comp_name'] = $qf['name'];
            
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
