<?php

class sale extends myfilter {
    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## Invoice Starts here ####################################################

         public function get_invoice_no($dlr_id){
            global $dbc;
            /*$qry="SELECT * from challan_order where ch_dealer_id='$dlr_id' order by challan_order.id desc limit 1";
            //echo $qry;die;
            $r=mysqli_query($dbc,$qry);

            $row= mysqli_fetch_object($r);
            $fetch_data=$row->ch_no;
            $exp_array=explode('/',$fetch_data);
            $exp_id = isset($exp_array[2])?$exp_array[2]:0;*/
            $exp_id=1;
            return $exp_id+1;
    }
    
    public function get_sale_list($filter = '', $filter_size = 0, $records = '', $orderby = '', $sale_data_str) {
        global $dbc;
        $out = array();
        $catalog_levels = $_SESSION[SESS . 'catlevel'];
        $filterbucket = '';
        if (!empty($sale_data_str))
            $filterbucket = " AND usd.user_id IN ($sale_data_str)";

        if ($filter_size < $catalog_levels) {
            
        } else {
            //$filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.order_id FROM `user_sales_order_details` usod "
                    . "INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN `user_sales_order` usd ON "
                    . " usd.order_id=usod.order_id "
                    . "INNER JOIN catalog_" . $filter_size . " ON cp.catalog_id=catalog_" . $filter_size . ".id "
                    . "WHERE catalog_" . $filter_size . ".id='" . $filter . "' $filterbucket";
        }
        //h1($q); exit();
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        //$brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
        //$category_map = get_my_reference_array('catalog_2', 'id', 'name'); 
        //$size_map = get_my_reference_array('catalog_product', 'id', 'name'); 
        //$working_status_map = get_my_reference_array('_working_status', 'id', 'name'); 
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['rate'] = $row['rate'];
            $out[$id]['quantity'] = $row['quantity'];
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_sale_dynamic_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.order_id FROM `user_sales_order_details` usod "
                . "INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN `user_sales_order` usd ON "
                . " usd.order_id=usod.order_id "
                . "$filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        //$brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
        //$category_map = get_my_reference_array('catalog_2', 'id', 'name'); 
        //$size_map = get_my_reference_array('catalog_product', 'id', 'name'); 
        //$working_status_map = get_my_reference_array('_working_status', 'id', 'name'); 
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['rate'] = $row['rate'];
            $out[$id]['quantity'] = $row['quantity'];
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_user_expanse_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,DATE_FORMAT(submit_date,'%e/%b/%Y') AS fdated FROM user_expense_report $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
        //$person_map = get_my_reference_array('person', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['travelling_mode'] = $travelling_map[$row['travelling_mode_id']];
            $out[$id]['pname'] = $this->get_username($row['person_id']);
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    //This function is used to fetch the price from hollow section
    public function get_sale_value($rateid, $qty, $rate) {
        global $dbc;
        $value = 0;
        switch ($rateid) {
            case 0: {
                    $value = $rate * $qty;
                    $value = $value == 0 ? '---' : $value;
                    break;
                }
        }
        return $value;
    }

    public function get_sale_order_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }
 public function get_last_level_catalog_id_list($count, $last_level_id, $catalog_level) {
        global $dbc;
        $out = array();

        $q = "SELECT catalog_product.id, catalog_product.name FROM catalog_$count";
        for ($i = $count; $i < $catalog_level; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN catalog_$j ON catalog_$j.catalog_" . $i . "_id = catalog_$i.id ";
        }
        $q .= " INNER JOIN catalog_product ON catalog_product.catalog_id = catalog_$catalog_level.id WHERE catalog_$count.id = '$last_level_id'";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[] = $row['id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }
    ///////////////////////////////////////////////////////
      public function get_sales_dynamic_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.order_id FROM `user_sales_order_details` usod "
                . "INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN `user_sales_order` usd ON "
                . " usd.order_id=usod.order_id INNER JOIN catalog_2 c2 ON cp.catalog_id = c2.id "
                . "$filterstr";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        //$brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
        //$category_map = get_my_reference_array('catalog_2', 'id', 'name'); 
        //$size_map = get_my_reference_array('catalog_product', 'id', 'name'); 
        //$working_status_map = get_my_reference_array('_working_status', 'id', 'name'); 
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['rate'] = $row['rate'];
            $out[$id]['quantity'] = $row['quantity'];
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }
    ///////////////////////////////////////
    public function get_sales_list($filter = '', $filter_size = 0, $records = '', $orderby = '', $sale_data_str) {
        global $dbc;
        $out = array();
        $catalog_levels = $_SESSION[SESS . 'catlevel'];
        $filterbucket = '';
        if (!empty($sale_data_str))
            $filterbucket = " AND usd.user_id IN ($sale_data_str)";

        if ($filter_size < $catalog_levels) {
            
        } else {
            //$filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.order_id FROM `user_sales_order_details` usod "
                    . "INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN `user_sales_order` usd ON "
                    . " usd.order_id=usod.order_id "
                    . "INNER JOIN catalog_" . $filter_size . " ON cp.catalog_id=catalog_" . $filter_size . ".id "
                    . "WHERE catalog_" . $filter_size . ".id='" . $filter . "' $filterbucket";
        }
        //h1($q); exit();
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        //$brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
        //$category_map = get_my_reference_array('catalog_2', 'id', 'name'); 
        //$size_map = get_my_reference_array('catalog_product', 'id', 'name'); 
        //$working_status_map = get_my_reference_array('_working_status', 'id', 'name'); 
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['rate'] = $row['rate'];
            $out[$id]['quantity'] = $row['quantity'];
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }
    public function sale_order_save() {

        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $upload_path = $this->get_document_type_list($filter = "id IN (2)", $records = '', $orderby = '');
        $sale_path = $upload_path[2]['documents_location'];
        $sale_path = MYUPLOADS . $sale_path;
        $browse_file = $_FILES['image_name']['name'];
        if (!empty($browse_file)) {
            list($uploadstat, $filename) = fileupload('image_name', $sale_path, $allowtype = array('image/jpeg', 'image/png', 'image/gif'), $maxsize = 52428800, $mandatory = true);
            if ($uploadstat) {
                resizeimage($filename, $sale_path, $newwidth = 400, $thumbnailwidth = 200, MSYM, $thumbnail = true);
            }
        } else
            $filename = '';

        $id = $orderno = $d1['uid'] . date('Ymdhis');
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `user_sales_order` 
                        (`id`,`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`image_name`,`remarks`, `company_id`) 
                    VALUES ('$id','$orderno', '$d1[uid]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', '$d1[call_status]',0,0,NOW(), NOW(),'$filename','$d1[remarks]', '{$_SESSION[SESS . 'data']['company_id']}')";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
        $rId = $id;
        $i = 0;
        $total_sale_value = array();
        if (!empty($d1['product'])) {
            foreach ($d1['product'] as $key => $value) {
                $prod = $d1['product'][$key];
                $rate = $d1['base_price'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total1 = $d1['prodvalue'][$key];
                $total_sale_value[] = $total1;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$uncode', '$orderno', '$prod', '$rate', '$qty', '$schqty')";
            }
            $str = implode(', ', $str);
            $total_sum = array_sum($total_sale_value);

            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                    . "VALUES $str";

            $r = mysqli_query($dbc, $q);

            if (!$r)
                return array('status' => false, 'myreason' => 'Sales could not be saved, some error occurred');
            if (!empty($d1['gift_id'])) {
                $str1 = array();
                foreach ($d1['gift_id'] as $key => $value) {
                    $gift = $d1['gift_id'][$key];
                    $uncode = $key + 1;
                    $gift_qty = $d1['gift_qty'][$key];
                    $str1[] = "('$uncode','$orderno','$gift','$gift_qty')";
                }
                $str1 = implode(', ', $str1);
                $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                        . "VALUES $str1";
                $r = mysqli_query($dbc, $q);
                if (!$r)
                    return array('status' => false, 'myreason' => 'Gift  Table error');
            }
            $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum' WHERE order_id = '$orderno' ";
            $r111 = mysqli_query($dbc, $q11);

            mysqli_commit($dbc);
        }
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

//$opt1 end here

    public function sale_order_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $this->next_order_num();
        $total_sale_value = $this->get_sale_value($d1['catalog_1_id'], $d1['metric_ton']);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE user_sales_order SET user_id='$d1[uid]',retailer_id = '$d1[retailer_id]',order_id='$id',call_status = '$d1[call_status]',total_sale_value = '$d1[total_sale_value]',date = NOW(),time = NOW(), company_id = '{$_SESSION[SESS . 'data']['company_id']}', remarks= '$d1[remarks]' WHERE id = '$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Could not be updated, some error occurred');
        }
        $rId = $id;

        $total_sale_value = array();
        if (!empty($d1['product'])) {
            foreach ($d1['product'] as $key => $value) {
                $prod = $d1['product'][$key];
                $rate = $d1['base_price'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total1 = $d1['prodvalue'][$key];
                $total_sale_value[] = $total1;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$uncode', '$id', '$prod', '$rate', '$qty', '$schqty')";
            }
            $str = implode(', ', $str);
            $total_sum = array_sum($total_sale_value);
            $q = "DELETE FROM user_sales_order_details WHERE order_id = '$id'";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Sales Could not be updated some error occurred.');
            }
            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                    . "VALUES $str";

            $r = mysqli_query($dbc, $q);

            if (!$r)
                return array('status' => false, 'myreason' => 'Sales could not be saved, some error occurred');
            if (!empty($d1['gift_id'])) {
                $str1 = array();
                foreach ($d1['gift_id'] as $key => $value) {
                    $gift = $d1['gift_id'][$key];
                    $uncode = $key + 1;
                    $gift_qty = $d1['gift_qty'][$key];
                    $str1[] = "('$uncode','$id','$gift','$gift_qty')";
                }
                $str1 = implode(', ', $str1);
                $q = "DELETE FROM user_retailer_gift_details WHERE order_id = '$id'";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => 'Sales Could not be updated some error occurred.');
                }
                $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                        . "VALUES $str1";
                $r = mysqli_query($dbc, $q);
                if (!$r)
                    return array('status' => false, 'myreason' => 'Sales order could not be updated some error occurred.');
            }
            $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum' WHERE order_id = '$orderno' ";
            $r111 = mysqli_query($dbc, $q11);

            mysqli_commit($dbc);
        }

        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    public function get_sale_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,usod.product_id,CONCAT_WS('##',usod.product_id,usod.order_id ) AS npid FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id');
            $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT * FROM user_retailer_gift_details INNER JOIN _retailer_mkt_gift ON _retailer_mkt_gift.id = user_retailer_gift_details.gift_id WHERE order_id = '$id'", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    //This function used to get user retailer gift deatils
    public function get_user_retailer_gift_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM user_retailer_gift_details  $filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $gift_map = get_my_reference_array('_retailer_mkt_gift', 'id', 'gift_name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['gift_name'] = $gift_map[$row['gift_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_sale_order_mobile_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT * FROM user_sales_order_details $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'firm_name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT user_sale.*,c1.name AS brand,c2.name AS catname,cp.name AS size FROM user_sales_order_details  user_sale INNER JOIN catalog_1 c1 ON c1.id = user_sale.catalog_1_id INNER JOIN catalog_2 c2 ON user_sale.catalog_2_id = c2.id INNER JOIN catalog_product cp ON user_sale.catalog_product_id = cp.id WHERE order_id = $id", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_user_wise_sale_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT user_id FROM user_sales_order ORDER BY id DESC";
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
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_sales_order ON user_sales_order.user_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_primary_sale_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT created_person_id FROM user_primary_sales_order ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['created_person_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_primary_sales_order ON user_primary_sales_order.created_person_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_expense_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT person_id FROM user_expense_report ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['person_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_expense_report ON user_expense_report.person_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_tracking_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT user_id FROM user_daily_tracking ORDER BY id DESC";
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
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_daily_tracking ON user_daily_tracking.user_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function next_order_num() {
        global $dbc;
        $out = array();
        $q = "SELECT MAX(id) AS total FROM user_sales_order";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        //return $rs['total']+1;	
    }

    public function get_username($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['uname'];
    }

    public function get_sale_order_temp_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function get_sale_order_temp_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM user_sale_order_details_temp $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'firm_name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['brand_name'] = $brand_map[$row['catalog_1_id']];
            $out[$id]['user_name'] = $this->get_username($row['user_id']);
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function sale_delete($id, $filter = '', $records = '', $orderby = '') {
        global $dbc;
        if (empty($filter))
            $filter = "itemId = $id";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_sale_list($filter = "order_id=$id", $records, $orderby);
        if (empty($deleteRecord)) {
            $out['myreason'] = 'user sales order not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");

        //Running the deletion queries
        $delquery = array();
        $delquery['user_sales_order'] = "DELETE FROM user_sales_order WHERE order_id = $id LIMIT 1";
        $delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE order_id = $id";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => '$key query failed');
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Sales Order successfully deleted');
    }

    public function sale_order_temp_delete() {
        global $dbc;

        $temp_id = $_POST['chk'];

        if (!empty($temp_id)) {
            $temp_id_array = implode(',', $temp_id);
            mysqli_query($dbc, "START TRANSACTION");
            $q = "DELETE FROM user_sale_order_details_temp WHERE id IN ($temp_id_array)";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Some error occurred in deletion please try again later');
            }
            return array('status' => TRUE, 'myreason' => 'Sale Order data deleted succesfully');
            mysqli_commit($dbc);
        } else {
            return array('status' => false, 'myreason' => 'Deletion failed please select any data for deletion');
        }
    }

    public function get_user_tracking_list1($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM user_daily_tracking $filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $pkeyid[] = $row['id'];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        $pkeyidarray = implode(',', $pkeyid);
        $q1 = "SELECT * FROM user_daily_tracking  $filterstr";

        list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'multi', $msg = '');
        while ($rows = mysqli_fetch_assoc($rs1)) {
            $id = $rows['user_id'];
            $out[$id] = $rows;
            $out[$id]['user_name'] = $this->get_username($rows['user_id']);
            $out[$id]['track_details'] = $this->get_my_reference_array_direct("SELECT * FROM user_daily_tracking WHERE id IN ($pkeyidarray)", 'id');
        }
        return $out;
    }

    public function get_user_tracking_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
        $q = "SELECT *,DATE_FORMAT(track_date,'%Y%m%d') AS tdate FROM user_daily_tracking $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($rows = mysqli_fetch_assoc($rs)) {
            $id = $rows['track_date'];
            $out[$id] = $rows;
            $out[$id]['user_name'] = $this->get_username($rows['user_id']);
            $out[$id]['track_details'] = $this->get_my_reference_array_direct("SELECT * FROM user_daily_tracking WHERE user_id = '$rows[user_id]' AND track_date = '$rows[track_date]' LIMIT $limit", 'id');
        }
        return $out;
    }

    public function get_user_tracking_distance_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
        $q = "SELECT *,DATE_FORMAT(track_date,'%Y%m%d') AS tdate FROM user_daily_tracking $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($rows = mysqli_fetch_assoc($rs)) {
            $id = $rows['track_date'] . $rows['user_id'];
            $out[$id] = $rows;
            $out[$id]['user_name'] = $this->get_username($rows['user_id']);
            $out[$id]['lat_long'] = $this->get_final_latlong_details($rows['user_id'], $rows['track_date']);
        }

        return $out;
    }

    //user_tracking_distance
    public function get_final_latlong_details($user_id, $track_date) {
        global $dbc;
        $out = array();
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
        $q = "SELECT * FROM user_daily_tracking WHERE user_id = '$user_id' AND track_date = '$track_date' LIMIT $limit";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($rows = mysqli_fetch_assoc($rs)) {
            $ltlg = explode(',', $rows['lat_lng']);
            if (($ltlg[0] != '0.0') && ($ltlg[1] != '0.0')) {
                $latlong = $rows['lat_lng'];
            } else {
                $mmc_mnc_lat_cellid = explode(':', $rows['mnc_mcc_lat_cellid']);
                $latlong = getlatlongbymccmnclaccid($mmc_mnc_lat_cellid[0], $mmc_mnc_lat_cellid[1], $mmc_mnc_lat_cellid[2], $mmc_mnc_lat_cellid[3]);
            }
            $out[] = $latlong;
        }
        return $out;
    }

    public function get_branch_staff_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,DATE_FORMAT(dob,'%d/%m/%Y') AS dob,DATE_FORMAT(anniversary,'%d/%m/%Y') AS anniversary FROM branch_staff_detail $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['person_name'] = $this->get_username($row['submit_person_id']);
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_all_user_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $loc_level = $_SESSION[SESS . 'constant']['location_level'];
        $q = "SELECT user_id FROM `location_$loc_level` l2 INNER JOIN dealer_location_rate_list dlrl ON dlrl.location_id = l2.id INNER JOIN user_dealer_retailer USING(dealer_id) $filterstr ";             //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['user_id'];
            $out[] = $id; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        $out = implode(',', $out);

        return $out;
    }

    //This function will help in the mulitpage print
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

    // This function is used to get last level catalog id
    public function get_last_level_catalog_id($count, $last_level_id, $catalog_level) {
        global $dbc;
        $out = array();

        $q = "SELECT catalog_product.id, catalog_product.name FROM catalog_$count";
        for ($i = $count; $i < $catalog_level; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN catalog_$j ON catalog_$j.catalog_" . $i . "_id = catalog_$i.id ";
        }
        $q .= " INNER JOIN catalog_product ON catalog_product.catalog_id = catalog_$catalog_level.id WHERE catalog_$count.id = '$last_level_id'";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[] = $row['id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }

    // Here we get last level location id
    public function get_last_level_location_id($count, $last_level_id, $location_level) {
        global $dbc;
        $out = array();
        $q = "SELECT user_id FROM location_$count";
        for ($i = $count; $i < $location_level; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.location_id = location_$location_level.id INNER JOIN user_dealer_retailer USING(dealer_id) WHERE location_$count.id = '$last_level_id'";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[$row['user_id']] = $row['user_id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }


//////////////////////////////START OF CHALAN DETAILS//////////////////////////////////////////////////
    
    
       public function get_chalan_details_report_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);        
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];        
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
        $q = "SELECT cod.id AS id,uso.date AS date,uso.user_id as user_id,uso.retailer_id as retailer_id,
                uso.order_id AS orderid, 
                uso.total_sale_value AS salevalue,
                (SELECT SUM(taxable_amt) FROM challan_order_details WHERE order_id=uso.order_id) AS tax
                FROM user_sales_order AS uso 
                INNER JOIN user_sales_order_details as usod ON uso.order_id = usod.order_id
                INNER JOIN challan_order_details AS cod ON uso.order_id=cod.order_id
                INNER JOIN challan_order AS co ON cod.ch_id=co.id
                $filterstr GROUP BY uso.order_id";

        
        // h1($q);
      
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
          while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['id'];
            $out[$id] = $row;
            $out[$id]['orderid'] = $row['orderid'];
            $out[$id]['salevalue'] = $row['salevalue'];
            $out[$id]['tax'] = $row['tax'];
            $out[$id]['pid']= myrowval('catalog_product', 'name',"id=".$row['pid']);
            $out[$id]['did'] = myrowval('dealer', 'name', "id=".$row['did']);
            $out[$id]['did'] = myrowval('dealer', 'name', "id=".$row['did']);
            $out[$id]['retailer'] = myrowval('retailer', 'name',"id=".$row['retailer_id']);
            $out[$id]['cid']=  myrowval('complaint','complaint',$row['cid']);
            $out[$id]['order_item']=  $this->sale_challan_item($out[$id]['orderid']);
        }
       // pre($out);
        return $out;
    }

    function get_retailer_ledger_list($filter = '', $records = '', $orderby = '')
        {
            global $dbc;
            $out = array();
            $filterstr = $this->oo_filter($filter, $records, $orderby);

            $q = "SELECT auto,DATE_FORMAT(ch_date,'%d-%m-%Y') as ch_date,remaining,ch_no FROM `challan_order` $filterstr ORDER BY `auto`";
            // h1($q);

            list($opt, $rs) = run_query($dbc, $q, 'multi');
            if (!$opt)
                return $out;

            while ($row = mysqli_fetch_assoc($rs))
            {
                $out[] = $row;
            }

            return $out;
        }
    
    public function sale_challan_item($order_id)
    {
       global $dbc;
       $out = array();

       /*$q = "SELECT usod.product_id as productid, usod.quantity as sale_qty, cod.qty as ch_qty
          FROM user_sales_order_details as usod
          INNER JOIN challan_order_details AS cod ON usod.order_id=cod.order_id
          where usod.order_id = '$order_id'"; */

        $q = "SELECT usod.product_id as productid, usod.quantity as sale_qty, cod.qty as ch_qty
          FROM user_sales_order_details as usod INNER JOIN challan_order_details AS cod ON usod.order_id=cod.order_id AND usod.product_id=cod.product_id
          where usod.order_id = '$order_id'";          

     // h1($q);
      $qm = mysqli_query($dbc,$q);
      while($row = mysqli_fetch_assoc($qm))
      {
         $product = $row['productid'];
         $cond="id=".$product;
         $out[$product]['product'] =  myrowval('catalog_product', 'name',$cond);
         $out[$product]['ch_qty'] = $row['ch_qty'];
         $out[$product]['sale_qty'] = $row['sale_qty'];
      }
      return $out;
    }

    //////////////////////////////////////// USER AGING  REPORT/////////////////////////////
    public function get_user_aging_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
      
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
            $date = date('Y-m-d', strtotime('-15 days'));
            $q = "SELECT DISTINCT(`user_id`) as id FROM `user_sales_order` as uso $filterstr AND date >='$date'";

     //h1($q);
      
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
          while ($row = mysqli_fetch_assoc($rs)) {
           
            $person[]=$row['id'];
       
        }
        $str1 = implode(', ', $person);
       // echo $str;
        $qrr = "SELECT person.id AS id,CONCAT(`first_name`,' ',middle_name,' ',last_name) AS name FROM `person` INNER JOIN user_dealer_retailer AS uso ON person.id=uso.user_id $filterstr AND id not in($str1)";  //h1($qrr);
        $qr = mysqli_query($dbc,$qrr);
        while($row_retail = mysqli_fetch_assoc($qr))
        {  
            $id = $row_retail['id'];
            $out[$id] = $row_retail;
            $out[$id]['id'] = $row_retail['id'];
            $out[$id]['name']=$row_retail['name'];
           
        }
       
       // pre($out);
        return $out;
    }
    ////////////////////////////////////////////////////////////////////
    ///////////////////////////////END OF CHALAN DETAILS///////////////////////////////////
////////////////////////////////////////AGING/////////////////////////////////////////////

 public function get_aging_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
      
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
            $date = date('Y-m-d', strtotime('-15 days'));
            $q = "SELECT DISTINCT(`retailer_id`) as id FROM `user_sales_order` as uso $filterstr AND date >='$date'";

   //  h1($q);
      
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
          while ($row = mysqli_fetch_assoc($rs)) {
           
            $retailer[]=$row['id'];
       
        }
        $str1 = implode(', ', $retailer);
       // echo $str;
        $qrr = "SELECT id,`name` FROM `retailer` as uso $filterstr AND id not in($str1)";  //h1($qrr);
        $qr = mysqli_query($dbc,$qrr);
        while($row_retail = mysqli_fetch_assoc($qr))
        {  
            $id = $row_retail['id'];
            $out[$id] = $row_retail;
            $out[$id]['id'] = $row_retail['id'];
            $out[$id]['name']=$row_retail['name'];
           
        }
       
       // pre($out);
        return $out;
    }
//////////////////////////////////////////////////////////////////////////////////////////////

}

?>
