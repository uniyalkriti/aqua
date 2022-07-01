<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
 $unique_id = array();
if(isset($_POST['response'])){$check=$_POST['response'];} else $check='';
$check ='{"response":{"DirectChallan":[{"order_id":"20170503055216","retailer_id":"126120170124122517","date":"2017-05-03","time":" 5:52:51","user_id":"711","dealer_id":"1223"},{"order_id":"20170503055216","retailer_id":"126120170124122517","date":"2017-05-03","time":" 5:52:52","user_id":"711","dealer_id":"1223"},{"order_id":"20170503055216","retailer_id":"126120170124122517","date":"2017-05-03","time":" 5:52:52","user_id":"711","dealer_id":"1223"},{"order_id":"20170503055216","retailer_id":"126120170124122517","date":"2017-05-03","time":" 5:52:52","user_id":"711","dealer_id":"1223"},{"order_id":"20170503055721","retailer_id":"126120170124122437","date":"2017-05-03","time":" 5:57:26","user_id":"711","dealer_id":"1223"},{"order_id":"20170503060020","retailer_id":"126120170124122480","date":"2017-05-03","time":" 6:00:48","user_id":"711","dealer_id":"1223"},{"order_id":"20170503060753","retailer_id":"126120170124122479","date":"2017-05-03","time":" 6:07:58","user_id":"711","dealer_id":"1223"},{"order_id":"20170503060840","retailer_id":"126120170124122478","date":"2017-05-03","time":" 6:08:46","user_id":"711","dealer_id":"1223"},{"order_id":"20170503060840","retailer_id":"126120170124122478","date":"2017-05-03","time":" 6:08:46","user_id":"711","dealer_id":"1223"},{"order_id":"20170503060947","retailer_id":"126120170124122436","date":"2017-05-03","time":" 6:10:13","user_id":"711","dealer_id":"1223"},{"order_id":"20170503061147","retailer_id":"126120170124122477","date":"2017-05-03","time":" 6:12:03","user_id":"711","dealer_id":"1223"}],"DirectChallanStatus":[{"order_id":"20170503055216","product_id":"15","rate":"410.00","quantity":"2","product_valu":"861.00","tradediscount":"","cdamount":""},{"order_id":"20170503055216","product_id":"14","rate":"159.90","quantity":"10","product_valu":"1678.95","tradediscount":"","cdamount":""},{"order_id":"20170503055216","product_id":"159","rate":"49.20","quantity":"10","product_valu":"516.60","tradediscount":"","cdamount":""},{"order_id":"20170503055216","product_id":"16","rate":"0.82","quantity":"2","product_valu":"1.72","tradediscount":"","cdamount":""},{"order_id":"20170503055721","product_id":"15","rate":"410.00","quantity":"5","product_valu":"2152.50","tradediscount":"","cdamount":""},{"order_id":"20170503060020","product_id":"15","rate":"410.00","quantity":"1","product_valu":"430.50","tradediscount":"","cdamount":""},{"order_id":"20170503060753","product_id":"15","rate":"410.00","quantity":"6","product_valu":"2583.00","tradediscount":"","cdamount":""},{"order_id":"20170503060840","product_id":"15","rate":"410.00","quantity":"22","product_valu":"9471.00","tradediscount":"","cdamount":""},{"order_id":"20170503060840","product_id":"16","rate":"0.82","quantity":"2","product_valu":"1.72","tradediscount":"","cdamount":""},{"order_id":"20170503060947","product_id":"15","rate":"410.00","quantity":"5","product_valu":"2152.50","tradediscount":"","cdamount":""},{"order_id":"20170503061147","product_id":"15","rate":"410.00","quantity":"5","product_valu":"2152.50","tradediscount":"","cdamount":""}],"DealerStock":[],"DealerStockStatus":[],"imei":"351980080590860","user_id":"711"}}';
//echo $dbc;
$check  = str_replace("'","",$check);
$data=json_decode($check);
//print_r($data);
if($data)
{
 
$challan=$data->response->RetailerFullfillment;
$payment=$data->response->Payment;
$direct_challan = $data->response->DirectChallan;
$direct_challan_details = $data->response->DirectChallanStatus;
$primarySaleSummary = $data->response->PrimarySaleSummary;
$primarySaleDetail = $data->response->PrimarySaleDetail;
$claim = $data->response->Claim;
$damege = $data->response->Damageandexpiry;
$damagedetails = $data->response->Damageandexpirystatus;
if(!empty($challan)){
  $count_challan=count($challan);
	$j=0;
		$retailer_id=$challan[$j]->Retailerid;
		$user_id=$challan[$j]->Userid;
		//$user_id = 570;		
		$dealer_id = $challan[$j]->Dealerid;
		$date=$challan->Date;
		$iddd = $dealer_id.date('ymdHis');
		$dt = date("Y-m-d H:i:s");
                $id = $dealer_id.date('ymdHis');
              //  mysqli_query($dbc, "START TRANSACTION");
                $uid= $id;
  $q="SELECT * From person WHERE id='$user_id'";
//echo $q;
  $user_res= mysqli_query($dbc, $q);
  $q_person=  mysqli_fetch_assoc($user_res);
  $person_id=$q_person['id'];
  $state=$q_person['state_id'];
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
     

               $result = mysqli_query($dbc,"INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`,
              `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `dispatch_status`, 
              `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`) VALUES ($iddd,'$ch_id','$user_id','$dealer_id',
                    '$retailer_id','$dt','1','0',
                       '0','','1','2','0')");
     //  $result=mysqli_query($dbc, $querych);
	while($j<$count_challan){
		
		$order_id=$challan[$j]->Orderid;
		$product_id = $challan[$j]->Productid;
		$qty=$challan[$j]->Productqty;
			
 
	 $sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$product_id' AND `dealer_id`=$dealer_id order by `mfg`    asc"; 
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

	//////////////////////////////////??END STOCK////////////////////////////////////////

          $qc = "SELECT * FROM catalog_product_rate_list WHERE stateId=$state AND `catalog_product_id`=$product_id";                 
          $rc = mysqli_query($dbc, $qc);
/// echo $qc;  
	 $rowc = mysqli_fetch_assoc($rc);
	 $taxId = $rowc['tax'];
	if(empty($taxId))
	$taxId = 0;
	 $rate = $rowc['base_price'];
	if(empty($rate))
	{	
	$rate = 0;
	}
	 $mrp = $rowc['rate'];
	if(empty($mrp))
	$mrp=0;
	$amt = $rate* $qty;
	$taxamt = ($amt*$taxId)/100;
	$taxable_amt = $amt+$taxamt;
	$qsc = "SELECT * FROM `scheme_product_details` WHERE NOW() BETWEEN `start_date` AND `end_date` AND product_id =    $product_id";                
        $rsc = mysqli_query($dbc, $qsc);
	$rowsc = mysqli_fetch_assoc($rsc);
		if(!empty($rowsc['buy_quantity']))
		{
                $buy = $rowsc['buy_quantity'];
	        $sc_qty = $rowsc['scheme_quantity'];
		$sc = $qty/$buy;
		$sq = $sc*$sc_qty;
		$scheme = floor($sq);
		}
		else
		{
		  $scheme ='0';	
		}	
	////////////////////////////////////RATE AND VAT//////////////////////////////////////
	
 	$qco = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, 
                `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, 
                `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, 
                `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`) VALUES ('$iddd','$product_id','0','','$taxId','$qty', '$rate','$scheme'
                  ,'$order_id','$user_id','$mrp','0','1','1','1',
                        '0','0','$taxable_amt')";
		$r = mysqli_query($dbc, $qco);
     // echo $qco."<br>";
	if($r)
	{
		$unique_id[] = $order_id;
	}
	else
	{ 
		$unique_id[] = 0;
	}
            	
	



	$j++;
	}
  $q3 = "update user_sales_order_details set status=2 where order_id = '$order_id'";
     $res3 = mysqli_query($dbc, $q3);
	 $q3 = "update user_sales_order set order_status=1 where order_id = '$order_id'";
     $res3 = mysqli_query($dbc, $q3);
	
}
////////////////////////////////////////////////////////////PAYMENT///////////////////////////////////////////////////////////////
if(!empty($payment)){
$j=0;
                $count_payment=count($payment);
		while($j<$count_payment){
		$cid = $payment[$j]->cid;
	        $retailer_id=$payment[$j]->retailer_id;
 //$retailer_id;
		$dealer_id=$payment[$j]->dealer_id;		
		//$challan_no = $payment->challan_no;
		$date= $payment[$j]->date_current;
		$paymentmode = $payment[$j]->payment_mode;
		$amount = $payment[$j]->amount;
		if(!empty($payment[$j]->cheque_date))
		{
		$cheque_date = $payment[$j]->cheque_date;
		}
		else if(!empty($payment[$j]->transaction_date))                
		{
		$cheque_date = $payment[$j]->transaction_date;
		}
		else
		{
		$cheque_date = "0000-00-00";
		}
		//$cheque_no = $payment[0]->cheque_no;
		$bank_name = $payment[$j]->branch_name;
		$remark = 'NO REMARK';
          
	//////////////////////////////////??INSERT PAYMENT COLLECTION////////////////////////////////////////
               $result = mysqli_query($dbc,"INSERT INTO `payment_collection`(`dealer_id`, `retailer_id`, `challan_id`, `total_amount`, `pay_mode`, `bank_name`, `chq_no`, `chq_date`, `Remark`) VALUES ('$dealer_id','$retailer_id','$cid','$amount','$paymentmode','$bank_name','$cid','$cheque_date','$remark')");
     	
	//////////////////////////////////??END PAYMENT COLLECTION////////////////////////////////////////

	////////////////////////////////////UPDATE CHALLAN ORDER//////////////////////////////////////
	
 	$qco = "UPDATE `challan_order` SET `payment_status`='1' WHERE id=$cid";
		$r = mysqli_query($dbc, $qco);
     // echo $qco."<br>";
	$j++;
       }
	}
/////////////////////////////////////////////////PAYMENT COLLECTION////////////////////////////////////////////////////////////
////////////////////////////////////////////////DIRECT CHALLAN/////////////////////////////////////////////////////////////////
if(!empty($direct_challan)){
  $count_DirectChallan=count($direct_challan);
	$count_DirectChallanstatus=count($direct_challan_details);
//echo $count_DirectChallanstatus;
	$j=0;
            while($count_DirectChallan>$j)
		{

		$retailer_id=$direct_challan[$j]->retailer_id;
		$user_id=$direct_challan[$j]->User_id;
		//$user_id = 570;		
		$dealer_id = $direct_challan[$j]->dealer_id;
		//$dealer_id=1252;
		$date=$direct_challan[$j]->date;
		$time=$direct_challan[$j]->time;
		//$datetime = $date." ".$time;
		$iddd = $direct_challan[$j]->order_id;
		$dt = date("Y-m-d H:i:s");
                $id = $dealer_id.date('ymdHis');
              //  mysqli_query($dbc, "START TRANSACTION");
                $uid= $id;
echo $dealer_id;
  $q="SELECT * From person WHERE id='$user_id'";
//echo $q;
  $user_res= mysqli_query($dbc, $q);
  $q_person=  mysqli_fetch_assoc($user_res);
  $person_id=$q_person['id'];
  $state=$q_person['state_id'];
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
     

               $querych1 = "INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`,
              `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `dispatch_status`, 
              `sesId`, `remark`, `sync_status`, `invoice_type`, `payment_status`) VALUES ($iddd,'$ch_id','$user_id','$dealer_id',
                    '$retailer_id','$dt','1','0',
                       '0','','1','2','0')";
		h1($querych1);
		$result=mysqli_query($dbc, $querych1);
}
     //  $result=mysqli_query($dbc, $querych);
$js=0;
	while($js<$count_DirectChallanstatus){
		
		$order_id=$direct_challan_details[$js]->order_id;
		$product_id = $direct_challan_details[$js]->product_id;
		$qty=$direct_challan_details[$js]->quantity;
		$amt=$direct_challan_details[$js]->product_valu;	
 		$tradediscount = $direct_challan_details[$js]->tradediscount;
		$cdamountdis = $direct_challan_details[$js]->cdamount;
	 $sq = "SELECT `id`,`remaining`,`product_id` FROM `stock` where `product_id`='$product_id' AND `dealer_id`=$dealer_id order by `mfg`    asc"; 
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

	//////////////////////////////////??END STOCK////////////////////////////////////////

          $qc = "SELECT * FROM catalog_product_rate_list WHERE stateId=$state AND `catalog_product_id`=$product_id";                 
          $rc = mysqli_query($dbc, $qc);
/// echo $qc;  
	 $rowc = mysqli_fetch_assoc($rc);
	 $taxId = $rowc['tax'];
	if(empty($taxId))
	$taxId = 0;
	 $rate = $rowc['base_price'];
	if(empty($rate))
	{	
	$rate = 0;
	}
	 $mrp = $rowc['rate'];
	if(empty($mrp))
	$mrp=0;
	//$amt = $rate* $qty;
	//$taxamt = ($amt*$taxId)/100;
	//$taxable_amt = $amt+$taxamt;
	$qsc = "SELECT * FROM `scheme_product_details` WHERE NOW() BETWEEN `start_date` AND `end_date` AND product_id =    $product_id";                
        $rsc = mysqli_query($dbc, $qsc);
	$rowsc = mysqli_fetch_assoc($rsc);
		if(!empty($rowsc['buy_quantity']))
		{
                $buy = $rowsc['buy_quantity'];
	        $sc_qty = $rowsc['scheme_quantity'];
		$sc = $qty/$buy;
		$sq = $sc*$sc_qty;
		$scheme = floor($sq);
		}
		else
		{
		  $scheme ='0';	
		}	
	////////////////////////////////////RATE AND VAT//////////////////////////////////////
	
 	$qco = "INSERT INTO `challan_order_details`(`ch_id`, `product_id`, 
                `catalog_details_id`, `batch_no`, `tax`, `qty`, `product_rate`, 
                `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`, `cd_amt`, 
                `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`) VALUES ('$iddd','$product_id','0','','$taxId','$qty', '$rate','$scheme'
                  ,'$order_id','$user_id','$mrp','$cdamountdis','1','1','1',
                        '0','tradediscount','$amt')";
		$r = mysqli_query($dbc, $qco);
     // echo $qco."<br>";
	if($r)
	{
		$unique_id[] = $order_id;
	}
	else
	{ 
		$unique_id[] = 0;
	}
            	
	



	$js++;
	}
  	
                }
/////////////////////////////////////////////END DIRECT CHALLAN////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////CLAIM///////////////////////////////////////////////////////////////
if(!empty($claim)){
$jd=0;
                $count_claim=count($claim);
		while($jd<$count_claim){
		$value = $claim[$jd]->value_from;
	        $value_to=$claim[$jd]->value_to;
		$dealer_id=$claim[$jd]->dealer_id;		
		//$challan_no = $payment->challan_no;
		$start_date= $claim[$jd]->start_date;
		$end_date = $claim[$jd]->end_date;
		$achieved = $claim[$jd]->achieved;
		$gift = $claim[$jd]->scheme_gift;
		
        if(strpos($gift, '%' ) !== false)
        {
            $g1 = explode("%", $gift);
           // echo $g1[0]; 
            $amt = ($achieved*$g1[0])/100;
        }
    else {
            $amt = '0';
         }
	//////////////////////////////////??INSERT PAYMENT COLLECTION////////////////////////////////////////
               $result = mysqli_query($dbc,"INSERT INTO `claim_challan`(`dealer_id`, `from_date`, `to_date`, `claim_amount`, `claim`, `total_amt`) VALUES ('$dealer_id','$start_date','$end_date','$achieved','$gift','$amt')");
     	
	//////////////////////////////////??END PAYMENT COLLECTION////////////////////////////////////////

	////////////////////////////////////UPDATE CHALLAN ORDER//////////////////////////////////////
	
 	 $qc = "UPDATE user_primary_sales_order SET  is_claim = '1' WHERE dealer_id = '$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d') BETWEEN 			'$start_date' AND '$end_date'";
      //  h1($q);
       $r = mysqli_query($dbc, $qc);
     // echo $qco."<br>";
	$jd++;
       }
	}
/////////////////////////////////////////////////CLAIM END////////////////////////////////////////////////////////////
//////////////////////////////////////////////////PRIMARY STOCK///////////////////////////////////////////////////////////////
if(!empty($primarySaleSummary)){
$jp=0;           
                $user = $data->response->user_id;
                $count_primary=count($primarySaleDetail);
		$count_primarys=count($primarySaleSummary);
while($jp<$count_primary){
		$order_id = $primarySaleDetail[$jd]->order_id;
	        $dealer_id=$primarySaleDetail[$jd]->dealer_id;
		$sale_date = $primarySaleDetail[$jd]->sale_date;	
		$created_date= $primarySaleDetail[$jd]->created_date;
		$ch_no = $primarySaleDetail[$jd]->created_date;
		$superstockist_id = $primarySaleDetail[$jd]->superstockist_id;
 /////////////////////////////////////////////////////INSERT PRIMARY ORDER//////////////////////////////////////////////////////
   $q1 = "INSERT INTO `user_primary_sales_order`(`id`, `order_id`, `dealer_id`, `created_date`, 
            `created_person_id`, `sale_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, 
            `challan_no`, `csa_id`, `action`) VALUES ('$order_id','$order_id','$dealer_id','$created_date','$user','$sale_date','$created_date','$created_date','1','$sale_date','$ch_no','$superstockist_id','0')";
    //  h1($q1);
       $r1 = mysqli_query($dbc,$q1);
    $jp++;           
    }
$jd= 0;
while($jd<$count_primarys){
                $order_ids = $primarySaleSummary[$jd]->order_id;
		$product_ids = $primarySaleSummary[$jd]->product_id;	
		$mrp= $primarySaleSummary[$jd]->mrp;
		$rate= $primarySaleSummary[$jd]->rate;
		$ch_nos = $primarySaleSummary[$jd]->ch_no;
		$quantitys = $primarySaleSummary[$jd]->quantity;
		$scheme_qtys = $primarySaleSummary[$jd]->scheme_qty;
		$cases = $primarySaleSummary[$jd]->case;
	//////////////////////////////////??PRIMARY DETAILS////////////////////////////////////////
               $result = mysqli_query($dbc,"INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `purchase_inv`,`receive_date`, `pr_rate`, `cases`) VALUES  ('$order_ids','$order_ids','$product_ids','$mrp','$quantitys','$scheme_qtys','Primary Stock',NOW(),'$rate','$cases')");
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////INSERT STOCK///////////////////////////////////////////////////////////////////////
	
 	 $qc = "INSERT INTO `stock`(`product_id`, `batch_no`, `rate`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`,`date`, `pr_rate`, `company_id`, `action`) VALUES('$product_ids','0','$mrp','$mrp','$user','$superstockist_id','$dealer_id','$quantitys','0','0','$quantitys',NOW(),'$rate',1,'1')";
      //  h1($q);
       $r = mysqli_query($dbc, $qc);
     // echo $qco."<br>";
	$jd++;
       }
	}
