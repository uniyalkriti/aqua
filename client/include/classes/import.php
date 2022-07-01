<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class import extends myfilter {

    public $poid = NULL;

    public function __construct() {
        parent::__construct();
    }

   public function import_invoice_save() {
        global $dbc;

        // if(true)
        if ($_FILES["excelFile"]["error"] > 0) {
        //  echo "manisha";
            echo "Error: " . $_FILES["excelFile"]["error"] . "<br>";
        } else {   
       // echo "manisha_new";      
            $a = $_FILES["excelFile"]["tmp_name"];        
            $csv_file = $a;
            $dealer_id=$_SESSION[SESS.'data']['dealer_id'];
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                 $inum=2;
                 $ch_data=array();
                    mysqli_autocommit($dbc, FALSE);
                    mysqli_query($dbc, "START TRANSACTION");
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                   
                    //echo $num;
                    //for ($c=0; $c < $num; $c++) {
                    $curr_date = date('Y-m-d');
                    $result = $data;
                    $result1  = str_replace(",","",$result);  
                    
                    $str = implode(",", $result1);
                   // $str = implode(",", $result);
                    $slice = explode(",", $str);

                   
                    
                    $invoice_date = $slice[2];
                    $invoice_date = date("Y-m-d", strtotime($invoice_date));
                    $invoice_no = $slice[1];
                    $ss_code = $slice[4];
                    $ss_id=  myrowval('csa', 'c_id', "csa_code='$ss_code'");   
                    $dealer_code = $slice[3];
                    $dealer_id=  myrowval('dealer', 'id', "dealer_code='$dealer_code'");
                    $order_id=  $invoice_no.$dealer_id.date('ymd');  
                    $icode = $slice[5];
                    $itemid=  myrowval('catalog_product', 'id', "itemcode='$icode'"); 
                    $item_id = $itemid;
//                    if(empty($itemid)){
//                        $item_id = myrowval('product_reconcile', 'product_id', "item_code='$icode'"); 
//                    }else{
//                        $item_id = $itemid;
//                    }
                    
                    
                    
                    $item_name = $slice[6];
                    $batch_no = $slice[9];                  
                    $mrp = $slice[13];
                    $rate = $slice[14];
                    $case_qty = 0;
                    $quantity = $slice[11]; 
                    $schemeqty = $slice[12];
                    if(empty($schemeqty)){
                      $scheme_qty = '0';
                    }else{
                      $scheme_qty = $schemeqty;
                    }
                    $gross_amt = $slice[15];
                    
                    $cdamt = $slice[19];
                    $tdamt = $slice[16];
                    $schamt = $slice[17];
                    $splamt = $slice[18];
                    $atdamt = $slice[20];
                    $tax_amt = ($gross_amt+($cdamt+$tdamt+$schamt+$splamt+$atdamt));
                    $hsncode = $slice[8];
                    if(empty($cdamt)){
                      $cd_amt = '0';
                    }else{
                      $cd_amt = $cdamt;
                    }

                    $cgst_amt = $slice[21];
                    $sgst_amt = $slice[22];
                    $igst_amt =0;
                    $gst_per = myrowval('_gst', 'igst', "hsn_code='$hsncode'"); 
                    $cr_note = $slice[24];  
                    $dr_note = $slice[23];  
                    $total_amt = $slice[26];  
                    $mfg_date = $slice[10];
                    $mfg_date = date("Y-m-d", strtotime($mfg_date));
                    $exp_date = $slice[21];
                    $ch_data[$invoice_no]=$invoice_no;
                    //$cases_cond="'$invoice_date' between DATE(from_date) and DATE(to_date) AND product_id='$item_id'";
                   // $piece =  myrowval('cases', 'piece', "$cases_cond"); 
                    //h1($piece);
                    //$quantity = $case_qty * $piece;
                    
                    //if(empty($item_id) || empty($dealer_id) || empty($ss_id)){
                     if(empty($dealer_id)){
                      $error_log="Dealer Code ($dealer_code) Not Match";  
                      return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b><br>$error_log");
                      }elseif(empty($ss_id)){
                       $error_log="Depot Code ($ss_code) Not Match";
   		      return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b> <br>$error_log");
                      }
					 // elseif(empty($item_id)){
			// $error_log="Item Code ($icode) Not Match";
                         // return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b> <br>$error_log");	
                       //     $queryitem="INSERT IGNORE `product_reconcile`(`order_id`,`dealer_id`,`product_id`,`item_code`, `item_name`,`created_person_id`,"
                       //             . "`order_date`,`receive_date`,`create_date_time`,`ch_date`,`challan_no`,`csa_id`,`rate`, `quantity`, "
                        //            . "`scheme_qty`, `purchase_inv`,`mfg_date`, `expiry_date`,`batch_no`, `pr_rate`, `cases`, "
                        //            . "`taxable_amount`, `cd_amount`,`cgst_amount`, `igst_amount`,`gst_percentage`, `total_amount`,`mrp`) "
                       //             . " VALUES ('$order_id','$dealer_id','0','$icode','$item_name','0','$curr_date',NOW(),NOW(),'$invoice_date',"
                       //             . "'$invoice_no','$ss_id','$mrp','0','$scheme_qty','$invoice_no','$mfg_date','$exp_date',"
                       //             . "'$batch_no','$rate','$case_qty','$tax_amt','$cd_amt','$cgst_amt','$igst_amt','$gst_per','$total_amt','$mrp')";          
                        //    $run_item = mysqli_query($dbc, $queryitem);  
                       //     if(!$run_item) {
                        //        mysqli_rollback($dbc);//exit;
                        //        return array('status' =>'false', 'myreason' => "product_reconcile table error");
                         //   }
                          
                      //}
					  else{
                   // }
                     
                    $query="INSERT IGNORE `purchase_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`, `order_date`, `receive_date`, "
                            . "`date_time`, `company_id`, `ch_date`, `challan_no`, `csa_id`) "
                            . " VALUES ('$order_id','$dealer_id',NOW(),'0','$curr_date',NOW(),NOW(),'1','1970-01-01', "
                            . " '$invoice_no','$ss_id')";  
                   //h1($query);
                   $run_query = mysqli_query($dbc, $query);   
                   
                    if(!$run_query) {
                        mysqli_rollback($dbc);//exit;
                        return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b> <br>$error_log <br> purchase_order table error");
                    }else{
                   
                       $query_details="INSERT INTO `purchase_order_details`(`order_id`, `product_id`,`item_code`, `mrp`,`rate`, `quantity`, `scheme_qty`, `purchase_inv`, "
                               . "`mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`,`gross_amt`, `taxable_amount`,`td_amount`,`sch_amt`,`spl_amt`, `cd_amount`,`atd_amt`, "
                               . "`cgst_amount`,`sgst_amount`, `igst_amount`,`gst_percentage`,`cr_note`,`dr_note`, `total_amount`) "
                               . "VALUES ('$order_id','$item_id','$icode','$mrp','$rate','$quantity','$scheme_qty','$invoice_no','$mfg_date','$exp_date','$curr_date','$batch_no','$rate',"
                               . " '$case_qty','$gross_amt','$tax_amt','$tdamt','$schamt','$splamt','$cd_amt','$atdamt','$cgst_amt','$sgst_amt','$igst_amt','$gst_per','$cr_note','$dr_note','$total_amt')";
                     // h1($query_details);
                       $run_query_details= mysqli_query($dbc, $query_details);
//                       if(!$run_query_details) {
//                        mysqli_rollback($dbc);//exit;
//                        return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b> <br>$error_log <br> purchase_order_details table error");
//                      }else{
//                           $chk_stock="SELECT id,qty FROM `stock` WHERE dealer_id='$dealer_id' AND product_id='$item_id' AND mrp='$mrp'";
//                           $run_chk_stock=mysqli_query($dbc, $chk_stock);
//                           $chk_stock_num=mysqli_num_rows($run_chk_stock);
//                           $chk_stock_data=  mysqli_fetch_assoc($run_chk_stock);
//                           if($chk_stock_num>0){
//                               $stock_qty=$chk_stock_data['qty'];
//                               $updated_stock_qty=$stock_qty+$quantity;
//                               $stock_id=$chk_stock_data['id']; 
//                              $up_stock="UPDATE `stock` SET `qty`='$updated_stock_qty',`update_date_time`=NOW() WHERE id='$stock_id'";
//                             // h1($up_stock);
//                               $run_up_stock=mysqli_query($dbc, $up_stock);
//                           }else{
//
//                              $query_stock="INSERT INTO `stock`(`product_id`,`batch_no`,`rate`,`dealer_rate`,`mrp`,`person_id`,"
//                           . " `csa_id`,`dealer_id`, `qty`, `salable_damage`,`nonsalable_damage`,`remaining`,`mfg`,`expire`, "
//                               . "`date`, `pr_rate`, `company_id`, `action`, `sync_status`,`update_date_time`) "
//                               . "VALUES ('$item_id','$batch_no','$rate','$rate','$mrp','0','$ss_id','$dealer_id','$quantity','0','0','0',"
//                                     . "'$mfg_date','$exp_date','$curr_date','0.00','1','1','0',NOW())";  
//                                $run_query_stock= mysqli_query($dbc, $query_stock);
// 				//h1($query_stock);
//                           }  
//                       }                       
                   }
                }
                   $inum++;
                   
                }
                 //exit;
                 mysqli_commit($dbc);
                 return array('status' =>'true', 'myreason' => 'Invoice imported successfully', 'ch_no' => $ch_data);
               
            }
        }
        
    }


   //********************************************** Closing Stock  **********************************************
    
   /*   public function closing_stock_save() {
        global $dbc;

        // if(true)
        if ($_FILES["excelFile"]["error"] > 0) {
            echo "Error: " . $_FILES["excelFile"]["error"] . "<br>";
        } else {         
            $a = $_FILES["excelFile"]["tmp_name"];        
            $csv_file = $a;
            $dealer_id=$_SESSION[SESS.'data']['dealer_id'];
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                 $inum=1;
                 $ch_data=array();

                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                   
                    //echo $num;
                    //for ($c=0; $c < $num; $c++) {
                    $curr_date = date('Y-m-d');
                    $result = $data;
                    $login_id= $_SESSION[SESS.'data']['id'];
                    $str = implode(",", $result);
                    $slice = explode(",", $str);


                    $ss_code = $slice[1];
                    $ss_id=  myrowval('ss_report', 'id', "ss_code='$ss_code'");   
                    $dealer_code = $slice[3];
                    $dealer_id=  myrowval('dealer', 'id', "d_code='$dealer_code'");
                    $order_id=$dealer_id.date('ymdhis');  
                    $icode = $slice[5];
                    $item_name = $slice[6];
                    $item_id=  myrowval('catalog_product', 'id', "itemcode='$icode'"); 
                    $mrp = $slice[7];
                    $rate = $slice[8];
                    $qty = $slice[9];                    
                    $mfg_date = $slice[10];
                    $exp_date = $slice[11];
                   
                   // mysqli_query($dbc, "START TRANSACTION");
                     
                    $query_stock="INSERT INTO `stock`(`product_id`,`batch_no`,`rate`,`dealer_rate`,`mrp`,`person_id`,"
                           . " `csa_id`,`dealer_id`, `qty`, `salable_damage`,`nonsalable_damage`,`remaining`,`mfg`,`expire`, "
                               . "`date`, `pr_rate`, `company_id`, `action`, `sync_status`,`update_date_time`) "
                               . "VALUES ('$item_id','0','$rate','$rate','$mrp','$login_id','$ss_id','$dealer_id','$qty','0','0','0',"
                                     . "'$mfg_date','$exp_date','$curr_date','0.00','1','1','0',NOW())";  
                     // h1($query_stock);
                               $run_query_stock= mysqli_query($dbc, $query_stock);
                   
                   
                }
                // exit;
                // mysqli_commit($dbc);
                 return array('status' =>'true', 'myreason' => 'Closing Stock imported successfully', 'item_details' => $item_name,'stock_qty' => $qty);
               
            }
        }
        
    }*/

      public function closing_stock_save() {
        global $dbc;

        // if(true)
        if ($_FILES["excelFile"]["error"] > 0) {
            echo "Error: " . $_FILES["excelFile"]["error"] . "<br>";
        } else {         
            $a = $_FILES["excelFile"]["tmp_name"];        
            $csv_file = $a;
            $dealer_id=$_SESSION[SESS.'data']['dealer_id'];
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                 $inum=1;
                 $ch_data=array();
                 //mysqli_autocommit($dbc, FALSE);
                //  mysqli_query($dbc, "START TRANSACTION");
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                   
                    //echo $num;
                    //for ($c=0; $c < $num; $c++) {
                    $curr_date = date('Y-m-d');
                    $result = $data;
                    $login_id= $_SESSION[SESS.'data']['id'];
                    $str = implode(",", $result);
                    $slice = explode(",", $str);


                    //$ss_code = $slice[1];
                    //$ss_id=  myrowval('ss_report', 'id', "ss_code='$ss_code'");   
                    $dealer_code = $slice[1];
                    $dealer_id=  myrowval('dealer', 'id', "d_code='$dealer_code'");
                    $order_id=date('ymdhis').$dealer_id;  
                    $icode = $slice[3];
                    $item_name = $slice[4];
                    $item_id=  myrowval('catalog_product', 'id', "itemcode='$icode'"); 
                    $mrp = $slice[5];
                    $rate = $slice[6];
                    $cases = $slice[7];
                    $pices = $slice[8];                                     
                    $mfg_date = $slice[9];
                    $exp_date = $slice[10];  
                    $sale_date = $slice[11];
                    $user_id = $slice[12];
                    $datetime = $slice[13];
                    
                    if(empty($mfg_date)){
                        $mfgdate = '0000-00-00';
                    }else{
                        $mfgdate = $mfg_date;
                    }
                    
                    if(empty($exp_date)){
                        $expdate = '0000-00-00';
                    }else{
                        $expdate = $exp_date;
                    }
                    
                    if(empty($sale_date)){
                        $saledate = '0000-00-00';
                    }else{
                        $saledate = $sale_date;
                    }
                   
                   // mysqli_query($dbc, "START TRANSACTION");
                     
                   /* $query_stock="INSERT INTO `stock`(`product_id`,`batch_no`,`rate`,`dealer_rate`,`mrp`,`person_id`,"
                           . " `csa_id`,`dealer_id`, `qty`, `salable_damage`,`nonsalable_damage`,`remaining`,`mfg`,`expire`, "
                               . "`date`, `pr_rate`, `company_id`, `action`, `sync_status`,`update_date_time`) "
                               . "VALUES ('$item_id','0','$rate','$rate','$mrp','$login_id','$ss_id','$dealer_id','$qty','0','0','0',"
                                     . "'$mfg_date','$exp_date','$curr_date','0.00','1','1','0',NOW())";  
                     // h1($query_stock);
                               $run_query_stock= mysqli_query($dbc, $query_stock);*/
                    $query_bal = "INSERT INTO `dealer_bal_stock`(`catalog_id`, `mrp`, `base_price`, `user_id`,"
                            . " `order_id`, `mobile_datetime`, `dealer_id`, `server_datetime`, `mfg_date`, `cases`, "
                            . "`pieces`, `exp_date`, `sale_date`, `balance_total_value`,`update_data`) VALUES ('$item_id','$mrp','$rate','$user_id',"
                            . "'$order_id','$datetime','$dealer_id',NOW(),'$mfgdate','$cases','$pices','$expdate','$saledate','0','1')";
                    //h1($query_bal); //exit;
                    $run_query_stock= mysqli_query($dbc, $query_bal);
                    if(!$run_query_stock) {
                        //mysqli_rollback($dbc);//exit;
                        return array('status' =>'false', 'myreason' => "dealer_bal_stock table error");
                      }
                   
                }
                // exit;
                // mysqli_commit($dbc);
                 return array('status' =>'true', 'myreason' => 'Dealer Balance Stock imported successfully', 'item_details' => $item_name,'stock_qty' => $qty);
               
            }
        }
        
    }


}

// class end here
?>
