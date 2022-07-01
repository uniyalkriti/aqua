<?php

class receive_order extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## Invoice Starts here ####################################################
public function update_receive_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM receive_order left join csa c on receive_order.csa_id=c.c_id  $filterstr";
   //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
//                if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
//                else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
            $urod = "SELECT rate as mrp,gst,hsn_code,cases,usod.id,cp.name,usod.rate,usod.quantity, pr_rate, 
                    batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv,
                    DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id
                    FROM receive_order_details usod INNER JOIN purchase_order uso ON 
                    usod.order_id = uso.order_id INNER JOIN catalog_product cp
                    ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]";
            //h1($urod);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct($urod, 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
       // pre($out);
        return $out;
    }

     public function update_receive_order_edit($id){
            global $dbc;
            $out = array('status' => 'false', 'myreason' => '');   

            list($status, $d1) = $this->get_receive_order_se_data();
            if (!$status)
                return array('staus' => false, 'myreason' => $d1['myreason']);
                 //pre($d1); exit;
          //  $receive_date = get_mysql_date($d1['receive_date'],'/',$time = false, $mysqlsearch = true);
          //pre($d1); exit;
            $rcdate = explode('/',$d1[receive_date]);
            $receive_date = $rcdate[2].'-'.$rcdate[1].'-'.$rcdate[0];
            //$id = $id;
            $pid = $_SESSION[SESS . 'data']['id'];
            $orderno = $d1['order_no'];
            $csa_id = $d1['csa_id'];
            $challan_no=$d1['challan_no'];
            $grn=$d1['grn'];
            $chdate = explode('/',$d1[ch_date]);
            $ch_date = $chdate[2].'-'.$chdate[1].'-'.$chdate[0];
            mysqli_query($dbc, "START TRANSACTION");
            // query to update
            $q = "UPDATE purchase_order SET receive_date = NOW(), ch_date = '$ch_date' , challan_no = '$d1[challan_no]' WHERE id = '$id'";
          
            $userid = $_SESSION[SESS.'data']['id'];
            $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];


            ###############################   <puneet>   ###################################
            $new_products = array();
            $old_products = array();
            $purchase_product_ids = $d1['product_id'];
            
            foreach($purchase_product_ids as $k=>$id)
            {
              $newmrp = $d1['base_price'][$k];
             // h1($newmrp); exit();
             

              $product_id = $id;
              $batch_no = $_POST['purchase_inv'][$k];
              $dealer_rate = $_POST['dealer_rate'][$k];
              $mfg_date = !empty($d1['mfg_date']) ? get_mysql_date($d1['mfg_date']) : '';
             /* $retailer_rate = $_POST['base_price'][$k];*/
              //$qty = $_POST['quantity'][$k];
              //$casess = $_POST['cases'][$k];
             // $s = "select piece,free_qty from `cases` where product_id = $product_id";
         //$rsss = mysqli_query($dbc, $s);
   //$rowrr = mysqli_fetch_assoc($rsss);
         //$qty11 = $rowrr['piece'];
       //  $qty = $qty11*$casess;
			$qty=$_POST['quantity'][$k];
          //  echo"QUANTITY".$qty; exit;  
              $company_id = $_POST['company_id'];
              $date = date('Y-m-d');

             
            }

               $r = mysqli_query($dbc, $q);
           
            $rId = $id;
