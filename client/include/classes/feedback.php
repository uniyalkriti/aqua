<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class feedback extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	######################################## catalog start here ######################################################		
	public function get_feedback_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Feedback';
		return array(true,$d1);
	}
	######################################## van save code  start here ######################################################
	public function feedback_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_feedback_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
                $feedback_no = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		$q = "INSERT INTO `feedback`(`dealer_id`, `date`, `feedback_no`, `feedback_type`, `feedback`, `status`, `server_date_time`) VALUES ('$dealer_id',NOW(),'$feedback_no','$d1[feedback_type]','$d1[feedback]','1',NOW())";
                //h1($q);
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].'Feedback table error');}
		$rId = $feedback_no;	     
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	######################################## van code edit start here ######################################################
	public function feedback_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_feedback_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_feedback_list("vanId = $id");
                 
		$originaldata = $originaldata[$id];
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE `feedback` SET `feedback_type`='$d1[feedback_type]',`feedback`='$d1[feedback]',`server_date_time`=NOW() WHERE id='$id'";
               // h1($q);exit;
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].'Feedback Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	######################################## van list code  start here ######################################################
	public function get_feedback_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT `feedback`.`id` AS id, `dealer_id`, `date`, `feedback_no`, `feedback_type`, `feedback`, `status`, `server_date_time`,_feedback_type.name AS feedback_type_name FROM `feedback` INNER JOIN `_feedback_type` ON `feedback`.`feedback_type`=`_feedback_type`.`id`  $filterstr ";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
   
}// class end here
?>
