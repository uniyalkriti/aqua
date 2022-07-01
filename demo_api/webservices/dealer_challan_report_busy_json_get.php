<?php
$postData = file_get_contents('php://input');
 //$postData='';
//$postData='';
$postData=str_replace("'"," ",$postData);
//print_r($postData);exit;
$array1 = json_decode($postData,TRUE);
$array=$array1['manacle'];
// $fp = fopen('sidebar_subscribers.txt', 'a') or die('fopen failed');
// fwrite($fp, $postData) or die('fwrite failed');
//print_r($array);exit;
date_default_timezone_set('Asia/Kolkata');
$csa_code=$array['ss_code'];
$voucher_list=$array['data'];
$master=$array['master'];
$opening=$array['opening'];
//print_r($voucher_list);exit;
require_once('../admin/include/conectdb.php');
global $dbc;
$qd="SELECT c_id FROM csa WHERE csa_code='$csa_code' LIMIT 1";
$rd=mysqli_query($dbc,$qd);
$rowd=mysqli_fetch_assoc($rd);
$csa_id=$rowd['c_id'];//exit;

if(!empty($master)){
	$qmd="DELETE FROM `busy_masters` WHERE `ss_code`='$csa_code'";
    $rmd=mysqli_query($dbc,$qmd);
	foreach($master as $mo=>$moo)
	{
		//print_r($master[0]);
		$Name=$master[$mo]['Name'];
        $Alias=$master[$mo]['Alias'];
        $Code=$master[$mo]['Code'];
        $MasterType=$master[$mo]['MasterType'];
        $qm="INSERT INTO `busy_masters`(`ss_code`, `csa_id`, `code`, `master_type`, `name`, `alias`,`server_date_time`) VALUES ('$csa_code','$csa_id','$Code','$MasterType','$Name','$Alias',NOW())";
        $rm=mysqli_query($dbc,$qm);
	}

}

if(!empty($opening)){
	$qmo="DELETE FROM `busy_opening_stock` WHERE `ss_code`='$csa_code'";
    $rmo=mysqli_query($dbc,$qmo);
	foreach($opening as $oo=>$ooo)
	{
		//print_r($master[0]);
		$qty1=$opening[$oo]['qty1'];
        $qty2=$opening[$oo]['qty2'];
        $opening_product_name=$opening[$oo]['MasterCode1'];
	     	$qpd1="SELECT alias FROM busy_masters WHERE code='$opening_product_name' AND `ss_code`='$csa_code' LIMIT 1";
	     	$rpd1=mysqli_query($dbc,$qpd1);
	     	$rowpd1=mysqli_fetch_assoc($rpd1);
	     	$product_code=$rowpd1['alias'];
	     	$qpd="SELECT id FROM tally_catalog_product WHERE itemcode='$product_code' LIMIT 1";
	     	$rpd=mysqli_query($dbc,$qpd);
	     	$rowpd=mysqli_fetch_assoc($rpd);
	     	$product_id=$rowpd['id'];
        $qm="INSERT INTO `busy_opening_stock`(`csa_id`, `ss_code`,`product_id`,`product_code`, `qty`, `rate`,`busy_master_code`,`server_date_time`) VALUES ('$csa_id','$csa_code','$product_id','$product_code','$qty1','$qty2','$opening_product_name',NOW())";
        $rm=mysqli_query($dbc,$qm);
	}

}

