<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
require_once('../client/include/classes/primary_sale.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
global $dbc;
//$out = array();
//$dealer_sale = array();
//$date = date("Y-m-d");

$data = array();
$q = "SELECT cod.id AS id,uso.date AS date,co.ch_date as ch_date,uso.user_id as user_id, uso.order_id AS orderid, uso.total_sale_value AS salevalue, SUM(cod.taxable_amt) AS tax, co.ch_dealer_id AS did FROM user_sales_order AS uso INNER JOIN challan_order_details AS cod ON uso.order_id=cod.order_id INNER JOIN challan_order AS co ON cod.ch_id=co.id WHERE uso.dealer_id = '$dealer_id' AND ch_date >='$fromdate' AND ch_date <='$todate'  GROUP BY uso.order_id";

$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['orderid'] = $row['orderid'];
            $out['salevalue'] = $row['salevalue'];
            $out['challan_value'] = $row['tax'];
            $out['saledate'] = $row['date'];
            $out['challandate'] = $row['ch_date'];
           // $out['pid']= myrowval('catalog_product', 'name',$row['pid']);
           // $out['did'] = myrowval('dealer', 'name', $row['did']);
            //$out['cid']=  myrowval('complaint','complaint',$row['cid']);
		$user = $row['user_id'];
                    $qn = mysqli_query($dbc,"SELECT CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM
                        person WHERE id=$user");
                    $qf = mysqli_fetch_assoc($qn);
                    $out['user_name'] = $qf['name'];
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
