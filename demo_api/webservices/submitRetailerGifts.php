<?php
// die(mysqli_error())
// TEST URL - http://localhost/PhpProject2/webservices/submitRetailergifts.php?order_id=20&gift_id=20140611&quantity=30

//submitRetailerGifts.php?
//sale_id=1&
//gift=T-Shirt&
//qty=2&
//gift_ido=5&
//cust_id=40&
//orderid=40140728190953&
//imei=1234567891234685
        
require_once('../admin/include/conectdb.php');
require_once('functions.php');

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){

    $user_id=$user_res['id'];
    if(isset($_GET['sale_id'])) $sale_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['sale_id']))); else $sale_id = 0;
    if(isset($_GET['orderid'])) $order_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['orderid']))); else $order_id = 0;
    if(isset($_GET['gift_id'])) $gift_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['gift_id']))); else $gift_id = 0;
    if(isset($_GET['qty'])) $quantity = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['qty']))); else $quantity = 0;

    $tablename="user_retailer_gift_details";
    $task="insert";
    $arraydata[]="id='".$sale_id."'";
    $arraydata[]="order_id='".$order_id."'";
    $arraydata[]="gift_id='".$gift_id."'";
    $arraydata[]="quantity='".$quantity."'";
    $condition="";
    $code="";

    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1){
        echo "Y";
    }else{
        echo "N";
    }
}
?>
