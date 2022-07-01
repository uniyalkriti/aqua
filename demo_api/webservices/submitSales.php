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
    if(isset($_GET['order'])) $order = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['order']))); else $order = 0;
    if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
    if(isset($_GET['rate'])) $rate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['rate']))); else $rate = 0;
    if(isset($_GET['p_qty'])) $quantity = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['p_qty']))); else $quantity = 0;
    
    if(isset($_GET['scheme'])) $scheme = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['scheme']))); else $scheme = 0;
    if(isset($_GET['sale_id'])) $sale_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['sale_id']))); else $sale_id = 0;
    
    $tablename="user_sales_order_details";
    $task="insert";
    //$arraydata[]="id='".$sale_id."'";
    $arraydata[]="order_id='".$order."'";
    $arraydata[]="product_id='".$product_id."'";
    $arraydata[]="rate='".$rate."'";
    $arraydata[]="quantity='".$quantity."'";
    $arraydata[]="scheme_qty='".$scheme."'";
    $condition="";
    $code="";
    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1)
        {
        echo "Y";
    }else{
        echo "N";
    }
    
}
?>
