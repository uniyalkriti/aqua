<?php
// This class will handle all the task related to purchase order creation
class po extends myfilter
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
	
	// This function will return the next available po no
	function get_next_pono()
	{
		global $dbc;
		$q = "SELECT MAX(po_no) AS pono FROM pur_order";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		return $rs['pono']+1;
	}
	
	public function get_po_data($mode='add')
	{
		$po = array('pono'=>$_POST['pono'], 'isvId'=>$_POST['isvId'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$poitems = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $po, $poitems);
	}
	
	// This function will create the user po  
	public function create_po()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $po, $poitems) = $this->get_po_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the po
		$q = "INSERT INTO `pur_order` (`poId`, `po_no`, `sesId`, `created`, `id`, `isvId`, `po_close`) VALUES (NULL , '$po[pono]', '$po[csess]', NOW(), '$po[uid]', '$po[isvId]', '0')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'po table error');
		$poid = mysqli_insert_id($dbc);
		$str = '';
		// preparing the save string
		foreach($poitems['itemId'] as $key => $itemid)
			$str .= "($poid, $itemid, {$poitems['qty'][$key]}, {$poitems['rate'][$key]}, '{$poitems['desc'][$key]}', 0), ";
		$str = rtrim($str,', ');
		//query to save the data in po_item table
		$q = "INSERT INTO `pur_order_item` (`poId`, `itemId`, `qty`, `rate`, `description`, `instatus`) VALUES $str";	
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Po item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Po added with Po no '.$po['pono']);
		//updating the pr_item
		$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'PO successfully Created', 'poId'=>$poid);
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
	public function edit_po($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $po, $poitems, $postatus) = $this->get_po_data('edit');
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		$poid = $id;
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		mysqli_query($dbc, "UPDATE pur_order SET modified=NOW(), mrid='$po[uid]' WHERE poId = '$id'");
		mysqli_query($dbc, "DELETE FROM pur_order_item WHERE poId = '$id'");
		$str = '';
		// preparing the save string
		foreach($poitems['itemId'] as $key => $itemid)
			$str .= "($poid, $itemid, {$poitems['qty'][$key]}, {$poitems['rate'][$key]}, '{$poitems['desc'][$key]}', 0), ";
		$str = rtrim($str,', ');
		//query to save the data in po_item table
		$q = "INSERT INTO `pur_order_item` (`poId`, `itemId`, `qty`, `rate`, `description`, `instatus`) VALUES $str";
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'PO item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'PO updated with PO no '.$po['pono']);
		return array('status'=>true, 'reason'=>'PO successfully updated');
	}
	
	
	
	//This function will return the list of pr based on the filter condition applied
	public function get_po_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT po.*, DATE_FORMAT(po.created,'%e/%b/%Y<br/> %r') AS fdated, DATE_FORMAT(po.created,'%e/%b/%Y') AS justdate, DATE_FORMAT(po.modified,'%e/%b/%Y<br/> %r') AS flastedit, code FROM pur_order AS po INNER JOIN item_source_vendor USING (isvId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['poId'];
			$out[$id]['poId'] = $id; // storing the item id
			$out[$id]['pono'] = $row['po_no'];
			$out[$id]['isvId'] = $row['isvId'];
			$out[$id]['code'] = $row['code'];
			$out[$id]['po_close'] = $row['po_close'];
			$out[$id]['dated'] = $row['fdated'];
			$out[$id]['justdate'] = $row['justdate'];
			$out[$id]['modified'] = $row['flastedit'];
		}
		return $out;
	}
	
	//This function will list the item order for a giver order id
	public function get_po_item($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT pur_order_item.*, salecode, purchasecode, filename, po_no FROM pur_order_item INNER JOIN items USING(itemId) INNER JOIN pur_order USING(poId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$ooId = $row['poId'];
			$out[$ooId][$id]['itemId'] = $id; // storing the item id
			$out[$ooId][$id]['qty'] = (int)$row['qty'];
			$out[$ooId][$id]['cpc'] = (int)$row['rate'];
			$out[$ooId][$id]['image'] = $row['filename'];
			$out[$ooId][$id]['pcode'] = $row['purchasecode'];
			$out[$ooId][$id]['scode'] = $row['salecode'];
			$out[$ooId][$id]['poId'] = $row['poId'];
			$out[$ooId][$id]['pono'] = $row['po_no'];
			$out[$ooId][$id]['desc'] = $row['description'];
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