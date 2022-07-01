<?php
$postdata = file_get_contents("php://input");
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();
if (isset($_POST['response'])) {
    $check = $_POST['response'];
} else {
    $check = '';

}
$allHeaders=getallheaders();
$user_id=$allHeaders['user_id'];
$company_id=$allHeaders['company_id'];
 //$check = '';

//echo 'test';die;
$check = str_replace("'", "", $check);
$data = json_decode($check);
//print_r($data);die;
if ($data) {

   // $user_id = $data->response->user_id;
    $q = "SELECT * From person_login WHERE person_id='$user_id'";

    $user_res = mysqli_query($dbc, $q);
    $q_person = mysqli_fetch_assoc($user_res);
    $person_id = $q_person['person_id'];
    $status = $q_person['person_status'];

    mysqli_query($dbc, "update person_login SET last_mobile_access_on=NOW(), app_type='SFA' Where person_id='$person_id'");
    if ($status == '1') {   
        $mtp = $data->response->Mtp;

        if (!empty($mtp)) {
            $mtp_con = count($mtp);
            $m = 0;
            while ($m < $mtp_con) {
                $working_date = $mtp[$m]->working_date;
                $dayname = $mtp[$m]->dayname;
                $working_status_id = $mtp[$m]->working_status_id;
                $dealer_id = $mtp[$m]->dealer_id;
                $locations = $mtp[$m]->locations;
                $total_calls = $mtp[$m]->total_calls;
                if(empty($total_calls)){
                   $total_calls=0; 
                }
                $total_sales = $mtp[$m]->total_sales;
                if(empty($total_sales)){
                   $total_sales=0; 
                }
                $ss_id = $mtp[$m]->ss_id;
                if(empty($ss_id)){
                   $ss_id=0; 
                }
                $travel_mode = $mtp[$m]->travel_mode;
                if(empty($travel_mode)){
                   $travel_mode=0; 
                }
                $from = $mtp[$m]->from;
                $to = $mtp[$m]->to;
                $travel_distance = $mtp[$m]->travel_distance;
                $category_wise = $mtp[$m]->category_wise;
                $mobile_save_date_time = $mtp[$m]->mobile_save_date_time;
                $pc = $mtp[$m]->pc;
                $rd = $mtp[$m]->rd;
                $collection = $mtp[$m]->collection;
                $primary_ord = $mtp[$m]->primary_ord;
                $new_outlet = $mtp[$m]->new_outlet;
                $any_other_task = $mtp[$m]->any_other_task;
                $town_id = $mtp[$m]->town_id;

                $q = "INSERT INTO `monthly_tour_program`(`company_id`,`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `mobile_save_date_time`,`upload_date_time`,`pc`,`rd`,`collection`,`primary_ord`,`new_outlet`,`any_other_task`)"
                    . " VALUES('$company_id','$user_id','$working_date','$dayname','$working_status_id','$dealer_id','$town_id','$locations','$total_calls','$total_sales','$ss_id','$travel_mode','$from','$to','$travel_distance','$category_wise','$mobile_save_date_time',NOW(),'$pc','$rd','$collection','$primary_ord','$new_outlet','$any_other_task')";
                $result = mysqli_query($dbc, $q);
                $m++;
            }
        }

    ob_start();
    ob_clean();
     $curdates=date('Y-m-d');
        $onedates=date('Y-m')."-01";
 $my_query = "SELECT SUM(rd) as total_rd
FROM monthly_tour_program
WHERE 
 DATE_FORMAT(`working_date`,'%Y-%m-%d')>='$onedates' AND DATE_FORMAT(`working_date`,'%Y-%m-%d')<='$curdates' AND person_id='$user_id'";
 $query_run=mysqli_query($dbc,$my_query);
 $fetch = mysqli_fetch_assoc($query_run);
 $mtd_target=!empty($fetch['total_rd'])?$fetch['total_rd']:0;
    $uniqueId = implode(',', $unique_id);
    $essential = array("response" => true, "mtd" => $mtd_target);
    $data = json_encode($essential);
    echo $data;

    ob_get_flush();
    ob_end_flush();

} 
}
else {
    $essential = array("response" => false, "unique_id" => 'null');
    $data = json_encode($essential);
    echo $data;
}
