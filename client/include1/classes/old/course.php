<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class course extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct(); 
	}
	public function get_course()
	{
		
		$d1 = array('cname'=>$_POST['cname'], 'forstudent'=>$_POST['forstudent'],'cdtId'=>$_POST['cdtId']);
		//$d1 = array('statename'=>'BIhar');
		//$d1 = array('rclename'=>'PGSt');
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_course()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course` (`cId`, `cname`, `forstudent`,`cdtId`) VALUES (NULL , '$d1[cname]', '$d1[forstudent]','$d1[cdtId]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Course table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Course added with Location State '.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Locality successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course` SET `cname` = '$d1[cname]', `forstudent`='$d1[forstudent]',`cdtId`='$_POST[cdtId]' WHERE cId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course updated with Location State'.$d1['cname']);
		return array('status'=>true, 'reason'=>'Course successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "select c.*,cdId,cdname,cdtId,cdt_name from course as c inner join course_domain_type using(cdtId) inner join course_domain using(cdId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cId'];
			$out[$id]['cId'] = $id; // storing the item id 
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['cdtId'] = $row['cdtId'];
			$out[$id]['cdId'] = $row['cdId'];
			$out[$id]['cdname'] = $row['cdname'];
			$out[$id]['cdt_name'] = $row['cdt_name'];
			
			$out[$id]['forstudent'] = $row['forstudent'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_course_branch()
	{
		
		$d1 = array('cId'=>$_POST['cId'], 'cbname'=>$_POST['cbname']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user course_branch  
	public function save_course_branch()
	{
		
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
		if(!$r) return array('status'=>false, 'reason'=>'Course branch table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Course branch added'.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Course branch successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course_branch($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course_branch` SET `cId` = '$d1[cId]', `cbname`='$d1[cbname]' WHERE cbId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course branch updated '.$d1['cbname']);
		return array('status'=>true, 'reason'=>'Course branch successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_bracnh_list($filter='',  $records = '', $orderby='')
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
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_course_branch_university()
	{
		
		$d1 = array('cbId'=>$_POST['cbId'], 'unId'=>$_POST['unId']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_course_branch_university()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch_university();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course_branch_university` (`cbuId`, `cbId`,`unId`) VALUES (NULL , '$d1[cbId]', '$d1[unId]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Course branch university table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Course branch university added'.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Course branch university successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course_branch_university($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch_university();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		 $q = "UPDATE `course_branch_university` SET `cbId` = '$d1[cbId]', `unId`='$d1[unId]' WHERE cbuId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course branch university updated '.$id);
		return array('status'=>true, 'reason'=>'Course branch university successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_branch_university_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q="SELECT * FROM course_branch_university INNER JOIN course_branch USING(cbId) INNER JOIN university USING(unId) inner join course using(cId) $filterstr";
		//$q = "SELECT * FROM course_branch_university INNER JOIN course_branch USING(cbId) INNER JOIN university USING(unId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbuId'];
			$out[$id]['cbuId'] = $id; // storing the item id
			$out[$id]['cbId'] = $row['cbId'];
			$out[$id]['unId'] = $row['unId'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['un_name'] = $row['un_name'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function get_course_branch_university_var()
	{
		
		$d1 = array('cbuId'=>$_POST['cbuId'], 'rduId'=>$_POST['rduId'], 'rcmId'=>$_POST['rcmId'], 'rcleId'=>$_POST['rcleId'], 'fess'=>$_POST['fess'], 'course_alias_uni'=>$_POST['course_alias_uni']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_course_branch_university_var()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch_university_var();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$str = '';
		$len = count($_POST['rduId']);
		for($i = 0; $i < $len ; $i++)
		{
			$cbuId = $d1['cbuId'][$i];
			$rduId = $d1['rduId'][$i];
			$rcmId = $d1['rcmId'][$i];
			$rcleId = $d1['rcleId'][$i];
			$fess = $d1['fess'][$i];
			$course_alias_uni = $d1['course_alias_uni'][$i];
			$cbuvkey = $cbuId.$rduId.$rcmId.$rcleId;
			$q1 = "SELECT * FROM course_branch_university_var WHERE cbuvkey ='$cbuvkey'"; 
			list($opt, $rs) = run_query($dbc, $q1, $mode='single', $msg='');
			if($opt) return array('status'=>true, 'reason'=>'Course branch university varation alerdy exist');
			$str.= "(NULL, '$cbuId', '$rduId', '$rcmId', '$rcleId', '$fess', '$course_alias_uni','$cbuvkey'),";
		}
	    $str = rtrim($str , ',');
		$q = "INSERT INTO `course_branch_university_var` (`cbuvId`, `cbuId`,`rduId`, `rcmId`,`rcleId`,`fess`,`course_alias_uni`,`cbuvkey`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'reason'=>'Course branch university varation table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Course branch university added'.$rId);
		//updating the pr_item
		//$this->pr_item_update_on_pocreate($poid, $vid=$po['isvId']);
		return array('status'=>true, 'reason'=>'Course branch university successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course_branch_university_var($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_branch_university_var();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		$cbuId = $d1['cbuId'];
		$rduId = $d1['rduId'];
		$rcmId = $d1['rcmId'];
		$rcleId = $d1['rcleId'];
		$fess = $d1['fess'];
		$course_alias_uni = $d1['course_alias_uni'];
		$cbuvkey = $cbuId.$rduId.$rcmId.$rcleId;
		$q1 = "SELECT * FROM course_branch_university_var WHERE cbuvkey ='$cbuvkey'"; 
		list($opt, $rs) = run_query($dbc, $q1, $mode='single', $msg='');
		if($opt) return array('status'=>true, 'reason'=>'Course branch university varation alerdy exist');
	
		$qry = "UPDATE course_branch_university_var SET cbuId = '$cbuId', rduId = '$rduId', rcmId = '$rcmId', rcleId = '$rcleId', cbuvkey = '$cbuvkey' WHERE  cbuvId = $id";
		$r1 = mysqli_query($dbc,$qry);
		/*$qd = "DELETE FROM course_branch_university_var WHERE cbuId = $id";
		$r1 = mysqli_query($dbc,$qd);
		$str = '';
		$len = count($_POST['cbuId']);
		for($i = 0; $i < $len ; $i++)
		{
			$cbuId = $d1['cbuId'][$i];
			$rduId = $d1['rduId'][$i];
			$rcmId = $d1['rcmId'][$i];
			$rcleId = $d1['rcleId'][$i];
			$fess = $d1['fess'][$i];
			$course_alias_uni = $d1['course_alias_uni'][$i];
			$str.= "(NULL, '$cbuId', '$rduId', '$rcmId', '$rcleId', '$fess', '$course_alias_uni'),";
		}
		$str = rtrim($str , ',');
		$q = "INSERT INTO `course_branch_university_var` (`cbuvId`, `cbuId`,`rduId`, `rcmId`,`rcleId`,`fess`,`course_alias_uni`) VALUES $str";
		$r = mysqli_query($dbc,$q);*/
		if(!$r1) return array('status'=>false, 'reason'=>'Course branch university varation table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course branch university updated '.$id);
		return array('status'=>true, 'reason'=>'Course branch university successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_branch_university_var_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
	    $q = "SELECT *,CONCAT_WS('', cname , ' (',cbname,')') AS cuorse_branch FROM `course_branch_university_var` INNER JOIN  course_branch_university USING(cbuId) INNER JOIN ref_duration USING(rduId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN ref_course_level USING(rcleId) INNER JOIN course_branch USING(cbId) INNER JOIN university USING(unId) INNER JOIN course USING(cId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{   
			$id = $row['cbuvId'];
			$out[$id]['cbuvId'] = $id; // storing the item id
			$out[$id]['cbuId'] = $row['cbuId'];
			$out[$id]['cuorse_branch'] = $row['cuorse_branch'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id]['unId'] = $row['unId'];
			$out[$id]['un_name'] = $row['un_name'];
			$out[$id]['rcmId'] = $row['rcmId'];
			$out[$id]['rcmname'] = $row['rcmname'];
			$out[$id]['rduId'] = $row['rduId'];
			$out[$id]['rduname'] = $row['rduname'];
			$out[$id]['rcleId'] = $row['rcleId'];
			$out[$id]['rclename'] = $row['rclename'];
			$out[$id]['fess'] = $row['fess'];
			$out[$id]['course_alias_uni'] = $row['course_alias_uni'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	// this function used to add content type
	public function get_course_domain_type()
	{
		
		$d1 = array('cdt_name'=>$_POST['cdt_name'], 'cdId'=>$_POST['cdId'], 'sortorder'=>$_POST['sortorder']);
		$d1['myreason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	
	// This function will save the user qc  
	public function save_course_domain_type()
	{
		//echo 'hiii';
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_domain_type();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course_domain_type` (`cdtId`,`cdId`, `cdt_name`, `sortorder`) VALUES (NULL ,'$d1[cdId]','$d1[cdt_name]', '$d1[sortorder]')";
		$r = mysqli_query($dbc,$q);
		$rId = mysqli_insert_id($dbc);
		if(!$r) return array('status'=>false, 'myreason'=>'Course table error');
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Add course domain type'.$rId);
		return array('status'=>true, 'myreason'=>'Course domain type successfully saved');
	}
	
	// This function will save the user pr  
	public function edit_course_domain_type($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_course_domain_type();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course_domain_type` SET `cdt_name` = '$d1[cdt_name]', `sortorder`='$d1[sortorder]' WHERE cdtId = '$id'";
		$r = mysqli_query($dbc,$q);
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Course domain type'.$id);
		return array('status'=>true, 'myreason'=>'Course domain type successfully updated');
	}
	//This function will return the list of pr based on the filter condition applied
	public function get_course_domain_type_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_domain_type INNER JOIN  course_domain USING(cdId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cdtId'];
			$out[$id]['cdtId'] = $id; // storing the item id
			$out[$id]['cdt_name'] = $row['cdt_name'];
			$out[$id]['cdname'] = $row['cdname'];
			$out[$id]['cdId'] = $row['cdId'];
			$out[$id]['sortorder'] = $row['sortorder'];
			$out[$id]['locked'] = $row['locked'];
		}
		return $out;
	}
	public function count_course_variation($cbuvId)
	{
		global $dbc;
		$out=array();
		$q="SELECT COUNT(cbuvId) as cbuvId FROM course_branch_university_var WHERE cbuId='$cbuvId'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		return $rs['cbuvId'];
	}
	public function get_course_domain_detail($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_domain INNER JOIN course_domain_type USING(cdId) INNER JOIN course USING(cdtId) INNER JOIN course_branch USING(cId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cdId'];
			$cId = $row['cId'];
			$cbId = $row['cbId'];
			$out[$id]['cdId'] = $id; // storing the item id 
			$out[$id]['cdname'] = $row['cdname'];
			$out[$id]['cdt_name'] = $row['cdt_name'];
			$out[$id]['cname'][$cId] = $row['cname'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id][$cId][$cbId] = $row['cbname'];
		}
		return $out;
	}
	public function get_course_branch_detail($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_domain INNER JOIN course_domain_type USING(cdId) INNER JOIN course USING(cdtId) INNER JOIN course_branch USING(cId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbId'];
			$cbId = $row['cbId'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id]['cbId'] = $row['cbId'];
		}
		return $out;
	}
	/*public function get_course($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course";
	}*/
	public function get_branch_duration($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
    	 $q = "SELECT * FROM course INNER JOIN course_branch USING(cId) INNER JOIN course_branch_university USING(cbId) INNER JOIN course_branch_university_var USING(cbuId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN ref_duration USING(rduId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['rduId'];
			$cbId = $row['cbId'];
			$out[$id]['rduId'] = $row['rduId'];
			$out[$id]['rcmId'] = $row['rcmId'];
			$out[$id]['rduname'] = $row['rduname'];
			$out[$id]['rcmname'] = $row['rcmname'];
		}
		return $out;
	}
	public function get_cities_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course INNER JOIN course_branch USING(cId) INNER JOIN course_branch_university USING(cbId) INNER JOIN course_branch_university_var USING(cbuId) INNER JOIN course_branch_college USING(cbuvId) INNER JOIN college USING(colgId) INNER JOIN loc_city_district USING(cityId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cityId'];
			$cbId = $row['cbId'];
			$out[$id]['cityId'] = $row['cityId'];
			$out[$id]['city_name'] = $row['city_name'];
		}
		return $out;
	}
}
?>