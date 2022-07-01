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
$q="SELECT stock.*,stock.id as sid,cp.name AS product_name FROM stock INNER JOIN catalog_product AS cp ON cp.id=stock.product_id INNER JOIN other_company ON other_company.id=cp.company_id $filterstr";

 //h1($q);
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

################## dispatch class####################################

public function get_product_wise_summary_list($filter='', $records = '', $orderby='')
{

global $dbc;
$out = array(); 
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);
//print_r($filterstr);
$q="SELECT catalog_product.*,catalog_product.id as product_id,catalog_product.name AS product_name FROM catalog_product $filterstr ORDER BY name ";

//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{

$id = $row['product_id'];
$product_id=$row['product_id'];
$out[$id] = $row;
$stock_cond="product_id=".$product_id." AND dealer_id=".$dea_id;
if(empty($start)){
$start=date(Ymd);
}
if(empty($end)){
$end=date(Ymd);
}
$cond="ch_dealer_id=".$dea_id." AND product_id=".$product_id." AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='".$end."'";
$out[$id][billed_qty] = myrowvaljoin('challan_order_details','ROUND(sum(qty+free_qty))', 'challan_order', 'challan_order_details.ch_id=challan_order.id ', $cond);
$out[$id][billed_amt] = myrowvaljoin('challan_order_details','ROUND(sum(qty*product_rate),2)', 'challan_order', 'challan_order_details.ch_id=challan_order.id ', $cond);
$out[$id][closing_stock] = myrowval('stock', 'qty', $stock_cond);

}
//pre($out);
return $out;
}

public function get_bill_summary_list($filter='', $records = '', $orderby='')
{
global $dbc;
$out = array(); 
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
if(empty($start)){
$start=date(Ymd);
}
if(empty($end)){
$end=date(Ymd);
}
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);
//print_r($filterstr);
$q="SELECT id,count(ch_no) AS billed_cut,sum(amount_round) AS total_amt FROM challan_order "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id != 0";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{
$id = $row['id'];
$out[$id] = $row;
}
//pre($out);
return $out;
}

public function get_retailer_bill_summary_list($filter='', $records = '', $orderby='')
{
global $dbc;
$out = array(); 
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
if(empty($start)){
$start=date(Ymd);
}
if(empty($end)){
$end=date(Ymd);
}
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);
//print_r($filterstr);
if(isset($_POST[retailer_id])&& !empty($_POST[retailer_id])){
$q="SELECT challan_order.id AS id,ch_retailer_id,ch_no as invoice_no,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,amount_round AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id=$_POST[retailer_id] GROUP BY date,ch_retailer_id,invoice_no";
}else{
$q="SELECT challan_order.id AS id,ch_retailer_id,ch_no as invoice_no,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,amount_round AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' GROUP BY date,ch_retailer_id,invoice_no";
}
// h1($q);die;
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{
$id = $row['id'];
$out[] = $row;
// $out[$id] = $row;
}
// echo(count($out));die;
return $out;
}

public function get_retailer_summary_list($filter='', $records = '', $orderby='')
{
global $dbc;
$out = array(); 
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
if(empty($start)){
$start=date(Ymd);
}
if(empty($end)){
$end=date(Ymd);
}
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);
//print_r($filterstr);
if(isset($_POST[retailer_id])&& !empty($_POST[retailer_id])){
$q="SELECT challan_order.id AS id,ch_retailer_id,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,amount_round AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id=$_POST[retailer_id] GROUP BY ch_retailer_id";
}else{
$q="SELECT challan_order.id AS id,ch_retailer_id,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,COUNT(ch_no) as challan_count,SUM(amount_round) AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' GROUP BY ch_retailer_id";
}
// h1($q);die;
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{
$id = $row['id'];
$out[] = $row;
// $out[$id] = $row;
}
// echo(count($out));die;
return $out;
}


