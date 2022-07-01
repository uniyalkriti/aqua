<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//$myobj = new dealer_sale();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
global $dbc;
$out = array();
$expense = array();
          $q = "SELECT date_time,status,total as expense FROM `travelling_expense_bill` WHERE DATE_FORMAT(date_time,'%Y-%m-%d') >='$fromdate' AND DATE_FORMAT(date_time,'%Y-%m-%d') <='$todate'  AND user_id = '$user_id'";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	   $out['date'] = $row['date_time'];
	    $out['value'] = $row['expense'];
  if($row['status']==0)
    {
    $out['status'] = 'PENDING';    
    }
    else
    {
    $out['status'] = 'APPROVED';  
    }
	   // $out['status'] = $row['status'];
	    
	
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
