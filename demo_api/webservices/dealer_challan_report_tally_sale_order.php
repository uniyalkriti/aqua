<?php
ini_set('max_execution_time', -1); 
// $postData = file_get_contents('php://input');
// $array1 = json_decode($postData,TRUE);
// $array=$array1['ENVELOPE'];
// $from_date=$array['TALLYREQUEST']['FROMDATE'];
// $start=explode('-',$from_date);
// if($start[0]<10){
// $d1="0".$start[0];
// }else{
// $d1=$start[0];
// }
// if($start[1]<10){
// $m1="0".$start[1];
// }else{
// $m1=$start[1];
// }
// $y1=$start[2];
// $to_date=$array['TALLYREQUEST']['TODATE'];
// $end=explode('-',$to_date);
// if($end[0]<10){
// $d2="0".$end[0];
// }else{
// $d2=$end[0];
// }
// if($end[1]<10){
// $m2="0".$end[1];
// }else{
// $m2=$end[1];
// }
// $y2=$end[2];
// $from_date=$y1."-".$m1."-".$d1;
// $to_date=$y2."-".$m2."-".$d2;
require_once('../admin/include/conectdb.php');
global $dbc;
//$dealer_code=$array['TALLYREQUEST']['DEALERCODE'];
// $qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
// 		$rdl=mysqli_query($dbc,$qdl);
// 		$rowdl=mysqli_fetassoc($rdl);
// 		$dealer_id=$rowdl['id'];
		$dealer_id='4';
		$from_date='2018-09-17';
        $to_date='2018-09-17';
$out=array();
//$str.='<TallyRequest>';
$q = "SELECT retailer_id AS retailer_id,retailer.name AS party_name,user_sales_order.id,user_sales_order.dealer_id,user_sales_order.user_id,user_sales_order.order_id,user_sales_order.amount, DATE_FORMAT(date, '%d-%m-%Y') AS date,(SELECT SUM(rate*quantity) FROM user_sales_order_details AS usod WHERE usod.order_id=user_sales_order.order_id) as tamount FROM user_sales_order INNER JOIN retailer ON retailer.id=user_sales_order.retailer_id WHERE DATE_FORMAT(`date`,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(`date`,'%Y-%m-%d')<='$to_date' AND user_sales_order.dealer_id='$dealer_id'";
$r=mysqli_query($dbc,$q);
$i=0;
while($row=mysqli_fetch_object($r)){
$id1=$row->order_id;
$rid=$row->retailer_id;
$out['Voucher'][$i]['id']=$row->id;
$out['Voucher'][$i]['party_name']=$row->party_name;
$out['Voucher'][$i]['order_id']=$row->order_id;
$out['Voucher'][$i]['date']=$row->date;
$out['Voucher'][$i]['tamount']=$row->tamount;
$q1 = "SELECT user_sales_order_details.*, "
                    . "catalog_product.name AS product_name,"
                    . "catalog_product.hsn_code FROM "
                    . "user_sales_order_details INNER JOIN catalog_product ON catalog_product.id = user_sales_order_details.product_id "
                    . " WHERE  order_id = '$id1' GROUP BY user_sales_order_details.id";
$rs1=mysqli_query($dbc,$q1);  
$cgst_amt_total=0;
$sgst_amt_total=0;
$tot_amt=0;
$j=0;
$outi=array();
while($row1 = mysqli_fetch_object($rs1)){
$item_amt=($row1->qty*$row1->product_rate);
$tax1=($row1->tax)/2;
$cgst1=round((($item_amt*$tax1)/100),2);
$sgst1=round((($item_amt*$tax1)/100),2);
$cgst_amt_total=$cgst_amt_total+$cgst1;
$sgst_amt_total=$sgst_amt_total+$sgst1;
$tot_amt=$tot_amt+$item_amt;
$outi[$j]['product_id']=$row1->product_id;
$outi[$j]['product_name']=preg_replace('/\s+/', ' ', $row1->product_name);
$outi[$j]['hsn_code']=$row1->hsn_code;
$outi[$j]['qty']=$row1->quantity;
$outi[$j]['product_rate']=$row1->rate;
$out['Voucher'][$i]['Item']=$outi;
$j++;
}
//$data['TallyRequest'] = $out;
$data = $out;
$i++;
}
header('Content-Type: application/json');
$data = json_encode($data);
echo $data;
?>
