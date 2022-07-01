<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class item extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Department Starts here ####################################################	
	public function get_department_se_data()
	{  
		$d1 = array('deptcode'=>$_POST['deptcode'], 'deptname'=>$_POST['deptname'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Department'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function deparment_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_department_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `department` (`deptId`, `deptcode`, `deptname`) VALUES (NULL , '$d1[deptcode]', '$d1[deptname]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function department_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_department_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_department_list("deptId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `department` SET `deptcode` = '$d1[deptcode]', `deptname` = '$d1[deptname]'  WHERE deptId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'department <strong>'.$d1['deptcode'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_department_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM department $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['deptId'];
			$out[$id] = $row; 
		}
		return $out;
	} 
	######################################## Department Ends here ######################################################
	
	
	######################################## Item Group Starts here ####################################################	
	public function get_item_group_se_data()
	{  
		$d1 = array('groupname'=>$_POST['groupname'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Item Group'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function item_group_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_group_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `item_group` (`groupId`, `groupname`) VALUES (NULL , '$d1[groupname]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_group table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'category <b>'.$d1['groupname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function item_group_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_group_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_item_group_list("groupId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `item_group` SET `groupname` = '$d1[groupname]' WHERE groupId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item_group table error');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['groupname'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_item_group_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM item_group $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['groupId'];
			$out[$id] = $row; 
		}
		return $out;
	} 
	######################################## Item Group Ends here ######################################################
		
	
	######################################## Item Unit Starts here ####################################################	
	public function get_item_unit_se_data()
	{  
		$d1 = array('utname'=>$_POST['utname'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Item Unit'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function item_unit_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_unit_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `item_unit` (`utId`, `utname`) VALUES (NULL , '$d1[utname]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'unit table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'unit <b>'.$d1['utname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function item_unit_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_unit_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_item_unit_list("utId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `item_unit` SET `utname` = '$d1[utname]' WHERE utId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'unit table error');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'unit <strong>'.$d1['utname'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_item_unit_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM item_unit $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['utId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
	} 
	######################################## Item Unit Ends here ######################################################	
	
	
	######################################## Item start here ######################################################		
	public function get_item_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		//$d1 = array('utId'=>$_POST['utId'], 'groupId'=>$_POST['groupId'], 'itemcode'=>$_POST['itemcode'], 'itemname'=>$_POST['itemname'], 'min_level'=>$_POST['min_level'], 'reorder_level'=>$_POST['reorder_level'], 'max_level'=>$_POST['max_level'], 'price'=>$_POST['price'], 'width'=>$_POST['width'], 'thickness'=>$_POST['thickness'], 'weight'=>$_POST['weight'], 'item_part_no'=>$_POST['item_part_no'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Item'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function item_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_item_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `item` (`itemId`, `utId`, `groupId`, `itemcode`, `itemname`, `min_level`, `reorder_level`, `max_level`, `opening_stock`, `price`, `ilength`, `iwidth`, `ithickness`, `iweight`, `icolor`, `crId`, `created`, `commodity`, `tarrif_head`) VALUES (NULL, '$d1[utId]', '$d1[groupId]', '$d1[itemcode]', '$d1[itemname]', '$d1[min_level]', '$d1[reorder_level]', '$d1[max_level]', '$d1[opening_stock]', '$d1[price]', '$d1[ilength]', '$d1[iwidth]', '$d1[ithickness]', '$d1[iweight]', '$d1[icolor]', '$d1[uid]', NOW(), '$d1[commodity]', '$d1[tarrif_head]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'item table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'item <b>'.$d1['itemname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function item_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_item_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE item SET `utId` = '$d1[utId]', `groupId` = '$d1[groupId]', `itemcode` = '$d1[itemcode]', `itemname` = '$d1[itemname]', `min_level` = '$d1[min_level]', `reorder_level` = '$d1[reorder_level]', `max_level` = '$d1[max_level]', `opening_stock` = '$d1[opening_stock]', `price` = '$d1[price]', `ilength` = '$d1[ilength]', `iwidth` = '$d1[iwidth]', `ithickness` = '$d1[ithickness]', `iweight` = '$d1[iweight]', `icolor` = '$d1[icolor]', `mrId` = '$d1[uid]', `modified` = NOW(), `commodity` = '$d1[commodity]', `tarrif_head` = '$d1[tarrif_head]' WHERE itemId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'item Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	
	public function get_item_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT item.*, groupname AS groupId_val, utname AS utId_val, DATE_FORMAT(created,'%e/%b/%Y <br/> %r') AS createdf, DATE_FORMAT(modified,'%e/%b/%Y <br/> %r') AS modifiedf FROM item INNER JOIN item_group USING(groupId) INNER JOIN item_unit USING(utId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
    // this function update the field of itemname according their code
	 public function get_unique_itemname($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$item = $this->get_item_list($filter='',  $records = '', $orderby='');
		if(!empty($item))
		{
			$inc = 1;
			foreach($item as $key=>$value)
			{
				
				if($value['itemcode'] == '') continue;
				$itemname = str_replace($value['itemcode'],'',$value['itemname']);
				$itemname = $itemname.' '.$value['itemcode'];
				$q = "UPDATE item SET itemname = '$itemname' WHERE itemId = '$value[itemId]'";
				$r = mysqli_query($dbc,$q);
				$inc++;
			}
			if($inc > 1)
				echo 'Itemname Succesfully updated';
		}
		
	}
	######################################## Item end here ######################################################	
	
	
	######################################## Process Plan start here ######################################################		
	public function get_process_plan_se_data()
	{
		$d1 = array('itemId'=>$_POST['itemId'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Process Plan'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function process_plan_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_process_plan_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$q = "INSERT INTO `process_plan` (`ppId`, `itemId`, `crId`, `created`) VALUES (NULL, '$d1[itemId]', '$d1[uid]', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreasdon'=>'process_plan Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->process_plan_extra('save', $rId, $_POST['pitemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function process_plan_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = $billamount_sum = $taxamount_sum = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM process_plan_item WHERE ppId = $rId");
		// saving the details for the stock item table
		foreach($itemId as $key=>$value){
			
			$uncode = $rId.$value;
			$str[] = "('$uncode', $rId, $value, {$qty[$key]}, $key)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `process_plan_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'process_plan_item Table error') ;		
		return array ('status'=>true,'myreason'=>'');
	}
	
    public function process_plan_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_process_plan_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE process_plan SET itemId = '$d1[itemId]', modified = NOW(), mrId = $d1[uid] WHERE ppId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'stock Table error') ;} 
		$extrawork = $this->process_plan_extra('update', $id, $_POST['pitemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'process plan <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}
	
	public function get_process_plan_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT process_plan.*, DATE_FORMAT(process_plan.created, '".MASKDATE."') AS createdf, DATE_FORMAT(process_plan.modified, '".MASKDATE."') AS modifiedf, itemname AS itemId_val FROM process_plan INNER JOIN item USING(itemId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['ppId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = isset($itemId_map[$row['itemId']]) ? $itemId_map[$row['itemId']] : ''; 
			$out[$id]['process_plan_item'] = $this->get_my_reference_array_direct("SELECT process_plan_item.*, itemname AS itemId_val FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id", 'ppiKey');
			//echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;
		
	}
	######################################## Process Plan end here ######################################################
	
	
	######################################## Nesting start here ######################################################		
	public function get_nesting_se_data()
	{
		$d1 = array('nestnum'=>$_POST['nestnum'], 'nestingcode'=>$_POST['nestingcode'], 'itemId'=>$_POST['itemId'], 'ithickness'=>$_POST['ithickness'], 'iwidth'=>$_POST['iwidth'], 'ilength'=>$_POST['ilength'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Nesting'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function nesting_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_nesting_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);	
		
		//Uploading the nesting file
		list($opt1, $filename) = fileupload('uploadfile', NESTING, array(), $maxsize = 52428800, $mandatory = false);
		$filename = $opt1 ? $filename : '';
			
		mysqli_query($dbc, "START TRANSACTION");		
		//Query to save the data
		$q = "INSERT INTO `nesting` (`nestingId`, `nestnum`, `nestingcode`, `itemId`, `ithickness`, `iwidth`, `ilength`, `filename`, `crId`, `created`) VALUES (NULL, '$d1[nestnum]', '$d1[nestingcode]', '$d1[itemId]', '$d1[ithickness]', '$d1[iwidth]', '$d1[ilength]', '$filename', '$d1[uid]', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreasdon'=>'nesting Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->nesting_extra('save', $rId, $_POST['pitemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'nesting <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function nesting_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = $billamount_sum = $taxamount_sum = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM nesting_item WHERE nestingId = $rId");
		// saving the details for the stock item table
		foreach($itemId as $key=>$value){
			
			$uncode = $rId.$value;
			$str[] = "('$uncode', $rId, $value, {$qty[$key]}, $key)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `nesting_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'nesting_item Table error') ;		
		return array ('status'=>true,'myreason'=>'');
	}
	
    public function nesting_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_nesting_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_nesting_list("nestingId = $id");
		$originaldata = $originaldata[$id];
		//pre($originaldata);
		/*$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Uploading the nesting file
		
		list($opt1, $filename) = fileupload('uploadfile', NESTING, array(), $maxsize = 52428800, $mandatory = false);
		
		$filename = !empty($filename) ? $filename : $originaldata['filename'];
		
		//echo $originaldata['filename'];
		//exit();
		//checking whether we need to delete the old file or not
		if(!empty($originaldata['filename']) && ($filename != $originaldata['filename'])){
			$oldfile = NESTING.MSYM.$originaldata['filename'];
			is_file($oldfile) ? unlink($oldfile) : '';
		}
		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE nesting SET itemId = '$d1[itemId]', ithickness = '$d1[ithickness]', iwidth = '$d1[iwidth]', ilength = '$d1[ilength]', filename = '$filename', modified = NOW(), mrId = $d1[uid] WHERE nestingId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'nesting Table error') ;} 
		$extrawork = $this->nesting_extra('update', $id, $_POST['pitemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'nesting <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}
	
	public function get_nesting_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT nesting.*, DATE_FORMAT(nesting.created, '".MASKDATE."') AS createdf, DATE_FORMAT(nesting.modified, '".MASKDATE."') AS modifiedf, itemname AS itemId_val FROM nesting INNER JOIN item USING(itemId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$itemId_map = get_my_reference_array('item', 'itemId', 'itemname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['nestingId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['itemId_val'] = isset($itemId_map[$row['itemId']]) ? $itemId_map[$row['itemId']] : ''; 
			$out[$id]['nesting_item'] = $this->get_my_reference_array_direct("SELECT nesting_item.*, itemname AS itemId_val FROM nesting_item INNER JOIN item USING(itemId) WHERE nestingId = $id ORDER BY itemname ASC", 'niKey');
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	// code start for nesting delete
	public function nesting_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "nestingId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_plate_planner_list($filter, $records, $orderby);
		if(!empty($deleteRecord)){ $out['myreason'] = 'Nesting could not be deleted.it entered in plate plannaer'; return $out;}
		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$pid = $this->get_plate_id($id);
		if(!empty($deleteRecord[$pid]['filename'])){
			$oldfile = NESTING.MSYM.$deleteRecord[$pid]['filename'];
			is_file($oldfile) ? unlink($oldfile) : '';
		}
		//Checking whether the invoice is deletable or not
		//Running the deletion queries
		$delquery = array();
		$delquery['nesting'] = "DELETE FROM nesting WHERE nestingId = $id LIMIT 1";
		$delquery['nesting_item'] = "DELETE FROM nesting_item WHERE nestingId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Nesting successfully deleted');
	}
	public function nesting_id($id)
	{
	   global $dbc;
	   $out = array();
	   $q = "SELECT nestingId FROM nesting_item WHERE itemId=$id";
	   list($opt, $rs) = run_query($dbc,$q,$mode='multi');
	   if(!$opt) return $out;
	   while($row = mysqli_fetch_assoc($rs))
	   {
		   $out[] = $row['nestingId'];
	   }
	   return $out;
	}
	######################################## nesting end here ######################################################
	public function get_plate_id($id)
	{
	   global $dbc;
	   $out = NULL;
	   $q = "SELECT pplnId FROM plate_planner WHERE nestingId=$id LIMIT 1";
	   list($opt, $rs) = run_query($dbc,$q,$mode='single');
	   if($opt) return $rs['pplnId'];
	   else return $out;
	}
	
	######################################## Plate Planner Starts here ####################################################	
	public function get_plate_planner_se_data()
	{  
		$d1 = array('nestingId'=>$_POST['nestingId'], 'itemId'=>$_POST['itemId'], 'qty'=>$_POST['qty'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Plate Planner'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function plate_planner_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_plate_planner_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `plate_planner` (`pplnId`, `nestingId`, `itemId`, `qty`) VALUES (NULL , '$d1[nestingId]', '$d1[itemId]', '$d1[qty]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'plate_planner table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'plate_planner <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function plate_planner_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_plate_planner_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_plate_planner_list("pplnId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `plate_planner` SET `nestingId` = '$d1[nestingId]', `itemId` = '$d1[itemId]', `qty` = '$d1[qty]'  WHERE pplnId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'plate_planner table error');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'plate_planner <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_plate_planner_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM plate_planner $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['pplnId'];
			$out[$id] = $row; 
			$out[$id]['itemId_val'] = myrowval('item', 'itemname', "itemId = {$row['itemId']}"); 
			$out[$id]['nestingcode'] = myrowval('nesting', 'nestingcode', "nestingId = {$row['nestingId']}");  
			$out[$id]['filename'] = myrowval('nesting', 'filename', "nestingId = {$row['nestingId']}");  
		}
		return $out;
	} 
	public function plate_planner_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		//if(empty($filter)) $filter = "itemId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		//Running the deletion queries
		$delquery = array();
		$delquery['plate_planner'] = "DELETE FROM plate_planner WHERE pplnId = $id LIMIT 1";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Plate Planner successfully deleted');
	}
	######################################## Plate Planner Ends here ######################################################
	public function item_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "itemId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_item_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Item not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		$q['PR'] = "SELECT itemId FROM pr_item WHERE itemId = ";
		$q['PO'] = "SELECT itemId FROM pur_order_item WHERE itemId = ";
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this item has been found in any one of the above query we can not delete it.				
		if($found) {$out['myreason'] = 'Item  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['stock'] = "DELETE FROM item WHERE itemId = $id LIMIT 1";
		$delquery['stock_item'] = "DELETE FROM stock_item WHERE itemId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Item successfully deleted');
	}
	public function print_looper_nesting($multiId, $options=array())
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
			$rcdstat = $this->get_nesting_list("nestingId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			//$out = $temp;
			$out[$id] = $temp;
			//$out[$id]['sf_item'] = $this->get_my_reference_array_direct("SELECT * FROM job_route_batch_item INNER JOIN item USING(itemId) WHERE jrbId = $temp[jrbId]", 'itemId');
			//$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);*/
		}
		//pre($out);
		return $out;
	}
}// class end here
?>