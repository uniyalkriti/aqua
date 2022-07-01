<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';


if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    echo"Request method must be POST!";exit;
    //throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
     echo"Content type must be: application/json";exit;
   // throw new Exception('Content type must be: application/json');
}

//Receive the RAW post data.
 $content = trim(file_get_contents("php://input"));
//exit;
//Attempt to decode the incoming RAW post data from JSON.
$data = json_decode($content, true);

//If json_decode failed, the JSON is invalid.
if(!is_array($data)){
     echo"Received content contained invalid JSON!";exit;
    //throw new Exception('Received content contained invalid JSON!');
}


if(!empty($data)){
//     echo"<pre>";
//print_r($data);
//echo"</pre>";  exit;
    $dealer_id=$data[dealer_id];
    $q_login = "SELECT id,dealer_status FROM dealer WHERE id='$dealer_id' LIMIT 0,1";
    $dealer_qry = mysqli_query($dbc, $q_login);
    $dealer_data=  mysqli_fetch_array($dealer_qry);
    if($dealer_data['dealer_status'] == '1')  
    {  
      //############################### All Array List  ##########################################
     $challan_order=$data[challan_order];
     $challan_order_details=$data[challan_order_details];
     $retailer=$data[retailer];
     $daily_dispatch=$data[daily_dispatch];
     $daily_dispatch_details=$data[daily_dispatch_details];
     $payment_collection=$data[payment_collection];
     $damage_order=$data[damage_order];
     $damage_order_details=$data[damage_order_details];
     $stock=$data[stock];
     $receive_order=$data[receive_order];
     $receive_order_details=$data[receive_order_details];
     $purchase_order=$data[purchase_order];
     $purchase_order_details=$data[purchase_order_details];
     
      //############################### Data Transaction ##########################################
     mysqli_query($dbc, "START TRANSACTION");
    
  //#################################################  $challan_order  #########################################################   
     
   if(isset($challan_order) && !empty($challan_order)){
    $tot_challan_order = count($challan_order);
    $co = 0;
        $str_co = array();
        $update_co = array();
    while($co<$tot_challan_order){
        $id = $challan_order[$co]['id'];
        $ch_no = $challan_order[$co]['ch_no'];
                $ch_created_by = $challan_order[$co]['ch_created_by'];
                $ch_dealer_id = $challan_order[$co]['ch_dealer_id'];
                $ch_retailer_id = $challan_order[$co]['ch_retailer_id'];
                $ch_user_id = $challan_order[$co]['ch_user_id'];
                $dispatch_date = $challan_order[$co]['dispatch_date'];
                $ch_date = $challan_order[$co]['ch_date'];
                $date_added = $challan_order[$co]['date_added'];
                $dispatch_status = $challan_order[$co]['dispatch_status'];
                $discount = $challan_order[$co]['discount'];
                $sesId = $challan_order[$co]['sesId'];
                $remark = $challan_order[$co]['remark'];
                $invoice_type = $challan_order[$co]['invoice_type'];
                $payment_status = $challan_order[$co]['payment_status'];
                $isclaim = $challan_order[$co]['isclaim'];
                $istarget_claim = $challan_order[$co]['istarget_claim'];
                $discount_per = $challan_order[$co]['discount_per'];
                $discount_amt = $challan_order[$co]['discount_amt'];
                $amount = $challan_order[$co]['amount'];
                $remaining = $challan_order[$co]['remaining'];
                $update_co[]= $id;
                $str_co[] = "('$id','$ch_no','$ch_created_by', '$ch_dealer_id','$ch_retailer_id','$ch_user_id','$dispatch_date','$ch_date','$date_added','1','$dispatch_status',"
                          . "'$discount','$sesId','$remark','0','$invoice_type','$payment_status','$isclaim','$istarget_claim','$discount_per'
                ,'$discount_amt','$amount','$remaining')";
                
    $co++;
    }
         $values_co = implode(',' ,$str_co);
         $qry_co = "INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`,`ch_user_id`, `dispatch_date`, `ch_date`,`date_added`,"
                        . " `company_id`,`dispatch_status`, `discount`, `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`, `isclaim`, "
                        . "`istarget_claim`, `discount_per`, `discount_amt`, `amount`, `remaining`) VALUES $values_co";
        $run_co=mysqli_query($dbc, $qry_co);
           if (!$run_co) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Challan Order !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_co=  implode(',', $update_co);
                $up_co="UPDATE challan_order SET sync_status=0 WHERE id IN($uid_co)";

            }
        
}
 //#################################################  $challan_order_details  #########################################################

 if(isset($challan_order_details) && !empty($challan_order_details)){
    $tot_challan_order_details = count($challan_order_details);
    $cod = 0;
        $str_cod = array();
    while($cod<$tot_challan_order_details){
        //$id = $challan_order_details[$cod]['id'];
        $ch_id = $challan_order_details[$cod]['ch_id'];
                $product_id = $challan_order_details[$cod]['product_id'];
                $supply_status = $challan_order_details[$cod]['supply_status'];
                $hsn_code = $challan_order_details[$cod]['hsn_code'];
                $catalog_details_id = $challan_order_details[$cod]['catalog_details_id'];
                $batch_no = $challan_order_details[$cod]['batch_no'];
                $tax = $challan_order_details[$cod]['tax'];
                $vat_amt = $challan_order_details[$cod]['vat_amt'];
                $qty = $challan_order_details[$cod]['qty'];
                $product_rate = $challan_order_details[$cod]['product_rate'];
                $free_qty = $challan_order_details[$cod]['free_qty'];
                $order_id = $challan_order_details[$cod]['order_id'];
                $user_id = $challan_order_details[$cod]['user_id'];
                $mrp = $challan_order_details[$cod]['mrp'];
                $cd = $challan_order_details[$cod]['cd'];
                $cd_type = $challan_order_details[$cod]['cd_type'];
                $cd_amt = $challan_order_details[$cod]['cd_amt'];
                $dis_type = $challan_order_details[$cod]['dis_type'];
                $dis_amt = $challan_order_details[$cod]['dis_amt'];
                $dis_percent = $challan_order_details[$cod]['dis_percent'];
                $taxable_amt = $challan_order_details[$cod]['taxable_amt'];
                $remain_amount = $challan_order_details[$cod]['remain_amount'];
                $sale_order_id = $challan_order_details[$cod]['sale_order_id'];
                

                
                $str_cod[] = "('$ch_id','$product_id', '$supply_status','$hsn_code','$catalog_details_id','$batch_no','$tax','$vat_amt','$qty',"
                          . "'$product_rate','$free_qty','$order_id','$user_id','$mrp','$cd','$cd_type','$cd_amt'
                ,'$dis_type','$dis_amt','$dis_percent','$taxable_amt','$remain_amount','$sale_order_id')";
                
    $cod++;
    }
         $values_cod = implode(',' ,$str_cod);
         $qry_cod = "INSERT INTO `challan_order_details`( `ch_id`, `product_id`, `supply_status`,`hsn_code`, `catalog_details_id`, `batch_no`, `tax`,"
                 . " `vat_amt`, `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, `dis_type`,"
                 . " `dis_amt`, `dis_percent`, `taxable_amt`, `remain_amount`,`sale_order_id`) VALUES $values_cod";
         //h1($qry_cod);exit;
        $run_cod=mysqli_query($dbc, $qry_cod);
           if (!$run_cod) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Challan Order Details !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }
        
}

 //#################################################  $retailer  #########################################################

