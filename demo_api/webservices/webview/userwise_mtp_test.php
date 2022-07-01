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

// if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
// {
  
// //   $approved_by= $_GET['user_id']; die;
//      $curdate = date('Y-m-d H:i:s');
//      $person_id=$_POST['person_id'];
//      // print_r($_POST);

      
//   foreach($_POST['wdate'] as $key=>$v)
//        {  
//        $p=$v;
//       //print_r($v);
//       $cdate1= $_POST['wdate'][$key];

//       $workdate= date('Y-m-d', strtotime($cdate1));
//      $dayname = $_POST['dayname'][$key];
//       $working_status_id  = $_POST['working_status_id'][$key];
//       $dealer_id  = $_POST['dealer_id'][$key];
//       $locations  = $_POST['locations'][$key]; 
//       $travel_mode  = $_POST['travel_mode'][$key]; 
//       $seniorapproval  = $_POST['seniorapproval'][$key]; 
//      $approved_id  = $_POST['approved_by'][$key];        
//      $mtp_id  = $_POST['mtpid'][$key];      
//     $admin_remark  = $_POST['admin_remark'][$key];
//  if(!empty($workdate)){
// 		 $qupdate = "UPDATE monthly_tour_program SET working_date='$workdate',dayname	='$dayname',
//                     dealer_id='$dealer_id',locations='$location_id',approved_by ='$approved_id',approved_on=NOW()
//                     ,admin_remark='$admin_remark',`admin_approved`='$seniorapproval' WHERE id=  '$mtp_id'";
//                    // h1($qupdate); exit;
                 
//                     $run = mysqli_query($dbc,$qupdate);
       

//        }
//       }
  
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>mSELL  </title>
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


<!-- <form name="sub" id="sub" action="" method="post"> -->

<!-- <div class="row"  style="position:fixed; width:100%; top:30px;">
<input type="submit"  value="Submit" name="submit"  style="width:100%; background-color:#0e597c;color:#fff;" class=" form-control" >
 
 
 </div> -->

         
         <div class="row"  style="margin-top:20%">
         <table id="example" class="table table-striped table-bordered table-hover" style="border: 1px solid black">
                    <thead class="hidden-xs">
                    <tr>                  
                    <td>Data</td>
                    </tr>

                     </thead>

                      <tbody>
 

<?php

	$inc=1;
  
              //pre($value);
  //            $query = "SELECT dealer.name as dealername ,_task_of_the_day.task as taskoftheday,location_6.name as towns, location_7.name as beat,monthly_tour_program.*,_travelling_mode.mode AS travelmode, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
		// DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
		// DATE_FORMAT(working_date,'%y%m%d') AS sortodate,
  //   DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program`
  //   LEFT JOIN _travelling_mode on _travelling_mode.id=monthly_tour_program.travel_mode 
	 // LEFT JOIN _task_of_the_day on _task_of_the_day.id=monthly_tour_program.working_status_id 
  //  	 LEFT JOIN dealer on dealer.id=monthly_tour_program.dealer_id 
  // LEFT JOIN location_7 ON location_7.id=monthly_tour_program.locations 
  // LEFT JOIN location_6 ON location_6.id=monthly_tour_program.town 	
  //       INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$user_id AND date_format(working_date,'%Y-%m-%d') = '$to_date' 
  // UNION All 
  // SELECT dealer.name as dealername ,_task_of_the_day.task as taskoftheday,location_6.name as towns, location_7.name as beat,monthly_tour_program_log.*,_travelling_mode.mode AS travelmode, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
		// DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
		// DATE_FORMAT(working_date,'%y%m%d') AS sortodate,
  //   DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program_log`
  //   LEFT JOIN _travelling_mode on _travelling_mode.id=monthly_tour_program_log.travel_mode 
	 // LEFT JOIN _task_of_the_day on _task_of_the_day.id=monthly_tour_program_log.working_status_id 
  //  	 LEFT JOIN dealer on dealer.id=monthly_tour_program_log.dealer_id 
  // LEFT JOIN location_7 ON location_7.id=monthly_tour_program_log.locations 
  // LEFT JOIN location_6 ON location_6.id=monthly_tour_program_log.town 	
  //       INNER JOIN person ON person.id=monthly_tour_program_log.person_id WHERE person_id=$user_id AND date_format(working_date,'%Y-%m-%d') = '$to_date'   "; 

   $query = "SELECT _task_of_the_day.task as taskoftheday,monthly_tour_program.*,_travelling_mode.mode AS travelmode, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
    DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
    DATE_FORMAT(working_date,'%y%m%d') AS sortodate,
    DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date,monthly_tour_program.dealer_id,monthly_tour_program.town,monthly_tour_program.locations FROM `monthly_tour_program`
    LEFT JOIN _travelling_mode on _travelling_mode.id=monthly_tour_program.travel_mode 
   LEFT JOIN _task_of_the_day on _task_of_the_day.id=monthly_tour_program.working_status_id 
  --    LEFT JOIN dealer on dealer.id=monthly_tour_program.dealer_id 
  -- LEFT JOIN location_7 ON location_7.id=monthly_tour_program.locations 
  -- LEFT JOIN location_6 ON location_6.id=monthly_tour_program.town   
        INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$user_id AND date_format(working_date,'%Y-%m-%d') = '$to_date' "; 
  //h1($query);
   
 //h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
