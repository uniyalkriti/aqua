<?php 
class srf extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_srf_se_data()
	{
		/*$_POST['srfdate'] = '06/01/2014'; $_POST['partyId'] = 3; $_POST['remark'] = 'remark'; 
		$_POST['challan_or_po_based'] = '2'; $_POST['po_challan_no'] = '789'; $_POST['po_challan_date'] = '01/01/2014';
		
		$_POST['itemId'] = $_POST['itemdesc'] = $_POST['lab_code'] = $_POST['equipno'] = $_POST['make'] = $_POST['model'] = $_POST['serial_no'] = $_POST['range_size'] = range(5,10);
		$_POST['least_count'] = $_POST['cal_step_type'] = $_POST['cal_step_detail'] = $_POST['calibration_frequency'] = range(5,10);*/
 
		$d1 = array('srfdate'=>$_POST['srfdate'], 'partyId'=>$_POST['partyId'], 'challan_or_po_based'=>$_POST['challan_or_po_based'], 'po_challan_no'=>$_POST['po_challan_no'], 'po_challan_date'=>$_POST['po_challan_date'], 'remark'=>$_POST['remark'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		
		$srfno = $this->get_next_srf();
		//$d1['srfcode'] = '20140106-A'.$d1['srfno'];
		$srfdate = explode('/', $d1['srfdate']);
		$srfcode = $srfdate[2].$srfdate[1].$srfdate[0].'-'.month_to_alphabet($srfdate[1]).$srfno;
		
		//We do not wish to find a new srfcode if we do not change the date		
		$d1['srfno'] = $srfno;
		$d1['srfcode'] = $srfcode;
		if(isset($_POST['eid'])){
			if($_POST['srfdate'] == $_POST['osrfdate']){
				$d1['srfno'] = $_POST['srfno'];
				$d1['srfcode'] = $_POST['srfcode'];
			}
		}
		
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'SRF'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function srf_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_srf_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$srfdate = $po_challan_date = '';
		if(!empty($d1['srfdate']))  $srfdate = get_mysql_date($d1['srfdate'], '/', false, false);
		if(!empty($d1['po_challan_date']))  $po_challan_date = get_mysql_date($d1['po_challan_date'], '/', false, false);
		$q = "INSERT INTO `srf` (`srfId`, `srfno`, `srfcode`, `srfdate`, `partyId`, `challan_or_po_based`, `po_challan_no`, `po_challan_date`, `remark`, `created`, `crId`) VALUES (NULL, '$d1[srfno]', '$d1[srfcode]', '$srfdate', '$d1[partyId]', '$d1[challan_or_po_based]', '$d1[po_challan_no]', '$po_challan_date', '$d1[remark]',  NOW(), '$d1[uid]')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'srf Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->srf_extra('save', $rId, array('srfcode'=>$d1['srfcode'], 'itemId'=>$_POST['itemId'], 'itemdesc'=>$_POST['itemdesc'], 'lab_code'=>$_POST['lab_code'], 'equipno'=>$_POST['equipno'], 'make'=>$_POST['make'], 'model'=>$_POST['model'], 'serial_no'=>$_POST['serial_no'], 'range_size'=>$_POST['range_size'], 'least_count'=>$_POST['least_count'], 'cal_step_type'=>$_POST['cal_step_type'], 'cal_step_detail'=>$_POST['cal_step_detail'], 'calibration_frequency'=>$_POST['calibration_frequency'], 'rifId'=>$_POST['rifId'], 'tmpmasterId'=>$_POST['tmpmasterId'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'SRF received <b>'.$d1['srfcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function srf_extra($actiontype, $rId, $option)
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//If we are editing
		if($actiontype == 'edit') mysqli_query($dbc, "DELETE FROM srf_item WHERE srfId = $rId");
		
		// saving the details for the stock item table
		foreach($itemId as $key=>$value){
			$lab_code[$key] = $srfcode.'/'.($key+1);
			$unkey = $_POST['srfItemId'][$key];
			$str[] = "($unkey, $rId, '$value', '{$itemdesc[$key]}', '{$lab_code[$key]}', '{$equipno[$key]}', '{$make[$key]}', '{$model[$key]}', '{$serial_no[$key]}', '{$range_size[$key]}', '{$least_count[$key]}', '{$cal_step_type[$key]}', '{$cal_step_detail[$key]}', '{$calibration_frequency[$key]}', $key+1, '{$tmpmasterId[$key]}', '{$rifId[$key]}')";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `srf_item` (srfItemId, srfId, itemId, itemdesc, lab_code, equipno, make, model, serial_no, range_size, least_count, cal_step_type, cal_step_detail, calibration_frequency, sort_order, tmpmasterId, rifId) VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'srf_item Table error') ;
		
		$q = "UPDATE srf SET itemqty = $key+1 WHERE srfId = $rId LIMIT 1";
		if(!mysqli_query($dbc,$q)) return array ('status'=>false,'myreason'=>'srf qty update error in sale');
		return array ('status'=>true,'myreason'=>'');
	}
	
    public function srf_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_srf_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		$srfdate = $po_challan_date = '';
		if(!empty($d1['srfdate']))  $srfdate = get_mysql_date($d1['srfdate'], '/', false, false);
		if(!empty($d1['po_challan_date']))  $po_challan_date = get_mysql_date($d1['po_challan_date'], '/', false, false);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q = "UPDATE srf SET srfno = '$d1[srfno]', srfcode = '$d1[srfcode]', srfdate = '$srfdate', partyId = '$d1[partyId]', challan_or_po_based = '$d1[challan_or_po_based]', po_challan_no = '$d1[po_challan_no]', po_challan_date = '$po_challan_date', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE srfId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'srf Table error') ;} 
		$rId = $id;
		
		$extrawork = $this->srf_extra('edit', $rId, array('srfcode'=>$d1['srfcode'], 'itemId'=>$_POST['itemId'], 'itemdesc'=>$_POST['itemdesc'], 'lab_code'=>$_POST['lab_code'], 'equipno'=>$_POST['equipno'], 'make'=>$_POST['make'], 'model'=>$_POST['model'], 'serial_no'=>$_POST['serial_no'], 'range_size'=>$_POST['range_size'], 'least_count'=>$_POST['least_count'], 'cal_step_type'=>$_POST['cal_step_type'], 'cal_step_detail'=>$_POST['cal_step_detail'], 'calibration_frequency'=>$_POST['calibration_frequency'], 'rifId'=>$_POST['rifId'], 'tmpmasterId'=>$_POST['tmpmasterId'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'SRF received <strong>'.$d1['srfcode'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_srf_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate, DATE_FORMAT(po_challan_date, '".MASKDATE."') AS po_challan_date FROM srf $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		$custId_map = get_my_reference_array('party', 'partyId', 'party_name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['srfId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['challan_or_po_based_val'] = $GLOBALS['challan_or_po_based'][$row['challan_or_po_based']];
			$out[$id]['partyId_val'] = $custId_map[$row['partyId']]; 
			$out[$id]['srf_item'] = $this->get_my_reference_array_direct("SELECT * FROM srf_item WHERE srfId = $id ORDER BY sort_order ASC", 'srfItemId');
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function srf_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "srfId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_srf_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'SRF No. not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		//Running the deletion queries
		$delquery = array();
		$delquery['srf'] = "DELETE FROM srf WHERE srfId = $id LIMIT 1";
		$delquery['srf_item'] = "DELETE FROM srf_item WHERE srfId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'SRF No. successfully deleted');
	}
	
	// This function will fetch the details about an particular lab_code
	public function get_srf_join_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate, DATE_FORMAT(po_challan_date, '".MASKDATE."') AS po_challan_date FROM srf INNER JOIN srf_item USING(srfId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		$custId_map = get_my_reference_array('party', 'partyId', 'party_name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['srfId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['challan_or_po_based_val'] = $GLOBALS['challan_or_po_based'][$row['challan_or_po_based']];
			$out[$id]['partyId_val'] = $custId_map[$row['partyId']]; 
			//$out[$id]['srf_item'] = $this->get_my_reference_array_direct("SELECT * FROM srf_item WHERE srfId = $id", 'srfItemId');
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function get_next_srf()
	{
		global $dbc;
		$srfdate = get_mysql_date($_POST['srfdate'], '/', false, false);
		list($opt,$rs) = run_query($dbc,"SELECT MAX(srfno) AS total FROM srf WHERE DATE_FORMAT(srfdate, '".MYSQL_DATE_SEARCH."') = DATE_FORMAT('$srfdate', '".MYSQL_DATE_SEARCH."')");
		return $rs['total']+1;
	}
	
	//This function will help in the mulitpage print
	public function print_looper($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the stat of the srf
			$srfstat = $this->get_srf_list("srfId = $id");
			if(empty($srfstat)) continue;
			$srfstat = $srfstat[$id];
			$out[$id]['srfdate'] = $srfstat['srfdate'];
			$out[$id]['srfcode'] = $srfstat['srfcode'];
			$out[$id]['partyId'] = $srfstat['partyId'];
			$out[$id]['partyId_val'] = $srfstat['partyId_val'];
			$out[$id]['challan_or_po_based'] = $GLOBALS['challan_or_po_based'][$srfstat['challan_or_po_based']];
			$out[$id]['po_challan_no'] = $srfstat['po_challan_no'];
			$out[$id]['po_challan_date'] = $srfstat['po_challan_date'];
			$out[$id]['cust_adr'] = '243, A1, Munirka, Village, Near Rama Market, New Delhi - 110067';
			
			//Fetch the contact person details
			list($opt0,$rs0) = run_query($dbc,"SELECT name, mobile, mobile_other FROM party_contact WHERE partyId = {$srfstat['partyId']} AND name != '' LIMIT 1");
			$out[$id]['contact_person'] = $out[$id]['contact_detail'] = '';
			if($opt0){
				$out[$id]['contact_person'] = ucwords(strtolower($rs0['name']));
				$out[$id]['contact_detail'] = !empty($rs0['mobile_other']) ? $rs0['mobile'].', '.$rs0['mobile_other'] : $rs0['mobile'];
			}
			
			$out[$id]['remark'] = $srfstat['remark'];
			
			//Store the item intake details
			$out[$id]['intake'] = $srfstat['srf_item'];
		}
		//pre($out);
		return $out;
	}
}
?>