if(isset($retailer) && !empty($retailer)){
    $tot_retailer = count($retailer);
    $ret = 0;
        $str_ret = array();
        $update_ret = array();
    while($ret<$tot_retailer){
        $id = $retailer[$ret]['id'];
        $name = $retailer[$ret]['name'];
                $image_name = $retailer[$ret]['image_name'];
                $dealer_id = $retailer[$ret]['dealer_id'];
                $location_id = $retailer[$ret]['location_id'];
                $company_id = $retailer[$ret]['company_id'];
                $address = $retailer[$ret]['address'];
                $email = $retailer[$ret]['email'];
                $contact_per_name = $retailer[$ret]['contact_per_name'];
                $landline = $retailer[$ret]['landline'];
                $other_numbers = $retailer[$ret]['other_numbers'];
                $tin_no = $retailer[$ret]['tin_no'];
                $pin_no = $retailer[$ret]['pin_no'];
                $outlet_type_id = $retailer[$ret]['outlet_type_id'];
                $card_swipe = $retailer[$ret]['card_swipe'];
                $bank_branch_id = $retailer[$ret]['bank_branch_id'];
                $current_account = $retailer[$ret]['current_account'];
                $avg_per_month_pur = $retailer[$ret]['avg_per_month_pur'];
                $lat_long = $retailer[$ret]['lat_long'];
                $mncmcclatcellid = $retailer[$ret]['mncmcclatcellid'];
                $track_address = $retailer[$ret]['track_address'];
                $created_on = $retailer[$ret]['created_on'];
                $created_by_person_id = $retailer[$ret]['created_by_person_id'];
                $status = $retailer[$ret]['status'];
                $retailer_status = $retailer[$ret]['retailer_status'];
                $deactivated_by_user = $retailer[$ret]['deactivated_by_user'];
                $deactivated_date_time = $retailer[$ret]['deactivated_date_time'];
                $update_ret[]= $id;

                
                $str_ret[] = "('$id','$name','$image_name', '$dealer_id','$location_id','1','$address','$email','$contact_per_name',"
                          . "'$landline','$other_numbers','$tin_no','$pin_no','$outlet_type_id','$card_swipe','$bank_branch_id','$current_account'
                ,'$avg_per_month_pur','$lat_long','$mncmcclatcellid','$track_address','$created_on','$created_by_person_id','$status','0','$retailer_status','$deactivated_by_user','$deactivated_date_time')";
                
    $ret++;
    }
         $values_ret = implode(',' ,$str_ret);
         $qry_ret ="INSERT INTO `retailer`(`id`, `name`, `image_name`, `dealer_id`, `location_id`, `company_id`, `address`, `email`,"
                 . " `contact_per_name`, `landline`, `other_numbers`, `tin_no`, `pin_no`, `outlet_type_id`, `card_swipe`, `bank_branch_id`,"
                 . " `current_account`, `avg_per_month_pur`, `lat_long`, `mncmcclatcellid`, `track_address`, `created_on`, `created_by_person_id`,"
                 . " `status`, `sync_status`, `retailer_status`,`deactivated_by_user`,`deactivated_date_time`) VALUES $values_ret";
        $run_ret=mysqli_query($dbc, $qry_ret);
           if (!$run_ret) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Retailer !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_ret=  implode(',', $update_ret);
                $up_ret="UPDATE retailer SET sync_status=0 WHERE id IN($uid_ret)";

            }
        
}    

