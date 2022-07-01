<?php	

//TEST URL - http://localhost/suryasteel/webservices/submitExpense.php?imei=1342537475886996&travelling_allowance=100&travelling_mode_id=1&start_journey=abc&end_journey=abcgmail.com&total_calls=5&drawing_allowance=100&other_expense=2300&remarks=sdk;jfdsofjdk&datetime=2014-07-05 12:45:15
require_once('../admin/include/conectdb.php');
require_once('functions.php');


//submitExpense.php?
//imei=1234567891234685&
//travel_mode=1&
//start_jrny=hhy&
//end_jrny=gg&
//ta=22&
//da=2&
//misc=2&
//date=2014-07-28&
//time=15:58:16&
//ttl_calls=25

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$num = mysqli_num_rows($user_qry);
if($num>0){

    $user_id=$user_res['id'];
    if(isset($_GET['travel_mode'])) $travelling_mode_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['travel_mode']))); else $travelling_mode_id = 0;
    if(isset($_GET['start_jrny'])) $start_journey = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['start_jrny']))); else $start_journey = '';
    if(isset($_GET['end_jrny'])) $end_journey = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['end_jrny']))); else $end_journey = '';
    if(isset($_GET['ttl_calls'])) $total_calls = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['ttl_calls']))); else $total_calls = 0;
    if(isset($_GET['ta'])) $travelling_allowance = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['ta']))); else $travelling_allowance = 0;
    if(isset($_GET['da'])) $drawing_allowance = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['da']))); else $drawing_allowance = 0;
    if(isset($_GET['misc'])) $other_expense = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['misc']))); else $other_expense = 0;
    if(isset($_GET['remarks'])) $remarks = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['remarks']))); else $remarks = '';
    if(isset($_GET['date'])) $date= mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date']))); else $date= '';
    if(isset($_GET['time'])) $time= mysqli_real_escape_string($dbc, trim(stripslashes($_GET['time']))); else $time = '';    

    $tablename="user_expense_report";
    $task="insert";
    $arraydata[]="person_id='".$user_id."'";
    $arraydata[]="end_journey='".$end_journey."'";
    $arraydata[]="travelling_allowance='".$travelling_allowance."'";
    $arraydata[]="total_calls='".$total_calls."'";
    $arraydata[]="travelling_mode_id='".$travelling_mode_id."'";
    $arraydata[]="start_journey='".$start_journey."'";
    $arraydata[]="drawing_allowance='".$drawing_allowance."'";
    $arraydata[]="other_expense='".$other_expense."'";
    $arraydata[]="remarks='".$remarks."'";
    $arraydata[]="submit_date='".$date."'";
    $arraydata[]="submit_time='".$time."'";
    $condition[]="";
    
    $code="";

    $result=insert_update($tablename,$arraydata,$task,$condition,$code);
    if($result==1)
            {
            echo "Y";
        }else{
            echo "N";
        }
}
?>

