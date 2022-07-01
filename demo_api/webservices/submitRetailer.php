<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
              
if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){
    $user_id=$user_res['id'];
    if(isset($_GET['retailer_id'])) $retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id']))); else $retailer_id = 0;
    if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;
    if(isset($_GET['r_name'])) $name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_name']))); else $name = 0;
    if(isset($_GET['image_name'])) $image_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['image_name']))); else $image_name = 0;
    if(isset($_GET['d_code'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['d_code']))); else $dealer_id = 0;
    if(isset($_GET['l_code'])) $location_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['l_code']))); else $location_id = 0;
    if(isset($_GET['r_address'])) $addres = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_address']))); else $addres= 0;
    if(isset($_GET['r_email'])) $email = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_email']))); else $email= 0;
    if(isset($_GET['cont_name'])) $cont_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['cont_name']))); else $cont_name = 0;
    if(isset($_GET['r_contact_no'])) $landline = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_contact_no']))); else $landline = 0;
    //if(isset($_GET['other_numbers'])) $other_numbers = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['other_numbers']))); else $other_numbers = 0;
    if(isset($_GET['r_tin'])) $tin_no= mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_tin']))); else $tin_no = 0;
    if(isset($_GET['r_pin_no'])) $pin_no = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_pin_no']))); else $pin_no = 0;
    if(isset($_GET['r_type'])) $outlet_type_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_type']))); else $outlet_type_id = 0;
    if(isset($_GET['r_avg_pur'])) $avg_per_month_pur = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['r_avg_pur']))); else $avg_per_month_pur = 0;
    if(isset($_GET['cr_date'])) $cr_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['cr_date']))); else $cr_date= 0;
    if(isset($_GET['cr_time'])) $cr_time = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['cr_time']))); else $cr_time= 0;    
    if(isset($_GET['lat'])) $lat = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['lat']))); else $lat= 0;    
    if(isset($_GET['long'])) $long = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['long']))); else $long= 0;    
    if(isset($_GET['swipe'])) $swipe = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['swipe']))); else $swipe= 0;    
    if(isset($_GET['currac'])) $currac = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['currac']))); else $currac= 0;    
    if(isset($_GET['bankid'])) $bankid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['bankid']))); else $bankid= 0;    
    if(isset($_GET['mccmnclaccellid'])) $mccmnclaccellid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['mccmnclaccellid']))); else $mccmnclaccellid= 0;
    if(isset($_GET['track_address'])) $track_address = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['track_address']))); else $track_address= 0;
    
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
    
    
    
    $tablename="retailer";
    $task="insert";
    $arraydata[]="id='".$retailer_id."'";    
    $arraydata[]="name='".$name."'";
    $arraydata[]="image_name='".$image_name."'";
    $arraydata[]="dealer_id='".$dealer_id."'";
    $arraydata[]="location_id='".$location_id."'";
    $arraydata[]="address='".$addres."'";
    $arraydata[]="email='".$email."'";
    $arraydata[]="contact_per_name='".$contact_per_name."'";
    $arraydata[]="landline='".$landline."'";
    $arraydata[]="tin_no='".$tin_no."'";
    $arraydata[]="pin_no='".$pin_no."'";    
    $arraydata[]="outlet_type_id='".$outlet_type_id."'";
    $arraydata[]="avg_per_month_pur='".$avg_per_month_pur."'";
    $arraydata[]="created_on='".$cr_date." ".$cr_time."'";
    $arraydata[]="lat_long='".$lat."|".$long."'";
    $arraydata[]="card_swipe='".$swipe."'";	 
    $arraydata[]="current_account='".$currac."'";
    $arraydata[]="bank_branch_id='".$bankid."'";
    $arraydata[]="company_id='1'";
    $arraydata[]="mncmcclatcellid='".$mccmnclaccellid."'";
    $arraydata[]="track_address='".$address."'";
    $arraydata[]="created_by_person_id='".$user_res['id']."'";    

    $condition="";
    $code="";
    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1){
         $q = "INSERT INTO user_dealer_retailer (`user_id`, `dealer_id`, `retailer_id`) VALUES ('$user_res[id]','$dealer_id','$retailer_id')";
        $r = mysqli_query($dbc,$q);
        echo "Y";
    }else
        {
        echo "N";
    }
    
}
?>
           