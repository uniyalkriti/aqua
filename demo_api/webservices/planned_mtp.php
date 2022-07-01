<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
//http://192.168.1.19/msell2/webservices/planned_mtp.php?imei=&date

$imei = $_GET['imei'];
//$date = date('Y-m-d', strtotime($_GET['date']));
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['date'])) $date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $date = 0;


$q = "select * from person where id = '$user_id'";
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];

$q = "select *,dealer.name as dname,l7.name as lname from monthly_tour_program mtp INNER JOIN dealer ON dealer.id = mtp.dealer_id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id = dealer.id INNER JOIN location_7 l7 ON dlrl.location_id = l7.id where mtp.person_id = $person_id AND mtp.working_date = '$date' GROUP BY mtp.id";

$mtp_res = mysqli_query($dbc, $q);
$result = array();
while($mtp_row = mysqli_fetch_array($mtp_res))
{
       $result['dealer'][] = $mtp_row['dname'];
       $result['location'][] = $mtp_row['lname'];
       $result['total_call'][] = $mtp_row['pc'];
       $result['totalsales'][] = $mtp_row['rd'];
     
}
$result['dealer'] = array_unique($result['dealer']);
$result['location'] = array_unique($result['location']);
if(!empty($result)){
    $dealer = implode('|' ,$result['dealer']);
    $location = implode('|' ,$result['location']);
    $total_call = array_sum($result['total_call']);
    $totalsales = array_sum($result['totalsales']);   
    $final_array['dealer'] = $dealer;
    $final_array['location'] = $location;
    $final_array['total_call'] = "$total_call";
    $final_array['totalsales'] = "$totalsales";
}
//echo '<pre>';
//print_r($final_array);

if($final_array['dealer'] =='')
{ 
    exit;
    
}
$res1[]=$final_array;
echo $try = json_encode(array('manacle' => $res1));