<?php
require_once('../admin/include/conectdb.php');
require_once 'functions.php';
if(isset($_POST['response'])){$check=$_POST['response'];} else $check='';
//print_r($_POST['response']);exit;
//$check='{"response":{"suggestion_data":"Kick ","emp_name":"dd","emp_designation":"ff","emp_department":"gg","emp_cadre":"sd","emp_band":"ff","emp_date_of_joining":"2017-11-17","emp_date":"2017-11-18","HOD_name":"dff","HOD_designation":"fgg","HOD_department":"sdd","HOD_cadre":"fgg","HOD_band":"ddd","HOD_date_of_joining":"2017-11-17","HOD_date":"2017-11-23","question_data":[{"question_id":"1","option_id":"1","question_type_id":"1"},{"question_id":"2","option_id":"3","question_type_id":"1"},{"question_id":"3","option_id":"4","question_type_id":"1"},{"question_id":"1","option_id":"2","question_type_id":"1"},{"question_id":"2","option_id":"3","question_type_id":"1"},{"question_id":"1","option_id":"3","question_type_id":"1"},{"question_id":"2","option_id":"3","question_type_id":"1"},{"question_id":"3","option_id":"4","question_type_id":"1"},{"question_id":"1","option_id":"2","question_type_id":"1"},{"question_id":"2","option_id":"3","question_type_id":"1"},{"question_id":"3","option_id":"4","question_type_id":"1"},{"question_id":"1","option_id":"2","question_type_id":"1"},{"question_id":"2","option_id":"3","question_type_id":"1"}],"user_id":"719"}}';
$data=json_decode($check);


if(!empty($data))
{
//*************************************** DATA LIST **************************************************
$user_id=$data->response->user_id;
$unique_id=$data->response->unique_id;
//$unique_id=date('ymdhis').$user_id;
$question_data = $data->response->question_data;
$suggestion_data=$data->response->suggestion_data;
$emp_name=$data->response->emp_name;
$emp_designation=$data->response->emp_designation;
$emp_department=$data->response->emp_department;
$emp_cadre=$data->response->emp_cadre;
$emp_band=$data->response->emp_band;
$emp_date_of_joining=$data->response->emp_date_of_joining;
$emp_date=$data->response->emp_date;
$HOD_name=$data->response->HOD_name;
$HOD_designation=$data->response->HOD_designation;
$HOD_department=$data->response->HOD_department;
$HOD_cadre=$data->response->HOD_cadre;
$HOD_band=$data->response->HOD_band;
$HOD_date_of_joining=$data->response->HOD_date_of_joining;
$HOD_date=$data->response->HOD_date;

//*******************************************************************************//

$q="SELECT * From person_login WHERE person_id='$user_id'";
$user_res= mysqli_query($dbc, $q);
$q_person=  mysqli_fetch_assoc($user_res);
$person_id=$q_person['person_id'];
$status=$q_person['person_status'];
if($status=='1')
{

$q_question_survey_details="INSERT INTO `question_survey_details`(`unique_id`, `user_id`, `date_time`, `emp_name`, `emp_designation`, `emp_department`, `emp_cadre`, `emp_band`, `emp_date_of_joining`, `emp_date`, `HOD_name`, `HOD_designation`, `HOD_department`, `HOD_cadre`, `HOD_band`, `HOD_date_of_joining`, `HOD_date`, `suggestion_data`) VALUES ('$unique_id','$user_id',NOW(),'$emp_name','$emp_designation','$emp_department','$emp_cadre','$emp_band','$emp_date_of_joining','$emp_date','$HOD_name','$HOD_designation',
'$HOD_department','$HOD_cadre','$HOD_band','$HOD_date_of_joining','$HOD_date','$suggestion_data')";
//echo $q_question_survey_details.'<br>';
$run_question_survey_details=mysqli_query($dbc,$q_question_survey_details);
if($run_question_survey_details){
echo 'Y';
}
if(!empty($question_data)){
	$count_question_data=count($question_data);
	$os=0;
	while($os<$count_question_data){
		$question_id=$question_data[$os]->question_id;
		$option_id = $question_data[$os]->option_id;
                $question_type_id = $question_data[$os]->question_type_id;
		

                   $question_q="INSERT INTO `question_survey_answer_details`(`unique_id`, `question_id`, `question_type_id`, `answer_option_id`, `date_time`) VALUES ('$unique_id','$question_id','$question_type_id','$option_id',NOW())";
             //echo $question_q.'<br>';
                   $result=mysqli_query($dbc,$question_q);
               
                 $os++;
	}
}

}
else{
	echo 'N';
}
}else{
     echo 'N1';
}

