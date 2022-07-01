<?php 
class po extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## PO Starts here ####################################################
	public function get_po_se_data()
	{
		//$_POST['deptId'] = 1; $_POST['prnum'] = 2; ;
 		//$_POST['itemId'] = $_POST['qty'] = range(5,9);
 		$d1 = $_POST;
		//$d1 = array('partycode'=>$_POST['partycode'], 'partyname'=>$_POST['partyname'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'fax'=>$_POST['fax'], 'contact_person'=>$_POST['contact_person'], 'website'=>$_POST['website'], 'tinno'=>$_POST['tinno'], 'ecc'=>$_POST['ecc'], 'prange'=>$_POST['prange'], 'division'=>$_POST['division'], 'adr'=>$_POST['adr'], 'locality'=>$_POST['locality'], 'landmark'=>$_POST['landmark'], 'city_district'=>$_POST['city_district'], 'state'=>$_POST['state'], 'pincode'=>$_POST['pincode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'PO'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function po_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_po_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$ponum = $this->next_po_num();
		$podate = empty($d1['podate']) ? '' : get_mysql_date($d1['podate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `po` (`poId`, `potype`, `ponum`, `podate`, `sesId`, `partyId`, `prId`, `payment`, `dispatch`, `excise`, `freight`, `loading`, `delivery`, `postat`, `crId`, `created`) 
			VALUES (NULL, '$d1[potype]', '$ponum', '$podate', '$d1[csess]', '$d1[partyId]', '$d1[prId]', '$d1[payment]', '$d1[dispatch]', '$d1[excise]', '$d1[freight]', '$d1[loading]', '$d1[delivery]', '0', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'po Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->po_extra('save', $rId, $_POST['itemId'], $_POST['prqty'],$_POST['qty'], $_POST['rate'], $d1['prId']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function po_extra($actiontype, $rId, $itemId, $prqty, $qty, $rate, $prId)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM po_item WHERE poId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM po_item WHERE poId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			$qty_received = $balance = $locked = 0;
			if(isset($po_item_old[$value])){
				$qty_received = $po_item_old[$value]['qty_received'];
				$balance = $po_item_old[$value]['balance'];
				$locked = $po_item_old[$value]['locked'];
			}
			mysqli_query($dbc, "UPDATE pr_item SET poId = $prId WHERE itemId = $value AND prId = $prId");
			$str[] = "('$uncode', $rId, $value, '{$prqty[$key]}', '{$qty[$key]}', '{$rate[$key]}', $qty_received, $balance, $locked, $key+1)";
		}
		 $str = implode(', ', $str);	
		$q = "INSERT INTO `po_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'po_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function po_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_po_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$podate = empty($d1['podate']) ? '' : get_mysql_date($d1['podate'], '/', false, false);
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE po SET potype = '$d1[potype]', podate = '$podate', partyId = '$d1[partyId]', prId ='$d1[prId]', payment = '$d1[payment]', dispatch = '$d1[dispatch]', excise = '$d1[excise]', freight = '$d1[freight]', loading = '$d1[loading]', delivery = '$d1[delivery]', modified = NOW(), mrId = $d1[uid] WHERE poId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'po Table error') ;} 
		$extrawork = $this->po_extra('update', $id, $_POST['itemId'], $_POST['prqty'],$_POST['qty'], $_POST['rate'], $d1['prId']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_po_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(podate, '".MASKDATE."') AS podate, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM po $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['poId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['potype_val'] = $GLOBALS['potype'][$row['potype']];
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['po_item'] = $this->get_my_reference_array_direct("SELECT po_item.*, itemname FROM po_item INNER JOIN item USING(itemId) WHERE poId = $id ORDER BY sortorder ASC", 'poKey'); 
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
	
	public function next_po_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(ponum) AS total FROM po WHERE sesId = {$_SESSION[SESS.'csess']}";
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
	public function po_close($id)
	{ 
		global $dbc;
		$q = "UPDATE po SET poclose = 1 WHERE poId = $id";
		if(mysqli_query($dbc, $q))
			return array ('status'=>true, 'myreason'=>'PO successfully Closed.');	
		else
			return array ('status'=>false, 'myreason'=>'Sorry, PO could not be Closed');	
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
	
	######################################## PO Material Schedule Starts here ####################################################	
	public function get_po_material_schedule_se_data()
	{
		//$_POST['deptId'] = 1; $_POST['prnum'] = 2; ;
 		//$_POST['itemId'] = $_POST['qty'] = range(5,9);
 		$d1 = $_POST;
		//$d1 = array('partycode'=>$_POST['partycode'], 'partyname'=>$_POST['partyname'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'fax'=>$_POST['fax'], 'contact_person'=>$_POST['contact_person'], 'website'=>$_POST['website'], 'tinno'=>$_POST['tinno'], 'ecc'=>$_POST['ecc'], 'prange'=>$_POST['prange'], 'division'=>$_POST['division'], 'adr'=>$_POST['adr'], 'locality'=>$_POST['locality'], 'landmark'=>$_POST['landmark'], 'city_district'=>$_POST['city_district'], 'state'=>$_POST['state'], 'pincode'=>$_POST['pincode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'PO Material Schedule'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function po_material_schedule_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_po_material_schedule_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$pdsnum = $this->next_po_delivery_schedule_num();
		$pdsdate = empty($d1['pdsdate']) ? '' : get_mysql_date($d1['pdsdate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `po_delivery_schedule` (`pdsId`, `poId`, `pdsnum`, `pdsdate`, `sesId`, `remark`, `crId`, `created`) 
			VALUES (NULL, '$d1[poId]', '$pdsnum', '$pdsdate', '$d1[csess]', '$d1[remark]', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'pds Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->po_material_schedule_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['rate']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function po_material_schedule_extra($actiontype, $rId, $itemId, $qty, $rate)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		//$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM po_delivery_schedule_item WHERE pdsId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM po_delivery_schedule_item WHERE pdsId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			/*$qty_received = $balance = $locked = 0;
			if(isset($po_item_old[$value])){
				$qty_received = $po_item_old[$value]['qty_received'];
				$balance = $po_item_old[$value]['balance'];
				$locked = $po_item_old[$value]['locked'];
			}*/
			
			$str[] = "('$uncode', $rId, $value, '{$qty[$key]}', '{$rate[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `po_delivery_schedule_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'pds_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function po_material_schedule_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_po_material_schedule_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$pdsdate = empty($d1['pdsdate']) ? '' : get_mysql_date($d1['pdsdate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		echo $q="UPDATE po_delivery_schedule SET pdsdate = '$pdsdate', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE pdsId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'pds Table error') ;} 
		$extrawork = $this->po_material_schedule_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['rate']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_po_material_schedule_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT po_delivery_schedule.*, DATE_FORMAT(pdsdate, '".MASKDATE."') AS pdsdate, DATE_FORMAT(po_delivery_schedule.created, '".MASKDATE."') AS createdf, partyId, ponum FROM po_delivery_schedule INNER JOIN po USING(poId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['pdsId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['po_delivery_schedule_item'] = $this->get_my_reference_array_direct("SELECT po_delivery_schedule_item.*, itemname FROM po_delivery_schedule_item INNER JOIN item USING(itemId) WHERE pdsId = $id ORDER BY sortorder ASC", 'pdsKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function po_material_schedule_delete($id, $filter='', $records='', $orderby='')
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
	
	public function next_po_delivery_schedule_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(pdsnum) AS total FROM po_delivery_schedule WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	
	// During edit of po_material_schedule if an item is dropped from po that might night be available in PO so 
	// using special method to incoporate the missing itemId
	public function po_material_schedule_edit_item($pdsId, $poId)
	{
		global $dbc;
		$out = array();
		$pdsitem = $this->get_my_reference_array_direct("SELECT itemId, itemname FROM po_delivery_schedule_item INNER JOIN item USING(itemId) WHERE pdsId = $pdsId ORDER BY sortorder ASC", 'itemId'); 
		foreach($pdsitem as $key=>$value) $out[$key] = $value['itemname'];
		
		$poitem = $this->get_my_reference_array_direct("SELECT itemId, itemname FROM po_item INNER JOIN item USING(itemId) WHERE poId = $poId ORDER BY sortorder ASC", 'itemId');
		foreach($poitem as $key=>$value) $out[$key] = $value['itemname'];
		return $out;
	}
	
	//This function will help in the mulitpage print
	public function print_looper_material_schedule($multiId, $options=array())
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
			$rcdstat = $this->get_po_material_schedule_list("pdsId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			$out[$id] = $rcdstat[$id];
			$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);
		}
		//pre($out);
		return $out;
	}
	/*public function get_pr_qty($id)
	{
		global $dbc;
		$out =  array();
		$qq = "SELECT * FROM po INNER JOIN pr USING(prId) INNER JOIN  pr_item USING() WHERE prkey = $id";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $out;
		return $rs['qty'];	
		
	}*/
	######################################## PO Material Schedule Ends here ####################################################
}
?>