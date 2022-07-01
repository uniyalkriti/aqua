<?php 
class gate_entry extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## GATE ENTRY Starts here ####################################################
	public function get_gate_entry_po_se_data()
	{
		//$_POST['deptId'] = 1; $_POST['prnum'] = 2; ;
 		//$_POST['itemId'] = $_POST['qty'] = range(5,9);
 		$d1 = $_POST;
		//$d1 = array('partycode'=>$_POST['partycode'], 'partyname'=>$_POST['partyname'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'fax'=>$_POST['fax'], 'contact_person'=>$_POST['contact_person'], 'website'=>$_POST['website'], 'tinno'=>$_POST['tinno'], 'ecc'=>$_POST['ecc'], 'prange'=>$_POST['prange'], 'division'=>$_POST['division'], 'adr'=>$_POST['adr'], 'locality'=>$_POST['locality'], 'landmark'=>$_POST['landmark'], 'city_district'=>$_POST['city_district'], 'state'=>$_POST['state'], 'pincode'=>$_POST['pincode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Gate Entry'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function gate_entry_po_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_gate_entry_po_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$gatenum = $this->next_gate_entry_po_num();
		$entrydate = empty($d1['entrydate']) ? '' : get_mysql_date($d1['entrydate'], '/', false, false); 
		$transdate = empty($d1['transdate']) ? '' : get_mysql_date($d1['transdate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `gate` (`gateId`, `gatenum`, `sesId`, `partyId`, `transtype`, `entrydate`, `transdate`, `transnum`, `poId`, `pdsId`, `crId`, `created`) 
			VALUES (NULL, '$gatenum', '$d1[csess]', '$d1[partyId]', '1', '$entrydate', '$transdate', '$d1[transnum]', '$d1[poId]', '', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'gate_entry_po Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->gate_entry_po_extra('save', $rId, $_POST['itemId'], $_POST['poqty'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function gate_entry_po_extra($actiontype, $rId, $itemId, $poqty, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM gate_item WHERE gateId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM gate_item WHERE gateId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			/*$qty_received = $balance = $locked = 0;
			if(isset($po_item_old[$value])){
				$qty_received = $po_item_old[$value]['qty_received'];
				$balance = $po_item_old[$value]['balance'];
				$locked = $po_item_old[$value]['locked'];
			}*/
			
			$str[] = "('$uncode', $rId, $value, '{$poqty[$key]}', '', '{$qty[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `gate_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'gate_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function gate_entry_po_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_gate_entry_po_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$entrydate = empty($d1['entrydate']) ? '' : get_mysql_date($d1['entrydate'], '/', false, false); 
		$transdate = empty($d1['transdate']) ? '' : get_mysql_date($d1['transdate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE gate SET entrydate = '$entrydate', partyId = '$d1[partyId]', transdate = '$transdate', poId = '$d1[poId]', pdsId ='', modified = NOW(), mrId = $d1[uid] WHERE gateId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'gate_entry Table error') ;} 
		$extrawork = $this->gate_entry_po_extra('update', $id, $_POST['itemId'], $_POST['poqty'], $_POST['qty'] ); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_gate_entry_po_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(entrydate, '".MASKDATE."') AS entrydate, DATE_FORMAT(transdate, '".MASKDATE."') AS transdate FROM gate $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['gateId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['potype_val'] = $GLOBALS['potype'][$row['potype']];
			//$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['gate_item'] = $this->get_my_reference_array_direct("SELECT gate_item.*, itemname FROM gate_item INNER JOIN item USING(itemId) WHERE gateId = $id ORDER BY sortorder ASC", 'gateKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function po_delete($id, $filter='', $records='', $orderby='')
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
	
	public function next_gate_entry_po_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(gatenum) AS total FROM gate WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	
	
	
	public function po_approve($poId)
	{ 
		global $dbc;
		$po_to_approve = implode(',', $poId);
		$q = "UPDATE po SET postat = 1 WHERE poId IN ($po_to_approve)";
		if(mysqli_query($dbc, $q))
			return array ('status'=>true, 'myreason'=>'PO successfully approved.');	
		else
			return array ('status'=>false, 'myreason'=>'Sorry, PO could not be approved');	
	}
	
	//This function will help in the mulitpage print
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
			$rcdstat = $this->get_po_list("poId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);
		}
		//pre($out);
		return $out;
	}
	######################################## PO ends here ####################################################
	
	######################################## MRR ENTRY Starts here ####################################################
	public function get_mrr_po_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'MRR'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function mrr_po_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_mrr_po_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$mrrnum = $this->next_mrr_num();
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `mrr` (`mrrId`, `gateId`, `mrrnum`, `sesId`, `crId`, `created`) 
			VALUES (NULL, '$d1[gateId]', '$mrrnum', '$d1[csess]', '$d1[uid]', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'mrr Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->mrr_po_extra('save', $rId, $_POST['itemId'], $_POST['mrrqty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		mysqli_query($dbc,"UPDATE gate SET locked = 1 WHERE gateId = $d1[gateId] LIMIT 1");
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function mrr_po_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM mrr_item WHERE mrrId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM mrr_item WHERE mrrId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			/*$qty_received = $balance = $locked = 0;
			if(isset($po_item_old[$value])){
				$qty_received = $po_item_old[$value]['qty_received'];
				$balance = $po_item_old[$value]['balance'];
				$locked = $po_item_old[$value]['locked'];
			}*/
			
			$str[] = "('$uncode', $rId, $value, '{$qty[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `mrr_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'mrr_item Table error') ;	
		//Lock the gate entry so that it becomes non editable
		//mysqli_query($dbc,"UPDATE gate SET locked = 1 WHERE gateId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function mrr_po_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_mrr_po_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE mrr SET modified = NOW(), mrId = $d1[uid] WHERE mrrId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'mrr Table error') ;} 
		$extrawork = $this->mrr_po_extra('update', $id, $_POST['itemId'], $_POST['mrrqty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_mrr_po_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM mrr $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['mrrId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['potype_val'] = $GLOBALS['potype'][$row['potype']];
			//$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['mrr_item'] = $this->get_my_reference_array_direct("SELECT mrr_item.*, itemname FROM mrr_item INNER JOIN item USING(itemId) WHERE mrrId = $id ORDER BY sortorder ASC", 'mrrKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function mrr_delete($id, $filter='', $records='', $orderby='')
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
	
	public function next_mrr_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(mrrnum) AS total FROM mrr WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## MRR Entry ends here ####################################################
	
	######################################## MRR ENTRY Starts here ####################################################
	public function get_quality_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Quality'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function quality_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_quality_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$qualitynum = $this->next_quality_num();
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `quality` (`qualityId`, `mrrId`, `qualitynum`, `sesId`, `crId`, `created`) 
			VALUES (NULL, '$d1[eid]', '$qualitynum', '$d1[csess]', '$d1[uid]', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'quality Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->quality_extra('save', $rId, $_POST['itemId'], $_POST['qty']); 
		//$stocksave = $this->stock_save($_POST['itemId'], $_POST['qty'], $transtype = 1); 
		$stocksave = save_central_stock(1, $_POST['transdate'], $_POST['itemId'], $_POST['qty']);
		if(!$extrawork['status'] && $stocksave['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		mysqli_query($dbc,"UPDATE mrr SET locked = 1 WHERE mrrId = $d1[eid] LIMIT 1");
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function quality_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM quality_item WHERE qualityId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM quality_item WHERE qualityId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			/*$qty_received = $balance = $locked = 0;
			if(isset($po_item_old[$value])){
				$qty_received = $po_item_old[$value]['qty_received'];
				$balance = $po_item_old[$value]['balance'];
				$locked = $po_item_old[$value]['locked'];
			}*/
			
			$str[] = "('$uncode', $rId, $value, '{$qty[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `quality_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'quality_item Table error') ;	
		//Lock the gate entry so that it becomes non editable
		//mysqli_query($dbc,"UPDATE gate SET locked = 1 WHERE gateId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	// this function used to save the quantity stock in stock item table 
	// and this table use for manage the complet stock
	public function stock_save($itemId, $qty, $transtype)
	{
		global $dbc;	
		$seId = $_SESSION[SESS.'csess'];	
		$uncode = '';
		$str = array();
		foreach($itemId as $key=>$value){
				if($transtype==2)  $qty1 = '-'.$qty[$key];
				else $qty1 = $qty[$key];
			$str[] = "(NULL, $transtype, '{$itemId[$key]}', '$qty1', $seId, NOW())";
		}
	    $str = implode(', ', $str);
		$q = "INSERT INTO `stock_item` (`transId`, `transtype`, `itemId`, `qty`, `sesId`, created) VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'item_stock Table error') ;	
		//Lock the gate entry so that it becomes non editable
		//mysqli_query($dbc,"UPDATE gate SET locked = 1 WHERE gateId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function quality_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_quality_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE quality SET modified = NOW(), mrId = $d1[uid] WHERE qualityId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'quality Table error') ;} 
		$extrawork = $this->quality_extra('update', $id, $_POST['itemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_quality_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM quality $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['qualityId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['potype_val'] = $GLOBALS['potype'][$row['potype']];
			//$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['quality_item'] = $this->get_my_reference_array_direct("SELECT quality_item.*, itemname FROM quality_item INNER JOIN item USING(itemId) WHERE qualityId = $id ORDER BY sortorder ASC", 'qualityKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function quality_delete($id, $filter='', $records='', $orderby='')
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
	
	public function next_quality_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(mrrnum) AS total FROM mrr WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## QUALITY Entry ends here ####################################################
}
?>