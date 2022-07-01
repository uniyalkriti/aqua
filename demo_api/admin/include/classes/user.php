<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class user extends myfilter
{
    public $poid = NULL;

    public function __construct()
    {
        parent::__construct();

    }

    ######################################## Person Save start here ######################################################
    public function get_user_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $labelchange = array(
            'first_name' => 1, 'middle_name' => 1, 'last_name' => 1, 'company_id' => 1,
            'role_id' => 1, 'person_id_senior' => 1, 'mobile' => 1, 'email' => 1, 'imei_number' => 1,
            'address' => 1, 'gender' => 1, 'dob' => 1, 'aniversary' => 1, 'alternate_number' => 1, 'created_on' => 1,
            'deleted_on' => 1, 'person_username' => 1, 'person_password' => 1, 'person_status' => 1,
            'company_id' => 1, 'linkoption' => 1, 'pan_no' => 1, 'tin_no' => 1, 'state_id' => 1
        );
        $d2 = check_post_data($_POST, $labelchange);

        $d1 = $d1 + $d2;

        //pre($d1);exit();
        $d1['uid'] = $_SESSION[SESS . 'id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Person'; //whether to do history log or not
        return array(true, $d1);
    }

    public function user_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_user_se_data();
        // pre($d1);exit();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);
        //pre($d1); exit();
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        $joining_date = !empty($d1['joining_date']) ? get_mysql_date($d1['joining_date']) : '';
        $resigning_date = !empty($d1['resigning_date']) ? get_mysql_date($d1['resigning_date']) : '';

        mysqli_query($dbc, "START TRANSACTION");
        $manual_attendance = isset($d1['manual_attendance']) ? '1' : '0';
        // query to save
//        echo $xp=$d1['person_password'];
//        echo $bpass=bcrypt($xp);die;
        $q = "INSERT INTO `person` (`id`, `position_id`,  `first_name`, `middle_name`, `last_name`,  `company_id`, `role_id`, `person_id_senior`,
		 `mobile`, `email`, `imei_number`,`state_id`,`manual_attendance`,`emp_code`,`product_division`,`joining_date`,`resigning_date`,`head_quar`,`region_txt`) VALUES 
		 ('', '$d1[position]', '$d1[first_name]', '$d1[middle_name]', '$d1[last_name]', '$d1[company_id]', '$d1[role_id]', '$d1[person_id_senior]',
		  '$d1[mobile]', '$d1[email]', '$d1[imei_number]', '$d1[state_id]','$manual_attendance','$d1[emp_code]','1','$joining_date','$resigning_date','$d1[head_quar]','$d1[region_txt]')";
        //echo $q;die;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person table error');
        }
        $rId = mysqli_insert_id($dbc);

        $q = "INSERT INTO `person_login` (`person_id`, `person_username`, `person_password`, `person_status`) VALUES ('$rId', '$d1[person_username]', AES_ENCRYPT('$d1[person_password]', '" . EDSALT . "') , '$d1[person_status]')";
        $r = mysqli_query($dbc, $q);
        if ($r) {
            $options = ['cost' => 12];
            $bcryptPass=password_hash("$d1[upass]", PASSWORD_BCRYPT, $options);
            #Insert in Uses table
            $q2 = "INSERT INTO `users` (`id`, `role_id`, `email`, `original_pass`, `password`, `status`) VALUES ('$rId', '$d1[role_id]', '$d1[person_username]','$d1[person_password]', '$bcryptPass' , '$d1[person_status]')";
//                    echo $q2;die;
            $r2 = mysqli_query($dbc, $q2);
        }
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Login Table error');
        }

        $q = "INSERT INTO `person_details` (`person_id`, `address`, `gender`, `dob`, `alternate_number`, `created_on`) VALUES ('$rId', '$d1[address]', '$d1[gender]', '$dob', '$d1[alternate_number]', NOW())";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Details table error');
        }

        $q = "INSERT INTO `person_finance_details` (`person_id`, `bank_branch_id`, `account_number`,`pan_no`,`tin_no`) "
            . "VALUES ('$rId', '$d1[bank_branch_id]', '$d1[account_number]', '$d1[pan_no]','0')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Finance Details table error');
        }

        $q = "SELECT acd.add_opt, acd.view_opt, acd.edit_opt, acd.del_opt, acd.sp_opt,  am.* FROM `_modules` AS am  LEFT JOIN  _role_module_rights AS acd ON am.module_id = acd.module_id AND  acd.role_id = '$d1[role_id]' ORDER BY menulinkorder, submenuorder ASC";
        list($opt1, $rs1) = run_query($dbc, $q, 'multi');
        if (!$opt1) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person modules rights table error');
        }
        $str = '';
        while ($row = mysqli_fetch_assoc($rs1)) {
            $str .= "('{$row['module_id']}', '$rId', '$row[add_opt]', '$row[edit_opt]', '$row[view_opt]', '$row[del_opt]', '$row[sp_opt]'), ";
        }

        $str = rtrim($str, ', ');

        $q = "INSERT INTO `person_modules_rights` (`module_id`, `person_id`, `add_opt`, `edit_opt`, `view_opt`, `del_opt`, `sp_opt`) VALUES $str";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person table error');
        }
        // code to add dealer person
        if (isset($d1['eid2']) && !empty($d1['eid2'])) {
            $dealer = new dealer();
            $dealer_list = $dealer->get_user_dealer_person_list($filter = "dealer_id = '$d1[eid2]'", $records = '', $orderby = '');
            $str = '';
            if (!empty($dealer_list)) {
                foreach ($dealer_list as $key => $value) {
                    $str .= '(\'' . $rId . '\', \'' . $d1['eid2'] . '\',\'' . $value['retailer_id'] . '\')';
                }
                $str = rtrim($str, ',');
                $q = "INSERT INTO user_dealer_retailer (`user_id`, `dealer_id`, `retailer_id`) VALUES $str";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => 'Person table error');
                }
            }
        }
        // code to add retailer person
        if (isset($d1['eid3']) && !empty($d1['eid3'])) {
            $dealer = new dealer();
            $retailer_list = $dealer->get_user_dealer_person_list($filter = "retailer_id = '$d1[eid3]'", $records = '', $orderby = '');
            $str = '';
            if (!empty($retailer_list)) {
                foreach ($retailer_list as $key => $value) {
                    $str .= '(\'' . $rId . '\', \'' . $d1['eid2'] . '\',\'' . $value['retailer_id'] . '\')';
                }
                $str = rtrim($str, ',');
                $q = "INSERT INTO user_dealer_retailer (`user_id`, `dealer_id`, `retailer_id`) VALUES $str";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => 'Person table error');
                }
            }
            //h1($q);
        }
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'item <b>'.$d1['itemname'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
    ######################################## Person Save end here ######################################################

    ######################################## Person edit start here ######################################################
    public function user_edit($id)
    {

        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_user_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $joining_date = !empty($d1['joining_date']) ? get_mysql_date($d1['joining_date']) : '';
        $resigning_date = !empty($d1['resigning_date']) ? get_mysql_date($d1['resigning_date']) : '';
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //pre($d1);die;
        $manual_attendance = isset($d1['manual_attendance']) ? '1' : '0';


        // query to update
        $q = "UPDATE person SET `position_id`='$d1[position]',`first_name` = '$d1[first_name]', `middle_name` = '$d1[middle_name]', `last_name` = '$d1[last_name]',
		 `company_id` = '$d1[company_id]', `role_id` = '$d1[role_id]', `person_id_senior` = '$d1[person_id_senior]',
		  `mobile` = '$d1[mobile]', `email` = '$d1[email]', `imei_number` = '$d1[imei_number]', `state_id` = '$d1[state_id]',
		  `manual_attendance`='$manual_attendance',`emp_code`='$d1[emp_code]',`product_division`='1',
		  `joining_date`='$joining_date',`resigning_date`='$resigning_date'  ,`head_quar`='$d1[head_quar]',`region_txt`='$d1[region_txt]'
		  WHERE id ='$id'";
        //h1($q);  //exit;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Table error');
        }
        $rId = $id;

        $q = "UPDATE person_login SET `person_username` = '$d1[person_username]', `person_password` = AES_ENCRYPT('$d1[upass]', '" . EDSALT . "') , `person_status` = '$d1[person_status]' WHERE person_id = '$id'";

        $r = mysqli_query($dbc, $q);
        if ($r) {
            $options = ['cost' => 12];
            $bcryptPass=password_hash("$d1[upass]", PASSWORD_BCRYPT, $options);
           // echo $bcryptPass;die;
            #Insert in Uses table
            $q2 = "UPDATE users SET `email` = '$d1[person_username]',`role_id` = '$d1[role_id]', `password` ='$bcryptPass',`original_pass`='$d1[upass]' , `status` = '$d1[person_status]' WHERE id = '$id'";
//                    $q2 = "INSERT INTO `users` (`id`, `email`, `password`, `status`) VALUES ('$rId', '$d1[person_username]', AES_ENCRYPT('$d1[person_password]', '".EDSALT."') , '$d1[person_status]')";
            $r2 = mysqli_query($dbc, $q2);
        }

        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Table error');
        }
        $q = "UPDATE person_details SET `address` = '$d1[address]', `gender` = '$d1[gender]', `dob` = '$dob',
                `alternate_number` = '$d1[alternate_number]'  WHERE person_id = '$id'";


        $r = mysqli_query($dbc, $q);

        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Table error');
        }

        $q = "UPDATE person_finance_details SET `bank_branch_id` = '$d1[bank_branch_id]', `account_number` = '$d1[account_number]',`pan_no`='$d1[pan_no]',`tin_no`='$d1[tin_no]' WHERE person_id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'person_finance_details error');
        }


        $str = '';
        $q = "SELECT acr.add_opt, acr.view_opt, acr.edit_opt, acr.del_opt, acr.sp_opt,am.* FROM `_modules` AS am LEFT JOIN `person_modules_rights` AS acr ON am.module_id = acr.module_id AND acr.person_id = '$id' ORDER BY menulinkorder, submenuorder ASC";
        list($opt1, $rs1) = run_query($dbc, $q, 'multi');
        if (!$opt1) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person modules rights table error');
        }
        $str = '';

        while ($row = mysqli_fetch_assoc($rs1)) {
            $str .= "('{$row['module_id']}', '$rId', '$row[add_opt]', '$row[edit_opt]', '$row[view_opt]', '$row[del_opt]', '$row[sp_opt]'), ";
        }

        $str = rtrim($str, ', ');
        $rdel = mysqli_query($dbc, "DELETE FROM `person_modules_rights` WHERE person_id  = '$id'");
        $q = "INSERT INTO `person_modules_rights` (`module_id`, `person_id`, `add_opt`, `edit_opt`, `view_opt`, `del_opt`, `sp_opt`) VALUES $str";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Person Table error');
        }
        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function recursiveall2($code)
    {
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


    public function get_user_list($filter = '', $records = '', $orderby = '')
    {
        $uni_state = 'location_3';
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *, CONCAT_WS(' ',first_name, middle_name, last_name) AS name,imei_number,version_code_name,DATE_FORMAT(joining_date,'%d/%m/%Y') 
		AS joining_date,DATE_FORMAT(resigning_date,'%d/%m/%Y') AS resigning_date,DATE_FORMAT(dob,'%d/%m/%Y') AS dob
		,$uni_state.name as state,DATE_FORMAT(last_web_access_on, '%e/%b/%Y AT %r') AS lastvisit, AES_DECRYPT(person_password, '" . EDSALT . "') as upass, person_username, email, person_status, rolename,$uni_state.id as state_id "
            . "FROM person INNER JOIN person_login ON person_login.person_id = person.id  "
            . "INNER JOIN person_details USING(person_id) "
            . "INNER JOIN $uni_state ON person.state_id=$uni_state.id "
            . "INNER JOIN _role  USING(role_id) "
            . "LEFT JOIN person_finance_details USING (person_id) "
            . "$filterstr";

//        h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $iid = $row['person_id'];
            $out[$iid] = $row; // storing the person id
        }
//        pre($out);die;
        return $out;
    }

    function get_ISR_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // echo $filterstr;die;
        //$q="SELECT its.*,statename,mobile,DATE_FORMAT(`Date`,'%d-%m-%Y') as s_date,rolename,CONCAT_WS(' ',first_name, middle_name, last_name) AS isr_name,l5.name as beat,DATE_FORMAT(uda.work_date,'%H:%i:%s') as attn_time,DATE_FORMAT(check_out.work_date,'%H:%i:%s') as checkout_time,dealer.name as dealer_name FROM `isr_total_sale_counter` its INNER JOIN person ON its.isr_id=person.id INNER JOIN state ON state.stateid=person.state_id INNER JOIN location_5 as l5 ON l5.id=its.`BeatId` INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN dealer ON its.`DistributorId`=dealer.id INNER JOIN user_daily_attendance uda ON uda.user_id=its.isr_id AND DATE_FORMAT(uda.work_date,'%Y-%m-%d')=its.Date INNER JOIN check_out ON check_out.user_id=its.isr_id AND DATE_FORMAT(check_out.work_date,'%Y-%m-%d')=its.Date  $filterstr group by  s_date, its.isr_id order by uda.work_date DESC";

        $q = "SELECT its.*,mobile,DATE_FORMAT(`Date`,'%d-%m-%Y') as s_date,(select statename from state where state.stateid=person.state_id) as statename,(select rolename from _role where _role.role_id=person.role_id) as rolename,(select role_group_id from _role where _role.role_id=person.role_id) as role_group_id,(select name from location_5 where location_5.id=its.BeatId) as beat,(select DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') from user_daily_attendance where user_daily_attendance .user_id=its.isr_id AND DATE_FORMAT(user_daily_attendance .work_date,'%Y-%m-%d')=its.Date) as attn_time,(select DATE_FORMAT(check_out.work_date,'%H:%i:%s') from check_out where check_out.user_id=its.isr_id AND DATE_FORMAT(check_out.work_date,'%Y-%m-%d')=its.Date) as checkout_time,(select dealer.name from dealer where dealer.id=its.`DistributorId`) as dealer_name,CONCAT_WS(' ',first_name, middle_name, last_name) AS isr_name FROM `isr_total_sale_counter` its INNER JOIN person ON its.isr_id=person.id  $filterstr group by  s_date, its.isr_id order by `Date` DESC";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the person id
        }
        //pre($out);die;
        return $out;

    }

    public function querystrname($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = NULL;
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        return $rs['uname'];
    }

    public function user_delete($id, $filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        if (empty($filter)) $filter = "person.id = '$id'";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_user_list($filter, $records, $orderby);
        $auth_user_id = $_SESSION[SESS . 'data']['id'];

        if (empty($deleteRecord)) {
            $out['myreason'] = 'person not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");

        //Running the deletion queries
        $delquery = array();
        $delquery['person_login'] = "UPDATE person_login SET person_status = '9',deleted_at=now(),deleted_by=$auth_user_id WHERE person_id = '$id'";
        // $delquery['person_login'] = "UPDATE person_login SET person_status = '9' WHERE person_id = '$id'";
        #seconday users
        $secondary_users[] = "UPDATE users SET status = '9' WHERE id = '$id'";

        foreach ($delquery as $key => $value) {
            mysqli_query($dbc, $secondary_users[$key]);
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => '$key query failed');
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Person successfully deleted');
    }

    public function get_parent_role($role_id)
    {
        global $dbc;
        $qq = "SELECT role_id,rolename, senior_role_id FROM _role WHERE role_id='$role_id'";
        list($opt, $rs) = run_query($dbc, $qq, 'single');
        $str = '';
        if ($rs['senior_role_id'] == 0) {
            $str .= $rs['role_id'];
            return $str;
        } else {
            $str .= $rs['senior_role_id'] . ',' . $this->get_parent_role($rs['senior_role_id']);
            return $str;
        }

    }

    //This function is used to whether dealer is assign or not by user
    public function get_user_dealer_person_icon($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT dealer_id,name,address,email,landline,other_numbers,tin_no,pin_no FROM user_dealer_retailer "
            . "INNER JOIN dealer ON dealer.id = user_dealer_retailer.dealer_id $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['dealer_id'];
            $out[$id] = $row;

        }
        return $out;
    }

