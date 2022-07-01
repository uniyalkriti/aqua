<?php

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//if (isset($_GET['imei']))
//    $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
//else
//    $imei = 0;
//if (isset($_GET['s_date']))
//    $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['s_date'])));
//else
//    $s_date = 0;
////echo "select id,role_id from person where imei_number='".$imei."'";
//$q = "select id,role_id from person where imei_number='" . $imei . "'";
//$user_qry = mysqli_query($dbc, $q) or die(mysqli_error($dbc, $q));
//$user_res = mysqli_fetch_assoc($user_qry);
//$role_id = $user_res['role_id'];
//$user_id = $user_res['id'];

if (isset($_GET['user_id']))
    $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
else
   $user_id = 0;

$myobj = new complaint();

if (isset($_GET['from']))
    $from = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from'])));
else
   $from = 0;

if (isset($_GET['to']))
    $to = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to'])));
else
   $to = 0;

$f = $from;
$fromdate = date("Y-m-d", strtotime($f));
$t = $to;
$todate = date("Y-m-d", strtotime($t));

$myobj = new complaint();

$cid = array();
$complaint = array();
//$rs = array(2,3,8);
$cq = "Select Complaint_report.*,CONCAT_WS('',first_name,middle_name,last_name) as person_name ,person.role_id from Complaint_report INNER JOIN person ON person.id=Complaint_report.user_id where user_id=$user_id AND date >='$from' AND date<='$to' ORDER by id";
//h1($cq);
$cr = mysqli_query($dbc, $cq);
while ($cfetch = mysqli_fetch_array($cr)) {

    $cid['complaint_id'] = $cfetch['complaintID'];
    $cid['user_name'] = $cfetch['person_name'];
    $cid['action'] = $cfetch['actionTaken'];
    $cid['dealer_retailer_id'] = $cfetch['complaintWithRetailer'];
    $cid['role_id'] = $cfetch['role_id'];
     if($cid['role_id']=='1'){
                    $rol_qry = "select name from dealer where id = '$cid[dealer_retailer_id]'";
                    $role_res = mysqli_query($dbc, $rol_qry);
                    $row = mysqli_fetch_assoc($role_res);
                     $cid['name']=$row['name'];
                }else {
                    $rol_qry = "select name from retailer where id = '$cid[dealer_retailer_id]'";
                    $role_res = mysqli_query($dbc, $rol_qry);
                    $row = mysqli_fetch_assoc($role_res);
                     $cid['name']=$row['name'];
                }
                // else if($cid['role_id']=='3'){
                //     $cid['name']=$cfetch['consumer_name'];
                // }
    if($cid['action']=='1')
    {
        $cid['type']='ON PROGRESS';
    }
    else if($cid['action']=='2')
    {
        $cid['type']='CLOSED';
    }
    else if($cid['action']=='0')
    {
        $cid['type']='INITIATE';
    }
    $cid['date'] = $cfetch['date'];
    $complaint[] = $cid;
   // $complaint[] = "complaint_id='".$cid."'";
   // $complaint[] = "action='".$caction."'";
}


//$user_info = ''; //foreach($rs as $key=>$value) end here
$final_array = array("result" => $complaint);
$data = json_encode($final_array);
echo $data;
// if(!empty($rs)){ end here
?>
