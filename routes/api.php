<?php

use Illuminate\Http\Request;
use App\Company;
use App\User;
use Illuminate\Support\Facades\Auth;


// $company_id = Auth::user()->company_id;
// $route = Company::where('company_id',$company_id)->first();
// dd($route);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::any('otp_send_status', 'API\CompanyRegistrationTrialWebsite@otp_send_status');
Route::any('trial_demo_website_api', 'API\CompanyRegistrationTrialWebsite@trial_demo_website_api');
Route::any('demo_trail_for_mail', 'API\CompanyRegistrationTrialWebsite@demo_trail_for_mail');
# for all company
Route::post('check_user_company', 'API\DemoLoginController@check_user_company');
Route::get('change_lat_lng_mnc_mcc_lat_cellid', 'API\CommonTrackingController@change_lat_lng_mnc_mcc_lat_cellid');
Route::get('change_addr_mnc_mcc_lat_cellid', 'API\CommonTrackingController@change_addr_mnc_mcc_lat_cellid');


#test login detail 
Route::post('login_v1', 'API\LoginControllerVersion1@login_v1');
Route::post('login_parallel_v1', 'API\LoginControllerVersion1@login_parallel_v1');

# url for demo company only 

Route::post('login_demo', 'API\DemoLoginController@login_demo');
Route::post('xotik_login_v1', 'API\DemoLoginController@xotik_login_v1');
Route::post('test_login_demo', 'API\TestLoginController@test_login_demo');

Route::post('register', 'API\UserController@register');
# dashboad data starts here 
Route::post('one_view_junior_data', 'API\JuniorDataController@one_view_junior_data');
Route::post('one_view_date_wise_self_data', 'API\JuniorDataController@one_view_date_wise_self_data');
Route::post('one_view_date_wise_self_data_copy', 'API\JuniorDataController@one_view_date_wise_self_data_copy');
# dashboad data ends here 

Route::post('one_view_junior_data_oyster', 'API\OysterbathDashboardController@one_view_junior_data_oyster');
Route::post('one_view_date_wise_self_data_oyster', 'API\OysterbathDashboardController@one_view_date_wise_self_data_oyster');

Route::post('GeofenceAttandence', 'API\AttendanceApiController@GeofenceAttandence');
Route::post('attendanceSubmit', 'API\AttendanceApiController@attendanceSubmit');
Route::post('attendanceSubmitNew', 'API\AttendanceApiController@attendanceSubmitNew');
Route::post('checkout_submit', 'API\AttendanceApiController@checkout_submit');
Route::post('tracking_mints', 'API\AttendanceApiController@tracking_mints');
Route::post('tracking_submit', 'API\AttendanceApiController@tracking_submit');
// Route::post('user_dealer_beat_retailer_data', 'API\SignInController@user_dealer_beat_retailer_data');
// Route::post('user_dealer_beat_retailer_data_test', 'API\SignInController@user_dealer_beat_retailer_data_test');
Route::post('user_attendance_data', 'API\PerfomanceController@user_attendance_data');
Route::post('overall_ranking_data', 'API\PerfomanceController@overall_ranking_data');
Route::post('fullfilment_data', 'API\SubmissionController@fullfilment_data');
Route::post('primary_sale_submission', 'API\SubmissionController@primary_sale_submission');
Route::post('create_beat', 'API\SubmissionController@create_beat');
Route::post('dealer_fulfillment', 'API\FulfillmentController@dealer_fulfillment');

Route::post('mtp_user_all_data', 'API\CommonController@mtp_data');
Route::post('profile_details_aqua', 'API\CommonController@profile_details_aqua');
Route::post('xotik_mtp_user_all_data', 'API\CommonController@xotik_mtp_data');
Route::post('user_daily_attendance_report', 'API\CommonController@user_daily_attendance_report');
Route::post('no_attendance_report', 'API\CommonController@no_attendance_report');
Route::post('master_details_retailer_beat_ss_personal', 'API\CommonController@master_details_retailer_beat_ss_personal');

Route::post('user_wise_sales_report', 'API\CommonController@user_wise_sales_report');
Route::post('junior_beat_wise_sale', 'API\CommonController@junior_beat_wise_sale');
Route::post('junior_retailer_wise_data', 'API\CommonController@junior_retailer_wise_data');
Route::post('junior_retailer_wise_data_all', 'API\CommonController@junior_retailer_wise_data_all');
Route::post('junior_product_wise_data', 'API\CommonController@junior_product_wise_data');

