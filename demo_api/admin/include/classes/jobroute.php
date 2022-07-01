<?php 
class jobroute extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## WORK PO Starts here ####################################################
	public function get_jobroute_data()
	{
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Job route'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function jobroute_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_jobroute_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$count = count($_POST['process_code']);
		$str = '';
		
		for($i=0;$i<$count;$i++)
		{
			if($_POST['process_code'][$i] == '') continue;
			$str .= "(NULL, '{$_POST['fgId']}', 0, '{$_POST['process_code'][$i]}', '{$_POST['process_name'][$i]}', '{$_POST['cycle_time'][$i]}', '', NOW(), '{$d1['uid']}'), ";
		}
		$str = rtrim($str,', ');
		mysqli_query($dbc, "START TRANSACTION");	
		$q = "INSERT INTO `job_route_fg` (`jrfId`, `fgId`, `itemId`, `process_code`, `process_name`, `cycle_time`, `sortorder`, `created`, `crId`) VALUES $str ";
		$r = mysqli_query($dbc, $q);
		
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Route Master Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function jobroute_edit($id)
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_jobroute_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$rr = mysqli_query($dbc, "DELETE FROM job_route_fg WHERE fgId = $id");
		if($rr) {
			$count = count($_POST['process_code']);
			$str = '';
			
			for($i=0;$i<$count;$i++)
			{
				if($_POST['process_code'][$i] == '') continue;
				$str .= "(NULL, '{$_POST['fgId']}', 0, '{$_POST['process_code'][$i]}', '{$_POST['process_name'][$i]}', '{$_POST['cycle_time'][$i]}', '', NOW(), '{$d1['uid']}'), ";
			}
			$str = rtrim($str,', ');
			mysqli_query($dbc, "START TRANSACTION");	
			$q = "INSERT INTO `job_route_fg` (`jrfId`, `fgId`, `itemId`, `process_code`, `process_name`, `cycle_time`, `sortorder`, `created`, `crId`) VALUES $str ";
			$r = mysqli_query($dbc, $q);
			
			if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Route Master Table error') ;} 
			$rId = mysqli_insert_id($dbc);	
			mysqli_commit($dbc);
			//Logging the history
			//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
			//Final success 
			return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Updayed', 'rId'=>$rId);
		}
		return array ('status'=>false, 'myreason'=>'Route Master Table error') ;
	}
	
	public function get_jobroute_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,CONCAT_WS('=>',itemname,itemcode) AS itemname FROM job_route_fg INNER JOIN item ON item.itemId = job_route_fg.fgId  $filterstr ";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['fgId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['procesing_detail'] = $this->get_my_reference_array_direct("SELECT *,CONCAT_WS('=>',itemname,itemcode) AS itemname FROM job_route_fg INNER JOIN item ON item.itemId = job_route_fg.fgId WHERE fgId = '$id'", 'jrfId');
			
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	/*public function get_jobroute_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,CONCAT_WS('=>',itemname,itemcode) AS itemname FROM route_master INNER JOIN item USING(itemId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['rmdId'];
			$out[$id] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}*/
	########################### work for job route item process start here ############################
	public function get_item_process_data()
	{
 		$d1 = array('rmdId'=>$_POST['rmdId']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Job Route Item Process Details'; //whether to do history log or not
		return array(true,$d1);	
	}
	public function item_process_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_item_process_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		//$podate = empty($d1['podate']) ? '' : get_mysql_date($d1['podate'], '/', false, false); 
		//Start the transaction
		$count = count($_POST['itemId']);
		
		$str = '';
		for($i=0;$i<$count;$i++)
		{
			$str .= '(NULL, \''.$_POST['itemId'][$i].'\' , \''.$d1['rmdId'].'\',\''.$_POST['qty'][$i].'\'),';
		}
		$str = rtrim($str,', ');
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `route_master_item` (`rmitmId`,`itemId`,`rmdId`,`qty`) VALUES $str";
		$r = mysqli_query($dbc, $q);
		
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Route Master Item Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function item_process_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_process_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		//if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$itemId = $_POST['itemId'][0];		
		$qty = $_POST['qty'][0];		
		$q = "UPDATE  route_master_item SET qty = '$qty',itemId = '$itemId', rmdId = '$d1[rmdId]' WHERE rmitmId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Route Master item table error');}
		//Doing some extra work
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
	public function job_route_batch_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		if(!empty($_POST['itemId'])) {
			$itemId = implode(',',$_POST['itemId']);
			mysqli_query($dbc,"DELETE FROM stock_item WHERE itemId IN($itemId)");
		}
		$batch_date = !empty($_POST['batch_date']) ? get_mysql_date($_POST['batch_date']) : '';
		$q = "UPDATE  job_route_batch SET  	batchno = '{$_POST['batchno']}', batchqty = '{$_POST['batchqty']}', batch_date = '$batch_date',  fgId = '{$_POST['fgId']}' WHERE  jrbId = '$id'";
		$r = mysqli_query($dbc,$q);
		$stocksave = save_central_stock(7, $_POST['batch_date'], $_POST['itemId'], $_POST['required']);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Job route batch table error');}
		//Doing some extra work
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>'Job route successfully updated');
	}
	public function get_item_process_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		//$qr = "SELECT *, CONCAT_WS('=>',item_name,item_code) AS item_name FROM route_master_item INNER JOIN item USING(item_id) WHERE rmdId = '$_GET[option]'";
		$q = "SELECT *, CONCAT_WS('=>',itemname,itemcode) AS itemname FROM route_master_item INNER JOIN item USING(itemId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['rmitmId'];
			$out[$id] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	############################ work for batch start code #####################################
	public function get_batch_start_data()
	{
 		$d1 = array('fgId'=>$_POST['fgId'],'batchno'=>$_POST['batchno'],'batchqty'=>$_POST['batchqty'], 'batch_date'=>$_POST['batch_date']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Batch Start'; //whether to do history log or not
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		return array(true,$d1);	
	}
	public function batch_start_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_batch_start_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$batch_date = !empty($d1['batch_date']) ? get_mysql_date($d1['batch_date']) : '';
		//checking  for sf stock
		/*if(!empty($d1['batchqty'])) {
			$q_fg = "SELECT process_plan_item.* FROM process_plan INNER JOIN process_plan_item USING(ppId) WHERE process_plan.itemId = {$d1['fgId']}";
			list($opt,$rs)= run_query($dbc, $q_fg, $mode='multi',$msg='');
			while($row = mysqli_fetch_assoc($rs)){
			    	$qty = $row['qty']*$d1['batchqty'];
					$q_qty = "SELECT SUM(qty) as qty FROM stock_item WHERE itemId = {$row['itemId']}";
					list($opt1,$rs1)= run_query($dbc, $q_qty, $mode='single',$msg='');
					if(!$opt1) $stock_qty = 0;
					// checking the SF stock and comparing to required stock
					if($qty > $rs1['qty']) {
						return array('status'=>false, 'myreason'=>"Sorry, you can't be processed your process, due to unavalability of SF stock");
					}
			}
		}*///checking  for sf stock end here
			
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `job_route_batch` (`jrbId`, `sesId`, `batchno`, `batchqty`, `batch_finish`, `fgId`, `batch_date`, `created`) VALUES (NULL, '{$d1['csess']}', '$d1[batchno]', '{$d1['batchqty']}', '', '{$d1['fgId']}', '$batch_date', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Batch Route Table error') ;} 
		$rId = mysqli_insert_id($dbc);
		$q_fg = "SELECT process_plan_item.* FROM process_plan INNER JOIN process_plan_item USING(ppId) WHERE process_plan.itemId = {$d1['fgId']}";
		list($opt3,$rs3)= run_query($dbc, $q_fg, $mode='multi',$msg='');
		$str = '';
		while($rows = mysqli_fetch_assoc($rs3))
		{
			$qty = $rows['qty']*$d1['batchqty'];
			$str.= "('$rId', '{$rows['itemId']}', '$qty'), ";
		}
		$str = rtrim($str,', ');
		// insert data rout process table
		$q2="INSERT INTO `job_route_batch_item` (`jrbId`, `itemId`, `qty`) VALUES $str";
		$ex = mysqli_query($dbc,$q2);
		$stocksave = save_central_stock(7, $_POST['batch_date'], $_POST['itemId'], $_POST['required']);
		if(!$ex) {mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Batch Route Table error') ;} 	
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function batch_start_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_batch_start_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//$batch_date = !empty($d1['batch_date']) ? get_mysql_date($d1['batch_date']) : '';
		//Checking whether the original data was modified or not
		//if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q = "UPDATE  route_batch SET qty = '$d1[qty]',itemId = '$d1[itemId]', batchno = '$d1[batchno]' WHERE rbId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Route Batch table error');}
		//Doing some extra work
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
	// route process update start here
	public function get_route_process_data()
	{
 		$d1 = array('jrbId'=>$_POST['jrbId'],'process_code'=>$_POST['process_code'],'process_name'=>$_POST['process_name'], 'start_time'=>$_POST['start_time'],'end_time'=>$_POST['end_time'],'in_qty'=>$_POST['in_qty'],'out_qty'=>$_POST['out_qty'], 'sortorder'=>$_POST['sortorder'], 'remark'=>$_POST['remark'],'fgId'=>$_POST['fgId']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Batch Start'; //whether to do history log or not
		return array(true,$d1);	
	}
	public function route_process_edit()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_route_process_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		$start = !empty($d1['start_time']) ? getmysqltime($d1['start_time']) : '';
		$end = !empty($d1['end_time'])  ? getmysqltime($d1['end_time']) : '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q = "INSERT INTO `job_route_batch_progress` (`jrbId`, `process_code`, `process_name`, `in_qty`, `out_qty`, `remark`, `start_time`, `end_time`, `is_completed`, `sortorder`) VALUES ('{$d1['jrbId']}' , '{$d1['process_code']}', '{$d1['process_name']}', '{$d1['in_qty']}', '{$d1['out_qty']}', '{$d1['remark']}', '$start', '$end', '1', '{$d1['sortorder']}');";
		$r = mysqli_query($dbc,$q);
		
		$qry = "SELECT COUNT(fgId) as tot FROM job_route_fg WHERE fgId = '{$d1['fgId']}'";
		list($opt3,$rs3)= run_query($dbc, $qry, $mode='single',$msg='');
		if($rs3['tot']== $d1['sortorder']) {
			//$gate = new gate_entry();
			$itmeId = array(0=>$d1['fgId']);
			$qty = array(0=>$d1['out_qty']);
			$stocksave = save_central_stock(6, '', $itmeId, $qty);
			//$stocksave = $gate->stock_save($itmeId, $qty, $transtype = 5);
			$qp = "UPDATE job_route_batch SET batch_finish = 1, batch_output_qty = {$d1['out_qty']} WHERE jrbId = {$d1['jrbId']}";
			mysqli_query($dbc,$qp);
		}
		//if()
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Route Batch table error');}
		//Doing some extra work
		mysqli_commit($dbc);
		//Saving the user modification history
		//	$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['icname'].'</strong> With RefCode :'.$id);
		//	$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}
	public function get_batch_start_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		//$qr = "SELECT *, CONCAT_WS('=>',item_name,item_code) AS item_name FROM route_master_item INNER JOIN item USING(item_id) WHERE rmdId = '$_GET[option]'";
		$q = "SELECT *,CONCAT_WS('=>',itemname,itemcode) AS itemname, DATE_FORMAT(job_route_batch.created,'%d/%m/%Y') as fdated,DATE_FORMAT(job_route_batch.batch_date,'%d/%m/%Y') as batch_date FROM job_route_batch INNER JOIN item ON item.itemId = job_route_batch.fgId  $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['jrbId'];
			$fgId = $row['fgId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['fdated'] = $row['fdated'];
			$out[$id]['sf_detail'] = $this->get_my_reference_array_direct("SELECT *,CONCAT_WS('=>',itemname,itemcode) AS itemname FROM job_route_fg INNER JOIN job_route_batch   USING(fgId) INNER JOIN  item ON item.itemId = job_route_fg.fgId WHERE fgId = '$fgId'", 'jrfId');
			
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	// this function used for showing the process planing detail
	public function get_process_planning_item($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out= array();
		list($status,$d1)= $this ->get_batch_start_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//checking  for sf stock
		if(!empty($d1['batchqty'])) {
		$q_fg = "SELECT * FROM process_plan INNER JOIN item USING(itemId) WHERE process_plan.itemId = {$d1['fgId']}";
			list($opt,$rs)= run_query($dbc, $q_fg, $mode='multi',$msg='');
			if(!$opt) { return $out; } //return array('status'=>false, 'myreason'=>'Sorry, process plan not available, please make first process plan.'); exit();}
			while($row = mysqli_fetch_assoc($rs)){
				    $ppId = $row['ppId'];
					$id = $row['itemId']; 
					$out[$id] = $row; 
					
					$out[$id]['process_plan'] = $this->get_my_reference_array_direct("SELECT * FROM  process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $ppId", 'ppiKey');
					$q_fg1 = "SELECT * FROM process_plan_item WHERE ppId = $ppId";
			        list($opt,$rs2)= run_query($dbc, $q_fg1, $mode='multi',$msg='');
					while($row1 = mysqli_fetch_assoc($rs2)){
						$ppkey = $row1['ppiKey'];
						$stock = new stock();
						$stock_qty = get_central_stock($row1['itemId']);
						/*$q_qty = "SELECT SUM(qty) as qty FROM stock_item WHERE itemId = {$row1['itemId']}";
						list($opt1,$rs1)= run_query($dbc, $q_qty, $mode='single',$msg='');
						if(!$opt1) $stock_qty = '';*/
						$qty = $row1['qty']*$d1['batchqty'];
						//$row1[$id]['ppiKey'] = 
						$out[$id]['process_plan'][$ppkey]['stock_qty'] = $stock_qty;
						$out[$id]['process_plan'][$ppkey]['required'] = $qty;
						if($qty > $stock_qty) {
							$out[$id]['qty_chk'] = false; 
							$out[$id]['process_plan'][$ppkey]['showicon'] =  '../icon-system/i16X16/no.png';
						
						} else { 
							$out[$id]['qty_chk'] = true;
							$out[$id]['process_plan'][$ppkey]['showicon'] =  '../icon-system/i16X16/yes.png';
						}
					}
			}
			return $out;
		}
		
	}
	public function get_planner_item($jrbId)
	{
		global $dbc;
		$out = array();
		$q = "SELECT * FROM process_plan INNER JOIN process_plan_item USING(ppId) WHERE process_plan.itemId = $jrbId";
		list($opt,$rs)= run_query($dbc, $q, $mode='multi',$msg='');
		if(!$opt)return $out; 
		$itemname = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['itemId'];
			$out[$id]['itemname'] = $itemname[$row['itemId']];
			$out[$id] = $row;
		}
		pre($out);
		return $out;
	}
	
	public function print_looper_invoice($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		$item = new item();
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_batch_start_list("jrbId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			//$out = $temp;
			$out[$id] = $temp;
			$out[$id]['sf_item'] = $this->get_my_reference_array_direct("SELECT * FROM job_route_batch_item INNER JOIN item USING(itemId) WHERE jrbId = $temp[jrbId]", 'itemId');
			//$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);*/
		}
		//pre($out);
		return $out;
	}
	
}

?>