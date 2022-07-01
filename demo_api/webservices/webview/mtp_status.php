<?php
session_start();
require_once('../../admin/functions/common_function.php');
require_once('../../admin/include/conectdb.php');
require_once('../../admin/include/config.inc.php');
require_once('../../admin/include/my-functions.php');
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['month'])));
global $dbc;

$myobj = new sale();

 $company_id_query = "SELECT company_id from person where person.id = $user_id";
  $company_id_run=mysqli_query($dbc,$company_id_query);

$company_id_fetch=mysqli_fetch_object($company_id_run);
$company_id = $company_id_fetch->company_id;
// print_r($company_id_fetch->company_id); die;

// comment this for undo

recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }


$explode_users = explode(',', $juniors);

$latest_rs = array();

foreach ($explode_users as $key => $value) {
  $latest_rs[]['user_id'] = $value;
}

$rs = array_map("unserialize", array_unique(array_map("serialize", $latest_rs)));


// pre($rs);die;

// comment this for undo


 // un comment this for previous changes

//   $del="DELETE FROM `users_junior_hierarchy` WHERE senior_id=$user_id";
//      $rn_del=mysqli_query($dbc,$del);
//      if($rn_del){
//      $ins="INSERT INTO `users_junior_hierarchy`(`login_user_id`,`user_id`, `full_name`, `role_id`,`senior_id`) 
//      SELECT id,id,CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname,role_id,person_id_senior 
//      from person where person_id_senior=$user_id";
//      $rn_ins=mysqli_query($dbc,$ins);
//      }
//      $rs= array();
 
//      $query="SELECT * FROM `users_junior_hierarchy` where senior_id='$user_id' ";
// $s= mysqli_query($dbc,$query);
// $i=0;
// 		while($value1=mysqli_fetch_assoc($s)){
//        $i++;
     
//         $rs[$i]['user_id']=$value1['user_id']; 
//          $rs[$i]['senior_id']=$value1['senior_id'];       
//    }

 // un comment this for previous changes


   // pre($rs);die;

?>


<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<?php 
include('script.php');
?>
<!-- LOADER -->
<style>
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
<script type="text/javascript">

