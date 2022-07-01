<?php

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if (isset($_GET['imei']))
    $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
else
    $imei = 0;
if (isset($_GET['s_date']))
    $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['s_date'])));
else
    $s_date = 0;
//echo "select id,role_id from person where imei_number='".$imei."'";
$q = "select id,role_id from person where imei_number='" . $imei . "'";
$user_qry = mysqli_query($dbc, $q) or die(mysqli_error($dbc, $q));
$user_res = mysqli_fetch_assoc($user_qry);
$role_id = $user_res['role_id'];
$user_id = $user_res['id'];

$myobj = new dealer();

$user_info = array();
$user_final_details = array();
//$rs = array(2,3,8);
$cq = "Select *,dis_code From damage_replace Where DATE_FORMAT(date_time,'%Y-%m-%d')='$s_date' AND user_id='$user_id' GROUP by dis_code";
$cr = mysqli_query($dbc, $cq);
while ($cfetch = mysqli_fetch_array($cr)) {
    $cid[] = $cfetch['dis_code'];
}

foreach ($cid as $k => $v) {
    // h1($v);


    $q = "Select dr.prod_code,cp.`name`,dr.prod_value,dr.mrp,dr.prod_qty,dr.dis_code  From damage_replace dr LEFT JOIN catalog_product cp ON cp.id= dr.prod_code Where DATE_FORMAT(dr.date_time,'%Y-%m-%d')='$s_date' AND dr.user_id='$user_id' AND dr.dis_code='$v' ORDER by cp.`name`";

    $dealer_map = get_my_reference_array('dealer', 'id', 'name');

    // $product_map=  get_my_reference_array('catalog_1','id','name');
    list($opt1, $rs1) = run_query($dbc, $q, 'multi');
    if ($opt1) {
        $user_info['damagereplaceretailer'][] = $dealer = array('name' => $dealer_map[$v], 'id' => $v);
//       
        while ($row = mysqli_fetch_assoc($rs1)) {
//           
//            if($row['task']=='D'){
//           
            $user_info['damageproduct'][] = $product = array('pid' => $row['prod_code'],
                                                            'name' => $row['name'],
                                                            'value' => $row['prod_value'],
                                                            'mrp' => $row['mrp'],
                                                            'quantity' => $row['prod_qty'],
                                                            'dealer_id' => $row['dis_code']);

        }
    }
}



//if($opt1) end here
// }
$user_final_details[] = $user_info;
$user_info = ''; //foreach($rs as $key=>$value) end here
$final_array = array("result" => $user_final_details);
$data = json_encode($final_array);
echo $data;
// if(!empty($rs)){ end here
?>
