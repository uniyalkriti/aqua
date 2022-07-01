<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
//$dbc = @mysqli_connect('localhost','root','Dcatch','msell-dsgroup-dms') OR die ('could not connect:');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();


if(isset($_POST['response']))
{
$checkRes  = str_replace("'","",$_POST['response']);
$str = str_replace('\\', '|', $checkRes);
} 
else $str='';
$check=cleanSpecialChar($str);

#This method used for removing special charactor
function cleanSpecialChar($string) 
{
   return preg_replace('/[^A-Za-z0-9\s:\/"{},[]]/', '', $string); // Removes special chars.
}
 // echo $check;
$utf8 = utf8_encode($check);
$data = json_decode($utf8);

if ($data) 
{
    $user_id = $data->response->user_id;
    $q = "SELECT * From person_login WHERE person_id='$user_id'";
    $user_res = mysqli_query($dbc, $q);
    $q_person = mysqli_fetch_assoc($user_res);
    $person_id = $q_person['person_id'];
    $status = $q_person['person_status'];
    $company_id = $q_person['company_id'];


    mysqli_query($dbc, "update person_login SET last_mobile_access_on=NOW(), app_type='SFA' Where person_id='$person_id'");
    if ($status == '1') 
    {
        
        $attendance = $data->response->Attandance;
        $Checkoutlocation = $data->response->Checkoutlocation;
        $merchandiserCheckIn = $data->response->merchandiserCheckIn;
        $merchandiserCheckout = $data->response->merchandiserCheckout;
        $BalanceStock = $data->response->BalanceStock;
        $coverageCheckIn = $data->response->coverageCheckIn;
        $dealerCheckout = $data->response->dealerCheckout;
        $mobile_dtls = $data->response->MobileDetails;
        $callwisereporting = $data->response->CallWiseReporting;
        $callwisereportingstatus = $data->response->CallWiseReportingStatus;


        if (!empty($PrimarySaleSummary)) {
            //print_r($PrimarySaleSummary);
            $psale = count($PrimarySaleSummary);
            $ps = 0;
            $del_order_id=$PrimarySaleSummary[0]->order_id;
            $delq="DELETE FROM user_primary_sales_order_details WHERE order_id='$del_order_id' AND company_id='$company_id' ";
            $rdel=mysqli_query($dbc,$delq);
            while ($ps < $psale) {
                $order_id = $PrimarySaleSummary[$ps]->order_id;
                $product_id = $PrimarySaleSummary[$ps]->product_id;
                $rate = $PrimarySaleSummary[$ps]->rate;  //app side sending pcs_rate as pcs rate(dealer rate)
                $quantity = $PrimarySaleSummary[$ps]->quantity;
                $scheme_qty = $PrimarySaleSummary[$ps]->scheme_qty;
                $case = $PrimarySaleSummary[$ps]->case;
                $pcs = $PrimarySaleSummary[$ps]->pcs;
                $case_rate = $PrimarySaleSummary[$ps]->case_rate;
                $pcs_rate = $PrimarySaleSummary[$ps]->pcs_rate;
                $discount = $PrimarySaleSummary[$ps]->discount;
                $value = $PrimarySaleSummary[$ps]->value;

                // $q = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`,`cases`,`pcs`,`pr_rate`,`discount_percent`) "
                //     . " VALUES('$order_id$ps','$order_id$user_id','$product_id','$rate','$quantity','$scheme_qty','$case','$pcs','$case_rate','$discount')";
                // $result = mysqli_query($dbc, $q);
                

                    $q = "INSERT INTO `user_primary_sales_order_details`(`company_id`,`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`,`cases`,`pcs`,`pr_rate`,`discount_percent`) "
                    . " VALUES('$company_id',$order_id$ps','$order_id','$product_id','$rate','$quantity','$scheme_qty','$case','$pcs','$case_rate','$discount')";
                $result = mysqli_query($dbc, $q);

                $ps++;
            }
        }

        if (!empty($Primarysaledetail)) {
            $psalesum = count($Primarysaledetail);
            $pd = 0;
            while ($pd < $psalesum) {
                $order_id = $Primarysaledetail[$pd]->order_id;
                $dealer_id = $Primarysaledetail[$pd]->dealer_id;
                $created_date = $Primarysaledetail[$pd]->created_date;
                $sale_date = $Primarysaledetail[$pd]->sale_date;
                $date_time = $Primarysaledetail[$pd]->date_time;
                $lat = $Primarysaledetail[$pd]->lat;
                $lng = $Primarysaledetail[$pd]->lng;
                $address = $Primarysaledetail[$pd]->address;

                $q = "INSERT INTO `user_primary_sales_order`(`id`,`order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`,`company_id`,`lat`,`lng`,`address`)"
                    . " VALUES('$order_id$user_id','$order_id','$dealer_id','$created_date','$user_id','$sale_date',NOW(),'$date_time','$company_id','$lat','$lng','$address')";
//                echo $q;die;
                $result = mysqli_query($dbc, $q);
                $pd++;
            }
        }

        if (!empty($BalanceStock)) {
            // print_r($retailerstock);
            $count_BalanceStock = count($BalanceStock);
            $bre = 0;

            while ($bre < $count_BalanceStock) {
                $dealer_id = $BalanceStock[$bre]->dealer_id;
                $mfg_date = $BalanceStock[$bre]->mfg_date;
                $balance_pieces = $BalanceStock[$bre]->balance_pieces;
                $balance_cases = $BalanceStock[$bre]->balance_cases;
                $balance_product_code = $BalanceStock[$bre]->balance_product_code_;
                $balance_order_id = $BalanceStock[$bre]->balance_order_id;
                $mobile_datetime = $BalanceStock[$bre]->mobile_datetime;
                $bsmrp = $BalanceStock[$bre]->mrp;
                $balance_pcs_mrp = $BalanceStock[$bre]->balance_pcs_mrp;
                $lat = $BalanceStock[$bre]->lat;
                $lng = $BalanceStock[$bre]->lng;
                $address = $BalanceStock[$bre]->address;
                $PrimarySaleSummary = $data->response->PrimarySaleSummary;
                $Primarysaledetail = $data->response->PrimarySaleDetail;

                $exp_date = strtotime(date('Y-m-d', strtotime($mfg_date)) . '-1 year');

                $qbr = "INSERT INTO `dealer_balance_stock`(`company_id`,`order_id`, `dealer_id`, `user_id`, `product_id`, `stock_qty`, `mfg_date`, `exp_date`, `cases`, `submit_date_time`, `server_date_time`,`mrp`,`pcs_mrp`,sstatus,`lat`,`lng`,`address`) VALUES ('$company_id','$balance_order_id$user_id','$dealer_id','$user_id','$balance_product_code','$balance_pieces','$mfg_date','$exp_date','$balance_cases','$mobile_datetime',NOW(),'$bsmrp','$balance_pcs_mrp','0','$lat','$lng','$address')";
                $result_br = mysqli_query($dbc, $qbr);

                // Get state id of dealer
                // $stq = "SELECT l2_id FROM location_view l INNER JOIN dealer_location_rate_list r ON  l.l5_id=r.location_id WHERE r.dealer_id=$dealer_id LIMIT 1";
                // $rstq=mysqli_query($dbc,$stq);
                // $row=mysqli_fetch_assoc($rstq);

                // $state_id = $row['l2_id'];

                // // Get rates and mrp of dealer
                // $rate_q = "SELECT mrp,dealer_rate,retailer_rate,ss_id FROM product_rate_list WHERE state_id=$state_id AND product_id=$balance_product_code LIMIT 1";
                // $rate_e = mysqli_query($dbc,$rate_q);
                // $rate = mysqli_fetch_assoc($rate_e);

                // $mrp = $rate['mrp'];
                // $dealer_rate = $rate['dealer_rate'];
                // $retailer_rate = $rate['retailer_rate'];
                // $csa_id = $rate['ss_id'];

                // $stk_q = "SELECT product_id FROM stock WHERE dealer_id=$dealer_id AND product_id=$balance_product_code AND mrp=$mrp";
                // $stk_e = mysqli_query($dbc,$stk_q);
                // if(mysqli_num_rows($stk_e)>0)
                // {
                //  $stk_uq = "UPDATE `stock` SET `rate`='$retailer_rate',`dealer_rate`='$dealer_rate',`mrp`='$mrp',`person_id`='$user_id',`qty`='$balance_pieces',`salable_damage`='0',`nonsalable_damage`='0',`mfg`='$mfg_date',`date`=NOW(),`last_updated`=NOW(),`company_id`='1' WHERE dealer_id=$dealer_id AND product_id=$balance_product_code AND mrp=$mrp";
                // }else{
                //  $stk_uq = "INSERT INTO `stock`(`product_id`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `date`,`last_updated`,`company_id`) VALUES ('$balance_product_code','$retailer_rate','$dealer_rate','$mrp','$user_id','$csa_id','$dealer_id',$balance_pieces,'0','0','0','$mfg_date',NOW(),NOW(),'1')";
                // }

                //mysqli_query($dbc,$stk_uq);
//echo $qbr;
                $bre++;
            }

        }
        if (isset($attendance) && !empty($attendance)) {
            $attcount = count($attendance);
            $k = 0;
            while ($k < $attcount) {
                $location = $data->response->Attandance[$k]->track_addrs;
                $remark = $data->response->Attandance[$k]->remarks;
                $status = $data->response->Attandance[$k]->work_status;
                $latlng = $data->response->Attandance[$k]->lat_lng;
                $order_id = $data->response->Attandance[$k]->order_id;
                $date_time = $data->response->Attandance[$k]->work_date;
                $working_with = $data->response->Attandance[$k]->colleague_id;
                $mnc_mcc_lat_cellid = $data->response->Attandance[$k]->mnc_mcc_lat_cellid;
                $battery_status = $data->response->Attandance[$k]->battery_status;
                $gps_status = $data->response->Attandance[$k]->gps_status;
                $ll = explode(",", $latlng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $new_work_date = date('Y-m-d', strtotime($date_time));
                $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from user_daily_attendance where company_id='" . $company_id . "'  AND user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {
                    $q = "INSERT INTO `user_daily_attendance`(`company_id`,`user_id`, `order_id`, `work_date`,`working_with`, `work_status`,`mnc_mcc_lat_cellid`, `lat_lng`, `track_addrs`, `remarks`,`server_date`,`battery_status`,`gps_status`)VALUES
                        ('$company_id','$user_id','$order_id$user_id','$date_time','$working_with','$status','$mnc_mcc_lat_cellid','$latlng','$user_location','$remark',CURRENT_TIMESTAMP,'$battery_status','$gps_status')";
                    $run = mysqli_query($dbc, $q);

                    $qd="INSERT INTO `user_work_tracking`(`company_id`,`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`) VALUES ('$company_id','$user_id','$new_work_date','$new_work_time','$mnc_mcc_lat_cellid','$latlng','$user_location','Attendance',NOW(),'$battery_status','$gps_status')";
                    $rd = mysqli_query($dbc, $qd);
                }
                $k++;
            }
        }
        if (!empty($Checkoutlocation)) {
            $checkcount = count($Checkoutlocation);
            $c = 0;
            while ($c < $checkcount) {
                $latlng = $Checkoutlocation[$c]->latlng;
                $mcc_mnc_lac_cellId_final = $Checkoutlocation[$c]->mcc_mnc_lac_cellId_final;
                $time = $Checkoutlocation[$c]->tim;
                $location = $Checkoutlocation[$c]->check_out;
                $date_time = $Checkoutlocation[$c]->date_time;
                $order_id = $Checkoutlocation[$c]->order_id;
                $total_call = $Checkoutlocation[$c]->total_call;
                $total_pc = $Checkoutlocation[$c]->total_productive_call;
                $total_sale_value = $Checkoutlocation[$c]->total_sale_value;
                $battery_status = $Checkoutlocation[$c]->battery_status;
                $gps_status = $Checkoutlocation[$c]->gps_status;
                if(isset($Checkoutlocation[$c]->remarks))
                {
                    $remarks=$Checkoutlocation[$c]->remarks;
                }else{
                    $remarks="N/A";
                }
                $ll = explode(",", $latlng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $new_work_date = date('Y-m-d', strtotime($date_time));
                $new_work_time = date('H:i:s', strtotime($date_time));
                $q2 = "SELECT *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where company_id='" . $company_id . "' AND user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) 
                {
                    $q = "INSERT INTO check_out(`company_id`,`user_id`,`lat_lng`,`mnc_mcc_lat_cellid`,`work_date`,`server_date_time`,`attn_address`,`order_id`,`remarks`,`total_call`,`total_pc`,`total_sale_value`,`battery_status`,`gps_status`)
                        VALUES('$user_id','$latlng','$mcc_mnc_lac_cellId_final','$date_time','$current_date_time','$user_location','$order_id$user_id','$remarks','$total_call','$total_pc','$total_sale_value','$battery_status','$gps_status')";

                    #update user_daily_attendance with checkout
                    $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$date_time', `checkout_server_date_time` = '$current_date_time',`checkout_remarks` = '$remarks', `checkout_lat_lng`= '$latlng', `checkout_mnc_mcc_lat_cellid`='$mcc_mnc_lac_cellId_final',`checkout_address`='$user_location'
                        WHERE `user_daily_attendance`.`user_id` = '$user_id' and company_id='$company_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($date_time,'Y-m-d')";

                    $run_update = mysqli_query($dbc, $update_user_daily_attendance);
                    $result = mysqli_query($dbc, $q);

                    $qd="INSERT INTO `user_work_tracking`(`company_id`,`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`) VALUES ('$company_id','$user_id','$new_work_date','$new_work_time','$mcc_mnc_lac_cellId_final','$latlng','$user_location','CheckOut',NOW(),'$battery_status','$gps_status')";
                    $rd = mysqli_query($dbc, $qd);
                }
                $c++;
            }
        }
        // coverageCheckIn
        if (!empty($coverageCheckIn))
        {
            foreach ($coverageCheckIn as $mci)
            {
                $IncoverageCheckIn="INSERT INTO `coverage_checkin`( `company_id`,`orderId`, `workDate`, `workStatus`, `mncMcc`, `latLng`, `address`, `remark`, `colleagueId`, `dealerId`, `userId`, `created_at`, `updated_at`) VALUES ( '$company_id','$mci->orderId', '$mci->workDate', '$mci->workStatus', '$mci->mncMcc', '$mci->latLng', '$mci->address', '$mci->remark', '$mci->colleagueId', '$mci->dealerId', '$mci->userId', NOW(), NOW())";

                $Cquery_run = mysqli_query($dbc, $IncoverageCheckIn);
            }
        }
        // coverage_checkout
        if (!empty($dealerCheckout))
        {
            foreach ($dealerCheckout as $mco)
            {
                $dealerCheckoutIN="INSERT INTO `coverage_checkout`(`company_id`,`dateTime`, `time`, `latLng`, `checkOut`, `mccMnc`, `orderId`, `dealerId`, `userId`, `remark`, `created_at`, `updated_at`) VALUES ('$company_id','$mco->dateTime', '$mco->time', '$mco->latLng', '$mco->checkOut', '$mco->mccMnc', '$mco->orderId', '$mco->dealerId', '$mco->userId', '$mco->remark',NOW(),NOW())";

                $co_query_run = mysqli_query($dbc, $dealerCheckoutIN);
            }
        }


        #merchandiserCheckIn
        if (!empty($merchandiserCheckIn))
        {
            foreach ($merchandiserCheckIn as $mci)
            {
                $insert_merchandiserCheckIn="INSERT INTO `merchandiser_checkin`( `company_id`,`orderId`, `workDate`, `workStatus`, `mncMcc`, `latLng`, `address`, `remark`, `colleagueId`, `dealerId`, `userId`, `created_at`, `updated_at`) VALUES ( '$company_id','$mci->orderId', '$mci->workDate', '$mci->workStatus', '$mci->mncMcc', '$mci->latLng', '$mci->address', '$mci->remark', '$mci->colleagueId', '$mci->dealerId', '$mci->userId', NOW(), NOW())";

                $merchandiserCheckIn_query_run = mysqli_query($dbc, $insert_merchandiserCheckIn);
            }
        }

        

        #merchandiserCheckout
        if (!empty($merchandiserCheckout))
        {
            foreach ($merchandiserCheckout as $mco)
            {
                $insert_merchandiserCheckout="INSERT INTO `merchandiser_checkout`(`company_id`,`dateTime`, `time`, `latLng`, `checkOut`, `mccMnc`, `orderId`, `dealerId`, `userId`, `remark`, `created_at`, `updated_at`) VALUES ('$company_id','$mco->dateTime', '$mco->time', '$mco->latLng', '$mco->checkOut', '$mco->mccMnc', '$mco->orderId', '$mco->dealerId', '$mco->userId', '$mco->remark',NOW(),NOW())";

                $merchandiserCheckout_query_run = mysqli_query($dbc, $insert_merchandiserCheckout);
            }
        }


        if (!empty($mobile_dtls)) {
            $cur_date = date('Y-m-d H:i:s');
            $cd = 0;
            $count_dtls = count($mobile_dtls);
            while ($cd < $count_dtls) {
                $user_id = $user_id;
                $d_name = $mobile_dtls[$cd]->deviceName;
                $d_manu = $mobile_dtls[$cd]->deviceMan;
                $d_version = $mobile_dtls[$cd]->deviceAndroidVersion;

                $mob_qry = "INSERT INTO user_mobile_details( `company_id`,`user_id`, `device_name`, `device_manuf`, `device_version`, `server_date_time`) "
                    . "VALUES('$company_id','$user_id','$d_name','$d_manu','$d_version','$cur_date')";
                $mob_qry_run = mysqli_query($dbc, $mob_qry);
                $cd++;
            }
        }


        ///////////////////////////////////////////////////////////////////////////////
        if (!empty($callwisereportingstatus)) {
            $callwisereport = count($callwisereportingstatus);
            $p = 0;
            while ($p < $callwisereport) {
                $orderid = $callwisereportingstatus[$p]->order_id;
                $uid = $callwisereportingstatus[$p]->unique_id;
                $product_id = $callwisereportingstatus[$p]->product_id;
                $prod_qty = $callwisereportingstatus[$p]->quantity;
                $scheme_qty = $callwisereportingstatus[$p]->scheme_qty;
                $rate = $callwisereportingstatus[$p]->rate;


                $chk_usod = "SELECT id FROM user_sales_order_details WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND company_id='$company_id' ";
                $run_uhod = mysqli_query($dbc, $chk_usod);
                $num_data = mysqli_num_rows($run_uhod);
                if ($num_data < 1) {
                    $qusod = "INSERT INTO `user_sales_order_details`(`company_id`,`order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`)"
                        . " VALUES('$company_id','$orderid$user_id','$product_id','$rate','$prod_qty','$scheme_qty')";


                } else {
                    $qusod = "UPDATE `user_sales_order_details` SET `quantity`='$prod_qty',`rate`='$rate' WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND company_id='$company_id' ";
                }

                $results = mysqli_query($dbc, $qusod);
                if ($results) {
                    $unique_id[] = $uid;

                }
                $p++;
            }
        }

        if (!empty($callwisereporting)) {
            $phonestatus = count($callwisereporting);
            $l = 0;
            while ($l < $phonestatus) {
                $beat_id = $callwisereporting[$l]->location_id;
                $discount_before = $callwisereporting[$l]->total_sale_value;
                $total_sale_qty = $callwisereporting[$l]->total_sale_qty;
                $override_status = $callwisereporting[$l]->override_status;
                $order_id = $callwisereporting[$l]->order_id;

                $date = $callwisereporting[$l]->date;
                $mcc_mnc = $callwisereporting[$l]->mccmnclatcellid;
                $time = $callwisereporting[$l]->time;
                $location = $callwisereporting[$l]->track_address;
                $dealer_id = $callwisereporting[$l]->dealer_id;
                $lat_lng = $callwisereporting[$l]->lat_lng;
                $lat_lngs = str_replace(' ',',',$lat_lng);

                $discount = !empty($callwisereporting[$l]->Discount)?$callwisereporting[$l]->Discount:'0.0';
                
                $total_sale_value = $callwisereporting[$l]->Finalvalue;
                $retailer_id = $callwisereporting[$l]->retailer_id;
                $call_status = $callwisereporting[$l]->call_status;
                $remarks = $callwisereporting[$l]->remarks;
                $customerName=$callwisereporting[$l]->customerName;
                $customerMobile=$callwisereporting[$l]->customerMobile;
                $geo_status = $callwisereporting[$l]->geo_status;
                $gps_status = !empty($callwisereporting[$l]->gps_status)?$callwisereporting[$l]->gps_status:'0';
                $battery_status = !empty($callwisereporting[$l]->battery_status)?$callwisereporting[$l]->battery_status:'0';
                $non_productive_id = !empty($callwisereporting[$l]->non_productive_id)?$callwisereporting[$l]->non_productive_id:'0';

                


                // $user_id=$callwisereporting[$l]->user_id;

                $ll = explode(",", $lat_lng);
                if ($location == "$$" || $location == ' ') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $chk_uso = "SELECT id FROM user_sales_order WHERE order_id='$order_id$user_id'";
                $run_uso = mysqli_query($dbc, $chk_uso);
                $num_uso = mysqli_num_rows($run_uso);
                if ($num_uso < 1) {
                    $quso = "INSERT INTO `user_sales_order`(`order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`,`company_id`,`call_status`, `total_sale_value`,`discount`,`amount`, `total_sale_qty`,`lat_lng`, `mccmnclatcellid`,`geo_status`,`track_address`, `date`, `time`, `image_name`, `override_status`,`remarks`,`battery_status`,`gps_status`,`reason`,`customer_name`,`customer_number`)"
                        . " VALUES('$order_id$user_id','$user_id','$dealer_id','$beat_id','$retailer_id','$company_id','$call_status','$total_sale_value','$discount','$discount_before','$total_sale_qty','$lat_lng','$mcc_mnc','$geo_status','$user_location','$date','$time','.jpg','$override_status','$remarks','$battery_status','$gps_status','$non_productive_id','$customerName','$customerMobile')";
                    //      echo $q;
                 $qd="INSERT INTO `user_work_tracking`(`company_id`,`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`) VALUES ('$company_id','$user_id','$date','$time','$mcc_mnc','$lat_lng','$user_location','Order Booking',NOW(),'$battery_status','$gps_status')";
                $rd = mysqli_query($dbc, $qd);
                }
                #Update archiv in MTP
                $cur_date=date('Y-m-d',strtotime('now'));
                $update_mtp="UPDATE monthly_tour_program SET arch=arch+$total_sale_value WHERE person_id=$user_id and working_date=$cur_date and company_id=$company_id ";
                $update_mtp_result = mysqli_query($dbc, $update_mtp);
                $result = mysqli_query($dbc, $quso);
                $l++;
            }
        }

    }  // person status if clause ends here 
    else 
    {
        // echo 'N';
    }


    ob_start();
    ob_clean();
    $uniqueId = implode(',', $unique_id);
    $essential = array("response" => "Y", "unique_id" => $uniqueId);
    $data = json_encode($essential);
    echo $data;

    ob_get_flush();
    ob_end_flush();

} else {
    $essential = array("response" => "N", "unique_id" => 'null');
    $data = json_encode($essential);
    echo $data;
}