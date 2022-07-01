<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');

//http://192.168.1.10/msell/webservices/submitSales.php?
//product_id=2&
//p_qty=5&
//rate=234&
//imei=1234567891234685&
//order=40140725180931

//Note : - The field expiry_date, mfg_date and receive_date ki field badhani hai

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
 $user_id=$user_res['id'];
     if(isset($_GET['orderid'])) $orderid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['orderid']))); else $orderid = 0;
     if(isset($_GET['sale_date'])) $selected_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['sale_date']))); else $selected_date = 0;
     
     if(isset($_GET['date_time'])) $date_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date_time']))); else $date_time = 0;
     
     if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;
    
     if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
    if(isset($_GET['quantity'])) $quantity = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['quantity']))); else $quantity = 0;
    
    if(isset($_GET['scheme'])) $scheme = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['scheme']))); else $scheme = 0;
    
    if(isset($_GET['ttl_prod_qty'])) $totalqty = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['ttl_prod_qty']))); else $totalqty = 0;
    
    $order_no = date('Ymdhis');
    $tablename="user_primary_sales_order";
    $task="insert";
    $arraydata[]="dealer_id='".$dealer_id."'";
    $arraydata[]="created_person_id='".$user_id."'";
    $arraydata[]="created_date= NOW()";
    $arraydata[]="order_id= '".$order_no."'";
    $arraydata[]="id = '".$order_no."'";
    $arraydata[]="sale_date= '".$selected_date."'";
    $arraydata[]="date_time= '".$date_time."'";
    $arraydata[]="company_id= '1'";
    $condition="";
    $code="";
    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1)
        {
        echo "Y";
    }
    else{
        echo "N";
    }
}
?>
