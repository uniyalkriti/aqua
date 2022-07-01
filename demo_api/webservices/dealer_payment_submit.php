<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
// $unique_id = array();
if(isset($_POST['response'])){$check=$_POST['response'];} else $check='';
//$check ='{"response":{"challan_no":"CATC\/1252\/000002\/2017-2018","retailer_id":"20170118181224157","dealer_id":1252,"payment_mode":"By Cash","amount":"488","transaction_date":"","cheque_date":"","cheque_no":"","date_current":"2017-04-25","branch_name":"","imei":"869162020046346","time":"15:08:52","cid":"1252170412101825"}}';
//echo $dbc;
$check  = str_replace("'","",$check);
$data=json_decode($check);
//print_r($data);
if($data)
{
$payment=$data->response;
//print_r($payment);exit;
if(!empty($payment)){
  $count_challan=count($payment);
		$cid = $payment->cid;
	        $retailer_id=$payment->retailer_id;
 //$retailer_id;
		$dealer_id=$payment->dealer_id;		
		$challan_no = $payment->challan_no;
		$date= $payment->date_current;
		$paymentmode = $payment->payment_mode;
		$amount = $payment->amount;
		if(!empty($payment->cheque_date))
		{
		$cheque_date = $payment->cheque_date;
		}
		else if(!empty($payment->transaction_date))                
		{
		$cheque_date = $payment->transaction_date;
		}
		else
		{
		$cheque_date = "0000-00-00";
		}
		$cheque_no = $payment->cheque_no;
		$bank_name = $payment->branch_name;
		$remark = 'abc';
          
	//////////////////////////////////??INSERT PAYMENT COLLECTION////////////////////////////////////////
               $result = mysqli_query($dbc,"INSERT INTO `payment_collection`(`dealer_id`, `retailer_id`, `challan_id`, `total_amount`, `pay_mode`, `bank_name`, `chq_no`, `chq_date`, `Remark`) VALUES ('$dealer_id','$retailer_id','$cid','$amount','$paymentmode','$bank_name','$cid','$cheque_date','$remark')");
     	
	//////////////////////////////////??END PAYMENT COLLECTION////////////////////////////////////////

	////////////////////////////////////UPDATE CHALLAN ORDER//////////////////////////////////////
	
 	$qco = "UPDATE `challan_order` SET `payment_status`='1' WHERE id=$cid";
		$r = mysqli_query($dbc, $qco);
     // echo $qco."<br>";
	
       
	}
  
	
//}

ob_start();
ob_clean();
                        
			 $essential= "Y"; 
                       //  $d = json_encode($essential);
                         echo $essential;
	
ob_get_flush();
ob_end_flush();

}
else{
                        $essential= "N"; 
                     //   $d = json_encode($essential);
                       echo $essential;
}


?>
