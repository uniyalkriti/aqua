<?php
//TEST URL -//http://localhost/msell2/webservices/login_auth.php?imei=123456&uname=anil123&pass=anil123
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('functions.php');

$imei = '';
$uname = '';
$pass = '';

$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));
if(!empty($imei) && !empty($uname) && !empty($pass))
{
    $q = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person_username = '$uname' AND AES_DECRYPT(person_password, '".EDSALT."') = '$pass' AND person_status = '1'  ORDER BY person_username ASC";
    $user_qry = mysqli_query($dbc, $q);
    $user_res = mysqli_fetch_assoc($user_qry);


if(mysqli_num_rows($user_qry)>0){
if(empty($user_res['imei_number'])){
        if(empty($imei)){

            echo "FALSE";
            exit;
        }
         $q_imei = "UPDATE person SET imei_number ='$imei' where id = '$user_res[id]'";
        // echo $q_imei;die;
         mysqli_query($dbc,$q_imei);
    }else{

      $chk_qry = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person_username = '$uname' AND person_password = AES_ENCRYPT('$pass', '" . EDSALT . "') AND person_status = '1'  ORDER BY person_username ASC";;
      $user_qry = mysqli_query($dbc,$chk_qry);
      // echo $chk_qry;die;
      if(mysqli_num_rows($user_qry) <=0){

        echo "FALSE";
        exit;
      }
    }
}

    if($user_qry && mysqli_num_rows($user_qry) > 0)
    {
        echo "TRUE";
    }
    else
    {
        echo "FALSE";
    }
}
else
{
    echo 'FALSE';
}
?>
