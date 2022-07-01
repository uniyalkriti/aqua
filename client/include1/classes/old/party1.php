<?php
// This class will handle all the task related to purchase order creation
class party extends myfilter
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
		$d1 = array('ptype'=>$_POST['ptype'], 'name'=>$_POST['name'], 'contact_person'=>$_POST['contact_person'], 'refper'=>$_POST['refper'], 'extra'=>$_POST['extra'], 'bankdetail'=>$_POST['bankdetail'], 'email'=>$_POST['email'], 'mobile'=>$_POST['mobile'], 'phone'=>$_POST['phone'], 'adr'=>$_POST['adr'], 'city'=>$_POST['city'], 'locality'=>$_POST['locality'], 'pincode'=>$_POST['pincode'], 'state'=>$_POST['state'], 'country'=>'India', 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);
		//as commission exist for sale party only
		if($_POST['ptype'] == 2) $d1['commission'] = $_POST['commission']; else $d1['commission'] = 0;
		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function party_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `party` (`pId`, `ptype`, `pname`, `commission`, `contact_person`, `refper`, `bankdetail`, `email`, `mobile`, `phone`, `address`, `locality`, `city`, `pincode`, `state`, `country`, `created`, `crId`, `modified`, `moId`, `extra-detail`) VALUES (NULL , '$d1[ptype]', '$d1[name]',  '$d1[commission]', '$d1[contact_person]', '$d1[refper]', '$d1[bankdetail]', '$d1[email]', '$d1[mobile]', '$d1[phone]', '$d1[adr]', '$d1[locality]', '$d1[city]', '$d1[pincode]', '$d1[state]', '$d1[country]', NOW(), '$d1[uid]', NULL, NULL,'$d1[extra]')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Party table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Party added with Id '.$rId. ' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Party successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given party id
	public function party_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE party SET `pname`='$d1[name]', `commission`='$d1[commission]', `contact_person`='$d1[contact_person]', `refper`='$d1[refper]', `bankdetail`='$d1[bankdetail]',`extra-detail`='$d1[extra]', `email`='$d1[email]', `mobile`='$d1[mobile]', `phone`='$d1[phone]', `address`='$d1[adr]', `locality`='$d1[locality]', `city`='$d1[city]', `pincode`='$d1[pincode]', `state`='$d1[state]', modified=NOW(), `moId`='$d1[uid]' WHERE pId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Party table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Party updated with Id '.$id.' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Party successfully updated', 'rId'=>$id);
	}
	
	//This function will return the list of as reflected from function name
	public function get_party_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(created,'".DTSB."') AS fdated, DATE_FORMAT(created,'".DC."') AS jd_created, DATE_FORMAT(modified,'".DTSB."') AS flastedit, DATE_FORMAT(modified,'".DC."') AS jd_modified FROM party $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['pId'];
			$out[$id]['pId'] = $id; // storing the item id
			$out[$id]['ptype'] = $row['ptype'];
			$out[$id]['name'] = $row['pname'];
			$out[$id]['commission'] = $row['commission'];
			$out[$id]['contact_person'] = $row['contact_person'];
			$out[$id]['refper'] = $row['refper'];
			$out[$id]['bankdetail'] = $row['bankdetail'];
			$out[$id]['extra'] = $row['extra-detail'];
			$out[$id]['email'] = $row['email'];
			$out[$id]['mobile'] = $row['mobile'];
			$out[$id]['phone'] = $row['phone'];
			// Adress related details
			$out[$id]['adr'] = $row['address'];
			$out[$id]['locality'] = $row['locality'];
			$out[$id]['city'] = $row['city'];
			$out[$id]['pincode'] = $row['pincode'];
			$out[$id]['state'] = $row['state'];
			$out[$id]['country'] = $row['country'];
			// Date Related Details
			$out[$id]['created'] = $row['fdated'];
			$out[$id]['jdc'] = $row['jd_created'];
			$out[$id]['modified'] = $row['flastedit'];
			$out[$id]['jdm'] = $row['jd_modified'];
			
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function purchase_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT pId, pname FROM party WHERE ptype=1 ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	//This function can be used when asking for sale party combobox array
	public function sale_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pId, pname FROM party WHERE ptype=2 ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party having GR Made
	public function purchase_gr_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pId, pname FROM party WHERE ptype=1 AND pId IN (SELECT DISTINCT p_party FROM billing INNER JOIN good_return USING(bId)) ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party having GR Made
	public function sale_gr_party_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pId, pname FROM party WHERE ptype=2 AND pId IN (SELECT DISTINCT s_party FROM billing INNER JOIN good_return USING(bId)) ORDER BY pname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['pId']] = $row['pname'];
		}
		return $out;
	}
	
	//This function will return the list of party that can be modified
	public function is_party_modifiable($prId)
	{
		$out = false;
		$status = $this->get_pr_modifiable_items($prId);
		if($status) $out = true;
		return $out;
	}
	
	//This function can be used to retrieve a party name via its id
	public function party_byId($id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT pname as name FROM party WHERE pId = $id";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return '';
		$out = $rs['name'];
		return $out;
	}
}
?>