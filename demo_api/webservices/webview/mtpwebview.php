<?php
require_once('../../admin/functions/common_function.php');
require_once('../../admin/functions/db_common_function.php');
require_once('../../admin/include/conectdb.php');
require_once('../../admin/include/config.inc.php');
require_once('../../admin/include/my-functions.php');
$suser_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
//$suser_id=53;
global $dbc;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>


<?php 
include('script.php');
?>
<!-- LOADER -->
<style>
.card-pricing.popular {
    z-index: 1;
    border: 3px solid #000;
}
.card-pricing .list-unstyled li {
    padding: .5rem 0;
    color: #6c757d;
}
.form-radio
{
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  display: inline-block;
  position: relative;
  background-color: #fff;
  color: #000;
  top: 10px;
  height: 30px;
  width: 30px;
  border: 0;
  border-radius: 50px;
  cursor: pointer;     
  margin-right: 7px;
  outline: none;
}

.form-radio:hover
{
     background-color: #000;
}
.form-radio:checked
{
     background-color: #000;
}
.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('loading.gif') 50% 50% no-repeat rgb(249,249,249);
    opacity: .8;
}
</style>
<div class="loader" ></div>

<script type="text/javascript">
$(window).load(function() {
    $(".loader").fadeOut("slow");
});
</script>
 <script>
   function load()
   {
$("#submitData").val("Filtering");
    $(".loader").fadeIn("slow");
   }

</script>

  <style>
.checkboxes label {
	
	font-size: 12px;
	color: #666;
	border-radius: 20px 20px 20px 20px;
	background: #f0f0f0;
	padding: 3px 10px;
	text-align: left;
}
input[type=checkbox]:checked + label {
	color: white;
	background: red;
}

</style>
  <style>
     html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow:hidden;
      }
  .dataTables_length,.dataTables_info,.dataTables_paginate 
  {
  display : none;
  }
 
</style>
<style>
     body{
     overflow:hidden;
     } 
      tbody {
      position: fixed;
    height: 70%;
    width: 90%;
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
  <?php 
if(isset($_POST['submit']) && ($_POST['submit'] == 'Filter' || $_POST['submit'] == 'Filtering'))
{
 $rs=array();
// print_r($_POST);
  $start = $_POST['start'];
 $end = $_POST['end'];
 if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
   $user_id=$_POST['user_id'];
       $q="SELECT monthly_tour_program.id AS mid,CONCAT_WS(' ',first_name,middle_name,last_name) AS uname,DATE_FORMAT(`working_date`,'%d-%m-%Y') AS mdate,dealer.name AS dname,l4_name AS town,l5_name AS beat,_task_of_the_day.task,pc,rd FROM monthly_tour_program 
    INNER JOIN person ON person.id=monthly_tour_program.person_id 
    INNER JOIN dealer ON dealer.id=monthly_tour_program.dealer_id 
    INNER JOIN location_view ON location_view.l5_id=monthly_tour_program.locations LEFT JOIN _task_of_the_day ON _task_of_the_day.id=monthly_tour_program.task_of_the_day 
    WHERE monthly_tour_program.person_id='$user_id' 
    AND DATE_FORMAT(`working_date`,'%Y-%m-%d')>='$start' 
    AND DATE_FORMAT(`working_date`,'%Y-%m-%d')<='$end'";
 }else{
    $q="SELECT monthly_tour_program.id AS mid,CONCAT_WS(' ',first_name,middle_name,last_name) AS uname,DATE_FORMAT(`working_date`,'%d-%m-%Y') AS mdate,dealer.name AS dname,l4_name AS town,l5_name AS beat,_task_of_the_day.task,pc,rd FROM monthly_tour_program 
    INNER JOIN person ON person.id=monthly_tour_program.person_id 
    INNER JOIN dealer ON dealer.id=monthly_tour_program.dealer_id 
    INNER JOIN location_view ON location_view.l5_id=monthly_tour_program.locations INNER JOIN _task_of_the_day ON _task_of_the_day.id=monthly_tour_program.task_of_the_day 
    WHERE 
     DATE_FORMAT(`working_date`,'%Y-%m-%d')>='$start' 
    AND DATE_FORMAT(`working_date`,'%Y-%m-%d')<='$end'";
 }
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
          $id=$row['mid'];
          $rs[$id]=$row;
        } 
}
if(empty($rs))
{
?>
<script>
  alert("No Record Found");
//  $.alert({
//     title: 'Alert!',
//     content: 'No Data Found',
// });
            </script>
<?php
}
  ?>
