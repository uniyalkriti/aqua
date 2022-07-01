<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class location extends myfilter
{
    public $poid = NULL;

    public function __construct()
    {
        parent::__construct();
    }


    ######################################## location start here ######################################################
    public function get_location_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];

        $d1['myreason'] = 'Please fill all the required information';
        $title = "location_title_" . $d1[mtype];
        $d1['what'] = $_SESSION[SESS . 'constant'][$title];
        return array(true, $d1);
    }

    ######################################## location save code  start here ######################################################
    public function location_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_location_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);
        $id = $d1['uid'] . date('Ymdhis');
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `location_1` (`id`, `name`, `company_id`) VALUES ('$id', '$d1[name]', '$d1[company_id]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' table error');
        }
        $rId = $id;
        mysqli_commit($dbc);
        //Logging the history
        //history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    ######################################## location code edit start here ######################################################
    public function location_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_location_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        $originaldata = $this->get_location_list("id = $id");
        $originaldata = $originaldata[$id];
        $modifieddata = $this->get_modified_data($originaldata, $d1);
        if (empty($modifieddata)) return array('status' => false, 'myreason' => 'Please do <strong>atleast 1 change</strong> to update');
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE location_1 SET `name` = '$d1[name]', company_id = '$d1[company_id]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' Table error');
        }

        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    ######################################## location list code  start here ######################################################
    public function get_location_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM location_1  $filterstr ";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    public function get_position_list($filter = '', $records = '', $orderby = '')
    {

        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

       $q = "SELECT * FROM  position_1  $filterstr ";
        //$q = "SELECT * FROM position_master where aid=66";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    ######################################## location code delete start here ######################################################
    public function get_location_deletion_list($filter = '', $records = '', $orderby = '', $mtype)
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM location_$mtype  $filterstr ";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    #Position delete list
    public function get_position_deletion_list($filter = '', $records = '', $orderby = '', $mtype)
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM position_master  $filterstr ";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    ######################################## location code delete start here ######################################################
    public function position_delete($id, $filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $id = explode('<$>', $id);
        $loc_id = $id[0];
        $mtype = '';
        $next_location = $mtype + 1;
        if (empty($filter)) $filter = "id = $loc_id";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_position_deletion_list($filter, $records, $orderby, $mtype);

        if (empty($deleteRecord)) {
            $out['myreason'] = 'Position not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //Checking whether the location is deletable or not
        $q['LOCATION'] = "SELECT id FROM position_master WHERE senior_position = ";

        $found = false;
        foreach ($q as $key => $value) {
            $q1 = "$value $loc_id LIMIT 1";
            list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'single', $msg = '');
            if ($opt1) {
                $found = true;
                $found_case = $key;
                break;
            }
        }
        // If this location has been found in any one of the above query we can not delete it.
        if ($found) {
            $out['myreason'] = 'Position  entered in <b>' . $found_case . '</b> so could not be deleted.';
            return $out;
        }

        //Running the deletion queries
        $delquery = array();
        $delquery['location'] = "DELETE FROM  position_master  WHERE id = '$loc_id' LIMIT 1";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Position successfully deleted');
    }

    #Position delete
    public function location_delete($id, $filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        $retailer_level = $_SESSION[SESS . 'constant']['retailer_level'];
        $id = explode('<$>', $id);
        $loc_id = $id[0];
        $mtype = $id[1];
        $next_location = $mtype + 1;
        if (empty($filter)) $filter = "id = $loc_id";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_location_deletion_list($filter, $records, $orderby, $mtype);

        if (empty($deleteRecord)) {
            $out['myreason'] = 'Location not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //Checking whether the location is deletable or not
        $q['LOCATION'] = "SELECT id FROM location_$next_location WHERE location_" . $mtype . "_id = ";
        if ($mtype == $dealer_level)
            $q['dealer'] = "SELECT dealer_id FROM dealer_location_rate_list INNER JOIN location_$mtype ON location_$mtype.id = dealer_location_rate_list.location_id INNER JOIN dealer ON dealer.id = dealer_location_rate_list.dealer_id WHERE location_id = ";

        if ($mtype == $retailer_level)
            $q['retailer'] = "SELECT r.id FROM location_$mtype INNER JOIN retailer r ON r.location_id = location_$mtype.id WHERE location_id = ";
        $found = false;
        foreach ($q as $key => $value) {
            $q1 = "$value $loc_id LIMIT 1";
            list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'single', $msg = '');
            if ($opt1) {
                $found = true;
                $found_case = $key;
                break;
            }
        }
        // If this location has been found in any one of the above query we can not delete it.
        if ($found) {
            $out['myreason'] = 'Location  entered in <b>' . $found_case . '</b> so could not be deleted.';
            return $out;
        }

        //Running the deletion queries
        $delquery = array();
        $delquery['location'] = "DELETE FROM  location_$mtype  WHERE id = '$loc_id' LIMIT 1";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Location successfully deleted');
    }

    ######################################## catalog save code  start here ######################################################
    public function get_location_category_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        //$d1['csess'] = $_SESSION[SESS.'csess'];
        $d1['myreason'] = 'Please fill all the required information';
        $title = "location_title_" . $d1[mtype];
        $d1['what'] = $_SESSION[SESS . 'constant'][$title];
        return array(true, $d1);
    }

    public function location_category_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_location_category_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        mysqli_query($dbc, "START TRANSACTION");
        $catalogloopid = "location_" . $loop . "_id";
        $catname = "name$mtype";
        $id = $d1['uid'] . date('Ymdhis');
        if ($mtype == '5') {
            $id = '';
        }
        // query to save
        $q = "INSERT INTO location_$mtype (`id`, `name`, `location_" . $loop . "_id`, `company_id`) VALUES ('$id', '$d1[$catname]','$d1[$catalogloopid]', '$d1[company_id]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . 'table error');
        }
        $rId = $id;
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    #for save position
    public function position_master_save($arr = Array())
    {
        global $dbc;

        $state = $arr['state'];
        $role =  $arr['role'];
        $senior = $arr['senior'];
        $name = $arr['name'];

        $role = implode(',',$role);
       
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO position_master (`name`, `senior_position`,`role_id`,`state_id`,`updated_at`) VALUES 
('$name','$senior','$role','$state',NOW())";

        #Run query
        $r = mysqli_query($dbc, $q);

        if (!$r) {
            mysqli_rollback($dbc);
        } else {
            mysqli_commit($dbc);
        }
        return array('status' => true, 'myreason' => 'Position successfully saved', 'rId' => '');
    }


    ######EDIT#############################################################
     public function position_master_edit($arr = Array(),$id)
    {
        global $dbc;
      // print_r($arr);
       //die;
        $state = $arr['state'];
        $role =  $arr['role'];
        $senior = $arr['senior'];
        
        //echo $senior;
     //   die;
        $name = $arr['name'];
        $id = $arr['eid'];
  
        $role = implode(',',$role);
       
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $qup = "UPDATE position_master SET `name`='$name', `senior_position`='$senior',`role_id`='$role',`state_id`='$state',`updated_at`=NOW() where id = '$id'"; 
      //  echo $qup;

        

        #Run query
        $r = mysqli_query($dbc, $qup);

        if (!$r) {
            mysqli_rollback($dbc);
        } else {
            mysqli_commit($dbc);
        }
        return array('status' => true, 'myreason' => 'Position updated Successfully', 'rId' => '');
    }

   ########################################################################
    #for position master
    public function location_position_save($arr = Array())
    {
        global $dbc;
        $mtype = !empty($arr['mtype']) ? $arr['mtype'] : '';
        $key = 'name' . $mtype;
        $parent = $mtype - 1;
        $parent_id = 'position_' . $parent . '_id';

//        echo '<pre>';print_r($arr);die;

        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        if ($mtype == 1) {
            $q = "INSERT INTO position_$mtype (`position_name`, `updated_at`) VALUES ('$arr[$key]',NOW())";
        } else {
            $q = "INSERT INTO position_$mtype (`position_name`, `position_" . $parent . "_id`, `updated_at`) VALUES ('$arr[$key]','$arr[$parent_id]', NOW())";
        }
        #Run query
        $r = mysqli_query($dbc, $q);

        if (!$r) {
            mysqli_rollback($dbc);
        } else {
            mysqli_commit($dbc);
        }
        return array('status' => true, 'myreason' => $arr['what'] . ' successfully Saved', 'rId' => '');
    }



    public function location_category_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_location_category_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        $catalogloopid = "location_" . $loop . "_id";
        $catname = "name$mtype";
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        h1($d1['uid'] . date('Ymdhis'));
        // query to update
        $q = "UPDATE location_$mtype SET `name` = '$d1[$catname]',`location_" . $loop . "_id` = '$d1[$catalogloopid]', `company_id` = '$d1[company_id]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . 'location_$mtype Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function location_position_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_location_category_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        $catalogloopid = "position_" . $loop . "_id";
        $catname = "name$mtype";
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
//                h1($d1['uid'].date('Ymdhis'));
        // query to update
        if ($loop > 0) {
            $q = "UPDATE position_$mtype SET `position_name` = '$d1[$catname]',`position_" . $loop . "_id` = '$d1[$catalogloopid]' WHERE id='$id'";
        } else {
            $q = "UPDATE position_$mtype SET `position_name` = '$d1[$catname]' WHERE id='$id'";
        }
