<?php
// die(mysqli_error())
//TEST URL - http://localhost/PhpProject2/webservices/submitAttendance.php?user_id=20&work_date=2014-06-24&work_status=2&mccmnclaccellid=4029:29:215:217&lat_lng=27.24345345,77,3453535&remarks=hellow%20world
require_once('../admin/include/conectdb.php');
require_once('functions.php');
$tower_details=' ';
if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;

 $q = "select id from person where imei_number='".$imei."'";
$user_qry = mysqli_query($dbc,$q);
$user_res = mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
    $user_id = $user_res['id'];
    
    if(isset($_GET['work_date'])) $work_date = mysqli_real_escape_string($dbc, trim(($_GET['work_date']))); else $work_date = '';
   
    if(isset($_GET['work_status'])) $work_status = mysqli_real_escape_string($dbc, trim(($_GET['work_status']))); else $work_status = 0;
   
    if(isset($_GET['mccmnclaccellid'])) $mccmnclaccellid = mysqli_real_escape_string($dbc, trim(($_GET['mccmnclaccellid']))); else $mccmnclaccellid = 0;
    
    if(isset($_GET['lat_lng'])) $lat_lng = mysqli_real_escape_string($dbc, trim(($_GET['lat_lng']))); else $lat_lng = 0;
    if(isset($_GET['track_addrs'])) $track_addrs = mysqli_real_escape_string($dbc, trim(($_GET['track_addrs']))); else $track_addrs = 0;
    if(isset($_GET['remarks'])) $remarks = mysqli_real_escape_string($dbc, trim(($_GET['remarks']))); else $remarks = 0;

    $tower_details=$mccmnclaccellid;
    $workdate = explode(' ',$work_date);
    $new_work_date = $workdate[0];
    
    if(strstr($mccmnclaccellid,':')){
        $mccmnclaccellid=explode(':',$mccmnclaccellid);
        $mcc=$mccmnclaccellid['0'];
        $mnc=$mccmnclaccellid['1'];
        $lac=$mccmnclaccellid['2'];
        $cid=$mccmnclaccellid['3'];
        $tower_details = $mcc.':'.$mnc.':'.$lac.':'.$cid;
        $lat_lng_mnc=getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid);
//        if(!isset($lat_lng)) {
//            $lat_lng=$lat_lng_mnc;
//        }
    }
    $ll=explode(",",$lat_lng);
    //print_r($ll);
    if($track_addrs == '$$'){
        if($ll[0] !='0.0' && $ll[1] !='0.0'){ 
         $address=getLocationByLatLng($ll[0],$ll[1]);
        // echo "hiii";exit;

        }else{
            $lat_lng_mnc=getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid);
            $latlngs=  explode(',',$lat_lng_mnc);
            $address=getLocationByLatLng($latlngs[0],$latlngs[1]);    
        } 
    }else{
       $address = $track_addrs; 
    }
   
   
    
    $tablename="user_daily_attendance";
    $task="insert";
    $arraydata[]="user_id='".$user_id."'";
    $arraydata[]="work_date='".$work_date."'";
    $arraydata[]="work_status='".$work_status."'";
    $arraydata[]="mnc_mcc_lat_cellid='".$tower_details."'";
    $arraydata[]="lat_lng='".$lat_lng."'";
    $arraydata[]="track_addrs='".$address."'";
    $arraydata[]="remarks='".$remarks."'";
    
    $condition[]="";
    $code="";
    $q1 = "select id from user_daily_attendance where user_id='".$user_id."' AND DATE_FORMAT(work_date,'%Y-%m-%d') ='".$new_work_date."'";
    $sql= mysqli_query($dbc,$q1);
    $num= mysqli_num_rows($sql);
   
    if($num<1){
        $result=insert_update($tablename,$arraydata,$task,$condition,$code);
        if($result==1){
            echo "Y";
        }else{
            echo "N";
        }
    }else
        {
         echo "Y";
        }
}
?>
