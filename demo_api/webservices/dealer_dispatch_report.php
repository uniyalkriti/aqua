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
$challan = array();
$q = "SELECT van_no,dispatch_id,DATE_FORMAT( daily_dispatch.dispatch_date, '%e/%b/%Y' ) AS dispatch_date,dispatch_no FROM daily_dispatch WHERE DATE_FORMAT(daily_dispatch.dispatch_date,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(daily_dispatch.dispatch_date,'%Y-%m-%d') <= '$todate' AND dealer_id = '$dealer_id' ORDER BY daily_dispatch.dispatch_id DESC";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['dispatch_id'] = $row['dispatch_id'];
             $out['dispatch_no'] = $row['dispatch_no'];
	     $out['dispatch_date'] = $row['dispatch_date'];
	      $out['van_no'] = $row['van_no'];
		$id = $row['dispatch_id'];
		// $out['dispatch_date'] = $row['dispatch_date'];
           // $out['csa_name'] = $row['csa_name'];
         //  $user = $row['created_person_id'];
		$inbc = 0;
                    $qn = mysqli_query($dbc,"SELECT ch_no FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = '$id'");
                   while($qf = mysqli_fetch_assoc($qn))
			{
                   $challan[] = $qf['ch_no'];
			
           		}
	$out['ch_no'] = implode(',',$challan);	
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
