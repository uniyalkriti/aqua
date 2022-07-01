<?php
$dealer = $_SESSION[SESS . 'data']['dealer_id'];
$state_idd = $_SESSION[SESS . 'data']['state_id'];
class dealer_sale_new extends myfilter {

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

        $id = $orderno = $d1['uid'] . date('YmdHis');
        //pre($d1);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
     $total = 0;
        if ($d1['company_id'] == 1){
            write_query($q);
        }
        $rId = $id;
        $i=1;
        $state_idd =  $d1['state_id'];
        $total_sale_qty = array();
        if (!empty($d1['product_id'])) {
            foreach ($d1['product_id'] as $key => $value) {
                $prod = $d1['product_id'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total_sale_qty[] = $qty;
                $uncode = $orderno.$i;
                
                echo $state_idd;
                $r = "SELECT `base_price` FROM `catalog_product_rate_list` WHERE `stateId`=$state_idd AND `catalog_product_id` =$prod";
                $rl = mysqli_query($dbc,$r);
                while($rows = mysqli_fetch_assoc($rl))
                {
                $rates = $rows['base_price'];
                }
                $total = $total+($qty*$rates);
                $i++;
                $str[] = "('$uncode','$orderno','$prod','$rates','$qty','$qty','$schqty','$schqty')";
            }
            
             $total_sum_qty = array_sum($total_sale_qty);
            
               $q = "INSERT INTO `user_sales_order`  (`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `company_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`image_name`,`remarks`,`total_sale_value`,`total_sale_qty`)  VALUES ('$orderno', '$d1[dspId]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', '$d1[company_id]','1',0,0,NOW(), NOW(),'$filename','$d1[remarks]','$total','$total_sum_qty')";
        
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
        
            $str = implode(', ', $str);
           
            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`remaining`,`scheme_qty`,`remaining_free`)  VALUES $str";
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
       // $q = "UPDATE user_sales_order SET  total_sale_qty = '$total_sum_qty' WHERE order_id = '$orderno' ";
       // $r = mysqli_query($dbc, $q);
         if ($d1['company_id'] == 1){
         write_query($q);         
         }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => TRUE, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
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
        $q = "UPDATE user_sales_order SET user_id = '$d1[dspId]',retailer_id = '$d1[retailer_id]',call_status = '1',date = NOW(),time = NOW(), remarks = '$d1[remarks]', company_id = '$d1[company_id]', location_id = '$d1[location_id]' WHERE order_id = '$id'";
        // h1($q);
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
                $rate = $d1['new_rate'][$key];
                $toal_sale_qty[] = $qty;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                // $str[] = "('$uncode','$id','$prod','$qty','$schqty')";
                $str[] = "('$id','$prod','$rate','$qty','$schqty')"; //puneet
            }
            $str = implode(', ', $str);
            $total_qty_sum = array_sum($toal_sale_qty);

            // $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`, `quantity`,`scheme_qty`) VALUES $str";
            $q = "INSERT INTO `user_sales_order_details` (`order_id`,`product_id`, `rate`,`quantity`,`scheme_qty`) VALUES $str"; //puneet

            // h1($q);
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

    public function get_dealer_sale_details_list($filter = '', $records = '', $orderby = '', $popup=false)
        {
            global $dbc;
            $out = array();
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            $mtype = $_SESSION[SESS . 'constant']['retailer_level'];

            /*$q = "SELECT *,order_id,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id) $filterstr AND user_sales_order_details.status !=2 AND user_sales_order.order_status !=8 GROUP BY user_sales_order.order_id";
            h1($q);
            die;*/

            $q = "SELECT uso.retailer_id,uso.user_id,uso.order_id,uso.remarks,count(distinct user_sales_order_details.id ) as total_qty, SUM(rate*quantity) as total_sale,
    (SELECT count(id) as order_item FROM `user_sales_order_details` WHERE `order_id` = uso.order_id AND remaining_qty>0) as rem_qty,(SELECT SUM(rate*remaining_qty) FROM `user_sales_order_details` WHERE `order_id` = uso.order_id AND remaining_qty>0) as rem_sale_val,
    order_id,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order uso INNER JOIN user_sales_order_details USING(order_id) $filterstr AND user_sales_order_details.status !=2 AND uso.order_status !=8 GROUP BY uso.order_id";

            // h1($q);
            // die;
            
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;

            //$retailer_map = get_my_reference_array('retailer', 'id', 'name');
            //$dealer_map = get_my_reference_array('dealer', 'id', 'name');        
            //$brand_map = get_my_reference_array('catalog_1', 'id', 'name');

            while ($row = mysqli_fetch_assoc($rs)) {
                $where = 'id = '.$row['retailer_id'];
                $retailer_map =  myrowval('retailer', 'name',$where);
                $id = $row['order_id'];
                $out[$id] = $row; // storing the item id
                $out[$id]['dspId'] = $row['user_id'];
                $out[$id]['remarks'] = $row['remarks'];
                $out[$id]['name'] = '';//$dealer_map[$row['dealer_id']];
                $out[$id]['firm_name'] = $retailer_map;
                $out[$id]['person_name'] = $this->get_username($row['user_id']);            
                  
                /* Check if there are pending order items */
                // $pq = "SELECT count(order_id) as order_item, SUM(rate*remaining_qty) as sale_value FROM `user_sales_order_details` WHERE `order_id` = $id AND remaining_qty>0";
                // $cp = mysqli_query($dbc,$pq);
                // $pending_items_check = mysqli_fetch_assoc($cp);
                
                // if($pending_items_check['order_item']>0)
                if($row['rem_qty']>0)
                {
                    $out[$id]['order_item'] = $row['rem_qty'];
                    $out[$id]['sale_value'] = $row['rem_sale_val'];
                }else{
                 /* $q = "SELECT count(order_id) as order_item, SUM(rate*quantity) as sale_value FROM `user_sales_order_details` WHERE `order_id` = $id AND status!=2";

                  $q_e = mysqli_query($dbc,$q);
                  $q_d = mysqli_fetch_assoc($q_e);*/
                  $out[$id]['order_item'] = $row['total_qty'];
                  $out[$id]['sale_value'] = $row['total_sale'];
                }
            }
            //pre($out);
          //  die;
            return $out;
        }

    public function multi_challan_cancel() {
    global $dbc;

    $out = array('status' => 'false', 'myreason' => '');
    list($status, $d1) = $this->get_challan_se_data();
    if (!$status)
    return array('status' => false, 'myreason' => $d1['myreason']);
    $orderno = date('YmdHis');
    // $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
    $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
    $i = 1;

    $iddd = $d1['dealer_id'].date('ymdHis');
    $id = $d1['dealer_id'].date('ymdHis');
    mysqli_query($dbc, "START TRANSACTION");
    $uid= $id;

        foreach ($d1['order_id'] as $k => $val)
        {
        $ch_no = $d1['ch_no'][$k];
        $dealer_id = $d1['dealer_id'];
        $remark = $d1['remark'][$k];
        $reasons = $d1['reasons'][$k];
        $retailer_id = $d1['retailer_id'][$k];
        $uso_order_id=$d1['uso_order_id'][$k];
        $disc_amt = $d1['discount'][$k];
        $disc_amt1 = $d1['total_amount_a'][$k];
        $discounted_amt = $d1['total_disc'][$k];
        $discount_per = $d1['dis'][$k];
        // $ch_date = date('Y-m-d',strtotime($d1['ch_date'][$k]));
        $ch_date = empty($d1['ch_date'][$k]) ? '' : get_mysql_date($d1['ch_date'][$k], '/', false, false);
        
        $qc="INSERT INTO `cancel_order`(`order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`, `company_id`, `call_status`, `total_sale_value`, `discount`, `amount`, `total_sale_qty`, `total_dispatch_qty`, `lat_lng`, `mccmnclatcellid`, `track_address`, `date`, `time`, `image_name`, `override_status`, `order_status`, `remarks`, `sync_status`) "
                . " SELECT  `order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`, `company_id`, `call_status`, `total_sale_value`, `discount`, `amount`, `total_sale_qty`, `total_dispatch_qty`, `lat_lng`, `mccmnclatcellid`, `track_address`, `date`, `time`, `image_name`, `override_status`, `order_status`, `remarks`, `sync_status` FROM `user_sales_order` WHERE `order_id`='$uso_order_id'";
       //h1($q);exit;
        $rc= mysqli_query($dbc, $qc);
        if (!$rc) {
            mysqli_rollback($dbc);
            }
        $qco="INSERT INTO `cancel_order_details`(`order_id`, `product_id`, `rate`, `quantity`, `remaining_qty`, `scheme_qty`, `status`, `sync_status`) "
                . " SELECT  `order_id`, `product_id`, `rate`, `quantity`, `remaining_qty`, `scheme_qty`, `status`, `sync_status` FROM `user_sales_order_details` WHERE `order_id`='$uso_order_id' ";
       // h1($qco);exit;
        $rco= mysqli_query($dbc, $qco);
         if (!$rco) {
            mysqli_rollback($dbc);
            }
            
             $update1="UPDATE `user_sales_order` SET `order_status` = '8' WHERE `order_id` ='$uso_order_id' ";
             mysqli_query($dbc, $update1) ;
             
             $update2="UPDATE `cancel_order` SET `remarks` = '$remark',`reasons`='$reasons' WHERE `order_id` ='$uso_order_id' ";
             mysqli_query($dbc, $update2) ;
    }

    mysqli_commit($dbc);

    return array('status' => true, 'myreason' => '<strong>Orders Successfully Canceled </strong>', 'rId' => $rId);

    }  

    public function get_dealer_sale_list($filter = '', $records = '', $orderby = '', $popup=false)
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

        $q = "SELECT *,order_id,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order $filterstr GROUP BY user_sales_order.order_id";
        
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        //$retailer_map = get_my_reference_array('retailer', 'id', 'name');
        //$dealer_map = get_my_reference_array('dealer', 'id', 'name');        
        //$brand_map = get_my_reference_array('catalog_1', 'id', 'name');

