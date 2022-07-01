<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class quality extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	//This functon will summarise the vendor wise PO that can be generated from the PR Request
	public function get_vendor_via_pr()
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pr_item.*, items.isvId, name FROM pr_item  INNER JOIN items USING(itemId) INNER JOIN item_source_vendor AS ISV ON ISV.isvId = items.isvId WHERE  pr_item.postatus = '0'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$id = $row['isvId'];
				$itemId = $row['itemId'];
				$out[$id][$itemId][] = $row['qty'];
				$out[$id]['qty'][] = $row['qty'];
				$out[$id]['vendor_name'] = $row['name'];
			}
		}
		return $out;
	}
	
	//This functon will summarise the vendor wise PO that can be generated from the PR Request
	public function get_po_item_by_vendor($vid)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pr_item.*, filename, cost_price, purchasecode, salecode, items.isvId, name FROM pr_item  INNER JOIN items USING(itemId) INNER JOIN item_source_vendor AS ISV ON ISV.isvId = items.isvId WHERE  pr_item.postatus = '0' AND isv.isvId='$vid'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$id = $row['isvId'];
				$itemId = $row['itemId'];
				$out[$id][$itemId]['qty'][] = $row['qty'];
				$out[$id][$itemId]['prId'][$row['prId']] = $row['prId'];
				$out[$id][$itemId]['cpc'] = $row['cost_price'];
				$out[$id][$itemId]['pcode'] = $row['purchasecode'];
				$out[$id][$itemId]['scode'] = $row['salecode'];
				$out[$id][$itemId]['image'] = $row['filename'];
			}
		}
		return $out;
	}
	
	// This function will return the next available ps no
	function get_next_qcno()
	{
		global $dbc;
		$q = "SELECT MAX(q_no) AS qcno FROM quality";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		return $rs['qcno']+1;
	}
	
	public function get_qc_data($mode='add')
	{
		$_POST['fchallan_date'] = get_mysql_date($_POST['challan_date']);
		
		$qc = array('qcno'=>$_POST['qcno'], 'isvId'=>$_POST['isvId'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id'], 'challan'=>$_POST['challan'], 'challan_date'=>$_POST['fchallan_date'], 'pono'=>$_POST['orderno'], 'billt'=>$_POST['billt'], 'wt'=>$_POST['wt']);
		$qcitems = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty']);
		return array(true, $qc, $qcitems);
	}
	
	// This function will save the user qc  
	public function save_qc()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $qc, $qcitems) = $this->get_qc_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `quality` (`qId`, `q_no`, `sesId`, `created`, `id`, `isvId`, `challan`, `challan_date`, `pono`, `weight`, `billt`, `bill_receive`) VALUES (NULL , '$qc[qcno]', '$qc[csess]', NOW(), '$qc[uid]', '$qc[isvId]', '$qc[challan]', '$qc[challan_date]', '$qc[pono]', '$qc[wt]', '$qc[billt]', 0)";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'qc table error');
		$qcid = mysqli_insert_id($dbc);
		$str = '';
		// preparing the save string
		foreach($qcitems['itemId'] as $key => $itemid)
			$str .= "($qcid, $itemid, {$qcitems['qty'][$key]}, {$qcitems['rate'][$key]}, 0), ";
		$str = rtrim($str,', ');
		//query to save the data in po_item table
		$q = "INSERT INTO `quality_item` (`qId`, `itemId`, `qty`, `rate`, `instatus`) VALUES $str";	
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Qc item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Quality successfully received', 'qId'=>$qcid);
	}
	
	// This function will update the pr_item entries on po creation
	public function pr_item_update_on_pocreate($poId,$vid)
	{
		global $dbc;
		//Getting the list of items which were shown from pr to this vendor for po creation
		// This will be either equal to or less or greater than the items order in the PO
		$pritems = $this->get_po_item_by_vendor($vid);
		if($pritems)
		{
			$unique_pr = array();
			foreach($pritems[$vid] as $key=>$value)
			{
				$str = implode(',', $value['prId']);
				$q = "UPDATE pr_item SET postatus = '$poId' WHERE prId IN ($str) AND itemId='$key'";
				mysqli_query($dbc, $q);
				foreach($value['prId'] as $key1=>$value1)
					$unique_pr[$key1] = $value1;
			}
			/* We need to update the pr table and set value as
			 * 1 - if some item of this PR has been considered in the po creation
			 * 2 - if all item of this PR has been considered in the po creation
			*/
			$this->pr_update_on_pocreate($unique_pr);
		}
	}
	
	//This functio will update the pr table po_status link
	public function pr_update_on_pocreate($upr)
	{
		global $dbc;
		foreach($upr as $key=>$value)
		{
			$q= "SELECT itemId FROM pr_item WHERE prId = '$key' AND postatus = 0";
			list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
			if($opt)
				mysqli_query($dbc, $q="UPDATE pr SET po_status=1 WHERE prId = '$key'");	// indicate partial pr ordered
			else
				mysqli_query($dbc, $q="UPDATE pr SET po_status=2 WHERE prId = '$key'");	 // indicate full pr ordered
		}
	}
	
	
	// This function will save the user pr  
	public function edit_qc($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $qc, $qcitems) = $this->get_qc_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `quality` SET `isvId` = '$qc[isvId]', `challan` = '$qc[challan]', `challan_date` = '$qc[challan_date]', `pono` = '$qc[pono]', `weight` = '$qc[wt]', `billt` = '$qc[billt]', `modified` = NOW(), `mrId` = '$qc[uid]' WHERE qId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_query($dbc, "DELETE FROM quality_item WHERE qId = '$id'");
		$str = '';
		// preparing the save string
		foreach($qcitems['itemId'] as $key => $itemid)
			$str .= "($id, $itemid, {$qcitems['qty'][$key]}, {$qcitems['rate'][$key]}, {$instatus[$key]}), ";
		$str = rtrim($str,', ');
		//query to save the data in pr_item table
		$q = "INSERT INTO `quality_item` (`qId`, `itemId`, `qty`, `rate`, `instatus`) VALUES $str";	
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'QC item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'QC updated with QC no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'Quality successfully updated');
	}
	
	
	
	//This function will return the list of pr based on the filter condition applied
	public function get_qc_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT qc.*, DATE_FORMAT(qc.created,'%e/%b/%Y<br/> %r') AS fdated, DATE_FORMAT(qc.created,'%e/%b/%Y') AS justdate, DATE_FORMAT(qc.challan_date,'%e/%b/%Y') AS challan_date, code FROM quality AS qc INNER JOIN item_source_vendor USING (isvId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['qId'];
			$out[$id]['qId'] = $id; // storing the item id
			$out[$id]['q_no'] = $row['q_no'];
			$out[$id]['isvId'] = $row['isvId'];
			$out[$id]['code'] = $row['code'];
			$out[$id]['pono'] = $row['pono'];
			$out[$id]['dated'] = $row['fdated'];
			$out[$id]['justdate'] = $row['justdate'];
			$out[$id]['challan'] = $row['challan'];
			$out[$id]['challan_date'] = $row['challan_date'];
			$out[$id]['weight'] = $row['weight'];
			$out[$id]['billt'] = $row['billt'];
			$out[$id]['bill_receive'] = $row['bill_receive'];
		}
		return $out;
	}
	
	//This function will list the item order for a giver order id
	public function get_qc_item($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT quality_item.*, salecode, purchasecode, filename, pono FROM quality_item INNER JOIN items USING(itemId) INNER JOIN quality USING(qId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$ooId = $row['qId'];
			$out[$ooId][$id]['itemId'] = $id; // storing the item id
			$out[$ooId][$id]['qty'] = (int)$row['qty'];
			$out[$ooId][$id]['cpc'] = (int)$row['rate'];
			$out[$ooId][$id]['image'] = $row['filename'];
			$out[$ooId][$id]['pcode'] = $row['purchasecode'];
			$out[$ooId][$id]['scode'] = $row['salecode'];
			$out[$ooId][$id]['qId'] = $row['qId'];
			$out[$ooId][$id]['pono'] = $row['pono'];
			$out[$ooId][$id]['instatus'] = $row['instatus'];
		}
		return $out;
	}
	
	// This function will return the items of the pr that are modifiable
	public function get_po_modifiable_items($prId)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT itemId FROM pr_item WHERE prId = '$prId' AND postatus=0";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$out[$prId][] = $row['itemId'];
			}
		}
		return $out;
	}
	
	//This function will return the list of pr based on the filter condition applied
	public function is_pr_modifiable($prId)
	{
		$out = false;
		$status = $this->get_pr_modifiable_items($prId);
		if($status) $out = true;
		return $out;
	}
}
?>