<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//TEST URL - http://localhost/msell2/webservices/circulardelete.php?imei=1234567891234567&c_id=2

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$circular_id = $_GET['c_id'];
$imei = $_GET['imei'];
//echo $circular_id;die;

$q ="select id from person where imei_number = '$imei'";
$res1 = mysqli_query($dbc,$q);
$row1 = mysqli_fetch_array($res1);
$person_id = $row1['id'];

$q = "select circular_id from person_login where person_id = $person_id ";
//echo $q;die;
$res2 = mysqli_query($dbc,$q);
$row2 = mysqli_fetch_array($res2);
$circular_string = $row2['circular_id'];
$temp = explode(',', $circular_string); //pre($temp);die;
for($i=0;$i<count($temp);$i++)
{
    if($temp[$i]==$circular_id)
    {
       $index = $i;
    }
}
//echo $temp[$index];die;
unset($temp[$index]);
$new_c_id = implode(',',$temp);

$q="update person_login set circular_id = '$new_c_id' where person_id = $person_id ";
//echo $q;die;
$res3 = mysqli_query($dbc, $q);

$q = "select user_id from circular_view where circular_id = $circular_id ";
$res4 = mysqli_query($dbc,$q);
$row4 = mysqli_fetch_array($res4);
$user_string = $row4['user_id'];
if($user_string =='')
{
    $user_string = $person_id;
}  else {
     $user_string = $user_string.",".$person_id;
}$user_string;

$q="update circular_view set user_id = '$user_string' where circular_id = $circular_id ";
$res3 = mysqli_query($dbc, $q);