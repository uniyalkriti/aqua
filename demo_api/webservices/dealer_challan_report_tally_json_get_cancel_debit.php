<?php
$postData = file_get_contents('php://input');
$postData=str_replace("'"," ",$postData);
//print_r($postData);exit;
//$xml = simplexml_load_string($postData, "SimpleXMLElement", LIBXML_NOCDATA);
//$json = json_encode($postData);
//$postData='';
$array1 = json_decode($postData,TRUE);
$array=$array1['ENVELOPE'];
//$myfile = fopen("newss.txt", "wr") or die("Unable to open file!");
//fwrite($myfile, $postData);
//fclose($myfile);
//print_r($postData);//exit;
date_default_timezone_set('Asia/Kolkata');
$csa_code=$array['TALLYREQUEST']['DEALERCODE'];
$voucher_list=$array['TALLYREQUEST']['VOUCHER'];
//print_r($voucher_list);exit;
require_once('../admin/include/conectdb.php');
global $dbc;
$qd="SELECT c_id FROM csa WHERE csa_code='$csa_code' LIMIT 1";
$rd=mysqli_query($dbc,$qd);
$rowd=mysqli_fetch_assoc($rd);
$csa_id=$rowd['c_id'];
if(!empty($voucher_list))
{
   // print_r($voucher_list);
	 $cosale=count($voucher_list);
	//while($co<$cosale)
	$product_list='';
	//mysqli_query($dbc, "START TRANSACTION");
	$str=array();
	foreach($voucher_list as $co=>$coo)
	{
		//print_r($coo);
		$id=date('YmdHis').$dealer_id;
	    $date=$voucher_list[$co]['DATE'];
	    $start=explode('-',$date);
	   // print_r($start);
        if($start[0]<10){
        $d1="0".$start[0];
        }else{
        $d1=$start[0];
        }
        if($start[1]<10){
       	$m1="0".$start[1];
        }else{
        $m1=$start[1];
        }
        $y1=$start[2];
        $ch_date=$y1."-".$m1."-".$d1;
        $cancel_date=$voucher_list[$co]['CANCELDATE'];
	    $cancel=explode('-',$cancel_date);
	   // print_r($start);
        if($cancel[0]<10){
        $d2="0".$cancel[0];
        }else{
        $d2=$cancel[0];
        }
        if($cancel[1]<10){
       	$m2="0".$cancel[1];
        }else{
        $m2=$cancel[1];
        }
        $y2=$cancel[2];
        $cancel_date=$y2."-".$m2."-".$d2;
        $voucher_no=$voucher_list[$co]['VOUCHERNUMBER'];
        $first_voucher_no=$voucher_list[$co]['FIRSTVOUCHERNUMBER'];
        $voucher_id=$voucher_list[$co]['VOUCHERID'];
        $ss_voucher_id=$voucher_list[$co]['DEALERVOUCHERID'];
		$dealer_code=$voucher_list[$co]['PARTYLEDGERCODE'];
		$dealer_id='';
		$qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
		$rdl=mysqli_query($dbc,$qdl);
		$rowdl=mysqli_fetch_assoc($rdl);
		$dealer_id=$rowdl['id'];
		$dealer_name=$voucher_list[$co]['PARTYLEDGERNAME'];
		$invoice_type_id=$voucher_list[$co]['ISCANCEL'];
		
		if($invoice_type_id=='No'){
				$invoice_type='6';
		}elseif($invoice_type_id=='Yes'){
				$invoice_type='7';
		}else{
				$invoice_type='6';
		}
		//print_r($product_list1);echo "</br>";
		mysqli_query($dbc, "START TRANSACTION");
		$qcheck="SELECT id FROM tally_demo WHERE ss_voucher_id='$ss_voucher_id' LIMIT 1";
		$rcheck=mysqli_query($dbc,$qcheck);
		$ncheck=mysqli_num_rows($rcheck);
		$rowcheck=mysqli_fetch_assoc($rcheck);
		$check_id=$rowcheck['id'];
		//if($ncheck>0){	
		$q="UPDATE `tally_demo` SET `ch_date`='$ch_date',`invoice_type`='7',`drcr`='DR',`amount`='$amount',`amount_round`='$amount',`cancel_date`='$cancel_date',`server_date_time`=NOW() WHERE ss_voucher_id='$ss_voucher_id'";		
			//}
		$result=mysqli_query($dbc,$q);
		if(!$result){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Table Error') ;
		}else{
			$qdelete="DELETE FROM `tally_demo_details` WHERE `td_id`='$check_id'";
	     	$rdelete=mysqli_query($dbc,$qdelete);
		}
           
}
mysqli_commit($dbc);	
$resposnse=array('TALLYREQUEST'=>'Success');
echo $result=json_encode($resposnse);
}
?>
