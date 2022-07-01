<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');


$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealerid'])));
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
// echo $dealer_id.$user_id.$fromdate.$todate;


global $dbc;
$out = array();
$expense = array();
          $q = "SELECT retailer_id,name, sum(amount) as amount FROM  `payment_collect_retailer` INNER JOIN retailer ON retailer.id = payment_collect_retailer.tr_code WHERE `payment_date`>='$fromdate' AND `payment_date`<='$todate' AND `user_id`='$user_id' AND `payment_collect_retailer`.`dealer_id`='$dealer_id' group by `tr_code`";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	   $out['retailer_id'] = $row['retailer_id'];
	    $out['retailer_name'] = $row['name'];
	     $out['amount'] = $row['amount'];
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
