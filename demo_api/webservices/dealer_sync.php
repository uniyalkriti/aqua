<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
 $unique_id = array();
if(isset($_POST['response'])){$check=$_POST['response'];} else ////$check='';
//$check='  {"response":{"PurchaseOrder":[{"order_id":"125210072017121912","dealer_id":"1252","created_date":"2017-10-07","person_id":"705","order_date":"2017-10-07","csa_id":"1","total_ammount":"60.0","received_status":"0","sync_status":"0"}],"PurchaseOrderDetail":[{"order_id":"125210072017121912","product_id":"59","rate":"5.00","qty":"12","received_status":"0","sync_status":"0"}],"PrimarySaleOrder":[{"order_id":"125210072017121912","dealer_id":"1252","created_date":"2017-10-07","person_id":"705","order_date":"2017-10-07","challan_no":"567567557","csa_id":"1","received_status":"0","sync_status":"0"}],"PrimarySaleOrderDetail":[{"order_id":"125210072017121912","product_id":"59","rate":"5.00","quantity":"12","batch_no":"6666","mfg":"2017-10-07","base_price":"3.75","received_status":"0","sync_status":"0"}],"Challan":[{"challan_no":"CATC-M-1252-1-2017-2018","challan_id":"1252201710071219","person_id":"705","dealer_id":"1252","retailer_id":"20170118181224157","dispatch_date":"","challan_date":"2017-10-07","company_id":"1","dispatch_status":"0","payment_status":"2","is_claimed":"0","is_target_cliamed":"0","amount":"1968.75","remaining_amount":"1958.75","discount_percent":"5","discount_amount":"98.44","sync_status":"0"}],"ChallanDetail":[{"challan_no":"CATC-M-1252-1-2017-2018","dealer_id":"1252","product_id":"15","tax":"5","quantity":"5","product_rate":"375.00","scheme_quantity":"","mrp":"500.00","cd":"","cd_type":"1","cd_ammount":"","trade_discount_type":"Percent(%)","trade_discount_ammount":"","trade_discount_percent":"","taxable_ammount":"1875","sync_status":"0"}],"DistributorProfile":[{"distributer_name":"Devendra  Dealer","distributer_email":"devd@gmail.com","distributer_mobile_no":"1345627890","distributer_tin_no":"","distributer_address":"","distributer_dob":"null"}],"DailyDispatch":[{"dispatch_id":"125210072017122000","dispatch_no":"DS1252\/16-17\/2","dealer_id":"1252","van_no":"DL-01-2515","dispatch_date":"2017-10-07","total_bills":"1","total_product":"1"}],"PaymentCollection":[{"payment_id":"125220171007122020","dealer_id":"1252","challan_id":"1252201710071219","retailer_id":"20170118181224157","payment_mode":"0","amount":"1958.75","bank_branch":"","cheque_no":"","cheque_date":"","payment_date":"2017-10-07","payment_time":"12:20:12","remark":"ghjgj","sync_status":"0","delete_status":"0"}],"Stock":[{"stock_product_id":"15","stock_qty":"302","stock_dealer_id":"1252"},{"stock_product_id":"16","stock_qty":"246","stock_dealer_id":"1252"},{"stock_product_id":"14","stock_qty":"0","stock_dealer_id":"1252"},{"stock_product_id":"159","stock_qty":"877","stock_dealer_id":"1252"},{"stock_product_id":"59","stock_qty":"127","stock_dealer_id":"56"},{"stock_product_id":"60","stock_qty":"0","stock_dealer_id":"56"},{"stock_product_id":"5","stock_qty":"0","stock_dealer_id":"1247"},{"stock_product_id":"89","stock_qty":"0","stock_dealer_id":"1252"},{"stock_product_id":"90","stock_qty":"375","stock_dealer_id":"1252"},{"stock_product_id":"71","stock_qty":"0","stock_dealer_id":"1252"},{"stock_product_id":"156","stock_qty":"514","stock_dealer_id":"1252"},{"stock_product_id":"3","stock_qty":"56","stock_dealer_id":"1252"},{"stock_product_id":"13","stock_qty":"447","stock_dealer_id":"1252"},{"stock_product_id":"82","stock_qty":"35","stock_dealer_id":"1252"},{"stock_product_id":"147","stock_qty":"25","stock_dealer_id":"1223"},{"stock_product_id":"143","stock_qty":"173","stock_dealer_id":"1223"},{"stock_product_id":"97","stock_qty":"140","stock_dealer_id":"1223"},{"stock_product_id":"88","stock_qty":"0","stock_dealer_id":"1223"},{"stock_product_id":"96","stock_qty":"0","stock_dealer_id":"1223"},{"stock_product_id":"20","stock_qty":"159","stock_dealer_id":"1223"},{"stock_product_id":"21","stock_qty":"209","stock_dealer_id":"1223"},{"stock_product_id":"25","stock_qty":"77","stock_dealer_id":"1223"},{"stock_product_id":"22","stock_qty":"150","stock_dealer_id":"1223"},{"stock_product_id":"23","stock_qty":"332","stock_dealer_id":"1223"},{"stock_product_id":"12","stock_qty":"149","stock_dealer_id":"1223"},{"stock_product_id":"38","stock_qty":"0","stock_dealer_id":"1223"},{"stock_product_id":"1","stock_qty":"0","stock_dealer_id":"1223"},{"stock_product_id":"66","stock_qty":"200","stock_dealer_id":"1223"},{"stock_product_id":"105","stock_qty":"132","stock_dealer_id":"1223"},{"stock_product_id":"102","stock_qty":"39","stock_dealer_id":"1223"},{"stock_product_id":"41","stock_qty":"100","stock_dealer_id":"1223"},{"stock_product_id":"139","stock_qty":"73","stock_dealer_id":"1223"},{"stock_product_id":"44","stock_qty":"39","stock_dealer_id":"1223"},{"stock_product_id":"50","stock_qty":"2147483642","stock_dealer_id":"1252"},{"stock_product_id":"51","stock_qty":"6594","stock_dealer_id":"1252"},{"stock_product_id":"52","stock_qty":"874","stock_dealer_id":"1252"},{"stock_product_id":"61","stock_qty":"59899","stock_dealer_id":"1252"},{"stock_product_id":"67","stock_qty":"417","stock_dealer_id":"1252"},{"stock_product_id":"72","stock_qty":"9372","stock_dealer_id":"1252"},{"stock_product_id":"53","stock_qty":"14991","stock_dealer_id":"1252"},{"stock_product_id":"56","stock_qty":"629976","stock_dealer_id":"1252"},{"stock_product_id":"57","stock_qty":"1702574","stock_dealer_id":"1252"},{"stock_product_id":"65","stock_qty":"119774","stock_dealer_id":"1252"},{"stock_product_id":"83","stock_qty":"5560","stock_dealer_id":"1252"},{"stock_product_id":"10","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"7","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"11","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"4","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"8","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"9","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"6","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"42","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"35","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"28","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"46","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"39","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"32","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"18","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"43","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"36","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"29","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"54","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"47","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"40","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"33","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"26","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"19","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"37","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"30","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"55","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"48","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"34","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"27","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"45","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"31","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"24","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"17","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"49","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"92","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"85","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"78","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"93","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"86","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"79","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"94","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"87","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"80","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"91","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"84","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"95","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"81","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"138","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"106","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"163","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"99","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"131","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"124","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"149","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"117","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"142","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"110","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"103","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"135","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"160","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"128","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"153","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"121","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"146","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"114","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"107","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"100","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"132","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"157","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"125","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"150","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"118","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"111","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"104","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"136","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"161","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"129","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"154","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"122","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"115","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"140","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"108","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"165","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"101","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"133","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"158","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"126","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"151","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"119","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"144","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"112","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"137","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"162","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"98","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"130","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"155","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"123","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"148","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"116","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"141","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"109","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"134","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"127","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"152","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"120","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"145","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"113","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"74","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"75","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"76","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"73","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"77","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"64","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"68","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"58","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"69","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"62","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"70","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"63","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"164","stock_qty":"0","stock_dealer_id":"24"},{"stock_product_id":"2","stock_qty":"0","stock_dealer_id":"24"}],"DailyDispatchDetail":[{"dispatch_id":"125210072017122000","challan_no":"DS1252\/16-17\/2","sort_order":""}],"DamageOrder":[{"created_by":"705","dealer_id":"1252","retailer_id":"20170118181224157","dispatch_date":"2017-10-07","remark":"jhjj","order_id":"125210072017122042","total_actual_amount":"1000.0","complaint_type":"2","saleble_and_non_saleble_type":"2"}],"DamageOrderDetail":[{"product_id":"15","tax":"","qty":"2","product_rate":"375.00","free_qty":"","order_id":"125210072017122042","mrp":"500.00","cd":"","cd_type":"","cd_amt":"","dis_type":"","dis_amt":"","dis_percent":"","taxable_amt":"","actual_amount":"1000.0","replace_product_id":"","replace_rate":"","replace_quantity":"","replace_amount":""}],"imei":"000000000000000","user_id":"705","dealer_id":"1252"}}';
//echo $dbc;
$check  = str_replace("'","",$check);
$data=json_decode($check);
//echo"<pre>";print_r($data); exit;
$dealer_out=$data->response->dealer_id;
$user_out=$data->response->user_id;
//print_r($data); exit;
//echo $dealer_out; exit;
if($data)
{
  
 
//$challan=$data->response->RetailerFullfillment;
$purchaseorder=$data->response->PurchaseOrder;
$purchasedetail=$data->response->PurchaseOrderDetail;
$primarySaleOrder=$data->response->PrimarySaleOrder;
$primarySaleOrderDetail=$data->response->PrimarySaleOrderDetail;
$challan = $data->response->Challan;
$challandetail = $data->response->ChallanDetail;
$dailydispatch = $data->response->DailyDispatch;
$dailydispatchdetail = $data->response->DailyDispatchDetail;
$damageorder = $data->response->DamageOrder;
$damageorderdetail = $data->response->DamageOrderDetail;
$payment = $data->response->PaymentCollection;
//print_r($purchaseorder); exit;
$damageorder_id = array();
$payment_id = array();
$dailydispatch_id = array();
$primary_id = array();
$challan_gen = array();
$damageorder_id = array();
$primary_detail = array();
$challan_detail = array();
$damageorder_detail = array();
$purchasedetail_id = array();
$dispatch_detail = array();
$purchase_id = array();
///////////////////////////////////DAMAGE SALE ORDER////////////////////////////////////////////////////////////
if(!empty($damageorder)){
$damageorder_id = array();
	$damageordercount = count($damageorder);
	$doc=0;
	while($doc<$damageordercount){
//echo"ANKUSH";
		$dealer_id=$damageorder[$doc]->dealer_id;
		$retailer_id=$damageorder[$doc]->retailer_id;
		$dispatch_date=$damageorder[$doc]->dispatch_date;
		$remark=$damageorder[$doc]->remark;
		$damage_id=$damageorder[$doc]->order_id;
		$amount=$damageorder[$doc]->total_actual_amount;
		$complaint_type=$damageorder[$doc]->complaint_type;
		$salable=$damageorder[$doc]->saleble_and_non_saleble_type;
		$created_by=$damageorder[$doc]->created_by;
		
	   $qdamr = "INSERT INTO `damage_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`, `dispatch_status`, `sesId`, `remark`, `complaint_id`, `actual_amount`, `saleable_non_saleable`, `sync_status`) VALUES  ('$damage_id','','$created_by','$dealer_id','$retailer_id','$dispatch_date','$dispatch_date','1','0','0','$remark','$complaint_type','$amount','$salable','1')";
      // echo $qdamr."<br/>";
        $damr = mysqli_query($dbc, $qdamr);
                if($damr){
                    $damageorder_id[$doc] = $damage_id;
                    
                }
		$doc++;
	}
}

if(!empty($damageorderdetail)){
	$damageorderdetailcount = count($damageorderdetail);
	$dodc=0;
	while($dodc<$damageorderdetailcount){
		$product_id=$damageorderdetail[$dodc]->product_id;
		$tax=$damageorderdetail[$dodc]->tax;
		if(empty($tax))
		{
			$tax = 0;
		}
		$qty=$damageorderdetail[$dodc]->qty;
		$product_rate=$damageorderdetail[$dodc]->product_rate;
		$free_qty=$damageorderdetail[$dodc]->free_qty;
		if(empty($free_qty))
		{
			$free_qty = 0;
		}
		$order_id=$damageorderdetail[$dodc]->order_id;
		$mrp=$damageorderdetail[$dodc]->mrp;
		$cd=$damageorderdetail[$dodc]->cd;
		if(empty($cd))
		{
			$cd = 0;
		}
		$cd_type=$damageorderdetail[$dodc]->cd_type;
		if(empty($cd_type))
		{
			$cd_type = 0;
		}
		$cd_amt=$damageorderdetail[$dodc]->cd_amt;
		if(empty($cd_amt))
		{
			$cd_amt = 0;
		}
		$dis_type=$damageorderdetail[$dodc]->dis_type;
		if(empty($dis_type))
		{
			$dis_type = 0;
		}
		$dis_amt=$damageorderdetail[$dodc]->dis_amt;
		if(empty($dis_amt))
		{
			$dis_amt = 0;
		}
		$dis_percent=$damageorderdetail[$dodc]->dis_percent;
		if(empty($dis_percent))
		{
			$dis_percent = 0;
		}
		$taxable_amt=$damageorderdetail[$dodc]->taxable_amt;
		if(empty($taxable_amt))
		{
			$taxable_amt = 0;
		}
		$actual_amount=$damageorderdetail[$dodc]->actual_amount;
		if(empty($actual_amount))
		{
			$actual_amount = 0;
		}
		$replace_product_id=$damageorderdetail[$dodc]->replace_product_id;
		if(empty($replace_product_id))
		{
			$replace_product_id = 0;
		}
		$replace_mrp=$damageorderdetail[$dodc]->replace_mrp;
		if(empty($replace_mrp))
		{
			$replace_mrp = 0;
		}
		$replace_rate=$damageorderdetail[$dodc]->replace_rate;
		if(empty($replace_rate))
		{
			$replace_rate = 0;
		}
		$replace_quantity=$damageorderdetail[$dodc]->replace_quantity;
		if(empty($replace_quantity))
		{
			$replace_quantity = 0;
		}
		$replace_amount=$damageorderdetail[$dodc]->replace_amount;
               if(empty($replace_amount))
		{
			$replace_amount = 0;
		}
               
	
	       $qdodc = "INSERT INTO `damage_order_details`(`ch_id`, `product_id`, `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, `actual_amount`, `replace_product_id`, `replace_mrp`, `replace_rate`, `replace_quantity`, `replace_amount`) VALUES   ('$order_id','$product_id','0',' ','$tax','$qty','$product_rate','$free_qty','$order_id','$user_out','$mrp','$cd','$cd_type','$cd_amt','$dis_type',
'$dis_amt','$dis_percent','$taxable_amt','$actual_amount','$replace_product_id','$replace_mrp','$replace_rate','$replace_quantity','$replace_amount')";
//echo $qdodc."<br/>"; //exit;
       $resultdodc=mysqli_query($dbc,$qdodc);
          if($resultdodc){
                    $damageorder_detail[$dodc] = $order_id;
                    
                }       
		$dodc++;
	}
}
/////////////////////////////////////////END DAMAGE ORDER/////////////////////////////////////////////////////
///////////////////////////////////PAYMENT////////////////////////////////////////////////////////////
if(!empty($payment)){
$payment_id = array();
	$paymentcount = count($payment);
	$payc=0;
	while($payc<$paymentcount){
//echo"ANKUSH";
		$paymentid=$payment[$payc]->payment_id;
		$dealer_id=$payment[$payc]->dealer_id;
		$ch_id=$payment[$payc]->challan_id;
		$retailer_id=$payment[$payc]->retailer_id;
		$payment_mode=$payment[$payc]->payment_mode;
		$amount=$payment[$payc]->amount;
		$bank_branch=$payment[$payc]->bank_branch;
		$cheque_no=$payment[$payc]->cheque_no;
		$cheque_date=$payment[$payc]->cheque_date;
		$payment_date=$payment[$payc]->payment_date;
		$payment_time=$payment[$payc]->payment_time;
		$paymentdatetime = $payment_date." ".$payment_time;
		$user_id=$user_out;
		$created_by=$payment[$payc]->created_by;
               // $payment_id = Date(Ymdhis);
		
	   $qpay = "INSERT INTO `payment_collection`(`payment_id`,`dealer_id`, `challan_id`, `retailer_id`, `total_amount`, `pay_mode`, `bank_name`, `chq_no`,"
                   . " `chq_date`, `Remark`, `pay_date_time`, `image`) VALUES ('$paymentid','$dealer_id','$ch_id','$retailer_id','$amount', '$payment_mode',"
                   . "'$bank_branch','$cheque_no','$cheque_date','$remark','$paymentdatetime',' ')";
      // echo $qpay."<br/>";
        $dr = mysqli_query($dbc, $qpay);
                if($dr){
                    $payment_id[$payc] = $paymentid;
                    
                }
		$payc++;
	}
}


