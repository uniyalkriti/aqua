<?php
require_once('../admin/include/conectdb.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
$date_raw=date('Y-m-d');
$back_date=date('Y-m-d', strtotime('-90 day', strtotime($date_raw)));

global $dbc;

    $sale_qry = mysqli_query($dbc,"SELECT cp.name as product_name,usod.rate,usod.quantity,dealer.name as dealer_name,retailer_id as ret_id,retailer.`name` as ret_name,uso.order_id,uso.dealer_id,l5_name,uso.location_id,uso.date,call_status,usod.product_id     
    FROM user_sales_order uso INNER JOIN user_sales_order_details usod USING(order_id)
    INNER JOIN location_view lv ON lv.l5_id=uso.location_id INNER JOIN catalog_product cp ON cp.id=usod.product_id 
    INNER JOIN retailer ON retailer.id=uso.retailer_id INNER JOIN dealer ON dealer.id=uso.dealer_id 
    WHERE uso.user_id = '$user_id' AND `date`>='$back_date' AND `date`<='$date_raw' GROUP BY usod.id");
//echo "";exit;
 $num = mysqli_num_rows($sale_qry);

if($num>0){
    while($sale_res = mysqli_fetch_assoc($sale_qry)){
            $ret_id = $sale_res['ret_id'];
            $dealer_id = $sale_res['dealer_id'];
            $dealer_name = $sale_res['dealer_name'];
            $product_name = $sale_res['product_name'];
            $product_id = $sale_res['product_id'];
            $rate = $sale_res['rate'];
            $quantity = $sale_res['quantity'];
            $sale_value = ($sale_res['rate']*$sale_res['quantity']);
            $call_status = $sale_res['call_status'];
            $beat_id = $sale_res['location_id'];
            $beat_name = $sale_res['l5_name'];
            $retailer_name = $sale_res['ret_name'];
            $order_id = $sale_res['order_id'];
            $date = $sale_res['date'];
             
        $sale_data[] = array('dealer_name' =>$dealer_name,'dealer_id' =>$dealer_id,'retailer_id' => $ret_id,'retailer_name' => $retailer_name,'beat_id' =>$beat_id,'beat_name' =>$beat_name,'order_id' =>$order_id,'date' =>$date,'call_status'=>$call_status,'product_name'=>$product_name,'product_id'=>$product_id,'rate'=>$rate,'quantity'=>$quantity,'sale_value'=>$sale_value);
           
    }
}else{
    
     $sale_data[] = array('dealer_name' => NULL,'dealer_id' =>NULL,'retailer_id' =>NULL,'retailer_name' =>NULL,'beat_id' =>NULL,'beat_name' =>NULL,'order_id' =>NULL,'date' =>NULL,'call_status'=>NULL,'product_name'=>NULL,'product_id'=>NULL,'rate'=>NULL,'quantity'=>NULL,'sale_value'=>NULL);
}
//print_r($sale_data);exit;
$data = array('sale_data' => $sale_data);
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
