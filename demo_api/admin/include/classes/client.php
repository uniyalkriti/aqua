<?php
class client extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Department Starts here ####################################################	
	public function get_client_se_data()
	{  
		$d1 = $_POST;
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Dealer Person Login'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function client_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_client_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `dealer_person_login` (`dpId`, `uname`, `dealer_id`,`state_id`, `pass`, `activestatus`, `phone`, `email`, `person_name`, `role_id`) VALUES (NULL , '$d1[uname]', '$d1[dealer_id]','$d1[state_id]',AES_ENCRYPT('$d1[pass]', '".EDSALT."'), '$d1[activestatus]', '$d1[phone]', '$d1[email]', '$d1[person_name]', '$d1[role_id]');";
                //h1($q);
		$r = mysqli_query($dbc,$q) ;
		if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Dealer Person Information could not be Saved');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
                
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function client_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_client_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `dealer_person_login` SET `uname` = '$d1[uname]', `dealer_id` = '$d1[dealer_id]',`state_id` = '$d1[state_id]', `pass`=  AES_ENCRYPT('$d1[pass]', '".EDSALT."'), activestatus = '$d1[activestatus]',person_name = '$d1[person_name]', phone = '$d1[phone]', email = '$d1[email]', role_id = '$d1[role_id]'  WHERE dpId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Dealer person login record Could not updated, Some error occurred.');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'department <strong>'.$d1['deptcode'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_client_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,AES_DECRYPT(pass, '".EDSALT."') as pass FROM dealer_person_login $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $rolemap_id = get_my_reference_array('_role', 'role_id', 'rolename');
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['dpId'];
			$out[$id] = $row; 
                        $out[$id]['rolename'] = $rolemap_id[$row['role_id']]; 
		}
		return $out;
	} 
}
	######################################## Department Ends here ######################################################
?>