Route::post('junior_distributor_wise_sale', 'API\CommonController@junior_distributor_wise_sale');
Route::post('return_chat_Api', 'API\CommonController@return_chat_Api');

Route::get('msellNotificationAPI', 'API\CommonController@msellNotificationAPI');




Route::post('juniorDashboard', 'API\CommonController@juniorDashboard');
Route::post('aqualabTrackDetails', 'API\CommonController@aqualabTrackDetails');




Route::post('list_all_dealer_with_sale', 'API\CommonController@list_all_dealer_with_sale');
Route::post('list_all_user_for_dealerid_with_total_sale', 'API\CommonController@list_all_user_for_dealerid_with_total_sale');
Route::post('list_all_locations_for_dealer_id_for_user_id_with_total_sale', 'API\CommonController@list_all_locations_for_dealer_id_for_user_id_with_total_sale');
Route::post('list_all_dealer_for_users_with_total_payment', 'API\CommonController@list_all_dealer_for_users_with_total_payment');
Route::post('list_all_retailer_payment', 'API\CommonController@list_all_retailer_payment');
Route::post('list_dealer_for_user', 'API\CommonController@list_dealer_for_user');
Route::post('dealer_stock_for_user', 'API\CommonController@dealer_stock_for_user');
Route::post('list_all_dealer_payment', 'API\CommonController@list_all_dealer_payment');
Route::post('list_all_dealer_payment_details', 'API\CommonController@list_all_dealer_payment_details');
Route::post('selfExpense', 'API\CommonController@selfExpense');
Route::post('close_of_the_day', 'API\CommonController@close_of_the_day');
Route::post('daily_comments', 'API\CommonController@daily_comments');
Route::post('mordern_trade_meeting', 'API\CommonController@mordern_trade_meeting');
Route::post('general_trade_meeting', 'API\CommonController@general_trade_meeting');
Route::post('market_report_1', 'API\CommonController@market_report_1');
Route::post('daily_reporting', 'API\CommonController@daily_reporting');
Route::post('market_report_2', 'API\CommonController@market_report_2');
Route::post('primary_sale_report', 'API\CommonController@primary_sale_report');
Route::post('update_attendance_page', 'API\DemoLoginController@update_attendance_page');
Route::post('list_all_retailer_for_dealerid_for_locationid_with_total_sale', 'API\CommonController@list_all_retailer_for_dealerid_for_locationid_with_total_sale');
Route::post('list_all_product_for_dealerid_userid_locationid_with_total_sale', 'API\CommonController@list_all_product_for_dealerid_userid_locationid_with_total_sale');
Route::post('incentive_calculation', 'API\IncentiveApiController@incentive_calculation');
Route::post('scheme_retailer_calculation', 'API\SchemeApiController@scheme_retailer_calculation');
Route::post('scheme_dealer_calculation', 'API\SchemeApiController@scheme_dealer_calculation');
Route::post('retailer_stock_report', 'API\CommonController@retailer_stock_report');
Route::post('retailer_comment_report', 'API\CommonController@retailer_comment_report');
Route::post('distributor_stock_report', 'API\CommonController@distributor_stock_report');
Route::post('dms_dealer_retailer_beat_details', 'API\CommonController@dms_dealer_retailer_beat_details');
Route::post('metting_order_booking', 'API\CommonController@metting_order_booking');
Route::post('pending_claim', 'API\CommonController@pending_claim');
Route::post('get_machine_data', 'API\CommonController@get_machine_data');
Route::post('janak_template_product_details', 'API\CommonController@janak_template_product_details');
Route::post('dsr_report_new', 'API\CommonController@dsr_report_new');
Route::post('send_sms_whtsapp', 'API\TestLoginController@send_sms_whtsapp');

Route::post('summary_report', 'API\CommonController@summary_report');
Route::post('ecart_primary_order_details_api', 'API\CommonController@ecart_primary_order_details_api');

Route::post('role_wise_expense_details', 'API\CommonController@role_wise_expense_details');

Route::post('user_tracking_details', 'API\CommonController@user_tracking_details');

Route::post('holiday_check', 'API\CommonController@holiday_check');

Route::post('working_status', 'API\CommonController@working_status');
Route::post('gift_master_details_user_wise', 'API\CommonController@gift_master_details_user_wise');

Route::post('daily_schedule', 'API\CommonController@daily_schedule');

