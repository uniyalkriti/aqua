<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
require_once('../client/include/classes/primary_sale.php');

$order_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['order_id'])));
global $dbc;
//$out = array();
//$dealer_sale = array();
//$date = date("Y-m-d");

$data = array();
$q = "SELECT cod.id, cp.name AS cpname, cod.product_rate as rate, cod.qty as qty , cod.free_qty as scheme, cod.product_id FROM challan_order_details cod INNER JOIN catalog_product cp ON cod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id WHERE cod.order_id = '$order_id'";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['product'] = $row['cpname'];
            $out['rate'] = $row['rate'];
            $out['qty'] = $row['qty'];
            $out['scheme'] = $row['scheme'];
            
           
		
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
