<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class dispatch extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Department Starts here ####################################################	
	public function get_dispatch_se_data()
	{  
		$d1 = $_POST;
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Dispatch'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function dispatch_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dispatch_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		if(!empty($d1['dispatch_date'])) 
                    $dispatch = get_mysql_date($d1['dispatch_date']);
                 else $dispatch = '';
                 if(!empty($d1['challan_order_id']))
                     $challan_order_id = implode(',' , $d1['challan_order_id']);
                 else $challan_order_id = '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `daily_dispatch` (`dispatch_id`, `dealer_id`, `van_no`, `dispatch_date`, `challan_order_id`) VALUES (NULL , '$d1[dealer_id]', '$d1[van_no]', '$dispatch', '$challan_order_id')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Dispatch could not be save some error occurred');}
		$rId = mysqli_insert_id($dbc);	
                $extrawork = $this->dispatch_extra('save', $rId, $_POST['challan_order_id']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history		
		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function dispatch_extra($actiontype, $dispId, $challan_no)
	{ 
		global $dbc;
		$str = $product = $retailer =  array();
		//during update we are required to remove the previous entry
		if($actiontype == 'Update') mysqli_query($dbc, "DELETE FROM daily_dispatch_details WHERE dispatch_id = '$dispId'");	
                if(!empty($challan_no))
                {
                    foreach ($challan_no as $key=>$value)
                    {
                       $str[] = $value;
                    }
                }
                $str = implode($str, ',');
                $str1 = array();
                $q = "SELECT * FROM  challan_order_details INNER JOIN challan_order ON challan_order.ch_no = challan_order_details.challan_no WHERE challan_no IN ($str)";
              
                list($opt, $rs) = run_query($dbc, $q, 'multi');
                if($opt)
                {
                    while($rows = mysqli_fetch_assoc($rs))
                    {
                        $retailer[$rows['ch_retailer_id']] = $rows['ch_retailer_id'];
                        $product[$rows['catalog_details_id']] = $rows['catalog_details_id'];
                        $str1[] = '(NULL,\''.$dispId.'\', \''.$rows['product_id'].'\', \''.$rows['catalog_details_id'].'\', \''.$rows['batch_no'].'\', \''.$rows['product_rate'].'\', \''.$rows['ch_qty'].'\', \''.$rows['free_qty'].'\')';
                    }
                }
                $str1 = implode($str1, ',');
                $q = "INSERT INTO daily_dispatch_details (`dis_details_id`, `dispatch_id`, `product_id`, `catalog_details_id`, `batch_no`, `rate`, `qty`, `scheme_qty`) VALUES $str1";
               
                //exit;
                $r = mysqli_query($dbc, $q);
                if(!$r) return array ('status'=>false, 'myreason'=>'Daily dispatch Details Could not be saved Some error occurred.') ;
                $total_bills = count($retailer);
                $total_product = count($product);
                $q = "UPDATE daily_dispatch SET total_bills = '$total_bills', total_product = '$total_product' WHERE dispatch_id = '$dispId'";
                $r = mysqli_query($dbc, $q);
                $challan_no_str = implode(',' ,$challan_no);
                $q = "UPDATE challan_order SET dispatch_status = '1' WHERE ch_no IN ($challan_no_str)";
		$r = mysqli_query($dbc, $q);
                return array ('status'=>true, 'myreason'=>'') ;	
	}
	public function dispatch_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dispatch_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
                if(!empty($d1['dispatch_date'])) 
                    $dispatch = get_mysql_date($d1['dispatch_date']);
                 else $dispatch = '';
                 if(!empty($d1['challan_order_id']))
                     $challan_order_id = implode(',' , $d1['challan_order_id']);
                 else $challan_order_id = '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `daily_dispatch` SET `dealer_id` = '$d1[dealer_id]', `van_no` = '$d1[van_no]', dispatch_date = '$dispatch', challan_order_id = '$challan_order_id'  WHERE dispatch_id = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
                $rId = $id;
                $extrawork = $this->dispatch_extra('Update', $rId, $_POST['challan_order_id']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_dispatch_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(dispatch_date, '%e/%b/%Y') AS dispatch_date FROM daily_dispatch $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs))
		{
                    $id = $row['dispatch_id'];
                    $out[$id] = $row;
                    $out[$id]['name'] = $dealer_map[$row['dealer_id']];
                    $out[$id]['dispatch_details'] = $this->get_my_reference_array_direct("SELECT * FROM daily_dispatch_details INNER JOIN catalog_product ON daily_dispatch_details.product_id = catalog_product.id  WHERE dispatch_id = $id ", 'dis_details_id'); 
                   
		}
		return $out;
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
	public function print_looper_daily_dispatch($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		//Create the object when needed
		$party = new dealer();		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_dispatch_list("dispatch_id = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['dealer_details'] = $party->get_dealer_list("id='$temp[dealer_id]'");
		}
		//pre($out);
		return $out;
	}
}// class end here
?>