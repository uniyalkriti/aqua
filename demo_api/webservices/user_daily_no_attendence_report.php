<?php
//session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date']))); 

$datearray1 = array();
                $start1=date_create($from_date);
                $start_date=date_format($start1,'Y-m-d');
                $end1=date_create($to_date);
                $end_date1=date_format($end1,'Y-m-d');
                $end_date2 = strtotime("+1 day", strtotime("$end_date1"));
                $end_date=date("Y-m-d", $end_date2);
               // h1($start_date);
           $period = new DatePeriod(
     new DateTime("$start_date"),
     new DateInterval('P1D'),
     new DateTime("$end_date")
);
           foreach( $period as $date1) {
               $datearray1[$date1->format('d-M-Y')] = $date1->format('Y-m-d'); 
           }
                      //print_r($datearray1);
      $datediff=count($datearray);
    //  h1($datediff);

recursivejuniors($user_id); 

$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
global $dbc;
  $data=array();
  foreach ($datearray1 as $key => $value) {
 $query = "select 
    person.id as id,head_quar as hq,
    concat(first_name,' ',middle_name,' ',last_name) as fullname,_role.rolename  
    FROM person INNER JOIN _role ON _role.role_id=person.role_id  
    where person.id NOT IN (SELECT user_id FROM user_daily_attendance AS uda WHERE date_format(uda.work_date,'%Y-%m-%d')>='$value'  AND uda.user_id in (".$juniors.",$user_id)) AND person.id IN (".$juniors.",$user_id)";
// h1($query);
//die;
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
      $id = $rows['id'];
      $q="SELECT work_date";
      $datas['id'] = $rows['id'];
      $datas['hq'] = $rows['hq'];
      $datas['fullname'] = $rows['fullname'];
      $datas['rolename'] = $rows['rolename'];
      $datas['date'] = $key;
      $data[]=$datas;
}
}
 if(empty($data)){

    $data2 = false; //die;
    $final_data = array('response'=>$data2);
 }
 else{
      $data2 = true;
      $final_data = array('response'=>$data2,'result'=>$data);
 }

$result = json_encode($final_data);
echo $result;