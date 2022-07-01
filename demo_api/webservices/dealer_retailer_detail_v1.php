<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$dealer_sale = array();
//$date = date("Y-m-d");
$i = 0;

          $q = "SELECT retailer.id as id,retailer.name as name,uso.user_id FROM `retailer` INNER JOIN user_sales_order uso ON uso.retailer_id = retailer.id WHERE uso.dealer_id='$dealer_id' AND uso.date = '$date' AND uso.order_status !=1";
       //h1($q);
        $rs = mysqli_query($dbc,$q);
       
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
             $out['id'] = $row['id'];
            $out['name'] = $row['name'];
	$out['user_id'] = $row['user_id'];
$q1 = mysqli_query($dbc,"SELECT first_name,last_name FROM `person` WHERE `id`= $out[user_id]");
	$row1 = mysqli_fetch_assoc($q1);
         $out['person'] = $row1['first_name']." ".$row1['last_name'];
 	 $retailer[$i] = $out;
$i++;
        }

//$retailer[] = $out;

$final_array = array("result" => $retailer);
$data = json_encode($final_array);
echo $data;



?>
