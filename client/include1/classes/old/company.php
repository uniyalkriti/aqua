<?php
class company extends myfilter
{
	private static $cid = NULL;
	private static $cdetails = NULL;
	
	function __construct()
	{
	
	}
	
	public function get_company_se_data()
	{  
		$d1 = array('cmp_name'=>$_POST['cmp_name'], 'adr_line1'=>$_POST['adr_line1'], 'adr_line2'=>$_POST['adr_line2'], 'mobile'=>$_POST['mobile'], 'tin_no'=>$_POST['tin_no'], 'deals_in'=>$_POST['deals_in'], 'cmp_email'=>$_POST['cmp_email'], 'website'=>$_POST['website'],'phone'=>$_POST['phone'], 'fax_no'=>$_POST['fax_no'], 'contact_person'=>$_POST['contact_person'], 'lab_status'=>$_POST['lab_status'], 'header_image'=>'', 'footer_image'=>'','email'=>$_POST['email'],'locality'=>$_POST['locality'],'landmark'=>$_POST['landmark'],'city_district'=>$_POST['city_district'],'state'=>$_POST['state'],'pincode'=>$_POST['pincode'],'secondary_phone'=>$_POST['secondary_phone'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Part'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function company_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_company_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `company` (`cmpId`, `cmp_name`, `adr_line1`, `adr_line2`, `mobile`, `tin_no`,`deals_in`,`cmp_email`, `phone`, `fax_no`,`contact_person`,`lab_status`, `header_image`, `footer_image`,`email`,`locality`,`city_district`,`landmark`,`state`,`secondary_phone`) VALUES (NULL, '$d1[cmp_name]', '$d1[adr_line1]', '$d1[adr_line2]','$d1[mobile]','$d1[tin_no]','$d1[deals_in]','$d1[cmp_email]','$d1[phone]','$d1[fax_no]','$d1[contact_person]','$d1[lab_status]', '$d1[header_image]', '$d1[header_image]','$d1[email]','$d1[locality]','$d1[city_district]','$d1[landmark]','$d1[state]','$d1[secondary_phone]')";
		//$q = "INSERT INTO `party` (`partyId`, `ptId`,`party_name`, `username`,`pass`,`group_code`,`division`,`website`,`phone`,`tin_no`,`fax_no`,`ecc_no`,`certificate_sign`,`adr_line1`,`adr_line2`,`locality`,`landmark`,`city_district`,`state`,`pincode`,`country`,`crId`,`created`,`blacklist`,`discount`) VALUES (NULL , '$d1[ptId]', '$d1[party_name]', '$d1[username]','$d1[pass]', '$d1[group_code]', '$d1[division]', '$d1[website]', '$d1[phone]', '$d1[tin_no]', '$d1[fax_no]', '$d1[ecc_no]', '$d1[certificate_sign]','$d1[adr_line1]', '$d1[adr_line2]', '$d1[locality]','$d1[landmark]', '$d1[city_district]', '$d1[state]','$d1[pincode]', '$d1[country]', $d1[uid],NOW(),0,'$d1[discount]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Company table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function company_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_company_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_company_list("cmpId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `company` SET  `cmp_name` = '$d1[cmp_name]',`mobile` = '$d1[mobile]',`tin_no` = '$d1[tin_no]',`deals_in` = '$d1[deals_in]',`cmp_email` = '$d1[cmp_email]',`website` = '$d1[website]',`phone` = '$d1[phone]',`fax_no` = '$d1[fax_no]',`contact_person` = '$d1[contact_person]',`lab_status` = '$d1[lab_status]',`adr_line1` = '$d1[adr_line1]',`adr_line2` = '$d1[adr_line2]',`adr_line2` = '$d1[adr_line2]',`locality` = '$d1[locality]',`landmark` = '$d1[landmark]',`city_district` = '$d1[city_district]',`state` = '$d1[state]',`pincode` = '$d1[pincode]', `email` = '$d1[email]', `secondary_phone` = '$d1[secondary_phone]',`modified`= NOW() WHERE cmpId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_category table error');}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
		
	public function get_company_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'%e/%b/%Y') AS fdated, DATE_FORMAT(modified,'%e/%b/%Y') AS flastedit FROM company $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cmpId'];
			$out[$id] = $row; // storing the item id
		}
		//pre($out);
		return $out;
	} 
	public function get_department_se_data()
	{  
		$d1 = array('dept_name'=>$_POST['dept_name'], 'dept_code'=>$_POST['dept_code'],'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Department'; //whether to do history log or not
		return array(true,$d1);
	}
	public function department_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_department_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `department` (`deptId`, `dept_name`, `dept_code`,`crId`,`created`) VALUES (NULL, '$d1[dept_name]', '$d1[dept_code]','$d1[uid]',NOW())";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Department table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' Department Saved', 'rId'=>$rId);
	}
	
	public function department_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_department_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_department_list("deptId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `department` SET  `dept_name` = '$d1[dept_name]',`dept_code` = '$d1[dept_code]', `mrId` = '$d1[uid]',`modified`= NOW() WHERE deptId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'depaertment table error');}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
		
	public function get_department_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'%e/%b/%Y') AS fdated, DATE_FORMAT(modified,'%e/%b/%Y') AS flastedit FROM department $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['deptId'];
			$out[$id] = $row; // storing the item id
		}
		//pre($out);
		return $out;
	} 
}
?>


