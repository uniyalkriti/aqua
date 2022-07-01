<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//http://192.168.1.19/msell2/webservices/actual_mtp.php?imei=&date
$imei = $_GET['imei'];
//$date = date('Y-m-d', strtotime($_GET['date']));
$date=$_GET['date'];

$q = "select * from person where imei_number = '$imei'";
    
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];
$role_id = $row['role_id'];
$user = array();

$myobj = new sale();
//$user = $myobj->get_user_wise_junior_data($person_id, $role_id);
//$user = implode(',', $user);
$user= get_juniour_id($person_id);
if(!empty($user))
{
$user=$user.',';
}
//print_r($user);exit;
 $q = "select *,_role.rolename,dealer.id as d_id,person.id as u_id,CONCAT_WS(' ',first_name,last_name)as u_name,dealer.name as d_name from person "
        . "INNER JOIN dealer_bal_stock ON person.id = dealer_bal_stock.user_id "
        . "INNER JOIN dealer ON dealer.id = dealer_bal_stock.dealer_id "
        . " INNER JOIN _role USING(role_id)"
        . " where person.id IN ($user$person_id)  AND server_datetime = '$date' GROUP BY dealer_bal_stock.order_id ";   
//h1($q);
$mtp_res = mysqli_query($dbc, $q);
$result = array();
$user_id = array();
$final_result_array = array();
$semi =array();
$sku_details = array();
$final_sku_details = array();
while($mtp_row = mysqli_fetch_array($mtp_res)){
    $id = $mtp_row['u_id'];
    $result['u_id'] = $mtp_row['u_id'];
    $result['user'] = $mtp_row['u_name'];
    $result['rolename'] = $mtp_row['rolename'];
    $result['d_id'] = $mtp_row['d_id'];
    $result['dealer'] = $mtp_row['d_name'];
    $result['order_id'] = $mtp_row['order_id'];
    $user_id[$mtp_row['u_id']] = $mtp_row['u_id'];
    $final_result_array[] = $result;
    
}
if(!empty($user_id))
{
    $user_id_str = implode(',' , $user_id);
    $q = "SELECT *,catalog_product.name AS cpname FROM dealer_bal_stock INNER JOIN catalog_product on catalog_product.id=dealer_bal_stock.catalog_id  WHERE user_id IN ($user_id_str) AND server_datetime = '$date' GROUP BY mfg_date,dealer_bal_stock.catalog_id";
    $r = mysqli_query($dbc, $q);
    if($r)
    {
        while($row = mysqli_fetch_assoc($r))
        {
            //$sku_details['id'] = $row['id'];
            $sku_details['pro_name'] = $row['cpname'];
            $sku_details['pieces'] = $row['pieces'];
            $sku_details['cases'] = $row['cases'];
            $sku_details['user_id'] = $row['user_id'];
            $sku_details['dealer_id'] = $row['dealer_id'];
            $sku_details['m_date'] = $row['mfg_date'];
            $sku_details['order_id'] = $row['order_id'];
            $final_sku_details[] = $sku_details;
        }
    }
    
}
$final = array();
$final['response'] = 'TRUE'; 
$final['user'] = $final_result_array;
$final['sku_details'] = $final_sku_details;
$final_out = array();
$final_out[] = $final;

//echo '<pre>';
//print_r($final_result_array);
//print_r($final_sku_details);
//echo '</pre>';
echo $try = json_encode(array('manacle' => $final_out));