/////////////////////////////////////////END PAYMENT/////////////////////////////////////////////////////

///////////////////////////////////DISPATCH SALE ORDER////////////////////////////////////////////////////////////
if(!empty($dailydispatch)){
$dailydispatch_id = array();
	$dailydispatchcount = count($dailydispatch);
	$ddc=0;
	while($ddc<$dailydispatchcount){
//echo"ANKUSH";
		$dealer_id=$dailydispatch[$ddc]->dealer_id;
		$retailer_id=$dailydispatch[$ddc]->retailer_id;
		$dispatch_id=$dailydispatch[$ddc]->dispatch_id;
		$dispatch_no=$dailydispatch[$ddc]->dispatch_no;
		$van_no=$dailydispatch[$ddc]->van_no;
		$dispatch_date=$dailydispatch[$ddc]->dispatch_date;
		$total_bills=$dailydispatch[$ddc]->total_bills;
		$total_product=$dailydispatch[$ddc]->total_product;
		$created_by=$dailydispatch[$ddc]->created_by;
		
	   $qdr = "INSERT INTO `daily_dispatch`(`dispatch_id`, `dispatch_no`, `dealer_id`, `van_no`, `dispatch_date`, `total_bills`, `total_product`, `company_id`, `created_by`, `sync_status`) VALUES ('$dispatch_id','$dispatch_no','$dealer_id','$van_no', '$dispatch_date','$total_bills','$total_product','1','$created_by','1')";
     //  echo $qdr."<br/>";
        $dr = mysqli_query($dbc, $qdr);
                if($dr){
                    $dailydispatch_id[$ddc] = $dispatch_id;
                    
                }
		$ddc++;
	}
}