//#######################################################  $daily_dispatch  ################################################

if(isset($daily_dispatch) && !empty($daily_dispatch)){
    $tot_daily_dispatch = count($daily_dispatch);
    $dd = 0;
        $str_dd = array();
        $update_dd = array();
    while($dd<$tot_daily_dispatch){
        $dispatch_id = $daily_dispatch[$dd]['dispatch_id'];
        $dispatch_no = $daily_dispatch[$dd]['dispatch_no'];
                $dealer_id = $daily_dispatch[$dd]['dealer_id'];
                $van_no = $daily_dispatch[$dd]['van_no'];
                $dispatch_date = $daily_dispatch[$dd]['dispatch_date'];
                $total_bills = $daily_dispatch[$dd]['total_bills'];
                $total_product = $daily_dispatch[$dd]['total_product'];
                $company_id = $daily_dispatch[$dd]['company_id'];
                $created_by = $daily_dispatch[$dd]['created_by'];
                $route = $daily_dispatch[$dd]['route'];
                $delivery_status = $daily_dispatch[$dd]['delivery_status'];
                $update_dd[]= $dispatch_id;
                $str_dd[] = "('$dispatch_id','$dispatch_no','$dealer_id', '$van_no','$dispatch_date','$total_bills','$total_product','1','$created_by',"
                          . "'$route','0','$delivery_status')";
                
    $dd++;
    }
         $values_dd = implode(',' ,$str_dd);
         $qry_dd ="INSERT INTO `daily_dispatch`(`dispatch_id`, `dispatch_no`, `dealer_id`, `van_no`, `dispatch_date`,"
                 . " `total_bills`, `total_product`, `company_id`, `created_by`, `route`, `sync_status`,"
                 . " `delivery_status`) VALUES $values_dd";
       $run_dd=mysqli_query($dbc, $qry_dd);
           if (!$run_dd) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Daily Dispatch !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_dd=  implode(',', $update_dd);
                $up_dd="UPDATE daily_dispatch SET sync_status=0 WHERE dispatch_id IN($uid_dd)";
                
                
            }
        
}       
//#######################################################  $daily_dispatch_details  ################################################

