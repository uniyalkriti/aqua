<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class calibration extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}

	######################################## Item start here ######################################################	
	
	public function get_work_order_se_data()
	{  
		$d1 = array('order_type'=>$_POST['order_type'], 'partyId'=>$_POST['partyId'], 'party_order_no'=>$_POST['party_order_no'], 'order_date'=>$_POST['order_date'], 'order_receive_date'=>$_POST['order_receive_date'], 'order_source'=>$_POST['order_source'], 'deal_status'=>$_POST['deal_status'], 'referenceId'=>$_POST['referenceId'], 'crId'=>$_SESSION[SESS.'data']['id'], 'sesId'=>$_SESSION[SESS.'sess']['ses_id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Work Order'; //whether to do history log or not
		return array(true,$d1);
	}	
	
	public function work_order_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_work_order_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		if(!empty($d1['order_date'])) $order_date = get_mysql_date($d1['order_date']);	else $order_date='0000-00-00';	
		if(!empty($d1['order_receive_date'])) $order_receive_date = get_mysql_date($d1['order_receive_date']);	else $order_receive_date='0000-00-00';	
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$refno = $this->get_refno("SELECT MAX(internal_order_no) AS num FROM work_order");
		$q = "INSERT INTO `work_order` (`woId`, `order_type`, `partyId`, `internal_order_no`, `party_order_no`, `order_date`, `order_receive_date`, `order_source`, `deal_status`, `referenceId`, `schedule_status`, `bill_status`,`crId`,`created`,`sesId`) VALUES (NULL , '$d1[order_type]', '$d1[partyId]', $refno, '$d1[party_order_no]', '$order_date', '$order_receive_date', '$d1[order_source]', '$d1[deal_status]', '$d1[referenceId]', '0', '0', $d1[crId],NOW(),'$d1[sesId]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Work Order table error');}
		$rId = mysqli_insert_id($dbc);
		
		// saving the items received details starts
		$woistat = $this->work_order_item_save($rId, $_POST['itemId'], $_POST['qty'], $_POST['job_type']);
		if(!$woistat['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$woistat['myreason']);}
		// saving the items received details ends
		
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	//This function will save the work order item for a given work order
	public function work_order_item_save($rId, $itemId, $qty, $jobtype)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		if(empty($itemId)) return array('status'=>false, 'myreason'=>'No items were found in the order');
		//Deleting the previous items
		mysqli_query($dbc, "DELETE FROM work_order_item WHERE woId = $rId");
		
		$str = '';
		foreach($itemId as $key=>$value){
			$woi_key = $rId.'-'.$value;
			$str .= "('".$woi_key."','".$rId."','".$itemId[$key]."','".$qty[$key]."','".$jobtype[$key]."'),";
		}
		$str = rtrim($str,',');
		$q = "INSERT INTO work_order_item (`woi_key`,`woId`,`itemId`,`qty`,`job_type`) VALUES ".$str;
		$r = mysqli_query($dbc,$q);
		return $r ? array('status'=>true, 'myreason'=>'') : array('status'=>false, 'myreason'=>'work_order_item error');
	}
	
	public function work_order_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_work_order_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		if(!empty($d1['order_date'])) $order_date = get_mysql_date($d1['order_date']);	else $order_date='0000-00-00';	
		if(!empty($d1['order_receive_date'])) $order_receive_date = get_mysql_date($d1['order_receive_date']);	else $order_receive_date='0000-00-00';
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("item_id = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q = "UPDATE work_order SET order_type = '$d1[order_type]', partyId = '$d1[partyId]', party_order_no = '$d1[party_order_no]', order_date = '$order_date', order_receive_date='$order_receive_date', order_source='$d1[order_source]', deal_status='$d1[deal_status]', referenceId='$d1[referenceId]', mrId=$d1[crId], modified = NOW() WHERE woId ='$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Wotk Order table  error');}
		
		// saving the items received details starts
		$woistat = $this->work_order_item_save($id, $_POST['itemId'], $_POST['qty'], $_POST['job_type']);
		if(!$woistat['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$woistat['myreason']);}
		// saving the items received details ends
		
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['item_name'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}	
	
	public function get_work_order_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(wo.created,'%e/%b/%Y <br/> %r') AS fdated, DATE_FORMAT(wo.modified,'%e/%b/%Y <br/> %r') AS flastedit,DATE_FORMAT(wo.order_date,'%e/%b/%Y') AS order_date,DATE_FORMAT(wo.order_receive_date,'%e/%b/%Y') AS order_receive_date FROM work_order AS wo INNER JOIN party USING(partyId) INNER JOIN party_type USING(ptId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['woId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['work_order_item'] = $this->get_my_reference_array_direct("SELECT *, CONCAT_WS('-',item_name,utname) AS item_name FROM work_order_item INNER JOIN item USING(itemId) INNER JOIN units USING(utId) WHERE woId = $id", 'woi_key');
			//$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
	} 
	######################################## Item end here ######################################################	
	
	
}// class end here

?>
