<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class colleges extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	//To read the why join details for a given college  
	public function whyJoinCollege($action='save',$id=NULL, $whyjoin=array())
	{
		global $dbc;
		$out = array('status'=>false, 'reason'=>'');
		list($opt,$rs) = run_query($dbc, "SELECT whyjoin FROM college WHERE colgId = $id", 'single');
		if(!$opt) return array('status'=>false, 'reason'=>'No such college found');
		switch($action){
			case'save':
			{
				if(!is_array($whyjoin)) die(__FUNCTION__.' expect third parameter to be array');
				//So as not to save blank why join
				foreach($whyjoin as $key=>$value){
					 if(empty($value)) unset($whyjoin[$key]);
				}
				$savestr = implode('<$>', $whyjoin);
				mysqli_query($dbc,"UPDATE college SET whyjoin = '$savestr' WHERE colgId = $id");			
				return array('status'=>true, 'reason'=>'Why Join Information successfully updated');
				break;	
			}
			case'read':
			{
				$savestr = explode('<$>', $rs['whyjoin']);			
				return array('status'=>true, 'reason'=>$savestr);
				break;	
			}		
		}
		return $out;
	}
	
	public function get_se_data()
	{   // $img=$_FILES['logo']['name'];
	
		$d1 = array('rarId'=>$_POST['rarId'],'colg_name'=>($_POST['colg_name']),'localityId'=>($_POST['localityId']),'cityId'=>($_POST['cityId']),'stateId'=>($_POST['stateId']),'countryId'=>($_POST['loc_countryId']),'website'=>($_POST['website']));
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function college_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//file upload here
		$upload_path = MYUPLOADS.MSYM.'logo';
		list($uploadstat, $filename) = fileupload('logo', $upload_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
		if($uploadstat) 
		{
			resizeimage($filename, $upload_path, $newwidth=800, $thumbnailwidth=100, MSYM, $thumbnail = true);			
				/*$this->unlink_user_files($filename, $upload_path); //Deleting the user uploaded file
				return array('status'=>false, 'myreason'=>'Image could not be uploaded, please try again');*/
		}
		//exit();
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `college` (`colgId`, `rarId`, `colg_name`,`localityId`,`cityId`,`stateId`,`countryId`,`website`,`logo`) VALUES (NULL , '$d1[rarId]', '$d1[colg_name]', '$d1[localityId]', '$d1[cityId]', '$d1[stateId]', '$d1[countryId]', '$d1[website]', '$filename')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'College successfully saved', 'rId'=>$rId);
	}
	public function college_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		$log = '';
		$upload_path = MYUPLOADS.MSYM.'logo';
		list($uploadstat, $filename) = fileupload('logo', $upload_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);		if(!empty($filename))
		{
		if($uploadstat) 
			{
				resizeimage($filename, $upload_path, $newwidth=800, $thumbnailwidth=100, MSYM, $thumbnail = true);			
					/*$this->unlink_user_files($filename, $upload_path); //Deleting the user uploaded file
					return array('status'=>false, 'myreason'=>'Image could not be uploaded, please try again');*/
				 	$log= ",logo = '$filename'";
					if(!empty($_POST['old_logo']))
					{
						$path = "'./myuploads/logo/$_POST[old_logo]'";
						//unlink($path);
					}
			}
		}
		else
		{
			$log = '';
		}
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		
		$q = "UPDATE `college` SET `rarId` = '$d1[rarId]', `colg_name` = '$d1[colg_name]', `localityId`='$d1[localityId]', `cityId`='$d1[cityId]',countryId='$d1[countryId]',stateId='$d1[stateId]',website='$d1[website]' $log WHERE colgId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'College Updated With '.$id);
		return array('status'=>true, 'reason'=>'College  successfully updated');
	}
	
	public function get_college_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		 $q = "SELECT c.*,localityname,city_name,statename,countryname,cbcId,website,logo,rar.rarname as rarname FROM college AS c  INNER JOIN loc_locality USING(localityId)INNER JOIN ref_aima_rating AS rar USING(rarId) INNER JOIN loc_city_district AS lcd ON c.cityId=lcd.cityId INNER JOIN loc_state AS lc ON lc.stateId=c.stateId INNER JOIN loc_country ON c.countryId=loc_country.loc_countryId INNER JOIN ref_aima_rating USING(rarId) LEFT JOIN course_branch_college USING(colgId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['colgId'];
			$out[$id]['colgId'] = $id; // storing the item id
			$out[$id]['rarId'] = $row['rarId'];
			$out[$id]['loc_countryId'] = $row['countryId'];
			$out[$id]['stateId'] = $row['stateId'];
			$out[$id]['localityId'] = $row['localityId'];
			$out[$id]['cityId'] = $row['cityId'];
			$out[$id]['colg_name'] = $row['colg_name'];
			$out[$id]['localityname'] = $row['localityname'];
			$out[$id]['city_name'] = $row['city_name'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['rarname'] = $row['rarname'];
			$out[$id]['website'] = $row['website'];
			$out[$id]['logo'] = $row['logo'];
			$out[$id]['cbcId'] = $row['cbcId'];
		}
		return $out;
	} 
	public function get_college_course_university($cbcid)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		//$filterstr = $this->oo_filter($filter, $records, $orderby);
		
	  //$q="SELECT * FROM course_branch_college INNER JOIN  college USING(colgId) INNER JOIN university USING(rarId) WHERE cbcId='$cbcid' LIMIT 1";
	  $q="SELECT * FROM course_branch_college INNER JOIN  course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId)INNER JOIN university using(unId) WHERE cbcId='$cbcid' LIMIT 1";
		
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		/*while($row = mysqli_fetch_assoc($rs))
		{
		$id=$row['cbcId'];	
			
		$out[$id]['un_name'] = $row['un_name']; 
		$out[$id]['fees'] = $row['fees'];
			
		}*/
		$uname=$rs['un_name'];
		$fees=$rs['fees'];
		$course=$rs['course_alias'];
		$arr=array('course_affiliation'=>$uname,'course_fee'=>$fees,'course_name'=>$course);
		return $arr;
	} 
	
	//This function will return an image filename of the college id being sent
	public function frontcollegeimg($id)
	{
		global $dbc;
		$out = NULL;
		$q = " SELECT filename FROM college_album INNER JOIN college_album_pics USING(caId) WHERE colgId='$id' AND album_type='1' LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; 
		return $rs['filename'];
	}
	
	public function get_college_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT colg_name FROM college $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		return $rs['colg_name'];
	}
	public function get_college_info_list($id)
	{ 
		global $dbc;
		$out = array('status'=>false,'myreason'=>'');		
		// if user has send some filter use them.
		//$filterstr = $this->oo_filter($filter, $records, $orderby);		
		$info_label = new info_label();
		$all_label = $info_label->get_label_list("ila_type=2");
		//pre($all_label);
		if(!empty($all_label)){
			//getting the details of info label already associated to this college
			
			list($opt, $rs) = run_query($dbc, "SELECT * FROM college_lable_detail WHERE colgId = $id", $mode='multi');
			//if this college has already some labels attached fetch them
			$colg_existing_label = array();
			if($opt){
				while($row = mysqli_fetch_assoc($rs)){
					$colg_existing_lable[$row['ilaId']]['cldId'] = 	$row['cldId'];
					$colg_existing_lable[$row['ilaId']]['labelinfo'] = 	$row['labelinfo'];
				}	//while end here			
			}//if($opt){
			$final_label = array();
			foreach($all_label as $key=>$value){
				$final_label[$key]['cldId'] = '';
				$final_label[$key]['labelinfo'] = '';
				$final_label[$key]['ila_name'] = $value['ila_name'];
				
				if(isset($colg_existing_lable[$key])){ // if this label has info associate it also used upper value
					$final_label[$key]['cldId'] = $colg_existing_lable[$key]['cldId'];
					$final_label[$key]['labelinfo'] = $colg_existing_lable[$key]['labelinfo'];
					//$final_label[$key]['ila_name'] = $colg_existing_lable[$key]['ila_name'];
				}//if(isset($colg_existing_lable[$key])) ends
			}//foreach($all_label as $key=>$value){			
			$out = array('status'=>true,'myreason'=>$final_label);		
		} //if(!empty($all_label)){ ends
		 else
			$out['myreason'] = 'Pleas define atleast 1 info lable for the college';
		return $out;
	}	
	public function get_college_course_info($id)
	{
		global $dbc;
		$out = array('status'=>false,'myreason'=>'');		
		// if user has send some filter use them.
		//$filterstr = $this->oo_filter($filter, $records, $orderby);		
		$info_label = new info_label();
		$all_label = $info_label->get_label_list("ila_type=1");
		
		if(!empty($all_label)){
			//getting the details of info label already associated to this college
		   
			list($opt, $rs) = run_query($dbc, "SELECT * FROM course_lable_detail INNER JOIN course_branch_college USING(cbcId) WHERE cbcId = $id", $mode='multi');
			//  "SELECT * FROM course_lable_detail INNER JOIN course_branch_college USING(cbcId) WHERE cbcId = $id";
			//if this college has already some labels attached fetch them
			$colg_existing_label = array();
			if($opt){
				while($row = mysqli_fetch_assoc($rs)){
					$colg_existing_lable[$row['ilaId']]['couldId'] = 	$row['couldId'];
					$colg_existing_lable[$row['ilaId']]['labelinfo'] = 	$row['labelinfo'];
				}	//while end here			
			}//if($opt){
			
			$final_label = array();
			foreach($all_label as $key=>$value){
				$final_label[$key]['couldId'] = '';
				$final_label[$key]['labelinfo'] = '';
				$final_label[$key]['ila_name'] = $value['ila_name'];
				
				if(isset($colg_existing_lable[$key])){ // if this label has info associate it also used upper value
					$final_label[$key]['couldId'] = $colg_existing_lable[$key]['couldId'];
					$final_label[$key]['labelinfo'] = $colg_existing_lable[$key]['labelinfo'];
					//$final_label[$key]['ila_name'] = $colg_existing_lable[$key]['ila_name'];
				}//if(isset($colg_existing_lable[$key])) ends
			}//foreach($all_label as $key=>$value){			
			$out = array('status'=>true,'myreason'=>$final_label);		
		} //if(!empty($all_label)){ ends
		 else
		    //$out = array('status'=>false,'myreason'=>'Pleas define atleast 1 info lable for the college');	
			$out['myreason'] = 'Pleas define atleast 1 info lable for the college course';
		return $out;
	}
	public function get_college_course_attach_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM course_branch_college  $filterstr ";
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
	public function college_label_details_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		$cldId = $_POST['cldId'];
		$ilaId = $_POST['ilaId'];
		$colgId = $_POST['eid'];
	//	$labelinfo = $_POST['labelinfo'];
		$reason = $_POST['reason'];
		$str = '';
		$i=0;
		foreach($cldId as $key=>$value)
		{
			$ilaId = $_POST['ilaId'][$i];
		//	$labelinfo=$_POST['labelinfo'][$i];
			$reason=$_POST['reason'][$i];
			$cldId=$_POST['cldId'][$i];
			if(empty($value))
			{
			
				$q = "INSERT INTO college_lable_detail(`cldId`,`ilaId`,`colgId`,`labelinfo`,`reason`) VALUES(NULL,'$ilaId','$colgId','$labelinfo',$reason)";
				$r = mysqli_query($dbc,$q);
				//$str.= '(NULL,\''.$_POST['ilaId'][$i].'\',\''.$colgId.'\',\''.$labelinfo.'\'),';
			}
			else
		/*	{
				$q = "UPDATE college_lable_detail SET ilaId='$ilaId',labelinfo='$labelinfo' WHERE cldId='$cldId'";
				$r = mysqli_query($dbc,$q);
			}*/
			$i++;
		}
		//start the transaction
		if($i==0) return array('status'=>false, 'reason'=>'table error');
		//$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'College label Details successfully saved');
	}
