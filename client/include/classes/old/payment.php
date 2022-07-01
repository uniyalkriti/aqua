<?php
// This class will handle all the task related to purchase order creation
class payment extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	// This function will return the next available po no
	public function get_payment_voucher_no()
	{
		global $dbc;
		$q = "SELECT MAX(pyno) AS pono FROM payment WHERE sesId={$_SESSION[SESS.'csess']}";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		$pono = (int)$rs['pono'];
		if($pono == 0) $pono = 1; else $pono += 1;
		return $pono;
	}
	
	public function get_se_data($mode='add')
	{
		$d1 = array('bId'=>$_POST['billno'], 'grId'=>$_POST['grno'],  'gr_adjust_amt'=>$_POST['gr_adjust_amt'], 'discrate'=>$_POST['discrate'], 'discount'=>$_POST['discount'],  'netdiscount'=>$_POST['netdiscount'],  'payment'=>$_POST['payment'], 'pmode'=>$_POST['pmode'], 'chequeno'=>$_POST['chequeno'], 'bankname'=>$_POST['bankname'],  'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		
		//Converting the sent dates to proper formats
		//$d1['payment_date'] = get_mysql_date($d1['payment_date'], $sep='/', $time = false, $mysqlsearch = false);
		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function payment_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$pyno = $this->get_payment_voucher_no();
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$inc = 0;
		for($i=0; $i<count($d1['bId']); $i++)
		{
			$d1['grId'][$i] = (int) $d1['grId'][$i];
			// if user is not selecting any grId then gr_amount has to be zero.
			if($d1['grId'][$i] == 0) $d1['gr_adjust_amt'][$i] == 0;
			
			$discrate_value = $d1['netdiscount'][$i] - $d1['discount'][$i];
			$bill_balance = $discrate_value + $d1['discount'][$i] + $d1['payment'][$i] + $d1['gr_adjust_amt'][$i];
			$gr_balance = $d1['gr_adjust_amt'][$i];
			
			$q = "INSERT INTO `payment` (`pyId`, `pyno`, `bId`, `grId`, `gr_adjust_amt`, `discrate`, `discrate_value`,  `discount`, `payment`,  `payment_date`, `pmode`, `chequeno`, `bankname`, `created`, `crId`, `modified`, `moId`, `sesId`) VALUES (NULL , $pyno,  '{$d1['bId'][$i]}', '{$d1['grId'][$i]}',  '{$d1['gr_adjust_amt'][$i]}', '{$d1['discrate'][$i]}', '$discrate_value',  '{$d1['discount'][$i]}', '{$d1['payment'][$i]}', NOW(), '{$d1['pmode'][$i]}', '{$d1['chequeno'][$i]}', '{$d1['bankname'][$i]}', NOW(), '$d1[uid]', NULL, NULL, '$d1[csess]')";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				// if grId is not available no need to update the good_return table
				$r2 = ($d1['grId'][$i] == 0) ? true: mysqli_query($dbc, $q = "UPDATE good_return SET gr_balance = (gr_balance - $gr_balance) WHERE grId = {$d1['grId'][$i]}");
				$r3 = mysqli_query($dbc, $q = "UPDATE billing SET balance = (balance - $bill_balance) WHERE bId = {$d1['bId'][$i]}");
				if($r2 && $r3) $inc += 1;
				
			}
		}
		//h1($inc.'---------->'.count($d1['bId']));
		if($inc == count($d1['bId'])) $r = true; else $r = false;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Payment table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		//$this->bill_balance_calculator($d1['bId']);
		history_log($dbc, 'Add', 'Payment added with Voucher no  '.$pyno);
		return array('status'=>true, 'myreason'=>'Payment successfully Saved against <br> <strong>Payment voucher No : '.$pyno.'</strong>', 'rId'=>$pyno);
	}
	
	// This function will edit the details of a given party id
	public function payment_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$pyno = $id;
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//fetching the bId and grId which we need to reverify balance after this update
		$mystat = $this->get_payment_list($filter="py.pyno = $pyno AND py.sesId={$d1['csess']}",  $records = '', $orderby='');
		$rdel = mysqli_query($dbc,"DELETE FROM payment WHERE pyno=$pyno AND sesId={$d1['csess']}");
		if(!$rdel) return array('status'=>false, 'myreason'=>'Payment pre entry deletion error');
		// query to save
		$inc = 0;
		for($i=0; $i<count($d1['bId']); $i++)
		{
			$d1['grId'][$i] = (int) $d1['grId'][$i];
			// if user is not selecting any grId then gr_amount has to be zero.
			if($d1['grId'][$i] == 0) $d1['gr_adjust_amt'][$i] == 0;
			
			$discrate_value = $d1['netdiscount'][$i] - $d1['discount'][$i];
			$bill_balance = $discrate_value + $d1['discount'][$i] + $d1['payment'][$i] + $d1['gr_adjust_amt'][$i];
			$gr_balance = $d1['gr_adjust_amt'][$i];
			$q = "INSERT INTO `payment` (`pyId`, `pyno`, `bId`, `grId`, `gr_adjust_amt`, `discrate`, `discrate_value`,  `discount`, `payment`,  `payment_date`, `pmode`, `chequeno`, `bankname`, `created`, `crId`, `modified`, `moId`, `sesId`) VALUES (NULL , $pyno,  '{$d1['bId'][$i]}', '{$d1['grId'][$i]}',  '{$d1['gr_adjust_amt'][$i]}', '{$d1['discrate'][$i]}', '$discrate_value', '{$d1['discount'][$i]}', '{$d1['payment'][$i]}', NOW(), '{$d1['pmode'][$i]}', '{$d1['chequeno'][$i]}', '{$d1['bankname'][$i]}', NOW(), '$d1[uid]', NULL, NULL, '$d1[csess]')";
			$r = mysqli_query($dbc,$q);
			if($r)
			{
				// if grId is not available no need to update the good_return table
				$r2 = ($d1['grId'][$i] == 0) ? true: mysqli_query($dbc, $q = "UPDATE good_return SET gr_balance = (gr_balance - $gr_balance) WHERE grId = {$d1['grId'][$i]}");
				$r3 = mysqli_query($dbc, $q = "UPDATE billing SET balance = (balance - $bill_balance) WHERE bId = {$d1['bId'][$i]}");
				if($r2 && $r3) $inc += 1;
				
			}
		}
		if($inc == count($d1['bId'])) $r = true; else $r = false;
		//we need to reverifgy the gr and bill balance
		if($r)
		{
			$bl = new bill();
			$gr = new gr();
			foreach($mystat as $key=>$value)
			{
				$bId = $value['bId'];
				$grId = $value['grId'];
				//balance need to be verified for every bId as it always have to be selected
				$bl->bill_balance_reverify_onedit($bId);
				//gr can be verified if  user has selected any gr so this value will be greater than zero
				if($grId > 0)
					$gr->gr_balance_reverify_onedit($grId);
			}
		}
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Payment table error');}
		//mysqli_rollback($dbc);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Payment updated with Voucher no '.$id.' '.$d1['csess']);
		return array('status'=>true, 'myreason'=>'payment successfully updated', 'rId'=>$id);
	}
	
	//This function will return the list of as reflected from function name
	public function get_summary_payment_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.		
		$pyId = $this->get_payment_list($filter,  $records, $orderby);
		$pyno = array();
		foreach($pyId as $key=>$value)
			$pyno[$value['pyno']] = $value['pyno'];
		if(empty($pyno)) return $out;	
		$pyno = implode(', ',$pyno);
		
		$q = "SELECT py.*, pp.pname AS p_party_name, sp.pname AS s_party_name, bl.s_party, bl.p_party , DATE_FORMAT(py.payment_date,'".DC."') AS payment_date FROM payment AS py INNER JOIN billing AS bl USING(bId) INNER JOIN party AS pp ON bl.p_party = pp.pId AND pp.ptype=1 INNER JOIN party AS sp ON bl.s_party = sp.pId  WHERE pyno IN ($pyno) AND py.sesId={$_SESSION[SESS.'csess']}";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['pyno'];
			$out[$id]['pyno'] = $id; // storing the item id
			$out[$id]['discount'][] = $row['discount'];
			$out[$id]['gr_adjust_amt'][] = $row['gr_adjust_amt'];
			$out[$id]['payment'][] = $row['payment'];			
			$out[$id]['payment_date'] = $row['payment_date'];
			$out[$id]['pmode'][$row['pmode']] = $row['pmode'];
			$out[$id]['chequeno'][$row['chequeno']] = $row['chequeno'];
			//Billing and purchase party details
			$out[$id]['p_party_name'][$row['p_party_name']] = $row['p_party_name'];
			$out[$id]['s_party_name'][$row['s_party_name']] = $row['s_party_name'];
		}
		// level 2 summarising
		$out2 = array();
		foreach($out as $key=>$value)
		{
			$out2[$key]['pyno'] = $value['pyno'];
			$out2[$key]['payment'] = array_sum($value['discount']) + array_sum($value['gr_adjust_amt']) + array_sum($value['payment']);
			$out2[$key]['payment_date'] = $value['payment_date'];
			$out2[$key]['pmode'] = implode(', ',$value['pmode']);
			$out2[$key]['chequeno'] = implode(', ',$value['chequeno']);
			$out2[$key]['p_party_name'] = implode(', ',$value['p_party_name']);
			$out2[$key]['s_party_name'] = implode(', ',$value['s_party_name']);
		}
		return $out2;
	}
	
	//This function will return the list of as reflected from function name
	public function get_payment_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		//$q = "SELECT py.*, p_party, s_party, pp.city AS p_city, pp.pname AS p_party_name, sp.pname AS s_party_name, DATE_FORMAT(py.payment_date,'".DC."') AS payment_date,  DATE_FORMAT(py.created,'".DTSB."') AS fdated, DATE_FORMAT(py.created,'".DC."') AS jd_created, DATE_FORMAT(py.modified,'".DTSB."') AS flastedit, DATE_FORMAT(py.modified,'".DC."') AS jd_modified FROM payment AS py INNER JOIN billing AS bl USING(bId) INNER JOIN party AS pp ON bl.p_party = pp.pId AND pp.ptype=1 INNER JOIN party AS sp ON bl.s_party = sp.pId  $filterstr";
		$q = "SELECT py.*, p_party, s_party FROM payment AS py INNER JOIN billing AS bl USING(bId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['pyId'];
			$out[$id]['pyId'] = $id; // storing the item id
			$out[$id]['pyno'] = $row['pyno'];
			$out[$id]['bId'] = $row['bId'];
			$out[$id]['grId'] = $row['grId'];
			$out[$id]['gr_adjust_amt'] = $row['gr_adjust_amt'];
			$out[$id]['discrate'] = $row['discrate'];
			$out[$id]['netdiscount'] = $row['discrate_value']+$row['discount'];
			$out[$id]['discount'] = $row['discount'];
			$out[$id]['payment'] = $row['payment'];
			$out[$id]['pmode'] = $row['pmode'];
			$out[$id]['chequeno'] = $row['chequeno'];
			$out[$id]['bankname'] = $row['bankname'];
			//Billing and purchase party details
			$out[$id]['p_party'] = $row['p_party'];
			$out[$id]['s_party'] = $row['s_party'];
			// Date Related Details			
		}
		return $out;
	}
}
?>