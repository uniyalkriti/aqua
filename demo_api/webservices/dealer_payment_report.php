<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//$myobj = new dealer_sale();
$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$expense = array();
$rq = "SELECT `total_amount`,DATE_FORMAT(`pay_date_time`,'%Y-%m-%d') as pay_date,DATE_FORMAT(`ch_date`,'%Y-%m-%d') as ch_date,ch_no,ch_retailer_id,`pay_mode` FROM `payment_collection` INNER JOIN challan_order ON payment_collection.`challan_id`= challan_order.id WHERE  DATE_FORMAT(`pay_date_time`,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(`pay_date_time`,'%Y-%m-%d') <= '$todate' AND dealer_id='$dealer_id'";
       // h1($rq);
        $rs = mysqli_query($dbc,$rq);
//echo mysqli_num_rows($rs);
$i = 0;
         while($row = mysqli_fetch_assoc($rs)) {
	    $out['challan_no'] = $row['ch_no'];
	    $out['payment_date'] = $row['pay_date'];
	    $out['ch_date'] = $row['ch_date'];
	    $retailer_id = $row['ch_retailer_id'];
	$ret = mysqli_query($dbc,"SELECT name FROM `retailer` WHERE id=$retailer_id");
	$row_ret= mysqli_fetch_assoc($ret);
	    $out['retailer_name'] = $row_ret['name'];
	    $out['amount'] = $row['total_amount'];
		if($row['pay_mode']==0)
		{
       	    $out['mode'] = "Cash";
	   	}
	  else if($row['pay_mode']==1)
		{
       	    $out['mode'] = "Cheque";
	   	}
	else if($row['pay_mode']==2)
		{
       	    $out['mode'] = "RTGS";
	   	}
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
