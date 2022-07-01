<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//..............192.168.0.108/precision-testing/webservices/signin.php?imei=11111111&uname=amit&pass=amit................//

$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$v_code = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['v_code'])));
$v_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['v_name'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));

$q = "SELECT person.id FROM `person_login` 
    INNER JOIN person ON person_login.person_id = person.id AND person_username = '$uname' AND AES_DECRYPT(person_password, '".EDSALT."') = '$pass' AND imei_number = '$imei' AND person_login.person_status = '1'";
//h1($q);
$r = mysqli_query($dbc, $q);
$person = mysqli_fetch_assoc($r);


$q_app_ver = "UPDATE person SET app_version='".$v_name."',app_code='".$v_code."' where id = '".$person['id']."'";
$q_app_ver_res = mysqli_query($dbc , $q_app_ver);

//echo 'signin.php/'.$person['id'].'.php';
$file= 'signin/'.$person['id'].'.php';
//chmod($file, 0777,true); 
//mkdir($file, 0777);
$data = file_get_contents('signin/'.$person['id'].'.php');

//$fp = fopen($file, 'w');
//fwrite($fp, $data);
//fclose($fp);


echo $data;