if(isset($daily_dispatch_details) && !empty($daily_dispatch_details)){
    $tot_daily_dispatch_details = count($daily_dispatch_details);
    $ddd = 0;
        $str_ddd = array();
    while($ddd<$tot_daily_dispatch_details){
        $dispatch_id = $daily_dispatch_details[$ddd]['dispatch_id'];
        $ch_id = $daily_dispatch_details[$ddd]['ch_id'];
                $sortorder = $daily_dispatch_details[$ddd]['sortorder'];
                
                
                $str_ddd[] = "('$dispatch_id','$ch_id','$sortorder')";
                
    $ddd++;
    }
         $values_ddd = implode(',' ,$str_ddd);
         $qry_ddd ="INSERT INTO `daily_dispatch_details`(`dispatch_id`, `ch_id`, `sortorder`) VALUES $values_ddd";
         $run_ddd=mysqli_query($dbc, $qry_ddd);
           if (!$run_ddd) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Daily Dispatch Deatils !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }
        
}

//#######################################################  Payment Collection  ################################################

if(isset($payment_collection) && !empty($payment_collection)){
    $tot_payment_collection = count($payment_collection);
    $pc = 0;
        $str_pc = array();
        $update_pc = array();
    while($pc<$tot_payment_collection){
                $pid = $payment_collection[$pc]['id'];
                $payment_id = $payment_collection[$pc]['payment_id'];
                $dealer_id = $payment_collection[$pc]['dealer_id'];
                $challan_id = $payment_collection[$pc]['challan_id'];
                $retailer_id = $payment_collection[$pc]['retailer_id'];
                $total_amount = $payment_collection[$pc]['total_amount'];
                $pay_mode = $payment_collection[$pc]['pay_mode'];
                $bank_name = $payment_collection[$pc]['bank_name'];
                $chq_no = $payment_collection[$pc]['chq_no'];
                $chq_date = $payment_collection[$pc]['chq_date'];
                $Remark = $payment_collection[$pc]['Remark'];
                $pay_date_time = $payment_collection[$pc]['pay_date_time'];
                $image = $payment_collection[$pc]['image'];
                $update_pc[]= $pid;

                
                $str_pc[] = "('$payment_id','$dealer_id','$challan_id', '$retailer_id','$total_amount','$pay_mode','$bank_name','$chq_no','$chq_date',"
                          . "'$Remark','$pay_date_time','$image','0')";
                
    $pc++;
    }
         $values_pc = implode(',' ,$str_pc);
         $qry_pc ="INSERT INTO `payment_collection`(`payment_id`,`dealer_id`, `challan_id`, `retailer_id`, `total_amount`, `pay_mode`,"
                 . " `bank_name`, `chq_no`, `chq_date`, `Remark`, `pay_date_time`, `image`, `sync_status`) VALUES $values_pc";
       $run_pc=mysqli_query($dbc, $qry_pc);
           if (!$run_pc) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Payment Collection !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_pc=  implode(',', $update_pc);
                $up_pc="UPDATE payment_collection SET sync_status=0 WHERE id IN($uid_pc)";
               
                
            }
        
}

//#######################################################  Damage Order  ################################################