if(!empty($dailydispatchdetail)){
	$dailydispatchdetailcount = count($dailydispatchdetail);
	$ddo=0;
	while($ddo<$dailydispatchdetailcount){
		$dispatch_id=$dailydispatchdetail[$ddo]->dispatch_id;
		$challan_no=$dailydispatchdetail[$ddo]->challan_no;
		$sortorder=$dailydispatchdetail[$ddo]->sort_order;
               
               
	
	       $q = "INSERT INTO `daily_dispatch_details`(`dispatch_id`, `ch_id`, `sortorder`, `sync_status`) VALUES  ('$dispatch_id','$challan_no','$sortorder','1')";
//echo $q."<br/>"; 
       $result=mysqli_query($dbc,$q);
                  if($result){
                    $dispatch_detail[$ddo] = $order_id;
                    
                }  
		$ddo++;
	}
}
/////////////////////////////////////////END DISPATCH ORDER/////////////////////////////////////////////////////
///////////////////////////////////PRIMARY SALE ORDER////////////////////////////////////////////////////////////
if(!empty($primarySaleOrder)){
$primary_id = array();
	$primarySaleOrdercount = count($primarySaleOrder);
	$psd=0;
	while($psd<$primarySaleOrdercount){
//echo"ANKUSH";
		$orderid=$primarySaleOrder[$psd]->order_id;
		
                $dealer_id=$primarySaleOrder[$psd]->dealer_id;
		$created_date=$primarySaleOrder[$psd]->created_date;
		$person_id=$primarySaleOrder[$psd]->person_id;
		$sale_date=$primarySaleOrder[$psd]->order_date;
		$challan_no=$primarySaleOrder[$psd]->challan_no;
		$csa_id=$primarySaleOrder[$psd]->csa_id;
		$receive_date=$primarySaleOrder[$psd]->receive_date;
		$received_status=$primarySaleOrder[$psd]->received_status;
		$sync_status=$primarySaleOrder[$psd]->sync_status;
		
	   $qpr = "INSERT INTO `user_primary_sales_order`(`id`, `order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, `challan_no`, `csa_id`, `action`, `is_claim`, `sync_status`) VALUES
                                          ('$orderid','$orderid','$dealer_id','$created_date', '$person_id','$sale_date','$created_date','$created_date','1', '$receive_date','$challan_no','$csa_id','1','0','1')";
     //  echo $qpr."<br/>";
        $pr = mysqli_query($dbc, $qpr);
                if($pr){
                    $primary_id[$psd] = $orderid;
                    
                }
		$psd++;
	}
}

