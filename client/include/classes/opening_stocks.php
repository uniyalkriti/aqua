<?php

class opening_stocks extends myfilter {

public $id = NULL;
public $id_detail = NULL;

public function __construct($id = NULL) {
parent::__construct();
$this->id = $id;
}

######################################## Invoice Starts here ####################################################

public function get_opening_stocks_se_data() {
$d1 = $_POST;
//$d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
$d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
$d1['myreason'] = 'Please fill all the required information';
$d1['what'] = 'Opening Stock'; //whether to do history log or not
return array(true, $d1);
}

public function opening_stocks_save() {
global $dbc;

$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pid = $_SESSION[SESS . 'data']['id'];

//$_SESSION[SESS . 'data']['csa_id'] = $data['csa_id'];
$out = array('status' => 'false', 'myreason' => '');
list($status, $d1) = $this->get_opening_stocks_se_data();
//pre($d1); exit;
if (!$status)
return array('staus' => false, 'myreason' => $d1['myreason']);
$mfg = !empty($d1['mfg']) ? get_mysql_date($d1['mfg']) : '';
$id = $_SESSION[SESS . 'data']['dealer_id'] . date('Ymdhis');
//Start the transaction
mysqli_query($dbc, "START TRANSACTION");
if(!empty($d1[product_id])){
    foreach ($d1[product_id] as $k => $value) {
        $product_id=$value;
        $mrp=$d1[base_price][$k];
        $rate=$d1[dealer_rate][$k];
        $batch_no=$d1[batch_no][$k];
        $mfg = !empty($d1['mfg'][$k]) ? get_mysql_date($d1['mfg'][$k]) : '';
        $qty=$d1[sqty][$k];
        $nqty=$d1[nsqty][$k];
  $qpos = "INSERT INTO `opening_stocks`(`product_id`, `batch_no`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`, `sync_status`, `update_date_time`) "
        . " VALUES ('$product_id','$batch_no','$rate','$rate','$mrp','0','0','$dealer_id','$qty','0','$nqty','$qty','$mfg','$mfg',NOW(),'0','1','1','1',NOW())";
$rpos = mysqli_query($dbc, $qpos);
if (!$rpos) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => 'Opening Stock table error');
}
else{
    $qchk="SELECT qty FROM stock WHERE product_id='$product_id' AND dealer_id='$dealer_id' AND batch_no='$batch_no' AND mfg='$mfg' ";
    $rchk=mysqli_query($dbc, $qchk);
    $num_row= mysqli_num_rows($rchk);
    if($num_row>0){
        $qps="UPDATE `stock` SET `product_id`='$product_id',`batch_no`='$batch_no',`dealer_rate`='$rate',`mrp`='$mrp',`qty`='$qty',`nonsalable_damage`='$nqty',`mfg`='$mfg',`expire`='$mfg',`date`=NOW(),`update_date_time`=NOW() WHERE dealer_id='$dealer_id' AND product_id='$product_id' AND batch_no='$batch_no' AND mfg='$mfg'";
    }
    else{ 
        $qps = "INSERT INTO `stock`(`product_id`, `batch_no`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`, `sync_status`, `update_date_time`) "
    . " VALUES ('$product_id','$batch_no','$rate','$rate','$mrp','0','0','$dealer_id','$qty','0','$nqty','$qty','$mfg','$mfg',NOW(),'0','1','1','1',NOW())";
    
    }
    //h1($qps);exit;
$rps = mysqli_query($dbc, $qps);
if (!$rps) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => 'Stock table error');
}
}
        
    }
}
$rId = $id;
mysqli_commit($dbc);
//Final success 
return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
}

