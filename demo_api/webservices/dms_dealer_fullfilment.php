<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealerid'])));
 $report_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['report_date'])));
//  $fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
// $todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));

global $dbc;
$myobj = new sale();
//INNER JOIN user_sales_order_details usod ON uso.order_id=usod.order_id
$order_id = '';
if(!empty($dealer_id)){
    $qrty="select distinct(retailer_id) as ret_id,`name` as ret_name,uso.order_id as order_id_id,uso.dealer_id,uso.location_id,uso.date   
    from user_sales_order uso
    INNER JOIN retailer ON retailer.id=uso.retailer_id 
    WHERE date_format(date,'%Y-%m-%d')='$report_date'  AND uso.dealer_id = '$dealer_id' 
    AND uso.call_status='1'
    AND uso.order_id NOT IN (SELECT order_id as uso_order_id FROM fullfillment_order group by uso_order_id)
    ";
    $retailer_qry = mysqli_query($dbc,$qrty);
}else{    
  $qrty= "select distinct(retailer_id) as ret_id,`name` as ret_name,uso.order_id as order_id_id,uso.dealer_id,uso.location_id,uso.date   
    from user_sales_order uso
    INNER JOIN retailer ON retailer.id=uso.retailer_id 
    WHERE uso.`date`='$report_date' AND uso.user_id = '$user_id' AND uso.call_status='1'
    AND uso.order_id NOT IN (SELECT order_id as uso_order_id FROM fullfillment_order group by uso_order_id)
    ";
    $retailer_qry = mysqli_query($dbc,$qrty);
}
  // h1($qrty); //exit;GROUP BY uso.retailer_id

$num = mysqli_num_rows($retailer_qry);
// print_r($num);die;

//$retailer = array();
//$products = array();
if($num>0){
    while($retailer_res = mysqli_fetch_assoc($retailer_qry)){
// print_r($retailer_res);die;

            $ret_id = $retailer_res['ret_id'];
            $dealer_id = $retailer_res['dealer_id'];
            $location_id = $retailer_res['location_id'];
            $name = $retailer_res['ret_name'];
            $order_id = $retailer_res['order_id_id']; 
            $date = $retailer_res['date'];
             $query = "SELECT usod.product_id,usod.case_qty,usod.rate,
                cp.`name`,usod.quantity,usod.remaining_qty,lv.l1_id,
                 uso.total_sale_value as sale_value,'0' as ff_amount
                  FROM user_sales_order_details usod 
                  INNER JOIN user_sales_order as uso ON uso.order_id=usod.order_id  
                  INNER JOIN location_view as lv ON lv.l7_id=uso.location_id  
            INNER JOIN catalog_product cp ON cp.id=usod.product_id  
            WHERE uso.order_id='$order_id'";            
           // h1($query);
              $sdate=date("Y-m-d");
            $res = mysqli_query($dbc, $query);
           // $rows = array();
            while($rows1 = mysqli_fetch_assoc($res)){
                $rows['retailer_id'] = $ret_id;
                $rows['order_id'] = $order_id;
                $rows['product_id'] = $rows1['product_id'];
                $rows['name'] = $rows1['name'];
                $rows['sale_value'] = $rows1['sale_value'];
                $rows['ff_amount'] = $rows1['ff_amount'];
                $rows['rate'] = $rows1['rate'];
                //$rows['piece_rate'] = $rows1['piece_rate'];
              //  $rows['case_product_mrp'] = $myobj->get_case_product_mrp($rows1['l2_id'],$rows1['product_id']);
              //  $rows['piece_product_mrp'] = $myobj->get_piece_product_mrp($rows1['l2_id'],$rows1['product_id']);
                // $cas = $rows1['case_qty'];        
                 $rows['case_qty'] = $rows1['case_qty']; 
                $rows['quantity']  =  $rows1['quantity'];
                $rows['remaining_qty']  = $rows1['remaining_qty'];//scheme_qty
             
              //  print_r($rows);
                $productsnew[$order_id][] = $rows;
            }
          //  print_r($products);
        $retailer[] = array('id' => $ret_id,'name' => $name,'dealer_id' =>$dealer_id,'location_id' =>$location_id,'order_id' =>$order_id,'date' =>$date,'products'=>$productsnew[$order_id]);
        // print_r($retailer);
        $products='';
    }
}
else{
    $products[] = array('product_id'=>0,'rate'=>0,'name'=>0,'quantity'=>0,'sale_value'=>0,'ff_amount'=>0,'retailer_id'=>0,'order_id'=>0);
    $retailer[] = array('id' => 0,'name' =>0,'dealer_id' =>0,'location_id' =>0,'order_id'=>0,'products'=>$products);
}
//print_r($retailer);
 
$data[] = array('retailer' => $retailer);
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
