<?php
session_start();
require_once('../../admin/functions/common_function.php');
require_once('../../admin/include/conectdb.php');
require_once('../../admin/include/config.inc.php');
require_once('../../admin/include/my-functions.php');


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
//$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
 



global $dbc;

$myobj = new sale();

  $del="DELETE FROM `users_junior_hierarchy` WHERE senior_id=$user_id";
     $rn_del=mysqli_query($dbc,$del);
     if($rn_del){
     $ins="INSERT INTO `users_junior_hierarchy`(`login_user_id`,`user_id`, `full_name`, `role_id`,`senior_id`) 
     SELECT id,id,CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname,role_id,person_id_senior 
     from person where person_id_senior=$user_id";
     $rn_ins=mysqli_query($dbc,$ins);
      //recursivejuniors_new($user_id,$user_id);
     }
     $rs= array();
 
     $query="SELECT * FROM `users_junior_hierarchy` where senior_id='$user_id' ";
$s= mysqli_query($dbc,$query);
$i=0;
//$rs[$i]['user_id']=$user_id;
		while($value1=mysqli_fetch_assoc($s)){
       $i++;
     
        $rs[$i]['user_id']=$value1['user_id']; 
         $rs[$i]['senior_id']=$value1['senior_id'];       
   }
    //print_r($rs);
    //$rs = $myobj->get_user_wise_sale_data_c15($user_id,$from_date,$to_date);

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
</head>
<body>
<form name="sub" id="sub" action="mtp_approval_status.php" method="post">
<div  class="container-fluid" >

<div style="color:#FF9933;">

  <h1> MTP  Details </h1>
   </div>
 <div class="row">
            <div class="col-xs-12">
            </div></div>


    <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                
            <div>
            <table id="dynamic-table" class="table table-striped table-bordered table-hover" style="border: 1px solid black">
                    <thead>
                    <tr class="search1tr">                  
                    
                        <th class="sno">S.No</th>                        
                        <th>User Name</th>
                        <th>Approved</th>
                        <th> Not Approved</th>
                        
                    </tr>

                     </thead>

                      <tbody>



<?php

$r='';
$to_date;
$d1=explode('-',$to_date);

$tdmonth=cal_days_in_month(CAL_GREGORIAN,$d1[1],$d1[0]);
?>Total Days= <?php echo $tdmonth;
if(!empty($rs)){
	$inc=1;
    foreach($rs as $key=>$value) {
              //pre($value);

     $qry="SELECT monthly_tour_program.person_id as person_id , CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname,
(SELECT Count( person_id )  FROM `monthly_tour_program` where admin_approved='1' AND  person_id=$value[user_id]
 AND date_format(working_date,'%Y-%m')='$to_date') as approved,
(SELECT Count(person_id )  FROM `monthly_tour_program` where admin_approved='0' AND  person_id=$value[user_id] AND date_format(working_date,'%Y-%m')='$to_date' ) 
as nonapproved FROM `monthly_tour_program` INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$value[user_id] AND date_format(working_date,'%Y-%m') = '$to_date'
group by monthly_tour_program.person_id ";         
    //          $query = "SELECT monthly_tour_program.*, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
    // //           SELECT Count(DISTINCT person_id ) as approvedmtp  FROM `monthly_tour_program` where admin_approved='0' AND date_format(working_date,'%Y-%m')='$to_date'
	// // 	DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
	// // 	DATE_FORMAT(working_date,'%y%m%d') AS sortodate,
    // // DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program` 
	// // 	INNER JOIN person ON person.id=monthly_tour_program.person_id WHERE person_id=$value[user_id] AND date_format(working_date,'%Y-%m') = '$to_date'  ";
  // h1($qry);
   
 //h1($query);
$res = mysqli_query($dbc, $qry);
$mtp_details=array();
while($rows = mysqli_fetch_assoc($res)){
   //echo $to_date;
    $val=$rows['person_id']; 
    $mtp_details = $myobj->get_monthly_tour_plan($to_date,$val);
//    print_r($mtp_details);
 echo  $mtp_details['id'];
      //die;
 
if($rows['admin_approved']=='1'){
$dr="Approved";
}else{
$dr="NOT Approved";  
}
 
 
  
?>

 <tr>
 
      <td style="color: black; background-color: #FF9933; font-size:100%; "> <?php echo $inc; ?></td>                           
      <td  onclick="show_hide_row('<?=$val?>');" style="color: black; background-color: #FF9933; font-size:100%; "><b><?php echo $rows['personname'];  ?></b></td> 
                   
      <td style="color: black; background-color: #FF9933; font-size:100%; "><?php echo $rows['approved']; ?></b></td>
      <td style="color: black; background-color: #FF9933; font-size:100%; "><?php echo $rows['nonapproved']; ?></b></td>
                        </tr>

    <tr class="<?=$val?>" style='display:none'> 
                   <th > Date </th>
                    <th >  Day </th>
                    <th > TotalSales</th>
                    <th > Remark </th> 
                      </tr>
                   <?php 
                  
                   foreach($mtp_details as $project){
        
                  echo "
                 <tr class='$val' style='display:none'> 
                   <td >$project[working_date] </td>
                    <td >$project[dayname]  </td>
                    <td >$project[total_sales] </td>
                    <td > $project[admin_remark] </td> 
                      </tr> 
                       " ;
  
                            }
                           

          ?> 

                                       
                   




<?php 
  //foreach end here for details data  
 }
$r;
$inc++;   

}

}

                     
				
                  ?>
                        </tbody>
                </table>                
            </div> 
                </div>
        </div>
    
   
 
   </div><!-- /.row -->
</div><!-- /.page-content -->  




</div>

</div>
</form>

</body>
</html>
<html>
<head>
<!-- <link rel="stylesheet" type="text/css" href="table_style.css"> -->
<!-- <script type="text/javascript" src="jquery.js"></script> -->
<script type="text/javascript">
function show_hide_row(row)
{
 $("."+row).toggle();
}
</script>
</head>
