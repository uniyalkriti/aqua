<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class colleges_course extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function get_se_data()
	{
		$d1 = array('cbuvId'=>$_POST['cbuvId'],'colgId'=>$_POST['colgId'],'course_alias'=>$_POST['course_alias'],'fees'=>$_POST['fees']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function college_course_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `course_branch_college` (`cbcId`, `cbuvId`, `colgId`,`course_alias`,`fees`) VALUES (NULL ,'$d1[cbuvId]', '$d1[colgId]', '$d1[course_alias]','$d1[fees]')";
		$r = mysqli_query($dbc,$q);
	//	$q1 = "INSERT INTO `course_branch_university` ";
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'College Course successfully saved', 'rId'=>$rId);
	}
	public function college_course_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `course_branch_college` SET `cbuvId` = '$d1[cbuvId]', `colgId` = '$d1[colgId]', `course_alias`='$d1[course_alias]', `fees`='$d1[fees]' WHERE cbcId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'College Course table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'College Course Updated With '.$id);
		return array('status'=>true, 'reason'=>'College Course  successfully updated');
	}
	
	public function get_college_course_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//$q = "SELECT c.*,localityname,city_name,statename,countryname FROM college AS c  INNER JOIN loc_locality USING(localityId)INNER JOIN ref_aima_rating AS rar USING(rarId) INNER JOIN loc_city_district AS lcd ON c.cityId=lcd.cityId INNER JOIN loc_state AS lc ON lc.stateId=c.stateId INNER JOIN loc_country ON c.countryId=loc_country.loc_countryId INNER JOIN ref_aima_rating USING(rarId) $filterstr ";
		//INNER JOIN ref_duration USING(rduId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN ref_course_level USING(rcleId)
		$q = "SELECT cbc.*,cbc.fees AS ff,CONCAT_WS('-',cname,cb.cbname) AS cc,un_name,CONCAT_WS(' -',rcmname,rclename,rduname) AS rrr,cb.cbId as cbId FROM  course_branch_college AS cbc  INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN ref_duration USING(rduId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN ref_course_level USING(rcleId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN university USING(unId) INNER JOIN course_branch AS cb USING(cbId) INNER JOIN course USING(cId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbcId'];
			$out[$id]['cbcId'] = $id; // storing the item id
			$out[$id]['cbname'] = $row['cc'];
			$out[$id]['cbId'] = $row['cbId'];
			$out[$id]['un_name'] = $row['un_name'];
			$out[$id]['ff'] = $row['ff'];
			$out[$id]['course_alias'] = $row['course_alias'];
			$out[$id]['rrr'] = $row['rrr'];
		}
		return $out;
	}
	public function set_college_course_edit($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//$q = "SELECT c.*,localityname,city_name,statename,countryname FROM college AS c  INNER JOIN loc_locality USING(localityId)INNER JOIN ref_aima_rating AS rar USING(rarId) INNER JOIN loc_city_district AS lcd ON c.cityId=lcd.cityId INNER JOIN loc_state AS lc ON lc.stateId=c.stateId INNER JOIN loc_country ON c.countryId=loc_country.loc_countryId INNER JOIN ref_aima_rating USING(rarId) $filterstr ";
		//INNER JOIN ref_duration USING(rduId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN ref_course_level USING(rcleId)
		$q = "SELECT cbc.*,cbc.fees AS ff,cb.cbId,unId,cbuId FROM  course_branch_college AS cbc INNER JOIN course_branch_university_var USING(cbuvId)  INNER JOIN course_branch_university USING(cbuId) INNER JOIN university USING(unId) INNER JOIN course_branch AS cb USING(cbId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbcId'];
			$out[$id]['cbcId'] = $id; // storing the item id
			$out[$id]['cbId'] = $row['cbId'];
			$out[$id]['cbuvId'] = $row['cbuvId'];
			$out[$id]['colgId'] = $row['colgId'];
			$out[$id]['cbuId'] = $row['cbuId'];
			$out[$id]['fees'] = $row['ff'];
			$out[$id]['course_alias'] = $row['course_alias'];
			
		}
		return $out;
	}
	public function get_college_course_info_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//$q = "SELECT * FROM info_label LEFT JOIN college_lable_detail USING(ilaId) $filterstr";
		$q = "SELECT * FROM course_branch_college $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbcId'];
			$out[$id]['cbcId'] = $id; // storing the item id
			$out[$id]['cbuvId'] = $row['cbuvId'];
			$out[$id]['colgId'] = $row['colgId'];
			$out[$id]['course_alias'] = $row['course_alias'];
			$out[$id]['fees'] = $row['fees'];
		}
		return $out;
	}
}
?>