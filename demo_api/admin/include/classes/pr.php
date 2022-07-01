<?php 
class pr extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_pr_se_data()
	{
		//$_POST['deptId'] = 1; $_POST['prnum'] = 2; ;
 		//$_POST['itemId'] = $_POST['qty'] = range(5,9);
 		$d1 = $_POST;
		//$d1 = array('partycode'=>$_POST['partycode'], 'partyname'=>$_POST['partyname'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'fax'=>$_POST['fax'], 'contact_person'=>$_POST['contact_person'], 'website'=>$_POST['website'], 'tinno'=>$_POST['tinno'], 'ecc'=>$_POST['ecc'], 'prange'=>$_POST['prange'], 'division'=>$_POST['division'], 'adr'=>$_POST['adr'], 'locality'=>$_POST['locality'], 'landmark'=>$_POST['landmark'], 'city_district'=>$_POST['city_district'], 'state'=>$_POST['state'], 'pincode'=>$_POST['pincode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'PR'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function pr_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_pr_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$prnum = $this->next_pr_num();
		$q = "INSERT INTO `pr` (`prId`, `prnum`, `sesId`, `deptId`, `crId`, `created`) VALUES (NULL, '$prnum', '$d1[csess]', '$d1[deptId]', '$d1[uid]', NOW())";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'pr Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->pr_extra('save', $rId, $_POST['itemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function pr_extra($actiontype, $rId, $itemId, $qty)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $qty_sum = array();
		// Fetching the old details store for other columns, so that during edit we can delete and reinsert data
		$pr_item_old = $this->get_my_reference_array_direct("SELECT * FROM pr_item WHERE prId = $rId", 'itemId');
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM pr_item WHERE prId = $rId");
		
		foreach($itemId as $key=>$value){
			if(empty($value)) continue;
			$uncode = $rId.$value;
			$qty_sum[] = $qty[$key];
			//To save the value of the other columns as some columns are affected by po
			$pr_item_approve = $po_status = $poId = 0;
			if(isset($pr_item_old[$value])){
				$pr_item_approve = $pr_item_old[$value]['pr_item_approve'];
				$po_status = $pr_item_old[$value]['po_status'];
				$poId = $pr_item_old[$value]['poId'];
			}
			
			$str[] = "('$uncode', $rId, $value, {$qty[$key]}, $pr_item_approve, $po_status, $poId, $key+1)";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `pr_item` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'pr_item Table error') ;	
		//Update the qty in the database 
		mysqli_query($dbc,"UPDATE pr SET totqty = ".array_sum($qty_sum)." WHERE prId = $rId LIMIT 1");
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	
    public function pr_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_pr_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE pr SET deptId = '$d1[deptId]', modified = NOW(), mrId = $d1[uid] WHERE prId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'pr Table error') ;} 
		$extrawork = $this->pr_extra('update', $id, $_POST['itemId'], $_POST['qty']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_pr_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM pr $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('vendor', 'vendorId', 'vendorname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['prId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['pr_item'] = $this->get_my_reference_array_direct("SELECT * FROM pr_item INNER JOIN item USING(itemId) WHERE prId = $id ORDER BY sortorder ASC", 'prKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	
	public function pr_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "stockId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_stock_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Invoice not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		//$q = "SELECT GROUP_CONCAT(uncode) FROM stock_item GROUP BY stockId HAVING stockId  = 1";
		list($opt, $rs) = run_query($dbc, "SELECT uncode FROM sale_item_uncode INNER JOIN stock_item USING(uncode) WHERE stockId  = $id LIMIT 1");
		if($opt) return array('status'=>false, 'myreason'=>'Invoice cannot be deleted as,<br> some items already sold from this invoice');
		
		//Running the deletion queries
		$delquery = array();
		$delquery['stock'] = "DELETE FROM stock WHERE stockId = $id LIMIT 1";
		$delquery['stock_item'] = "DELETE FROM stock_item WHERE stockId = $id";
		$delquery['stock_tax'] = "DELETE FROM stock_tax WHERE stockId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Invoice successfully deleted');
	}
	
	public function next_pr_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(prnum) AS total FROM pr WHERE sesId = {$_SESSION[SESS.'csess']}";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
	
	public function pr_item_approve($prId)
	{ 
		global $dbc;		
		if(!isset($_POST['itemId'])) return array('status'=>false, 'myreason'=>'Please select atleast 1 item to approve');
		$itemId = $_POST['itemId'];		
		foreach($itemId as $key=>$value){
			$q = "UPDATE pr_item SET pr_item_approve = 1 WHERE prId = $prId AND itemId = $value";
			mysqli_query($dbc, $q);
		}
		return array ('status'=>true, 'myreason'=>'PR item successfully approved.');	
	}
	public function get_pr_list_for_aproval($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(pr.created, '".MASKDATE."') AS createdf FROM pr INNER JOIN pr_item USING(prId) INNER JOIN item USING(itemId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('vendor', 'vendorId', 'vendorname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['prKey'];
			$out[$id] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function pr_approve($prId)
	{ 
		global $dbc;
		$pr_to_approve = implode(',', $prId);
		$q = "UPDATE pr_item SET po_status = 1 WHERE prKey IN ($pr_to_approve)";
		if(mysqli_query($dbc, $q)){
			return array ('status'=>true, 'myreason'=>'Pr successfully approved.');	
		}
		else
			return array ('status'=>false, 'myreason'=>'Sorry, PO could not be approved');	
	}
	
	public function pr_unapprove($prId)
	{ 
		global $dbc;
		$pr_to_approve = implode(',', $prId);
		$q = "UPDATE pr_item SET po_status = 2 WHERE prKey IN ($pr_to_approve)";
		if(mysqli_query($dbc, $q)){
			return array ('status'=>true, 'myreason'=>'Pr successfully unapproved.');	
		}
		else
			return array ('status'=>false, 'myreason'=>'Sorry, Pr could not be approved');	
	}
	public function pr_close()
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS createdf FROM pr $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		//$vendorId_map = get_my_reference_array('vendor', 'vendorId', 'vendorname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['prId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['pr_item'] = $this->get_my_reference_array_direct("SELECT * FROM pr_item INNER JOIN item USING(itemId) WHERE prId = $id ORDER BY sortorder ASC", 'prKey'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
}
?>