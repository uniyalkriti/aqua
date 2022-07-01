<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class catalog extends myfilter
{
    public $poid = NULL;

    public function __construct()
    {
        parent::__construct();
    }


    ######################################## catalog start here ######################################################
    public function get_catalog_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        //$d1['csess'] = $_SESSION[SESS.'csess'];

        $d1['myreason'] = 'Please fill all the required information';
        $title = "catalog_title_" . $d1[mtype];
        $d1['what'] = $_SESSION[SESS . 'constant'][$title];
        return array(true, $d1);
    }

    ######################################## catalog save code  start here ######################################################
    public function catalog_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_catalog_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $id = $d1['uid'] . date('Ymdhis');
        $q = "INSERT INTO `catalog_1` (`id`, `name`, `company_id`) VALUES ('$id', '$d1[name]', '$d1[company_id]')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    ######################################## catalog code edit start here ######################################################
    public function catalog_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_catalog_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
        $originaldata = $this->get_catalog_list("id = $id");
        $originaldata = $originaldata[$id];
        $modifieddata = $this->get_modified_data($originaldata, $d1);
        if (empty($modifieddata)) return array('status' => false, 'myreason' => 'Please do <strong>atleast 1 change</strong> to update');
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE catalog_1 SET `name` = '$d1[name]', company_id = '$d1[company_id]' WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    ######################################## catalog list code  start here ######################################################
    public function get_catalog_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM catalog_1  $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    ######################################## catalog code delete start here ######################################################
    public function get_catalog_deletion_list($filter = '', $records = '', $orderby = '', $mtype)
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM catalog_$mtype  $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    // This function is used to delte catalog product
    public function category_delete($id, $filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $catalog_level = $_SESSION[SESS . 'constant']['catalog_level'];
        $id = explode('<$>', $id);
        $catalog_id = $id[0];
        $mtype = $id[1];
        $next_catalog = $mtype + 1;
        if (empty($filter)) $filter = "id = $catalog_id";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_catalog_deletion_list($filter, $records, $orderby, $mtype);

        if (empty($deleteRecord)) {
            $out['myreason'] = 'Catalog not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //Checking whether the category is deletable or not
        $q['catalog'] = "SELECT id FROM catalog_$next_catalog WHERE catalog_" . $mtype . "_id = ";
        if ($mtype == $catalog_level)
            $q['catalog_product'] = "SELECT catalog_id FROM catalog_product INNER JOIN catalog_$mtype ON catalog_$mtype.id = catalog_product.catalog_id  WHERE catalog_id = ";

        $found = false;
        foreach ($q as $key => $value) {
            $q1 = "$value $catalog_id LIMIT 1";
            list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'single', $msg = '');
            if ($opt1) {
                $found = true;
                $found_case = $key;
                break;
            }
        }
        // If this category has been found in any one of the above query we can not delete it.
        if ($found) {
            $out['myreason'] = 'Catalog  entered in <b>' . $found_case . '</b> so could not be deleted.';
            return $out;
        }

        //Running the deletion queries
        $delquery = array();
        $delquery['location'] = "DELETE FROM catalog_$mtype  WHERE id = $catalog_id LIMIT 1";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Catalog successfully deleted');
    }

    public function get_catalog_category_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        $d1['myreason'] = 'Please fill all the required information';
        $title = "catalog_title_" . $d1[mtype];
        $d1['what'] = $_SESSION[SESS . 'constant'][$title];
        return array(true, $d1);
    }

    ######################################## catalog save code  start here ######################################################
    public function catalog_category_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_catalog_category_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);
        //start the transaction
        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        mysqli_query($dbc, "START TRANSACTION");
        $catalogloopid = "catalog_" . $loop . "_id";
        $catname = $mtype == 2 ? 'cname' : "name$mtype";
        $catalog_1_id = $_POST[catalog_1_id];
        $company_id = $_SESSION[SESS . 'data']['company_id'];
        $id = $d1['uid'] . date('Ymdhis');
        // query to save
        $q = "INSERT INTO catalog_$mtype (`id`, `name`,`catalog_1_id`,`company_id`) VALUES ('$id', '$d1[$catname]','$catalog_1_id', '$company_id')";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' table error');
        }
        $rId = $id;
        mysqli_commit($dbc);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    public function catalog_category_edit($id)
    {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_catalog_category_se_data();
        if (!$status) return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $mtype = $d1['mtype'];
        $loop = $mtype - 1;
        $catalogloopid = "catalog_" . $loop . "_id";
        $catname = "cname";
        $catalog_1_id = "catalog_1_id";
        $company_id = $_SESSION[SESS . 'data']['company_id'];
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE catalog_$mtype SET `name` = '$d1[$catname]',`catalog_1_id` = '$d1[$catalog_1_id]', company_id = '$company_id' WHERE id='$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' Table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        $rId = $id;
        //Saving the user modification history
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function get_catalog_category_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        if (isset($_POST['mtype'])) $mtype = $_POST['mtype'];
        if (isset($_GET['mtype'])) $mtype = $_GET['mtype'];
        $loop = $mtype - 1;
        $q = "SELECT *, catalog_2.id as id,catalog_2.name as cname,catalog_1.name as c1 FROM catalog_2  INNER JOIN catalog_1 ON catalog_1.id = catalog_2.catalog_1_id $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["id"];
            $out[$id] = $row; // storing the item id

        }
        return $out;
    }