if(!empty($primarySaleOrderDetail)){
	$primarySaleOrderDetailcount = count($primarySaleOrderDetail);
	$psdo=0;
	while($psdo<$primarySaleOrderDetailcount){
		$order_id=$primarySaleOrderDetail[$psdo]->order_id;
		$product_id=$primarySaleOrderDetail[$psdo]->product_id;
		$rate=$primarySaleOrderDetail[$psdo]->rate;
                $qty=$primarySaleOrderDetail[$psdo]->quantity;
		$scheme_qty=$primarySaleOrderDetail[$psdo]->scheme_quantity;
		$batch_no=$primarySaleOrderDetail[$psdo]->batch_no;
                $mfg=$primarySaleOrderDetail[$psdo]->mfg;
		$received_status=$primarySaleOrderDetail[$psdo]->received_status;
               
	$s = "select piece,free_qty from `cases` where product_id = $value";
                  $rsss = mysqli_query($dbc, $s);
                  while($row = mysqli_fetch_assoc($rsss))
                  {
                     $piece = $row['piece']; 
                     $free = $row['free_qty']; 
                  }
		$quant = $qty*$piece;
		$sch = $free*$qty;	
		//$expire = date('Y-m-d', strtotime('+1 year', strtotime($mfg)) );
		$expdate =  $prev_date = date('Y-m-d', strtotime($mfg .' +365 day'));
	       $q = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`) VALUES ('$order_id','$order_id','$product_id','$rate','$quant','$sch','$batch_no','$mfg','$expdate',NOW(),'$batch_no','0.00','$qty')";
//echo $q."<br/>"; 
       $resultp=mysqli_query($dbc,$q);
                 if($resultp){
                   $primary_detail[$psdo] = $order_id;
                    
                }
		$psdo++;
	}
}
/////////////////////////////////////////END PRIMARY SALE ORDER/////////////////////////////////////////////////////
//echo"ANKUSH"; exit;
//echo "ANKUSH";
/////////////////////////////////////PURCHASE ORDER////////////////////////////////////////////////////////////
if(!empty($purchaseorder)){
$purchase_id = array();

	$purchaseordercpunt = count($purchaseorder);
	$p=0;
	while($p<$purchaseordercpunt){

		$orderid=$purchaseorder[$p]->order_id;
		$dealer_id=$purchaseorder[$p]->dealer_id;
		$created_date=$purchaseorder[$p]->created_date;
		$person_id=$purchaseorder[$p]->person_id;
		$order_date=$purchaseorder[$p]->order_date;
		$csa_id=$purchaseorder[$p]->csa_id;
		$total_ammount=$purchaseorder[$p]->total_ammount;
		
	   $q = "INSERT INTO `purchase_order`(`order_id`, `dealer_id`, `created_date`, `created_person_id`, `order_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, `challan_no`, `csa_id`) VALUES
                                          ('$orderid','$dealer_id','$created_date', '$person_id','$order_date', '0000-00-00','0000-00-00 00:00:00', '1', '0000-00-00','','$csa_id')";
      // echo $q."<br/>"; 
        $resultpurchase = mysqli_query($dbc, $q);
                if($resultpurchase){
                  $purchase_id[$p] = $orderid; 
                    
                }
		$p++;
	}
}