$mtpid=$rows['id'];

$multiple_dealer_id = rtrim($rows['dealer_id'],',');
$multiple_town_id = rtrim($rows['town'],',');
$multiple_locations_id = rtrim($rows['locations'],',');

$mult_dealer_name_query = "SELECT GROUP_CONCAT(DISTINCT name) as dealers_name from dealer where id IN ($multiple_dealer_id) LIMIT 1 ";
$mult_dealer_name_run = mysqli_query($dbc,$mult_dealer_name_query);
$mult_dealer_name_assoc = mysqli_fetch_assoc($mult_dealer_name_run);
$mult_dealers_name = $mult_dealer_name_assoc['dealers_name'];

$mult_town_name_query = "SELECT GROUP_CONCAT(DISTINCT name) as town_name from location_6 where id IN ($multiple_town_id) LIMIT 1 ";
$mult_town_name_run = mysqli_query($dbc,$mult_town_name_query);
$mult_town_name_assoc = mysqli_fetch_assoc($mult_town_name_run);
$mult_towns_name = $mult_town_name_assoc['town_name'];

$mult_location_name_query = "SELECT GROUP_CONCAT(DISTINCT name) as locations_name from location_7 where id IN ($multiple_locations_id) LIMIT 1 ";
$mult_location_name_run = mysqli_query($dbc,$mult_location_name_query);
$mult_location_name_assoc = mysqli_fetch_assoc($mult_location_name_run);
$mult_locations_name = $mult_location_name_assoc['locations_name'];


  $admin_approved=$rows['admin_approved'];
 if(isset($admin_approved) && $admin_approved == 2)
{
  $checked = 'checked';
  $values = 2;
  $status='Approved As Modification';
  $r= '<p style="color: black; background-color: orange; font-size:100%; ">';
}
elseif(isset($admin_approved) && $admin_approved == 1)
{
  $checked = 'checked';
  $values = 1;
  $status='Approved As Filled';
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
 <div class="col-md-12 col-sm-12 col-lg-12" style="color: black; background-color:#dcebf2;">
 

   <b> Date : <?php echo $rows['wdate']; ?> </b></br>
  <b>  Day   :</b>  <?php echo $rows['dayname']; ?>  </br>                 
   <b>User Name :</b>  <?php echo $rows['personname']; ?></br>
 <b>Dealer Name  :</b> <?php echo $mult_dealers_name; ?></br> 
 <b>Town :</b> <?php echo $mult_towns_name; ?></br>
<b>Beat :</b> <?php echo $mult_locations_name; ?></br>
  <b>Task Of The day :</b> <?php echo $rows['taskoftheday']; ?></br> 
  <b>Productive Call:</b> <?php echo $rows['pc']; ?></br> 
  <b>RD(RV) :</b> <?php echo $rows['rd']; ?></br> 
  <b>Collection(RV):</b> <?php echo $rows['collection']; ?></br> 
  <b>Primary Order (RV):</b> <?php echo $rows['primary_ord']; ?></br> 
 <b>New Outlet  Opening:</b> <?php echo $rows['new_outlet']; ?></br> 
 <b>Any Other Task (Remarks):</b> <?php echo $rows['any_other_task']; ?></br> 
 <b> Status :</b> <?php echo $status; ?></br>
  <b> Remarks :</b> <?php echo $rows['admin_remark']; ?> </br>
           
 
 
 </div>
</td>
</tr>                   

                                         
                  

<?php 
 $inc++;
 }
     
				
                  ?>
                        </tbody>
                </table>                
            </div>
         

</div>
</body>
</html>






