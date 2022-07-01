<?php
// This class will handle all the task related to purchase order creation
class transport extends myfilter
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
		$d1 = array('trname'=>$_POST['trname'], 'adrs'=>$_POST['adrs'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function transport_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `transport` (`trId`, `trname`, `adrs`, `created`, `crId`, `modified`, `moId`) VALUES (NULL , '$d1[trname]', '$d1[adrs]', NOW(), '$d1[uid]', NULL, NULL)";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'transport table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Transport added with Id '.$rId. ' '.$d1['trname']);
		return array('status'=>true, 'myreason'=>'Transport successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given party id
	public function transport_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE transport SET `trname`='$d1[trname]', `adrs`='$d1[adrs]', modified=NOW(), `moId`='$d1[uid]' WHERE trId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'transport table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Transport updated with Id '.$id.' '.$d1['trname']);
		return array('status'=>true, 'myreason'=>'Transport successfully updated', 'rId'=>$id);
	}
	
	//This function will return the list of as reflected from function name
	public function get_transport_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'".DTSB."') AS fdated, DATE_FORMAT(created,'".DC."') AS jd_created, DATE_FORMAT(modified,'".DTSB."') AS flastedit, DATE_FORMAT(modified,'".DC."') AS jd_modified FROM transport $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['trId'];
			$out[$id]['trId'] = $id; // storing the item id
			$out[$id]['trname'] = $row['trname'];
			$out[$id]['adrs'] = $row['adrs'];
			// Date Related Details
			$out[$id]['created'] = $row['fdated'];
			$out[$id]['jdc'] = $row['jd_created'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['jdm'] = $row['jd_modified'];
			
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function transport_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT trId AS id, trname AS name FROM transport ORDER BY trname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['id']] = $row['name'];
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function transport_byId($id)
	{
		global $dbc;
		$out = '';
		$q = "SELECT trname AS name FROM transport WHERE trId = $id";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
}
?>