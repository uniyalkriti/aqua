<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

//$myobj = new dealer_sale();
$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
//$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
global $dbc;
$out = array();
$expense = array();
          $q = "SELECT * FROM `dealer_available_stock` WHERE dealer_id ='$dealer_id'";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
$i = 0;
         while ($row = mysqli_fetch_assoc($rs)) {
	   $out['product_name'] = $row['product_name'];
	    $rate = $row['rate'];
	    $out['purchase_stock'] = $row['purchase_stock'];
	    $out['sold_qty'] = $row['sold_qty'];
	    $out['balance_stock'] = $row['balance_stock'];
	    $out['salable_stock'] = ($row['balance_stock']+$row['salable_stock']);
         if(!empty($row['non_salable_stock']))
	  {
	    $out['non_salable_stock'] = $row['non_salable_stock'];
	   }
		else
		{
		$out['non_salable_stock'] = "0";
		}   
         $out['value']=(($row['balance_stock']-$row['salable_stock'])* $rate);
         $expense[$i] = $out;  
$i++;
        }

	
        


 //foreach($rs as $key=>$value) end here
$final_array = array("result" => $expense);
$data = json_encode($final_array);
echo $data;



?>
