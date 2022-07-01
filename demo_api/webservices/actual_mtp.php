<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');

$imei = $_GET['imei'];
//$date = date('Y-m-d', strtotime($_GET['date']));
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['date'])) $date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $date = 0;

$q = "select * from person where id = '$user_id'";
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];

 $q = "select *,dealer.name as dname,l7.name as lname,uso.order_id as order_id from user_sales_order uso INNER JOIN dealer ON dealer.id = uso.dealer_id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id = dealer.id INNER JOIN location_7 l7 ON uso.location_id = l7.id  where uso.user_id = $person_id AND uso.date = '$date' group by uso.order_id ";
$mtp_res = mysqli_query($dbc, $q);
$result = array();
$call_count = 0 ;
 $ttl_call = mysqli_num_rows($mtp_res);
if($ttl_call>0){

while($mtp_row = mysqli_fetch_array($mtp_res))
{
       $result['dealer'][] = $mtp_row['dname'];
       $result['location'][] = $mtp_row['lname'];
       $result['total_call'][] = $call_count;
       $q = "select SUM(rate * quantity)as sales from user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id where usod.order_id = $mtp_row[order_id] ";
       $sal_res = mysqli_query($dbc, $q);
       $s_row = mysqli_fetch_array($sal_res);       
       $result['totalsales'][] = $s_row['sales'];      
}
//echo '<pre>';
//print_r($result);


$result['dealer'] = array_unique($result['dealer']);
$result['location'] = array_unique($result['location']);
if(!empty($result)){
    $dealer = implode('|' ,$result['dealer']);
    $location = implode('|' ,$result['location']);
    $total_call = $ttl_call;
    $totalsales = array_sum($result['totalsales']);
    $final_array['dealer'] = $dealer;
    $final_array['location'] = $location;
    $final_array['total_call'] = $total_call;
    $final_array['totalsales'] = $totalsales;
}
}
//echo '<pre>';
//print_r($final_array);
if($final_array['dealer'] =='')
{ exit;}
$res1[]=$final_array;
echo $try = json_encode(array('manacle' => $res1));