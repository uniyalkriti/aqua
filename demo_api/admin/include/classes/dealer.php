<?php

class dealer extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## WORK DEALER Starts here ####################################################

    public function get_dealer_se_data() {
        $d1 = $_POST;

        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'dealer'; //whether to do history log or not
        return array(true, $d1);
    }

    public function dealer_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');

        list($status, $d1) = $this->get_dealer_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //Start the transaction
        $town_id=$d1[location_4_id];
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `dealer` (`id`, `name`,`contact_person`, `dealer_code`, `address`, `email`, `landline`, `other_numbers`, `tin_no`, `pin_no`,`ownership_type_id`,`avg_per_month_pur`,`csa_id`)
			VALUES (NULL, '$d1[name]', '$d1[contact_person]', '$d1[dealer_code]', '$d1[address]', '$d1[email]', '$d1[landline]', '$d1[other_numbers]','$d1[tin_no]','$d1[pin_no]','$d1[ownership_type_id]','$d1[avg_per_month_pur]','$d1[csa_id]');";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Could not be saved, some error occurred');
        }
        $rId = mysqli_insert_id($dbc);
        $extrawork = $this->dealer_location_extra('save', $rId, $_POST['location_id'], $_POST['rate_list_id'],$town_id);
        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function dealer_location_extra($actiontype, $rId, $location, $rate_list_id,$town_id) {
        global $dbc;
        $uncode = '';
        $str = array();
        // Fetching the old details store for other columns, so that during edit we can delete and reinsert data
        //during update we are required to remove the previous entry
        // if ($actiontype == 'update') mysqli_query($dbc, "DELETE FROM dealer_location_rate_list WHERE dealer_id = $rId");
        $q="DELETE w FROM dealer_location_rate_list w INNER JOIN location_view e ON w.location_id=e.l5_id WHERE e.l4_id = '$town_id' AND w.dealer_id='$rId'";
        $r=mysqli_query($dbc,$q);

        if (!empty($location)) {
            foreach ($location as $key => $value) {
                //$uncode = $rId.$value;
                //To save the value of the other columns as some columns are affected by po

                $str1[] = "($rId, $value, 1, 1)";
            }
            $str = implode(', ', $str1);
        } // !empty end here
        if (!empty($str)) {
            $q = "INSERT INTO `dealer_location_rate_list`(dealer_id,location_id,rate_list_id,company_id) VALUES $str";
            //h1($q);
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Please select At least one location');
        }
        //Update the qty in the database
        //mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
        return array('status' => true, 'myreason' => '');
    }

    public function dealer_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        $originaldata = $this->get_dealer_list_modified_data("id = $id");
        $originaldata = $originaldata[$id];
        $modifieddata = $this->get_modified_data($originaldata, $d1);
        //if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
        //Start the transaction
        $town_id=$d1[location_4_id];
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE dealer SET name = '$d1[name]', contact_person = '$d1[contact_person]', address = '$d1[address]', email = '$d1[email]', landline = '$d1[landline]', other_numbers = '$d1[other_numbers]', tin_no = '$d1[tin_no]', pin_no = '$d1[pin_no]', ownership_type_id='$d1[ownership_type_id]',avg_per_month_pur='$d1[avg_per_month_pur]', dealer_code = '$d1[dealer_code]', csa_id = '$d1[csa_id]' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'dealer/Retailer Table error');
        }
        //$person_table_update = $this->update_person_login_table($actiontype='Update',$id,$_POST['location_id']);
        //if(!$person_table_update['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$person_table_update['myreason']);}

        $multiple_rate_list = $this->get_is_multiple_rate_list_status($id, $_POST['rate_list_id']);
        if (!$multiple_rate_list['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $multiple_rate_list['myreason']);
        }

        $extrawork = $this->dealer_location_extra('update', $id, $_POST['location_id'], $_POST['rate_list_id'],$town_id);

        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    ####This function told whether dealer is modified or not#############################

    public function get_dealer_list_modified_data($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM dealer $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['location_id'] = $this->get_dealer_location_attach_list($filter = "dealer_id='$id'", $records = '', $orderby = '');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

      public function get_dealername($id)
	{
		global $dbc;
		$out = array();
		 $q = "SELECT name FROM dealer WHERE id = '$id'";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['name'];
	}

    public function get_dealer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *, dealer.id as id,dealer.name as name,location_view.l4_id as city_id,
        dealer.contact_person as cname,csa_id,csa_name,csa_code , "
                . "location_view.l2_name as state,location_view.l2_id as stateid,location_view.l3_name as citys,"
                . "location_view.l4_name as district,location_view.l5_name as locality FROM dealer "
                 . "LEFT JOIN csa   ON dealer.csa_id=csa.c_id "
                . "LEFT JOIN dealer_location_rate_list as dlrl ON dealer.id=dlrl.dealer_id "
                . "LEFT JOIN location_view ON dlrl.location_id=location_view.l5_id $filterstr";
       /*$q = "SELECT *, dealer.id as id,dealer.name as name,location_4.id as city_id,dealer.contact_person as cname,location_2.name as state,location_2.id as stateid,location_3.name as citys,location_4.name as district,location_5.name as locality FROM dealer INNER JOIN dealer_location_rate_list as dlrl ON dealer.id=dlrl.dealer_id INNER JOIN location_5 ON dlrl.location_id=location_5.id
               INNER JOIN location_4 ON location_5.location_4_id=location_4.id INNER JOIN location_3 ON location_4.location_3_id=location_3.id INNER JOIN location_2 ON location_3.location_2_id=location_2.id $filterstr LIMIT 100";*/

//h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
         if (!$opt)
            return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['rate_list_id'] = $this->get_rate_list_id($row['id']);
      
            //h1("SELECT id, name FROM dealer_location_rate_list INNER JOIN location_{} AS l3 ON l3.id = dealer_location_rate_list.location_id WHERE dealer_id = '$id'");
            //$out[$id]['location_details'] = $this->get_my_reference_array_direct($q1 = "SELECT *, id, name,l4.name as district FROM dealer_location_rate_list INNER JOIN location_$dealer_level AS l3 ON l3.id = dealer_location_rate_list.location_id INNER JOIN location_4 AS l4 ON l3.location_4_id = l4.id WHERE dealer_id = '$id'", 'id');
            $out[$id]['location_details'] = $this->get_my_reference_array_direct($q = "SELECT *, l3.id, l3.name,l4.name as city FROM dealer_location_rate_list INNER JOIN location_$dealer_level AS l3 ON l3.id = dealer_location_rate_list.location_id INNER JOIN location_4 AS l4 ON l3.location_4_id = l4.id WHERE dealer_id = '$id'", 'id');

         }

//// while($row = mysqli_fetch_assoc($rs)){ ends
     //   pre ($out);
        return $out;
    }
