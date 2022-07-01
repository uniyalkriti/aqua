<?php
$dealer = $_SESSION[SESS . 'data']['dealer_id'];
class damage_sale extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
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
    public function get_invoice_no($dlr_id){

     global $dbc;
      $qry="SELECT * from challan_order where ch_dealer_id='$dlr_id' order by challan_order.id desc limit 1";
      //echo $qry;die;
     $r=mysqli_query($dbc,$qry); 
     $row= mysqli_fetch_object($r); 
     $fetch_data=$row->ch_no; 
     $exp_array=explode('/',$fetch_data); 
     $exp_id = isset($exp_array[2])?$exp_array[2]:0;
      return $exp_id+1; 
    }

    public function get_dealer_sale_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Dealer Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function dealer_sale_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_sale_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
//                $upload_path = $this->get_document_type_list($filter="id IN (2)",  $records = '', $orderby='');
//                
//                $sale_path = $upload_path[2]['documents_location'];
//                $sale_path = MYUPLOADS.$sale_path;
//                $browse_file = $_FILES['image_name']['name'];
//                if(!empty( $browse_file))
//                {
//                    list($uploadstat, $filename) = fileupload('image_name', $sale_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
//                    if($uploadstat) 
//                    {
//                            resizeimage($filename, $sale_path, $newwidth=400, $thumbnailwidth=200, MSYM, $thumbnail = true);          
//                    }
//                }
//                else $filename = '';  

        $id = $orderno = $d1['uid'] . date('YmdHis');

        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `user_sales_order`  (`id`,`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `company_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`image_name`,`remarks`)  VALUES ('$id', '$orderno', '$d1[dspId]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', '$d1[company_id]','1',0,0,NOW(), NOW(),'$filename','$d1[remarks]')";