//       $qr = "INSERT INTO `receive_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`, `order_date`, `receive_date`, `date_time`,  `ch_date`, `challan_no`,`grn`, `csa_id`) VALUES
//                                              ('$orderno','$dealer_id', NOW(), '$pid', '$receive_date', '$receive_date',NOW(), '$ch_date','$challan_no','$grn','$csa_id')";
      
            $qr = "UPDATE `receive_order` SET `created_date`= NOW(),
           `date_time`= NOW(),`challan_no`=$challan_no,
           `grn`=$grn WHERE order_id = '$orderno'"; 
        //echo $qr;exit;
            $rr = mysqli_query($dbc, $qr);
            if (!$rr) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Receive_order Table error');
            }
            $extrawork = $this->receive_order_extra('Update', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['cases'],  $_POST['scheme'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['dealer_rate'],$_POST['company_id'],$_POST['cases']);

            if (!$extrawork['status']) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => $extrawork['myreason']);
            }
            mysqli_commit($dbc);
            //Saving the user modification history
            //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
            //$this->save_log($hid, $modifieddata, $d1['what']);
            return array('status' => true, 'myreason' => $d1['what'] . ' successfully Changed');
        }
    public function get_receive_order_se_data() {
        $d1 = $_POST;
        //$d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
        $d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Purchase Order'; //whether to do history log or not
        return array(true, $d1);
    }

