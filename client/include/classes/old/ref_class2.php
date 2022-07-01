<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class ref_class2 extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_entrance_exam()
	{
		$d1 = array('reexname'=>$_POST['reexname']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_entrance_exam()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_entrance_exam();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `ref_entrance_exam` (`reexId`, `reexname`, `locked`) VALUES (NULL , '$d1[reexname]', 0)";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>' Ref entrance exam successfully saved', 'rId'=>$rId);
	}
	public function edit_entrance($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_entrance_exam();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_entrance_exam` SET `reexname` = '$d1[reexname]'  WHERE reexId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'QC updated with QC no '.$id);
		return array('status'=>true, 'reason'=>'Entrance table successfully updated');
	}
	
	public function get_entrance_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM ref_entrance_exam $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['reexId'];
			$out[$id]['reexId'] = $id; // storing the item id
			$out[$id]['reexname'] = $row['reexname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>