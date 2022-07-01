<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class ref_coursedomain extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_course_domain()
	{
		$d1 = array('cdname'=>$_POST['cdname']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function save_course_domain()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_course_domain();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course_domain` (`cdId`, `cdname`, `locked`) VALUES (NULL , '$d1[cdname]', 0)";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'myreason'=>' Course Domainsuccessfully saved', 'rId'=>$rId);
	}
	public function edit_course_domain($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_course_domain();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course_domain` SET `cdname` = '$d1[cdname]'  WHERE cdId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Course_domain table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'QC updated with QC no '.$id);
		return array('status'=>true, 'myreason'=>'Course_domain table successfully updated');
	}
	
	public function get_course_domain_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM course_domain $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cdId'];
			$out[$id]['cdId'] = $id; // storing the item id
			$out[$id]['cdname'] = $row['cdname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_course_domain_content()
	{
		$d1 = array('cdId'=>$_POST['cdId'],'cId'=>$_POST['cId']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the aima_rating
	public function save_course_domain_content()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_domain_content();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		$str = '';
		mysqli_query($dbc, "START TRANSACTION");
		foreach($d1['cId'] as $key=>$val)
		{
			$cdcId = $d1['cdId'].$val;
			$str.= "('$cdcId', '$d1[cdId]', $val),";
		}
		$str = rtrim($str,',');		
		$q = "INSERT INTO `course_domain_content` (`cdcId`, `cdId`, `cId`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Course Domin Content preference table error');
		mysqli_commit($dbc);
		return array('status'=>true, 'reason'=>'Course domain content successfully saved', 'rId'=>$rId);
	}
	
	
	// This function will save the user pr  
	public function update_course_domain_content($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_domain_content();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		$qd= "DELETE FROM course_domain_content WHERE cdid =$id";
		$rs = mysqli_query($dbc,$qd);
		mysqli_query($dbc, "START TRANSACTION");
		$str = '';
		mysqli_query($dbc, "START TRANSACTION");
		foreach($d1['cId'] as $key=>$val)
		{
			$cdcId = $d1['cdId'].$val;
			$str.= "('$cdcId', '$d1[cdId]', $val),";
		}
	    $str = rtrim($str,',');		
		$q = "INSERT INTO `course_domain_content` (`cdcId`, `cdId`, `cId`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Degree preference table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Degree preference  no '.$id);
		return array('status'=>true, 'myreason'=>'Degree preference successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_domain_content_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
	    $q = "SELECT * FROM course_domain_content INNNER JOIN course_domain USING(cdId) INNER JOIN course USING(cId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cdId'];
			$cId = $row['cId'];
			$out[$id]['cdcId'] = $row['cdcId']; // storing the item id
			$out[$id]['cId'][$cId] = $row['cId'];
			$out[$id]['cdId'] = $row['cdId'];
			$out[$id]['cdname'] = $row['cdname'];
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
}
?>