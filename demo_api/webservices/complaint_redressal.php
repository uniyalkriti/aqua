<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new mtp();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
$final_result = array();
//$q = "SELECT * FROM `user_complaint`  WHERE person_id='$user_id' AND `complaint_status`='1' AND `is_view`='0'";
﻿$q = "SELECT * FROM `user_complaint`  WHERE person_id='$user_id' AND `complaint_status`!='0' AND `is_view`='0'";
//echo $q;die;
$res = mysqli_query($dbc, $q);

//$row = mysqli_fetch_array($res);


if ($res && mysqli_num_rows($res) > 0) {

            while($row1 = mysqli_fetch_assoc($res)){
                if($row1['role_id']=='1'){
                    $rol_qry = "select name from dealer where id = '$row1[dealer_retailer_id]'";
                    $result['role_type']='Dealer';
                }else if($row1['role_id']=='2'){
                    $rol_qry = "select name from retailer where id = '$row1[dealer_retailer_id]'";
                    $result['role_type']='Retailer';
                }
//echo $rol_qry;die;
                $role_res = mysqli_query($dbc, $rol_qry);
                $fetch_role_type_name = mysqli_fetch_object($role_res);
                $role_type_name = isset($fetch_role_type_name->name)?$fetch_role_type_name->name:'';

            $result['role_type_name'] = $role_type_name;
            $result['order_id'] = $row1['order_id'];
            $result['complaint_msg'] = $row1['message'];
            $result['complaint_redressal'] = $row1['complaint_redressal'];
            $final_result[] = $result;
            }
        }else{

           $final_result[] = array('Role_type'=>'','role_type_name'=>'','order_id'=>'','complaint_msg'=>'','complaint_redressal'=>''); 
        }


        echo $try = json_encode(array('result' => $final_result));

?>