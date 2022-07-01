<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new mtp();
$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));

$q = "select p.id,pl.circular_id from person p, person_login pl where p.imei_number = '$imei' AND p.id = pl.person_id ";
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];
$c_string = $row['circular_id'];
$result = array();
$final_result = array();
$circular_id = array();
$circular_str = '';

//$q = "SELECT circular_id FROM circular_view WHERE user_id = '$person_id'";
//$r = mysqli_query($dbc,$q);
// if($r && mysqli_num_rows($r) > 0)
// {
//     while($row = mysqli_fetch_assoc($r))
//     {
//        $circular_id[$row['circular_id']] = $row['circular_id'];
//     }
// }
// $filterbucket = '';
//if(!empty($circular_id)) {
//    $circular_str = implode(',' , $circular_id);
//    $filterbucket = " AND id NOT IN ($circular_str)";
//}
if(!$c_string=='')
{   
    $circular_id = explode(',', $c_string);
    foreach ($circular_id as $val) {
        $q = "SELECT id,content,title  FROM circular WHERE id = $val AND circular_type ='notifi'";
        $r = mysqli_query($dbc, $q);
        if ($r && mysqli_num_rows($r) > 0) {
            $row1 = mysqli_fetch_assoc($r);
            $result['id'] = $row1['id'];
            $result['title'] = $row1['title'];
            $result['content'] = $row1['content'];
            $final_result[] = $result;
        } 
//        else {
//            $result['id'] = 0;
//            $result['title'] = 'False';
//            $result['content'] = 'False';
//            $final_result[] = $result;
//        }
    }
}
echo $try = json_encode(array('result' => $final_result));
//    } else {
//        $result['id'] = 0;
//        $result['content'] = 'False';
//        $final_result[] = $result;
//        echo $try = json_encode(array('result' => $final_result));
//    }
?>