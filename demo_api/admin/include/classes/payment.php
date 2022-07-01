<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class payment extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Payment code Starts here ####################################################	
	public function get_challan_payment_se_data()
	{  
		$d1 = $_POST;
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Payment'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function payment_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		if(!empty($d1['payment_date'])) 
                    $payment = get_mysql_date($d1['payment_date']);
                 else $payment = '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `challan_order_wise_payment` (`pay_id`, `retailer_id`, `payment_date`, `pay_amount`) VALUES (NULL , '$d1[retailer_id]', '$payment', '$d1[pay_amount]')";
               
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		$rId = mysqli_insert_id($dbc);	
                $extrawork = $this->payment_extra('save', $rId, $_POST['challan_no']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                $payment_status = $this->payment_status($_POST['challan_no']); 
		mysqli_commit($dbc);
		//Logging the history		
		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function payment_extra($actiontype, $payId, $challan_no)
	{ 
		global $dbc;
		$str = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'Update') mysqli_query($dbc, "DELETE FROM challan_order_wise_payment_details WHERE pay_id = '$payId'");	
                if(!empty($challan_no))
                {
                    foreach ($challan_no as $key=>$value)
                    {
                       $str[] = '(NULL, \''.$payId.'\', \''.$value.'\')'; 
                    }
                }
                $str = implode($str, ',');
                $q = "INSERT INTO challan_order_wise_payment_details (`ch_pay_id`, `pay_id`, `challan_no`) VALUES $str";
               
                $r = mysqli_query($dbc, $q);
                if(!$r) return array ('status'=>false, 'myreason'=>'payment Details Could not be saved Some error occurred.') ;
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	public function payment_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
                $payment = !empty($d1['payment_date']) ? get_mysql_date($d1['payment_date']) : '';
		//Checking whether the original data was modified or not
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `challan_order_wise_payment` SET `retailer_id` = '$d1[retailer_id]', `payment_date` = '$payment', pay_amount = '$d1[pay_amount]'  WHERE pay_id = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
                $rId = $id;
                $extrawork = $this->payment_extra('Update', $rId, $_POST['challan_no']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                $payment_status = $this->payment_status($_POST['challan_no']);
		mysqli_commit($dbc);
		
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_payment_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(payment_date, '%e/%b/%Y') AS payment_date FROM challan_order_wise_payment $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs))
		{
                    $id = $row['pay_id'];
                    $out[$id] = $row;
                    $out[$id]['name'] = $retailer_map[$row['retailer_id']];
                    $out[$id]['challan_details'] = $this->get_my_reference_array_direct("SELECT * FROM challan_order_wise_payment_details  WHERE pay_id = $id ", 'ch_pay_id'); 
                   
		}
		return $out;
	} 
	public function payment_status($challan_no='')
        {
            global $dbc;
            $challan_no_str = '';
            if(!empty($challan_no))
            $challan_no_str = implode(',' , $challan_no); 
            $q = "SELECT (SELECT SUM(pay_amount) AS pamt FROM challan_order_wise_payment INNER JOIN challan_order_wise_payment_details USING(pay_id) WHERE challan_no IN ($challan_no_str)) AS total_payment_value,(SELECT SUM(product_rate * ch_qty) AS total_challan_value FROM challan_order INNER JOIN challan_order_details ON challan_order_details.challan_no = challan_order.ch_no WHERE challan_order_details.challan_no IN ($challan_no_str)) AS total_challan_value FROM challan_order_wise_payment LIMIT 1";
            list($opt, $rs) = run_query($dbc, $q, 'single');
            if($opt)
            {
                if($rs['total_payment_value'] >= $rs['total_challan_value'])
                {
                    $q = "UPDATE challan_order SET dispatch_status = '2' WHERE ch_no IN ($challan_no_str)";
                    $r = mysqli_query($dbc, $q);
                }
            }
            
        }
	######################################## Plate Planner Ends here ######################################################
	public function payment_delete($id, $filter='', $records='', $orderby='')
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
	public function print_looper_payment($multiId, $options=array())
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