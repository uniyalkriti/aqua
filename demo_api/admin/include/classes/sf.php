<?php 
class sf extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## WORK PO Starts here ####################################################
	public function get_sf_se_data()
	{
 		$d1 = array('raw'=>$_POST['raw'],'stock'=>$_POST['stock'],'used'=>$_POST['used'],'branch_id'=>1);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'SF Entry'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function sf_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_sf_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		//$ponum = $this->next_po_num();
		$podate = empty($d1['podate']) ? '' : get_mysql_date($d1['podate'], '/', false, false); 
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `sf` (`sf_id`, `raw_material_id`, `use_issued_qty`, `user_id`, `branch_id`, `date`, `ses_id`) VALUES (NULL, '$d1[raw]', '$d1[used]', '$d1[uid]', '$d1[branch_id]', Now(),'$d1[csess]');";
		
		$r = mysqli_query($dbc, $q);
		
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'SF Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->sf_extra('save', $rId, $_POST['item'], $_POST['qty'], $_POST['ac_qty'],$_POST['hid']);
		//$gate = new gate_entry();
		//$stocksave = $gate->stock_save($_POST['hid'], $_POST['ac_qty'], $transtype = 4); 
		$stocksave = save_central_stock(4, '', $_POST['hid'], $_POST['qty']);
		if(!$extrawork['status'] && !$stocksave['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function sf_extra($actiontype, $rId, $itemId, $qty, $ac_qty,$hid)
	{ 
		global $dbc;		
		$uncode = '';
		$str = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		//$po_item_old = $this->get_my_reference_array_direct("SELECT * FROM work_po_item WHERE wpoId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		//if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM work_po_item WHERE wpoId = $rId");
		
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			//To save the value of the other columns as some columns are affected by po
			
			$str[] = "(NULL,$rId, '{$hid[$key]}', '{$ac_qty[$key]}')";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `sf_item` (`sf_item_id` ,`sf_id` ,`itemId` ,`qty`) VALUES $str";
		$r = mysqli_query($dbc, $q);
		if($r)
		{
			$q3 = "INSERT INTO `item_partial_used` (`ipuId`,`itemId`,`created`,`used_status`) VALUES (NULL,'$_POST[raw]',NOW(),'$_POST[used_status]')";
			$r3 = mysqli_query($dbc,$q3);
		}
		if(!$r) return array ('status'=>false, 'myreason'=>'sf_item Table error') ;	
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
			$out[$id]['work_po_item'] = $this->get_my_reference_array_direct("SELECT work_po_item.*, itemname FROM work_po_item INNER JOIN item USING(itemId) WHERE wpoId = $id ORDER BY sortorder ASC", 'itemId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_sf_stock($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$sessId = $_SESSION[SESS.'csess'];
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q ="SELECT * FROM sf INNER JOIN sf_item USING(sf_id) INNER JOIN item USING(itemId) WHERE groupId = 2";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
		
			$id = $row['itemId'];
			$out[$id] = $row;
			$out[$id]['stock_qty'] = get_central_stock($row['itemId']);
		}
		return $out;
	}
	public function save_sf_partial()
	{
		global $dbc;
		$out = array();
		$csess = $_SESSION[SESS.'csess'];	
		$uid = $_SESSION[SESS.'id'];
		if(!empty($_POST['raw_material_id']) && !empty($_POST['used']))
		{
			$count = count($_POST['sf_item_id']);
			$count;
			$q = "INSERT INTO `sf` (`sf_id` ,`raw_material_id` ,`user_id` ,`branch_id` ,`date`, `use_issued_qty`, `ses_id`)VALUES (NULL , '$_POST[raw_material_id]' , '$uid', '', NOW(), '$_POST[used]', '$csess')";
			$r = mysqli_query($dbc,$q);
			$id = mysqli_insert_id($dbc);
			$str = '';
			$flag = 0;
			$input = array();
			for($i=0;$i<$count;$i++)
			{
				//$str .= '(\'NULL\', '.$id.' , '.$_POST['hid'][$i].' , '.$_POST['ac_qty'][$i]*$_POST['used'].'), ';
				if(empty($_POST['sf_item_id'][$i])) continue;
				$input[$_POST['sf_item_id'][$i]] = $_POST['ac_qty'][$i];
				$str .= '(\'NULL\', '.$id.' , '.$_POST['sf_item_id'][$i].' , '.$_POST['ac_qty'][$i].'), ';
			}
			$str = rtrim($str , ', ');
			if($r)
			{
				$this->qty_updater($_POST['raw_material_id'], $_POST['used']);
				$q1 = "INSERT INTO `sf_item` (`sf_item_id` ,`sf_id` ,`itemId` ,`qty`) VALUES $str";
				$r1 = mysqli_query($dbc,$q1);
				if($_POST['used_status']==0) {
					$q2 = "INSERT INTO item_partial_used (`ipuId`, `itemId`, `created`, `used_status`) VALUES (NULL, '$_POST[raw_material_id]', NOW(), '$_POST[used_status]')";
					$r2 = mysqli_query($dbc,$q2);
				}
				
				
				/*$gate = new gate_entry();
				$stocksave = $gate->stock_save($_POST['sf_item_id'], $_POST['ac_qty'], $transtype = 6); */
				$stocksave = save_central_stock(5, '', $_POST['sf_item_id'], $_POST['ac_qty']);
				if(!$r){ return array ('status'=>false, 'myreason'=>'sf_item Table error') ;} 
				return array ('status'=>true,'myreason'=>'SF Partial Sucessfuly inserted');
			}
		}
		return array ('status'=>false,'myreason'=>'Please filled mandatory');
	}
	public function qty_updater($item_id, $qty)
		{
			global $dbc;
			list($opt, $rs)	= run_query($dbc, "SELECT ptrqId, used, balance FROM item_partial_return_qty WHERE itemId = $item_id AND balance > 0", 'multi');
			while($row = mysqli_fetch_assoc($rs)){
				if($row['balance'] >= $qty ){
					mysqli_query($dbc, "UPDATE item_partial_return_qty SET used = (used+$qty), balance = (balance - $qty) WHERE ptrqId = $row[ptrqId]");
					break;	
				}else{
					$qtyupdatable = $row['balance'];
					$qty -= $qtyupdatable;
					mysqli_query($dbc, "UPDATE item_partial_return_qty SET used = (used+$qtyupdatable), balance = (balance - $qtyupdatable) WHERE ptrqId = $row[ptrqId]");
				}
			}	
		}
	
}
?>