        while ($row = mysqli_fetch_assoc($rs))
        {
            $where = 'id = '.$row['retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dspId'] = $row['user_id'];
            $out[$id]['remarks'] = $row['remarks'];
            $out[$id]['name'] = '';//$dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $retailer_map;
            $out[$id]['person_name'] = $this->get_username($row['user_id']);

            if($popup)
            {                
                $item_q = "SELECT usod.id,cp.name, cp.taxable,usod.quantity,usod.scheme_qty,usod.product_id,usod.remaining_qty FROM user_sales_order_details usod 
                    INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id 
                    INNER JOIN catalog_product cp ON usod.product_id=cp.id 
                    WHERE usod.order_id = $row[order_id] AND status!=2 AND uso.dealer_id=$dealer_id ORDER BY usod.product_id";
                // h1($item_q);
                // die;
                $o_item_data = mysqli_query($dbc,$item_q);

                $multi_p = array();
                while($odata = mysqli_fetch_assoc($o_item_data))
                {
                    $multi_p[$odata['id']][]=$odata;
                }

                // pre($multi_p);
                
                $order_items = array();
                foreach($multi_p as $pid=>$items)
                {
                    if(is_array($items) && count($items)>1)
                    {
                        foreach($items as $item)
                        {
                            if($item['aval_qty']>0)
                            {
                                $order_items[$pid] = $item;
                                break;
                            }
                        }
                    }else{
                        foreach($items as $item)
                        {
                            $order_items[$pid] = $item;
                        }
                    }
                }
                $out[$id]['order_item'] = $order_items;
                // $out[$id]['order_item'] = $this->get_my_reference_array_direct($item_q, 'id');                
                //$rrr = "SELECT usod.id,cp.name, cp.taxable,usod.rate,usod.quantity,usod.remaining as remaining,usod.scheme_qty,usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id WHERE usod.order_id = $row[order_id] AND  status!=2 ";
                //h1($rrr);

                // $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT gift_id,quantity FROM user_retailer_gift_details WHERE order_id = $row[order_id]", 'gift_id');
            }else{
                  
                  $q = "SELECT count(order_id) as order_item, SUM(rate*quantity) as sale_value FROM `user_sales_order_details` WHERE `order_id` = $id";

                  $q_e = mysqli_query($dbc,$q);
                  $q_d = mysqli_fetch_assoc($q_e);
                  $out[$id]['order_item'] = $q_d['order_item'];
                  $out[$id]['sale_value'] = $q_d['sale_value'];
            }
        }
        return $out;
    }

    //retailer_add_location

     //////////////////////////////////////////////////////////////////////////////
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
        $q = "SELECT user_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id WHERE dealer_id = '$dealer_id' GROUP BY user_id";
        // h1($q);
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

        $q = "SELECT l.id, l.name FROM location_5 l INNER JOIN dealer_location_rate_list dlrl ON dlrl.location_id=l.id WHERE dlrl.dealer_id=$id GROUP BY l.id";

        /*$q = "SELECT location_" . $_SESSION[SESS . 'constant']['retailer_level'] . ".id,location_" . $_SESSION[SESS . 'constant']['retailer_level'] . ".name FROM dealer_location_rate_list "
                . "INNER JOIN location_" . $_SESSION[SESS . 'constant']['dealer_level'] . " ON location_" . $_SESSION[SESS . 'constant']['dealer_level'] . ".id=dealer_location_rate_list.location_id";
        h1($q);       
        die('CHECK POINT!!!');

        for ($i = $_SESSION[SESS . 'constant']['dealer_level']; $i < $_SESSION[SESS . 'constant']['retailer_level']; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " WHERE dealer_location_rate_list.dealer_id=" . $id . "  ";*/

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
        $q = "INSERT INTO `challan_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `sesId`, `remark`) VALUES ('$id', '$chnum','$d1[uid]', '$d1[dealer_id]', '$d1[ch_retailer_id]', '$ch_date', '$d1[company_id]', '$d1[sesId]', '$d1[remark]');"; 
        // h1($q);exit;
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
       // h1($q);
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

    public function calculate_available_stock($id)
    {
        global $dbc;
        $sesId = $_SESSION[SESS . 'csess'];
        $stock = 0;

        /*$q1 = "SELECT SUM(qty) AS issue FROM challan_order INNER JOIN `challan_order_details` ON challan_order.id = challan_order_details.ch_id WHERE challan_order_details.product_id = '$id' AND ch_dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ";*/

        $q1 = "SELECT SUM(qty) AS avb_stk FROM stock WHERE product_id = '$id' AND dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ";

        //echo 'TRUE<$>'.$q1;
        $r1 = mysqli_query($dbc, $q1);
        $d1 = mysqli_fetch_assoc($r1);
        if ($d1['avb_stk'] == '') {
            $d1['avb_stk'] = 0;
        }

       // $q2 = "SELECT SUM(quantity)as quantity FROM user_primary_sales_order_details  usod INNER JOIN user_primary_sales_order uso ON uso.order_id = usod.order_id WHERE usod.product_id = '$id' AND uso.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."'  ";
       //  //echo $q2;
       // $r2 = mysqli_query($dbc, $q2);
       //  $d2 = mysqli_fetch_assoc($r2);
       //  if ($d2['quantity'] == '') {
       //      $d2['quantity'] = 0;
       //  }

        //here we get stock
        $stock = $d1['avb_stk'];
        return $stock;
    }

    public function calculate_free_stock($id) {
        global $dbc;
        $sesId = $_SESSION[SESS . 'csess'];
        //h1($sesId);
        $stock = 0;
         $q1 = "SELECT SUM(free_qty) AS issue FROM challan_order INNER JOIN `challan_order_details` ON challan_order.id = challan_order_details.ch_id WHERE challan_order_details.product_id = '$id' AND ch_dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ";
        //echo 'TRUE<$>'.$q1;
        $r1 = mysqli_query($dbc, $q1);
        $d1 = mysqli_fetch_assoc($r1);
        if ($d1['issue'] == '') {
            $d1['issue'] = 0;
        }
       $q2 = "SELECT SUM(scheme_qty)as quantity FROM user_primary_sales_order_details  usod INNER JOIN user_primary_sales_order uso ON uso.order_id = usod.order_id WHERE usod.product_id = '$id' AND uso.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."'  ";
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

    public function get_challan_list($filter ='', $records ='', $orderby ='', $ifilter='')
    {
        global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        	
        $filterstr  = $this->oo_filter($filter, $records, $orderby);
        if(!empty($ifilter))
        {
            $ifilterstr = $this->oo_filter($ifilter).' AND';            
        }else{
            $ifilterstr = 'WHERE';
        }
        // $q = "SELECT *,challan_order.id as id, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order left join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id $filterstr";
        $q = "SELECT challan_order.discount_per,challan_order.ch_retailer_id,challan_order.ch_dealer_id,challan_order.ch_user_id,challan_order.date_added,challan_order.ch_no,challan_order.ch_no,challan_order.discount_amt,challan_order.amount,challan_order.id as id, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date,(SELECT SUM(taxable_amt) FROM challan_order_details WHERE ch_id=challan_order.id) as tamount FROM challan_order $filterstr";
        //h1($q);exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
       
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        //  $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        //  echo "ANKUSH PANDEY";
        //  print_r($retailer_map);
        //  exit; 
        while ($row = mysqli_fetch_assoc($rs)) {
            $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $retailer_map;
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            //$out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, "
                    . "catalog_product.name,"
                    . "catalog_product.hsn_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id "
                    . " LEFT JOIN product_rate_list ON challan_order_details.product_id = product_rate_list.product_id "
                    . " AND product_rate_list.state_id ='$state_id' $ifilterstr ch_id = $id GROUP BY challan_order_details.product_id,challan_order_details.ch_id ";
            
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
            
            
            // $q1 = "SELECT challan_order_details.id,catalog_product.hsn_code,sum(challan_order_details.tax) as gst_tax,"
            //         . "sum(challan_order_details.vat_amt) as gst_amt,sum(challan_order_details.taxable_amt) as taxable_amt FROM challan_order_details "
            //         . "INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
            //         . "INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id "
            //         . "LEFT JOIN product_rate_list ON challan_order_details.product_id = product_rate_list.catalog_product_id "
            //         . "AND product_rate_list.state_id ='$state_id' WHERE ch_id = $id "
            //         . "GROUP BY catalog_product.hsn_code,challan_order_details.ch_id";
            // //h1($q1);
            // $out[$id]['challan_hsn_dtl'] = $this->get_my_reference_array_direct($q1, 'id');
            
       //  pre($out);   
            
        }
       
        return $out;
    }

    public function get_invoice_report_list($filter ='', $records ='', $orderby ='', $ifilter='')
        {
            global $dbc;
            $out = array();
            $state_id = $_SESSION[SESS . 'data']['state_id'];
                
            $filterstr  = $this->oo_filter($filter, $records, $orderby);
            if(!empty($ifilter))
            {
                $ifilterstr = $this->oo_filter($ifilter).' AND';            
            }else{
                $ifilterstr = 'WHERE';
            }
            
            $q = "SELECT challan_order.discount_per,challan_order.id as cid,challan_order.discount_per,challan_order.ch_retailer_id,challan_order.ch_user_id,challan_order.date_added,challan_order.ch_no,challan_order.ch_no,challan_order.discount_amt,challan_order.amount,challan_order.id as id, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order $filterstr";
            // h1($q);

            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;       

            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['cid'];
                $where = 'id = '.$row['ch_retailer_id'];
                $out[$id] = $row;
                $out[$id]['retailer_name'] = myrowval('retailer', 'name',$where);

                $division = '';
                if(!empty($_POST['division']))
                {
                    $division = ' AND division='.$_POST['division'];
                }

                $where1 = 'ch_id = '.$row['cid'].$division;
                $out[$id]['tamount'] = myrowvaljoin('challan_order_details','SUM(taxable_amt)','catalog_product','challan_order_details.product_id=catalog_product.id',$where1);
            }
           
            return $out;
        }

    public function get_retaielr_sale_report_list($filter ='', $records ='', $orderby ='', $ifilter='')
        {
            global $dbc;
            $out = array();
            $state_id = $_SESSION[SESS . 'data']['state_id'];
            $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
            $start = get_mysql_date($_POST['start'], '/', $time = false, $mysqlsearch = true);
            $end = get_mysql_date($_POST['end'], '/', $time = false, $mysqlsearch = true);
                
            $filterstr  = $this->oo_filter($filter, $records, $orderby);
            if(!empty($ifilter))
            {
                $ifilterstr = $this->oo_filter($ifilter).' AND';            
            }else{
                $ifilterstr = 'WHERE';
            }            

            $q = "SELECT co.ch_retailer_id,r.name FROM challan_order co INNER JOIN retailer r ON co.ch_retailer_id=r.id $filterstr GROUP BY co.ch_retailer_id";

            // h1($q);

            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;       

            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['ch_retailer_id'];
                $out[$id] = $row;

                $division = '';
                if(!empty($_POST['division']))
                {
                    $division = ' AND division='.$_POST['division'];
                }

                $tamount_arr   = array();
                $dis_amt_arr   = array();
                $final_amt_arr = array();

                $q1 = "SELECT co.id as ch_id,co.discount_per FROM challan_order co WHERE ch_retailer_id = '$id' AND DATE_FORMAT(ch_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(ch_date,'%Y%m%d') <= '$end'";
                // h1($q1);
                $ret_ch_id = mysqli_query($dbc,$q1);
                while($ret_data = mysqli_fetch_assoc($ret_ch_id))
                {
                    $where1 = 'ch_id = '.$ret_data['ch_id'].$division;
                    $tamount_arr[] = $tamount = myrowvaljoin('challan_order_details','SUM(taxable_amt)','catalog_product','challan_order_details.product_id=catalog_product.id',$where1);
                    $dis_amt_arr[] = $dis_amt = (($tamount*$ret_data['discount_per'])/100);
                    $final_amt_arr[] = $final_amt = $tamount-$dis_amt;
                }

               $out[$id]['tamount'] = array_sum($tamount_arr); 
               $out[$id]['dis_amt'] = array_sum($dis_amt_arr); 
               $out[$id]['total_amt'] = array_sum($final_amt_arr); 

            }
           
            return $out;
        }

    public function get_challan_report_list($filter = '', $records = '', $orderby = '')
        {
            global $dbc;
            $out = array();        
            $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
                
            $filterstr = $this->oo_filter($filter, $records, $orderby);        

            $q = "SELECT cod.id AS cid,co.ch_date,co.ch_no,r.name as rname,r.tin_no,p.name,p.itemcode,p.hsn_code,cod.qty,cod.product_rate,cod.mrp,(cod.qty*cod.product_rate) as amount,(cod.cd_amt+dis_amt) as discount,cod.taxable_amt,cod.tax,cod.vat_amt,co.amount as famount FROM challan_order_details cod 
            JOIN challan_order co ON cod.ch_id=co.id 
            JOIN retailer r ON co.ch_retailer_id=r.id AND r.tin_no!='0' AND r.tin_no!='' 
            JOIN catalog_product p ON cod.product_id=p.id 
            $filterstr";
            
            // h1($q);
            // die;
            
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
           
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['cid'];
                $out[$id] = $row;
            }
           
            return $out;
        }


    public function get_challan_list_for_payment($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date,IF(ISNULL(dispatch_id),'Pending','Dispatched') as is_dispatch ,DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order inner join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id LEFT JOIN daily_dispatch_details ON challan_order.id=daily_dispatch_details.ch_id $filterstr";
     // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
      //  $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)){
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name,catalog_product_rate_list.surcharge,catalog_product_rate_list.comunity_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id "
                    . " INNER JOIN catalog_product_rate_list ON challan_order_details.product_id = catalog_product_rate_list.catalog_product_id "
                    . " AND catalog_product_rate_list.stateId ='$state_id'  WHERE ch_id = $id ";
             //h1($q);
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q,'id');
            
        }// while($row = mysqli_fetch_assoc($rs)){ ends
      // pre($out);
        return $out;
    }
    ///////////////////////////////GET PAYMENT NEW///////////////////////////////////////////////
    public function get_payment_data($filter = '', $records = '', $orderby = '')
     {
       global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT ch_retailer_id, sum(challan_order.remaining) as total FROM 
            challan_order  $filterstr ";
      //h1($q);
        //left join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
      
      //  $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)){
           // pre($row);
            $id = $row['ch_retailer_id'];
            $out[$id] = $row; // storing the item id
            $retailer = $row['ch_retailer_id'];
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['total'] = $row['total'];
            $where = 'id = '.$row['ch_retailer_id'];
            $out[$id]['retailer_name']=  myrowval('retailer', 'name',$where);
            $out[$id]['last'] = $this->get_last_payment($retailer,$dealer_id);
            //$out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
            
            
        }// while($row = mysqli_fetch_assoc($rs)){ ends
     // pre($out);
        return $out;  
     }
     public function get_last_payment($retailer,$dealer)
     {
         global $dbc;
         $out = array();
         $query = "SELECT pay_date_time,total_amount FROM payment_collection WHERE retailer_id='$retailer' AND dealer_id='$dealer'
                 ORDER BY pay_date_time DESC limit 1";
         //h1($query);
         $qm = mysqli_query($dbc,$query);
         $row=mysqli_fetch_assoc($qm);
         $out['lastdate'] = $row['pay_date_time'];
         $out['lastamt'] = $row['total_amount'];
         return $out;
     }
    ///////////////////////////??FOR PAYMENT//////////////////////////////////////////
     public function get_payment_list($filter = '', $records = '', $orderby = '')
     {
       global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,challan_order.id as ch_id, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date,IF(payment_status=0 ,
            'Pending','Paid') as payment ,DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM 
            challan_order left join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id $filterstr";
     //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
      
      //  $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)){
           // pre($row);
            $id = $row['ch_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];

             $q="SELECT  `challan_order`.`remaining`
                    FROM  `challan_order` 
                    WHERE  `challan_order`.`id`='$id' order by auto ASC";
             //h1($q);
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q,'id');
            
        }
      
        return $out;  
     }
