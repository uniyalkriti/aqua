<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class info_label extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_info_data()
	{
		
		$d1 = array('ila_name'=>$_POST['ila_name'], 'is_required'=>$_POST['is_required'],'ila_type'=>$_POST['ila_type'], 'sortorder'=>$_POST['sortorder']);
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_info_label()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_info_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `info_label` (`ilaId`,`ila_name`, `is_required`, `ila_type`,`sortorder`) VALUES (NULL , '$d1[ila_name]', '$d1[is_required]','$d1[ila_type]','$d1[sortorder]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'info_label  table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'label added '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'label successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_info_label($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_info_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `info_label` SET `ila_name` = '$d1[ila_name]', `is_required`='$d1[is_required]', `ila_type`='$d1[ila_type]', `sortorder`='$d1[sortorder]' WHERE ilaId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Info label updated'.$d1['ila_name']);
		return array('status'=>true, 'reason'=>'Info label successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_label_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *FROM  info_label $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['ilaId'];
			$out[$id]['ilaId'] = $id; // storing the item id 
			$out[$id]['ila_name'] = $row['ila_name'];
			$out[$id]['is_required'] = $row['is_required'];
			$out[$id]['ila_type'] = $row['ila_type'];
			$out[$id]['sortorder'] = $row['sortorder'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>