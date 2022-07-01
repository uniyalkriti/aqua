<?php
// This class will handle all the task related to purchase order creation
class employee extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	public function get_se_data($mode='add')
	{
		$d1 = array('empname'=>$_POST['empname'], 'empcode'=>$_POST['empcode'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function employee_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `employee` (`empId`, `empname`, `empcode`, `created`, `crId`, `modified`, `moId`) VALUES (NULL , '$d1[empname]', '$d1[empcode]', NOW(), '$d1[uid]', NULL, NULL)";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'employee table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Employee added with Id '.$rId. ' '.$d1['empname']);
		return array('status'=>true, 'myreason'=>'Employee successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given party id
	public function employee_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE employee SET `empname`='$d1[empname]', `empcode`='$d1[empcode]', modified=NOW(), `moId`='$d1[uid]' WHERE empId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'employee table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Employee updated with Id '.$id.' '.$d1['empname']);
		return array('status'=>true, 'myreason'=>'Employee successfully updated', 'rId'=>$id);
	}
	
	//This function will return the list of as reflected from function name
	public function get_employee_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'".DTSB."') AS fdated, DATE_FORMAT(created,'".DC."') AS jd_created, DATE_FORMAT(modified,'".DTSB."') AS flastedit, DATE_FORMAT(modified,'".DC."') AS jd_modified FROM employee $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['empId'];
			$out[$id]['empId'] = $id; // storing the item id
			$out[$id]['empname'] = $row['empname'];
			$out[$id]['empcode'] = $row['empcode'];
			// Date Related Details
			$out[$id]['created'] = $row['fdated'];
			$out[$id]['jdc'] = $row['jd_created'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['jdm'] = $row['jd_modified'];
			
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function employee_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT empId AS id, empname AS name FROM employee WHERE empId != 1 ORDER BY empname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			$out[1] = 'N/A';
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['id']] = $row['name'];
		}
		return $out;
	}
}
?>