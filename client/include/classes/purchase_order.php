<?php

class purchase_order extends myfilter {

public $id = NULL;
public $id_detail = NULL;

public function __construct($id = NULL) {
parent::__construct();
$this->id = $id;
}

######################################## Invoice Starts here ####################################################

public function get_purchase_order_se_data() {
$d1 = $_POST;
//$d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
$d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
$d1['myreason'] = 'Please fill all the required information';
$d1['what'] = 'Purchase Order'; //whether to do history log or not
return array(true, $d1);
}

public function purchase_order_save() {
global $dbc;

$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$dealer_code = $_SESSION[SESS . 'data']['dealer_code'];
$pid = $_SESSION[SESS . 'data']['id'];
$company_id_cus = $_SESSION[SESS.'data']['company_id_cus'];

//$_SESSION[SESS . 'data']['csa_id'] = $data['csa_id'];
$out = array('status' => 'false', 'myreason' => '');
list($status, $d1) = $this->get_purchase_order_se_data();
//pre($d1); //exit;
if (!$status)
return array('staus' => false, 'myreason' => $d1['myreason']);
//$orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
$orderno=$d1['order_no'];
$receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
$challan_date = !empty($d1['ch_date']) ? get_mysql_date($d1['ch_date']) : '';
$id = $_SESSION[SESS . 'data']['dealer_id'] . date('Ymdhis');
$csa_id = $d1['csa_id'];
$comp_id = $d1['comp_id'];
$challan_no = $d1['challan_no'];
$firm_name = $d1['firm_name'];


if($comp_id=='1')
 {
	$csa_id =$_SESSION[SESS . 'data']['csa_id'];
	$depo_code =myrowval('csa','csa_name','c_id='.$csa_id);
 }else{
 	$csa_id ='0';
 	$depo_code = '0';
 }

$date_time=date('Y-m-d H:i:s');
mysqli_query($dbc, "START TRANSACTION");
if($_POST['quantity']!=0 || !empty($_POST['quantity'])){
$qps = "INSERT INTO `purchase_order`(`id`,`order_id`, `dealer_code`, `dealer_id`, `created_date`, `created_person_id`, `order_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, `challan_no`, `depo_code`, `csa_id`,`firm_name`,`sale_date`)
 VALUES ('$orderno','$orderno','$dealer_code', '$dealer_id', '$challan_date', '$pid','$receive_date',NOW(), 
 '$date_time', '$company_id_cus','$challan_date', '$challan_no','$depo_code','$csa_id','$firm_name',NOW())";
// h1($qps); 
//die;
$rps = mysqli_query($dbc, $qps);
}
if (!$rps) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => 'Purchase Order Table error');
}else{
	$productId=$d1['product_id'];
	foreach ($productId as $key => $value) {
				$product_id=$value;
		$item_code=myrowval('catalog_product','itemcode','id='.$product_id);
		$mrp=$d1['base_price'][$key];
		$rate=$d1['dealer_rate'][$key];
		$quantity=$d1['quantity'][$key];
		$scheme_qty=$d1['scheme_quantity'][$key];
		$mfg_date1=$d1['mfg_date'][$key]; 
		$mfgdate = date('Y-m-d',strtotime($mfg_date1));
		// $mfgdate=$mfg_date1[2].'-'.$mfg_date1[1].'-'.$mfg_date1[0];
		//$mfgdate = !empty($mfg_date1) ? get_mysql_date($mfg_date1) : '';
		$batch_no=$d1['batch_no'][$key];
		$gross_amt=$d1['gross_Amt'][$key];
		$td_amount=-($d1['trade_price'][$key]);
		$sch_amt=-($d1['scheme_amt'][$key]);
		$spl_amt=-($d1['spl_amt'][$key]);
		$cd_amount=-($d1['cd_amount'][$key]);
		$atd_amt=-($d1['atd_amt'][$key]);
		$taxable_amount=$gross_amt+($td_amount+$sch_amt+$spl_amt+$cd_amount+$atd_amt);
		$cgst_amount=$d1['cgst_amount'][$key];
		//$cgst_amount=ROUND((($taxable_amount*$cgst)/100),2);
		$sgst_amount=$d1['sgst_amount'][$key];
		$igst_amount=$d1['igst_amount'][$key];
		$qgst = "select igst from `_gst` INNER JOIN catalog_product ON catalog_product.hsn_code = 
	_gst.hsn_code where catalog_product.id = $value";
		$rgst = mysqli_query($dbc, $qgst);
		while($rowgst = mysqli_fetch_assoc($rgst))
		{
			$gst = $rowgst['igst']; 
		}
		$total_amount=$d1['total_amt'][$key];
		$qpod="INSERT INTO `purchase_order_details`(`company_id`,`order_id`, `product_id`, `item_code`, `mrp`, `rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`, `gross_amt`, `taxable_amount`, `td_amount`, `sch_amt`, `spl_amt`, `cd_amount`, `atd_amt`, `cgst_amount`, `sgst_amount`, `igst_amount`, `gst_percentage`, `cr_note`, `dr_note`, `total_amount`) VALUES ('$company_id_cus','$orderno','$product_id','$item_code','$mrp','$rate','$quantity','$scheme_qty','$challan_no','$mfgdate','$mfgdate',NOW(),'$batch_no','$rate','0','$gross_amt','0.00','$td_amount','$sch_amt','$spl_amt','$cd_amount','$atd_amt','$cgst_amount','$sgst_amount','$igst_amount','$gst','0.00','0.00','$total_amount')";
	// h1($qpod);exit;
		$rod=mysqli_query($dbc,$qpod);
		if (!$rod) {
			mysqli_rollback($dbc);
			return array('status' => false, 'myreason' => 'Purchase Order Details Table error');
		}

		else{
			$qsu="SELECT product_id FROM stock WHERE product_id='$product_id' AND dealer_id='$dealer_id' AND batch_no='$batch_no' AND mfg='$mfgdate'";
			// h1($qsu);exit;
			$rsu=mysqli_query($dbc,$qsu);
			
			if(mysqli_num_rows($rsu)>0){	
				$qs="UPDATE `stock` SET `qty`=qty+$quantity,`remaining`=remaining+$quantity,`update_date_time`=NOW() WHERE product_id='$product_id' AND dealer_id='$dealer_id' AND batch_no='$batch_no' AND mfg='$mfgdate'";

				
			// h1($qs);exit;
			}
			else{
				$qs="INSERT INTO `stock`(`product_id`, `batch_no`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`, `sync_status`, `update_date_time`) VALUES ('$product_id','$batch_no','$rate','$rate','$mrp','$pid','$csa_id','$dealer_id','$quantity','0','0','$quantity','$mfgdate','$mfgdate',NOW(),'$rate','$comp_id','1','1',NOW())";
				// h1($qs);exit;	
			}
			$rs=mysqli_query($dbc,$qs);
			if (!$rs) {
				mysqli_rollback($dbc);
				return array('status' => false, 'myreason' => 'Stock Table error');
			}	
		}
	}
}
$rId = $challan_no;
mysqli_commit($dbc);
//Final success 
return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
}

