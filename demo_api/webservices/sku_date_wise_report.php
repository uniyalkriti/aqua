<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
if(isset($_GET['product_id'])) $product_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['product_id']))); else $product_id = 0;
if(isset($_GET['s_date'])) $e_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['s_date']))); else $e_date = 0;
if(isset($_GET['e_date'])) $s_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['e_date']))); else $s_date = 0;
if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); else $user_id = 0;

$q1 = "SELECT `id` from person where id = '$user_id'";
$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
$q_row = mysqli_fetch_assoc($q_res);

$person_id = $q_row['id'];

$data = array();
 $q = "select SUM(rate * quantity)as sale,order_id from user_sales_order "
        . "INNER JOIN user_sales_order_details USING (order_id) where product_id  = '$product_id' AND date >='$s_date' "
        . "AND date<= '$e_date' AND user_id  = '$user_id' "; 
// h1($q);
$sale_res = mysqli_query($dbc, $q);
$sale_row = mysqli_fetch_array($sale_res);
$order_id=$sale_row['order_id'];
$data["total_sale_value"] = my2digit($sale_row['sale']);
$data["quantity"] =get_squ($order_id,$product_id);
//foreach($data as $k => $v)
//echo $v;

$f = array();
$f[] = $data;

$final_array = array("result"=>$f);	
$data = json_encode($final_array);
echo  $data;



 function get_squ($order_id,$product_id) 
	{ 
		global $dbc;
		$out= array();
                $q = "SELECT SUM(quantity) as squ FROM user_sales_order_details WHERE order_id ='$order_id' AND product_id='$product_id'";
		
                list($opt ,$rs) = run_query($dbc ,$q ,'multi');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs))
                {
                    $out= $row['squ'];
                }
		return $out;
	}
?>          