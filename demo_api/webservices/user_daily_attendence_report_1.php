<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

function fetch_data($table, $col, $where)
{
	global $dbc;
	list($opt, $rs) = run_query($dbc, "SELECT $col FROM $table WHERE $where LIMIT 1");
	return ($opt) ? $rs : '';
}


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$report_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['report_date'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "select 
    person.id as id,
    concat(first_name,' ',middle_name,' ',last_name) as fullname,
    ws.name as working_status, 
    date_format(uda.work_date,'%H:%i:%s') as checkin_time,
    uda.remarks
    FROM person INNER JOIN user_daily_attendance uda ON person.id=uda.user_id 
    INNER JOIN _working_status ws ON ws.id=uda.work_status
    where date_format(uda.work_date,'%Y-%m-%d')='$report_date'  and person.id in (".$juniors.",$user_id)";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
      $id = $rows['id'];
      $checkout = fetch_data('check_out', "date_format(work_date,'%H:%i:%s') as checkout_time", "user_id='$id' AND date_format(work_date,'%Y-%m-%d')='$report_date'");
      $rows['checkout_time'] = $checkout['checkout_time'];
      $data[] = $rows;
      $checkout='';
}
if(empty($data)){
   $rows['id'] = 0;
   $rows['fullname'] = 0;
   $rows['working_status'] = 0;
   $rows['checkin_time'] = 0;
   $rows['remarks'] = 0;
   $rows['checkout_time'] = 0; 
   $data[] = $rows;
}
$final_data = array('result'=>$data);
$result = json_encode($final_data);
echo $result;
