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
                $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Dispatch'; //whether to do history log or not
		return array(true,$d1);
	}
	public function dispatch_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dispatch_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $dispatch = '';
                if(!empty($d1['dispatch_date'])) $dispatch = get_mysql_date($d1['dispatch_date']);
                $dispatch_num = $this->next_dispatch_num();
                $dis_num = "DS{$_SESSION[SESS.'data']['dealer_id']}/{$_SESSION[SESS.'sess']['short_period']}/$dispatch_num";
                $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
               //pre($d1); die;
                $rId = $id;
               // echo $rId; exit;    
		$q = "INSERT INTO `daily_dispatch` (`dispatch_id`, `dealer_id`, `van_no`, `dispatch_date`, `created_by`, `dispatch_no`,`route`) VALUES ('$id' , '$d1[dealer_id]', '$d1[van_no]', '$dispatch', '$d1[uid]', '$dis_num', '$d1[dispatch_beat]')";
		//h1($q);exit;
               $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Dispatch could not be save some error occurred');}
		
                $extrawork = $this->dispatch_extra('save', $rId, $d1); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function dispatch_extra($actiontype, $rId, $d1)
	{ 
		global $dbc;
		$str = $ch_no = $retailer =  array();
		//during update we are required to remove the previous entry
		if($actiontype == 'Update') mysqli_query($dbc, "DELETE FROM daily_dispatch_details WHERE dispatch_id = '$rId'");	
                
                $j = 1;
                $quan =0;
                foreach ($d1['chk'] as $key=>$value){
                    $ch_no[] = $value;
                    $str[] = '(\''.$rId.'\', \''.$value.'\', \''.$j.'\')';
                    
                      $query1 = "select qty,free_qty,weight from `challan_order_details` inner join catalog_product on 
                           challan_order_details.product_id=catalog_product.id
                           WHERE `ch_id` = '$value'"; 
                   // h1($query1);
                       $quan1 = array();
                       $result1 = mysqli_query($dbc,$query1);
                     while($row1 = mysqli_fetch_assoc($result1)){
                          $qtyy = $row1['qty'];
                     $fqtyy = $row1['free_qty'];
                     $weight = $row1['weight'];
                     $quan1[] = ($qtyy+$fqtyy)*$weight;
                     }
                    $quant = array_sum($quan1);
                    $quanti = $quant/1000;
                     $quan = $quanti+$quan;
                     
                     
                    $j++;
                }
                $van_no = $d1['van_no'];   
                $query = "select capacity from `van` WHERE `van_no` = '$van_no'"; 
                   // h1($query1);
                       $result = mysqli_query($dbc,$query);
                      $row = mysqli_fetch_assoc($result);
                     $cap = $row['capacity'];
                  //  echo $quan."cap".$cap; exit; 
                     if($quan>$cap)
                     {
        mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Selected Quantity is greater than van capacity');                 
                     }
 else {
                $str = implode($str, ',');
                $challan_no_str = implode($ch_no, ',');
                $q = "INSERT INTO daily_dispatch_details ( `dispatch_id`, `ch_id`, `sortorder`) VALUES $str";
               
                $r = mysqli_query($dbc, $q);
                if(!$r) return array ('status'=>false, 'myreason'=>'Daily dispatch Details Could not be saved Some error occurred.') ;
                
                $q = "UPDATE challan_order SET dispatch_status = '1' WHERE id IN ($challan_no_str)";
		$r = mysqli_query($dbc, $q);
                return array ('status'=>true, 'myreason'=>'') ;	
}
	}
	public function dispatch_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dispatch_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
                $dispatch = '';
                if(!empty($d1['dispatch_date'])) $dispatch = get_mysql_date($d1['dispatch_date']);
                
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `daily_dispatch` SET `dealer_id` = '$d1[dealer_id]', `van_no` = '$d1[van_no]', dispatch_date = '$dispatch', company_id = '{$_SESSION[SESS.'data']['company_id']}'  WHERE dispatch_id = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
                $rId = $id;
                $extrawork = $this->dispatch_extra('Update', $rId, $d1); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_dispatch_inc_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();	
                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * , DATE_FORMAT( daily_dispatch.dispatch_date, '%e/%b/%Y' ) AS dispatch_date FROM daily_dispatch
INNER JOIN daily_dispatch_details ON daily_dispatch_details.dispatch_id = daily_dispatch.dispatch_id
INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE   `daily_dispatch`.`dealer_id`='$dealer_id' AND `daily_dispatch`.`delivery_status`='0'";
            // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
            //    $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
               // $vanmap = get_my_reference_array('van', 'vanId', 'van_no');
                //pre($vanmap);
		while($row = mysqli_fetch_assoc($rs))
		{
                    $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
                    $id = $row['dispatch_id'];
                    $out[$id] = $row;
                    $out[$id]['dname'] = $dealer_map[$row['dealer_id']];
                    $out[$id]['rname'] = $retailer_map;
                    //$out[$id]['van_name'] = $vanmap[$row['van_no']];
                    $q= "SELECT * FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ";
                   //h1($q);
                    $out[$id]['dispatch_details'] = $this->get_my_reference_array_direct($q, 'sortorder'); 
                    
                //     $q1= "SELECT sum(qty) as totalqty FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ";
                  // h1($q1);
                //    $out[$id]['total_qty'] = $this->get_my_reference_array_direct($q1, 'sortorder'); 
                   
		}
               // pre($out);
		return $out;
	} 
        
        public function get_dispatch_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * , DATE_FORMAT( daily_dispatch.dispatch_date, '%e/%b/%Y' ) AS dispatch_date FROM daily_dispatch
