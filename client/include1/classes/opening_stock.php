<?php

class opening_stock extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## Invoice Starts here ####################################################

    public function get_opening_stock_se_data() {
        $d1 = $_POST;
        //$d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
        $d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Stock Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function opening_stock_save() {
        global $dbc;
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        
        //$_SESSION[SESS . 'data']['csa_id'] = $data['csa_id'];
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_opening_stock_se_data();
        ///pre($d1['dealer_id']); exit;
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
       //pre($d1);
       $extrawork = $this->opening_stocks_order_extra('save', $_POST['dealer_id'], $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'],  $_POST['date'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['person_id'],$_POST['company_id'],$_POST['csa_id']);

        if (!$extrawork['status']) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => $extrawork['myreason']);
        }
        mysqli_commit($dbc);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
    }

    public function opening_stocks_order_extra($actiontype, $rId, $productId, $batch_no, $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $person,$company_id,$csa_id) {
        global $dbc;
        $uncode = '';
          $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
           $user = $_SESSION[SESS . 'data']['id'];
           $primary_id = $dealer_id.date('Ymdhis');
        $str = $str_cat = array();
        if ($actiontype == 'Update')
            mysqli_query($dbc, "DELETE FROM stock WHERE order_id = $rId");
     //   if ($company_id == 1)
          //  write_query("DELETE FROM user_opening_stocks_order_details WHERE order_id = $rId");
        // saving the details for the stock item table
 $dd = explode('/',$scheme);
 $date = $dd[2].'-'.$dd[1].'-'.$dd[0];
        foreach ($productId as $key => $value) {
            $mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
            $expdate = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
      //     $primary_id1 = $dealer_id.date('Ymdhis');
            if ($actiontype == 'Update')
                $uncode = date('Ymdhis') + $key + 1;
            else
                $uncode = date('Ymdhis') + $key + 1;
           $p_rate = ($base_price[$key])*18/100;
           $pr_rate =  $base_price[$key]-$p_rate;
      $str[] = "('$value', '{$purchase_inv[$key]}', '{$base_price[$key]}', {$person[$key]}, 0,{$rId[$key]}, '{$quantity[$key]}',0,0, '{$quantity[$key]}', '$mfdate', '$expdate', '$date', '$pr_rate','$company_id','0')";
      $primary[] = "('$primary_id','$primary_id','$value', '{$base_price[$key]}', '{$quantity[$key]}',0,'{$purchase_inv[$key]}', '$mfdate', '$expdate',NOW(),'{$purchase_inv[$key]}',$pr_rate,1)";
        
      }
        $str = implode(', ', $str);
        $primary = implode(', ', $primary);
        $str_cat = implode(', ', $str_cat);
        $q = "INSERT INTO `stock` (`product_id`, `batch_no`, `rate`, `person_id`, `csa_id`, 
            `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`,
            `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`) VALUES $str";
     //  h1($q);
        $r = mysqli_query($dbc, $q);
        //////////////////////////////////////PRIMARY SALES ORDER///////////////////////////////////
 
       $q1 = "INSERT INTO `user_primary_sales_order`(`id`, `order_id`, `dealer_id`, `created_date`, 
            `created_person_id`, `sale_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, 
            `challan_no`, `csa_id`, `action`) VALUES ('$primary_id','$primary_id','$dealer_id','NOW()','$user','NOW()','NOW()','NOW()','$company_id','NOW()','Opening Stock','$csa_id',0)";
    //  h1($q1);
       $r1 = mysqli_query($dbc,$q1);
     /////////////////////////////////////PRIMARY SALES ORDER DETAILS//////////////////////////////   
        $q2 = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, 
            `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, 
            `pr_rate`, `cases`) VALUES $primary";
        $r2 = mysqli_query($dbc,$q2);
    //  h1($q2);
        if (!$r)
            return array('status' => false, 'myreason' => 'User Primary Stock details could not be saved some error occurred.');
    if ($company_id == 1){
            write_query($q);
    }
        return array('status' => true, 'myreason' => '');
    }

    public function opening_stock_order_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_opening_stock_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('YmdHis');
        $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
        $id = $id;
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE user_opening_stocks_order SET order_id='$orderno',sale_date = NOW(),date_time = NOW(), company_id = '$d1[company_id]', ch_date = '$d1[ch_date]' ,challan_no = '$d1[challan_no]' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Primary Stock could not be updated some error occurred');
        }
        if ($company_id == 1){
          write_query($q);
        }
        $rId = $id;
        $extrawork = $this->opening_stocks_order_extra('Update', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['quantity'], $_POST['scheme'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['pr_rate'],$_POST['company_id'],$_POST['csa_id']);

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

    public function get_opening_stock_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,csa_name,DATE_FORMAT(expire,'%d/%m/%Y') AS expire,DATE_FORMAT(mfg,'%d/%m/%Y') AS mfg,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM stock left join csa c on stock.csa_id=c.c_id  $filterstr";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
       
       $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
           $out[$id]['product'] = $this->get_product($row['product_id']);
          //  $out[$id]['order_item'] = 
                    
                    
        }// while($row = mysqli_fetch_assoc($rs)){ ends

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
    public function get_product($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT name  FROM catalog_product WHERE id = $id";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['name'];
    }


    public function next_primary_order_num() {
        global $dbc;
        $out = array();
        $q = "SELECT MAX(id) AS total FROM user_sales_order";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        //return $rs['total']+1;	
    }
    public function opening_stock_delete($id, $filter = '', $records = '', $orderby = '') {
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
