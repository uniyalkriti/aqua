<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class ref_class extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_aima_rating()
	{
		$d1 = array('rarname'=>$_POST['rarname']);
		//$d1 = array('rarname'=>'rarname1', 'rarweight'=>'81','locked'=>'0');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the aima_rating
	public function save_aima_rating()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_aima_rating();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
        $q1="SELECT MAX(rarweight) as rarId FROM ref_aima_rating";
		list($opt, $rs) = run_query($dbc, $q1, $mode='single', $msg='');
		$rarweight = $rs['rarId'] + 10;
		$q = "INSERT INTO `ref_aima_rating` (`rarId`, `rarname`,`rarweight`) VALUES (NULL , '$d1[rarname]','$rarweight' )";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Aima rating table error');
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Aima Rating successfully saved', 'rId'=>$rId);
	}
	
	
	// This function will save the user pr  
	public function update_aima_rating($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_aima_rating();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_aima_rating` SET  `rarname` = '$d1[rarname]' WHERE rarId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Aima rating table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Aima rating with Aima no '.$id);
		return array('status'=>true, 'myreason'=>'Aima rating successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_aima_rating_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM ref_aima_rating $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['rarId'];
			$out[$id]['rarId'] = $id; // storing the item id
			$out[$id]['rarname'] = $row['rarname'];
			$out[$id]['rarweight'] = $row['rarweight'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_degree_preference()
	{
		//$d1 = array('rdpname'=>$_POST['rdpname'],'locked'=>$_POST['locked']);
		$d1 = array('rdpname'=>'rdpname1','locked'=>'1');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the aima_rating
	public function save_degree_preference()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_degree_preference();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `ref_degree_preference` (`rdpId`, `rdpname`, `locked`) VALUES (NULL , '$d1[rdpname]','$d1[locked]' )";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Degree preference table error');
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Degree preference successfully saved', 'rId'=>$rId);
	}
	
	
	// This function will save the user pr  
	public function update_degree_preference($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_degree_preference();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_degree_preference` SET `rdpname` = '$d1[rdpname]', `locked` = '$d1[locked]' WHERE rdpId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Degree preference table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Degree preference  no '.$id);
		return array('status'=>true, 'myreason'=>'Degree preference successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_degree_preference_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM ref_degree_preference $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['rdpId'];
			$out[$id]['rdpId'] = $id; // storing the item id
			$out[$id]['rdpname'] = $row['rdpname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_affiliation()
	{
		$d1 = array('aff_name'=>$_POST['aff_name']);
		//$d1 = array('rarname'=>'rarname1', 'rarweight'=>'81','locked'=>'0');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the aima_rating
	public function save_affiliation()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_affiliation();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "INSERT INTO `ref_affiliation` (`afId`, `aff_name`) VALUES (NULL , '$d1[aff_name]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'myreason'=>'Ref affiliation table error');
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Affiliation successfully saved', 'rId'=>$rId);
	}
	
	
	// This function will save the user pr  
	public function update_affiliation($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_affiliation();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_affiliation` SET  `aff_name` = '$d1[aff_name]' WHERE afId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Aima rating table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Affiliation with affiliation no '.$id);
		return array('status'=>true, 'myreason'=>'Affiliation successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_affiliation_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM ref_affiliation $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['afId'];
			$out[$id]['afId'] = $id; // storing the item id
			$out[$id]['aff_name'] = $row['aff_name'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	
}
?>