<div class="container-fluid">
<form action="" method="POST">
    
  
    <div class="row" style="position:fixed; width:100%; top:0px;">

<div class="col-xs-6">
Start Date
</div>
<div class="col-xs-6">
End Date
</div>
<div class="col-xs-6">
<input type="date" name="start" class="form-control" value="<?php if(isset($_POST['start'])){ echo $_POST['start']; } ?>">

</div>
<div class="col-xs-6">
<input   type="date" name="end" class="form-control" value="<?php if(isset($_POST['end'])){ echo $_POST['end']; } ?>">

</div>
<div class="col-xs-12">
Users
<select name="user_id" class="form-control" style="height:30px">
<option value="">Please Select</option>
<?php 
recursivejuniors($suser_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
$query = "SELECT person.id as id,concat(first_name,' ',middle_name,' ',last_name) as fullname FROM person WHERE  person.id in (".$juniors.",$suser_id)";
//h1($query);
$res = mysqli_query($dbc, $query);
while($rows = mysqli_fetch_assoc($res)){
?>
<option value="<?=$rows['id']?>" <?php if($_POST['user_id'] == $rows['id']) echo"selected"; ?> ><?=$rows['fullname']?></option>
<?php  
}
?>
</select>
<br>
</div>

<div class="col-xs-12">
  <input type="submit"  value="Filter" name="submit"  id="submitData" onclick="return load()" class="form-control btn btn-primary"  >
</div>

    </div>
       
    <div style="margin-top:52%">
    <!-- <div style="padding-top:10px;"> -->

   <!--  <input type="hidden" name="user_id" value="<?=$userId?>"> -->
           <table  id="example"   style="width:100%">
  <tbody >
  <?php 

  foreach ($rs as $key => $value) {
      ?>
  <tr><td>
  
  <div class="card card-pricing popular shadow  mb-4">
            <span class="h6 w-60 px-4 py-1 rounded-bottom bg-success text-white shadow-sm" style="margin-top: 0px !important"><?=$value['uname']?></span>
          
            <div class="card-body pt-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Date:<?=$value['mdate']?></li>
                    <li class="list-group-item">Task:<?=$value['task']?></li>
                    <li class="list-group-item">Town:<?=$value['town']?></li>
                    <li class="list-group-item">DName:<?=$value['dname']?></li>
                    <li class="list-group-item">Beat:<?=$value['beat']?></li>
                    <li class="list-group-item">PC&nbsp;:<?=$value['pc']?></li>
                    <li class="list-group-item">RV&nbsp;:<?=$value['rd']?></li>
                </ul>
            </div>
        </div>
</td>
  </tr>
<?php } ?>
  </tbody>
<tfoot>
    <tr>
<td>
<hr>

</td>
  </tr>
</tfoot>
  </table>

    </div>  
 </form>   
  </div>
</div>
</body>
</html>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<!-- <script>
function display(str)
{

 $.ajax({url: str, async: false, success: function(result){
 //console.log(result);
            $("body").html(result);
        }});

}
</script> -->
<!-- <script type="text/javascript">
   function takeOption(selected,selectId,table,superId) {
    // $('#city').empty();
     selectId = $('#'+selectId);
               $.ajax({
                    type: "GET",
                    url: "optionData.php",
                    // dataType: 'json',
                    data: {'selectedData':selected,'tableName':table,'superId':superId},
                    success: function (data) {
                      //console.log(data.result);
                       selectId.empty();
                       selectId.html(data);
                      },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }
  </script> -->