// Function For Genrate Signin data
    public function generate_signin_data($userid)
    {

        $url = HOST_PATH . '/msell/webservices/signin_data_generate.php?id=' . $userid;
        //$url = 'http://8.30.244.74/bcas/gallery.php';
        //echo $url;die;
        $data = $this->url_get_contents($url);
        // echo $data;die('file done');
        if (fsockopen("dsdsr.com", 3360)) {
            print "I can see port 3360";
            die;
        } else {
            print "I cannot see port 3360";
            die;
        }
        //$data = file_get_contents(HOST_PATH.'/msell-ds/webservices/signin_data_generate.php?id='.$userid);
        //  echo HOST_PATH.'/msell-dsgroup_sync/webservices/signin_data_generate.php?id='.$userid;die;
        //echo $data;die;
        $file = fopen("../../../webservices/signin/" . $userid . ".php", "w");

        fwrite($file, $data);
        fclose($file);
        return array('status' => true, 'myreason' => 'File Generated Successfully');
    }


    function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_URL, 53150);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 53150);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /*  *******************************************   */
    public function get_user_dealer_count($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT count(distinct dealer_id) as dealer_count ,count(distinct location_id) as beat_count  FROM dealer_location_rate_list
            inner join person on person.id=dealer_location_rate_list.user_id 
            inner join _role on _role.role_id=person.role_id $filterstr ";
        //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['dealer_id'];
            $out = $row;

        }
        return $out;

    }

    #######################################
    public function get_user_dealer_retailer_count($filter2 = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter2, $records, $orderby);
        $q = "SELECT count(distinct retailer_id) as retailer_count FROM user_dealer_retailer
            inner join person on person.id=user_dealer_retailer.user_id 
            inner join _role on _role.role_id=person.role_id $filterstr ";
        //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['dealer_id'];
            $out = $row;

        }
        return $out;

    }

    public function get_user_count($filter1 = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter1, $records, $orderby);
        $q = "SELECT count(distinct user_id) as user_count FROM user_dealer_retailer
            inner join person on person.id=user_dealer_retailer.user_id 
            inner join _role on _role.role_id=person.role_id $filterstr   ";
        //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['user_id'];
            $out = $row;

        }
        return $out;

    }


}// class end here
?>
