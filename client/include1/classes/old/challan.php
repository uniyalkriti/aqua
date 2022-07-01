<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class challan extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}

	######################################## Item start here ######################################################	
	
	public function get_challan_order_se_data()
	{  
		$d1 = array('woId'=>$_POST['woId'], 'party_challan_no'=>$_POST['party_challan_no'], 'challan_date'=>$_POST['challan_date'], 'challan_receive_date'=>$_POST['challan_receive_date'],'crId'=>$_SESSION[SESS.'data']['id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Challan Order'; //whether to do history log or not
		return array(true,$d1);
	}
	
	
	public function work_order_challan_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_order_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$challan_date = $challan_receive_date = '0000-00-00';
		if(!empty($d1['challan_date'])) $challan_date = get_mysql_date($d1['challan_date']); 	
		if(!empty($d1['challan_receive_date'])) $challan_receive_date = get_mysql_date($d1['challan_receive_date']); 	
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `work_order_challan` (`wocId`, `woId`,`party_challan_no`, `challan_date` ,`challan_receive_date` ,`crId`,`created`) VALUES (NULL , '$d1[woId]', '$d1[party_challan_no]','$challan_date', '$challan_receive_date',$d1[crId],NOW())";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Challan Order table error');}
		$rId = mysqli_insert_id($dbc);
		
		// saving the items received details starts
		$woistat = $this->challan_order_item_save($rId, $_POST['itemId'], $_POST['equipno'], $_POST['itmuniqId']);
		if(!$woistat['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$woistat['myreason']);}
		// saving the items received details ends
		
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	//This function will save the work_order_challan_item for a given work_order_challan
	public function challan_order_item_save($rId, $itemId, $equipno, $itmuniqId, $actiontype='save')
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		if(empty($itemId)) return array('status'=>false, 'myreason'=>'No items were found in the order');		
		$str_arr = array();
		//Getting the ids to be updated or deleted
		$update_id = array();
		if($actiontype == 'edit'){
			list($opt, $rs) = run_query($dbc, "SELECT * FROM work_order_challan_item WHERE wocId = $rId", 'multi');
			if($opt){
				while($row = mysqli_fetch_assoc($rs)){
					$update_id[$row['itemId']][$row['itmuniqId']] = $row['itmuniqId'];
				}
			}
		}// if($actiontype == 'edit'){ ends
		foreach($itemId as $key=>$value){
			$preid = isset($update_id[$itemId[$key]][$itmuniqId[$key]]) ? $update_id[$itemId[$key]][$itmuniqId[$key]] : 'NULL';
			if($preid != 'NULL'){
				$q = "UPDATE work_order_challan_item SET `itemId` = {$itemId[$key]}, `equipno` = {$equipno[$key]} WHERE itmuniqId = $preid LIMIT 1";
				$r = mysqli_query($dbc,$q);
				unset($update_id[$itemId[$key]][$itmuniqId[$key]]);
			}else{
				$str_arr[] = "(NULL,'".$rId."','".$itemId[$key]."','".$equipno[$key]."')";
			}
		}// foreach($itemId as $key=>$value){ ends
		$str = implode(', ', $str_arr);
		$r = false;
		if(!empty($str_arr)){
			$q = "INSERT INTO work_order_challan_item (`itmuniqId`,`wocId`,`itemId`,`equipno`) VALUES ".$str;
			$r = mysqli_query($dbc,$q);
		}
		if($actiontype == 'save')
			return $r ? array('status'=>true, 'myreason'=>'') : array('status'=>false, 'myreason'=>'Challan Order item table error');
		//Deleting the leftover itmuniqId
		foreach($update_id as $key=>$value){
			foreach($value as $key1=>$value1)
				mysqli_query($dbc, "DELETE FROM work_order_challan_item WHERE itmuniqId = $value1");
		}
		return array('status'=>true, 'myreason'=>'');
	}
	
	public function work_order_challan_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_order_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		$challan_date = $challan_receive_date = '0000-00-00';
		if(!empty($d1['challan_date'])) $challan_date = get_mysql_date($d1['challan_date']);		
		if(!empty($d1['challan_receive_date'])) $challan_receive_date = get_mysql_date($d1['challan_receive_date']);	
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("item_id = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q = "UPDATE work_order_challan SET party_challan_no = '$d1[party_challan_no]',challan_date = '$challan_date', challan_receive_date = '$challan_receive_date', mrId = '$d1[crId]', modified = NOW() WHERE wocId ='$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Challan Order table  error');}
		
		// saving the items received details starts
		$woistat = $this->challan_order_item_save($id, $_POST['itemId'], $_POST['equipno'], $_POST['itmuniqId'], 'edit');
		if(!$woistat['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$woistat['myreason']);}
		// saving the items received details ends
		
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['item_name'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated');
	}	
	
	public function get_work_order_challan_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(wo.created,'%e/%b/%Y <br/> %r') AS fdated, DATE_FORMAT(wo.modified,'%e/%b/%Y <br/> %r') AS flastedit,DATE_FORMAT(wo.challan_date,'%e/%m/%Y') AS challan_date,DATE_FORMAT(wo.challan_receive_date,'%e/%m/%Y') AS challan_receive_date FROM work_order_challan AS wo INNER JOIN work_order USING(woId) INNER JOIN party USING(partyId) INNER JOIN party_type USING(ptId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['wocId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['challan_order_item'] = $this->get_my_reference_array_direct("SELECT *, CONCAT_WS('-',item_name,utname) AS item_name FROM work_order_challan_item INNER JOIN item USING(itemId) INNER JOIN units USING(utId) WHERE wocId = $id ORDER BY itemId ASC", 'itmuniqId');
			//$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
	} 
	
	public function get_item_challan_received_qty($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = NULL;		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT count(itmuniqId) as total FROM work_order_challan_item INNER JOIN work_order_challan USING(wocId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['total'];
		return $out;
	} 
	######################################## Item end here ######################################################	
	
	
}// class end here

?>
