<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['start_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_date']))); else $s_date = 0;
if(isset($_GET['end_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_date']))); else $e_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['role_id'])) $role_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['role_id']))); else $role_id = 0;



$dealers = array();
$array=array();

$q1 = "SELECT dealer.id,dealer.name FROM dealer INNER JOIN dealer_location_rate_list AS dlrl ON dlrl.dealer_id=dealer.id WHERE dlrl.user_id='$user_id' GROUP BY dlrl.user_id,dlrl.dealer_id";
//h1($q1);
$r1 = mysqli_query($dbc, $q1);
while($row1 = mysqli_fetch_array($r1)){
$dealers['user_id'] =$user_id; 
$dealers['dealer_id'] =$row1['id'];
$dealers['dealer_name'] =$row1['name'];
$array[]=$dealers;
}
$f = array("result"=>$array);
$data = json_encode($f);
echo  $data;

?>          