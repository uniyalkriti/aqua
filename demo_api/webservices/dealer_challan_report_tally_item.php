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
$data = array();
$out = array();
 $q = "SELECT `catalog_product`.`id` AS `id`,`itemcode`,`catalog_product`.`name` AS `product_name`,`hsn_code`,`catalog_2`.`name` AS `category`,`gst_percent` AS gst FROM `catalog_product` INNER JOIN `catalog_2` ON `catalog_product`.`catalog_id`=`catalog_2`.`id` GROUP BY `catalog_product`.`id`";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $i=0;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $i;
            $out[$id]['id'] = $row['id']; // storing the item id
            $out[$id]['itemcode'] = $row['itemcode'];
            $out[$id]['product_name'] = preg_replace('/\s+/', ' ', $row['product_name']);
            $out[$id]['hsn_code'] = $row['hsn_code'];
            $out[$id]['category'] = $row['category'];
            $out[$id]['gst'] = $row['gst'];
            $out[$id]['date'] = '01-04-2018';
            $out[$id]['unit'] = 'PCS';
            $data['item'] = $out;
            $i++;
        }
       // print_r($out);

header('Content-Type: application/json');
$data = json_encode($data);
echo $data;
?>
