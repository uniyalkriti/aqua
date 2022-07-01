<?php
error_reporting(0);
include('../admin/include/conectdb.php');
session_start();
$_SESSION['juniordata']=array();
$sales_head=$_GET['sales_head'];
$crdate=$_GET['date'];
$qury = "SELECT id,email,CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name FROM person WHERE id='$sales_head' LIMIT 0,1";
//echo $qury;DIE;
$run=  mysqli_query($dbc, $qury);
$sales_head_in= mysqli_fetch_array($run);
//print_r($sales_head_in);die;
$sales_head_email=$sales_head_in[1];
$sales_head_name=$sales_head_in[2];
    $date=  date('d-M-Y');
//    $display_date = date('d-M-Y',strtotime($date.' -2 day'));
//    $attn_date=  date('Y-m-d');
//    $predate = date('Y-m-d',strtotime($attn_date.' -2 day'));

     function recursiveall2($id) {
        global $dbc;
//static $data;
        $qry = "";
        $res1 = "";
        $res2 = "";
        $qry = mysqli_query($dbc, "select id  from person INNER JOIN person_login ON person.id=person_login.person_id where person_id_senior=trim('" . $id . "') AND person_login.person_status='1'");

        $num = mysqli_num_rows($qry);
       // echo $num;die; 
        if ($num <= 0) {
            $res1 = mysqli_fetch_assoc($qry);
            if ($res1['id'] != "") {
                $_SESSION['juniordata'][] = "'" . $res1['id'] . "'";
            }
        } else {
            while ($res2 = mysqli_fetch_assoc($qry)) {
                if ($res2['id'] != "") {
                    $_SESSION['juniordata'][] = "'" . $res2['id'] . "'";
                    recursiveall2($res2['id']);
                }
            }
        }
    }

    if(!empty($sales_head) && $sales_head !=0){

   recursiveall2($sales_head);
//echo count($_SESSION['juniordata']);die;
    $juniors = join(',',$_SESSION['juniordata']);
       //print_r($_SESSION);die;
    $_SESSION['juniordata']='';
   // echo $juniors;die('jnr id');
   // echo $juniors;die;
    
if(!empty($juniors)){
//echo $juniors;die;
 $q="SELECT IF( ISNULL( user_daily_attendance.user_id ) , 'ABSENT', 'PRESENT' ) AS attn_status,_working_status.name as work_status,DATE_FORMAT(user_daily_attendance.work_date,'%e/%b/%Y') AS work_date,DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-/%d') as `date`,DATE_FORMAT(user_daily_attendance.work_date,'%h:%i:%s %p') AS att_time,user_daily_attendance.remarks as att_remarks,DATE_FORMAT(check_out.work_date,'%h:%i:%s %p') as ch_time,CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,person.emp_code as emp_code,rolename as role_name,mobile,(select CONCAT_WS(' ',per.first_name,per.middle_name,per.last_name) from person per where id=person.person_id_senior ) as senior,check_out.remarks as ch_remarks FROM`person` LEFT JOIN check_out ON person.id=check_out.user_id AND DATE_FORMAT(check_out.work_date,'%Y-%m-%d') ='$crdate' INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1' left join user_daily_attendance on person.id=user_daily_attendance.user_id and date_format(user_daily_attendance.work_date,'%Y-%m-%d') = '$crdate' INNER JOIN _role ON _role.role_id= person.role_id AND _role.role_group_id =11 LEFT JOIN _working_status ON user_daily_attendance.work_status = _working_status.id INNER JOIN state ON person.state_id=state.stateid WHERE person.id IN($juniors) AND IF(person.role_id=14 OR person.role_id=15, 0, 1) ORDER BY user_daily_attendance.work_date DESC";
//echo $q;
//echo "<br>";
    $r=  mysqli_query($dbc, $q);
    $num=  mysqli_num_rows($r);
    //echo $num;die; 
    $inc=1;
    $data_info = array();
    $data_final = array();
   
    if($num>0){
         
        while($row=  mysqli_fetch_array($r)){     
        if($row['role_name']=="RSM" || $row['role_name']=="ASM") {
         $chktime="10:30:59";
        }else{
        $chktime="10:00:59";
        }
       // $attendance_data=get_attendance($row['uid'],$attn_date);
  if(isset($row['att_time']) && $row['att_time'] !='' && $row['att_time']>$chktime)
      {
    
      
            $data_info['full_name'] = isset($row['full_name'])?$row['full_name']:'';
            $data_info['emp_code'] = isset($row['emp_code'])?$row['emp_code']:'';
            $data_info['role_name'] = isset($row['role_name'])?$row['role_name']:'';
            $data_info['mobile'] = isset($row['mobile'])?$row['mobile']:'';
            $data_info['senior'] = isset($row['senior'])?$row['senior']:'';
            $data_info['sales_head'] = isset($row['sales_head'])?$row['sales_head']:'';
            $data_info['work_status'] = isset($row['work_status'])?$row['work_status']:'';
            $data_info['att_time'] = isset($row['att_time'])?$row['att_time']:'';
            $data_info['att_remarks'] = isset($row['att_remarks'])?$row['att_remarks']:'';
            $data_info['ch_time'] = isset($row['ch_time'])?$row['ch_time']:'';
            $data_info['ch_remarks'] = isset($row['ch_remarks'])?$row['ch_remarks']:'';
            $data_info['date'] = $crdate;
            $data_final[] = $data_info;
    }
    }
     $final_array = array("result"=>$data_final);	
     $data = json_encode($final_array);
     echo $data;
            $file = fopen("notification/".$sales_head.".php","w");
            fwrite($file,$data);
            fclose($file);
   }else{
	   
	        $data_info['full_name'] = null;
            $data_info['emp_code'] = null;
            $data_info['role_name'] = null;
            $data_info['mobile'] = null;
            $data_info['senior'] = null;
            $data_info['sales_head'] = null;
            $data_info['work_status'] = null;
            $data_info['att_time'] = null;
            $data_info['att_remarks'] = null;
            $data_info['ch_time'] = null;
            $data_info['ch_remarks'] = null;
            $data_info['date'] = $crdate;
            $data_final[] = $data_info;
            
        $final_array = array("result"=>$data_final);
        echo $data = json_encode($final_array);
        $file = fopen("notification/".$sales_head.".php","w");
        fwrite($file,$data);
        fclose($file);
   }
 
}else{

 $data_info['full_name'] = null;
            $data_info['emp_code'] = null;
            $data_info['role_name'] = null;
            $data_info['mobile'] = null;
            $data_info['senior'] = null;
            $data_info['sales_head'] = null;
            $data_info['work_status'] = null;
            $data_info['att_time'] = null;
            $data_info['att_remarks'] = null;
            $data_info['ch_time'] = null;
            $data_info['ch_remarks'] = null;
            $data_info['date'] = $crdate;
            $data_final[] = $data_info;
            
        $final_array = array("result"=>$data_final);
        echo $data = json_encode($final_array);
        $file = fopen("notification/".$sales_head.".php","w");
        fwrite($file,$data);
        fclose($file);

}

}else{

    $data_info['full_name'] = null;
            $data_info['emp_code'] = null;
            $data_info['role_name'] = null;
            $data_info['mobile'] = null;
            $data_info['senior'] = null;
            $data_info['sales_head'] = null;
            $data_info['work_status'] = null;
            $data_info['att_time'] = null;
            $data_info['att_remarks'] = null;
            $data_info['ch_time'] = null;
            $data_info['ch_remarks'] = null;
            $data_info['date'] = $crdate;
            $data_final[] = $data_info;
            
        $final_array = array("result"=>$data_final);
        echo $data = json_encode($final_array);
        $file = fopen("notification/".$sales_head.".php","w");
        fwrite($file,$data);
        fclose($file);

}