function checkAll(checkIds){
  var checkId = checkIds+"n";
  console.log(checkId);

    var inputs = document.getElementsByTagName("input");
    for (var i = 0; i < inputs.length; i++) { 
        if (inputs[i].type == "checkbox" && inputs[i].id == checkId) { 
            if(inputs[i].checked == true) {
                inputs[i].checked = false ;
            } else if (inputs[i].checked == false ) {
                inputs[i].checked = true ;
            }
        }  
    }  
//       alert(i);
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
  .stuck {
    position: fixed;
    top: 10px;
    bottom: 10px;
    overflow-y: scroll;
}
</style>
<style>
     body{
     overflow:hidden;
     } 
      tbody {
      position: fixed;
    height: 400px;
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
<div  class="container-fluid" >
<form name="sub" id="sub" action="" method="post">

<!-- MY CODE -->

    <div class="row" style="position:fixed; width:100%; top:0px;">
 
  <input type="submit"  value="Submit" name="submit" style="width:100%; background-color:#0e597c;color:#fff;" class=" form-control" >
  
    </div>

      <div style="margin-top:15%">
            <table  id="example"   style="width:100%">
      <thead class="hidden-xs">
      <tr>
      <th>
      Data
        </th></tr>
  </thead>
  
  <tbody>
    <?php
    $i = 0;
    if(!empty($rs)){
//1
      foreach($rs as $key=>$value) {
   //2
  $qry="SELECT monthly_tour_program.person_id as person_id , CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname,
        (SELECT Count( person_id )  FROM `monthly_tour_program` where admin_approved IN ('1','2') AND  person_id=$value[user_id]
         AND date_format(working_date,'%Y-%m')='$to_date') as approved,
        (SELECT Count(person_id )  FROM `monthly_tour_program` where admin_approved='0' AND  person_id=$value[user_id] AND date_format(working_date,'%Y-%m')='$to_date' ) 
        as nonapproved FROM `monthly_tour_program` INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$value[user_id] AND date_format(working_date,'%Y-%m') = '$to_date' AND monthly_tour_program.company_id = $company_id AND person.company_id = $company_id
        group by monthly_tour_program.person_id ";

$approved_by=$user_id;
$res = mysqli_query($dbc, $qry);
$mtp_details=array();
while($rows = mysqli_fetch_assoc($res)){
    $val=$rows['person_id']; 
    $mtp_details = $myobj->get_monthly_tour_plan($to_date,$val);
// FOR DEALER DROPDOWN //
    $dealer_id_only = "SELECT dealer_id,dealer.name as dealer_name FROM `dealer_location_rate_list` INNER JOIN dealer ON dealer.id = dealer_id WHERE `user_id` = '$val' AND dealer_location_rate_list.company_id = $company_id GROUP by dealer_id";
    $dealer_id_only_run = mysqli_query($dbc,$dealer_id_only);
    $dealer_id_only_fetch = mysqli_fetch_assoc($dealer_id_only_run);

   $dealerq = "SELECT dealer_id,dealer.name as dealer_name FROM `dealer_location_rate_list` INNER JOIN dealer ON dealer.id = dealer_id WHERE `user_id` = '$val' AND dealer_location_rate_list.company_id = $company_id GROUP by dealer_id";
    $runDealer = mysqli_query($dbc,$dealerq);
    // $rowD = array();
    // print_r($rowDealer_id); die;
    while($rowDealer = mysqli_fetch_assoc($runDealer))
    {
      $rowD[] = $rowDealer;
    }
    // print_r($rowD);die;
    // END OF DEALER DROPDOWN //

// FOR BEAT DROPDOWN //
 $beatq = "SELECT location_id,location_7.name as location_name FROM `dealer_location_rate_list` INNER JOIN location_7 ON location_7.id = location_id WHERE `user_id` = '$val' AND dealer_id='$dealer_id_only_fetch[dealer_id]' AND dealer_location_rate_list.company_id = $company_id GROUP by location_id";
$runBeat = mysqli_query($dbc,$beatq);
$rowB = array();
while($rowBeat = mysqli_fetch_assoc($runBeat))
{
  $rowB[] = $rowBeat;
}
// END OF BEAT DROPDOWN //
// START TASK OF THE DAY //
$taskq = "SELECT * FROM `_task_of_the_day` WHERE status = 1 AND company_id = $company_id ";
$taskR = mysqli_query($dbc,$taskq);
$rowT = array();
while($rowTask = mysqli_fetch_assoc($taskR))
{
  $rowT[] = $rowTask;
}

// END TASK OF THE DAY //
    // print_r($rowB);
      ?>
 
  
  <tr><td>
  
    <div  class="row" data-toggle="collapse" 
    style="background-color:#e0e0e8; border-top:1px solid #000; border-bottom:1px solid #000;" 
     data-target="#demo<?php echo $i;?>">
    <div class="col-sm-4">User Name: <?= $rows['personname']?></div>
    <div class="col-sm-4">Approved: <?=$rows['approved']?></div>
    <div class="col-sm-4">Pending: <?=$rows['nonapproved']?></div>
  </div>
  <div class="row" style="background-color:#e0e0e8;  border-bottom:1px solid #000;" >

  <div class="col-md-12 col-sm-12 col-lg-12">
   
   <input type="checkbox" id="option<?=$val?>1"  name="check_box[<?=$val?>]"
    value="1"  onclick="checkAll('option<?=$val?>1');"/> Approve  &nbsp;
    
</div>
</div>

    <div class="collapse" id="demo<?php echo $i;?>">
    <div class="row" style="background-color:#dcebf2;  border-bottom:1px solid #000;" >

  <div class="col-md-12 col-sm-12 col-lg-12">
  <?php  
   foreach($mtp_details as $project){
     //4   
    //  print_r($project); 
if($project['admin_approved']=='1' || $project['admin_approved']=='2'){

$dr="Approved";
$st='<p style="color: black; background-color: green; font-size:100%; ">';
}else{
$dr="NOT Approved"; 
 $st='<p style="color: black; background-color: lightgreen; font-size:100%; ">';
}   ?>
     
         
   <input type="checkbox" id="option<?=$val?>1n"  name="check[<?=$project['id']?>]" value="1" <?php 
   if($project['admin_approved']=='1' || $project['admin_approved']=='2'){
     echo "checked  ";
   }
   
   ?> /> Approve 
      <br>
   <span style="float:left"> <b>  Date :</b> <?php echo $project['working_date']; ?></span>
   <span style="float:right">  <b>  Day :</b> <?php echo $project['dayname'] ; ?></span>
   <br>
   <b>Dealer </b>
   

<select class="form-control" name="dealer[<?=$project['id']?>]" onchange="myFunction(this.value,<?=$project['id']?>)"  id="mySelect_<?=$project['id']?>">
     <option value="0">Please Select</option>
       <?php
       foreach($rowD as $keyD=>$valueD)
       {
        // print_r($valueD); die;
        // echo $valueD['dealer_id']; die;
        echo'<option value="'.$valueD['dealer_id'].'"';
        if($project['dealer_id'] == $valueD['dealer_id'])
        {
          echo "selected";
        }
        echo'>'.$valueD['dealer_name'].'</option>';   
       }
      
      ?>           
     </select>
    
    
     <b>Beat </b>
     <select class="form-control" name="beat[<?=$project['id']?>]" id="beat_<?=$project['id']?>" >
     <option value="0">Please Select</option>
       <?php
      
       foreach($rowB as $keyB=>$valueB)
       {
        echo'<option value="'.$valueB['location_id'].'"';
        if($project['locations'] == $valueB['location_id'])
        {
          echo "selected";
        }
        echo'>'.$valueB['location_name'].'</option>';   
       }
      
      ?>           
     </select>
     
     <b>Task For The Day </b>
     <select class="form-control" name="task[<?=$project['id']?>]">
     <option value="0">Please Select</option>
       <?php
      
       foreach($rowT as $keyT=>$valueT)
       {
        echo'<option value="'.$valueT['id'].'"';
        if($project['working_status_id'] == $valueT['id'])
        {
          echo "selected";
        }
        echo'>'.$valueT['task'].'</option>';   
       }
      
      ?>           
     </select>
     <b>Productive Call</b><input type="text" name="pc[<?=$project['id']?>]" class="form-control" value="<?=$project['pc']?>">
     <b>RD(RV)</b><input type="text" name="rd[<?=$project['id']?>]" class="form-control"  value="<?=$project['rd']?>">
     <b>Collection(RV)</b><input type="text" name="collection[<?=$project['id']?>]" class="form-control"  value="<?=$project['collection']?>">
     <b>Primary Order(RV)</b><input type="text" name="primary_ord[<?=$project['id']?>]" class="form-control"  value="<?=$project['primary_ord']?>">
     <b>New Outlet Opening</b><input type="text" name="new_outlet[<?=$project['id']?>]" class="form-control"  value="<?=$project['new_outlet']?>">
     <b>Any Other Task</b><input type="text" name="any_other_task[<?=$project['id']?>]" class="form-control"  value="<?=$project['any_other_task']?>">
     <b>  Status: </b> <?php echo $dr; ?>  <br>
   <input type="hidden" name="person_id[<?=$project['id']?>]" class="form-control"  value="<?=$user_id?>">
 <hr>
<?php }
//4
?>
</div>
</div>

  </div>
</td>
  </tr>
    <?php 
  $i++;
  }
}
    }
  ?>

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
  </div>
<!-- END OF MY CODE -->

  <!-- /.row -->
</div><!-- /.page-content -->  
</div>

</div>
</form>

</body>
</html>
<html>


<!-- <script>
function myFunction(key) {
  //
  
  

 // document.getElementById("demo").innerHTML = "You selected: " + x;
}
</script> -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
function myFunction(val,id) {
  //alert("The input value has changed. The new value is: " + val);


   $.ajax({
                         type: 'POST',
                         url: 'dealer_onchange_beat_formtp.php',
                        data: {'id': val},
                         success: function(data){
                          $('#beat_'+id).html(data);

                         }
                      });


}
</script>


<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
<script type="text/javascript">
  $("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
</script>

<?php
 if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
      {
     // echo "<pre>";
   //  print_r($_POST); exit;
      $check = $_POST['check'];
      $userid = $_POST['user_id'];
      $approved_by = $_POST['user_id'];
      $curdate = date('Y-m-d H:i:s');
      foreach($check as $keycheck=> $valuecheck)
      {
       $checkbox = $valuecheck;
       $pc = $_POST['pc'][$keycheck];
       $rd = $_POST['rd'][$keycheck];
       $collection = $_POST['collection'][$keycheck];
       $primary_ord = $_POST['primary_ord'][$keycheck];
       $new_outlet = $_POST['new_outlet'][$keycheck];
       $any_other_task = $_POST['any_other_task'][$keycheck];
       $dealer = $_POST['dealer'][$keycheck];
       $beat = $_POST['beat'][$keycheck];       
       $task = $_POST['task'][$keycheck];
       $remark = $_POST['remark'][$keycheck];
       $qch="SELECT dealer_id,locations,admin_approved FROM monthly_tour_program WHERE id='$keycheck' AND monthly_tour_program.company_id = $company_id  LIMIT 1";
       $rch=mysqli_query($dbc,$qch);
       $rowch=mysqli_fetch_assoc($rch);
       $dealerch=$rowch['dealer_id'];
       $beatch=$rowch['locations'];
       $admin_approved_ch=$rowch['admin_approved'];
       if($admin_approved_ch==0 || $admin_approved_ch==1){
       	$admin_approved=1;
       }else{
        $admin_approved=2;
       }
       if($dealerch!=$dealer){
       	$admin_approved=2;
       	$qlog1="INSERT INTO monthly_tour_program_log (`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on`) 
       	    SELECT   `person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on` FROM monthly_tour_program WHERE id='$keycheck'";
       	    $rlog1=mysqli_query($dbc,$qlog1);
       }elseif($beatch!=$beat){
       	$admin_approved=2;
       	$qlog2="INSERT INTO monthly_tour_program_log (`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on`) 
       	    SELECT   `person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on` FROM monthly_tour_program WHERE id='$keycheck'"; 
       	    $rlog2=mysqli_query($dbc,$qlog2);
       }
        $uq = "UPDATE `monthly_tour_program` SET `dealer_id`='$dealer',`locations`='$beat',`working_status_id`='$task',`admin_approved`='$admin_approved',`admin_remark`='$remark',`pc`='$pc',`rd`='$rd',`collection`='$collection',`primary_ord`='$primary_ord',`new_outlet`='$new_outlet',`any_other_task`='$any_other_task',`approved_by`=$user_id,`approved_on`=NOW() WHERE id='$keycheck'";  
        $ruq =   mysqli_query($dbc,$uq);
       if($ruq)
       {
     ?> <script>
     // alert("DONE");
                    setTimeout("window.parent.location = ''", 100);
                     </script>



                <?php 
  }
  
      }
    
      }
?>
