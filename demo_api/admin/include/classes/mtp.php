<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class mtp extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Department Starts here ####################################################	
	public function get_mtp_se_data()
	{  
		$d1 = array('person_id'=>$_GET['person_id'], 'working_date'=>$_GET['working_date'], 'uid'=>$_SESSION[SESS.'id'],'working_status_id'=>$_GET['working_status_id'],'location_id'=>$_GET['location_id'],'total_calls'=>$_GET['total_calls'],'productive_calls'=>$_GET['productive_calls'],'total_sale_value'=>$_GET['total_sale_value'],'mob_submit_date'=>$_GET['mob_submit_date'],'onwebdatetime'=>$_GET['onwebdatetime']);
                
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Monthly Tour Program'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function mtp_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_mtp_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
               // h1($d1['working_date']);
                $working_date = !empty($d1['working_date']) ? get_mysql_date($d1['working_date']) :'';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `monthly_tour_program` (`id`, `person_id`, `working_date`, `working_status_id`, `location_id`, `total_calls`, `productive_calls`, `total_sale_value`, `mob_submit_date`, `onwebdatetime`) 
		VALUES (NULL , '$d1[person_id]', '$working_date','$d1[working_status_id]','$d1[location_id]','$d1[toal_calls]','$d1[productive_calls]','$d1[total_sale_value]','$d1[mob_submit_date]','$d1[onwebdatetime]')";
             //  h1($q); die;
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Mtp table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        ########### This code is used to save mtp data #################
        public function get_mtp_details_se_data()
	{  
		$d1 = $_POST;
                $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Monthly Tour Program'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function mtp_details_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_mtp_details_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $working_date = !empty($d1['working_date']) ? get_mysql_date($d1['working_date']) :'';
                $category = array();
                $category_array_str = '';
                if(!empty($d1['catalog_1_id']))
                {
                    foreach ($d1['catalog_1_id'] as $key=>$value)
                    {
                        $category[] = $value.'|'.$d1['value'][$key];
                    }
                }
                $category_array_str = implode(',' ,  $category);
                $id = $d1['uid'].date('Ymdhis');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `monthly_tour_program` (`id`, `person_id`, `working_date`, `working_status_id`,`dealer_id`, `locations`, 
		`total_calls`, `total_sales`, `category_wise`,`admin_remark`, `dayname`,`admin_approved`) 
		VALUES ('$id' , '$d1[person_id]', '$working_date','$d1[working_status_id]', '$d1[dealer_id]', '$d1[location_name]',
		'$d1[total_calls]','$d1[total_sales]','$category_array_str','$d1[admin_remark]', '$d1[day]','$d1[admin_approved]')";
         //h1($q);    exit; 
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Mtp could not be saved some error occurred');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function mtp_details_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_mtp_details_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $working_date = !empty($d1['working_date']) ? get_mysql_date($d1['working_date']) :'';
                //SELECT l3.id,l3.name FROM `location_5` l5 INNER JOIN location_4 l4 ON l5.location_4_id = l4.id INNER JOIN location_3 l3 ON l4.location_3_id = l3.id WHERE l5.id IN (8,9) GROUP BY l3.id DESC
               
                $category = array();
                $category_array_str = '';
                if(!empty($d1['catalog_1_id']))
                {
                    foreach ($d1['catalog_1_id'] as $key=>$value)
                    {
                        $category[] = $value.'|'.$d1['value'][$key];
                    }
                }
                $category_array_str = implode(',' ,  $category);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$qlog="INSERT INTO monthly_tour_program_log (`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on`) 
       	    SELECT   `person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on` FROM monthly_tour_program WHERE id='$id'"; 
       	 $rlog=mysqli_query($dbc,$qlog);
		$q = "UPDATE monthly_tour_program SET working_date= '$working_date',dealer_id ='$d1[dealer_id]',locations='$d1[location_name]',
		pc = '$d1[total_calls]',rd='$d1[total_sales]', admin_remark='$d1[admin_remark]',dayname = '$d1[day]' ,`admin_approved`='$d1[admin_approved]',town='$d1[town]',task_of_the_day='$d1[working_status_id]',primary_ord='$d1[primary_ord]',new_outlet='$d1[new_outlet]',collection='$d1[collection]',any_other_task='$d1[any_other_task]'  WHERE id='$id'";
         // h1($q);   exit;
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Mtp could not be updated some error occurred');}
		$rId = $id;	
                
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}
	// this code used for tarcking mobile data using _constant table
	public function tracking_mobile_data($filter='',  $records = '', $orderby='')
	{
		global $dbc;
                $filterstr = $this->oo_filter($filter, $records, $orderby);
		$qq = "SELECT * FROM _constant";
                $rr = mysqli_query($dbc,$qq);
                if($rr)
                {
                    $row1 = mysqli_fetch_assoc($rr);
                    if($row1['tracking_status'] == 1) //here we check wheather tracking time is on or off
                    {
                        $q = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id $filterstr";
                        $r = mysqli_query($dbc ,$q);
                        if($r)
                        {
                                $row = mysqli_fetch_assoc($r); // in $row we are getting person record
                                    $time_tracking = array();
                                    $tracking_time_start = $row1['tracking_time_start'];
                                    $tstart = strtotime($tracking_time_start);
                                    $tracking_time_end = $row1['tracking_time_end'];
                                    $tend = strtotime($tracking_time_end);

                                    //echo gmdate("H::i::s",$ss);
                                    $diff = abs(strtotime($tracking_time_end) - strtotime($tracking_time_start)); 
                                    //$diff =   gmdate("H::i::s",$diff);
                                    $time_tracking['time1'] =  $tracking_time_start;

                                    if($row['tracking_status'] == 1) // wheather tracking is on for person or not
                                    {
                                        
                                        if(!empty($row1['tracking_intervals']))
                                        {
                                            $count_interval = $row1['tracking_count'];
                                            $past_tracking_time = '';
                                            for($i=1; $i<=$count_interval;$i++)
                                            {
                                               
                                                $j = 2 + $i;

                                                if(empty($past_tracking_time))  $past_tracking_time = $tracking_time_start;
                                                $tracking_interval = $row1['tracking_intervals'];
                                                $sum = AddPlayTime ($tracking_interval, $past_tracking_time);
                                                $time_tracking["time$j"] =  $sum;
                                                $past_tracking_time = $sum;

                                            }
                                             $time_tracking['time2'] =  $tracking_time_end ;
                                             $time_tracking['tracking_sleep_minutes'] = $row1['tracking_sleep_minutes'];
                                            $time_tracking['tracking_trials'] = $row1['tracking_trials'];
                                            
                                             return $time_tracking;
                                        } //if(!empty($row1['tracking_intervals'])) end here
                                        else {

                                            $diff = $diff/$row1['tracking_count'];
                                            $tracking_interval =   gmdate("H:i:s",$diff);
                                            for($i=1; $i<=$row1['tracking_count'];$i++)
                                            {
                                              
                                                $j = 2 + $i;
                                               if(empty($past_tracking_time)) 
                                                   $past_tracking_time = $tracking_time_start;

                                                $sum = AddPlayTime ($tracking_interval, $past_tracking_time);
                                                $time_tracking["time$j"] =  $sum;
                                                $past_tracking_time = $sum;

                                            }
                                            $time_tracking['time2'] =  $tracking_time_end ;
                                            $time_tracking['tracking_sleep_minutes'] = $row1['tracking_sleep_minutes'];
                                            $time_tracking['tracking_trials'] = $row1['tracking_trials'];
                                            return $time_tracking;
                                        } // else part end here
                                    } //if($row['tracking_status'] == 1) 
                                
                        } //if($r) end here
                    } //if($row1['tracking_status'] == 1)  end here

                } //if($rr) end here

	}	
	######################################## Tracking Mobile data Ends here ######################################################
        
        ######################################## Tracking Mobile data Ends here ######################################################
        public function get_tracking_time_se_data()
	{  
		$d1 = $_POST;
                
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Tracking'; //whether to do history log or not
		return array(true,$d1);
	}
        
	public function tracking_time_update($id=1)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_tracking_time_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
               
                $past_tracking_minute = '';
                $totminutes = array();
               
               // if(!empty($d1['track']))
               //{
//                    foreach($d1['track'] as $key=>$value)
//                    {
//                        if(empty($past_tracking_minute)) $past_tracking_minute = $d1['tracking_time_start'];
//                        $tt = getTimeDiff("$past_tracking_minute","$value");
//                        $minutes = $this->get_total_minutes($tt);
//                        $totminutes[] = $minutes;
//                       // h1($diff1);
//                        $past_tracking_minute = $value;
//                      
//                    }
                 // $minutes_str = implode(',', $totminutes);
                //}
                if(!empty($d1['track'])) $track_time = implode(',', $d1['track']); else $track_date='';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "UPDATE _constant SET tracking_time_start = '$d1[tracking_time_start]',tracking_time_end = '$d1[tracking_time_end]',tracking_count='$d1[tracking_count]',tracking_intervals='$track_time' WHERE id='$id'";
               
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Mtp table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function get_total_minutes($hm)
        {
            global $dbc;
            $hm = explode(':',$hm);
            $hours = $hm[0];
            $minutes = $hm[1];
            $total_minutes = $hours* 60 + $minutes;
            return $total_minutes;
        }
        // here we get _constant data list
	public function get_constant_datas_list($filter='',  $records = '', $orderby='',$company_id)
	{
		global $dbc;
		$out = array();
                $inc =1;
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM `_constant` WHERE company_id = 1";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			//$id = $row['id'];
                    $id = $inc; 
			$out[$id] = $row; // storing the person id
                    $inc++;
		}
             //  pre($out);
		return $out;
	}
        public function get_constant_data_list($filter='',  $records = '', $orderby='',$company_id)
	{
		global $dbc;
		$out = array();
                $inc =1;
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM `_constant` $filterstr ";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			//$id = $row['id'];
                    $id = $inc; 
			$out[$id] = $row; // storing the person id
                    $inc++;
		}
             //  pre($out);
		return $out;
	}

	public function recursiveall2($code) {
        global $dbc;
//static $data;
        $qry = "";
        $res1 = "";
        $res2 = "";
        $qry = mysqli_query($dbc, "select id  from person where person_id_senior=trim('" . $code . "')");
        $num = mysqli_num_rows($qry);
        if ($num <= 0) {
            $res1 = mysqli_fetch_assoc($qry);
            if ($res1['id'] != "") {
                $_SESSION['juniordata'][] = "'" . $res1['id'] . "'";
            }
        } else {
            while ($res2 = mysqli_fetch_assoc($qry)) {
                if ($res2['id'] != "") {
                    $_SESSION['juniordata'][] = "'" . $res2['id'] . "'";
                    $this->recursiveall2($res2['id']);
                }
            }
        }
    }

    public function recursiveall2_signin($code) {
        global $dbc;
//static $data;
        $qry = "";
        $res1 = "";
        $res2 = "";
       //echo "select id,CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name  from person where person_id_senior=trim('" . $code . "')  AND `manual_attendance`='1'";die;
        $qry = mysqli_query($dbc, "select rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,dealer.name as dealer,dealer.id as did from person INNER JOIN _role ON _role.role_id=person.role_id  INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON dealer.id=udr.dealer_id where person_id_senior=trim('" . $code . "')  AND `manual_attendance`='1' GROUP BY person.id,dealer.id");
       
        $num = mysqli_num_rows($qry);

        if ($num <= 0) {
            $res1 = mysqli_fetch_assoc($qry);
            if ($res1['id'] != "") {
                $_SESSION['juniordata'][] = array('id'=>$res1['id'],'name'=>$res1['full_name'].''.$res1['rolename']);
            }
        } else {
            while ($res2 = mysqli_fetch_assoc($qry)) {
            	//pre($res2);die;
                if ($res2['id'] != "") {
                    $_SESSION['juniordata'][] = array('id'=>$res2['id'],'isr_name'=>$res2['full_name'].'/'.$res2['rolename'],'isr_dealer_id'=>$res2['did'],'isr_dealer_name'=>$res2['dealer']);
                    $this->recursiveall2_signin($res2['id']);
                }
            }
        }
        //pre($_SESSION['juniordata']);die;
    }

        public function get_mtp_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
                $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT monthly_tour_program.*, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
		DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
		DATE_FORMAT(working_date,'%y%m%d') 
		AS sortodate,DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program` 
		INNER JOIN person ON person.id=monthly_tour_program.person_id  $filterstr ";
	                	//h1($q);//die;
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealerid_map = get_my_reference_array('dealer', 'id', 'name'); 
                $working_map = get_my_reference_array('_working_status', 'id', 'name'); 
                $location_map = get_my_reference_array("location_$retailer_level", 'id', 'name'); 
               
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the person id
                        $out[$id]['dealer_name'] = $dealerid_map[$row['dealer_id']];
                        $out[$id]['working_status'] = $working_map[$row['working_status_id']];
                        $out[$id]['task'] = myrowval('_task_of_the_day','task','id='.$row['task_of_the_day']);
                        $out[$id]['location_name'] = $location_map[$row['locations']];
                        $out[$id]['town'] =myrowval('location_4','name','id='.$row['town']);
                        $category = explode(',' , $row['category_wise']);
                        $category_array = array();
                        foreach($category as $key=>$value)
                        {
                           $ca = explode('|', $value);
                           $category_array[$ca[0]] = $ca[1];
                           
                        }
                        $out[$id]['category'] = $category_array;
                        
		}
              //  pre($out);
		return $out;
	}

	        public function get_mtp_details_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
                $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT monthly_tour_program.*, CONCAT_WS(' ',person.first_name, person.middle_name, person.last_name) AS personname, 
		DATE_FORMAT(working_date,'%e-%b-%Y') AS wdate,DATE_FORMAT(working_date,'%W') AS dayname,
		DATE_FORMAT(working_date,'%y%m%d') 
		AS sortodate,DATE_FORMAT(working_date, '%d/%m/%Y') AS working_date FROM `monthly_tour_program` 
		INNER JOIN person ON person.id=monthly_tour_program.person_id  $filterstr ";
	                	//h1($q);//die;
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $dealerid_map = get_my_reference_array('dealer', 'id', 'name'); 
                $working_map = get_my_reference_array('_working_status', 'id', 'name'); 
                $location_map = get_my_reference_array("location_$retailer_level", 'id', 'name'); 
               
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the person id
                        $out[$id]['dealer_name'] = $dealerid_map[$row['dealer_id']];
                        $out[$id]['working_status'] = $working_map[$row['working_status_id']];
                        $out[$id]['task'] = myrowval('_task_of_the_day','task','id='.$row['task_of_the_day']);
                        $out[$id]['location_name'] = $location_map[$row['locations']];
                        $out[$id]['town'] =myrowval('location_4','name','id='.$row['town']);
                        $category = explode(',' , $row['category_wise']);
                        $category_array = array();
                        foreach($category as $key=>$value)
                        {
                           $ca = explode('|', $value);
                           $category_array[$ca[0]] = $ca[1];
                           
                        }
                        $out[$id]['category'] = $category_array;
                        
		}
              //  pre($out);
		return $out;
	}
        public function get_person_name($id)
        {
            global $dbc;
            $out = NULL;
            $q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM person WHERE id = '$id'";
            list($opt,$rs) = run_query($dbc, $q,'single');
            if(!$opt) return $out;
            else return $rs['name'];
        }
        // getting all the dealer records of above person_id
        public function get_user_dealer_list($userid)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		//$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT id,name FROM `user_dealer_retailer` INNER JOIN dealer ON dealer.id = user_dealer_retailer.dealer_id WHERE user_id='$userid'";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row['name']; // storing the person id
		}
		return $out;
	}
        public function get_dealer_location_list($dealerid)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$dlevel = $_SESSION[SESS.'constant']['dealer_level'];
                $rlevel = $_SESSION[SESS.'constant']['retailer_level'];
                
                $q = "SELECT location_$rlevel.id, location_$rlevel.name FROM dealer_location_rate_list INNER JOIN location_$dlevel ON dealer_location_rate_list.location_id = location_$dlevel.id ";
               for($i = $dlevel; $i< $rlevel;$i++ )
               {
                   $j = $i+1;
                   $q .= " INNER JOIN location_$j ON location_$j.location_".$i."_id = location_$i.id";
               }
               $q .= " WHERE dealer_id = '$dealerid'";
               
		//$q = "SELECT id, name FROM `location_$mtype` INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.location_id = location_$mtype.id WHERE dealer_id='$dealerid'";
              
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row['name']; // storing the person id
		}
		return $out;
	}
        public function get_mtp_data()
	{  
		$d1 = $_POST;
                
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Monthly Tour Program'; //whether to do history log or not
		return array(true,$d1);
	}
        public function mtp_update()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_mtp_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
               
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
               // pre($d1);  die; 
                $checkloc=1;
                if(!empty($d1['seniorapproval'])){
                foreach($d1['working_date'] AS $k=>$v){
                $p=$v;
                $id=$d1['eid'][$k];
                $cdate1=$d1['working_date'][$k];
                $working_date = !empty($cdate1) ? get_mysql_date($cdate1) : '';
                $person_id=$d1['person_id'][$k];
                $dealer_id=$d1['dealer_id'][$k];
                $task_of_the_day=$d1['task_of_the_day'][$k];
                $location_id=$d1['location'][$k];
                $total_calls=$d1['total_calls'][$k];
                $total_sales=$d1['total_sales'][$k];
                $admin_remark=$d1['admin_remark'][$k];
                $seniorapproval=$d1['seniorapproval'][$k];
                $pc=$d1['total_calls'][$k];
               $arch=$d1['arch'][$k];
               $collection=$d1['collection'][$k];
            // h1($seniorapproval);
            // h1($id);
           // exit;
    if(!empty($seniorapproval)){


		 $qupdate = "UPDATE monthly_tour_program SET 
                    dealer_id='$dealer_id',locations='$location_id',total_calls = '$total_calls',
                    total_sales='$total_sales',`pc`='$pc',`arch`='$arch',`collection` ='$collection',
                    admin_remark='$admin_remark',`admin_approved`='1',`approved_by`='1',`task_of_the_day`='$task_of_the_day',`approved_on`=NOW() WHERE id=  '$seniorapproval'";
                    //h1($qupdate); exit;
                 
                    $r = mysqli_query($dbc,$qupdate);
                    if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}

			$quplog="INSERT INTO `monthly_tour_program_log`(`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`, `town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on`) SELECT `person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`, `town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `task_of_the_day`, `mobile_save_date_time`, `upload_date_time`, `admin_approved`, `admin_remark`, `pc`, `rd`, `arch`, `collection`, `primary_ord`, `new_outlet`, `any_other_task`, `approved_by`, `approved_on` FROM `monthly_tour_program` WHERE id=  '$seniorapproval'";   
			 $rlog = mysqli_query($dbc,$quplog);    
         }
         
         $checkloc++;
			}
		} else{
          	return array('status'=>false, 'myreason'=>'No Mtp Select');

          }
			             
			
	                    $rId = mysqli_insert_id($dbc);  
                               mysqli_commit($dbc);
        return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);

	}
        //here we get last_level_location list
   
   ###########################################################################
        public function get_last_level_location_list($location_id)
	{
		global $dbc;
		$out = array();
                $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
                $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
                if($dealer_level == $retailer_level) return $location_id;
                $q = "SELECT location_$retailer_level.id,location_$retailer_level.name FROM location_$dealer_level ";
                for($i = $dealer_level;$i<$retailer_level; $i++)
                {
                    $j = $i+1; //6
                    $q .= "INNER JOIN location_$j ON location_$i.id = location_$j.location_".$i."_id ";
                }
                $q .= "WHERE location_5.id IN ($location_id)";
		// if user has send some filter use them.
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$out[] = $row['id']; // storing the person id
		}
                $out = implode(',',$out);
		return $out;
	}
        public function get_user_wise_mtp_data($id , $role_id)
	{
 		global $dbc;
                $main_id = $id;
		$out = array();
                if($role_id == 1) {
                $q = "SELECT person_id FROM monthly_tour_program ORDER BY id DESC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                    $id = $row['person_id'];
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
                  $q = "SELECT person.id FROM person INNER JOIN monthly_tour_program ON monthly_tour_program.person_id = person.id  WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
               
                  list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                  $out[$main_id] = $main_id;
                  if(!$opt) return $out;
                   while($row = mysqli_fetch_assoc($rs)){
                       $out[$row['id']] = $row['id'];  
                   }
              }
              
            return $out;
	}
        
}// class end here
?>
