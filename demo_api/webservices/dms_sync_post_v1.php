<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time=date("Y-m-d H:i:s");
// require_once('../admin/include/conectdb.php');
// require_once('../admin/include/config.inc.php');
// require_once('../admin/functions/db_common_function.php');
// require_once 'functions.php';
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
if(isset($_POST['response'])){$check=$_POST['response'];} 
else $check='';
//////////////////user sale Order////////////////////////////////////////

//$check='';


function transliterateString($txt) {
    $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
    return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
}
        
$check_data  = str_replace("'","",$check);


$data=json_decode(transliterateString($check_data));
//echo'<pre>';
//print_r($data);exit;
$unique_id=array();


//print_r($data);

if($data)
{
  
$dealer_id=$data->response->user_id; //DEALER PERSON LOGIN ID
$company_id=$data->response->company_id; //DEALER COMPANY ID
$rt="SELECT dealer_id FROM dealer_person_login WHERE dpId='$dealer_id'";
$cqt=mysqli_query($dbc,$rt);
$rowdata=mysqli_fetch_assoc($cqt);
 $dealerid=$rowdata['dealer_id']; //DEALERID


{
$Stock_SummaryDetail=$data->response->StockSummaryDetail;
$StockSummary=$data->response->StockSummary;  
$FullfillmentSummary=$data->response->FullfillmentSummary;
$FullfillmentDetail=$data->response->FullfillmentDetail;
$ProductWiseFulfillment=$data->response->ProductWiseFulfillment;
$attendance=$data->response->Attandance;
$Complaint=$data->response->Complaint;
$paymentCollect = $data->response->PaymentCollection;





##############Complaint##################
if(!empty($Complaint)){
  $comp=count($Complaint);
  $ct=0;
       
  while($ct<$comp){
    $company_id=$Complaint[$ct]->company_id;
    $message=$Complaint[$ct]->message;
    $role_id=$Complaint[$ct]->role_id;
    $image_name=$Complaint[$ct]->image_name;
    $comp_type=$Complaint[$ct]->complaint_type;
    $dealer_retailer_id=$Complaint[$ct]->dealer_retailer_id;
    $image=$Complaint[$ct]->image;
    $feedback_form=$Complaint[$ct]->feedback_from;
    $data=$Complaint[$ct]->date;
    $order_id=$Complaint[$ct]->order_id;
    $date_time=$Complaint[$ct]->date_time;
    
    $ct++;
  $q="INSERT INTO `user_complaint`(`company_id`,`complaint_type`,`role_id`,`dealer_retailer_id`, `message`, `order_id`, `person_id`, `date_time`, `complaint_from`, `image_name`)
  VALUES ('$company_id',$comp_type','$role_id','$dealer_retailer_id','$message','$order_id','$user_id','$date_time','$feedback_form','$image_name')";
            $result=mysqli_query($dbc,$q);
}

}

if(!empty($ProductWiseFulfillment)){
  $totProductWiseFulfillment=count($ProductWiseFulfillment);
  $ostr=0;
      
  while($ostr<$totProductWiseFulfillment){

            $order_id=$ProductWiseFulfillment[$ostr]->OrderId;
            $retailer_id=$ProductWiseFulfillment[$ostr]->RetailerId;
            $product_id=$ProductWiseFulfillment[$ostr]->ProductId;
            $product_name=$ProductWiseFulfillment[$ostr]->ProductName;
            $retailer_name=$ProductWiseFulfillment[$ostr]->retailer_name;
            $invoive_number=$ProductWiseFulfillment[$ostr]->invoive_number;
            $date=$ProductWiseFulfillment[$ostr]->Date;
            $FulfillQty=$ProductWiseFulfillment[$ostr]->FulfillQty;
             $time=date('H:i:s');
             $datetime = $date.' '.$time;
            $Rate=$ProductWiseFulfillment[$ostr]->Rate;
            $SaleValue=$ProductWiseFulfillment[$ostr]->SaleValue;
            $product_qty=!empty($ProductWiseFulfillment[$ostr]->product_qty)?$ProductWiseFulfillment[$ostr]->product_qty:'0';
            // $fullfilment_type=$ProductWiseFulfillment[$ostr]->fullfilment_type;
        
             
             
            
                 $ostr++;
 
      
  
   $cqr ="INSERT INTO `fullfillment_order`(`order_id`,`company_id`, `dealer_id`,`retailer_id`,`retailer_name`,`date`,`time`,`order_date`,`fullfilment_type`,`invoice_number`,`server_date`,`created_by`) 
       VALUES ('$order_id','$company_id','$dealerid','$retailer_id','$retailer_name','$date','$time','$datetime','2','$invoive_number',NOW(),'$dealerid')";
          $results=mysqli_query($dbc,$cqr);
          if($results){
          $sqr="INSERT INTO `fullfillment_order_details`(`order_id`,`company_id`, `product_id`,`product_name`,`product_qty`,`product_rate`,`product_value`, `product_fullfiment_qty`,`created_at`) 
            VALUES ('$order_id','$company_id','$product_id','$product_name','$product_qty','$Rate','$SaleValue','$FulfillQty',NOW())";
          $resultqt=mysqli_query($dbc,$sqr);
if($resultqt){
   $upqry="UPDATE `dealer_balance_stock` SET `stock_qty`=`stock_qty`-$FulfillQty  where `product_id`='$product_id'AND `dealer_id`='$dealerid'";
        $retq=mysqli_query($dbc,$upqry);
}
          }
           }

}

#############################
if(!empty($FullfillmentSummary)){
  $totFullfillmentSummary=count($FullfillmentSummary);
  $ost=0;
      
  while($ost<$totFullfillmentSummary){
           
              $current_date=$FullfillmentSummary[$ost]->current_date;
              $date_time=$FullfillmentSummary[$ost]->date_time;
              $selectdate=$FullfillmentSummary[$ost]->selectdate;
              $time=$FullfillmentSummary[$ost]->time;
              $order_id=$FullfillmentSummary[$ost]->order_id;    
              $retailer_id=$FullfillmentSummary[$ost]->retailer_id;    
              $retailer_name=$FullfillmentSummary[$ost]->retailer_name;    
              $invoice_number=$FullfillmentSummary[$ost]->invoice_number;    
          $status='1';//OrderWise Details
             
             
             
            
                 $ost++;
 
       
  
     $counter_q ="INSERT INTO `fullfillment_order`(`order_id`,`company_id`,`user_id`,`dealer_id`,`retailer_id`,`retailer_name`,`date`,`time`,`order_date`,`fullfilment_type`,`invoice_number`,`server_date`,`created_by`) 
       VALUES ('$order_id','$company_id','0','$dealerid','$retailer_id','$retailer_name','$selectdate','$time','$date_time','2','$invoice_number',NOW(),'$dealerid')";
          $result=mysqli_query($dbc,$counter_q);
 }
}

if(!empty($FullfillmentDetail)){
  $total_FullfillmentDetail=count($FullfillmentDetail);
  $osit=0;
      
  while($osit<$total_FullfillmentDetail){
              $rate=$FullfillmentDetail[$osit]->rate;
              $qty=$FullfillmentDetail[$osit]->qty;
              $prod_code=$FullfillmentDetail[$osit]->prod_code;
              $product_name=$FullfillmentDetail[$osit]->product_name;
              $case_value=$FullfillmentDetail[$osit]->case_value;
              $rate=$FullfillmentDetail[$osit]->rate;
              $date_time=$FullfillmentDetail[$osit]->date_time;
              $order_id=$FullfillmentDetail[$osit]->orderid  ;    
              $sale_value=$rate*$qty;  
              $osit++;
  
       
  
      $sales_q ="INSERT INTO `fullfillment_order_details`(`order_id`,`company_id`, `product_id`,`product_name`, `product_qty`,`product_rate`, `product_value`,`product_fullfiment_qty`,`created_at`) 
       VALUES ('$order_id','$company_id','$prod_code','$product_name','$qty','$rate','$sale_value','$qty','NOW()')";
          $resultq=mysqli_query($dbc,$sales_q);
          if($resultq){
 $upqry="UPDATE `dealer_balance_stock` SET `stock_qty`=`stock_qty`-$qty  where `product_id`='$prod_code'AND `dealer_id`='$dealerid'";
        $retq=mysqli_query($dbc,$upqry);
}
}
}
#########################
if(!empty($StockSummary)){
  $total_stockSummary=count($StockSummary);
  $os=0;
      
  while($os<$total_stockSummary){
           
            $current_date=$StockSummary[$os]->current_date;
              $date_time=$StockSummary[$os]->date_time;
              $selectdate=$StockSummary[$os]->selectdate;
              $qty=$StockSummary[$os]->qty;
              $prod_code=$StockSummary[$os]->prod_code;
              $case_value=$StockSummary[$os]->case_value;
              $date_time=$StockSummary[$os]->date_time;
              $order_id=$StockSummary[$os]->order_id  ;    
  
            
                 $os++;
 
       
  
      $counter_q ="INSERT INTO `user_primary_sales_order`(`id`,`order_id`, `dealer_id`, `created_date`,
        `created_person_id`, `sale_date`,`receive_date`, `date_time`,`company_id`) 
       VALUES ('$order_id','$order_id','$dealerid','$date_time','0','$selectdate','$date_time','$date_time','$company_id')";
          $result=mysqli_query($dbc,$counter_q);
 }
}

if (!empty($paymentCollect)) {
            $paycount = count($paymentCollect);
            $pay = 0;
            while ($pay < $paycount) {
                $dealer = $paymentCollect[$pay]->tdcode;
                $location = $paymentCollect[$pay]->tlcode;
                $trcode = $paymentCollect[$pay]->trcode;
                $mode = $paymentCollect[$pay]->tpaymode;
                $anount = $paymentCollect[$pay]->tamount2;
                $branch = $paymentCollect[$pay]->tbbranch;
                $chequeno = $paymentCollect[$pay]->tcheqno;
                $cheque_date = $paymentCollect[$pay]->tcheqdate;
                $trans_no = $paymentCollect[$pay]->transno;
                $trans_date = $paymentCollect[$pay]->transdate;
                $ttime = $paymentCollect[$pay]->ttime;
                $retailer = $paymentCollect[$pay]->retailer_id;
                $today = date("Y-m-d");
                // $user_id;
           echo     $q = "INSERT INTO `payment_collect_retailer`(`dealer_id`, `retailer_id`, `tl_code`, `tr_code`, `payment_mode`, `amount`, `bank_branch`,
         `cheque_no`, `cheque_date`, `trans_no`, `trans_date`, `payment_date`, `payment_time`, `user_id`,`company_id`) VALUES('$dealer','$retailer',
         '$location','$trcode','$mode','$anount','$branch','$chequeno','$cheque_date','$trans_no','$trans_date','$today','$ttime','0','$company_id')";
                $result = mysqli_query($dbc, $q);
            

                $pay++;
            }
        }

if(!empty($Stock_SummaryDetail)){
  $total_stockSummaryDetails=count($Stock_SummaryDetail);
  $osi=0;
 
  while($osi<$total_stockSummaryDetails){
              $rate=$Stock_SummaryDetail[$osi]->rate;
              $qty=!empty($Stock_SummaryDetail[$osi]->qty)?$Stock_SummaryDetail[$osi]->qty:'0';
              $prod_code=$Stock_SummaryDetail[$osi]->prod_code;
              $case_value=$Stock_SummaryDetail[$osi]->case_value;
              $date_time=$Stock_SummaryDetail[$osi]->date_time;
              $order_id=$Stock_SummaryDetail[$osi]->orderid;    
          $date = date('Y-m-d', strtotime($date_time));
                 $osi++;
  
       
  
  $sales_q ="INSERT INTO `user_primary_sales_order_details`(`company_id`,`id`,`order_id`, `product_id`,`rate`, `quantity`,`scheme_qty`,`pr_rate`,`cases`) 
       VALUES ('$company_id','$order_id','$order_id','$prod_code','$rate','$qty','0','$rate','$case_value')";
          $resultq=mysqli_query($dbc,$sales_q);
          if($resultq){
  $chr= "SELECT id, stock_qty FROM dealer_balance_stock  WHERE `product_id`='$prod_code'AND `dealer_id`='$dealerid' ";
 $req=mysqli_query($dbc,$chr);
 $check=mysqli_num_rows($req);
if($check>0)
{
 $upqry="UPDATE `dealer_balance_stock` SET `stock_qty`=`stock_qty`+$qty  WHERE `product_id`='$prod_code'AND `dealer_id`='$dealerid'";
        $retq=mysqli_query($dbc,$upqry);
}else{
    $insq ="INSERT INTO `dealer_balance_stock`(`company_id`,`order_id`,`dealer_id`,`user_id`, `product_id`,`stock_qty`,`cases`,`submit_date_time`,`server_date_time`) VALUES ('$company_id','$order_id','$dealerid','0','$prod_code','$qty','$case_value','$date_time',NOW())";
 $retq=mysqli_query($dbc,$insq);

}


}
}
}





ob_start();
ob_clean();
                         $uniqueId=  implode(',',$uid);
                   $essential= array("response"=>"Y","unique_id"=>$uiddtl); 
                         $data = json_encode($essential);
                         echo $data;
  
ob_get_flush();
ob_end_flush();

}


}
else{
                        $essential= array("response"=>"N","unique_id"=>'null'); 
                         $data = json_encode($essential);
                         echo $data;
}
