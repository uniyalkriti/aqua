<?php
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
//$dbc = @mysqli_connect('localhost','root','Dcatch','msell-dsgroup-dms') OR die ('could not connect:');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();

// $check = '';
 #Added By Deepak At 28-01-2019
 #Removing special character from post JSON 
//echo'ankushhhhhhhhhhhh';
 //print_r($_POST['response']);die;

if(isset($_POST['response']))
{
$checkRes  = str_replace("'","",$_POST['response']);
$str = str_replace('\\', '|', $checkRes);
// $checkRes  = str_replace("'","",$_POST['response']);
} 
else $str='';


 //$str='';
$check=cleanSpecialChar($str);



//$check = '';

#This method used for removing special charactor
function cleanSpecialChar($string) 
{
   return preg_replace('/[^A-Za-z0-9\s:\/"{},[]]/', '', $string); // Removes special chars.
}
 // 
$utf8 = utf8_encode($check);

$data = json_decode($utf8);

//print_r($utf8);die;

 //print_r($data);die;


if ($data) {
//
    $user_id = $data->response->user_id;
    $q = "SELECT * From person_login WHERE person_id='$user_id'";

    $user_res = mysqli_query($dbc, $q);
    $q_person = mysqli_fetch_assoc($user_res);
    $person_id = $q_person['person_id'];
    $status = $q_person['person_status'];

    mysqli_query($dbc, "update person_login SET last_mobile_access_on=NOW(), app_type='SFA' Where person_id='$person_id'");
    if ($status == '1') {

        $paymentReceviedDetails = $data->response->paymentReceivedDetail;

        $expense = $data->response->Expense;
        $retailerstock = $data->response->RetailerStock;
        $BalanceStock = $data->response->BalanceStock;
        $retailerstockdetails = $data->response->RetailerStockStatus;
        $merchandise = $data->response->MERCHANDISE;
        $merchandise_requirement = $data->response->MERCHANDISE_REQUIREMENT;
        $attendance = $data->response->Attandance;
        $Checkoutlocation = $data->response->Checkoutlocation;
        $callwisereporting = $data->response->CallWiseReporting;
        $callwisereportingstatus = $data->response->CallWiseReportingStatus;
        $tracking = $data->response->Tracking;
        $Complaint = $data->response->Complaint;
        $createcustomer = $data->response->CreateCustomer;
        $callwisereason = $data->response->CallWiseReason;
        $mtp = $data->response->Mtp;
        $PrimarySaleSummary = $data->response->PrimarySaleSummary;
        $Primarysaledetail = $data->response->PrimarySaleDetail;
        $damage_detail = $data->response->DamageArray;
        $damage_detail_retailer = $data->response->damage_stock_retailer;
        $TotalCounterSale = $data->response->TotalCounterSale;
        $ISRSaleDetail = $data->response->ISRCallWiseReportingStatus;
        $JuniorCheckIn = $data->response->JuniorCheckIn;
        $JuniorCheckOut = $data->response->JuniorCheckOut;
        $ISRAttandance = $data->response->ISRAttandance;
        $RetailerSchemeStatus = $data->response->RetailerSchemeStatus;
        $RetailerSchemeStatusOtherFocusState = $data->response->RetailerSchemeStatusOtherFocusState;
        $RetailerSchemeStatusDiscoveryoutlet = $data->response->RetailerSchemeStatusDiscoveryoutlet;
        $paymentCollect = $data->response->PaymentCollection;
        $paymentCollectDealer = $data->response->PaymentCollectionForDealer;
        $getRetailerLocation = $data->response->getRetailerLocation;
//        $userInformation = $data->response->newUserInformation; // repeated code
        $leaveUpdate = $data->response->leaveUpdate;
        $RetailerDeleteStatus = $data->response->RetailerDeleteStatus;
        $RetailerMerge = $data->response->MergeRetailer;
        $RetailerReshuffle = $data->response->RetailerReshuffle;
        $CometitorNewLaunchProductReport = $data->response->CometitorNewLaunchProductReport;
        $DailyProspectWorking = $data->response->DailyProspectWorking;
        $ComplaintReport = $data->response->ComplaintReport;
//        $CometitorNewLaunchProductReport = $data->response->CometitorNewLaunchProductReport;
        $TravellingExpenseBill = $data->response->TravellingExpenseBill;
        $feedbackSuggestion = $data->response->feedbackSuggestion;
        $pendingClaim = $data->response->pendingClaim;
        $competitivePriceIntelligence = $data->response->competitivePriceIntelligence;
        $ProductInvestigationReport = $data->response->productInvestigationReport;
        $UserEditInFormation = $data->response->UserEditInFormation;

        #Feedback
        if (!empty($feedbackSuggestion))
        {
            foreach ($feedbackSuggestion as $fs)
            {
                $insert_feedback="INSERT INTO `feedbackSuggestion` (`suggestion`, `suggested_start_date`, `estimated_volume_growth`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`) VALUES ('$fs->suggestion','$fs->suggested_start_date','$fs->estimated_volume_growth','$fs->order_id','$fs->user_id','$fs->latitude','$fs->longitude','$fs->location','$fs->mcc_mnc_lac_cellid','$fs->cur_date_time')";

                $feedback_query_run = mysqli_query($dbc, $insert_feedback);
            }
        }

        #update user
        if (isset($UserEditInFormation) && !empty($UserEditInFormation)) {
            foreach ($UserEditInFormation as $udata)
            {
                $update_user = "UPDATE `person` SET `email`='$udata->email_id',``='$user_id',`mobile`='$udata->mobile_no' WHERE `id` = '$udata->user_id'";
                $update_user_result = mysqli_query($dbc, $update_user);

                $person_log = "INSERT INTO `person_log` ( `user_id`, `mobile`, `updated by`) VALUES ('$udata->user_id', '$udata->mobile_no', 'app');";
                $person_log_result = mysqli_query($dbc, $person_log);
            }
        }

        #Product Investigation Report
        if (!empty($ProductInvestigationReport))
        {
            foreach ($ProductInvestigationReport as $pir)
            {
                $q2="INSERT INTO `product_investigation_report` (`brand_product`, `pack_size`, `product_purchased_from`, `product_purchased_from_town`, `product_purchased_from_district`, `product_purchased_from_state`, `product_purchased_from_phone_no`, `product_purchased_from_fax`, `product_purchased_from_email`, `other_town_estimated_sales`, `manufacture_detail`, `manufacture_town`, `manufacture_district`, `manufacture_state`, `manufacture_godown_phone`, `manufacture_godown_mobile`, `manufacture_godown_fax`, `manufacture_godown_email`, `manufacture_godown_office_phone`, `manufacture_godown_office_mobile`, `manufacture_godown_office_fax`, `manufacture_godown_office_email`, `manufacture_godown_residence_phone`, `manufacture_godown_residence_mobile`, `manufacture_godown_residence_fax`, `manufacture_godown_residence_email`, `detail_of_stockiest`, `stockiest_town`, `stockiest_district`, `stockiest_state`, `stockiest_godown_phone`, `stockiest_godown_mobile`, `stockiest_godown_fax`, `stockiest_godown_email`, `stockiest_godown_office_phone`, `stockiest_godown_office_mobile`, `stockiest_godown_office_fax`, `stockiest_godown_office_email`, `stockiest_godown_residence_phone`, `stockiest_godown_residence_mobile`, `stockiest_godown_residence_fax`, `stockiest_godown_residence_email`, `estimated_monthly_turnover`, `any_other_comment`, `latitude`, `longitude`, `geo_address`, `mcc_mnc`, `order_id`, `unique_id`, `date_time`, `user_id`) VALUES ('$pir->brand_product','$pir->pack_size','$pir->product_purchased_from','$pir->product_purchased_from_town','$pir->product_purchased_from_district','$pir->product_purchased_from_state','$pir->product_purchased_from_phone_no','$pir->product_purchased_from_fax','$pir->product_purchased_from_email','$pir->other_town_estimated_sales','$pir->manufacture_detail','$pir->manufacture_town','$pir->manufacture_district','$pir->manufacture_state','$pir->manufacture_godown_phone','$pir->manufacture_godown_mobile','$pir->manufacture_godown_fax','$pir->manufacture_godown_email','$pir->manufacture_godown_office_phone','$pir->manufacture_godown_office_mobile','$pir->manufacture_godown_office_fax','$pir->manufacture_godown_office_email','$pir->manufacture_godown_residence_phone','$pir->manufacture_godown_residence_mobile','$pir->manufacture_godown_residence_fax','$pir->manufacture_godown_residence_email','$pir->detail_of_stockiest','$pir->stockiest_town','$pir->stockiest_district','$pir->stockiest_state','$pir->stockiest_godown_phone','$pir->stockiest_godown_mobile','$pir->stockiest_godown_fax','$pir->stockiest_godown_email','$pir->stockiest_godown_office_phone','$pir->stockiest_godown_office_mobile','$pir->stockiest_godown_office_fax','$pir->stockiest_godown_office_email','$pir->stockiest_godown_residence_phone','$pir->stockiest_godown_residence_mobile','$pir->stockiest_godown_residence_fax','$pir->stockiest_godown_residence_email','$pir->estimated_monthly_turnover','$pir->any_other_comment','$pir->latitude','$pir->longitude','$pir->geo_address','$pir->mcc_mnc','$pir->order_id','$pir->unique_id','$pir->date_time','$pir->user_id')";

                $daily_query_run = mysqli_query($dbc, $q2);
            }
        }

        #Competitive Price Intelligence
        if (!empty($competitivePriceIntelligence))
        {
            foreach ($competitivePriceIntelligence as $cpi)
            {
                $q1 = "INSERT INTO `competitive_price_intelligence` (`id`, `brand`, `weight`, `mrp`, `being_sold_to_consumer`, `before_trade_scheme`, `trade_scheme`, `after_trade_scheme`, `units_per_case_bag`, `net_cost_price_to_retailer`, `retailer_margin_per_unit`, `consumer_scheme`, `must_enclose_cash_memo_no`, `must_enclose_cash_memo_date`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`) VALUES ('$cpi->id','$cpi->brand','$cpi->weight','$cpi->mrp','$cpi->being_sold_to_consumer','$cpi->before_trade_scheme','$cpi->trade_scheme','$cpi->after_trade_scheme','$cpi->units_per_case_bag','$cpi->net_cost_price_to_retailer','$cpi->retailer_margin_per_unit','$cpi->consumer_scheme','$cpi->must_enclose_cash_memo_no','$cpi->must_enclose_cash_memo_date','$cpi->order_id','$cpi->user_id','$cpi->latitude','$cpi->longitude','$cpi->location','$cpi->mcc_mnc_lac_cellid','$cpi->cur_date_time')";

                $q1_run = mysqli_query($dbc, $q1);
            }
        }

        #Pending Claim
        if (!empty($pendingClaim)) {
            foreach ($pendingClaim as $pc) {
                $pc_query = "INSERT INTO `pending_claim` (`submission_date`, `distributor_id`, `town_id`, `nature_of_claim`, `invoice_number`, `invoice_date`, `claim_paper`, `remark`, `expected_resolution_date`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`) VALUES ('$pc->submission_date','$pc->distributor_id','$pc->town_id','$pc->nature_of_claim','$pc->invoice_number','$pc->invoice_date','$pc->claim_paper','$pc->remark','$pc->expected_resolution_date','$pc->order_id','$pc->user_id','$pc->latitude','$pc->longitude','$pc->location','$pc->mcc_mnc_lac_cellid','$pc->cur_date_time');";

                $pc_query_run = mysqli_query($dbc, $pc_query);
            }
        }

        #TravellingExpenseBill
        if (!empty($TravellingExpenseBill)) {
            foreach ($TravellingExpenseBill as $tr_data) {
                $travelling_query = "INSERT INTO `travelling_expense_bill` (`travellingDate`, `arrivalTime`, `departureTime`, `distance`, `fare`, `da`, `hotel`, `postage`, `telephoneExpense`, `conveyance`, `misc`, `total`, `arrivalID`, `departureID`, `travelModeID`, `date_time`, `lat_lng`, `geo_address`, `mcc_mnc`, `unique_id`, `order_id`, `user_id`) VALUES ('$tr_data->travellingDate','$tr_data->arrivalTime','$tr_data->departureTime','$tr_data->distance','$tr_data->fare','$tr_data->da','$tr_data->hotel','$tr_data->postage','$tr_data->telephoneExpense','$tr_data->conveyance','$tr_data->misc','$tr_data->total','$tr_data->arrivalID','$tr_data->departureID','$tr_data->travelModeID','$tr_data->date_time','$tr_data->lat_lng','$tr_data->geo_address','$tr_data->mcc_mnc','$tr_data->unique_id','$tr_data->order_id','$tr_data->user_id')";
                $travelling_query_run = mysqli_query($dbc, $travelling_query);
            }
        }

        #CometitorNewLaunchProductReport
        if (!empty($CometitorNewLaunchProductReport)) {
            foreach ($CometitorNewLaunchProductReport as $compProduct) {
                $cmp_query = "INSERT INTO `competitors_launched_product` (`user_id`,`town_area`, `launch_date`, `product_and_brand_name`, `marketed_by`, `sddcsacf`, `address`, `town`, `district`, `state`, `pincode`, `weight`, `nature_of_inner_packaging`, `nature_of_outer_packaging`, `brand`, `weight_pricing`, `mrp`, `being_sold_to_consumer`, `before_trade_scheme`, `trade_scheme`, `after_trade_scheme`, `units_per_case_bag`, `net_cost_price_to_retailer`, `retailer_margin_per_unit`, `consumer_scheme`, `must_enclose_if_any`, `must_enclose_cash_memo`, `must_enclose_cash_memo_date`, `advertising_support`, `pop_material_send_sample`, `outlet_covered`, `samples_must_send`, `comments`, `orderid`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`) VALUES ('$compProduct->user_id','$compProduct->town_area','$compProduct->launch_date','$compProduct->product_and_brand_name','$compProduct->marketed_by','$compProduct->sddcsacf','$compProduct->address','$compProduct->town','$compProduct->district','$compProduct->state','$compProduct->pincode','$compProduct->weight','$compProduct->nature_of_inner_packaging','$compProduct->nature_of_outer_packaging','$compProduct->brand','$compProduct->weight_pricing','$compProduct->mrp','$compProduct->being_sold_to_consumer','$compProduct->before_trade_scheme','$compProduct->trade_scheme','$compProduct->after_trade_scheme','$compProduct->units_per_case_bag','$compProduct->net_cost_price_to_retailer','$compProduct->retailer_margin_per_unit','$compProduct->consumer_scheme','$compProduct->must_enclose_if_any','$compProduct->must_enclose_cash_memo','$compProduct->must_enclose_cash_memo_date','$compProduct->advertising_support','$compProduct->pop_material_send_sample','$compProduct->outlet_covered','$compProduct->samples_must_send','$compProduct->comments','$compProduct->orderid','$compProduct->latitude','$compProduct->longitude','$compProduct->location','$compProduct->mcc_mnc_lac_cellid','$compProduct->cur_date_time')";
//                echo $cmp_query;die;
                $cmp_query_run = mysqli_query($dbc, $cmp_query);
            }
        }

        #Complaint Report
        if (!empty($ComplaintReport)) {
            foreach ($ComplaintReport as $cr) {
                $cr_insert = "INSERT INTO `Complaint_report` (`complaint_product`, `natureOfComplaintMentioned`, `quantityLying`, `complaintWithRetailer`, `casesWithComplaint`, `casesRv`, `packersSlip`, `billNo`, `date`, `amountOfBill`, `productDispatched`, `manufacturingUnit`, `sampleClosed`, `concernedSuperDistributorAddress`, `concernedRetailerAddress`, `concernedConsumerAddress`, `actionTaken`, `comments`, `date_time`, `lat_lng`, `geo_address`, `mcc_mnc`, `unique_id`, `order_id`, `user_id`, `complaintByStr`, `agreeStr`, `complaintID`, `agreeID`, `created_at`) VALUES ('$cr->complaintP  roduct','$cr->natureOfComplaintMentioned','$cr->quantityLying','$cr->complaintWithRetailer','$cr->casesWithComplaint','$cr->casesRv','$cr->packersSlip','$cr->billNo','$cr->date','$cr->amountOfBill','$cr->productDispatched','$cr->manufacturingUnit','$cr->sampleClosed','$cr->concernedSuperDistributorAddress','$cr->concernedRetailerAddress','$cr->concernedConsumerAddress','$cr->actionTaken','$cr->comments','$cr->date_time','$cr->lat_lng','$cr->geo_address','$cr->mcc_mnc','$cr->unique_id','$cr->order_id','$cr->user_id','$cr->complaintByStr','$cr->agreeStr','$cr->complaintID','$cr->agreeID',CURRENT_TIMESTAMP)";

                $cr_insert_run = mysqli_query($dbc, $cr_insert);
            }
        }

        #Daily Prospect Working
        if (!empty($DailyProspectWorking)) {
            foreach ($DailyProspectWorking as $newd) {
                $dp_query = "INSERT INTO `daily_prospecting_working` (`user_id`,`town`,`district`,`state`,`party_name`, `party_address`, `phone_no`, `residence_phone`, `mobile_no`, `email_id`, `person_met_and_status`, `established_since`, `annual_turn_over`, `reputation_trade_relation`, `financial_position`, `level_ofinterst`, `from_time`, `to_time`, `units_availble_and_qty`, `gst_no`, `gst_registrtion_date`, `pan_card_no`, `pan_card_date`, `godown_size`, `no_of_employee`, `terms_condition`, `assured_investment`, `stockiest_from_filled`, `comments`, `orderid`, `latitude`, `longitude`, `mcc_mnc_lac_cellid`, `cur_date_time`) VALUES ('$newd->user_id','$newd->town','$newd->district','$newd->state,'$newd->party_name','$newd->party_address','$newd->phone_no','$newd->residence_phone','$newd->mobile_no','$newd->email_id','$newd->person_met_and_status','$newd->established_since','$newd->annual_turn_over','$newd->reputation_trade_relation','$newd->financial_position','$newd->level_ofinterst','$newd->from_time','$newd->to_time','$newd->units_availble_and_qty','$newd->gst_no','$newd->gst_registrtion_date','$newd->pan_card_no','$newd->pan_card_date','$newd->godown_size','$newd->no_of_employee','$newd->terms_condition','$newd->assured_investment','$newd->stockiest_from_filled','$newd->comments','$newd->orderid','$newd->latitude','$newd->longitude','$newd->mcc_mnc_lac_cellid','$newd->cur_date_time')";
                $dp_query_run = mysqli_query($dbc, $dp_query);
            }
        }

        #Payment Received Details
        if (!empty($paymentReceviedDetails)) {
            foreach ($paymentReceviedDetails as $k => $paymentData) {
                $addDealerPayment = "INSERT INTO dealer_payments( `user_id`,`dealer_id`, `zone_id`, `emp_id`, `user_designation`, `user_hq`, `town`,`payment_mode`, `invoice_number`, `amount`, `drawn_from_bank`,`deposited_bank`,`payment_recevied_date`,`deposited_date`,`lat`,`lng`,`geo_address`,`cur_datetime`,`unique_id`,`order_id`, `mcc_mnc`) "
                    . "VALUES('$paymentData->user_id','$paymentData->distributor_id', '$paymentData->zone_id', '$paymentData->emp_id', '$paymentData->user_designation', '$paymentData->user_hq', '$paymentData->town', '$paymentData->payment_mode_id','$paymentData->number','$paymentData->amount','$paymentData->drawn_from_bank','$paymentData->deposited_bank','$paymentData->payment_received_date','$paymentData->deposited_date','$paymentData->latitude','$paymentData->longitude','$paymentData->geo_address','$paymentData->cur_datetime','$paymentData->unique_id','$paymentData->order_id', '$paymentData->mcc_mnc')";
                $mob_qry_run = mysqli_query($dbc, $addDealerPayment);
            }
        }


//////////////////////////////////RETAILER DELETE/////////////////////////
        if (isset($RetailerDeleteStatus) && !empty($RetailerDeleteStatus)) {
            $RetailerDelete_count = count($RetailerDeleteStatus);
            $retdelc = 0;
            while ($retdelc < $RetailerDelete_count) {
                $retailer_id = $RetailerDeleteStatus[$retdelc]->c_code;
                $qryretdel = "UPDATE `retailer` SET `retailer_status`='0',`deactivated_by_user`='$user_id',`deactivated_date_time`=NOW() WHERE `id` = '$retailer_id'";
                $result = mysqli_query($dbc, $qryretdel);
                $retdelc++;
            }
        }


//////////////////////////////////LEAVE UPDATE/////////////////////////
        if (isset($leaveUpdate) && !empty($leaveUpdate)) {
            $leaveUpdate_count = count($leaveUpdate);
            $leavec = 0;
            while ($leavec < $leaveUpdate_count) {
                $userid = $leaveUpdate[$leavec]->user_id;
                $leaveid = $leaveUpdate[$leavec]->leave_id;
                $leave = $leaveUpdate[$leavec]->leave_value;

                $qryleave = "UPDATE `user_leave` SET `value`='$leave' WHERE `user_id` = '$userid' AND `leave_id`='$leaveid'";
                $result = mysqli_query($dbc, $qryleave);
                $leavec++;
            }
        }
////////////////////////////////////USER INFO//////////////////////////////
        if (isset($userInformation) && !empty($userInformation)) {
            //$userInformation_count = count($userInformation);
            $usi = 0;
            //while($usi<$TotalCounterSale_count){
            $userid = $userInformation[$usi]->user_id;
            $email = $userInformation[$usi]->new_email;
            $contact = $userInformation[$usi]->new_mobile;

            $qryuser = "UPDATE `person` SET `mobile`='$contact',`email`='$email' WHERE `id` = '$userid'";
            $result = mysqli_query($dbc, $qryuser);

            $person_log = "INSERT INTO `person_log` ( `user_id`, `mobile`, `updated by`) VALUES ('$userid', '$contact', 'app');";
            $person_log_result = mysqli_query($dbc, $person_log);
            $usi++;
            //}
        }
////////////////////////////ISR PRODUCT DETAILS///////////////////

        if (isset($ISRAttandance) && !empty($ISRAttandance)) {
            $ISRAttandance_count = count($ISRAttandance);
            $isrc = 0;
            while ($isrc < $ISRAttandance_count) {
                $Checkout = $ISRAttandance[$isrc]->Checkout;
                $isr_id = $ISRAttandance[$isrc]->isr_id;
                $Checkin = $ISRAttandance[$isrc]->Checkin;
                //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;

                if (isset($Checkin) && !empty($Checkin)) {

                    $order_id = date('YmdHis', strtotime($Checkin)) . $isr_id;
                    $new_work_date = date('Y-m-d', strtotime($Checkin));
                    $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from user_daily_attendance where user_id='" . $isr_id . "' AND "
                        . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                    $sql = mysqli_query($dbc, $q2);
                    $num = mysqli_num_rows($sql);
                    if ($num < 1) {


                        $q = "INSERT INTO `user_daily_attendance`(`user_id`,`order_id`,`work_date`,`server_date`)VALUES
					('$isr_id','$order_id','$Checkin',NOW())";
                        //echo $q;die;

                        $run = mysqli_query($dbc, $q);
                    }
                }

                if (isset($Checkout) && !empty($Checkout)) {

                    $order_id = date('YmdHis', strtotime($Checkout)) . $isr_id;
                    $new_work_date = date('Y-m-d', strtotime($Checkout));
                    $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where user_id='" . $isr_id . "' AND "
                        . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";

                    $sql = mysqli_query($dbc, $q2);
                    $num = mysqli_num_rows($sql);
                    if ($num < 1) {


                        $q = "INSERT INTO `check_out`(`user_id`,`order_id`,`work_date`,`server_date_time`)VALUES
					('$isr_id','$order_id','$Checkout',NOW())";

                        #update user_daily_attendance with checkout
                        $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$Checkout', `checkout_server_date_time` = NOW()
WHERE `user_daily_attendance`.`user_id` = '$isr_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($Checkout,'Y-m-d')";

                        $run = mysqli_query($dbc, $q);
                        $run_update = mysqli_query($dbc, $update_user_daily_attendance);
                    }
                }

                $isrc++;
            }

        }

        if (isset($JuniorCheckIn) && !empty($JuniorCheckIn)) {
            $JuniorCheckIn_count = count($JuniorCheckIn);
            $jci = 0;
            while ($jci < $JuniorCheckIn_count) {
                $junior_id = $JuniorCheckIn[$jci]->junior_id;
                $Date = $JuniorCheckIn[$jci]->Date;
                $remarks = $JuniorCheckIn[$jci]->remarks;
                //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;

                $order_id = date('YmdHis', strtotime($Date)) . $junior_id;
                $new_work_date = date('Y-m-d', strtotime($Date));
                $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from user_daily_attendance where user_id='" . $junior_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {


                    $q = "INSERT INTO `user_daily_attendance`(`user_id`,`order_id`,`work_date`,`remarks`,`server_date`)VALUES
					('$junior_id','$order_id','$Date','$remarks',NOW())";
                    //echo $q;die;

                    $run = mysqli_query($dbc, $q);
                }
                $jci++;
            }


        }

        if (isset($JuniorCheckOut) && !empty($JuniorCheckOut)) {
            $JuniorCheckOut_count = count($JuniorCheckOut);
            $jco = 0;
            while ($jco < $JuniorCheckIn_count) {
                $junior_id = $JuniorCheckOut[$jco]->junior_id;
                $Date = $JuniorCheckOut[$jco]->Date;
                $remarks = $JuniorCheckOut[$jco]->remarks;
                //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;

                $order_id = date('YmdHis', strtotime($Date)) . $junior_id;
                $new_work_date = date('Y-m-d', strtotime($Date));
                $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where user_id='" . $junior_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                //echo $q2;die;
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {


                    $q = "INSERT INTO `check_out`(`user_id`,`order_id`,`work_date`,`remarks`,`server_date_time`)VALUES
					('$junior_id','$order_id','$Date','$remarks',NOW())";
                    //echo $q;die;

                    #update user_daily_attendance with checkout
                    $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$Date', `checkout_server_date_time` = NOW(),
`remarks` = '$remarks'
WHERE `user_daily_attendance`.`user_id` = '$junior_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($Date,'Y-m-d')";

                    $run_update = mysqli_query($dbc, $update_user_daily_attendance);

                    $run = mysqli_query($dbc, $q);
                }
                $jco++;
            }


        }

////////////////////////////ISR SALE///////////////////////////////////////
        if (isset($TotalCounterSale) && !empty($TotalCounterSale)) {
            $TotalCounterSale_count = count($TotalCounterSale);
            $tcs = 0;
            while ($tcs < $TotalCounterSale_count) {
                $TotalSale = isset($TotalCounterSale[$tcs]->TotalSale) ? $TotalCounterSale[$tcs]->TotalSale : '';
                $valuefromnewoutlet = isset($TotalCounterSale[$tcs]->valuefromnewoutlet) ? $TotalCounterSale[$tcs]->valuefromnewoutlet : '';
                $Totalcall = isset($TotalCounterSale[$tcs]->Totalcall) ? $TotalCounterSale[$tcs]->Totalcall : '';
                $Date = isset($TotalCounterSale[$tcs]->Date) ? $TotalCounterSale[$tcs]->Date : '';
                $DistributorId = isset($TotalCounterSale[$tcs]->DistributorId) ? $TotalCounterSale[$tcs]->DistributorId : '';
                $Remarks = isset($TotalCounterSale[$tcs]->Remarks) ? $TotalCounterSale[$tcs]->Remarks : '';
                $BeatId = isset($TotalCounterSale[$tcs]->BeatId) ? $TotalCounterSale[$tcs]->BeatId : '';
                $Productivecall = isset($TotalCounterSale[$tcs]->Productivecall) ? $TotalCounterSale[$tcs]->Productivecall : '';
                $newoutlet = isset($TotalCounterSale[$tcs]->newoutlet) ? $TotalCounterSale[$tcs]->newoutlet : '';
                $Isrname = isset($TotalCounterSale[$tcs]->Isrname) ? $TotalCounterSale[$tcs]->Isrname : '';
                $isr_id = isset($TotalCounterSale[$tcs]->isr_id) ? $TotalCounterSale[$tcs]->isr_id : '';
                $order_id = $TotalCounterSale[$tcs]->order_id;

                $qry = "INSERT INTO `isr_total_sale_counter`(`id`,`order_id`, `Isrname`,`isr_id`, `TotalSale`, `valuefromnewoutlet`, `Totalcall`, `Date`, `DistributorId`, `Remarks`, `BeatId`, `Productivecall`, `newoutlet`) VALUES ('','$order_id','$Isrname','$isr_id','$TotalSale','$valuefromnewoutlet','$Totalcall','$Date','$DistributorId','$Remarks','$BeatId','$Productivecall','$newoutlet')";
                $result = mysqli_query($dbc, $qry);
                $tcs++;
            }
        }
////////////////////////////ISR SALE DETAILS////////////////////////////////
        if (isset($ISRSaleDetail) && !empty($ISRSaleDetail)) {
            $ISRSaleDetail_count = count($ISRSaleDetail);
            $isrc = 0;
            while ($isrc < $ISRSaleDetail_count) {
                $order_id = $ISRSaleDetail[$isrc]->order_id;
                $product_id = $ISRSaleDetail[$isrc]->product_id;
                $rate = $ISRSaleDetail[$isrc]->rate;
                $qty = $ISRSaleDetail[$isrc]->quantity;
                $pv = $ISRSaleDetail[$isrc]->product_value;
                $qry = "INSERT INTO `isr_product_details`(`order_id`, `product_id`,`rate`, `quantity`, `amount`) VALUES ('$order_id','$product_id','$rate','$qty','$pv')";

                $result = mysqli_query($dbc, $qry);
                //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;

                $isrc++;
            }
        }

        if (isset($RetailerSchemeStatus) && !empty($RetailerSchemeStatus)) {
            $RetailerScheme_count = count($RetailerSchemeStatus);
            $rescs = 0;
            while ($rescs < $RetailerScheme_count) {
                $order_id = $RetailerSchemeStatus[$rescs]->order_id;
                $retailer_id = $RetailerSchemeStatus[$rescs]->retailerId;
                $status = $RetailerSchemeStatus[$rescs]->status;
                $date = $RetailerSchemeStatus[$rescs]->date;
                $time = $RetailerSchemeStatus[$rescs]->time;

                $qo = "select * from retailer_scheme_status where order_id='" . $order_id . "'";
                //echo $q2;die;
                $sqlo = mysqli_query($dbc, $qo);
                $numo = mysqli_num_rows($sqlo);
                if ($numo < 1) {


                    $qry = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`) VALUES ('$order_id','$retailer_id','$status','1','$date','$time',NOW())";

                    $result = mysqli_query($dbc, $qry);
                    //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;
                }
                $rescs++;
            }
        }

        if (isset($RetailerSchemeStatusOtherFocusState) && !empty($RetailerSchemeStatusOtherFocusState)) {
            $RetailerSchemeStatusOtherFocusState_count = count($RetailerSchemeStatusOtherFocusState);
            $rescs1 = 0;
            while ($rescs1 < $RetailerSchemeStatusOtherFocusState_count) {
                $order_id = $RetailerSchemeStatusOtherFocusState[$rescs1]->order_id;
                $retailer_id = $RetailerSchemeStatusOtherFocusState[$rescs1]->retailerId;
                $status = $RetailerSchemeStatusOtherFocusState[$rescs1]->status;
                $date = $RetailerSchemeStatusOtherFocusState[$rescs1]->date;
                $time = $RetailerSchemeStatusOtherFocusState[$rescs1]->time;

                $qo1 = "select * from retailer_scheme_status where order_id='" . $order_id . "'";
                //echo $q2;die;
                $sqlo1 = mysqli_query($dbc, $qo1);
                $numo1 = mysqli_num_rows($sqlo1);
                if ($numo1 < 1) {


                    $qry1 = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`) VALUES ('$order_id','$retailer_id','$status','2','$date','$time',NOW())";

                    $result1 = mysqli_query($dbc, $qry1);
                    //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;
                }
                $rescs1++;
            }
        }
        if (isset($RetailerSchemeStatusDiscoveryoutlet) && !empty($RetailerSchemeStatusDiscoveryoutlet)) {
            $RetailerScheme_count2 = count($RetailerSchemeStatusDiscoveryoutlet);
            $rescs2 = 0;
            while ($rescs2 < $RetailerScheme_count2) {
                $order_id = $RetailerSchemeStatusDiscoveryoutlet[$rescs2]->order_id;
                $retailer_id = $RetailerSchemeStatusDiscoveryoutlet[$rescs2]->retailerId;
                $status = $RetailerSchemeStatusDiscoveryoutlet[$rescs2]->status;
                $date = $RetailerSchemeStatusDiscoveryoutlet[$rescs2]->date;
                $time = $RetailerSchemeStatusDiscoveryoutlet[$rescs2]->time;

                $qo2 = "select * from retailer_scheme_status where order_id='" . $order_id . "'";
                //echo $q2;die;
                $sqlo2 = mysqli_query($dbc, $qo2);
                $numo2 = mysqli_num_rows($sqlo2);
                if ($numo2 < 1) {


                    $qry2 = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`) VALUES ('$order_id','$retailer_id','$status','3','$date','$time',NOW())";

                    $result2 = mysqli_query($dbc, $qry2);
                    //$Checkin_time=$JuniorCheckIn[$jci]->Checkin;
                }
                $rescs2++;
            }
        }


        if (!empty($expense)) {
            // print_r($expense);
            $count_expense = count($expense);
            $ex = 0;

            while ($ex < $count_expense) {
                $totcalls = $expense[$ex]->total_calls;
                $start = $expense[$ex]->start_journey;
                $da = $expense[$ex]->drawing_allowance;
                $cr_time = $expense[$ex]->submit_time;
                $end = $expense[$ex]->end_journey;
                $ta = $expense[$ex]->travelling_allowance;
                $order_id = $expense[$ex]->orderid;
                $travelling_mode_id = $expense[$ex]->travelling_mode_id;
                $cr_date = $expense[$ex]->submit_date;
                $other_expense = $expense[$ex]->other_expense;
                $remarks = $expense[$ex]->remarks;
                $expense_date = $expense[$ex]->date;
                $rent = $expense[$ex]->hotel_rent;
                $q = "INSERT INTO `user_expense_report`(`total_calls`, `travelling_allowance`, `drawing_allowance`, `other_expense`, `travelling_mode_id`, `start_journey`, `end_journey`, `person_id`, `submit_date`, `submit_time`, `remarks`,`order_id`,`expense_date`,`rent`)"
                    . " VALUES ('$totcalls','$ta','$da','$other_expense','$travelling_mode_id','$start','$end','$user_id','$cr_date','$cr_time','$remarks','$order_id$user_id','$expense_date','$rent')";
                $result = mysqli_query($dbc, $q);
                $ex++;
            }

        }

        if (!empty($merchandise)) {
            // print_r($merchandise);
            $count_merchandise = count($merchandise);
            $me = 0;

            while ($me < $count_merchandise) {
                $mer_id = $merchandise[$me]->Merchandiseid;
                $mer_name = $merchandise[$me]->Merchandisename;
                $date = $merchandise[$me]->Date;
                $time = $merchandise[$me]->Time;
                $retailer = $merchandise[$me]->retailerid;
                $orderid = $merchandise[$me]->orderid;
                $qty = $merchandise[$me]->qty;
                $lat = $merchandise[$me]->lat;
                $lng = $merchandise[$me]->lngi;
                $address = $merchandise[$me]->adsress;
                $mcc_mnc = $merchandise[$me]->mcc_mnc_lac_cellid;

                $q = "INSERT INTO `merchandise`(`merchandise_id`, `merchandise_name`, `date`, `time`,`user_id`, `retailer_id`,`order_id`,`lat`,`lng`,`address`,`mcc_mnc`,`qty`,`server_date_time`) VALUES ('$mer_id','$mer_name','$date','$time','$user_id','$retailer','$orderid','$lat','$lng','$address','$mcc_mnc','$qty',NOW())";
                $result = mysqli_query($dbc, $q);
                $me++;
            }

        }

        if (!empty($merchandise_requirement)) {
            // print_r($merchandise);
            $count_merchandise_requrement = count($merchandise_requirement);
            $mer = 0;

            while ($mer < $count_merchandise_requrement) {
                $mer_id = $merchandise_requirement[$mer]->Merchandiseid;
                $mer_name = $merchandise_requirement[$mer]->Merchandisename;
                $date = $merchandise_requirement[$mer]->Date;
                $time = $merchandise_requirement[$mer]->Time;
                $retailer = $merchandise_requirement[$mer]->retailerid;
                $orderid = $merchandise_requirement[$mer]->orderid;
                $qty = $merchandise_requirement[$mer]->qty;
                $remarks = $merchandise_requirement[$mer]->remarks;

                $q = "INSERT INTO `merchandise_requirement`(`merchandise_id`, `merchandise_name`, `date`, `time`, `user_id`,`retailer_id`,`order_id`,`remarks`,`qty`,`server_date_time`) VALUES ('$mer_id','$mer_name','$date','$time','$user_id','$retailer','$orderid','$remarks','$qty',NOW())";
                $result = mysqli_query($dbc, $q);
                $mer++;
            }

        }


        if (!empty($retailerstock)) {
            // print_r($retailerstock);
            $count_retailerstock = count($retailerstock);
            $re = 0;

            while ($re < $count_retailerstock) {
                $order_id = $retailerstock[$re]->order_id;
                $dealer_id = $retailerstock[$re]->dealer_id;
                $location_id = $retailerstock[$re]->location_id;
                $retailer_id = $retailerstock[$re]->retailer_id;
                $date = $retailerstock[$re]->date;

                $q = "INSERT INTO `retailer_stock`(`order_id`,`user_id`, `dealer_id`, `location_id`, `date`,`retailer_id`) VALUES ('$order_id','$user_id','$dealer_id','$location_id','$date','$retailer_id')";
                $result = mysqli_query($dbc, $q);
//echo $q;
                $re++;
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

                $exp_date = strtotime(date('Y-m-d', strtotime($mfg_date)) . '-1 year');

                $qbr = "INSERT INTO `dealer_balance_stock`(`order_id`, `dealer_id`, `user_id`, `product_id`, `stock_qty`, `mfg_date`, `exp_date`, `cases`, `submit_date_time`, `server_date_time`,`mrp`,`pcs_mrp`,sstatus,`lat`,`lng`,`address`) VALUES ('$balance_order_id$user_id','$dealer_id','$user_id','$balance_product_code','$balance_pieces','$mfg_date','$exp_date','$balance_cases','$mobile_datetime',NOW(),'$bsmrp','$balance_pcs_mrp','0','$lat','$lng','$address')";
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
                // 	$stk_uq = "UPDATE `stock` SET `rate`='$retailer_rate',`dealer_rate`='$dealer_rate',`mrp`='$mrp',`person_id`='$user_id',`qty`='$balance_pieces',`salable_damage`='0',`nonsalable_damage`='0',`mfg`='$mfg_date',`date`=NOW(),`last_updated`=NOW(),`company_id`='1' WHERE dealer_id=$dealer_id AND product_id=$balance_product_code AND mrp=$mrp";
                // }else{
                // 	$stk_uq = "INSERT INTO `stock`(`product_id`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `date`,`last_updated`,`company_id`) VALUES ('$balance_product_code','$retailer_rate','$dealer_rate','$mrp','$user_id','$csa_id','$dealer_id',$balance_pieces,'0','0','0','$mfg_date',NOW(),NOW(),'1')";
                // }

                //mysqli_query($dbc,$stk_uq);
//echo $qbr;
                $bre++;
            }

        }

        if (!empty($retailerstockdetails)) {
            // print_r($retailerstock);
            $count_retailerstockdetails = count($retailerstockdetails);
            $j = 0;

            while ($j < $count_retailerstockdetails) {
                $order_id = $retailerstockdetails[$j]->order_id;
                $product_id = $retailerstockdetails[$j]->product_id;
                $qty = $retailerstockdetails[$j]->quantity;
                $stock_month = $retailerstockdetails[$j]->stock_month;


                $q = "INSERT INTO `retailer_stock_details`(`order_id`, `product_id`, `quantity`,`stock_month`) VALUES  ('$order_id','$product_id','$qty','$stock_month')";
                $result = mysqli_query($dbc, $q);
                $j++;
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
                $ll = explode(",", $latlng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }

                $new_work_date = date('Y-m-d', strtotime($date_time));
                $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from user_daily_attendance where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";

                                   
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {
                     
                    $q = "INSERT INTO `user_daily_attendance`(`user_id`, `order_id`, `work_date`,`working_with`, `work_status`,`mnc_mcc_lat_cellid`, `lat_lng`, `track_addrs`, `remarks`,`server_date`)VALUES
				('$user_id','$order_id$user_id','$date_time','$working_with','$status','$mnc_mcc_lat_cellid','$latlng','$user_location','$remark',CURRENT_TIMESTAMP)";
                    $run = mysqli_query($dbc, $q);
                }
                $k++;
            }
        }
        if (!empty($Checkoutlocation)) {
          //  print_r($Checkoutlocation); die;
            $checkcount = count($Checkoutlocation);
            $c = 0;
            while ($c < $checkcount) {
                $latlng = $Checkoutlocation[$c]->latlng;
                $mcc_mnc_lac_cellId_final = $Checkoutlocation[$c]->mcc_mnc_lac_cellId_final;
                $time = $Checkoutlocation[$c]->tim;
                $location = $Checkoutlocation[$c]->check_out;
                $date_time = $Checkoutlocation[$c]->date_time;
                $order_id = $Checkoutlocation[$c]->order_id;
                $ll = explode(",", $latlng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $new_work_date = date('Y-m-d', strtotime($date_time));
                $q2 = "SELECT *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {
             $q = "INSERT INTO check_out(`user_id`,`lat_lng`,`mnc_mcc_lat_cellid`,`work_date`,`server_date_time`,`attn_address`,`order_id`)
VALUES('$user_id','$latlng','$mcc_mnc_lac_cellId_final','$date_time','$current_date_time','$user_location','$order_id$user_id')";

                    #update user_daily_attendance with checkout
                    $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$date_time', `checkout_server_date_time` = '$current_date_time',
`checkout_remarks` = '$remarks', `checkout_lat_lng`= '$latlng', `checkout_mnc_mcc_lat_cellid`='$mcc_mnc_lac_cellId_final',`checkout_address`='$user_location'
WHERE `user_daily_attendance`.`user_id` = '$user_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($date_time,'Y-m-d')";

                    $run_update = mysqli_query($dbc, $update_user_daily_attendance);
                    $result = mysqli_query($dbc, $q);
                }
                $c++;
            }
        }
////////////////////////////////PAYMENT COLLECTION DEALER//////////////////////////////

        if (!empty($paymentCollectDealer)) {
            $paycount1 = count($paymentCollectDealer);
            $payd = 0;
            while ($payd < $paycount1) {
                $dealer = $paymentCollectDealer[$payd]->tdcode;
                $location = $paymentCollectDealer[$payd]->tlcode;
                $trcode = $paymentCollectDealer[$payd]->trcode;
                $mode = $paymentCollectDealer[$payd]->tpaymode;
                $anount = $paymentCollectDealer[$payd]->tamount2;
                $branch = $paymentCollectDealer[$payd]->tbbranch;
                $chequeno = $paymentCollectDealer[$payd]->tcheqno;
                $cheque_date = $paymentCollectDealer[$payd]->tcheqdate;
                $trans_no = $paymentCollectDealer[$payd]->transno;
                $trans_date = $paymentCollectDealer[$payd]->transdate;
                $ttime = $paymentCollectDealer[$payd]->ttime;
                //$retailer=$paymentCollectDealer[$payd]->retailer_id;
                $today = date("Y-m-d");
                // $user_id;
                $qpd = "INSERT INTO `payment_collect_dealer`(`dealer_id`, `tl_code`, `tr_code`, `payment_mode`, `amount`, `bank_branch`,
		 `cheque_no`, `cheque_date`, `trans_no`, `trans_date`, `payment_date`, `payment_time`, `user_id`) VALUES('$dealer',
		 '$location','$trcode','$mode','$anount','$branch','$chequeno','$cheque_date','$trans_no','$trans_date','$today','$ttime','$user_id')";
                $result_pd = mysqli_query($dbc, $qpd);

                $payd++;
            }
        }

////////////////////////////////PAYMENT COLLECTION RETAILER//////////////////////////////

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
                $q = "INSERT INTO `payment_collect_retailer`(`dealer_id`, `retailer_id`, `tl_code`, `tr_code`, `payment_mode`, `amount`, `bank_branch`,
		 `cheque_no`, `cheque_date`, `trans_no`, `trans_date`, `payment_date`, `payment_time`, `user_id`) VALUES('$dealer','$retailer',
		 '$location','$trcode','$mode','$anount','$branch','$chequeno','$cheque_date','$trans_no','$trans_date','$today','$ttime','$user_id')";
                $result = mysqli_query($dbc, $q);

                $pay++;
            }
        }


////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////getRetailerLocation UPDATE//////////////////////////////

        if (!empty($getRetailerLocation)) {
            $retcount = count($getRetailerLocation);
            $rl = 0;
            while ($rl < $retcount) {
                $lat = $getRetailerLocation[$rl]->new_lat;
                $long = $getRetailerLocation[$rl]->new_long;
                $retailer = $getRetailerLocation[$rl]->retailer_id;
                $contactperson = $getRetailerLocation[$rl]->contactperson;
                $email = $getRetailerLocation[$rl]->email;
                $contactno = $getRetailerLocation[$rl]->contactno;
                $mncmcclatcellid = $getRetailerLocation[$rl]->mncmcclatcellid;
                $lat_long = $lat . "," . $long;
                $qr = "UPDATE `retailer` SET `lat_long`='$lat_long',`email`='$email',`contact_per_name`='$contactperson',`landline`='$contactno',`mncmcclatcellid`='$mncmcclatcellid' WHERE `id`='$retailer'";
                // echo $qr;
                $result = mysqli_query($dbc, $qr);

                $rl++;
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

if(empty($product_id)){
    $product_id='0';
}else{
     $product_id = $callwisereportingstatus[$p]->product_id;
}
//$prod_qty
if(empty($prod_qty)){
   $prod_qty='0';
}else{
     $prod_qty = $callwisereportingstatus[$p]->quantity;
}
                $chk_usod = "SELECT id FROM user_sales_order_details WHERE order_id='$orderid$user_id' AND product_id='$product_id'";
                $run_uhod = mysqli_query($dbc, $chk_usod);
                $num_data = mysqli_num_rows($run_uhod);
                if ($num_data < 1) {
                  $qusod = "INSERT INTO `user_sales_order_details`(`order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`)"
                        . " VALUES('$orderid$user_id','$product_id','$rate','$prod_qty','$scheme_qty')";


                } else {
                   $qusod = "UPDATE `user_sales_order_details` SET `quantity`='$prod_qty',`rate`='$rate' WHERE order_id='$orderid$user_id' AND product_id='$product_id'";
                }

                $results = mysqli_query($dbc, $qusod);
                if ($results) {
                    $unique_id[] = $uid;

                }
                $p++;
            }
        }

        if (!empty($callwisereporting)) {
          //  print_r($callwisereporting);
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

                $discount = $callwisereporting[$l]->Discount;
                $total_sale_value = $callwisereporting[$l]->Finalvalue;
                $retailer_id = $callwisereporting[$l]->retailer_id;
                $call_status = $callwisereporting[$l]->call_status;
                $remarks = $callwisereporting[$l]->remarks;
                $geo_status = $callwisereporting[$l]->geo_status;
                $user_id=$callwisereporting[$l]->user_id;

                $ll = explode(",", $lat_lng);
                if ($location == "$$" || $location == ' ') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $chk_uso = "SELECT id FROM user_sales_order WHERE order_id='$orderid$user_id'";
                $run_uso = mysqli_query($dbc, $chk_uso);
                $num_uso = mysqli_num_rows($run_uso);
                if ($num_uso < 1) {
                    $quso = "INSERT INTO `user_sales_order`(`order_id`, `company_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`,`call_status`, `total_sale_value`,`discount`,`amount`, `total_sale_qty`,`lat_lng`, `mccmnclatcellid`,`geo_status`,`track_address`, `date`, `time`, `image_name`, `override_status`,`remarks`)"
                        . " VALUES('$order_id$user_id','1','$user_id','$dealer_id','$beat_id','$retailer_id','$call_status','$total_sale_value','$discount','$discount_before','$total_sale_qty','$lat_lng','$mcc_mnc','$geo_status','$user_location','$date','$time','.jpg','$override_status','$remarks')";
                    //      echo $q;
                }
                #Update archiv in MTP
                $cur_date=date('Y-m-d',strtotime('now'));
                $update_mtp="UPDATE monthly_tour_program SET arch=arch+$total_sale_value WHERE person_id=$user_id and working_date=$cur_date";
                $update_mtp_result = mysqli_query($dbc, $update_mtp);
                $result = mysqli_query($dbc, $quso);

                $l++;
            }
        }

        if (!empty($PrimarySaleSummary)) {
            //print_r($PrimarySaleSummary);
            $psale = count($PrimarySaleSummary);
            $ps = 0;
            while ($ps < $psale) {
                $order_id = $PrimarySaleSummary[$ps]->order_id;
                $product_id = $PrimarySaleSummary[$ps]->product_id;
                $rate = $PrimarySaleSummary[$ps]->pcs_rate;  //app side sending pcs_rate as pcs rate(dealer rate)
                $quantity = $PrimarySaleSummary[$ps]->quantity;
                $scheme_qty = $PrimarySaleSummary[$ps]->scheme_qty;
                $case = $PrimarySaleSummary[$ps]->case;
                $pcs = $PrimarySaleSummary[$ps]->pcs;
                $case_rate = $PrimarySaleSummary[$ps]->case_rate;
                $pcs_rate = $PrimarySaleSummary[$ps]->pcs_rate;

                $q = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`,`cases`,`pcs`,`pr_rate`) "
                    . " VALUES('$order_id$ps','$order_id$user_id','$product_id','$rate','$quantity','$scheme_qty','$case','$pcs','$case_rate')";
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
                //$ch_date=$Primarysaledetail[$pd]->ch_date;


                $q = "INSERT INTO `user_primary_sales_order`(`id`,`order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`,`lat`,`lng`,`address`)"
                    . " VALUES('$order_id$user_id','$order_id$user_id','$dealer_id','$created_date','$user_id','$sale_date',NOW(),'$date_time','$lat','$lng','$address')";
//                echo $q;die;
                $result = mysqli_query($dbc, $q);
                $pd++;
            }
        }
        if (!empty($Complaint)) {
            $comp = count($Complaint);
            $ct = 0;
            while ($ct < $comp) {
                $message = $Complaint[$ct]->message;
                $role_id = $Complaint[$ct]->role_id;
                $image_name = $Complaint[$ct]->image_name;
                $c_name = $Complaint[$ct]->name;
                $c_contact = $Complaint[$ct]->contact;
                $comp_type = $Complaint[$ct]->complaint_type;
                $dealer_retailer_id = $Complaint[$ct]->dealer_retailer_id;
                $image = $Complaint[$ct]->image;
                $feedback_form = $Complaint[$ct]->feedback_from;
                $date = $Complaint[$ct]->date;
                $order_id = $Complaint[$ct]->order_id;
                $date_time = $Complaint[$ct]->date_time;
                $q = "INSERT INTO user_complaint(`person_id`,`message`,`role_id`,`image_name`,`complaint_type`,`dealer_retailer_id`,`complaint_from`,`order_id`,`date_time`) VALUES('$user_id','$message','$role_id','$image_name','$comp_type','$dealer_retailer_id','$feedback_form','$order_id','$date_time')";
                $result = mysqli_query($dbc, $q);
                $q1 = "INSERT INTO complaint(`user_id`,`consumer_name`,`consumer_contact`,`complaint`,`role_id`,`image_name`,`complaint_type`,`dealer_retailer_id`,`action`,`complaint_id`,`date`) VALUES('$user_id','$c_name','$c_contact','$message','$role_id','$image_name','$comp_type','$dealer_retailer_id','0','$order_id','$date_time')";
                $result = mysqli_query($dbc, $q1);
                $ct++;
            }
        }

        if (!empty($createcustomer)) {
            $customer = count($createcustomer);
            $cc = 0;
            while ($cc < $customer) {
                $cr_time = $createcustomer[$cc]->cr_time;
                $d_code = $createcustomer[$cc]->d_code;
                $location = $createcustomer[$cc]->add_str;
                $full_address = $createcustomer[$cc]->full_address;
                $r_type = $createcustomer[$cc]->r_type;
                $long = $createcustomer[$cc]->long;
                $r_name = $createcustomer[$cc]->r_name;
                $id = $createcustomer[$cc]->id;
                $category = $createcustomer[$cc]->category;
                $l_code = $createcustomer[$cc]->l_code;
                $image_name = $createcustomer[$cc]->image_name;
                $mccmnclaccellid = $createcustomer[$cc]->mccmnclaccellid;
                $r_pin_no = $createcustomer[$cc]->r_pin_no;
                $r_email = $createcustomer[$cc]->r_email;
                $cr_date = $createcustomer[$cc]->cr_date;
                $r_tin = $createcustomer[$cc]->r_tin;
                $cont_name = $createcustomer[$cc]->cont_name;
                $r_contact_no = $createcustomer[$cc]->r_contact_no;
                $lat = $createcustomer[$cc]->lat;
                $seq_no = $createcustomer[$cc]->seq_no;
                $lat_lng = $lat . ',' . $long;
                $ll = explode(",", $lat_lng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $q = "INSERT INTO retailer(`id`,`created_by_person_id`,`dealer_id`,`address`,`track_address`,`outlet_type_id`,`lat_long`,`name`,`location_id`,`image_name`,`mncmcclatcellid`,`pin_no`,`email`,`created_on`,`tin_no`,`contact_per_name`,`landline`) " . "VALUES('$id','$user_id','$d_code','$user_location','$full_address','$r_type','$lat_lng','$r_name','$l_code','$image_name','$mccmnclaccellid','$r_pin_no','$r_email','$cr_date','$r_tin','$cont_name','$r_contact_no')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $q = "INSERT INTO user_dealer_retailer(`user_id`,`dealer_id`,`retailer_id`,`seq_id`) VALUES('$user_id','$d_code','$id',$seq_no)";
                    mysqli_query($dbc, $q);

                    function getSeniorList($user_id,$dbc,$j=0)
                    { global $dbc;

                        if ($user_id==1){return $_SESSION['idArr'];}
                        #role array
//                        $rol=[12,17,34,39,40,42,46];
//                        $in = join(',', array_fill(0, count($rol), '?'));
                        $queryx = "SELECT a.id as id,CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name) as name from person a INNER JOIN  person b ON a.id=b.person_id_senior WHERE b.id=$user_id and b.role_id IN (12,17,34,39,40,42,46)";
                        $qrx = mysqli_query($dbc, $queryx) or die(mysqli_error($dbc));
                        if (mysqli_num_rows($qrx) > 0) {
                            while ($rowx = mysqli_fetch_assoc($qrx)) {
                                if ($rowx['id']>1)
                                {
                                    $i=['id'=>$rowx['id'],'name'=>$rowx['name']];
                                    $_SESSION['idArr'][$j]=$i;
                                    $j++;

                                    getSeniorList($rowx['id'],$dbc,$j);
                                }
                            }
                        }

                        return $_SESSION['idArr'];
                    }
                    $ox1=getSeniorList($user_id,$dbc);
                    $custom_seniors=!empty($ox1)?$ox1:[];


                    if (!empty($custom_seniors))
                    {
                        foreach ($custom_seniors as $cs)
                        {
                            $q = "INSERT INTO user_dealer_retailer(`user_id`,`dealer_id`,`retailer_id`,`seq_id`) VALUES('$cs[id]','$d_code','$id',$seq_no)";
                            mysqli_query($dbc, $q);
                        }
                    }
                }
                $cc++;
            }
        }

        if (!empty($callwisereason)) {
            $callwise = count($callwisereason);
            $crr = 0;
            while ($crr < $callwise) {
                $reason_text = $callwisereason[$crr]->reason_text;
                $dealer_id = $callwisereason[$crr]->dealer_id;
                $location_id = $callwisereason[$crr]->location_id;
                $retailer_id = $callwisereason[$crr]->retailer_id;
                $order_id = $callwisereason[$crr]->order_id;
                $date = $callwisereason[$crr]->date;
                $time = $callwisereason[$crr]->time;
                $q = "INSERT INTO sale_reason_remarks(`user_id`,`retailer_id`,`order_id`,`sale_remarks`,`date`,`time`) VALUES('$user_id','$retailer_id','$order_id','$reason_text','$date','$time')";
                $result = mysqli_query($dbc, $q);
//                if($result)
//                {
//                $q1="INSERT INTO user_sales_order (`user_id`,`order_id`,`dealer_id`,`location_id`,`retailer_id`,`order_status`,`date`,`time`) VALUES ('$user_id','$order_id','$dealer_id','$location_id','$retailer_id','1','$date','$time')";
//                $result1=mysqli_query($dbc,$q1);
//                }

                $crr++;
            }
        }


        if (!empty($mtp)) {
            $mtp_con = count($mtp);
            $m = 0;
            while ($m < $mtp_con) {
                $working_date = $mtp[$m]->working_date;
                $dayname = $mtp[$m]->dayname;
                $working_status_id = $mtp[$m]->working_status_id;
                $dealer_id = $mtp[$m]->dealer_id;
                $locations = $mtp[$m]->locations;
                $town_id = $mtp[$m]->town_id;
                $total_calls = $mtp[$m]->total_calls;
                $total_sales = $mtp[$m]->total_sales;
                $ss_id = $mtp[$m]->ss_id;
                $travel_mode = $mtp[$m]->travel_mode;
                $from = $mtp[$m]->from;
                $to = $mtp[$m]->to;
                $travel_distance = $mtp[$m]->travel_distance;
                $category_wise = $mtp[$m]->category_wise;
                $mobile_save_date_time = $mtp[$m]->mobile_save_date_time;
                $pc = $mtp[$m]->pc;
                $rd = $mtp[$m]->rd;
                $collection = $mtp[$m]->collection;
                $primary_ord = $mtp[$m]->primary_ord;
                $new_outlet = $mtp[$m]->new_outlet;
                $any_other_task = $mtp[$m]->any_other_task;

                $q = "INSERT INTO `monthly_tour_program`(`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `mobile_save_date_time`,`upload_date_time`,`pc`,`rd`,`collection`,`primary_ord`,`new_outlet`,`any_other_task`)"
                    . " VALUES('$user_id','$working_date','$dayname','$working_status_id','$dealer_id','$town_id','$locations','$total_calls','$total_sales','$ss_id','$travel_mode','$from','$to','$travel_distance','$category_wise','$mobile_save_date_time',NOW(),'$pc','$rd','$collection','$primary_ord','$new_outlet','$any_other_task')";
                $result = mysqli_query($dbc, $q);
                $m++;
            }
        }

        if (!empty($tracking)) {
            $track = count($tracking);
            $tr = 0;
            while ($tr < $track) {
                $track_date = $tracking[$tr]->track_date;
                $track_time = $tracking[$tr]->track_time;
                $mnc_mcc_lat_cellid = $tracking[$tr]->mnc_mcc_lat_cellid;
                $lat_lng = $tracking[$tr]->lat_lng;
                $track_address = $tracking[$tr]->track_address;
                $ll = explode(",", $lat_lng);
                if ($track_address == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $track_address;
                }
                $q2 = "SELECT count(user_id) as num from user_daily_tracking where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(track_date,'%Y-%m-%d') ='" . $date . "' AND track_time= '" . $time . "'";
                $sql = mysqli_fetch_assoc(mysqli_query($dbc, $q2));
                $num = $sql['num'];
                if ($num < 1) {
                    $q = "INSERT INTO `user_daily_tracking`(`user_id`, `track_date`, `track_time`,`mnc_mcc_lat_cellid`, `lat_lng`, `track_address`)"
                        . " VALUES('$user_id','$track_date','$track_time','$mnc_mcc_lat_cellid','$lat_lng','$user_location')";
                    $result = mysqli_query($dbc, $q);
                }
                $tr++;
            }
        }


        if (!empty($damage_detail)) {
            // print_r($damage_detail);
            $damage_con = count($damage_detail);
            $dd = 0;
            while ($dd < $damage_con) {

                $replaceid = $damage_detail[$dd]->replaceid;
                $dis_code = $damage_detail[$dd]->dis_code;
                $prod_code = $damage_detail[$dd]->prod_code;
                $ret_code = $damage_detail[$dd]->ret_code;
                $prod_qty = $damage_detail[$dd]->prod_qty;
                $prod_value = $damage_detail[$dd]->prod_value;
                $date_time = $damage_detail[$dd]->date_time;
                $location = $damage_detail[$dd]->location;
                $reason = $damage_detail[$dd]->reason;
                $mrp = $damage_detail[$dd]->mrp;
                $task = $damage_detail[$dd]->task;
                $extra_amt = $damage_detail[$dd]->extra_amt;

                $q = "INSERT INTO `damage_replace`(`replaceid`, `user_id`, `dis_code`, `prod_code`, `ret_code`, `prod_qty`, `prod_value`, `date_time`, `location`, `reason`, `mrp`, `task`, `extra_amt`) "
                    . " VALUES('$replaceid','$user_id','$dis_code','$prod_code','$ret_code','$prod_qty','$prod_value','$date_time','$location','$reason','$mrp','$task','$extra_amt')";
                $result = mysqli_query($dbc, $q);
                $dd++;
            }
        }
        if (!empty($damage_detail_retailer)) {
            // print_r($damage_detail);
            $damage_con = count($damage_detail_retailer);
            $ddr = 0;
            while ($ddr < $damage_con) {

                $replaceid = $damage_detail_retailer[$ddr]->replaceid;
                $dis_code = $damage_detail_retailer[$ddr]->dis_code;
                $prod_code = $damage_detail_retailer[$ddr]->prod_code;
                $ret_code = $damage_detail_retailer[$ddr]->ret_code;
                $prod_qty = $damage_detail_retailer[$ddr]->prod_qty;
                $prod_value = $damage_detail_retailer[$ddr]->prod_value;
                $date_time = $damage_detail_retailer[$ddr]->date_time;
                $location = $damage_detail_retailer[$ddr]->location;
                $reason = $damage_detail_retailer[$ddr]->reason;
                $mrp = $damage_detail_retailer[$ddr]->mrp;
                $task = $damage_detail_retailer[$ddr]->task;
                $extra_amt = $damage_detail_retailer[$ddr]->extra_amt;

                $q = "INSERT INTO `damage_replace_retailer`(`replaceid`, `user_id`, `dis_code`, `prod_code`, `ret_code`, `prod_qty`, `prod_value`, `date_time`, `location`, `reason`, `mrp`, `task`, `extra_amt`) "
                    . " VALUES('$replaceid','$user_id','$dis_code','$prod_code','$ret_code','$prod_qty','$prod_value','$date_time','$location','$reason','$mrp','$task','$extra_amt')";
                $result = mysqli_query($dbc, $q);
                $ddr++;
            }
        }
