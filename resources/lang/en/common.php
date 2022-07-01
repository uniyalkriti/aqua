<?php 
namespace App\Http\Controllers;

use Auth;
use DB;
use Session;
use App\User;
// dd(Auth);
// $company_id = User::getCompanyId();
$company_id = session('company_id_en');
// dd($company_id);
$company_id_in = !empty($company_id[0])?$company_id[0]:'37';
// dd($company_id_in);
$array = array('location1','location2','location3','location4','location5','location6','location7');
$query = DB::table('sub_web_module')
        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
        ->select('sub_web_module.title as title')
        ->whereIn('sub_web_module_bucket.title',$array)
        ->where('company_id',$company_id_in)
        ->get();
        // dd($query);
foreach ($query as $key => $value) 
{
    $loc_array[] = $value->title;
}
// dd($loc_array);  
return [
    'username' =>'User Name',
    'distributor_name' =>'Stockist Name',
    'user' =>'User',
    'user_detail' =>'User Details',
    'dealer_detail' =>'Stockist Details',
    'retailer_detail' =>'Retailer Details',
    's_no' =>'S.No.',
    'image' =>'Image',
    'emp_code' =>'Emp Code',
    'user_name' =>'Username',
    'password' =>'Password',
    'senior_name' =>'Senior Name',
    'user_contact' =>'Mobile No.',
    'user_address' =>'Residential Address',
    'manual_order_booking'=>'Manual Order Booking',
    'address' =>'Address',
    'user_imei' =>'Imei',
    'email' =>'Email',
    'landline' =>'landline',
    'pin_no' =>'Pin No',
    'total' =>'Total',
    'total_call' =>'Total Call',
    'collection' =>'Collection',
    'productive_call' =>'Productive Call',
    'rv_lakh' =>'(RV LAKHS)',
    'productive' =>'Productive',
    'as_per_tour' =>'As Per Tour Program',
    'secondary_sale' =>'Secondary Sale',
    'primary_sale' =>'Primary Sale',
    'primary_order' =>'Primary Order Booking',
    'gst_no' =>'Gst No',
    'grand' =>'Grand',
    'type' =>'Type',
    'depo' =>'Depo',
    'state_wise_secondary' =>'State Wise Secondary Sales Summary',
    'state_wise_product' =>'State Wise Product Wise Secondary Sales Summary',
    'fulfillment_order' =>'Fullfilment Order Report',
    'ownership_type' =>'Ownership Type',
    'avg_month' =>'Avg.Per Month Purchase',
    'mothly_attendance' =>'Monthly Attendance',
    'dsr_monthly' =>'DSR Monthly',
    'rds_wise_sale' =>'RDS Wise Sale Report',
    'beatWiseSale' =>'Beat Wise Sale Report',
    'retailerWiseSale' =>'Retailer Wise Sale Report',
    'userWiseSale' =>'User Wise Sale Report',
    'month' =>'Month',
    'created_date' =>'Created Date',
    'updated_date' =>'Updated Date',
    'deactivate_date' =>'De-Activated Date',
    'last_sync_date' =>'Last Sync Date',
    'joining_date' =>'Joining Date',
    'app_version' =>'App Version',
    'status' =>'Status',
    'action' =>'Action',
    'remarks' =>'Remarks',
    'per_page' =>'Per Page',
    'order_booking' =>'Order Booking',
    'dashboard' =>'Dashboard',
    'retailer_type' =>'Retailer Type',
    'retailer_owner_name' =>'Owner Name',
    'search_by_name' =>'Search By Name',
    'check_out' =>'Check Out',
    'distributor_detail_report' =>'Stockist Details Report',
    'find' =>'Find',
    'add' =>'Add',
    'edit' =>'Edit',
    'master' =>'Master',
    'image' =>'Image',
    'close' =>'Close',
    'assign' =>'Assign',
    'details' =>'Details',
    'weight' =>'Weight',
    'hsn' =>'Hsn code',
    'time' =>'Time',
    'count' =>'Count',
    'bill' =>'Bill',
    'upload' =>'Upload',
    'sku' =>'SKU',
    'export_data' =>'Export',
    'format' =>'Format',
    'upload_button' =>'Button For Upload',
    'time_report' =>'Time Report',
    'module_check_status' =>'Module Check Status Report',
    'quantity_per_case' =>'Quantity Per Case',
    'gst_per_case' =>'Gst Per Case',
    'mrp' =>'M.R.P',
    'rate' =>'Rate',
    'case' =>'Case',
    'piece' =>'Piece',
    'date_range' =>'Date Range Picker',
    'date' =>'Date',
    'day' =>'Day',
    'sku_wise_target' =>'SKU Wise Target',
    'ss_sku_wise_monthly' =>'SS SKU wise Monthly Target vs Achievement',
    'distributor_wise_monthly' =>'Distributor SKU wise Monthly Target vs Achievement',
    'ss_monthly' =>'SS MTD Target vs Achievement Report',
    'db_monthly' =>'DB MTD Target vs Achievement Report',
    'advance_summary_report' =>'Advance Summary Report',
    'db_salt' => 'demo',
    'stock' => 'Stock',
    'retailer_stock' => 'Retailer Stock',
    'outlet' => 'Outlet',
    'location3' => 'State',
    'booking-report' =>'Booking Report',
    'competitors_new_product'=>'Competitors New Product',
    'product-investigation' => 'Product Investigation',
    'daily_prosecting'=>'Daily Prospecting',
    'competitive_price_intelligence' => 'Competitive  price Intelligence',
    'payment_collection' => 'Payment Collection',
    'payment' => 'Payment',
    'return' => 'Return',
    'merchandise' =>'Merchandise',
    'company' =>'Company',
    'user_target_monthly' => 'User Monthly Target',
    'outlet_category' => 'Outlet Category',
    'daily_schedule' => 'Daily Schedule',
    'return_type' => 'Return Type Damage',
    'no_sale_reason' => 'No Sale Reason',
    'manual_attendance_reason' => 'No Manual Attendance Reason',
    'mtp'=>'Monthly Tour Program',
    'user_wise_mtp'=>'User Wise MTP',
    'location4' => 'Depot',
    'location5' => 'Head Quater',
    'location6' => 'Town/City',
    'location7' => 'Beat',
    'locationFourTown' => 'Travelling Town',

    'csa' => 'Super Stockist',
    'user-mgmt' => 'User Management',
    'retailer_mgmt' => 'Retailer Management',
    'retailer' => 'Retailer',
    'dealer' => 'Stockist Management',
    'distributor' => 'Stockist',
    'dealer_module' => 'Stockist',
    'roleGroup' => 'Role Group',
    'add_dealer' => 'Add Stockist',
    'edit_dealer' => 'Edit Stockist',
    'sub_module' => 'Sub Module',
    'mtp_status'=> 'MTP Status',
    'mtp_not_interested_status'=> 'MTP Not Interested Status',
    'no_contacted_reason'=> 'Non Contacted Sale Reason',
    'super-stockist' => 'Super-Stockist Management',
    'l1' => 'Zone',
    'l2' => 'Region', // 'l2' => 'Area/Zone',
    'l3' => 'State',
    'l4' => 'Depot',
    'l5' => 'Head Quater',
    'l6' => 'Town/City',
    'l7' => 'Beat',
    'catalog_1_master' => 'Catalog-1 Master',
    'catalog_2_master' => 'Catalog-2 Master',
    'catalog_3_master' => 'Catalog-3 Master',
    'catalog_4_master' => 'Catalog-4 Master',
    'catalog_5_master' => 'Catalog-5 Master',
    'catalog_1' => 'Classification',
    'catalog_2' => 'Category',
    'catalog_3' => 'Product Name',
    'catalog_4' => 'SKU Name',
    'catalog_5' => 'Catalog-5',
    'catalog_product_master' => 'Catalog Product Master',
    'product_rate_list' => 'SKU Rate List',
    'product_rate' => 'Product Rate',
    'catalog_product' => 'SKU Name',
    'super_stock_mgmt' => 'Super Stockist Management',
    'super_stock' => 'Super Stockist',
    'super_stockist' => 'SuperStockist',
    'location_master' => 'Location Master',
    'incentive'=> 'Incentive',
    'scheme'=> 'Scheme Plan',
    'productScheme'=> 'Product Wise Scheme Plan',
    'location' => 'Location',
    'attendance' => 'Attendance',
    'no_attendance' => 'No Attendance',
    'no_booking' => 'No Booking',
    'beat_route' => 'Beat Route',
    'senior_hirarchy' => 'User Senior Hierarchy Report',
    'holiday' => 'Holiday',
    'attendance_report' => 'Attendance Report',
    'vendor' => 'Vendor',
    'module'=>'Module',
    'location1' => !empty($loc_array[0])?$loc_array[0]:'Zone',
    'location2' => !empty($loc_array[1])?$loc_array[1]:'Region',
    'location3' => !empty($loc_array[2])?$loc_array[2]:'State',
    'location4' => 'Depot',
    'location4TownExp' => !empty($loc_array[4])?$loc_array[4]:'Head Quater',
    'location5' => !empty($loc_array[4])?$loc_array[4]:'Head Quater',
    'location6' => !empty($loc_array[5])?$loc_array[5]:'Town/City',
    'location7' => !empty($loc_array[6])?$loc_array[6]:'Beat',
    'role'=>'Role Master',
    'role_key'=>'Role',
    'department'=>'Department',
    'usertracking' => 'User Tracking',
    'saleorder' => 'Sale Order',
    'sale_order_report' => 'Sale Order Report',
    'primary_sale_order_report' => 'Primary Sale Order Report',
    'order_id' => 'Order No',
    'usertracking' =>'User Tracking',
    'daily_tracking' =>'Daily Tracking',
    'focus_product' =>'Focus Product',
    'Deep_freeze_change' =>'Deep Freeze Change',
    'search_request' =>'Search Request',
    'request_approval_report' =>'Request Approval Report',
    'work_order' => 'Painting',
    'working' => 'Working',
    'hour' => 'Hour',
    'purchase_order_detail'=>'Purchase Order',
    'catalog_product_title'=>'Catalog Product Master',
    'catalog_product_add_button'=>'Add Catalog Product',
    'deep-freeze-issue'=>'Deep Freeze Issue',
    'machine_transfer'=>'Machine-Transfer',
    'survey_report_detail'=>'New Survey Report',
    'market_beat_plan' => 'Market Beat Plan',
    'daily_attendance' => 'Daily Attendance',
    'daily_performance' => 'Daily Performance Report',
    'payment_details' => 'Payment Details',
    'leave_type' => 'Leave Type',
    'competitors_new_product' => 'Competitors New Product',
    'pending_claim' => 'Pending Claim',
    'travelling_expenses' => 'Travelling Expense',
    'msp' => 'PRIMARY & SECONDARY PLANNING',
    'ucdp' => 'Ucdp',
    'board_review' => 'Board Review',
    'dwsst' => 'DISTRIBUTOR WISE SECONDARY SALES TREND',
    'isr_so_tgt' => 'ISR SO wise TGT month',
    'sales_trends' => 'Sales Trends',
    'outlet_opening_status' => 'Retailer Opening Status',
    'sales_review' => 'ASM & Above Sales Review',
    'aging' => 'Ageing',
    'distributor_stock_status' => 'Stockist Stock Status',
    'comp_price_intelligence' => 'Competitive Price Intelligence',
    'feedback' => 'Feedback',
    'tour_program' => 'Tour Program Report',
    'tour_prg' => 'Tour Program',
    'stock_in_hand' => 'Stock In Hand',
    'new_sd_dist_prospecting' => 'Prospecting Data',
    'product-investigation' => 'Product Investigation',
    'Complaint' => 'Complaint',
    'distributer_stock_report'=>'Distributer Stock Report',
    'distributer_sales_trend'=>'Distributer Secondary Sales Trend',
    'geofence' => 'Geo Fence',
    'manual_attendance' => 'Manual Attendance',
    'meeting_type' => 'Meeting Type',
    'type_of_meeting' => 'Type Of Meeting',
    'monthly_progressive' => 'Monthly Progressive',
    'export' => 'Export Master Data',
    'export_excel' => 'Export Excel',
    'date_wise_product_wise_report'=>'DATE WISE USER WISE REPORT',
    'daily-reporting-report'=>'Daily Reporting',
    'attendance-summary-report'=>'Attendance Summary Report',
    'call-time-summary-report'=>'First Call Time Report',
    'sales-team-attendance'=>'Sales Team/Man Attendance Report',
    'notification-non-contacted'=>' Notification Non Contacted Report',
    'user-complaint'=>'User Complaint Report',
    'mobile-on-off'=>'Mobile On/Off',
    'circular'=>'Circular',
    'liveTracking'=>'Field Force Tracking',
    'circular_report'=>'Circular Report',
    'expense_report' => 'Expense Report',
    'retailermovecopy' => 'Move/Copy',
    'import' => 'Import',
    'dealer_ownership' => 'Dealer Ownership',
    'outlet_type' => 'Outlet Type',
    'weight_type' => 'Weight Type',
    'app_module' => 'App Module',
    'app_sub_module' => 'App Sub Module',
    'working_type' => 'Working Type',
    'task_of_the_day' => ' Task Of The Day',
    'travelling_type' => 'Travelling Mode',
    'merchandise_type' => 'Merchandise Type',
    'gst_type' => 'Gst Type',
    'assign_module' => 'Assign Module',
    'assign_role_module' => 'Assign Role Wise Module',
    'MainMenu' => 'Main Menu',
    'Menu' => 'Sub Menu',
    'SubMenus' => 'Sub Sub Menu',
    'interface' => 'Interface Url',
    'urllist' => 'URL List',
    'editappmodule' => 'Edit App Module',
    'editappsubmodule' => 'Edit App Sub Module',
    'assign_url' => 'Assign URL',
    'editurllist' => 'Edit Assign URL List',
    'version' => 'Version Management',
    'dealer_beat_assign'=> 'Dealer Beat Assign',
    'product_type' =>'Unit Configuration',
    'product_rate_list_template'=> 'Product Rate List Template',
    'dms_order_reason'=> 'DMS Order Reasons',
    'dealer_counter_sale'=> 'Stockist Counter Sale',
    'dms_dealer_complaint'=> 'DMS Stockist Complaint Report',
    'payment_recieved'=> 'Payment Recieved Details',
    'template'=>'Template',
    'salary'=> 'Salary Management',
    'plant'=> 'Plant Master',
    'salesman_secondary_sales'=> 'Sales Man Secondary Sales Report',
    'salesman_secondary'=> 'Sales Man Secondary',
    'dealer_wise_ss'=> 'Dealer Wise SS Report',
    'target_ss'=> 'SS Target Report',
    'target_db'=> 'Stockist Target Report',
    'new_calling'=> 'New Calling',
    'order_enquiiry'=> 'Order Enquiry Form',
    'vehicle_details'=> 'Vehicle Management',
    '_vehicle_details'=> 'Vehicle Management',
    'document_upload'=> 'Dealer Document Upload ',
    'manual_tour_plan'=> 'Monthly Tour Plan',
    'dms_calling_type'=> 'DMS Calling Type',
    'dms_document_master'=> 'DMS Document Master',
    'dms_social_form_master'=> 'DMS Social Form Master',
    'dms_contact_details'=> 'DMS Contact Details',
    'dms_about_us_master'=> 'DMS About Us Master',
    'dms_notification_data'=> 'DMS Motification Data',
    'finalStock'=> 'Current Available Stock',
    'attendance_time_report'=> 'User Attendance Time Report',
    'dailyAttendanceEditReport'=> 'Daily Attendance Edit Report',
    'user_dealer_beat_details'=> 'User Dealer Beat Details',
    'customerOrderReport'=> 'Customer Order Report',
    'dsrMonthlyForNehaReport'=> 'User Wise SKU Place Report',
    'unbilledOutletReport'=> 'UnBilled Outlets Report',
    'distributorAssignReport'=> 'Stockist Assign Report',
    'skuWiseCounterSaleReport'=> 'SKU Wise Counter Wise Sale Report',

// dms lang start here 
     'quick_order'=> 'Quick Order',
    'search_by_order_no'=> 'Search By Order No',
    'seacrh_by_ref_no'=> 'Search By Ref No',
    'order_details_dms'=> 'Order Details',
    'new_order_dms'=> 'Create New Order',
    'order_no'=> 'Order No',
    'order_date'=> 'Order Date',
    'order_value'=> 'Order Value (Rs.)',
    'erp_order_no'=> 'Erp Order No',
    'remark'=> 'Remark',
    'date_range'=> 'Date Range Pcker',
    'per_page'=> 'Per Page',
    's_no'=> 'Sr.no',
    'order_history'=> 'Order History',
    'find'=> 'Find',

    'pro_forma_invoice'=> 'Pro Forma Invoice',
    'item_name'=> 'Item Name',
    'wholesale_rate'=> 'Rate',
    'sale_in_box_pcs'=> 'Sale in  
    Box/Pieces',
    'qty'=> 'Order 
    Quantity',
    'pcs'=> 'Quantity In 
    Pcs',
    'billed_qty'=> 'Billed Qty',
    'free_qty'=> 'Free Qty',
    'value'=> 'Value',
    'dms_dealer'=> 'Stockist',
    'dms-payment-details'=> 'Dealer Payment',
    'gift_master'=> 'Gift Master',
    'dms_social_link_master'=> 'Social',
    // dms lang ends here 
   


    
    
];
