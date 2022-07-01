<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
 $dealer_id = mysqli_real_escape_string($dbc, $_GET['dealer_id']);//exit;
//$dealer_id=$_SESSION[SESS.'data']['dealer_id']; 
//$stateid=$_SESSION[SESS.'data']['state_id'];        
//***************************  ARRAY LIST DEFINE  ****************************************//

$challan_order = array();
$challan_order_details = array(); 
$retailer= array(); 
$receive_order=array();
$receive_order_details=array();
$daily_dispatch=array();
$daily_dispatch_details=array();
$payment_collection=array();
$damage_order=array();
$damage_order_details=array();
$stock=array();
$purchase_order=array();
$purchase_order_details=array();
$product_rate_list=array();
$cases=array();
$dealer_target=array();
$user_sales_order=array();
$user_sales_order_details=array();
$catalog_product=array();
 //*************************** DEFINE  ARRAY LIST  END HERE ****************************************//   
    
$q_login = "SELECT id,dealer_status FROM dealer WHERE id='$dealer_id' LIMIT 0,1";
 // h1($query_person_login);
$dealer_qry = mysqli_query($dbc, $q_login);
$dealer_data=  mysqli_fetch_array($dealer_qry);
if(mysqli_num_rows($dealer_qry) > 0)
{
$dealer_status=$dealer_data['dealer_status'];
//echo $dealer_status;exit();

if($dealer_status == '1')  
{    
   
//################################################### DATA GET PROCESS   ###########################################################################    
   //******************************************* challan_order  *****************************//

  $q_clocal = "SELECT * FROM challan_order WHERE sync_status=1 AND ch_dealer_id='$dealer_id'";
    $r_clocal = mysqli_query($dbc,$q_clocal);
    $res_clocal=  mysqli_num_rows($r_clocal);
     if($res_clocal>0){
    
     while($row_cho = mysqli_fetch_assoc($r_clocal))
        {
           //$cho_id[]=$row_cho[''];
            $challan_order[]=$row_cho;
        }
     }

  //******************************************* challan_order_details  *****************************//

 $q_chdloc = "SELECT chd.* FROM challan_order_details chd INNER JOIN challan_order ch ON ch.id=chd.ch_id "
           . "WHERE ch.sync_status=1 AND ch_dealer_id='$dealer_id' ORDER BY ch_id ASC";

    $r_chdloc = mysqli_query($dbc,$q_chdloc);
    $res_chdlocal=  mysqli_num_rows($r_chdloc);
     if($res_chdlocal>0){
    
     while($row_chd = mysqli_fetch_assoc($r_chdloc))
        {
            $challan_order_details[]=$row_chd;
        }
     }
         
     //******************************************* retailer  *****************************//
    $qr = "SELECT * FROM retailer WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qr = mysqli_query($dbc,$qr);
    $res_qr=  mysqli_num_rows($r_qr);
     if($res_qr>0){
    
     while($row_ret = mysqli_fetch_assoc($r_qr))
        {
            $retailer[]=$row_ret;
        }
     } 
 
      //******************************************* receive_order  *****************************//
    $qrc = "SELECT * FROM receive_order WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qrc = mysqli_query($dbc,$qrc);
    $res_qrc=  mysqli_num_rows($r_qrc);
     if($res_qrc>0){
    
     while($row_rco = mysqli_fetch_assoc($r_qrc))
        {
          $receive_order[]=$row_rco;
        }
     } 
     
  //******************************************* receive_order_details  *****************************//
    $qrd = "SELECT receive_order_details.* FROM receive_order_details INNER JOIN receive_order USING(order_id) WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qrd = mysqli_query($dbc,$qrd);
    $res_qrd=  mysqli_num_rows($r_qrd);
     if($res_qrd>0){
    
     while($row_rcd = mysqli_fetch_assoc($r_qrd))
        {
          $receive_order_details[]=$row_rcd;
        }
     }
     
      //******************************************* damage_order  *****************************//
    $qrdo = "SELECT * FROM damage_order WHERE sync_status=1 AND ch_dealer_id='$dealer_id'";
    $r_qrdo = mysqli_query($dbc,$qrdo);
    $res_qrdo=  mysqli_num_rows($r_qrdo);
     if($res_qrdo>0){
    
     while($row_rcdo = mysqli_fetch_assoc($r_qrdo))
        {
          $damage_order[]=$row_rcdo;
        }
     }
     
     //******************************************* damage_order_details  *****************************//
    $qrdd = "SELECT damage_order_details.* FROM damage_order_details INNER JOIN damage_order ON damage_order.id=damage_order_details.ch_id WHERE damage_order.sync_status=1 AND ch_dealer_id='$dealer_id'";
    $r_qrdd = mysqli_query($dbc,$qrdd);
    $res_qrdd=  mysqli_num_rows($r_qrdd);
     if($res_qrdd>0){
    
     while($row_rcdd = mysqli_fetch_assoc($r_qrdd))
        {
          $damage_order_details[]=$row_rcdd;
        }
     }
     
      //******************************************* payment_collection  *****************************//
    $qrpc = "SELECT * FROM payment_collection  WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qrpc = mysqli_query($dbc,$qrpc);
    $res_qrpc=  mysqli_num_rows($r_qrpc);
     if($res_qrpc>0){
    
     while($row_rcpc = mysqli_fetch_assoc($r_qrpc))
        {
          $payment_collection[]=$row_rcpc;
        }
     }
     
     
     //******************************************* daily_dispatch  *****************************//
    $qdd = "SELECT * FROM daily_dispatch WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qdd = mysqli_query($dbc,$qdd);
    $res_qdd=  mysqli_num_rows($r_qdd);
     if($res_qdd>0){
    
     while($row_dd = mysqli_fetch_assoc($r_qdd))
        {
          $daily_dispatch[]=$row_dd;
        }
     }
     
     //******************************************* daily_dispatch_details  *****************************//
    $qrddd = "SELECT daily_dispatch_details.* FROM daily_dispatch_details INNER JOIN daily_dispatch USING(dispatch_id) WHERE daily_dispatch.sync_status=1 AND dealer_id='$dealer_id'";
    $r_qrddd = mysqli_query($dbc,$qrddd);
    $res_qrddd=  mysqli_num_rows($r_qrddd);
     if($res_qrddd>0){
    
     while($row_rcddd = mysqli_fetch_assoc($r_qrddd))
        {
          $daily_dispatch_details[]=$row_rcddd;
        }
     }
     
     //******************************************* stock  *****************************//
    $qst = "SELECT * FROM stock WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qst = mysqli_query($dbc,$qst);
     $res_qst=  mysqli_num_rows($r_qst);//exit;
   //  if($res_qst>0){
   // echo "hiii";exit;
    
     $stock_data=array();
     while($row_st = mysqli_fetch_assoc($r_qst))
        {
         
            $stock_data[]=$row_st;
        }
    // }
// print_r($stock_data);exit;
     //******************************************* Purchase Order  *****************************//
    $qpo = "SELECT * FROM `purchase_order` WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qpo = mysqli_query($dbc,$qpo);
    $res_qpo=  mysqli_num_rows($r_qpo);
     if($res_qpo>0){
    
     while($row_po = mysqli_fetch_assoc($r_qpo))
        {
          $purchase_order[]=$row_po;
        }
     }
     
     //******************************************* Purchase Order Details  *****************************//
    $qrpod = "SELECT purchase_order_details.* FROM purchase_order_details INNER JOIN purchase_order USING(order_id) WHERE purchase_order.sync_status=1 AND dealer_id='$dealer_id'";
    $r_qrpod = mysqli_query($dbc,$qrpod);
    $res_qrpod=  mysqli_num_rows($r_qrpod);
     if($res_qrpod>0){
    
     while($row_rpod = mysqli_fetch_assoc($r_qrpod))
        {
          $purchase_order_details[]=$row_rpod;
        }
     }
     
     //******************************************* Product Rate List  *****************************//
    $qprl = "SELECT * FROM `product_rate_list` AND state_id=$stateid";
    $r_qprl = mysqli_query($dbc,$qprl);
    $res_qprl=  mysqli_num_rows($r_qprl);
     if($res_qprl>0){
    
     while($row_prl = mysqli_fetch_assoc($r_qprl))
        {
          $product_rate_list[]=$row_prl;
        }
     }
     //******************************************* Cases  *****************************//
    $qcases = "SELECT * FROM `cases` ";
    $r_qcases = mysqli_query($dbc,$qcases);
    $res_qcases=  mysqli_num_rows($r_qcases);
     if($res_qcases>0){
    
     while($row_cases = mysqli_fetch_assoc($r_qcases))
        {
          $cases[]=$row_cases;
        }
     }

     //******************************************* Dealer Target  *****************************//
    $qdt = "SELECT * FROM `dealer_target` WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_qdt = mysqli_query($dbc,$qdt);
    $res_qdt=  mysqli_num_rows($r_qdt);
     if($res_qdt>0){
    
     while($row_dt = mysqli_fetch_assoc($r_qdt))
        {
          $dealer_target[]=$row_dt;
        }
     }
     
     //******************************************* User Sales Order  *****************************//
    $quso = "SELECT * FROM `user_sales_order` WHERE sync_status=1 AND dealer_id='$dealer_id'";
    $r_quso = mysqli_query($dbc,$quso);
    $res_quso=  mysqli_num_rows($r_quso);
     if($res_quso>0){
    
     while($row_uso = mysqli_fetch_assoc($r_quso))
        {
          $user_sales_order[]=$row_uso;
        }
     }
     
     //******************************************* User Sales Order Details  *****************************//
    $qrusod = "SELECT user_sales_order_details.* FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order.order_id=user_sales_order_details.order_id WHERE user_sales_order.sync_status=1 AND dealer_id='$dealer_id'";
    $r_qusod = mysqli_query($dbc,$qrusod);
    $res_qrusod=  mysqli_num_rows($r_qusod);
     if($res_qrusod>0){
    
     while($row_usod = mysqli_fetch_assoc($r_qusod))
        {
          $user_sales_order_details[]=$row_usod;
        }
     }
     
     //******************************************* Cases  *****************************//
    // $qcatp = "SELECT * FROM `catalog_product` ";
    // $r_qcatp = mysqli_query($dbc,$qcatp);
    // $res_qcatp=  mysqli_num_rows($r_qcatp);
    //  if($res_qcatp>0){
    
    //  while($row_catp = mysqli_fetch_assoc($r_qcatp))
    //     {
    //       $catalog_product[]=$row_catp;
    //     }
    //  }
    // print_r($stock);exit;
 
$final_data=array("dealer_id"=>"$dealer_id"
        ,"challan_order"=>$challan_order
        ,"challan_order_details"=>$challan_order_details
        ,"retailer"=>$retailer                            
        ,"daily_dispatch"=>$daily_dispatch
        ,"daily_dispatch_details"=>$daily_dispatch_details
        ,"payment_collection"=>$payment_collection
        ,"damage_order"=>$damage_order
        ,"damage_order_details"=>$damage_order_details
        ,"stock"=>$stock_data
         ,"receive_order"=>$receive_order
         ,"receive_order_details"=>$receive_order_details
         ,"purchase_order"=>$purchase_order
        ,"purchase_order_details"=>$purchase_order_details
         ,"product_rate_list"=>$product_rate_list
        ,"cases"=>$cases
        ,"dealer_target"=>$dealer_target
        ,"user_sales_order"=>$user_sales_order
       ,"user_sales_order_details"=>$user_sales_order_details
        );
//print_r($final_data);exit;

echo $json_data = json_encode($final_data); exit;
    }else{
        echo json_encode(array('msg'=>'status wrong'));
    }

}else{
     echo json_encode(array('msg'=>'no rows'));
}
//**********************************************  GET PROCESS END HERE    *********************************
##########################################################################################################



//####################################################### SYNC PROCESS  ##########################################################
//API Url
//$url = 'http://192.168.0.124/msell-dsgroup-dms/webservices/server_receive_data.php';

////Initiate cURL.
//$ch = curl_init($url);
////$jsonData = array(
////    'username' => 'MyUsername',
////    'password' => 'MyPassword'
////);
////Encode the array into JSON.
////$jsonDataEncoded = json_encode($jsonData);
//
////Tell cURL that we want to send a POST request.
//curl_setopt($ch, CURLOPT_POST, 1);
////Attach our encoded JSON string to the POST fields.
//$final_json_data  = str_replace("'","",$json_data);
//
//curl_setopt($ch, CURLOPT_POSTFIELDS, $final_json_data);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
////Set the content type to application/json
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//
////Execute the request
// echo $result = curl_exec($ch);
//if($result){
////echo"<pre>";
////print_r($result);
////echo"</pre>";
// }
?>