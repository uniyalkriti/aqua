<?php
// This class will handle all the task related to purchase order creation
class bill extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	public function get_se_data($mode='add')
	{
		$d1 = array('p_party'=>$_POST['p_party'], 's_party'=>$_POST['s_party'], 'invoice_no'=>$_POST['invoice_no'], 'invoice_date'=>$_POST['invoice_date'], 'lr_no'=>$_POST['lr_no'], 'lr_date'=>$_POST['lr_date'], 'bill_amount'=>$_POST['bill_amount'], 'transport'=>$_POST['transport'], 'empId'=>$_POST['empId'], 'remark'=>$_POST['remark'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		
		//Converting the sent dates to proper formats
		$d1['invoice_date'] = get_mysql_date($d1['invoice_date'], $sep='/', $time = false, $mysqlsearch = false);
		$d1['lr_date'] = get_mysql_date($d1['lr_date'], $sep='/', $time = false, $mysqlsearch = false);	
		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function bill_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$balance = $d1['bill_amount'];
		$q = "INSERT INTO `billing` (`bId`, `p_party`, `s_party`, `invoice_no`, `invoice_date`, `lr_no`, `lr_date`, `bill_amount`, `transport`, `empId`, `remark`, `created`, `crId`, `modified`, `moId`, `balance`, `sesId`) VALUES (NULL , '$d1[p_party]', '$d1[s_party]', '$d1[invoice_no]', '$d1[invoice_date]', '$d1[lr_no]', '$d1[lr_date]', '$d1[bill_amount]', '$d1[transport]', '$d1[empId]', '$d1[remark]', NOW(), '$d1[uid]', NULL, NULL, $balance, '$d1[csess]')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Billing table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Bill added with Id '.$rId. ' '.$d1['invoice_no']);
		// in case the user is interested for doing the settlement
		//if($d1['agr'] == 1) $this->gr_updater_onsave($rId, $d1['p_party'], $d1['s_party']);//
		return array('status'=>true, 'myreason'=>'Bill successfully Saved', 'rId'=>$rId);
	}
	
	public function gr_updater_onsave($bId,$pp,$sp)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT grId, gramount FROM good_return WHERE settlement=0 AND bId IN (SELECT bId FROM billing WHERE p_party=$pp AND s_party=$sp)";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$totgr = 0;
		$grId = array();
		while($row = mysqli_fetch_assoc($rs))
		{
			$grId[] = $row['grId'];
			$totgr += $row['gramount'];
		}
		$grIdstr = implode(',',$grId);
		//update the GR table
		mysqli_query($dbc, $q="UPDATE good_return SET settlement='$bId' WHERE grId IN ($grIdstr)" );
		//update the Billing table for the same amount
		mysqli_query($dbc, $q="UPDATE billing SET prev_due='$totgr' WHERE bId = '$bId'" );
	}
	
	// This function will edit the details of a given party id
	public function bill_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE billing SET `p_party`='$d1[p_party]', `s_party`='$d1[s_party]', `invoice_no`='$d1[invoice_no]', `invoice_date`='$d1[invoice_date]', `lr_no`='$d1[lr_no]', `lr_date`='$d1[lr_date]', `bill_amount`='$d1[bill_amount]', `transport`='$d1[transport]', `empId`='$d1[empId]', `remark`='$d1[remark]', modified=NOW(), `moId`='$d1[uid]' WHERE bId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Billing table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Bill updated with Id '.$id.' '.$d1['invoice_no']);
		$this->bill_balance_reverify_onedit($id);
		return array('status'=>true, 'myreason'=>'Bill successfully updated', 'rId'=>$id);
	}
	
	//This function will compute a bil balance if the bill is edited
	public function bill_balance_reverify_onedit($bId)
	{
		global $dbc;
		$q = "SELECT SUM(payment) AS payment, SUM(discount) AS discount, SUM(discrate_value) AS discrate_value, SUM(gr_adjust_amt) AS gr_adjust_amt FROM payment WHERE bId = $bId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$amount_paid = $rs['payment'] + $rs['discrate_value'] + $rs['discount'] +  $rs['gr_adjust_amt'];
		mysqli_query($dbc,"UPDATE billing SET balance = (bill_amount - $amount_paid) WHERE bId = $bId LIMIT 1");
	}
	
	//This function will return the list of as reflected from function name
	public function get_bill_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT bl.*, pp.pname AS p_party_name, sp.pname AS s_party_name, DATE_FORMAT(bl.invoice_date,'".DQ."') AS invoice_date,  DATE_FORMAT(bl.lr_date,'".DQ."') AS lr_date,  DATE_FORMAT(bl.created,'".DTSB."') AS fdated, DATE_FORMAT(bl.created,'".DC."') AS jd_created, DATE_FORMAT(bl.modified,'".DTSB."') AS flastedit, DATE_FORMAT(bl.modified,'".DC."') AS jd_modified FROM billing AS bl INNER JOIN party AS pp ON bl.p_party = pp.pId AND pp.ptype=1 INNER JOIN party AS sp ON bl.s_party = sp.pId  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['bId'];
			$out[$id]['bId'] = $id; // storing the item id
			$out[$id]['invoice_no'] = $row['invoice_no'];
			$out[$id]['invoice_date'] = $row['invoice_date'];
			$out[$id]['lr_no'] = $row['lr_no'];
			$out[$id]['lr_date'] = $row['lr_date'];
			$out[$id]['bill_amount'] = $row['bill_amount'];
			$out[$id]['balance'] = $row['balance'];
			$out[$id]['transport'] = $row['transport'];
			$out[$id]['empId'] = $row['empId'];
			$out[$id]['remark'] = $row['remark'];
			//Billing and purchase party details
			$out[$id]['p_party'] = $row['p_party'];
			$out[$id]['p_party_name'] = $row['p_party_name'];
			$out[$id]['s_party'] = $row['s_party'];
			$out[$id]['s_party_name'] = $row['s_party_name'];
			
			// Date Related Details
			$out[$id]['created'] = $row['fdated'];
			$out[$id]['jdc'] = $row['jd_created'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['jdm'] = $row['jd_modified'];
			
		}
		return $out;
	}
	
	// This function will return the pending payment against a bill no 
	public function pending_payment($bId=NULL)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT sum(amount) as amount FROM payment WHERE bId=$bId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
			$out = $rs['amount'];
		return $out;
	}
	
	// This function will return the discount against a bill no 
	public function discount_payment($bId=NULL)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT sum(discount) as amount FROM payment WHERE bId=$bId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
			$out = $rs['amount'];
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function purchase_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT pId, pname FROM party WHERE ptype=1 ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	//This function can be used when asking for sale party combobox array
	public function sale_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT pId, pname FROM party WHERE ptype=2 ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	public function sale_party_with_balance($p_party = '')
	{
		global $dbc;
		$out = array();
		$str = '';
		if(!empty($p_party)) $str = " AND p_party = $p_party ";
		$q = "SELECT pId, pname FROM party INNER JOIN billing ON s_party = pId  WHERE balance > 0 $str ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	public function purchase_party_with_balance($s_party = '')
	{
		global $dbc;
		$out = array();
		$str = '';
		if(!empty($p_party)) $str = " AND s_party = $s_party ";
		$q = "SELECT pId, pname FROM party INNER JOIN billing ON p_party = pId  WHERE balance > 0 $str ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	public function bill_with_balance($condstr = '')
	{
		global $dbc;
		$out = array();
		$str = '';
		if(!empty($condstr)) $str = " AND $condstr ";
		$q = "SELECT bId, concat_ws(' => Rs ',invoice_no, balance) AS billno FROM billing  WHERE balance > 0 $str ORDER BY invoice_no ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['bId']] = $row['billno'];
		}
		return $out;
	}
	// This function will return the gr whose adjustment is balance
	public function gr_with_balance($condstr = '')
	{
		global $dbc;
		$out = array();
		$str = '';
		if(!empty($condstr)) $str = " AND $condstr ";
		$q = "SELECT grId, concat_ws(' => Rs ',gr_no, gr_balance) AS gr_no FROM good_return  WHERE gr_balance > 0 $str ORDER BY gr_balance ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['grId']] = $row['gr_no'];
		}
		return $out;
	}
}
?>