public function purchase_order_extra($actiontype, $rId, $productId, $batch_no,
 $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date,
  $sale_price,$company_id,$cases,$landing_price,$total_amt,$cgst_amount,
  $sgst_amount,$gross_Amt,$trade_price,$cash_amt,$scheme_amt,$spl_amt,$atd_amt) {
	/*pre($base_price); exit();*/
	global $dbc;
	$uncode = '';
	$str = $str_cat = array();
	if ($actiontype == 'Update')
	mysqli_query($dbc, "DELETE FROM purchase_order_details WHERE order_id = $rId");

	foreach ($productId as $key => $value) {
	
		$mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
		$expdate = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';

		if ($actiontype == 'Update')
		$uncode = date('ymdhis') + $key + 1;
		else
		$uncode = date('ymdhis') + $key + 1;
	// print_r($cases[$key]);
		if($quantity[$key]!=0 || !empty($quantity[$key])){
		///////////////CHANGE CODE////////////////////
  $qgst = "select igst from `_gst` INNER JOIN catalog_product ON catalog_product.hsn_code = 
_gst.hsn_code where catalog_product.id = $value";
$rgst = mysqli_query($dbc, $qgst);
while($rowgst = mysqli_fetch_assoc($rgst))
{
$gst = $rowgst['igst']; 

}

		$s = "select piece,free_qty from `cases` where product_id = $value";
		$rsss = mysqli_query($dbc, $s);
		while($row = mysqli_fetch_assoc($rsss))
		{
		$p = $row['piece']; 
		$free = $row['free_qty']; 
		}
		//pre($gst);//exit();*/
	//	if($cases[$key]==1)
		//{
			//$landing_price=	$landing_price[$key];
		
	//	$cases[$key] = floor($quantity[$key]/$p); 
		$str[] = "('$rId', '$value','0', '{$base_price[$key]}', '{$landing_price[$key]}',
		'{$quantity[$key]}','{$scheme[$key]}', '{$purchase_inv[$key]}','{$mfg_date[$key]}', 
		'$expdate','','{$batch_no[$key]}','','{$quant[$key]}','{$gross_Amt[$key]}',
		'{$taxable_amount[$key]}','{$trade_price[$key]}','{$scheme_amt[$key]}',
		'{$spl_amt[$key]}','{$cash_amt[$key]}','{$atd_amt[$key]}',
		'{$cgst_amount[$key]}','{$sgst_amount[$key]}','','$gst','','' ,'{$total_amt[$key]}')";

		//}
		// else
		// {
		// $quant[$key] = $quantity[$key]*$p;
		// $schem[$key] = $free*$quantity[$key];
		// $str[] = "('$rId', '$value','0', '{$base_price[$key]}', '{$landing_price[$key]}',
		//  '{$quantity[$key]}','{$scheme_quantity[$key]}', '{$purchase_inv[$key]}','$mfdate', 
		//  '$expdate','','{$batch_no[$key]}','','{$quant[$key]}','{$gross_Amt[$key]}',
		//  '{$taxable_amount[$key]}','{$trade_price[$key]}','{$scheme_amt[$key]}',
		//  '{$spl_amt[$key]}','{$cash_amt[$key]}','{$atd_amt[$key]}',
		//  '{$cgst_amount[$key]}','{$sgst_amount[$key]}','','$gst','','' ,'{$total_amt[$key]}')";

		// }

		}
	}
	$str = implode(', ', $str);
	$str_cat = implode(', ', $str_cat);
	$q="INSERT INTO `purchase_order_details`(`order_id`, `product_id`, `item_code`, `mrp`, 
	`rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`,
	 `receive_date`, `batch_no`, `pr_rate`,
	 `cases`, `gross_amt`, `taxable_amount`, `td_amount`, `sch_amt`, `spl_amt`, 
	 `cd_amount`, `atd_amt`, `cgst_amount`, `sgst_amount`, `igst_amount`,
	  `gst_percentage`, `cr_note`, `dr_note`, `total_amount`) values $str
	";
	//h1($q);
