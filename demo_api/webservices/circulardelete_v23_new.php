<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$circular_id = $_GET['c_id'];
$person_id = $_GET['user_id'];

$qu="update circular set status = 'Publish' where id = $circular_id AND circular_for_persons = $person_id ";
//echo $qu;die;
$resu3 = mysqli_query($dbc, $qu);
if($resu3){
	echo "Y";
}