//h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
        if ($d1['company_id'] == 1){
            write_query($q);
        }
        $rId = $id;
        $i=1;
        $total_sale_qty = array();
        if (!empty($d1['product_id'])) {
            foreach ($d1['product_id'] as $key => $value) {
                $prod = $d1['product_id'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total_sale_qty[] = $qty;
                $uncode = $orderno.$i;
                $i++;
                $str[] = "('$uncode','$orderno','$prod','$qty','$schqty')";
            }
            $str = implode(', ', $str);
            $total_sum_qty = array_sum($total_sale_qty);
            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`quantity`,`scheme_qty`)  VALUES $str";
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => FALSE, 'myreason' => 'Sale Order could not be saved');
            if ($d1['company_id'] == 1){
           write_query($q);
            }
        }
        if (!empty($d1['gift_id'])) {
            $str1 = array();
            foreach ($d1['gift_id'] as $key => $value) {
                $gift = $d1['gift_id'][$key];
                $gift_qty = $d1['gift_qty'][$key];
                $uncode = $key + 1;
                $str1[] = "('$uncode','$orderno','$gift','$gift_qty')";
            }
            $str1 = implode(', ', $str1);
            $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) VALUES $str1";
        //  h1($q);
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Gift  Table error');
             if ($d1['company_id'] == 1){
            write_query($q);
             }
        }
        $q = "UPDATE user_sales_order SET  total_sale_qty = '$total_sum_qty' WHERE order_id = '$orderno' ";
        $r = mysqli_query($dbc, $q);
         if ($d1['company_id'] == 1){
         write_query($q);         
         }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => TRUE, 'myreason' => $d1['what'] . ' successfully Saved1', 'rId' => $rId);
    }

    #################### This function is used to edit sale  order details       

    public function dealer_sale_edit($id) {

        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_dealer_sale_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        //Start the transaction

        mysqli_query($dbc, "START TRANSACTION");
        // query to update
      echo  $q = "UPDATE user_sales_order SET user_id = '$d1[dspId]',retailer_id = '$d1[retailer_id]',call_status = '1',date = NOW(),time = NOW(), remarks = '$d1[remarks]', company_id = '$d1[company_id]', location_id = '$d1[location_id]' WHERE order_id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Retailer Table error');
        }
         if ($d1['company_id'] == 1)
            write_query($q);
        $rId = $id;
        $i = 0;
        $total_sale_value = array();
        $toal_sale_qty = '';
        $q = "DELETE FROM user_sales_order_details WHERE order_id = '$id'";
        $r = mysqli_query($dbc, $q); 
        if($r){
             if ($d1['company_id'] == 1)
            write_query($q);
        }
        if (!empty($d1['product'])) {
            foreach ($d1['product'] as $key => $value) {
                $prod = $d1['product'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $toal_sale_qty[] = $qty;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$uncode','$id','$prod','$qty','$schqty')";
            }
            $str = implode(', ', $str);
            $total_qty_sum = array_sum($toal_sale_qty);
            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`, `quantity`,`scheme_qty`) VALUES $str";
            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Sale Order could not be saved');
             if ($d1['company_id'] == 1)
            write_query($q);
        }
        $q = "DELETE FROM user_retailer_gift_details WHERE order_id = '$id'";
        $del_res = mysqli_query($dbc, $q);
        if($del_res){
             if ($d1['company_id'] == 1)
            write_query($q);
        }
        if (!empty($d1['gift_id'])) {
            $str1 = array();
            foreach ($d1['gift_id'] as $key => $value) {
                $gift = $d1['gift_id'][$key];
                $gift_qty = $d1['gift_qty'][$key];
                $uncode = $key + 1;
                $str1[] = "('$uncode','$id','$gift','$gift_qty')";
            }
            $str1 = implode(', ', $str1);
            $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) VALUES $str1";

            $r = mysqli_query($dbc, $q);
            if (!$r)
                return array('status' => false, 'myreason' => 'Gift  Table error');
             if ($d1['company_id'] == 1)
            write_query($q);
        }
        $q = "UPDATE user_sales_order SET total_sale_qty = '$total_qty_sum' WHERE order_id = '$id'";
        $r = mysqli_query($dbc, $q);
        if($r){
             if ($d1['company_id'] == 1)
            write_query($q);
        }
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    public function get_dealer_sale_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) $filterstr AND user_sales_order_details.status !=2 ";
      //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dspId'] = $row['user_id'];
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name, cp.taxable,usod.rate,usod.quantity,usod.scheme_qty,usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id WHERE usod.order_id = $row[order_id] AND  status!=2 ", 'id');

            $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT gift_id,quantity FROM user_retailer_gift_details WHERE order_id = $row[order_id]", 'gift_id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
       //pre($out);
        return $out;
    }

    //retailer_add_location

    public function calculate_order_item($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $qty = 0;
        $sch_qty = 0;
        $out = array('qty' => 0, 'scheme_qty' => 0);
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT (SELECT quantity FROM user_sales_order_details $filterstr) AS quantity,(SELECT scheme_qty FROM user_sales_order_details $filterstr) AS scheme_qty,(SELECT SUM(free_qty)  FROM challan_order_details $filterstr) AS fqty,(SELECT SUM(qty)  FROM challan_order_details $filterstr) AS qty FROM user_sales_order_details LIMIT 1";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        $qty = $rs['quantity'] - $rs['ch_qty'];
        $sch_qty = $rs['scheme_qty'] - $rs['fqty'];
        return array('qty' => $qty, 'scheme_qty' => $sch_qty);
    }
    
     public function item_details($filter = '', $records = '', $orderby = '') {
        global $dbc;       
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT rate,tax,base_price from catalog_product_rate_list cprl INNER JOIN state ON state.stateid = cprl.stateId INNER JOIN person ON person.state_id = state.stateid INNER JOIN user_dealer_retailer udr ON udr.user_id = person.id $filterstr LIMIT 1 ";
     // h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;        
        return array('rate' => $rs['rate'], 'tax' => $rs['tax'],'base_price'=>$rs['base_price']);
    }

    public function get_sale_order_details_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,id FROM user_sales_order_details $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function dealer_sale_delete($id, $filter = '', $records = '', $orderby = '') {
        global $dbc;

        if (empty($filter)) {
            $id = explode('<$>', $id);
            $order_id = $id[1];
            $product_id = $id[2];
            $filter = "order_id = '$order_id' AND product_id = '$product_id'";
        }

        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_sale_order_details_list($filter, $records, $orderby);
        if (empty($deleteRecord)) {
            $out['myreason'] = 'Product not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");

        //Running the deletion queries
        $delquery = array();
        $delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE $filter";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Product deleted Succesfully');
    }

    //This function used to get user retailer gift deatils
    public function get_dealer_sale_gift_list($filter = '', $records = '', $orderby = '') {
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

    public function get_dsp_wise_user_data($dealer_id) {
        global $dbc;
        $out = array();
        $q = "SELECT user_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id WHERE dealer_id = '$dealer_id' ";

        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if ($opt) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['user_id']] = $row['user_id'];
            }
        }
        return $out;
    }

    public function get_retailer_challan_no($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT id, ch_no FROM challan_order $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
        }
        return $out;
    }

    public function get_challan_checkbox_list($filter = '', $records = '', $orderBy = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT challan_no FROM challan_order_wise_payment_details $filterstr";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['challan_no'];
            $out[$id] = $id;
        }
        return $out;
    }

    public function get_total_challan_value($filter = '', $records = '', $orderBy = '') {
        global $dbc;
        $out = NULL;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT SUM(product_rate * ch_qty) AS rvalue FROM challan_order_details $filterstr";
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['rvalue'];
    }

    //This function is used to find out in which firm  person belongs
    public function get_dealer_user_sale_data($id, $role_id) {
        global $dbc;
        $out = array();
        // here $id is dealer manager id
        $dealer_id = $this->get_dealer_id($id, $role_id);
        $q = "SELECT user_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN _role USING(role_id) WHERE dealer_id = '$dealer_id' AND role_group_id = '11'";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if ($opt) {
            while ($rows = mysqli_fetch_assoc($rs)) {
                $out[$rows['user_id']] = $rows['user_id'];
            }
        }
        return $out;
    }

    public function get_dealer_location_list($id) {
        global $dbc;
        $out = array();
        //$filterstr=$this->oo_filter($filter, $records, $orderby);
        $q = "SELECT location_" . $_SESSION[SESS . 'constant']['retailer_level'] . ".id,location_" . $_SESSION[SESS . 'constant']['retailer_level'] . ".name FROM dealer_location_rate_list "
                . "INNER JOIN location_" . $_SESSION[SESS . 'constant']['dealer_level'] . " ON location_" . $_SESSION[SESS . 'constant']['dealer_level'] . ".id=dealer_location_rate_list.location_id";
        for ($i = $_SESSION[SESS . 'constant']['dealer_level']; $i < $_SESSION[SESS . 'constant']['retailer_level']; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " WHERE dealer_location_rate_list.dealer_id=" . $id . "  ";

        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[$row['id']] = $row['name'];
        }
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

    public function get_dealer_id($id, $role_id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT dealer_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id WHERE user_id = '$id' AND role_id = '$role_id' LIMIT 1";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        else
            return $rs['dealer_id'];
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

    ############# DSP WISE CHALLAN WORKING START HERE ############################

    public function get_challan_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['dealer_id'] = $_SESSION[SESS . 'data']['dealer_id'];
        $d1['sesId'] = $_SESSION[SESS . 'csess'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Challan'; //whether to do history log or not
        return array(true, $d1);
    }

    public function challan_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);

        $chnum = 'ch_' . $d1['dealer_id'] . '_' . $this->next_challan_num();
        $id = $d1['dealer_id'] . date('Ymdhis');
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `damage_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `sesId`, `remark`,`complaint_id`) VALUES ('$id', '$chnum','$d1[uid]', '$d1[dealer_id]', '$d1[ch_retailer_id]', '$ch_date', '$d1[company_id]', '$d1[sesId]', '$d1[remark]',''$d1[complaint_id]'');"; 
        //h1($q);exit;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }else{
            if($d1['company_id']==1)
                write_query($q);
        }
        $rId = $id;
        $extrawork = $this->challan_extra('save', $rId, $d1,$d1['company_id']);
        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

    ##################### This function is used to save data in challan order details ############

    public function challan_extra($actiontype, $rId, $d1,$company_id) {
        global $dbc;
        $order_status = $qty_sum = array();
        $item = $d1['product_id'];
        //during update we are required to remove the previous entry
        if ($actiontype == 'update'){
            mysqli_query($dbc, "DELETE FROM damage_order_details WHERE ch_id = '$rId'");
            if($company_id==1){
                write_query("DELETE FROM damage_order_details WHERE ch_id = '$rId'");
            }
        }
        $str = '';
        foreach ($item as $key => $value) {
            $value = explode('#', $value);
            $itemId = $value[0];
            $order_id = $value[1];
            $order_status[$value[1]] = $value[1];
            $batch = $d1['batch'][$key];
            $taxId = $d1['taxId'][$key];
            $ch_qty = $d1['qty'][$key];
            $rate = $d1['rate'][$key];
            $user_id = $d1['user_id'][$key];
            $str[] = '(NULL, \'' . $rId . '\', \'' . $itemId . '\', \'' . $batch . '\', \'' . $taxId . '\', \'' . $ch_qty . '\', \'' . $rate . '\', \'' . $order_id . '\', \'' . $user_id . '\')';
            
            //$this->calculate_stock($value, $catalog_details_id);
            //$this->update_order_status($value);
            //return array ('status'=>true, 'myreason'=>''); 
        } //foreach($item as $key=>$value) end here
        $str = implode(',', $str);
        $q = "INSERT INTO damage_order_details (`id`,`ch_id`,`product_id`, `batch_no`,  `taxId`, `ch_qty`, `product_rate`, `order_id`, `user_id`) VALUES $str";
        h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r){
        return array('status' => false, 'myreason' => 'Challan Order Details Could not be saved Some error occurred.');}else{                       if($company_id==1){
           write_query($q);
        }
        }
        $order_status = implode(',', $order_status);
        $q = "UPDATE user_sales_order SET order_status = '1' WHERE order_id IN ($order_status)";
        $r = mysqli_query($dbc, $q);
          if($company_id==1){
           write_query($q);
          }
        return array('status' => true, 'myreason' => 'Challan Order Succesfully Saved.');
    }
 public function challan_extra_edit($actiontype, $rId, $d1,$company_id) {
        global $dbc;
        $order_status = $qty_sum = array();
        $item = $d1['product_id'];
        //during update we are required to remove the previous entry
        if ($actiontype == 'update'){
            mysqli_query($dbc, "DELETE FROM challan_order_details WHERE ch_id = '$rId'");
            if($company_id==1){
                write_query("DELETE FROM challan_order_details WHERE ch_id = '$rId'");
            }
        }
        $str = '';
        foreach ($item as $key => $value) {
            $value = explode('#', $value);
            $itemId = $value[0];
            $order_id = $value[1];
            $order_status[$value[1]] = $value[1];
            $batch = $d1['batch'][$key];
            $taxId = $d1['taxId'][$key];
            $ch_qty = $d1['qty'][$key];
            $rate = $d1['rate'][$key];
            $user_id = $d1['user_id'][$key];
            $str[] = '(NULL, \'' . $rId . '\', \'' . $itemId . '\', \'' . $batch . '\', \'' . $taxId . '\', \'' . $ch_qty . '\', \'' . $rate . '\', \'' . $order_id . '\', \'' . $user_id . '\')';
            
            //$this->calculate_stock($value, $catalog_details_id);
            //$this->update_order_status($value);
            //return array ('status'=>true, 'myreason'=>''); 
        } //foreach($item as $key=>$value) end here
        $str = implode(',', $str);
        $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`, `batch_no`,  `taxId`, `ch_qty`, `product_rate`, `order_id`, `user_id`) VALUES $str";
       //h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r){
        return array('status' => false, 'myreason' => 'Challan Order Details Could not be saved Some error occurred.');}else{                       if($company_id==1){
           write_query($q);
        }
        }
        $order_status = implode(',', $order_status);
        $q = "UPDATE user_sales_order SET order_status = '1' WHERE order_id IN ($order_status)";
        $r = mysqli_query($dbc, $q);
          if($company_id==1){
           write_query($q);
          }
        return array('status' => true, 'myreason' => 'Challan Order Succesfully Saved.');
    }
    public function challan_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //Manipulation and value reading
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");

        $q = "UPDATE challan_order SET ch_retailer_id = '$d1[ch_retailer_id]',company_id = '{$_SESSION[SESS . 'data']['company_id']}', remark = '$d1[remark]',ch_date = '$ch_date' WHERE id = '$id'";
     //   h1($q1);
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
        $rId = $id;
        $extrawork = $this->challan_extra_edit('update', $rId, $d1);
        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated', 'rId' => $rId);
    }

    public function calculate_available_stock($id) {
        global $dbc;
        $sesId = $_SESSION[SESS . 'csess'];
        //h1($sesId);
        $stock = 0;
         $q1 = "SELECT SUM(qty+free_qty) AS issue FROM challan_order INNER JOIN `challan_order_details` ON challan_order.id = challan_order_details.ch_id WHERE challan_order_details.product_id = '$id' AND ch_dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ";
        //echo 'TRUE<$>'.$q1;
        $r1 = mysqli_query($dbc, $q1);
        $d1 = mysqli_fetch_assoc($r1);
        if ($d1['issue'] == '') {
            $d1['issue'] = 0;
        }
       $q2 = "SELECT SUM(quantity)as quantity FROM user_primary_sales_order_details  usod INNER JOIN user_primary_sales_order uso ON uso.order_id = usod.order_id WHERE usod.product_id = '$id' AND uso.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."'  ";
        //echo $q2;
       $r2 = mysqli_query($dbc, $q2);
        $d2 = mysqli_fetch_assoc($r2);
        if ($d2['quantity'] == '') {
            $d2['quantity'] = 0;
        }
        //here we get stock
        $stock = $d2['quantity'] - $d1['issue'];
        return $stock;
    }

    public function challan_delete($id, $filter = '', $records = '', $orderby = '') {
        global $dbc;
        if (empty($filter))
            $filter = "ch_no = $id";
        $out = array('status' => false, 'myreason' => '');
        $deleteRecord = $this->get_dsp_challan_list($filter, $records, $orderby);
        if (empty($deleteRecord)) {
            $out['myreason'] = 'Challan not found';
            return $out;
        }
        //start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        //Checking whether the invoice is deletable or not
        $q = "SELECT batch_no, ch_qty FROM challan_order_details WHERE challan_no = '$id'";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if ($opt) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $q = "UPDATE catalog_product_details SET ostock = ostock + '$row[ch_qty]' WHERE id = '$row[batch_no]'";
                $r = mysqli_query($dbc, $q);
            }
        }
        $q = "SELECT `order_id` FROM `challan_order_details` WHERE challan_no = '$id'";
        $r = mysqli_query($dbc, $q);

        if ($r && mysqli_num_rows($r) > 0) {
            while ($row = mysqli_fetch_assoc($r)) {
                $q2 = "UPDATE user_sales_order SET `order_status` = '2' WHERE order_id = '$row[order_id]'";
                $r2 = mysqli_query($dbc, $q2);
            }
        }

        //Running the deletion queries
        $delquery = array();
        $delquery['challan_order'] = "DELETE FROM challan_order WHERE ch_no = $id LIMIT 1";
        $delquery['challan_order_details'] = "DELETE FROM challan_order_details WHERE challan_no = $id";
        foreach ($delquery as $key => $value) {
            if (!mysqli_query($dbc, $value)) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => "$key query failed");
            }
        }
        //After successfull deletion

        mysqli_commit($dbc);
        return array('status' => true, 'myreason' => 'Challan successfully deleted');
    }

    public function get_challan_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $qrr = "SELECT *,damage_order.id as doid, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, "
                . " DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM damage_order "
                . " INNER JOIN dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id $filterstr";
        //h1($qrr);
        list($opt, $rs) = run_query($dbc, $qrr, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
      // $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        $complaint_map = get_my_reference_array('complaint_type', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
                $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id = $row['doid'];
          //   $out[$id]['ch_id'] = $row['ch_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['complaint_name'] = $complaint_map[$row['complaint_id']];
            $out[$id]['retailer_name'] = $retailer_map;

            $q = "SELECT dod.*,dod.product_id AS pid, p.name FROM "
                    . " damage_order_details dod INNER JOIN catalog_product p ON p.id = dod.product_id "
                    . " INNER JOIN catalog_2 c2 ON c2.id = p.catalog_id "
                    . " INNER JOIN product_rate_list r ON dod.product_id = r.product_id "
                    . " AND r.state_id ='$state_id' WHERE dod.ch_id = '$id' GROUP BY dod.product_id";
            //h1($q);
            //die;
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
            
        }// while($row = mysqli_fetch_assoc($rs)){ ends
    //  pre($out);exit;
        return $out;
    }

    
    public function get_tax_inv_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order "
                . " $filterstr";
    // h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;


        $dealer_map = get_my_reference_array('dealer', 'id', 'name');

        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
      
        //$retailer_map1 = get_my_reference_array('retailer', 'id','tin_no');
         //echo 'hiiiiiiiiiiiiii';die('byyyyyy');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
           // $out[$id]['tin_no'] = $retailer_map1[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,sum(qty*product_rate) as challan_val,sum(cd_amt) as total_cd_amt,challan_order_details.product_id AS pid, catalog_product.name FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  WHERE ch_id =$id AND tax=0 group by ch_id";
           //h1($q);
            $q1 = "SELECT challan_order_details.*,sum(qty*product_rate) as challan_val,sum(cd_amt) as total_cd_amt,challan_order_details.product_id AS pid, catalog_product.name FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  WHERE ch_id =$id AND tax=5 group by ch_id";
           //h1($q1);
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
            $out[$id]['challan_item1'] = $this->get_my_reference_array_direct($q1, 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
         //pre($out);
        return $out;
    }
//    public function get_tax_register_list($filter = '', $records = '', $orderby = '') {
//        global $dbc;
//        $out = array();
//        $filterstr = $this->oo_filter($filter, $records, $orderby);
//        $q = "SELECT *, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order "
//                . " $filterstr";
//      //  h1($q);
//        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
//        if (!$opt)
//            return $out;
//        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
//        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
//        $retailer_map1 = get_my_reference_array('retailer', 'id','tin_no');
//        
//        while ($row = mysqli_fetch_assoc($rs)) {
//            $id = $row['id'];
//            $out[$id] = $row; // storing the item id
//            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
//            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
//            $out[$id]['tin_no'] = $retailer_map1[$row['ch_retailer_id']];
//            $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  WHERE ch_id = $id AND tax=0 ";
//           // h1($q);
//            $q1 = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  WHERE ch_id = $id AND tax=5 ";
//          //  h1($q1);
//            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
//            $out[$id]['challan_item1'] = $this->get_my_reference_array_direct($q1, 'id');
//        }// while($row = mysqli_fetch_assoc($rs)){ ends
//        return $out;
//    }
    
     public function get_tax_register_list($filter='',  $records = '', $orderby='')
    {
        global $dbc;
        $out = array(); 
                $filterstr = $this->oo_filter($filter, $records, $orderby);
               // pre($filter);exit;
        $q = "SELECT challan_order.ch_dealer_id as dealer_id, ch_date,challan_order.id as id, challan_order_details.ch_id as order_id FROM challan_order INNER JOIN challan_order_details ON challan_order.id = challan_order_details.ch_id $filterstr";
               // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        while($row = mysqli_fetch_assoc($rs))
        {
            $id = $row['id'];
            $out[$id] = $row; // storing the person id
                        $out[$id]['dealer_name'] = $this->get_dealer_name($row['dealer_id']);
                        $out[$id]['zero_vat'] = '0.00';
                        $out[$id]['zero_sale'] = $this->get_zero_sale($row['dealer_id'],$filter);
                        $out[$id]['five_sale'] = $this->get_five_sale($row['dealer_id'],$filter);
                        $out[$id]['primary_zero_vat'] = '0.00';
                        $out[$id]['primary_zero_sale'] = $this->get_primary_zero_sale($row['dealer_id'],$filter[0],$filter[1]);
                        $out[$id]['primary_five_sale'] = $this->get_primary_five_sale($row['dealer_id'],$filter[0],$filter[1]);
        }
               // pre($out);
        return $out;                
    }
        public function get_dealer_name($dealer_id) {
        global $dbc;
        $out = array();
        $q = "SELECT name FROM dealer WHERE id = $dealer_id";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['name'];
        }
        public function get_zero_sale($dealer_id,$filter='',$records='', $orderby='') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT SUM( qty * mrp ) AS total_zero_sale, ch_dealer_id ,ch_date FROM challan_order INNER JOIN challan_order_details ON challan_order_details.ch_id = challan_order.id  $filterstr AND tax=0 ";
     //   h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['total_zero_sale'];
        }
        public function get_five_sale($dealer_id,$filter='',$records='', $orderby='') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT SUM( qty * mrp ) AS total_five_sale, ch_dealer_id ,ch_date FROM challan_order INNER JOIN challan_order_details ON challan_order_details.ch_id = challan_order.id  $filterstr AND tax=5 ";
      //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['total_five_sale'];
        }
//        public function get_primary_five_sale($dealer_id,$filter='',$records='', $orderby='') {
//        global $dbc;
//        $out = array();
//        $filterstr = $this->oo_filter($filter, $records, $orderby);
//        $q = "SELECT SUM( qty * mrp ) AS total_five_sale, ch_dealer_id ,ch_date FROM challan_order INNER JOIN challan_order_details ON challan_order_details.ch_id = challan_order.id  $filterstr AND tax=5 ";
//     //   h1($q);
//        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
//        return $rs['total_five_sale'];
//        }
        
        public function get_primary_zero_sale($dealer_id,$stdate,$enddate,$filter='',$records='', $orderby='') {
        global $dbc;
        $out = array();
        
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT SUM( quantity * user_primary_sales_order_details.rate ) AS primary_zero_sale FROM user_primary_sales_order_details 
            INNER JOIN user_primary_sales_order ON user_primary_sales_order.order_id = user_primary_sales_order_details.order_id
            INNER JOIN catalog_product_rate_list ON catalog_product_rate_list.catalog_product_id = user_primary_sales_order_details.product_id Where $stdate AND $enddate AND dealer_id=$dealer_id AND tax =0";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['primary_zero_sale'];
        }
         public function get_primary_five_sale($dealer_id,$stdate,$enddate,$filter='',$records='', $orderby='') {
        global $dbc;
        $out = array();
        
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT SUM( quantity * user_primary_sales_order_details.rate ) AS primary_five_sale FROM user_primary_sales_order_details 
            INNER JOIN user_primary_sales_order ON user_primary_sales_order.order_id = user_primary_sales_order_details.order_id
            INNER JOIN catalog_product_rate_list ON catalog_product_rate_list.catalog_product_id = user_primary_sales_order_details.product_id Where $stdate AND $enddate AND dealer_id=$dealer_id AND tax =5";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['primary_five_sale'];
        }
    ######################################## Invoice ends here ####################################################

    public function print_looper_challan($multiId, $options = array()) {

        global $dbc;
        $out = array();
        if (is_null($multiId) || empty($multiId))
            return $out;

        //Create the object when needed
        $party = new retailer();
        //Explode to get an array of all the Id
        $multiId = explode('-', $multiId);
        foreach ($multiId as $key => $value) {
            $id = trim($value);
            if (empty($id))
                continue;

            //read the record statistics
            $rcdstat = $this->get_challan_list("id = $id");
            if (empty($rcdstat))
                continue;
            $temp = $rcdstat[$id];

            $out[$id] = $rcdstat[$id];
            $out[$id]['adr'] = $party->get_retailer_adr($temp['ch_retailer_id']);
        }
       // pre($out);
        return $out;
    }

    public function next_challan_num() {
        global $dbc;
        $out = array();
        $q = "SELECT COUNT(ch_no) AS total FROM challan_order";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        return $rs['total'] + 1;
    }

    ############# DSP WISE CHALLAN WORKING END HERE ############################

     public function direct_challan_save()
     {
        global $dbc;
        
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $orderno = date('YmdHis');
        
        //Manipulation and value reading
        //$chnum = $this->next_challan_num();
       // $dispatch = empty($d1['dispatch_date']) ? '' : get_mysql_date($d1['dispatch_date'], '/', false, false);
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        //Start the transaction
       // $_SESSION['chalan_id'] = $chnum;
        $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
          $idd = $d1['dealer_id'].$orderno;
          $id = $d1['dealer_id'].$orderno;
          $chlaan_no = $d1['ch_no_prifix'].$d1['ch_no'];
        mysqli_query($dbc, "START TRANSACTION");

        //$nid= $id;
        if (!empty($d1['product_id'])) {
            $str = array();
            $str1 = array();
            $batch_id = array();
            $total_sale_value = 0;
            $total_qty = 0;
            $act_amt1 = 0;
              $sns = $d1['saleable_non_saleable'];
            foreach ($d1['product_id'] as $key => $value)
            {
                 if(!empty($value)){
                $prod = $d1['product_id'][$key];
                $rate = $d1['rate'][$key];
                $taxId = $d1['vat'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $cd = $d1['cd'][$key];
                $mrp = $d1['mrp'][$key];
                $cd_type = $d1['cd_type'][$key];
                $batch_no = $d1['batch_no'][$key];
                $cd_amt = $d1['cd_amt'][$key];
                $dis_type = $d1['trade_disc_type'][$key];
                $dis_amt = $d1['trade_disc_amt'][$key];
                $dis_percent = $d1['trade_disc_val'][$key];
                $taxable_amt = $d1['taxable_amt'][$key];
                $replace_product_id = $d1['replace_product_id'][$key];
                $replace_mrp = $d1['replace_mrp'][$key];
                $replace_rate = $d1['replace_rate'][$key];
                $replace_quantity = $d1['replace_quantity'][$key];
                $replace_amount = $d1['replace_amount'][$key];
                $dealer_id = $d1['dealer_id'];
                $qtyy = $qty;
                if($sns==1)
                {
            
        $sq = "SELECT `id`,`remaining`,`qty`,`product_id` FROM `stock` where `product_id`='$prod' AND `mrp`='$mrp' order by `id` asc";

        $rs1=mysqli_query($dbc,$sq);
                while($row = mysqli_fetch_assoc($rs1))
                {
                     $remaining_qty = $row['remaining'];
                     $quant = $row['qty'];
                     $product_id = $row['product_id'];
                     $id = $row['id'];  
                     $m = $quant-$remaining_qty;                     
                    if($qtyy > $m)
                        {  
                       // echo"ANKUSH1".$quant."aaa".$qtyy; exit;
                        $v = $quant-$remaining_qty;
                        $remaining_qty = $remaining_qty+$v;
                        $qtyy = $qtyy-$v;
                  ////  $qtyy =  $qtyy - $remaining_qty;
                        //$balqty = 0;
                        $q = "UPDATE stock SET remaining = '$remaining_qty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);
                        }else{
                         //      echo"ANKUSH"; exit;
                         $remaining_qty = $remaining_qty+$qtyy;
                       // $qtyy = $qtyy-$v;
                       // $baltemp = $qty - $remaining_qty;
                        $q = "UPDATE stock SET remaining = '$remaining_qty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);   
                       }
                    
                }

                /*<puneet>*/
                
                  $update_stock_q = "UPDATE `stock` SET qty=(qty+$qty) WHERE `product_id`=$prod AND `dealer_id`=$dealer_id AND `mrp`=$mrp";
                
                  mysqli_query($dbc, $update_stock_q);

                /*</puneet>*/
                }


                /*<puneet>*/  
                if($sns==2)
                {
                    $update_stock_q = "UPDATE `stock` SET nonsalable_damage=(nonsalable_damage+$qty) WHERE `product_id`=$prod AND `dealer_id`=$dealer_id AND `mrp`=$mrp";
                    mysqli_query($dbc, $update_stock_q);
                }
                /*</puneet>*/  
          
                $act_amt = $d1['actual_amount'][$key];
                $act_amt1 = $act_amt+$act_amt1;
               //h1($taxable_amt);
                //$total_sale_value = $total_sale_value + $d1['base_price'][$key] * $d1['quantity'][$key];
               // $total_qty = $total_qty + $d1['quantity'][$key];
                //To save the value of the other columns as some columns are affected by po
                $str[] = "(NULL,'$idd','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$taxable_amt','$replace_product_id','$replace_mrp','$replace_rate','$replace_quantity','$replace_amount','$act_amt')";
                //$str1[] = "(,'$orderno','$prod','$rate', '$qty','$schqty')";
                $nid++;
              }
            }
           

             $q = "INSERT INTO `damage_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`,`dispatch_date`, `ch_date`,`actual_amount`, `company_id`,`complaint_id`,`saleable_non_saleable`) 
    VALUES ($idd, '$chlaan_no','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]', NOW() , '$ch_date','$act_amt1', '$d1[company_id]','$d1[complaint_id]','$d1[saleable_non_saleable]')";
            $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
        if($d1['company_id']==1){
            write_query($q);
        }
        $rId = mysqli_insert_id($dbc);
             $str = implode(',', $str);
             $q = "INSERT INTO damage_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`,`replace_product_id`,`replace_mrp`,`replace_rate`,`replace_quantity`,`replace_amount`,`actual_amount`)
            VALUES $str";
           //h1($q);die;
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            if($d1['company_id']==1){
            write_query($q);
        }
        }
            if(!empty($d1['product_id_ex']) && $d1['product_id_ex'] !=''){
            foreach ($d1['product_id_ex'] as $key => $value) {
                if(!empty($value)){
                $prod = $d1['product_id_ex'][$key];
                $rate = $d1['rate_ex'][$key];
                $taxId = $d1['vat_ex'][$key];
                $qty = $d1['quantity_ex'][$key];
                $schqty = $d1['scheme_ex'][$key];
                $cd = $d1['cd_ex'][$key];
                $mrp = $d1['mrp_ex'][$key];
                $cd_type = $d1['cd_type_ex'][$key];
                 $batch_no = $d1['batch_no'][$key];
                $str_ex[] = "($nid,'$id','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$batch_no')";
                $nid++;
            }
            }
            $str_ex = implode(',', $str_ex);
            if(!empty($str_ex)){
           $q = "INSERT INTO damage_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`batch_no`) VALUES $str_ex";
         //  h1($q); 
           $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            if($d1['company_id']==1){
           write_query($q);
        }
            }
            }
          
           
        
           
           // $this->calculate_stock($d1[uid], $batch_id);
            //$str1 = implode(',', $str1);
          // echo $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
             //       . "VALUES $str1"; 
