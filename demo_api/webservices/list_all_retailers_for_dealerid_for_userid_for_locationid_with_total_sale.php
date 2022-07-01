<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//http://8.30.244.74/msell-dsgroup-dms/webservices/list_all_retailers_for_dealerid_for_userid_for_locationid_with_total_sale.php?userid=157&fromdate=2017-05-03&todate=2017-05-03&dealerid=17&salespersonid=157&locationid=155
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['fromdate'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['todate'])));
$dealerid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealerid'])));
$salespersonid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['salespersonid'])));
$locationid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['locationid'])));
//echo $user_id.$fromdate.$todate.$dealerid.$salespersonid.$locationid;
//$retailerid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailerid'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "select retailer.id as retailer_id, retailer.name as retailer_name,SUM(rate*quantity) as sale 
    FROM dealer
    INNER JOIN user_sales_order udr ON dealer.id=udr.dealer_id
    INNER JOIN user_sales_order_details USING(order_id)
    INNER JOIN retailer ON retailer.id=udr.retailer_id 
    INNER JOIN location_5 ON location_5.id=udr.location_id 
    INNER JOIN person ON person.id=udr.user_id
    WHERE udr.`date` between '$fromdate' AND '$todate' and person.id in (".$juniors.",$user_id) 
    AND dealer.id = '$dealerid' 
    AND location_5.id = '$locationid'
   
    GROUP BY udr.retailer_id";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
    $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
