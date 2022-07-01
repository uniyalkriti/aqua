<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class course_branch extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_course_branch()
	{
		
		$d1 = array('cbname'=>$_POST['cbname'], 'cbId'=>$_POST['cbId'] );
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_course_branch()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course_branch` (`cbId`, `cId`, `cbname`) VALUES (NULL , '$d1[cId]', '$d1[cbname]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Course_Branch table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'State added with Location State '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Branch successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course_branch($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course_branch` SET `cId` = '$d1[cId]', `cbname`='$d1[cbname]' WHERE cbId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Branch updated with Course'.$d1['cbname']);
		return array('status'=>true, 'reason'=>'Branch successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_branch_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_branch INNER JOIN course USING(cId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbId'];
			$out[$id]['cbId'] = $id; // storing the item id
			$out[$id]['cId'] = $row['cId'];
			$out[$id]['cbname'] = $row['cbname'];
		//	$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>