//    public function receive_order_save() {
//        global $dbc;
//        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
//        $pid = $_SESSION[SESS . 'data']['id'];
//       
//        //$_SESSION[SESS . 'data']['csa_id'] = $data['csa_id'];
//        $out = array('status' => 'false', 'myreason' => '');
//        list($status, $d1) = $this->get_receive_order_se_data();
//      //pre($d1); exit;
//        if (!$status)
//             return array('staus' => false, 'myreason' => $d1['myreason']);
//        $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('ymdHis');
//        $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
//        $challan_date = !empty($d1['ch_date']) ? get_mysql_date($d1['ch_date']) : '';
//        $id = $_SESSION[SESS . 'data']['dealer_id'] . date('ymdhis');
//        $csa_id = $d1['csa_id'];
//        //Start the transaction
//
//        mysqli_query($dbc, "START TRANSACTION");
//        
//        mysqli_commit($dbc);
//        //Final success 
//        return array('status' => true, 'myreason' => $d1['what'] . ' Successfully Saved', 'rId' => $rId);
//    }

     public function get_receive_order_list_on_purchase($filter = '', $records = '', $orderby = '') {
	global $dbc;
	$out = array();
	$filterstr = $this->oo_filter($filter, $records, $orderby);
	$mtype = $_SESSION[SESS . 'constant']['retailer_level'];
	$q = "SELECT *,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(created_date,'%d-%m-%Y') AS created_dates,"
	. "DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order left join csa c on purchase_order.csa_id=c.c_id $filterstr";
	 //h1($q);
	list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
	if (!$opt)
	return $out;
	// if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
	// else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
	$brand_map = get_my_reference_array('catalog_1', 'id', 'name');
	while ($row = mysqli_fetch_assoc($rs)) {
		$id = $row['id'];
		$out[$id] = $row; // storing the item id
		$out[$id]['name'] = $dealer_map[$row['dealer_id']];
		$out[$id]['person_name'] = $this->get_username($row['created_person_id']);
                $rq = "SELECT td_amount,atd_amt,total_amount,scheme_qty,gross_amt,gst_percentage,cgst_amount,sgst_amount,hsn_code,cases,usod.id,cp.name,usod.rate,usod.quantity, pr_rate, 
                    batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv,cr_note,dr_note,
                    DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id,taxable_amount  
                    FROM purchase_order_details usod INNER JOIN purchase_order uso ON 
                    usod.purchase_inv = uso.challan_no AND uso.order_id=usod.order_id LEFT JOIN catalog_product cp
                    ON usod.product_id=cp.id WHERE usod.purchase_inv ='$row[challan_no]' AND uso.csa_id='$row[csa_id]' ORDER BY cp.name";
              //h1($rq);
		$out[$id]['order_item'] = $this->get_my_reference_array_direct($rq, 'id');
	}// while($row = mysqli_fetch_assoc($rs)){ ends

	return $out;
}
    
    public function receive_order_extra($actiontype, $rId, $productId, $batch_no, $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,$company_id,$cases) {
        global $dbc;
        $uncode = '';
        $str = $str_cat = array();
        if ($actiontype == 'Update')
             mysqli_query($dbc, "DELETE FROM receive_order_details WHERE order_id = $rId");
     //   if ($company_id == 1)
          //  write_query("DELETE FROM receive_order_details WHERE order_id = $rId");
        // saving the details for the stock item table

        foreach ($productId as $key => $value) {
            $mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
           $expdate =  date('Y-m-d', strtotime('+1 year', strtotime($mfdate)) );
            if ($actiontype == 'Update')
                $uncode = date('Ymdhis') + $key + 1;
            else
                $uncode = date('Ymdhis') + $key + 1;
           // print_r($cases[$key]);
             $qgst = "select igst from `_gst` INNER JOIN catalog_product ON catalog_product.hsn_code = 
                  _gst.hsn_code where catalog_product.id = $value";
		$rgst = mysqli_query($dbc, $qgst);
		while($rowgst = mysqli_fetch_assoc($rgst))
		{
		$gst = $rowgst['igst']; 
		
		}
           $case = $quantity[$key];
           $s = "select piece,free_qty from `cases` where product_id = $value";
		       $rsss = mysqli_query($dbc, $s);
	         $rowrr = mysqli_fetch_assoc($rsss);
           $qty111 = $rowrr['piece'];

            $qtyamt = $qty111*$case;
            $caseprice = $sale_price[$key];
            $rate_pcs = $caseprice/$qty111;
            $gst_amt = (($caseprice*$case)*($gst))/100;
            
            $str[] = "('$rId', '$value', '{$base_price[$key]}', '$qtyamt','', '$mfdate', '$expdate', '{$purchase_inv[$key]}','{$sale_price[$key]}', '{$cases[$key]}','$rate_pcs','$gst','$gst_amt')";
        }
        $str = implode(', ', $str);
        $str_cat = implode(', ', $str_cat);
        $q = "INSERT INTO `receive_order_details` (`order_id`, `product_id`, `rate`, `quantity`,`purchase_inv`, `mfg_date`, `expiry_date`, `batch_no`, `pr_rate`,`cases`,`rate_pcs`,`gst`,`gst_amt`) VALUES $str";
        $r = mysqli_query($dbc, $q);
      // h1($q);
//		if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved some error occurred.') ;
//                $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES $str_cat";
//              
//		$r = mysqli_query($dbc, $q);
        if (!$r)
            return array('status' => false, 'myreason' => 'User Primary Stock details could not be saved some error occurred.');
    if ($company_id == 1){
            write_query($q);
    }
        return array('status' => true, 'myreason' => '');
    }

    public function receive_order_edit($id){
            global $dbc;

            $out = array('status' => 'false', 'myreason' => '');   

            list($status, $d1) = $this->get_receive_order_se_data();
            if (!$status)
                return array('staus' => false, 'myreason' => $d1['myreason']);
                 //pre($d1); exit;
          //  $receive_date = get_mysql_date($d1['receive_date'],'/',$time = false, $mysqlsearch = true);
            $rcdate = explode('/',$d1[receive_date]);
            $receive_date = $rcdate[2].'-'.$rcdate[1].'-'.$rcdate[0];
            //$id = $id;
            $pid = $_SESSION[SESS . 'data']['id'];
            $orderno = $_SESSION[SESS . 'data']['dealer_id'] . date('ymdHis');
            $csa_id = $d1['csa_id'];
            $challan_no=$d1['challan_no'];
            $order_id=$d1['order_id'];
            $grn=$d1['grn'];
            $chdate = explode('/',$d1[ch_date]);
            $ch_date = $chdate[2].'-'.$chdate[1].'-'.$chdate[0];
            mysqli_query($dbc, "START TRANSACTION");
            // query to update
            $q = "UPDATE purchase_order SET receive_date = NOW(), ch_date = '$ch_date' , challan_no = '$d1[challan_no]' WHERE id = '$id'";
            $r = mysqli_query($dbc, $q);
            $userid = $_SESSION[SESS.'data']['id'];
            $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];


            ###############################   <puneet>   ###################################
            $new_products = array();
            $old_products = array();
            $purchase_product_ids = $d1['product_id'];
            
            foreach($purchase_product_ids as $k=>$id)
            {
              $newmrp = $d1['base_price'][$k];
              $dealer_rate = $d1['dealer_rate'][$k];
              $qtyb = $_POST['quantity'][$k];
              $qtyf = $_POST['scheme_qty'][$k];
              $gross_amt = $d1['gross_amt'][$k];
              $td_amt = $d1['td_amt'][$k];
              $cd_amt = $d1['cd_amt'][$k];
              $sch_amt = $d1['sch_amt'][$k];
              $spl_amt = $d1['spl_amt'][$k];
              $atd_amt = $d1['atd_amt'][$k];
              $cgst_amount = $d1['cgst_amount'][$k];
              $sgst_amount = $d1['sgst_amount'][$k];
              $igst_amount = $d1['igst_amount'][$k];
              $total_amount = $d1['total_amount'][$k];
              $batch_no = $_POST['batch_no'][$k];
              $mfg_date = !empty($_POST['mfg_date'][$k]) ? get_mysql_date($_POST['mfg_date'][$k]) : '';
              $hbatch_no = $_POST['hbatch_no'][$k];
              $hmfg_date = !empty($_POST['hmfg_date'][$k]) ? get_mysql_date($_POST['hmfg_date'][$k]) : '';
              $podsq="SELECT id FROM purchase_order_details WHERE order_id='$order_id' AND purchase_inv='$challan_no' AND product_id='$id' AND batch_no='$hbatch_no' AND mfg_date='$hmfg_date'";
              $podsrq=mysqli_query($dbc,$podsq);
              $podsrow=mysqli_fetch_assoc($podsrq);
              $podid=$podsrow['id'];
              $podq="UPDATE `purchase_order_details` SET `mrp`='$newmrp',`rate`='$dealer_rate',`quantity`='$qtyb',`scheme_qty`='$qtyf',`mfg_date`='$mfg_date',`expiry_date`='$mfg_date',`receive_date`=NOW(),`batch_no`='$batch_no',`pr_rate`='$dealer_rate',`gross_amt`='$gross_amt',`td_amount`='$td_amt',`sch_amt`='$sch_amt',`spl_amt`='$spl_amt',`cd_amount`='$cd_amt',`atd_amt`='$atd_amt',`cgst_amount`='$cgst_amount',`sgst_amount`='$sgst_amount',`igst_amount`='$igst_amount',`total_amount`='$total_amount' WHERE id='$podid'";
              //h1($podq);exit;
              $podr=mysqli_query($dbc,$podq);
              if (!$podr)
              {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'purchase order details error');
              }
              $stock_q = "SELECT `product_id` FROM `stock` where `dealer_id`='$dealer_id' AND `product_id`='$id' AND mrp='$newmrp' AND batch_no='$batch_no' AND `mfg`='$mfg_date'";
              
              $stock_e = mysqli_query($dbc,$stock_q);
              $stock_row = mysqli_fetch_assoc($stock_e);
              $product = $stock_row['product_id'];

              $product_id = $id;
              
             $dealer_rate1 = $_POST['dealer_rate'][$k];
             /* $retailer_rate = $_POST['base_price'][$k];*/
              //$qty = $_POST['quantity'][$k];
              $casess = $_POST['cases'][$k];
              //$s = "select piece,free_qty from `cases` where product_id = $product_id";
         //$rsss = mysqli_query($dbc, $s);
	// $rowrr = mysqli_fetch_assoc($rsss);
        // $qty11 = $rowrr['piece'];
         //$qty = $qty11*$casess;
         $qty=$qtyb+$qtyf;

          $dealer_rate = $dealer_rate1/$qty11;
          //  echo"QUANTITY".$qty; exit;  
              $company_id = $_POST['company_id'];
              $date = date('Y-m-d');

              if(!$product)
              {
                $new_products[] = "('$id','$batch_no','$dealer_rate1','$newmrp','$csa_id','$dealer_id','$qty','$date','$company_id','$dealer_rate1','$mfg_date')";
              }else{
                $stock_upd_q =  "UPDATE `stock` SET `qty` = (qty+$qty) WHERE `dealer_id`='$dealer_id' AND `product_id`='$id' AND mrp='$newmrp' AND batch_no='$batch_no' AND `mfg`='$mfg_date'";
                $stock_upd_e = mysqli_query($dbc, $stock_upd_q);
              }
            }

            /*Updating existing product stock in `stock` table*/
          /*  if(!empty($old_products))
            {          
              $stock_upd_q = implode(';',$old_products);
              $stock_upd_e = mysqli_query($dbc, $ddq);
              $stock_upd_e = mysqli_affected_rows($dbc);
              die;

              if(!$stock_upd_e)
              {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Stock could not be updated some error occurred');
              }
            }*/

            /*Inserting new product in `stock` table*/
            if(!empty($new_products))
            {
                $stock_ins_q1= implode(',',$new_products);
              $stock_ins_q = "INSERT INTO `stock` (`product_id`, `batch_no`,`dealer_rate`,`mrp`,`csa_id`, `dealer_id`, `qty`, `date`, `company_id`,`rate`,`mfg`) VALUES $stock_ins_q1";         

              //$stock_ins_q .= implode(',',$new_products);
              $stock_ins_e  = mysqli_query($dbc, $stock_ins_q);

              if (!$stock_ins_e)
              {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Stock could not be updated some error occurred');
              }
            }
            
            ###############################   </puneet>  ###################################

            