//////////////////////////////////RETAILER MERGE/////////////////////////
        if (isset($RetailerMerge) && !empty($RetailerMerge)) {
            $RetailerMerge_count = count($RetailerMerge);
            $retmerc = 0;
            while ($retmerc < $RetailerMerge_count) {
                $retailer_merge_id = $RetailerMerge[$retmerc]->new_ret_id;
                $retailer_merge_id_old = $RetailerMerge[$retmerc]->old_ret_id;
                $retailer_merge_submit_date = $RetailerMerge[$retmerc]->submit_date;
                $retailer_merge_submit_time = $RetailerMerge[$retmerc]->submit_time;
                $qryretmer = "UPDATE `retailer` SET `retailer_status`='0',`deactivated_by_user`='$user_id',`deactivated_date_time`=NOW() WHERE `id` IN ($retailer_merge_id_old) AND id != $retailer_merge_id";
                $result_mer = mysqli_query($dbc, $qryretmer);
                $qryretmerge = "INSERT INTO `retailer_merge`( `new_ret_id`, `old_ret_id`, `submit_date`, `submit_time`, `server_date_time`) VALUES ('$retailer_merge_id','$retailer_merge_id_old','$retailer_merge_submit_date','$retailer_merge_submit_time',NOW())";
                $result_merge = mysqli_query($dbc, $qryretmerge);
                $retmerc++;
            }
        }

