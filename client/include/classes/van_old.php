<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class van extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	######################################## catalog start here ######################################################		
	public function get_van_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Van';
		return array(true,$d1);
	}
	######################################## van save code  start here ######################################################
	public function van_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_van_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
                $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		$q = "INSERT INTO `van` (`vanId`, `van_no`, `mobile`, `license_no`, `address`, `driver_name`, `vtype`, `company_id`,`dealer_id`,`capacity`) VALUES ('$id', '$d1[van_no]', '$d1[mobile]', '$d1[license_no]', '$d1[address]', '$d1[driver_name]', '1', '{$_SESSION[SESS.'data']['company_id']}','$dealer_id','$d1[capacity]')";
                //h1($q);
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].'Van table error');}
		$rId = $id;	
             
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	######################################## van code edit start here ######################################################
	public function van_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_van_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_van_list("vanId = $id");
                 
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
                    $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE van SET `van_no` = '$d1[van_no]', company_id = '$d1[company_id]',mobile = '$d1[mobile]', license_no = '$d1[license_no]', address = '$d1[address]', driver_name = '$d1[driver_name]',`dealer_id` = '$dealer_id' WHERE vanId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].'Van Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	######################################## van list code  start here ######################################################
	public function get_van_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM van  $filterstr ";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['vanId'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
   
}// class end here
?>
