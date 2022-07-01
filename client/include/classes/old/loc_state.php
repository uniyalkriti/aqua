<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class loc_state extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_loc_state()
	{
		
		$d1 = array('statename'=>$_POST['statename'], 'loc_countryId'=>$_POST['loc_countryId'] );
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_loc_state()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_state();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `loc_state` (`stateId`, `loc_countryId`, `statename`) VALUES (NULL , '$d1[loc_countryId]', '$d1[statename]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'state table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'State added with Location State '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'State successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_loc_state($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_state();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_state` SET `loc_countryId` = '$d1[loc_countryId]', `statename`='$d1[statename]' WHERE stateId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'State updated with Location State'.$d1['statename']);
		return array('status'=>true, 'reason'=>'State successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_loc_state_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM loc_country INNER JOIN loc_state USING(loc_countryId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['stateId'];
			$out[$id]['stateId'] = $id; // storing the item id
			$out[$id]['loc_countryId'] = $row['loc_countryId'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>