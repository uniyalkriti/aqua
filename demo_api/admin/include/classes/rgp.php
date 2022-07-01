<?php 
class rgp extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## WORK rgp Starts here ####################################################
	public function get_rgp_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Rgp'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function rgp_challan_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_rgp_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$chanum = empty($d1['chanum']) ? $this->next_rgp_num() : $d1['chanum'];
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		$challan_date = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `ch_rgp` (`chrgpId` , `sesId`, `partyId`, `chanum`, `challan_date`, `modetpt`, `vehicleno`,`duration`,`remark`, `created`, `crId`) 
			VALUES (NULL, '$d1[csess]', '$d1[partyId]', '$chanum', '$challan_date', '$d1[modetpt]', '$d1[vehicleno]', '$d1[duration]','$d1[remark]', NOW(), '$d1[uid]');";
		
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Rgp Challan Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->rgp_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function rgp_extra($actiontype, $rId, $itemId, $qty, $unit, $goodvalue, $job_process)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $challan_value = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM ch_rgp_item WHERE chrgpId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM ch_rgp_item WHERE chrgpId = $rId");
		
		foreach($itemId as $key=>$value){
			if(empty($value)) continue;
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			$challan_value[] = $goodvalue[$key];
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($uncode, $rId, $value, '{$qty[$key]}', '{$unit[$key]}', '{$goodvalue[$key]}', '{$job_process[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `ch_rgp_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'ch_rgp_item Table error') ;	
		//Update the qty in the database 
		
		mysqli_query($dbc,"UPDATE ch_rgp SET totalqty = '".array_sum($qty_sum)."', challan_value = '".array_sum($challan_value)."' WHERE chrgpId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function rgp_challan_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_rgp_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$chdate = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE ch_rgp SET partyId = '$d1[partyId]', challan_date = '$chdate', modetpt = '$d1[modetpt]', vehicleno = '$d1[vehicleno]', duration = '$d1[duration]', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE chrgpId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch_annexure Table error') ;} 
		$extrawork = $this->rgp_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_rgp_challan_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(challan_date, '".MASKDATE."') AS challan_date, DATE_FORMAT(created, '%e/%d/%Y  %h:%m') AS challan_date_time, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM ch_rgp $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['chrgpId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['cha_item'] = $this->get_my_reference_array_direct("SELECT ch_rgp_item.*, itemname FROM ch_rgp_item INNER JOIN item USING(itemId) WHERE chrgpId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function work_po_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "stockId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_stock_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Invoice not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		//$q = "SELECT GROUP_CONCAT(uncode) FROM stock_item GROUP BY stockId HAVING stockId  = 1";
		list($opt, $rs) = run_query($dbc, "SELECT uncode FROM sale_item_uncode INNER JOIN stock_item USING(uncode) WHERE stockId  = $id LIMIT 1");
		if($opt) return array('status'=>false, 'myreason'=>'Invoice cannot be deleted as,<br> some items already sold from this invoice');
		
		//Running the deletion queries
		$delquery = array();
		$delquery['stock'] = "DELETE FROM stock WHERE stockId = $id LIMIT 1";
		$delquery['stock_item'] = "DELETE FROM stock_item WHERE stockId = $id";
		$delquery['stock_tax'] = "DELETE FROM stock_tax WHERE stockId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Invoice successfully deleted');
	}
	
	public function next_rgp_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(chanum) AS total FROM ch_rgp WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## WORK rgp Starts here ####################################################
	public function get_rgp_receiving_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Rgp Receiving'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function rgp_receiving_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_rgp_receiving_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		$challan_date = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		$rec_date = empty($d1['receive_date']) ? '' : get_mysql_date($d1['receive_date'], '/', false, false);
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `ch_rgp_receive` (`chrgprecId`,`chrgpId` , `sesId`, `partyId`, `chanum`, `challan_date`, `receive_date`, `modetpt`, `vehicleno`, `remark`, `created`, `crId`) 
			VALUES (NULL, '$d1[chrgpId]', '$d1[csess]', '$d1[partyId]', '$d1[chanum]', '$challan_date', '$rec_date', '$d1[modetpt]', '$d1[vehicleno]', '$d1[remark]', NOW(), '$d1[uid]');";
		
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Rgp Challan Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->rgp_receiving_extra('save', $rId, $_POST['itemId'], $_POST['recqty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function rgp_receiving_extra($actiontype, $rId, $itemId, $recqty, $unit, $goodvalue, $job_process)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $challan_value  = $recqty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$ch_rec_item_old = $this->get_my_reference_array_direct("SELECT * FROM ch_rgp_receive_item WHERE chrgprecId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM ch_rgp_receive_item WHERE chrgprecId = $rId");
		
		foreach($itemId as $key=>$value){
			if(empty($value)) continue;
			$uncode = $rId.$value;
			$recqty_sum[] = $recqty[$key];
			$challan_value[] = $goodvalue[$key];
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($uncode, $rId, $value, '{$recqty[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `ch_rgp_receive_item` (`chrgpreckey`, `chrgprecId`, `itemId`, `qty`, `sortorder`)  VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'ch_rgp_receive_item Table error') ;	
		//Update the qty in the database 
		
		//echo "UPDATE ch_rgp_receive SET totalqty = '".array_sum($recqty_sum)."', challan_value = '".array_sum($challan_value)."' WHERE chrgprecId = $rId LIMIT 1";
		mysqli_query($dbc,"UPDATE ch_rgp_receive SET totalqty = '".array_sum($recqty_sum)."' WHERE chrgprecId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function  rgp_receiving_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_rgp_receiving_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$chdate = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		$rec_date = empty($d1['receive_date']) ? '' : get_mysql_date($d1['receive_date'], '/', false, false);
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE ch_rgp_receive SET partyId = '$d1[partyId]', challan_date = '$chdate',receive_date = '$rec_date', modetpt = '$d1[modetpt]', vehicleno = '$d1[vehicleno]', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE chrgprecId ='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch rgp receive Table error') ;} 
		$extrawork = $this->rgp_receiving_extra('update', $id, $_POST['itemId'], $_POST['recqty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_rgp_receiving_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(challan_date, '".MASKDATE."') AS challan_date, DATE_FORMAT(created, '%e/%d/%Y  %h:%m') AS challan_date_time, DATE_FORMAT(created, '".MASKDATE."') AS createdf,DATE_FORMAT(receive_date, '".MASKDATE."') AS receive_date FROM ch_rgp_receive  $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['chrgprecId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['cha__rgp_item'] = $this->get_my_reference_array_direct("SELECT ch_rgp_receive_item.*, itemname,qty as recqty FROM `ch_rgp_receive_item`  INNER JOIN item USING(itemId) WHERE chrgprecId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	######################################## WORK rgp challan ends here ####################################################
	public function print_looper_rgp($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		//Create the object when needed
		$party = new party();
		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_rgp_challan_list("chrgpId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);
		}
		//pre($out);
		return $out;
	}
	public function print_looper_rgp_receiving($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		//Create the object when needed
		$party = new party();
		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_rgp_receiving_list("chrgprecId  = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);
		}
		//pre($out);
		return $out;
	}
}
?>