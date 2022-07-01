<?php
$postData = file_get_contents('php://input');
$xml = simplexml_load_string($postData, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
//$json='{"TALLYREQUEST":{"TYPE":"Sales","DEALERCODE":"SS0004","VOUCHERLIST":{"VOUCHER":[{"DATE":"1-4-2018","VOUCHERNUMBER":"1","VOUCHERID":"2","REFERENCE":"1","PARTYLEDGERNAME":"Rahul Test","PARTYLEDGERCODE":"RD0001","GSTIN":"07AAAAA1234A1Z5","STATE":"Delhi","ADDRESS":"East of Kailash, New Delhi, 12122","AMOUNT":"11,740.00","ALLINVENTORYENTRIES":[{"STOCKITEMNAME":"B.Kool Fruit Blast 3GMS PCH","STOCKITEMID":"FG000078","STOCKITEMGSTRATE":"12","TAX":"120.00","ACTUALQTY":"1","QTY":"1","UNIT":"PCS","RATE":"1000","DISCOUNT":"0","DISCOUNTVALUE":"0.00","AMOUNT":"1,000.00"},{"STOCKITEMNAME":"B.Kool Mouth Freshner","STOCKITEMID":"FG000079","STOCKITEMGSTRATE":"18","TAX":"1,620.00","ACTUALQTY":"1","QTY":"1","UNIT":"PCS","RATE":"10000","DISCOUNT":"10","DISCOUNTVALUE":"1,000.00","AMOUNT":"9,000.00"}],"ALLLEDGERENTRIES":[{"LEDGERNAME":"Rahul Test","GSTRATE":"0","AMOUNT":"11,740.00"},{"LEDGERNAME":"CGST","GSTRATE":"0","AMOUNT":"870.00"},{"LEDGERNAME":"SGST","GSTRATE":"0","AMOUNT":"870.00"}]},{"DATE":"2-4-2018","VOUCHERNUMBER":"2","VOUCHERID":"3","REFERENCE":{},"PARTYLEDGERNAME":"Rahul Test","PARTYLEDGERCODE":"RD0001","GSTIN":"07AAAAA1234A1Z5","STATE":"Delhi","ADDRESS":"East of Kailash, New Delhi, 12122","AMOUNT":"56,050.00","ALLINVENTORYENTRIES":{"STOCKITEMNAME":"B.Kool Mouth Freshner","STOCKITEMID":"FG000079","STOCKITEMGSTRATE":"18","TAX":"8,550.00","ACTUALQTY":"5","QTY":"5","UNIT":"PCS","RATE":"10000","DISCOUNT":"5","DISCOUNTVALUE":"2,500.00","AMOUNT":"47,500.00"},"ALLLEDGERENTRIES":[{"LEDGERNAME":"Rahul Test","GSTRATE":"0","AMOUNT":"56,050.00"},{"LEDGERNAME":"CGST","GSTRATE":"0","AMOUNT":"4,275.00"},{"LEDGERNAME":"SGST","GSTRATE":"0","AMOUNT":"4,275.00"}]},{"DATE":"1-5-2018","VOUCHERNUMBER":"3","VOUCHERID":"4","REFERENCE":"1","PARTYLEDGERNAME":"Rahul Test","PARTYLEDGERCODE":"RD0001","GSTIN":"07AAAAA1234A1Z5","STATE":"Delhi","ADDRESS":"East of Kailash, New Delhi, 12122","AMOUNT":"985.60","ALLINVENTORYENTRIES":{"STOCKITEMNAME":"B.Kool Fruit Blast 3GMS PCH","STOCKITEMID":"FG000078","STOCKITEMGSTRATE":"12","TAX":"105.60","ACTUALQTY":"1","QTY":"1","UNIT":"PCS","RATE":"1000","DISCOUNT":"12","DISCOUNTVALUE":"120.00","AMOUNT":"880.00"},"ALLLEDGERENTRIES":[{"LEDGERNAME":"Rahul Test","GSTRATE":"0","AMOUNT":"985.60"},{"LEDGERNAME":"CGST","GSTRATE":"0","AMOUNT":"52.80"},{"LEDGERNAME":"SGST","GSTRATE":"0","AMOUNT":"52.80"}]}]}}}';
$array = json_decode($json,TRUE);
//$myfile = fopen("newss.txt", "wr") or die("Unable to open file!");
//fwrite($myfile, $postData);
//fclose($myfile);
print_r($json);//exit;
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
    //print_r($voucher_list);
	$cosale=count($voucher_list);
	//while($co<$cosale)
	$product_list='';
	//mysqli_query($dbc, "START TRANSACTION");
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
        $voucher_id=$voucher_list[$co]['VOUCHERID'];
        $reference=$voucher_list[$co]['REFERENCE'];
		$dealer_code=$voucher_list[$co]['PARTYLEDGERCODE'];
		$qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
		$rdl=mysqli_query($dbc,$qdl);
		$rowdl=mysqli_fetch_assoc($rdl);
		$dealer_id=$rowdl['id'];
		$dealer_name=$voucher_list[$co]['PARTYLEDGERNAME'];
		$amount=$voucher_list[$co]['AMOUNT'];
	    $amount=str_replace(",","",$amount);
		$product_list1=array();
		$product_list=$voucher_list[$co]['ALLINVENTORYENTRIES'];
		 if(empty($product_list[0])){
		 	$product_list1=array();
		 	$product_list1[0]=(array)$voucher_list[$co]['ALLINVENTORYENTRIES'];
		 	//print_r($product_list1);
		 }else{
		 	$product_list1=$product_list;
		 }
		//print_r($product_list1);echo "</br>";
		mysqli_query($dbc, "START TRANSACTION");	
		$q="INSERT IGNORE `tally_demo`( `ch_no`,`ch_date`, `vocher_id`, `csa_id`, `dealer_id`, `dealer_name`, `amount`, `amount_round`,`server_date_time`) VALUES ('$voucher_no','$ch_date','$voucher_id','$csa_id','$dealer_id','$dealer_name','$amount','$amount',NOW())";
		$result=mysqli_query($dbc,$q);
		if(!$result){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Table Error') ;
		}
            $rId = mysqli_insert_id($dbc); 
             //print_r($product_list1);  
            if($rId!=0)
            {
             foreach($product_list1 as $cod=>$poo)
	     { 
	     	$product_name=$poo['STOCKITEMNAME'];
	     	$product_code=$poo['STOCKITEMID'];
	     	$qpd="SELECT id FROM catalog_product WHERE itemcode='$product_code' LIMIT 1";
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
	     	$dis_amt=str_replace(",","",$dis_amt);  
	     	$taxable_amt=$poo['AMOUNT'];
	     	$taxable_amt=str_replace(",","",$taxable_amt);

	     	$qdet="INSERT IGNORE `tally_demo_details`(`td_id`, `product_id`, `product_name`, `qty`, `rate`, `gst`, `gst_amt`, `dis_per`, `dis_amt`,`taxable_amt`) VALUES ('$rId','$product_id','$product_name','$qty','$rate','$gst','$gst_amt','$dis_per','$dis_amt','$taxable_amt')";
		$resultdet=mysqli_query($dbc,$qdet);
		if(!$resultdet){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Details Table Error') ;
		}
	}
}
	}
	mysqli_commit($dbc);

}
?>
