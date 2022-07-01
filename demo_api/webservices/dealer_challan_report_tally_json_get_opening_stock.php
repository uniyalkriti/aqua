<?php
$postData = file_get_contents('php://input');
$postData=str_replace("'"," ",$postData);
//print_r($postData);exit;
//$postData='';
$array1 = json_decode($postData,TRUE);
$array=$array1['ENVELOPE'];
//print_r($postData);//exit;
date_default_timezone_set('Asia/Kolkata');
$csa_code=$array['TALLYREQUEST']['DEALERCODE'];
$stock_list=$array['TALLYREQUEST']['STOCKLIST'];
$from_date=$array['TALLYREQUEST']['FROMDATE'];
	    $start=explode('-',$from_date);
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
        $from_date=$y1."-".$m1."-".$d1;
        $to_date=$array['TALLYREQUEST']['TODATE'];
	    $end=explode('-',$to_date);
	   // print_r($end);
        if($end[0]<10){
        $d2="0".$end[0];
        }else{
        $d2=$end[0];
        }
        if($end[1]<10){
       	$m2="0".$end[1];
        }else{
        $m2=$end[1];
        }
        $y2=$end[2];
        $to_date=$y2."-".$m2."-".$d2;
//print_r($stock_list);exit;
require_once('../admin/include/conectdb.php');
global $dbc;
$qd="SELECT c_id AS id FROM csa WHERE csa_code='$csa_code' LIMIT 1";
$rd=mysqli_query($dbc,$qd);
$rowd=mysqli_fetch_assoc($rd);
$dealer_id=$rowd['id'];
if(!empty($stock_list))
{
	 $cosale=count($stock_list);
	$product_list='';
	//mysqli_query($dbc, "START TRANSACTION");
	$qcheck="DELETE FROM `tally_opening_stock` WHERE `from_date`='$from_date' AND `csa_id`='$dealer_id'";
	$rcheck=mysqli_query($dbc,$qcheck);
	$str=array();
	foreach($stock_list as $co=>$coo)
	{
		//print_r($coo);
		$id=date('YmdHis').$dealer_id;
	     	$product_name=$coo['NAME'];
	     	$product_code=$coo['CODE'];
	     	$product_code=str_replace(" ","",$product_code);
	     	$qpd="SELECT id FROM tally_catalog_product WHERE itemcode='$product_code' LIMIT 1";
	     	$rpd=mysqli_query($dbc,$qpd);
	     	$rowpd=mysqli_fetch_assoc($rpd);
	     	$product_id=$rowpd['id'];
	     	$opening=$coo['OPENING'];
	     	$opening=str_replace("(-)","-",$opening);
	     	
	     	    $qdet="INSERT IGNORE `tally_opening_stock`(`csa_id`, `product_id`, `product_name`, `from_date`, `to_date`, `opening`, `server_date_time`) VALUES ('$dealer_id','$product_id','$product_name','$from_date','$to_date','$opening',NOW())";	
	     	$resultdet=mysqli_query($dbc,$qdet);
		if(!$resultdet){
			mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tally Demo Stock Table Error') ;
		}	
}
mysqli_commit($dbc);	
$resposnse=array('TALLYREQUEST'=>'Success');
echo $result=json_encode($resposnse);
}
?>
