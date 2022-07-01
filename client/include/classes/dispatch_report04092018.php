<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class dispatch_report extends myfilter
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
        


################## dispatch class####################################



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
$q="SELECT challan_order.id AS id,ch_retailer_id,ch_no as invoice_no,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,amount AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id=$_POST[retailer_id] AND retailer_status='1' GROUP BY date,ch_retailer_id,invoice_no";
}else{
$q="SELECT challan_order.id AS id,ch_retailer_id,ch_no as invoice_no,date_format(ch_date,'%d-%m-%Y') as date,retailer.name AS retailer_name,amount AS total_amt FROM challan_order "
        . "INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id "
        . "WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND retailer_status='1' GROUP BY date,ch_retailer_id,invoice_no";
}
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



            public function get_opening_closing_stock_list($start1,$end1,$filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = $start1;
            	$end = $end1;
            	if(empty($start1)){
            		$start=date('Ymd');
            		$end = date('Ymd');
            	}
            	// if user has send some filter use them.

            	$filterstr = $this->oo_filter($filter, $records, $orderby);
            	$q="SELECT cv.product_name,stock.id AS id,stock.product_id,sum(qty) AS qty,batch_no FROM stock INNER JOIN catalog_view AS cv ON cv.product_id=stock.product_id $filterstr GROUP BY stock.product_id ORDER BY cv.product_name ";

            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            	if(!$opt) return $out; // if no order placed send blank array
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id = $row['id'];
            	$product_id=$row['product_id'];
            	$out[$id] = $row;
            	$stock_cond="id=".$product_id;
            	$cond="dealer_id=".$dea_id." AND product_id=".$row['product_id']." AND date_format(created_date,'%Y%m%d')>='".$start."' AND date_format(created_date,'%Y%m%d')<='".$end."' AND date_format(purchase_order.receive_date,'%Y-%m-%d')!='0000-00-00'";

            	$out[$id]['purchase']= myrowvaljoin('purchase_order_details','SUM(quantity+scheme_qty)','purchase_order','purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no',$cond);
            	$cond1="ch_dealer_id=".$dea_id." AND product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."'";
            	$out[$id]['billed']= myrowvaljoin('challan_order_details','ROUND(SUM(qty+free_qty))','challan_order','challan_order_details.ch_id=challan_order.id',$cond1);
            	//$out[$id]['opening'] = $row['qty']-$out[$id]['purchase'];
				$ostock_cond="product_id=".$product_id." AND dealer_id=".$dea_id." GROUP BY product_id";
				$out[$id]['opening'] = myrowval('opening_stocks', 'sum(qty)', $ostock_cond);
            	}
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

            public function get_stock_ledger_tranc_list($product_id1,$cdate,$filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$out1 = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	$product_id=$product_id1;
            	$filterstr = $this->oo_filter($filter, $records, $orderby);          	
            	$i=1;
            	$q="SELECT co.id AS cid,retailer.name AS pert,cod.batch_no,cod.product_id,cod.id,cv.product_name,ROUND(sum(cod.qty+cod.free_qty)) AS oqty,co.ch_no,DATE_FORMAT(co.ch_date,'%d-%m-%Y') AS ch_date,ROUND((taxable_amt),2) AS ovalue FROM challan_order AS co INNER JOIN challan_order_details AS cod ON co.id=cod.ch_id INNER JOIN catalog_view AS cv ON cv.product_id=cod.product_id INNER JOIN retailer ON retailer.id=co.ch_retailer_id WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')='$cdate' AND co.ch_dealer_id='$dea_id' AND cod.product_id='$product_id' GROUP BY ch_date,ch_no,product_id,batch_no ORDER BY ch_no,product_name";
            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');           	
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id=$i;
            	 $out[$id] = $row;
            	$i++;
            	}
            	
            	$q="SELECT po.id AS cid,csa.csa_name AS pert,pod.batch_no,pod.product_id,pod.id,cv.product_name,ROUND(sum(pod.quantity+pod.scheme_qty)) AS iqty,po.challan_no AS ch_no,DATE_FORMAT(po.created_date,'%d-%m-%Y') AS ch_date,ROUND((total_amount),2) AS ivalue FROM purchase_order AS po INNER JOIN purchase_order_details AS pod ON po.order_id=pod.order_id AND po.challan_no=pod.purchase_inv INNER JOIN catalog_view AS cv ON cv.product_id=pod.product_id LEFT JOIN csa ON csa.c_id=po.csa_id WHERE DATE_FORMAT(`created_date`,'%Y%m%d')='$cdate' AND DATE_FORMAT(po.receive_date,'%Y-%m-%d')!='0000-00-00' AND po.dealer_id='$dea_id' AND pod.product_id='$product_id' GROUP BY ch_date,ch_no,product_id,batch_no ORDER BY ch_no,product_name";
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
            public function get_retailer_ledger_tranc_list($cdate,$filter='', $records = '', $orderby='')
            {
            	global $dbc;
            	$out = array(); 
            	$out1 = array(); 
            	$dea_id = $_SESSION[SESS.'data']['dealer_id'];
            	$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
            	$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
            	$retailer_id=$_POST['retailer_id'];
            	$filterstr = $this->oo_filter($filter, $records, $orderby);          	
            	$i=1;
            	$q="SELECT retailer.name AS pert,co.id AS id,co.ch_retailer_id,co.ch_no,DATE_FORMAT(co.ch_date,'%d-%m-%Y') AS ch_date,ROUND(amount) AS ovalue FROM challan_order AS co INNER JOIN retailer ON retailer.id=co.ch_retailer_id WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')='$cdate' AND co.ch_dealer_id='$dea_id' AND co.ch_retailer_id='$retailer_id' GROUP BY ch_date,ch_no,ch_retailer_id ORDER BY ch_no";
            	 //h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');           	
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id=$i;
            	 $out[$id] = $row;
            	$i++;
            	}

            	$q="SELECT dealer.name AS pert,pc.id AS id,pc.reciept_no AS ch_no,DATE_FORMAT(pc.pay_date_time,'%d-%m-%Y') AS ch_date,ROUND((total_amount),2) AS ivalue FROM payment_collection AS pc INNER JOIN dealer ON dealer.id=pc.dealer_id WHERE DATE_FORMAT(`pay_date_time`,'%Y%m%d')='$cdate' AND pc.dealer_id='$dea_id' AND pc.retailer_id='$retailer_id' GROUP BY ch_date,ch_no,retailer_id ORDER BY ch_no";
            	// h1($q);
            	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');           	
            	while($row = mysqli_fetch_assoc($rs))
            	{
            	$id=$i;
            	 $out[$id] = $row;
            	$i++;
            	}
            	//pre($out);
            	return $out;
            	
            }
                public function get_hsn_wise_list($start,$end,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        if($status=='1'){
        $q="SELECT catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' GROUP BY catalog_product.hsn_code";
        }else if($status=='2'){
        $q="SELECT catalog_product.hsn_code,ROUND(SUM(quantity+scheme_qty)) AS qty,sum(total_amount) AS total_amt,sum(cgst_amount) AS cgst_amount,sum(sgst_amount) AS sgst_amount,sum(igst_amount) AS igst_amount,sum(gross_amt+td_amount+cd_amount+atd_amt+sch_amt+spl_amt) AS taxable_amt FROM catalog_product INNER JOIN purchase_order_details ON purchase_order_details.product_id=catalog_product.id INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no WHERE dealer_id=".$dealer_id." AND DATE_FORMAT(created_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(created_date,'%Y%m%d')<='".$end."' GROUP BY catalog_product.hsn_code";
        }
       //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['hsn_code'];
        $out[$id]=$row;
        }
        return $out;
        }
       public function get_hsn_detailed_list($start,$end,$hsn_code,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
         $party = new retailer();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        if($status==1){
        $q="SELECT challan_order.ch_retailer_id,challan_order.ch_no,retailer.tin_no,DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date,retailer.name AS pert,challan_order.id AS id,catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND catalog_product.hsn_code=".$hsn_code." GROUP BY challan_order.id";
        }elseif($status==2){
        $q="SELECT purchase_order.csa_id,purchase_order.challan_no,csa.gst_no,DATE_FORMAT(created_date,'%d-%m-%Y') AS ch_date,csa.csa_name AS pert,purchase_order.id AS id,catalog_product.hsn_code,ROUND(sum(quantity+scheme_qty)) AS qty,sum(total_amount) AS total_amt,sum(cgst_amount) AS cgst_amount,sum(sgst_amount) AS sgst_amount,sum(igst_amount) AS igst_amount,sum(gross_amt+td_amount+cd_amount+atd_amt+sch_amt+spl_amt) AS taxable_amt FROM catalog_product INNER JOIN purchase_order_details ON purchase_order_details.product_id=catalog_product.id INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no INNER JOIN csa ON csa.c_id=purchase_order.csa_id  WHERE dealer_id=".$dealer_id." AND DATE_FORMAT(created_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(created_date,'%Y%m%d')<='".$end."' AND catalog_product.hsn_code=".$hsn_code." GROUP BY purchase_order.id";
        }
      //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['id'];
        $out[$id]=$row;
        $out[$id]['adr'] = $party->get_retailer_adr($row['ch_retailer_id']);
        }
        return $out;
        }

        public function get_book_wise_list($start,$end,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        if($status==1){
        $q="SELECT challan_order.id,retailer.name AS rname,retailer.tin_no,challan_order.ch_no,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer on retailer.id=challan_order.ch_retailer_id WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='1' GROUP BY challan_order.ch_no";
    }elseif($status==2){
    	$q="SELECT challan_order.id,retailer.name AS rname,retailer.tin_no,challan_order.ch_no,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer on retailer.id=challan_order.ch_retailer_id WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='0' GROUP BY challan_order.ch_no";
    }
       //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['ch_no'];
        $out[$id]=$row;
        }
        return $out;
        }
        public function get_book_wise_doc_list($start,$end,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $wherebf=" ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='1' ORDER BY ch_no ASC";
        $wherebt=" ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='1' ORDER BY ch_no DESC";
        $q="SELECT count(ch_no) AS bch_no FROM challan_order  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='1' ";
      // h1($q);
        $r=mysqli_query($dbc,$q);
		$row=mysqli_fetch_assoc($r);
        $out['bos']['pert'] = "Bill of Supply";
        $out['bos']['bfrom']=myrowval('challan_order','ch_no',$wherebf);
        $out['bos']['bto']=myrowval('challan_order','ch_no',$wherebt);
        $out['bos']['tot']=$row['bch_no'];
        $out['bos']['bcan']=0;


        $wherecf=" ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='0' ORDER BY ch_no ASC";
        $wherect=" ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='0' ORDER BY ch_no DESC";
        $qc="SELECT count(ch_no) AS bch_no FROM challan_order  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order.book_status='0' ";
      // h1($qc);
        $rc=mysqli_query($dbc,$qc);
		$rowc=mysqli_fetch_assoc($rc);
        $out['boc']['pert'] = "Generated Invoice";
        $out['boc']['bfrom']=myrowval('challan_order','ch_no',$wherecf);
        $out['boc']['bto']=myrowval('challan_order','ch_no',$wherect);
        $out['boc']['tot']=$rowc['bch_no'];
        $out['boc']['bcan']=0;
        return $out;
        }
          public function get_bill_detailed_list($start,$end,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
         $party = new retailer();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        if($status==1){
        $q="SELECT challan_order.ch_retailer_id,challan_order.ch_no,retailer.tin_no,DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date,retailer.name AS pert,challan_order.id AS id,catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order_details.tax!='0.00'  GROUP BY challan_order.id";
        }elseif($status==2){
        $q="SELECT challan_order.ch_retailer_id,challan_order.ch_no,retailer.tin_no,DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date,retailer.name AS pert,challan_order.id AS id,catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."'  AND challan_order_details.tax='0.00' GROUP BY challan_order.id";
        }
        elseif($status==3){
        $q="SELECT challan_order.ch_retailer_id,challan_order.ch_no,retailer.tin_no,DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date,retailer.name AS pert,challan_order.id AS id,catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' GROUP BY challan_order.id";
        }
      //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['id'];
        $out[$id]=$row;
        $out[$id]['adr'] = $party->get_retailer_adr($row['ch_retailer_id']);
        }
        return $out;
        }
        public function get_bill_detailed_gst_list($start,$end,$gst,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
         $party = new retailer();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $q="SELECT challan_order.ch_retailer_id,challan_order.ch_no,retailer.tin_no,DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date,retailer.name AS pert,challan_order.id AS id,catalog_product.hsn_code,ROUND(sum(qty)) AS qty,sum(taxable_amt) AS total_amt,sum(vat_amt) AS vat_amt,sum(taxable_amt-vat_amt) AS taxable_amt FROM catalog_product INNER JOIN challan_order_details ON challan_order_details.product_id=catalog_product.id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id INNER JOIN retailer ON retailer.id=challan_order.ch_retailer_id  WHERE ch_dealer_id=".$dealer_id." AND DATE_FORMAT(ch_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(ch_date,'%Y%m%d')<='".$end."' AND challan_order_details.tax='$gst'  GROUP BY challan_order.id";
      //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['id'];
        $out[$id]=$row;
        $out[$id]['adr'] = $party->get_retailer_adr($row['ch_retailer_id']);
        }
        return $out;
        }
        public function get_purchase_detailed_list($start,$end,$status,$filter,  $records = '', $orderby='') {
        global $dbc;
        $out = array();
         $party = new retailer();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        if($status==4){
        $q="SELECT purchase_order.csa_id,purchase_order.challan_no,csa.gst_no,DATE_FORMAT(created_date,'%d-%m-%Y') AS ch_date,csa.csa_name AS pert,purchase_order.id AS id,catalog_product.hsn_code,ROUND(sum(quantity+scheme_qty)) AS qty,sum(total_amount) AS total_amt,sum(cgst_amount) AS cgst_amount,sum(sgst_amount) AS sgst_amount,sum(igst_amount) AS igst_amount,sum(gross_amt+td_amount+cd_amount+atd_amt+sch_amt+spl_amt) AS taxable_amt FROM catalog_product INNER JOIN purchase_order_details ON purchase_order_details.product_id=catalog_product.id INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no INNER JOIN csa ON csa.c_id=purchase_order.csa_id  WHERE dealer_id=".$dealer_id." AND DATE_FORMAT(created_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(created_date,'%Y%m%d')<='".$end."' AND (cgst_amount!=0 AND sgst_amount!=0 OR igst_amount!=0) GROUP BY purchase_order.id";
        }elseif($status==5){
       $q="SELECT purchase_order.csa_id,purchase_order.challan_no,csa.gst_no,DATE_FORMAT(created_date,'%d-%m-%Y') AS ch_date,csa.csa_name AS pert,purchase_order.id AS id,catalog_product.hsn_code,ROUND(sum(quantity+scheme_qty)) AS qty,sum(total_amount) AS total_amt,sum(cgst_amount) AS cgst_amount,sum(sgst_amount) AS sgst_amount,sum(igst_amount) AS igst_amount,sum(gross_amt+td_amount+cd_amount+atd_amt+sch_amt+spl_amt) AS taxable_amt FROM catalog_product INNER JOIN purchase_order_details ON purchase_order_details.product_id=catalog_product.id INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no INNER JOIN csa ON csa.c_id=purchase_order.csa_id  WHERE dealer_id=".$dealer_id." AND DATE_FORMAT(created_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(created_date,'%Y%m%d')<='".$end."' AND cgst_amount=0 AND sgst_amount=0  AND igst_amount=0 GROUP BY purchase_order.id";
        }
        elseif($status==6){
        $q="SELECT purchase_order.csa_id,purchase_order.challan_no,csa.gst_no,DATE_FORMAT(created_date,'%d-%m-%Y') AS ch_date,csa.csa_name AS pert,purchase_order.id AS id,catalog_product.hsn_code,ROUND(sum(quantity+scheme_qty)) AS qty,sum(total_amount) AS total_amt,sum(cgst_amount) AS cgst_amount,sum(sgst_amount) AS sgst_amount,sum(igst_amount) AS igst_amount,sum(gross_amt+td_amount+cd_amount+atd_amt+sch_amt+spl_amt) AS taxable_amt FROM catalog_product INNER JOIN purchase_order_details ON purchase_order_details.product_id=catalog_product.id INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order_details.purchase_inv=purchase_order.challan_no INNER JOIN csa ON csa.c_id=purchase_order.csa_id  WHERE dealer_id=".$dealer_id." AND DATE_FORMAT(created_date,'%Y%m%d')>='".$start."' AND DATE_FORMAT(created_date,'%Y%m%d')<='".$end."'  GROUP BY purchase_order.id";
        }
      //h1($q);
        $r=mysqli_query($dbc,$q);
        while($row=mysqli_fetch_assoc($r)){
        $id=$row['id'];
        $out[$id]=$row;
        $out[$id]['adr'] = $party->get_retailer_adr($row['ch_retailer_id']);
        }
        return $out;
        }
        
}// class end here
?>
