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
$voucher_list=$array['TALLYREQUEST']['VOUCHERLIST']['VOUCHER'];
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
	$strs=array();
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
        $voucher_no=$voucher_list[$co]['VOUCHERNUMBER'];
        $first_voucher_no=$voucher_list[$co]['FIRSTVOUCHERNUMBER'];
        $voucher_id=$voucher_list[$co]['VOUCHERID'];
        $ss_voucher_id=$voucher_list[$co]['DEALERVOUCHERID'];
        $reference=$voucher_list[$co]['REFERENCE'];
		$dealer_code=$voucher_list[$co]['PARTYLEDGERCODE'];
		$dealer_code=str_replace(" ","",$dealer_code);
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
		//$invoice_type='6';
		$amount=$voucher_list[$co]['AMOUNT'];
	    $amount=str_replace(",","",$amount);
		$product_list1=$voucher_list[$co]['ALLINVENTORYENTRIES'];
		//print_r($product_list1);echo "</br>";
		mysqli_query($dbc, "START TRANSACTION");
		$qcheck="SELECT id FROM tally_demo WHERE ss_voucher_id='$ss_voucher_id' LIMIT 1";
		$rcheck=mysqli_query($dbc,$qcheck);
		$ncheck=mysqli_num_rows($rcheck);
		$rowcheck=mysqli_fetch_assoc($rcheck);
		$check_id=$rowcheck['id'];
		if($ncheck<=0){	
		$q="INSERT IGNORE `tally_demo`( `ch_no`,`first_ch_no`,`ch_date`, `vocher_id`,`ss_voucher_id`, `csa_code`,`csa_id`,`dealer_code`, `dealer_id`, `dealer_name`,`invoice_type`,`drcr`, `amount`, `amount_round`,`server_date_time`) VALUES ('$voucher_no','$first_voucher_no','$ch_date','$voucher_id','$ss_voucher_id','$csa_code','$csa_id','$dealer_code','$dealer_id','$dealer_name','$invoice_type','DR','$amount','$amount',NOW())";
			}else{
		$q="UPDATE `tally_demo` SET `ch_no`='$voucher_no',`first_ch_no`='$first_voucher_no',`ch_date`='$ch_date',`dealer_code`='$dealer_code',`dealer_id`='$dealer_id',`dealer_name`='$dealer_name',`invoice_type`='$invoice_type',`drcr`='DR',`amount`='$amount',`amount_round`='$amount',`server_date_time`=NOW() WHERE ss_voucher_id='$ss_voucher_id'";		
			}
		$result=mysqli_query($dbc,$q);
		if(!$result){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Table Error') ;
		}
            $rId = mysqli_insert_id($dbc); 
             //print_r($product_list1);  
            $str=array();
             foreach($product_list1 as $cod=>$poo)
	     		{ 
	     	$product_name=$poo['STOCKITEMNAME'];
	     	$product_code=$poo['STOCKITEMID'];
	     	$product_code=str_replace(" ","",$product_code);
	     	$qpd="SELECT id FROM tally_catalog_product WHERE itemcode='$product_code' LIMIT 1";
	     	$rpd=mysqli_query($dbc,$qpd);
	     	$rowpd=mysqli_fetch_assoc($rpd);
	     	$product_id=$rowpd['id'];
	     	$qty=$poo['QTY'];
	     	$rate=$poo['RATE'];
	     	$gst=$poo['STOCKITEMGSTRATE'];
	     	$gst_amt=$poo['TAX'];
	     	$gst_amt=str_replace(",","",$gst_amt);
	     	$dis_per=$poo['DISCOUNT'];
	     	$dis_amt=$poo['DISCOUNTVALUE'];
	     	$item_for=$poo['ITEMFOR'];
	     	$dis_amt=str_replace(",","",$dis_amt);  
	     	$taxable_amt=$poo['AMOUNT'];
	     	$taxable_amt=str_replace(",","",$taxable_amt);
	     	if($ncheck<=0){	
	     	$str[]="('$rId','$product_id','$product_name','$qty','$rate','$gst','$gst_amt','$dis_per','$dis_amt','$taxable_amt','$item_for')";
	     	//print_r($str);
	     }else{
	     	$str[]="('$check_id','$product_id','$product_name','$qty','$rate','$gst','$gst_amt','$dis_per','$dis_amt','$taxable_amt','$item_for')";
	     	//print_r($str);
	     }
		}
			$strs='';
	        $strs = implode(',', $str);
	     	$qdelete="DELETE FROM `tally_demo_details` WHERE `td_id`='$check_id'";
	     	$rdelete=mysqli_query($dbc,$qdelete);
	     	if(!empty($strs)){
	     	$qdet="INSERT IGNORE `tally_demo_details`(`td_id`, `product_id`, `product_name`, `qty`, `rate`, `gst`, `gst_amt`, `dis_per`, `dis_amt`,`taxable_amt`,`item_for`) VALUES $strs";
	     	$resultdet=mysqli_query($dbc,$qdet);
		if(!$resultdet){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Details Table Error') ;
		}	
	}
}
mysqli_commit($dbc);	
$resposnse=array('TALLYREQUEST'=>'Success');
echo $result=json_encode($resposnse);
}
?>
