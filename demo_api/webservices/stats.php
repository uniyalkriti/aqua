<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
//$dbc = @mysqli_connect('localhost','root','Dcatch','msell-dsgroup-dms') OR die ('could not connect:');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();
if (isset($_POST['user_id'])) {
    $uid = $_POST['user_id'];
} else {
    $uid = '';

}

if (!empty($uid)) {
    $day=date('d',strtotime('now'));
    $my_query = "SELECT SUM(rd) as total_rd
FROM monthly_tour_program
WHERE MONTH(`working_date`) = MONTH(CURRENT_DATE())
AND YEAR(`working_date`) = YEAR(CURRENT_DATE())
AND DAY(`working_date`)>='1' AND DAY(`working_date`)<='$day' AND person_id='$uid'";

    $ach = "SELECT SUM(arch) as total_achievement
FROM monthly_tour_program
WHERE MONTH(`working_date`) = MONTH(CURRENT_DATE())
AND YEAR(`working_date`) = YEAR(CURRENT_DATE())
AND DAY(`working_date`)>='1' AND DAY(`working_date`)<='$day' AND person_id='$uid'";
    $query_run = mysqli_query($dbc, $my_query);
    $ach_run = mysqli_query($dbc, $ach);
    $fetch = mysqli_fetch_assoc($query_run);
    $fetch2 = mysqli_fetch_assoc($ach_run);
    $percentage_ratio=0;
    if (!empty($fetch2['total_achievement']) && !empty($fetch['total_rd']))
    {
        $percentage_ratio=($fetch2['total_achievement']/$fetch['total_rd'])*100;
    }
    $d=array("total_rd"=>!empty($fetch['total_rd'])?$fetch['total_rd']:0,
        'total_achievement'=>!empty($fetch2['total_achievement'])?$fetch2['total_achievement']:0,
        'percentage_ratio'=>$percentage_ratio);
    $response=array("response"=>true,
        "message"=>'MTP',
        "data"=>$d);

} else {
    $response = array("response" => false, "message" => 'User Id Required');
}
echo json_encode($response);

