<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
 $unique_id = array();
if(isset($_POST['response'])){$check=$_POST['response'];} else ////$check='';
//$check ='';
//echo $dbc;
$check  = str_replace("'","",$check);
$data=json_decode($check);
$dealer_out=$data->response->dealer_id;
$user_out=$data->response->user_id;
//print_r($data); exit;
if($data)
{
 
//$challan=$data->response->RetailerFullfillment;
$purchaseorder=$data->response->PurchaseOrder;
$purchasedetail=$data->response->PurchaseOrderDetail;
$primarySaleOrder=$data->response->PrimarySaleOrder;
$primarySaleOrderDetail=$data->response->PrimarySaleOrderDetail;
$challan = $data->response->Challan;
$challandetail = $data->response->ChallanDetail;


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
                                          ('$orderid','$orderid','$dealer_id','$created_date', '$person_id','$sale_date','$created_date','$created_date','1', '$receive_date','$challan_no','$csa_id','1','0','0')";
      // echo $qpr;
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
		$order_id=$purchasedetail[$psdo]->order_id;
		$product_id=$purchasedetail[$psdo]->product_id;
		$rate=$purchasedetail[$psdo]->rate;
                $qty=$purchasedetail[$psdo]->quantity;
		$scheme_qty=$purchasedetail[$psdo]->scheme_quantity;
		$batch_no=$purchasedetail[$psdo]->batch_no;
                $mfg=$purchasedetail[$psdo]->mfg;
		$received_status=$purchasedetail[$psdo]->received_status;
               
	$s = "select piece,free_qty from `cases` where product_id = $value";
                  $rsss = mysqli_query($dbc, $s);
                  while($row = mysqli_fetch_assoc($rsss))
                  {
                     $piece = $row['piece']; 
                     $free = $row['free_qty']; 
                  }
		$quant = $qty*$piece;
		$sch = $free*$qty;	
		$expire = date('Y-m-d', strtotime('+1 year', strtotime($mfg)) );
	       $q = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`, `cases`) VALUES ('$order_id','$order_id','$product_id','$rate','$quant','$sch','$batch_no','$mfg','$expire',NOW(),'$batch_no','0.00','$qty')";
//echo $q; exit;
       $result=mysqli_query($dbc,$q);
                
		$psdo++;
	}
}
/////////////////////////////////////////END PRIMARY SALE ORDER/////////////////////////////////////////////////////
//echo"ANKUSH"; exit;

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
       //echo $q; exit;
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
		$scheme_qty=$purchasedetail[$l]->scheme_qty;
	$s = "select piece,free_qty from `cases` where product_id = $value";
                  $rsss = mysqli_query($dbc, $s);
                  while($row = mysqli_fetch_assoc($rsss))
                  {
                     $p = $row['piece']; 
                     $free = $row['free_qty']; 
                  }
		$quant = $qty*$p;
		$sch = $free*$qty;	
		
	       $q = "INSERT INTO `purchase_order_details` (`order_id`, `product_id`, `rate`, `quantity`,`scheme_qty`,`purchase_inv`, `mfg_date`, `expiry_date`, `pr_rate`,`cases`) VALUES ('$order_id','$product_id','$rate','$quant','$sch','','0000-00-00','0000-00-00','0.00','$qty')";
       //echo $q; 
       $result=mysqli_query($dbc,$q);
                
		$l++;
	}
}

/////////////////////////////////////////END PURCHASE/////////////////////////////////////////////////////


///////////////////////////////////////////////??CHALLAN/////////////////////////////////////////////////
if(!empty($challan)){
$challanG = array();
$challan_gen = array();
  $count_challan=count($challan);
	$chc=0;
while($chc<$count_challan){
		$retailer_id=$challan[$chc]->retailer_id;
		$user_id=$challan[$j]->person_id;
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
    

               $resultchc = mysqli_query($dbc,"INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`, `dispatch_status`, `discount`, `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`, `isclaim`, `discount_per`, `discount_amt`, `amount`, `remaining`) VALUES ($challan_id,'$ch_id','$user_id','$dealer_id',
                    '$retailer_id','0000-00-00 00-00-00','$challan_date','$company_id', '$dispatch_status','0.00','0','','1','$invoice_type','$payment_status','$is_claimed','$discount_percent','$discount_amount','$amount','$amount')");
       $result=mysqli_query($dbc, $querych);
	 if($resultchc)
		{
		  $challanG['challan_id'] = $challan_id;
		  $challanG['challan_no'] = $ch_id;
			$challan_gen = $ch;
		}


	$chc++;
	}
 
	
}

//////////////////////////////////////////////CHALLAN ORDER DETAILS////////////////////////////////////////////

if(!empty($challandetail)){
	$challandetailcount = count($challandetail);
	$cdd=0;
        
	while($cdd<$challandetailcount){
		$challan_id=$purchasedetail[$cd]->challan_id;
		$product_id=$purchasedetail[$cd]->product_id;
		$rate=$purchasedetail[$cd]->product_rate;
                $qty=$purchasedetail[$cd]->quantity;
		$tax=$purchasedetail[$cd]->tax;
		$mrp=$purchasedetail[$cd]->mrp;
		$qty=$purchasedetail[$cd]->qty;
		$cd=$purchasedetail[$cd]->cd;
		$cd_type=$purchasedetail[$cd]->cd_type;
		$cd_amount=$purchasedetail[$cd]->cd_ammount;
		$trade_discount_type=$purchasedetail[$cd]->trade_discount_type;
		$trade_discount_ammount=$purchasedetail[$cd]->trade_discount_ammount;
		$trade_discount_percent=$purchasedetail[$cd]->trade_discount_percent;
		$scheme_qty=$purchasedetail[$cd]->scheme_quantity;
		$dealer_id=$purchasedetail[$cd]->dealer_id;
		$taxable_ammount=$purchasedetail[$cd]->taxable_ammount;
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
	$r = mysqli_query($dbc, $qco);
     // echo $qco."<br>";
                
		$cdd++;
             //   echo $cd;echo $challandetailcount; exit;
	}
}
////////////////////////////////////////////////////////////STOCK//////////////////////////////////////////////////////

$stock = array();

//$stockq = "SELECT pid,dealer_available_stock.product_name as product_name,rate,balance_stock,salable_stock,non_salable_stock FROM `dealer_available_stock` INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid WHERE dealer_id = '$dealer_out' ORDER BY catalog_view.c1_id";





//////////////////////////////////////////////////////////////END STOCK////////////////////////////////////////////////
/////////////////////////////////////////////END CHALLAN ORDER DETAILS//////////////////////////////////////////


ob_start();
ob_clean();
                         //$uniqueId=  implode(',',$unique_id);
			 $essential= array("response"=>"Y","purchase"=>$purchase_id,"primary"=>$primary_id,"challan"=>$challan_gen); 
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
