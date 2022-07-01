<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class loc_countryclass extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_country_name()
	{
		$d1 = array('countryname'=>ucwords($_POST['countryname']));
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_country_name()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_name();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `loc_country` (`loc_countryId`, `countryname`, `locked`) VALUES (NULL , '$d1[countryname]', 0)";
		$r = mysqli_query($dbc,$q);
		/*$rId = mysqli_insert_id($d55bc5);
		$q1 = "INSERT INTO loc_country_group (`lcgId`)";*/
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'Country name successfully saved', 'rId'=>$rId);
	}
	public function edit_country($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_name();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_country` SET `countryname` = '$d1[countryname]'  WHERE loc_countryId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'QC updated with QC no '.$id);
		return array('status'=>true, 'reason'=>'Country name successfully updated');
	}
	
	public function get_country_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM loc_country $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['loc_countryId'];
			$out[$id]['loc_countryId'] = $id; // storing the item id
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_country_group()
	{
		$d1 = array('grname'=>ucwords($_POST['grname']),'grtype'=>$_POST['grtype']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_country_group()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_group();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `loc_group` (`grpId`, `grname`,`grtype`, `locked`) VALUES (NULL , '$d1[grname]','$d1[grtype]', 0)";
		$r = mysqli_query($dbc,$q);
		/*$rId = mysqli_insert_id($d55bc5);
		$q1 = "INSERT INTO loc_country_group (`lcgId`)";*/
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'location group added'.$d1['grname']);
		return array('status'=>true, 'reason'=>'Location successfully saved', 'rId'=>$rId);
	}
	public function edit_country_group($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_group();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_group` SET `grname` = '$d1[grname]',`grtype` = '$d1[grtype]'  WHERE grpId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'location group updaded'.$id);
		return array('status'=>true, 'reason'=>'location group successfully updated');
	}
	
	public function get_country_group_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM loc_group $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['grpId'];
			$out[$id]['grpId'] = $id; // storing the item id
			$out[$id]['grname'] = $row['grname'];
			$out[$id]['grtype'] = $row['grtype'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_country_group_assign_data()
	{
		$d1 = array('grpId'=>$_POST['grpId'],'loc_countryId'=>$_POST['loc_countryId']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_country_group_assign()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_group_assign_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$str = '';
		foreach($d1['loc_countryId'] as $key=>$value)
		{
			$str.= "(NULL, '$d1[grpId]','$value'), ";
		}
		$str = rtrim($str,', ');
		$q = "INSERT INTO `loc_country_group` (`lcgId`, `grpId`,`loc_countryId`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		/*$rId = mysqli_insert_id($d55bc5);
		$q1 = "INSERT INTO loc_country_group (`lcgId`)";*/
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'location group added'.$d1['grname']);
		return array('status'=>true, 'reason'=>'Country group location successfully saved', 'rId'=>$rId);
	}
	public function edit_country_group_assign($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_country_group_assign_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `loc_country_group` SET `grname` = '$d1[grname]',`grtype` = '$d1[grtype]'  WHERE grpId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'location group updaded'.$id);
		return array('status'=>true, 'reason'=>'location group successfully updated');
	}
	public function get_country_group_assign_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM loc_country_group INNER JOIN loc_country USING(loc_countryId) INNER JOIN loc_group USING(grpId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$country = '';
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['grpId'];
			$out[$id]['grpId'] = $id; // storing the item id
			$out[$id]['grname'] = $row['grname'];
			$country.=$row['countryname'].', ';
			$out[$id]['countryname'] = rtrim($country,', ');
			$out[$id]['grtype'] = $row['grtype'];
			$out[$id]['loc_countryId'] = $row['loc_countryId'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>