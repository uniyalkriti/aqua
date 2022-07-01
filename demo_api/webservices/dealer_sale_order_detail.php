<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new dealer_sale();
//$retailer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['retailer_id'])));
$order_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['order_id'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$dealer_sale = array();
//$sale_array = array();
//$sale_order = array();
   
 $q = "SELECT * FROM user_sales_order_details WHERE order_id = '$order_id' AND status !=2";
       // h1($q);exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	$out['product_id'] = $row['product_id'];
	$q1 = mysqli_query($dbc,"SELECT name FROM `catalog_product` WHERE `id`= $out[product_id]");
	$row1 = mysqli_fetch_assoc($q1);
	    $out['product_name'] = $row1['name'];
	    $out['order_id'] = $row['order_id'];
	    $out['qty'] = $row['quantity']; 
	    $out['rate'] = $row['rate'];
	    $out['scheme_qty'] = $row['scheme_qty'];
		//$qqqq = "SELECT tax FROM catalog_product_rate_list WHERE catalog_product_id = '$out[product_id]' AND stateId='$state' ";
              //  $rrrr = mysqli_query($dbc, $qqqq);
		//$tax_r = mysqli_fetch_assoc($rrrr);
	      // $out['vat'] = $tax_r['tax'];
         
         $dealer_sale[$i] = $out;  
$i++;
        }

 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $dealer_sale);
$data = json_encode($final_array);
echo $data;



?>
