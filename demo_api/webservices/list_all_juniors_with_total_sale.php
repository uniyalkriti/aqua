<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$report_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['report_date'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "SELECT person.id as id,concat(first_name,' ',middle_name,' ',last_name) as fullname,round(SUM(rate*quantity),2) as sale from person INNER JOIN user_sales_order udr ON person.id=udr.user_id INNER JOIN user_sales_order_details usod ON usod.order_id=udr.order_id where udr.`date`='$report_date' and person.id in (".$juniors.",$user_id) group by udr.user_id";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}
if(empty($data)){
     $rows['id'] = 0;
     $rows['fullname'] = 0;
     $rows['sale'] = 0;
     $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