///////////////////////////??END PAYMENT///////////////////////////////////////////////////
     public function get_paid_challan_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT challan_order.ch_no,challan_order.payment_status,challan_order.amount,challan_order.remaining, challan_order.dispatch_status,IF(ISNULL(dispatch_id),'Pending','Dispatched') as is_dispatch ,"
                . " challan_order.ch_retailer_id,challan_order.id as chln_id,"
                . "  DATE_FORMAT(dispatch_date, '%d/%m/%Y') AS dispatch_date,DATE_FORMAT(ch_date, '%d-%b-%Y') AS ch_date,"
                . " DATE_FORMAT(payment_collection.pay_date_time, '%Y-%m-%d') as payment_date FROM challan_order "
                . " inner join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id "
                . " LEFT JOIN daily_dispatch_details ON challan_order.id=daily_dispatch_details.ch_id"
                . "  LEFT JOIN payment_collection ON FIND_IN_SET(challan_order.id,payment_collection.challan_id) $filterstr";
       //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['chln_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name,catalog_product_rate_list.surcharge,catalog_product_rate_list.comunity_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id "
                    . " INNER JOIN catalog_product_rate_list ON challan_order_details.product_id = catalog_product_rate_list.catalog_product_id "
                    . " AND catalog_product_rate_list.stateId ='$state_id'  WHERE ch_id = $id ";
           //h1($q);//die;
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
            
             
            
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        //pre($out);
        return $out;
    }

    
    public function get_tax_inv_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order "
                . " $filterstr";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $dealer_map = get_my_reference_array('dealer', 'id', 'name');

       // $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        //$retailer_map1 = get_my_reference_array('retailer', 'id','tin_no');
        
        while ($row = mysqli_fetch_assoc($rs)) {
            $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map;
            $out[$id]['tin_no'] = myrowval('retailer', 'tin_no',$where);
           // $out[$id]['tin_no'] = $retailer_map1[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,sum(qty*product_rate) as challan_val,sum(cd_amt) as total_cd_amt,challan_order_details.product_id AS pid,sum(taxable_amt) AS amt, catalog_product.name 
            FROM challan_order_details 
            INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  
            INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  
            WHERE ch_id =$id group by ch_id";
            // h1($q);
           
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
            //$out[$id]['challan_item1'] = $this->get_my_reference_array_direct($q1, 'id');
        }
        return $out;
    }

    
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

//    public function print_looper_challan($multiId, $options = array()) {
//
//        global $dbc;
//        $out = array();
//        if (is_null($multiId) || empty($multiId))
//            return $out;
//
//        //Create the object when needed
//        $party = new retailer();
//        //Explode to get an array of all the Id
//        $multiId = explode('-', $multiId);
//        foreach ($multiId as $key => $value) {
//            $id = trim($value);
//            if (empty($id))
//                continue;
//
//            //read the record statistics
//            $rcdstat = $this->get_challan_list("id = $id");
//            if (empty($rcdstat))
//                continue;
//            $temp = $rcdstat[$id];
//
//            $out[$id] = $rcdstat[$id];
//            $out[$id]['adr'] = $party->get_retailer_adr($temp['ch_retailer_id']);
//        }
//        //pre($out);
//        return $out;
//    }

        ######################################## Invoice ends here ####################################################
    public function print_looper_challan($multiId, $options = array())
    {
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
            $rcdstat = $this->get_challan_list("challan_order.id = $id");
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
//    public function next_challan_num() {
//        global $dbc;
//        $out = array();
//        $q = "SELECT COUNT(ch_no) AS total FROM challan_order";
//        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
//        return $rs['total'] + 1;
//    }

    ############# DSP WISE CHALLAN WORKING END HERE ############################

    public function direct_challan_save() {
        global $dbc;
        
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
     
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
        $orderno = date('YmdHis');
       // $iddd = $d1['dealer_id'].$orderno;
        $iddd = $d1['dealer_id'].date('ymdHis');
       
        $chlaan_no = $d1['ch_no'];
        mysqli_query($dbc, "START TRANSACTION");
        
     
        if($d1['company_id']==1){
            write_query($q);
        }
        $rId = mysqli_insert_id($dbc);
        
        if (!empty($d1['product_id'])) {
            $str = array();
            $str1 = array();
            $batch_id = array();          
            $total_sale_value = 0;
            $total_qty = 0;
            $amount = 0;
            
            foreach ($d1['product_id'] as $key => $value) {
                
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
                $taxable_amt = $d1['amount'][$key];
                $vat_amt = $d1['vat_amt'][$key];
                $aval_stock = $d1['aval_stock'][$key];
                $dealer_id = $d1['dealer_id'];
                $amount = $amount+$taxable_amt;

                $bill_of_supply = $d1['bos'][$key];

               // echo $schqty; exit;
               //h1($taxable_amt);
                //$total_sale_value = $total_sale_value + $d1['base_price'][$key] * $d1['quantity'][$key];
               // $total_qty = $total_qty + $d1['quantity'][$key];
                //To save the value of the other columns as some columns are affected by po
                $str[] = "(NULL,'$iddd','$prod','$bill_of_supply','$qty', '$rate','$schqty','$taxId','$mrp',
                    '$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$taxable_amt','$taxable_amt','$vat_amt')";
                //$str1[] = "(,'$orderno','$prod','$rate', '$qty','$schqty')";
                 $qtyy= $qty;
                $sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$prod' order by `mfg` asc"; 
              
                $rs1=mysqli_query($dbc,$sq);     
                
                while($row = mysqli_fetch_assoc($rs1))
                {
                     $remaining_qty = $row['remaining'];
                     $product_id = $row['product_id'];
                     $id = $row['id'];  
                    
                    if($qtyy >= $remaining_qty){
                          $qtyy =  $qtyy - $remaining_qty;
                          $balqty = 0;
                        $q = "UPDATE stock SET remaining = '$balqty' WHERE id='$id'";
                        $r = mysqli_query($dbc, $q);

                       //  $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$remaining_qty')";
                       // $rsrt = mysqli_query($dbc, $qsrt);
                     //   if (!$rsrt)
                      //      return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
                    }else{
                        $balqty =  $remaining_qty - $qtyy; 
                       // $baltemp = $qty - $remaining_qty;
                        $q = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);   
                      //   $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$balqty')";
                      //  $rsrt = mysqli_query($dbc, $qsrt);
                     //   if (!$rsrt)
                     //       return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
                    }

                    /*<puneet>*/

                    $new_stock_qty = $aval_stock-$qtyy;
                    $update_stock_q="UPDATE `stock` SET qty=$new_stock_qty WHERE `product_id`=$prod AND `dealer_id`=$dealer_id AND `mrp`=$mrp";

                    mysqli_query($dbc, $update_stock_q);

                    /*</puneet>*/
                    
                }
                
                
                $nid++;
              }
            }
            $d = date('Y-m-d');

//            $sch = "SELECT value,value_to,scheme_gift FROM scheme_on_sale_details INNER JOIN scheme_on_sale ON scheme_on_sale.scheme_id = 
//                scheme_on_sale_details.scheme_id WHERE scheme_on_sale.start_date <='$d' AND scheme_on_sale.end_date>='$d'";
//            //h1($sch);exit;
//            $sch_q = mysqli_query($dbc, $sch);
//            while($sch_row = mysqli_fetch_assoc($sch_q))
//            {
//               $schvalue = $sch_row['value'];
//               $schvalueto = $sch_row['value_to'];
//               $sch_gift = $sch_row['scheme_gift'];
//               if($amount>=$schvalue && $amount<=$schvalueto)
//               {
//                  $gift = $sch_gift; 
//               }
//            }
            $disc = $d1['dis'];
            $disc_amt = ($amount*$disc)/100;