Route::post('outletTypeAndCategory', 'API\CommonController@outletTypeAndCategory');
Route::post('availableStock', 'API\CommonController@availableStock');
Route::post('attendance_module_status', 'API\CommonController@attendance_module_status');



Route::post('dailyreportingSubmit', 'API\SubmissionController@dailyreportingSubmit');
Route::post('createRetailer', 'API\SubmissionController@createRetailer');
Route::post('customer_order_form_aeris', 'API\SubmissionController@customer_order_form_aeris');
Route::post('registerUser', 'API\SubmissionController@registerUser');

Route::post('retailerInfoEdit', 'API\SubmissionController@retailerInfoEdit');



Route::post('juniorTrackDetails', 'API\CommonController@juniorTrackDetails');

Route::post('BtwDistanceDetails', 'API\CommonController@BtwDistanceDetails');


Route::post('pdfDetails', 'API\CommonController@pdfDetails');
Route::post('pdfPrimaryDetails', 'API\CommonController@pdfPrimaryDetails');

Route::post('checkOutWithDsr', 'API\CommonController@checkOutWithDsr');


Route::post('juniorDetailsData', 'API\CommonController@juniorDetailsData');
Route::post('sendMessageToJunior', 'API\CommonController@sendMessageToJunior');
Route::post('DistributorCatalogPdfDetails', 'API\CommonController@DistributorCatalogPdfDetails');
Route::post('UserCatalogPdfDetails', 'API\CommonController@UserCatalogPdfDetails');


Route::post('juniorSaleDetails', 'API\CommonController@juniorSaleDetails');
Route::post('juniorSaleDetailsReport', 'API\CommonController@juniorSaleDetailsReport');

Route::post('distribubtorAnalyticsDetails', 'API\CommonController@distribubtorAnalyticsDetails');
Route::post('userAnalyticsDetails', 'API\CommonController@userAnalyticsDetails');
Route::post('retailerAnalyticsDetails', 'API\CommonController@retailerAnalyticsDetails');
Route::post('beatAnalyticsDetails', 'API\CommonController@beatAnalyticsDetails');


Route::post('filterDetails', 'API\CommonController@filterDetails');

Route::post('purchaseOrderStatus', 'API\CommonController@purchaseOrderStatus');


Route::post('beatAnalysis', 'API\CommonController@beatAnalysis');
Route::post('userWiseDistributorAnalysis', 'API\CommonController@userWiseDistributorAnalysis');

// Route::post('totalCallDetails', 'API\CommonController@totalCallDetails');
// Route::post('productiveCallDetails', 'API\CommonController@productiveCallDetails');
// Route::post('nonProductiveCallDetails', 'API\CommonController@nonProductiveCallDetails');

Route::post('overAllRetailerDetails', 'API\AnalysisController@overAllRetailerDetails');
Route::post('overAllTotalProductiveDetails', 'API\AnalysisController@overAllTotalProductiveDetails');
Route::post('overAllTotalNonProductiveDetails', 'API\AnalysisController@overAllTotalNonProductiveDetails');
Route::post('overAllTotalNotVisitedDetails', 'API\AnalysisController@overAllTotalNotVisitedDetails');

Route::post('overAllBeatDetails', 'API\AnalysisController@overAllBeatDetails');



Route::post('retailerFilter', 'API\AnalysisController@retailerFilter');

Route::post('patanjaliAttendanceAPI', 'API\CommonController@patanjaliAttendanceAPI');
Route::post('isGoldenData', 'API\SubmissionController@isGoldenData');

Route::post('productDescriptionImage', 'API\CommonController@productDescriptionImage');

Route::post('live_tracking_api', 'API\AttendanceApiController@live_tracking_api');






















# send otp  and outlet creation with otp url starts here

Route::post('dealer_submission', 'API\DealerDataController@dealer_submission');

Route::post('order_check_otp', 'API\orderBookingController@order_check_otp');
Route::post('order_submission', 'API\orderBookingController@order_submission');




#sync images starts here added by karan 2019-09-11
Route::post('image_sync', 'API\SyncImagesControllers@image_sync');
#sync images ends here added by karan 2019-09-11
# force full updated api added by karan 2019-09-11
Route::post('force_fully_update', 'API\JuniorDataController@force_fully_update');
Route::post('return_update_status_for_app', 'API\JuniorDataController@return_update_status_for_app');
Route::post('mtp_user_data', 'API\JuniorDataController@mtp_user_data');

