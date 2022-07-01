<?php

class primary_sale extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## Invoice Starts here ####################################################

    public function get_primary_sale_order_se_data() {
        $d1 = $_POST;
        //$d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
        $d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Primary Stock Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function primary_sale_order_save() {
        global $dbc;
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        
        //$_SESSION[SESS . 'data']['csa_id'] = $data['csa_id'];
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_primary_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
        $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
        $challan_date = !empty($d1['ch_date']) ? get_mysql_date($d1['ch_date']) : '';
        $id = $_SESSION[SESS . 'data']['dealer_id'] . date('Ymdhis');
        $csa_id = $d1['csa_id'];
        //Start the transaction

        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `user_primary_sales_order` (`id`,`csa_id`, `order_id`,`dealer_id`,`created_date`, `created_person_id`, `sale_date`,`receive_date`,`date_time`, `company_id`, `ch_date`, `challan_no`)  VALUES ('$id','$csa_id', '$orderno','$dealer_id', NOW(), '$d1[created_person_id]',NOW(), '$receive_date' ,NOW(), '$d1[company_id]', '$challan_date', '$d1[challan_no]')";
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
        //h1($_POST['quantity']);
        $extrawork = $this->primary_sales_order_extra('save', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'],  $_POST['scheme'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['sale_price'],$_POST['company_id']);

        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
    }

    public function primary_sales_order_extra($actiontype, $rId, $productId, $batch_no, $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,$company_id) {
        global $dbc;
        $uncode = '';
        $str = $str_cat = array();
        $stock = $stock_cat = array();
        if ($actiontype == 'Update')
            mysqli_query($dbc, "DELETE FROM user_primary_sales_order_details WHERE order_id = $rId");
            mysqli_query($dbc, "DELETE FROM stock WHERE order_id = $rId");
     //   if ($company_id == 1)
          //  write_query("DELETE FROM user_primary_sales_order_details WHERE order_id = $rId");
        // saving the details for the stock item table
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $person_id = $_SESSION[SESS.'data']['id'];
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_primary_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        foreach ($productId as $key => $value) {
            $mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
            $expdate = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
            if ($actiontype == 'Update')
                $uncode = date('Ymdhis') + $key + 1;
            else
                $uncode = date('Ymdhis') + $key + 1;
            
            $str[] = "('$uncode', '$rId', '$value', '{$base_price[$key]}', '{$quantity[$key]}','{$purchase_inv[$key]}', '$mfdate', '$expdate', '{$batch_no[$key]}','{$sale_price[$key]}')";
      
            $stock[]="('$value','{$purchase_inv[$key]}','$rId','{$base_price[$key]}','$person_id','$d1[csa_id]','$dealer_id','{$quantity[$key]}','0','0','{$quantity[$key]}', '$mfdate', '$expdate',NOW(),'{$sale_price[$key]}','$d1[company_id]')";
            
        }
        $str = implode(', ', $str);
        $str_cat = implode(', ', $str_cat);
        $stock = implode(', ', $stock);
        $q = "INSERT INTO `user_primary_sales_order_details` (`id`, `order_id`, `product_id`, `rate`, `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `batch_no`, `pr_rate`) VALUES $str";
        $r = mysqli_query($dbc, $q);
        if (!$r)
            return array('status' => false, 'myreason' => 'User Primary Stock details could not be saved some error occurred.');
        
        $stockqry= "INSERT INTO `stock` (`product_id`,`batch_no`, `order_id`,`rate`,`person_id`, `csa_id`, `dealer_id`,`qty`,`salable_damage`, `nonsalable_damage`, `remaining`, `mfg`,`expire`,`date`,`pr_rate`,`company_id`) VALUES $stock";
       // h1($stockqry);
        $rs = mysqli_query($dbc, $stockqry);
        if (!$rs) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Stock Table error');
        }
        
        if ($company_id == 1){
            write_query($q);
        }
        return array('status' => true, 'myreason' => '');
    }

    public function primary_sale_order_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_primary_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
        $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
        $id = $id;
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE user_primary_sales_order SET order_id='$orderno',sale_date = NOW(),date_time = NOW(), company_id = '$d1[company_id]', ch_date = '$d1[ch_date]' ,challan_no = '$d1[challan_no]' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Primary Stock could not be updated some error occurred');
        }
        if ($company_id == 1){
          write_query($q);
        }
        $rId = $id;
        $extrawork = $this->primary_sales_order_extra('Update', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'], $_POST['scheme'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['pr_rate']);

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

    public function get_primary_sale_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];

        // $q = "SELECT *,csa_name,DATE_FORMAT(sale_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM user_primary_sales_order left join csa c on user_primary_sales_order.csa_id=c.c_id  $filterstr";

        $q = "SELECT *,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,"
        . "DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order left join csa c on purchase_order.csa_id=c.c_id $filterstr";
       
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
    
        while ($row = mysqli_fetch_assoc($rs)) {

            $order_item = "SELECT cases,usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]";


            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct($order_item, 'id');
        }     
        return $out;
    }
    
    
    

    //This function used to get user retailer gift deatils
    public function get_username($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['uname'];
    }

    public function next_primary_order_num() {
        global $dbc;
        $out = array();
        $q = "SELECT MAX(id) AS total FROM user_sales_order";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        //return $rs['total']+1;	
    }
    public function primary_sale_delete($id, $filter = '', $records = '', $orderby = '') {
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

}

?>