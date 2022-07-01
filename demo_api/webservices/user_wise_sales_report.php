<?php

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
if(isset($_GET['userid'])) $userid = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid']))); else $userid = 0;
if(isset($_GET['from_date'])) $from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date']))); else $from_date = 0;
if(isset($_GET['to_date'])) $to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date']))); else $to_date = 0;

//echo "select id,role_id from person where imei_number='".$imei."'";
$q = "select id,role_id,company_id from person where id='".$userid."'";
//echo $q;
$user_qry=mysqli_query($dbc,$q) or die(mysqli_error($dbc, $q));
$user_res=  mysqli_fetch_assoc($user_qry);
$role_id = $user_res['role_id'];
$user_id = $user_res['id'];
$company_id = $user_res['company_id'];
// echo $company_id;
$myobj = new sale();
$rs = $myobj->get_user_wise_sale_data($user_id, $role_id);
//print_r($rs);
$user_info = array();
$user_final_details = array();
//$rs = array(2,3,8);
//pre($rs);
if(!empty($rs)){
    foreach($rs as $key=>$value) {

        if($company_id == '50'){

             $q = "SELECT user_sales_order.date as sale_date,user_sales_order.user_id,(SELECT COUNT(retailer_id) FROM user_sales_order  WHERE user_id = '$value' AND date =sale_date) AS tot_calls, (SELECT COUNT(retailer_id) FROM user_sales_order  WHERE user_id  = '$value' AND call_status = '1' AND date =sale_date) AS productive, (SELECT CONCAT_WS(' ', first_name, middle_name, last_name) FROM person WHERE person.id = '$value') AS name, (SELECT ROUND(SUM(final_secondary_rate*final_secondary_qty),2) AS total_sale_value FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) WHERE user_id = '$value' AND date =sale_date) AS total_sale_value FROM user_sales_order WHERE user_id = '$value' AND date >= '$from_date' AND date <= '$to_date' group by user_id,date ORDER BY date ASC";

        }else{

  $q = "SELECT user_sales_order.date as sale_date,user_sales_order.user_id,(SELECT COUNT(retailer_id) FROM user_sales_order  WHERE user_id = '$value' AND date =sale_date) AS tot_calls, (SELECT COUNT(retailer_id) FROM user_sales_order  WHERE user_id  = '$value' AND call_status = '1' AND date =sale_date) AS productive, (SELECT CONCAT_WS(' ', first_name, middle_name, last_name) FROM person WHERE person.id = '$value') AS name, (SELECT SUM(rate*quantity) AS total_sale_value FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) WHERE user_id = '$value' AND date =sale_date) AS total_sale_value FROM user_sales_order WHERE user_id = '$value' AND date >= '$from_date' AND date <= '$to_date' group by user_id,date ORDER BY date ASC";
        }


  // $q = "SELECT (SELECT COUNT(DISTINCT retailer_id) FROM user_sales_order  WHERE user_id = '$value' AND date >= '$from_date' AND date <= '$to_date') AS tot_calls, (SELECT COUNT(DISTINCT retailer_id) FROM user_sales_order  WHERE user_id  = '$value' AND call_status = '1' AND date >= '$from_date'  AND date <= '$to_date') AS productive, (SELECT count(DISTINCT order_id) FROM user_sales_order WHERE user_id = '$value' AND call_status = '0' AND date >= '$from_date'  AND date <= '$to_date') AS non_productive,(SELECT CONCAT_WS(' ', first_name, middle_name, last_name) FROM person WHERE person.id = '$value') AS name, (SELECT SUM(rate*quantity) AS total_sale_value FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) WHERE user_id = '$value' AND date >= '$from_date' AND date <= '$to_date') AS total_sale_value,date FROM user_sales_order WHERE user_id = '$value' AND date >= '$from_date' AND date <= '$to_date' group by date";
 //h1($q);
   
        
    list($opt1, $rs1) = run_query($dbc, $q , 'multi');
    if($opt1) 
      {
        while($row = mysqli_fetch_assoc($rs1))
        {  
            if(!empty($row['total_sale_value']))
           {
           $total_sale=$row['total_sale_value'];
           }else{
               $total_sale=0;
           }
            $user_info['name'] = $row['name'];
            $user_info['tot_calls'] = $row['tot_calls'];
            $user_info['productive'] = $row['productive'];
	          $user_info['date'] = $row['sale_date'];
            $user_info['non_productive'] = $row['tot_calls']-$row['productive'];
            $user_info['total_sale_value'] = round($total_sale,2);
            $user_info['user_id'] = $value;
            //$user_info['user_location'] = $myobj->get_user_location($value,$from_date);
            $user_final_details[] = $user_info;
         
        }
      } 
//if($opt1) end here
   
  } //foreach($rs as $key=>$value) end here
     $final_array = array("result"=>$user_final_details);	
     $data = json_encode($final_array);
     echo $data;
} // if(!empty($rs)){ end here

?>
