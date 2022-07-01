<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class billing extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Item Category Starts here ####################################################	
	public function get_billing_se_data()
	{  
		$d1 = array('billno'=>$_POST['billno'], 'billdate'=>$_POST['billdate'],'billamount'=>$_POST['billamount'],'discount'=>$_POST['discount'],'taxamount'=>$_POST['taxamount'],'otheramount'=>$_POST['otheramount'],'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'sess']['ses_id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Billing'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function billing_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_billing_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		$billdate = '0000-00-00';
		if(!empty($d1['billdate'])) $billdate = get_mysql_date($d1['billdate']); 
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `billing` (`billingId`, `billno`, `billdate`,`discount`,`taxamount`,`otheramount`, `crId`, `created`) VALUES (NULL , '$d1[billno]','$billdate', '$d1[discount]','$d1[taxamount]','$d1[otheramount]', $d1[uid],  NOW())";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Billing table error');}
		$rId = mysqli_insert_id($dbc);	
		// this function used to save billing item
		$billing_item = $this->billing_item_save($rId, $_POST['itmuniqId'], $_POST['rate']);
		if(!$billing_item['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$billing_item['myreason']);}
		// this function used to save the taxes of bill
		$billing_tax = $this->billing_tax_save($rId, $_POST['taxId'], $_POST['amount'], $_POST['taxalias'], $_POST['taxvalue']);
		if(!$billing_item['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$billing_tax['myreason']);}
		
		
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	//This function will save the billing item for a given bill
	public function billing_item_save($rId, $itmuniqId, $rate)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		if(empty($itmuniqId)) return array('status'=>false, 'myreason'=>'No items were found in the order');
		//Deleting the previous items
		mysqli_query($dbc, "DELETE FROM billing_item WHERE billingId = $rId");
		$str = '';
		foreach($itmuniqId as $key=>$value){
			$str .= "('".$rId."','".$itmuniqId[$key]."','".$rate[$key]."'),";
			$qq = mysqli_query($dbc,"UPDATE work_order_challan_item SET billingId = 1 WHERE billingId= $itmuniqId[$key]");
		}
		$str = rtrim($str,',');
		$q = "INSERT INTO billing_item (`billingId`,`itmuniqId`,`rate`) VALUES ".$str;
		$r = mysqli_query($dbc,$q);
		return $r ? array('status'=>true, 'myreason'=>'') : array('status'=>false, 'myreason'=>'billing item error');
	}
	//This function will save the billing item for a given bill
	public function billing_tax_save($rId, $taxId, $amount, $taxalias, $taxvalue)
	{
		echo 'Hiii';
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		if(empty($taxId)) return array('status'=>false, 'myreason'=>'No billing tax found');
		mysqli_query($dbc, "DELETE FROM billing_item WHERE billingId = $rId");
		$str = '';
		foreach($taxId as $key=>$value){
			$str .= "('".$taxId[$key]."','".$rId."','".$amount[$key]."','".$taxalias[$key]."','".$taxvalue[$key]."'),";
		}
		$str = rtrim($str,',');
		$q = "INSERT INTO billing_tax (`taxId`,`billingId`,`amount`, `taxalias`, `taxvalue`) VALUES ".$str;
		$r = mysqli_query($dbc,$q);
		return $r ? array('status'=>true, 'myreason'=>'') : array('status'=>false, 'myreason'=>'billing tax error');
	}
	
	public function billing_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_billing_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_billing_list("icId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION"); 	
		// query to update 		
		$q = "UPDATE `billing` SET `billno` = '$d1[billno]', `billdate` = '$d1[billdate]', `discount` = '$d1[discount]', `taxamount` = '$d1[taxamount]', `otheramount` = '$d1[otheramount]', `modified`= NOW(), `mrId`= $d1[uid] WHERE billingId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'billing table error');}
		$billing_item = $this->billing_item_save($id, $_POST['itmuniqId'], $_POST['rate']);
		if(!$billing_item['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$billing_item['myreason']);}
		// this function used to save the taxes of bill
		$billing_tax = $this->billing_tax_save($id, $_POST['taxId'], $_POST['amount'], $_POST['taxalias'], $_POST['taxvalue']);
		if(!$billing_item['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$billing_tax['myreason']);}
		if(!$billing_item['status']){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$billing_tax['myreason']);}
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'category <b>'.$d1['icname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	//This function will save the billing item for a given bill
	
	public function get_billing_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM item_category $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['icId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
	} 
	public function get_item_calibration_bill($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM  work_order INNER JOIN work_order_challan  USING(woId) INNER JOIN work_order_challan_item USING(wocId) INNER JOIN item USING(itemId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itmuniqId'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
	
	
	######################################## billing  Ends here ######################################################		
}// class end here

?>
