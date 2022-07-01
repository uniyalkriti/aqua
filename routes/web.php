<?php

/* 
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// DMS part routes start here

Route::get('tracking_details_custom_details', 'analyticsController@tracking_details_custom_details');
Route::get('welcome', 'DMSHomeController@index');
Route::get('report_welcome', 'DMSReportController@index');
Route::get('Signup', 'DmsRegistrationController@signup');
Route::post('DmsSignup', 'DmsRegistrationController@dms_signup');

Route::get('phpInfo', 'WebviewController@phpInfo');



Route::resource('Order-details', 'DMSQuickOrderController');
    Route::resource('Scheme-details', 'DMSSchemeControllers');
    Route::get('check_scheme', 'DMSQuickOrderController@check_scheme');
    Route::get('download_image', 'DMSHomeController@download_image');
    Route::post('send_order_to_erp', 'DMSQuickOrderController@send_order_to_erp');
    Route::get('autocomplete_search_url_dms', 'DMSQuickOrderController@autocomplete_search_url_dms')->name('autocomplete_search_url_dms');
    Route::resource('dms-payment-details', 'PaymentDetailsController');
    Route::post('store_final', 'DMSQuickOrderController@store_final')->name('store_final');
    Route::post('dms_return_rate_on_the_behalf_of_product', 'DMSQuickOrderController@dms_return_rate_on_the_behalf_of_product')->name('dms_return_rate_on_the_behalf_of_product');
    Route::post('return_cart_data_for_modal', 'DMSQuickOrderController@return_cart_data_for_modal')->name('return_cart_data_for_modal');
    Route::get('dealer_scheme', 'DMSSchemeControllers@dealer_scheme');
    Route::post('dealer_scheme_admin', 'DMSSchemeControllers@dealer_scheme');
    Route::get('admin_scheme_details', 'DMSSchemeControllers@admin_scheme_details');
    // Route::get('credit_debit_notes', 'DMSQuickOrderController@credit_debit_notes')->name('credit_debit_notes');

    Route::get('credit_debit_notes', 'DMSQuickOrderController@credit_debit_notes')->name('credit_debit_notes');
    Route::get('invoice-details', 'DMSQuickOrderController@invoice_details')->name('invoice_details');
    Route::get('dms_dealer_dashboard', 'DMSQuickOrderController@dms_dealer_dashboard')->name('dms_dealer_dashboard');
    Route::get('edit_index', 'DMSDealerController@edit_index')->name('edit_index');
    Route::get('update_dealer_details_tab', 'DMSDealerController@update_dealer_details_tab')->name('update_dealer_details_tab');

    Route::get('saleStatement', 'DMSQuickOrderController@saleStatement')->name('saleStatement');
    Route::post('saleStatementAjax', 'DMSInvoiceDetailsController@saleStatementAjax')->name('saleStatementAjax');



    Route::get('saleStatementEthical', 'DMSQuickOrderController@saleStatementEthical')->name('saleStatementEthical');
    Route::post('saleStatementEthicalAjax', 'DMSInvoiceDetailsController@saleStatementEthicalAjax')->name('saleStatementEthicalAjax');



    Route::get('/takeActionForCreditDebitNotes', 'DMSQuickOrderController@takeActionForCreditDebitNotes');


    
    Route::post('credit_debit_notes_ajax', 'DMSQuickOrderController@credit_debit_notes_ajax')->name('credit_debit_notes_ajax');
    Route::post('dms_order_delete_function', 'DMSQuickOrderController@dms_order_delete_function');
    Route::get('dmsforpdf', 'DMSQuickOrderController@dmsforpdf');
    Route::get('change_date', 'DMSReportDealerController@change_date');
    Route::get('short_item_list_report', 'DMSReportDealerController@short_item_list_report');
    Route::post('short_item_list_report_ajax', 'DMSReportDealerController@short_item_list_report_ajax');
    Route::post('item_detailer_short_item', 'DMSReportDealerController@item_detailer_short_item');
    Route::get('dealer_sale_details_report', 'DMSReportDealerController@dealer_sale_details_report');
    Route::post('dealer_sale_details_report_ajax', 'DMSReportDealerController@dealer_sale_details_report_ajax');

    // Route::get('invoice-details', function () {
    //     return view('DMS/InvoiceDetails.index');
    // });
    Route::post('invoice_details_ajax', 'DMSInvoiceDetailsController@invoice_details_ajax')->name('invoice_details_ajax');
    Route::get('return_details_invoice_order_id', 'DMSInvoiceDetailsController@return_details_invoice_order_id')->name('return_details_invoice_order_id');
    Route::get('dms_invoice_export_csv', 'DMSInvoiceDetailsController@dms_invoice_export_csv')->name('dms_invoice_export_csv');
    Route::get('export_invoice_details_pdf', 'DMSInvoiceDetailsController@export_invoice_details_pdf')->name('export_invoice_details_pdf');

    Route::get('otc', 'DMSQuickOrderController@otc');
    Route::get('ot2', 'DMSQuickOrderController@ot2');
    Route::get('ethical', 'DMSQuickOrderController@ethical');

    Route::get('JPS', 'DMSQuickOrderController@JPS');
    Route::get('JP2', 'DMSQuickOrderController@JP2');


    Route::get('fmcg', 'DMSQuickOrderController@fmcg');
    Route::resource('dms_dealer', 'DMSDealerController');
    Route::post('dms_get_dealer_person_details', 'DMSDealerController@dms_get_dealer_person_details');
    Route::post('dms_dealer_send_mail', 'DMSDealerController@dms_dealer_send_mail')->name('dms_dealer_send_mail');
    
// DMS part routes ends here

Route::get('imagesWebview', 'WebviewController@imagesWebview');


Route::get('expense_webview', 'WebviewController@expenseList');
Route::get('expense_approve/{id}/{user_id}/{company_id}', 'WebviewController@expense_approve');
Route::get('update_expense_approval', 'WebviewController@update_expense_approval');
Route::get('mail_sent_close', 'MailController@mail_sent');
    Route::get('download_image', 'MailController@download_image');
// Route::get('btwMailSent', 'btwMailController@btwMailSent');
// Route::get('btwTourProgramMailSent', 'btwMailController@btwTourProgramMailSent');
// Route::get('btwAutoMail', 'MailController@btwAutoMail');
// Route::get('btwDailySalesReport', 'btwMailController@btwDailySalesReport');
// Route::get('btwManagerWiseSale', 'btwMailController@btwManagerWiseSale');
Route::get('btwDistanceCalculate', 'DistanceCalculateController@btwDistanceCalculate');
Route::get('btwSubStateDSR', 'btwMailController@btwSubStateDSR'); // running
Route::get('btwManagerDailyReporting', 'btwMailController@btwManagerDailyReporting'); // running
Route::get('btwManagerUserDailyReport', 'btwMailController@btwManagerUserDailyReport');
Route::get('btwManagerUserDailyReportTest', 'btwMailController@btwManagerUserDailyReportTest');
Route::get('btwManagerReportToEveryOne', 'btwMailController@btwManagerReportToEveryOne'); // running
Route::put('newClientRequest', 'NewClientRequestController@newClientRequest');

Route::get('hitkaryMailSent', 'MailController@hitkaryMailSent'); // running


Route::get('btwManagerReportToEveryOneForTest', 'NewClientRequestController@btwManagerReportToEveryOneForTest'); // running

Route::get('csvToJson', 'CsvToJsonController@csvToJson');
Route::post('UploadCsvToJsonData', 'CsvToJsonController@UploadCsvToJsonData');



/// notifications routes starts
Route::get('checkOutNotification', 'NotificationController@checkOutNotification');
Route::get('oysterFollowUpNotification', 'NotificationController@oysterFollowUpNotification');

Route::get('managerNotification', 'NotificationController@managerNotification');

// notification routes ends
Route::get('btwManualDistanceCalculatenew', 'DistanceCalculateController@btwManualDistanceCalculatenew');

Route::get('janakSalesOrderDetailsScript', 'CommonScriptController@janakSalesOrderDetailsScript');










Route::get('/', function () {
    return redirect('login');
});
Route::get('activeLogin', function () {
    return view('auth.loginInfo');
});
 
Auth::routes();

Route::get('/home', 'HomeControllerNew@index')->name('home');
Route::get('/testhome', 'HomeControllerNew@home_test')->name('home');
Route::get('/test_vedio/{id}', 'HomeControllerNew@test_vedio')->name('test_vedio');
Route::get('/TraningModule', 'HomeControllerNew@test_vedio')->name('test_vedio');
Route::get('/TraningModule/{id}', 'HomeControllerNew@TraningModuleWork')->name('test_vedio');
// Route::get('testhome', 'TestHomeController@index')->name('testhome');
Route::get('retailer_create_code', 'TestHomeController@retailer_create_code')->name('retailer_create_code'); // for update retiler code 

Route::get('/retailerAnalytics', 'analyticsController@retailerAnalytics');
Route::get('getDateFormat', 'analyticsController@getDateFormat');

Route::get('getBeatWiseAnalysis', 'analyticsController@getBeatWiseAnalysis');

Route::get('getBeatWiseAnalysisGraph', 'analyticsController@getBeatWiseAnalysisGraph');





Route::group(['middleware' => ['auth']], function () {

    Route::post('get_year_wise_data', 'HomeControllerNew@get_year_wise_data');
    Route::post('get_year_wise_data_product', 'HomeControllerNew@get_year_wise_data_product');
    Route::post('get_year_wise_data_product_cat_wise', 'HomeControllerNew@get_year_wise_data_product_cat_wise');
    Route::post('get_month_wise_data_user_wise', 'HomeControllerNew@get_month_wise_data_user_wise');
    Route::get('/employeeDashboard', 'EmployeeController@index')->name('employeeDashboard');
    Route::get('performanceDashboad', 'PanelPerfomanceDashboardController@index')->name('performanceDashboad');
    Route::get('get_role_id_sale_value', 'PanelPerfomanceDashboardController@get_role_id_sale_value')->name('get_role_id_sale_value');
    Route::get('get_state_wise_sale', 'PanelPerfomanceDashboardController@get_state_wise_sale')->name('get_state_wise_sale');
    Route::get('get_location_1_id_sale_value', 'PanelPerfomanceDashboardController@get_location_1_id_sale_value')->name('get_location_1_id_sale_value');
    Route::get('get_user_details_perfomance', 'PanelPerfomanceDashboardController@get_user_details_perfomance')->name('get_user_details_perfomance');
    #user-management
    Route::resource('user-management', 'UserManagementController');

     Route::get('getDayWisePrimary', 'HomeControllerNew@getDayWisePrimary');
    Route::get('getMonthWisePrimary', 'HomeControllerNew@getMonthWisePrimary');

    Route::get('getDealerSecondarySales', 'DealerController@getDealerSecondarySales');
    Route::get('getDealerPrimarySales', 'DealerController@getDealerPrimarySales');
    Route::get('getDealerSKUDetails', 'DealerController@getDealerSKUDetails');
    Route::get('getDealerRetailerDetails', 'DealerController@getDealerRetailerDetails');

    #Dealer
    Route::resource('distributor', 'DealerController');
    Route::post('get_dealer_person_details', 'DealerController@get_dealer_person_details');
    #Add Distributor User
    Route::post('addDealerUser','DealerController@addDealerUser')->name('addDealerUser');
    Route::post('search_url_details','NewAjaxController@search_url_details');
    Route::get('autocomplete_search_url','NewAjaxController@autocomplete_search_url')->name('autocomplete_search_url');
    #Update Distributor User
    Route::post('updateDealerUser','DealerController@updateDealerUser')->name('updateDealerUser');

    #Retailer
    Route::resource('retailer', 'RetailerController');
    Route::get('retailer_map', 'RetailerController@retailerMap');
    Route::get('get_statewise_user', 'RetailerController@getUser');

     Route::get('getRetailerSecondarySales', 'RetailerController@getRetailerSecondarySales');
    Route::get('getRetailerSkuDetails', 'RetailerController@getRetailerSkuDetails');

     Route::get('getRetailerSecondarySalesDateSku', 'RetailerController@getRetailerSecondarySalesDateSku');


    #Catalog 1
    Route::resource('catalog_1', 'Catalog1Controller');
    #Catalog 2
    Route::resource('catalog_2', 'Catalog2Controller');
    #Catalog 3
    Route::resource('catalog_3', 'Catalog3Controller');

    #product rate list starts here 
    Route::resource('product_rate_list', 'ProductRateListController');
    Route::resource('product_rate_list_template', 'ProductRateListControllerTemplate');
    Route::get('product_rate_list_template_test1', 'ProductRateListControllerTemplate@testIndex');
    Route::get('productRateListTemplateFormat', 'ProductRateListControllerTemplate@productRateListTemplateFormat');
    Route::post('UploadTemmplate', 'ProductRateListControllerTemplate@UploadTemmplate');

    Route::resource('template', 'TemplateController');
    Route::resource('Gift-Master', 'GiftControllerMaster');
    Route::resource('plant', 'PlantController');
    Route::post('dms_plant_stock_submit', 'PlantController@dms_plant_stock_submit');
    Route::resource('vehicle_details', 'VehicleDetailsTypeController');

    Route::resource('division_master', 'DivisionMasterController');
    #csa
    Route::resource('csa', 'SsController');
    # distributor dashboard starts here
    Route::resource('stock', 'StockController');
    Route::resource('purchase', 'PurchaseController');
    Route::resource('challan', 'ChallanController');
    Route::resource('saleTrend', 'DistributorTrendController');
    Route::resource('primarSaleDashboard', 'DealerPrimarySaleController');
    Route::resource('payment_collection', 'PaymentCollectionController');
    Route::resource('return_dashboard', 'ReturnController');

    #retailer Dashboard starts here
    Route::resource('retailer_stock_dashboard', 'RetailerStockController');
    Route::resource('merchandise_dashboard', 'MerchandiseController');
    Route::resource('retailer_order_booking', 'RetailerBookingController');
    Route::resource('retailer_payment_collection', 'RetailerPaymentController');
    Route::resource('promotional_request', 'PromotionalRequestController');
    Route::resource('retailer_comment', 'RetailerCommentController');
    Route::resource('rds_dashboard', 'RDSController');
    
    #Routes for Company master 
    Route::resource('company','CompanyController');

    #Get AnyData
    Route::post('getAny', 'NewAjaxController@getAny');


    #for clear imei and active and deactive 
    Route::post('/takeAction', 'NewAjaxController@takeAction');
    Route::post('/changeExpenseStatus', 'NewAjaxController@changeExpenseStatus');
    Route::post('/deleteExpense', 'NewAjaxController@deleteExpense');
    Route::post('/show_expense_log_data', 'NewAjaxController@show_expense_log_data');
    Route::post('/edit_travelling_expense', 'NewAjaxController@edit_travelling_expense');
    Route::get('/submit_expense_edit', 'NewAjaxController@submit_expense_edit');
    Route::post('order_wise_pdf_format', 'NewAjaxController@order_wise_pdf_format');
    Route::post('order_wise_pdf_format_primary', 'AjaxDailyTeamController@order_wise_pdf_format_primary');
    Route::post('/takeActionIsGolden', 'NewAjaxController@takeActionIsGolden');
    


    Route::post('primaryOrderWisePdfFormat', 'NewAjaxController@primaryOrderWisePdfFormat');


    Route::resource('catalog_product', 'CatalogProductController');
    Route::post('productTypeName', 'CatalogProductController@productTypeName');

    #super-stockist Module
    Route::resource('super-stockist', 'SuperStockistController');

    #super-stockist Module
    Route::resource('role', 'RoleController');
    Route::get('role_wise_assign', 'RoleController@role_wise_assign');


    #department
    Route::resource('department', 'DepartmentController');

    #Dealer Ownership (Dealer owner)
    Route::resource('dealerOwnership', 'DealerOwnershipController');

    #Outlet type
    Route::resource('outletType', 'OutletTypeController');

    #category category
    Route::resource('outlet_category', 'OutletCategoryController');

    #daily schedule 
    Route::resource('daily_schedule', 'DailyScheduleController');

    #daily schedule 
    Route::resource('return_type', 'ReturnTypeDamageController');

    #no_sale_reason
    Route::resource('no_sale_reason', 'NoSaleReasonController');
    Route::resource('meeting_type', 'MeetingTypeController');
    Route::resource('product_type', 'productTypeController');
    Route::resource('mtp_status', 'MtpStatusController');
    Route::resource('mtp_not_interested_status', 'MtpNotInterestedStatusController');
    Route::resource('leave_type', 'LeaveTypeController');
    Route::resource('no_contacted_reason', 'NotContactedReasonController');
    Route::resource('type_of_meeting', 'TypeOfMeetingController');

    #manual_attendance_reason
    Route::resource('manual_attendance_reason', 'ManualAttendanceReasonController');

    #weightType
    Route::resource('weightType', 'WeightTypeController');

    #App Modules
    Route::resource('appModule', 'AppModuleController');

    #App Modules
    Route::resource('editAppModule', 'EditAppModuleController');

    #edit url list
    Route::resource('editUrlList', 'EditUrlListController');
    Route::get('get_version_by_company', 'EditUrlListController@get_version_by_company');

    #App Modules
    Route::resource('editAppSubModule', 'EditAppSubModuleController');

    #App Modules
    Route::resource('appSubModule', 'AppSubModuleController');

    #travelType
    Route::resource('travelType', 'TravellingTypeController');

    #merchandiseType
    Route::resource('merchandiseType', 'MerchandiseTypeController');

    #workType
    Route::resource('workType', 'WorkingTypeController');
    Route::resource('roleGroup', 'RoleGroupController');
    Route::resource('TaskOfTheDay', 'TaskOfTheDayController');

    #modules
    Route::resource('Modules', 'AssigningController');
    Route::get('addModules','AssigningController@addModules');
    Route::resource('roleAppAssign', 'RoleAppAssigningController');
    Route::get('RoleAddModules','RoleAppAssigningController@RoleAddModules');

    # for URL Assign
    Route::resource('urlassign', 'UrlAssigningController');
    Route::get('addUrl','UrlAssigningController@addUrl');

    #MAINMENUS
    Route::resource('MainMenu', 'MenuController');
    Route::resource('Menu', 'SubMenuController');
    Route::resource('SubMenus', 'SubsubMenusController');

    Route::get('SubmitWebAssigning', 'WebAssigningController@SubmitWebAssigning');
    Route::resource('webAssigning', 'WebAssigningController');
    //Route::get('AddMenus','MenusAssigningController@addModules');

    Route::get('SubmitWebRoleAssigning', 'WebRoleAssigningController@SubmitWebRoleAssigning');
    Route::resource('webRoleAssigning', 'WebRoleAssigningController');
    Route::POST('check_read_write_permission', 'NewAjaxController@check_read_write_permission');
    

    #gstType
    Route::resource('gstType', 'GstTypeController');

    #Location 1 (country)
    Route::resource('location1', 'Location1Controller');
    #Location 2 (state)
    Route::resource('location2', 'Location2Controller');
    #Location 3 (head-quaters)
    Route::resource('location3', 'Location3Controller');
    #Location 4 (district)
    Route::resource('location4', 'Location4Controller');
    #Location 5 (town)
    Route::resource('location5', 'Location5Controller');
    #Location 6 (pincode)
    Route::resource('location6', 'Location6Controller');
    #Location 7 (beat)
    Route::resource('location7', 'Location7Controller');


     #Location 4 treat as expense town for rajdhani
    Route::resource('locationFourTown', 'LocationFourTownController');



    #Holiday
    Route::resource('holiday', 'HolidayController');
    
    #Interface
    Route::resource('url', 'UrlController');

    #Version
    Route::resource('version', 'VersionController');

    #Url list
    Route::resource('urllist', 'UrlListController');

    #incentive
    Route::resource('incentive', 'IncentiveController');
    Route::resource('scheme','SchemeController');
    Route::resource('productScheme','productSchemeController');
    Route::resource('schemeAssign','SchemeAssignController');
    Route::get('editSchemeDetails', 'productSchemeController@editSchemeDetails');
    

    Route::resource('plan_assign', 'PlanAssignController');
    Route::get('assignPlans','PlanAssignController@assignPlans');
    Route::get('schemeStoreData','SchemeAssignController@schemeStoreData');

    #Report
    Route::get('beat-route', 'ReportController@beatRoute');
    #Market Beat Plan
    Route::get('market-beat-plan', 'ReportController@marketBeatPlan');
    #Tour Program
    Route::get('tour-program', 'ReportController@tourProgram');
    #Daily Attendance
    Route::get('daily-attendance', 'ReportController@dailyAttendance');
    Route::get('manacle_report', function () {
        return view('reports.manacle_reports.index');
    });
    Route::get('manacle_overall_report', 'NewAjaxController@manacle_overall_report');
    #Day wise performance
    Route::get('daily-performance', 'ReportController@dailyPerformance');

    #Get Location
    Route::post('getLocation', 'NewAjaxController@getLocation');
    Route::post('statndard_filter_onchange', 'NewAjaxController@statndard_filter_onchange');
    Route::post('statndard_filter_onchange_for_user', 'NewAjaxController@statndard_filter_onchange_for_user');
    Route::post('statndard_filter_onchange_for_dealer', 'NewAjaxController@statndard_filter_onchange_for_dealer');
    Route::post('getLocationForStandaradFilter', 'NewAjaxController@getLocationForStandaradFilter');
    Route::post('getCatalogForStandaradFilter', 'NewAjaxController@getCatalogForStandaradFilter');


    Route::post('/getBeatDealer', 'NewAjaxController@getBeatDealer');

    #Get CSA
    Route::post('getCSA', 'NewAjaxController@getCSA');

    Route::get('product-investigation', 'ReportController@productInvestigation');

    Route::get('competitors-new-product', 'ReportController@competitorsNewProduct');

    Route::get('complaint-report', 'ReportController@complaintReport');

    Route::get('travelling-expenses', 'ReportController@travellingExpenses');

    Route::get('pending-claim', 'ReportController@pendingClaim');

    Route::get('competitive-price-intelligence', 'ReportController@competitivePriceIntelligence');

    Route::get('new-sd-dist-prospecting', 'ReportController@newSdDistProspecting');
    Route::get('new-sd-dist-prospecting-report', 'NewAjaxController@newSdDistProspectingReport');

    Route::get('aging', 'ReportController@aging');

    Route::get('distributor-stock-status', 'ReportController@distributorStockStatus');

    Route::get('stock-in-hand', 'ReportController@stockInHand');

    Route::get('month_s_primary_and_secondary_sales_plan', 'ReportController@monthSPrimaryAndSecondarySalesPlan');

    Route::get('ucdp', 'ReportController@ucdp');

    Route::get('board-review', 'ReportController@boardReview');

    Route::get('rs-wise-secondary-sales', 'ReportController@rsWiseSecondarySales');

    Route::get('rsm-asm-so-performance', 'ReportController@rsmAsmSoPerformance');

    Route::get('distributor-performance', 'ReportController@distributorPerformance');

    Route::get('sales-trends', 'ReportController@salesTrends');

//    Route::get('outlet-opening-status', 'ReportController@outletOpeningStatus');

    Route::get('sales-review', 'ReportController@salesReview');

    Route::get('feedback', 'ReportController@feedback');
    Route::post('getAllVersion', 'NewAjaxController@getAllVersion');

    Route::get('payment-details', 'ReportController@paymentDetails');

    Route::get('pending-claim', 'ReportController@pendingClaim');

    Route::get('isr-so-tgt-month', 'ReportController@isrSoTgtMonth');

    Route::get('tour-program-report', 'NewAjaxController@tourProgramReport');

    #time-report starts here 
    Route::get('time-report', 'ReportController@timeAttd');
    Route::get('time-attd-report', 'AjaxAttdController@timeAttdReport');

     #time-report for btw starts here 
    Route::get('ctime-report', 'ReportController@timeAttdBtw');
    Route::get('ctime-attd-report', 'AjaxAttdController@timeAttdBtwReport');

    #time-sale-report starts here 
    Route::get('time-sale-report', 'ReportController@time_report');
    Route::get('time_sale_report', 'NewAjaxController@time_sale_report');

    #get state wise isr and so
    Route::post('get_state_isrso', 'NewAjaxController@getStateIsrsSo');
###
    Route::get('daily-attendance-report', 'AjaxAttdController@dailyAttendanceReport');

    Route::get('market-beat-plan-report', 'NewAjaxController@marketBeatPlanReport');

    Route::get('payment-details-report', 'NewAjaxController@paymentDetailsReport');

    Route::post('get_beat', 'NewAjaxController@beatMultiple');

    #Action for multiple distributor select
    Route::post('get_distributor', 'NewAjaxController@distributorMultiple');

    #get user data by role
    Route::post('get_user_by_role', 'NewAjaxController@getUserByRole');

    #Action for listing pages
    Route::post('/EnableAction', 'UserController@EnableAction');
    Route::post('/deleteAction', 'UserController@deleteAction');
    Route::get('/junior_list_assign_to_senior', 'UserController@junior_list_assign_to_senior');
    Route::post('get_immediate_junior_list', 'UserController@get_immediate_junior_list');
    Route::get('junior_list_submit_for_new_user', 'UserController@junior_list_submit_for_new_user');
    Route::get('delete_user_details', 'UserController@delete_user_details');

    Route::post('/EnableHolidayAction', 'UserController@EnableHolidayAction');

    Route::get('getUserProductiveCallModal', 'UserController@getUserProductiveCallModal');

    Route::get('getUserNonProductiveCallModal', 'UserController@getUserNonProductiveCallModal');
    Route::get('getUserSecondarySaleModal', 'UserController@getUserSecondarySaleModal');
    Route::get('getNewRetailersDetailsModal', 'UserController@getNewRetailersDetailsModal');
    Route::get('getOutletCoverageModal', 'UserController@getOutletCoverageModal');
    Route::get('getPrimarySalesModal', 'UserController@getPrimarySalesModal');
    Route::get('getSKUSalesModal', 'UserController@getSKUSalesModal');
    Route::get('TotalBeatModal', 'UserController@TotalBeatModal');

    Route::get('/assign_mult_states', 'UserController@assign_mult_states');
    



    
    #Get User
    Route::post('getUser', 'NewAjaxController@getUser');

    #get Distributor
    Route::post('getDistributor', 'NewAjaxController@getDistributor');

    #get Senior
    Route::post('getSenior', 'NewAjaxController@getSenior');

    #Senior name
    Route::post('/senior_name', 'NewAjaxController@senior_name');

    Route::post('/city_wise_distributor', 'NewAjaxController@city_wise_distributor');

    #location 3 data
    Route::post('/cities', 'NewAjaxController@cities');
    #location 4 data
    Route::post('/cities_location4', 'NewAjaxController@cities_location4');
    #location 7 data
    Route::post('location7_data', 'NewAjaxController@location7');

    #user sale report starts here
    Route::get('user-sale', 'ReportController@userSales');
    Route::get('user-sale-report', 'NewAjaxController@userSalesReport');
     Route::post('orderDetails', 'NewAjaxController@orderDetails');
    Route::get('orderDetailsUpdate', 'NewAjaxController@orderDetailsUpdate');
    Route::post('/takeActionForOrder', 'NewAjaxController@takeActionForOrder');


      Route::post('primaryOrderDetails', 'NewAjaxController@primaryOrderDetails');
    Route::get('primaryOrderDetailsUpdate', 'NewAjaxController@primaryOrderDetailsUpdate');
    Route::get('primaryOrderUpdate', 'NewAjaxController@primaryOrderUpdate');


      Route::post('stockOrderDetails', 'NewAjaxController@stockOrderDetails');
    Route::get('primaryStockUpdate', 'NewAjaxController@primaryStockUpdate');



    #user sale report ends here

    #sales_man_secondary report starts here
    Route::get('sales_man_secondary', 'ReportController@sales_man_secondary');
    Route::get('sales_man_secondary_report', 'NewAjaxController@sales_man_secondary_report');
    #sales_man_secondary report ends here

     #score card report starts here
    Route::get('score_card', 'ReportController@score_card');
    Route::get('score_card_report', 'NewAjaxController@score_card_report');
    #score card report ends here

      #score card report starts here
    Route::get('score_card_new', 'ReportController@score_card_new');
    Route::get('score_card_report_new', 'NewAjaxController@score_card_report_new');
    #score card report ends here

    #location 7 data based on id
    Route::post('location7_id', 'NewAjaxController@location7_id');

    #location 7 data with district
    Route::post('location7_dist', 'NewAjaxController@location7_dist');

    #beat_wise_distributor
    Route::post('beat_wise_distributor', 'NewAjaxController@beat_wise_distributor');

    #state data
    Route::post('get_state', 'NewAjaxController@state_name');
    Route::get('get_user_name_new', 'NewAjaxController@get_user_name');

    Route::get('get_catalog_product', 'NewAjaxController@get_catalog_product');


    Route::get('get_location_5', 'NewAjaxController@get_location_5');
    Route::get('get_location_6', 'NewAjaxController@get_location_6');
    Route::get('get_location_7', 'NewAjaxController@get_location_7');
    Route::get('get_retailer', 'NewAjaxController@get_retailer');
    Route::get('get_dealer', 'NewAjaxController@get_dealer');

    Route::get('get_town_from_state', 'NewAjaxController@get_town_from_state');
    Route::get('get_distributor_from_state', 'NewAjaxController@get_distributor_from_state');
    Route::get('get_beat_from_distributor', 'NewAjaxController@get_beat_from_distributor');





    #HQ data
    Route::post('get_location4', 'NewAjaxController@get_hq');

    #Get District
    Route::post('get_dist', 'NewAjaxController@districtMultiple');

    #Get Pin
    Route::post('get_pin', 'NewAjaxController@townMultiple');

    #Get beat wise reatilers
    Route::post('beat_wise_retailer', 'NewAjaxController@beat_wise_retailer');

    #Outlet(retailers based on beat selected)
    Route::post('outlet_data', 'NewAjaxController@beatWiseDistributor');

    #Distributor Beat
    Route::post('distributors_beat', 'NewAjaxController@distributorsBeat');

    #Beat Route Ajax request
    Route::get('beat_route_report', 'NewAjaxController@beat_route_report');

    #Town Distributor
    Route::post('town_distributor', 'NewAjaxController@town_distributor');

    #Get Belt users
    Route::post('get_belt_users', 'NewAjaxController@getBeltUsers');
    #Get Beat users
    Route::post('get_beat_users', 'NewAjaxController@getBeatUsers');

    #user monthly report starts here
    Route::get('user_monthy', 'ReportController@user_monthy');
    Route::get('user_monthy_report', 'NewAjaxController@user_monthy_report');

    Route::post('get_brands', 'NewAjaxController@getBrands');

    Route::get('competitors-new-product-report', 'NewAjaxController@competitorsNewProductReport');

//    Route::get('travelling-expenses-report', 'NewAjaxController@TravellingExpensesReport');
    #user_expense_report satrts here 
    Route::get('expense_report', 'ReportController@expense_report');
    Route::get('user_expense_report', 'NewAjaxController@user_expense_report');
    #user_expense_report ends here 

    Route::get('pending-claim-report', 'NewAjaxController@pendingClaimReport');

    #get distributors
    Route::post('get-dealer', 'NewAjaxController@getDealer');

    Route::get('aging-report', 'NewAjaxController@agingReport');

    Route::get('distributor-stock-status-report', 'NewAjaxController@distributorStockStatusReport');

    Route::get('stock-in-hand-report', 'NewAjaxController@stockInHandReport');

    Route::get('month_s_primary_and_secondary_sales_plan-report', 'NewAjaxController@primarySecondarySales');

    Route::get('ucdp-report', 'NewAjaxController@ucdpReport');

    Route::get('board-review-report', 'NewAjaxController@boardReviewReport');

    Route::get('rs-wise-secondary-sales-report', 'NewAjaxController@rwssr');

    Route::post('get_town_distributor', 'NewAjaxController@getTownfeedbackReportDistributor');

    Route::post('get_zone_user', 'NewAjaxController@getZoneUser');

    Route::post('get_state_user', 'NewAjaxController@getStateUser');

    Route::post('get_receipt', 'NewAjaxController@getReceipt');

    Route::post('get_region_user', 'NewAjaxController@getRegionUser');

    Route::get('isr-so-tgt-month-report', 'NewAjaxController@isrSoTgtMonthReport');

    Route::get('sales-trends-report', 'NewAjaxController@salesTrendsReport');

//    Route::get('outlet-opening-status-report', 'NewAjaxController@outletOpeningStatusReport');

    Route::get('sales-review-report', 'NewAjaxController@salesReviewReport');

    Route::get('competitive-price-intelligence-report', 'NewAjaxController@competitivePriceIntelligenceReport');

    Route::get('feedback-report', 'NewAjaxController@feedbackReport'); 

    Route::get('daily-performance-report', 'NewAjaxController@dailyPerformanceReport');

    Route::get('daily-performance-reportv2', 'NewAjaxController@dailyPerformanceReportv2');

    #Ganesh Code Start
   
    Route::get('product-investigation-report', 'NewAjaxController@productInvestigationReport');
    Route::get('complaint', 'NewAjaxController@complaintReport');
    Route::get('travelling-expenses-report', 'NewAjaxController@travellingExpensesReport');
    Route::get('distributer-stock', 'AjaxDailyTeamController@distributerStock');

    Route::get('makeOpeningStock', 'AjaxDailyTeamController@makeOpeningStock');
    

    Route::get('dealerprimarysale', 'AjaxDailyTeamController@dealerPrimarySale');

    Route::get('payment-collection-report', 'AjaxDailyTeamController@paymemtCollectionReport');
    Route::get('return-report', 'AjaxDailyTeamController@distributerReportReturn');

    Route::get('distributor-secondary-sale', 'AjaxDailyTeamController@distributerSecondarySale');

    Route::get('distributorClosingStock', 'AjaxDailyTeamController@distributorClosingStock');



    Route::get('distributer-stock-report', 'ReportController@distributerStockReport');

    Route::get('distributer-wise-scondary-sales-trends','ReportController@distributerWiseSecondarySalesTrends');
    Route::get('distributer-wise-scondary-sales-trends-report','NewAjaxController@distributerWiseSecondarySalesTrendsReport');

    Route::get('state-wise-scondary-sales-trends','ReportController@stateWiseSecondarySalesTrends');
    Route::get('state-wise-scondary-sales-trends-report','NewAjaxController@stateWiseSecondarySalesTrendsReport');
    Route::post('product_tracker_report','NewAjaxController@product_tracker_report');
    Route::get('product_tracker','ReportController@product_tracker');

    Route::resource('mtp', 'UserMtpController');
    Route::resource('reporting', 'ReportingController');
    Route::resource('booking', 'BookingControllerNew');
    Route::resource('primaryBooking', 'PrimaryBookingController');
    Route::resource('expense', 'ExpenseController');
    Route::any('expense_paid', 'ExpenseController@paid')->name('expense_paid');
    Route::resource('product_investigation', 'ProductInvestigationController');
    Route::resource('Competitors_Product', 'CompetitorsNewProductController');
    Route::resource('daily_prospecting', 'DailyProspectingController');
    Route::resource('competitive_price_intelligence', 'CompetitivePriceIntelligenceController');
    Route::resource('feedackDashbord', 'FeedbackController');
    Route::resource('pending_claim', 'PendingClaimController');

        

    #Deepak Kumar    --------------Code Start Here--------------------
    #OUTLET OPENING STATUS
    Route::get('outlet-opening-status', 'ReportNewController@outletOpeningStatus');
    // ---------AJAX Request-------
    Route::get('outlet-opening-status-report', 'AjaxNewControllerV2@outletOpeningStatusReport');

    #Deepak Kumar    --------------Code End Here--------------------

    Route::get('user-sales-summary', 'ReportController@userSalesSummary');
    Route::get('user-sales-summary-report', 'AjaxSaleOrderController@userSalesSummaryReport');
    Route::post('get_active_user_sales', 'AjaxSaleOrderController@getActiveUserSale');
    Route::get('statewise_user_eatos','ReportController@statewise_user_eatos');

    Route::get('statewise_user_eatos-report','NewAjaxController@statewise_user_eatosReport');
    Route::resource('user', 'UserController');
    Route::get('testdatauser', 'UserController@testdatauser');
    Route::post('get_user_assign_distributor', 'UserController@get_user_assign_distributor');
    Route::post('get_beat_details_dealer', 'UserController@get_beat_details_dealer');
    Route::post('get_retailer_details', 'UserController@get_retailer_details');

    Route::resource('user_attendance', 'UserAttendanceController');

    Route::resource('user_tracking', 'UserTrackingController');

    ####################
    #date_wise_product_wise_report start here
    Route::get('date_wise_product_wise', 'ReportController@date_wise_product_wise');
    Route::get('date_wise_product_wise_report', 'NewAjaxController@date_wise_product_wise_report');
    #date_wise_product_wise_report Ends here
    
    #user dailyReporting starts here
    Route::get('dailyReporting', 'ReportController@dailyReporting');
    Route::get('daily-reporting-report', 'NewAjaxController@dailyReportingReport');
    #user dailyReporting ends here

     #user mobile 0n/off starts here
     Route::get('mobileOnOff', 'ReportController@mobileOnOff');
     Route::get('mobile-on-off-report', 'NewAjaxController@mobileOnOffReport');
     #user mobile 0n/off ends here

    #user complaint report starts here
    Route::get('userComplaint', 'ReportController@userComplaint');
    Route::get('user-complaint-report', 'NewAjaxController@userComplaintReport');
    #user complaint report ends here

    #notification non contacted report starts here
    Route::get('notificationNonContacted', 'ReportController@notificationNonContacted');
    Route::get('notification-non-contacted', 'NewAjaxController@notificationNonContactedReport');
    #notification non contacted report ends here


    #user attendance Summary starts here
    Route::get('attendanceSummary', 'ReportController@attendanceSummary');
    Route::get('attendance-summary-report', 'NewAjaxController@attendanceSummaryReport');
    #user attendance Summary ends here


     #call time report starts here
     Route::get('callTimeSummary', 'ReportController@callTimeSummary');
     Route::get('call-time-summary-report', 'NewAjaxController@callTimeSummaryReport');
     #call time report ends here


    #sales team attendance starts here
    Route::get('salesTeamAttendanceSummary', 'ReportController@salesTeamAttendanceSummary');
    Route::get('sales-team-attendance-summary-report', 'NewAjaxController@salesTeamAttendanceSummaryReport');
    #sales team attendance ends here
    
    Route::get('tablet_working_status','ReportController@budget_target');
    Route::get('budget_target_status-report','NewAjaxController@budget_target_statusReport');

    Route::get('merchandise-order', 'ReportController@merchandiseOrder');
    Route::get('merchandise-order-report', 'AjaxSaleOrderController@merchandiseOrderReport');

    Route::get('retailer-Stock', 'ReportController@retailerStock');
    Route::get('retailer-Stock-Report', 'AjaxSaleOrderController@retailerStockReport');

     Route::get('user-Meeting-Order', 'ReportController@userMeetingOrder');
    Route::get('user-Meeting-Order-Report', 'AjaxSaleOrderController@userMeetingOrderReport');

    # no booking and no attendance starts here
    Route::get('no-attendance', 'ReportController@noAttendance');
    Route::get('no-booking', 'ReportController@noBooking');
    Route::get('no-attendance-report', 'AjaxSaleOrderController@noAttendanceReport');
    Route::get('no-booking-report', 'AjaxSaleOrderController@noBookingReport');

    #................ senior info and dealer info starts here 
    Route::get('seniorInfo', 'ReportController@seniorInfo');
    Route::get('seniorInfoReport', 'NewAjaxController@seniorInfoReport');
    Route::get('userDealerInfo', 'ReportController@userDealerInfo');
    Route::get('userDealerInfoReport', 'NewAjaxController@userDealerInfoReport');

    // #............................geofence module routes ..............................

    Route::resource('geofence', 'GeofenceController'); 
    Route::get('createGeofence', 'GeofenceController@createGeofence');
    Route::POST('geofenceSubmit', 'GeofenceController@geofenceSubmit');

    // #............................End Here................................
    // #............................catalog dashboard module routes ..............................

    Route::resource('catalogdashboard', 'CatalogDashboardController'); 
   

    // #............................End Here................................
    // #............................manual-attandence module routes ..............................

    Route::resource('manualAttandence', 'ManualAttandenceController');
    Route::get('submitManualAttandence', 'ManualAttandenceController@submitManualAttandence');
    Route::get('updateManualAttandence','ManualAttandenceController@updateManualAttandence');

    // #............................End Here................................
      // #............................manual-attandence module routes ..............................

      Route::resource('manualTourPlan', 'ManualTourPlanController');
      Route::get('submitManualTourPlan', 'ManualTourPlanController@submitManualTourPlan');
      Route::get('updateManualTourPlan','ManualTourPlanController@updateManualTourPlan');

    Route::get('get_dealer_name', 'NewAjaxController@get_dealer_name');

    Route::get('get_beat_name_new', 'NewAjaxController@get_beat_name_new');


  
      // #............................End Here................................
    // #............................mtp module routes ..............................
    Route::get('mtpEnable','UserController@mtpEnable');
    // #............................End Here................................
    // #............................Monthly progressive Routes here ..............................

        Route::get('monthlyProgressive','ReportController@monthlyProgressive');

        Route::get('monthlyProgressiveReport','AjaxSaleOrderController@monthlyProgressiveReport');

    // #............................End Here................................
    ##....................................Tally Reports Route ....................................##
    Route::get('tallySsBilling','ReportController@tallySsBilling');
    Route::get('tallySsBillingReport','TallyController@tallySsBillingReport');

    Route::get('tallySsStock','ReportController@tallySsStock');
    Route::get('tallySsStockReport','TallyController@tallySsStockReport');

    Route::get('tallySsClosingStock','ReportController@tallySsClosingStock');
    Route::get('tallClosingReport','TallyController@tallClosingReport');

    ##....................................Tally Reports Route Ends Here............................##
    ##....................................For Asiign in user master Route Starts Here............................##
    Route::post('getLocationForAssign', 'AjaxDistributorAssignController@getLocationForAssign');
    Route::post('/filter_distributor', 'AjaxDistributorAssignController@distributorList');
    Route::post('/distributor-beat', 'AjaxDistributorAssignController@distributorBeat');
    Route::post('/assign-beat', 'AjaxDistributorAssignController@assignBeat');
    ##.....................................For Asiign in user master RouteEnds Here............................##
    Route::post('/filter_distributor_template', 'ProductRateListControllerTemplate@filter_distributor_template');
    Route::post('/filter_csa_template', 'ProductRateListControllerTemplate@filter_csa_template');
    Route::post('/filter_state_template', 'ProductRateListControllerTemplate@filter_state_template');
    Route::post('/distributor_template_assign', 'ProductRateListControllerTemplate@distributor_template_assign');
    Route::post('/csa_template_assign', 'ProductRateListControllerTemplate@csa_template_assign');
    Route::post('/state_template_assign', 'ProductRateListControllerTemplate@state_template_assign');
    Route::post('/get_template_type', 'ProductRateListControllerTemplate@get_template_type');

    ##....................................route for export data for master data............................##

     Route::get('ExportData', 'ExportController@ExportData');
     Route::get('ShowExportData', 'ExportController@ShowExportMasterData');
     Route::get('ExportRetailer', 'ExportController@RetailerExport');
     Route::get('userExport', 'ExportController@userExport');
     Route::get('dealerExport', 'ExportController@dealerExport');
    Route::get('export_sale_data', 'ExportController@export_sale_data');
     
     

    ##....................................route for export data for transactional data............................##

     Route::get('ShowExportTransactionalData', 'ExportController@ShowExportTransactionalData');
     Route::get('ExportTransactionalData', 'ExportController@ExportTransactionalData');
     // Route::get('showExportTransactionalData', 'ExportController@showExportTransactionalData');
     Route::get('DailyPerformanceReportExport', 'ExportController@ExportdailyPerformanceReport');


    ##....................................route for export data. Ends here...........................##

    Route::resource('circular', 'CircularController');
    Route::post('send_sms_notification', 'CircularController@send_sms_notification');
    Route::get('circularReport', 'ReportController@circularReport');
    Route::get('user_circular_report', 'CircularController@user_circular_report');
    Route::get('advance_summary', 'ReportController@advance_summary');
    Route::get('advance_summary_report', 'NewAjaxController@advance_summary_report');

    Route::get('userLiveLocation', 'MapController@userLiveLocation');
    Route::get('userMapTracking', 'MapController@userMapTracking');
    


    Route::get('daily_isr_report', function () {
        return view('reports.advance_summary_report.isrIndex');
    });
    Route::post('advance_summary_report_ghanta', 'NewAjaxController@advance_summary_report_ghanta');


    ##...................................dsr monthly repports here ................................##

    Route::get('dsrMonthly', 'ReportController@dsrMonthly');
    Route::get('dsrMonthlyReport', 'NewAjaxController@dsrMonthlyReport');

    Route::get('dsrMonthlyCases', 'ReportController@dsrMonthlyCases');
    Route::get('dsrMonthlyCasesReport', 'NewAjaxController@dsrMonthlyCasesReport');

    Route::get('rds_wise_sale', 'ReportController@rds_wise_sale');
    Route::get('rds_wise_sale_report', 'NewAjaxController@rds_wise_sale_report');
    Route::get('rds_monthly', 'ExportController@rds_monthly');

    ##....................................route for Address Update data Script............................##
    Route::get('AddressUpdate', 'AddressUpdationController@AddressUpdateData');
    Route::get('ShowAddressUpdationOption', 'AddressUpdationController@ShowAddressUpdationOption');
    Route::get('UpdateAttendanceAddress', 'AddressUpdationController@UpdateAttendanceAddress');
    Route::get('UpdateCheckoutAddress', 'AddressUpdationController@UpdateCheckoutAddress');
    Route::get('UpdateDailyTrackingAddress', 'AddressUpdationController@UpdateDailyTrackingAddress');
    Route::get('dailyTracking', 'ReportController@dailyTracking');
    Route::get('dailyTrackingTest', 'ReportController@dailyTrackingTest');
    Route::get('dailyTrackingReport', 'AjaxSaleOrderController@dailyTrackingReport');
    Route::get('dailyTrackingReportTest', 'AjaxSaleOrderController@dailyTrackingReportTest');


    Route::get('dailyTrackingNeha', 'ReportController@dailyTrackingNeha');
    Route::get('dailyTrackingNehaReport', 'AjaxSaleOrderController@dailyTrackingNehaReport');



    Route::get('dailyTrackingLog', 'ReportController@dailyTrackingLog');
    Route::get('dailyTrackingLogReport', 'AjaxSaleOrderController@dailyTrackingLogReport');

    





    ##....................................route for Address Update data Script Ends Here............................##

    // Route::get('user_monthly_target', 'SettingController@user_monthly_target');
    // Route::get('save_monthly_target', 'SettingController@save_monthly_target');

    ##............................. Retailer Move/Copy ....................##
    Route::resource('retailermovecopy', 'RetailerMoveCopyController');
    Route::get('moveretailer', 'RetailerMoveCopyController@moveRetailer');
    Route::get('get_beat_name', 'NewAjaxController@get_beat_name');
    ##............................. Retailer Move/Copy Ends ....................##

    ##....................................route for import data. Starts here...........................##
    Route::get('ImportData', 'ImportController@ImportData');
    Route::get('VideoImportData', 'ImportController@ImportData');
    Route::get('RetailerData', 'ImportController@RetailerData');
    Route::get('OwnerData', 'ImportController@OwnerData');
    Route::get('CsaData', 'ImportController@CsaData');
    Route::get('OutletTypeData', 'ImportController@OutletTypeData');
    Route::get('OutletClassTypeData', 'ImportController@OutletClassTypeData');
    Route::get('BeatData', 'ImportController@BeatData');
    Route::get('RetailerFormat', 'ImportController@RetailerFormat');
    Route::get('CatalogFormat', 'ImportController@CatalogFormat');
    Route::get('DealerFormat', 'ImportController@DealerFormat');
    Route::get('BeatFormat', 'ImportController@BeatFormat');
    Route::get('TownFormat', 'ImportController@TownFormat');
    Route::get('DlrlFormat', 'ImportController@DlrlFormat');
    Route::get('DealerData', 'ImportController@DealerData');
    Route::get('StateData', 'ImportController@StateData');
    Route::get('StateId', 'ImportController@StateId');
    Route::get('TownData', 'ImportController@TownData');
    Route::get('ProductData', 'ImportController@ProductData');
    Route::post('UploadData', 'ImportController@UploadData');
    Route::get('ProductRateListFormat', 'ImportController@ProductRateListFormat');
    Route::get('DistributorPersonalFormat', 'ImportController@DistributorPersonalFormat');
    Route::get('DealerCredentailsFormat', 'ImportController@DealerCredentailsFormat');
    Route::get('SchemePlanFormat', 'ImportController@SchemePlanFormat');
    Route::get('ExportTaskOfTheDay', 'ImportController@ExportTaskOfTheDay');
    Route::get('ExportWorkStatus', 'ImportController@ExportWorkStatus');
    Route::get('ExportOutletDataForMaheshNamkeen', 'ImportController@ExportOutletDataForMaheshNamkeen');

    Route::get('ExportStockFormat', 'ImportController@ExportStockFormat');
    

    Route::get('location1_data', 'ImportController@location1_data');
    Route::get('location2_data', 'ImportController@location2_data');
    Route::get('location4_data', 'ImportController@location4_data');
    Route::get('location2Format', 'ImportController@location2Format');
    Route::get('location3Format', 'ImportController@location3Format');
    Route::get('location4Format', 'ImportController@location4Format');
    Route::get('location5Format', 'ImportController@location5Format');
    Route::get('SKUExport', 'ImportController@SKUExport');
    Route::get('userDealerAssignFormat', 'ImportController@userDealerAssignFormat');
    Route::get('userFormat', 'ImportController@userFormat');
    Route::get('roleExport', 'ImportController@roleExport');
    Route::get('personExport', 'ImportController@personExport');
    Route::get('productUnitType', 'ImportController@productUnitType');
    Route::get('weightTypeMaster', 'ImportController@weightType');
    Route::get('ExportSchemePlanDetailsMaster', 'ImportController@ExportSchemePlanDetailsMaster');

    Route::get('superStockiestSkuWiseTargetFormat', 'ImportController@superStockiestSkuWiseTargetFormat');
    Route::get('distributorSkuWiseTargetFormat', 'ImportController@distributorSkuWiseTargetFormat');
    Route::get('UserSkuWiseTargetFormat', 'ImportController@UserSkuWiseTargetFormat');

    Route::get('TourPlanFormat', 'ImportController@TourPlanFormat');
    Route::get('exportTargetFormat', 'ImportController@exportTargetFormat');

    Route::get('SchemePlanUploadFormat', 'ImportController@SchemePlanUploadFormat');
    Route::get('PrimaryUploadFormat', 'ImportController@PrimaryUploadFormat');
    Route::get('CsaStockFormat', 'ImportController@CsaStockFormat');


    ##....................................route for import data. Ends here...........................##


    ##............................. Modern Trade ...........................................##

    Route::get('modern-user-sale', 'ReportController@userSalesModern');
    Route::get('modern-user-sale-report', 'NewAjaxController@userSalesReportModern');

    Route::get('merchandise-visit', 'ReportController@merchandiseVisit');
    Route::get('merchandise-visit-report', 'AjaxSaleOrderController@merchandiseVisitReport');

    Route::get('supervisor-visit', 'ReportController@supervisorVisit');
    Route::get('supervisor-visit-report', 'AjaxSaleOrderController@supervisorVisitReport');

    Route::get('retailer_capture_images', 'ReportController@retailerCaptureImages');
    Route::get('retailer_capture_images_report', 'AjaxSaleOrderController@retailerCaptureImagesReport');

    Route::get('coverage_capture_images', 'ReportController@coverageCaptureImages');
    Route::get('coverage_capture_images_report', 'AjaxSaleOrderController@coverageCaptureImagesReport');

    Route::get('sluggish_retailer_list', 'HomeControllerNew@sluggish_retailer_list');
    Route::get('getTotalSalesTeamHome', 'HomeControllerNew@getTotalSalesTeamHome');
    Route::get('total_beat_coverage_details', 'HomeControllerNew@total_beat_coverage_details');
    Route::get('total_productive_coverage_details', 'HomeControllerNew@total_productive_coverage_details');
    Route::get('get_state_wise_details_home', 'HomeControllerNew@get_state_wise_details_home');
    Route::get('get_dealer_details_home', 'HomeControllerNew@get_dealer_details_home');
    Route::get('get_dealer_coverage_details_home', 'HomeControllerNew@get_dealer_coverage_details_home');
    Route::get('get_retailer_details_home', 'HomeControllerNew@get_retailer_details_home');
    Route::get('get_retailer_coverage_details_home', 'HomeControllerNew@get_retailer_coverage_details_home');
    Route::get('get_beat_details_home', 'HomeControllerNew@get_beat_details_home');
    Route::get('get_state_wise_primary_booking_details_home', 'HomeControllerNew@get_state_wise_primary_booking_details_home');
    Route::get('get_total_call_details_home', 'HomeControllerNew@get_total_call_details_home');
    Route::get('user_details_on_roles', 'HomeControllerNew@user_details_on_roles');

    Route::get('get_retailer_details_neha_home', 'HomeControllerNew@get_retailer_details_neha_home');
    Route::get('get_distributor_details_home_common', 'HomeControllerNew@get_distributor_details_home_common');


    ##............................. Modern Trade ...........................................##
    ##..............................dms routes starts here ................................##
    Route::resource('dms_order_reason', 'DmsOrderReasonController');
    Route::resource('saleorder', 'SaleOrderController');
    Route::post('dms_get_order_details', 'SaleOrderController@dms_get_order_details');
    Route::post('submit_dms_order_details', 'SaleOrderController@submit_dms_order_details');
    Route::post('submit_dms_order_dispatch', 'SaleOrderController@submit_dms_order_dispatch');
    Route::post('submit_dms_reject_order', 'SaleOrderController@submit_dms_reject_order');
    Route::post('dms_order_confirm_submit', 'SaleOrderController@dms_order_confirm_submit');
    Route::post('submit_dms_invoice_genrate', 'SaleOrderController@submit_dms_invoice_genrate');
    Route::post('dms_rate_bhelaf_product_id', 'SaleOrderController@dms_rate_bhelaf_product_id');
    Route::get('Payment-Recieved-Details', 'SaleOrderController@dms_payement_recieved_details');
    Route::post('Payment-Recieved-Details-Report', 'SaleOrderController@dms_payement_recieved_details_report');
    Route::post('submit_payment_adjusment', 'SaleOrderController@submit_payment_adjusment');
    Route::get('SRN-Details', 'SaleOrderController@dms_srn_function');
    Route::post('dms_get_srn_details', 'SaleOrderController@dms_get_srn_details');
    Route::post('submit_sale_return', 'SaleOrderController@submit_sale_return');
    Route::post('submit_dms_damge', 'SaleOrderController@submit_dms_damge');
    Route::post('dms_status_logs', 'SaleOrderController@dms_status_logs');



     ##..............................dms for retailer routes starts here ................................##
    // Route::resource('dms_order_reason', 'DmsOrderReasonController');
    Route::resource('retailersaleorder', 'RetailerSaleOrderController');
    Route::post('retailer_dms_get_order_details', 'RetailerSaleOrderController@retailer_dms_get_order_details'); // need to edit
    Route::post('submit_retailer_dms_order_details', 'RetailerSaleOrderController@submit_retailer_dms_order_details'); // needto edit
    Route::post('submit_retailer_dms_order_dispatch', 'RetailerSaleOrderController@submit_retailer_dms_order_dispatch'); // need to edit
    Route::post('submit_retailer_dms_reject_order', 'RetailerSaleOrderController@submit_retailer_dms_reject_order'); // not edit 
    Route::post('dms_retailer_order_confirm_submit', 'RetailerSaleOrderController@dms_retailer_order_confirm_submit');  // not edit
    Route::post('dms_submit_reciept_no', 'NewAjaxController@dms_submit_reciept_no');  // not edit

    
    
    ##..............................dms routes ends here ................................##

   # manual order booking starts here 
    Route::resource('manualOrderBooking', 'ManualOrderBookingController');
    Route::post('manual_order_booking_form', 'ManualOrderBookingController@manual_order_booking_form');
    Route::post('manual_orderproduct_details', 'ManualOrderBookingController@manual_orderproduct_details');
    Route::post('submit_manual_order_booking', 'ManualOrderBookingController@submit_manual_order_booking');


   # manual order booking ends here 

    # counter sale starts here
    Route::get('dealer_counter_sale', 'ReportController@dealer_counter_sale');
    Route::get('dealer_counter_sale_report', 'NewAjaxController@dealer_counter_sale_report'); 
    # counter sale ends  here 

    Route::get('dms_complaint_details', 'ReportController@dms_complaint_details');
    Route::get('dms_dealer_complaint_details_report', 'NewAjaxController@dms_dealer_complaint_details_report'); 

    Route::get('fullifillment_order','ReportController@fullifillment_order'); //only for patanjali
    Route::get('fullfillment_sale_report','NewAjaxController@fullfillment_sale_report');  // only for patajali

    Route::get('common_fullifillment_order','ReportController@common_fullifillment_order'); //common for all
    Route::get('common_fullfillment_sale_report','NewAjaxController@common_fullfillment_sale_report'); //common for all


     #user attendance Summary starts here
     Route::get('salesManSecondarySales', 'ReportController@salesManSecondarySales');
     Route::get('salesManSecondarySalesReport', 'NewAjaxController@salesManSecondarySalesReport');
     #user attendance Summary ends here

     Route::get('dealerWiseSS', 'ReportController@dealerWiseSS');
    Route::get('dealerWiseSSReport', 'NewAjaxController@dealerWiseSSReport');


    #user sale report starts here
    Route::get('user-sale-btw', 'ReportController@userSalesBtw');
    Route::get('user-sale-report-btw', 'NewAjaxController@userSalesReportBtw');
    #user sale report ends here

    Route::get('daily-attendance-btw', 'ReportController@dailyAttendanceBtw');
    Route::get('daily-attendance-report-btw', 'AjaxAttdController@dailyAttendanceReportBtw');

     #ss sku wise monthly target v/s Achievement report starts here
     Route::get('super-stockist-sku-monthly-target-details', 'ReportController@superStockistSkuMonthlyTargetDetails');
     Route::get('super-stockist-sku-monthly-target-report-details', 'NewAjaxController@superStockistSkuMonthlyTargetReportDetails');
     #ss sale report ends here
 
       #DB sku wise monthly target v/s Achievement report starts here
     Route::get('distributor-sku-monthly-target-details', 'ReportController@distributorSkuMonthlyTargetDetails');
     Route::get('distributor-sku-monthly-target-report-details', 'NewAjaxController@distributorSkuMonthlyTargetReportDetails');
     #DB sale report ends here



       #ss-monthly-target-report starts here 
    Route::get('ss-monthly-report', 'ReportController@ssMonthly');
    Route::get('ss-monthly-target-report', 'AjaxAttdController@ssMonthlyTargetReport');

    #ss-monthly-target-report starts here 
    Route::get('distributor-monthly-report', 'ReportController@distributorMonthly');
    Route::get('distributor-monthly-target-report', 'AjaxAttdController@distributorMonthlyTargetReport');

    Route::get('distributor-target-report', 'ReportController@distributorTargetReport');
    Route::get('distributor-ajax-target-report', 'AjaxSaleOrderController@distributorAjaxTargetReport');

    Route::get('target_ss', 'ReportController@target_ss');
    Route::get('target_ss_report', 'NewAjaxController@target_ss_report');

    Route::get('target_db', 'ReportController@target_db');
    Route::get('target_db_report', 'NewAjaxController@target_db_report');


    Route::get('dms_new_calling','ReportController@dms_new_calling'); //only for patanjali
    Route::get('dms_new_calling_report','NewAjaxController@dms_new_calling_report');  // only for patajali
    Route::post('dms_submit_new_calling', 'NewAjaxController@dms_submit_new_calling');  // not edit

    Route::get('dms_sale_order_report','DmsReportController@dms_sale_order_report'); //only for patanjali
    Route::get('dms_order_booking_report_final','DmsReportController@dms_order_booking_report_final'); //only for patanjali
    Route::get('dms_sale_order_report_final','DmsReportController@dms_sale_order_report_final'); //only for patanjali

    Route::get('dms_payment_report','DmsReportController@dms_payment_report'); //only for patanjali
    Route::get('dms_payment_report_final','DmsReportController@dms_payment_report_final'); //only for patanjali

    Route::get('dms_sale_payment_report','DmsReportController@dms_sale_payment_report'); //only for patanjali
    Route::get('dms_sale_payment_report_final','DmsReportController@dms_sale_payment_report_final'); //only for patanjali


    Route::get('dms_order_enquiry','ReportController@dms_order_enquiry'); //only for patanjali
    Route::get('dms_order_enquiry_report','NewAjaxController@dms_order_enquiry_report');  // only for patajali
    Route::post('dms_update_enquiry_details', 'NewAjaxController@dms_update_enquiry_details');  // not edit


    Route::get('dms_dealer_details_for_document','ReportController@dms_dealer_details_for_document'); //only for patanjali
    Route::get('dms_dealer_details_for_document_report','NewAjaxController@dms_dealer_details_for_document_report');  // only for patajali
    Route::post('dms_upload_documents_dealer','NewAjaxController@dms_upload_documents_dealer');  // only for patajali
    Route::post('user_upload_documents','UserController@user_upload_documents');  



     ##............................common mastere starts here 

     Route::resource('dms_calling_type', 'CommonMasterControllers');
     // Route::resource('_leave_type', 'CommonMasterControllers');
     Route::resource('dms_document_master', 'CommonMasterControllers');
     Route::resource('dms_social_form_master', 'CommonMasterWithImageController');
     Route::resource('dms_social_link_master', 'CommonMasterWithImageController');
     Route::resource('dms_contact_details', 'CommonMasterWithImageController');
     Route::resource('dms_about_us_master', 'CommonMasterControllers');
     Route::resource('dms_notification_data', 'CommonMasterControllers');
     Route::resource('app_link_master', 'CommonMasterControllers');
     Route::resource('_retailer_filter_master', 'CommonMasterControllers');
     Route::resource('_retailer_filter_master_details', 'CommonMasterControllers');
    
     ##............................common mastere ends here 


    ////////////// for oyster ////////////////
    Route::get('daily-attendance-oyster', 'ReportController@dailyAttendanceOyster');
    Route::get('daily-attendance-report-oyster', 'AjaxAttdController@dailyAttendanceReportOyster');
    ////////////// for oyster ////////////////


     Route::get('skuSales', 'ReportController@skuSales');
     Route::get('skuSalesReport', 'NewAjaxController@skuSalesReport');
    Route::post('skuSalesDetails', 'NewAjaxController@skuSalesDetails');
    Route::post('skuOrderDetails', 'NewAjaxController@skuOrderDetails');

    Route::get('ExportSkuSales', 'NewAjaxController@ExportSkuSales');



    Route::get('skuSalesPrimary', 'ReportController@skuSalesPrimary');
    Route::get('skuSalesPrimaryReport', 'NewAjaxController@skuSalesPrimaryReport');
    Route::post('skuSalesPrimaryDetails', 'NewAjaxController@skuSalesPrimaryDetails');



    Route::get('finalStock', 'ReportController@finalStock');
    Route::get('finalStockReport', 'NewAjaxController@finalStockReport');

    Route::get('dailyAttendancePatanjali', 'ReportController@dailyAttendancePatanjali');
    Route::get('dailyAttendancePatanjaliReport', 'AjaxAttdController@dailyAttendancePatanjaliReport');
    Route::post('get_mtp_details', 'AjaxAttdController@get_mtp_details');

    Route::get('salesTeamAttendanceSummaryPatanajali', 'ReportController@salesTeamAttendanceSummaryPatanajali');
    Route::get('salesTeamAttendanceSummaryPatanajaliReport', 'NewAjaxController@salesTeamAttendanceSummaryPatanajaliReport');


    Route::get('userAttendanceTimePatanajali', 'ReportController@userAttendanceTimePatanajali');
    Route::get('userAttendanceTimePatanajaliReport', 'NewAjaxController@userAttendanceTimePatanajaliReport');


    Route::get('dailyAttendanceEdit', 'ReportController@dailyAttendanceEdit');
    Route::get('dailyAttendanceEditReport', 'NewAjaxController@dailyAttendanceEditReport');

    Route::post('attendanceDetails', 'NewAjaxController@attendanceDetails');
    Route::post('attendanceDetailsUpdate', 'NewAjaxController@attendanceDetailsUpdate');



    Route::get('user_dealer_beat_retailer', function () {
        return view('reports.userDealerBeatDetails.index');
    });
    Route::post('user_dealer_beat_retailer_report', 'NewAjaxController@user_dealer_beat_retailer_report');
    Route::post('get_user_assign_retailer', 'NewAjaxController@get_user_assign_retailer');


    Route::get('customerOrder', function () {
        return view('reports.customerOrderReport.index');
    });
    Route::post('customerOrderReport', 'NewAjaxController@customerOrderReport');

     Route::get('tourProgram', function () {
        return view('reports.tourProgramReport.index');
    });
    Route::post('tourProgramReport', 'NewReportController@tourProgramReport');


    Route::get('dsrMonthlyNeha', 'ReportController@dsrMonthlyNeha');
    Route::get('dsrMonthlyNehaReport', 'NewReportController@dsrMonthlyNehaReport');

    

    Route::get('userSalesSummaryRajdhani', 'ReportController@userSalesSummaryRajdhani');
    Route::get('userSalesSummaryRajdhaniReport', 'NewAjaxController@userSalesSummaryRajdhaniReport');


    # employee managemet route starts here
        Route::get('leave',               [ 'as'=>'leave',              'uses' => 'LeaveController@index']);
        Route::get('leave/create',        [ 'as'=>'leave.create',       'uses' => 'LeaveController@create']);
        Route::post('leave/store',        [ 'as'=>'leave.store',        'uses' => 'LeaveController@store']);
        Route::get('leave/search',       [ 'as'=>'leave.search',      'uses' => 'LeaveController@search']);

        Route::post('leave/approve/{id}',        [ 'as'=>'leave.approve',        'uses' => 'LeaveController@approve']);
        Route::post('leave/paid/{id}',        [ 'as'=>'leave.paid',        'uses' => 'LeaveController@paid']);
        Route::post('getDays',   [ 'as'=>'getDays',  'uses' => 'LeaveController@getDays']);

        Route::resource('salary', 'SalaryController');
        Route::get('salary_generation', function () {
            return view('salary.salary_generation_index');
        });
        Route::post('salary_generation_ajax', 'SalaryController@salary_generation_ajax');
        Route::post('process_salry_bulk', 'SalaryController@process_salry_bulk');


    Route::get('dsrMonthlyForNeha', 'ReportController@dsrMonthlyForNeha');
    Route::get('dsrMonthlyForNehaReport', 'NewAjaxController@dsrMonthlyForNehaReport');
    Route::post('get_outlet_details', 'NewAjaxController@get_outlet_details');


    Route::get('unbilledOutlet', 'ReportController@unbilledOutlet');
    Route::get('unbilledOutletReport', 'NewAjaxController@unbilledOutletReport');



    Route::get('userPrimarySales', 'ReportController@userPrimarySales');
    // Route::get('userPrimarySalesReport', 'NewAjaxController@userPrimarySalesReport'); // not in use


    Route::get('beatWiseSale', 'ReportController@beatWiseSale');
    Route::get('beatWiseSaleReport', 'NewAjaxController@beatWiseSaleReport');
    Route::get('modules-check-status', 'ModuleReportController@modules_array_data');
    Route::post('modules_array_data_report', 'ModuleReportController@modules_array_data_report');
    

    Route::get('hitkaryBeatWiseSale', 'ReportController@hitkaryBeatWiseSale');
    Route::get('hitkaryBeatWiseSaleReport', 'NewAjaxController@hitkaryBeatWiseSaleReport');

    Route::get('hitkaryRetailerWiseSale', 'ReportController@hitkaryRetailerWiseSale');
    Route::get('hitkaryRetailerWiseSaleReport', 'NewAjaxController@hitkaryRetailerWiseSaleReport');

    Route::get('hitkaryUserWiseSale', 'ReportController@hitkaryUserWiseSale');
    Route::get('hitkaryUserWiseSaleReport', 'NewAjaxController@hitkaryUserWiseSaleReport');

    Route::get('distributorAssign', 'ReportController@distributorAssign');
     Route::get('distributorAssignReport', 'NewAjaxController@distributorAssignReport');

     Route::get('skuWiseCounterSale', 'ReportController@skuWiseCounterSale'); // for neha herbals
    Route::get('skuWiseCounterSaleReport', 'NewAjaxController@skuWiseCounterSaleReport');

    # employee managemet route ends here

    Route::get('dailyTrackingKoyas', 'ReportController@dailyTrackingKoyas');
    Route::get('dailyTrackingKoyasReport', 'AjaxSaleOrderController@dailyTrackingKoyasReport');






});
