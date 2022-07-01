<?php
 require_once('../admin/include/conectdb.php');
 require_once('functions.php');

//http://192.168.1.12/msell-bscpaints/webservices/payment_enrollment.php?imei=xyz&amount=67890&pay_mode=0&dealer_id=1&retailer_id=1&location_id=1&cheque_number=123456&cheque_date=2014-09-18&bank_name=xyz&pay_date=2014-09-07&pay_time=10:48:26

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
    $user_id=$user_res['id'];
    if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;
    if(isset($_GET['location_id'])) $location_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['location_id']))); else $location_id = 0;
    if(isset($_GET['retailer_id'])) $retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id']))); else $retailer_id = 0;
    if(isset($_GET['pay_mode'])) $pay_mode = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pay_mode']))); else $pay_mode = '';
    
    if(isset($_GET['bank_name'])) $bank_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['bank_name']))); else $bank_name = '';
    if(isset($_GET['cheque_number'])) $cheque_number = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['cheque_number']))); else $cheque_number = '';
     if(isset($_GET['cheque_date'])) $cheque_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['cheque_date']))); else $cheque_date = '';
    if(isset($_GET['amount'])) $amount = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['amount']))); else $amount = '';
    
    if(isset($_GET['pay_date'])) $pay_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pay_date']))); else $pay_date = '';
    
     if(isset($_GET['pay_time'])) $pay_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pay_time']))); else $pay_time = '';
    
    
    $tablename="payment_enrollment";
    $task="insert";
    $arraydata[]="dealer_id='".$dealer_id."'";
    $arraydata[]="location_id='".$location_id."'";
    $arraydata[]="retailer_id='".$retailer_id."'";
    $arraydata[]="pay_mode='".$pay_mode."'";
    $arraydata[]="amount='".$amount."'";
    $arraydata[]="bank_name='".$bank_name."'";
    $arraydata[]="cheque_number='".$cheque_number."'";
    $arraydata[]="cheque_date='".$cheque_date."'";
    $arraydata[]="user_id='".$user_id."'";
    $arraydata[]="pay_date='".$pay_date."'";
    $arraydata[]="pay_time='".$pay_time."'";
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