//	$q = "INSERT INTO `purchase_order_details` (`order_id`, `product_id`, `mrp`,`rate`,
	// `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`,`gst`)
	// VALUES $str";
	$r = mysqli_query($dbc, $q);
	//h1($q);//exit();
	// if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved some error occurred.') ;
	// $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES $str_cat";
	// 
	// $r = mysqli_query($dbc, $q);
	if (!$r)
	return array('status' => false, 'myreason' => ' Purchase Stock details could not be saved some error occurred.');

	write_query($q);

	return array('status' => true, 'myreason' => '');
}



public function purchase_order_extra_edit($actiontype, $rId, $productId, $batch_no, 
	$base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,
	$cases,$landing_price) {
	/*pre($base_price);
	pre($landing_price); exit();*/
	global $dbc;
	$uncode = '';
	$str = $str_cat = array();
	if ($actiontype == 'Update')
	mysqli_query($dbc, "DELETE FROM purchase_order_details WHERE order_id = $rId");

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
	$q = "INSERT INTO `purchase_order_details` (`order_id`, `product_id`, `mrp`,`rate`, `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`) VALUES $str";
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

public function purchase_order_edit($id) {
global $dbc;
$out = array('status' => 'false', 'myreason' => '');
list($status, $d1) = $this->get_purchase_order_se_data();
if (!$status)
return array('staus' => false, 'myreason' => $d1['myreason']);
// $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
$receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
$id = $id;
$orderno = $d1['order_no'];
//Start the transaction
mysqli_query($dbc, "START TRANSACTION");
// query to update
$q = "UPDATE purchase_order SET order_date='$receive_date', created_date = NOW(), company_id = '$d1[company_id]' ,csa_id = '$d1[csa_id]' WHERE id = '$id'";
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

$extrawork = $this->purchase_order_extra_edit('Update', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'], $_POST['scheme_quantity'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['pr_rate'], $_POST['cases'],$_POST['dealer_rate']);

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
#############

public function get_purchase_order_details_list($filter = '', $records = '', $orderby = '') {
	global $dbc;
	$out = array();
	$filterstr = $this->oo_filter($filter, $records, $orderby);
	$mtype = $_SESSION[SESS . 'constant']['retailer_level'];
	$q = "SELECT *,csa_name,DATE_FORMAT(created_date,'%d/%m/%Y') AS sale_date,
	DATE_FORMAT(order_date,'%d/%m/%Y') AS order_date,
	DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,"
	. "DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order_details_view
	left join csa c on purchase_order_details_view.csa_id=c.c_id  $filterstr";
	// h1($q);
	list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
	if (!$opt)
	return $out;
	// if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
	// else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
	$brand_map = get_my_reference_array('catalog_1', 'id', 'name');
	while ($row = mysqli_fetch_assoc($rs)) {
		$id = $row['oid'];
		$out[$id] = $row; // storing the item id
		$out[$id]['name'] = $dealer_map[$row['dealer_id']];
		$out[$id]['person_name'] = $this->get_username($row['created_person_id']);

		// if($out[$id]['receive_date']=='00/00/0000')
        //        {
        //         $pquery = "SELECT total_amount,scheme_qty,mrp,gst_percentage,cases,usod.id,cp.name,usod.rate,
        //             usod.quantity,usod.scheme_qty,pr_rate, batch_no
        //             ,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,
        //             purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,
        //             cp.id AS product_id FROM purchase_order_details usod 
        //             INNER JOIN purchase_order uso ON usod.order_id = uso.order_id AND usod.purchase_inv=uso.challan_no 
        //             INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE
        //             usod.purchase_inv = '$row[challan_no]' AND uso.csa_id='$row[csa_id]'";
        //        // h1($pquery);
        //        }
        //        else
        //        {
        //         $pquery = "SELECT total_amount,scheme_qty,gst_percentage,cases,usod.id,cp.name,usod.rate,usod.quantity, pr_rate, 
        //             batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv,
        //             DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id
        //             FROM purchase_order_details usod INNER JOIN purchase_order uso ON 
        //             usod.order_id = uso.order_id AND usod.purchase_inv=uso.challan_no INNER JOIN catalog_product cp
        //             ON usod.product_id=cp.id WHERE usod.purchase_inv = '$row[challan_no]' AND uso.csa_id='$row[csa_id]'";
        //        //h1($pquery);
                
        //        }
               $out[$id]['order_item'] = $this->get_my_reference_array_direct($pquery, 'id');
               $where3="purchase_order.csa_id='$row[csa_id]' AND purchase_order_details.purchase_inv='$row[challan_no]'";
               $out[$id]['total_purchase_amount']=myrowvaljoin('purchase_order_details','sum(total_amount)','purchase_order','purchase_order_details.order_id=purchase_order.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv',$where3);
	
		// $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT gst,cases,usod.mrp,usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id');
	}// while($row = mysqli_fetch_assoc($rs)){ ends

	return $out;
}
###############
public function get_purchase_order_list($filter = '', $records = '', $orderby = '') {
	global $dbc;
	$out = array();
	$filterstr = $this->oo_filter($filter, $records, $orderby);
	$mtype = $_SESSION[SESS . 'constant']['retailer_level'];
	$q = "SELECT *,csa_name,DATE_FORMAT(created_date,'%d/%m/%Y') AS sale_date,
	DATE_FORMAT(order_date,'%d/%m/%Y') AS order_date,
	DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,"
	. "DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order
	left join csa c on purchase_order.csa_id=c.c_id  $filterstr";
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
		$out[$id]['name'] = $dealer_map[$row['dealer_id']];
		$out[$id]['person_name'] = $this->get_username($row['created_person_id']);

		if($out[$id]['receive_date']=='00/00/0000')
               {
                $pquery = "SELECT total_amount,scheme_qty,mrp,gst_percentage,cases,usod.id,cp.name,usod.rate,
                    usod.quantity,usod.scheme_qty,pr_rate, batch_no
                    ,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,
                    purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,
                    cp.id AS product_id FROM purchase_order_details usod 
                    INNER JOIN purchase_order uso ON usod.order_id = uso.order_id AND usod.purchase_inv=uso.challan_no 
                    INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE
                    usod.purchase_inv = '$row[challan_no]' AND uso.csa_id='$row[csa_id]'";
               // h1($pquery);
               }
               else
               {
                $pquery = "SELECT total_amount,scheme_qty,gst_percentage,cases,usod.id,cp.name,usod.rate,usod.quantity, pr_rate, 
                    batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv,
                    DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id
                    FROM purchase_order_details usod INNER JOIN purchase_order uso ON 
                    usod.order_id = uso.order_id AND usod.purchase_inv=uso.challan_no INNER JOIN catalog_product cp
                    ON usod.product_id=cp.id WHERE usod.purchase_inv = '$row[challan_no]' AND uso.csa_id='$row[csa_id]'";
               //h1($pquery);
                
               }
               $out[$id]['order_item'] = $this->get_my_reference_array_direct($pquery, 'id');
              //  $where3="purchase_order.csa_id='$row[csa_id]' AND purchase_order_details.purchase_inv='$row[challan_no]'";
               $where3="purchase_order.csa_id='$row[csa_id]' AND purchase_order_details.purchase_inv='$row[challan_no]' AND purchase_order_details.order_id='$row[order_id]'";
               $out[$id]['total_purchase_amount']=myrowvaljoin('purchase_order_details','sum(total_amount)','purchase_order','purchase_order_details.order_id=purchase_order.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv',$where3);
	
		// $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT gst,cases,usod.mrp,usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id');
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