//         
//            $primary = "INSERT INTO `user_primary_sales_order`(`id`,`order_id`,
//           `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`,
//           `date_time`, `company_id`, `ch_date`, `challan_no`, `csa_id`) VALUES(
//           '$orderno','$orderno','$dealer_id',NOW(),'$userid','$_POST[receive_date]',NOW(),NOW(),
//           '$_POST[company_id]','$ch_date','$_POST[challan_no]','$_POST[csa_id]'
//           )";
//
//           $r1 = mysqli_query($dbc, $primary);
//           if (!$r && !$r1) {
//                    mysqli_rollback($dbc);
//                    return array('status' => false, 'myreason' => 'Receive Stock could not be updated some error occurred');
//                }
          
            $rId = $id;
       $qr = "INSERT INTO `receive_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`, `order_date`, `receive_date`, `date_time`,  `ch_date`, `challan_no`,`grn`, `csa_id`) VALUES
                                              ('$orderno','$dealer_id', NOW(), '$pid', '$receive_date', '$receive_date',NOW(), '$ch_date','$challan_no','$grn','$csa_id')";
            // echo $qr;exit;
            $rr = mysqli_query($dbc, $qr);
            if (!$rr) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Receive_order Table error');
            }
            $extrawork = $this->receive_order_extra('save', $orderno, $_POST['product_id'], $_POST['batch_no'], $_POST['base_price'], $_POST['cases'],  $_POST['scheme'],$_POST['purchase_inv'], $_POST['mfg_date'], $_POST['expiry_date'], $_POST['dealer_rate'],$_POST['company_id'],$_POST['cases']);

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
     public function get_receive_order_extra($actiontype, $rId, $productId, $batch_no, $base_price, $quantity, $scheme,$purchase_inv, $mfg_date, $expiry_date, $sale_price,$company_id,$cases,$csa_id) {
        global $dbc;
        $uncode = '';
        $str = $str_cat = array();
    $userid = $_SESSION[SESS.'data']['id'];
   $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
        foreach ($productId as $key => $value) {
            $mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
          $expdate =  $prev_date = date('Y-m-d', strtotime($mfdate .' +365 day'));
            $expdate1 = !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
            if ($actiontype == 'Update')
                $uncode = date('Ymdhis') + $key + 1;
            else
                $uncode = date('Ymdhis') + $key + 1;
           // print_r($cases[$key]);
             $s = "select piece,free_qty from `cases` where product_id = $value";
                  $rsss = mysqli_query($dbc, $s);
                  while($row = mysqli_fetch_assoc($rsss))
                  {
                     $p = $row['piece']; 
                     $free = $row['free_qty']; 
                  }
	//echo $scheme[$key]."ANKUSH";
          
             $quant[$key] = $cases[$key]*$p;
             $schem[$key] = $free*$cases[$key];
                      
          $str[] = "('$rId','$rId', '$value', '{$base_price[$key]}', '{$quant[$key]}','{$schem[$key]}',
              '{$purchase_inv[$key]}', '$mfdate', '$expdate',NOW(),'{$purchase_inv[$key]}',
                  '{$sale_price[$key]}','{$cases[$key]}')";
                    
            $stock[] = "('$value','{$purchase_inv[$key]}', '{$base_price[$key]}','$userid','$csa_id','$dealer_id',
                '{$quant[$key]}','0','0','{$quant[$key]}','$mfdate', '$expdate',NOW(),'{$sale_price[$key]}',
                 '$company_id','1')";
               
          
        }
        // echo $expdate;
        // pre($stock);
        $stock = implode(', ', $stock);
         $q1 = "INSERT INTO `stock`(`product_id`, `batch_no`, `rate`, `person_id`,
             `csa_id`, `dealer_id`, `qty`,`salable_damage`, `nonsalable_damage`, `remaining`, `mfg`,
             `expire`, `date`, `pr_rate`, `company_id`, `action`) VALUES $stock";
        // h1($q1);
        $r = mysqli_query($dbc, $q1); 
        
        $str = implode(', ', $str);
        $str_cat = implode(', ', $str_cat);
      $prd = "INSERT INTO `user_primary_sales_order_details`(`id`,`order_id`, `product_id`,
          `rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`,
          `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`) VALUES $str";
      $rp = mysqli_query($dbc, $prd);
      
        if (!$r && !$rp)
            return array('status' => false, 'myreason' => 'User Primary Stock details could not be saved some error occurred.');
    if ($company_id == 1){
            write_query($q);
    }
        return array('status' => true, 'myreason' => '');
    }


    public function get_receive_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order left join csa c on purchase_order.csa_id=c.c_id  $filterstr";
   //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
