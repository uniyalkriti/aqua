<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
$v_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['app_version'])));
if(isset($_GET['app_version']))
{
$app_version =$v_name; 
}
else 
{
$app_version ="33";    
}   

$final_result = array();
$result = array();

$result['app_version']=$app_version;
$final_result[]=$result;

//pre($final_result);
echo $try = json_encode(array('result' => $final_result));

?>