//		echo $q;die;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . 'position_' . $mtype . ' Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;

        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function location_position_master_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_location_category_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        $catalogloopid = "position_" . $loop . "_id";
        $catname = "name$mtype";
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
//                h1($d1['uid'].date('Ymdhis'));
        // query to update
        if ($loop > 0) {
            $q = "UPDATE position_$mtype SET `position_name` = '$d1[$catname]',`position_" . $loop . "_id` = '$d1[$catalogloopid]' WHERE id='$id'";
        } else {
            $q = "UPDATE position_$mtype SET `position_name` = '$d1[$catname]' WHERE id='$id'";
        }
//		echo $q;die;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . 'position_' . $mtype . ' Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;

        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_location_category_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        if (isset($_POST['mtype'])) $mtype = $_POST['mtype'];
        if (isset($_GET['mtype'])) $mtype = $_GET['mtype'];
        $loop = $mtype - 1;
        $str = '';
        for ($k = $mtype; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
        $q = "SELECT * $str FROM location_$mtype ";
        for ($i = $mtype; $i > 1; $i--) {
            $j = $i - 1;
            $q .= "INNER JOIN location_$j ON location_$i.location_" . $j . "_id = location_$j.id ";
        }
        $q .= "$filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["location_" . $mtype . "_id"];
            $out[$id] = $row; // storing the item id

        }
        return $out;
    }

    public function get_location_position_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        if (isset($_POST['mtype'])) $mtype = $_POST['mtype'];
        if (isset($_GET['mtype'])) $mtype = $_GET['mtype'];
        $loop = $mtype - 1;
        $str = '';
        for ($k = $mtype; $k >= 1; $k--) {
            $str .= ",position_$k.position_name AS name$k,position_$k.id AS position_" . $k . "_id ";
        }
        $q = "SELECT * $str FROM position_$mtype ";
        for ($i = $mtype; $i > 1; $i--) {
            $j = $i - 1;
            $q .= "INNER JOIN position_$j ON position_$i.position_" . $j . "_id = position_$j.id ";
        }
        $q .= "$filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["position_" . $mtype . "_id"];
            $out[$id] = $row; // storing the item id

        }
        return $out;
    }

    public function get_position_master_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        $state = 'location_3';
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        /*$q = "SELECT position_master.*,_role.rolename,$state.name as state_name, (SELECT p.name FROM position_master p WHERE p.id=position_master.senior_position) as senior FROM position_master INNER JOIN $state ON position_master.state_id=location_2.id INNER JOIN _role ON _role.role_id=position_master.role_id ";*/


         $q ="SELECT position_master.*,_role.rolename,l3.name as state_name, (SELECT p.name FROM position_master p WHERE p.id=position_master.senior_position) as senior FROM position_master INNER JOIN location_3 l3 ON position_master.state_id=l3.id INNER JOIN _role ON _role.role_id=position_master.role_id ";

        // $q ="SELECT position_master.*,_role.rolename,state.statename as state_name, (SELECT p.name FROM position_master p WHERE p.id=position_master.senior_position) as senior FROM position_master INNER JOIN state ON position_master.state_id=state.location2_id INNER JOIN _role ON _role.role_id=position_master.role_id ";
      // h1($q);
//exit;

        $q .= "$filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        $all_roles='';
        while ($row = mysqli_fetch_assoc($rs)) {
        $q1 ="SELECT role_id,rolename from _role where role_id in($row[role_id])";
        $res=mysqli_query($dbc,$q1);
        $role_name=array();
        while ($row1 = mysqli_fetch_assoc($res)) {
            $role_name[] = $row1['rolename'];

        }

        $all_roles = implode(',',$role_name);
     
        $id = $row["id"];

        $out[$id] = $row; // storing the item id
        $out[$id]['rolenames']=$all_roles;
             
        }
//		echo '<pre>';print_r($out);die;
        return $out;
    }
}// class end here
?>