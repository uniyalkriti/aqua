<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
if(isset($_GET['from'])) $from = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from']))); else $from = 0;
if(isset($_GET['to'])) $to = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to']))); else $to = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;

    $myobj=new mtp();
    $data_info = array();
    $total_sale = array();
    $final_data_info = array();

   $query = "SELECT DISTINCT person_id,CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname,travelling_allowance,drawing_allowance,other_expense,start_journey,end_journey,submit_date FROM user_expense_report uer INNER JOIN person ON person.id=uer.person_id WHERE `submit_date` >='$from' AND `submit_date` <='$to' AND uer.person_id = $user_id";
 //echo $query;die;
h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
   // $rows['total_allowance'] = $rows['travelling_allowance']+$rows['drawing_allowance']+$rows['other_expense'];
    //$data[] = $rows['total_allowance'];
    $data[] = $rows;
}

    if(empty($data)){
        $final_data = Array ( 'result' => Array ('0' =>Array ( 'person_id' =>NULL,'person_fullname' =>NULL,'total_allowance'=>NULL,'start_journey'=>NULL,'end_journey'=>NULL)));
    }else{
        $final_data = array('result'=>$data);
    }

$result = json_encode($final_data);
echo $result;

?>