//////////////////////////////////RETAILER Reshuffle/////////////////////////
        if (isset($RetailerReshuffle) && !empty($RetailerReshuffle)) {
            $RetailerReshuffle_count = count($RetailerReshuffle);
            $retres = 0;
            while ($retres < $RetailerReshuffle_count) {
                $retailer_res_id = $RetailerReshuffle[$retres]->ret_id;
                $retailer_dealer_id = $RetailerReshuffle[$retres]->dealer_id;
                $retailer_res_seq_old = $RetailerReshuffle[$retres]->old_sequence;
                $retailer_res_seq_new = $RetailerReshuffle[$retres]->new_sequence;
                $retailer_merge_submit_date = $RetailerReshuffle[$retres]->date;
                $retailer_merge_submit_time = $RetailerReshuffle[$retres]->time;
                $qryretres = "UPDATE `user_dealer_retailer` SET `seq_id`='$retailer_res_seq_new',`udr_date_time`=NOW() WHERE user_id='$user_id' AND dealer_id='$retailer_dealer_id' AND retailer_id='$retailer_res_id'";
                $result_res = mysqli_query($dbc, $qryretres);
                $retres++;
            }
        }

        $mobile_dtls = $data->response->MobileDetails;
        if (!empty($mobile_dtls)) {
            $cur_date = date('Y-m-d H:i:s');
            $cd = 0;
            $count_dtls = count($mobile_dtls);
            while ($cd < $count_dtls) {
                $user_id = $user_id;
                $d_name = $mobile_dtls[$cd]->deviceName;
                $d_manu = $mobile_dtls[$cd]->deviceMan;
                $d_version = $mobile_dtls[$cd]->deviceAndroidVersion;

                $mob_qry = "INSERT INTO user_mobile_details( `user_id`, `device_name`, `device_manuf`, `device_version`, `server_date_time`) "
                    . "VALUES('$user_id','$d_name','$d_manu','$d_version','$cur_date')";
                $mob_qry_run = mysqli_query($dbc, $mob_qry);
                $cd++;
            }
        }

    } else {
        // echo 'N';
    }