//            $r = mysqli_query($dbc, $q);
//            if (!$r)
//                return array('status' => false, 'myreason' => 'Sale Order could not be saved');
      
   // $q = "INSERT INTO `user_sales_order`  (`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`total_sale_value`,`total_sale_qty`, `order_status`, `company_id`) VALUES ('$orderno', '$d1[uid]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', 'True',0,0,NOW(), NOW(),'$total_sale_value','$total_qty', '1', '$company_id')";
//        $r = mysqli_query($dbc, $q);
//        if (!$r) {
//            mysqli_rollback($dbc);
//            return array('status' => false, 'myreason' => 'Sales Table error');
//        }
        if (!empty($d1['product_id'])) {           
            foreach ($d1['product_id'] as $key => $value) {
                if(!empty($d1['order_id'][$key])){
         $q = "update user_sales_order_details set status=2 where order_id = '{$d1[order_id][$key]}' AND product_id = '$value'  ";
               $res3 = mysqli_query($dbc, $q);
               if(!$res3){
                    mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Sales order can not be updated succesfully.');
               }else{
                     if($d1['company_id']==1){
           write_query($q);
        }
               }
                }
            }
        }
        if (!empty($d1['product_id_ex'])) {           
            foreach ($d1['product_id_ex'] as $key => $value) {
                if(!empty($d1['order_id_ex'][$key])){
       $q = "update user_sales_order_details set status=2 where order_id = '{$d1[order_id_ex][$key]}' AND product_id = '$value'  ";
               $res3 = mysqli_query($dbc, $q);
               if(!$res3){
                    mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Sales order can not be updated succesfully.');
               }else{
                     if($d1['company_id']==1){
           write_query($q);
        }
               }
                }
            }
        }
       
        mysqli_commit($dbc); 
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
    function direct_replace_challan_save()
    {
        global $dbc;
        
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $orderno = date('YmdHis');
        //Manipulation and value reading
        //$chnum = $this->next_challan_num();
       // $dispatch = empty($d1['dispatch_date']) ? '' : get_mysql_date($d1['dispatch_date'], '/', false, false);
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        //Start the transaction
       // $_SESSION['chalan_id'] = $chnum;
        $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
          $id = $d1['dealer_id'].$orderno;
          $chlaan_no = $d1['ch_no_prifix'].$d1['ch_no'];
        mysqli_query($dbc, "START TRANSACTION");
        //pre($d1); 
      $q = "INSERT INTO `damage_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`,`complaint_id`,`saleable_non_saleable`) 
    VALUES ($id, '$chlaan_no','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]', '$ch_date', '$d1[company_id]','$d1[complaint_id]','$d1[saleable_non_saleable]')";
    //  h1($q);  
      $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
        $ord = date('YmdHis');
        $cid = $d1['dealer_id'].$ord;
      //  if($d1[complaint_id]==1){
        $ch=$this->get_invoice_no($d1['dealer_id']);
        $chlaan_no="CATC/$d1[dealer_id]/$ch";
        
//        $qch = "INSERT INTO `challan_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`,`invoice_type`) 
//                VALUES ($cid, '$chlaan_no','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]', '$ch_date', '$d1[company_id]','3')";
//  //  h1($qch);
//        $run_qch = mysqli_query($dbc, $qch);
//         if (!$run_qch) {
//            mysqli_rollback($dbc);
//            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
//             }
    //   }
        if($d1['company_id']==1){
            write_query($q);
        }
        $rId = mysqli_insert_id($dbc);
        //$nid= $id;
        if (!empty($d1['product_id'])) {
            $str = array();
            $ch_str = array();
            $qty_error =  array();
            $rp_product_q = array();  


            foreach ($d1['product_id'] as $key => $value) 
            {
                if(!empty($value)){
                $prod = $d1['product_id'][$key];
                $rate = $d1['rate'][$key];
                $taxId = $d1['vat'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $cd = $d1['cd'][$key];
                $mrp = $d1['mrp'][$key];
                $cd_type = $d1['cd_type'][$key];
                $batch_no = $d1['batch_no'][$key];
                $cd_amt = $d1['cd_amt'][$key];
                $dis_type = $d1['trade_disc_type'][$key];
                $dis_amt = $d1['trade_disc_amt'][$key];
                $dis_percent = $d1['trade_disc_val'][$key];
                $taxable_amt = $d1['taxable_amt'][$key];
                $replace_product_id = $d1['replace_product_id'][$key];
                $replace_mrp = $d1['replace_mrp'][$key];
                $replace_rate = $d1['replace_rate'][$key];
                $replace_vat = $d1['vat'][$key];
                $replace_quantity = $d1['replace_quantity'][$key];
                $replace_amount = $d1['replace_amount'][$key];
                $act_amt = $d1['actual_amount'][$key];
                $str[] = "(NULL,'$id','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$taxable_amt','$replace_product_id','$replace_mrp','$replace_rate','$replace_quantity','$replace_amount','$act_amt')";

                
               #####################################
               ##              PUNEET             ##
               #####################################

                /*
                    Damage & replace->Damage/Replace Details:
                    Decrease the stock in stock table.
                */                             

                $stock_q = "UPDATE `stock` SET `qty`=(qty-$qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";
                
                $stock_e = mysqli_query($dbc,$stock_q);

              }
            }

            
            /*
                Damage & replace->Damage/Replace Details:
                Increase the stock in stock table.
            */

           if(!empty($d1['replace_product_id']))
           {
              foreach ($d1['replace_product_id'] as $r => $r_pid)
              {
                    $qty = $d1['replace_quantity'][$r];
                    $stock_q = "UPDATE `stock` SET `qty`=(qty+$qty) WHERE `product_id`='$r_pid' AND `dealer_id`='$d1[dealer_id]'";
                    $stock_e = mysqli_query($dbc,$stock_q);
              }
           }

           /*########################## PUNEET ######################*/           


           // pre($d1['replace_product_id']);
//             foreach ($d1['replace_product_id'] as $Rey => $Rvalue) {
//                 if(!empty($Rvalue)){
//                $replace_product_id = $d1['replace_product_id'][$Rey];
//                $replace_mrp = $d1['replace_mrp'][$Rey];
//                $replace_rate = $d1['replace_rate'][$Rey];
//                $replace_vat = $d1['vat'][$Rey];
//                $replace_quantity = $d1['replace_quantity'][$Rey];
//                $replace_amount = $d1['replace_amount'][$Rey];  
//                
//                $ch_str[] = "(NULL,'$cid','$replace_product_id','$replace_quantity', '$replace_rate','$schqty','$replace_vat','$replace_mrp','$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$replace_amount')";
//                 }
//             }
            
            $dr_str = implode(',', $str);
            $q = "INSERT INTO damage_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`,`replace_product_id`,`replace_mrp`,`replace_rate`,`replace_quantity`,`replace_amount`,`actual_amount`)
            VALUES $dr_str";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            
//            $cho_str = implode(',', $ch_str);
//            $qchod = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`)
//            VALUES $cho_str";
//           // h1($qchod);
//            $run_qchod = mysqli_query($dbc, $qchod);
//            if (!$run_qchod) {
//                mysqli_rollback($dbc);
//                return array('status' => false, 'myreason' => 'Challan Details items can not be added succesfully.');
//            }
        }
            
  
//        if (!empty($d1['product_id'])) {           
//            foreach ($d1['product_id'] as $key => $value) {
//                if(!empty($d1['order_id'][$key])){
//         $q = "update user_sales_order_details set status=2 where order_id = '{$d1[order_id][$key]}' AND product_id = '$value'  ";
//               $res3 = mysqli_query($dbc, $q);
//               if(!$res3){
//                    mysqli_rollback($dbc);
//                return array('status' => false, 'myreason' => 'Sales order can not be updated succesfully.');
//               }else{
//                     if($d1['company_id']==1){
//           write_query($q);
//        }
//               }
//                }
//            }
//        }
       
       
        mysqli_commit($dbc); 
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    

    }

    public function direct_challan_edit($id) {
        global $dbc;
        //h1($id);
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
        return array('status' => false, 'myreason' => $d1['myreason']);
        $orderno = date('YmdHis');
        if(strpos($d1['ch_date'], '-')){
            $ch_date = date('Y-m-d',strtotime($d1['ch_date']));
        }else{
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        }
          $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
          $chlaan_no = $d1['ch_no_prifix'].$d1['ch_no'];
         // $id = $d1['dealer_id'] . date('Ymdhis');
          mysqli_query($dbc, "START TRANSACTION");
        ///pre($d1); exit;
        $q="UPDATE `challan_order` SET `id`='$id',`ch_no`='$chlaan_no',`ch_created_by`='$d1[uid]',`ch_dealer_id`='$d1[dealer_id]',`ch_retailer_id`='$d1[retailer_id]',`ch_date`='$ch_date',`company_id`='$d1[company_id]' WHERE id='$id' ";
        //h1($q);die;
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
        if($d1['company_id']==1){
           write_query($q);
        }
        $rId = mysqli_insert_id($dbc);
        $nid= $id;
        if (!empty($d1['product_id'])) {
            $str = array();
            $str1 = array();
            $batch_id = array();
            //pre($d1); exit;
            $total_sale_value = 0;
            $total_qty = 0;
            foreach ($d1['product_id'] as $key => $value) {
                $prod = $d1['product_id'][$key];
                $rate = $d1['rate'][$key];
                $taxId = $d1['vat'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $cd = $d1['cd'][$key];
                $mrp = $d1['mrp'][$key];
                $cd_type = $d1['cd_type'][$key];
                $batch_no = $d1['batch_no'][$key];
                $cd_amt = $d1['cd_amt'][$key];
                $dis_type = $d1['trade_disc_type'][$key];
                $dis_amt = $d1['trade_disc_amt'][$key];
                $dis_percent = $d1['trade_disc_val'][$key];
                $taxable_amt = $d1['taxable_amt'][$key];
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$id','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$taxable_amt')";
                $nid++;
            }
            $str = implode(',', $str); 
              $q = "DELETE FROM challan_order_details WHERE ch_id = '$id'";
              $r = mysqli_query($dbc, $q);
               foreach ($d1['product_id_ex'] as $key => $value) {
                $prod = $d1['product_id_ex'][$key];
                $rate = $d1['rate_ex'][$key];
                $taxId = $d1['vat_ex'][$key];
                $qty = $d1['quantity_ex'][$key];
                $schqty = $d1['scheme_ex'][$key];
                $cd = $d1['cd_ex'][$key];
                $mrp = $d1['mrp_ex'][$key];
                $cd_type = $d1['cd_type_ex'][$key];
                $str_ex[] = "($nid,'$id','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type')";
                $nid++;
            }
            $str_ex = implode(',', $str_ex);
            if(!empty($str_ex)){
            $q = "INSERT INTO challan_order_details (`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`) VALUES $str_ex"; 
           // h1($q);
             $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            if($d1['company_id']==1){
            write_query($q);
        }
            }
         $q = "INSERT INTO challan_order_details (`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`) VALUES $str";
      
          //h1($q);
              $r = mysqli_query($dbc, $q);
            if (!$r) {
                
                
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            if($d1['company_id']==1){
            write_query($q);
        }
        }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
    public function get_dealer_location_id_list($id) {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT location_" . $_SESSION[SESS . 'retailer_level'] . ".id,location_" . $_SESSION[SESS . 'retailer_level'] . ".name FROM dealer_location_rate_list "
                . " INNER JOIN location_" . $_SESSION[SESS . 'dealer_level'] . " ON location_" . $_SESSION[SESS . 'dealer_level'] . ".id=dealer_location_rate_list.location_id";
        for ($i = $_SESSION[SESS . 'dealer_level']; $i < $_SESSION[SESS . 'retailer_level']; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " WHERE dealer_location_rate_list.dealer_id=" . $id;
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[$row['id']] = $row['id'];
        }
        return $out;
    }

    ########################## This function is uesd to get dealer location Id ##############

    public function get_company_wise_dealer_location_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
        $retailer_level = $_SESSION[SESS . 'constant']['retailer_level'];
        $prev_dlevel = $dealer_level - 1;
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        for ($i = $dealer_level; $i >= 1; $i--) {
            $loc .= "location_$i.name as loc$i, ";
        }
        $q = "SELECT $loc location_$dealer_level.id FROM `location_$dealer_level`";
        for ($i = $dealer_level; $i > 1; $i--) {
            $j = $i - 1;
            $q .= " INNER JOIN location_$j ON location_$j.id = location_$i.location_" . $j . "_id  ";
        }
        $q .= " INNER JOIN dealer_location_rate_list dlrl ON dlrl.location_id = location_$dealer_level.id ";
        $q .= " $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
        }
        return $out;
    }

    public function get_assigned_dealer_location($dealer_id) {
        global $dbc;
        $out = array();
        $q = "SELECT location_id FROM dealer_location_rate_list WHERE dealer_id = '$dealer_id' AND company_id = '{$_SESSION[SESS . 'data']['company_id']}'";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $out[$row['location_id']] = $row['location_id'];
        }
        return $out;
    }

    public function company_wise_dealer_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Dealer Location'; //whether to do history log or not
        return array(true, $d1);
    }

    public function company_wise_dealer_location_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->company_wise_dealer_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);

        $str = '';

        if (!empty($d1['chk'])) {
            foreach ($d1['chk'] as $key => $value)
                $str .= '(\'' . $d1['dealer_id'] . '\', \'' . $value . '\', \'' . $d1['company_id'] . '\',\'1\'),';
        }
        $str = rtrim($str, ',');
        mysqli_query($dbc, "START TRANSCATION");
        $qp = "DELETE FROM dealer_location_rate_list WHERE dealer_id = '$d1[dealer_id]' AND company_id = '$d1[company_id]'";
        $rp = mysqli_query($dbc, $qp);
        if($rp){
              if($d1['company_id']==1){
           write_query($q);
        }
        }
         $q = "INSERT INTO `dealer_location_rate_list` (`dealer_id`, `location_id`, `company_id`, `rate_list_id`)"
        . " VALUES $str";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Location could not be assigned some error occurred');
        }else{
              if($d1['company_id']==1){
           write_query($q);
        }
        }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved');
    }

}

?>
