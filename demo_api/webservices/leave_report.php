<?php

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if (isset($_GET['start_date']))
    $start_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_date'])));
else
    $start_date = 0;
if (isset($_GET['end_date']))
    $end_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_date'])));
else
    $end_date = 0;


if ($start_date==$end_date) {
  $q = "SELECT ude.id AS eid,DATE_FORMAT(ude.from_date,'%d-%m-%Y') AS from_date,DATE_FORMAT(ude.to_date,'%d-%m-%Y') AS to_date,ude.status
      FROM user_leave_request as ude
        INNER JOIN person ON person.id=ude.user_id
        where (DATE_FORMAT(ude.to_date,'%Y-%m-%d')>='$start_date' and DATE_FORMAT(ude.from_date,'%Y-%m-%d')<='$start_date')"
          . "and ude.user_id='$user_id' GROUP BY eid";
          // echo $q;die;
}else{
  $q = "SELECT ude.id AS eid,DATE_FORMAT(ude.from_date,'%d-%m-%Y') AS from_date,DATE_FORMAT(ude.to_date,'%d-%m-%Y') AS to_date,ude.status
      FROM user_leave_request as ude
        INNER JOIN person ON person.id=ude.user_id
        where ((DATE_FORMAT(ude.from_date,'%Y-%m-%d')>='$start_date' and DATE_FORMAT(ude.from_date,'%Y-%m-%d')<='$end_date')"
        ." or (DATE_FORMAT(ude.to_date,'%Y-%m-%d')>='$start_date' and DATE_FORMAT(ude.to_date,'%Y-%m-%d')<='$end_date'))"
          . "and ude.user_id='$user_id' GROUP BY eid";
}
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        //if (!$opt)
        //    return $out;
        $out = array();
        $expense = array();
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['eid'];
            $out['eid'] = $row['eid']; // storing the item id
              $out['from_date'] = $row['from_date'];
            $out['to_date'] = $row['to_date'];
            if($row['status']=='0'){
              $status='Not Approved';
            }elseif($row['status']=='1'){
              $status='Approved';
            }elseif($row['status']=='2'){
              $status='Decline';
            }
            $out['status'] = $status;
            $expense[]=$out;
        }
    //pre($out);exit;
//     $final_array = array("result"=>$out);
//     $data = json_encode($final_array);
//     echo $data;

if(empty($out))
{

$exp[] = array("response"=>"FALSE");
}else
{
$exp[]=array("response"=>"TRUE","Leave"=>$expense);
}

$final_array = array("result"=>$exp);

$data = json_encode($final_array);

echo $data;


?>
