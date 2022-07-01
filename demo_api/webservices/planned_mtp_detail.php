<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
//http://192.168.1.19/msell2/webservices/planned_mtp_detail.php?imei=&date
// This query is used to get actual data
$q_cons = "SELECT * FROM _constant";
$r_con = mysqli_query($dbc, $q_cons);
$row_con = mysqli_fetch_assoc($r_con);
$dlevel = $row_con['dealer_level'];
$rlevel = $row_con['retailer_level'];

$imei = $_GET['imei'];
$date = $_GET['date'];
$fdate = str_replace('-','',$date);
$q = "select * from person where imei_number = '$imei'";
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];
 $q = "SELECT *, location_$rlevel.name AS location_name,_task_of_the_day.task as wname,dealer.name AS dname,dealer.id AS dealer_id,DATE_FORMAT(working_date, '%Y%m%d') AS sortdate"
        . " FROM monthly_tour_program INNER JOIN dealer ON  monthly_tour_program.dealer_id = dealer.id "
        . "INNER JOIN location_$rlevel ON location_$rlevel.id = monthly_tour_program.locations "
        . "LEFT JOIN `_task_of_the_day` ON _task_of_the_day.id = monthly_tour_program.working_status_id "
        . "LEFT JOIN _travelling_mode ON _travelling_mode.id=monthly_tour_program.travel_mode "
        . "WHERE monthly_tour_program.person_id = $person_id AND DATE_FORMAT(monthly_tour_program.working_date,'%m%Y') = '$fdate' GROUP BY sortdate ASC";

$r = mysqli_query($dbc, $q);
$out = array();
$final_arry = array();
if(mysqli_num_rows($r) > 0 )
{
    while($row = mysqli_fetch_assoc($r))
    {
        $id = $row['sortdate'];
        $out[$id]['location_name'] = $row['location_name'];
        $out[$id]['dealer_name'] = $row['dname'];
        $out[$id]['location_name'] = $row['location_name'];
        $out[$id]['total_calls'] = $row['total_calls'];
        $out[$id]['total_sales'] = $row['total_sales'];
        $out[$id]['from'] = $row['from'];
        $out[$id]['to'] = $row['to'];
        $out[$id]['distance'] = $row['travel_distance'];
        $out[$id]['travel_mode'] = $row['mode'];
        $out[$id]['work_status'] = $row['wname'];
        $out[$id]['date'] = $row['working_date'];
        $out[$id]['dealer_id'] = $row['dealer_id'];
        $final_arry['planned'] = $out;
    }
}

$q = "SELECT *, DATE_FORMAT(date, '%Y%m%d') AS sortdate,dealer.name AS dname,dealer.id AS dealer_id, location_$rlevel.name AS location_name FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id INNER JOIN location_$rlevel ON location_$rlevel.id = user_sales_order.location_id INNER JOIN retailer ON retailer.id = user_sales_order.retailer_id  WHERE user_id = '$person_id'AND DATE_FORMAT(date,'%m%Y') = '$fdate'";

$r = mysqli_query($dbc, $q);
$out1 = array();
if(mysqli_num_rows($r) > 0 )
{
    $inc = 1;
    while($row = mysqli_fetch_assoc($r))
    {
        $id = $row['sortdate'];
        $out1[$id]['location_name'] = $row['location_name'];
        $out1[$id]['dealer_name'] = $row['dname'];
        $out1[$id]['date'] = $row['date'];
        $out1[$id]['dealer_id'] = $row['dealer_id'];
        $out1[$id]['total_calls'] = get_total_calls($person_id, $row['date']);
        $out1[$id]['total_sales'] = get_total_sales($person_id, $row['date']);
        $final_arry['actual'] = $out1;
    }
}
function get_total_calls($id, $fdate)
{
    global $dbc;
    $fdate = str_replace('-', '', $fdate);
    $q = "SELECT COUNT(order_id) AS tot_calls FROM user_sales_order WHERE DATE_FORMAT(date, '%Y%m%d') = '$fdate' AND user_id = '$id'";
    $r = mysqli_query($dbc, $q);
    if($r)
        $row = mysqli_fetch_assoc($r);
     $tot_calls = $row['tot_calls'];
    return $tot_calls;
}
function get_total_sales($id, $fdate)
{
    global $dbc;
    $fdate = str_replace('-', '', $fdate);
    $q = "SELECT  SUM(total_sale_value) as total_sale_value FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) WHERE DATE_FORMAT(date, '%Y%m%d') = '$fdate' AND user_id = '$id'";
// h1($q);
    $r = mysqli_query($dbc, $q);
    if($r)
     $row = mysqli_fetch_assoc($r);
     $tot_sale_value = $row['total_sale_value'];
    return $tot_sale_value;
}
//echo '<pre>';
//print_r($final_arry);
//$final_array = array("result"=>$essential);
echo $try = json_encode(array('manacle' => $final_arry));
######################## Planned MTP ends here  #######################################################

