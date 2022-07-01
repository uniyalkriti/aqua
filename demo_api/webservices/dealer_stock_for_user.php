<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

  //$myobj = new dealer_sale();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
  $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
  $from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
  //$date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['date'])));
  global $dbc;
  $out = array();
  $expense = array();
  /*$q = "SELECT * FROM `dealer_available_stock` WHERE dealer_id ='$dealer_id'";*/
    $q = "SELECT DATE_FORMAT(`submit_date_time`,'%d-%m-%Y')AS sdate,p.product_name, s.stock_qty AS qty,s.cases,s.mrp,s.pcs_mrp FROM `dealer_balance_stock` s JOIN `catalog_view` p ON s.product_id=p.product_id WHERE s.dealer_id ='$dealer_id' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(`submit_date_time`,'%Y-%m-%d')<='$to_date' AND s.user_id='$user_id' GROUP BY s.user_id,sdate,s.dealer_id,s.product_id";
 $rs=mysqli_query($dbc,$q);
  $i = 0;
$num=mysqli_num_rows($rs);
if($num<=0){
$final_array = array("response" => false);
}else{
  while ($row = mysqli_fetch_assoc($rs))
  {
    $out['date'] = $row['sdate'];
    $out['product_name'] = $row['product_name'];
    $out['stock'] = $row['qty'];    
    $out['cases'] = $row['cases'];
    $out['mrp'] = $row['mrp'];
    $out['cases'] = $row['cases'];
    $out['mrp'] = $row['mrp'];
    $out['pcs_mrp'] = $row['pcs_mrp'];
    $out['total'] = ROUND(($row['cases']*$row['mrp'])+($row['qty']*$row['pcs_mrp']),2);
    $expense[$i] = $out;
    $i++;
  }       

$final_array = array("response" => true,"result" => $expense);
}
$data = json_encode($final_array);
echo $data;



?>
