<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class settings extends myfilter {

    public $poid = NULL;

    public function __construct() {
        parent::__construct();
    }

    ######################################## Ownership code Starts here ####################################################	

    public function get_ownership_type_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Department'; //whether to do history log or not
        return array(true, $d1);
    }

    public function dealer_ownership_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_ownership_type_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `_dealer_ownership_type` (`id`, `ownership_type`) VALUES (NULL , '$d1[ownership_type]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Ownership table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function dealer_ownership_edit($id) {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_ownership_type_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => 'Please fill all the required information');
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 		
        $q = "UPDATE `_dealer_ownership_type` SET `ownership_type` = '$d1[ownership_type]'  WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Ownership table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'department <strong>'.$d1['deptcode'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully updated', 'rId' => $rId);
    }

    public function get_dealer_ownership_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _dealer_ownership_type $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
        }
        return $out;
    }

    ######################################## Department Ends here ######################################################
    ######################################## Item Group Starts here ####################################################	

    public function get_field_experience_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Field Experiance'; //whether to do history log or not
        return array(true, $d1);
    }

    public function field_experience_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_field_experience_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `_field_experience` (`id`, `experience`) VALUES (NULL , '$d1[experience]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Field Experience table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'category <b>'.$d1['groupname'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
// public function get_user_attd_list($filter = '', $records = '', $orderby = '',$date='') {
//         global $dbc;
//         $out = array();
//         // if user has send some filter use them.
//         $filterstr = $this->oo_filter($filter, $records, $orderby);
     
//          $q="SELECT *, IF( ISNULL( user_daily_attendance.user_id ) , 'ABSENT', 'PRESENT' ) AS attn_status,_working_status.name as working,person.id AS uid,DATE_FORMAT(user_daily_attendance.work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(user_daily_attendance.work_date,'%h:%i:%s %p') AS wtime,DATE_FORMAT(check_out.work_date,'%h:%i:%s %p') as check_out_time,check_out.attn_address as checkout_addr,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM`person` LEFT JOIN check_out ON person.id=check_out.user_id AND DATE_FORMAT(check_out.work_date,'%Y%m%d') ='$date' INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1' left join  user_daily_attendance on person.id=user_daily_attendance.user_id and date_format(user_daily_attendance.work_date,'%Y%m%d') = '$date' INNER JOIN _role ON _role.role_id= person .role_id AND _role.role_group_id =11 LEFT JOIN _working_status ON user_daily_attendance.work_status = _working_status.id INNER JOIN state ON person.state_id=state.stateid $filterstr";
//         //h1($q);
//         list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

//         if (!$opt)  return $out; // if no order placed send blank array  
//         while ($row = mysqli_fetch_assoc($rs)) {
//             $id = $row['uid'];
//             //$wdate = $row['wdate'];
//             $out[$id] = $row;
//             $out[$id]['todate'] = $date;
//             $out[$id]['pcall'] = $this->get_productive_call($row['person_id'],$date);
//             $out[$id]['tcall'] = $this->get_user_expense_call($row['person_id'],$date);
//         }
//          // pre($out);   
//         return $out;
//     }

    public function get_user_attd_list($filter = '', $records = '', $orderby = '',$date='') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // $filterstr='';
       // echo $filterstr;die;
      //  $q = "SELECT *,_working_status.name as working,user_daily_attendance.id AS uid,DATE_FORMAT(work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(work_date,'%h:%i:%s') AS wtime, CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM user_daily_attendance LEFT JOIN user_dealer_retailer USING(user_id) LEFT JOIN dealer_location_rate_list USING(dealer_id) LEFT JOIN person ON person.id = user_dealer_retailer.user_id LEFT JOIN _role USING(role_id) INNER JOIN _working_status ON user_daily_attendance.work_status = _working_status.id $filterstr ";
         //$q ="SELECT *,_working_status.name as working,user_daily_attendance.id AS uid,DATE_FORMAT(work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(work_date,'%h:%i:%s') AS wtime, CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM user_daily_attendance LEFT JOIN person ON person.id = user_daily_attendance.user_id LEFT JOIN _role ON _role.role_id= person .role_id INNER JOIN _working_status ON user_daily_attendance.work_status = _working_status.id  LEFT JOIN state ON person.state_id=state.stateid $filterstr";
         $q="SELECT *, IF( ISNULL( user_daily_attendance.user_id ) , 'ABSENT', 'PRESENT' ) AS attn_status,_working_status.name as working,person.id AS uid,DATE_FORMAT(user_daily_attendance.work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(user_daily_attendance.work_date,'%h:%i:%s %p') AS wtime,DATE_FORMAT(check_out.work_date,'%h:%i:%s %p') as check_out_time,check_out.attn_address as checkout_addr,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM`person` LEFT JOIN check_out ON person.id=check_out.user_id AND DATE_FORMAT(check_out.work_date,'%Y%m%d') ='$date' INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1' left join  user_daily_attendance on person.id=user_daily_attendance.user_id and date_format(user_daily_attendance.work_date,'%Y%m%d') = '$date' INNER JOIN _role ON _role.role_id= person .role_id AND _role.role_group_id =11 LEFT JOIN _working_status ON user_daily_attendance.work_status = _working_status.id INNER JOIN state ON person.state_id=state.stateid $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

        if (!$opt)  return $out; // if no order placed send blank array  
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uid'];
            //$wdate = $row['wdate'];
            $out[$id] = $row;
            $out[$id]['todate'] = $date;
            $out[$id]['pcall'] = $this->get_productive_call($row['person_id'],$date);
            $out[$id]['tcall'] = $this->get_user_expense_call($row['person_id'],$date);
        }
         // pre($out);   
        return $out;
    }
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
    public function field_experience_edit($id) {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_field_experience_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => 'Please fill all the required information');
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 		
        $q = "UPDATE `_field_experience` SET `experience` = '$d1[experience]' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Field Experiance table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'category <strong>'.$d1['groupname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully updated', 'rId' => $rId);
    }

    public function get_field_experience_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM _field_experience $filterstr ";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
        }
        return $out;
    }

    ######################################## Item Group Ends here ######################################################
    ######################################## Item Unit Starts here ####################################################	
