<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class daily_activity extends myfilter {

    public $poid = NULL;

    public function __construct() {
        parent::__construct();
    }

   
 #####################################################################################
    public function get_productive_call($id,$from) {
        global $dbc;
        $out = NULL;
        $q = "SELECT count(call_status) as c FROM user_sales_order WHERE user_id = $id AND DATE_FORMAT(`date`,'%Y%m%d') =$from";
     // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['c'];
    }
     public function get_user_expense_call($id,$from) {
        global $dbc;
        $out = NULL;
        $q = "SELECT count(travelling_mode_id) as t FROM user_expense_report WHERE person_id = $id AND DATE_FORMAT(`submit_date`,'%Y%m%d') =$from";
      //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['t'];
    }
 #####################################################################################33
    public function get_user_wise_attendence_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT user_id FROM user_daily_attendance ORDER BY user_id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['user_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
           
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt) return $out;   
            $role_id_array = array();

            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT user_id FROM user_daily_attendance INNER JOIN person ON person.id = user_daily_attendance.user_id  WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id' ORDER BY user_id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            $out[$id] = $main_id;
            if (!$opt)  return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['user_id']] = $row['user_id'];
            }
        }

        return $out;
    }
 ######################################## Item Unit Starts here ####################################################	
 public function get_user_daily_act_list($filter = '', $records = '', $orderby = '',$date='') {
    global $dbc;
    $out = array();
    // if user has send some filter use them.
    $filterstr = $this->oo_filter($filter, $records, $orderby);
    
    $q="SELECT distinct daily_reporting.user_id, _working_status.name as workstatusname, person.state_id,daily_reporting.id  as uid , daily_reporting.user_id,daily_reporting.attn_address,daily_reporting.location_id,date_format(`work_date`,'%d-%m-%Y') as
     workdate,date_format(`work_date`,'%H:%i:%s') as worktime , date_format(`server_date_time`,'%d-%m-%Y %H:%i:%s') 
     as server_date,working_with,work_status,_role.rolename as role,
    daily_reporting.dealer_id, CONCAT_WS(' ',person.first_name,person.middle_name,
            person.last_name) as person_fullname ,CONCAT_WS(' ',ps.first_name,ps.middle_name,
            ps.last_name) as senior,lv.l3_name as statename,
    lv.l4_name as town, lv.l1_name as zone, lv.l4_id zoneid, lv.l5_name as beat,daily_reporting.remarks
    FROM daily_reporting  
    LEFT JOIN _working_status ON _working_status.id = daily_reporting.working_with 
    LEFT JOIN person ON person.id = daily_reporting.user_id 
    LEFT JOIN person ps ON ps.id = person.person_id_senior           
    LEFT JOIN user_dealer_retailer ON person.id = user_dealer_retailer.user_id 
    LEFT JOIN dealer_location_rate_list dlrl ON person.id = dlrl.user_id 
    LEFT JOIN dealer ON dealer.id=dlrl.dealer_id
    LEFT JOIN location_view lv ON lv.l5_id = dlrl.location_id
    LEFT JOIN _role ON person.role_id = _role.role_id  $filterstr ";
    //h1($q);
    list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
    
    if (!$opt) return $out; // if no order placed send blank array 
    while ($row = mysqli_fetch_assoc($rs)) {
    $id = $row['uid'];
    //$wdate = $row['wdate'];
    $out[$id] = $row;
    $out[$id]['todate'] = $date;
    //$out[$id]['pcall'] = $this->get_productive_call($row['person_id'],$date);
   //$out[$id]['tcall'] = $this->get_user_expense_call($row['person_id'],$date);
    }
    // pre($out); 
    return $out;
    }
    
    ############################################################################################
   public function get_user_beatcase_data($loc, $uid,$todate) {
        global $dbc;
       //$out = array();
        $q = "SELECT lv.l5_name FROM `daily_reporting` INNER JOIN  location_view as lv ON lv.l5_id=daily_reporting.location_id
         where daily_reporting.location_id='$loc' and user_id='$uid' and DATE_FORMAT(work_date,'%Y%m%d') = '$todate'  ";
     // h1($q);
$rs=mysqli_query($dbc,$q);
         $row = mysqli_fetch_assoc($rs);
       // h1($row['l5_name']);
	 return $row['l5_name'];
       
    }



###################################

}

// class end here
?>