public function opening_stocks_extra($actiontype, $rId, $productId, $batch_no, $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,$company_id,$cases,$landing_price) {
	/*pre($base_price); exit();*/
	global $dbc;
	$uncode = '';
	$str = $str_cat = array();
	if ($actiontype == 'Update')
	mysqli_query($dbc, "DELETE FROM opening_stocks_details WHERE order_id = $rId");

	foreach ($productId as $key => $value) {
		$mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
		$expdate = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
		if ($actiontype == 'Update')
		$uncode = date('ymdhis') + $key + 1;
		else
		$uncode = date('ymdhis') + $key + 1;
		// print_r($cases[$key]);
		if($quantity[$key]!=0 || !empty($quantity[$key])){
		//$s = "select piece,free_qty from `cases` where product_id = $value";
		//$rsss = mysqli_query($dbc, $s);
	//	while($row = mysqli_fetch_assoc($rsss))
		//{
		//$p = $row['piece']; 
		//$free = $row['free_qty']; 
		//}
		/*pre($landing_price);exit();*/
		if($cases[$key]==1)
		{

		$cases[$key] = floor($quantity[$key]/$p); 
		$str[] = "('$rId', '$value', '{$base_price[$key]}','{$landing_price[$key]}', '{$quantity[$key]}', '$mfdate', '$expdate', '{$purchase_inv[$key]}','{$sale_price[$key]}', '{$cases[$key]}')";

		}
		else
		{
		//$quant[$key] = $quantity[$key]*$p;
		$quant[$key] = $quantity[$key];
		//$schem[$key] = $free*$quantity[$key];
		$schem[$key] = 0;
		$str[] = "('$rId', '$value', '{$base_price[$key]}', '{$landing_price[$key]}', '{$quant[$key]}', '$mfdate', '$expdate', '{$purchase_inv[$key]}','{$sale_price[$key]}', '{$quantity[$key]}')";

		}

		}
	}
	$str = implode(', ', $str);
	$str_cat = implode(', ', $str_cat);
	$q = "INSERT INTO `opening_stocks_details` (`order_id`, `product_id`, `mrp`,`rate`, `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`) VALUES $str";
	$r = mysqli_query($dbc, $q);
	/*h1($q);exit();*/
	// if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved some error occurred.') ;
	// $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES $str_cat";
	// 
	// $r = mysqli_query($dbc, $q);
	if (!$r)
	return array('status' => false, 'myreason' => ' Purchase Stock details could not be saved some error occurred.');
	if ($company_id == 1){
	write_query($q);
	}
	return array('status' => true, 'myreason' => '');
}



public function opening_stocks_extra_edit($actiontype, $rId, $productId, $batch_no, 
	$base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,
	$cases,$landing_price) {
	/*pre($base_price);
	pre($landing_price); exit();*/
	global $dbc;
	$uncode = '';
	$str = $str_cat = array();
	if ($actiontype == 'Update')
	mysqli_query($dbc, "DELETE FROM opening_stocks_details WHERE order_id = $rId");

	foreach ($productId as $key => $value) {
		$mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
		$expdate = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
		if ($actiontype == 'Update')
		$uncode = date('ymdhis') + $key + 1;
		else
		$uncode = date('ymdhis') + $key + 1;
		// print_r($cases[$key]);
		if($quantity[$key]!=0 || !empty($quantity[$key])){
		$s = "select piece,free_qty from `cases` where product_id = $value";
		$rsss = mysqli_query($dbc, $s);
		while($row = mysqli_fetch_assoc($rsss))
		{
		$p = $row['piece']; 
		$free = $row['free_qty']; 
		}
		/*pre($landing_price);exit();*/
		if($cases[$key]==1)
		{

		$cases[$key] = floor($quantity[$key]/$p); 
		$str[] = "('$rId', '$value', '{$base_price[$key]}','{$landing_price[$key]}', '{$quantity[$key]}', '$mfdate', '$expdate', '{$purchase_inv[$key]}','{$sale_price[$key]}', '{$cases[$key]}')";

		}
		else
		{
		$quant[$key] = $quantity[$key]*$p;
		$schem[$key] = $free*$quantity[$key];
		$str[] = "('$rId', '$value', '{$base_price[$key]}', '{$landing_price[$key]}', '{$quant[$key]}', '$mfdate', '$expdate', '{$purchase_inv[$key]}','{$sale_price[$key]}', '{$quantity[$key]}')";

		}

		}
	}
	$str = implode(', ', $str);
	$str_cat = implode(', ', $str_cat);
	$q = "INSERT INTO `opening_stocks_details` (`order_id`, `product_id`, `mrp`,`rate`, `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`) VALUES $str";
	$r = mysqli_query($dbc, $q);
	/*h1($q);exit();*/
	// if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved some error occurred.') ;
	// $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES $str_cat";
	// 
	// $r = mysqli_query($dbc, $q);
	if (!$r)
	return array('status' => false, 'myreason' => ' Purchase Stock details could not be saved some error occurred.');
	if ($company_id == 1){
	write_query($q);
	}
	return array('status' => true, 'myreason' => '');
}

