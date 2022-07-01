<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class party extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Vendor Starts here ####################################################	
	public function get_vendor_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		//$d1 = array('partycode'=>$_POST['partycode'], 'partyname'=>$_POST['partyname'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'fax'=>$_POST['fax'], 'contact_person'=>$_POST['contact_person'], 'website'=>$_POST['website'], 'tinno'=>$_POST['tinno'], 'ecc'=>$_POST['ecc'], 'prange'=>$_POST['prange'], 'division'=>$_POST['division'], 'adr'=>$_POST['adr'], 'locality'=>$_POST['locality'], 'landmark'=>$_POST['landmark'], 'city_district'=>$_POST['city_district'], 'state'=>$_POST['state'], 'pincode'=>$_POST['pincode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];		
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Party'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function vendor_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_vendor_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
               
               
		$q = "INSERT INTO `party` (`partyId`, `ptype`, `partycode`, `partyname`, `email`, `mobile`, `phone`, `fax`, `website`, `tinno`, `eccno`, `vatno`, `cstno`, `crange`, `cdivision`, `adr`, `locality`, `landmark`, `city_district`, `state`, `pincode`, `country`, `crId`, `created`, `remark`) VALUES (NULL, 1, '$partycode', '$d1[partyname]', '$d1[email]', '$d1[mobile]', '$d1[phone]', '$d1[fax]', '$d1[website]', '$d1[tinno]', '$d1[eccno]', '$d1[vatno]', '$d1[cstno]', '$d1[crange]', '$d1[cdivision]', '$d1[adr]', '$d1[locality]', '$d1[landmark]', '$d1[city_district]', '$d1[state]', '$d1[pincode]', '$d1[country]', '$d1[uid]', NOW(), '$d1[remark]');";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'party table error');}
		$rId = mysqli_insert_id($dbc);
		$extrawork = $this->party_extra('save', $rId, $_POST['cname'], $_POST['cdesignation'], $_POST['cmobile'], $_POST['cemail'], $_POST['cphone'], $_POST['cremark']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);}			
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function party_extra($actiontype, $rId, $cname, $cdesignation, $cmobile, $cemail, $cphone, $cremark)
	{ 
		global $dbc;		
		$uncode = '';
		$contact_person = $contact_phone = '';
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM party_contact WHERE partyId = $rId");
		// saving the details for the stock item table
		$str = array();
		$inc = 0;
		foreach($cname as $key=>$value){			
			$uncode = $rId.$inc;
			if(empty($value)) continue; // not to store detail if it is empty
			//Saving the person contact detail
			if(empty($contact_person)){				
				$contact_person = $value;
				$temp = array();
				if(!empty($cmobile[$key])) $temp[] = $cmobile[$key];
				if(!empty($cphone[$key])) $temp[] = $cphone[$key];
				if(!empty($temp)) $contact_phone = implode(', ', $temp);				
			}
			$str[] = "($uncode, $rId, '$value', '{$cdesignation[$key]}', '{$cmobile[$key]}', '{$cemail[$key]}', '{$cphone[$key]}', '{$cremark[$key]}', $inc)";
			$inc++;
		}
		if(empty($str)) return array('status'=>false, 'myreason'=>'atleast enter 1 contact person name');
		$str = implode(', ', $str);	
		$q = "INSERT INTO `party_contact` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'party_contact Table error') ;
		
		//update the contact person name and phone in the party table 
		mysqli_query($dbc, "UPDATE party SET contact_person = '$contact_person', contact_phone = '$contact_phone' WHERE partyId = $rId LIMIT 1");
		return array ('status'=>true,'myreason'=>'');
	}
	
	public function vendor_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_vendor_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		//$originaldata = $this->get_party_list("partyId = $id");
		//$originaldata = $originaldata[$id];
		//$modifieddata = $this->get_modified_data($originaldata, $d1);
		//if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `party` SET `partycode` = '$d1[partycode]', `partyname` = '$d1[partyname]', `email` = '$d1[email]', `mobile` = '$d1[mobile]', `phone` = '$d1[phone]', `fax` = '$d1[fax]', `website` = '$d1[website]', `tinno` = '$d1[tinno]', `eccno` = '$d1[eccno]', `vatno` = '$d1[vatno]', `cstno` = '$d1[cstno]', `crange` = '$d1[crange]', `cdivision` = '$d1[cdivision]', `adr` = '$d1[adr]', `locality` = '$d1[locality]', `landmark` = '$d1[landmark]', `city_district` = '$d1[city_district]', `state` = '$d1[state]', `pincode` = '$d1[pincode]', `remark` = '$d1[remark]', `mrId` = $d1[uid], `modified` = NOW() WHERE partyId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'party table error');}
		$rId = $id;
		//Doing some extra work
		$extrawork = $this->party_extra('update', $rId, $_POST['cname'], $_POST['cdesignation'], $_POST['cmobile'], $_POST['cemail'], $_POST['cphone'], $_POST['cremark']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}
		
	public function get_party_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'%e/%b/%Y') AS createdf, DATE_FORMAT(modified,'%e/%b/%Y') AS modifiedf FROM party $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['partyId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['party_contact'] = $this->get_my_reference_array_direct("SELECT * FROM party_contact WHERE partyId = $id", 'pcKey');
		}
		//pre($out);
		return $out;
	} 
	
	public function get_party_adr($id, $seperator='<br>')
	{
		global $dbc;
		$out = '';
		$q = "SELECT * FROM party WHERE partyId = $id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$out = address_presenter($rs, array('adr','locality','landmark','city_district','pincode'=>'pincode', 'state'=>'state'), $seperator);
		return $out;
	} 
	######################################## Vendor Ends here ####################################################
	
	######################################## Customer Starts here ####################################################
	public function get_customer_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];		
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Customer'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function customer_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_customer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `party` (`partyId`, `ptype`, `partycode`, `partyname`, `email`, `mobile`, `phone`, `fax`, `website`, `tinno`, `eccno`, `vatno`, `cstno`, `crange`, `cdivision`, `adr`, `locality`, `landmark`, `city_district`, `state`, `pincode`, `country`, `crId`, `created`, `remark`) VALUES (NULL, 2, '$d1[partycode]', '$d1[partyname]', '$d1[email]', '$d1[mobile]', '$d1[phone]', '$d1[fax]', '$d1[website]', '$d1[tinno]', '$d1[eccno]', '$d1[vatno]', '$d1[cstno]', '$d1[crange]', '$d1[cdivision]', '$d1[adr]', '$d1[locality]', '$d1[landmark]', '$d1[city_district]', '$d1[state]', '$d1[pincode]', '$d1[country]', '$d1[uid]', NOW(), '$d1[remark]');";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'party table error');}
		$rId = mysqli_insert_id($dbc);
		$extrawork = $this->party_extra('save', $rId, $_POST['cname'], $_POST['cdesignation'], $_POST['cmobile'], $_POST['cemail'], $_POST['cphone'], $_POST['cremark']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);}			
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function customer_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_customer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		//$originaldata = $this->get_party_list("partyId = $id");
		//$originaldata = $originaldata[$id];
		//$modifieddata = $this->get_modified_data($originaldata, $d1);
		//if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `party` SET `partycode` = '$d1[partycode]', `partyname` = '$d1[partyname]', `email` = '$d1[email]', `mobile` = '$d1[mobile]', `phone` = '$d1[phone]', `fax` = '$d1[fax]', `website` = '$d1[website]', `tinno` = '$d1[tinno]', `eccno` = '$d1[eccno]', `vatno` = '$d1[vatno]', `cstno` = '$d1[cstno]', `crange` = '$d1[crange]', `cdivision` = '$d1[cdivision]', `adr` = '$d1[adr]', `locality` = '$d1[locality]', `landmark` = '$d1[landmark]', `city_district` = '$d1[city_district]', `state` = '$d1[state]', `pincode` = '$d1[pincode]', `remark` = '$d1[remark]', `mrId` = $d1[uid], `modified` = NOW() WHERE partyId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'party table error');}
		$rId = $id;
		//Doing some extra work
		$extrawork = $this->party_extra('update', $rId, $_POST['cname'], $_POST['cdesignation'], $_POST['cmobile'], $_POST['cemail'], $_POST['cphone'], $_POST['cremark']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);}
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}
	######################################## Customer Ends here ####################################################
	public function party_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "partyId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_party_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Party not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		//$q['PR'] = "SELECT itemId FROM pr_item WHERE itemId = ";
		$q['PO'] = "SELECT partyId FROM po WHERE partyId = ";
		$q['work_po'] = "SELECT partyId FROM work_po WHERE partyId = ";
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this item has been found in any one of the above query we can not delete it.				
		if($found) {$out['myreason'] = 'party  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['party'] = "DELETE FROM party WHERE partyId = $id LIMIT 1";
		$delquery['party_contact'] = "DELETE FROM party_contact WHERE partyId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Party successfully deleted');
	}
	
}// class end here
?>