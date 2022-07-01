<?php
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
$myobj = new mtp();
$tracking = $myobj->tracking_mobile_data($filter="person_username = 'mohan123' AND person_password = 'mohan123' AND imei_number = '1342537475886996' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');
pre($tracking);
?>
