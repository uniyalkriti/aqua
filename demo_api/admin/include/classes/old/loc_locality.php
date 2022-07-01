<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class loc_locality extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_loc_locality()
	{
		
		$d1 = array('cityId'=>$_POST['cityId'], 'localityname'=>$_POST['localityname'], 'pincode'=>$_POST['pincode'] );
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_loc_locality()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_locality();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `loc_locality` (`localityId`, `cityId`, `localityname`, `pincode`) VALUES (NULL , '$d1[cityId]', '$d1[localityname]', '$d1[pincode]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'locality table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Locality added with Location State '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Locality successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_loc_locality($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_loc_locality();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//$instatus = $_POST['instatus'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_locality` SET `cityId` = '$d1[cityId]', `localityname`='$d1[localityname]', `pincode`='$d1[pincode]' WHERE localityId = '$id'";
		//$q = "UPDATE `ref_course_level` SET `rclename` = 'PTUG' WHERE rcleId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Locality updated with Location State'.$d1['localityname']);
		return array('status'=>true, 'reason'=>'Locality successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_loc_locality_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM loc_city_district INNER JOIN loc_locality USING(cityId) INNER JOIN loc_state USING(stateId) INNER JOIN loc_country USING(loc_countryId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['localityId'];
			$out[$id]['localityId'] = $id; // storing the item id
			$out[$id]['cityId'] = $row['cityId'];
			$out[$id]['stateId'] = $row['stateId'];
			$out[$id]['loc_countryId'] = $row['loc_countryId'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['city_name'] = $row['city_name'];
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['localityname'] = $row['localityname'];
			$out[$id]['pincode'] = $row['pincode'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>