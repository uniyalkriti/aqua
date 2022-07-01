<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
//http://192.168.1.19/msell2/webservices/actual_mtp.php?imei=&date
$imei = $_GET['imei'];
$date = date('Y-m-d', strtotime($_GET['date']));

$q = "SELECT * FROM person WHERE imei_number = '$imei'";
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row[id];

$q = "SELECT *,dealer.name as dname,l3.name as lname FROM user_sales_order uso INNER JOIN dealer ON dealer.id = uso.dealer_id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id = dealer.id INNER JOIN location_3 l3 ON dlrl.location_id = l3.id WHERE uso.user_id = $person_id AND uso.date = '$date'";
$mtp_res = mysqli_query($dbc, $q);
$result = array();
$call_count = 1;
while($mtp_row = mysqli_fetch_array($mtp_res))
{
       $result['dealer'][] = $mtp_row['dname'];
       $result['location'][] = $mtp_row['lname'];
       $result['total_call'][] = $call_count;
       $result['totalsales'][] = $mtp_row['total_sale_value'];
       $call_count++;
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
    $final_array['total_call'] = $total_call;
    $final_array['totalsales'] = $totalsales;
}
//echo '<pre>';
//print_r($final_array);
if($final_array['dealer'] =='')
{ exit;}
$res1[]=$final_array;
echo $try = json_encode(array('manacle' => $res1));
?>