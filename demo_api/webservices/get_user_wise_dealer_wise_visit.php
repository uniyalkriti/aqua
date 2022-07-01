<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['start_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_date']))); else $s_date = 0;
if(isset($_GET['end_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_date']))); else $e_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['role_id'])) $role_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['role_id']))); else $role_id = 0;
if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;



$merchandiser = array();
$array=array();
// if($role_id==187){
$q1 = "SELECT DATE_FORMAT(`workDate`,'%d-%m-%Y') AS work_date,DATE_FORMAT(`workDate`,'%H:%i:%s') AS checkin,merchandiser_checkout.time AS checkout,merchandiser_checkin.dealerId FROM merchandiser_checkin LEFT JOIN merchandiser_checkout ON merchandiser_checkout.orderId=merchandiser_checkin.orderId WHERE DATE_FORMAT(`workDate`,'%Y-%m-%d')>='$s_date' AND DATE_FORMAT(`workDate`,'%Y-%m-%d')<='$e_date' AND merchandiser_checkin.userId='$user_id' AND merchandiser_checkin.dealerId='$dealer_id' GROUP BY merchandiser_checkin.orderId";
//h1($q1);
$r1 = mysqli_query($dbc, $q1);
while($row1 = mysqli_fetch_array($r1)){
$merchandiser['role_id'] =$role_id;
$merchandiser['user_id'] =$user_id; 
$merchandiser['dealer_id'] =$row1['dealerId']; 
$merchandiser['date'] = $row1['work_date'];
$merchandiser['checkin'] = $row1['checkin'];
$merchandiser['checkout'] = $row1['checkout'];  
$array[]=$merchandiser;
}
// }
$supervisor = array();
// if($role_id==187){
// $q2 = "SELECT DATE_FORMAT(`workDate`,'%d-%m-%Y') AS work_date,DATE_FORMAT(`workDate`,'%H:%i:%s') AS checkin,coverage_checkout.time AS checkout,coverage_checkin.dealerId FROM coverage_checkin LEFT JOIN coverage_checkout ON coverage_checkout.orderId=coverage_checkin.orderId WHERE DATE_FORMAT(`workDate`,'%Y-%m-%d')>='$s_date' AND DATE_FORMAT(`workDate`,'%Y-%m-%d')<='$e_date' AND coverage_checkin.userId='$user_id' AND coverage_checkin.dealerId='$dealer_id' GROUP BY coverage_checkin.orderId";
//h1($q2);
// $r2 = mysqli_query($dbc, $q2);
// while($row2 = mysqli_fetch_array($r2)){
// $supervisor['role_id'] = $role_id;
// $supervisor['user_id'] = $user_id;
// $supervisor['dealer_id'] = $row2['dealerId'];
// $supervisor['date'] = $row2['work_date'];
// $supervisor['checkin'] = $row2['checkin'];  
// $supervisor['checkout'] = $row2['checkout'];
// $array[]=$supervisor;
// }
// }
//$final_array = array("merchandiser"=>$merchandiser,"supervisor"=>$supervisor);
//pre($final_array);
$f = array("result"=>$array);
$data = json_encode($f);
echo  $data;

?>          