if(isset($damage_order) && !empty($damage_order)){
    $tot_damage_order = count($damage_order);
    $do = 0;
        $str_do = array();
        $update_do = array();
    while($do<$tot_damage_order){
                $did = $damage_order[$do]['id'];
                $ch_no = $damage_order[$do]['ch_no'];
                $ch_created_by = $damage_order[$do]['ch_created_by'];
                $ch_dealer_id = $damage_order[$do]['ch_dealer_id'];
                $ch_retailer_id = $damage_order[$do]['ch_retailer_id'];
                $dispatch_date = $damage_order[$do]['dispatch_date'];
                $ch_date = $damage_order[$do]['ch_date'];
                $company_id = $damage_order[$do]['company_id'];
                $dispatch_status = $damage_order[$do]['dispatch_status'];
                $sesId = $damage_order[$do]['sesId'];
                $remark = $damage_order[$do]['remark'];
                $complaint_id = $damage_order[$do]['complaint_id'];
                $damage_set = $damage_order[$do]['damage_set'];
                $actual_amount = $damage_order[$do]['actual_amount'];
                $saleable_non_saleable = $damage_order[$do]['saleable_non_saleable'];
                $update_do[]= $did;

                
                $str_do[] = "('$ch_no','$ch_created_by', '$ch_dealer_id','$ch_retailer_id','$dispatch_date','$ch_date','1','$dispatch_status',"
                          . "'$sesId','$remark','$complaint_id','$damage_set','$actual_amount','$saleable_non_saleable','0')";
                
    $do++;
    }
         $values_do = implode(',' ,$str_do);
         $qry_do ="INSERT INTO `damage_order`(`ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`,"
                 . " `ch_date`, `company_id`, `dispatch_status`, `sesId`, `remark`, `complaint_id`,`damage_set`, `actual_amount`, `saleable_non_saleable`,"
                 . " `sync_status`) VALUES $values_do";
       $run_do=mysqli_query($dbc, $qry_do);
           if (!$run_do) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Damage Order !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_do=  implode(',', $update_do);
                $up_do="UPDATE damage_order SET sync_status=0 WHERE id IN($uid_do)";
 
            }
        
}

//#######################################################  Damage Order Details  ################################################

if(isset($damage_order_details) && !empty($damage_order_details)){
    $tot_damage_order_details = count($damage_order_details);
    $dod = 0;
        $str_dod = array();
    while($dod<$tot_damage_order_details){
        //$id = $damage_order_details[$dod]['id'];
        $ch_id = $damage_order_details[$dod]['ch_id'];
                $product_id = $damage_order_details[$dod]['product_id'];
                $catalog_details_id = $damage_order_details[$dod]['catalog_details_id'];
                $batch_no = $damage_order_details[$dod]['batch_no'];
                $tax = $damage_order_details[$dod]['tax'];
                $qty = $damage_order_details[$dod]['qty'];
                $product_rate = $damage_order_details[$dod]['product_rate'];
                $free_qty = $damage_order_details[$dod]['free_qty'];
                $order_id = $damage_order_details[$dod]['order_id'];
                $user_id = $damage_order_details[$dod]['user_id'];
                $mrp = $damage_order_details[$dod]['mrp'];
                $cd = $damage_order_details[$dod]['cd'];
                $cd_type = $damage_order_details[$dod]['cd_type'];
                $cd_amt = $damage_order_details[$dod]['cd_amt'];
                $dis_type = $damage_order_details[$dod]['dis_type'];
                $dis_amt = $damage_order_details[$dod]['dis_amt'];
                $dis_percent = $damage_order_details[$dod]['dis_percent'];
                $taxable_amt = $damage_order_details[$dod]['taxable_amt'];
                $actual_amount = $damage_order_details[$dod]['actual_amount'];
                $replace_product_id = $damage_order_details[$dod]['replace_product_id'];
                $replace_mrp = $damage_order_details[$dod]['replace_mrp'];
                $replace_rate = $damage_order_details[$dod]['replace_rate'];
                $replace_quantity = $damage_order_details[$dod]['replace_quantity'];
                $replace_amount = $damage_order_details[$dod]['replace_amount'];
                
                
                
                $str_dod[] = "('$ch_id','$product_id','$catalog_details_id','$batch_no','$tax','$qty',"
                          . "'$product_rate','$free_qty','$order_id','$user_id','$mrp','$cd','$cd_type','$cd_amt'
                ,'$dis_type','$dis_amt','$dis_percent','$taxable_amt','$actual_amount','$replace_product_id','$replace_mrp',"
                        . "'$replace_rate','$replace_quantity','$replace_amount')";
                
    $dod++;
    }
         $values_dod = implode(',' ,$str_dod);
         $qry_dod = "INSERT INTO `damage_order_details`(`ch_id`, `product_id`, `catalog_details_id`, `batch_no`, `tax`,"
                 . " `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, `dis_type`, "
                 . "`dis_amt`, `dis_percent`, `taxable_amt`, `actual_amount`, `replace_product_id`, `replace_mrp`, `replace_rate`, "
                 . "`replace_quantity`, `replace_amount`) VALUES $values_dod";
        $run_dod=mysqli_query($dbc, $qry_dod);
           if (!$run_dod) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Damage Order Details !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }
        
}