//            h1($gift);
//            h1($disc_amt); exit;
            $amount1 = $amount - $disc_amt;
           
        $qqq = "INSERT INTO `challan_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`,`ch_user_id`, `ch_date`,`date_added`, `company_id`,`invoice_type`
            ,`discount_per`,`discount_amt`,`amount`,`remaining`) 
    VALUES ($iddd, '$chlaan_no','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]','$d1[user_id]','$ch_date',NOW(), '1','1',
        '$disc','$disc_amt','$amount1','$amount1')";
        //  h1($qqq);exit;
        $r = mysqli_query($dbc, $qqq);
        
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
            $str = implode(',', $str);
             $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`supply_status`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,
                 `cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`,`remain_amount`,`vat_amt`)
            VALUES $str";
           /*h1($q);
           die;*/
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
           $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`batch_no`) VALUES $str_ex";
         //  h1($q); exit;
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
        
        return array('status' => true, 'myreason' => '<strong>'.$d1['what'] . ' successfully Saved <br/> Discount % ='.$gift.
            '% &nbsp; | &nbsp; Discount Amount = '.round($disc_amt,2).'<br><br> Total Amount = '.round($amount1,2).'</strong>', 'rId' => $rId, 'challan_id'=>$iddd);
    }
    
    public function direct_challan_edit($id)
    {
        /*pre($_POST);
        die;*/
        global $dbc;
        
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
        $chlaan_no = $d1['ch_no'];
        mysqli_query($dbc, "START TRANSACTION");

        $product_ids_str = explode(',',$d1['idstr']);
        $product_qty_str = explode(',',$d1['qtystr']);
        $product_mrp_str = explode(',',$d1['mrpstr']);
      
        if (!empty($d1['product_id']))
        {
            $str = array();
            $str1 = array();
            $batch_id = array();
            $total_sale_value = 0;
            $total_vat = 0;
            $total_qty = 0;
            
            foreach ($d1['product_id'] as $key => $value)
            {
                $prod = $d1['product_id'][$key];
                $bill_of_supply = ($d1['bos'][$key])?1:0;
                $rate = $d1['rate'][$key];
                $taxId = $d1['vat'][$key];
                $vat_amt = $d1['vat_amt'][$key];
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
                $amount = $d1['amount'][$key];
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$id','$prod','$bill_of_supply','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$cd_amt','$batch_no','$dis_type','$dis_amt','$dis_percent','$amount','$amount','$vat_amt')";
                $nid++;
                $total_sale_value = $total_sale_value+$amount;
                $total_vat = $total_vat+$vat_amt;

                #####################################
                ##              PUNEET             ##
                #####################################

                $ordered_qty = $d1['ordered_qty'][$key];
                $aval_stock  = $d1['aval_stock'][$key];

                $ordered_mrp = $d1['ordered_mrp'][$key];                

                /* ########## INVOICE/BILLING->INVOIVE DETAILS ########### */

                //If retailer update the qty of product with 0 in edit mode

                /*if($qty==0)
                {
                    $stock_q = "UPDATE `stock` SET `qty`=(qty+$ordered_qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]'";

                    $stock_e = mysqli_query($dbc,$stock_q);
                }*/
                
                //If retailer increase the qty of product in edit mode                

                $changed_new_mrp = 0;

                //If retailer increase the qty of product in edit mode                

                if($qty>$ordered_qty && $qty!=$aval_stock)
                {
                    if($mrp!=$ordered_mrp)
                    {
                        $changed_new_mrp = 1;
                    }else{
                        $updated_qty = $qty-$ordered_qty;
                        $stock_q = "UPDATE `stock` SET `qty`=(qty-$updated_qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";                        
                        $stock_e = mysqli_query($dbc,$stock_q);
                    }

                }

                // If retailer decrease the qty of product in edit mode

                elseif($qty<$ordered_qty)
                {
                    if($mrp!=$ordered_mrp)
                    {
                        $changed_new_mrp = 1;
                    }else{
                        $updated_qty = $ordered_qty-$qty;
                        $stock_q = "UPDATE `stock` SET `qty`=(qty+$updated_qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";
                        $stock_e = mysqli_query($dbc,$stock_q);
                    }
                }

                //If retailer purchase the whole stock in edit mode

                elseif($qty==$aval_stock)
                {
                    if($mrp!=$ordered_mrp)
                    {
                        $changed_new_mrp = 1;
                    }else{
                       $updated_qty = $product_qty_str[$key];
                       $stock_q = "UPDATE `stock` SET `qty`= '$updated_qty' WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";
                       $stock_e = mysqli_query($dbc,$stock_q);
                   }
               }

               elseif(($qty==$ordered_qty) && ($mrp!=$ordered_mrp))
               {
                   $changed_new_mrp = 1;
               }                


                if($changed_new_mrp)
                {
                    // If mrp changed on edit mode the qty of prev mrp will added back to stock of product of prev mrp and update(add/delete) from selected mrp.
                    $reverse_stock_q = "UPDATE `stock` SET `qty`=(qty+$ordered_qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$ordered_mrp'";
                    mysqli_query($dbc,$reverse_stock_q);

                    // Now performing the normal task.
                    $updated_qty = $qty;
                    $stock_q = "UPDATE `stock` SET `qty`=(qty-$updated_qty) WHERE `product_id`='$prod' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";
                    $stock_e = mysqli_query($dbc,$stock_q);
                }

                $product_qty_detail[$prod] = $qty;
            }

            

           // Updating the stock when an item removed from challan on edit  
           

            if(count($d1['product_id'])<count($product_ids_str))
            {
                $challan_item_ids = $d1['product_id'];
                $new_id_arr = array_diff($product_ids_str,$challan_item_ids);

                foreach($product_ids_str as $k=>$removed_id)
                {
                    $removed_product_qty[$removed_id] = array(
                                                            'qty'=>$product_qty_str[$k],
                                                            'mrp'=>$product_mrp_str[$k]
                                                        );
                }

                foreach($new_id_arr as $pid)
                {
                    $updated_qty = $removed_product_qty[$pid]['qty'];
                    $mrp = $removed_product_qty[$pid]['mrp'];
                    $stock_q = "UPDATE `stock` SET `qty`=(qty+$updated_qty) WHERE `product_id`='$pid' AND `dealer_id`='$d1[dealer_id]' AND `mrp`='$mrp'";
                    $stock_e = mysqli_query($dbc,$stock_q);
                }
            }

            /*########################## PUNEET ######################*/
            
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
            $total_value = $total_vat+$total_sale_value;
            $discount = ($total_sale_value*$d1['dis'])/100;
            // $final = $total_sale_value-$discount;
            $final = $d1['totamt'];

         $qup="UPDATE `challan_order` SET `ch_no`='$chlaan_no',`ch_created_by`='$d1[uid]',
             `ch_dealer_id`='$d1[dealer_id]',`ch_retailer_id`='$d1[retailer_id]',
              `ch_date`='$ch_date',`discount_per`='$d1[dis]',`discount_amt`='$discount',`amount`='$final',`remaining`='$final' WHERE id='$id' ";
      /* h1($qup);
       die;*/
        $rup = mysqli_query($dbc, $qup);
        if (!$rup) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }


        $rId = mysqli_insert_id($dbc);
        $nid= $id;
            $str_ex = implode(',', $str_ex);
            if(!empty($str_ex)){
            $q = "INSERT INTO challan_order_details (`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`,`vat_amt`) VALUES $str_ex"; 

            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
            if($d1['company_id']==1){
            write_query($q);
        }
            }

         $q1 = "INSERT INTO challan_order_details (`ch_id`,`product_id`,`supply_status`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`cd_amt`,`batch_no`,`dis_type`,`dis_amt`,`dis_percent`,`taxable_amt`,`remain_amount`,`vat_amt`) VALUES $str";
      

            $r1 = mysqli_query($dbc, $q1);
            if (!$r1) {                
                
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
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
    
    //////////////////////////////////////////////////////////////////////////////////////////////
  public function multi1_challan_save() {
        global $dbc;
        
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_challan_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        $orderno = date('YmdHis');
        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
        $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
        $i = 1;
        $iddd = $d1['dealer_id'].date('ymdHis');
        $id = $d1['dealer_id'].date('ymdHis');
      //  pre($d1);
        mysqli_query($dbc, "START TRANSACTION");
        $uid= $id;
  //pre($d1);exit;
          foreach ($d1['order_id'] as $k => $val) {
             
                $ch_no = $d1['ch_no'][$k];
                $dealer_id = $d1['dealer_id'];
                $retailer_id = $d1['retailer_id'][$k];
                $uso_order_id=$d1['uso_order_id'][$k];
                
                if(empty($d1['user_id'][$k])){
                    $user_id=$d1['uid'];  // doubt
                }else{
                     $user_id = $d1['user_id'][$k];
                }
               $ch_date = $d1['date'][$k];
                 
                $str1[] = "($iddd,'$ch_no','$user_id','$dealer_id',
                    '$retailer_id','','$ch_date','1','0',
                       '0','','1','2','0')";
                
                foreach ($d1['product_id'][$k] as $key => $value) {
                $prod = $d1['product_id'][$k][$key];
               // $order_id = $d1['product_id'][$k][$key];
                $rate = $d1['rate'][$k][$key];
                $taxId = $d1['vat'][$k][$key];
                $qty = $d1['quantity'][$k][$key];
                $schqty = $d1['scheme'][$k][$key];
                $cd = $d1['cd'][$k][$key];
                $mrp = $d1['mrp'][$k][$key];
                $cd_type = $d1['cd_type'][$k][$key];
                $cd_amt = $d1['cd_amt'][$k][$key];                
                $dis_type = $d1['trade_disc_type'][$k][$key];
                $dis_amt = $d1['trade_disc_amt'][$k][$key];
                $dis_percent = $d1['trade_disc_val'][$k][$key];
                $taxable_amt = $d1['amount'][$k][$key];
                $qtyy= $qty+$schqty; 
                $store[] = "('$iddd','$prod','0','','$taxId','$qty', '$rate','$schqty'
                  ,'$uso_order_id','$user_id','$mrp','$cd','$cd_type','$cd_amt','$dis_type',
                        '$dis_amt','$dis_percent','$taxable_amt')";
              
                $sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$prod' order by `mfg` asc"; 
              
                $rs1=mysqli_query($dbc,$sq);                
                while($row = mysqli_fetch_assoc($rs1))
                {
                     $remaining_qty = $row['remaining'];
                     $product_id = $row['product_id'];
                     $id = $row['id'];  
                      
                    if($qtyy >= $remaining_qty){
                          $qtyy =  $qtyy - $remaining_qty;
                          $balqty = 0;
                        $q = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);
                        // $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$remaining_qty')";
                      //  $rsrt = mysqli_query($dbc, $qsrt);
                     //   if (!$rsrt)
                      //      return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
                    }else{
                        $balqty =  $remaining_qty - $qtyy; 
                       // $baltemp = $qty - $remaining_qty;
                        $q = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);   
                       //  $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$balqty')";
                       // $rsrt = mysqli_query($dbc, $qsrt);
                     //   if (!$rsrt)
                     //       return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
                    }
                    
                }
                
                }
                
                 $i++;
            }          
             //***********************INSERT INTO challan_order*********************
          $str2 = implode(',', $str1);
          $query ="INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`,
              `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`, `dispatch_status`, 
              `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`) VALUES $str2";
  // h1($query);//exit;
           $run = mysqli_query($dbc, $query);
          if (!$run) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
        }
          //***********************INSERT INTO challan_order_details*********************
        $rId = mysqli_insert_id($dbc);
          $stored = implode(',', $store);
        //  print_r($store);
           
            $q = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, 
                `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, 
                `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, 
                `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`) VALUES $stored";
         h1($q);//exit;
            $r = mysqli_query($dbc, $q);
              
            if (!$r) {
                mysqli_rollback($dbc);
             //   return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }   
            //***********************New product add/*********************
            
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
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
                $str_ex[] = "($nid,'$uid','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$batch_no')";
                $q="UPDATE balance_stock SET quantity='$qty' WHERE product_id='$prod' AND dealer_id='$dealer_id'";
                $run=mysqli_query($dbc, $q);
                
                $nid++;
            }
            }
            $str_ex = implode(',', $str_ex);
            if(!empty($str_ex)){
            $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`batch_no`) VALUES $str_ex";
             $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
            }
         
            }
            }
 //**************************Update user_sales_order_details status***************************
         foreach ($d1['order_id'] as $k1 => $val1) {
           foreach ($d1['product_id'][$k1] as $key2 => $value2) {
           $order_id=$d1['order_id'][$k1][$key2];
           $q = "update user_sales_order_details set status=2 where order_id = '$order_id' AND product_id = '$value2'  ";
         //  h1($q);
          $res3 = mysqli_query($dbc, $q);     
       }
    }
       ///******************************************** End Update user_sales_order_details status******
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
        //echo ;
   // header("Location:index.php?option=make-challan&showmode=1&mode=1&actiontype=print&id=-1252170411181515"); 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////
