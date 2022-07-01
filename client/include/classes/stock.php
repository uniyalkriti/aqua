<?php 
class stock extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	######################################## Stock Issue Starts here ####################################################
	public function get_stock_se_data()
	{ 
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Stock Issued'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function stock_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_stock_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$q = "INSERT INTO `stock_issue` (`stockId`, `sesId`, `deptId`, `created`, `crId`, `remark`) VALUES (NULL, '$d1[csess]', '$d1[deptId]', NOW(), '$d1[uid]', '$d1[remark]')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'stock Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->stock_extra('save', $rId, $_POST['itemId'], $_POST['qty']); 
		//$gate = new gate_entry();
		//$stocksave = $gate->stock_save($_POST['itemId'], $_POST['qty'], $transtype = 2);
		$stocksave = save_central_stock(2, '', $_POST['itemId'], $_POST['qty']);
		if(!$extrawork['status'] && !$stocksave['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function stock_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM stock_issue_item WHERE stockId = $rId");
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			$str[] = "('$uncode', $rId, $value, {$qty[$key]})";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `stock_issue_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'stock_item Table error') ;
		
		return array ('status'=>true,'myreason'=>'');
	}
	public function get_stock_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(billdate, '".MASKDATE."') AS billdate FROM stock $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		$vendorId_map = get_my_reference_array('vendor', 'vendorId', 'vendorname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['stockId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['vendorId_val'] = isset($vendorId_map[$row['vendorId']]) ? $vendorId_map[$row['vendorId']] : ''; 
			$out[$id]['stock_item'] = $this->get_my_reference_array_direct("SELECT * FROM stock_item WHERE stockId = $id", 'itemId');
			$out[$id]['stock_tax'] = $this->get_my_reference_array_direct("SELECT * FROM stock_tax WHERE stockId = $id", 'taxId');
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	######################################## Stock Issue Ends here ####################################################
	
	######################################## Stock Return Starts here ####################################################
	public function get_stock_return_se_data()
	{ 
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Stock Return'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function stock_return_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_stock_return_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$billdate = '';
		if(!empty($d1['returndate']))  $returndate = get_mysql_date($d1['returndate'], '/', false, false);
		$q = "INSERT INTO `stock_return` (`srId`, `sesId`, `returndate`, `created`, `crId`, `remark`) VALUES (NULL, '$d1[csess]', '$returndate', NOW(), '$d1[uid]', '$d1[remark]')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'stock return Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->stock_return_extra('save', $rId, $_POST['itemId'], $_POST['qty']); 
		/*$gate = new gate_entry();
		$stocksave = $gate->stock_save($_POST['itemId'], $_POST['qty'], $transtype = 3);*/
		$stocksave = save_central_stock(3, $_POST['returndate'], $_POST['itemId'], $_POST['qty']);
		if(!$extrawork['status'] && !$stocksave['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function stock_return_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM stock_return_item WHERE srId = $rId");
		foreach($itemId as $key=>$value){
			$uncode = $rId.$value;
			$str[] = "('$uncode', $rId, $value, {$qty[$key]})";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `stock_return_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'stock_return_item Table error') ;
		
		return array ('status'=>true,'myreason'=>'');
	}
	public function save_partial_item_return()
	{
		global $dbc;
		$out =  array();
		$crId = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		if(!empty($_POST['ipuId'])){
			foreach($_POST['size'] as $key=>$value){
			$qry = "SELECT itemId FROM item WHERE itemname = '{$_POST['size'][$key]}' LIMIT 1";
				list($opt, $rs) = run_query($dbc, $qry, $mode="single");
				if($opt) 
					$rId = $rs['itemId'];
				else {
					$q ="INSERT INTO item (`itemId`, `utId`, `groupId`, `itemname`, `ithickness`, `created`, `crId`) VALUES(NULL, 2, 8, '{$_POST['size'][$key]}', '{$_POST['ithickness']}', NOW(), $crId)";
					$rs = mysqli_query($dbc, $q) or die('could not insert item');	
					$rId = mysqli_insert_id($dbc);		
				}
				$str ='';
				
					$str .= '(NULL,'.$_POST['ipuId'].' , \''.$rId.'\' , '.$_POST['qty'][$key].', '.$_POST['qty'][$key].'), ';
				}
		}
		$str = rtrim($str,', ');
		$q1 = "INSERT INTO `item_partial_return_qty` (ptrqId, ipuId, itemId, qty, balance) VALUES $str";
		$r1 = mysqli_query($dbc,$q1);
		if(!$r1) return array ('status'=>false, 'myreason'=>'item_partial_return_qty Table error') ;
		$q2 = "UPDATE item_partial_used SET width='$_POST[ithickness]', used_status = 1 WHERE ipuId = '$_POST[ipuId]'";
		$r2 = mysqli_query($dbc,$q2);
		//$gate = new gate_entry();
		//$stocksave = $gate->stock_save($_POST['itemId'], $_POST['qty'], $transtype = 6);
		if(!$r2) return array ('status'=>false, 'myreason'=>'item_partial_used Table error') ;
		
		return array ('status'=>true,'myreason'=>'Partial item successfully inserted');
    	}
	// This function used for get the item stock	
	public function get_item_stock($itemId)
	{
		global $dbc;
		$out = array();
		$q = "SELECT SUM(qty) as qty FROM stock_item WHERE itemId = $itemId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single');
		if($rs['qty']==0) 
			return 0;
		else 
			return $rs['qty'];
	}
	
	// This function used for stcok ledger
	public function get_stock_ledger_report($itemId)
	{
		global $dbc;
		$out = $out1 = $out12 = array();
		$q_stock = "SELECT po_item.itemId, partyname, quality_item.qty, DATE_FORMAT(po.created,'%d/%m/%Y') as podate  FROM quality INNER JOIN quality_item USING(qualityId) INNER JOIN mrr USING(mrrId) INNER JOIN gate USING(gateId) INNER JOIN po USING(poId) INNER JOIN po_item USING(poId) INNER JOIN party USING(partyId) WHERE po_item.itemId = $itemId";
		list($opt, $rs) = run_query($dbc, $q_stock, $mode="multi");
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$out[$id] = $row;
			$out[$id]['podate'] = $row['podate'];
			$stock_qty = $this->get_item_stock($itemId);
			$out[$id]['stock_qty'] = $stock_qty;
		}
		/*$q_retun = "SELECT * FROM stock_return INNER JOIN stock_return_item USING(srId) WHERE itemId = $itemId";
		list($opt1, $rs1) = run_query($dbc, $q_retun, $mode="multi");
		while($row1 = mysqli_fetch_assoc($rs1))
		{
			$id1 = $row1['itemId'];
			$out1[$id1] = $row1;
		}
		$q_issue = "SELECT * FROM stock_issue INNER JOIN stock_issue_item USING(stockId) WHERE itemId = $itemId";
		list($opt2, $rs2) = run_query($dbc, $q_issue, $mode="multi");
		while($row2 = mysqli_fetch_assoc($rs2))
		{
			$id2 = $row2['itemId'];
			$out2[$id2] = $row2;
		}*/
		return $out;
		
	}
        public function get_dealer_balance_stock_list($filter='', $records='', $orderby='')
	{
		global $dbc;
                $out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
		$q_stock = "SELECT * FROM dealer_bal_stock $filterstr";
             
		list($opt, $rs) = run_query($dbc, $q_stock, $mode="multi");
                if(!$opt) return $out;
                $dealermap = get_my_reference_array('dealer', 'id', 'name');
                $catalogmap = get_my_reference_array('catalog_5', 'id', 'name');
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row;
			$out[$id]['username'] = $this->get_person_name($row['user_id']);
                        $out[$id]['dname'] = $dealermap[$row['dealer_id']];
                        $out[$id]['category'] = $catalogmap[$row['catalog_id']];
                        //$out[$id]['dname'] = $dealermap[$row['dealer_id']];
                        
		}
		
		return $out;
		
	}
	######################################## Stock Return Ends here ####################################################
}
?>