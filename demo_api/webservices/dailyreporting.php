<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
if(isset($_GET['userid'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid']))); else $user_id = 0;
if(isset($_GET['from_date'])) $from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date']))); else $from_date = 0;
if(isset($_GET['to_date'])) $to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date']))); else $to_date = 0;
//$q1 = "SELECT `id` from person where imei_number = '$imei'";
//$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
//$q_row = mysqli_fetch_assoc($q_res);
$person_id = $user_id;
$f_date = date("Ymd", strtotime($from_date));
$t_date = date("Ymd", strtotime($to_date));
//$user=  get_juniour_id($person_id);
//if($user==NULL){
//    $user=0;
//}
if(!empty($person_id)){
   $del="DELETE FROM `users_junior_hierarchy` WHERE login_user_id=$person_id";
     $rn_del=mysqli_query($dbc,$del);
     if($rn_del){
     $ins="INSERT INTO `users_junior_hierarchy`(`login_user_id`,`user_id`, `full_name`, `role_id`,`senior_id`) SELECT id,id,CONCAT_WS( ' ',  `first_name`, `middle_name`, `last_name` ) AS person_fullname,role_id,person_id_senior from person where id=$person_id";
     $rn_ins=mysqli_query($dbc,$ins);
      recursivejuniors_new($person_id,$person_id);
     }
}
$data = array();
  $q=" SELECT DISTINCT users_junior_hierarchy.full_name AS user_name,DATE_FORMAT(daily_reporting.work_date,'%d-%m-%Y') AS wdate,
     dealer.name AS dealer_name,location_5.name AS beat,daily_reporting.work_status AS working_with, _daily_schedule.name AS work_status_name, 
     _working_status.name AS work_status,daily_reporting.remarks AS remarks
     FROM daily_reporting LEFT JOIN dealer ON dealer.id=daily_reporting.dealer_id 
     LEFT JOIN location_5 ON location_5.id=daily_reporting.location_id 
     LEFT JOIN users_junior_hierarchy ON users_junior_hierarchy.user_id=daily_reporting.user_id 
     LEFT JOIN `_daily_schedule` ON _daily_schedule.id=daily_reporting.working_with
     LEFT JOIN `_working_status` ON _working_status.id=daily_reporting.work_status_id 
     WHERE 
     DATE_FORMAT(daily_reporting.work_date,'%Y%m%d')>='$f_date' AND DATE_FORMAT(daily_reporting.work_date,'%Y%m%d')<='$t_date' AND login_user_id='$person_id' ORDER BY wdate,users_junior_hierarchy.id ASC ";
//h1($q);

$att = mysqli_query($dbc,$q);

while($row = mysqli_fetch_assoc($att))
{ 
  $data[]=$row;
}

if(empty($data)){
     $row['user_name'] = null;
     $row['wdate'] = null;
     $row['dealer_name'] =null;
     $row['beat'] = null;
     $row['work_status'] = null;
     $row['work_status_name'] = null;
     $row['working_with'] = null;
     $row['remarks'] = null;
     $data[] = $row;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
?>          