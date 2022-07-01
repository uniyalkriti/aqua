<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class catalog_product extends myfilter {

    public $poid = NULL;

    public function __construct() {
        parent::__construct();
    }

    ######################################## catalog start here ######################################################		

    public function get_catalog__product_se_data() {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        //$d1['csess'] = $_SESSION[SESS.'csess'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Catalog Product'; //whether to do history log or not
        return array(true, $d1);
    }

    ########################## Catalog save code  start here ######################

    public function catalog_product_save() {
        global $dbc;
        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_catalog__product_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
         
      $q = " SELECT stateid from state INNER JOIN person ON state.stateid = person.state_id INNER JOIN user_dealer_retailer ON person.id = user_dealer_retailer.user_id where dealer_id ='".$sesId = $_SESSION[SESS.'data']['dealer_id']."'  LIMIT 1 ";
        $res = mysqli_query($dbc, $q);
        $rows = mysqli_fetch_array($res);
        $state_id = $rows['stateid'];
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        foreach ($d1['name'] as $key => $value) {

            $q = "INSERT INTO `catalog_product` (`id`, `name`,`image_name`,`catalog_id`,`unit`,`base_price`,`taxable`, `company_id`) VALUES (NULL, '{$d1[name][$key]}','Null','0','{$d1[unit][$key]}','0.00', '0', '$d1[company_id]')";

            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Catalog Product table error');
            }
            $rId = mysqli_insert_id($dbc);
           $q = "INSERT INTO `catalog_product_rate_list` (`catalog_product_id`, `stateId`, `rate`,`tax`,`company_id`) VALUES ('$rId','$state_id','{$d1[rate][$key]}','{$d1[tax][$key]}','$d1[company_id]')"; 
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Catalog Product Rate table error');
        }
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    ######################################## catalog code edit start here ######################################################

    public function catalog_product_edit($id) {       
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_catalog__product_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not
//        $upload_path = $this->get_document_type_list($filter = "id IN (8,9)", $records = '', $orderby = '');
//        $catalog_path = $upload_path[8]['documents_location'];
//        $catalog_path = MYUPLOADS . $catalog_path;
//        $browse_file = $_FILES['image_name']['name'];
//
//        if (!empty($browse_file)) {
//            list($uploadstat, $filename) = fileupload('image_name', $catalog_path, $allowtype = array('image/jpeg', 'image/png', 'image/gif'), $maxsize = 52428800, $mandatory = true);
//            if ($uploadstat) {
//                resizeimage($filename, $catalog_path, $newwidth = 240, $thumbnailwidth = 200, MSYM, $thumbnail = true);
//                $path = "../myuploads/product/$_POST[old_file]";
//                $path1 = "../myuploads/product/thumbnail/$_POST[old_file]";
//                if (is_file($path))
//                    unlink($path);
//                if (is_file($path1))
//                    unlink($path1);
//            }
//        }
//        else {
//            $filename = $_POST['old_file'];
//        }
        // h1($d1['uid'].date('Ymdhis'));

//        $catlevel = $_SESSION[SESS . 'constant']['catalog_level'];
//        $name = "catalog_" . $catlevel . "_id";
//                $mdate = !empty($d1['manufacture_date']) ? get_mysql_date($d1['manufacture_date']) : '';
//                $exdate = !empty($d1['expiry_date']) ? get_mysql_date($d1['expiry_date']) : '';
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        foreach($d1['name'] as $key => $val){
     $q = "UPDATE catalog_product SET `name` = '{$d1[name][$key]}', `image_name` = 'Null', `unit` = '{$d1[unit][$key]}', `base_price` = '0.00', `catalog_id` = '0', taxable = '0', company_id = '$d1[company_id]' WHERE id='$id'";
       
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Product Catalog Table error');
        }else{
         $q = "UPDATE `catalog_product_rate_list` SET `catalog_product_id`='$id' , `rate` ='{$d1[rate][$key]}' ,`tax` ='{$d1[tax][$key]}' ,`company_id` ='$d1[company_id]' WHERE catalog_product_id='$id' "; 
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Catalog Product Rate table error');
        }
         }
        $rId = $id;
        mysqli_commit($dbc);
        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    ######################################## catalog list code  start here ######################################################

    public function get_catalog_product_list($filter = '', $records = '', $orderby = '')
    {
        global $dbc;
        $out = array();

        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
	    $state_id  = $_SESSION[SESS . 'data']['state_id'];

        $q = "SELECT *,catalog_product.id as uid,c1_name as c1,catalog_product.id, CONCAT_WS(' ',catalog_product.name)as name, cases.piece 
                FROM catalog_product 
                INNER JOIN product_rate_list ON catalog_product.id = product_rate_list.product_id 
                INNER JOIN catalog_view ON catalog_view.c2_id=catalog_product.catalog_id 
                JOIN cases ON catalog_product.id=cases.product_id 
                WHERE state_id = $state_id ORDER BY c1"; 
                
        //h1($q);
                
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uid'];
            $out[$id] = $row; // storing the item id
        }

        return $out;
    }

    public function get_product_catalog_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['catalog_level'];
        $loop = $mtype - 1;
        $str = '';
        for ($k = $mtype; $k >= 1; $k--) {
            $str .= ",catalog_$k.name AS name$k,catalog_$k.id AS catalog_" . $k . "_id ";
        }
        $q = "SELECT * $str FROM catalog_product INNER JOIN  catalog_$mtype ON catalog_$mtype.id = catalog_product.catalog_id ";
        for ($i = $mtype; $i > 1; $i--) {
            $j = $i - 1;
            $q .= "INNER JOIN catalog_$j ON catalog_$i.catalog_" . $j . "_id = catalog_$j.id ";
        }
        $q .= "$filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row["catalog_" . $mtype . "_id"];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    // This function is used to set automatic path for all the document
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

    ######################################## catalog start here ######################################################		

    public function get_product_details_se_data() {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Catalog Product Details'; //whether to do history log or not
        return array(true, $d1);
    }

    ######################################## catalog save code  start here ######################################################

    public function product_details_save() {
        global $dbc;

        $out = array('status' => false, 'myreason' => '');
        list($status, $d1) = $this->get_product_details_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $mdate = !empty($d1['manufacture_date']) ? get_mysql_date($d1['manufacture_date']) : '';
        $exdate = !empty($d1['expiry_date']) ? get_mysql_date($d1['expiry_date']) : '';
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to save
        $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`,`batch_no`,`ostock`,`rate`,`mfg_date`,`expiry_date`, `created`) VALUES (NULL, '$d1[product_id]','$d1[batch_no]','$d1[ostock]','$d1[rate]','$mfg_date', '$exp_date', NOW())";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Catalog Product table error');
        }
        $rId = mysqli_insert_id($dbc);
        mysqli_commit($dbc);
        //Logging the history		
        //history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    ######################################## catalog code edit start here ######################################################

    public function product_details_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_catalog__product_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Checking whether the original data was modified or not

        $mdate = !empty($d1['mfg_date']) ? get_mysql_date($d1['mfg_date']) : '';
        $exdate = !empty($d1['expiry_date']) ? get_mysql_date($d1['expiry_date']) : '';
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update 
        $q = "UPDATE catalog_product_details SET `batch_no` = '$d1[batch_no]', `ostock` = '$d1[ostock]', `rate` = '$d1[rate]', `mfg_date` = '$mdate', `expiry_date` = '$exdate', ostock = '$d1[ostock]'  WHERE id='$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Product Catalog Table error');
        }
        $rId = $id;
        mysqli_commit($dbc);

        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    ######################################## catalog list code  start here ######################################################

    public function get_product_details_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT *, DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date FROM catalog_product_details  $filterstr ";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array

        $produc_map = get_my_reference_array('catalog_product', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['product_name'] = $produc_map[$row[product_id]];
        }
        return $out;
    }

}

// class end here
?>