public function get_user_atted_list($filter = '', $records = '', $orderby = '',$date='') {
global $dbc;
$out = array();
// if user has send some filter use them.
$filterstr = $this->oo_filter($filter, $records, $orderby);

$q="SELECT *, IF( ISNULL( user_daily_attendance.user_id ) , 'ABSENT', 'PRESENT' ) AS attn_status,_working_status.name as working,person.id AS uid,
DATE_FORMAT(user_daily_attendance.work_date,'%e/%b/%Y') AS wdate,
DATE_FORMAT(user_daily_attendance.work_date,'%h:%i:%s %p') AS wtime,
DATE_FORMAT(check_out.work_date,'%h:%i:%s %p') as check_out_time,
check_out.attn_address as checkout_addr,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename 
FROM`person` LEFT JOIN check_out ON person.id=check_out.user_id 
AND DATE_FORMAT(check_out.work_date,'%Y%m%d') ='$date' 
INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1' left join user_daily_attendance on person.id=user_daily_attendance.user_id and date_format(user_daily_attendance.work_date,'%Y%m%d') = '$date' INNER JOIN _role ON _role.role_id= person .role_id AND _role.role_group_id =11 LEFT JOIN _working_status ON user_daily_attendance.work_status = _working_status.id
 INNER JOIN location_view lv ON person.state_id=lv.l3_id $filterstr";


//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

if (!$opt) return $out; // if no order placed send blank array 
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['uid'];
//$wdate = $row['wdate'];
$out[$id] = $row;
$out[$id]['todate'] = $date;
$out[$id]['pcall'] = $this->get_productive_call($row['person_id'],$date);
$out[$id]['tcall'] = $this->get_user_expense_call($row['person_id'],$date);
}
// pre($out); 
return $out;
}

    public function get_retailer_market_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Retailer Market'; //whether to do history log or not
        return array(true, $d1);
    }

    public function retailer_market_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_retailer_market_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `_retailer_mkt_gift` (`id`, `gift_name`) VALUES (NULL , '$d1[gift_name]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer Gift table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'unit <b>'.$d1['utname'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function retailer_market_edit($id) {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_retailer_market_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => 'Please fill all the required information');
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 		
        $q = "UPDATE `_retailer_mkt_gift` SET `gift_name` = '$d1[gift_name]' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer Gift table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'unit <strong>'.$d1['utname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully updated', 'rId' => $rId);
    }

    public function get_retailer_market_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM _retailer_mkt_gift $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            //$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
        }
        return $out;
    }

    ######################################## Item Unit Ends here ######################################################	
    ######################################## Item start here ######################################################		

    public function get_outlet_type_data() {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];

        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Outlet Type'; //whether to do history log or not
        return array(true, $d1);
    }

    public function outlet_type_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_outlet_type_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `_retailer_outlet_type` (`id`, `outlet_type`) VALUES (NULL, '$d1[outlet_type]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'item table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'item <b>'.$d1['itemname'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function outlet_type_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_outlet_type_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE _retailer_outlet_type SET `outlet_type` = '$d1[outlet_type]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Outlet Type Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_outlet_type_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM _retailer_outlet_type $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    ######################################## Item end here ######################################################	
    ######################################## Process Plan start here ######################################################		

    public function get_travelling_mode_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Travelling Mode'; //whether to do history log or not
        return array(true, $d1);
    }

    public function travelling_mode_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_travelling_mode_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `_travelling_mode` (`id`, `mode`) VALUES (NULL, '$d1[mode]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreasdon' => 'Travelling Mode Table error');
        }
        $rId = mysqli_insert_id($dbc);

        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function travelling_mode_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_travelling_mode_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);

        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE _travelling_mode SET mode = '$d1[mode]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Travelling Mode Table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'process plan <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_travelling_mode_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _travelling_mode $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    ######################################## Process Plan end here ######################################################
    ######################################## Nesting start here ######################################################		

    public function get_working_status_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Working Status'; //whether to do history log or not
        return array(true, $d1);
    }

    public function working_status_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_working_status_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);

        //Uploading the nesting file

        mysqli_query($dbc, "START TRANSACTION");
        //Query to save the data
        $q = "INSERT INTO `_working_status` (`id`, `name`, `parent_id`) VALUES (NULL, '$d1[name]', '$d1[parent_id]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreasdon' => 'Working Status Table error');
        }
        $rId = mysqli_insert_id($dbc);

        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'nesting <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function working_status_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_working_status_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE _working_status SET name = '$d1[name]', parent_id = '$d1[parent_id]' WHERE id ='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Working Status Table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'nesting <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_working_status_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _working_status $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['parent_name'] = $this->get_parent_name($row['parent_id']);
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_parent_name($pid) {
        global $dbc;
        $out = NULL;
        if ($pid == 0)
            return $out;
        $q = "SELECT name FROM _working_status WHERE id = '$pid'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single');
        if (!$opt)
            return $out;
        return $rs['name'];
    }
    // Here we set user attendnace save Update post value
    public function get_user_attendence_save_edit_data()
    {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Department'; //whether to do history log or not
        return array(true, $d1);
    }
    public function user_attendence_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_user_attendence_save_edit_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `user_daily` (`id`, `ownership_type`) VALUES (NULL , '$d1[ownership_type]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Ownership table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'department <b>'.$d1['deptcode'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
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

// Here we set user attendnace save Update post value
   ###################################################################
    // Here we set user attendnace save Update post value
    public function get_user_attendence_se_data()
    {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Attendance'; //whether to do history log or not
        return array(true, $d1);
     
    }
   
    public function user_attendence_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_user_attendence_se_data();
     //   print_r($d1);
     $work_date1=$d1['work_date'];
    // h1( $work_date1);
     $work_date = !empty($work_date1) ? get_mysql_date($work_date1) : '';
      
     $user_id=$d1['eid'];
    $work_status= $d1['work_status'];
    $check_in=$d1['check_in'];
    $check_out=$d1['check_out'];
    if($check_out){

        $datetime_out= $work_date." ". $check_out ;
    }
    
    $wdate=explode('-',$work_date);
    $year=$wdate[0];
    $month=$wdate[1];
    $date=$wdate[2];
   /*    if($date=='1' || $date=='2' || $date=='3' || $date=='4' || $date=='5' || $date=='6' || $date=='7'|| $date=='8' || $date=='9' ){
        $date="0".$date;
    }
if($date=='01' || $date=='02' || $date=='03' || $date=='04' || $date=='05' || $date=='06' || $date=='07'|| $date=='08' || $date=='09' ){
        $date="".$date;
    } */
    $work_date=$year."-".$month."-".$date;
    $datetime_in= $work_date." ". $check_in ;

     // $work_date = get_mysql_date($d1['work_date']);
   //    h1($datetime_in);
       //die;
       $qry="SELECT user_id  from 
      user_daily_attendance where  user_id='$user_id'
      and date_format(user_daily_attendance.work_date,'%Y-%m-%d') = '$work_date' ";
        
        $run= mysqli_query($dbc,$qry);
       $count=mysqli_num_rows($run);
    
        if (!$status)
         return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
       
        if($count>0){

    $q = "UPDATE `user_daily_attendance` SET 
            `work_date`='$datetime_in',`server_date`=NOW(),`work_status`='$work_status',
             `track_addrs`='$d1[attn_address]',
            `remarks`='$d1[remark]' WHERE  `user_id`='$user_id' and date_format(user_daily_attendance.work_date,'%Y-%m-%d') = '$work_date' ";
              
        }
      
        else{

     $q = "INSERT INTO `user_daily_attendance`(`user_id`, `order_id`, `work_date`,
             `server_date`, `work_status`, `user_location`, `mnc_mcc_lat_cellid`,
              `lat_lng`, `track_addrs`, `remarks` )
             VALUES ('$user_id','','$datetime_in',NOW(),'$work_status','','',
             '','$d1[attn_address]','$d1[remark]')";
        }
 
      $r = mysqli_query($dbc, $q);

        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Attendance Table error');
        }

        else{
        $qry1= "SELECT `work_date` ,`user_id`  FROM `check_out`  where  user_id='$user_id'
           and date_format(check_out.work_date,'%Y-%m-%d') = '$work_date' limit 1"; 
            $run= mysqli_query($dbc,$qry1);
            $count=mysqli_num_rows($run);
           if($count>0)
            {
         $up1="UPDATE `check_out` SET `user_id`='$user_id',
               `work_date`='$datetime_out',`server_date_time`=NOW(),`work_status`='$work_status',
                `remarks`='$d1[checkout_remarks]', `attn_address`='$d1[checkout_addr]' 
                 WHERE  `user_id`='$user_id' and date_format(check_out.work_date,'%Y-%m-%d') = '$work_date' ";

            }
            else{
        $up1="INSERT INTO `check_out`(`user_id`, `work_date`, 
                `server_date_time`, `work_status`, `user_location`, `mnc_mcc_lat_cellid`, `lat_lng`,
                 `remarks`, `attn_address`, `image_name`, `order_id`) 
                VALUES ('$user_id','$datetime_out',NOW(),'$work_status','',
                '','','$d1[checkout_remarks]','$d1[checkout_addr]','','')";

            }

    

        }
        $r = mysqli_query($dbc, $up1);
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'nesting <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }
    ###################
    public function get_user_attendence_list($filter = '', $records = '', $orderby = '',$date='') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // $filterstr='';
       // echo $filterstr;die;
      //  $q = "SELECT *,_working_status.name as working,user_daily_attendance.id AS uid,DATE_FORMAT(work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(work_date,'%h:%i:%s') AS wtime, CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM user_daily_attendance LEFT JOIN user_dealer_retailer USING(user_id) LEFT JOIN dealer_location_rate_list USING(dealer_id) LEFT JOIN person ON person.id = user_dealer_retailer.user_id LEFT JOIN _role USING(role_id) INNER JOIN _working_status ON user_daily_attendance.work_status = _working_status.id $filterstr ";
         //$q ="SELECT *,_working_status.name as working,user_daily_attendance.id AS uid,DATE_FORMAT(work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(work_date,'%h:%i:%s') AS wtime, CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM user_daily_attendance LEFT JOIN person ON person.id = user_daily_attendance.user_id LEFT JOIN _role ON _role.role_id= person .role_id INNER JOIN _working_status ON user_daily_attendance.work_status = _working_status.id  LEFT JOIN state ON person.state_id=state.stateid $filterstr";
        
        $q="SELECT uda.track_addrs ,uda.id as uda_id , lv.name statename ,person.mobile ,person.emp_code as emp_code ,uda.work_status as work_status_id, 
        IF( ISNULL( uda.user_id ) , 'ABSENT', 'PRESENT' ) AS attn_status, _working_status.name as working,person.id AS uid, uda.remarks as att_remarks, 
       uda.image_name , DATE_FORMAT(uda.work_date,'%d/%b/%Y') AS wdate, DATE_FORMAT(uda.work_date,'%h:%i:%s %p') AS wtime,
       DATE_FORMAT(uda.work_date,'%H:%i:%s ') AS w_time,
       DATE_FORMAT(uda.work_date,'%Y%m%d') AS work_date, 
         DATE_FORMAT(check_out.work_date,'%h:%i:%s %p') as check_out_time, check_out.attn_address as checkout_addr,
         DATE_FORMAT(check_out.work_date,'%h:%i:%s ') as check_out_time1,
        CONCAT_WS(' ',first_name,middle_name,last_name) AS name,check_out.remarks as checkout_remarks, 
        rolename FROM`person` LEFT JOIN check_out ON person.id=check_out.user_id AND DATE_FORMAT(check_out.work_date,'%Y%m%d')
           ='$date'
            INNER JOIN person_login ON person.id=person_login.person_id 
       left join user_daily_attendance as uda  on person.id=uda.user_id and date_format(uda.work_date,'%Y%m%d') = '$date' 
       INNER JOIN _role ON _role.role_id= person.role_id 
       LEFT JOIN _working_status ON uda.work_status = _working_status.id 
       INNER JOIN location_3 as lv  ON person.state_id=lv.id $filterstr ";
 
        // h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

        if (!$opt)  return $out; // if no order placed send blank array  
        $i=1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uid'];
            $out[$id] = $row;
            $i++;
        }
           //pre($out);  
        return $out;
    }

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

   

    public function delete_common_table_data($table, $id, $field_name, $cls_fun_str, $checking_array, $filter = '', $deletion_msg = '', $wfalse = FALSE, $obj_create = '') {
        global $dbc;
        if (empty($filter))
            $filter = "$field_name = '$id'";
        $out = array('status' => false, 'myreason' => '');
        $cls_fun_str = "get_" . $cls_fun_str . "_list";
        if ($wfalse) {
            $myobj = new $obj_create();
            $deleteRecord = $myobj->$cls_fun_str($filter, $records, $orderby);
        } else {
            $deleteRecord = $this->$cls_fun_str($filter, $records, $orderby);
        }

        if (empty($deleteRecord)) {
            $out['myreason'] = "$table not found";
            return $out;
        }
        //start the transaction
        // checking whether settings is deletable or not
        mysqli_query($dbc, "START TRANSACTION");
        $q = array();
        if (!empty($checking_array)) {
            foreach ($checking_array as $key => $value)
                $q[$key] = "SELECT $value FROM  $key WHERE $value = ";
        }

        if (!empty($q)) {
            $found = false;
            foreach ($q as $key => $value) {
                $q1 = " $value $id LIMIT 1";
                list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'single', $msg = '');
                if ($opt1) {
                    $found = true;
                    $found_case = $key;
                    break;
                }
            }
            // If this item has been found in any one of the above query we can not delete it.			  
            if ($found) {
                $out['myreason'] = "$deletion_msg  entered in <b>$found_case</b> so could not be deleted.";
                return $out;
            }
        } //if(!empty($q)) end here
        //Running the deletion queries
        $delquery = array();
        $delquery["$table"] = "DELETE FROM $table WHERE  $filter";

        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => "$deletion_msg successfully deleted");
    }

    public function get_tax_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Tax'; //whether to do history log or not
        return array(true, $d1);
    }

    public function tax_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_tax_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `_tex` (`id`, `name`,`value`) VALUES (NULL, '$d1[name]','$d1[value]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreasdon' => 'Tax Table error');
        }
        $rId = mysqli_insert_id($dbc);

        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function tax_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_tax_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);

        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE _tax SET name = '$d1[name]', value = '$d1[value]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Tax Table error');
        }
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'process plan <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_tax_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _tax $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = ''); //pre($rs);
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_scheme_se_data() {
        $d1 = $_POST;
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Scheme'; //whether to do history log or not
        return array(true, $d1);
    }

    public function scheme_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_scheme_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);

        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO scheme (`id`, `name`,`created_by`,`created_datetime`,`start_date`,`end_date`) VALUES (NULL, '$d1[name]','" . $_SESSION[SESS . 'data']['id'] . "',NOW(),'" . get_mysql_date($d1[s_date]) . "','" . get_mysql_date($d1[e_date]) . "')";
        $r = mysqli_query($dbc, $q);

        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreasdon' => 'Tax Table error');
        }
        $sid = mysqli_insert_id($dbc);
        if (!empty($d1['product_id'])) {
            foreach ($d1['product_id'] as $key => $val) {
                $pro_qty = $d1['p_q'][$key];
                $sch_qty = $d1['s_q'][$key];
                $q = "INSERT INTO scheme_details (`id`, `scheme_id`,`product_id`,`product_quantity`,`scheme_quantity`) VALUES (NULL, '$sid','$val','$pro_qty',$sch_qty)";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreasdon' => 'Tax Table error');
                }
            }
        }

        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function scheme_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_scheme_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);

        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE scheme SET name = '$d1[name]', created_by = '" . $_SESSION[SESS . 'data']['id'] . "', created_datetime = NOW(),start_date = '" . get_mysql_date($d1[s_date]) . "', end_date= '" . get_mysql_date($d1[e_date]) . "'   WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Travelling Mode Table error');
        }
        $q = "delete from scheme_details where scheme_id = $id";
        $r = mysqli_query($dbc, $q);
        if (!empty($d1['product_id'])) {
            foreach ($d1['product_id'] as $key => $val) {
                $pro_qty = $d1['p_q'][$key];
                $sch_qty = $d1['s_q'][$key];
                $q = "INSERT INTO scheme_details (`id`, `scheme_id`,`product_id`,`product_quantity`,`scheme_quantity`) VALUES (NULL, '$id','$val','$pro_qty',$sch_qty)";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreasdon' => 'Tax Table error');
                }
            }
        }

        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'process plan <strong>'.$d1['itemId'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_scheme_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM scheme  $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = ''); //pre($rs);
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['scheme_qty'] = $this->get_my_reference_array_direct("SELECT * FROM scheme_details WHERE scheme_id = $row[id]", 'id');

            //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    function get_scheme_details_list() {
        global $dbc;
        $out = array();
        //$filterstr=$this->oo_filter($filter, $records, $orderby);
        $q = "select cp.name, sd.product_quantity as pq,sd.scheme_quantity as sq,sd.id from scheme_details as sd INNER JOIN catalog_product cp on sd.product_id = cp.id where scheme_id = $_GET[scheme_id] $orderarray";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = ''); //pre($rs);
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    function get_scheme_dealer_list() {
        global $dbc;
        $out = array();
        //$filterstr=$this->oo_filter($filter, $records, $orderby);
        $q = "select d.id,d.name from dealer d INNER JOIN dealer_location_rate_list dlrl ON d.id = dlrl.dealer_id";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = ''); //pre($rs);
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function scheme_dealer_save() {
        $dealer = array();
        $dealer = $_POST['dealer'];
        global $dbc;
//		$out= array('status'=>'false','myreason'=>'');
//		list($status,$d1)= $this ->get_tax_se_data();  
//		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
        $q = "select * from scheme_to_dealer where scheme_id = $_POST[s_id]";
        $r = mysqli_query($dbc, $q);
        $count = mysqli_num_rows($r);
        if ($count == 0) {
            $dealer_id = implode(',', $dealer);
            mysqli_query($dbc, "START TRANSACTION");
            $q = "INSERT INTO `scheme_to_dealer` (`scheme_id`, `dealer_id`) VALUES ('$_POST[s_id]','$dealer_id')";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreasdon' => 'Tax Table error');
            }
        } else {
            $diff = array();
            $row = mysqli_fetch_array($r);
            $d_id = explode(',', $row['dealer_id']);
            $diff = array_diff($dealer, $d_id);
            if (!empty($diff)) {
                $dealer = array_merge($d_id, $diff);
                $dealer_id = implode(',', $dealer);
                 $dealer_id = trim($dealer_id,',');
            } else {
                $dealer_id = implode(',', $d_id);
                 $dealer_id = trim($dealer_id,',');
            }
            $q = "UPDATE `scheme_to_dealer` set `dealer_id` = '$dealer_id' where scheme_id = $_POST[s_id] ";
            $r = mysqli_query($dbc, $q);
        }

        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    function get_scheme_assign_dealer_list() {

        global $dbc;
        $out = array();
        //$filterstr=$this->oo_filter($filter, $records, $orderby);
        $q = "select dealer_id from scheme_to_dealer where scheme_id = $_GET[id]";
        $r = mysqli_query($dbc, $q);
        $dealer_row = mysqli_fetch_array($r);
        $dealer_id = $dealer_row['dealer_id'];
        if (!empty($dealer_id)) {
            $q = "select d.id, d.name as dname,l3.name as 3name,l2.name as 2name,l1.name as 1name from dealer_location_rate_list dlrl, dealer d, location_3 l3,location_2 l2,location_1 l1  where d.id IN ($dealer_id) AND d.id = dlrl.dealer_id AND dlrl.location_id = l3.id AND l3.location_2_id = l2.id AND l2.location_1_id = l1.id ";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = ''); //pre($rs);
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['id'];
                $out[$id] = $row; // storing the item id
                //echo "SELECT process_plan_item.*, itemname FROM process_plan_item INNER JOIN item USING(itemId) WHERE ppId = $id";
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        }
        return $out;
    }
    
    public function scheme_assign_dealer_save() {
               
        $dealer = array();
        $dealer = $_POST['dealer'];
        global $dbc;

        $q = "select * from scheme_to_dealer where scheme_id = $_POST[s_id]";
        $r = mysqli_query($dbc, $q);
       
            $diff = array();
            $row = mysqli_fetch_array($r);
            $d_id = explode(',', $row['dealer_id']);
            $diff = array_diff($dealer, $d_id);
            if (!empty($diff)) {
                $dealer = array_merge($d_id, $diff);
                 $dealer_id = implode(',', $dealer);
                 $dealer_id = trim($dealer_id,',');
            } else {
                $dealer_id = implode(',', $d_id);
                $dealer_id = trim($dealer_id,',');
            }
          $q1 = "UPDATE `scheme_to_dealer` set `dealer_id` = '$dealer_id' where scheme_id = '$_POST[s_id]'";
            $r1 = mysqli_query($dbc, $q1);     
        if(!$r1)
        {
            echo 'fail';
        }
        //Logging the history
        //history_log($dbc, 'Add', 'process plan <b>'.$d1['itemId'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function get_ess_survey_list($filter = '', $records = '', $orderby = '',$date='') {
            global $dbc;
            $out = array();
            // if user has send some filter use them.
            $filterstr = $this->oo_filter($filter, $records, $orderby);
             $q="SELECT person.id AS pid,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,emp_designation AS rolename,statename,emp_cadre,emp_band,DATE_FORMAT(`emp_date_of_joining`,'%d-%m-%Y') AS edoj,DATE_FORMAT(`date_time`,'%d-%m-%Y') AS cdate,HOD_name,HOD_designation,HOD_cadre,HOD_band,DATE_FORMAT(`HOD_date`,'%d-%m-%Y') AS HOD_date,suggestion_data,unique_id FROM`person` INNER JOIN state ON person.state_id=state.stateid INNER JOIN question_survey_details ON question_survey_details.user_id=person.id $filterstr";
           // h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

            if (!$opt)  return $out; // if no order placed send blank array  
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['pid'];
                $unique=$row['unique_id'];
                $out[$id] = $row;
            }     
            return $out;
        }


}

// class end here
?>
