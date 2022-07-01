<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if (isset($_GET['complaint_id']))
    $complaint_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['complaint_id'])));
else
   $complaint_id = 0;

$myobj = new complaint();

$cid = array();
$complaint_history = array();
//$rs = array(2,3,8);
$cq = "SELECT * FROM `complaint_history` where complaint_id=$complaint_id ORDER by id";
//h1($cq);
$cr = mysqli_query($dbc, $cq);
while ($cfetch = mysqli_fetch_array($cr)) {
    $cid['complaint_id'] = $cfetch['complaint_id'];
    $cid['msg'] = $cfetch['msg'];
    $cid['date'] = $cfetch['date'];
    $complaint_history[] = $cid;
   
}


//$user_info = ''; //foreach($rs as $key=>$value) end here
$final_array = array("result" => $complaint_history);
$data = json_encode($final_array);
echo $data;
// if(!empty($rs)){ end here
?>
