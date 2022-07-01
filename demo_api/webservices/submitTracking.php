<?php
// die(mysqli_error())

//TEST URL - http://localhost/suryasteel/webservices/submitTracking.php?user_id=20&track_date=2014-06-24&track_time=12:12:20&mccmnclaccellid=4029:29:215:217&lat_lng=27.24345345,77,3453535
require_once('../admin/include/conectdb.php');
require_once('functions.php');

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){

    $user_id=$user_res['id'];
    if(isset($_GET['track_date'])) $track_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['track_date']))); else $track_date = '';
    if(isset($_GET['track_time'])) $track_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['track_time']))); else $track_time = '';
  if(isset($_GET['mccmnclaccellid'])) $mccmnclaccellid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mccmnclaccellid']))); else $mccmnclaccellid = 0;
  if(isset($_GET['lat_lng'])) $lat_lng = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['lat_lng']))); else $lat_lng = 0;
  if(isset($_GET['track_address'])) $track_address = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['track_address']))); else $track_address = 0;
  
    if(strstr($mccmnclaccellid,':')){
        $mccmnclaccellid=explode(':',$mccmnclaccellid);
        $mcc=$mccmnclaccellid['0'];
        $mnc=$mccmnclaccellid['1'];
        $lac=$mccmnclaccellid['2'];
        $cid=$mccmnclaccellid['3'];
        $tower_details = $mcc.':'.$mnc.':'.$lac.':'.$cid;
        $lat_lng_mnc=getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid);
        if(!isset($lat_lng)) {
            $lat_lng=$lat_lng_mnc;
        }
        $ll=explode(",",$lat_lng);
        if($track_address == '$$'){
        if($ll[0] !='0.0' && $ll[1] !='0.0'){ 
        $address=getLocationByLatLng($ll[0],$ll[1]);

        }else{
            $lat_lng_mnc=getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid);
            $latlngs=  explode(',',$lat_lng_mnc);
            $address=getLocationByLatLng($latlngs[0],$latlngs[1]);    
        } 
    }else{
       $address = $track_address; 
    }
    }
    $tablename="user_daily_tracking";
    $task="insert";
    $arraydata[]="user_id='".$user_id."'";
    $arraydata[]="track_date='".$track_date."'";
    $arraydata[]="track_time='".$track_time."'";
    $arraydata[]="mnc_mcc_lat_cellid='".$tower_details."'";
    $arraydata[]="lat_lng='".$lat_lng."'";
    $arraydata[]="track_address='".$address."'";
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