//#######################################################  Stock  ################################################

if(isset($stock) && !empty($stock)){
    $tot_stock = count($stock);
    $stk = 0;
        $str_stk = array();
        $update_stk = array();
    while($stk<$tot_stock){
               $sid = $stock[$stk]['id'];
                $product_id = $stock[$stk]['product_id'];
                $batch_no = $stock[$stk]['batch_no'];
                $rate = $stock[$stk]['rate'];
                $dealer_rate = $stock[$stk]['dealer_rate'];
                $mrp = $stock[$stk]['mrp'];
                $person_id = $stock[$stk]['person_id'];
                $csa_id = $stock[$stk]['csa_id'];
                $dealer_id = $stock[$stk]['dealer_id'];
                $qty = $stock[$stk]['qty'];
                $salable_damage = $stock[$stk]['salable_damage'];
                $nonsalable_damage = $stock[$stk]['nonsalable_damage'];
                $remaining = $stock[$stk]['remaining'];
                $mfg = $stock[$stk]['mfg'];
                $expire = $stock[$stk]['expire'];
                $date = $stock[$stk]['date'];
                $pr_rate = $stock[$stk]['pr_rate'];
                $company_id = $stock[$stk]['company_id'];
                $action = $stock[$stk]['action'];
                $update_stk[]= $sid;
                
                
                $str_stk[] = "('$product_id','$batch_no','$rate','$dealer_rate','$mrp',"
                          . "'$person_id','$csa_id','$dealer_id','$qty','$salable_damage','$nonsalable_damage','$remaining','$mfg'
                ,'$expire','$date','$pr_rate','1','$action','0')";
                
                $stock_pid="SELECT `product_id` FROM `stock` WHERE `dealer_id`='$dealer_id' AND `product_id`=$product_id";
               $run_stk_pid= mysqli_query($dbc, $stock_pid);
        //    $count_stk_pid=mysqli_num_rows($run_stk_pid);
        
        if(mysqli_num_rows($run_stk_pid)>0){
           $qry_stk="UPDATE `stock` SET `product_id`='$product_id',`batch_no`='$batch_no',`rate`='$rate',`dealer_rate`='$dealer_rate',`mrp`='$mrp', "
                   . "`person_id`='$person_id',`csa_id`='$csa_id',`dealer_id`='$dealer_id',`qty`='$qty',`salable_damage`='$salable_damage',"
                   . "`nonsalable_damage`='$nonsalable_damage',`remaining`='$remaining',`mfg`='$mfg',`expire`='$expire',`date`='$date',"
                   . "`pr_rate`='$pr_rate',`company_id`='1',`action`='$action',`sync_status`='0' WHERE `dealer_id`='$dealer_id' AND `product_id`=$product_id"; 
        }else{
    
         //$values_stk = implode(',' ,$str_stk);
         $qry_stk = "INSERT INTO `stock`(`product_id`, `batch_no`, `rate`,`dealer_rate`,`mrp`,`person_id`, `csa_id`, `dealer_id`, `qty`,"
                 . " `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`,"
                 . " `action`, `sync_status`) "
                 . "VALUES ('$product_id','$batch_no','$rate','$dealer_rate','$mrp','$person_id','$csa_id','$dealer_id','$qty',"
                 . "'$salable_damage','$nonsalable_damage','$remaining','$mfg','$expire','$date','$pr_rate','1','$action','0')";
        }
       // echo "$qry_stk";
        $run_stk=mysqli_query($dbc, $qry_stk);
         if (!$run_stk) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Stock !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_stk=  implode(',', $update_stk);
                $up_stk="UPDATE stock SET sync_status=0 WHERE id IN($uid_stk)";
            }    
    $stk++;
    }
