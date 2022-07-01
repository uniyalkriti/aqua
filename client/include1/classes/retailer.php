<?php

class retailer extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## WORK PO Starts here ####################################################

    public function get_retailer_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['urole'] = $_SESSION[SESS . 'data']['urole'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Retailer'; //whether to do history log or not
        return array(true, $d1);
    }

    public function retailer_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_retailer_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);

        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $location = "location_" . $mtype . "_id";
       /* if ($d1['chk'] == 'chk') {
            $company_id = 1;
        } else {
            $company_id = 2;
        }*/
        //echo "<pre>";
        //print_r($d1);
        $company_id = 1;
        //Start the transaction
        $maxid = $_SESSION[SESS . 'data']['dealer_id'] . date('Ymdhis');
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `retailer` (`id`, `name`, `image_name`,`location_id`, `address`,`contact_per_name`, `email`, `landline`, `other_numbers`, `tin_no`, `pin_no`,`outlet_type_id`,`avg_per_month_pur`,`created_on`, `created_by_person_id`,`dealer_id`, `company_id`) 
            VALUES ($maxid, '$d1[name]','' ,'$d1[$location]', '$d1[address]','$d1[contact_per_name]','$d1[email]', '$d1[landline]', '$d1[other_numbers]','$d1[tin_no]','$d1[pin_no]','$d1[outlet_type_id]','$d1[avg_per_month_pur]',NOW(),'$d1[so]','{$_SESSION[SESS . 'data']['dealer_id']}','$company_id' );";
//            h1($q);die;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer Table error');
        } else {
            if ($company_id == 1)
                write_query($q);
            $q = "INSERT user_dealer_retailer VALUES('$d1[so]','$dealer_id','$maxid')";
            $r1 = mysqli_query($dbc, $q);
            if (!$r1) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'User Dealer Retailer Table error');
            }
            if ($company_id == 1)
                write_query($q);
        }
        $rId = $maxid;
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $maxid);
    }

    public function retailer_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_retailer_se_data();
        
        if (!$status){
            return array('staus' => false, 'myreason' => $d1['myreason']);            
        }

        //Checking whether the original data was modified or not
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $location = "location_" . $mtype . "_id";
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //query to update
        if ($d1['chk'] == 'chk') {
            $company_id = 1;
        } else {
            $company_id = 2;
        }
        $q = "UPDATE retailer SET name = '$d1[name]', address = '$d1[address]', email = '$d1[email]', landline = '$d1[landline]', other_numbers = '$d1[other_numbers]', tin_no = '$d1[tin_no]', pin_no = '$d1[pin_no]', `location_id` = '$d1[$location]',`outlet_type_id`='$d1[outlet_type_id]',`avg_per_month_pur`='$d1[avg_per_month_pur]', dealer_id = '$d1[dealer_id]', `company_id` = '$company_id',`contact_per_name`='$d1[contact_per_name]',created_by_person_id='$d1[so]' WHERE id = '$id'";
       
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer could not be updated, some error occurred.');
            if($company_id==1)
            {
                write_query($q);
            }
        }
        $rId = $id;

        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function get_retailer_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT retailer.*, CONCAT_WS(' ',person.first_name,person.last_name)as pname,created_by_person_id as so "
                . " FROM retailer LEFT JOIN person ON retailer.created_by_person_id = person.id   $filterstr";
    // h1($q);

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $r_level = $_SESSION[SESS . 'constant']['retailer_level'];
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the retailer id
            $out[$id]['retailer_location'] = $this->get_retailer_location($row['id']);
        } // while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_retailer_location($id) {
        global $dbc;
         $q1 = "SELECT location_5.* from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where retailer.id=$id";
      $rs = mysqli_query($dbc, $q1);
        while($row1 = mysqli_fetch_array($rs))  
        $out = $row1['name'];
        return $out;
    }

    // Here we get retailer address
    public function get_retailer_adr($id, $seperator = '<br>') {
        global $dbc;
        $out = '';
        $q = "SELECT retailer.*,(select lv.state_id from location_view lv where retailer.location_id=lv.l5_id) as state_id FROM retailer WHERE retailer.id = $id LIMIT 1";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        $out = address_representer($rs, array('address', 'pin_no', 'tin_no', 'landline', 'other_numbers','state_id'), $seperator);
        return $out;
    }

    public function get_retailer_location_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $loop = $mtype - 1;
        $str = '';
        for ($k = $mtype; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
        $q = "SELECT * $str FROM retailer INNER JOIN  location_$mtype ON location_$mtype.id = retailer.location_id ";
        for ($i = $mtype; $i > 1; $i--) {
            $j = $i - 1;
            $q .= "INNER JOIN location_$j ON location_$i.location_" . $j . "_id = location_$j.id ";
        }
        $q .= "$filterstr";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["location_" . $mtype . "_id"];
            $out[$id] = $row; // storing the item id
        }
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

    public function get_retailer_person_icon($id) {
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
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            // $id = $row['dealer_id'].$row['person_id'];
            $id = $row['person_id'];
            $out[$id] = $row;
        }

        return $out;
    }

    public function get_document_type_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _document_type $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    public function get_user_wise_retailer_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT id FROM retailer ORDER BY id DESC";
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
                $q = "SELECT retailer_id FROM user_dealer_retailer WHERE user_id IN ($user_id_str)";
                list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
                if (!$opt)
                    return $out;
                while ($row = mysqli_fetch_assoc($rs)) {
                    $out[$row['retailer_id']] = $row['retailer_id'];
                }
            } //if($opt) end here
            else {
                $q = "SELECT retailer_id FROM user_dealer_retailer WHERE user_id = '$main_id'";
                list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
                if (!$opt)
                    return $out;
                while ($row = mysqli_fetch_assoc($rs)) {
                    $out[$row['retailer_id']] = $row['retailer_id'];
                }
            } // else part end here
        }
        return $out;
    }

    public function get_user_wise_retailer_location_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1)
            return $out;
        $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            $out[$id] = $id;
        if ($opt) {
            $role_id_array = array();
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT id FROM person WHERE role_id IN ($role_id_str) AND person_id_senior='$main_id'";

            list($opt, $rs) = run_query($dbc, $q, 'multi');
            $out[$id] = $id;
            if ($opt) {
                while ($row = mysqli_fetch_assoc($rs)) {
                    $out[$row['id']] = $row['id'];
                }
            } // if($opt) end here
        }
        return $out;
    }

    public function get_location_ids($id) {
        global $dbc;
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        for ($i = 1; $i > ($dealer_level - 1); $i++) {
            $j = $i + 1;
            $str .= "location_$i.id, location_$i.name,";
            $loc_str .= " INNER JOIN location_$i INNER JOIN location_$j ON location_$i.id = location_$j.location_$i.'_id'  ";
        }
        //h1($str);
        //h1($loc_str);
    }

    ######################################## WORK FOR MULTIPLE COMPANY WISE RETAILER START HERE ####################################################
}

?>