INNER JOIN daily_dispatch_details ON daily_dispatch_details.dispatch_id = daily_dispatch.dispatch_id
INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id  $filterstr ";
             //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
            //    $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
               // $vanmap = get_my_reference_array('van', 'vanId', 'van_no');
                //pre($vanmap);
		while($row = mysqli_fetch_assoc($rs))
		{
                    $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
                    $id = $row['dispatch_id'];
                    $out[$id] = $row;
                    $out[$id]['dname'] = $dealer_map[$row['dealer_id']];
                    $out[$id]['rname'] = $retailer_map;
                    //$out[$id]['van_name'] = $vanmap[$row['van_no']];
                    $q= "SELECT * FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ";
                   //h1($q);
                    $out[$id]['dispatch_details'] = $this->get_my_reference_array_direct($q, 'sortorder'); 
                    
                //     $q1= "SELECT sum(qty) as totalqty FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ";
                  // h1($q1);
                //    $out[$id]['total_qty'] = $this->get_my_reference_array_direct($q1, 'sortorder'); 
                   
		}
               // pre($out);
		return $out;
	} 
        
        public function get_dispatch_challan_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(dispatch_date, '%e/%b/%Y') AS dispatch_date FROM daily_dispatch $filterstr ";
               
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
                $vanmap = get_my_reference_array('van', 'vanId', 'van_no');
                //pre($vanmap);
		while($row = mysqli_fetch_assoc($rs))
		{
                    $id = $row['dispatch_id'];
                    $out[$id] = $row;
                    $out[$id]['name'] = $dealer_map[$row['dealer_id']];
                    $out[$id]['van_name'] = $vanmap[$row['van_no']];
                    $out[$id]['dispatch_details'] = $this->get_my_reference_array_direct("SELECT * FROM daily_dispatch_details INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ", 'sortorder'); 
                   $out[$id]['challan_dispatch'] = $this->get_my_reference_array_direct("SELECT * FROM daily_dispatch_details INNER JOIN challan_order_details ON challan_order_details.ch_id = daily_dispatch_details.ch_id INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id WHERE dispatch_id = $id ", 'id'); 
                ///  $out[$id]['total_qty'] = $this->get_my_reference_array_direct("SELECT * FROM daily_dispatch_details INNER JOIN challan_order_details ON challan_order_details.ch_id = daily_dispatch_details.ch_id INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id WHERE dispatch_id = $id ", 'id'); 
                   
//echo "SELECT * FROM daily_dispatch_details INNER JOIN challan_order_details ON challan_order_details.ch_id = daily_dispatch_details.ch_id WHERE dispatch_id = $id ";
		}
              //  pre($out);
		return $out;
	} 
	public function next_dispatch_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT COUNT(dispatch_no) AS total FROM daily_dispatch";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
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
			$rcdstat = $this->get_dispatch_challan_list("dispatch_id = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			
			$out[$id] = $rcdstat[$id];
			$out[$id]['dealer_details'] = $party->get_dealer_list("id='{$_SESSION[SESS.'data']['dealer_id']}'");
		}
		//pre($out);
		return $out;
	}
        public function get_challan_wise_retailer_list($location_id)
        {
            global $dbc;
            $out = array();
            $rlevel = $_SESSION[SESS.'constant']['retailer_level'];
            $dlevel = $_SESSION[SESS.'constant']['dealer_level'];
            $q = "SELECT retailer.id FROM retailer INNER JOIN location_$rlevel ON location_$rlevel.id = retailer.location_id ";
            for($i = $rlevel; $i >= $dlevel; $i--)
            {
                $j = $i - 1;
                $q .= " INNER JOIN location_$j ON location_$j.id = location_$i.location_".$j."_id";
            }
            $q .= " WHERE location_$dlevel.id = '$location_id'";
            //h1($q);
            list($opt, $rs) = run_query($dbc, $q, 'multi');
            if(!$opt) return $out;
            while($row = mysqli_fetch_assoc($rs))
            {
                $out[$row['id']] = $row['id'];
            }
            return $out;
        }
        
        public function get_stock_age_list($filter='', $records = '', $orderby='')
{
global $dbc;
$out = array(); 
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q="SELECT stock.*,stock.id as sid,cp.name AS product_name FROM stock INNER JOIN catalog_product AS cp ON cp.id=stock.product_id $filterstr";

// h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{

$id = $row['sid'];
$out[$id] = $row;

}
//pre($out);
return $out;
}
        
}// class end here
?>