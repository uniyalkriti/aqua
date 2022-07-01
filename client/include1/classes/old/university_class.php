<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class university_class extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_se_data()
	{
		$d1 = array('un_name'=>ucwords($_POST['un_name']),'rarId'=>($_POST['rarId']),'address'=>($_POST['address']),'un_short_name'=>($_POST['un_short_name']));
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_university_name()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `university` (`unId`, `rarId`, `un_name`,`address`,`un_short_name` ) VALUES (NULL , '$d1[rarId]', '$d1[un_name]', '$d1[address]',  '$d1[un_short_name]')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'University name successfully saved', 'rId'=>$rId);
	}
	public function edit_unversity($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `university` SET `rarId` = '$d1[rarId]', `un_name` = '$d1[un_name]', `address`='$d1[address]', `un_short_name`='$d1[un_short_name]' WHERE unId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'QC updated with QC no '.$id);
		return array('status'=>true, 'reason'=>'Unversity name successfully updated');
	}
	
	public function get_unversity_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *,uni.locked as loc FROM university as uni INNER JOIN  ref_aima_rating as rat USING(rarId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['unId'];
			$out[$id]['unId'] = $id; // storing the item id
			$out[$id]['rarId'] = $row['rarId'];
			$out[$id]['un_name'] = $row['un_name'];
			$out[$id]['un_short_name'] = $row['un_short_name'];
			$out[$id]['address'] = $row['address'];
			$out[$id]['locked'] = $row['loc'];
			$out[$id]['rarname'] = $row['rarname'];
		}
		return $out;
	}
}
?>