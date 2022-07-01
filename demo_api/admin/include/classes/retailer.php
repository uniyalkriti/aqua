<?php 
class retailer extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	

	######################################## WORK PO Starts here ####################################################
	public function get_retailer_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
                    $d1['urole'] = $_SESSION[SESS.'data']['urole'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Retailer'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function retailer_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_retailer_se_data();  
               
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $upload_path = $this->get_document_type_list($filter="id IN (1)",  $records = '', $orderby='');
                $retailer_path = $upload_path[1]['documents_location'];
                $retailer_path = MYUPLOADS.$retailer_path;
                
                $browse_file = $_FILES['image_name']['name'];
                if(!empty( $browse_file))
                {
                    list($uploadstat, $filename) = fileupload('image_name', $retailer_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
                    if($uploadstat) 
                    {
                            resizeimage($filename, $retailer_path, $newwidth=400, $thumbnailwidth=200, MSYM, $thumbnail = true);			
                    }
                }
                else $filename = '';
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
                $location = "location_".$mtype."_id";
		
                //Start the transaction
                $maxid = $d1['user_id'].date('Ymdhis');
		mysqli_query($dbc, "START TRANSACTION");
                $q = "INSERT INTO `retailer` (`id`, `name`, `image_name`,`location_id`, `address`, `email`, `landline`, `other_numbers`, 
                `tin_no`, `pin_no`,`outlet_type_id`,`avg_per_month_pur`,`created_on`, `created_by_person_id`,`dealer_id`, `company_id`,`retailer_status`) 
			VALUES ($maxid, '$d1[name]','$filename' ,'$d1[$location]', '$d1[address]', '$d1[email]', '$d1[landline]', '$d1[other_numbers]',
			'$d1[tin_no]','$d1[pin_no]','$d1[outlet_type_id]','$d1[avg_per_month_pur]',NOW(),'$d1[uid]','$d1[dealer_id]',
			 '{$_SESSION[SESS.'data']['company_id']}','$d1[status]' );";               
                
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ;} 
		$rId = $maxid;	
                if($d1['urole'] != 1) {
                     $q = "INSERT INTO `user_dealer_retailer` (`user_id`, `dealer_id`, `retailer_id`) VALUES ('$d1[user_id]', '$d1[dealer_id]', '$maxid')";
                   
                    $r = mysqli_query($dbc, $q);

                    if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ;} 
                }
               
		mysqli_commit($dbc);
		//Logging the history
		//history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$maxid);
	}
	
    public function retailer_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_retailer_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
                //code for uploading retailer images
                $upload_path = $this->get_document_type_list($filter="id IN (1)",  $records = '', $orderby='');
                $retailer_path = $upload_path[1]['documents_location'];
                $retailer_path = MYUPLOADS.$retailer_path;
                $browse_file = $_FILES['image_name']['name'];
                if(!empty( $browse_file))
                {
                    list($uploadstat, $filename) = fileupload('image_name', $retailer_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
                    if($uploadstat) 
                    {
                            resizeimage($filename, $retailer_path, $newwidth=400, $thumbnailwidth=200, MSYM, $thumbnail = true);			
                    }
                }
                else $filename = $d1['old_image'];
		//Checking whether the original data was modified or not
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
                $location = "location_".$mtype."_id";
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//query to update
		$q="UPDATE retailer SET name = '$d1[name]', address = '$d1[address]', email = '$d1[email]', landline = '$d1[landline]',
		 other_numbers = '$d1[other_numbers]', tin_no = '$d1[tin_no]', pin_no = '$d1[pin_no]', `location_id` = '$d1[$location]',
		 `outlet_type_id`='$d1[outlet_type_id]',`avg_per_month_pur`='$d1[avg_per_month_pur]', image_name = '$filename', dealer_id = '$d1[dealer_id]', 
		 `company_id` = '{$_SESSION[SESS.'data']['company_id']}' ,`retailer_status`='$d1[status]' WHERE id = '$id'";
		
                $r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer could not be updated, some error occurred.') ;} 
		$rId = $id;
               
                if($d1['urole'] != 1)
                {
                    $q = "DELETE FROM `user_dealer_retailer` WHERE user_id = '$d1[user_id]' AND dealer_id = '$d1[dealer_id]' AND retailer_id = '$rId'";
                    $r = mysqli_query($dbc,$q);
                    if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ;} 
                    $q = "INSERT INTO `user_dealer_retailer` (`user_id`, `dealer_id`, `retailer_id`) VALUES ('$d1[user_id]', '$d1[dealer_id]', '$rId')";
                    $r = mysqli_query($dbc, $q);
                    if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ;} 
                }
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function get_retailer_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                $records='';
                //print_r($filter); exit;
		$filterstr=$this->oo_filter($filter, $records, $orderby);
             //   h1($filterstr);exit;
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];               
		$q = "SELECT _outlet_types.name as ret_type,DATE_FORMAT(created_on,'%d-%m-%Y %H:%i:%s') as created_date ,retailer.*,lv.l1_name as zone,lv.l2_name as state,lv.l3_name as city,lv.l5_name as beat,dealer.name as dname,retailer.name as name,
                        retailer.id as id,retailer.email as email,retailer.landline as landline,retailer.address as address,
                        retailer.pin_no as pin FROM retailer 
                        INNER JOIN dealer ON retailer.dealer_id = dealer.id 
                        INNER JOIN _outlet_types ON _outlet_types.id=retailer.outlet_type_id
                        INNER JOIN location_view lv ON lv.l5_id=retailer.location_id $filterstr";
		
