<?php
session_start();
require_once('../../admin/functions/common_function.php');
require_once('../../admin/include/conectdb.php');
require_once('../../admin/include/config.inc.php');
require_once('../../admin/include/my-functions.php');


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
//$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
 
$approved_by=$_GET['approved_by'];


global $dbc;

if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
{
  
//   $approved_by= $_GET['user_id']; die;
     $curdate = date('Y-m-d H:i:s');
     $person_id=$_POST['person_id'];
     // print_r($_POST);

      
  foreach($_POST['wdate'] as $key=>$v)
       {  
       $p=$v;
      //print_r($v);
      $cdate1= $_POST['wdate'][$key];

      $workdate= date('Y-m-d', strtotime($cdate1));
     $dayname = $_POST['dayname'][$key];
      $working_status_id  = $_POST['working_status_id'][$key];
      $dealer_id  = $_POST['dealer_id'][$key];
      $locations  = $_POST['locations'][$key]; 
      $travel_mode  = $_POST['travel_mode'][$key]; 
      $seniorapproval  = $_POST['seniorapproval'][$key]; 
     $approved_id  = $_POST['approved_by'][$key];        
     $mtp_id  = $_POST['mtpid'][$key];      
    $admin_remark  = $_POST['admin_remark'][$key];
 if(!empty($workdate)){
		 $qupdate = "UPDATE monthly_tour_program SET working_date='$workdate',dayname	='$dayname',
                    dealer_id='$dealer_id',locations='$location_id',approved_by ='$approved_id',approved_on=NOW()
                    ,admin_remark='$admin_remark',`admin_approved`='$seniorapproval' WHERE id=  '$mtp_id'";
                   // h1($qupdate); exit;
                 
                    $run = mysqli_query($dbc,$qupdate);
       

       }
      }
  
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Msell Gopal </title>
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
                    <td>ANK</td>
                    </tr>

                     </thead>

                      <tbody>
 <div class="col-md-12 col-sm-12 col-lg-12">
<b>Approved All:</b><input  class="checkb"  type="checkbox" name="checkbox[]" onClick="selectall(this)">
  
</div>

<?php

	$inc=1;
  
              //pre($value);
             $query = "SELECT l5_name as beat ,monthly_tour_program.*,_travelling_mode.mode AS travelmode, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
		DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
		DATE_FORMAT(working_date,'%y%m%d') AS sortodate,
    DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program`
    LEFT JOIN _travelling_mode on _travelling_mode.id=monthly_tour_program.travel_mode 
	LEFT JOIN location_view ON location_view.l5_id=monthly_tour_program.locations 	
        INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$user_id AND date_format(working_date,'%Y-%m') = '$to_date'  ";
   //h1($query);
   
 //h1($query);
$res = mysqli_query($dbc, $query);

while($rows = mysqli_fetch_assoc($res)){
$mtpid=$rows['id'];

  $admin_approved=$rows['admin_approved'];
 if(isset($admin_approved) && $admin_approved == 1)
{
  $checked = 'checked';
  $values = 1;
  $status='Approved';
 $r= '<p style="color: black; background-color: lightgreen; font-size:100%; ">';
}
else
{
$checked = '';
$values = 0;
$status=' Not Approved';
$r= '<p style="color: black; background-color: orange; font-size:100%; ">';
}
 
  
?>

 <tr><td>
 <div class="col-md-12 col-sm-12 col-lg-12" >
 <p style="color: black; background-color:#50beed; font-size:100%; ">
  <b> S.No :</b> <?php echo $inc; ?> </br>
   <b> Date :</b> <?php echo $rows['wdate']; ?> </br>
  <b>  Day   :</b>  <?php echo $rows['dayname']; ?>  </br>                 
   <b>User Name :</b>  <?php echo $rows['personname']; ?></br>
   <b>Travel Mode :</b><?php echo $rows['travelmode'] ;?></br>
   <b>Beat :</b> <?php echo $rows['beat']; ?></br> </p>
   <?php echo $r;?><b> Status :</b> <?php echo $status; ?></br>
  <b> Remarks :</b> <input type="text" name="admin_remark[<?=$mtpid?>]" value="<?php echo $rows['admin_remark']; ?>"> </br>
 <b> Approved :</b> <input type="checkbox" class="checkb" name="seniorapproval[<?=$mtpid?>]" id="seniorapproval<?=$inc?>" value="<?php echo $values; ?>"<?=$checked?>> </br>               
 
 
 </div>
</td>
</tr>                   
                          <?php 

          ?> 

                                         
                  
<input type="hidden" name="mtpid[<?=$mtpid?>]" value="<?=$mtpid?>">
<input type="hidden" name="approved_by[<?=$mtpid?>]" value="<?=$approved_by?>">
<input type="hidden" name="wdate[<?=$mtpid?>]" value="<?=$rows['wdate']?>">
<input type="hidden" name="dayname[<?=$mtpid?>]" value="<?=$rows['dayname']?>">
<input type="hidden" name="person_id" value="<?=$rows['person_id']?>">
<input type="hidden" name="working_status_id[<?=$mtpid?>]" value="<?=$rows['working_status_id']?>">
<input type="hidden" name="dealer_id[<?=$mtpid?>]" value="<?=$rows['dealer_id']?>"> 
<input type="hidden" name="locations[<?=$mtpid?>]" value="<?=$rows['locations']?>">
<input type="hidden" name="travel_mode[<?=$mtpid?>]" value="<?=$rows['travel_mode']?>">


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





<script>
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
 </script>
