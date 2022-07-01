<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class dsp extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}

	######################################## Person Save start here ######################################################		
	public function get_dsp_se_data()
	{  
		$d1 = array();
                $d1=$_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Person'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function dsp_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dsp_se_data();
               // pre($d1);exit();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                //pre($d1); exit();
                $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
               
                mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `person` (`id`,  `first_name`, `middle_name`, `last_name`,  `company_id`, `role_id`, `mobile`, `email`, `person_id_senior`) VALUES (NULL, '$d1[first_name]', '$d1[middle_name]', '$d1[last_name]', '$d1[company_id]', '$d1[role_id]', '$d1[mobile]', '$d1[email]','$d1[person_id_senior]')";
                $r = mysqli_query($dbc,$q) ;
		if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Person table error'); }
                $rId = mysqli_insert_id($dbc);
                
                $q = "INSERT INTO `person_login` (`person_id`, `person_username`, `person_password`, `person_status`) VALUES ('$rId', '$d1[person_username]', '$d1[person_password]', '$d1[person_status]')";
                $r = mysqli_query($dbc, $q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Person Login Table error'); }
                
                $q = "INSERT INTO `person_details` (`person_id`, `address`, `gender`, `dob`, `alternate_number`, `created_on`) VALUES ('$rId', '$d1[address]', '$d1[gender]', '$dob', '$d1[alternate_number]', NOW())";
                $r = mysqli_query($dbc, $q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Person Details table error'); }
                 // if(isset($d1['tin_no']) && (!empty(trim($d1['tin_no'])))){ $tin_no=$d1['tin_no']; } else { $tin_no=0; }
              
                $q = "INSERT INTO `person_finance_details` (`person_id`, `bank_branch_id`, `account_number`,`pan_no`,`tin_no`) "
                         . "VALUES ('$rId', '$d1[bank_branch_id]', '$d1[account_number]', '$d1[pan_no]','0')";
                $r = mysqli_query($dbc, $q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Person Finance Details table error'); }
                $retailer_id = $this->get_retailer_id($d1['dealer_id']);
               
                $str = array();
                if(!empty($retailer_id))
                {
                    foreach ($retailer_id as $key=>$value) $str[] = '(\''.$rId.'\',\''.$d1[dealer_id].'\',\''.$value.'\')'; 
                }
                $str = implode(',' , $str);
                $q = "INSERT INTO user_dealer_retailer (`user_id`, `dealer_id`, `retailer_id`) VALUES $str";
                $r = mysqli_query($dbc,$q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'User table error'); }
                mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'item <b>'.$d1['itemname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
      ######################################## Person Save end here ######################################################	  
        
        ######################################## Person edit start here ######################################################	  
        public function dsp_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_dsp_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
                $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE person SET `first_name` = '$d1[first_name]', `middle_name` = '$d1[middle_name]', `last_name` = '$d1[last_name]', `company_id` = '$d1[company_id]', `role_id` = '$d1[role_id]', `mobile` = '$d1[mobile]', `email` = '$d1[email]' WHERE id ='$id'";
                
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Person Table error') ;} 
		$rId = $id;

                $q = "UPDATE person_login SET `person_username` = '$d1[person_username]', `person_password` = '$d1[upass]', `person_status` = '$d1[person_status]' WHERE person_id = '$id'";
               
                $r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Person Table error') ;}         
                $q = "UPDATE person_details SET `address` = '$d1[address]', `gender` = '$d1[gender]', `dob` = '$dob',`alternate_number` = '$d1[alternate_number]' WHERE person_id = '$id'";
                $r=mysqli_query($dbc, $q);
		
                if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Person Table error') ;} 

                $q = "UPDATE person_finance_details SET `bank_branch_id` = '$d1[bank_branch_id]', `account_number` = '$d1[account_number]',`pan_no`='$d1[pan_no]',`tin_no`='$d1[tin_no]' WHERE person_id = '$id'";
                $r=mysqli_query($dbc, $q);
                
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'person_finance_details error') ;} 
                
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}
        
        public function get_dsp_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, CONCAT_WS(' ',first_name, middle_name, last_name) AS name,DATE_FORMAT(dob,'%d/%m/%Y') AS dob,DATE_FORMAT(last_web_access_on, '%e/%b/%Y AT %r') AS lastvisit,person_password as upass, person_username, email, person_status, rolename "
                        . "FROM person INNER JOIN person_login ON person_login.person_id = person.id "
                        . "INNER JOIN person_details USING(person_id) "
                        . "INNER JOIN _role  USING(role_id) "
                        . "LEFT JOIN person_finance_details USING (person_id)"
                        . "$filterstr";
              h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the person id
		}
		return $out;
	}
        public function get_all_dsp_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
//		//$q = "SELECT * , _dealer_ownership_type.ownership_type as dwname FROM dealer_person_login INNER JOIN dealer ON dealer.id = dealer_person_login.dealer_id "
//                        . " INNER JOIN _dealer_ownership_type ON _dealer_ownership_type.id=dealer.ownership_type_id ";
                       
                $q = "SELECT *,AES_DECRYPT(pass, '".EDSALT."') as pass,DATE_FORMAT(lastvisit,'%d/%m/%Y, %H:%I:%S') AS ldate FROM dealer_person_login INNER JOIN dealer ON dealer.id = dealer_person_login.dealer_id "
                   . " INNER JOIN _role ON _role.role_id=dealer_person_login .role_id $filterstr";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['dpId'];
			$out[$id] = $row; // storing the person id
		}
		return $out;
	}
        
         public function querystrname($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = NULL;		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		return $rs['uname'];
	}
	public function user_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "person.id = '$id'";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_user_list($filter, $records, $orderby);
                
		if(empty($deleteRecord)){ $out['myreason'] = 'person not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['person_login'] = "UPDATE person_login SET person_status = '9' WHERE person_id = '$id'";
                
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
                return array('status'=>true, 'myreason'=>'Person successfully deleted');
	}
     public function get_parent_role($role_id)
     {
        global $dbc;
        $qq = "SELECT role_id,rolename, senior_role_id FROM _role WHERE role_id='$role_id'";
        list($opt,$rs) = run_query($dbc,$qq,'single');
        $str = '';
        if($rs['senior_role_id'] == 0) 
        {
            $str .= $rs['role_id'];
            return $str;
        }
        else{  
            $str .= $rs['senior_role_id'].','.$this->get_parent_role($rs['senior_role_id']);
            return $str;
        }

    }
    // This function is used to get dealer id form person table
    public function get_dealer_id($id)
    {
        global $dbc;
        $out = NULL;
        $q = "SELECT dealer_id FROM dealer_person WHERE person_id = '$id'";
        list($opt,$rs) = run_query($dbc,$q,'single');
        if(!$opt) return $out;
        return $rs['dealer_id'];
    }
    // get retailer id
    public function get_retailer_id($id)
    {
        global $dbc;
        $out = array();
        $q = "SELECT id FROM retailer WHERE dealer_id = '$id'";
        list($opt,$rs) = run_query($dbc,$q,'multi');
        if(!$opt) return $out;
        while($row = mysqli_fetch_assoc($rs))
        {
            $out[$row['id']] = $row['id'];
        }
        return $out;
    }
       
  }// class end here
?>