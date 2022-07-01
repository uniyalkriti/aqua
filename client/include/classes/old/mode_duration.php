<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class mode_duration extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_course_mode()
	{
		
		$d1 = array('rcmname'=>$_POST['rcmname']);
	    //$d1 = array('rcmname'=>' course');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save course mode
	public function save_course_mode()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_course_mode();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
	 	 $q = "INSERT INTO `ref_course_mode` (`rcmId`, `rcmname` ) VALUES (NULL , '$d1[rcmname]')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'myreason'=>'table error');
		$rmid = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		
		return array('status'=>true, 'myreason'=>'Mode successfully Inserted');
	}
	
		
	
	
	
	// This function will edit mode 
	public function edit_course_mode($id='')
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $qc) = $this->get_course_mode();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `ref_course_mode` SET `rcmname` = '$qc[rcmname]' WHERE rcmId = '$id'";
	    $r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'course mode item table error');}
		mysqli_commit($dbc);
		//history_log($dbc, 'Edit', 'QC updated with QC no '.$qc['qcno']);
		return array('status'=>true, 'myreason'=>'course mode successfully updated');
	}
	
	
	
	//This function will list the course mode
	public function course_mode_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM ref_course_mode $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{			
			$id = $row['rcmId'];		
			$out[$id]['rcmId'] = $row['rcmId'];
			$out[$id]['rcmname'] =$row['rcmname'];
			$out[$id]['locked'] =$row['locked'];
		}
		return $out;
	}
	
	//// FUNCTION FOR COURSE DURATIOM
	public function get_course_duration()
	{
		
		//$d1 = array('rcmname'=>$_POST['rcmname'],'locked'=>$_POST['locked']);
	    $d1 = array('rduvalue'=>$_POST['rduvalue'],'rdutype'=>$_POST['rdutype']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save course duration
	public function save_course_duration()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_course_duration();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$a=$d1['rduvalue'];
		$b=$d1['rdutype'];
		 if($b==1)
			$uidname='Hour';
			if( $b==2)
			$uidname='Day';
			if( $b==3)
			$uidname='Week';
			if( $b==4)
			$uidname='Month';
			if( $b==5)
			$uidname='Year';
		$con= $a.' '.$uidname;
		 $q = "INSERT INTO `ref_duration` (`rduId`, `rduname`,`rduvalue`,`rdutype` ) VALUES (NULL , '$con','$d1[rduvalue]','$d1[rdutype]')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'myreason'=>'table error');
		$rmid = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		
		return array('status'=>true, 'myreason'=>' Course Duration successfully Inserted');
	}
	// This function will edit mode 
	public function edit_course_duration($id='')
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $qc) = $this->get_course_duration();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$a=$qc['rduvalue'];
		$b=$qc['rdutype'];
		 if($b==1)
			$uidname='Hour';
			if( $b==2)
			$uidname='Day';
			if( $b==3)
			$uidname='Week';
			if( $b==4)
			$uidname='Month';
			if( $b==5)
			$uidname='Year';
		$con= $a.'  '.$uidname;
		 $q = "UPDATE `ref_duration` SET `rduname` = '$con',`rduvalue`='$qc[rduvalue]',`rdutype`='$qc[rdutype]' WHERE rduId = '$id'";
	    $r1 = mysqli_query($dbc, $q);
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'course duration item table error');}
		mysqli_commit($dbc);
		//history_log($dbc, 'Edit', 'QC updated with QC no '.$qc['qcno']);
		return array('status'=>true, 'myreason'=>'course Duration successfully updated');
	}
	//This function will list the course duration
	public function course_duration_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM ref_duration $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			
			$id = $row['rduId'];
		    $out[$id]['rduname'] = $row['rduname'];
			$out[$id]['rduvalue'] =$row['rduvalue'];
			$out[$id]['rdutype'] = $row['rdutype'];
			$out[$id]['locked'] =$row['locked'];
			$out[$id]['rduId'] =$id;
		}
		return $out;
	}
	
}
?>