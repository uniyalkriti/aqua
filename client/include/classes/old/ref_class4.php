<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class ref_class4 extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_ref_course_level()
	{
		
		$d1 = array('rclename'=>$_POST['rclename']);
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_ref_course_lavel()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_ref_course_level();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		echo $q = "INSERT INTO `ref_course_level` (`rcleId`, `rclename`) VALUES (NULL , '$d1[rclename]')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'course level table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Course added with Course level '.$d1['rclename']);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Course level successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_ref_course_lavel($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_ref_course_level();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_course_level` SET `rclename` = '$d1[rclename]' WHERE rcleId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course updated with Course level'.$d1['rclename']);
		return array('status'=>true, 'reason'=>'Course level successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_ref_course_lavel_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM ref_course_level $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['rcleId'];
			$out[$id]['rcleId'] = $id; // storing the item id
			$out[$id]['rclename'] = $row['rclename'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>