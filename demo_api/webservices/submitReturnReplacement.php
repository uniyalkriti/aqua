<?php 
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('functions.php'); 
//test_url://localhost/msell-ambeygroup/webservices/SubmitReturnReplacement.php?imei=&replace_id=1&dis_code=1&ret_code=2&prod_qty=4&prod_value=3&locaton=4&reason=hii&mrp=1200&task=return&extra_amt=1
if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$user_id=$user_res['id'];
$num = mysqli_num_rows($user_qry);
if($num>0){
   // echo "in num loop";
     $user_id=$user_res['id'];
     if(isset($_GET['replaceid'])) $replace_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['replaceid']))); else $order = 0;
     if(isset($_GET['dis_code'])) $dis_code = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dis_code']))); else $order = 0;
     if(isset($_GET['ret_code'])) $ret_code = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['ret_code']))); else $order = 0;
     if(isset($_GET['prod_qty'])) $prod_qty = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['prod_qty']))); else $order = 0;
     if(isset($_GET['prod_value'])) $prod_value = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['prod_value']))); else $order = 0;
      if(isset($_GET['prod_code'])) $prod_code = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['prod_code']))); else $order = 0;
     if(isset($_GET['date_time'])) $date_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date_time']))); else $order = 0;
     if(isset($_GET['location'])) $location = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['location']))); else $order = 0;
     if(isset($_GET['reason'])) $reason = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['reason']))); else $order = 0;
     if(isset($_GET['mrp'])) $mrp = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mrp']))); else $order = 0;
     if(isset($_GET['task'])) $tasks = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['task']))); else $order = 0;
     if(isset($_GET['extra_amt'])) $extra_amt = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['extra_amt']))); else $order = 0;
     

    $tablename="damage_replace";
    $task="insert";
    $arraydata[]="replaceid='".$replace_id."'";
    $arraydata[]="user_id='".$user_res['id']."'";
    $arraydata[]="dis_code='".$dis_code."'";
    $arraydata[]="ret_code='".$ret_code."'";
    $arraydata[]="prod_qty='".$prod_qty."'";
    $arraydata[]="prod_code='".$prod_code."'";
    $arraydata[]="prod_value='".$prod_value."'";
    $arraydata[]="date_time='".$date_time."'";
    $arraydata[]="location='".$location."'";
    $arraydata[]="reason='".$reason."'";
    $arraydata[]="mrp='".$mrp."'";
    $arraydata[]="task='".$task."'";    
    $arraydata[]="extra_amt='".$extra_amt."'";
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