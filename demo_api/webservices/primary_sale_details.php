<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');

//http://192.168.1.10/msell/webservices/submitSales.php?
//product_id=2&
//p_qty=5&
//rate=234&
//imei=1234567891234685&
//order=40140725180931

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
 $user_id=$user_res['id'];
     if(isset($_GET['orderid'])) $orderid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['orderid']))); else $orderid = 0;
    
    if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
    if(isset($_GET['quantity'])) $quantity = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['quantity']))); else $quantity = 0;
    
    if(isset($_GET['scheme'])) $scheme = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['scheme']))); else $scheme = 0;
    
    if(isset($_GET['expiry_date'])) $expiry_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['expiry_date']))); else $expiry_date = 0;
    
    if(isset($_GET['mfg_date'])) $mfg_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mfg_date']))); else $mfg_date = 0;
    
    if(isset($_GET['receive_date'])) $receive_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['receive_date']))); else $receive_date = 0;
    
    if(isset($_GET['batch_no'])) $batch_no = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['batch_no']))); else $batch_no = 0;
    
    if(isset($_GET['rate'])) $rate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['rate']))); else $rate = 0;
      if(isset($_GET['pr_rate']))echo $pr_rate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pr_rate']))); else $pr_rate = 0;
     if(isset($_GET['prime_id'])) $prime_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['prime_id']))); else $prime_id = 0;
    $order_no = date('Ymdhis');
    $tablename="user_primary_sales_order_details";
    $task="insert";
    $arraydata[]="id='".$prime_id."'";
    $arraydata[]="product_id='".$product_id."'";
    $arraydata[]="quantity='".$quantity."'";
    $arraydata[]="order_id= '".$order_no."'";
    $arraydata[]="scheme_qty= '".$scheme."'";
    $arraydata[]="expiry_date= '".$expiry_date."'";
    $arraydata[]="mfg_date= '".$mfg_date."'";
    $arraydata[]="receive_date = '".$receive_date."'";
    $arraydata[]="batch_no= '".$batch_no."'";
    $arraydata[]="rate= '".$rate."'";
    $arraydata[]="pr_rate= '".$pr_rate."'";
    $condition="";
    $code="";
    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1)
        {
        
        $q = "INSERT INTO catalog_product_details (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES ('$prime_id', '$product_id', '$batch_no' , '$quantity', '$rate', '$mfg_date', '$expiry_date',NOW())";
        $r = mysqli_query($dbc , $q);
        echo "Y";
    }
    else{
        echo "N";
    }
}
?>