//         $values_stk = implode(',' ,$str_stk);
//         $qry_stk = "INSERT INTO `stock`(`product_id`, `batch_no`, `rate`,`dealer_rate`,`mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`,"
//                 . " `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`,"
//                 . " `action`, `sync_status`) VALUES $values_stk";
//        $run_stk=mysqli_query($dbc, $qry_stk);
//           if (!$run_stk) {
//            mysqli_rollback($dbc);
//            echo'<span class="awm"><h3><b>Error in Stock Save !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
//            }else{
//                $uid_stk=  implode(',', $update_stk);
//                $up_stk="UPDATE stock SET sync_status=0 WHERE id IN($uid_stk)";
//            
//        
//                    }
                 }
                 
                 //#######################################################  Receive Order  ################################################
                 
                 
if(isset($receive_order) && !empty($receive_order)){
    $tot_receive_order = count($receive_order);
    $ro = 0;
        $str_ro = array();
        $update_ro = array();
    while($ro<$tot_receive_order){
               $roid = $receive_order[$ro]['id'];
                $order_id = $receive_order[$ro]['order_id'];
                $dealer_id = $receive_order[$ro]['dealer_id'];
                $created_date = $receive_order[$ro]['created_date'];
                $created_person_id = $receive_order[$ro]['created_person_id'];
                $order_date = $receive_order[$ro]['order_date'];
                $receive_date = $receive_order[$ro]['receive_date'];
                $date_time = $receive_order[$ro]['date_time'];
                $ch_date = $receive_order[$ro]['ch_date'];
                $challan_no = $receive_order[$ro]['challan_no'];
                $grn = $receive_order[$ro]['grn'];
                $csa_id = $receive_order[$ro]['csa_id'];
                $update_ro[]= $roid;
                
                
                $str_ro[] = "('$order_id','$dealer_id','$created_date',"
                          . "'$created_person_id','$order_date','$receive_date','$date_time','$ch_date','$challan_no','$grn','$csa_id','0')";
                
    $ro++;
    }
         $values_ro = implode(',' ,$str_ro);
         $qry_ro = "INSERT INTO `receive_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`,"
                 . " `order_date`, `receive_date`, `date_time`, `ch_date`, `challan_no`,`grn`, `csa_id`, `sync_status`)"
                 . " VALUES $values_ro";
        $run_ro=mysqli_query($dbc, $qry_ro);
           if (!$run_ro) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Receive Order !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_ro=  implode(',', $update_ro);
                $up_ro="UPDATE receive_order SET sync_status=0 WHERE id IN($uid_ro)"; 
            }
        
}

//#######################################################  Receive Order Details  ################################################

if(isset($receive_order_details) && !empty($receive_order_details)){
    $tot_receive_order_details = count($receive_order_details);
    $rod = 0;
        $str_rod = array();
    while($rod<$tot_receive_order_details){
        //$id = $receive_order_details[$rod]['id'];
                $order_id = $receive_order_details[$rod]['order_id'];
                $product_id = $receive_order_details[$rod]['product_id'];
                $rate = $receive_order_details[$rod]['rate'];
                $quantity = $receive_order_details[$rod]['quantity'];
                $purchase_inv = $receive_order_details[$rod]['purchase_inv'];
                $mfg_date = $receive_order_details[$rod]['mfg_date'];
                $expiry_date = $receive_order_details[$rod]['expiry_date'];
                $batch_no = $receive_order_details[$rod]['batch_no'];
                $pr_rate = $receive_order_details[$rod]['pr_rate'];
                $cases = $receive_order_details[$rod]['cases'];
                
                
                
                $str_rod[] = "('$order_id','$product_id','$rate',"
                          . "'$quantity','$purchase_inv','$mfg_date','$expiry_date','$batch_no','$pr_rate','$cases')";
                
    $rod++;
    }
         $values_rod = implode(',' ,$str_rod);
         $qry_rod = "INSERT INTO `receive_order_details`(`order_id`, `product_id`, `rate`, `quantity`, `purchase_inv`, `mfg_date`,"
                 . " `expiry_date`, `batch_no`, `pr_rate`, `cases`)"
                 . " VALUES $values_rod";
        $run_rod=mysqli_query($dbc, $qry_rod);
           if (!$run_rod) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Receive Order Details !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }        
}
  // exit;
//#######################################################  Purchase Order  ################################################

