<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));

global $dbc;
$out = array();
$expense = array();
          $q = "SELECT dealer_id,name FROM  `dealer_balance_stock`  INNER JOIN dealer ON dealer.id = dealer_balance_stock.dealer_id WHERE `user_id`='$user_id' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')>='$to_date' group by dealer_id";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	   $out['dealer_id'] = $row['dealer_id'];
	    $out['dealer_name'] = $row['name'];
	     
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
