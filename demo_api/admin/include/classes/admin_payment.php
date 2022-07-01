<?php
// This class will handle all the task related to retailer payment 
class admin_payment extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}
	######################################## Admin Payment code Starts here ####################################################	
	public function get_admin_payment_se_data()
	{  
		$d1 = $_POST;
                $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Payment'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function admin_payment_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_admin_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
                if(!empty($d1['pay_date'])) $payment = get_mysql_date($d1['pay_date']);
                else $payment = '';  
                $cheque_date = !empty($d1['cheque_date']) ? get_mysql_date($d1['cheque_date']) : '';
                $id = $d1['uid'].date('Ymdhis');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
                $q = "INSERT INTO `payment_enrollment` (`id`, `dealer_id`, `user_id`, `location_id`, `retailer_id`, `pay_mode`, `amount`, `bank_name`, `cheque_number`, `cheque_date`, `pay_time`, `pay_date`) VALUES ('$id', '$d1[dealer_id]', '$d1[uid]', '$d1[location_id]', '$d1[retailer_id]', '$d1[pay_mode]', '$d1[amount]', '$d1[bank_name]', '$d1[cheque_number]', '$cheque_date', NOW(), '$payment');";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function admin_payment_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_admin_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
                $payment = !empty($d1['pay_date']) ? get_mysql_date($d1['pay_date']) : '';
		//Checking whether the original data was modified or not
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//query to update 		
		$q = "UPDATE `payment_enrollment` SET `retailer_id` = '$d1[retailer_id]', `pay_date` = '$payment', amount = '$d1[amount]', dealer_id = '$d1[dealer_id]', location_id = '$d1[location_id]', pay_mode = '$d1[pay_mode]', bank_name = '$d1[bank_name]'  WHERE id = '$id'";
               
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
                $rId = $id;
		mysqli_commit($dbc);
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_admin_payment_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(pay_date, '%e/%b/%Y') AS pay_date FROM payment_enrollment $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
                $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
                $location_map = get_my_reference_array("location_{$_SESSION[SESS.'constant']['retailer_level']}", 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs))
		{
                    $id = $row['id'];
                    $out[$id] = $row;
                    $out[$id]['retailer_name'] = $retailer_map[$row['retailer_id']];
                    $out[$id]['dealer_name'] =   $dealer_map[$row['dealer_id']];
                    $out[$id]['location_name'] = $location_map[$row['location_id']];
                    $out[$id]['user'] = myrowvaladvance('person', "CONCAT_WS(' ',first_name,middle_name,last_name)", 'username', $where = "id = '$row[user_id]'");
                   
		}
		return $out;
	} 
######################################## Admin Payment code end here ####################################################        
	
}// class end here
?>