# janak company api details starts here 
Route::post('push_sku_layer_detail_janak', 'API\CommonController@push_sku_layer_detail_janak');
# janak company api details ends here 

# dms related routes starts here

Route::post('dms_login', 'API\NewDmsController@dms_login');
Route::post('company_login_dms', 'API\NewDmsController@company_login_dms');
Route::post('dms_company_dealer_list', 'API\NewDmsController@dms_company_dealer_list');
Route::post('dms_counter_sale_report', 'API\NewDmsController@dms_counter_sale_report');
Route::post('dms_dealer_jar_return_report', 'API\NewDmsController@dms_dealer_jar_return_report');
Route::post('dms_product_details', 'API\NewDmsController@dms_product_details');
Route::post('dms_ecart_product_details', 'API\NewDmsController@dms_ecart_product_details');
Route::post('dms_primary_sale_submit', 'API\NewDmsController@dms_primary_sale_submit');
Route::post('dms_banner_images', 'API\NewDmsController@dms_banner_images');
Route::post('dms_total_ecart', 'API\NewDmsController@dms_total_ecart');
Route::post('dms_primary_sale_report', 'API\NewDmsController@dms_primary_sale_report');
Route::post('dms_primary_sale_report_user_wise', 'API\NewDmsController@dms_primary_sale_report_user_wise');
Route::post('dms_counter_sale_submit', 'API\NewDmsController@dms_counter_sale_submit');
Route::post('dms_counter_sale_submit_new', 'API\NewDmsController@dms_counter_sale_submit_new');
Route::post('dms_dealer_stock', 'API\NewDmsController@dms_dealer_stock');
Route::post('dms_receive_product_update', 'API\NewDmsController@dms_receive_product_update');
Route::post('dms_cart_product_update_patanjali', 'API\NewDmsController@dms_cart_product_update_patanjali');
Route::post('dms_cart_product_update', 'API\NewDmsController@dms_cart_product_update');

Route::post('dms_send_empty_jar_data', 'API\NewDmsController@dms_send_empty_jar_data');
Route::post('dms_order_cancel_reason', 'API\NewDmsController@dms_order_cancel_reason');
Route::post('dms_cancel_order_update', 'API\NewDmsController@dms_cancel_order_update');
Route::post('complaint_feedback_array', 'API\NewDmsController@complaint_feedback_array');
Route::post('complaint_feedback_submit', 'API\NewDmsController@complaint_feedback_submit');
Route::post('dms_counter_draft_product_details', 'API\NewDmsController@dms_counter_draft_product_details');
Route::post('dms_draft_product_update', 'API\NewDmsController@dms_draft_product_update');
Route::post('dms_vehicle_type', 'API\NewDmsController@dms_vehicle_type');
Route::post('dms_send_order_enquiry_data', 'API\NewDmsController@dms_send_order_enquiry_data');
Route::post('dms_social_master_data', 'API\NewDmsController@dms_social_master_data');
Route::post('dms_aboutus_data', 'API\NewDmsController@dms_aboutus_data');
Route::post('dms_feedback_form_data_submit', 'API\NewDmsController@dms_feedback_form_data_submit');
Route::post('dms_feedback_form_data', 'API\NewDmsController@dms_feedback_form_data');
Route::post('dms_feedback_form_data_update', 'API\NewDmsController@dms_feedback_form_data_update');
Route::post('dms_dealer_profile_details', 'API\NewDmsController@dms_dealer_profile_details');
Route::post('dms_notification_data', 'API\NewDmsController@dms_notification_data');
Route::post('dms_dealer_profile_details_submit', 'API\NewDmsController@dms_dealer_profile_details_submit');
Route::post('dms_dealer_bank_details_submit', 'API\NewDmsController@dms_dealer_bank_details_submit');
Route::post('dms_dealer_image_submit', 'API\NewDmsController@dms_dealer_image_submit');
Route::post('dms_cart_product_edit', 'API\NewDmsController@dms_cart_product_edit');
Route::post('dms_plant_details', 'API\NewDmsController@dms_plant_details');
Route::post('dms_plant_stock_details', 'API\NewDmsController@dms_plant_stock_details');
Route::post('dms_contacts_custom', 'API\NewDmsController@dms_contacts_custom');
Route::post('dms_inner_notification_details', 'API\NewDmsController@dms_inner_notification_details');
Route::post('dms_dealer_beat', 'API\NewDmsController@dms_dealer_beat');
Route::post('dms_dealer_beat_retailer', 'API\NewDmsController@dms_dealer_beat_retailer');
Route::post('dms_secondary_supply', 'API\NewDmsController@dms_secondary_supply');