if(!empty($purchasedetail)){
	$purchasedetailcount = count($purchasedetail);
	$l=0;
	while($l<$purchasedetailcount){
		$order_id=$purchasedetail[$l]->order_id;
		$product_id=$purchasedetail[$l]->product_id;
		$rate=$purchasedetail[$l]->rate;
                $qty=$purchasedetail[$l]->qty;
		//$scheme_qty=$purchasedetail[$l]->scheme_qty;
	$s = "select piece,free_qty from `cases` where product_id = $product_id";
                  $rsss = mysqli_query($dbc, $s);
                  while($row = mysqli_fetch_assoc($rsss))
                  {
                     $p = $row['piece']; 
                     $free = $row['free_qty']; 
                  }
		$quant = $qty*$p;
		$sch = $free*$qty;	
		
	       $q = "INSERT INTO `purchase_order_details` (`order_id`, `product_id`, `rate`, `quantity`,`scheme_qty`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`) VALUES ('$order_id','$product_id','$rate','$quant','$sch','','0000-00-00','0000-00-00','0.00','$qty')";
      // echo $q."<br/>"; 
       $resultpd=mysqli_query($dbc,$q);
             if($resultpurchase){
                  $purchasedetail_id[$l] = $orderid; 
                    
                }    
		$l++;
	}
}

