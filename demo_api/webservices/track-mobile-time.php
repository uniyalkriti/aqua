<?php
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
$myobj = new mtp();

$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');

?>