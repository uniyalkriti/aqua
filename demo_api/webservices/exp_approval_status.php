<?php
session_start();
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
 
$approved_by=$_GET['approved_by'];


global $dbc;
$GLOBALS['EMAILs'];

if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
{
  
//   $approved_by= $_GET['user_id']; die;
     $curdate = date('Y-m-d H:i:s');
     $suser_id=$_POST['sid'];
     // print_r($_POST);exit;

      
  foreach($_POST['seniorapproval'] as $key=>$v)
       {  
       $p=$v;
      //print_r($v);
      $cdate1= $_POST['wdate'][$key];

      $workdate= date('Y-m-d', strtotime($cdate1));      
     $mtp_id  = $_POST['mtpid'][$key];      
    $admin_remark  = $_POST['admin_remark'][$key];
    $person_id  = $_POST['person_id'][$key];
    $total  = $_POST['total'][$key];

    $qcheck="SELECT status FROM user_daily_expense WHERE order_id=  '$mtp_id'";
    $rcheck=mysqli_query($dbc,$qcheck);
    $rowcheck=mysqli_fetch_assoc($rcheck);
    $statuscheck=$rowcheck['status'];
    if($statuscheck!=$v)
    {

 if(!empty($workdate)){
		 $qupdate = "UPDATE user_daily_expense SET status='$v',approved_by='$user_id',remarks='$admin_remark',approved_date=NOW()  WHERE order_id=  '$mtp_id'";
                   // h1($qupdate); exit;
                    $run = mysqli_query($dbc,$qupdate);
 if($run)
 {      
  $eq="SELECT person.person_fullname AS jname,email AS jemail FROM person WHERE person.id='$person_id'";

$runeq = mysqli_query($dbc,$eq);
$roweq=mysqli_fetch_assoc($runeq);
$jname=$roweq['jname'];
$jemail=$roweq['jemail'];
 $sq="SELECT person.person_fullname AS sname,email AS semail FROM person WHERE person.id='$suser_id'";
$runsq = mysqli_query($dbc,$sq);
$rowsq=mysqli_fetch_assoc($runsq);
$sname=$rowsq['sname'];
$semail=$rowsq['semail'];
$estatus="Approved";
unset($data);
$data.="Hello $jname ,</br><p></p>";
$data.="Your request for $cdate1 of daily expense amount INR $total has been $estatus .";
$data.="<p></p></br>Regards</br>$sname";
require_once '../admin/mailer_classes/class.phpmailer.php';
$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP //https://accounts.google.com/DisplayUnlockCaptcha
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587,465, if using ssl port no. should be 465.
$mail->IsHTML(true);
$mail->Username = $GLOBALS['EMAILs'][1];
$mail->Password = $GLOBALS['EMAILs'][2];
$mail->SetFrom($GLOBALS['EMAILs'][1],'mSELL Uniline:Please do not reply');
//$mail->AddEmbeddedImage('./admin/images/green_check.jpg', 'green_check');
//$mail->AddEmbeddedImage('./admin/images/red_cross.png', 'red_cross');
//$mail->Subject = "Functionality Usage Analysis Report For Date $show_date";
$mail->Subject = "Daily Expense Approval";
$mail->Body = $data;
//echo $jemail;
//echo $semail;
//exit;
//$mail->AddAddress('shivank@manacleindia.com','Mr.Shivank Srivastava');
 $mail->AddAddress($jemail,$jname);
 $mail->AddCC($semail,$sname);
$mail->AddCC('hr@uniline.in','Uniline HR');
if(!empty($jemail) && !empty($semail))
{ 
$mail->Send();
unset($mail);
}
}
}  
}
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Msell Uniline </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
  $( function() {
    $( ".datepicker" ).datepicker();
  } );
  </script>

<style>
     body{
     overflow:hidden;
     } 
      tbody {
      position: fixed;
    height: 400px;
    width: 100%;
    overflow: auto;
    overflow-x: hidden;
    display: block;
}

tbody tr{
display: table;
width:100%;
table-layout:fixed}
</style>
</head>




<body>

<div  class="container-fluid">

<form name="sub" id="sub" action="" method="post">

<div class="row"  style="position:fixed; width:100%; top:30px;">
<input type="submit"  value="Submit" name="submit"  style="width:100%; background-color:#0e597c;color:#fff;" class=" form-control" >
 </div>

         
         <div class="row"  style="margin-top:20%">
         <table id="example" class="table table-striped table-bordered table-hover" style="border: 1px solid black">
                    <thead class="hidden-xs">
                    <tr>                  
                    <td></td>
                    </tr>

                     </thead>

                      <tbody>
<!--  <div class="col-md-12 col-sm-12 col-lg-12">
<b>Approved All:</b><input  class="checkb"  type="checkbox" name="checkbox[]" onClick="selectall(this)">
  
</div> -->

<?php

	$inc=1;
  
              //pre($value);
             $query = "SELECT user_daily_expense.id AS id,user_daily_expense.*,_travelling_mode.mode,person.person_fullname,DATE_FORMAT(`submit_date_time`,'%d-%m-%Y') AS wdate FROM user_daily_expense INNER JOIN person ON person.id=user_daily_expense.user_id INNER JOIN _travelling_mode ON _travelling_mode.id=user_daily_expense.travelling_mode WHERE DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')<='$to_date' AND approved_for='$user_id' AND status=1";
   //h1($query);
   
 //h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
$mtpid=$rows['order_id'];

  $admin_approved=$rows['status'];
 if(isset($admin_approved) && $admin_approved == 0)
{
  $checked = 'checked';
  $values = 1;
  $status='Approved';
 $r= '<p style="color: black; background-color: lightgreen; font-size:100%; ">';
}elseif(isset($admin_approved) && $admin_approved == 2)
{
  $checked = 'checked';
  $values = 2;
  $status='Decline';
 $r= '<p style="color: black; background-color: lightgreen; font-size:100%; ">';
}else
{
$checked = '';
$values = 0;
$status='Pending';
$r= '<p style="color: black; background-color: orange; font-size:100%; ">';
}
 
  
?>

 <tr><td>
 <div class="col-md-12 col-sm-12 col-lg-12" >
 <p style="color: black; background-color:#50beed; font-size:100%; ">
  <b> S.No :</b> <?php echo $inc; ?> </br>
   <b> Date :</b> <?php echo $rows['wdate']; ?> </br>                
   <b>User Name :</b>  <?php echo $rows['person_fullname']; ?></br>
   <b>Travel Mode :</b><?php echo $rows['mode'] ;?></br>
   <b>Total Expense :</b> <?php echo $rows['total_expense']; ?></br> </p>
   <?php echo $r;?><b> Status :</b> <?php echo $status; ?></br>
  <b> Remarks :</b> <input type="text" name="admin_remark[<?=$mtpid?>]" value="<?php echo $rows['remarks']; ?>"> </br>
 <b> Approved :</b> <input type="radio" class="checkb" name="seniorapproval[<?=$mtpid?>]" id="seniorapproval<?=$inc?>" value="1"<?=$checked?>>&nbsp;&nbsp;&nbsp;
<b> Decline :</b> <input type="radio" class="checkb" name="seniorapproval[<?=$mtpid?>]" id="seniorapproval<?=$inc?>" value="2"<?=$checked?>> </br>               
 
 
 </div>
</td>
</tr>                   
                          <?php 

          ?> 

                                         
                  
<input type="hidden" name="mtpid[<?=$mtpid?>]" value="<?=$mtpid?>">
<input type="hidden" name="approved_by[<?=$mtpid?>]" value="<?=$approved_by?>">
<input type="hidden" name="wdate[<?=$mtpid?>]" value="<?=$rows['wdate']?>">
<input type="hidden" name="person_id[<?=$mtpid?>]" value="<?=$rows['user_id']?>">
<input type="hidden" name="total[<?=$mtpid?>]" value="<?=$rows['total_expense']?>">
<input type="hidden" name="sid" value="<?=$user_id?>">
<?php 
 $inc++;
 }






                     
				
                  ?>
                        </tbody>
                </table>                
            </div>
         
</form>
</div>
</body>
</html>





<!-- <script>
function selectall(source) {
  checkboxes = document.getElementsByClassName('checkb');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}


$("#sub").submit(function () {
	//alert('ank');
    var this_master = $(this);
    this_master.find('input[type="checkbox"]').each( function () {
        var checkbox_this = $(this);
        if( checkbox_this.is(":checked") == true ) {
            checkbox_this.attr('value','1');
        } else {
            checkbox_this.prop('checked',true);
            //DONT' ITS JUST CHECK THE CHECKBOX TO SUBMIT FORM DATA    
            checkbox_this.attr('value','0');
        }
    })
})
 </script> -->
