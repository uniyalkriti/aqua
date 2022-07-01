<?php
// This class will handle all the task related to purchase order creation
class gr extends myfilter
{
	public function __construct($id = NULL)
	{
		parent::__construct();
		//$this->id = $id;
	}
	
	// This function will return the next available po no
	public function get_voucher_no()
	{
		global $dbc;
		$q = "SELECT MAX(vchr_no) AS pono FROM good_return WHERE sesId={$_SESSION[SESS.'csess']}";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		$pono = (int)$rs['pono'];
		if($pono == 0) $pono = 1; else $pono += 1;
		return $pono;
	}
	
	public function get_se_data($mode='add')
	{
		$d1 = array('vchr_no'=>$_POST['vchr_no'], 's_party'=>$_POST['s_party'], 'p_party'=>$_POST['p_party'], 'gr_no'=>$_POST['gr_no'], 'gr_amount'=>$_POST['gr_amount'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		
		//Converting the sent dates to proper formats
		$d1['gr_date'] = get_mysql_date($_POST['gr_date'], $sep='/', $time = false, $mysqlsearch = false);
		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function gr_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		 $q = "INSERT INTO `good_return` (`grId`, `vchr_no`, `p_party`, `s_party`, `gr_no`, `gr_date`, `gr_amount`, `created`, `crId`, `gr_balance`, `sesId`) VALUES (NULL, '$d1[vchr_no]', '$d1[p_party]', '$d1[s_party]', '$d1[gr_no]','$d1[gr_date]','$d1[gr_amount]', NOW(),'$d1[uid]','$d1[gr_amount]', '$d1[csess]')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'good_return table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Good Return added with Id '.$rId. ' '.$d1['vchr_no']);
		return array('status'=>true, 'myreason'=>'Good Return successfully Saved', 'rId'=>$rId);
	}	
	
	// This function will edit the details of a given party id
	public function gr_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE good_return SET `p_party`='$d1[p_party]', `s_party`='$d1[s_party]', `gr_no`='$d1[gr_no]', `gr_date`='$d1[gr_date]', `gr_amount`='$d1[gr_amount]', `gr_balance`='$d1[gr_amount]', `modified`=NOW(), `moId`='$d1[uid]' WHERE grId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'good_return table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Good Return updated with Id '.$id.' '.$d1['vchr_no']);
		$this->gr_balance_reverify_onedit($id);
		return array('status'=>true, 'myreason'=>'Good Return successfully updated', 'rId'=>$id);
	}
	//This function will compute a gr balance if user modifies the gr amount
	public function gr_balance_reverify_onedit($grId)
	{
		global $dbc;
		$q = "SELECT SUM(gr_adjust_amt) AS gr_adjust_amt FROM payment WHERE grId = $grId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		mysqli_query($dbc,$q="UPDATE good_return SET gr_balance = (gr_amount - {$rs['gr_adjust_amt']}) WHERE grId = $grId LIMIT 1");
	}

//This function will return the list of as reflected from function name
	public function get_gr_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT gr.*, pp.pname as p_partyname, sp.pname as s_partyname, DATE_FORMAT(gr.gr_date,'".DC."') AS gr_date,  DATE_FORMAT(gr.created,'".DTSB."') AS fdated, DATE_FORMAT(gr.created,'".DC."') AS jd_created, DATE_FORMAT(gr.modified,'".DTSB."') AS flastedit, DATE_FORMAT(gr.modified,'".DC."') AS jd_modified FROM good_return AS gr INNER JOIN party AS pp ON pp.pId = gr.p_party INNER JOIN party AS sp ON sp.pId = gr.s_party  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['grId'];
			$out[$id]['grId'] = $id; // storing the item id
			$out[$id]['vchr_no'] = $row['vchr_no'];
			$out[$id]['gr_no'] = $row['gr_no'];
			$out[$id]['gr_amount'] = $row['gr_amount'];
			$out[$id]['gr_balance'] = $row['gr_balance'];
			$out[$id]['gr_date'] = $row['gr_date'];		
			//purchase and sparty details
			$out[$id]['p_party'] = $row['p_party'];
			$out[$id]['p_partyname'] = $row['p_partyname'];
			$out[$id]['s_party'] = $row['s_party'];			
			$out[$id]['s_partyname'] = $row['s_partyname'];	
			// Date Related Details
			$out[$id]['created'] = $row['fdated'];
			$out[$id]['jdc'] = $row['jd_created'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['jdm'] = $row['jd_modified'];
		}
		return $out;
	}
}

?>