public function multi_challan_save() {
global $dbc;

$out = array('status' => 'false', 'myreason' => '');
list($status, $d1) = $this->get_challan_se_data();
if (!$status)
return array('status' => false, 'myreason' => $d1['myreason']);
$orderno = date('YmdHis');
// $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
$_SESSION['chalan_dealer_id'] = $d1[dealer_id];
$i = 1;

$iddd = $d1['dealer_id'].date('ymdHis');
$id = $d1['dealer_id'].date('ymdHis');
mysqli_query($dbc, "START TRANSACTION");
$uid= $id;

    foreach ($d1['order_id'] as $k => $val)
    {

    /* Deleting order item which are deleted by the user at the time of making challan. */

    if($d1['deleted_order_item'][$k]!='')
    {
        $deleted_item_id = array_filter(explode(',',$d1['deleted_order_item'][$k]));
        $deleted_item_id = implode(',',$deleted_item_id);

        $del_item_q = "UPDATE user_sales_order_details SET status=2 WHERE id IN($deleted_item_id) AND order_id = $k";
        mysqli_query($dbc,$del_item_q);
    }



    $ch_no = $d1['ch_no'][$k];
    $dealer_id = $d1['dealer_id'];

    $retailer_id = $d1['retailer_id'][$k];
    $sub_total = $d1['total'][$k];
    $ch_uid = $d1['user_id'][$k];
    $uso_order_id=$d1['uso_order_id'][$k];
    $disc_amt = $d1['discount'][$k];
    $disc_amt1 = $d1['total_amount_a'][$k];
    $discounted_amt = $d1['total_disc'][$k];
    $discount_per = $d1['dis'][$k];
    // $ch_date = date('Y-m-d',strtotime($d1['ch_date'][$k]));
    $ch_date = empty($d1['ch_date'][$k]) ? '' : get_mysql_date($d1['ch_date'][$k], '/', false, false);

    if(empty($d1['user_id'][$k])){
    $user_id=$d1['uid'];
    }else{
    $user_id = $d1['user_id'][$k];
    }

        // $ch_date = $d1['date'][$k];
        
        $amount=0;
        $remaining_stock_val=array();

        foreach ($d1['product_id'][$k] as $key => $value)
        {

        $prod = $d1['product_id'][$k][$key];
        $bill_of_supply = $d1['bos'][$k][$key];
        $rate = $d1['rate'][$k][$key];
        $taxId = $d1['vat'][$k][$key];
        $vat_amt = $d1['vat_amt'][$k][$key];
        $qty1 = $d1['quantity'][$k][$key];
        $schqty1 = $d1['scheme'][$k][$key];
        $aval_stock = $d1['aval_stock'][$k][$key];
        $free_stock= $d1['free_stock'][$k][$key];

        if(!empty($d1['cd'][$k][$key]) || $d1['cd'][$k][$key] > 0)
        {
            $cd = $d1['cd'][$k][$key];
        }
        else {
            $cd = 0.00;
        }


        $mrp = $d1['mrp'][$k][$key];
        $cd_type = $d1['cd_type'][$k][$key];

        if(!empty($d1['cd_amt'][$k][$key]) || $d1['cd_amt'][$k][$key] > 0)
        {
            $cd_amt = $d1['cd_amt'][$k][$key];
        }
        else {
            $cd_amt = 0.00;
        }

        $dis_type = $d1['trade_disc_type'][$k][$key];

        if(!empty($d1['trade_disc_amt'][$k][$key]) || $d1['trade_disc_amt'][$k][$key] > 0)
        {
            $dis_amt = $d1['trade_disc_amt'][$k][$key];
        }
        else {
            $dis_amt = 0.00;
        }

        if(!empty($d1['trade_disc_val'][$k][$key]) || $d1['trade_disc_val'][$k][$key] > 0)
        {
            $dis_percent = $d1['trade_disc_val'][$k][$key];
        }
        else {
            $dis_percent = 0.00;
        }


        $taxable_amt = $d1['amount'][$k][$key];

        /* For calculation of final amount */
        $tax_amt = $taxable_amt;

        $checkstatus = 0;

        if($aval_stock<$qty1)
        {
            if($aval_stock>0)
            {
                $checkstatus = $checkstatus+1;
                $product_status=3;

                $remain = $qty1-$aval_stock;
                $qty=$aval_stock;
                $taxable_amt1 = $qty*$rate;
                
                if($cd_type==1)
                {
                    $cd_amt = $taxable_amt1*$cd/100;
                }else{
                    $cd_amt = $cd;
                }

                if($dis_type==1)
                {
                    $dis_amt = $taxable_amt1*$dis_percent/100;
                }else{
                    $dis_amt = $dis_percent;
                }

                $taxable_amt2 = $taxable_amt1-$cd_amt-$dis_amt;
                $vat_amt = $taxable_amt2*$taxId/100;
                $taxable_amt = $taxable_amt2+$vat_amt;


                /* Calculation of final amount of a particular product if ordered stock is less then avl stock (amount-remaining_amount) */

                $remaining_stock_val[] = $tax_amt-$taxable_amt;

                #######################################################################            

                $update_usod="UPDATE `user_sales_order_details` SET status=$product_status, `remaining_qty` = '$remain' WHERE `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
                mysqli_query($dbc, $update_usod);

                $update_stock_q="UPDATE `stock` SET qty=0 WHERE `product_id`=$prod AND `dealer_id`=$dealer_id AND mrp=$mrp";

                mysqli_query($dbc, $update_stock_q);                
            }else{
                $checkstatus = 2;
                $remaining_stock_val[] = $taxable_amt;
                $update="UPDATE `user_sales_order_details` SET status=0 WHERE `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
                mysqli_query($dbc, $update);
            }

        }else{
        $qty = $qty1;
        $product_status=2;
        // $update="UPDATE `user_sales_order_details` SET status=$product_status WHERE `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
        $update="UPDATE `user_sales_order_details` SET status=$product_status WHERE `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";

        mysqli_query($dbc, $update);

        /*<puneet>*/

        $new_stock_qty = $aval_stock-$qty1;
        $update_stock_q="UPDATE `stock` SET qty=$new_stock_qty WHERE `product_id`=$prod AND `dealer_id`=$dealer_id AND mrp=$mrp";

        mysqli_query($dbc, $update_stock_q);

        /*</puneet>*/

        }

        if($free_stock< $schqty1)
        {
        $remain_free = $schqty1 -$free_stock;
        $schqty =$free_stock;
        $update1="UPDATE `user_sales_order_details` SET `remaining_free` = '$remain_free' WHERE `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
        // h1($update);
        mysqli_query($dbc, $update1) ;
        } else
        {
            $schqty = $schqty1;
        }

        
        if($checkstatus==0)
        {
            /* Order complete */
            $update1="UPDATE `user_sales_order` SET `order_status` = '1' WHERE `order_id` ='$uso_order_id' ";
            mysqli_query($dbc, $update1) ;
        }elseif($checkstatus==1){
            /* updating user_sale_order if there is some remaining qty in the order */
            $update1="UPDATE `user_sales_order` SET `order_status` = '7' WHERE `order_id` ='$uso_order_id' ";
            mysqli_query($dbc, $update1) ;
        }elseif($checkstatus==2){
            /* updating user_sale_order if available stock is 0 */
            $update1="UPDATE `user_sales_order` SET `order_status` = '0' WHERE `order_id` ='$uso_order_id' ";
            mysqli_query($dbc, $update1) ;
        }


        $qtyy= $qty+$schqty;

        $new_ch_id = number_format(($iddd+$i),0,'','');
        ////////////// CHECK AVAL_STOCK >= FILLED_QUANTITY ///////////
        // if($aval_stock>=$qty1)
        if($checkstatus!=2)
        {
        $unique_order_ids[$k] = $new_ch_id;
        $store[] = "('$new_ch_id','$prod','$bill_of_supply','0','','$taxId','$vat_amt','$qty', '$rate','$schqty'
        ,'$uso_order_id','$user_id','$mrp','$cd','$cd_type','$cd_amt','$dis_type',
        '$dis_amt','$dis_percent','$taxable_amt','$taxable_amt')";

        $amount=$amount+$taxable_amt;

        /*$sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$prod' order by `mfg` asc";

        $rs1=mysqli_query($dbc,$sq);

            while($row = mysqli_fetch_assoc($rs1))
            {
                $remaining_qty = $row['remaining'];
                $product_id = $row['product_id'];
                $id = $row['id'];

                if($qtyy >= $remaining_qty){
                $qtyy = $qtyy - $remaining_qty;
                $balqty = 0;
                $q = "UPDATE stock SET remaining = '$balqty' WHERE id='$id'";
                $r = mysqli_query($dbc, $q);
                                }else{
                $balqty = $remaining_qty - $qtyy;
                $q = "UPDATE stock SET remaining = '$balqty' WHERE id='$id'";
                $r = mysqli_query($dbc, $q);
               
                }
            }*/
        }

        ///////////////////////////////////CHECK DISCOUNTS//////////////////////////////////////////
        /*$cdr = "SELECT * FROM `retailer_cd` where `product_id`='$prod' AND retailer_id='$retailer_id'";
        $cdrms=mysqli_query($dbc,$cdr);
        if(mysqli_num_rows($cdrms)>0)
        {
        $cdrow = mysqli_fetch_assoc($cdrms);
        $cdtype = $cdrow['cd_type'];
        $cd1 = $cdrow['cd'];
        $updatecd = "UPDATE `retailer_cd` SET `cd_type`='$cd_type',`cd`='$cd' WHERE `product_id`='$prod' AND retailer_id='$retailer_id'";
        $updatecdm = mysqli_query($dbc,$updatecd);
        }
        else {
        $cdtype = '';
        $cd1 = '0';
        $insertcd = "INSERT INTO `retailer_cd` (`retailer_id`, `product_id`, `cd_type`, `cd`)
        values ('$retailer_id','$prod','$cd_type','$cd')";
        $insertcdm = mysqli_query($dbc,$insertcd);

        }*/

        /*$tdr = "SELECT * FROM `retailer_trade` where `product_id`='$prod' AND retailer_id='$retailer_id'";
        
        $tdrms=mysqli_query($dbc,$tdr);
        if(mysqli_num_rows($tdrms)>0)
        {
        $tdrow = mysqli_fetch_assoc($tdrms);
        $tradetype = $tdrow['trade_type'];
        $trade = $tdrow['trade_disc'];
        $updatetr = "UPDATE `retailer_trade` SET `trade_type`='$dis_type',`trade_disc`='$dis_percent' WHERE `product_id`='$prod' AND retailer_id='$retailer_id'";
        $updatetrm = mysqli_query($dbc,$updatetr);
        }
        else {
        $tradetype = '';
        $trade = '0';
        $inserttrade = "INSERT INTO `retailer_trade`(`retailer_id`, `product_id`, `trade_type`, `trade_disc`) VALUES
        ('$retailer_id','$prod','$dis_type','$dis_percent') ";
        $inserttrm = mysqli_query($dbc, $inserttrade);
        }*/
    }

    

// $unique_order_ids[] = $new_ch_id;
$d = date('Y-m-d');
// $sch = "SELECT value,value_to,scheme_gift FROM scheme_on_sale_details INNER JOIN scheme_on_sale ON scheme_on_sale.scheme_id =
// scheme_on_sale_details.scheme_id WHERE scheme_on_sale.start_date <='$d' AND scheme_on_sale.end_date>='$d'";
// //h1($sch);exit;
// $sch_q = mysqli_query($dbc, $sch);
// while($sch_row = mysqli_fetch_assoc($sch_q))
// {
// $schvalue = $sch_row['value'];
// $schvalueto = $sch_row['value_to'];
// $sch_gift = $sch_row['scheme_gift'];
// if($amount>=$schvalue && $amount<=$schvalueto)
// {
// $gift = $sch_gift;
// }
// }
///$disc_amt = ($amount*$gift)/100;

/*if($checkstatus==0)
{
$amount1 = $amount - $disc_amt;
}
else
{
$amount1 = $amount;
}*/

if(!empty($remaining_stock_val))
{
    $adjusted_amt = $sub_total-array_sum($remaining_stock_val);
    $disc_amount  = $adjusted_amt*$discount_per/100;
    $amount1      = $adjusted_amt-$disc_amount;
}else{
   $amount1 = $disc_amt1;
   $disc_amount = $discounted_amt;
}

$remark = $d1['remark'];

$str1[] = "($new_ch_id,'$ch_no','$user_id','$dealer_id','$retailer_id','$ch_uid','$ch_date',NOW(),'1','0','0','$remark','1','2','0','$discount_per','$disc_amount','$amount1','$amount1')";
$i++;
}