public function get_dispatch_productwise_list($filter='',  $records = '', $orderby='')
    {
        global $dbc;
        $out = array();        
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT daily_dispatch.van_no,challan_order_details.ch_id,challan_order_details.id,challan_order_details.product_id AS product_id,catalog_product.name,catalog_product.itemcode,challan_order_details.mrp,sum(challan_order_details.qty) AS qty FROM daily_dispatch 
                INNER JOIN daily_dispatch_details ON daily_dispatch_details.dispatch_id = daily_dispatch.dispatch_id
                INNER JOIN challan_order_details ON challan_order_details.ch_id = daily_dispatch_details.ch_id INNER JOIN catalog_product ON catalog_product.id=challan_order_details.product_id $filterstr GROUP BY product_id,mrp ORDER BY daily_dispatch.dispatch_date";
               // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        while($row = mysqli_fetch_assoc($rs))
        {
                    $id = $row['id'];
                    $out[$id] = $row;
        }
               // pre($out);
        return $out;
    }
    /* 
        public function get_dispatch_datewise_list($filter='',  $records = '', $orderby='')
    {
        global $dbc;
        $out = array();        
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT challan_order.id,challan_order.ch_no,DATE_FORMAT(`challan_order`.`ch_date`,'%d-%m-%Y') AS ch_date,challan_order.ch_retailer_id,retailer.name,challan_order.amount FROM daily_dispatch 
                INNER JOIN daily_dispatch_details ON daily_dispatch_details.dispatch_id = daily_dispatch.dispatch_id
                INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id 
                INNER JOIN retailer ON retailer.id=ch_retailer_id $filterstr  ORDER BY challan_order.ch_date DESC";
                //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
            //    $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
        while($row = mysqli_fetch_assoc($rs))
        {
                    $id = $row['ch_no'];
                    $out[$id] = $row;
        }
               // pre($out);
        return $out;
    }*/

    public function print_looper_date_dispatch($multiId, $options=array())
        {
            global $dbc;
            $out = array();
                    $out1 = array();
                    $out2 = array();
            if(is_null($multiId) || empty($multiId)) return $out;
            //Create the object when needed
            $party = new dealer();    
                   // pre($multiId);
            //Explode to get an array of all the Id
            $multiId = explode('-', $multiId);
                   $mulid=implode(',',$multiId);
                   $mulid=ltrim($mulid,',');
                   //h1($mulid);
                   $cq="SELECT challan_order.id AS coid,ch_no,ch_retailer_id,ch_dealer_id,amount,name,DATE_FORMAT(`ch_date`,'%d-%m-%Y') AS ch_date FROM challan_order INNER JOIN retailer ON retailer.id=ch_retailer_id WHERE challan_order.id IN($mulid)";
                  //h1($cq);
                   $cr= mysqli_query($dbc, $cq);
                           while($rowc=mysqli_fetch_assoc($cr)){
                               $id=$rowc['coid'];
                               $out1[$id]=$rowc;
                           }
                   $cq1="SELECT challan_order_details.id AS cid,product_id,mrp,sum(qty) AS qty,catalog_product.name,catalog_product.itemcode FROM challan_order_details INNER JOIN catalog_product ON challan_order_details.product_id=catalog_product.id WHERE ch_id IN($mulid) GROUP BY product_id,mrp";
                 //  h1($cq1);
                   $cr1= mysqli_query($dbc, $cq1);
                           while($rowc1=mysqli_fetch_assoc($cr1)){
                               $id1=$rowc1['cid'];
                               $out2[$id1]=$rowc1;
                           }
                $out[$mulid]['challan'] = $out1;
                            $out[$mulid]['challan_details'] = $out2;
                $out[$mulid]['dealer_details'] = $party->get_dealer_list("id='{$_SESSION[SESS.'data']['dealer_id']}'");
                $out[$mulid]['van'] = myrowvaljoin('daily_dispatch','van_no','daily_dispatch_details','daily_dispatch_details.dispatch_id=daily_dispatch.dispatch_id',"ch_id IN($mulid)");

            //pre($out);
            return $out;
        }

        public function get_dispatch_datewise_list($filter='',  $records = '', $orderby='')
            {
                global $dbc;
                $out = array();        
                // if user has send some filter use them.
                $filterstr = $this->oo_filter($filter, $records, $orderby);
                $q = "SELECT daily_dispatch.van_no,challan_order.id AS cid,challan_order.ch_no,DATE_FORMAT(`challan_order`.`ch_date`,'%d-%m-%Y') AS ch_date,challan_order.ch_retailer_id,retailer.name,challan_order.amount FROM daily_dispatch 
                        INNER JOIN daily_dispatch_details ON daily_dispatch_details.dispatch_id = daily_dispatch.dispatch_id
                        INNER JOIN challan_order ON challan_order.id = daily_dispatch_details.ch_id 
                        INNER JOIN retailer ON retailer.id=ch_retailer_id $filterstr  ORDER BY challan_order.ch_date DESC, challan_order.ch_no ASC";
                    //  h1($q);
                list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
                if(!$opt) return $out; // if no order placed send blank array
                    //    $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
                while($row = mysqli_fetch_assoc($rs))
                {
                            $id = $row['ch_no'];
                            $cid = $row['cid'];
                            $out[$id] = $row;
           
                }
                       // pre($out);
                return $out;
            }
            public function get_stock_report_se_data()
	{  
		$d1 = $_POST;
                $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Stock'; //whether to do history log or not
		return array(true,$d1);
	}
           
         public function stock_report_edit($id)
         {
                 
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_stock_report_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		
               // print_r($d1);
                $id= $d1[id];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE `stock` SET `qty`='$d1[qty]' WHERE id = '$id'";
        //h1($q);
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Stock Table error') ;} 
		$rId = $id;	
		mysqli_commit($dbc);
		
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	 
         

 
        
###########################################
            public function get_stock_report_list($filter='', $records = '', $orderby='')
            {

            global $dbc;
            $out = array(); 
            $dea_id = $_SESSION[SESS.'data']['dealer_id'];
            $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            // if user has send some filter use them.
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            //print_r($filterstr);
            $q="SELECT stock.id AS id,product_id,qty,(qty*mrp) AS mrp_value,(qty*dealer_rate) AS value,batch_no,mrp,mfg as mfg_date,dealer_rate as rate FROM stock $filterstr  ORDER BY product_id";

            // h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            if(!$opt) return $out; // if no order placed send blank array
            while($row = mysqli_fetch_assoc($rs))
            {
            $id = $row['id'];
            $product_id=$row['product_id'];
            $out[$id] = $row;
            $stock_cond="id=".$product_id;
            $out[$id][product_name] = myrowval('catalog_product', 'name', $stock_cond);
            $out[$id][itemcode] = myrowval('catalog_product', 'itemcode', $stock_cond);

            }
            // pre($out);
            return $out;
            }

            public function get_stock_min_max_level_list($filter='', $records = '', $orderby='')
            {

            global $dbc;
            $out = array(); 
            $dea_id = $_SESSION[SESS.'data']['dealer_id'];
            $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            // if user has send some filter use them.
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            //print_r($filterstr);
            $dataq = "SELECT * FROM `threshold` where dealer_id = $dea_id";
            list($opt, $set1_data) = run_query($dbc, $dataq, $mode='multi', $msg='');
            while($row = mysqli_fetch_assoc($set1_data))
            {
            	$product_id = $row['product_id'];
            	$set_array[$product_id]['product_id'] = $row['product_id'];
            	$set_array[$product_id]['qty'] = $row['qty'];
            	$set_array[$product_id]['max_qty'] = $row['max_qty'];
            	// $set_array_final[] = $set_array;
            }


            $q="SELECT stock.id AS id,product_id,sum(qty) as qty,sum(qty*mrp) AS mrp_value,sum(qty*dealer_rate) AS value,batch_no,mrp,mfg as mfg_date,dealer_rate as rate FROM stock $filterstr  group by product_id ORDER BY product_id";

            // h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            if(!$opt) return $out; // if no order placed send blank array
            while($row = mysqli_fetch_assoc($rs))
            {
            $id = $row['id'];
            $product_id=$row['product_id'];
            $out[$id] = $row;
            $stock_cond="id=".$product_id;
            $out[$id][product_name] = myrowval('catalog_product', 'name', $stock_cond);
            $out[$id][itemcode] = myrowval('catalog_product', 'itemcode', $stock_cond);
            $out[$id][min] = $set_array[$product_id]['qty'];
            $out[$id][max] = $set_array[$product_id]['max_qty'];

            }
            // pre($out);
            return $out;
            }

            public function get_opening_closing_stock_list($filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	if(empty($start)){
            		$start=date('Ymd');
            		$end = date('Ymd');
            	}
            	$prev_date = date('Ymd', strtotime($start .' -1 day'));
		        $odate=date('Y')."0401";
            	// if user has send some filter use them.

            	$filterstr = $this->oo_filter($filter, $records, $orderby);
            	$q="SELECT stock.id AS id,stock.product_id,sum(qty) AS qty,MAX(rate) as rate,mrp,batch_no,catalog_view.product_name FROM stock INNER JOIN catalog_view ON catalog_view.product_id=stock.product_id $filterstr GROUP BY stock.product_id ORDER BY stock.product_id ";

            	//  h1($q);die;
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            	if(!$opt) return $out; // if no order placed send blank array
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id = $row['id'];
            	$product_id=$row['product_id'];
            	$out[$id] = $row;
            	$stock_cond="id=".$product_id;
            	//$out[$id][product_name] = myrowval('catalog_product', 'name', $stock_cond);
            	$cond="dealer_id=".$dea_id." AND purchase_order_details.product_id=".$row['product_id']." AND date_format(order_date,'%Y%m%d')>='".$start."' AND date_format(order_date,'%Y%m%d')<='".$end."'
				AND DATE_FORMAT(purchase_order.receive_date,'%Y-%m-%d')!='0000-00-00' ";

            	//$out[$id]['purchase']= myrowvaljoin('purchase_order_details','SUM(quantity+scheme_qty)','purchase_order','purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no',$cond);
				$out[$id]['purchase']=$this->get_opurchase_stock($cond);
				

            	$cond1="ch_dealer_id=".$dea_id." AND challan_order_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."'";
            	//$out[$id]['billed']= myrowvaljoin('challan_order_details','ROUND(SUM(qty+free_qty))','challan_order','challan_order_details.ch_id=challan_order.id',$cond1);
            	$out[$id]['billed']=$this->get_obilled_stock($cond1);
            	//$out[$id]['opening'] = $row['qty']-$out[$id]['purchase'];
				$ostock_cond1="product_id=".$product_id." AND dealer_id=".$dea_id." GROUP BY product_id";
				//$out[$id]['opening'] = myrowval('opening_stocks', 'sum(qty)', $ostock_cond);
				$out[$id]['opening_rate'] = myrowval('opening_stocks', 'rate', $ostock_cond1);
				$out[$id]['opening_mrp'] = myrowval('opening_stocks', 'mrp', $ostock_cond1);

				$cond2="dealer_id=".$dea_id." AND purchase_order_details.product_id=".$row['product_id']." AND date_format(created_date,'%Y%m%d')>='".$odate."' AND date_format(created_date,'%Y%m%d')<='".$prev_date."' AND date_format(purchase_order.receive_date,'%Y-%m-%d')!='0000-00-00'";
            	$opurchase=$this->get_opurchase_stock($cond2);

            	$cond3="ch_dealer_id=".$dea_id." AND challan_order_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."'";
            	$obilled=$this->get_obilled_stock($cond3);

				$ostock_cond="opening_stocks.product_id=".$row['product_id']." AND dealer_id=".$dea_id." ";
				$opening = $this->get_oopening_stock($ostock_cond);

				$opening_stock=($opening+$opurchase)-$obilled;
				$out[$id]['opening'] = $opening_stock;

				// mine
				$stock_condi="stock.product_id=".$row['product_id']." AND dealer_id=".$dea_id." ";
				$stck = $this->get_stock_new($stock_condi);

				$out[$id]['stock_new'] = $stck;
				// mine end

            	}
            	//pre($out);
            	return $out;
            	
			}
			
			public function get_stock_new($cond)
	{
		global $dbc;
		$out = array();
		$q = "SELECT ROUND(sum(qty)) AS oqty FROM stock INNER JOIN catalog_view ON stock.product_id=catalog_view.product_id WHERE $cond";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['oqty'];	
	}
        public function get_division_summary_list($filter='', $records = '', $orderby='')
		{
		global $dbc;
		$out = array();
		$dea_id = $_SESSION[SESS.'data']['dealer_id'];
		$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
		if(empty($start)){
		$start=date(Ymd);
		}
		if(empty($end)){
		$end=date(Ymd);
		}
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//print_r($filterstr);
		// $q="SELECT c2.name AS type,c2.id AS pdid,sum(product_rate*qty) AS total_amt FROM challan_order AS co INNER JOIN  challan_order_details AS cod ON co.id=cod.ch_id "
		//         . "INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id "
		//         . "INNER JOIN catalog_3 AS c2 ON cv.c2_id=c2.id "
		//         . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' GROUP BY c2.id";
		$q="SELECT c3_name AS type,c3_id AS pdid,sum(taxable_amt) AS total_amt,sum(taxable_amt-vat_amt) AS taxable_amt,sum(vat_amt) AS vat_amt FROM challan_order AS co INNER JOIN  challan_order_details AS cod ON co.id=cod.ch_id "
		        . "INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id "
		        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' GROUP BY c3_id";
		//h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
		$id = $row['pdid'];
		$out[$id] = $row;
		}
		//pre($out);
		return $out;
		}

		public function get_gstr_list($filter='', $records = '', $orderby='')
{
global $dbc;
$out = array(); 
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
if(empty($start)){
$start=date(Ymd);
}
if(empty($end)){
$end=date(Ymd);
}
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);