//             h1($q);
            //  $rs = mysqli_query($dbc,$q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['id'];
			$out[$id] = $row; // storing the retailer id	
		} // while($row = mysqli_fetch_assoc($rs)){ ends
               // pre($out);
                //echo "ank";
		return $out;	
	}
        // Here we get retailer address
        public function get_retailer_adr($id, $seperator='<br>')
	{
		global $dbc;
		$out = '';
		$q = "SELECT * FROM retailer WHERE id = $id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$out = address_representer($rs, array('address','pin_no', 'tin_no','landline','other_numbers'), $seperator);
		return $out;
	} 
        
        public function get_retailer_location_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
                $loop = $mtype - 1;
                $str = '';
                for($k = $mtype; $k>=1; $k--)
               {
                   $str .= ",location_$k.name AS name$k,location_$k.id AS location_".$k."_id ";
               }
                $q = "SELECT * $str FROM retailer INNER JOIN  location_$mtype ON location_$mtype.id = retailer.location_id ";
                for($i = $mtype; $i>1;$i--)
                {
                    $j = $i - 1; 
                    $q .= "INNER JOIN location_$j ON location_$i.location_".$j."_id = location_$j.id ";
                }
               $q .= "$filterstr";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["location_".$mtype."_id"];
			$out[$id] = $row; // storing the item id
                           
		}
		return $out;
	}
	
	public function get_groupname($id)
	{
		global $dbc;
		$out = array();
		$q = "SELECT group_name FROM _role_group WHERE id = '$id'";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['group_name'];	
	}
        public function get_role_id($id)
	{
		global $dbc;
		$out = array();
		$q = "SELECT role_id FROM _role WHERE role_group_id = '$id'";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs))
                {
                    $out[] = $row['role_id'];
                }
                $out = implode(',',$out);
                return $out;
			
	}
        public function get_retailer_person_icon($id)
	{
		global $dbc;
		$out = array();
		$q = "SELECT *,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,DATE_FORMAT(last_web_access_on,'%e/%m/%Y AT %r') AS lastlogin "
                        . "FROM retailer_person "
                        . "INNER JOIN person ON person.id = retailer_person.person_id "
                        . "INNER JOIN person_login ON person_login.person_id = person.id "
                        . "INNER JOIN _role USING(role_id) "
                      //  . "INNER JOIN user_dealer_retailer udr ON udr.retailer_id=retailer_person.retailer_id "
                        . "WHERE retailer_person.retailer_id = '$id'";
		//echo $q;
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs))
                {
                   // $id = $row['dealer_id'].$row['person_id'];
                    $id = $row['person_id'];
                    $out[$id] = $row;
                    
                }
                
                return $out;
			
	}
  
        public function get_document_type_list($filter='',  $records = '', $orderby='')
	 {
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q = "SELECT * FROM _document_type $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
                           
		}
		return $out;
	}         
        public function get_user_wise_retailer_data($id , $role_id)
	{
 		global $dbc;
		$out = array();
                $main_id = $id;
                if($role_id == 1 ||$role_id == 50) {
                $q = "SELECT id FROM retailer ORDER BY id DESC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                    $id = $row['id'];
                    $out[$id] = $id; // storing the item id
                 }// while($row = mysqli_fetch_assoc($rs)){ ends
              } // if($role_id == 1) end here
              else {
                    $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
                   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                   if(!$opt) return $out;
                   $role_id_array = array();
                   
                   while($row = mysqli_fetch_assoc($rs)){
                       $role_id_array[$row['role_id']] = $row['role_id'];  
                   }
                  $role_id_str = implode(',',$role_id_array);
                  $q = "SELECT id FROM person WHERE role_id IN ($role_id_str) AND person_id_senior='$main_id'";
                  list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                  if($opt){
                   $user_data[$main_id] = $main_id;
                   while($row = mysqli_fetch_assoc($rs)){
                       $user_data[$row['id']] = $row['id'];  
                   }
                   $user_id_str = implode(',',$user_data);
                   $q = "SELECT retailer_id FROM user_dealer_retailer WHERE user_id IN ($user_id_str)";
                   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                   if(!$opt) return $out;
                     while($row = mysqli_fetch_assoc($rs)){
                       $out[$row['retailer_id']] = $row['retailer_id'];  
                   }
                 } //if($opt) end here
                 else {
                     $q = "SELECT retailer_id FROM user_dealer_retailer WHERE user_id = '$main_id'";
                     list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                     if(!$opt) return $out;
                     while($row = mysqli_fetch_assoc($rs))
                     {
                        $out[$row['retailer_id']] = $row['retailer_id'];  
                     }
                     
                 } // else part end here
              }
             // pre($out); exit;
            return $out;
	}
        public function get_user_wise_retailer_location_data($id , $role_id)
	{
 		global $dbc;
		$out = array();
                $main_id = $id;
                if($role_id == 1) return $out;
                $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) $out[$id] = $id;
                if($opt)
                {
                    $role_id_array = array();
                    while($row = mysqli_fetch_assoc($rs)){
                        $role_id_array[$row['role_id']] = $row['role_id'];  
                    }
                  $role_id_str = implode(',',$role_id_array);
                  $q = "SELECT id FROM person WHERE role_id IN ($role_id_str) AND person_id_senior='$main_id'";
                
                  list($opt,$rs) = run_query($dbc,$q,'multi');
                  $out[$id] = $id;
                  if($opt){
                  while($row = mysqli_fetch_assoc($rs)){
                       $out[$row['id']] = $row['id'];  
                   }
                  } // if($opt) end here
                }
                return $out;
        }
        
	######################################## WORK FOR MULTIPLE COMPANY WISE RETAILER START HERE ####################################################
	
	 ################################################################   REG. to master Move ####################################

    public function get_retailer_move_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Retailer Move'; //whether to do history log or not
        return array(true, $d1);
    }

    public function get_retailer_move_list($filter, $records = '', $orderby = "") {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT retailer.name,retailer.id as uid,l4_id as zone_id,l2_name as state,dealer.name as dealer,l5_name FROM retailer 
            INNER JOIN dealer ON dealer.id=retailer.dealer_id INNER JOIN location_view lv
             ON lv.l5_id=retailer.location_id
            $filterstr  GROUP BY retailer.id ORDER BY l4_id ASC";
      // h1($q);
        $run = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_assoc($run)) {
            $id = $row['uid'];
            $out[$id] = $row;
        }
        //pre($out);
        return $out;
    }

    public function retailer_move() {
        global $dbc;
        $out = array('status'=>'false','myreason'=>'');
        list($status,$d1)=$this->get_retailer_move_se_data();
        if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
        $retailersid=implode($d1[retailer_id],',');
        $retailers_id = str_replace('on,', '', $retailersid);
        
     //   pre($d1);die;
        mysqli_query($dbc, "START TRANSACTION");
       // die;
        $insert_retailer_log ="insert into retailer_log(retailer_id,name,image_name,dealer_id,location_id,company_id,address,email,"
                . "contact_per_name,landline,other_numbers,tin_no,pin_no,outlet_type_id,card_swipe,bank_branch_id,"
                . "current_account,avg_per_month_pur,lat_long,mncmcclatcellid,track_address,created_on,created_by_person_id,"
                . "status,sync_status,retailer_status) SELECT id,name,image_name,dealer_id,location_id,company_id,address,email,"
                . "contact_per_name,landline,other_numbers,tin_no,pin_no,outlet_type_id,card_swipe,bank_branch_id,current_account,"
                . "avg_per_month_pur,lat_long,mncmcclatcellid,track_address,created_on,created_by_person_id,status,"
                . "sync_status,retailer_status from retailer WHERE id IN ( $retailers_id ) ";
       // h1($insert_retailer_log);
        $insert_retailer_run = mysqli_query($dbc, $insert_retailer_log);    
        if($insert_retailer_run){
        $update_retailer ="UPDATE `retailer` SET dealer_id='$d1[move_dealer_id]',location_id='$d1[move_beat_id]' WHERE id IN ( $retailers_id ) ";
        $update_retailer_run = mysqli_query($dbc, $update_retailer);
        }
        
        if(!$update_retailer_run){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;}
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => "Retailer Successfully Moved");
    }
    
    public function retailer_move_copy() {
        global $dbc;
        $out = array('status'=>'false','myreason'=>'');
        list($status,$d1)=$this->get_retailer_move_se_data();
        if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
        mysqli_query($dbc, "START TRANSACTION");
        foreach ($d1['retailer_id'] as $rkey => $rvalue) {
          If($rkey!=0){
        $q_cr = "CREATE TEMPORARY TABLE tmptable_retailer SELECT * FROM retailer WHERE id=$rvalue";
      //  h1($q_cr);
        $run_q_cr=mysqli_query($dbc, $q_cr);
        if(!$run_q_cr){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;}
        
        $q_up = " UPDATE tmptable_retailer SET id = NULL,dealer_id=$d1[move_dealer_id],location_id=$d1[move_beat_id]";
       // h1($q_up);     
        $run_q_up=mysqli_query($dbc, $q_up);   
              if(!$run_q_up){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;}
              
        $q_in = " INSERT INTO retailer SELECT * FROM tmptable_retailer";
       // h1($q_in);  
        $run_q_in=mysqli_query($dbc, $q_in);    
             if(!$run_q_in){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;}
            
        $q_dr = "DROP TEMPORARY TABLE IF EXISTS tmptable_retailer;";
      //  h1($q_dr);   
        $run_q_dr= mysqli_query($dbc, $q_dr);
           if(!$run_q_dr){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;}

          
        //return array('status' => true, 'myreason' => "Retailer Successfully Copied");
      }
    }
       mysqli_commit($dbc);
      return array('status' => true, 'myreason' => "Retailer Successfully Copied");
	
    }
     #############################User Dealer Retailer @2018-09-28@#####################
      public function get_user_retailer_details_report_list($filter = '', $records = '', $orderby = '') {
        $holder = array();
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);       

        $mtype = $_SESSION[SESS.'constant']['retailer_level'];               
		$q = "SELECT _outlet_types.name as ret_type,user_dealer_retailer_view.*,DATE_FORMAT(retailer_created,'%d-%m-%Y %H:%i:%s') as created_date  FROM `user_dealer_retailer_view`
                 INNER JOIN _outlet_types ON _outlet_types.id=user_dealer_retailer_view.outlet_type_id $filterstr ";
    //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['retailer_id'];
            $out[$id] = $row;         
        }
        return $out;
    }
  #####
	
}
?>