if(count($unique_order_ids)>0)
{
//***********************INSERT INTO challan_order*********************
$str2 = implode(',', $str1);
// print_r($str2);exit;
$query ="INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`,
`ch_dealer_id`, `ch_retailer_id`,`ch_user_id`, `ch_date`, `date_added`, `company_id`, `dispatch_status`,
`sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`,`discount_per`,`discount_amt`,`amount`,`remaining`) VALUES $str2";
// h1($query);
$run = mysqli_query($dbc, $query);
if (!$run) {
mysqli_rollback($dbc);
return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
}
//***********************INSERT INTO challan_order_details*********************
$rId = mysqli_insert_id($dbc);
}
//echo $rId;exit();
if(count($unique_order_ids)>0)
{
$stored = implode(',',$store);
$qcod = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, `supply_status`,
`catalog_details_id`, `batch_no`, `tax`,`vat_amt`, `qty`, `product_rate`,
`free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`,
`dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, `remain_amount`) VALUES $stored";
$r = mysqli_query($dbc, $qcod);
// h1($qcod); die;

if (!$r) {
mysqli_rollback($dbc);
// return array('status' => true, 'myreason' => '<strong>'.$d1['what'] . ' successfully Saved <br/> Discount % ='.$gift.
// '% &nbsp; | &nbsp; Discount Amount = '.round($disc_amt,2).'<br><br> Total Amount = '.$amount1.'</strong>', 'rId' => $rId);
//
// return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
}
}
//**************************Update user_sales_order_details status***************************
// foreach ($d1['order_id'] as $k1 => $val1) {
// foreach ($d1['product_id'][$k1] as $key2 => $value2) {
// $order_id=$d1['order_id'][$k1][$key2];
// $qusod = "update user_sales_order_details set status=$product_status where order_id = '$order_id' AND product_id = '$value2' ";
// // h1($q);
// $res3 = mysqli_query($dbc, $qusod);
// }
// }
// ///******************************************** End Update user_sales_order_details status******
// if (!empty($d1['product_id_ex'])) {
// foreach ($d1['product_id_ex'] as $key => $value) {
// if(!empty($d1['order_id_ex'][$key])){
// $qusodx = "update user_sales_order_details set status=$product_status where order_id = '{$d1[order_id_ex][$key]}' AND product_id = '$value' ";
// $res3 = mysqli_query($dbc, $qusodx);
// if(!$res3){
// mysqli_rollback($dbc);
// return array('status' => false, 'myreason' => 'Sales order can not be updated succesfully.');
// }else{
// if($d1['company_id']==1){
// write_query($q);
// }
// }
// }
// }
// }




//***********************New product add/*********************
//
// $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
// if(!empty($d1['product_id_ex']) && $d1['product_id_ex'] !=''){
// foreach ($d1['product_id_ex'] as $key => $value) {
// if(!empty($value)){
// $prod = $d1['product_id_ex'][$key];
// $rate = $d1['rate_ex'][$key];
// $taxId = $d1['vat_ex'][$key];
// $qty = $d1['quantity_ex'][$key];
// $schqty = $d1['scheme_ex'][$key];
// $cd = $d1['cd_ex'][$key];
// $mrp = $d1['mrp_ex'][$key];
// $cd_type = $d1['cd_type_ex'][$key];
// $batch_no = $d1['batch_no'][$key];
// $str_ex[] = "($nid,'$uid','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$batch_no')";
// $q="UPDATE balance_stock SET quantity='$qty' WHERE product_id='$prod' AND dealer_id='$dealer_id'";
// $run=mysqli_query($dbc, $q);
//
// $nid++;
// }
// }
// $str_ex = implode(',', $str_ex);
// if(!empty($str_ex)){
// $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`batch_no`) VALUES $str_ex";
// $r = mysqli_query($dbc, $q);
// if (!$r) {
// mysqli_rollback($dbc);
// return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
// }
//
// }
// }

if(count($unique_order_ids)>0)
{
    $order_ids = implode('-',$unique_order_ids); 
    $txt = ' successfully Saved';   
}else{
    $txt = ' Not Saved.Please update your stock.';   
    $order_ids = '';
}
mysqli_commit($dbc);
//Final success
//echo ;
// header("Location:index.php?option=make-challan&showmode=1&mode=1&actiontype=print&id=-1252170411181515");

return array('status' => true, 'myreason' => '<strong>'.$d1['what'] . $txt.' </strong>', 'rId' => $rId,'invoice_url'=>$order_ids);

}

/////////////////////////////END MULTI CHALLAN///////////////////    
    
    
    
 /////////////////////////////////////////////////////////////////////////////////////////////////////////   
//    public function multi_challan_save() {
//        global $dbc;
//        
//        $out = array('status' => 'false', 'myreason' => '');
//        list($status, $d1) = $this->get_challan_se_data();
//        if (!$status)
//            return array('status' => false, 'myreason' => $d1['myreason']);
//        $orderno = date('YmdHis');
//        $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false);
//        $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
//        $i = 1;
//       
//        $iddd = $d1['dealer_id'].date('ymdHis');
//        $id = $d1['dealer_id'].date('ymdHis');
//       //pre($d1);exit;
//        mysqli_query($dbc, "START TRANSACTION");
//        $uid= $id;
//          
//          foreach ($d1['order_id'] as $k => $val) {
//             //pre($val);
//                $ch_no = $d1['ch_no'][$k];
//                $dealer_id = $d1['dealer_id'];
//                
//                $retailer_id = $d1['retailer_id'][$k];
//                $uso_order_id=$d1['uso_order_id'][$k];
//                $disc_amt = $d1['discount'][$k];
//                
//                if(empty($d1['user_id'][$k])){
//                    $user_id=$d1['uid'];
//                }else{
//                     $user_id = $d1['user_id'][$k];
//                }
//                $ch_date = $d1['date'][$k];
//                
//                //pre($d1['product_id']);exit;
//                $amount=0;
//                foreach ($d1['product_id'][$k] as $key => $value) {
//                  //pre($value);exit;
//             
//                $prod = $d1['product_id'][$k][$key];
//                $rate = $d1['rate'][$k][$key];
//                $gst_tax = $d1['vat'][$k][$key];
//                $gst_amt = $d1['vat_amt'][$k][$key];
//                $taxId = $d1['vat'][$k][$key];
//                $qty1 = $d1['quantity'][$k][$key];
//                $schqty1 = $d1['scheme'][$k][$key];
//                $aval_stock= $d1['aval_stock'][$k][$key];
//                $free_stock= $d1['free_stock'][$k][$key];
//                
//                if(!empty($d1['cd'][$k][$key]) || $d1['cd'][$k][$key] > 0)
//                {
//                  $cd = $d1['cd'][$k][$key];  
//                }
//                else {
//                    $cd = 0.00;
//                }
//                $mrp = $d1['mrp'][$k][$key];
//                $cd_type = $d1['cd_type'][$k][$key];
//                
//                  if(!empty($d1['cd_amt'][$k][$key]) || $d1['cd_amt'][$k][$key] > 0)
//                {
//                 $cd_amt = $d1['cd_amt'][$k][$key]; 
//                }
//                else {
//                    $cd_amt = 0.00;
//                }
//                $dis_type = $d1['trade_disc_type'][$k][$key];
//           
//                if(!empty($d1['trade_disc_amt'][$k][$key]) || $d1['trade_disc_amt'][$k][$key] > 0)
//                {
//                      $dis_amt = $d1['trade_disc_amt'][$k][$key];
//                }
//                else {
//                    $dis_amt = 0.00;
//                }
//                
//                if(!empty($d1['trade_disc_val'][$k][$key]) || $d1['trade_disc_val'][$k][$key] > 0)
//                {
//                    $dis_percent = $d1['trade_disc_val'][$k][$key];
//                }
//                else {
//                    $dis_percent = 0.00;
//                }
//                $taxable_amt = $d1['amount'][$k][$key];
//               
//               $checkstatus = 0;
//                if($aval_stock<$qty1)
//                {
//                    $checkstatus = $checkstatus+1;
//                    $remain = $qty1-$aval_stock;
//                    $qty=$aval_stock;
//                    $product_status=0;
//                    $update="UPDATE  `user_sales_order_details` SET status=$product_status,  `remaining` =  '$remain' WHERE  `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
//                     //h1($update);
//                    mysqli_query($dbc, $update) ;
//                }else
//                {
//                    $qty = $qty1;
//                    $product_status=2;
//                    $update="UPDATE  `user_sales_order_details` SET status=$product_status WHERE  `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
//                     //h1($update);
//                    mysqli_query($dbc, $update) ;
//                }
//                 
//                 if($free_stock< $schqty1)
//                {
//                    $remain_free =  $schqty1 -$free_stock;
//                    $schqty =$free_stock;
//                    $update1="UPDATE  `user_sales_order_details` SET  `remaining_free` =  '$remain_free' WHERE  `user_sales_order_details`.`order_id` =$uso_order_id AND `user_sales_order_details`.`product_id`=$prod";
//                    // h1($update);
//                    mysqli_query($dbc, $update1) ;
//                } else
//                {
//                    $schqty = $schqty1;
//                }
//                if($checkstatus==0)
//                {
//                 $update1="UPDATE  `user_sales_order` SET  `order_status` =  '1' WHERE `order_id` ='$uso_order_id' ";
//                 mysqli_query($dbc, $update1) ;   
//                }
//                $qtyy= $qty+$schqty; 
//            ///////////////////////CHECK AVAL_STOCK >= FILLED_QUANTITY///////////////////////
//                 if($aval_stock>=$qty1)
//                 {
//                 $store[] = "('$iddd','$prod','0','','$taxId','$qty', '$rate','$schqty'
//                  ,'$uso_order_id','$user_id','$mrp','$cd','$cd_type','$cd_amt','$dis_type',
//                        '$dis_amt','$dis_percent','$taxable_amt','$taxable_amt','$gst_tax','$gst_amt')";
//                 
//                 $amount=$amount+$taxable_amt;
//                                  
//                  $sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$prod' order by `mfg` asc"; 
//              
//                $rs1=mysqli_query($dbc,$sq);  
//                
//                while($row = mysqli_fetch_assoc($rs1))
//                {
//                     $remaining_qty = $row['remaining'];
//                     $product_id = $row['product_id'];
//                     $id = $row['id'];  
//                      
//                    if($qtyy >= $remaining_qty){
//                          $qtyy =  $qtyy - $remaining_qty;
//                          $balqty = 0;
//                        $q = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
//                        $r = mysqli_query($dbc, $q);
//                        // $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$remaining_qty')";
//                      //  $rsrt = mysqli_query($dbc, $qsrt);
//                     //   if (!$rsrt)
//                      //      return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
//                    }else{
//                        $balqty =  $remaining_qty - $qtyy; 
//                       // $baltemp = $qty - $remaining_qty;
//                        $q = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
//                        $r = mysqli_query($dbc, $q);   
//                       //  $qsrt = "INSERT INTO `stock_remaining_temp` (`stock_id`,`product_id`,`order_id`,`qty`)  VALUES ('$id','$product_id','$order_id','$balqty')";
//                       // $rsrt = mysqli_query($dbc, $qsrt);
//                     //   if (!$rsrt)
//                     //       return array('status' => FALSE, 'myreason' => 'stock remaining temp could not be saved');
//                    }
//                    
//                }
//                     
//                   
//                 }
//                 
//                 ///////////////////////////////////CHECK DISCOUNTS//////////////////////////////////////////
//               $cdr = "SELECT * FROM `retailer_cd` where `product_id`='$prod' AND retailer_id='$retailer_id'"; 
//               $cdrms=mysqli_query($dbc,$cdr);
//               if(mysqli_num_rows($cdrms)>0)
//               {
//                   $cdrow = mysqli_fetch_assoc($cdrms);
//                   $cdtype = $cdrow['cd_type'];
//                   $cd1 = $cdrow['cd'];
//                  $updatecd = "UPDATE `retailer_cd` SET `cd_type`='$cd_type',`cd`='$cd' WHERE `product_id`='$prod' AND retailer_id='$retailer_id'";
//                  $updatecdm = mysqli_query($dbc,$updatecd);
//                  }
//                else {
//                        $cdtype = '';
//                        $cd1 = '0';
//                   $insertcd = "INSERT INTO `retailer_cd` (`retailer_id`, `product_id`, `cd_type`, `cd`)
//                       values ('$retailer_id','$prod','$cd_type','$cd')";    
//                   $insertcdm = mysqli_query($dbc,$insertcd);
//                        
//                    }
//                    
//                $tdr = "SELECT * FROM `retailer_trade` where `product_id`='$prod' AND retailer_id='$retailer_id'"; 
//              // h1($tdr);
//                $tdrms=mysqli_query($dbc,$tdr);
//               if(mysqli_num_rows($tdrms)>0)
//               {
//                   $tdrow = mysqli_fetch_assoc($tdrms);
//              $tradetype = $tdrow['trade_type'];
//                   $trade = $tdrow['trade_disc'];
//              $updatetr = "UPDATE `retailer_trade` SET `trade_type`='$dis_type',`trade_disc`='$dis_percent' WHERE `product_id`='$prod' AND retailer_id='$retailer_id'";
//                 $updatetrm  = mysqli_query($dbc,$updatetr);  
//               }
//                else {
//                        $tradetype = '';
//                        $trade = '0';
//             $inserttrade = "INSERT INTO `retailer_trade`(`retailer_id`, `product_id`, `trade_type`, `trade_disc`) VALUES
//                       ('$retailer_id','$prod','$dis_type','$dis_percent') ";
//             $inserttrm = mysqli_query($dbc, $inserttrade);
//                    }
//               
//               
//                
//                }
//                
//                 $i++;
//            } 
//             
//             $d = date('Y-m-d');
////            $sch = "SELECT value,value_to,scheme_gift FROM scheme_on_sale_details INNER JOIN scheme_on_sale ON scheme_on_sale.scheme_id = 
////                scheme_on_sale_details.scheme_id WHERE scheme_on_sale.start_date <='$d' AND scheme_on_sale.end_date>='$d'";
////            //h1($sch);exit;
////            $sch_q = mysqli_query($dbc, $sch);
////            while($sch_row = mysqli_fetch_assoc($sch_q))
////            {
////               $schvalue = $sch_row['value'];
////               $schvalueto = $sch_row['value_to'];
////               $sch_gift = $sch_row['scheme_gift'];
////               if($amount>=$schvalue && $amount<=$schvalueto)
////               {
////                  $gift = $sch_gift; 
////               }
////            }
//            ///$disc_amt = ($amount*$gift)/100;
//              if($checkstatus==0)
//                {
//            $amount1 = $amount - $disc_amt;
//                }
//                else
//                {
//                   $amount1 = $amount; 
//                }
//                 $remark = $d1['remark'];
//                $str1[] = "($iddd,'$ch_no','$user_id','$dealer_id',
//                    '$retailer_id','$ch_date','1','0',
//                       '0','$remark','1','2','0','0','$disc_amt','$amount1','$amount1')";
//            
//             //***********************INSERT INTO challan_order*********************
//          $str2 = implode(',', $str1);
//         // print_r($str2);exit;
//          $query ="INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`,
//              `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `dispatch_status`, 
//              `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`,`discount_per`,`discount_amt`,`amount`,`remaining`) VALUES $str2";
//        // h1($query); exit;
//           $run = mysqli_query($dbc, $query);
//          if (!$run) {
//            mysqli_rollback($dbc);
//            return array('status' => false, 'myreason' => 'Challan Could Not saved, Some error occurred');
//        }
//          //***********************INSERT INTO challan_order_details*********************
//        $rId = mysqli_insert_id($dbc);
//        //echo $rId;exit();
//          $stored = implode(',',$store);
//           $qcod = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, 
//                `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, 
//                `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, 
//                `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, `remain_amount`,`tax`,`vat_amt`) VALUES $stored";
//          // h1($qcod); exit;
//            $r = mysqli_query($dbc, $qcod);
//              
//            if (!$r) {
//                mysqli_rollback($dbc);
////              return array('status' => true, 'myreason' => '<strong>'.$d1['what'] . ' successfully Saved <br/> Discount % ='.$gift.
////            '% &nbsp; | &nbsp; Discount Amount = '.round($disc_amt,2).'<br><br> Total Amount = '.$amount1.'</strong>', 'rId' => $rId);
////      
//             //   return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
//            }   
//            
//         //**************************Update user_sales_order_details status***************************
////         foreach ($d1['order_id'] as $k1 => $val1) {
////           foreach ($d1['product_id'][$k1] as $key2 => $value2) {
////           $order_id=$d1['order_id'][$k1][$key2];
////           $qusod = "update user_sales_order_details set status=$product_status where order_id = '$order_id' AND product_id = '$value2'  ";
////         //  h1($q);
////          $res3 = mysqli_query($dbc, $qusod);     
////       }
////    }
////       ///******************************************** End Update user_sales_order_details status******
////        if (!empty($d1['product_id_ex'])) {           
////            foreach ($d1['product_id_ex'] as $key => $value) {
////                if(!empty($d1['order_id_ex'][$key])){
////       $qusodx = "update user_sales_order_details set status=$product_status where order_id = '{$d1[order_id_ex][$key]}' AND product_id = '$value'  ";
////               $res3 = mysqli_query($dbc, $qusodx);
////               if(!$res3){
////                    mysqli_rollback($dbc);
////                return array('status' => false, 'myreason' => 'Sales order can not be updated succesfully.');
////               }else{
////                     if($d1['company_id']==1){
////           write_query($q);
////        }
////               }
////                }
////            }
////        }   
//            
//            
//            
//            
//            //***********************New product add/*********************
////            
////        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
////            if(!empty($d1['product_id_ex']) && $d1['product_id_ex'] !=''){
////            foreach ($d1['product_id_ex'] as $key => $value) {
////                if(!empty($value)){
////                $prod = $d1['product_id_ex'][$key];
////                $rate = $d1['rate_ex'][$key];
////                $taxId = $d1['vat_ex'][$key];
////                $qty = $d1['quantity_ex'][$key];
////                $schqty = $d1['scheme_ex'][$key];
////                $cd = $d1['cd_ex'][$key];
////                $mrp = $d1['mrp_ex'][$key];
////                $cd_type = $d1['cd_type_ex'][$key];
////                $batch_no = $d1['batch_no'][$key];
////                $str_ex[] = "($nid,'$uid','$prod','$qty', '$rate','$schqty','$taxId','$mrp','$cd','$cd_type','$batch_no')";
////                $q="UPDATE balance_stock SET quantity='$qty' WHERE product_id='$prod' AND dealer_id='$dealer_id'";
////                $run=mysqli_query($dbc, $q);
////                
////                $nid++;
////            }
////            }
////            $str_ex = implode(',', $str_ex);
////            if(!empty($str_ex)){
////            $q = "INSERT INTO challan_order_details (`id`,`ch_id`,`product_id`,`qty`,`product_rate`, `free_qty`,`tax`,`mrp`,`cd`,`cd_type`,`batch_no`) VALUES $str_ex";
////             $r = mysqli_query($dbc, $q);
////            if (!$r) {
////                mysqli_rollback($dbc);
////                return array('status' => false, 'myreason' => 'DSP items can not be added succesfully.');
////            }
////         
////            }
////            }
//
//       
//        mysqli_commit($dbc);
//        //Final success
//        //echo ;
//   // header("Location:index.php?option=make-challan&showmode=1&mode=1&actiontype=print&id=-1252170411181515"); 
//        return array('status' => true, 'myreason' => '<strong>'.$d1['what'] . ' successfully Saved <br/> Discount % ='.$gift.
//            '% &nbsp; | &nbsp; Discount Amount = '.round($disc_amt,2).'<br><br> Total Amount = '.round($amount1,2).'</strong>', 'rId' => $rId);
//   
//    }
//    
    /////////////////////////////END MULTI CHALLAN///////////////////
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
    ///////////////////////////////////////???CLAIM CHALLAN////////////////////////////////////////
    
     public function get_claim_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
       //echo "ANKUSH";exit;
        $out = array();
        //pre($filter);
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT sum(`taxable_amt`)as amt FROM `challan_order_details` INNER JOIN challan_order ON challan_order.id = challan_order_details.`ch_id` $filterstr ";
      //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['ch_id'];
           // $out[$id] = $row; // storing the item id
            $out[$id]['amt'] = $row['amt'];
           
            }// while($row = mysqli_fetch_assoc($rs)){ ends
       //pre($out);
        return $out;
    }
    
    public function get_claim_target_list() {
        global $dbc;
       //echo "ANKUSH";exit;
        $out = array();
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        //pre($filter);
       // $filterstr = $this->oo_filter($filter, $records, $orderby);
       // $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT challan_order.id as cid,istarget_claim,`ch_dealer_id`,DATE_FORMAT(`ch_date`,'%M-%Y') as myear,DATE_FORMAT(`ch_date`,'%m') as month,DATE_FORMAT(`ch_date`,'%Y-%m') as my,SUM(taxable_amt) as sale FROM `challan_order` INNER JOIN `challan_order_details` cod ON cod.ch_id=challan_order.id WHERE ch_dealer_id='$dealer_id' GROUP BY `ch_dealer_id`,myear ASC";
      //  h1($q);
        $rs = mysqli_query($dbc,$q);
        
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['cid'];
           $out[$id]['ch_dealer_id'] = $row['ch_dealer_id'];
            $out[$id]['myear'] = $row['myear'];
            $month = $row['month'];
             $out[$id]['month'] = $this->month($month);
             $out[$id]['my'] = $row['my'];
             $out[$id]['sale'] = $row['sale'];
             $out[$id]['istarget_claim'] = $row['istarget_claim'];
            }// while($row = mysqli_fetch_assoc($rs)){ ends
       //pre($out);
        return $out;
    }
    public function get_claim_retailer_list() {
        global $dbc;
        $out = array();
        $a = array();
        $ch = array();
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        //pre($filter);
       // $filterstr = $this->oo_filter($filter, $records, $orderby);
       // $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
      //  $q = "SELECT ch_retailer_id,challan_order.id as cid,istarget_claim,`ch_dealer_id`,DATE_FORMAT(`ch_date`,'%M-%Y') as myear,DATE_FORMAT(`ch_date`,'%m') as month,DATE_FORMAT(`ch_date`,'%Y-%m') as my,SUM(taxable_amt) as sale FROM `challan_order` INNER JOIN `challan_order_details` cod ON cod.ch_id=challan_order.id WHERE ch_dealer_id='$dealer_id' GROUP BY `ch_retailer_id` ASC";
      
       $q ="SELECT name,id FROM `retailer` WHERE `dealer_id`=$dealer_id";
       //h1($q);
       $rs = mysqli_query($dbc,$q);
       //$rs = mysqli_query($dbc,$qq);
        
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
           // $out[$id]['rid'] = $row['name'];
            $out[$id]['retailer_name'] = $row['name'];
            $ch[$id]['challan'] = $this->get_challan($id);
            $out[$id]['sale'] =  $ch[$id]['challan']['sale'];
            $out[$id]['ch_dealer_id'] =  $ch[$id]['challan']['ch_dealer_id'];
         
            $a[$id]['scheme'] = $this->current_scheme($out[$id]['sale']);
            $out[$id]['value'] =  $a[$id]['scheme']['value'];
            $out[$id]['value_to'] =  $a[$id]['scheme']['value_to'];
            $out[$id]['scheme_gift'] =  $a[$id]['scheme']['scheme_gift'];
            $out[$id]['start_date'] =  $a[$id]['scheme']['start_date'];
            $out[$id]['end_date'] =  $a[$id]['scheme']['end_date'];
            }// while($row = mysqli_fetch_assoc($rs)){ ends
     // pre($out);
        return $out;
    }
    
    public function get_challan($rid)
    {
        global $dbc;
        $q =mysqli_query($dbc,"SELECT ch_retailer_id,challan_order.id as cid,istarget_claim,`ch_dealer_id`,DATE_FORMAT(`ch_date`,'%M-%Y') as myear,DATE_FORMAT(`ch_date`,'%m') as month,DATE_FORMAT(`ch_date`,'%Y-%m') as my,SUM(taxable_amt) as sale FROM `challan_order` INNER JOIN `challan_order_details` cod ON cod.ch_id=challan_order.id WHERE ch_retailer_id='$rid' GROUP BY `ch_retailer_id` ASC");
        $row = mysqli_fetch_assoc($q);
        return $row;
    }
    
     public function current_scheme($sale) {
        $state = $_SESSION[SESS.'data']['state_id'];
        global $dbc;   
        $date = date('Y-m-d');
        $q = "SELECT svp.id,svp.value,svp.value_to,svp.scheme_gift,svpd.start_date,svpd.end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where '$date' BETWEEN svpd.start_date AND svpd.end_date AND user = 3 AND state_id = $state AND '$sale' BETWEEN svp.value AND svp.value_to";
      // h1($q);
        $r = mysqli_query($dbc, $q);
      $row = mysqli_fetch_assoc($r);
        //pre($row);
        return $row;
    }
        public function month($month)
    {
        if($month=='1')
        return 'jan';
        if($month=='2')
        return 'feb';
        if($month=='3')
        return 'march';
        if($month=='4')
        return 'april';
        if($month=='5')
        return 'may';
        if($month=='6')
        return 'june';
        if($month=='7')
        return 'july';
        if($month=='8')
        return 'august';
        if($month=='9')
        return 'sept';
        if($month=='10')
        return 'oct';
        if($month=='11')
        return 'nov';
        if($month=='12')
        return 'december';
    }

      public function get_claim_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Dealer Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function claim_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_claim_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