#####


    public function get_user_wise_dealer_data($id, $role_id) {
        global $dbc;
        $out = array();
        $user_data = array();
        $main_id = $id;
        if ($role_id == 1||$role_id == 50) {
            //$q = "SELECT id FROM dealer ORDER BY id DESC";
            $q = "SELECT id FROM dealer where dealer_status = '1' ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT id FROM person WHERE role_id IN ($role_id_str) AND person_id_senior='$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if ($opt) {
                $user_data[$main_id] = $main_id;
                while ($row = mysqli_fetch_assoc($rs)) {
                    $user_data[$row['id']] = $row['id'];
                }
                $user_id_str = implode(',', $user_data);
                $q = "SELECT dealer_id FROM user_dealer_retailer WHERE user_id IN ($user_id_str)";
                list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
                if (!$opt)
                    return $out;
                while ($row = mysqli_fetch_assoc($rs)) {
                    $out[$row['dealer_id']] = $row['dealer_id'];
                }
            } //if($opt) end here
            else {
                $q = "SELECT dealer_id FROM user_dealer_retailer WHERE user_id = '$main_id'";
                list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
                if (!$opt)
                    return $out;
                while ($row = mysqli_fetch_assoc($rs)) {
                    $out[$row['dealer_id']] = $row['dealer_id'];
                }
            } // else part end here
        }

        return $out;
    }

    // This function is used to save dealer person information
    public function get_dealer_person_se_data() {
        $d1 = $_POST;

        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'dealer person'; //whether to do history log or not
        return array(true, $d1);
    }

    public function dealer_person_save_info() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_person_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        $doma = !empty($d1['anniversary']) ? get_mysql_date($d1['anniversary']) : '';
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `dealer_person` (`id`, `dealer_id`, `person_name`, `ownership_type_id`, `contact_no`, `address`, `district`, `tehsil`, `pin_no`, `email`, `dob`, `anniversary`, `tin_no`, `avg_per_month_prod_purchase`, `no_of_assoc_head_plumbers`, `no_of_assoc_plumbers`, `created_by_person_id`, `created_on`)
			VALUES (NULL, '$d1[dealer_id]', '$d1[person_name]', '$d1[ownership_type_id]', '$d1[contact_no]', '$d1[address]','$d1[district]','$d1[tehsil]','$d1[pin_no]','$d1[email]','$dob','$doma','$d1[tin_no]','$d1[avg_per_month_prod_purchase]','$d1[no_of_assoc_head_plumbers]','$d1[no_of_assoc_plumbers]','$d1[uid]',NOW());";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Person Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function dealer_person_edit_info($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_person_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        $doma = !empty($d1['anniversary']) ? get_mysql_date($d1['anniversary']) : '';
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE dealer_person SET dealer_id = '$d1[dealer_id]', person_name = '$d1[person_name]', ownership_type_id = '$d1[ownership_type_id]', contact_no = '$d1[contact_no]', address = '$d1[address]', district = '$d1[district]', tehsil = '$d1[tehsil]', pin_no = '$d1[pin_no]',email='$d1[email]',dob='$dob',anniversary='$doma',tin_no = '$d1[tin_no]',avg_per_month_prod_purchase='$d1[avg_per_month_prod_purchase]',no_of_assoc_head_plumbers='$d1[no_of_assoc_head_plumbers]',no_of_assoc_plumbers='$d1[no_of_assoc_plumbers]' WHERE id = '$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Person Table error');
        }

        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    public function get_dealer_perosn_info_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,DATE_FORMAT(dob,'%d/%b/%Y') AS dob,DATE_FORMAT(anniversary,'%d/%b/%Y') AS anniversary FROM dealer_person $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealerid_map = get_my_reference_array('dealer', 'id', 'name');
        $outlet_map = get_my_reference_array('_dealer_ownership_type', 'id', 'ownership_type');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_val'] = $dealerid_map[$row['dealer_id']];
            $out[$id]['outlet_val'] = $outlet_map[$row['ownership_type_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    // function end here
    // This function is used to save dealer sales person information
    public function get_dealer_sales_person_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'dealer sales person'; //whether to do history log or not
        return array(true, $d1);
    }

    public function dealer_sales_person_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_sales_person_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        $doma = !empty($d1['anniversary']) ? get_mysql_date($d1['anniversary']) : '';
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `dealer_sales_person` (`id`, `user_id`,`role_id`, `dealer_id`, `dsp_name`, `contact_no`, `address`, `district`, `tehsil`, `pin_no`, `email`, `dob`, `anniversary`, `field_experience`, `bank_name`, `bank_account_no`, `bank_branch_name`, `ifsc_code`, `pan_no`,`average_purchase`) VALUES (NULL, '$d1[uid]', '$d1[role_id]', '$d1[dealer_id]', '$d1[dsp_name]', '$d1[contact_no]', '$d1[address]','$d1[district]','$d1[tehsil]','$d1[pin_no]','$d1[email]','$dob','$doma','$d1[field_experience]','$d1[bank_name]','$d1[bank_account_no]','$d1[bank_branch_name]','$d1[ifsc_code]','$d1[pan_no]','$d1[average_purchase]');";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer Person Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function dealer_sales_person_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_sales_person_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        $dob = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
        $doma = !empty($d1['anniversary']) ? get_mysql_date($d1['anniversary']) : '';
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE dealer_sales_person SET dealer_id = '$d1[dealer_id]', dsp_name = '$d1[dsp_name]',  contact_no = '$d1[contact_no]', address = '$d1[address]', district = '$d1[district]', tehsil = '$d1[tehsil]', pin_no = '$d1[pin_no]',email='$d1[email]',dob='$dob',anniversary='$doma',average_purchase='$d1[average_purchase]',bank_name='$d1[bank_name]',bank_branch_name='$d1[bank_branch_name]',bank_account_no = '$d1[bank_account_no]',ifsc_code = '$d1[ifsc_code]',pan_no = '$d1[pan_no]',role_id = '$d1[role_id]',user_id = '$d1[uid]',dealer_id = '$d1[dealer_id]',field_experience='$d1[field_experience]' WHERE id = '$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer sales Person Table error');
        }

        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    public function get_dealer_sales_perosn_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,DATE_FORMAT(dob,'%d/%b/%Y') AS dob,DATE_FORMAT(anniversary,'%d/%b/%Y') AS anniversary FROM dealer_sales_person $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealerid_map = get_my_reference_array('dealer', 'id', 'name');
        $outlet_map = get_my_reference_array('_retailer_outlet_type', 'id', 'outlet_type');
        $outlet_map = get_my_reference_array('_field_experience', 'id', 'experience');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_val'] = $dealerid_map[$row['dealer_id']];
            $out[$id]['outlet_val'] = $outlet_map[$row['outlet_type_id']];
            $out[$id]['experience_val'] = $outlet_map[$row['field_experience']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    // This function is used to save dealer sales person information
    public function get_rate_list_id($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT rate_list_id FROM dealer_location_rate_list WHERE dealer_id = $id GROUP BY dealer_id ASC";
      //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['rate_list_id'];
    }

    public function get_dealer_location_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['dealer_level'];
        $loop = $mtype - 1;
        $str = '';
        for ($k = $mtype; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
        $q = "SELECT * $str FROM dealer_location_rate_list INNER JOIN  location_$mtype ON location_$mtype.id = dealer_location_rate_list.location_id ";
        for ($i = $mtype; $i > 1; $i--) {
            $j = $i - 1;
            $q .= "INNER JOIN location_$j ON location_$i.location_" . $j . "_id = location_$j.id ";
        }
        $q .= "$filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["location_" . $mtype . "_id"];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    public function get_common_location_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['dealer_level'];
        $q = "SELECT * FROM location_$mtype $filterstr";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id]['location_id'] = $id; // storing the item id
            $out[$id]['name'] = $row['name'];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_dealer_location_attach_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['dealer_level'];
        $q = "SELECT * FROM dealer_location_rate_list INNER JOIN location_$mtype ON location_$mtype.id = dealer_location_rate_list.location_id $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {

            $out[] = $row['location_id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_retailer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM retailer $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_person'] = $this->get_my_reference_array_direct("SELECT retailer_person.*, CONCAT_WS(' ',first_name,middle_name,last_name) AS pname,CONCAT_WS('',retailer_id,person_id) AS cjokey FROM retailer_person INNER JOIN person ON person.id = retailer_person.person_id WHERE retailer_id = '$id'", 'cjokey');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_groupname($id) {
        global $dbc;
        $out = array();
        $q = "SELECT group_name FROM _role_group WHERE id = '$id'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['group_name'];
    }

    public function get_role_id($id) {
        global $dbc;
        $out = array();
        $q = "SELECT role_id FROM _role WHERE role_group_id = '$id'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[] = $row['role_id'];
        }
        $out = implode(',', $out);
        return $out;
    }

    public function get_dealer_person_icon($id) {
        global $dbc;
        $out = array();
        $q = "SELECT * , CONCAT_WS( ' ', first_name, middle_name, last_name ) AS name, DATE_FORMAT( last_web_access_on, '%e/%m/%Y AT %r' ) AS lastlogin FROM person INNER JOIN person_login ON person_login.person_id = person.id INNER JOIN user_dealer_retailer ON person_login.person_id = user_dealer_retailer.user_id INNER JOIN _role USING ( role_id ) WHERE dealer_id = '$id' AND role_group_id = '22'";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['dealer_id'] . $row['person_id'];
            $out[$id] = $row;
        }

        return $out;
    }

    public function get_location_dealer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $out = array();
        $dealer = array();
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        $q = "SELECT id FROM location_$dealer_level $filterstr";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {

            $out[] = $row['id'];
        }
        $out = implode(',', $out);
        if (!empty($out)) {
            $q = "SELECT * FROM dealer_location_rate_list INNER JOIN dealer ON dealer.id = dealer_location_rate_list.dealer_id WHERE location_id IN ($out)";
            // h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $dealer;
            while ($row = mysqli_fetch_assoc($rs)) {

                $id = $row['dealer_id'];
                $dealer[$id] = $row;
            }
        }
        return $dealer;
    }

     public function get_person_dealer_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];

                if(!isset($d1['location_id'])) {   return array('status'=>false, 'myreason'=>'Sorry No location Selected'); }

                if(!empty($d1['location_id'])){
                    foreach($d1['location_id'] as $key=>$value){
                        foreach($d1["retailer_id$value"] as $inkey=>$invalue){
                            $d1['retailer_id'][] = $invalue;
                        }
                    }
                }

		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Dealer Retailer'; //whether to do history log or not
		return array(true,$d1);
	}

    public function person_dealer_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_person_dealer_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);

        $originaldata = $this->get_person_dealer_modified_data($d1['user_id'], $d1['dealer_id']);
        if (!empty($d1['retailer_id']) && !empty($originaldata)) {
            $modifieddata = $this->get_modified_data($originaldata, $d1);
            if (empty($modifieddata))
                return array('status' => false, 'myreason' => 'Please do <strong>atleast 1 change</strong> to update');
        }

         //$is_update_person_table = $this->update_retailer_id_fetch_coloumn($actiontype='Update',$d1['dealer_id'],$d1['retailer_id'], $d1['user_id']);

        //Start the transaction
         //----------------------------2nd time update user dealer retailer and user dealer retailer log-------------------------//


                 $ud="SELECT user_id,dealer_id,retailer_id,udr_date_time FROM  `user_dealer_retailer` WHERE  `dealer_id` =$d1[dealer_id] AND  `user_id` =$d1[user_id]";
        //h1($ud);
                 $run_ud = mysqli_query($dbc, $ud);
                     if(mysqli_num_rows($run_ud) > 0 )
					{
                         while($row1 = mysqli_fetch_assoc($run_ud)){
                          $qlog = "INSERT INTO `user_dealer_retailer_log` (`user_id`, `dealer_id`,`retailer_id`,`udr_date_time`,`server_date`)
			VALUES ('$row1[user_id]','$row1[dealer_id]','$row1[retailer_id]','$row1[udr_date_time]',NOW())";
                        // h1($qlog);
                          $rellog = mysqli_query($dbc,$qlog);
                         }
                        }
                     if($rellog){
                        $qdel = "DELETE FROM user_dealer_retailer WHERE user_id = '$d1[user_id]' AND dealer_id = '$d1[dealer_id]' AND retailer_id= 0 ";
                      // h1($qdel);
                        $rel = mysqli_query($dbc,$qdel);
                     }
                $str = '';
                $str_log='';
                $udr_date_time=  date('Y-m-d h:i:s');
                //echo  $udr_date_time;exit;

         //---------------------- Ist time save user dealer retailer and user dealer retailer log ---------------------------------//

       // $qdel = "DELETE FROM user_dealer_retailer WHERE user_id = '$d1[user_id]' AND dealer_id = '$d1[dealer_id]'";
       // $rel = mysqli_query($dbc, $qdel);
     //   $str = '';
        if (!empty($d1['retailer_id'])) {
            $field_arry = array('user_id' => $d1['user_id']); // checking for  duplicate Unit Name
             $qdel = "DELETE FROM user_dealer_retailer WHERE user_id = '$d1[user_id]' AND dealer_id = '$d1[dealer_id]'  ";
                      // h1($qdel);
                        $rel = mysqli_query($dbc,$qdel);
            foreach ($d1['retailer_id'] as $key => $retailer_id) {
               // if (!uniqcheck_msg($dbc, $field_arry, 'user_dealer_retailer', false, "retailer_id='$retailer_id' AND dealer_id='$d1[dealer_id]'"))
               
                    $str .= '(\'' . $d1['user_id'] . '\',\'' . $d1['dealer_id'] . '\',\'' . $retailer_id . '\'),';
                    $str_log .= '(\''.$d1['user_id'].'\',\''.$d1['dealer_id'].'\',\''.$retailer_id.'\',\''.$udr_date_time.'\'),';
            }
        }
        else {
            $str .= '(\'' . $d1['user_id'] . '\',\'' . $d1['dealer_id'] . '\',\'0\'),';
            $str_log .= '(\''.$d1['user_id'].'\',\''.$d1['dealer_id'].'\',\'0\',\''.$udr_date_time.'\'),';
        }
        $str = rtrim($str, ',');
        $str_log = rtrim($str_log , ',');
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `user_dealer_retailer` (`user_id`, `dealer_id`,`retailer_id`)
			VALUES $str";
      // h1($q);
        $r = mysqli_query($dbc, $q);
        if($r)
        {
           $q_ret = "INSERT INTO `user_dealer_retailer_log` (`user_id`, `dealer_id`,`retailer_id`,`udr_date_time`)
			VALUES $str_log";
              $rs = mysqli_query($dbc, $q_ret);
        }
      // h1($q_ret); //exit;
        if (!$rs) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer already assign With another dealer');
        }
        $rId = mysqli_insert_id($dbc);


          //---------------------2nd time change and update beat--------------------------------//
                if($d1['submit']=='Proceed')
                {
                  $udl="SELECT user_id,dealer_id,server_date,location_id FROM  `dealer_location_rate_list` WHERE  `dealer_id` =$d1[dealer_id] AND  `user_id` =$d1[user_id] ";
                //  h1($udl);
                  // echo "2nd";
                 $run_udl = mysqli_query($dbc, $udl);
                     if(mysqli_num_rows($run_udl) > 0 )
					{
                       while($del_row = mysqli_fetch_assoc($run_udl)){
                          $qlogl = "INSERT INTO `dealer_location_rate_list_log` (`user_id`, `dealer_id`,`location_id`,`server_date`,`dlrl_date_time`)
			VALUES ('$del_row[user_id]','$del_row[dealer_id]','$del_row[location_id]','$del_row[server_date]',NOW())";
                        //  h1($qlogl);
                          $relloc = mysqli_query($dbc,$qlogl);
                         }
                        }
                     if($relloc){
                        $qdl = "DELETE FROM dealer_location_rate_list WHERE user_id = '$d1[user_id]' AND dealer_id = '$d1[dealer_id]' ";
                      //  h1($qdl);
                        $dell = mysqli_query($dbc,$qdl);
                     }

          //---------------------Ist time change and update beat--------------------------------//

               foreach($d1['location_id'] as $k=>$location_id)
                    {
                     $qr="SELECT user_id,server_date FROM  `dealer_location_rate_list` WHERE  `dealer_id` =$d1[dealer_id] AND `location_id`=$location_id AND  `user_id` =$d1[user_id]";
                    //  h1($qr);
                     // echo "first";
                     $run_qr = mysqli_query($dbc, $qr);
                     if(mysqli_num_rows($run_qr) < 1 )
	               {
                       $r_up = "INSERT INTO `dealer_location_rate_list`(`dealer_id`, `location_id`, `user_id`,`server_date`) VALUES ($d1[dealer_id],$location_id,$d1[user_id],NOW())";
                      // h1($r_up);
                       $rup = mysqli_query($dbc, $r_up);
                       if($rup)
                       {
                       $r1_up = "INSERT INTO `dealer_location_rate_list_log`(`dealer_id`, `location_id`, `user_id`,`server_date`) VALUES ($d1[dealer_id],$location_id,$d1[user_id],NOW())";
                     //  h1($r1_up);
                       mysqli_query($dbc, $r1_up);
                       }
                      }
                    }
                }

        mysqli_commit($dbc);

        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved');
    }

    //Here we check wheather dealer is modified or not
    public function get_person_dealer_modified_data($user_id, $dealer_id) {
        global $dbc;
        $out = array();
        $q = "SELECT * FROM user_dealer_retailer INNER JOIN dealer ON dealer.id = user_dealer_retailer.dealer_id WHERE user_id = '$user_id' AND dealer_id = '$dealer_id' LIMIT 1";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        if ($opt) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $out['dealer_id'] = $row['dealer_id'];
                $out['user_id'] = $row['user_id'];
                $out['dealer_name'] = $row['name'];
                $out['retailer_id'] = $this->get_user_dealer_retailer_checkbox_list($dealer_id, $user_id);
                $out['submit'] = 'Save';
                $out['uid'] = $_SESSION[SESS . 'data']['id'];
                $out['myreason'] = 'Please fill all the required information';
                $out['what'] = 'Dealer Retailer';
            }
        }
        return $out;
    }

    public function get_person_dealer_list($user_id) {
        global $dbc;
        $out = array();
        $q = "SELECT dealer_id,retailer_id FROM user_dealer_retailer WHERE user_id='$user_id'";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[] = $row['dealer_id'];
        }
        return $out;
    }

    public function get_user_dealer_relation($dealer_id) {
        global $dbc;
        $out = FALSE;
        $q = "SELECT dealer_id FROM user_dealer_retailer WHERE dealer_id='$dealer_id'";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if ($opt)
            $out = TRUE;
        return $out;
    }

    public function get_user_dealer_person_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $out = array();
        $q = "SELECT * FROM user_dealer_retailer $filterstr";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $inc = 1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[$inc]['user_id'] = $row['user_id'];
            $out[$inc]['retailer_id'] = $row['retailer_id'];
            $out[$inc]['dealer_id'] = $row['dealer_id'];
        }
        return $out;
    }

    public function get_user_dealer_retailer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $out = array();
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        $loop = $dealer_level + 1;
        $retailer_level = $_SESSION[SESS . 'constant']['retailer_level'];

        $q = "SELECT r.id,CONCAT(r.name,' ( ',r.contact_per_name,', ',r.address,' )')as name FROM dealer_location_rate_list dlrl INNER JOIN location_$dealer_level l$dealer_level ON l$dealer_level.id = dlrl.location_id ";
        for ($i = $loop; $i <= $retailer_level; $i++) {
            $j = $i - 1;
            $q .= "  INNER JOIN location_$i l$i ON l$i.location_" . $j . "_id = l$j.id ";
        }
        $q .= " INNER JOIN retailer r ON r.location_id = l$retailer_level.id $filterstr";
        //echo $q;
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
        }
        return $out;
    }

       public function get_user_dealer_retailer_checkbox_list($id ,$user_id)
	{
		global $dbc;
		$out= array();
                $q = "SELECT location_id FROM dealer_location_rate_list WHERE dealer_id='$id' AND user_id = $user_id";
                //$q = "SELECT retailer_id FROM user_dealer_retailer WHERE dealer_id='$id' AND user_id = $user_id";
		//h1($q);
                list($opt ,$rs) = run_query($dbc ,$q ,'multi');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs))
                {
                    $out[] = $row['location_id'];
                }
		return $out;
	}

    ######################################## This Function is used to Find out dealer retailer data ####################################################
    //get_user_dealer_retailer_checkbox_list
    ######################################## ANNEXURE ends here ####################################################
    // This function is used to assign dealer rate for individual location

    public function dealer_location_rate_list_assign($actiontype, $rId, $location, $ratelist) {
        global $dbc;
        $uncode = '';
        $str = array();

        //during update we are required to remove the previous entry
        if ($actiontype == 'update')
            mysqli_query($dbc, "DELETE FROM dealer_location_rate_list WHERE dealer_id = $rId");
        if (!empty($location)) {
            $i = 0;
            foreach ($location as $key => $value) {
                //$uncode = $rId.$value;
                //To save the value of the other columns as some columns are affected by po
                //$q = "UPDATE dealer_location_rate_list SET rate_list_id = '$ratelist[$i]' WHERE location_id=$value AND dealer_id = $rId";
                //  h1($q);
                $str[] = "($rId, $value,$ratelist[$i])";
                $i++;
            }
            $str = implode(', ', $str);
        } // !empty end here
        $q = "INSERT INTO `dealer_location_rate_list` VALUES $str";
        //h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r)
            return array('status' => false, 'myreason' => 'Please select At least on location');
        //Update the qty in the database
        //mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
        return array('status' => true, 'myreason' => 'Rate List succesfully assign to location');
    }

    // This function is used to track multiple rate list statement
    public function get_is_multiple_rate_list_status($dealer_id, $rate_list_id) {
        global $dbc;
        $out = array();
        $rate_list = array();
        $rate_list[$rate_list_id] = $rate_list_id;

        //$filterstr=$this->oo_filter($filter, $records, $orderby);
        //Here we checkout this dealer is assigned with person or not
        $q = "SELECT user_id FROM user_dealer_retailer WHERE dealer_id = '$dealer_id' LIMIT 1";
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return array('status' => true, 'myreason' => '');

        $q = "SELECT rate_list_id FROM user_dealer_retailer INNER JOIN dealer_location_rate_list USING(dealer_id) WHERE user_id = '$rs[user_id]' AND rate_list_id != ''";
        list($opt1, $rs1) = run_query($dbc, $q, 'multi');
        if ($opt1) {
            while ($row = mysqli_fetch_assoc($rs1)) {
                $id = $row['rate_list_id'];
                $out[$id] = $id;
            }
        }
        $result = array_diff($out, $rate_list);
        $result1 = array_diff($rate_list, $out);
        if (!empty($result) || !empty($result1)) {
            $q = "UPDATE person_login SET is_multiple_rate_list = '1' WHERE person_id = $rs[user_id]";
            $r = mysqli_query($dbc, $q);
        }
        return array('status' => true, 'myreason' => '');
    }

    public function get_user_person_multiple_status($user_id, $dealer_id) {
        global $dbc;
        $rate_list = array();
        $q = "SELECT rate_list_id FROM dealer_location_rate_list WHERE dealer_id = '$dealer_id' AND rate_list_id != '' ";


        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return array('status' => true, 'myreason' => '');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['rate_list_id'];
            $rate_list[$id] = $id;
        }

        $q = "SELECT rate_list_id FROM user_dealer_retailer INNER JOIN dealer_location_rate_list USING(dealer_id) WHERE user_id = '$user_id' AND rate_list_id != ''";
        //h1($q);
        list($opt1, $rs1) = run_query($dbc, $q, 'multi');
        while ($row1 = mysqli_fetch_assoc($rs1)) {
            $id = $row1['rate_list_id'];
            $out[$id] = $id;
        }

        $result = array_diff($rate_list, $out);
        $result1 = array_diff($out, $rate_list);
        if (!empty($result) || !empty($result1)) {
            $q = "UPDATE person_login SET is_multiple_rate_list = '1' WHERE person_id = $user_id";
            $r = mysqli_query($dbc, $q);
        }
        return array('status' => true, 'myreason' => '');
    }

    //This code is used to update person_login table details when location is changed for dealer
    public function update_person_login_table($actiontype, $dealer_id, $newlocation) {
        global $dbc;
        list($status, $d1) = $this->get_dealer_se_data();

        $delete_id_location = array();
        $fetch_location_id = array();
        $beat_id_delete = array();
        $beat_id_fetch = array();
        $location_id_fetch = array();
        $location_id_delete = array();
        $out = array('status' => true, 'myreason' => '');
        $oldlocation = array();
        $new_loc_array = array();
        ### -------This is the beat WHich is submit by the user----###
        if (!empty($newlocation)) {
            foreach ($newlocation as $key => $value)
                $new_loc_array[$value] = $value;
        }

        ##-- IF this dealer not assign to any user then this process is not working  --##
        $qq = "SELECT user_id,dealer_id_fetch, dealer_id_delete, beat_id_fetch, beat_id_delete FROM user_dealer_retailer INNER JOIN person_login ON person_login.person_id = user_dealer_retailer.user_id  WHERE dealer_id = '$dealer_id' GROUP BY user_id ASC";

        list($opt1, $rs1) = run_query($dbc, $qq, 'multi');
        if (!$opt1)
            return $out;

        ### -------This is the beat WHich is already assign  by the dealer----###
        $q = "SELECT location_id FROM dealer_location_rate_list WHERE dealer_id = '$dealer_id' GROUP BY location_id ASC";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if ($opt) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['location_id'];
                $oldlocation[$id] = $id;
            }
        }

        ###--Here we have to find which beat are fetched and which beat are deleted--##
        if (!empty($oldlocation)) {
            foreach ($oldlocation as $key => $value) {
                if (!in_array($value, $new_loc_array)) {
                    $delete_id_location[$value] = $value;
                    //unset($new_loc_array[$key]);
                } else
                    unset($new_loc_array[$key]);
            }
            if (!empty($new_loc_array)) {
                foreach ($new_loc_array as $key => $value)
                    $fetch_location_id[$value] = $value;
            }
        } // if(!empty($oldlocation)) end here
        else {
            foreach ($new_loc_array as $key => $value)
                $fetch_location_id[$value] = $value;
        }

        $userid = array();
        //This query gives all the userid of that particular dealer
        if (!empty($fetch_location_id) || !empty($delete_id_location) || true) {
            if ($opt1) {
                while ($rows = mysqli_fetch_assoc($rs1)) {
                    if (!empty($rows['beat_id_fetch'])) {
                        $beat_fetch = explode(',', $rows['beat_id_fetch']);
                        $beat_fetch_final = array_unique(array_merge($beat_fetch, $fetch_location_id));
                        foreach ($beat_fetch_final as $key => $value)
                            $beat_fetch_final_array[$value] = $value;
                        //pre($beat_fetch_final_array);
                        //pre($delete_id_location);
                        if (!empty($delete_id_location)) {
                            foreach ($delete_id_location as $inkey => $invalue) {
                                if (array_key_exists($inkey, $beat_fetch_final_array))
                                    unset($beat_fetch_final_array[$inkey]);
                            }
                        }
                    }
                    else {
                        $beat_fetch_final = $fetch_location_id;
                        foreach ($beat_fetch_final as $key => $value)
                            $beat_fetch_final_array[$value] = $value;
                        if (!empty($delete_id_location)) {
                            foreach ($delete_id_location as $inkey => $invalue) {
                                if (array_key_exists($inkey, $beat_fetch_final_array))
                                    unset($beat_fetch_final_array[$inkey]);
                            }
                        }
                    } // else part end here
                    if (!empty($rows['beat_id_delete'])) {
                        $beat_delete = explode(',', $rows['beat_id_delete']);
                        $beat_delete_final = array_unique(array_merge($beat_delete, $delete_id_location));
                    } else {
                        $beat_delete_final = $delete_id_location;
                    }
                    $beat_fetch_final_string = !empty($beat_fetch_final_array) ? implode(',', $beat_fetch_final_array) : '';
                    $beat_delete_final_string = !empty($beat_delete_final) ? implode(',', $beat_delete_final) : '';
                    $q = "UPDATE person_login SET beat_id_fetch = '$beat_fetch_final_string', beat_id_delete='$beat_delete_final_string', dealer_id_fetch = '$dealer_id', dealer_id_delete = '$dealer_id', sync_status = '1' WHERE person_id = '$rows[user_id]'";

                    $r = mysqli_query($dbc, $q);
                } // While loop end here
            } //  if($opt1) end here
        } // if(!empty($fetch_location_id) || !empty($delete_id_location)) end here
        return $out;
    }

    public function update_dealer_id_fetch($actiontype, $dealer_id, $userid) {
        global $dbc;
        $out = array('status' => true, 'myreason' => '');
        $updater = array();
        $dealerarray = array();
        $beat = array();
        $location = array();
        $retailer = array();
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        $retailer_level = $_SESSION[SESS . 'constant']['retailer_level'];

        $q = "SELECT dealer_id FROM user_dealer_retailer WHERE user_id='$userid'";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            $dealerarray[$dealer_id] = $dealer_id;
        if ($opt) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['dealer_id'];
                $dealerarray[$id] = $id;
            }
        }
        $dealerarraystr = implode(',', $dealerarray); //here we get dealer id fetch data
        $q = "SELECT l.id FROM dealer_location_rate_list INNER JOIN location_$dealer_level l ON l.id= dealer_location_rate_list.location_id WHERE dealer_id IN ($dealerarraystr) ";
        list($opt1, $rs1) = run_query($dbc, $q, 'multi');
        if ($opt1) {
            while ($rows = mysqli_fetch_assoc($rs1)) {
                $id = $rows['id'];
                $beat[$id] = $id;
            }
            $beatstr = implode(',', $beat); // here we get beat id fetch data

            if (!empty($beat)) {

                $q = "SELECT location_$retailer_level.id AS lid,location_$retailer_level.name FROM location_$dealer_level";
                for ($i = $dealer_level; $i < $retailer_level; $i++) {
                    $j = $i + 1;
                    $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
                }
                $q .= " INNER JOIN dealer_location_rate_list ON location_$dealer_level.id = dealer_location_rate_list.location_id WHERE location_$dealer_level.id IN ($beatstr)";
                list($opt2, $rs2) = run_query($dbc, $q, 'multi');
                if ($opt2) {
                    while ($rows2 = mysqli_fetch_assoc($rs2)) {
                        $id = $rows2['lid'];
                        $location[$id] = $id;
                    }
                    //pre($location);
                    $locationstr = implode(',', $location); //here we get location_id data
                    if (!empty($location)) {
                        $q = "SELECT id FROM retailer WHERE location_id IN ($locationstr)";
                        list($opt3, $rs3) = run_query($dbc, $q, 'multi');
                        if ($opt3) {

                            while ($rows3 = mysqli_fetch_assoc($rs3)) {
                                $id = $rows3['id'];
                                $retailer[$id] = $rows3['id'];
                            }

                            $retailerstr = implode(',', $retailer); //here we get retailer id fetch data
                        }
                    }
                }
            }
        }
        $q = "SELECT dealer_id_fetch,retailer_id_fetch,location_id_fetch,beat_id_fetch FROM person_login WHERE person_id = '$userid'";
        list($opt, $rs) = run_query($dbc, $q, 'single');
        $dealer_id_fetch = explode(',', $rs['dealer_id_fetch']);
        $retailer_id_fetch = explode(',', $rs['retailer_id_fetch']);
        $location_id_fetch = explode(',', $rs['location_id_fetch']);
        $beat_id_fetch = explode(',', $rs['beat_id_fetch']);
        foreach ($dealerarray as $key => $value) {
            if (!in_array($value, $dealer_id_fetch)) {
                $dealer_id_fetch[$value] = $value;
                //array_push($dealer_id_fetch, $value);
            }
        }
        foreach ($retailer as $key1 => $value1) {
            if (!in_array($value1, $retailer_id_fetch)) {
                // array_push($retailer_id_fetch, $value1);
                $retailer_id_fetch[$value1] = $value1;
            }
        }
        foreach ($location as $key2 => $value2) {
            if (!in_array($value2, $location_id_fetch)) {
                //array_push($location_id_fetch, $value2);
                $location_id_fetch[$value2] = $value2;
            }
        }
        foreach ($beat as $key3 => $value3) {
            if (!in_array($value3, $beat_id_fetch)) {
                //array_push($beat_id_fetch, $value3);
                $beat_id_fetch[$value3] = $value3;
            }
        }
        $beat_id_fetch_str = implode(',', $beat_id_fetch);
        $beat_id_fetch_str = ltrim($beat_id_fetch_str, ',');
        $location_id_fetch_str = implode(',', $location_id_fetch);
        $location_id_fetch_str = ltrim($location_id_fetch_str, ','); //$retailer_id_fetch
        $retailer_id_fetch_str = implode(',', $retailer_id_fetch);
        $retailer_id_fetch_str = ltrim($retailer_id_fetch_str, ','); //$retailer_id_fetch
        $dealer_id_fetch_str = implode(',', $dealer_id_fetch);
        $dealer_id_fetch_str = ltrim($dealer_id_fetch_str, ','); //$retailer_id_fetch
        $q = "UPDATE person_login SET beat_id_fetch = '$beat_id_fetch_str' , retailer_id_fetch = '$retailer_id_fetch_str', location_id_fetch = '$location_id_fetch_str', dealer_id_fetch = '$dealer_id_fetch_str' WHERE person_id = '$userid'";

        $r = mysqli_query($dbc, $q);
        return $out;
    }

    //This function is used to update retailer_id_fetch and retailer_id delete
    public function update_retailer_id_fetch_coloumn($actiontype, $dealer_id, $new_retailer, $user_id) {
        global $dbc;

        $fetch_retailer_id = array();
        $delete_retailer_id = array();
        $out = array('status' => true, 'myreason' => '');
        $new_retailer_array = array();
        $dealer_array = array();
        $dealer_array[$dealer_id] = $dealer_id;
        if (!empty($new_retailer)) {
            foreach ($new_retailer as $key => $value)
                $new_retailer_array[$value] = $value;
        }

        $existing_retailer = array();
        //This query gives all the userid of that particular dealer
        $qq = "SELECT retailer_id FROM user_dealer_retailer WHERE dealer_id = '$dealer_id' AND user_id = '$user_id'";

        list($opt1, $rs1) = run_query($dbc, $qq, 'multi');
        //if(!$opt1) return $out = array('status'=>true,'myreason'=>'');
        if ($opt1) {
            while ($rows = mysqli_fetch_assoc($rs1)) {
                $existing_retailer[$rows['retailer_id']] = $rows['retailer_id'];
            }
        }

        //here we check if not empty existing retailer id
        if (!empty($existing_retailer)) {
            foreach ($existing_retailer as $key => $value) {
                if (!in_array($value, $new_retailer_array)) {
                    if ($value == 0)
                        continue;
                    $delete_retailer_id[$value] = $value;
                } else
                    unset($new_retailer_array[$key]);
            }

            if (!empty($new_retailer_array)) {
                foreach ($new_retailer_array as $key => $value)
                    $fetch_retailer_id[$value] = $value;
            }
        } //if(!empty($oldlocation)) end here
        else {
            foreach ($new_retailer_array as $key => $value)
                $fetch_retailer_id[$value] = $value;
        }

        if (!empty($fetch_retailer_id) || !empty($delete_retailer_id)) {
            //This query gives particular location_id_fetch and location_id_delete
            //This function is used to find dealer beat id details
            $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
            $q_beat = "SELECT location_id FROM dealer_location_rate_list INNER JOIN location_$dealer_level l ON l.id= dealer_location_rate_list.location_id WHERE dealer_id = '$dealer_id'";

            list($opt2, $rs2) = run_query($dbc, $q_beat, 'multi');
            $beat_fetch_array = array();
            if ($opt2) {
                while ($row = mysqli_fetch_assoc($rs2)) {
                    $beat_fetch_array[$row['location_id']] = $row['location_id'];
                }
            }

            $q = "SELECT dealer_id_fetch,retailer_id_fetch, retailer_id_delete,location_id_fetch,location_id_delete,beat_id_fetch FROM person_login WHERE person_id = '$user_id' LIMIT 1";

            list($opt, $rs) = run_query($dbc, $q, 'single');
            if (!$opt)
                return $out;
            if (!empty($rs['retailer_id_fetch'])) {
                $retailer_fetch = explode(',', $rs['retailer_id_fetch']);
            } else
                $retailer_fetch = array();

            if (!empty($rs['dealer_id_fetch'])) {
                $dealer_fetch = explode(',', $rs['dealer_id_fetch']);
            } else
                $dealer_fetch = array();

            if (!empty($rs['beat_id_fetch'])) {
                $beat_fetch = explode(',', $rs['beat_id_fetch']);
            } else
                $beat_fetch = array();

            if (!empty($rs['retailer_id_delete'])) {
                $retailer_delete = explode(',', $rs['retailer_id_delete']);
            } else
                $retailer_delete = array();
            if (!empty($rs['location_id_fetch'])) {
                $location_fetch = explode(',', $rs['location_id_fetch']);
            } else
                $location_fetch = array();

            $final_dealer_fetch_id = array_unique(array_merge($dealer_fetch, $dealer_array));
            $final_beat_fetch_id = array_unique(array_merge($beat_fetch, $beat_fetch_array));

            $final_retailer_fetch_id = array_unique(array_merge($retailer_fetch, $fetch_retailer_id));
            $final_retailer_fetch_id_array = array();
            if (!empty($final_retailer_fetch_id)) {
                foreach ($final_retailer_fetch_id as $key => $value) {
                    $final_retailer_fetch_id_array[$value] = $value;
                }
            }

            if (!empty($delete_retailer_id)) {
                foreach ($delete_retailer_id as $inkey => $invalue) {
                    if (array_key_exists($inkey, $final_retailer_fetch_id_array))
                        unset($final_retailer_fetch_id_array[$inkey]);
                }
            }

            $final_retailer_delete_id = array_unique(array_merge($retailer_delete, $delete_retailer_id));
            $final_dealer_fetch_id_str = implode(',', $final_dealer_fetch_id);
            $final_beat_fetch_id_str = implode(',', $final_beat_fetch_id);
            $final_retailer_fetch_id_array_str = implode(',', $final_retailer_fetch_id_array);
            $final_retailer_delete_id_str = implode(',', $final_retailer_delete_id);

            //here we also updated location_id_fetch and location_id_delete
            if (!empty($final_retailer_fetch_id_array_str)) {
                $q1 = "SELECT location_id FROM retailer WHERE id IN ($final_retailer_fetch_id_array_str)";
                $location_fetch_id = array();
                list($opt1, $rs1) = run_query($dbc, $q1, 'multi');
                if ($opt1) {
                    while ($row = mysqli_fetch_assoc($rs1)) {
                        $location_fetch_id[$row['location_id']] = $row['location_id'];
                    }
                    $final_location_details = array_unique(array_merge($location_fetch, $location_fetch_id));
                    $final_location_details_str = implode(',', $final_location_details);
                } // if($opt1) end here
            } // if(!empty($final_retailer_fetch_id_str)) end here
            //here we update person login table status
            $q = "UPDATE person_login SET retailer_id_fetch = '$final_retailer_fetch_id_array_str', retailer_id_delete = '$final_retailer_delete_id_str', sync_status='1',location_id_fetch='$final_location_details_str', location_id_delete = '$final_location_details_str',dealer_id_fetch = '$final_dealer_fetch_id_str',beat_id_fetch = '$final_beat_fetch_id_str',beat_id_delete = '$final_beat_fetch_id_str' WHERE person_id = '$user_id'";

            $r = mysqli_query($dbc, $q);
        } //if(!empty($diff_array)) end here
        return $out;
    }
    // This location should be added in dealer location rate list according to multiple company wise
    public function multi_company_dealer_location_extra($rId, $location, $rate_list_id) {
        global $dbc;
        $uncode = '';
        $str = array();
        // Fetching the old details store for other columns, so that during edit we can delete and reinsert data
        //during update we are required to remove the previous entry
        if (!empty($location)) {
            foreach ($location as $key => $value) {
                //$uncode = $rId.$value;
                //To save the value of the other columns as some columns are affected by po

                $str[] = "($rId, $value,$rate_list_id, '{$_SESSION[SESS.'data']['company_id']}')";
            }
            $str = implode(', ', $str);
        } // !empty end here
        if (!empty($str)) {
            $q = "INSERT INTO `dealer_location_rate_list` VALUES $str";
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Location could not be attach, please try after some times..');
        }
        //Update the qty in the database
        //mysqli_query($dbc,"UPDATE po SET totqty = ".array_sum($qty_sum)." WHERE p0Id = $rId LIMIT 1");
        return array('status' => true, 'myreason' => 'Location Succesfully assign to dealer');
    }
    // This function is used tof ind dealer balance stock
    public function get_dealer_balance_stock_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM dealer_bal_stock $filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $category_map =  get_my_reference_array('catalog_2', 'id', 'name');
        //pre($category_map);
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['category_name'] = $category_map[$row['catalog_id']];
            $out[$id]['username'] = $this->get_userfullname($row['user_id']);
        }
        return $out;
    }
    public function get_userfullname($id)
    {
        global $dbc;
        $out = NULL;
        $q = "SELECT CONCAT_WS(' ', first_name,middle_name,last_name) AS name FROM person WHERE id = '$id'";
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
         if(!$opt) return $out;
         $out = $rs['name'];
         return $out;
    }

      public function get_user_dealer_retailer_assign_checkbox_list($location_id ,$user_id, $dealer_id)
	{
		global $dbc;
                $out = array();
		$status= array('status'=>FALSE, 'data'=>'');
                $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
                $retailer_level = $_SESSION[SESS.'constant']['dealer_level'];
                $q = "SELECT location_$retailer_level.id FROM location_$dealer_level";
                for($i = $dealer_level; $i < $retailer_level; $i++)
                {
                    $j = $i +1;
                    $q .= " INNER JOIN location_$j ON location_$j.location_".$i."_id = location_$i.id";
                }
                $q .= " WHERE location_$dealer_level.id = '$location_id'";
              //  h1($q);
                list($opt ,$rs) = run_query($dbc ,$q ,'multi');
                if(!$opt) return $out;
                $location_data = array();
                while($row = mysqli_fetch_assoc($rs))
                {
                    $id = $row['id'];
                    $location_data[$id] = $row['id'];
                }
                if(!empty($location_data)){
                    $location_data_str = implode(',' , $location_data);
                    $q = "SELECT retailer.id FROM retailer INNER JOIN user_dealer_retailer ON user_dealer_retailer.retailer_id = retailer.id WHERE location_id IN ($location_data_str) AND user_id = '$user_id' AND user_dealer_retailer.dealer_id = '$dealer_id' AND retailer.retailer_status='1'";
                   // h1($q);
                   list($opt ,$rs) = run_query($dbc ,$q ,'multi');
                   if($opt) {
                       while($rows = mysqli_fetch_assoc($rs)) {
                           $id = $rows['id'];
                           $out[$id] = $id;
                       }
                    return array('status'=>TRUE, 'data'=>$out);
                   }
                   else return array('status'=>FALSE, 'data'=>'');
                }
                else return array('status'=>FALSE, 'data'=>'');

	}


         public function get_retailer_assign_by_user_list($filter='', $records='', $orderby='')
	{
		global $dbc;
                $inc=1;
                $filterstr=$this->oo_filter($filter, $records, $orderby);
              //  h1($filterstr);
		$out= array();
                $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
                $loop = $dealer_level + 1;
                $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
                $q = "SELECT r.id, r.name, address,email,landline FROM dealer_location_rate_list dlrl  INNER JOIN location_$dealer_level l$dealer_level ON l$dealer_level.id = dlrl.location_id";
           //   h1($q);
                for($i = $loop; $i<= $retailer_level;$i++)
                {
                    $j = $i - 1;
                    $q .= "  INNER JOIN location_$i l$i ON l$i.location_".$j."_id = l$j.id ";
                }
                $q .= " INNER JOIN retailer r ON r.location_id = l$retailer_level.id $filterstr";

             //----------------------Query BY Murari-------------------------//
               //$q = "SELECT r.id, r.name, address,email,landline FROM retailer r INNER JOIN location_$dealer_level l$dealer_level ON l$dealer_level.id = r.location_id INNER JOIN
//dealer_location_rate_list dlrl on dlrl.dealer_id=r.dealer_id $filterstr ";


               // h1($q);

                list($opt ,$rs) = run_query($dbc ,$q , 'multi');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs))
                {
                    $id = $row['id'].$inc;
                    $out[$id] = $row;
                $inc++;}
                return $out;

	}

          public function get_dealer_location_ajax_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
                $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
		$q = "SELECT location_$dealer_level.id, location_$dealer_level.name FROM dealer_location_rate_list INNER JOIN location_$dealer_level ON location_$dealer_level.id = dealer_location_rate_list.location_id $filterstr";
             //   h1($q);
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['id'];
			$out[$id] = $row; // storing the item id

		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;
	}


           public function delete_map_dealer($data,$filter='', $records='', $orderby='')
	{
		global $dbc;
                $exdata = explode('_', $data);
                $dealerid = $exdata[0];
                $userid = $exdata[1];

                $ud="SELECT user_id,dealer_id,retailer_id,udr_date_time FROM  `user_dealer_retailer` WHERE `dealer_id` ='$dealerid' AND `user_id` ='$userid'";
                   //h1($ud);
                 $run_ud = mysqli_query($dbc, $ud);
                     if(mysqli_num_rows($run_ud) > 0 )
			{
                         while($row1 = mysqli_fetch_assoc($run_ud)){
                            $qlog = "INSERT INTO `user_dealer_retailer_log` (`user_id`, `dealer_id`,`retailer_id`,`udr_date_time`,`server_date`)
                          VALUES ('$row1[user_id]','$row1[dealer_id]','$row1[retailer_id]','$row1[udr_date_time]',NOW())";
                            $rellog = mysqli_query($dbc,$qlog);
                         }
                        }
                     if($rellog){
                        $qdel = "DELETE FROM user_dealer_retailer WHERE user_id = '$userid' AND dealer_id = '$dealerid'";
                      // h1($qdel);
                        $rel = mysqli_query($dbc,$qdel);
                     }


                 $udl="SELECT user_id,dealer_id,server_date,location_id FROM  `dealer_location_rate_list` WHERE  `dealer_id` ='$dealerid' AND `user_id` ='$userid'";
                //  h1($udl);
                 $run_udl = mysqli_query($dbc, $udl);
                     if(mysqli_num_rows($run_udl) > 0 )
                    {
                       while($del_row = mysqli_fetch_assoc($run_udl)){
                          $qlogl = "INSERT INTO `dealer_location_rate_list_log` (`user_id`, `dealer_id`,`location_id`,`server_date`,`dlrl_date_time`)
			VALUES ('$del_row[user_id]','$del_row[dealer_id]','$del_row[location_id]','$del_row[server_date]',NOW())";
                        //  h1($qlogl);
                          $relloc = mysqli_query($dbc,$qlogl);
                         }
                    }
                     if($relloc){
                        $qdl = "DELETE FROM dealer_location_rate_list WHERE user_id = '$userid' AND dealer_id = '$dealerid' ";
                      //  h1($qdl);
                        $dell = mysqli_query($dbc,$qdl);
                     }

              //  mysqli_commit($dbc);
                return array('status'=>true, 'myreason'=>'Dealer successfully deleted');

	}

    public function dealer_delete($id, $filter='', $records='', $orderby='')
    {
        global $dbc;
        if(empty($filter)) $filter = "person.id = '$id'";
        $out = array('status'=>false, 'myreason'=>'');
        //$deleteRecord = $this->get_user_list($filter, $records, $orderby);

        //if(empty($deleteRecord)){ $out['myreason'] = 'Dealer not found'; return $out;}
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");

        //Running the deletion queries
        $delquery = array();
        $delquery['dealer'] = "UPDATE dealer SET dealer_status = '0' WHERE id = '$id'";

        foreach($delquery as $key=>$value){
            if(!mysqli_query($dbc, $value)){
                mysqli_rollback($dbc);
                return array('status'=>false, 'myreason'=>'$key query failed');
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
                return array('status'=>true, 'myreason'=>'Dealer successfully deleted');
    }

    public function get_new_dealer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM dealer $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['rate_list_id'] = $this->get_new_rate_list_id($row['id']);
            $out[$id]['dealer_state_id'] = $this->get_new_dealer_rate_list($row['id']);
            //h1("SELECT id, name FROM dealer_location_rate_list INNER JOIN location_{} AS l3 ON l3.id = dealer_location_rate_list.location_id WHERE dealer_id = '$id'");
            $out[$id]['location_details'] = $this->get_my_reference_array_direct($q = "SELECT id, name FROM dealer_location_rate_list INNER JOIN location_$dealer_level AS l3 ON l3.id = dealer_location_rate_list.location_id WHERE dealer_id = '$id'", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
       // pre($out); exit;
        return $out;
    }

    public function get_new_rate_list_id($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT rate_list_id FROM dealer_location_rate_list WHERE dealer_id = $id GROUP BY dealer_id ASC";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['rate_list_id'];
    }

    public function get_new_dealer_rate_list($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT lv.state_id FROM dealer_location_rate_list dlrl INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id WHERE dlrl.dealer_id = $id limit 1";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['state_id'];
    }

}

?>
