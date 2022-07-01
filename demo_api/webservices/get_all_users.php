<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['start_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_date']))); else $s_date = 0;
if(isset($_GET['end_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_date']))); else $e_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;
if(isset($_GET['role_id'])) $role_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['role_id']))); else $role_id = 0;

recursivejuniors($user_id); 
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';

$users = array();
$array=array();
if($role_id==1){
$q1 = "SELECT person.id AS id,CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name,person.role_id,_role.rolename FROM person INNER JOIN person_login ON person.id=person_login.person_id INNER JOIN _role ON _role.role_id=person.role_id WHERE person_status='1' AND person.id!='1' GROUP BY person.id";
}else{
$q1 = "SELECT person.id AS id,CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name,person.role_id,_role.rolename FROM person INNER JOIN person_login ON person.id=person_login.person_id INNER JOIN _role ON _role.role_id=person.role_id WHERE person_status='1' AND person.id IN (".$juniors.",$user_id) GROUP BY person.id";
}
//h1($q1);
$r1 = mysqli_query($dbc, $q1);
while($row1 = mysqli_fetch_array($r1)){
$users['user_id'] =$row1['id']; 
$users['user_name'] =$row1['user_name'];
$users['role_id'] =$row1['role_id'];
$users['rolename'] =$row1['rolename'];
$array[]=$users;
}
$f = array("result"=>$array);
$data = json_encode($f);
echo  $data;

?>          