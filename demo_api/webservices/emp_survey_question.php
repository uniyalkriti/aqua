<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;

if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
$q1 = "SELECT person_status,person_id_senior from person_login INNER JOIN person ON person.id=person_login.person_id where person_id = '$user_id'";
$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
$q_row = mysqli_fetch_assoc($q_res);
$person_status = $q_row['person_status'];
$person_id_senior = $q_row['person_id_senior'];
$person_status = $q_row['person_status'];
if($person_status==1){
$final_question_survey = array();
$final_question_type = array();
$final_answer_options = array();
$final_reporting_manager= array();

//**************************************** final_reporting_manager  ******************************************************
$qsp= "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) as senior_name,rolename FROM person
         INNER JOIN _role ON _role.role_id=person.role_id WHERE id=$person_id_senior LIMIT 1";
    //    h1($qstt);
                $srp = mysqli_query($dbc, $qsp);
                        
                while($rowsp = mysqli_fetch_assoc($srp))
                {
                   $final_reporting_manager[]=$rowsp;
                }
                
//**************************************** _survey_questions  ******************************************************
$qstt = "SELECT * FROM `_survey_questions` ORDER BY id ASC";
	//	h1($qstt);
                $srtt = mysqli_query($dbc, $qstt);
                        
                while($rowtt = mysqli_fetch_assoc($srtt))
                {
                   $final_question_survey[]=$rowtt;
                }
                

                        
     //**************************************** _question_type ***************************************************************
                
        $query_question_type = "SELECT * FROM `_question_type` ORDER BY id ASC";
        $run_question_type = mysqli_query($dbc, $query_question_type);
        while($question_type = mysqli_fetch_assoc($run_question_type)){    
            $final_question_type[]=$question_type;
            
        }

            
    //**************************************** _answer_options ***************************************************************
                
        $query_answer_options = "SELECT * FROM `_answer_options` ORDER BY id ASC";
        $run_answer_options = mysqli_query($dbc, $query_answer_options);
        while($answer_options = mysqli_fetch_assoc($run_answer_options)){    
            $final_answer_options[]=$answer_options;
            
        }        

    
if(empty($final_question_survey))
{
$question_data[] = array("response"=>"FALSE"); 
}else
    {
$question_data[]=array("response"=>"TRUE"
        ,"reporting_manager"=>$final_reporting_manager
        ,"question_survey"=>$final_question_survey
        ,"question_type"=>$final_question_type
        ,"question_options"=>$final_answer_options);
}
$final_array = array("result"=>$question_data);

$data = json_encode($final_array);

echo $data;
}else{
    echo"N";
}
?>          

