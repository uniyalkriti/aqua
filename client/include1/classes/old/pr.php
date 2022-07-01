<?php
// This class will handle all the task related to purchase request creation
class pr extends myfilter
{
	public $prid = NULL;
	public $prno = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_pr_data($mode='add')
	{
		$pr = array('prno'=>$_POST['prno'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$pritems = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['rate'], 'qty'=>$_POST['qty']);
		return array(true, $pr, $pritems);
	}
	
	// This function will save the user pr  
	public function save_pr()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $pr, $pritems) = $this->get_pr_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the pr
		$q = "INSERT INTO `pr` (`prId`, `prno`, `sesId`, `created`, `id`, `po_status`) VALUES (NULL , '$pr[prno]', '$pr[csess]', NOW(), '$pr[uid]', '0')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'pr table error');
		$prid = mysqli_insert_id($dbc);
		$str = '';
		// preparing the save string
		foreach($pritems['itemId'] as $key => $itemid)
			$str .= "($prid, $itemid, {$pritems['qty'][$key]}, 0), ";
		$str = rtrim($str,', ');
		//query to save the data in pr_item table
		$q = "INSERT INTO `pr_item` (`prId`, `itemId`, `qty`, `postatus`) VALUES $str";	
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'PR item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Pr added with PR no '.$pr[prno]);
		return array('status'=>true, 'reason'=>'Pr successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_pr($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $pr, $pritems, $postatus) = $this->get_pr_data('edit');
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		$postatus = $_POST['postatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		mysqli_query($dbc, "DELETE FROM pr_item WHERE prId = '$id'");
		$str = '';
		// preparing the save string
		foreach($pritems['itemId'] as $key => $itemid)
			$str .= "($id, $itemid, {$pritems['qty'][$key]}, {$postatus[$key]}), ";
		$str = rtrim($str,', ');
		//query to save the data in pr_item table
		$q = "INSERT INTO `pr_item` (`prId`, `itemId`, `qty`, `postatus`) VALUES $str";	
		$r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'PR item table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Pr updated with PR no '.$pr[prno]);
		return array('status'=>true, 'reason'=>'Pr successfully updated');
	}
	
	// This function will return the next available pr no
	function get_next_prno()
	{
		global $dbc;	
		$q = "SELECT MAX(prno) AS purno FROM pr";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		return $rs['purno']+1;
	}
	
	//This function will return the list of pr based on the filter condition applied
	public function get_pr_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = '';
		if(!empty($filter) || !empty($records) || !empty($orderby))
			$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT pr.*, prno, DATE_FORMAT(pr.created,'%e/%b/%Y<br/> %r') AS fdated, DATE_FORMAT(pr.modified,'%e/%b/%Y<br/> %r') AS flastedit,uname FROM pr INNER JOIN admin USING(id) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['prId'];
			$out[$id]['prId'] = $id; // storing the item id
			$out[$id]['prno'] = $row['prno'];
			$out[$id]['dated'] = $row['fdated'];
			$out[$id]['postatus'] = $row['po_status'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['user'] = $row['uname'];
		}
		return $out;
	}
	
	//This function will list the item order for a giver order id
	public function get_pr_item($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT pr_item.*, salecode, purchasecode, filename, prno FROM pr_item INNER JOIN items USING(itemId) INNER JOIN pr USING(prId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$ooId = $row['prId'];
			$out[$ooId][$id]['itemId'] = $id; // storing the item id
			$out[$ooId][$id]['qty'] = (int)$row['qty'];
			$out[$ooId][$id]['image'] = $row['filename'];
			$out[$ooId][$id]['pcode'] = $row['purchasecode'];
			$out[$ooId][$id]['scode'] = $row['salecode'];
			$out[$ooId][$id]['prId'] = $row['prId'];
			$out[$ooId][$id]['prno'] = $row['prno'];
			$out[$ooId][$id]['postatus'] = $row['postatus'];
		}
		return $out;
	}
	
	// This function will return the items of the pr that are modifiable
	public function get_pr_modifiable_items($prId)
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