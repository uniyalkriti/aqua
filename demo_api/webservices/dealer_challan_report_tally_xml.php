<?php
ini_set('max_execution_time', -1); 
$postData = file_get_contents('php://input');
$xml = simplexml_load_string($postData, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
$from_date=$array['TALLYREQUEST']['FROMDATE'];
$start=explode('-',$from_date);
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
$to_date=$array['TALLYREQUEST']['TODATE'];
$end=explode('-',$to_date);
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
$from_date=$y1."-".$m1."-".$d1;
$to_date=$y2."-".$m2."-".$d2;
// $myfile = fopen("new.txt", "wr") or die("Unable to open file!");
// fwrite($myfile, $postData);
// fclose($myfile);
header('Content-type: text/xml');
require_once('../admin/include/conectdb.php');
global $dbc;
$dealer_code=$array['TALLYREQUEST']['DEALERCODE'];
$qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
		$rdl=mysqli_query($dbc,$qdl);
		$rowdl=mysqli_fetch_assoc($rdl);
		$dealer_id=$rowdl['id'];
$q_dealer_state="SELECT l2_id FROM dealer_location_rate_list AS dlrl INNER JOIN location_view AS lv ON lv.l5_id=dlrl.location_id WHERE dealer_id='$dealer_id' LIMIT 1";
$r_dealer_state=mysqli_query($dbc,$q_dealer_state);
$dealer_state=mysqli_fetch_assoc($r_dealer_state);
$str = '<?xml version="1.0" encoding="UTF-8"?>';
$str.='<TallyRequest>';
$q = "SELECT ch_retailer_id AS retailer_id,retailer.name AS party_name,challan_order.id,challan_order.discount_per,challan_order.ch_dealer_id,challan_order.ch_user_id,DATE_FORMAT(challan_order.date_added,'%d-%m-%Y') AS date_added,challan_order.ch_no,challan_order.discount_amt,challan_order.amount, DATE_FORMAT(ch_date, '%d-%m-%Y') AS ch_date,(SELECT SUM(taxable_amt) FROM challan_order_details WHERE ch_id=challan_order.id) as tamount FROM challan_order INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id WHERE DATE_FORMAT(`ch_date`,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(`ch_date`,'%Y-%m-%d')<='$to_date' AND ch_dealer_id='$dealer_id'";
$r=mysqli_query($dbc,$q);
while($row=mysqli_fetch_object($r)){
$id1=$row->id;
$rid=$row->retailer_id;
$q_retailer_state="SELECT l2_id FROM retailer AS rl INNER JOIN location_view AS lv ON lv.l5_id=rl.location_id WHERE rl.id='$rid' LIMIT 1";
$r_retailer_state=mysqli_query($dbc,$q_retailer_state);
$retailer_state=mysqli_fetch_assoc($r_retailer_state);
$str.= '<Voucher>';
$q1 = "SELECT challan_order_details.*, "
                    . "catalog_product.name AS product_name,"
                    . "catalog_product.hsn_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " WHERE  ch_id = '$id1' GROUP BY challan_order_details.id";
$rs1=mysqli_query($dbc,$q1);  
$cgst_amt_total=0;
$sgst_amt_total=0;
$tot_amt=0;
while($row1 = mysqli_fetch_object($rs1)){
$item_amt=($row1->qty*$row1->product_rate)-$row1->cd_amt-$row1->dis_amt;
$tax1=($row1->tax)/2;
$cgst1=round((($item_amt*$tax1)/100),2);
$sgst1=round((($item_amt*$tax1)/100),2);
$cgst_amt_total=$cgst_amt_total+$cgst1;
$sgst_amt_total=$sgst_amt_total+$sgst1;
$tot_amt=$tot_amt+($item_amt+$cgst1+$sgst1);
$str.= '<Item>';
$str.= '<product_id>'.$row1->product_id. '</product_id>';
$str.= '<product_name>'.$row1->product_name. '</product_name>';
$str.= '<hsn_code>'.$row1->hsn_code. '</hsn_code>';
$str.= '<qty>'.$row1->qty. '</qty>';
$str.= '<product_rate>'.$row1->product_rate. '</product_rate>';
$str.= '<mrp>'.$row1->mrp. '</mrp>';
$str.= '<gst>'.$row1->tax. '</gst>';
$str.= '<gst_amt>'.$row1->vat_amt. '</gst_amt>';
$str.= '<cd_type>'.$row1->cd_type. '</cd_type>';
$str.= '<cd>'.$row1->cd. '</cd>';
$str.= '<cd_amt>'.$row1->cd_amt. '</cd_amt>';
$str.= '<dis_type>'.$row1->dis_type. '</dis_type>';
$str.= '<dis_percent>'.$row1->dis_percent. '</dis_percent>';
$str.= '<dis_amt>'.$row1->dis_amt. '</dis_amt>';
$str.= '<item_amt>'.$item_amt. '</item_amt>';
$str.= '<taxable_amt>'.$row1->taxable_amt. '</taxable_amt>';
$str.= '</Item>';
}
$cgst=$cgst_amt_total;
$sgst=$sgst_amt_total;
$igst=$cgst+$sgst;
if($dealer_state==$retailer_state){
$str.= '<LedgerEntry>';
$str.= '<LedgerName>'."CGST". '</LedgerName>';
$str.= '<Amount>'.$cgst. '</Amount>';
$str.= '</LedgerEntry>';
$str.= '<LedgerEntry>';
$str.= '<LedgerName>'."SGST". '</LedgerName>';
$str.= '<Amount>'.$sgst. '</Amount>';
$str.= '</LedgerEntry>';
$str.= '<LedgerEntry>';
$str.= '<LedgerName>'."Discount". '</LedgerName>';
$str.= '<Amount>'.$row->discount_amt. '</Amount>';
$str.= '</LedgerEntry>';	
}else{
$str.= '<LedgerEntry>';
$str.= '<LedgerName>'."IGST". '</LedgerName>';
$str.= '<Amount>'.$igst. '</Amount>';
$str.= '</LedgerEntry>';
$str.= '<LedgerEntry>';
$str.= '<LedgerName>'."Discount". '</LedgerName>';
$str.= '<Amount>'.$row->discount_amt. '</Amount>';
$str.= '</LedgerEntry>';
}
$str.= '<id>'.$row->id. '</id>';
$str.= '<party_name>'.$row->party_name. '</party_name>';
$str.= '<vocher_no>'.$row->ch_no. '</vocher_no>';
$str.= '<tamount>'.$tot_amt. '</tamount>';
$str.= '<vdate>'.$row->ch_date. '</vdate>';
$str.= '</Voucher>';
}
$str.='</TallyRequest>';
echo $str;  
// $myfile = fopen("new1.txt", "wr") or die("Unable to open file!");
// fwrite($myfile, $str);
// fclose($myfile);
?>
