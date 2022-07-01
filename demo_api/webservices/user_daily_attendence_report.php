<?php
//session_start();
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
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
$role=fetch_data('person','role_id',"id=$user_id");
// if($role['role_id']==9 || $role['role_id']==2)
// {
//   recursivejuniors_new($user_id);
// }else{
//   recursivejuniors($user_id);  
// }
//     
recursivejuniors($user_id); 

$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
$query = "select 
    person.id as id,head_quar as hq,
    concat(first_name,' ',middle_name,' ',last_name) as fullname,date_format(uda.work_date,'%Y-%m-%d') as date,
    ws.name as working_status, 
    date_format(uda.work_date,'%H:%i:%s') as checkin_time,
    uda.remarks,uda.image_name
    FROM person INNER JOIN user_daily_attendance uda ON person.id=uda.user_id 
    INNER JOIN _working_status ws ON ws.id=uda.work_status
    where date_format(uda.work_date,'%Y-%m-%d')>='$from_date' AND date_format(uda.work_date,'%Y-%m-%d')<='$to_date'   AND person.id in (".$juniors.",$user_id)";
/*h1($query);
die;*/
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
      $id = $rows['id'];
      $checkout = fetch_data('check_out', "date_format(work_date,'%H:%i:%s') as checkout_time", "user_id='$id' AND date_format(work_date,'%Y-%m-%d')='$rows[date]'");
      $rows['checkout_time'] = $checkout['checkout_time'];

      if($rows['image_name'] != NULL){
      $rows['att_image'] = "attendance_images/".$rows['image_name'];
      }else{
      $rows['att_image'] = "msell/images/avatars/profile-pic.jpg";
      }


      $data[] = $rows;
      $checkout='';
}
 if(empty($data)){
     
//    $rows['id'] = '';
//    $rows['fullname'] = '';
//    $rows['working_status'] ='';
//    $rows['checkin_time'] ;
//    $rows['remarks'] = 0;
//    $rows['checkout_time'] = 0; 

    $data2 = false; //die;
    $final_data = array('response'=>$data2);
 }
 else{
      $data2 = true;
      $final_data = array('response'=>$data2,'result'=>$data);
 }

$result = json_encode($final_data);
echo $result;