/////////////////////////////////////////END PURCHASE/////////////////////////////////////////////////////


///////////////////////////////////////////////??CHALLAN/////////////////////////////////////////////////
if(!empty($challan)){
   // echo "manisha";
$challanG = array();
$challan_gen = array();
  $count_challan=count($challan);
	$chc=0;
while($chc<$count_challan){
		$retailer_id=$challan[$chc]->retailer_id;
		$user_id=$challan[$chc]->person_id;
		$challan_id = $challan[$chc]->challan_id;		
		$dealer_id = $challan[$chc]->dealer_id;
		$challan_date = $challan[$chc]->challan_date;
		$company_id = $challan[$chc]->company_id;
		$dispatch_status = $challan[$chc]->dispatch_status;
		$payment_status = $challan[$chc]->payment_status;
		$is_claimed = $challan[$chc]->is_claimed;
		$is_target_cliamed = $challan[$chc]->is_target_cliamed;
		$amount = $challan[$chc]->amount;
		$discount_percent = $challan[$chc]->discount_percent;
		$discount_amount = $challan[$chc]->discount_amount;
		if($discount_percent=='' && $discount_percent=='0')
		{
			$invoice_type = 1;
		}
		else
		{
                        $invoice_type = 2;
		}
		$iddd = $dealer_id.date('ymdHis');
		$dt = date("Y-m-d H:i:s");
                $id = $dealer_id.date('ymdHis');
                mysqli_query($dbc, "START TRANSACTION");
                $uid= $id;

  //$status=$q_person['person_status'];

$query2 = "select `ch_no` from `challan_order` where `ch_dealer_id`=$dealer_id order by `ch_date` DESC,`id` DESC";
                $q = mysqli_query($dbc,$query2);
                $row = mysqli_fetch_row($q);
                $ch = $row[0];
                $ch_value = explode('/',$ch);
                $value_inv = $ch_value[2];
                $value_year = $ch_value[3];
              ////////////////////////////////////FOR SESSION///////////////////////////////
              $query1 = "select `session` from `session` where `action`='1'";
                $q1 = mysqli_query($dbc,$query1);
                $row1 = mysqli_fetch_row($q1);
              $year = $row1[0];
              
              if($year == $value_year)
              {
                
                  $jj= $value_inv+1;
                 
                 $num = str_pad($jj,6,'0',STR_PAD_LEFT);
                 $ch_id = "CATC/".$dealer_id."/".$num."/".$year; 
                 // h1($inc);
              }
              else
              {
                 $ch_id = "CATC/".$dealer_id."/000001/".$year; 
              }
    

               $resultchc = "INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`, `dispatch_status`, `discount`, `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`, `isclaim`, `discount_per`, `discount_amt`, `amount`, `remaining`) VALUES ($challan_id,'$ch_id','$user_id','$dealer_id',
                    '$retailer_id','0000-00-00 00-00-00','$challan_date','$company_id', '$dispatch_status','0.00','0','','1','$invoice_type','$payment_status','$is_claimed','$discount_percent','$discount_amount','$amount','$amount')";
    //echo $resultchc."<br/>";
               $result=mysqli_query($dbc, $resultchc);
	 if($result)
		{
		  $challanG['challan_id'] = $challan_id;
		  $challanG['challan_no'] = $ch_id;
			$challan_gen[$chc] = $ch;
		}


	$chc++;
	}
 
	
}

