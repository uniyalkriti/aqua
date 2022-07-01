<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$isr_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['isr_id'])));
$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));

global $dbc;
$out = array();
$expense = array();
          $q = "SELECT product_id,quantity,catalog_product.name as product_name FROM  `isr_product_details` INNER JOIN isr_total_sale_counter istr ON istr.order_id = isr_product_details.order_id
		  INNER JOIN catalog_product ON catalog_product.id = isr_product_details.product_id
		  WHERE isr_id='$isr_id' AND `Date` = '$date'";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
			// $proid = $row['product_id'];
	    $out['product_name'] = $row['product_name'];
	    $out['quantity'] = $row['quantity'];
	     
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>