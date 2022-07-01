<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class party extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Item Category Starts here ####################################################	
	public function get_party_se_data()
	{  
		$d1 = array('ptId'=>$_POST['ptId'], 'party_code'=>$_POST['party_code'], 'party_name'=>$_POST['party_name'], 'username'=>$_POST['username'], 'pass'=>$_POST['pass'], 'group_code'=>$_POST['group_code'], 'division'=>$_POST['division'], 'website'=>$_POST['website'], 'phone'=>$_POST['phone'], 'tin_no'=>$_POST['tin_no'], 'fax_no'=>$_POST['fax_no'], 'ecc_no'=>$_POST['ecc_no'], 'certificate_sign'=>$_POST['certificate_sign'], 'adr_line1'=>$_POST['adr_line1'],'adr_line2'=>$_POST['adr_line2'],'locality'=>$_POST['locality'],'landmark'=>$_POST['landmark'],'city_district'=>$_POST['city_district'],'state'=>$_POST['state'],'pincode'=>$_POST['pincode'],'country'=>'India','pincode'=>$_POST['pincode'],'discount'=>$_POST['discount'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Part'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function party_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_party_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `party` (`partyId`, `ptId`,`party_name`, `username`,`pass`,`group_code`,`division`,`website`,`phone`,`tin_no`,`fax_no`,`ecc_no`,`certificate_sign`,`adr_line1`,`adr_line2`,`locality`,`landmark`,`city_district`,`state`,`pincode`,`country`,`crId`,`created`,`blacklist`,`discount`) VALUES (NULL , '$d1[ptId]', '$d1[party_name]', '$d1[username]','$d1[pass]', '$d1[group_code]', '$d1[division]', '$d1[website]', '$d1[phone]', '$d1[tin_no]', '$d1[fax_no]', '$d1[ecc_no]', '$d1[certificate_sign]','$d1[adr_line1]', '$d1[adr_line2]', '$d1[locality]','$d1[landmark]', '$d1[city_district]', '$d1[state]','$d1[pincode]', '$d1[country]', $d1[uid],NOW(),0,'$d1[discount]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_category table error');}
		$rId = mysqli_insert_id($dbc);	
		if($r){
				$k = 0;
				$str = '';
				foreach($_POST['name'] as $key)
				{
					$str .= "(NULL,'".$rId."','".$_POST['name'][$k]."','".$_POST['email'][$k]."','".$_POST['department'][$k]."','".$_POST['desgination'][$k]."','".$_POST['mobile'][$k]."','".$_POST['mobile_other'][$k]."'),";
					$k++;
				}
				$str = rtrim($str,',');
				$qq = "INSERT INTO party_contact (`pcId`,`partyId`,`name`,`email`,`department`,`desgination`,`mobile`,`mobile_other`) VALUES ".$str;
				$rr = mysqli_query($dbc,$qq);
		}
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function party_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_party_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_party_list("partyId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `party` SET `ptId` = '$d1[ptId]', `party_name` = '$d1[party_name]',`username` = '$d1[username]',`pass` = '$d1[pass]',`group_code` = '$d1[group_code]',`division` = '$d1[division]',`website` = '$d1[website]',`phone` = '$d1[phone]',`tin_no` = '$d1[tin_no]',`fax_no` = '$d1[fax_no]',`ecc_no` = '$d1[ecc_no]',`certificate_sign` = '$d1[certificate_sign]',`adr_line1` = '$d1[adr_line1]',`adr_line2` = '$d1[adr_line2]',`locality` = '$d1[locality]',`landmark` = '$d1[landmark]',`city_district` = '$d1[city_district]',`state` = '$d1[state]',`pincode` = '$d1[pincode]',`country` = '$d1[country]',`modified`= NOW(), `mrId`= $d1[uid] WHERE partyId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_category table error');}
		if($r){
			    $qq = "DELETE FROM party_contact WHERE partyId = '$id'";
				$k = 0;
				$str = '';
				foreach($_POST['name'] as $key)
				{
					$str .= "(NULL,'".$id."','".$_POST['name'][$k]."','".$_POST['email'][$k]."','".$_POST['department'][$k]."','".$_POST['desgination'][$k]."','".$_POST['mobile'][$k]."','".$_POST['mobile_other'][$k]."'),";
					$k++;
				}
				$str = rtrim($str,',');
				$qq = "INSERT INTO party_contact (`pcId`,`partyId`,`name`,`email`,`department`,`desgination`,`mobile`,`mobile_other`) VALUES ".$str;
				$rr = mysqli_query($dbc,$qq);
		}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
		
	public function get_party_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'%e/%b/%Y') AS fdated, DATE_FORMAT(modified,'%e/%b/%Y') AS flastedit FROM party $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['partyId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['contact_person'] = $this->get_my_reference_array_direct("SELECT * FROM party_contact WHERE partyId = $id", 'pcId');
		}
		//pre($out);
		return $out;
	} 
	public function get_work_order_se_data()
	{  
		$d1 = array('ptId'=>$_POST['ptId'], 'partyId'=>$_POST['partyId'], 'internal_order_no'=>$_POST['internal_order_no'], 'party_order_no'=>$_POST['party_order_no'], 'order_date'=>$_POST['order_date'], 'order_receive_date'=>$_POST['order_receive_date'], 'order_source'=>$_POST['order_source'], 'deal_status'=>$_POST['deal_status'], 'referenceId'=>$_POST['referenceId'], 'schedule_status'=>$_POST['schedule_status'], 'bill_status'=>$_POST['bill_status'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Part'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function work_order_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_party_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `work_order` (`woId`, `ptId`,`partyId`,`internal_order_no`, `party_order_no`,`order_date`,`order_receive_date`,`order_source`,`deal_status`,`referenceId`,`schedule_status`,`bill_status`,`crId`,`created`,`blacklist`,`discount`) VALUES (NULL , '$d1[ptId]', '$d1[partyId]', '$d1[internal_order_no]','$d1[party_order_no]', '$d1[order_date]', '$d1[order_receive_date]', '$d1[order_source]', '$d1[deal_status]', '$d1[referenceId]', '$d1[schedule_status]', '$d1[bill_status]', $d1[uid],NOW(),0,'$d1[discount]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_category table error');}
		$rId = mysqli_insert_id($dbc);	
		if($r){
				$k = 0;
				$str = '';
				foreach($_POST['name'] as $key)
				{
					$str .= "(NULL,'".$rId."','".$_POST['itemId'][$k]."','".$_POST['qty'][$k]."','".$_POST['job_type'][$k]."'),";
					$k++;
				}
				$str = rtrim($str,',');
				$qq = "INSERT INTO party_contact (`woi_key`,`woId`,`itemId`,`qty`,`job_type`) VALUES ".$str;
				$rr = mysqli_query($dbc,$qq);
		}
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function work_order_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_work_order_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_party_list("partyId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `party` SET `ptId` = '$d1[ptId]', `party_name` = '$d1[party_name]',`username` = '$d1[username]',`pass` = '$d1[pass]',`group_code` = '$d1[group_code]',`division` = '$d1[division]',`website` = '$d1[website]',`phone` = '$d1[phone]',`tin_no` = '$d1[tin_no]',`fax_no` = '$d1[fax_no]',`ecc_no` = '$d1[ecc_no]',`certificate_sign` = '$d1[certificate_sign]',`adr_line1` = '$d1[adr_line1]',`adr_line2` = '$d1[adr_line2]',`locality` = '$d1[locality]',`landmark` = '$d1[landmark]',`city_district` = '$d1[city_district]',`state` = '$d1[state]',`pincode` = '$d1[pincode]',`country` = '$d1[country]',`modified`= NOW(), `mrId`= $d1[uid] WHERE partyId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_category table error');}
		if($r){
			$qq = "DELETE FROM party_contact WHERE pcId = '$id'";
				$k = 0;
				$str = '';
				foreach($_POST['name'] as $key)
				{
					$str .= "(NULL,'".$id."','".$_POST['name'][$k]."','".$_POST['email'][$k]."','".$_POST['department'][$k]."','".$_POST['desgination'][$k]."','".$_POST['mobile'][$k]."','".$_POST['mobile_other'][$k]."'),";
					$k++;
				}
				$str = rtrim($str,',');
				$qq = "INSERT INTO party_contact (`pcId`,`partyId`,`name`,`email`,`department`,`desgination`,`mobile`,`mobile_other`) VALUES ".$str;
				$rr = mysqli_query($dbc,$qq);
		}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
		
	public function get_work_order_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'%e/%b/%Y') AS fdated, DATE_FORMAT(modified,'%e/%b/%Y') AS flastedit FROM party $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['partyId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['contact_person'] = $this->get_my_reference_array_direct("SELECT * FROM party_contact WHERE partyId = $id", 'pcId');
			/*$out[$id1][$id]['name'] = $row['name'];
			$out[$id1][$id]['email'] = $row['email'];
			$out[$id1][$id]['mobile'] = $row['mobile'];*/
			
		}
		//pre($out);
		return $out;
	} 
	
}// class end here

?>
