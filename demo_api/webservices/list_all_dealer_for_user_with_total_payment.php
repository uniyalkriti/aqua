<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$expense = array();
          $q = "SELECT dealer_id, name, sum(amount) as amount FROM  `payment_collect_retailer` INNER JOIN dealer ON dealer.id=payment_collect_retailer.dealer_id
               WHERE DATE_FORMAT(`payment_date`,'%Y-%m-%d')>='$fromdate' AND DATE_FORMAT(`payment_date`,'%Y-%m-%d')<='$todate' AND `user_id`='$user_id' GROUP BY dealer_id";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	   $out['dealer_id'] = $row['dealer_id'];
	   $out['dealer_name'] = $row['name'];
	  $out['amount'] = $row['amount'];
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
