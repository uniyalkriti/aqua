<?php	

//TEST URL - http://localhost/PhpProject2/webservices/submitmtp.php?imei=1342537475886996&working_date=2014-06-24&working_status_id=2&beat_id=2&total_calls=200&total_sales=2009800&mobile_save_date_time=2014-06-01&gi=2&black=2&hollow_chs=200&hollow_rhs=100&hollow_shs=150&extra_light=200

require_once('../admin/include/conectdb.php');
require_once('functions.php');

$dealer_location = array();
$final_location = array();
$final_dealer_details = array();
$retailer_info = array();

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
    $user_id=$user_res['id'];

    if(isset($_GET['date'])) $working_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $working_date = '';
    if(isset($_GET['status'])) $working_status_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['status']))); else $working_status_id = 0;
    if(isset($_GET['dis_code'])) $dealer = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dis_code']))); else $dealer = 0;
    //if(isset($_GET['beat_code'])) $beat_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['beat_code']))); else $beat_id = 0;
     if(isset($_GET['locations'])) $locations = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['locations']))); else $locations = 0;
     
    if(isset($_GET['tot_call'])) $total_calls = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['tot_call']))); else $total_calls = 0;
    if(isset($_GET['tot_sale_val'])) $total_sales = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['tot_sale_val']))); else $total_sales = 0;
//    if(isset($_GET['mobile_save_date_time'])) $mobile_save_date_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mobile_save_date_time']))); else $mobile_save_date_time = 0;
    if(isset($_GET['category'])) $category = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['category']))); else $category = 0;
    if(isset($_GET['mtp_smt_date'])) $mtp_smt_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mtp_smt_date']))); else $mtp_smt_date = 0;
    if(isset($_GET['root_id'])) $route_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['root_id']))); else $route_id = 0;
     $category = rtrim($category,',');

    //$location=join(',',$final_location);
    $tablename="monthly_tour_program";
    $task="insert";
    $arraydata[]="id='".$route_id."'";
    $arraydata[]="person_id='".$user_id."'";
    $arraydata[]="working_date='".$working_date."'";
    $arraydata[]="working_status_id='".$working_status_id."'";
    $arraydata[]="dealer_id='".$dealer."'";
    $arraydata[]="locations='".$locations."'";
    $arraydata[]="total_calls='".$total_calls."'";
    $arraydata[]="category_wise='".$category."'";
    $arraydata[]="total_sales='".$total_sales."'";
    $arraydata[]="mobile_save_date_time='".$mtp_smt_date."'";
    $arraydata[]="upload_date_time=CURRENT_TIMESTAMP";
    $condition[]="";
    $code="";
    
    $sql= mysqli_query($dbc,"select id from monthly_tour_program where person_id='".$user_id."' AND working_date='".$working_date."'") or die(mysqli_error($dbc));
    $num= mysqli_num_rows($sql);
    if($num<1){
        $result=insert_update($tablename,$arraydata,$task,$condition,$code);
        if($result == 1){
            echo "Y";
        }else{
            echo "N";
        }
    }else{
        echo $working_date;
    }
}
?>
