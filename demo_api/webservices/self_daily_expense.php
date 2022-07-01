<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
if(isset($_GET['date'])) $date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
$myobj=new mtp();
    $data_info = array();
    $total_sale = array();
    $final_data_info = array();

   $query = "SELECT travelling_allowance,drawing_allowance,other_expense,start_journey,end_journey,status FROM user_expense_report WHERE DATE_FORMAT(`submit_date`,'%Y-%m-%d')='$date' AND `person_id` = '$user_id'";
 //echo $query;die;
//h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
    $rows['value'] = $rows['travelling_allowance']+$rows['drawing_allowance']+$rows['other_expense'];
    $data[] = $rows['value'];
    if($rows['status']=0)
    {
    $data[] = 'PENDING';    
    }
    else
    {
    $data[] = 'APPROVED';  
    }
    //$data[] = $rows['status'];
    $data[] = $date;
}

    if(empty($data)){
        $final_data = Array ( 'result' => Array ('0' =>Array ( 'person_id' =>NULL,'person_fullname' =>NULL,'total_allowance'=>NULL,'start_journey'=>NULL,'end_journey'=>NULL)));
    }else{
        $final_data = array('result'=>$data);
    }

$result = json_encode($final_data);
echo $result;

?>