######################################################


    public function get_catalog_rate_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
     $q = "select DISTINCT(location_3.id) as stateid ,location_3.name as statename from location_3 LEFT JOIN product_rate_list ON location_3.id = product_rate_list.state_id $filterstr ";
      list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["stateid"];
            $out[$id] = $row;
            // $q = " SELECT cprl.*,CONCAT_WS(' ',c2.name,cp.name,cp.unit)as name from product_rate_list cprl LEFT JOIN catalog_product cp ON cp.id = cprl.product_id LEFT JOIN catalog_2 c2 ON c2.id = cp.catalog_id  where state_id = '$id' ";
           $q = " SELECT cp.id as product_id, cprl.mrp,cprl.mrp_pcs,cprl.dealer_rate,cprl.dealer_pcs_rate,cprl.retailer_pcs_rate,
           cprl.retailer_rate,cprl.company_id,CONCAT_WS(' ',c2.name,cp.name,cp.unit)as name from  catalog_product cp
    LEFT JOIN catalog_2 c2 ON c2.id = cp.catalog_id 
    LEFT JOIN product_rate_list cprl  ON cp.id = cprl.product_id AND state_id = '$id' ORDER BY mrp DESC";
    
            $out[$id]['rate_list'] = $this->get_rate_list($q);
        }
        return $out;
    }

    public function get_rate_list($q)
    {
        global $dbc;
        $res = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($res)) {
            $id = $row['product_id'];
            $out[$id] = $row;
        }
        return $out;
    }


   public function get_catalog_rate_se_data()
    {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        //$d1['csess'] = $_SESSION[SESS.'csess'];

        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = "Catalog Rate List";
        return array(true, $d1);
    }

    ######################################## catalog save code  start here ######################################################
 
 
 
    public function catalog_rate_save()
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_catalog_rate_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);

        mysqli_query($dbc, "START TRANSACTION");
        $company_id = $_SESSION[SESS . 'data']['company_id'];
        foreach ($d1['name'] as $key => $value) {
           
if(($d1['dealer_rate'][$key]+$d1['retailer_rate'][$key]+$d1['mrp'][$key]+$d1['mrp_pcs'][$key]+$d1['dealer_pcs_rate'][$key]+$d1['retailer_pcs_rate'][$key])!=0){
            $str .= "('{$d1[product_id][$key]}','{$d1[state_id]}','{$d1[mrp][$key]}','{$d1[mrp_pcs][$key]}','{$d1[dealer_rate][$key]}','{$d1[dealer_pcs_rate][$key]}','{$d1[retailer_rate][$key]}','{$d1[retailer_pcs_rate][$key]}','1'),";
        }
    }
        $str = rtrim($str, ',');

        $q = "INSERT INTO product_rate_list (`product_id`, `state_id`, `mrp`,`mrp_pcs`,`dealer_rate`,`dealer_pcs_rate`, `retailer_rate`,`retailer_pcs_rate`,`company_id`) VALUES $str ";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' table error');
        }
        $rId = $id;
        mysqli_commit($dbc);
        //Logging the history
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }


 public function catalog_rate_edit($id)
    {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_catalog_rate_se_data();
        if (!$status) return array('status' => false, 'myreason' => $d1['myreason']);

        mysqli_query($dbc, "START TRANSACTION");
        $q = "delete from product_rate_list where state_id ='$d1[state_id]' ";
        $r = mysqli_query($dbc, $q);
        $company_id = $_SESSION[SESS . 'data']['company_id'];
        foreach ($d1['name'] as $key => $value) {           
if(($d1['dealer_rate'][$key]+$d1['retailer_rate'][$key]+$d1['mrp'][$key]+$d1['mrp_pcs'][$key]+$d1['dealer_pcs_rate'][$key]+$d1['retailer_pcs_rate'][$key])!=0){
       
$str .= "('{$d1[product_id][$key]}','{$d1[state_id]}','{$d1[mrp][$key]}','{$d1[mrp_pcs][$key]}','{$d1[dealer_rate][$key]}','{$d1[dealer_pcs_rate][$key]}','{$d1[retailer_rate][$key]}','{$d1[retailer_pcs_rate][$key]}','1'),";
}
            
        }
        $str = rtrim($str, ',');

        $q = "INSERT INTO product_rate_list (`product_id`, `state_id`, `mrp`,`mrp_pcs`,dealer_rate,`dealer_pcs_rate`, `retailer_rate`,`retailer_pcs_rate`,`company_id`) VALUES $str ";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $d1['what'] . ' table error');
        }
        $rId = $id;
        mysqli_commit($dbc);
        //Logging the history
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
}// class end here
?>