<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');

//http://192.168.1.10/msell/webservices/submitSalesSummary.php?
//dealer_id=1&
//product_id=2&
//retailer_id=40&
//date=2014-07-25&
//imei=1234567891234685&
//location_id=2&
//order=40140725180931&
//time=18:09:31&
//productive=true&
//override=0

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$user_id=$user_res['id'];
$num = mysqli_num_rows($user_qry);
if($num>0){

    $user_id=$user_res['id'];
    if(isset($_GET['order'])) $order = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['order']))); else $order = 0;
    if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;
    if(isset($_GET['location_id'])) $location_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['location_id']))); else $location_id = 0;
    if(isset($_GET['retailer_id'])) $retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id']))); else $retailer_id = 0;
    if(isset($_GET['productive'])) $productive = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['productive']))); else $productive = 0;
    if(isset($_GET['date'])) $date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $date = 0;
    if(isset($_GET['time'])) $time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['time']))); else $time = 0;
    if(isset($_GET['override'])) $override = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['override']))); else $override = 0;
    if(isset($_GET['lat_lng'])) $lat_lng = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['lat_lng']))); else $lat_lng = 0;    
    if(isset($_GET['image_name'])) $image_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['image_name']))); else $image_name = "";        
    if(isset($_GET['mccmnclatcellid'])) $mccmnclatcellid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mccmnclatcellid']))); else $mccmnclatcellid = 0;   
   if(isset($_GET['track_address'])) $track_address = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['track_address']))); else $track_address = 0;

    $tablename="user_sales_order";
    $task="insert";
    $arraydata[]="id='".$order."'";
    $arraydata[]="order_id='".$order."'";
    $arraydata[]="user_id='".$user_res['id']."'";
    $arraydata[]="dealer_id='".$dealer_id."'";
    $arraydata[]="location_id='".$location_id."'";
    $arraydata[]="retailer_id='".$retailer_id."'";
    $arraydata[]="call_status='".$productive."'";
    $arraydata[]="date='".$date."'";
    $arraydata[]="time='".$time."'";
    $arraydata[]="image_name='".$image_name."'";
    $arraydata[]="override_status='".$override."'";    
    $arraydata[]="lat_lng='".$lat_lng."'";
    $arraydata[]="mccmnclatcellid='".$mccmnclatcellid."'";
    $arraydata[]="track_address='".$track_address."'";
    $arraydata[]="company_id='1'";
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