//pre($d1);exit;
            $amt = $d1['amt'];
            $gift = $d1['gift'];
        if(strpos($gift, '%' ) !== false)
        {
            $g1 = explode("%", $gift);
           // echo $g1[0]; 
            $amt = ($amt*$g1[0])/100;
        }
          
            $fdate = get_mysql_date($d1['from_date']);
            $tdate = get_mysql_date($d1['to_date']);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `claim_challan`(`dealer_id`, `from_date`, `to_date`, `claim_amount`, `claim`,`total_amt`) VALUES 
           ('$d1[dealer_id]','$fdate','$tdate','$d1[amt]','$d1[gift]','$amt')";
//h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
      
       $q = "UPDATE challan_order SET  isclaim = '1' WHERE ch_dealer_id = '$d1[dealer_id]' AND DATE_FORMAT(ch_date,'%Y-%m-%d') BETWEEN '$fdate' AND '$tdate'";
        $r = mysqli_query($dbc, $q);
        mysqli_commit($dbc);
        //Final success 
        return array('status' => TRUE, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
    
      public function get_claim_desk_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Claim Generated'; //whether to do history log or not
        return array(true, $d1);
    }

    public function claim_desk_save() {
        global $dbc;
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_claim_desk_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
//pre($d1);exit;
      // echo "ABC"; exit;
            $amt = $d1['achieved'];
            $gift = $d1['scheme_gift'];
        if(strpos($gift, '%' ) !== false)
        {
            $g1 = explode("%", $gift);
           // echo $g1[0]; 
            $amt = ($amt*$g1[0])/100;
        }
    else {
            $amt = '0';
         }
          
            $fdate = $d1['start'];
            $tdate = $d1['end'];
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `claim_challan`(`dealer_id`, `from_date`, `to_date`, `claim_amount`, `claim`,`total_amt`) VALUES 
           ('$dealer_id','$fdate','$tdate','$d1[achieved]','$d1[scheme_gift]','$amt')";
//h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
      
       $q = "UPDATE user_primary_sales_order SET  is_claim = '1' WHERE dealer_id = '$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d') BETWEEN '$fdate' AND '$tdate'";
        h1($q);
       $r = mysqli_query($dbc, $q);
        mysqli_commit($dbc);
        //Final success 
        
        return array('status' => TRUE, 'myreason' => $d1['what'] . ' successfully', 'rId' => $rId);
    }
    
     public function get_dealer_claim_list() {
           global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT id,(select name from dealer where id=claim_challan.dealer_id) as dealer_name,claim_amount,claim,DATE_FORMAT(claim_date,'%d-%m-%Y') as claim_date,total_amt,status FROM claim_challan";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; 
            $out[$id]['dealer_name'] = $row['dealer_name'];
            $out[$id]['claim_amount'] = $row['claim_amount'];
            $out[$id]['claim'] = $row['claim'];
            $out[$id]['claim_date'] = $row['claim_date'];
            $out[$id]['total_amt'] = $row['total_amt'];
            $out[$id]['status'] = $row['status'];
        }
       // print_r($out);
        return $out;
    }

    
    //////////////////////////////////////////////////////////////////////////////////////////////////


    public function get_mrp_list($pid,$did)
    {
        global $dbc;
        $qry = "SELECT CAST(mrp as decimal(10,2)) as mrp FROM stock WHERE product_id=$pid AND dealer_id=$did";
        $qry_e = mysqli_query($dbc,$qry);
        $data = array();

        if($qry_e)
        {
            while($row=mysqli_fetch_assoc($qry_e))
            {
                $data[] = $row['mrp'];
            }
        }

        return $data;
    }



    /*****************************************************************************
    Naveen:15112017:Case get_dealer_landing_rate on taxtable OR non-taxable products ********************************************************************************/

        public function get_dealer_landing_rate($product_id)
        {   
            global $dbc;
            $location_id = (isset($_SESSION[SESS . 'data']['state_id']))? $_SESSION[SESS . 'data']['state_id']: 0;
            $product_id = (isset($product_id)? $product_id : 0);
            /* echo json_encode(array('location_id'=>$location_id, 'product_id'=>$_POST['pid'])); die();*/

            $q = "SELECT CAST(prl.mrp as decimal(10,2)) as mrp, (CASE WHEN(t.igst IS NULL) THEN 0 ELSE t.igst END) as gst 
            FROM product_rate_list prl
            INNER JOIN catalog_product p  ON p.id = prl.product_id 
            LEFT JOIN _gst t ON  p.hsn_code=t.hsn_code 
            WHERE prl.state_id='$location_id' AND prl.product_id =  '$product_id'";
            /*h1($q); */
            $item_data_e = mysqli_query($dbc, $q);
            /*echo "count: ".mysqli_num_rows($item_data_e);*/
            $drate = 0; $finaldrate = 0;
            if($item_data_e)
            {                        
                $item_data = array();
                while($row = mysqli_fetch_array($item_data_e))
                {
                        /*if($rows['product_gst'])
                     {
                      $r_rate = $mrp-($mrp*25/100);
                       $d_rate = $r_rate-($r_rate*7.33/100);
                     }else{
                      $r_rate = $mrp-($mrp*18/100);
                      $d_rate = $r_rate-($r_rate*7/100);
                  }*/

                  if($row['gst'] > 0)
                    {  $drate = ( $row['mrp'] - ($row['mrp'] * .25) );  
                $finaldrate = ( $drate - ($drate *.0733) );
            }   
            else{  
                $drate = ( $row['mrp'] - ($row['mrp'] * .18) );  
                $finaldrate = ( $drate - ($drate *.07) );
            } 
            $item_data['mrp'][] = $row['mrp'];
            $item_data['gst']    = $row['gst'];
            $item_data['dealer_rate'] = $finaldrate;
        }
    }
    return round($item_data['dealer_rate'],2);
}

