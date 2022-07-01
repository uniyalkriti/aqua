<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$obj = new myzip('admin');
$outformat = isset($_GET['outformat']) &&  $_GET['outformat'] == 'pdf' ? 'pdf' : 'zip';
if(isset($_GET['id'])) $obj->send_record_zip($_GET['id'], true, $outformat);
?>