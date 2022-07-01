<?php
require_once('../admin/include/conectdb.php');
require_once('../admin/functions/common_function.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

 global $dbc;
//$myobj = new mtp();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['userid'])));
$from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['current_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
recursivejuniors($user_id);
$juniors = join(',',$_SESSION['resursivedata']);
if($juniors==''){ $juniors=0; }
$_SESSION['resursivedata']='';
$query_focus_target = "SELECT person.id AS person_id,CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name)as user_name,_role.rolename, "
        . "focus_product_users_target.product_id,catalog_product.name AS product_name,`focus_product_users_target`.`target_value`,"
        . " (SELECT sum(`rate`*`quantity`) AS total_sale FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) WHERE DATE_FORMAT(`date`,'%Y-%m-%d')='$from_date' AND user_id IN (".$juniors.",$user_id) AND user_sales_order_details.product_id=focus_product_users_target.product_id) AS achieved "
        . "FROM focus_product_users_target INNER JOIN person ON person.id=focus_product_users_target.user_id INNER JOIN  _role ON person.role_id=_role.role_id INNER JOIN catalog_product ON focus_product_users_target.product_id=catalog_product.id WHERE user_id IN (".$juniors.",$user_id) AND '$from_date' BETWEEN start_date AND end_date";

 //h1($query_focus_target);die;
$user_qry = mysqli_query($dbc, $query_focus_target);
$a=mysqli_num_rows($user_qry);
//echo $a."hello";exit;
$final_focus_details = array();
if(mysqli_num_rows($user_qry)>0){
//print_r($user_qry);die;
	while ($focus_fetch = mysqli_fetch_assoc($user_qry)) {
                $focus_info['person_id'] = $focus_fetch['product_id'];
                $focus_info['username'] = $focus_fetch['user_name'];
                $focus_info['rolename'] = $focus_fetch['rolename'];
                $focus_info['product_id'] = $focus_fetch['product_id'];
                $focus_info['product_name'] = $focus_fetch['product_name'];
                $focus_info['target_value'] = $focus_fetch['target_value'];
                $focus_info['achieved'] = $focus_fetch['achieved'];
                $final_focus_details[] = $focus_info;
            }
            //print_r($final_focus_details);die;
}else{
                                $focus_info['person_id'] = null;
                                $focus_info['username'] = null;
                                $focus_info['rolename'] = null;
				$focus_info['product_id'] = null;
				$focus_info['product_name'] = null;
				$focus_info['target_value'] = null;
				$focus_info['achieved'] = null;
				$final_focus_details[] = $focus_info;
}
$final_array = array("result"=>$final_focus_details);	

echo json_encode($final_array);
?>


