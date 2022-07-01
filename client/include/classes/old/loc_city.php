<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class loc_city extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_loc_city()
	{
		
		$d1 = array('stateId'=>$_POST['stateId'], 'city_name'=>$_POST['city_name'] );
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_loc_city()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_city();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `loc_city_district` (`cityId`, `stateId`, `city_name`) VALUES (NULL , '$d1[stateId]', '$d1[city_name]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'course level table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'State added with Location State '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'City successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_loc_city($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_city();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_city_district` SET `stateId` = '$d1[stateId]', `city_name`='$d1[city_name]' WHERE cityId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'City updated with Location State'.$d1['city_name']);
		return array('status'=>true, 'reason'=>'City successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_loc_city_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM  loc_state INNER JOIN loc_city_district USING(stateId) INNER JOIN loc_country USING(loc_countryId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cityId'];
			$out[$id]['cityId'] = $id; // storing the item id 
			$out[$id]['stateId'] = $row['stateId'];
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['city_name'] = $row['city_name'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>