<?php
// echo 'php';
// echo 'php'; die;
// 
date_default_timezone_set('Asia/Calcutta');
$current_date_time = date("Y-m-d H:i:s");
require_once('../admin/include/conectdb.php');
//$dbc = @mysqli_connect('localhost','root','Dcatch','msell-dsgroup-dms') OR die ('could not connect:');
require_once('../admin/include/config.inc.php');
require_once 'functions.php';
$unique_id = array();
// if (isset($_POST['response'])) {
//     $check = $_POST['response'];
// } else {
//     $check = '';

// }
// $check = str_replace("'", "", $check);
// $data = json_decode($check);
 #Added By Deepak At 28-01-2019
 #Removing special character from post JSON 

// if(isset($_POST['response']))
// {
// $checkRes  = str_replace("'","",$_POST['response']);
// $str = str_replace('\\', ' ', $checkRes);
// // $checkRes  = str_replace("'","",$_POST['response']);
// } 
// else $str='';

// $check=cleanSpecialChar($str);

// #This method used for removing special charactor
// function cleanSpecialChar($string) 
// {
//    return preg_replace('/[^A-Za-z0-9\s:"{}/\,[]]/', '', $string); // Removes special chars.
// }

// $utf8 = utf8_encode($check);
// $data = json_decode($utf8);
// pre($data);die;
#----------------------------Phase -2 -------------------------------#

if(isset($_POST['response']))
{
$checkRes  = str_replace("'","",$_POST['response']);
$str_test= str_replace('\"', '|', $checkRes);
$str = str_replace('\\', '|', $str_test);
// $str = str_replace('\\', '|', $checkRes);
} 
else $str='';
$check=cleanSpecialChar($str);

#This method used for removing special charactor
function cleanSpecialChar($string) 
{
   return preg_replace('/[^A-Za-z0-9\s:\/"{},[]]/', '', $string); // Removes special chars.
}
 // echo $check; die;
$utf8 = utf8_encode($check);

$data = json_decode($utf8);
#----------------------------Phase -2 END HERE-------------------------------#