//                if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
//                else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
            $odq="SELECT cases,usod.id,usod.mrp,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id,usod.gross_amt,usod.td_amount,usod.cd_amount,usod.sch_amt,usod.spl_amt,usod.atd_amt,usod.cgst_amount,usod.sgst_amount,usod.total_amount FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = '$row[order_id]'";
           // h1($odq);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct($odq, 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }    
    public function receive_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT *,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM purchase_order left join csa c on purchase_order.csa_id=c.c_id  $filterstr";
  //  h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
//                if($_SESSION[SESS.'data']['company_id'] == 1) $dealer_map = get_my_reference_array('dealer', 'id', 'name');
//                else $dealer_map = get_my_reference_array('party', 'partyId', 'partyname');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
            // $qrcv="SELECT usod.*,cases,usod.mrp,usod.id AS id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.purchase_inv = uso.challan_no AND usod.order_id = uso.order_id LEFT JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.purchase_inv = '$row[challan_no]' AND uso.csa_id='$row[csa_id]'";
            $qrcv="SELECT usod.*,cases,usod.mrp,usod.id AS id,cp.name,usod.rate,usod.quantity,usod.scheme_qty,pr_rate, batch_no,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date,purchase_inv, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date,cp.id AS product_id FROM purchase_order_details usod INNER JOIN purchase_order uso ON usod.purchase_inv = uso.challan_no AND usod.order_id = uso.order_id LEFT JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.purchase_inv = '$row[challan_no]' AND usod.order_id = '$row[order_id]' AND uso.csa_id='$row[csa_id]'";
          //  h1($qrcv);die;
            $out[$id]['order_item'] = $this->get_my_reference_array_direct($qrcv, 'id');
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