/////////////////////////////////////////////////PRIMARY STOCK END////////////////////////////////////////////////////////////
//////////////////////////////////////////////////DAMAGE STOCK///////////////////////////////////////////////////////////////
if(!empty($damagedetails)){
$jp=0;           
                
                $count_damage=count($damege);
		$count_damagedetails=count($damagedetails);
while($jp<$count_damage){
		$user_id = $damege[$jd]->userid;
		$order_id = $damege[$jd]->orderid;
	        $dealer_id=$damege[$jd]->dealer_id;
		$retailer_id = $damege[$jd]->retailer_id;	
		$complaint_id= $damege[$jd]->complantid;
		$saleable_no = $damege[$jd]->saleble_non_saleble;
		$date = date("Y-m-d h:i:s");
 /////////////////////////////////////////////////////INSERT DAMAGE ORDER//////////////////////////////////////////////////////
   $q1 = "INSERT INTO `damage_order`(`id`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `ch_date`, `company_id`, `dispatch_status`, `sesId`, `remark`, `complaint_id`, `actual_amount`, `saleable_non_saleable`, `sync_status`) VALUES ('$order_id','$user_id','$dealer_id','$retailer_id','$date','1','0','0',' ','$complaint_id','0.00','$saleable_no','1')";
    //  h1($q1);
       $r1 = mysqli_query($dbc,$q1);
    $jp++;           
    }
$jd= 0;
while($jd<$count_damagedetails){
                $order_ids = $damagedetails[$jd]->orderid;
		$product_ids = $damagedetails[$jd]->product_id;	
		$mrp= $damagedetails[$jd]->mrp;
		$rate= $damagedetails[$jd]->rate;
		$quantitys = $damagedetails[$jd]->quantity;
		$value = $damagedetails[$jd]->product_valu;
		$saleable_no = $damagedetails[$jd]->saleble_non_saleble;
	$qtyy = $quantitys;
	//////////////////////////////////??DAMAGE DETAILS////////////////////////////////////////
               $result = mysqli_query($dbc,"INSERT INTO `damage_order_details`(`ch_id`, `product_id`, `catalog_details_id`, `qty`, `product_rate`, `mrp`,) VALUES ('$order_ids','$product_ids','0','$quantitys','$rate','$mrp')");
     
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////INSERT STOCK///////////////////////////////////////////////////////////////////////
if($saleable_no==1)	
 {
            
     $sq = "SELECT `id`,`remaining`,`qty`,`product_id` FROM `stock` where `product_id`='$product_ids' order by `id` asc"; 
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
                     
                        $v = $quant-$remaining_qty;
                        $remaining_qty = $remaining_qty+$v;
                        $qtyy = $qtyy-$v;
                  ////  $qtyy =  $qtyy - $remaining_qty;
                        //$balqty = 0;
                        $q = "UPDATE stock SET  remaining = '$remaining_qty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);
                        }else{
                         
                         $remaining_qty = $remaining_qty+$qtyy;
                       // $qtyy = $qtyy-$v;
                       // $baltemp = $qty - $remaining_qty;
                        $q = "UPDATE stock SET  remaining = '$remaining_qty' WHERE id='$id'";                        
                        $r = mysqli_query($dbc, $q);   
                       }
                    
                }
                }
 	 
	$jd++;
       }
	}
/////////////////////////////////////////////////DAMAGE END////////////////////////////////////////////////////////////
ob_start();
ob_clean();
                         $uniqueId=  implode(',',$unique_id);
			 $essential= array("response"=>"Y"); 
                         $data = json_encode($essential);
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