$input_tax=0;
$input_total=0;
$output_tax=0;
$output_total=0;
//print_r($filterstr);
$q="SELECT challan_order.id AS id,sum(vat_amt) AS tax,sum(taxable_amt-vat_amt) AS total_amt FROM challan_order INNER JOIN challan_order_details ON challan_order.id=challan_order_details.ch_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND challan_order_details.tax!='0.00'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
$i=1;
while($row = mysqli_fetch_assoc($rs))
{
//$id = $row['id'];
$out[$i]['per'] = 'Outward Supplies';
$out[$i]['tax'] = $row['tax'];
$out[$i]['total_amt'] = $row['total_amt'];
$i++;
$output_tax+=$row['tax'];
$output_total+=$row['total_amt'];
}
$q="SELECT challan_order.id AS id,sum(vat_amt) AS tax,ROUND(sum(taxable_amt-vat_amt),2) AS total_amt FROM challan_order INNER JOIN challan_order_details ON challan_order.id=challan_order_details.ch_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND challan_order_details.tax='0.00'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row1 = mysqli_fetch_assoc($rs))
{
//$id = $row['id'];
$out[$i]['per'] = 'Outward Supplies Excempted';
$out[$i]['tax'] = $row1['tax'];
$out[$i]['total_amt'] = $row1['total_amt'];
$i++;
$output_tax+=$row1['tax'];
$output_total+=$row1['total_amt'];
}
$out[$i]['per'] = 'Outward Supplies Total';
$out[$i]['tax'] = $output_tax;
$out[$i]['total_amt'] = $output_total;
$i++;

