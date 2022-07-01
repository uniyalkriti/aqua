<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "SELECT dealer.id as dealerid,udr.date as date, dealer.name as dname,SUM(rate*quantity) as sale from user_sales_order udr 
    INNER JOIN user_sales_order_details USING(order_id)
    INNER JOIN dealer ON dealer.id=udr.dealer_id
    where udr.`date` between '$fromdate' AND '$todate' and user_id IN (".$juniors.",$user_id)
    group by udr.dealer_id";
//h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}

    if(empty($data)){
        $final_data = Array ('status'=>false, 'result' => []);
    }else{
        $final_data = array('status'=>true,'result'=>$data);
    }

$result = json_encode($final_data);
echo $result;