// This function used to save college course details
public function college_course_details_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		$couldId = $_POST['couldId'];
		$ilaId = $_POST['ilaId'];
		$cbcId = $_POST['eid'];
		$labelinfo = $_POST['labelinfo'];
		$str = '';
		$i=0;
		foreach($couldId as $key=>$value)
		{
			$ilaId = $_POST['ilaId'][$i];
			$labelinfo=$_POST['labelinfo'][$i];
			$couldId=$_POST['couldId'][$i];
			if(empty($value))
			{
				$q = "INSERT INTO course_lable_detail(`couldId`,`ilaId`,`cbcId`,`labelinfo`) VALUES(NULL,'$ilaId','$cbcId','$labelinfo')";
				$r = mysqli_query($dbc,$q);
				//$str.= '(NULL,\''.$_POST['ilaId'][$i].'\',\''.$colgId.'\',\''.$labelinfo.'\'),';
			}
			else
			{
				$q = "UPDATE course_lable_detail SET ilaId='$ilaId',labelinfo='$labelinfo' WHERE couldId='$couldId'";
				$r = mysqli_query($dbc,$q);
			}
			$i++;
		}
		//start the transaction
		if($i==0) return array('status'=>false, 'reason'=>'table error');
		//$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'Course label Details successfully saved');
	}
