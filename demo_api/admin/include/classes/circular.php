<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class circular extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Department Starts here ####################################################	
	public function get_circular_se_data()
	{  
                $circularopt=$_POST['circularopt'];
                switch ($circularopt){
                     case 'sms':
                     {
                         $d1 = array('circulartype'=>$_POST['circularopt'], 'personid'=>$_POST['personid'],'title'=>$_POST['subtxt'], 'contenttxt'=>$_POST['smstxt'],'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
                         break;
                     }
                     case 'email':
                     {
                         $d1 = array('circulartype'=>$_POST['circularopt'], 'personid'=>$_POST['personid'], 'title'=>$_POST['subtxt'], 'contenttxt'=>$_POST['emailtxt'],'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
                         break;
                     }   
                     case 'notifi':
                     {
                         $image_name = $_FILES['image_name']['name'];
                         $d1 = array('circulartype'=>$_POST['circularopt'], 'personid'=>$_POST['personid'],'title'=>$_POST['subtxt'], 'contenttxt'=>$_POST['notifitxt'],'image_name'=>$image_name,'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
                         break;
                     }                      
                }
                //pre($_FILES);
		//$d1 = array('personid'=>$_POST['personid'], ''=>$_POST['deptname'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Circular'; //whether to do history log or not
		return array(true,$d1);
	}
	
	//public function circular_save()
	// {
	// 	global $dbc;	
	// 	$out = array('status'=>false, 'myreason'=>'');
	// 	list($status, $d1) = $this->get_circular_se_data();
	// 	if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);	
 //                $pid=  join(',',$d1['personid']);
	// 	//start the transaction
	// 	mysqli_query($dbc, "START TRANSACTION");
	// 	// query to save
	// 	$q = "INSERT INTO `circular` (`circular_type`,`title`, `content`, `issued_by_person_id`,`issued_time`,`circular_for_persons`,`status`) VALUES ('$d1[circulartype]','$d1[title]', '$d1[contenttxt]', '$d1[uid]',NOW(),'$pid','1')";
 //                //h1($q);
	// 	$r = mysqli_query($dbc,$q) ;
	// 	if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Circular table error');}
	// 	$rId = mysqli_insert_id($dbc);	
	// 	//mysqli_commit($dbc);
	// 	//Logging the history		
	// 	//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
	// 	//Final success
	// 	 if ($r) {
 //            $q = "INSERT INTO `circular_view` (`circular_id`,`user_id`) VALUES ('$rId','$pid')";
 //            $res = mysqli_query($dbc, $q);
 //            if (!$res) {
 //                mysqli_rollback($dbc);
 //                return array('status' => false, 'myreason' => 'Circular View table error');
 //            }
 //        }
 //        $user_id = array();
 //        $user_id = explode(',', $user_id);
 //     //  pre($user_id);
                     
 //            $q = "UPDATE `person_login` SET `circular_id`= '$rId' where person_id = $pid";
 //           // h1($q);
 //            $res2 = mysqli_query($dbc, $q);
 //            if (!$res2) {
 //                $check = 1;
 //                mysqli_rollback($dbc);
 //                return array('status' => false, 'myreason' => 'Circular table error');
 //            }
 //        mysqli_commit($dbc);
 //        //Logging the history		
 //        //history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
 //        //Final success
 //        return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
	// }
	public function circular_save()
    {
        global $dbc;    
        $out = array('status'=>false, 'myreason'=>'');
        list($status, $d1) = $this->get_circular_se_data();
        if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);   
                $person = $d1['personid'];
                $p_id = count($d1['personid']);              
              // pre($d1); 
                if(!empty($person)){  
                 $c = 0;
                while ($c < $p_id) {
        mysqli_query($dbc, "START TRANSACTION");
        
        // $catalog_path = $upload_path[8]['documents_location'];
                $noti_path = MYUPLOADS."/notification/$d1[image_name]"; 
                //pre($_FILES); 
                $browse_file = $d1['image_name'];
                $tmp_name=$_FILES['image_name']['tmp_name'];
                
                if(!empty($browse_file))
                {
                    $upl= move_uploaded_file($tmp_name, $noti_path);
                   
                }
             //   exit;
               
        // query to save
        $q = "INSERT INTO `circular` (`circular_type`,`title`, `content`, `issued_by_person_id`,`issued_time`,`circular_for_persons`,`image`) VALUES ('$d1[circulartype]','$d1[title]', '$d1[contenttxt]', '$d1[uid]',NOW(),'$person[$c]','$browse_file')";
               // h1($q);
        $r = mysqli_query($dbc,$q) ; //exit;
        if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Circular table errorrrrrrrrrrr');}
        $rId = mysqli_insert_id($dbc);            
                        
                $q = "UPDATE `person_login` SET `circular_id`= '$rId' where person_id = $person[$c]";
               // h1($q);
                $res2 = mysqli_query($dbc, $q);
                if (!$res2) {
                    $check = 1;
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => 'person_login table error');
                }
                    $c++;
                 }
             }else{
                  return array('status' => false, 'myreason' => 'Person Not Selected');
             }
        mysqli_commit($dbc);        
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
    }
	
	public function circular_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_circular_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//Checking whether the original data was modified or not
		$originaldata = $this->get_department_list("deptId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `department` SET `deptcode` = '$d1[deptcode]', `deptname` = '$d1[deptname]'  WHERE deptId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'department <strong>'.$d1['deptcode'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	//This function gives extra utility list wheather you show extra responsiblity
	public function get_person_circular_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = " SELECT *,person.id AS rId,CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM person "
                           . " INNER JOIN person_login ON person_login.person_id = person.id INNER JOIN _role USING(role_id) INNER JOIN user_dealer_retailer ON person.id=user_dealer_retailer.user_id $filterstr ";
              //  h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['rId'];
			$out[$id] = $row; 
		}
		return $out;
	} 
	######################################## Department Ends here ######################################################
	 public function get_circular_report_list($filter='',  $records = '', $orderby='')
    {
        global $dbc;
        $out = array();        
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
                $q= "SELECT `circular`.`id` AS cid,CONCAT_WS(' ',first_name,middle_name,last_name) AS name, `circular_type`, `title`, `content`, `issued_by_person_id`, DATE_FORMAT(`issued_time`,'%d-%m-%Y') AS cdate, `circular_for_persons`, `status`, `image` FROM `circular` "
                        . " INNER JOIN person ON person.id = `circular_for_persons` INNER JOIN location_view ON location_view.l2_id=person.state_id $filterstr";
                
               // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        while($row = mysqli_fetch_assoc($rs))
        {
            $id = $row['cid'];
            $out[$id] = $row;
        }
        return $out;
    } 
}// class end here
?>