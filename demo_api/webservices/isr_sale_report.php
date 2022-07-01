<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new mtp();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));


$query_person_login = "SELECT its.*,mobile,DATE_FORMAT(`Date`,'%d-%m-%Y') as s_date,(select statename from state where state.stateid=person.state_id) as statename,(select rolename from _role where _role.role_id=person.role_id) as rolename,(select role_group_id from _role where _role.role_id=person.role_id) as role_group_id,(select name from location_5 where location_5.id=its.BeatId) as beat,(select DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') from user_daily_attendance where user_daily_attendance .user_id=its.isr_id AND DATE_FORMAT(user_daily_attendance .work_date,'%Y-%m-%d')=its.Date) as attn_time,(select DATE_FORMAT(check_out.work_date,'%H:%i:%s') from check_out where check_out.user_id=its.isr_id AND DATE_FORMAT(check_out.work_date,'%Y-%m-%d')=its.Date) as checkout_time,(select dealer.name from dealer where dealer.id=its.`DistributorId`) as dealer_name,CONCAT_WS(' ',first_name, middle_name, last_name) AS isr_name FROM `isr_total_sale_counter` its INNER JOIN person ON its.isr_id=person.id where person.`person_id_senior`='$user_id' AND its.`Date`>='$from_date' AND its.`Date`<='$to_date' group by  s_date, its.isr_id,its.DistributorId,its.BeatId order by `Date` DESC";
//h1($query_person_login);die;
$user_qry = mysqli_query($dbc, $query_person_login);
$final_dealer_details = array();
if(mysqli_num_rows($user_qry)>0){
//print_r($user_qry);die;
	while ($isr_fetch = mysqli_fetch_assoc($user_qry)) {
                $isr_info['isr_id'] = $isr_fetch['isr_id'];
                $isr_info['isr_name'] = $isr_fetch['isr_name'];
              //  $isr_info['attn_time'] = $isr_fetch['attn_time'];
      $isr_info['date'] = $isr_fetch['s_date'];
                $isr_info['rolename'] = $isr_fetch['rolename'];
                $isr_info['Totalcall'] = $isr_fetch['Totalcall'];
                $isr_info['Productivecall'] = $isr_fetch['Productivecall'];
              //  $isr_info['newoutlet'] = $isr_fetch['newoutlet'];
              //  $isr_info['beat'] = $isr_fetch['beat'];
                $isr_info['TotalSale'] = $isr_fetch['TotalSale'];
                
                $final_dealer_details[] = $isr_info;
            }
            //print_r($final_dealer_details);die;

}else{

				$isr_info['isr_name'] = null;
			//	$isr_info['attn_time'] = null;
			//	$isr_info['checkout_time'] = null;
				$isr_info['rolename'] = null;
				$isr_info['Totalcall'] = null;
				$isr_info['Productivecall'] = null;
			//	$isr_info['newoutlet'] = null;
			//	$isr_info['beat'] = null;
				$isr_info['TotalSale'] = null;

				$final_dealer_details[] = $isr_info;


}
$final_array = array("result"=>$final_dealer_details);	

echo json_encode($final_array);
?>