public function opening_stocks_edit($id) {
global $dbc;
$out = array('status' => 'false', 'myreason' => '');
list($status, $d1) = $this->get_opening_stocks_se_data();
if (!$status)
return array('staus' => false, 'myreason' => $d1['myreason']);
// $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
$receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
$id = $id;
$orderno = $d1['order_no'];
//Start the transaction
mysqli_query($dbc, "START TRANSACTION");
// query to update
$q = "UPDATE opening_stocks SET created_date = NOW(), company_id = '$d1[company_id]' ,csa_id = '$d1[csa_id]' WHERE id = '$id'";
// h1($q);

$r = mysqli_query($dbc, $q);
if (!$r) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => 'Primary Stock could not be updated some error occurred');
}
if ($company_id == 1){
write_query($q);
}
/*pre($_POST['dealer_rate']);exit();*/
$rId = $id;

$extrawork = $this->opening_stocks_extra_edit('Update', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'], $_POST['scheme_quantity'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['pr_rate'], $_POST['cases'],$_POST['dealer_rate']);

if (!$extrawork['status']) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => $extrawork['myreason']);
}
mysqli_commit($dbc);
//Saving the user modification history
//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
//$this->save_log($hid, $modifieddata, $d1['what']);
return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
}

public function get_opening_stocks_list($filter = '', $records = '', $orderby = '') {
	global $dbc;
	$out = array();
	$filterstr = $this->oo_filter($filter, $records, $orderby);
	$mtype = $_SESSION[SESS . 'constant']['retailer_level'];
	$q = "SELECT opening_stocks.*,opening_stocks.id AS id,cv.product_name,cv.itemcode as item_code FROM opening_stocks INNER JOIN catalog_view AS cv ON cv.product_id=opening_stocks.product_id $filterstr";
	// h1($q);
	list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
	if (!$opt)
	return $out;
	// if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
	// else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
	$brand_map = get_my_reference_array('catalog_1', 'id', 'name');
	while ($row = mysqli_fetch_assoc($rs)) {
		$id = $row['id'];
		$out[$id] = $row; // storing the item id
		//$out[$id]['name'] = $dealer_map[$row['dealer_id']];
		//$out[$id]['person_name'] = $this->get_username($row['created_person_id']);
		//$out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT cases,usod.mrp,usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM opening_stocks_details usod INNER JOIN opening_stocks uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id');
	}// while($row = mysqli_fetch_assoc($rs)){ ends

	return $out;
}

//This function used to get user retailer gift deatils
public function get_username($id) {
global $dbc;
$out = NULL;
$q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
if (!$opt)
return $out;
return $rs['uname'];
}

public function next_primary_order_num() {
global $dbc;
$out = array();
$q = "SELECT MAX(id) AS total FROM user_sales_order";
list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
//return $rs['total']+1; 
}
public function primary_sale_delete($id, $filter = '', $records = '', $orderby = '') {
global $dbc;
if (empty($filter))
$filter = "itemId = $id";
$out = array('status' => false, 'myreason' => '');
$deleteRecord = $this->get_sale_list($filter = "order_id=$id", $records, $orderby);
if (empty($deleteRecord)) {
$out['myreason'] = 'user sales order not found';
return $out;
}
//start the transaction
mysqli_query($dbc, "START TRANSACTION");
//Running the deletion queries
$delquery = array();
$delquery['user_sales_order'] = "DELETE FROM user_sales_order WHERE order_id = $id LIMIT 1";
$delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE order_id = $id";
foreach ($delquery as $key => $value) {
if (!mysqli_query($dbc, $value)) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => '$key query failed');
}
}
//After successfull deletion
mysqli_commit($dbc);
return array('status' => true, 'myreason' => 'Sales Order successfully deleted');
}

}

?>