public function get_tax_inv_gst_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT count(DISTINCT ch_retailer_id) as retailer_count,co.ch_retailer_id as id,sum(cod.vat_amt) as total_tax_amount,sum(cod.taxable_amt) as total_taxable_amount FROM challan_order co inner join challan_order_details cod on co.id = cod.ch_id inner join retailer r on co.ch_retailer_id = r.id $filterstr and co.ch_dealer_id=$dealer_id and r.tin_no != 0";

        /*h1($q);*/
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        
        while ($row = mysqli_fetch_assoc($rs)) {           
            $id = $row['id'];
            $out['B2B'] = $row; // storing the item id
          
        }

         $q = "SELECT count(DISTINCT ch_retailer_id) as retailer_count,co.ch_retailer_id as cid,sum(cod.vat_amt) as total_tax_amount,sum(cod.taxable_amt) as total_taxable_amount FROM challan_order co inner join challan_order_details cod on co.id = cod.ch_id inner join retailer r on co.ch_retailer_id = r.id $filterstr and co.ch_dealer_id=$dealer_id and r.tin_no = 0";

        /*h1($q);*/
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        
        while ($row = mysqli_fetch_assoc($rs)) {           
            $id = $row['cid'];
            $out['B2C'] = $row; // storing the item id
          
        }
        return $out;
    }

 public function print_looper_challan_gst($start, $end,$status)
    {  
        global $dbc;
        $out = array();
       $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
    if($status == 1){
        $q = "SELECT r.name as retailer_name,cod.id as id, r.tin_no as gstino,co.ch_no as invoice_no,date_format(co.ch_date,'%d/%m/%Y') as invoice_date,cod.tax as rate,sum(cod.vat_amt) as tax_amount,sum(cod.taxable_amt) as taxable_amt, SUM((qty*product_rate)-(cd_amt+dis_amt)) as txbl FROM challan_order co inner join challan_order_details cod on co.id = cod.ch_id inner join retailer r on co.ch_retailer_id = r.id where date_format(`ch_date`,'%Y%m%d')>= $start and date_format(`ch_date`,'%Y%m%d') <= $end and co.ch_dealer_id=$dealer_id and r.tin_no != 0 group by co.ch_no,cod.tax ";
    }
    elseif($status == 2){
        $q = "SELECT r.name as retailer_name,cod.id as id, r.tin_no as gstino,co.ch_no as invoice_no,date_format(co.ch_date,'%d/%m/%Y') as invoice_date,cod.tax as rate,sum(cod.vat_amt) as tax_amount,sum(cod.taxable_amt) as taxable_amt, SUM(qty*product_rate) as txbl FROM challan_order co inner join challan_order_details cod on co.id = cod.ch_id inner join retailer r on co.ch_retailer_id = r.id where date_format(`ch_date`,'%Y%m%d')>= $start and date_format(`ch_date`,'%Y%m%d') <= $end and co.ch_dealer_id=$dealer_id and r.tin_no = 0 group by co.ch_no,cod.tax ";
    }

         /*h1($q);*/
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        
        while ($row = mysqli_fetch_assoc($rs)) {           
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
          
        }
       /* pre($out); exit();*/       
       // pre($out);
        return $out;
    }
    public function get_dealer_sale_report($filter = '', $records = '', $orderby = '', $popup=false)
    {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

        $q = "SELECT *,order_id,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order $filterstr GROUP BY user_sales_order.order_id";
       //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs))
        {
            $where = 'id = '.$row['retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dspId'] = $row['user_id'];
            $out[$id]['remarks'] = $row['remarks'];
            $out[$id]['name'] = '';//$dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $retailer_map;
            $out[$id]['person_name'] = $this->get_username($row['user_id']);             
                $item_q = "SELECT s.qty as aval_qty,usod.id,cp.name, cp.taxable,s.rate,usod.quantity,usod.scheme_qty,usod.product_id,usod.remaining_qty FROM user_sales_order_details usod 
                    INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id 
                    INNER JOIN catalog_product cp ON usod.product_id=cp.id 
                    INNER JOIN stock s ON usod.product_id=s.product_id
                    WHERE usod.order_id = $row[order_id]  AND s.dealer_id=$dealer_id";
                // h1($item_q);
                // die;
                $out[$id]['order_item'] = $this->get_my_reference_array_direct($item_q, 'id');                
        }
        return $out;
    }


////////////////////////////////////////PURCHASE GST REPORT/////////////////////////////////

public function get_purchase_reg_list($filter = '', $records = '', $orderby = '')
{
global $dbc;
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$filterstr = $this->oo_filter($filter, $records, $orderby);
$out = array();
//echo $filterstr;
$q = "SELECT rod.order_id,round(SUM(rod.pr_rate*rod.cases),2) as total_amount,ro.receive_date,ro.challan_no,csa.csa_name,csa.gst_no FROM receive_order_details rod JOIN receive_order ro ON rod.order_id=ro.order_id JOIN csa ON ro.csa_id=csa.c_id $filterstr GROUP BY csa_id";

//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

while ($row = mysqli_fetch_assoc($rs))
{
$out[] = $row;
}

return $out;
}


public function print_purchase_gst($start, $end,$did)
{
global $dbc;
$out = array();
//echo $start."<br>".$end."<br>".$did;

$q = "SELECT ro.receive_date,ro.order_id as orderid , round(SUM(rod.pr_rate*rod.cases),2) as total_amount,gst,ro.challan_no,csa.csa_name,csa.gst_no FROM receive_order_details rod JOIN receive_order ro ON rod.order_id=ro.order_id JOIN csa ON ro.csa_id=csa.c_id WHERE DATE_FORMAT(ro.receive_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(ro.receive_date,'%Y%m%d') <= '$end' AND ro.dealer_id = '$did' GROUP BY ro.order_id,rod.gst";

// h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;

while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['orderid'];
$out[] = $row; // storing the item id

}
/* pre($out); exit();*/
// pre($out);
return $out;
}

public function print_purchase_gst_wise_report($start, $end,$did)
{
global $dbc;
$out = array();
//echo $start."<br>".$end."<br>".$did;

$q = "SELECT ro.receive_date,ro.order_id as orderid , round(SUM(rod.pr_rate*rod.cases),2) as total_amount,gst,ro.challan_no,csa.csa_name FROM receive_order_details rod JOIN receive_order ro ON rod.order_id=ro.order_id JOIN csa ON ro.csa_id=csa.c_id WHERE DATE_FORMAT(ro.receive_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(ro.receive_date,'%Y%m%d') <= '$end' AND ro.dealer_id = '$did' GROUP BY rod.gst";

// h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;

while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['orderid'];
$out[] = $row; // storing the item id

}
/* pre($out); exit();*/
// pre($out);
return $out;
}

/////////////////////////////////////////END PURCHASE GST REPORT/////////////////////////////
    public function pur_looper_challan_gst($start, $end,$oid)
       {  
           global $dbc;
           $out = array();
           $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

           $q = "SELECT rod.order_id,(rod.pr_rate*rod.cases) as total_amount,ro.receive_date,ro.challan_no,d.tin_no,csa.csa_name, ROUND((((rod.pr_rate*rod.cases)*5/100)/2),2) as tx_amt, csa.csa_name FROM receive_order_details rod JOIN receive_order ro ON rod.order_id=ro.order_id JOIN dealer d ON ro.dealer_id=d.id JOIN csa ON ro.csa_id=csa.c_id WHERE DATE_FORMAT(ro.receive_date,'%Y%m%d') >= $start AND DATE_FORMAT(ro.receive_date,'%Y%m%d') <= $end AND ro.dealer_id = $dealer_id AND rod.order_id=$oid";
           // h1($q);
           list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');

           while ($row = mysqli_fetch_assoc($rs))
           {
              $out[] = $row;
           }

           return $out;          

       }
       

}

?>
