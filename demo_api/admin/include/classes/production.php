<?php 
class production extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## WORK PO Starts here ####################################################
	public function get_work_po_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'WORK PO'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function work_po_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_work_po_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		$podate = empty($d1['podate']) ? '' : get_mysql_date($d1['podate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `work_po` (`wpoId`, `sesId`, `partyId`, `vatId`, `ponum`, `podate`, `wpostatus`, `remark`, `crId`, `created`) 
			VALUES (NULL, '$d1[csess]', '$d1[partyId]', '$d1[vatId]', '$d1[ponum]', '$podate', '1', '$d1[remark]', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_po Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->work_po_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['rate'], $_POST['lineno']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function work_po_extra($actiontype, $rId, $itemId, $qty, $rate, $lineno)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM work_po_item WHERE wpoId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM work_po_item WHERE wpoId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "($rId, $value, '{$qty[$key]}', '{$rate[$key]}', '{$lineno[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `work_po_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'work_po_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function work_po_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_work_po_se_data();
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
		$q="UPDATE work_po SET partyId = '$d1[partyId]', vatId = '$d1[vatId]', ponum = '$d1[ponum]', podate = '$podate', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE wpoId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_po Table error') ;} 
		$extrawork = $this->work_po_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['rate'], $_POST['lineno']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_work_po_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(podate, '".MASKDATE."') AS podate, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM work_po $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wpoId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['work_po_item'] = $this->get_my_reference_array_direct("SELECT work_po_item.*, itemname FROM work_po_item INNER JOIN item USING(itemId) WHERE wpoId = $id ORDER BY itemname ASC", 'itemId'); 
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
	
	public function next_po_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(ponum) AS total FROM po WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## WORK PO ends here ####################################################
	
	######################################## WORK DELIVERY ORDER Starts here ####################################################
	public function get_work_delivery_order_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'WORK DELIVERY ORDER'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function work_delivery_order_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_work_delivery_order_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//next_do_num()
		//Manipulation and value reading
		$donum = empty($d1['donum']) ? $this->next_do_num() : $d1['donum'];
		$dodate = empty($d1['dodate']) ? '' : get_mysql_date($d1['dodate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `work_delivery_order` (`wdoId`, `wpoId`, `sesId`, `partyId`, `dodate`, `donum`, `remark`, `crId`, `created`) 
			VALUES (NULL, '$d1[wpoId]', '$d1[csess]', '$d1[partyId]', '$dodate', '$donum', '$d1[remark]', '$d1[uid]', NOW());";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_delivery_order Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->work_delivery_order_extra('save', $rId, $_POST['itemId'], $_POST['qty'], $_POST['lineno']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function work_delivery_order_extra($actiontype, $rId, $itemId, $qty, $lineno)
	{ 
		global $dbc;
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM work_delivery_order_item WHERE wdoId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM work_delivery_order_item WHERE wdoId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//To save the value of the other columns as some columns are affected by po
			$qty_schedule = 0;
			$balance = $qty[$key];
			
			if($actiontype == 'update'){
				$qty_schedule = $this->get_qty_scheduled($rId, $value);
				$balance = $balance - $qty_schedule;
				
			}
			$str[] = "($rId, $value, '{$qty[$key]}', $qty_schedule, $balance, '{$lineno[$key]}', $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `work_delivery_order_item` (`wdoId`, `itemId`, `qty`, `qty_scheduled`, `balance`,`lineno`, `sortorder`) VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'work_delivery_order_item Table error') ;	
		//Update the qty in the database 
		//mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function work_delivery_order_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_work_delivery_order_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Manipulation and value reading
		$dodate = empty($d1['dodate']) ? '' : get_mysql_date($d1['dodate'], '/', false, false);
		//Start the transaction
	
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE work_delivery_order SET wpoId = '$d1[wpoId]', partyId = '$d1[partyId]', dodate = '$dodate' , remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE wdoId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_delivery_order Table error') ;} 
		$extrawork = $this->work_delivery_order_extra('update', $id, $_POST['itemId'], $_POST['qty'], $_POST['lineno']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_work_delivery_order_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(dodate, '".MASKDATE."') AS dodate, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM work_delivery_order $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		$wpoId_map = get_my_reference_array('work_po', 'wpoId', 'ponum'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wdoId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['wpoId_val'] = $wpoId_map[$row['wpoId']];
			$out[$id]['work_delivery_order_item'] = $this->get_my_reference_array_direct("SELECT work_delivery_order_item.*,DATE_FORMAT(do_item_date,'%d/%m/%Y') AS do_item_date, itemname FROM work_delivery_order_item INNER JOIN item USING(itemId) WHERE wdoId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function work_delivery_order_delete($id, $filter='', $records='', $orderby='')
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
	######################################## WORK DELIVERY ORDER ends here ####################################################
	public function next_do_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(donum) AS total FROM work_delivery_order WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	######################################## PRODUCTION SCHEDULE starts here ####################################################
	public function get_qty_scheduled($wdoId, $itemId)
	{
		global $dbc;
		$out = 0;
		$q = "SELECT SUM(qty) AS qty FROM work_production_schedule WHERE wdoId  = $wdoId AND itemId = $itemId";
		list($opt, $rs) = run_query($dbc, $q);
		return (int)$rs['qty'];
	}
	
	public function get_scheduled_item_metadata($wdoId, $itemId)
	{
		global $dbc;
		$out = array();
		$q = "SELECT partyId, wdoId, wpoId, donum, itemId, qty, DATE_FORMAT(dodate, '".MASKDATE."') AS dodate FROM work_delivery_order INNER JOIN work_delivery_order_item USING(wdoId) WHERE wdoId = $wdoId AND itemId =  $itemId";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wdoId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyname'] = myrowval('party', 'partyname', "partyId = {$row['partyId']}");
			$out[$id]['ponum'] = myrowval('work_po', 'ponum', "wpoId = {$row['wpoId']}");
			$out[$id]['itemname'] = myrowval('item', 'itemname', "itemId = {$row['itemId']}");
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	//To get the detail of the  schedule date against an item of give delivery order
	public function get_scheduled_item_detail($wdoId, $itemId)
	{
		global $dbc;
		$out = array();
		$q = "SELECT itemId, qty, wpsId, schedule_complete, qty, DATE_FORMAT(schedule_date, '".MASKDATE."') AS schedule_date FROM work_production_schedule WHERE wdoId = $wdoId AND itemId =  $itemId ORDER BY schedule_complete DESC, schedule_date ASC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wpsId'];
			$out[$id] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function work_production_schedule_save()
	{ 
		global $dbc;
		$rId = $_POST['wdoId'];
		$itemId = $_POST['itemId'];
		$wpsId = $_POST['wpsId'];
		$qty = $_POST['qty'];
		$schedule_date =  $_POST['schedule_date'];
		$str = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		//$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM work_production_schedule WHERE wpsId = $rId", 'wpsId');
		//during update we are required to remove the previous entry
		mysqli_query($dbc, "DELETE FROM work_production_schedule WHERE wdoId = $rId AND schedule_complete = 0 AND itemId = '$itemId'");
		
		foreach($schedule_date as $key=>$value){
			
			if(empty($value)) continue;
			$dodate = get_mysql_date($value, '/', false, false);
			
			$str[] = "(NULL, $rId, '{$_SESSION[SESS.'csess']}', '$itemId', '{$qty[$key]}', '$dodate', 0, '')";
		}
		$str = implode(', ', $str);	
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "INSERT INTO `work_production_schedule` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'work_production_schedule Table error') ;
		$doneqty = $this->get_qty_scheduled($rId, $itemId);
		
		if(!mysqli_query($dbc,"UPDATE work_delivery_order_item SET qty_scheduled = $doneqty, balance = qty-$doneqty WHERE wdoId = $rId AND itemId = $itemId LIMIT 1"))
			return array ('status'=>false, 'myreason'=>'Production schedule could not be saved') ;
		//Commit the transaction
		mysqli_commit($dbc);
		return array ('status'=>true, 'myreason'=>'Production schedule successfully Saved') ;	
	}
	//List of item that have not been scheduled for production
	public function work_production_item_unscheduled()
	{ 
		global $dbc;
		$out = array();
		$q = "SELECT work_delivery_order_item.*, donum, DATE_FORMAT(dodate, '".MASKDATE."') AS dodatef FROM work_delivery_order_item INNER JOIN work_delivery_order USING(wdoId) WHERE balance != 0 ORDER BY dodate ASC, qty ASC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$inc = 1;
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $inc;
			$out[$id] = $row; // storing the item id
			$out[$id]['inc'] = $inc;
			$out[$id]['itemId_val'] = $itemId_map[$row['itemId']];
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	//List of item that have been scheduled for production or items with batch finished
	public function work_production_item_scheduled($schedule_complete=0)
	{ 
		global $dbc;
		$out = array();
		$q = "SELECT work_production_schedule.*, donum, DATE_FORMAT(dodate, '".MASKDATE."') AS dodatef, DATE_FORMAT(schedule_date, '".MASKDATE."') AS schedule_datef , DATE_FORMAT(finish_date, '".MASKDATE."') AS finish_datef FROM work_production_schedule INNER JOIN work_delivery_order USING(wdoId) WHERE  schedule_complete = $schedule_complete ORDER BY dodate ASC, qty ASC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$inc = 1;
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wpsId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = $itemId_map[$row['itemId']];
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function work_production_batch_delete($wpsId)
	{ 
		global $dbc;
		$out = array();
		//Set batch finished to unfinish
		mysqli_query($dbc, "UPDATE work_production_schedule SET schedule_complete = 0 WHERE wpsId = $wpsId LIMIT 1");
		//Delete the details of the item consumed
		$q = "DELETE FROM work_schedule_item_consumed WHERE wpsId = $wpsId";
		mysqli_query($dbc, $q);
		return array('status'=>true, 'myreason'=>'Batch successfully moved');	
	}
	
	public function get_item_consumed_detail($wpsId, $qty)
	{ 
		/*return array(1=>array('itemId'=>'1', 'itemname'=>'BB', 'qtyinstock'=>'21', 'qtyconsumed'=>2),
					 2=>array('itemId'=>'2', 'itemname'=>'YY', 'qtyinstock'=>'3', 'qtyconsumed'=>4),
					 );*/
		global $dbc;
		$out = array();
		$q = "SELECT *, process_plan_item.qty as qtyconsumed FROM process_plan INNER JOIN process_plan_item USING(ppId) INNER JOIN item ON process_plan_item.itemId = item.itemId WHERE process_plan.itemId = '$wpsId'";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['ppiKey'];
			$out[$id] = $row; 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;
		//Set batch finished to unfinish
		//mysqli_query($dbc, "UPDATE work_production_schedule SET schedule_complete = 0 WHERE wpsId = $wpsId LIMIT 1");
		//Delete the details of the item consumed
		//$q = "DELETE FROM work_schedule_item_consumed WHERE wpsId = $wpsId";
		//mysqli_query($dbc, $q);
		//return array('status'=>true, 'myreason'=>'Batch successfully moved');	
	}
	
	public function get_work_production_schedule_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(schedule_date, '".MASKDATE."') AS schedule_datef, DATE_FORMAT(finish_date, '".MASKDATE."') AS finish_datef FROM work_production_schedule $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['wpsId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = $itemId_map[$row['itemId']];
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	// here we get month wise scheduling list of po
	public function get_month_wise_schedule_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,  DATE_FORMAT(dodate, '".MASKDATE."') AS dodatef FROM work_production_schedule INNER JOIN work_delivery_order USING(wdoId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$partyId_map = get_my_reference_array('party', 'partyId', 'partyname'); 
		$inc = 1;
		while($row = mysqli_fetch_assoc($rs)){
			//$id = $row['wpsId'];
			$id = $inc;
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $partyId_map[$row['partyId']];
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function work_production_batch_finish()
	{
		global $dbc;
		$d1 = $_POST;
		$wpsId = $_POST['wpsId'];
		$itemId = $_POST['itemIdc'];
		$qty = $_POST['reqqty'];
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		if(mysqli_query($dbc, "UPDATE work_production_schedule SET schedule_complete = 1 WHERE wpsId = {$d1['wpsId']} LIMIT 1")){
			$str = array();
			foreach($itemId as $key=>$value)
				$str[] = "('$wpsId', $value, '{$qty[$key]}')";
			$str = implode(', ', $str);	
			$q = "INSERT INTO `work_schedule_item_consumed` VALUES $str";
			$r = mysqli_query($dbc, $q);
			if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'work_schedule_item_consumed Table error') ;}			
		}
		else
			return array ('status'=>false, 'myreason'=>'work_production_schedule Table error');
		mysqli_commit($dbc);	
		return array ('status'=>true, 'myreason'=>'Batch Successfullly finished') ;	
	}
	######################################## PRODUCTION SCHEDULE ends here ####################################################
	######################################## PRODUCTION SCHEDULE ends here ####################################################
	public function get_plate_planning_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		//, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM work_delivery_order_item INNER JOIN item USING(itemId)
		$q = "SELECT *  FROM work_delivery_order_item INNER JOIN work_delivery_order USING(wdoId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		$inc = 1;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $inc;
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = $itemId_map[$row['itemId']];
			$q="SELECT ppiKey, process_plan_item.itemId,itemname,groupId FROM process_plan_item INNER JOIN process_plan USING(ppId) INNER JOIN item ON item.itemId = process_plan_item.itemId WHERE process_plan.itemId=$row[itemId]";
			$out[$id]['consumed_item'] = $this->get_my_reference_array_direct($q, $primarykey='ppiKey');
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_nesting_plate_item($sfgitem)
	{
		global $dbc;
		$out =NULL;
		//$filterstr=$this->oo_filter($filter, $records, $orderby);
		//, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM work_delivery_order_item INNER JOIN item USING(itemId)
		$q = "SELECT qty,nestingcode,itemname AS raw_plate FROM plate_planner INNER JOIN nesting USING(nestingId) INNER JOIN item ON item.itemId = plate_planner.itemId WHERE plate_planner.itemId = $sfgitem LIMIT 1 ";
		//$q = "SELECT nestingcode,qty,itemname AS raw_plate FROM nesting_item INNER JOIN nesting USING(nestingId) INNER JOIN item ON item.itemId = nesting.itemId  WHERE nesting_item.itemId = '$sfgitem' LIMIT 1";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $out;
		//pre($rs);
		return $rs;
	}
	public function get_stock($id)
	{
		global $dbc;
		$rs =NULL;
		//$filterstr=$this->oo_filter($filter, $records, $orderby);
		//, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM work_delivery_order_item INNER JOIN item USING(itemId)
		$q = "SELECT SUM(qty) AS tot FROM stock_item WHERE itemId = '$id'";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $rs;
		//pre($rs);
		return $rs;
	}
	public function get_plate_planning_list1($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		//, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM work_delivery_order_item INNER JOIN item USING(itemId)
		
		$q = "SELECT *, DATE_FORMAT(do_item_date, '".MASKDATE."') AS dodatef FROM work_delivery_order_item $filterstr";
		
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		$inc = 1;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $inc;
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = $itemId_map[$row['itemId']];
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_consumed_item($fgitem)
	{
		global $dbc;
		$out = array();
		//$filterstr=$this->oo_filter($filter, $records, $orderby);
		//, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM work_delivery_order_item INNER JOIN item USING(itemId)
		$q = "SELECT ppId FROM process_plan  WHERE itemId = $fgitem";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $out;
		$qq = "SELECT *,itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId='$rs[ppId]' ";
		list($opt1,$rs1)= run_query($dbc,$qq,$mode='multi',$msg=''); 
		$inc =1;
		while($row = mysqli_fetch_assoc($rs1)){
			$id = $inc;
			$out[$id] = $row; // storing the item id
			$inc++;
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
}
?>