//////////////////////////////////////////////CHALLAN ORDER DETAILS////////////////////////////////////////////

if(!empty($challandetail)){
	$challandetailcount = count($challandetail);
	$cdd=0;
        
	while($cdd<$challandetailcount){
		$challan_id=$challandetail[$cdd]->challan_id;
		$product_id=$challandetail[$cdd]->product_id;
		$rate=$challandetail[$cdd]->product_rate;
                $qty=$challandetail[$cdd]->quantity;
		$tax=$challandetail[$cdd]->tax;
		$mrp=$challandetail[$cdd]->mrp;
		$qty=$challandetail[$cdd]->qty;
		$cd=$challandetail[$cdd]->cd;
		$cd_type=$challandetail[$cdd]->cd_type;
		$cd_amount=$challandetail[$cdd]->cd_ammount;
		$trade_discount_type=$challandetail[$cdd]->trade_discount_type;
		$trade_discount_ammount=$challandetail[$cdd]->trade_discount_ammount;
		$trade_discount_percent=$challandetail[$cdd]->trade_discount_percent;
		$scheme_qty=$challandetail[$cdd]->scheme_quantity;
		$dealer_id=$challandetail[$cdd]->dealer_id;
		$taxable_ammount=$challandetail[$cdd]->taxable_ammount;
  $q="SELECT * From person WHERE id='$user_id'";
//echo $q;
  $user_res= mysqli_query($dbc, $q);
  $q_person=  mysqli_fetch_assoc($user_res);
  $person_id=$q_person['id'];
  $state=$q_person['state_id'];
$sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$product_id' AND `dealer_id`='$dealer_id' order by `mfg` asc"; 
            // echo $sq;
                $rs1=mysqli_query($dbc,$sq);                
                while($row = mysqli_fetch_assoc($rs1))
                {
                     $remaining_qty = $row['remaining'];
                     $product_id = $row['product_id'];
                     $id = $row['id'];  
                      
                    if($qtyy >= $remaining_qty){
                          $qtyy =  $qtyy - $remaining_qty;
                          $balqty = 0;
                        $qu = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
                        $ru = mysqli_query($dbc, $q);
                       
                    }else{
                        $balqty =  $remaining_qty - $qtyy; 
                       // $baltemp = $qty - $remaining_qty;
                        $qu = "UPDATE stock SET  remaining = '$balqty' WHERE id='$id'";                        
                        $ru = mysqli_query($dbc, $q);   
                       
                    }
                    
                }

	
	////////////////////////////////////RATE AND VAT//////////////////////////////////////
	
 	$qco = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, 
                `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, 
                `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, 
                `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`) VALUES ('$challan_id','$product_id','0','0','$tax','$qty', '$rate','$scheme_qty','$challan_id','$user_id','$mrp','$cd','$cd_type','$cd_amount','$trade_discount_type',
          '$trade_discount_ammount','$trade_discount_percent','$taxable_ammount')";
	$chr = mysqli_query($dbc, $qco);
     // echo $qco."<br/>";
           if($chr)
		{	
	 $challan_detail[$cdd] = $challan_id; 
		}     
	
		$cdd++;
             //   echo $cd;echo $challandetailcount; exit;
	}
}
////////////////////////////////////////////////////////////STOCK//////////////////////////////////////////////////////