$q="SELECT purchase_order.id AS id,sum(cgst_amount+sgst_amount+igst_amount) AS tax,sum(gross_amt+sch_amt+spl_amt+cd_amount+td_amount+atd_amt) AS total_amt FROM purchase_order INNER JOIN purchase_order_details ON purchase_order.order_id=purchase_order_details.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv "
        . "WHERE DATE_FORMAT(`created_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`created_date`,'%Y%m%d')<='$end' AND dealer_id='$dea_id' AND (cgst_amount!=0 AND sgst_amount!=0 OR igst_amount!=0) LIMIT 1";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{
//$id = $row['id'];
$out[$i]['per'] = 'Inward Supplies';
$out[$i]['tax'] = $row['tax'];
$out[$i]['total_amt'] = $row['total_amt'];
$input_tax+=$row['tax'];
$input_total+=$row['total_amt'];
$i++;
}



$q="SELECT purchase_order.id AS id,sum(cgst_amount+sgst_amount) AS tax,sum(gross_amt+sch_amt+spl_amt+cd_amount+td_amount+atd_amt) AS total_amt FROM purchase_order INNER JOIN purchase_order_details ON purchase_order.order_id=purchase_order_details.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv "
        . "WHERE DATE_FORMAT(`created_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`created_date`,'%Y%m%d')<='$end' AND dealer_id='$dea_id' AND cgst_amount=0 AND sgst_amount=0  AND igst_amount=0 LIMIT 1";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
