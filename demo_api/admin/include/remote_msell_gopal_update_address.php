<?php
error_reporting(0);
ini_set('max_execution_time','-1');
require_once 'admin/functions/common_function.php';
$dbc = @mysqli_connect('localhost','root','4HF8tB5@%ny8gzAG','msell-gopal') OR die ('could not connect:');

$date = date('Y-m-d');
$predate = date('Y-m-d',strtotime($date.' -10	 day'));
$datet=date('Y-m-d',strtotime($date.' +1 day'));
echo $q = "select * from check_out where DATE_FORMAT(work_date,'%Y-%m-%d') >= '$predate' AND DATE_FORMAT(work_date,'%Y-%m-%d') <= '$date' and attn_address='' ";
$res = mysqli_query($dbc, $q);
$count = 0;
$r=[];
echo mysqli_num_rows($res);die;
while ($row = mysqli_fetch_array($res)) {
	 $lat_lng1 = explode(',', $row['lat_lng']);
         $lat = $lat_lng1[0];
         $lng = $lat_lng1[1];
    if ($row['attn_address'] == "" || $row['attn_address'] == ", , , "  || empty($row['attn_address'] )) {

$r[]=$row['lat_lng'];
           // $address = getLocationByLatLng($lat, $lng);
           //  echo $q = "update check_out set attn_address = '$address' where id = '$row[id]' ";
           //  echo '<br/>';
           //  $res2 = mysqli_query($dbc2, $q);
           //  $count++;

    }
}
echo '<pre>';print_r($r);
// echo $count . " RECORDS UPDATED";
