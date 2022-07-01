<?php

date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';


if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    echo"Request method must be POST!";exit;
    //throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
     echo"Content type must be: application/json";exit;
   // throw new Exception('Content type must be: application/json');
}

//Receive the RAW post data.
 $content = trim(file_get_contents("php://input"));

//Attempt to decode the incoming RAW post data from JSON.
$update_response = json_decode($content, true);

//If json_decode failed, the JSON is invalid.
if(!is_array($update_response)){
     echo"Received content contained invalid JSON!";exit;
    //throw new Exception('Received content contained invalid JSON!');
}
if(!empty($update_response)){

  $dealer_id=$update_response[dealer_id];
  //------------------- Update Table -------------------------
  $up_co=$update_response[challan_order];
  $run_up_co= mysqli_query($dbc, $up_co);
  
  //------------------- Update Table -------------------------
  $up_ret=$update_response[retailer];
  $run_up_ret= mysqli_query($dbc, $up_ret);
  
  //------------------- Update Table -------------------------
  $up_dd=$update_response[daily_dispatch];
  $run_up_dd= mysqli_query($dbc, $up_dd);
  
  //------------------- Update Table -------------------------
  $up_pc=$update_response[payment_collection];
  $run_up_pc= mysqli_query($dbc, $up_pc);
  
  //------------------- Update Table -------------------------
  $up_do=$update_response[damage_order];
  $run_up_do= mysqli_query($dbc, $up_do);
  
  //------------------- Update Table -------------------------
  $up_stk=$update_response[stock];
  $run_up_stk= mysqli_query($dbc, $up_stk);
  
  //------------------- Update Table -------------------------
  $up_ro=$update_response[receive_order];
  $run_up_ro= mysqli_query($dbc, $up_ro);
  
  //------------------- Update Table -------------------------
  $up_po=$update_response[purchase_order];
  $run_up_po= mysqli_query($dbc, $up_po);
  //------------------- Update Table -------------------------
  $up_uso=$update_response[user_sales_order];
  $run_up_uso= mysqli_query($dbc, $up_uso);
    echo"Y";
}else{
    echo"N";
}



?>