if(isset($purchase_order) && !empty($purchase_order)){
    $tot_purchase_order = count($purchase_order);
    $po = 0;
        $str_po = array();
        $update_po = array();
    while($po<$tot_purchase_order){
                $poid = $purchase_order[$po]['id'];
                $order_id = $purchase_order[$po]['order_id'];
                $dealer_id = $purchase_order[$po]['dealer_id'];
                $created_date = $purchase_order[$po]['created_date'];
                $created_person_id = $purchase_order[$po]['created_person_id'];
                $order_date = $purchase_order[$po]['order_date'];
                $receive_date = $purchase_order[$po]['receive_date'];
                $date_time = $purchase_order[$po]['date_time'];
                $company_id = $purchase_order[$po]['company_id'];
                $ch_date = $purchase_order[$po]['ch_date'];
                $challan_no = $purchase_order[$po]['challan_no'];
                $csa_id = $purchase_order[$po]['csa_id'];
                $update_po[]= $poid;
                
                $str_po[] = "('$order_id','$dealer_id','$created_date',"
                          . "'$created_person_id','$order_date','$receive_date','$date_time','1','$ch_date','$challan_no','$csa_id','0')";
                
    $po++;
    }
         $values_po = implode(',' ,$str_po);
         $qry_po = "INSERT INTO `purchase_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`,"
                 . " `order_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, `challan_no`, `csa_id`, `sync_status`)"
                 . " VALUES $values_po";
        $run_po=mysqli_query($dbc, $qry_po);
           if (!$run_po) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Purchase Order !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }else{
                $uid_po=  implode(',', $update_po);
                $up_po="UPDATE purchase_order SET sync_status=0 WHERE id IN($uid_po)"; 
            }        
}
//#######################################################  Purchase Order Details  ################################################

if(isset($purchase_order_details) && !empty($purchase_order_details)){
    $tot_purchase_order_details = count($purchase_order_details);
    $pod = 0;
        $str_pod = array();
    while($pod<$tot_purchase_order_details){
                //$id = $purchase_order_details[$pod]['id'];
                $order_id = $purchase_order_details[$pod]['order_id'];
                $product_id = $purchase_order_details[$pod]['product_id'];
                $rate = $purchase_order_details[$pod]['rate'];
                $mrp = $purchase_order_details[$pod]['mrp'];
                $quantity = $purchase_order_details[$pod]['quantity'];
                $scheme_qty = $purchase_order_details[$pod]['scheme_qty'];
                $purchase_inv = $purchase_order_details[$pod]['purchase_inv'];
                $mfg_date = $purchase_order_details[$pod]['mfg_date'];
                $expiry_date = $purchase_order_details[$pod]['expiry_date'];
                $receive_date = $purchase_order_details[$pod]['receive_date'];
                $batch_no = $purchase_order_details[$pod]['batch_no'];
                $pr_rate = $purchase_order_details[$pod]['pr_rate'];
                $cases = $purchase_order_details[$pod]['cases'];
                
                
                
                $str_pod[] = "('$order_id','$product_id','$rate','$mrp',"
                          . "'$quantity','$scheme_qty','$purchase_inv','$mfg_date','$expiry_date','$receive_date','$batch_no','$pr_rate','$cases')";
                
    $pod++;
    }
         $values_pod = implode(',' ,$str_pod);
         $qry_pod = "INSERT INTO `purchase_order_details`(`order_id`, `product_id`, `rate`,`mrp`, `quantity`, "
                 . "`scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`)"
                 . " VALUES $values_pod";
        $run_pod=mysqli_query($dbc, $qry_pod);
           if (!$run_pod) {
            mysqli_rollback($dbc);
            echo'<span class="awm"><h3><b>Error in Purchase Order Details !!</b><br/>Data not Sync on server please contact to support team</h3></span>';exit;
            }        
}
      if(mysqli_commit($dbc)){
       $return_response=array("dealer_id"=>"$dealer_id"
        ,"challan_order"=>"$up_co"
        ,"retailer"=>"$up_ret"                            
        ,"daily_dispatch"=>"$up_dd"
        ,"payment_collection"=>"$up_pc"
        ,"damage_order"=>"$up_do"
        ,"stock"=>"$up_stk"
        ,"receive_order"=>"$up_ro"
        ,"purchase_order"=>"$up_po");
echo $response_data = json_encode($return_response); exit; 
                
               //echo $jsonData =file_get_contents('http://192.168.0.124/msell-dsgroup-dms/webservices/server_return_data.php?dealer_id='.$dealer_id.'');
            }
    }
 
}
?>