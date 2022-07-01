<?php
$postData = file_get_contents('php://input');
$postData=str_replace("'"," ",$postData);
//print_r($postData);exit;
//$xml = simplexml_load_string($postData, "SimpleXMLElement", LIBXML_NOCDATA);
//$json = json_encode($postData);
$array1 = json_decode($postData,TRUE);
$array=$array1['ENVELOPE'];
print_r($postData);//exit;
date_default_timezone_set('Asia/Kolkata');
$dealer_code=$array['TALLYREQUEST']['DEALERCODE'];
$voucher_list=$array['TALLYREQUEST']['VOUCHERLIST']['VOUCHER'];
//print_r($voucher_list);exit;
require_once('../admin/include/conectdb.php');
global $dbc;
$qd="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
$rd=mysqli_query($dbc,$qd);
$rowd=mysqli_fetch_assoc($rd);
$dealer_id=$rowd['id'];
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
		$retailer_code=$voucher_list[$co]['PARTYLEDGERCODE'];
		$qdl="SELECT id FROM retailer WHERE retailer_code='$retailer_code' LIMIT 1";
		$rdl=mysqli_query($dbc,$qdl);
		$rowdl=mysqli_fetch_assoc($rdl);
		$retailer_id=$rowdl['id'];
		$retailer_name=$voucher_list[$co]['PARTYLEDGERNAME'];
		$amount=$voucher_list[$co]['AMOUNT'];
	    $amount=str_replace(",","",$amount);
		$product_list1=$voucher_list[$co]['ALLINVENTORYENTRIES'];
		$allLedger=$voucher_list[$co]['ALLLEDGERENTRIES'];
		// foreach($allLedger as $lod=>$loo)
	 //     {
	 //     }
		//print_r($allLedger);echo "</br>";
		mysqli_query($dbc, "START TRANSACTION");	

		$q="INSERT INTO `challan_order`(`id`,`ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_user_id`, `ch_date`, `date_added`, `company_id`, `dispatch_status`, `discount`, `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`, `isclaim`, `istarget_claim`, `discount_per`, `discount_amt`, `amount`, `remaining`) VALUES ('$id','$voucher_no','$dealer_id','$dealer_id','$retailer_id','1','$ch_date',NOW(),'1','0','$discount','1','','0','11','0','0','0','$discount','$discount_amt','$amount','$amount')";
		$result=mysqli_query($dbc,$q);
		if(!$result){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Table Error') ;
		}
            $rId = mysqli_insert_id($dbc); 
             //print_r($product_list1);  
            if($rId!=0)
            {
            	$qup="UPDATE `challan_order` SET `id`=`auto` WHERE `auto`='$rId'";
            	$resultup=mysqli_query($dbc,$qup);
             foreach($product_list1 as $cod=>$poo)
	     { 
	     	$product_name=$poo['STOCKITEMNAME'];
	     	$product_code=$poo['STOCKITEMID'];
	     	$qpd="SELECT id,base_price AS mrp FROM catalog_product WHERE itemcode='$product_code' LIMIT 1";
	     	$rpd=mysqli_query($dbc,$qpd);
	     	$rowpd=mysqli_fetch_assoc($rpd);
	     	$product_id=$rowpd['id'];
	     	$mrp=$rowpd['mrp'];
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
	     	$order_id=$poo['ORDERNO'];
	     	$qdet="INSERT INTO `challan_order_details`(`ch_id`, `product_id`, `supply_status`, `hsn_code`, `catalog_details_id`, `batch_no`, `tax`, `vat_amt`, `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, `remain_amount`, `sale_order_id`, `sync_status`) VALUES ('$rId','$product_id','0','0','0','0','$gst','$gst_amt','$qty','$rate','0','$order_id','1','$mrp','0','1','0',1,'$$dis_amt','$dis_per','$taxable_amt','$taxable_amt',$order_id,'0')";
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
