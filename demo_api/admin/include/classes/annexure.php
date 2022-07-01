<?php 
class annexure extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## WORK PO Starts here ####################################################
	public function get_job_order_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Job Order'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function job_order_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_job_order_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$cjonum = empty($d1['cjonum']) ? $this->next_jo_num() : $d1['cjonum'];
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		$cjodate = empty($d1['cjo_date']) ? '' : get_mysql_date($d1['cjo_date'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `ch_job_order` (`cjoId`, `cjonum`, `sesId`, `partyId`, `cjo_date`, `pmt_days`, `remark`, `created`, `crId`) 
			VALUES (NULL, '$cjonum', '$d1[csess]', '$d1[partyId]', '$cjodate', '$d1[pmt_days]', '$d1[remark]', NOW(), '$d1[uid]');";
		
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Job Order Challan Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->work_jo_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['rate']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function work_jo_extra($actiontype, $rId, $itemId, $qty, $rate)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$jo_item_old = $this->get_my_reference_array_direct("SELECT * FROM ch_job_order_item WHERE cjoId = $rId", 'cjoKey');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM ch_job_order_item WHERE cjoId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($uncode,$rId, $value, '{$qty[$key]}', '{$rate[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `ch_job_order_item` VALUES $str";
		
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'ch_job_order_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function job_order_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_job_order_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$cjodate = empty($d1['cjo_date']) ? '' : get_mysql_date($d1['cjo_date'], '/', false, false);
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE ch_job_order SET partyId = '$d1[partyId]', pmt_days = '$d1[pmt_days]', cjonum = '$d1[cjonum]', cjo_date = '$cjodate', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE cjoId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_po Table error') ;} 
		$extrawork = $this->work_jo_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['rate']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_job_order_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(cjo_date, '".MASKDATE."') AS cjo_date, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM ch_job_order $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['cjoId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['work_jo_item'] = $this->get_my_reference_array_direct("SELECT ch_job_order_item.*, itemname FROM ch_job_order_item INNER JOIN item USING(itemId) WHERE cjoId = $id", 'cjoKey'); 
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
	
	public function next_jo_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(cjonum) AS total FROM ch_job_order WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## WORK PO ends here ####################################################
	
	######################################## Annexure Starts here ####################################################
	public function get_annexure_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Annexure'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function annexure_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_annexure_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$chanum = empty($d1['chanum']) ? $this->next_annexure_num() : $d1['chanum'];
		$chdate = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `ch_annexure` (`chaId`, `cjoId`, `sesId`, `partyId`, `chanum`, `challan_date`, `modetpt`, `vehicleno`, `duration`, `remark`, `crId`, `created`) 
			VALUES (NULL, '$d1[cjoId]', '$d1[csess]', '$d1[partyId]', '$chanum', '$chdate', '$d1[modetpt]', '$d1[vehicleno]', '$d1[duration]', '$d1[remark]', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch_annexure Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->annexure_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function annexure_extra($actiontype, $rId, $itemId, $qty, $unit, $goodvalue, $job_process)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $challan_value = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM ch_annexure_item WHERE chaId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM ch_annexure_item WHERE chaId = $rId");
		
		foreach($itemId as $key=>$value){
			if(empty($value)) continue;
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			$challan_value[] = $goodvalue[$key];
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($uncode, $rId, $value, '{$qty[$key]}', '{$unit[$key]}', '{$goodvalue[$key]}', '{$job_process[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `ch_annexure_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'ch_annexure_item Table error') ;	
		//Update the qty in the database 
		mysqli_query($dbc,"UPDATE ch_annexure SET totqty = '".array_sum($qty_sum)."', challan_value = '".array_sum($challan_value)."' WHERE chaId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function annexure_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_annexure_se_data();
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
		$q="UPDATE ch_annexure SET partyId = '$d1[partyId]', challan_date = '$chdate', modetpt = '$d1[modetpt]', vehicleno = '$d1[vehicleno]', duration = '$d1[duration]', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE chaId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch_annexure Table error') ;} 
		$extrawork = $this->annexure_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['unit'], $_POST['goodvalue'], $_POST['job_process']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_annexure_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(challan_date, '".MASKDATE."') AS challan_date, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM ch_annexure $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['chaId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['cha_item'] = $this->get_my_reference_array_direct("SELECT ch_annexure_item.*, itemname FROM ch_annexure_item INNER JOIN item USING(itemId) WHERE chaId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function annexure_delete($id, $filter='', $records='', $orderby='')
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
		return array('status'=>true, 'myreason'=>'Annexure successfully deleted');
	}
	
	public function next_annexure_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(chanum) AS total FROM ch_annexure WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## ANNEXURE ends here ####################################################
	
	public function print_looper($multiId, $options=array())
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
			$rcdstat = $this->get_annexure_list("chaId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);
		}
		//pre($out);
		return $out;
	}
	
	public function annexure_return_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_annexure_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		//$chanum = empty($d1['chanum']) ? $this->next_annexure_num() : $d1['chanum'];
		$challan_date = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		$receive_date = empty($d1['receive_date']) ? '' : get_mysql_date($d1['receive_date'], '/', false, false); 
		
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `ch_annexure_receive` (`charId`, `sesId`, `chaId`, `receive_date`, `challan_date`, `chanum`, `partyId`, `modetpt`, `remark`, `vehicleno`, `created`, `crId`) VALUES (NULL, '{$d1['csess']}', '{$d1['chaId']}', '$receive_date', '$challan_date', '{$d1['chanum']}', '{$d1['partyId']}', '{$d1['modetpt']}', '{$d1['remark']}', '{$d1['vehicleno']}', NOW(), '{$d1['uid']}')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch_annexure_return Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->annexure_return_extra('save', $rId, $_POST['itemId'], $_POST['recqty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>'Annexure receive successfully Saved', 'rId'=>$rId);
	}
	
	public function annexure_return_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $challan_value = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM ch_annexure_receive_item WHERE charId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM ch_annexure_receive_item WHERE charId = $rId");
		
		foreach($itemId as $key=>$value){
			if(empty($value)) continue;
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			//$challan_value[] = $goodvalue[$key];
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($uncode, $rId, $value, '{$qty[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `ch_annexure_receive_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'ch_annexure_return_item Table error') ;	
		//Update the qty in the database 
		mysqli_query($dbc,"UPDATE ch_annexure_receive SET totalqty = '".array_sum($qty_sum)."' WHERE charId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function annexure_return_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_annexure_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$challan_date = empty($d1['challan_date']) ? '' : get_mysql_date($d1['challan_date'], '/', false, false); 
		$receive_date = empty($d1['receive_date']) ? '' : get_mysql_date($d1['receive_date'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		
		$q="UPDATE ch_annexure_receive SET `chanum` = '$d1[chanum]', partyId = '$d1[partyId]', challan_date = '$challan_date', `receive_date` = '$receive_date', modetpt = '$d1[modetpt]', vehicleno = '$d1[vehicleno]', `remark` = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE charId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'ch_annexure_receive Table error') ;} 
		$extrawork = $this->annexure_return_extra('update', $id, $_POST['itemId'], $_POST['recqty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>'Annexure receive successfully Updated');
	}
	
	public function get_annexure_return_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(challan_date, '".MASKDATE."') AS challan_date, DATE_FORMAT(receive_date, '".MASKDATE."') AS receive_date, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM ch_annexure_receive $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['charId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['cha_return_item'] = $this->get_my_reference_array_direct("SELECT  ch_annexure_receive_item.*, itemname FROM ch_annexure_receive INNER JOIN  ch_annexure_receive_item USING(charId) INNER JOIN item USING(itemId) WHERE charId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function print_looper_annexure_return($multiId, $options=array())
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
			$rcdstat = $this->get_annexure_return_list("charId = $id");
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