if(!$opt) return $out; // if no order placed send blank array
while($row = mysqli_fetch_assoc($rs))
{
//$id = $row['id'];
$out[$i]['per'] = 'Inward Supplies Excempted';
$out[$i]['tax'] = $row['tax'];
$out[$i]['total_amt'] = $row['total_amt'];
$i++;
$input_tax+=$row['tax'];
$input_total+=$row['total_amt'];
}
$out[$i]['per'] = 'Inward Supplies Total';
$out[$i]['tax'] = $input_tax;
$out[$i]['total_amt'] = $input_total;
//pre($out);
return $out;
}
public function get_stock_summary_list($filter='', $records = '', $orderby='')
		{
		global $dbc;
		$out = array();
		$dea_id = $_SESSION[SESS.'data']['dealer_id'];
		$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
		if(empty($start)){
		$start=date(Ymd);
		}
		if(empty($end)){
		$end=date(Ymd);
		}
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//print_r($filterstr);
		$q="SELECT c2_name AS type,c2_id AS pdid,sum(rate*qty) AS total_amt,sum(qty) AS qty FROM stock AS cod "
		        . "INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id "
		        . " $filterstr GROUP BY c2_id";
		//h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
		$id = $row['pdid'];
		$out[$id] = $row;
		}
		//pre($out);
		return $out;
		}

		public function get_stock_ledger_list($filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	// if user has send some filter use them.

            	$filterstr = $this->oo_filter($filter, $records, $orderby);
            	$q="SELECT co.id AS cid,cod.batch_no,cod.product_id,cod.id,cv.product_name,ROUND(sum(cod.qty+cod.free_qty)) AS qty,co.ch_no,DATE_FORMAT(co.ch_date,'%d-%m-%Y') AS ch_date,ROUND((taxable_amt),2) AS value FROM challan_order AS co INNER JOIN challan_order_details AS cod ON co.id=cod.ch_id INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id $filterstr GROUP BY ch_date,ch_no,product_id,batch_no ORDER BY ch_no,product_name";

            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            	if(!$opt) return $out; // if no order placed send blank array
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id = $row['id'];
            	$product_id=$row['product_id'];
            	$out[$id] = $row;
            	}
            	//pre($out);
            	return $out;
            	
            }
            public function get_stock_ledger_tranc_list($cdate,$filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$out1 = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	$product_id=$_POST['product_id'];
            	$filterstr = $this->oo_filter($filter, $records, $orderby);
            	
            	$i=1;
            	
            	$q="SELECT co.id AS cid,retailer.name AS pert,cod.batch_no,cod.product_id,cod.id,cv.product_name,ROUND(sum(cod.qty+cod.free_qty)) AS oqty,co.ch_no,DATE_FORMAT(co.ch_date,'%d-%m-%Y') AS ch_date,ROUND((taxable_amt),2) AS ovalue FROM challan_order AS co INNER JOIN challan_order_details AS cod ON co.id=cod.ch_id INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id INNER JOIN retailer ON retailer.id=co.ch_retailer_id WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')='$cdate' AND co.ch_dealer_id='$dea_id' AND cod.product_id='$product_id' GROUP BY ch_date,ch_no,product_id,batch_no ORDER BY ch_no,product_name";
            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            	//if(!$opt) return $out; // if no order placed send blank array
            	
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id=$i;
            	 $out[$id] = $row;
            	$i++;
            	}
            	
            	$q="SELECT po.id AS cid,csa.csa_name AS pert,pod.batch_no,pod.product_id,pod.id,cv.product_name,ROUND(sum(pod.quantity+pod.scheme_qty)) AS iqty,po.challan_no AS ch_no,DATE_FORMAT(po.order_date,'%d-%m-%Y') AS ch_date,ROUND((total_amount),2) AS ivalue FROM purchase_order AS po INNER JOIN purchase_order_details AS pod ON po.order_id=pod.order_id AND po.challan_no=pod.purchase_inv INNER JOIN catalog_view AS cv ON cv.product_id=pod.product_id LEFT JOIN csa ON csa.c_id=po.csa_id WHERE DATE_FORMAT(`order_date`,'%Y%m%d')='$cdate' AND DATE_FORMAT(po.receive_date,'%Y-%m-%d')!='0000-00-00' AND po.dealer_id='$dea_id' AND pod.product_id='$product_id' GROUP BY ch_date,ch_no,product_id,batch_no ORDER BY ch_no,product_name";
            	//h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            	//if(!$opt) return $out; 
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id=$i;
            	$out[$id] = $row;
            	$i++;
            	}
            	//pre($out);
            	return $out;
            	
            }
            public function get_stock_ledger_open_list($start,$end,$product_id)
            {
            	global $dbc;
            	//$out = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	//$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	//$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	$prev_date = date('Ymd', strtotime($start .' -1 day'));
		        $odate=date('Y')."0401";
            	//$product_id=$_POST['product_id'];
            	$filterstr = $this->oo_filter($filter, $records, $orderby);          	
            	$q="SELECT ROUND(sum(cod.qty+cod.free_qty)) AS oqty,ROUND(sum(cod.product_rate*qty)) AS ovalue FROM challan_order AS co INNER JOIN challan_order_details AS cod ON co.id=cod.ch_id WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$odate' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$prev_date' AND co.ch_dealer_id='$dea_id' AND cod.product_id='$product_id'";
            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');           	
            	$row = mysqli_fetch_assoc($rs);
            	$oqty=$row['oqty'];
            	$ovalue=$row['ovalue'];

            	$qp="SELECT ROUND(sum(pod.quantity+pod.scheme_qty)) AS iqty,ROUND(sum(pod.quantity*pod.rate)) AS ivalue FROM purchase_order AS po INNER JOIN purchase_order_details AS pod ON po.order_id=pod.order_id AND po.challan_no=pod.purchase_inv LEFT JOIN csa ON csa.c_id=po.csa_id WHERE DATE_FORMAT(`created_date`,'%Y%m%d')>='$odate' AND DATE_FORMAT(`created_date`,'%Y%m%d')<='$prev_date' AND DATE_FORMAT(po.receive_date,'%Y-%m-%d')!='0000-00-00' AND po.dealer_id='$dea_id' AND pod.product_id='$product_id'";
            	//h1($qp);
            	$rsp=mysqli_query($dbc,$qp);
            	//if(!$opt) return $out; 
            	$rowp = mysqli_fetch_assoc($rsp);
            	$iqty=$rowp['iqty'];
            	$ivalue=$rowp['ivalue'];

            	$q1="SELECT sum(qty) AS qty,sum(qty*rate) AS value FROM opening_stocks WHERE product_id=".$product_id." AND dealer_id=".$dea_id." GROUP BY product_id";
                //h1($q1);
                $rs1=mysqli_query($dbc,$q1);
                $row1 = mysqli_fetch_assoc($rs1);
                $dqty=$row1['qty'];
                $dvalue=$row1['value'];

                $fqty=($dqty+$iqty)-$oqty;
                //$fqty=abs($fqty);
                $fvalue=($dvalue+$ivalue)-$ovalue;
                //$fvalue=abs($fvalue);
                $out=$fqty."|".$fvalue;
            	return $out;
            	
            }
            public function get_opurchase_stock($cond)
	{
		global $dbc;
		$out = array();
		$q = "SELECT sum(quantity+scheme_qty) AS pqty FROM purchase_order_details INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv INNER JOIN catalog_view ON purchase_order_details.product_id=catalog_view.product_id WHERE $cond";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['pqty'];	
	}
	public function get_oopening_stock($cond)
	{
		global $dbc;
		$out = array();
		$q = "SELECT ROUND(sum(qty)) AS oqty FROM opening_stocks INNER JOIN catalog_view ON opening_stocks.product_id=catalog_view.product_id WHERE $cond";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['oqty'];	
	}
	public function get_obilled_stock($cond)
	{
		global $dbc;
		$out = array();
		$q = "SELECT sum(qty+free_qty) AS bqty FROM challan_order_details 
		INNER JOIN challan_order ON challan_order_details.ch_id=challan_order.id
		 INNER JOIN catalog_view ON challan_order_details.product_id=catalog_view.product_id 
		 INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id
		 WHERE $cond";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['bqty'];	
	}

        
}// class end here
?>