//$stock = array();

//$stockq = "SELECT pid,dealer_available_stock.product_name as product_name,rate,balance_stock,salable_stock,non_salable_stock FROM `dealer_available_stock` INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid WHERE dealer_id = '$dealer_out' ORDER BY catalog_view.c1_id";





//////////////////////////////////////////////////////////////END STOCK////////////////////////////////////////////////
/////////////////////////////////////////////END CHALLAN ORDER DETAILS//////////////////////////////////////////


ob_start();
ob_clean();
                         //$uniqueId=  implode(',',$unique_id);
			 $essential= array("response"=>"Y","purchase"=>$purchase_id,"primary"=>$primary_id,"challan"=>$challan_gen,"dispatch"=>$dailydispatch_id,"damage"=>$damageorder_id,"payment"=>$payment_id,"purchase_detail"=>$purchasedetail_id,"primary_detail"=>$primary_detail,"challan_detail"=>$challan_detail,"dispatch_detail"=>$dispatch_detail,"damage_detail"=>$damageorder_detail); 
                        $data = json_encode($essential);
                        if($result) 
			echo $data;
	
ob_get_flush();
ob_end_flush();

}
else{
                        $essential= array("response"=>"N"); 
                        $data = json_encode($essential);
                       echo $data;
}


?>
