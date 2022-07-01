<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
//$dbc = @mysqli_connect('localhost','root','Dcatch','msell-dsgroup-dms') OR die ('could not connect:');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();
if (!empty($_POST['user_id']) && !empty($_POST['month'])) {
    $uid = $_POST['user_id'];
    $month = $_POST['month'];
} elseif (empty($_POST['user_id'])) {
    $response = array("response" => false, "message" => 'User Id Required');
} elseif (empty($_POST['month'])) {
    $response = array("response" => false, "message" => 'Month Required');
} else {
    $response = array("response" => false, "message" => 'Something went wrong');
}

if (!empty($uid) && !empty($month)) {

    #Create month data
    $arr = [];
    $newArr=[];
    $last_date = date("Y-m-t", strtotime($month));
    $first_date = $month . "-01";

    $query = "SELECT monthly_tour_program.*, dealer.name as dealer_name, location_5.name as beat, location_4.name as town,_task_of_the_day.task as working_status from monthly_tour_program
    LEFT JOIN dealer ON dealer.id = dealer_id 
    LEFT JOIN location_4 ON monthly_tour_program.town=location_4.id 
    LEFT JOIN location_5 ON monthly_tour_program.locations=location_5.id 
    LEFT JOIN _task_of_the_day ON _task_of_the_day.id=monthly_tour_program.working_status_id
     WHERE person_id=$uid and working_date >= '$first_date' and working_date <= '$last_date' ORDER BY working_date";
//    echo $query;die;

//    echo $query;die;
    $query_run = mysqli_query($dbc, $query);
    while ($rows = mysqli_fetch_assoc($query_run)) {
        $arr[] = $rows;
    }
   /* while ($rows = mysqli_fetch_assoc($query_run)) {
        $arr[$rows['working_date']] = $rows;
    }
    $newArr = array();
    for ($x=0;$x<=date("t", strtotime($month));$x++)
    {
       $y = $x+1;
        if (!empty($arr[$month . "-".$y]))
        {
            $newArr[$y]=$arr[$month . "-".$y];
        }
        else{
            $newArr[$y]=(object)[];
        }
    }
    */
//print_r($newArr); exit;
    $a[]=$newArr;
    $response = array("response" => true,
        "message" => 'MTP DATA',
        "data" => $arr);

}
echo json_encode($response);