//    if (!empty($user_id)) {
//        $day=date('d',strtotime('now'));
//        $my_query = "SELECT SUM(rd) as total_rd
//FROM monthly_tour_program
//WHERE MONTH(`working_date`) = MONTH(CURRENT_DATE())
//AND YEAR(`working_date`) = YEAR(CURRENT_DATE())
//AND DAY(`working_date`)>='1' AND DAY(`working_date`)<='$day' AND person_id='$user_id'";
//
//        $ach = "SELECT SUM(arch) as total_achievement
//FROM monthly_tour_program
//WHERE MONTH(`working_date`) = MONTH(CURRENT_DATE())
//AND YEAR(`working_date`) = YEAR(CURRENT_DATE())
//AND DAY(`working_date`)>='1' AND DAY(`working_date`)<='$day' AND person_id='$user_id'";
//        $query_run = mysqli_query($dbc, $my_query);
//        $ach_run = mysqli_query($dbc, $ach);
//        $fetch = mysqli_fetch_assoc($query_run);
//        $fetch2 = mysqli_fetch_assoc($ach_run);
//        $percentage_ratio=0;
//        if (!empty($fetch2['total_achievement']) && !empty($fetch['total_rd']))
//        {
//            $percentage_ratio=($fetch2['total_achievement']/$fetch['total_rd'])*100;
//        }
//        $d=[];
//        $d=array("total_rd"=>!empty($fetch['total_rd'])?$fetch['total_rd']:0,
//            'total_achievement'=>!empty($fetch2['total_achievement'])?$fetch2['total_achievement']:0,
//            'percentage_ratio'=>$percentage_ratio);
//
//    }

    ob_start();
    ob_clean();
    //$uniqueId = implode(',', $unique_id);
    $uniqueId="'". implode("','", $unique_id) ."'";
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