//autonomous college function8888888888888888888888888888888888888888888888888888888888888888888
public function get_autonomous_college()
	{
		$d1 = array('course_alias_uni'=>$_POST['course_alias_uni'],'fees'=>$_POST['fees'],'cbId'=>$_POST['cbId'],'cId'=>$_POST['cId'],'rduId'=>$_POST['rduId'],'rcleId'=>$_POST['rcleId'],'rcmId'=>$_POST['rcmId'],'colgId'=>$_POST['colgId'],'unId'=>$_POST['unId']);
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function autonomous_college_save()
	{
		global $dbc;	
		
		
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_autonomous_college();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		mysqli_query($dbc, "START TRANSACTION");
		//checking branch  id
		$q1="SELECT * FROM course_branch_university WHERE cbId='$d1[cbId]' AND unId='$d1[unId]'";
		list($opt, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');
		if($opt)
			{
				$cbid=$rs1['cbId'];
				$unId=$rs1['unId'];
				$cbuId=$rs1['cbuId'];
			}
			else{
					$cbid=$d1['cbId'];
					$unId=$d1['unId'];
					$q = "INSERT INTO `course_branch_university` (`cbuId`, `cbId`, `unId`) VALUES (NULL , '$cbid', '$unId')";
					$r = mysqli_query($dbc,$q);
					$ins=mysqli_insert_id($dbc);
					$cbuId=$ins;
				}
				//checking cap id
	    $q2="SELECT * FROM college_autonomous_programs WHERE colgId='$d1[colgId]' AND cap_name='$d1[course_alias_uni]'";
		list($opt, $rs1) = run_query($dbc, $q2, $mode='single', $msg='');			
		if($opt)
		 {
			 $colgid=$rs1['colgId'];
			 $cap_name=$rs1['cap_name'];
			 $capid=$rs1['capId'];
		 }
		 else{
		        $colgid=$d1['cbId'];
				$cap_name=$d1['course_alias_uni'];
				$q3 = "INSERT INTO `college_autonomous_programs` (`capId`, `colgId`, `cap_name`) VALUES (NULL , '$colgid', '$cap_name')";
		        $r = mysqli_query($dbc,$q3);
		        $capid=mysqli_insert_id($dbc);
		        $capid=$capid;
			 }
		
		 $crr="INSERT INTO  course_branch_university_var (`cbuvId`,`capId`,`cbuId`,`rduId`,`rcmId`,`rcleId`,`fess`,`course_alias_uni`)VALUES (NULL,'$capid','$cbuId','$d1[rduId]','$d1[rcmId]','$d1[rcleId]','$d1[fees]','$d1[course_alias_uni]') ";
		 $crr = mysqli_query($dbc,$crr);
	   
		 $cbuvId=mysqli_insert_id($dbc);
		
		/// insert data in course_branch_college
		$cr="INSERT INTO `course_branch_college` (`cbcId`,`cbuvId`,`colgId`,`course_alias`,`fees`)VALUES(NULL,'$cbuvId','$d1[colgId]','$d1[course_alias_uni]','$d1[fees]')";       $rrr = mysqli_query($dbc,$cr);
		if(!$rrr){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>' table error');}
		mysqli_commit($dbc);
		return array('status'=>true, 'reason'=>'Program  successfully save');
	}
  
  
  public function edit_autonomous_college($id)
	{
		
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_autonomous_college();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction 
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
	 	 $q = "UPDATE `course_branch_university_var` SET  `rduId` = '$d1[rduId]',`rcleId` = '$d1[rcleId]',`rcmId` = '$d1[rcmId]',`fess` = '$d1[fees]',`course_alias_uni` = '$d1[course_alias_uni]' WHERE cbuvId = '$id'";
		  $q2 = "UPDATE `course_branch_college` SET  `fees` = '$d1[fees]',`course_alias` = '$d1[course_alias_uni]' WHERE cbuvId = '$id'";
		$r = mysqli_query($dbc,$q);
		$r2 = mysqli_query($dbc,$q2);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', ' Autonomous  College Updated With '.$id);
		return array('status'=>true, 'reason'=>'Autonomous College  successfully updated');
	}
	
	public function get_autonomous_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "select c.*,cbname,rduname,cname,rclename,rcmname,rclename,fees,course_alias,course_alias_uni,colg_name, capId,cbuId,rduId,rcmId,rcleId,cId,unId,cbId from course_branch_college as c inner join course_branch_university_var using(cbuvId) inner join course_branch_university using(cbuId) inner join course_branch using(cbId)inner join course using(cId) inner join college using(colgId)  inner join ref_duration using(rduId) inner join ref_course_level using (rcleId) inner join ref_course_mode using(rcmId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['cbuvId'];
			$out[$id]['cbuvId'] = $id; // storing the item id rcmname colgId
			
			$out[$id]['colg_name'] = $row['colg_name'];
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id]['rduname'] = $row['rduname'];
			$out[$id]['rclename'] = $row['rclename'];
			$out[$id]['rcmname'] = $row['rcmname'];
			$out[$id]['course_alias'] = $row['course_alias'];
			$out[$id]['course_alias'] = $row['course_alias'];
			$out[$id]['course_alias_uni'] = $row['course_alias_uni'];
			$out[$id]['fees'] = $row['fees'];
			$out[$id]['capId'] = $row['capId'];
			$out[$id]['cbuId'] = $row['cbuId'];
			$out[$id]['rduId'] = $row['rduId'];
			$out[$id]['rcmId'] = $row['rcmId'];
			$out[$id]['rcleId'] = $row['rcleId'];
			$out[$id]['unId'] = $row['unId'];
			$out[$id]['cId'] = $row['cId'];
			$out[$id]['colgId'] = $row['colgId'];
			$out[$id]['cbId'] = $row['cbId'];
		}
		return $out;
	}
	/*public function get_college_bracnh_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM  college INNER JOIN course_branch_college USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN USING() $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$out[] = $row[''];
		}
	}*/
	public function get_university_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,cbc.fees as clg_fee FROM college INNER JOIN course_branch_college as cbc USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN university USING(unId) INNER JOIN course_branch USING(cbId) INNER JOIN course USING(cId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
	         $id =  $row['colgId'];
			 $cid =  $row['cbId'];
			 $out[$id]['colgId'] =  $id;
			 $out[$id]['cId'] =  $cid;
		     $out[$id]['un_name'] =  $row['un_name'];
			 $out[$id]['fees'] =  $row['clg_fee'];
			 $out[$id]['forstudent'] =  $row['forstudent'];
			 $out[$id]['cbname'] =  $row['cbname'];
			 $out[$id]['cname'] =  $row['cname'];
			 $out[$id]['cbname'][$cid] =  $row['cbname'];
			
		}
		return $out;
	}
	// function used to show the college course details
	public function get_college_course_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		 $q = "SELECT * FROM college INNER JOIN course_branch_college as cbc USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN  course_branch USING(cbId) INNER JOIN course USING(cId) INNER JOIN  university USING(rarId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			 $id = $row['colgId'];
			 $id1 = $row['cbId'];
			 $cId = $row['cId'];
			 $out[$id]['cbcId'] =  $row['cbcId'];
			 $out[$id]['colgId'] =  $row['colgId'];
			 $out[$id]['cname'] =  $row['cname'];
			 $out[$id]['cname'][$cId] =  $row['cname'];
			 $out[$id]['colg_name'] =  $row['colg_name'];
			 $out[$id]['cbname'][$id1] =  $row['cbname'];
			 $out[$id]['fees'][$id1] =  $row['fees'];
			 $out[$id]['forstudent'] =  $row['forstudent'];
			 $out[$id]['un_name'] =  $row['un_name'];
			// $out[$id]['fees'] =  $row['fees'];
		}
		return $out;
	}
	
	// function used to show the college course details
	public function get_college_course_list1($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_branch_college INNER JOIN course_branch_university_var USING(cbuvId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		// Fetching the ref_duration, ref mode and ref_course_level data
		$rduId = $this->get_my_reference_array('ref_duration', 'rduId', 'rduname');
		$rcmId = $this->get_my_reference_array('ref_course_mode', 'rcmId', 'rcmname');
		$rcleId = $this->get_my_reference_array('ref_course_level', 'rcleId', 'rclename');
		while($row = mysqli_fetch_assoc($rs))
		{
			 $id = $row['cbcId'];
			 $out[$id]['cbcId'] =  $id;
			 $out[$id]['colgId'] =  $row['colgId'];
			 $out[$id]['fees'] =  $row['fees'];
			 $out[$id]['cbuvId'] =  $row['cbuvId']; 
			 $out[$id]['cbuId'] =  $row['cbuId']; 
			 $out[$id]['capId'] =  $row['capId'];
			 $out[$id]['rduId'] =  $row['rduId'];
			 $out[$id]['rduname'] =  isset($rduId[$row['rduId']]) ? $rduId[$row['rduId']] : '';
			 $out[$id]['rcmId'] =  $row['rcmId'];
			 $out[$id]['rcmname'] =  isset($rcmId[$row['rcmId']]) ? $rcmId[$row['rcmId']] : '';
			 $out[$id]['rcleId'] =  $row['rcleId'];
			 $out[$id]['rclename'] =  isset($rcleId[$row['rcleId']]) ? $rcleId[$row['rcleId']] : '';
		}
		return $out;
		
	} 
	// This function will build the reference array for single column
	public function get_my_reference_array($tablename, $primarykey, $column, $orderby= '', $outtype = 'single')
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, "SELECT * FROM $tablename $orderby", 'multi');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row[$primarykey];
			if($outtype == 'multi')
				$out[$id][$column] = $row[$column];
			else
				$out[$id] = $row[$column];
		}
		return $out;
	}
	// This function will build the reference array for multi column
	public function get_my_reference_array_direct($q, $primarykey)
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, $q, 'multi');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row[$primarykey];
			$out[$id] = $row;
		}
		return $out;
	}
		
	
	//getting one course details
	
	public function get_one_college_course($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		  $q = "SELECT * FROM college INNER JOIN course_branch_college as cbc USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN  course_branch USING(cbId) INNER JOIN course USING(cId) INNER JOIN university USING(rarId) INNER JOIN ref_duration USING(rduId) INNER JOIN  ref_course_mode USING(rcmId)  $filterstr LIMIT 1 " ;
		
		
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			 
			 
			 //$out['colgId'] =  $id;
			 $out['colg_name'] =  $row['colg_name'];
			  $out['fees'] =  $row['fees'];
			 $out['cbname'] =  $row['cbname'];
			 $out['un_name'] =  $row['un_name'];
			 $out['rduname'] =  $row['rduname'];
			 $out['rcmname '] =  $row['cbname'];
			
		}
		return $out;
	}
	// this function used to show thw college course branch details with fee
	public function get_branch_fee_detail($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//$q = "SELECT * FROM college INNER JOIN course_branch_college as cbc USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN  course_branch USING(cbId) INNER JOIN course USING(cId) $filterstr";
		// query change satya
		 $q="SELECT * FROM college INNER JOIN course_branch_college as cbc USING(colgId) INNER JOIN course_branch_university_var USING(cbuvId) INNER JOIN course_branch_university USING(cbuId) INNER JOIN  course_branch USING(cbId) INNER JOIN course USING(cId) INNER JOIN university USING(unId) INNER JOIN ref_course_mode USING(rcmId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg = '');
		if(!$opt) return $out;
		
		while($row = mysqli_fetch_assoc($rs))
		{
			 $id =  $row['cId'];
			  $rcmid =  $row['rcmId'];
			 $cbcId =  $row['cbcId'];
			 $cbuvId =  $row['cbuvId'];
			 $colgId =  $row['colgId'];
			 $out[$id]['colgId'] =  $row['colgId'];
			 $out[$id]['cbuvId'] =  $row['cbuvId'];
			 $out[$id]['cname'] =  $row['cname'];
			 $out[$id]['cbname'][$cbcId] =  $row['cbname'];
			 $out[$id]['fees'][$cbcId] =  $row['fees'];
			 $out[$id]['course_alias'][$cbcId] =  $row['course_alias'];
			 $out[$id]['un_name'] =  $row['un_name'];
			// $out[$id]['rcmId'] =  $row['rcmId'];
			 $out[$id]['rcmname'][$cbuvId] =  $row['rcmname'];
		}
		return $out;
	}
	public function fee($id)
	{
		global $dbc;
		$out = array();
		$q="SELECT * FROM course_branch_college WHERE cbcId='$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg = '');
		//if(!$opt) return $out;
		
		//while($row = mysqli_fetch_assoc($rs))
		/*{
			
		
		 $out['fees'] =  $row['fees'];
			
			
		}*/
		return $rs['fees'];
		
		
	}
	public function mode($cbuvid)
	{
		global $dbc;
		$out = array();
		 $q="SELECT * FROM course_branch_university_var INNER JOIN ref_course_mode USING(rcmId) WHERE cbuvId='$cbuvid'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg = '');
		/*if(!$opt) return $out;
		
		while($row = mysqli_fetch_assoc($rs))
		{
		$out['rcmname'] =  $row['rcmname'];
			}*/
		return $rs['rcmname'];
		
		
	}
	public function getcbcid($colgId)
	{
		global $dbc;
		$out = array();
		 $q="SELECT * FROM college INNER JOIN course_branch_college USING(colgId) WHERE colgId='$colgId' LIMIT 1 ";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg = '');
		/*if(!$opt) return $out;
		
		while($row = mysqli_fetch_assoc($rs))
		{
		$out['rcmname'] =  $row['rcmname'];
			}*/
		return $rs['cbcId'];
		
		
	}
	public function get_set_data()
	{
		$d1 = array('colgId'=>($_POST['colgId']),'cc_type'=>($_POST['cc_type']),'cc_name'=>($_POST['cc_name']),'contact_no'=>($_POST['contact_no']),'cc_email'=>($_POST['cc_email']),'designation'=>($_POST['designation']));
		//$d1 = array('reexname'=>'gug', 'locked'=>0);
		$d1['reason'] = 'Please fill all the required information';	
		return array(true,$d1);
	}
	// This function will save the user qc  
	public function college_contact_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_set_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save the qc
		$q = "INSERT INTO `college_contact` (`ccId`, `colgId`, `cc_type`,`cc_name`,`contact_no`,`cc_email`,`designation`) VALUES (NULL , '$d1[colgId]', '$d1[cc_type]', '$d1[cc_name]', '$d1[contact_no]','$d1[cc_email]','$d1[designation]')";
		$r = mysqli_query($dbc,$q);
		if(!$r) return array('status'=>false, 'reason'=>'table error');
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Qc added with Qc no '.$qc['qcno']);
		return array('status'=>true, 'reason'=>'College_contact successfully saved', 'rId'=>$rId);
	}
		public function get_college_contact_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM college_contact INNER JOIN college USING (colgId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['ccId'];
			$out[$id]['ccId'] = $id; // storing the item id
			$out[$id]['colg_name'] = $row['colg_name'];
			$out[$id]['cc_type'] = $row['cc_type'];
			$out[$id]['cc_name'] = $row['cc_name'];
			$out[$id]['contact_no'] = $row['contact_no'];
			$out[$id]['cc_email'] = $row['cc_email'];
			$out[$id]['designation'] = $row['designation'];
		}
		return $out;
	}
	public function college_contact_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_set_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `college_contact` SET `cc_name` = '$d1[cc_name]', `contact_no`='$d1[contact_no]', `cc_email`='$d1[cc_email]',`designation`='$d1[designation]' WHERE ccId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'College_contact Updated With '.$id);
		return array('status'=>true, 'reason'=>'College_contact  successfully updated');
	}
	public function whyjoin($id)
	{
		//echo 'hiiiiiiiiiiiii';
		global $dbc;
		$out = array();
		$q="SELECT * FROM college WHERE colgId='$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg = '');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs))
		{
			
			$id = $row['colgId'];
			$out[$id]['colgId'] = $id; // storing the item id
			$out[$id]['whyjoin'] = $row['whyjoin'];
		}
		return $out;
		
	}

}// class end here
?>