if(!empty($voucher_list))
{
	//print_r($voucher_list);
	$str=array();
	foreach($voucher_list as $co=>$coo)
	{
		//print_r($coo);
		$id=date('YmdHis').$dealer_id;
	    $ch_date=$voucher_list[$co]['Date'];
        $voucher_no=$voucher_list[$co]['VchNo'];
        $first_voucher_no=$voucher_list[$co]['VchNo'];
        $voucher_id=$voucher_list[$co]['VchCode'];
        $ss_voucher_id=$voucher_list[$co]['VchCode'];
        $reference=$voucher_list[$co]['REFERENCE'];
		$dealer_codes=$voucher_list[$co]['MasterCode1'];
		$qd1="SELECT alias FROM busy_masters WHERE code='$dealer_codes' AND `ss_code`='$csa_code' LIMIT 1";
	    $rd1=mysqli_query($dbc,$qd1);
	    $rowd1=mysqli_fetch_assoc($rd1);
	    $dealer_code=$rowd1['alias'];
	    if(empty($dealer_code)){
	    	$dealer_id=0;
	    }else{
		$qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
		$rdl=mysqli_query($dbc,$qdl);
		$rowdl=mysqli_fetch_assoc($rdl);
		$dealer_id=$rowdl['id'];
		}
		//$dealer_name=$voucher_list[$co]['PARTYLEDGERNAME'];
		$invoice_type_id=$voucher_list[$co]['VchType'];
		if($invoice_type_id=='9'){
				$invoice_type='1';
				$drcr="DR";
				$cr_dr="DR";
		}elseif($invoice_type_id=='2'){
				$invoice_type='4';
				$drcr="CR";
				$cr_dr="CR";
		}elseif($invoice_type_id=='3'){
				$invoice_type='2';
				$drcr="CR";
				$cr_dr="CR";
		}elseif($invoice_type_id=='10'){
				$invoice_type='6';
				$drcr="DR";
				$cr_dr="DR";
		}elseif($invoice_type_id=='8'){
				$invoice_type='8';
				$drcr="CR/DR";
				$cr_dr="CR";
		}elseif($invoice_type_id=='5'){
				$invoice_type='10';
				$drcr="CR/DR";
				$cr_dr="CR";
		}
		$amount=$voucher_list[$co]['Amount'];
		$product_list1=$voucher_list[$co]['details'];
		mysqli_query($dbc, "START TRANSACTION");
		$qcheck="SELECT id FROM busy_bill WHERE ss_voucher_id='$ss_voucher_id' AND csa_id='$csa_id' LIMIT 1";
		$rcheck=mysqli_query($dbc,$qcheck);
		$ncheck=mysqli_num_rows($rcheck);
		$rowcheck=mysqli_fetch_assoc($rcheck);
		$check_id=$rowcheck['id'];
		if($ncheck<=0){	
		$q="INSERT IGNORE `busy_bill`( `ch_no`,`first_ch_no`,`ch_date`, `vocher_id`,`ss_voucher_id`,`csa_code`, `csa_id`,`dealer_code`, `dealer_id`, `dealer_name`,`invoice_type`,`drcr`, `amount`, `amount_round`,`server_date_time`) VALUES ('$voucher_no','$first_voucher_no','$ch_date','$voucher_id','$ss_voucher_id','$csa_code','$csa_id','$dealer_code','$dealer_id','$dealer_name','$invoice_type','$drcr','$amount','$amount',NOW())";

			}else{
		$q="UPDATE `busy_bill` SET `ch_no`='$voucher_no',`first_ch_no`='$first_voucher_no',`ch_date`='$ch_date',`dealer_code`='$dealer_code',`dealer_id`='$dealer_id',`dealer_name`='$dealer_name',`invoice_type`='$invoice_type',`drcr`='$drcr',`amount`='$amount',`amount_round`='$amount',`server_date_time`=NOW() WHERE ss_voucher_id='$ss_voucher_id'";		
			}
		//echo $q;
		$result=mysqli_query($dbc,$q);
			if(!$result){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Table Error') ;
		}
		$rId = mysqli_insert_id($dbc); 
		$str=array();
		foreach($product_list1 as $cod=>$poo)
	     		{ 
	     	$product_name=$poo['product_code'];
	     	$qpd1="SELECT alias FROM busy_masters WHERE code='$product_name' AND `ss_code`='$csa_code' LIMIT 1";
	     	$rpd1=mysqli_query($dbc,$qpd1);
	     	$rowpd1=mysqli_fetch_assoc($rpd1);
	     	$product_code=$rowpd1['alias'];
	     	$qpd="SELECT id FROM tally_catalog_product WHERE itemcode='$product_code' LIMIT 1";
	     	$rpd=mysqli_query($dbc,$qpd);
	     	$rowpd=mysqli_fetch_assoc($rpd);
	     	$product_id=$rowpd['id'];
	     	$qty=$poo['Qty'];
	     	$qty2=$poo['Qty2'];
	     	$rate=$poo['Rate'];
	     	$gst=$poo['Amount'];
	     	$gst_amt=$poo['gst_amt'];
	     	$dis_per=$poo['dis_per'];
	     	$dis_amt=$poo['dis_amt'];
	     	$taxable_amt=$poo['Amount'];
	     	$item_for='GOPAL';
	     	if($qty<0)
	     	{
	     		$cr_dr='DR';
	     	}
	     	 	if($ncheck<=0){	
	     	$str[]="('$rId','$product_id','$product_name','$qty','$cr_dr','$rate','$gst','$gst_amt','$dis_per','$dis_amt','$taxable_amt','$item_for')";
	     	//print_r($str);
	     }else{
	     	$str[]="('$check_id','$product_id','$product_name','$qty','$cr_dr','$rate','$gst','$gst_amt','$dis_per','$dis_amt','$taxable_amt','$item_for')";
	     	//print_r($str);
	     }
	     		}
	     	
	
		$qdelete="DELETE FROM `busy_bill_details` WHERE `td_id`='$check_id'";
	    $rdelete=mysqli_query($dbc,$qdelete);
	      	$strs='';
	        $strs = implode(',', $str);
	     	$qdet="INSERT INTO `busy_bill_details`(`td_id`, `product_id`, `product_name`, `qty`, `cr_dr`, `rate`, `gst`, `gst_amt`, `dis_per`, `dis_amt`, `taxable_amt`, `item_for`) VALUES $strs";
	     	$resultdet=mysqli_query($dbc,$qdet);
		if(!$resultdet){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Details Table Error') ;
		}
	}
}
	     	

mysqli_commit($dbc);	
$resposnse=array('BUSYREQUEST'=>'Success');
echo $result=json_encode($resposnse);
?>