if($data) 
{
// print_r($data); die;

    $user_id = $data->response->user_id;
    $company_id = $data->response->company_id;
    $company_id_new = $data->response->company_id;

    // $cdate = date('Y-m-d');
    // $ctime = date('H:i:s');

    // $insertJsonQuery = "INSERT into dump_json (`user_id`,`company_id`,`date`,`time`,`json`) VALUES ('$user_id','$company_id','$cdate','$ctime','$utf8')";
    // $insertRun = mysqli_query($dbc,$insertJsonQuery);

    //  ========================= GENERATE SIGN DATA   ==============================
    // $contents = file_get_contents('signin/'.$user_id.'.php');
    // $contents .= '|||'.$utf8;

    // $updatedContent =  utf8_encode(cleanSpecialChar($contents));

    // $file = fopen("signin/".$user_id.".php","w");

    // fwrite($file,$updatedContent);
    // fclose($file);
    // chmod("signin/".$user_id.".php",777);
//=================================================================================
    



     $q = "SELECT * From person_login WHERE person_id='$user_id' AND `company_id` = '$company_id'";

    $user_res = mysqli_query($dbc, $q);
    $q_person = mysqli_fetch_assoc($user_res);
    // print_r($q_person);
    $person_id = $q_person['person_id'];
    $status = $q_person['person_status'];

    mysqli_query($dbc, "update person_login SET last_mobile_access_on=NOW(), app_type='SFA' Where person_id='$person_id'");
    if ($status == '1') {

        // print_r($status);
        $unique_id_array = array();
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
        $damage_detail_retailer = $data->response->damage_stock_retailer; // some key are not added because rupak sir said that stop don't add 
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
        $dailyReporting = $data->response->dailyReporting; // dialy reporting array added by karan
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
        $CreateDealer = $data->response->CreateDealer;
        $Visit = $data->response->Visit;
        $retailerComment = $data->response->retailerComment;
        $generalTradeMeetings = $data->response->generalTradeMeetings;
        $modernTradeMeeting = $data->response->modernTradeMeeting;
        $CloseOfTheDay = $data->response->CloseOfTheDay;
        $daily_comments = $data->response->daily_comments;
        $MarketReport1 = $data->response->MarketReport1;
        $MarketReport2 = $data->response->MarketReport2;
        $MeetingOrderBooking = $data->response->MeetingOrderBooking;
        $CounterSaleSummary = $data->response->CounterSaleSummary;
        $CounterSaleDetail = $data->response->CounterSaleDetail;
        $MarketingActivity = $data->response->MarketingActivity;
        
        if(!empty($MarketingActivity))
        {
            $marketActArray = array();
            foreach ($MarketingActivity as $mar_key => $mar_value) 
            {
                $activity = $mar_value->activity;                                                                    
                $plan_type = $mar_value->plan_type;                                                                 
                $total_count = $mar_value->total_count;                                                             
                $venue = $mar_value->venue;                                                                    
                $budget = $mar_value->budget;                                                                  
                $gift_budget = $mar_value->gift_budget;                                                        
                $gift_stock = $mar_value->gift_stock;                                                                    
                $name_of_gift = $mar_value->name_of_gift;                                                                    
                $date = $mar_value->date;                                                                    
                $latitude = $mar_value->latitude;                                                            
                $longitude = $mar_value->longitude;                                                          
                $mcc_mnc_lac_cellid = $mar_value->mcc_mnc_lac_cellid;                                        
                $cur_date = $mar_value->cur_date;                                                            
                $cur_time = $mar_value->cur_time;                                                            
                $location = $mar_value->location;                                                            
                $battery_status = $mar_value->battery_status;                                                
                $gps_status = $mar_value->gps_status;                                                        
                $requirement = $mar_value->requirement;                                                                    
                $branding_budget = $mar_value->branding_budget;                                                                    
                $orderid = $mar_value->orderid;            
                $latlng = $latitude.','.$longitude;


                $marketing_order_data = "INSERT INTO `market_activity`(`company_id`, `activity`, `plan_type`, `total_count`, `venue`, `budget`, `gift_budget`, `gift_stock`, `name_of_gift`, `date`, `lat`, `lng`, `mcc_mnc_lac_cellid`, `cur_date`, `cur_time`, `location`, `battery_status`, `gps_status`, `branding_budget`, `order_id`, `server_date_time`, `created_by`) VALUES ('$company_id','$activity','$plan_type','$total_count','$venue','$budget','$gift_budget','$gift_stock','$name_of_gift','$date','$latitude','$longitude','$mcc_mnc_lac_cellid','$cur_date','$cur_time','$location','$battery_status','$gps_status','$branding_budget','$orderid',NOW(),'$user_id')";
                $marketing_order_data_run = mysqli_query($dbc, $marketing_order_data);

                $marketActArray[] = "('$user_id','$cur_date','$cur_time','$mcc_mnc_lac_cellid','$latlng','$geoaddress','Marketing Activity',NOW(),'$battery_status','$gps_status','$company_id')";


                

                if ($marketing_order_data_run) {
                    $unique_id_array[] = $orderid;

                }
            }

            $marketActArrayImp = implode(',',$marketActArray);

            $marketing__activitity_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES $marketActArrayImp ";
                $marketing__activitity_track_run = mysqli_query($dbc, $marketing__activitity_track);
        }


        if(!empty($MeetingOrderBooking))
        {
            foreach ($MeetingOrderBooking as $m_key => $m_value) 
            {
                $meeting_id = $m_value->meeting_id;
                $meeting_with = $m_value->meeting_with;
                $meet_address = $m_value->meet_address;
                $meet_name = $m_value->meet_name;
                $type_of_meet = $m_value->type_of_meet;
                $time_in = $m_value->time_in;
                $time_out = $m_value->time_out;
                $meeting_remark = $m_value->meeting_remark;
                $followup_date = $m_value->followup_date;
                $contact_no = $m_value->contact_no;
                $current_date = $m_value->current_date;
                $current_datetime = $m_value->current_datetime;
                $time = date('H:i:s', strtotime($current_datetime));
                $followup_time = $m_value->followup_time;
                $mcc_mnc_cellId = $m_value->mcc_mnc_cellId;
                $lat = $m_value->lat;
                $lng = $m_value->lng;
                $latlng = $lat.','.$lng;
                $geoaddress = $m_value->geoaddress;
                $battery_status = $m_value->battery_status;
                $gps_status = $m_value->gps_status;
                $order_id = $m_value->order_id;
                $created_at = date('Y-m-d H:i:s');
                // $time = date('H:i:s');
                $status = 1;
                

                $metting_order_data = "INSERT INTO `meeting_order_booking`(`meeting_id`, `meeting_with`, `meet_address`, `meet_name`, `type_of_meet`, `time_in`, `time_out`, `meeting_remark`, `followup_date`, `contact_no`, `current_date_m`, `current_datetime`, `followup_time`, `mcc_mnc_cellId`, `lat`, `lng`, `geoaddress`, `user_id`, `created_at`, `status`, `battery_status`, `gps_status`,`company_id`,`order_id`) VALUES ('$meeting_id', '$meeting_with', '$meet_address', '$meet_name', '$type_of_meet', '$time_in', '$time_out', '$meeting_remark', '$followup_date', '$contact_no', '$current_date', '$current_datetime', '$followup_time', '$mcc_mnc_cellId', '$lat', '$lng', '$geoaddress', '$user_id', '$created_at', '$status', '$battery_status', '$gps_status','$company_id','$order_id')";
                $metting_order_data_run = mysqli_query($dbc, $metting_order_data);

                $market_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$current_date','$time','$mcc_mnc_cellId','$latlng','$geoaddress','MeetingOrderBooking',NOW(),'$battery_status','$gps_status','$company_id')";
                $market_track_run = mysqli_query($dbc, $market_track);

                if ($metting_order_data_run) {
                    $unique_id_array[] = $order_id;

                }
            }
        }
        if(!empty($CounterSaleDetail))
        {
            foreach ($CounterSaleDetail as $key => $c_value_s) 
            {
                $order_id = $c_value_s->order_id;
                $dealer_id = $c_value_s->dealer_id;
                $sale_date = $c_value_s->sale_date;
                $created_date = $c_value_s->created_date;
                $date_time = $c_value_s->date_time;
                $battery_status = $c_value_s->battery_status;
                $gps_status = $c_value_s->gps_status;
                $lat = $c_value_s->lat;
                $lng = $c_value_s->lng;
                $latlng = $lat.','.$lng;
                // $time = date('H:i:s');
                $time = date('H:i:s', strtotime($date_time));

                $mcc_mnc_lac_cellid = $c_value_s->mcc_mnc_lac_cellid;
                $address = $c_value_s->address;

                $counter_sale_data = "INSERT INTO `counter_sale_summary`(`order_id`, `dealer_id`, `sale_date`, `created_date`, `date_time`, `battery_status`, `gps_status`, `lat`, `lng`, `mcc_mnc_lac_cellid`, `company_id`, `address`, `created_by_person`,`server_date`) VALUES ('$order_id','$dealer_id','$sale_date','$created_date','$date_time','$battery_status','$gps_status','$lat','$lng','$mcc_mnc_lac_cellid','$company_id','$address','$user_id',NOW())";
                $counter_sale_data_run = mysqli_query($dbc, $counter_sale_data);

                $market_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$sale_date','$time','$mcc_mnc_lac_cellid','$latlng','$address','Counter Sale',NOW(),'$battery_status','$gps_status','$company_id')";
                $market_track_run = mysqli_query($dbc, $market_track);

                if ($counter_sale_data_run) {
                    $unique_id_array[] = $order_id;

                }
            }
        }
        if(!empty($CounterSaleSummary))
        {
            foreach ($CounterSaleSummary as $counter_key => $counter_value) 
            {
                $order_id = $counter_value->order_id;
                $product_id = $counter_value->product_id;
                $rate = $counter_value->rate;
                $quantity = $counter_value->quantity;
                $Barcode = $counter_value->Barcode;
                $case = $counter_value->case;
                $pcs = $counter_value->pcs;
                $value = $counter_value->value;
                $case_rate = $counter_value->case_rate;
                $pcs_rate = $counter_value->pcs_rate;
                $secondary_qty = !empty($counter_value->secondary_qty)?$counter_value->secondary_qty:'0';

                $counter_details_sale_data = "INSERT INTO `counter_sale_details`(`order_id`, `product_id`, `rate`, `quantity`, `barcode`, `cases`, `pcs`, `value`, `case_rate`, `pcs_rate`, `company_id`,`created_by`,`server_date_time`,`secondary_qty`) VALUES ('$order_id','$product_id','$rate','$quantity','$barcode','$case','$pcs','$value','$case_rate','$pcs_rate','$company_id','$user_id',NOW(),'$secondary_qty')";
                $counter_details_sale_data_run = mysqli_query($dbc, $counter_details_sale_data);

              

                if ($counter_details_sale_data_run) {
                    $unique_id_array[] = $order_id;

                }
            }
        }
        if(!empty($MarketReport2))
        {
            foreach($MarketReport2 as $m2_key => $m2_value)
            {
                $outlet_visited_today  = $m2_value->outlet_visited_today;
                $outlet_added_today  = $m2_value->outlet_added_today;
                $distributor_visited_today  = $m2_value->distributor_visited_today;
                $time  = $m2_value->time;
                $date  = $m2_value->date;
                $lat  = $m2_value->lat;
                $lng  = $m2_value->lng;
                $check_mcc_mnc_lac_cellId = $m2_value->check_mcc_mnc_lac_cellId;
                $orderid = $m2_value->orderid;
                $location = $m2_value->location;
                $latlng = $lat.','.$lng;
                $gps_status = $m2_value->gps_status;
                $battery_status = $m2_value->battery_status;

                $MarketReport2Query = "INSERT INTO `market_report_2`(`outlet_visited_today`, `outlet_added_today`, `distributor_visited_today`, `time`, `date`, `lat`, `lng`, `check_mcc_mnc_lac_cellId`, `orderid`, `location`, `company_id`, `created_at`, `created_by`) VALUES ('$outlet_visited_today','$outlet_added_today','$distributor_visited_today','$time','$date','$lat','$lng','$check_mcc_mnc_lac_cellId','$orderid','$location','$company_id',NOW(),'$user_id')";
                $MarketReport2Run = mysqli_query($dbc, $MarketReport2Query);

                // $rd = mysqli_query($dbc, $qd);

                $market_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$check_mcc_mnc_lac_cellId','$latlng','$location','Market Report 2',NOW(),'$battery_status','$gps_status','$company_id')";
                $market_track_run = mysqli_query($dbc, $market_track);

                if ($MarketReport2Run) {
                    $unique_id_array[] = $orderid;

                }
            }
        }

        # market report 1 
        if(!empty($MarketReport1))
        {
            foreach($MarketReport1 as $m1_key => $m1_value)
            {
                $range_selling = $m1_value->range_selling;
                $retailer_issue_resolution = $m1_value->retailer_issue_resolution;
                $merchandising = $m1_value->merchandising;
                $time = $m1_value->time;
                $date = $m1_value->date;
                $lat = $m1_value->lat;
                $lng = $m1_value->lng;
                $check_mcc_mnc_lac_cellId = $m1_value->check_mcc_mnc_lac_cellId;
                $orderid = $m1_value->orderid;
                $location = $m1_value->location;
                $latlng = $lat.','.$lng;
                $gps_status = $m1_value->gps_status;
                $battery_status = $m1_value->battery_status;
                $MarketReport1Query = "INSERT INTO `market_report_1`(`range_selling`, `retailer_issue_resolution`, `merchandising`, `time`, `date`, `lat`, `lng`, `check_mcc_mnc_lac_cellId`, `orderid`, `location`, `company_id`, `created_at`, `created_by`) VALUES ('$range_selling','$retailer_issue_resolution','$merchandising','$time','$date','$lat','$lng','$check_mcc_mnc_lac_cellId','$orderid','$location','$company_id',NOW(),'$user_id')";
                $MarketReport1Run = mysqli_query($dbc, $MarketReport1Query);

                $market2_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$check_mcc_mnc_lac_cellId','$latlng','$location','Market Report 1',NOW(),'$battery_status','$gps_status','$company_id')";
                $market2_track_run = mysqli_query($dbc, $market2_track);

                if ($MarketReport1Run) {
                    $unique_id_array[] = $orderid;

                }
            }

        }
        #daily_comments
        if(!empty($daily_comments))
        {
            foreach($daily_comments as $d_key => $d_value)
            {
                $imei = $d_value->imei;
                $date_time = $d_value->date_time;
                $orderid = $d_value->orderid;
                $gate_meeting_summary = $d_value->gate_meeting_summary;
                $new_ideas = $d_value->new_ideas;
                $ss_comments = $d_value->ss_comments;
                $distributor_comments = $d_value->distributor_comments;
                $market_comments = $d_value->market_comments;
                $damage_comments = $d_value->damage_comments;
                $competitors_comments = $d_value->competitors_comments;
                $pjp_follow = $d_value->pjp_follow;
                $outlet_cover = $d_value->outlet_cover;
                $sale_outlet = $d_values->sale_outlet;

                 $daily_comments_query = "INSERT INTO `daily_comments`(`imei`, `date_time`, `orderid`, `gate_meeting_summary`, `new_ideas`, `ss_comments`, `distributor_comments`, `market_comments`, `damage_comments`, `competitors_comments`, `pjp_follow`, `outlet_cover`, `sale_outlet`, `company_id`, `created_at`,`created_by`) VALUES ('$imei','$date_time','$orderid','$gate_meeting_summary','$new_ideas','$ss_comments','$distributor_comments','$market_comments','$damage_comments','$competitors_comments','$pjp_follow','$outlet_cover','$sale_outlet','$company_id', NOW(),'$user_id')";
                $daily_comments_run = mysqli_query($dbc, $daily_comments_query);

                if ($daily_comments_run) {
                    $unique_id_array[] = $orderid;

                }

            }
        }

        #CloseOfTheDay 
        if(!empty($CloseOfTheDay))
        {
            foreach($CloseOfTheDay as $c_key => $c_value)
            {
                $new_outlets_opened_today = $c_value->new_outlets_opened_today;
                $remarks_on_NPD_sales = $c_value->remarks_on_NPD_sales;
                $remarks_on_competitor_activity = $c_value->remarks_on_competitor_activity;
                $any_suggestion_to_the_company = $c_value->any_suggestion_to_the_company;
                $check_in_lat = $c_value->check_in_lat;
                $check_in_long = $c_value->check_in_long;
                $currentDate = $c_value->currentDate;
                $currentTime = $c_value->currentTime;
                $check_mcc_mnc_lac_cellId = $c_value->check_mcc_mnc_lac_cellId;
                $location = $c_value->location;
                $unique_id = $c_value->unique_id; 

                $close_day_query = "INSERT INTO `close_of_the_day`(`new_outlets_opened_today`, `remarks_on_NPD_sales`, `remarks_on_competitor_activity`, `any_suggestion_to_the_company`, `check_in_lat`, `check_in_long`, `currentDate`, `currentTime`, `check_mcc_mnc_lac_cellId`, `location`, `company_id`, `unique_id`, `created_at`,`created_by`) VALUES ('$new_outlets_opened_today','$remarks_on_NPD_sales','$remarks_on_competitor_activity','$any_suggestion_to_the_company','$check_in_lat','$check_in_long','$currentDate','$currentTime','$check_mcc_mnc_lac_cellId','$location','$company_id','$unique_id',NOW(),'$user_id')";

                $close_day_run = mysqli_query($dbc, $close_day_query);

                 if ($close_day_run) {
                    $unique_id_array[] = $unique_id;

                }

            }
        }
                // print_r($unique_id_array);

        #modernTradeMeeting
        if(!empty($modernTradeMeeting))
        {
            foreach($modernTradeMeeting as $mm_key => $mm_value)
            {
                $start_time = $mm_value->start_time;
                $duration = $mm_value->duration;
                $isr_name = $mm_value->isr_name;
                $so_name= $mm_value->so_name;
                $asm_name = $mm_value->asm_name;
                $rsm_name = $mm_value->rsm_name;
                $sales_head = $mm_value->sales_head;
                $so_target = $mm_value->so_target;
                $is_sales_performance= $mm_value->is_sales_performance;
                $is_primary_secondary_scheme = $mm_value->is_primary_secondary_scheme;
                $is_stock_shortage= $mm_value->is_stock_shortage;
                $so_one_travelling= $mm_value->so_one_travelling;
                $so_two_travelling = $mm_value->so_two_travelling;
                $latitude = $mm_value->latitude;
                $longitude = $mm_value->longitude;
                $geo_address = $mm_value->geo_address;
                $mcc_mnc = $mm_value->mcc_mnc;
                $unique_id = $mm_value->unique_id;
                $user_role = $mm_value->user_role;
                $cur_date = $mm_value->cur_date;
                $cur_time = $mm_value->cur_time;
                $image = $mm_value->image;
                $latlng = $latitude.','.$longitude;
                $gps_status = $mm_value->gps_status;
                $battery_status = $mm_value->battery_status;


             $morder_trade_meeting_query = "INSERT INTO `mordern_trade_meeting`(`start_time`, `duration`, `isr_name`, `so_name`, `asm_name`, `rsm_name`, `sales_head`, `so_target`, `is_sales_performance`, `is_primary_secondary_scheme`, `is_stock_shortage`, `so_one_travelling`, `so_two_travelling`, `latitude`, `longitude`, `geo_address`, `mcc_mnc`, `unique_id`, `user_id`, `user_role`, `cur_date`, `cur_time`, `image`, `server_date_time`,`company_id`) VALUES('$start_time','$duration','$isr_name','$so_name','$asm_name','$rsm_name','$sales_head','$so_target','$is_sales_performance','$is_primary_secondary_scheme','$is_stock_shortage','$so_one_travelling','$so_two_travelling','$latitude','$longitude','$geo_address','$mcc_mnc','$unique_id','$user_id','$user_role','$cur_date','$cur_time','$image',NOW(),'$company_id')";
                $morder_trade_meeting_run = mysqli_query($dbc, $morder_trade_meeting_query);

                $modern_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$cur_date','$cur_time','$mcc_mnc','$latlng','$geo_address','Modern Trade',NOW(),'$battery_status','$gps_status','$company_id')";
                $modern_track_run = mysqli_query($dbc, $modern_track);
      
                if ($morder_trade_meeting_run) {
                    $unique_id_array[] = $unique_id;

                }
            }
        }

          #retailer comment 
          if(!empty($generalTradeMeetings))
          {
              foreach ($generalTradeMeetings as $gtm_key => $gtm_value) 
              {
                  $start_time = $gtm_value->start_time;
                  $duration = $gtm_value->duration;
                  $dsm_name = $gtm_value->dsm_name;
                  $so_name = $gtm_value->so_name;
                  $asm_name = $gtm_value->asm_name;
                  $rsm_name = $gtm_value->rsm_name;
                  $sm_agm_name = $gtm_value->sm_agm_name;
                  $sales_head = $gtm_value->sales_head;
                  $segment_wise_target = $gtm_value->segment_wise_target;
                  $first_product = $gtm_value->first_product;
                  $second_product = $gtm_value->second_product;
                  $third_product = $gtm_value->third_product;
                  $is_a_class_outlet = $gtm_value->is_a_class_outlet;
                  $is_sales_performance = $gtm_value->is_sales_performance;
                  $is_primary_secondary_scheme = $gtm_value->is_primary_secondary_scheme;
                  $is_stock_shortage = $gtm_value->is_stock_shortage;
                  $is_retailer_replacement = $gtm_value->is_retailer_replacement;
                  $so_one_travelling = $gtm_value->so_one_travelling;
                  $so_two_travelling = $gtm_value->so_two_travelling;
                  $latitude = $gtm_value->latitude;
                  $longitude = $gtm_value->longitude;
                  $geo_address = $gtm_value->geo_address;
                  $mcc_mnc = $gtm_value->mcc_mnc;
                  $unique_id = $gtm_value->unique_id;
                  $user_id = $gtm_value->user_id;
                  $user_role = $gtm_value->user_role;
                  $cur_date = $gtm_value->cur_date;
                  $cur_time = $gtm_value->cur_time;
                  $image = $gtm_value->image;

                $latlng = $latitude.','.$longitude;
                $gps_status = $gtm_value->gps_status;
                $battery_status = $gtm_value->battery_status;

                  $comment_query = "INSERT INTO `general_trade_meeting`(`start_time`, `duration`, `dsm_name`, `so_name`, `asm_name`, `rsm_name`, `sm_agm_name`, `sales_head`, `segment_wise_target`, `first_product`, `second_product`, `third_product`, `is_a_class_outlet`, `is_sales_performance`, `is_primary_secondary_scheme`,`is_stock_shortage`,`is_retailer_replacement`,`so_one_travelling`,`so_two_travelling`,`latitude`,`longitude`,`geo_address`,`mcc_mnc`,`unique_id`,`user_id`,`user_role`,`cur_date`,`cur_time`,`image`,`created_at`,`updated_at`,`company_id`) VALUES ('$start_time','$duration','$dsm_name','$so_name','$asm_name','$rsm_name','$sm_agm_name','$sales_head','$segment_wise_target','$first_product','$second_product','$third_product','$is_a_class_outlet','$is_sales_performance','$is_primary_secondary_scheme','$is_stock_shortage','$is_retailer_replacement','$so_one_travelling','$so_two_travelling','$latitude','$longitude','$geo_address','$mcc_mnc','$unique_id','$user_id','$user_role','$cur_date','$cur_time','$image',NOW(),NOW(),'$company_id')";
                //   h1($comment_query); die;
                  $comment_query_run = mysqli_query($dbc, $comment_query);

                  $generale_trade="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$cur_date','$cur_time','$mcc_mnc','$latlng','$geo_address','Generral Trade',NOW(),'$battery_status','$gps_status','$company_id')";
                $generale_trade_run = mysqli_query($dbc, $generale_trade);

                  if ($comment_query_run) {
                    $unique_id_array[] = $unique_id;

                }
  
              }
          }


        # daily visit 
        if(!empty($Visit))
        {
            foreach($Visit as $vs => $visit_data)
            {
                $latlng = $visit_data->latitude.','.$visit_data->longitude;
                $geo_address = $visit_data->geo_address;
                $gps_status = $visit_data->gps_status;
                $battery_status = $visit_data->battery_status;
                $visit_query = "INSERT INTO `daily_visit`(`user_id`, `colleague_id`, `location_address`, `dealer_id`, `date`, `time`, `latitude`, `longitude`, `mcc_mnc_lac_cellId`, `created_at`,`check_out_time`,`company_id`) VALUES ('$user_id','$visit_data->colleague_id','$visit_data->location','$visit_data->dealer_id','$visit_data->date','$visit_data->time','$visit_data->latitude','$visit_data->longitude','$visit_data->mcc_mnc_lac_cellId',NOW(),'$visit_data->check_out_time','$company_id')";
                $visit_query_run = mysqli_query($dbc, $visit_query);

                $visit_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$visit_data->date','$visit_data->time','$visit_data->mcc_mnc_lac_cellId','$latlng','$geo_address','Daily Visit',NOW(),'$battery_status','$gps_status','$company_id')";
                $visit_track_run = mysqli_query($dbc, $visit_track);
                
   

            }
            // die();
        }

        #create dealer 
        if(!empty($CreateDealer))
        {
            foreach ($CreateDealer as $dealer_key => $dealer_value) 
            {
                $town_id = $dealer_value->town_id;
                $state_id = "SELECT  `location_3.id` from `location_6` 
                            join `location_5` on `location_5`.`id` = location_6.location_5_id
                            join `location_4` on `location_4`.`id` = location_5.location_4_id
                            join `location_3` on `location_3`.`id` = location_4.location_3_id
                             where `id` = '$town_id' AND `company_id` = '$company_id' LIMIT 1";
                $state_id_run = mysqli_query($dbc, $state_id);
                $state_data = mysqli_fetch_row($state_id_run);
                $latlng = $dealer_value->latitude.','.$dealer_value->longitude;
                $gps_status = $dealer_value->gps_status;
                $geo_address = $dealer_value->location;
                $battery_status = $dealer_value->battery_status;
                  $create_dealer_query = "INSERT INTO `dealer`(`id`,`name`,`address`,`lat`,`lng`,`mcc_mnc_lac_cellId`,`date`,`time`,`state_id`,`town_id`,`created_at`,`created_by`,`company_id`) VALUES ('$dealer_value->dealer_id','$dealer_value->dealer_name','$dealer_value->location','$dealer_value->latitude','$dealer_value->longitude','$dealer_value->mcc_mnc_lac_cellid','$dealer_value->date','$dealer_value->time','$state_data[0]','$dealer_value->town_id',NOW(),'$user_id','$company_id')";
                $dealer_query_run = mysqli_query($dbc, $create_dealer_query);

                $dealer_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$dealer_value->date','$dealer_value->time','$dealer_value->mcc_mnc_lac_cellid','$latlng','$geo_address','Dealer Creation',NOW(),'$battery_status','$gps_status','$company_id')";
                $dealer_track_run = mysqli_query($dbc, $dealer_track);

                if ($dealer_query_run) {
                    $unique_id_array[] = $dealer_value->dealer_id;

                }
               

            }
        }

        #retailer comment 
        if(!empty($retailerComment))
        {
            foreach ($retailerComment as $c_key => $c_value) 
            {
                $dealer_id = $c_value->dealer_id;
                $retailer_id = $c_value->retailer_id;
                $date = $c_value->date;
                $time = $c_value->time;
                $date_time = $c_value->date_time;
                $location_id = $c_value->location_id;
                $order_id = $c_value->order_id;
                $user_id = $c_value->user_id;
                $location = $c_value->location;
                $retailer_name = $c_value->retailer_name;
                $lat_lng = $c_value->lat_lng;
                $mccmnclatcellid = $c_value->mccmnclatcellid;
                $time_in_stamp  = $c_value->time_in_stamp;
                $time_out_stamp = $c_value->time_out_stamp;
                $comment = $c_value->comment;
                $battery_status = $c_value->battery_status;
                $gps_status = $c_value->gps_status;
                $comment_query = "INSERT INTO `retailer_comment`(`dealer_id`, `retailer_id`, `user_id`, `date`, `time`, `address`, `order_id`, `lat_lng`, `mcc_mnc_lac_cellId`, `time_in`, `time_out`, `comment`, `batter_status`, `gps_status`, `created_at`,`company_id`) VALUES ('$dealer_id','$retailer_id','$user_id','$date','$time','$location','$order_id','$lat_lng','$mncmcclatcellid','$time_in_stamp','$time_out_stamp','$comment','$batter_status','$gps_status',NOW(),'$company_id')";
                $comment_query_run = mysqli_query($dbc, $comment_query);

                if ($comment_query_run) {
                    $unique_id_array[] = $order_id;

                }

            }
        }
        
        #Feedback
        if (!empty($feedbackSuggestion))
        {
            foreach ($feedbackSuggestion as $fs)
            {
                $insert_feedback="INSERT INTO `feedbackSuggestion` (`suggestion`, `suggested_start_date`, `estimated_volume_growth`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`,`company_id`) VALUES ('$fs->suggestion','$fs->suggested_start_date','$fs->estimated_volume_growth','$fs->order_id','$fs->user_id','$fs->latitude','$fs->longitude','$fs->location','$fs->mcc_mnc_lac_cellid','$fs->cur_date_time','$company_id')";

                $feedback_query_run = mysqli_query($dbc, $insert_feedback);
                if ($feedback_query_run) {
                    $unique_id_array[] = $fs->order_id;

                }
            }
        }

        #update user
        if (isset($UserEditInFormation) && !empty($UserEditInFormation)) {
            foreach ($UserEditInFormation as $udata)
            {
                $update_user = "UPDATE `person` SET `email`='$udata->email_id',``='$user_id',`mobile`='$udata->mobile_no' WHERE `id` = '$udata->user_id' AND `company_id` = '$company_id' ";
                $update_user_result = mysqli_query($dbc, $update_user);

                $person_log = "INSERT INTO `person_log` ( `user_id`, `mobile`, `updated by`,`company_id`) VALUES ('$udata->user_id', '$udata->mobile_no', 'app','$company_id');";
                $person_log_result = mysqli_query($dbc, $person_log);

                if ($person_log_result) {
                    $unique_id_array[] = $udata->user_id;

                }
            }
        }
        // print_r($unique_id_array);
        #Product Investigation Report
        if (!empty($ProductInvestigationReport))
        {
            foreach ($ProductInvestigationReport as $pir)
            {   
                $latlng = $pir->latitude.','.$pir->longitude;
                $geo_address = $pir->geo_address;
                $gps_status = $pir->gps_status;
                $battery_status = $pir->battery_status;
                $p_date = date('Y-m-d');
                $p_time = date('H:i:s');

                $q2="INSERT INTO `product_investigation_report` (`brand_product`, `pack_size`, `product_purchased_from`, `product_purchased_from_town`, `product_purchased_from_district`, `product_purchased_from_state`, `product_purchased_from_phone_no`, `product_purchased_from_fax`, `product_purchased_from_email`, `other_town_estimated_sales`, `manufacture_detail`, `manufacture_town`, `manufacture_district`, `manufacture_state`, `manufacture_godown_phone`, `manufacture_godown_mobile`, `manufacture_godown_fax`, `manufacture_godown_email`, `manufacture_godown_office_phone`, `manufacture_godown_office_mobile`, `manufacture_godown_office_fax`, `manufacture_godown_office_email`, `manufacture_godown_residence_phone`, `manufacture_godown_residence_mobile`, `manufacture_godown_residence_fax`, `manufacture_godown_residence_email`, `detail_of_stockiest`, `stockiest_town`, `stockiest_district`, `stockiest_state`, `stockiest_godown_phone`, `stockiest_godown_mobile`, `stockiest_godown_fax`, `stockiest_godown_email`, `stockiest_godown_office_phone`, `stockiest_godown_office_mobile`, `stockiest_godown_office_fax`, `stockiest_godown_office_email`, `stockiest_godown_residence_phone`, `stockiest_godown_residence_mobile`, `stockiest_godown_residence_fax`, `stockiest_godown_residence_email`, `estimated_monthly_turnover`, `any_other_comment`, `latitude`, `longitude`, `geo_address`, `mcc_mnc`, `order_id`, `unique_id`, `date_time`, `user_id`,`company_id`) VALUES ('$pir->brand_product','$pir->pack_size','$pir->product_purchased_from','$pir->product_purchased_from_town','$pir->product_purchased_from_district','$pir->product_purchased_from_state','$pir->product_purchased_from_phone_no','$pir->product_purchased_from_fax','$pir->product_purchased_from_email','$pir->other_town_estimated_sales','$pir->manufacture_detail','$pir->manufacture_town','$pir->manufacture_district','$pir->manufacture_state','$pir->manufacture_godown_phone','$pir->manufacture_godown_mobile','$pir->manufacture_godown_fax','$pir->manufacture_godown_email','$pir->manufacture_godown_office_phone','$pir->manufacture_godown_office_mobile','$pir->manufacture_godown_office_fax','$pir->manufacture_godown_office_email','$pir->manufacture_godown_residence_phone','$pir->manufacture_godown_residence_mobile','$pir->manufacture_godown_residence_fax','$pir->manufacture_godown_residence_email','$pir->detail_of_stockiest','$pir->stockiest_town','$pir->stockiest_district','$pir->stockiest_state','$pir->stockiest_godown_phone','$pir->stockiest_godown_mobile','$pir->stockiest_godown_fax','$pir->stockiest_godown_email','$pir->stockiest_godown_office_phone','$pir->stockiest_godown_office_mobile','$pir->stockiest_godown_office_fax','$pir->stockiest_godown_office_email','$pir->stockiest_godown_residence_phone','$pir->stockiest_godown_residence_mobile','$pir->stockiest_godown_residence_fax','$pir->stockiest_godown_residence_email','$pir->estimated_monthly_turnover','$pir->any_other_comment','$pir->latitude','$pir->longitude','$pir->geo_address','$pir->mcc_mnc','$pir->order_id','$pir->unique_id','$pir->date_time','$pir->user_id','$company_id')";

                // $product_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$p_date','$p_time','$pir->mcc_mnc','$latlng','$geo_address','Product Investigation',NOW(),'$battery_status','$gps_status','$company_id')";
                // $product_track_run = mysqli_query($dbc, $product_track);

                $daily_query_run = mysqli_query($dbc, $q2);

                if ($daily_query_run) {
                    $unique_id_array[] = $pir->order_id;

                }
            }
        }

        #Competitive Price Intelligence
        if (!empty($competitivePriceIntelligence))
        {
            foreach ($competitivePriceIntelligence as $cpi)
            {   
                $latlng = $cpi->latitude.','.$cpi->longitude;
                $geo_address = $cpi->location;
                $gps_status = $cpi->gps_status;
                $battery_status = $cpi->battery_status;
                $c_date = date('Y-m-d');
                $cs_time = date('H:i:s');

                $q1 = "INSERT INTO `competitive_price_intelligence` (`id`, `brand`, `weight`, `mrp`, `being_sold_to_consumer`, `before_trade_scheme`, `trade_scheme`, `after_trade_scheme`, `units_per_case_bag`, `net_cost_price_to_retailer`, `retailer_margin_per_unit`, `consumer_scheme`, `must_enclose_cash_memo_no`, `must_enclose_cash_memo_date`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`,`company_id`) VALUES ('$cpi->id','$cpi->brand','$cpi->weight','$cpi->mrp','$cpi->being_sold_to_consumer','$cpi->before_trade_scheme','$cpi->trade_scheme','$cpi->after_trade_scheme','$cpi->units_per_case_bag','$cpi->net_cost_price_to_retailer','$cpi->retailer_margin_per_unit','$cpi->consumer_scheme','$cpi->must_enclose_cash_memo_no','$cpi->must_enclose_cash_memo_date','$cpi->order_id','$cpi->user_id','$cpi->latitude','$cpi->longitude','$cpi->location','$cpi->mcc_mnc_lac_cellid','$cpi->cur_date_time','$company_id')";

                $q1_run = mysqli_query($dbc, $q1);

                // $competitive_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$c_date','$c_time','$cpi->mcc_mnc_lac_cellid','$latlng','$geo_address','Competitive Intelligence',NOW(),'$battery_status','$gps_status','$company_id')";
                // $competitive_track_run = mysqli_query($dbc, $competitive_track);

                if ($q1_run) {
                    $unique_id_array[] = $cpi->order_id;

                }
            }
        }

        #Pending Claim
        if (!empty($pendingClaim)) {
            foreach ($pendingClaim as $pc) {
                $pc_query = "INSERT INTO `pending_claim` (`submission_date`, `distributor_id`, `town_id`, `nature_of_claim`, `invoice_number`, `invoice_date`, `claim_paper`, `remark`, `expected_resolution_date`, `order_id`, `user_id`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`,`company_id`) VALUES ('$pc->submission_date','$pc->distributor_id','$pc->town_id','$pc->nature_of_claim','$pc->invoice_number','$pc->invoice_date','$pc->claim_paper','$pc->remark','$pc->expected_resolution_date','$pc->order_id','$pc->user_id','$pc->latitude','$pc->longitude','$pc->location','$pc->mcc_mnc_lac_cellid','$pc->cur_date_time','$company_id');";

                $pc_query_run = mysqli_query($dbc, $pc_query);
                if ($pc_query_run) {
                    $unique_id_array[] = $pc->order_id;

                }
            }
        }

        #TravellingExpenseBill
        if (!empty($TravellingExpenseBill)) {
            foreach ($TravellingExpenseBill as $tr_data) {

                $arrival_beat_id = !empty($tr_data->arrival_beat_id)?$tr_data->arrival_beat_id:'';
                $departure_beat_id = !empty($tr_data->departure_beat_id)?$tr_data->departure_beat_id:'';

                $qo = "select * from travelling_expense_bill where order_id='" . $tr_data->order_id . "'";
                //echo $q2;die;
                $sqlo = mysqli_query($dbc, $qo);
                $numo = mysqli_num_rows($sqlo);
                if ($numo < 1) {

                    $travelling_query = "INSERT INTO `travelling_expense_bill` (`travellingDate`, `arrivalTime`, `departureTime`, `distance`, `fare`, `da`, `hotel`, `postage`, `telephoneExpense`, `conveyance`, `misc`,`stationary`, `total`, `arrivalID`, `departureID`, `travelModeID`, `date_time`, `lat_lng`, `geo_address`, `mcc_mnc`, `unique_id`, `order_id`, `user_id`,`remarks`,`company_id`,`class_type_id`,`class_type_detail_id`,`temp_delete`,`arrival_beat_id`,`departure_beat_id`) VALUES ('$tr_data->travellingDate','$tr_data->arrivalTime','$tr_data->departureTime','$tr_data->distance','$tr_data->fare','$tr_data->da','$tr_data->hotel','$tr_data->postage','$tr_data->telephoneExpense','$tr_data->conveyance','$tr_data->misc','$tr_data->stationary','$tr_data->total','$tr_data->arrivalID','$tr_data->departureID','$tr_data->travelModeID','$tr_data->date_time','$tr_data->lat_lng','$tr_data->geo_address','$tr_data->mcc_mnc','$tr_data->unique_id','$tr_data->order_id','$tr_data->user_id','$tr_data->remark','$company_id','$tr_data->class_type_id','$tr_data->class_type_detail_id','2','$arrival_beat_id','$departure_beat_id')";
                    $travelling_query_run = mysqli_query($dbc, $travelling_query);
                    if ($travelling_query_run) {
                        $unique_id_array[] = $tr_data->order_id;

                    }
                }
            }
        }

        #CometitorNewLaunchProductReport
        if (!empty($CometitorNewLaunchProductReport)) {
            foreach ($CometitorNewLaunchProductReport as $compProduct) {

                $latlng = $compProduct->latitude.','.$compProduct->longitude;
                $geo_address = $compProduct->location;
                $gps_status = $compProduct->gps_status;
                $battery_status = $compProduct->battery_status;
                $c_date = date('Y-m-d');
                $cs_time = date('H:i:s');

                $cmp_query = "INSERT INTO `competitors_launched_product` (`user_id`,`town_area`, `launch_date`, `product_and_brand_name`, `marketed_by`, `sddcsacf`, `address`, `town`, `district`, `state`, `pincode`, `weight`, `nature_of_inner_packaging`, `nature_of_outer_packaging`, `brand`, `weight_pricing`, `mrp`, `being_sold_to_consumer`, `before_trade_scheme`, `trade_scheme`, `after_trade_scheme`, `units_per_case_bag`, `net_cost_price_to_retailer`, `retailer_margin_per_unit`, `consumer_scheme`, `must_enclose_if_any`, `must_enclose_cash_memo`, `must_enclose_cash_memo_date`, `advertising_support`, `pop_material_send_sample`, `outlet_covered`, `samples_must_send`, `comments`, `orderid`, `latitude`, `longitude`, `location`, `mcc_mnc_lac_cellid`, `cur_date_time`,`company_id`) VALUES ('$compProduct->user_id','$compProduct->town_area','$compProduct->launch_date','$compProduct->product_and_brand_name','$compProduct->marketed_by','$compProduct->sddcsacf','$compProduct->address','$compProduct->town','$compProduct->district','$compProduct->state','$compProduct->pincode','$compProduct->weight','$compProduct->nature_of_inner_packaging','$compProduct->nature_of_outer_packaging','$compProduct->brand','$compProduct->weight_pricing','$compProduct->mrp','$compProduct->being_sold_to_consumer','$compProduct->before_trade_scheme','$compProduct->trade_scheme','$compProduct->after_trade_scheme','$compProduct->units_per_case_bag','$compProduct->net_cost_price_to_retailer','$compProduct->retailer_margin_per_unit','$compProduct->consumer_scheme','$compProduct->must_enclose_if_any','$compProduct->must_enclose_cash_memo','$compProduct->must_enclose_cash_memo_date','$compProduct->advertising_support','$compProduct->pop_material_send_sample','$compProduct->outlet_covered','$compProduct->samples_must_send','$compProduct->comments','$compProduct->orderid','$compProduct->latitude','$compProduct->longitude','$compProduct->location','$compProduct->mcc_mnc_lac_cellid','$compProduct->cur_date_time','$company_id')";
//                echo $cmp_query;die;
                $cmp_query_run = mysqli_query($dbc, $cmp_query);

                // $cometitior_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$c_date','$c_time','$compProduct->mcc_mnc_lac_cellid','$latlng','$geo_address','Cometitor Product',NOW(),'$battery_status','$gps_status','$company_id')";
                // $cometitior_track_run = mysqli_query($dbc, $cometitior_track);

                if ($cmp_query_run) {
                    $unique_id_array[] = $compProduct->orderid;

                }
            }
        }

        #Complaint Report
        if (!empty($ComplaintReport)) {
            foreach ($ComplaintReport as $cr) {
                $cr_insert = "INSERT INTO `Complaint_report` (`complaint_product`, `natureOfComplaintMentioned`, `quantityLying`, `complaintWithRetailer`, `casesWithComplaint`, `casesRv`, `packersSlip`, `billNo`, `date`, `amountOfBill`, `productDispatched`, `manufacturingUnit`, `sampleClosed`, `concernedSuperDistributorAddress`, `concernedRetailerAddress`, `concernedConsumerAddress`, `actionTaken`, `comments`, `date_time`, `lat_lng`, `geo_address`, `mcc_mnc`, `unique_id`, `order_id`, `user_id`, `complaintByStr`, `agreeStr`, `complaintID`, `agreeID`, `created_at`,`company_id`) VALUES ('$cr->complaintProduct','$cr->natureOfComplaintMentioned','$cr->quantityLying','$cr->complaintWithRetailer','$cr->casesWithComplaint','$cr->casesRv','$cr->packersSlip','$cr->billNo','$cr->date','$cr->amountOfBill','$cr->productDispatched','$cr->manufacturingUnit','$cr->sampleClosed','$cr->concernedSuperDistributorAddress','$cr->concernedRetailerAddress','$cr->concernedConsumerAddress','$cr->actionTaken','$cr->comments','$cr->date_time','$cr->lat_lng','$cr->geo_address','$cr->mcc_mnc','$cr->unique_id','$cr->order_id','$cr->user_id','$cr->complaintByStr','$cr->agreeStr','$cr->complaintID','$cr->agreeID',CURRENT_TIMESTAMP,'$company_id')";

                $cr_insert_run = mysqli_query($dbc, $cr_insert);

                if ($cr_insert_run) {
                    $unique_id_array[] = $cr->unique_id;

                }
            }
        }

        #Daily Prospect Working
        if (!empty($DailyProspectWorking)) {
            foreach ($DailyProspectWorking as $newd) {

                $latlng = $newd->latitude.','.$newd->longitude;
                $geo_address = $newd->party_address;
                $gps_status = $newd->gps_status;
                $battery_status = $newd->battery_status;
                $mcc_mnc_lac_cellid = $newd->mcc_mnc_lac_cellid;
                // $c_date = date('Y-m-d');
                // $cs_time = date('H:i:s');

                $dp_query = "INSERT INTO `daily_prospecting_working` (`user_id`,`town`,`district`,`state`,`party_name`, `party_address`, `phone_no`, `residence_phone`, `mobile_no`, `email_id`, `person_met_and_status`, `established_since`, `annual_turn_over`, `reputation_trade_relation`, `financial_position`, `level_ofinterst`, `from_time`, `to_time`, `units_availble_and_qty`, `gst_no`, `gst_registrtion_date`, `pan_card_no`, `pan_card_date`, `godown_size`, `no_of_employee`, `terms_condition`, `assured_investment`, `stockiest_from_filled`, `comments`, `orderid`, `latitude`, `longitude`, `mcc_mnc_lac_cellid`, `cur_date_time`,`company_id`) VALUES ('$newd->user_id','$newd->town','$newd->district','$newd->state','$newd->party_name','$newd->party_address','$newd->phone_no','$newd->residence_phone','$newd->mobile_no','$newd->email_id','$newd->person_met_and_status','$newd->established_since','$newd->annual_turn_over','$newd->reputation_trade_relation','$newd->financial_position','$newd->level_ofinterst','$newd->from_time','$newd->to_time','$newd->units_availble_and_qty','$newd->gst_no','$newd->gst_registrtion_date','$newd->pan_card_no','$newd->pan_card_date','$newd->godown_size','$newd->no_of_employee','$newd->terms_condition','$newd->assured_investment','$newd->stockiest_from_filled','$newd->comments','$newd->orderid','$newd->latitude','$newd->longitude','$newd->mcc_mnc_lac_cellid','$newd->cur_date_time','$company_id')";
                
                $c_date = date('Y-m-d', strtotime($newd->cur_date_time));
                $c_time = date('H:i:s', strtotime($newd->cur_date_time));
                
                $cometitior_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$c_date','$c_time','$mcc_mnc_lac_cellid','$latlng','$geo_address','Daily Prospecting',NOW(),'$battery_status','$gps_status','$company_id')";
                $cometitior_track_run = mysqli_query($dbc, $cometitior_track);

                $dp_query_run = mysqli_query($dbc, $dp_query);
                if ($dp_query_run) {
                    $unique_id_array[] = $newd->orderid;

                }
            }
        }

        #Daily Reporting
        if (!empty($dailyReporting)) {
            foreach ($dailyReporting as $newda) {

                 $latlng = $newda->latlng;
                $geo_address = $newda->location;
                $gps_status = $newda->gps_status;
                $battery_status = $newda->battery_status;
                $mcc_mnc_lac_cellid = $newda->check_mcc_mnc_lac_cellId_final;

                $primary_target = $newda->primary_target;
                $secondary_target = $newda->secondary_target;

                // $c_date = date('Y-m-d');
                // $cs_time = date('H:i:s');

                $c_date = date('Y-m-d', strtotime($newda->date_time));
                $cs_time = date('H:i:s', strtotime($newda->date_time));

                $dr_query = "INSERT INTO `daily_reporting`(`user_id`, `work_date`, `server_date_time`, `work_status`, `working_with`, `user_location`, `mnc_mcc_lat_cellid`, `lat_lng`, `remarks`, `attn_address`, `order_id`, `dealer_id`, `location_id`,`company_id`,`primary_target`,`secondary_target`) 
                VALUES ('$user_id','$newda->date_time',NOW(),'$newda->status','$newda->working_with','$newda->location','$newda->check_mcc_mnc_lac_cellId_final','$newda->latlng','$newda->remark','$newda->location','$newda->orderid','$newda->dealerid','$newda->LocId','$company_id','$primary_target','$secondary_target')";
                $dr_query_run = mysqli_query($dbc, $dr_query);

                 $daily_reporting_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$c_date','$cs_time','$mcc_mnc_lac_cellid','$latlng','$geo_address','Daily Reporting',NOW(),'$battery_status','$gps_status','$company_id')";
                $daily_reporting_track_run = mysqli_query($dbc, $daily_reporting_track);

                if ($dr_query_run) {
                    $unique_id_array[] = $newda->orderid;

                }
            }
        }

        #Payment Received Details
        if (!empty($paymentReceviedDetails)) {
            foreach ($paymentReceviedDetails as $k => $paymentData) {
                $addDealerPayment = "INSERT INTO dealer_payments( `user_id`,`dealer_id`, `zone_id`, `emp_id`, `user_designation`, `user_hq`, `town`,`payment_mode`, `invoice_number`, `amount`, `drawn_from_bank`,`deposited_bank`,`payment_recevied_date`,`deposited_date`,`lat`,`lng`,`geo_address`,`cur_datetime`,`unique_id`,`order_id`, `mcc_mnc`,`company_id`) "
                    . "VALUES('$paymentData->user_id','$paymentData->distributor_id', '$paymentData->zone_id', '$paymentData->emp_id', '$paymentData->user_designation', '$paymentData->user_hq', '$paymentData->town', '$paymentData->payment_mode_id','$paymentData->number','$paymentData->amount','$paymentData->drawn_from_bank','$paymentData->deposited_bank','$paymentData->payment_received_date','$paymentData->deposited_date','$paymentData->latitude','$paymentData->longitude','$paymentData->geo_address','$paymentData->cur_datetime','$paymentData->unique_id','$paymentData->order_id', '$paymentData->mcc_mnc','$company_id')";
                $mob_qry_run = mysqli_query($dbc, $addDealerPayment);
                if ($mob_qry_run) {
                    $unique_id_array[] = $paymentData->unique_id;

                }
            }
        }


//////////////////////////////////RETAILER DELETE/////////////////////////
        if (isset($RetailerDeleteStatus) && !empty($RetailerDeleteStatus)) {
            $RetailerDelete_count = count($RetailerDeleteStatus);
            $retdelc = 0;
            while ($retdelc < $RetailerDelete_count) {
                $retailer_id = $RetailerDeleteStatus[$retdelc]->c_code;
                $qryretdel = "UPDATE `retailer` SET `retailer_status`='0',`deactivated_by_user`='$user_id',`deactivated_date_time`=NOW() WHERE `id` = '$retailer_id' AND `company_id` = '$company_id'";
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

                $qryleave = "UPDATE `user_leave` SET `value`='$leave' WHERE `user_id` = '$userid' AND `leave_id`='$leaveid' AND `company_id`='$company_id'";
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

            $qryuser = "UPDATE `person` SET `mobile`='$contact',`email`='$email' WHERE `id` = '$userid' AND `company_id` = '$company_id'";
            $result = mysqli_query($dbc, $qryuser);

            $person_log = "INSERT INTO `person_log` ( `user_id`, `mobile`, `updated by`,`company_id`) VALUES ('$userid', '$contact', 'app','$company_id');";
            $person_log_result = mysqli_query($dbc, $person_log);
            if ($person_log_result) {
                $unique_id_array[] = $userid;

            }
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
                        . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "' AND `company_id` = '$company_id' ";
                    $sql = mysqli_query($dbc, $q2);
                    $num = mysqli_num_rows($sql);
                    if ($num < 1) {


                        $q = "INSERT INTO `user_daily_attendance`(`user_id`,`order_id`,`work_date`,`server_date`,`company_id`)VALUES
                    ('$isr_id','$order_id','$Checkin',NOW(),'$company_id')";
                        //echo $q;die;

                        $run = mysqli_query($dbc, $q);

                        if ($run) {
                            $unique_id_array[] = $order_id;
            
                        }
                    }
                }

                if (isset($Checkout) && !empty($Checkout)) {

                    $order_id = date('YmdHis', strtotime($Checkout)) . $isr_id;
                    $new_work_date = date('Y-m-d', strtotime($Checkout));
                    $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where user_id='" . $isr_id . "' AND "
                        . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "' AND `company_id` = '$company_id' ";

                    $sql = mysqli_query($dbc, $q2);
                    $num = mysqli_num_rows($sql);
                    if ($num < 1) {


                        $q = "INSERT INTO `check_out`(`user_id`,`order_id`,`work_date`,`server_date_time`,`company_id`)VALUES
                    ('$isr_id','$order_id','$Checkout',NOW(),'$company_id')";

                        #update user_daily_attendance with checkout
                        $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$Checkout', `checkout_server_date_time` = NOW()
WHERE `user_daily_attendance`.`user_id` = '$isr_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($Checkout,'Y-m-d') AND `company_id` = '$company_id' ";

                        $run = mysqli_query($dbc, $q);
                        $run_update = mysqli_query($dbc, $update_user_daily_attendance);

                        if ($run) {
                            $unique_id_array[] = $order_id;
            
                        }
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
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "' AND `company_id` = '$company_id'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {


                    $q = "INSERT INTO `user_daily_attendance`(`user_id`,`order_id`,`work_date`,`remarks`,`server_date`,`company_id`)VALUES
                    ('$junior_id','$order_id','$Date','$remarks',NOW(),'$company_id')";
                    //echo $q;die;

                    $run = mysqli_query($dbc, $q);
                    if ($run) {
                        $unique_id_array[] = $order_id;
        
                    }
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
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "' AND `company_id` = '$company_id' ";
                //echo $q2;die;
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {


                    $q = "INSERT INTO `check_out`(`user_id`,`order_id`,`work_date`,`remarks`,`server_date_time`,`company_id`)VALUES
                    ('$junior_id','$order_id','$Date','$remarks',NOW(),'$company_id')";
                    //echo $q;die;

                    #update user_daily_attendance with checkout
                    $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$Date', `checkout_server_date_time` = NOW(),
`remarks` = '$remarks'
WHERE `user_daily_attendance`.`user_id` = '$junior_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($Date,'Y-m-d') AND `company_id` = '$company_id' ";

                    $run_update = mysqli_query($dbc, $update_user_daily_attendance);

                    $run = mysqli_query($dbc, $q);
                    if ($run) {
                        $unique_id_array[] = $order_id;
        
                    }
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

                $qry = "INSERT INTO `isr_total_sale_counter`(`id`,`order_id`, `Isrname`,`isr_id`, `TotalSale`, `valuefromnewoutlet`, `Totalcall`, `Date`, `DistributorId`, `Remarks`, `BeatId`, `Productivecall`, `newoutlet`,`company_id`) VALUES ('','$order_id','$Isrname','$isr_id','$TotalSale','$valuefromnewoutlet','$Totalcall','$Date','$DistributorId','$Remarks','$BeatId','$Productivecall','$newoutlet','$company_id')";
                $result = mysqli_query($dbc, $qry);
                if ($result) {
                    $unique_id_array[] = $order_id;
    
                }
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
                $qry = "INSERT INTO `isr_product_details`(`order_id`, `product_id`,`rate`, `quantity`, `amount`,`company_id`) VALUES ('$order_id','$product_id','$rate','$qty','$pv','$company_id')";

                $result = mysqli_query($dbc, $qry);
                if ($result) {
                    $unique_id_array[] = $order_id;
    
                }
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


                    $qry = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`,`company_id`) VALUES ('$order_id','$retailer_id','$status','1','$date','$time',NOW(),'$company_id')";

                    $result = mysqli_query($dbc, $qry);
                    if ($result) {
                        $unique_id_array[] = $order_id;
        
                    }
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

                $qo1 = "select * from retailer_scheme_status where order_id='" . $order_id . "' AND `company_id` = '$company_id' ";
                //echo $q2;die;
                $sqlo1 = mysqli_query($dbc, $qo1);
                $numo1 = mysqli_num_rows($sqlo1);
                if ($numo1 < 1) {


                    $qry1 = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`,`company_id`) VALUES ('$order_id','$retailer_id','$status','2','$date','$time',NOW(),'$company_id')";

                    $result1 = mysqli_query($dbc, $qry1);
                    if ($result1) {
                        $unique_id_array[] = $order_id;
        
                    }
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

                $qo2 = "select * from retailer_scheme_status where order_id='" . $order_id . "' AND `company_id` = '$company_id' ";
                //echo $q2;die;
                $sqlo2 = mysqli_query($dbc, $qo2);
                $numo2 = mysqli_num_rows($sqlo2);
                if ($numo2 < 1) {


                    $qry2 = "INSERT INTO `retailer_scheme_status`(`order_id`, `retailer_id`,`status`,`scheme_id`, `date`, `time`,`server_date_time`,`company_id`) VALUES ('$order_id','$retailer_id','$status','3','$date','$time',NOW(),'$company_id')";

                    $result2 = mysqli_query($dbc, $qry2);
                    if ($result2) {
                        $unique_id_array[] = $order_id;
        
                    }
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
                $q = "INSERT INTO `user_expense_report`(`total_calls`, `travelling_allowance`, `drawing_allowance`, `other_expense`, `travelling_mode_id`, `start_journey`, `end_journey`, `person_id`, `submit_date`, `submit_time`, `remarks`,`order_id`,`expense_date`,`rent`,`company_id`)"
                    . " VALUES ('$totcalls','$ta','$da','$other_expense','$travelling_mode_id','$start','$end','$user_id','$cr_date','$cr_time','$remarks','$order_id$user_id','$expense_date','$rent','$company_id')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;
    
                }
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
                $latlng = $lat.','.$lng;
                $address = $merchandise[$me]->adsress;
                $mcc_mnc = $merchandise[$me]->mcc_mnc_lac_cellid;
                $gps_status = $merchandise[$me]->gps_status;
                $battery_status = $merchandise[$me]->battery_status;

                $q = "INSERT INTO `merchandise`(`merchandise_id`, `merchandise_name`, `date`, `time`,`user_id`, `retailer_id`,`order_id`,`lat`,`lng`,`address`,`mcc_mnc`,`qty`,`server_date_time`,`company_id`) VALUES ('$mer_id','$mer_name','$date','$time','$user_id','$retailer','$orderid','$lat','$lng','$address','$mcc_mnc','$qty',NOW(),'$company_id')";
                $result = mysqli_query($dbc, $q);

                $merchandise_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$mcc_mnc','$latlng','$address','Merchandise',NOW(),'$battery_status','$gps_status','$company_id')";
                $merchandise_track_run = mysqli_query($dbc, $merchandise_track);

                if ($result) {
                    $unique_id_array[] = $orderid;
    
                }
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

                $q = "INSERT INTO `merchandise_requirement`(`merchandise_id`, `merchandise_name`, `date`, `time`, `user_id`,`retailer_id`,`order_id`,`remarks`,`qty`,`server_date_time`,`company_id`) VALUES ('$mer_id','$mer_name','$date','$time','$user_id','$retailer','$orderid','$remarks','$qty',NOW(),'$company_id')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $orderid;
    
                }
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

                $q = "INSERT INTO `retailer_stock`(`order_id`,`user_id`, `dealer_id`, `location_id`, `date`,`retailer_id`,`company_id`) VALUES ('$order_id','$user_id','$dealer_id','$location_id','$date','$retailer_id','$company_id')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;
    
                }
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
                $mcc_mnc = $BalanceStock[$bre]->mcc_mnc_lac_cellId;
                $latlng = $lat.','.$lng;
                $address = $BalanceStock[$bre]->address;
                // $date = date('Y-m-d');
                // $time = date('H:i:s');
                
                $date = date('Y-m-d', strtotime($mobile_datetime));
                $time = date('H:i:s', strtotime($mobile_datetime));
                
                $gps_status = $BalanceStock[$bre]->gps_status;
                $battery_status = $BalanceStock[$bre]->battery_status;
                $balance_secondary_qty = !empty($BalanceStock[$bre]->balance_secondary_qty)?$BalanceStock[$bre]->balance_secondary_qty:'0';
                $exp_date = strtotime(date('Y-m-d', strtotime($mfg_date)) . '-1 year');

                $qbr = "INSERT INTO `dealer_balance_stock`(`order_id`, `dealer_id`, `user_id`, `product_id`, `stock_qty`, `mfg_date`, `exp_date`, `cases`, `submit_date_time`, `server_date_time`,`mrp`,`pcs_mrp`,sstatus,`lat`,`lng`,`address`,`company_id`,`balance_secondary_qty`) VALUES ('$balance_order_id$user_id','$dealer_id','$user_id','$balance_product_code','$balance_pieces','$mfg_date','$exp_date','$balance_cases','$mobile_datetime',NOW(),'$bsmrp','$balance_pcs_mrp','0','$lat','$lng','$address','$company_id','$balance_secondary_qty')";

                $balance_stock="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$mcc_mnc','$latlng','$address','Dealer Balance Stock',NOW(),'$battery_status','$gps_status','$company_id')";
                $balance_stock_run = mysqli_query($dbc, $balance_stock);


                $result_br = mysqli_query($dbc, $qbr);
                if ($result_br) {
                    $unique_id_array[] = $balance_order_id;
    
                }

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

        if (!empty($retailerstockdetails)) {
            // print_r($retailerstock);
            $count_retailerstockdetails = count($retailerstockdetails);
            $j = 0;

            while ($j < $count_retailerstockdetails) {
                $order_id = $retailerstockdetails[$j]->order_id;
                $product_id = $retailerstockdetails[$j]->product_id;
                $qty = $retailerstockdetails[$j]->quantity;
                $cases = !empty($retailerstockdetails[$j]->case_qty)?$retailerstockdetails[$j]->case_qty:'0';
                $secondary_qty = !empty($retailerstockdetails[$j]->secondary_qty)?$retailerstockdetails[$j]->secondary_qty:'0';
                $stock_month = $retailerstockdetails[$j]->stock_month;


                $q = "INSERT INTO `retailer_stock_details`(`order_id`, `product_id`, `quantity`,`stock_month`,`company_id`,`cases`,`secondary_qty`) VALUES  ('$order_id','$product_id','$qty','$stock_month','$company_id','$cases','$secondary_qty')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;
    
                }
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
                $battery_status = $data->response->Attandance[$k]->battery_status;
                $gps_status = $data->response->Attandance[$k]->gps_status;


                $leave_from_date = $data->response->Attandance[$k]->from_date;
                $leave_to_date = $data->response->Attandance[$k]->to_date;
                $leave_type_id = $data->response->Attandance[$k]->leave_type_id;


                $ll = explode(",", $latlng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
                $new_work_date = date('Y-m-d', strtotime($date_time));
                $new_work_time = date('H:i:s', strtotime($date_time));
                $q2 = "select *,DATE_FORMAT(work_date,'Y-m-d') as work_date from user_daily_attendance where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "' AND `company_id` = '$company_id' ";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {
                    $q = "INSERT INTO `user_daily_attendance`(`user_id`, `order_id`, `work_date`,`working_with`, `work_status`,`mnc_mcc_lat_cellid`, `lat_lng`, `track_addrs`, `remarks`,`server_date`,`battery_status`,`gps_status`,`company_id`,`leave_from_date`,`leave_to_date`,`leave_id`)VALUES
                ('$user_id','$order_id$user_id','$date_time','$working_with','$status','$mnc_mcc_lat_cellid','$latlng','$user_location','$remark',CURRENT_TIMESTAMP,'$battery_status','$gps_status','$company_id','$leave_from_date','$leave_to_date','$leave_type_id')";
                    $run = mysqli_query($dbc, $q);

                    if($status == '48' && $company_id == '37')
                    {
                        $date1 = date('d',strtotime($leave_from_date));
                        $date2 = date('d',strtotime($leave_to_date));
                        $dat3 = $date2-$date1;

                        $q1 = "INSERT INTO `leaves`(`employee_id`, `leave_type`, `date_from`,`date_to`, `days`,`reason`,`company_id`)VALUES
                            ('$user_id','2','$leave_from_date','$leave_to_date','$dat3','Leave','$company_id')";
                        $run1 = mysqli_query($dbc, $q1);
                    }
                    
                    


                    if ($run) {
                        $unique_id_array[] = $order_id;
        
                    }

                $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$new_work_date','$new_work_time','$mnc_mcc_lat_cellid','$latlng','$user_location','Attendance',NOW(),'$battery_status','$gps_status','$company_id')";
                $rd = mysqli_query($dbc, $qd);
                if ($rd) {
                    $unique_id_array[] = $order_id;
    
                }
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

                $total_secondary_target = !empty($Checkoutlocation[$c]->total_secondary_target)?$Checkoutlocation[$c]->total_secondary_target:'0';


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
                $q2 = "SELECT *,DATE_FORMAT(work_date,'Y-m-d') as work_date from check_out where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(work_date,'%Y-%m-%d') ='" . $new_work_date . "'";
                $sql = mysqli_query($dbc, $q2);
                $num = mysqli_num_rows($sql);
                if ($num < 1) {
                     $q = "INSERT INTO check_out(`user_id`,`lat_lng`,`mnc_mcc_lat_cellid`,`work_date`,`server_date_time`,`attn_address`,`order_id`,`remarks`,`total_call`,`total_pc`,`total_sale_value`,`total_secondary_target`,`battery_status`,`gps_status`,`company_id`)
                        VALUES('$user_id','$latlng','$mcc_mnc_lac_cellId_final','$date_time','$current_date_time','$user_location','$order_id$user_id','$remarks','$total_call','$total_pc','$total_sale_value','$total_secondary_target','$battery_status','$gps_status','$company_id')";
                    #update user_daily_attendance with checkout
                    $update_user_daily_attendance="UPDATE `user_daily_attendance` SET `checkout_date` = '$date_time', `checkout_server_date_time` = '$current_date_time',`checkout_remarks` = '$remarks', `checkout_lat_lng`= '$latlng', `checkout_mnc_mcc_lat_cellid`='$mcc_mnc_lac_cellId_final',`checkout_address`='$user_location'
                        WHERE `user_daily_attendance`.`user_id` = '$user_id' and DATE_FORMAT(`user_daily_attendance`.`work_date`,'Y-m-d') = DATE_FORMAT($date_time,'Y-m-d') AND `company_id` = '$company_id' ";

                    $run_update = mysqli_query($dbc, $update_user_daily_attendance);
                    if ($run_update) {
                        $unique_id_array[] = $order_id;
        
                    }
                    $result = mysqli_query($dbc, $q);
                    if ($result) {
                        $unique_id_array[] = $order_id;
        
                    }

                $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$new_work_date','$new_work_time','$mcc_mnc_lac_cellId_final','$latlng','$user_location','CheckOut',NOW(),'$battery_status','$gps_status','$company_id')";
                $rd = mysqli_query($dbc, $qd);
                if ($rd) {
                    $unique_id_array[] = $order_id;
    
                }
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
         `cheque_no`, `cheque_date`, `trans_no`, `trans_date`, `payment_date`, `payment_time`, `user_id`,`company_id`) VALUES('$dealer',
         '$location','$trcode','$mode','$anount','$branch','$chequeno','$cheque_date','$trans_no','$trans_date','$today','$ttime','$user_id','$company_id')";
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
         `cheque_no`, `cheque_date`, `trans_no`, `trans_date`, `payment_date`, `payment_time`, `user_id`,`company_id`) VALUES('$dealer','$retailer',
         '$location','$trcode','$mode','$anount','$branch','$chequeno','$cheque_date','$trans_no','$trans_date','$today','$ttime','$user_id','$company_id')";
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
                $qr = "UPDATE `retailer` SET `lat_long`='$lat_long',`email`='$email',`contact_per_name`='$contactperson',`landline`='$contactno',`mncmcclatcellid`='$mncmcclatcellid' WHERE `id`='$retailer' AND `company_id` = '$company_id' ";
                // echo $qr;
                $result = mysqli_query($dbc, $qr);

                $rl++;
            }
        }


///////////////////////////////////////////////////////////////////////////////

        // updated code for secondare sales starts
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

                $discount = $callwisereporting[$l]->Discount;
                $total_sale_value = $callwisereporting[$l]->Finalvalue;
                $retailer_id = $callwisereporting[$l]->retailer_id;
                $call_status = $callwisereporting[$l]->call_status;
                $remarks = $callwisereporting[$l]->remarks;
                $geo_status = $callwisereporting[$l]->geo_status;
                $gps_status = $callwisereporting[$l]->gps_status;
                $battery_status = $callwisereporting[$l]->battery_status;
                $non_productive_id = $callwisereporting[$l]->non_productive_id;

                // $user_id=$callwisereporting[$l]->user_id;

                $ll = explode(",", $lat_lng);
                if ($location == "$$" || $location == ' ') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }

                $finalOrderID = $order_id.$user_id;

                //  $chk_ret = "SELECT id,company_id FROM retailer WHERE retailer_id='$retailer_id' LIMIT 1";
                // $run_ret = mysqli_query($dbc, $chk_ret);
                // $num_ret = mysqli_num_rows($run_ret);
                // $assoc_ret = mysqli_fetch_assoc($run_ret);

                // if ($num_ret >= 1) {

                //     if($assoc_ret['company_id'] == $company_id){



                        // $chk_uso = "SELECT id FROM user_sales_order WHERE order_id='$order_id$user_id'";
                        $chk_uso = "SELECT id,order_id,time,call_status FROM user_sales_order WHERE user_id='$user_id' AND `date`='$date' AND `retailer_id`='$retailer_id' AND `company_id`='$company_id' ORDER BY id DESC LIMIT 1";
                        $run_uso = mysqli_query($dbc, $chk_uso);
                        $num_uso = mysqli_num_rows($run_uso);
                        $assoc_uso = mysqli_fetch_assoc($run_uso);
                        if ($num_uso < 1) {
                            $quso = "INSERT INTO `user_sales_order`(`order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`,`call_status`, `total_sale_value`,`discount`,`amount`, `total_sale_qty`,`lat_lng`, `mccmnclatcellid`,`geo_status`,`track_address`, `date`, `time`, `image_name`, `override_status`,`remarks`,`battery_status`,`gps_status`,`reason`,`company_id`,`total_dispatch_qty`,`order_status`)"
                                . " VALUES('$order_id$user_id','$user_id','$dealer_id','$beat_id','$retailer_id','$call_status','$total_sale_value','$discount','$discount_before','$total_sale_qty','$lat_lng','$mcc_mnc','$geo_status','$user_location','$date','$time','.jpg','$override_status','$remarks','$battery_status','$gps_status','$non_productive_id','$company_id','0','0')";
                            //      echo $q;
                         $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$mcc_mnc','$lat_lng','$user_location','Order Booking',NOW(),'$battery_status','$gps_status','$company_id')";
                        $rd = mysqli_query($dbc, $qd);
                        }
                        elseif($num_uso >= 1 && $assoc_uso['order_id'] < $finalOrderID){

                           
                            if($assoc_uso['call_status'] == '1' && $call_status == '0'){
                                // No action Performed For Productive To NP
                            }
                            else{
                            $deleteSaleOrderDetails = "DELETE FROM user_sales_order_details
                                                        WHERE user_sales_order_details.order_id='$assoc_uso[order_id]' 
                                                        AND user_sales_order_details.company_id='$company_id'";
                            $deleteSaleOrderDetailsRun = mysqli_query($dbc, $deleteSaleOrderDetails);

                            if($deleteSaleOrderDetailsRun){

                            $updateWorkTracking = "UPDATE user_work_tracking SET `status` = 'Tracking' 
                                                    WHERE user_work_tracking.user_id = '$user_id' 
                                                    AND user_work_tracking.track_date = '$date' 
                                                    AND user_work_tracking.track_time = '$assoc_uso[time]' 
                                                    AND user_work_tracking.company_id = '$company_id' 
                                                    AND user_work_tracking.status = 'Order Booking'";
                            $updateWorkTrackingRun = mysqli_query($dbc, $updateWorkTracking);


                            $deleteSaleOrder = "DELETE FROM user_sales_order WHERE user_id='$user_id' AND `date`='$date' AND `retailer_id`='$retailer_id' AND `company_id`='$company_id'";
                            $deleteSaleOrderRun = mysqli_query($dbc, $deleteSaleOrder);
                            }


                            $quso = "INSERT INTO `user_sales_order`(`order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`,`call_status`, `total_sale_value`,`discount`,`amount`, `total_sale_qty`,`lat_lng`, `mccmnclatcellid`,`geo_status`,`track_address`, `date`, `time`, `image_name`, `override_status`,`remarks`,`battery_status`,`gps_status`,`reason`,`company_id`,`total_dispatch_qty`,`order_status`)"
                                . " VALUES('$order_id$user_id','$user_id','$dealer_id','$beat_id','$retailer_id','$call_status','$total_sale_value','$discount','$discount_before','$total_sale_qty','$lat_lng','$mcc_mnc','$geo_status','$user_location','$date','$time','.jpg','$override_status','$remarks','$battery_status','$gps_status','$non_productive_id','$company_id','0','0')";
                            //      echo $q;
                             $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$mcc_mnc','$lat_lng','$user_location','Order Booking',NOW(),'$battery_status','$gps_status','$company_id')";
                            $rd = mysqli_query($dbc, $qd);
                            }


                    //     }
                    // }
                }

                #Update archiv in MTP
                $cur_date=date('Y-m-d',strtotime('now'));
                $update_mtp="UPDATE monthly_tour_program SET arch=arch+$total_sale_value WHERE person_id=$user_id AND `company_id` = '$company_id' and working_date=$cur_date";
                $update_mtp_result = mysqli_query($dbc, $update_mtp);
                $result = mysqli_query($dbc, $quso);
                if ($result) {
                    $unique_id_array[] = $order_id;

                }
                $l++;
            }
        }



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
                $secondary_quantity = !empty($callwisereportingstatus[$p]->secondary_quantity)?$callwisereportingstatus[$p]->secondary_quantity:'0';
                $secondary_rate = !empty($callwisereportingstatus[$p]->secondary_rate)?$callwisereportingstatus[$p]->secondary_rate:'0';
                $case_quantity = !empty($callwisereportingstatus[$p]->case_quantity)?$callwisereportingstatus[$p]->case_quantity:'0';
                $case_rate = !empty($callwisereportingstatus[$p]->case_rate)?$callwisereportingstatus[$p]->case_rate:'0';
                $piece_quantity = !empty($callwisereportingstatus[$p]->piece_quantity)?$callwisereportingstatus[$p]->piece_quantity:'0';

                $pro_weight = !empty($callwisereportingstatus[$p]->pro_weight)?$callwisereportingstatus[$p]->pro_weight:'0';

                $finalOrderId = $orderid.$user_id;

                $chk_uso = "SELECT order_id FROM user_sales_order WHERE order_id='$orderid$user_id' LIMIT 1";
                $run_uho = mysqli_query($dbc, $chk_uso);
                $run_assoc_uho = mysqli_num_rows($run_uho);



                $chk_usod = "SELECT id FROM user_sales_order_details WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND `company_id` = '$company_id' ";
                $run_uhod = mysqli_query($dbc, $chk_usod);
                $num_data = mysqli_num_rows($run_uhod);


                if ($run_assoc_uho >= 1) {


                    if ($num_data < 1 ) {

                        $qusod = "INSERT INTO `user_sales_order_details`(`order_id`, `product_id`, `rate`,`case_rate`, `quantity`, `scheme_qty`,`company_id`,`piece_quantity`,`case_quantity`,`secondary_quantity`,`secondary_rate`,`weight`,`status`)"
                            . " VALUES('$orderid$user_id','$product_id','$rate','$case_rate','$prod_qty','$scheme_qty','$company_id','$piece_quantity','$case_quantity','$secondary_quantity','$secondary_rate','$pro_weight','0')";


                    } else {
                        $qusod = "UPDATE `user_sales_order_details` SET `quantity`='$prod_qty',`rate`='$rate' WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND `company_id` = '$company_id' ";
                    }

                }



                $results = mysqli_query($dbc, $qusod);


                if ($results) {
                    $unique_id_array[] = $uid;

                }
                $p++;
            }
        }
        // updated code for secondary sales ends




        // uncomment if facing issue starts

        // if (!empty($callwisereportingstatus)) {
        //     $callwisereport = count($callwisereportingstatus);
        //     $p = 0;
        //     while ($p < $callwisereport) {
        //         $orderid = $callwisereportingstatus[$p]->order_id;
        //         $uid = $callwisereportingstatus[$p]->unique_id;
        //         $product_id = $callwisereportingstatus[$p]->product_id;
        //         $prod_qty = $callwisereportingstatus[$p]->quantity;
        //         $scheme_qty = $callwisereportingstatus[$p]->scheme_qty;
        //         $rate = $callwisereportingstatus[$p]->rate;
        //         $secondary_quantity = !empty($callwisereportingstatus[$p]->secondary_quantity)?$callwisereportingstatus[$p]->secondary_quantity:'0';
        //         $secondary_rate = !empty($callwisereportingstatus[$p]->secondary_rate)?$callwisereportingstatus[$p]->secondary_rate:'0';
        //         $case_quantity = !empty($callwisereportingstatus[$p]->case_quantity)?$callwisereportingstatus[$p]->case_quantity:'0';
        //         $case_rate = !empty($callwisereportingstatus[$p]->case_rate)?$callwisereportingstatus[$p]->case_rate:'0';
        //         $piece_quantity = !empty($callwisereportingstatus[$p]->piece_quantity)?$callwisereportingstatus[$p]->piece_quantity:'0';

        //         $pro_weight = !empty($callwisereportingstatus[$p]->pro_weight)?$callwisereportingstatus[$p]->pro_weight:'0';



        //         $chk_usod = "SELECT id FROM user_sales_order_details WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND `company_id` = '$company_id' ";
        //         $run_uhod = mysqli_query($dbc, $chk_usod);
        //         $num_data = mysqli_num_rows($run_uhod);
        //         if ($num_data < 1) {
        //             $qusod = "INSERT INTO `user_sales_order_details`(`order_id`, `product_id`, `rate`,`case_rate`, `quantity`, `scheme_qty`,`company_id`,`piece_quantity`,`case_quantity`,`secondary_quantity`,`secondary_rate`,`weight`)"
        //                 . " VALUES('$orderid$user_id','$product_id','$rate','$case_rate','$prod_qty','$scheme_qty','$company_id','$piece_quantity','$case_quantity','$secondary_quantity','$secondary_rate','$pro_weight')";


        //         } else {
        //             $qusod = "UPDATE `user_sales_order_details` SET `quantity`='$prod_qty',`rate`='$rate' WHERE order_id='$orderid$user_id' AND product_id='$product_id' AND `company_id` = '$company_id' ";
        //         }

        //         $results = mysqli_query($dbc, $qusod);
                
        //         if ($results) {
        //             $unique_id_array[] = $uid;

        //         }
        //         $p++;
        //     }
        // }

        // if (!empty($callwisereporting)) {
        //     $phonestatus = count($callwisereporting);
        //     $l = 0;
        //     while ($l < $phonestatus) {
        //         $beat_id = $callwisereporting[$l]->location_id;
        //         $discount_before = $callwisereporting[$l]->total_sale_value;
        //         $total_sale_qty = $callwisereporting[$l]->total_sale_qty;
        //         $override_status = $callwisereporting[$l]->override_status;
        //         $order_id = $callwisereporting[$l]->order_id;

        //         $date = $callwisereporting[$l]->date;
        //         $mcc_mnc = $callwisereporting[$l]->mccmnclatcellid;
        //         $time = $callwisereporting[$l]->time;
        //         $location = $callwisereporting[$l]->track_address;
        //         $dealer_id = $callwisereporting[$l]->dealer_id;
        //         $lat_lng = $callwisereporting[$l]->lat_lng;
        //         $lat_lngs = str_replace(' ',',',$lat_lng);

        //         $discount = $callwisereporting[$l]->Discount;
        //         $total_sale_value = $callwisereporting[$l]->Finalvalue;
        //         $retailer_id = $callwisereporting[$l]->retailer_id;
        //         $call_status = $callwisereporting[$l]->call_status;
        //         $remarks = $callwisereporting[$l]->remarks;
        //         $geo_status = $callwisereporting[$l]->geo_status;
        //         $gps_status = $callwisereporting[$l]->gps_status;
        //         $battery_status = $callwisereporting[$l]->battery_status;
        //         $non_productive_id = $callwisereporting[$l]->non_productive_id;

        //         // $user_id=$callwisereporting[$l]->user_id;

        //         $ll = explode(",", $lat_lng);
        //         if ($location == "$$" || $location == ' ') {
        //             $user_location = getLocationByLatLng($ll[0], $ll[1]);
        //         } else {

        //             $user_location = $location;
        //         }
        //         $chk_uso = "SELECT id FROM user_sales_order WHERE order_id='$order_id$user_id'";
        //         $run_uso = mysqli_query($dbc, $chk_uso);
        //         $num_uso = mysqli_num_rows($run_uso);
        //         if ($num_uso < 1) {
        //             $quso = "INSERT INTO `user_sales_order`(`order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`,`call_status`, `total_sale_value`,`discount`,`amount`, `total_sale_qty`,`lat_lng`, `mccmnclatcellid`,`geo_status`,`track_address`, `date`, `time`, `image_name`, `override_status`,`remarks`,`battery_status`,`gps_status`,`reason`,`company_id`,`total_dispatch_qty`,`order_status`)"
        //                 . " VALUES('$order_id$user_id','$user_id','$dealer_id','$beat_id','$retailer_id','$call_status','$total_sale_value','$discount','$discount_before','$total_sale_qty','$lat_lng','$mcc_mnc','$geo_status','$user_location','$date','$time','.jpg','$override_status','$remarks','$battery_status','$gps_status','$non_productive_id','$company_id','0','0')";
        //             //      echo $q;
        //          $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$date','$time','$mcc_mnc','$lat_lng','$user_location','Order Booking',NOW(),'$battery_status','$gps_status','$company_id')";
        //         $rd = mysqli_query($dbc, $qd);
              
                
        //         }
        //         #Update archiv in MTP
        //         $cur_date=date('Y-m-d',strtotime('now'));
        //         $update_mtp="UPDATE monthly_tour_program SET arch=arch+$total_sale_value WHERE person_id=$user_id AND `company_id` = '$company_id' and working_date=$cur_date";
        //         $update_mtp_result = mysqli_query($dbc, $update_mtp);
        //         $result = mysqli_query($dbc, $quso);
        //         if ($result) {
        //             $unique_id_array[] = $order_id;

        //         }
        //         $l++;
        //     }
        // }


        // uncomment if facing issue ends



        if (!empty($PrimarySaleSummary)) {
            //print_r($PrimarySaleSummary);
            $psale = count($PrimarySaleSummary);
            $ps = 0;
            while ($ps < $psale) {
                $order_id = $PrimarySaleSummary[$ps]->order_id;
                $product_id = $PrimarySaleSummary[$ps]->product_id;
                $rate = $PrimarySaleSummary[$ps]->pcs_rate;  //app side sending pcs_rate as pcs rate(dealer rate)
                $quantity = $PrimarySaleSummary[$ps]->quantity;
                $scheme_qty = !empty($PrimarySaleSummary[$ps]->scheme_qty)?$PrimarySaleSummary[$ps]->scheme_qty:'0';
                $case = $PrimarySaleSummary[$ps]->case;
                $pcs = $PrimarySaleSummary[$ps]->pcs;
                $case_rate = $PrimarySaleSummary[$ps]->case_rate;
                $pcs_rate = $PrimarySaleSummary[$ps]->pcs_rate;
                $secondary_qty = !empty($PrimarySaleSummary[$ps]->secondary_qty)?$PrimarySaleSummary[$ps]->secondary_qty:'0';

                $pro_weight = !empty($PrimarySaleSummary[$ps]->pro_weight)?$PrimarySaleSummary[$ps]->pro_weight:'0';


                 $q = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`,`cases`,`pcs`,`pr_rate`,`company_id`,`secondary_qty`,`weight`) "
                    . " VALUES('$order_id$ps','$order_id$user_id','$product_id','$rate','$quantity','$scheme_qty','$case','$pcs','$case_rate','$company_id','$secondary_qty','$pro_weight')";
                    // die;
                $result = mysqli_query($dbc, $q);

                $q1 = "INSERT INTO `purchase_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`,`cases`,`pcs`,`pr_rate`,`company_id`) "
                    . " VALUES('$order_id$ps','$order_id$user_id','$product_id','$rate','$quantity','$scheme_qty','$case','$pcs','$case_rate','$company_id')";
                    // die;
                $result1 = mysqli_query($dbc, $q1);


                if ($result) {
                    $unique_id_array[] = $order_id;

                }
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
                $new_work_time = date('H:i:s', strtotime($date_time));
                $lat = $Primarysaledetail[$pd]->lat;
                $lng = $Primarysaledetail[$pd]->lng;
                $lat_lng = $lat.','.$lng;
                $address = $Primarysaledetail[$pd]->address;
                $gps_status = $Primarysaledetail[$pd]->gps_status;
                $mcc_mnc_lac_cellid = !empty($Primarysaledetail[$pd]->mcc_mnc_lac_cellid)?$Primarysaledetail[$pd]->mcc_mnc_lac_cellid:'NA';
                $battery_status = $Primarysaledetail[$pd]->battery_status;
                $dispatch_through = !empty($Primarysaledetail[$pd]->dispatch_through)?$Primarysaledetail[$pd]->dispatch_through:'';
                $destination = !empty($Primarysaledetail[$pd]->destination)?$Primarysaledetail[$pd]->destination:'';
                $comment = !empty($Primarysaledetail[$pd]->comment)?$Primarysaledetail[$pd]->comment:'';
                //$ch_date=$Primarysaledetail[$pd]->ch_date;


                $q = "INSERT INTO `user_primary_sales_order`(`id`,`order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`,`lat`,`lng`,`address`,`company_id`,`dispatch_through`,`destination`,`comment`)"
                    . " VALUES('$order_id$user_id','$order_id$user_id','$dealer_id','$created_date','$user_id','$sale_date',NOW(),'$date_time','$lat','$lng','$address','$company_id','$dispatch_through','$destination','$comment')";

                $q12 = "INSERT INTO `purchase_order`(`id`,`order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`,`lat`,`lng`,`address`,`company_id`,`vehicle_id`)"
                    . " VALUES('$order_id$user_id','$order_id$user_id','$dealer_id','$created_date','$user_id','$sale_date',NOW(),'$date_time','$lat','$lng','$address','$company_id','11')";
                $result12 = mysqli_query($dbc, $q12);
//                echo $q;die;
                $primary_order_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$sale_date','$new_work_time','$mcc_mnc_lac_cellid','$lat_lng','$address','Primary Booking',NOW(),'$battery_status','$gps_status','$company_id')";
                $primary_order_track_run = mysqli_query($dbc, $primary_order_track);
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;

                }
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
                $q = "INSERT INTO user_complaint(`person_id`,`message`,`role_id`,`image_name`,`complaint_type`,`dealer_retailer_id`,`complaint_from`,`order_id`,`date_time`,`company_id`) VALUES('$user_id','$message','$role_id','$image_name','$comp_type','$dealer_retailer_id','$feedback_form','$order_id','$date_time','$company_id')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;

                }
                $q1 = "INSERT INTO complaint(`user_id`,`consumer_name`,`consumer_contact`,`complaint`,`role_id`,`image_name`,`complaint_type`,`dealer_retailer_id`,`action`,`complaint_id`,`date`,`company_id`) VALUES('$user_id','$c_name','$c_contact','$message','$role_id','$image_name','$comp_type','$dealer_retailer_id','0','$order_id','$date_time','$company_id')";
                $result = mysqli_query($dbc, $q1);
                if ($result) {
                    $unique_id_array[] = $order_id;

                }
                $ct++;
            }
        }

        if (!empty($createcustomer)) {
            $customer = count($createcustomer);
            $cc = 0;
            $cc_id = 1;
            $sequence_id_fetch = "SELECT retailer_code from retailer where company_id = $company_id order by retailer_code DESC limit 1 ";
            $sequence_id_run = mysqli_query($dbc, $sequence_id_fetch);
            $sequence_id = mysqli_fetch_object($sequence_id_run);
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
                $battery_status = $createcustomer[$cc]->battery_status;
                $gps_status = $createcustomer[$cc]->gps_status;
                $lat_lng = $lat . ',' . $long;
                $date_time = $cr_date.' '.$cr_time;
                $ll = explode(",", $lat_lng);
                if ($location == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $location;
                }
               
                // echo $sequence_id->retailer_code;
                // print_r($sequence_id->retailer_code); die;

               $sequence = ($sequence_id->retailer_code)+$cc_id;
               // die;
                $q = "INSERT INTO retailer(`id`,`retailer_code`,`class`,`created_by_person_id`,`dealer_id`,`address`,`track_address`,`outlet_type_id`,`lat_long`,`name`,`location_id`,`image_name`,`mncmcclatcellid`,`pin_no`,`email`,`created_on`,`tin_no`,`contact_per_name`,`landline`,`battery_status`,`gps_status`,`company_id`) " . "VALUES('$id','$sequence','$category','$user_id','$d_code','$user_location','$full_address','$r_type','$lat_lng','$r_name','$l_code','$image_name','$mccmnclaccellid','$r_pin_no','$r_email','$date_time','$r_tin','$cont_name','$r_contact_no','$battery_status','$gps_status','$company_id')";
                $result = mysqli_query($dbc, $q);

                // $sequence_retailer = "INSERT INTO `user_retailer_sequence`(`sequence_id`, `retailer_id`, `user_id`, `dealer_id`, `company_id`, `created_at`) VALUES('$sequence','$id','$user_id','$d_code','$company_id',NOW())";
                // $sequence_retailer_run = mysqli_query($dbc, $sequence_retailer);

                $retailer_track="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$cr_date','$cr_time','$mccmnclaccellid','$lat_lng','$full_address','Retailer Creation',NOW(),'$battery_status','$gps_status','$company_id')";
                $retailer_track_run = mysqli_query($dbc, $retailer_track);

                if ($result) {
                    $unique_id_array[] = $order_id;

                }
                if ($result) 
                {
                    // $q = "INSERT INTO user_dealer_retailer(`user_id`,`dealer_id`,`retailer_id`,`seq_id`,`company_id`) VALUES('$user_id','$d_code','$id',$seq_no,'$company_id')";
                    // mysqli_query($dbc, $q);

//                     function getSeniorList($user_id,$dbc,$j=0)
//                     { 
//                         global $dbc;

//                         if ($user_id==1){return $_SESSION['idArr'];}
//                         #role array
// //                        $rol=[12,17,34,39,40,42,46];
// //                        $in = join(',', array_fill(0, count($rol), '?'));
//                         $queryx = "SELECT a.id as id,CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name) as name from person a INNER JOIN  person b ON a.id=b.person_id_senior WHERE b.id=$user_id and b.role_id IN (12,17,34,39,40,42,46)";
//                         $qrx = mysqli_query($dbc, $queryx) or die(mysqli_error($dbc));
//                         if (mysqli_num_rows($qrx) > 0) {
//                             while ($rowx = mysqli_fetch_assoc($qrx)) {
//                                 if ($rowx['id']>1)
//                                 {
//                                     $i=['id'=>$rowx['id'],'name'=>$rowx['name']];
//                                     $_SESSION['idArr'][$j]=$i;
//                                     $j++;

//                                    // getSeniorList($rowx['id'],$dbc,$j);
//                                 }
//                             }
//                         }

//                         return $_SESSION['idArr'];
//                     }
                    // $ox1=getSeniorList($user_id,$dbc);
                    // if(!empty($ox1)){
                    //     $custom_seniors=$ox1;
                    // }else{
                    //     $custom_seniors=array();
                    // }
                   // $custom_seniors=!empty($ox1)?$ox1:[];


                    // if (!empty($custom_seniors))
                    // {
                    //     foreach ($custom_seniors as $cs)
                    //     {
                    //         $q = "INSERT INTO user_dealer_retailer(`user_id`,`dealer_id`,`retailer_id`,`seq_id`) VALUES('$cs[id]','$d_code','$id',$seq_no)";
                    //         mysqli_query($dbc, $q);
                    //     }
                    // }
                }
                $cc++;
                $cc_id++;
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
                $q = "INSERT INTO sale_reason_remarks(`user_id`,`retailer_id`,`order_id`,`sale_remarks`,`date`,`time`,`company_id`) VALUES('$user_id','$retailer_id','$order_id','$reason_text','$date','$time','$company_id')";
                $result = mysqli_query($dbc, $q);
                if ($result) {
                    $unique_id_array[] = $order_id;

                }
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

                $q = "INSERT INTO `monthly_tour_program`(`company_id`,`person_id`, `working_date`, `dayname`, `working_status_id`, `dealer_id`,`town`, `locations`, `total_calls`, `total_sales`, `ss_id`, `travel_mode`, `from`, `to`, `travel_distance`, `category_wise`, `mobile_save_date_time`,`upload_date_time`,`pc`,`rd`,`collection`,`primary_ord`,`new_outlet`,`any_other_task`)"
                    . " VALUES('$company_id','$user_id','$working_date','$dayname','$working_status_id','$dealer_id','$town_id','$locations','$total_calls','$total_sales','$ss_id','$travel_mode','$from','$to','$travel_distance','$category_wise','$mobile_save_date_time',NOW(),'$pc','$rd','$collection','$primary_ord','$new_outlet','$any_other_task')";
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
                $battery_status = $tracking[$tr]->battery_status;
                $gps_status = $tracking[$tr]->gps_status;
                $notifi_status = !empty($tracking[$tr]->notifi_status)?$tracking[$tr]->notifi_status:'0';
                $ll = explode(",", $lat_lng);
                if ($track_address == '$$') {
                    $user_location = getLocationByLatLng($ll[0], $ll[1]);
                } else {

                    $user_location = $track_address;
                }
                $q2 = "SELECT count(user_id) as num from user_daily_tracking where user_id='" . $user_id . "' AND "
                    . " DATE_FORMAT(track_date,'%Y-%m-%d') ='" . $date . "' AND track_time= '" . $time . "' AND `company_id` = '$company_id' ";
                $sql = mysqli_fetch_assoc(mysqli_query($dbc, $q2));
                $num = $sql['num'];
                if ($num < 1) {
                    $q = "INSERT INTO `user_daily_tracking`(`user_id`, `track_date`, `track_time`,`mnc_mcc_lat_cellid`, `lat_lng`, `track_address`,`battery_status`,`gps_status`,`company_id`,`notifi_status`)"
                        . " VALUES('$user_id','$track_date','$track_time','$mnc_mcc_lat_cellid','$lat_lng','$user_location','$battery_status','$gps_status','$company_id','$notifi_status')";
                    $result = mysqli_query($dbc, $q);

                   $qd="INSERT INTO `user_work_tracking`(`user_id`, `track_date`, `track_time`, `mnc_mcc_lat_cellid`, `lat_lng`, `track_address`, `status`, `server_date_time`,`battery_status`,`gps_status`,`company_id`) VALUES ('$user_id','$track_date','$track_time','$mnc_mcc_lat_cellid','$lat_lng','$user_location','Tracking',NOW(),'$battery_status','$gps_status','$company_id')";
                $rd = mysqli_query($dbc, $qd);
            
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
                $reason_type_id = $damage_detail[$dd]->reason_type_id;

                $q = "INSERT INTO `damage_replace`(`replaceid`, `return_reason_id`,`user_id`, `dis_code`, `prod_code`, `ret_code`, `prod_qty`, `prod_value`, `date_time`, `location`, `reason`, `mrp`, `task`, `extra_amt`,`company_id`) "
                    . " VALUES('$replaceid','$reason_type_id','$user_id','$dis_code','$prod_code','$ret_code','$prod_qty','$prod_value','$date_time','$location','$reason','$mrp','$task','$extra_amt','$company_id')";
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
                $reason_type_id = $damage_detail_retailer[$ddr]->reason_type_id;

                $q = "INSERT INTO `damage_replace_retailer`(`replaceid`,`return_reason_id`, `user_id`, `dis_code`, `prod_code`, `ret_code`, `prod_qty`, `prod_value`, `date_time`, `location`, `reason`, `mrp`, `task`, `extra_amt`,`company_id`) "
                    . " VALUES('$replaceid','$reason_type_id','$user_id','$dis_code','$prod_code','$ret_code','$prod_qty','$prod_value','$date_time','$location','$reason','$mrp','$task','$extra_amt','$company_id')";
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
                $qryretmer = "UPDATE `retailer` SET `retailer_status`='0',`deactivated_by_user`='$user_id',`deactivated_date_time`=NOW() WHERE `id` IN ($retailer_merge_id_old) AND id != $retailer_merge_id AND `company_id` = '$company_id' ";
                $result_mer = mysqli_query($dbc, $qryretmer);
                $qryretmerge = "INSERT INTO `retailer_merge`( `new_ret_id`, `old_ret_id`, `submit_date`, `submit_time`, `server_date_time`,`company_id`) VALUES ('$retailer_merge_id','$retailer_merge_id_old','$retailer_merge_submit_date','$retailer_merge_submit_time',NOW(),'$company_id')";
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
                //  $qryretres = "UPDATE `user_retailer_sequence` SET `sequence_id`='$retailer_res_seq_new',`updated_at`='$retailer_merge_submit_date $retailer_merge_submit_time',`user_id`='$user_id' WHERE dealer_id='$retailer_dealer_id' AND retailer_id='$retailer_res_id' AND `company_id` = '$company_id' ";
                // // die;

                // $result_res = mysqli_query($dbc, $qryretres);
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

                $mob_qry = "INSERT INTO user_mobile_details( `user_id`, `device_name`, `device_manuf`, `device_version`, `server_date_time`,`company_id`) "
                    . "VALUES('$user_id','$d_name','$d_manu','$d_version','$cur_date','$company_id')";
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


    if($company_id == '43'){

          $url = "http://demo.msell.in/public/btwDistanceCalculate?user_id=$user_id";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

    }







    ob_start();
    ob_clean();
    //$uniqueId = implode(',', $unique_id);
    // print_r($unique_id_array);
    $uniqueId="'". implode("','", $unique_id_array) ."'";
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
