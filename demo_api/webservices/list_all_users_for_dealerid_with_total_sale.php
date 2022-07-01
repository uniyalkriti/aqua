<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
$dealerid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealerid'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "select person.id as person_id, CONCAT_WS(' ',first_name,last_name) as person_name,SUM(rate*quantity) as sale 
    FROM user_sales_order udr 
    INNER JOIN user_sales_order_details USING(order_id)
    INNER JOIN dealer ON dealer.id=udr.dealer_id
    INNER JOIN person ON person.id=udr.user_id
    WHERE udr.`date` between '$fromdate' AND '$todate' and user_id IN (".$juniors.",$user_id) AND dealer.id = '$dealerid'
    GROUP BY udr.user_id";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
