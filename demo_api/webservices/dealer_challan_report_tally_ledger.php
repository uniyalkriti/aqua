<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
ini_set('max_execution_time', -1); 
$postData = file_get_contents('php://input');
$array1 = json_decode($postData,TRUE);
$array=$array1['ENVELOPE'];
global $dbc;
$dealer_code=$array['TALLYREQUEST']['DEALERCODE'];
$qdl="SELECT id FROM dealer WHERE dealer_code='$dealer_code' LIMIT 1";
    $rdl=mysqli_query($dbc,$qdl);
    $rowdl=mysqli_fetch_assoc($rdl);
    $dealer_id=$rowdl['id'];
   // $dealer_id=4;
$data = array();
$i = 0;
 $q = "SELECT `id`, `retailer_code`, `name`, `class`, `dealer_id`,  `address`,`email`, `contact_per_name`, `landline`, `other_numbers`, `tin_no`, `pin_no`,`location_view_tally`.`t6_name` AS state FROM retailer INNER JOIN location_view_tally ON `retailer`.`location_id`=`location_view_tally`.`l5_id` WHERE dealer_id='$dealer_id'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id1 = $row['id'];
            $id = $i;
            $out[$id] = $row; // storing the item id
            $data['ledger'] = $out;
            $i++;
        }

header('Content-Type: application/json');
$data = json_encode($data);
echo $data;
?>
