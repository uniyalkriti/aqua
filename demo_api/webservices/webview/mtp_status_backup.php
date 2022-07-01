<?php
session_start();
require_once('../../admin/functions/common_function.php');
require_once('../../admin/include/conectdb.php');
require_once('../../admin/include/config.inc.php');
require_once('../../admin/include/my-functions.php');


$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
//$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['month'])));
 



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
    //print_r($rs);
    //$rs = $myobj->get_user_wise_sale_data_c15($user_id,$from_date,$to_date);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Msell Gopal </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
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
    height: 500px;
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
<form name="sub" id="sub" action="mtp_status.php" method="post">
<div  class="container-fluid" >


 <div class="row" style="position:fixed; width:100%; top:5px;">
<div class="col-sm-12" style="background-color:#64a082;">Monthly Tour Plan </div>

 </div>

    <div class="row" style="margin-top:10%" >
            
              
                <br>
                                   
                
            <!-- <div  class="col-sm-12 col-xs-12"  > -->
         
 
            <table  id="example"   style="width:100%">
                <thead class="hidden-xs" >
                 <tr class="search1tr">                  
                    
                        
                        <th > Data </th>
                        
                    </tr> 

                     </thead>

                      <tbody>



<?php
	$in=1;
$r='';
$to_date;
$d1=explode('-',$to_date);

$tdmonth=cal_days_in_month(CAL_GREGORIAN,$d1[1],$d1[0]);

?>
 <?php 
if(!empty($rs)){
//1
    foreach($rs as $key=>$value) {
   //2           //pre($value);

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
 $approved_by=$user_id;
$res = mysqli_query($dbc, $qry);
$mtp_details=array();
while($rows = mysqli_fetch_assoc($res)){
  //3
   //echo $to_date;
    $val=$rows['person_id']; 
    $mtp_details = $myobj->get_monthly_tour_plan($to_date,$val);
//    print_r($mtp_details);
 //echo  $mtp_details['id'];
      //die;
 
?>

 <tr><td  onclick="show_hide_row('<?=$val?>');" >
 <div class="col-md-12 col-sm-12 col-lg-12" style="background-color:#50beed;  border-bottom:1px solid #000;">
<b> S.No :   </b>   <?php echo $in; ?> </br>               
<b> User Name :</b> <?php echo $rows['personname'];  ?></br> 
<b> Approved :</b> <?php echo $rows['approved']; ?></br> 
<b> Not Approved :</b> <?php echo $rows['nonapproved']; ?></b></br> 
 </div>
 </td>
 </tr>
     
<?php  
   foreach($mtp_details as $project){
     //4   
if($project['admin_approved']=='1'){

$dr="Approved";
$st='<p style="color: black; background-color: green; font-size:100%; ">';
}else{
$dr="NOT Approved"; 
 $st='<p style="color: black; background-color: lightgreen; font-size:100%; ">';
}   ?>
  <tr class="<?=$val?>" style='display:none'> 
     <td><div class="col-md-12 col-sm-12 col-lg-12" style="background-color:#dcebf2;  border-bottom:1px solid #000;">
      <b>  Date :</b> <?php echo $project[working_date]; ?></br>
      <b>  Day :</b> <?php echo $project[dayname] ; ?></br>
      <b>  Total Sales:  </b><?php echo $project[total_sales] ;?></br>         
      <b>  Status: </b> <?php echo $dr; ?>  </br>
    </div>
     
     </td></tr>

  <?php         } ?>
    <tr><td><div class="col-md-12 col-sm-12 col-lg-12" style="background-color:#dcebf2;  border-bottom:1px solid #000;" >
     <!-- <b > <a class='cls' href="mtp_approval_status.php?user_id=<?=$val?>&to_date=<?=$to_date?>&status=1&approved_by=<?=$approved_by?>"> Update </a> </b>
   -->
   <span style="float:right">
  <button onclick="send('mtp_approval_status.php?user_id=<?=$val?>&to_date=<?=$to_date?>&status=1&approved_by=<?=$approved_by?>')">Update</button></span>
  
  </div> </td>  </tr>
            
                   <?php 
                  
                
  
                            }  //foreach end here for details data  
 

$in++;   

}

}

                     
				
                  ?>
                        </tbody>
                </table>                
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
<script>
function send(str)
{
//alert(str);
 $.ajax({url: str, async: false, success: function(result){
console.log(result);
            $("body").html(result);
        }});

}
</script>
<script type="text/javascript">
function show_hide_row(row)
{
 $("."+row).toggle();
}
</script>
</head>
