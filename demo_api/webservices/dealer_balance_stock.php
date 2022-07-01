<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//TEST URL - http://localhost/msell2/dealer_balance_stock.php?imei=1234567891234567

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
 $user_id=$user_res['id'];
 if(isset($_GET['order_id'])) $orderid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['order_id']))); else $orderid = 0;
    
 if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
 if(isset($_GET['quantity'])) $quantity = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['quantity']))); else $quantity = 0;
    
 if(isset($_GET['scheme'])) $scheme = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['scheme']))); else $scheme = 0;
   
   $dealer_id = $_GET['dealer_id'];   
   $batch_no = $_GET['batch_no']; 
   $exp_date = $_GET['expiry_date'];  
   $mobile_datetime = $_GET['date_time']; 
   $sale_date = $_GET['date_time']; 
   $receive_date = $_GET['receive_date'];
   
if(!empty($product_id)){
$catalog_qry = mysqli_query($dbc,"select * from catalog_product  where id='".$product_id."'") or die(mysqli_error($dbc));
$catalog_res=  mysqli_fetch_assoc($catalog_qry);
$catalog_id = $catalog_res['catalog_id'];
$product_name = $catalog_res['name'];
$unit = $catalog_res['unit'];
$bp = $catalog_res['base_price'];
}
   $q = "INSERT INTO `dealer_bal_stock` (`id`, `name`,`catalog_id`,`unit`,`base_price`,`user_id`,`mobile_datetime`,`dealer_id`,`server_datetime`,`batch_no`,`exp_date`,`sale_date`, `receive_date`) VALUES (NULL, '$product_name','$catalog_id','$unit','$bp','$user_id','$mobile_datetime','$dealer_id',NOW(),'$batch_no','$exp_date','$sale_date', '$receive_date')";
    
   $r = mysqli_query($dbc,$q) ;
   if($r)
   {
       echo 'Y';
   }  
   else 
       {
       echo 'N';
   }
}