Route::post('dms_distributor_wise_details', 'API\NewDmsController@dms_distributor_wise_details');

Route::post('dms_social_link_data', 'API\NewDmsController@dms_social_link_data');

// Route::post('dms_counter_sale_submit', 'API\NewDmsController@dms_counter_sale_submit');




Route::post('dms_dealer_detail', 'API\NewDmsController@dms_dealer_detail');
Route::post('dms_retailer_detail', 'API\NewDmsController@dms_retailer_detail');


Route::post('attendenceDetails', 'API\NewDmsController@attendenceDetails');
Route::post('gift_scheme_details', 'API\NewDmsController@gift_scheme_details');
Route::post('gift_scheme_submit', 'API\NewDmsController@gift_scheme_submit');
Route::post('dealerBalanceStock', 'API\NewDmsController@dealerBalanceStock');
Route::post('ssBalanceStock', 'API\NewDmsController@ssBalanceStock');







Route::post('retailer_dealer_stock', 'API\RetailerDmsControllers@retailer_dealer_stock');
Route::post('retailer_purchase_order_submit', 'API\RetailerDmsControllers@retailer_purchase_order_submit');
Route::post('retailer_product_details', 'API\RetailerDmsControllers@retailer_product_details');
Route::post('retailer_purchase_order_report_btw', 'API\RetailerDmsControllers@retailer_purchase_order_report_btw');
Route::post('retailer_order_to_dealer_btw', 'API\RetailerDmsControllers@retailer_order_to_dealer_btw');

Route::post('retailer_counter_sale_submit', 'API\RetailerDmsControllers@retailer_counter_sale_submit');
Route::post('retailer_counter_draft_product_details', 'API\RetailerDmsControllers@retailer_counter_draft_product_details');
Route::post('retailer_total_ecart', 'API\RetailerDmsControllers@retailer_total_ecart');
Route::post('retailer_ecart_product_details', 'API\RetailerDmsControllers@retailer_ecart_product_details');
Route::post('retailer_action_list_btw', 'API\RetailerDmsControllers@retailer_action_list_btw');
Route::post('sumbit_status_action_retailer_btw', 'API\RetailerDmsControllers@sumbit_status_action_retailer_btw');


Route::post('submitOpeningStock', 'API\MyntraDmsController@submitOpeningStock');

Route::post('myntraCounterSale', 'API\MyntraDmsController@myntraCounterSale');











// Route::post('dms_dealer_beat', 'API\DmsController@dms_dealer_beat');
// Route::post('dms_dealer_beat_retailer', 'API\DmsController@dms_dealer_beat_retailer');
// Route::post('dms_direct_supply', 'API\DmsController@dms_direct_supply');
// Route::post('brand_details', 'API\DmsController@brand_details');
// Route::post('layer_retailer_for_dealer', 'API\DmsController@layer_retailer_for_dealer');
// Route::post('retailer_order_to_dealer', 'API\DmsController@retailer_order_to_dealer');
// Route::post('retailer_order_to_dealer_btw', 'API\DmsController@retailer_order_to_dealer_btw');
// // Route::post('dms_scheme_dealer', 'API\DmsController@dms_scheme_dealer');


# dms related routes ends here 


#retailer login and outlet related api starts here
Route::post('send_sms', 'API\RetailerDataController@send_sms');
Route::post('retailer_check_otp', 'API\RetailerDataController@retailer_check_otp');
Route::post('verify_retailer', 'API\RetailerDataController@verify_retailer');
Route::post('retailer_submission', 'API\RetailerDataController@retailer_submission');
Route::post('retailer_login', 'API\RetailerDataController@retailer_login');
Route::post('layer_for_retailer_signup', 'API\RetailerDataController@layer_for_retailer_signup');
Route::post('retailer_otp_for_signup', 'API\RetailerDataController@retailer_otp_for_signup');
Route::post('verify_retailer_for_signup', 'API\RetailerDataController@verify_retailer_for_signup');
Route::post('retailer_submission_btw', 'API\RetailerDataController@retailer_submission_btw');
Route::post('list_aeris', 'API\CommonController@list_aeris');
Route::post('notification_update_msell', 'API\CommonController@notification_update_msell');

Route::post('aquaDashboard', 'API\CommonController@aquaDashboard');



#retailer login ends here 

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
