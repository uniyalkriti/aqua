<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//$myobj = new dealer_sale();
$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$expense = array();
         $q = "SELECT id,(select name from dealer where id=claim_challan.dealer_id) as dealer_name,claim_amount,claim,DATE_FORMAT(claim_date,'%d-%m-%Y') as claim_date,total_amt,status FROM claim_challan WHERE dealer_id=$dealer_id ";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
		return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	    $out['dealer_name'] = $row['dealer_name'];
	    $out['claim_amount'] = $row['claim_amount'];
	    $out['claim'] = $row['claim'];
	    $out['claim_date'] = $row['claim_date'];
	    $out['total_amt'] = $row['total_amt'];
		if($row['status']==0)
		{
       	    $out['status'] = "Pending";
	   	}
	  else if($row['status']==1)
		{
       	    $out['status'] = "Accepted";
	   	}
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
