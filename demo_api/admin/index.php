<?php
# index.php
/*
* This is the main page.
* This page includes the configuration file,
* the templates, and any content-specific modules.
*/
ini_set('session.gc_maxlifetime', 18000); // 3 hours set for session
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);

set_time_limit (660);// to increase the script execution time limit
@session_start();
@ob_start();
// Require the configuration file before any PHP code:
require_once ('./include/config.inc.php');

// once a token has been used we need to have a new token
if(!isset($_SESSION[SESS.'securetoken']) || empty($_SESSION[SESS.'securetoken']))
	$securetoken = $_SESSION[SESS.'securetoken']  = md5(uniqid(mt_rand(), true));
else
	$securetoken = $_SESSION[SESS.'securetoken'];

//Validate what page to show:
if(isset($_GET['option']))
	$p = $_GET['option'];
elseif(isset($_POST['option'])) // Forms
	$p = $_POST['option'];
else
	$p = NULL;
//suboption to do the subtask related to one major task
$suboption = !isset($_GET['suboption']) ? (isset($_POST['suboption']) ? $_POST['suboption'] : NULL) : $_GET['suboption'];

//if user is not logged in then dont allow him to access anything
if(!isset($_SESSION[SESS.'user'])) $p = NULL;

require_once(DB);
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-functions.php');

//To make redirection as and when redirect
function iredirect()
{
	if(!isset($_SESSION[SESS.'uname'])) {header ("Location: ".BASE_URL."index.php"); exit();}
}
//The page are available to only admin account
function admin_only_page()
{
	global $page;
	if($_SESSION[SESS.'data']['urole'] != 1) $page = 'payout/notfound.inc.php';
}
// Determine what page to display:
switch ($p)
{
	//----------------------------- MY AJAX Starts --------------------------------------------
	case 'myajax': //for using my own ajax files
	{
	   //if suboption not found  then error page will be displayed
	   switch($suboption)
	   {
		   case'ajax-delete':
			   $page = 'myajax/ajax-delete.php';
			   break;
		   case'ajax-general':
			   $page = 'myajax/ajax-general.php';
			   break;
		   case'ajax-datadiv':
			   $page = 'myajax/ajax-datadiv.php';
			   break;
		   case'ajax-pulldown':
			   $page = 'myajax/ajax-pulldown.php';
			   break;
		   case'ajax-project':
			   $page = 'myajax/ajax-project.php';
			   break;
			default:
				$page = 'notfound.php';
	   }
	   // if we are able to find the default ajax files, then use them
	   if(isset($page) && file_exists(BASE_URI_ROOT.ADMINFOLDER.'/modules/'.$page)){
			ob_end_clean();
			include(BASE_URI_ROOT.ADMINFOLDER.'/modules/'.$page);
			ob_start();
			exit();
	   }
	   else $p='notfound';
	   break;
	}
	//----------------------------- MY AJAX ENDS --------------------------------------------

	//----------------------------- Essential Options starts HERE --------------------------------------------
	#- User SUBMENU starts here -- #
        case 'myajax-autocomplete': //for display of the items categorywise
	{
	   require_once('./include/my-functions.php');
	   require_once('./modules/myajax/myajax.php');
	   exit();
	   break;
	}
	case 'set-default-rights': //for setting of the default user rights
	{
	   if(INDV_MODULE_CONTROL)
	   	$page = 'master/users/default-user-rights.inc.php';
	   else
	   	$page = 'master/users/default-user-rights_noindv.inc.php';
	   $page_title = 'Default User Rights :: '.GEN_TITLE;
	   break;
	}
	case 'add-user': //for addition of new users
	{
	   if(INDV_MODULE_CONTROL)
	   	$page = 'master/users/users.inc.php';
	   else
	   	$page = 'master/users/users_noindv.inc.php';
	   $page_title = 'Users :: '.GEN_TITLE;
	   break;
	}

	case 'ess-survey': //for addition of new users
	    {
	       $page = 'reports/mtp/ess_survey_report.php';
	       $page_title = 'ESS Survey Report :: '.GEN_TITLE;
	       break;
	    }
        case 'dealer': //for addition of new users
	{
	   $page = 'master/users/dealer.inc.php';
	   $page_title = 'Dealer :: '.GEN_TITLE;
	   break;
	}
	case 'Import-dealer': //for addition of new users
	{
	   $page = 'master/users/import_dealer.inc.php';
	   $page_title = 'Dealer :: '.GEN_TITLE;
	   break;
	}
	
	  ####################2018-05-26#######################################
       case 'daily-atteandance-modified': //for addition of new users
       {
          $page = 'track/daily_attendance_inc.php';
          $page_title = 'Daily Attendance Modified :: '.GEN_TITLE;
          break;
       }
       case 'daily-tracking-modified': //for addition of new users
	{
	   $page = 'track/daily_tracking_inc.php';
	   $page_title = 'Daily Tracking Modified :: '.GEN_TITLE;
	   break;
	}
	 case 'daily-checkout-modified': //for addition of new users
       {
          $page = 'track/daily_checkout_address_inc.php';
          $page_title = 'Daily Checkout Modified :: '.GEN_TITLE;
          break;
       }
#################2018-08-01###############################
	case 'daily-activity-reports': //for addition of new users
    {
       $page = 'reports/mtp/daily_activity_reports.php';
       $page_title = 'Daily Activity of User :: '.GEN_TITLE;
       break;
    }
///////////////////////////////////UPLOAD PDF ////////////////////////////////////////////////////////////
// case 'upload-pdf': //for addition of new users
//     {
//        $page = 'pdf/upload-pdf.php';
//        $page_title = 'Upload PDF :: '.GEN_TITLE;
//        break;p
//     }
case 'upload-pdf': //for addition of new users
	{
	   if(INDV_MODULE_CONTROL)
	   	$page = 'master/pdf/upload-pdf.php';
	   else
	   	$page = 'master/pdf/upload-pdf.php';
	   $page_title = 'Uploads :: '.GEN_TITLE;
	   break;
	}

       #######################################################
        case 'csa': //for addition of new users
	{
	   $page = 'master/users/csa.inc.php';
	   $page_title = 'CSA :: '.GEN_TITLE;
	   break;
	}
        case 'all_dealer_person': //for addition of new users
	{
	   $page = 'master/users/all_dealer_person.php';
	   $page_title = 'Dealer Login Details:: '.GEN_TITLE;
	   break;
	}
        case 'scheme': //for addition of new users
	{
	   $page = 'master/users/scheme.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
         case 'scheme-value': //for addition of new users
	{
	   $page = 'master/users/scheme-value.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
	case 'damage-details': //for addition of new users
	{
	   $page = 'reports/sales/damage-details-report.php';
	   $page_title = 'Damage Replace Report :: '.GEN_TITLE;
	   break;
	}
         case 'chalan-details': //for addition of new users
	{
	   $page = 'reports/sales/chalan-details-report.php';
	   $page_title = 'Chalan Report :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-scheme': //for addition of new users
	{
	   $page = 'master/users/dealer-scheme.inc.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
        //dealer-scheme
        case 'page-refresher': //for addition of new users
	{
	   $page = 'master/advanced/page-refresher.inc.php';
	   $page_title = 'Page Refresher :: '.GEN_TITLE;
	   break;
	}
        case 'add-dsp': //for addition of new users
	{

	   $page = 'dsp/add-dsp.inc.php';
	   $page_title = 'Add Dsp :: '.GEN_TITLE;
	   break;
	}

case 'user-monthly-reports': //for addition of new users
{

$page = 'sales/order-details/user-monthly-calls-reports.php';
$page_title = 'User records :: '.GEN_TITLE;
break;
}
        case 'add-dealer-user': //for addition of new users
	{
	   $page = 'dealer/add-dealer-user.inc.php';
	   $page_title = 'Add Dealer :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-balance-stock': //for addition of new users
	{
	   $page = 'master/users/dealer-balance-stock.inc.php';
	   $page_title = 'Dealer Balance Stock :: '.GEN_TITLE;
	   break;
	}
      case 'all_sales_reports': //for addition of new users
	{
		$page = 'mtp/dashboard.php';
		$page_title = 'SALES MONTHLY REPORT :: '.GEN_TITLE;
		break;
	}
        case 'sale-order-detailes': //for addition of new users
	{
	   $page = 'dealer/sale-order-details.inc.php';
	   $page_title = 'Dealer Sale Order :: '.GEN_TITLE;
	   break;
	}
        case 'add-company': //for addition of new users
	{
	   $page = 'dealer/company.inc.php';
	   $page_title = 'Dealer Company :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-add': //for addition of new users
	{
	   $page = 'dealer/retailer.inc.php';
	   $page_title = 'Retailer :: '.GEN_TITLE;
	   break;
	}

        case 'retailer-move': //for addition of new users
	{
	   $page = 'master/users/retailer_move.php';
	   $page_title = 'Retailer Move :: '.GEN_TITLE;
	   break;
	}

        case 'dealer-multi-wise-location': //for addition of new users
	{
	   $page = 'dealer/dealer-multi-wise-location.inc.php';
	   $page_title = 'Dealer Wise Location :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-user-sales': //for addition of new users
	{
	   $page = 'dealer/dealer-user-sales.inc.php';
	   $page_title = 'User Sales details :: '.GEN_TITLE;
	   break;
	}

		case 'dealer-user-info': //for addition of new users
        {
        $page = 'sales/order-details/dealer_user_info.php';
        $page_title = 'Dealer Users :: '.GEN_TITLE;
        break;
        }
        case 'person-senior-details': //for addition of new users
        {
        $page = 'sales/order-details/person-senior-details.php';
        $page_title = 'Users Team :: '.GEN_TITLE;
        break;
        }
	case 'laravel': //for addition of new users
{

$page = 'sales/order-details/laravel.php';
$page_title = 'Sale Order :: '.GEN_TITLE;
break;
}

	case 'sale-order-row': //for addition of new users
{

$page = 'sales/order-details/sale-order-rowwise.php';
$page_title = 'Sale Order :: '.GEN_TITLE;
break;
}
/// SALE SUMMARY EXPORT 11 FEB
case 'sale-summary-export': //for addition of new users
{

$page = 'sales/order-details/sale-summary-export.php';
$page_title = 'Sale Summary :: '.GEN_TITLE;
break;
}

/// Dealer Data Export 15 March
case 'dealer-beat-export': //for addition of new users
{

$page = 'sales/order-details/dealer_beat_export.php';
$page_title = 'Dealer Download :: '.GEN_TITLE;
break;
}


case 'Dealer-Stock': //for addition of new users
{

$page = 'sales/order-details/dealer_stock.php';
$page_title = 'Dealer Stock :: '.GEN_TITLE;
break;
}
        case 'dsp-wise-challan': //for addition of new users
	{
	   $page = 'challan/dsp-wise-challan.inc.php';
	   $page_title = 'DSP Wise Challan :: '.GEN_TITLE;
	   break;
	}
        case 'balance-stock': //for addition of new users
	{
	   $page = 'reports/stock/balance-stock.inc.php';
	   $page_title = 'Balance Stock :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-invoice': //for addition of new users
	{
	   $page = 'reports/sales/dealer_invoice.inc.php';
	   $page_title = 'Dealer Invoice :: '.GEN_TITLE;
	   break;
	}
        case 'stock_report': //for addition of new users
	{
	   $page = 'reports/stock/distributor-stock.php';
	   $page_title = 'Distributor Stock :: '.GEN_TITLE;
	   break;
	}
        case 'party-wise-ledger-report': //for addition of new users
	{
	   $page = 'reports/ledger/party-wise-ledger-report.inc.php';
	   $page_title = 'Party Wise Ledger Report :: '.GEN_TITLE;
	   break;
	}
        //balance-stock
        case 'dsp-challan-list': //for addition of new users
	{
	   $page = 'challan/dsp-challan-list.inc.php';
	   $page_title = 'DSP Challan List :: '.GEN_TITLE;
	   break;
	}
        case 'direct-challan': //for addition of new users
	{
	   $page = 'challan/direct-challan.inc.php';
	   $page_title = 'Direct Challan :: '.GEN_TITLE;
	   break;
	}
        case 'payment-collection': //for addition of new users
	{
	   $page = 'payment/payment-collection.inc.php';
	   $page_title = 'Payment Collection :: '.GEN_TITLE;
	   break;
	}
	case 'payment-enrollment': //for addition of new users
	{
	   $page = 'payment_enrollment/payment-enrollment.inc.php';
	   $page_title = 'Payment Enrolment :: '.GEN_TITLE;
	   break;
	}
        case 'daily-dispatch-details': //for addition of new users
	{
	   $page = 'dispatch/daily-dispatch-details.inc.php';
	   $page_title = 'Daily Dispatch :: '.GEN_TITLE;
	   break;
	}
        case 'location-fetch': //for addition of new users
	{
	   $page = 'master/users/location-fetch.inc.php';
	   $page_title = 'Dealer :: '.GEN_TITLE;
	   break;
	}
        case 'test-js': //for addition of new users
	{
	   $page = 'challan/test-js.php';
	   $page_title = 'Test Js :: '.GEN_TITLE;
	   break;
	}
        case 'file-import': //for addition of new users
	{

	   $page = 'important/zipper/zipper.inc.php';
	   $page_title = 'File Import :: '.GEN_TITLE;
	   break;
	}
        case 'user-tracking-distance': //for addition of new users
	{

	   $page = 'reports/sales/user-tracking-distance.inc.php';
	   $page_title = 'User Tracking Distance :: '.GEN_TITLE;
	   break;
	}

        case 'plumber': //for addition of new users
	{
	   $page = 'master/users/plumber.inc.php';
	   $page_title = 'Plumber :: '.GEN_TITLE;
	   break;
	}
        case 'order-details': //for addition of new users
	{

	   $page = 'sales/order-details/sale-order.inc.php';
	   $page_title = 'Sale Order :: '.GEN_TITLE;
	   break;
	}
        case 'order-details-location': //for addition of new users
	{

	   $page = 'reports/mtp/user-attendence-location.inc.php';
	   $page_title = 'Attendence Location :: '.GEN_TITLE;
	   break;
	}
        case 'primary-sale-details': //for addition of new users
	{
	   $page = 'sales/order-details/primary-sales.inc.php';
	   $page_title = 'Primary Sales :: '.GEN_TITLE;
	   break;
	}
        case 'complaint': //for addition of new users
	{
	   $page = 'sales/order-details/complaint.inc.php';
	   $page_title = 'Complaint :: '.GEN_TITLE;
	   break;
	}
        case 'retailer': //for addition of new users
	{

	   $page = 'master/users/retailer.inc.php';
	   $page_title = 'Retailer :: '.GEN_TITLE;
	   break;
	}

    case 'sale-month-report': //for addition of new users
	{
		$page = 'reports/sales/sale-month-report.php';
		$page_title = 'SALES MONTHLY REPORT :: '.GEN_TITLE;
		break;
	}
	case 'sale-month-report-npc': //for addition of new users
	{
		$page = 'reports/sales/sale-month-report-npc.php';
		$page_title = 'SALES MONTHLY REPORT :: '.GEN_TITLE;
		break;
	}

	case 'sale-month-report-pulse': //for addition of new users
	{
		$page = 'reports/sales/sale-month-report-pulse.php';
		$page_title = 'SALES MONTHLY REPORT PULSE:: '.GEN_TITLE;
		break;
	}

case 'sale-month-report-summary': //for addition of new users
{

$page = 'reports/sales/sale-month-report-summary.php';
$page_title = 'SALES MONTHLY REPORT SUMMARY :: '.GEN_TITLE;
break;
}

case 'customer-performance': //for addition of new users
{
$page = 'reports/sales/customer-performance.php';
$page_title = 'Customer Performance Report :: '.GEN_TITLE;
break;
}
case 'merchandise-report': //for addition of new users
{
$page = 'reports/sales/merchandise_report.php';
$page_title = 'Merchandise Report :: '.GEN_TITLE;
break;
}

case 'customer-performance-pulse': //for addition of new users
{
$page = 'reports/sales/customer-performance-pulse.php';
$page_title = 'Customer Performance Report Pulse:: '.GEN_TITLE;
break;
}

case 'rds-vs-billing':
{
	$page = 'reports/sales/rds-vs-billing.php';
	$page_title = 'RDS Retail Booking VS Billing :: '.GEN_TITLE;
	break;
}
case 'ss-stock':
{
	$page = 'reports/tally/ss-stock.php';
	$page_title = 'SS Tally Stock :: '.GEN_TITLE;
	break;
}
case 'ss-billing':
{
	$page = 'reports/tally/ss-billing.php';
	$page_title = 'SS Tally Billing :: '.GEN_TITLE;
	break;
}
case 'busy-ss-billing':
{
	$page = 'reports/busy/ss-billing.php';
	$page_title = 'SS Busy Billing :: '.GEN_TITLE;
	break;
}
case 'ss-closing-stock':
{
	$page = 'reports/tally/ss-closing-stock.php';
	$page_title = 'SS Closing Tally Stock :: '.GEN_TITLE;
	break;
}


case 'ss-closing-stock-pcs':
{
	$page = 'reports/tally/ss-closing-stock-pcs.php';
	$page_title = 'SS Closing Tally Stock :: '.GEN_TITLE;
	break;
}

case 'busy-ss-closing-stock':
{
	$page = 'reports/busy/ss-closing-stock.php';
	$page_title = 'SS Closing Busy Stock :: '.GEN_TITLE;
	break;
}

case 'rds-vs-billing-pulse':
{
	$page = 'reports/sales/rds-vs-billing-pulse.php';
	$page_title = 'RDS Retail Booking VS Billing Pulse:: '.GEN_TITLE;
	break;
}

case 'category-performance': //for addition of new users
{
$page = 'reports/sales/category-performance.php';
$page_title = 'Category Performance Report :: '.GEN_TITLE;
break;
}
case 'manpower-performance': //for addition of new users
{
$page = 'reports/sales/manpower_performance.php';
$page_title = 'Manpower Performance Report :: '.GEN_TITLE;
break;
}
case 'dropped-outlet-report': //for addition of new users
{
$page = 'reports/sales/dropped_outlet_report.php';
$page_title = 'Dropped Outlet Report :: '.GEN_TITLE;
break;
}
case 'dropped-outlet-report-pulse': //for addition of new users
{
$page = 'reports/sales/dropped_outlet_report_pulse.php';
$page_title = 'Dropped Outlet Report :: '.GEN_TITLE;
break;
}
case 'dropped-sku-report': //for addition of new users
{
$page = 'reports/sales/dropped_sku_report.php';
$page_title = 'Dropped SKU Report :: '.GEN_TITLE;
break;
}
case 'dropped-sku-report-pulse': //for addition of new users
{
$page = 'reports/sales/dropped_sku_report_pulse.php';
$page_title = 'Dropped SKU Report :: '.GEN_TITLE;
break;
}

case 'productive-outlets-tracking': //for addition of new users
{
$page = 'reports/sales/productive_outlets_tracking.php';
$page_title = 'Productive Outlets Tracking :: '.GEN_TITLE;
break;
}
case 'productive-outlets-tracking-pulse': //for addition of new users
{
$page = 'reports/sales/productive_outlets_tracking_pulse.php';
$page_title = 'Productive Outlets Tracking Pulse:: '.GEN_TITLE;
break;
}

case 'test': //for addition of new users
{
$page = 'reports/sales/test.php';
$page_title = 'Productive Outlets Tracking :: '.GEN_TITLE;
break;
}



   case 'dealer-sale-details': //for addition of new users
        {
        $page = 'sales/order-details/dealer-sale-details.php';
        $page_title = 'Dealer Sale :: '.GEN_TITLE;
        break;
        }



case 'state-level-performance': //for addition of new users
{
$page = 'reports/sales/state-level-performance.php';
$page_title = 'State Level Performance Report :: '.GEN_TITLE;
break;
}

case 'state-level-performance-pulse': //for addition of new users
{
$page = 'reports/sales/state-level-performance-pulse.php';
$page_title = 'State Level Performance Report :: '.GEN_TITLE;
break;
}
        case 'catalog_1': //for addition of new users
	{

	   $page = 'catalog/catalog_1.inc.php';
	   $page_title = 'Catalog :: '.GEN_TITLE;
	   break;
	}
        case 'catalog_2': //for addition of new users
	{

	   $page = 'catalog/catalog_2.inc.php';
	   $page_title = 'Catalog_2 :: '.GEN_TITLE;
	   break;
	}
         case 'case': //for addition of new users
	{

	   $page = 'catalog/case.php';
	   $page_title = 'Case :: '.GEN_TITLE;
	   break;
	}
         case 'dealer_target': //for addition of new users
	{

	   $page = 'catalog/dealer_target.php';
	   $page_title = 'Dealer Target :: '.GEN_TITLE;
	   break;
	}
        case 'catalog-product': //for addition of new users
	{
	   $page = 'catalog/catalog-product.inc.php';
	   $page_title = 'Catalog Product :: '.GEN_TITLE;
	   break;
	}

         case 'focus-product': //for addition of new users
	{
	   $page = 'catalog/focus-product.inc.php';
	   $page_title = 'Focus Product :: '.GEN_TITLE;
	   break;
	}
	case 'user-daily-report': //for addition of new users
    {
       $page = 'reports/mtp/user-daily-attn-expense-sale-report.inc.php';
       $page_title = 'User Attendence :: '.GEN_TITLE;
       break;
    }
        case 'pack-name': //for addition of new users
	{
	   $page = 'catalog/packname.inc.php';
	   $page_title = 'Pack Name :: '.GEN_TITLE;
	   break;
	}
        case 'product-company': //for addition of new users
	{
	   $page = 'catalog/product_company.inc.php';
	   $page_title = 'Product Company Name :: '.GEN_TITLE;
	   break;
	}
        case 'catalog-rate-list': //for addition of new users
	{
	   $page = 'catalog/catalog_rate_list.php';
	   $page_title = 'Catalog Rate List :: '.GEN_TITLE;
	   break;
	}
	 case 'Import-rate-list': //for addition of new users
	{
	   $page = 'catalog/import_rate_list.php';
	   $page_title = 'Catalog Rate List :: '.GEN_TITLE;
	   break;
	}
     ########### index page for setting Master start here ################
        case 'dealer-ownership': //for addition of new users
	{
	   $page = 'settings/dealer-ownership.inc.php';
	   $page_title = 'Dealer Ownership :: '.GEN_TITLE;
	   break;
	}
        case 'field-experience': //for addition of new users
	{
	   $page = 'settings/field-experiance.inc.php';
	   $page_title = 'Field Experience :: '.GEN_TITLE;
	   break;
	}
        case 'tracking-time': //for addition of new users
	{
	   $page = 'tracking/tracking-time.inc.php';
	   $page_title = 'Tracking Time :: '.GEN_TITLE;
	   break;
	}
        //tracking-time
        case 'retailer-mkt-gift': //for addition of new users
	{
	   $page = 'settings/retailer-mkt-gift.inc.php';
	   $page_title = 'Retailer Market Gift :: '.GEN_TITLE;
	   break;
	}
        case 'outlet-type': //for addition of new users
	{
	   $page = 'settings/outlet-type.inc.php';
	   $page_title = 'Outlet Type :: '.GEN_TITLE;
	   break;
	}
        case 'travelling-mode': //for addition of new users
	{
	   $page = 'settings/travelling-mode.inc.php';
	   $page_title = 'Travelling Mode :: '.GEN_TITLE;
	   break;
	}
        case 'working-status': //for addition of new users
	{
	   $page = 'settings/working-status.inc.php';
	   $page_title = 'Working Status :: '.GEN_TITLE;
	   break;
	}
        case 'tax-type': //for addition of new users
	{
	   $page = 'settings/tax.inc.php';
	   $page_title = 'Tax Type :: '.GEN_TITLE;
	   break;
	}
        case 'circular': //for addition of new users
	{
	   $page = 'circular/user-circular.inc.php';
	   $page_title = 'Circular :: '.GEN_TITLE;
	   break;
	}
	case 'circular_dealer': //for addition of new users
	{
	   $page = 'circular/dealer-circular.inc.php';
	   $page_title = 'Circular :: '.GEN_TITLE;
	   break;
	}
       //Report Section start here //
        case 'user-attendence': //for addition of new users
	{
	   $page = 'reports/mtp/user-attendance.inc.php';
	   $page_title = 'User Attendence :: '.GEN_TITLE;
	   break;
	}
        case 'monthly-tour-plan': //for addition of new users
	{
	   $page = 'reports/mtp/mtp.inc.php';
	   $page_title = 'Monthly Tour Plan :: '.GEN_TITLE;
	   break;
	}
	     case 'monthly-tour-plan-new': //for addition of new users
	{
	   $page = 'reports/mtp/mtp_new.inc.php';
	   $page_title = 'Monthly Tour Plan :: '.GEN_TITLE;
	   break;
	}
         case 'mtp': //for addition of new users
	{
	   $page = 'mtp/mtp-details.inc.php';
	   $page_title = 'Monthly Tour Plan :: '.GEN_TITLE;
	   break;
	}
        case 'user-sales-report': //for addition of new users
	{
	   $page = 'reports/sales/user-sales-report.inc.php';
	   $page_title = 'User Sales Report :: '.GEN_TITLE;
	   break;
	}
        case 'claim-pop': //for addition of new users
	{
	   $page = 'reports/sales/claim_pop.php';
	   $page_title = 'User Claim Report :: '.GEN_TITLE;
	   break;
	}
	 case 'winback': //for addition of new users
	{
	   $page = 'reports/sales/retailer-winback.php';
	   $page_title = 'Retailer WinBack :: '.GEN_TITLE;
	   break;
	}
         case 'user-claim-report': //for addition of new users
	{
	   $page = 'reports/sales/claim-report.php';
	   $page_title = 'User Claim Report :: '.GEN_TITLE;
	   break;
	}
         case 'claim-retailer-report': //for addition of new users
	{
	   $page = 'reports/sales/claim-retailer-report.php';
	   $page_title = 'User Claim Report :: '.GEN_TITLE;
	   break;
	}
         case 'sales-team-attendance': //for addition of new users
	{
	   $page = 'reports/sales/sales_team_attendance.php';
	   $page_title = 'Sales Team/Man Attendance Report :: '.GEN_TITLE;
	   break;
	}

          case 'attendance-summary': //for addition of new users
	{
	   $page = 'reports/sales/attendance_summary_team_wise.php';
	   $page_title = 'Attendance Summary Report :: '.GEN_TITLE;
	   break;
	}

          case 'sale-time-report': //for addition of new users
	{

	   $page = 'reports/sales/sale_time_report.inc.php';
	   $page_title = 'Sale Time Report :: '.GEN_TITLE;
	   break;
	}

         case 'user-complaint': //for addition of new users
	{
	   $page = 'reports/mtp/user-complaint.inc.php';
	   $page_title = 'User Complaint :: '.GEN_TITLE;
	   break;
	}

         case 'notification-non-contacted': //for addition of new users
	{
	   $page = 'reports/mtp/notification-non-contacted.inc.php';
	   $page_title = 'Non Contacted Notification Report :: '.GEN_TITLE;
	   break;
	}

         case 'user-daily-sales-report': //for addition of new users
	{
	   $page = 'reports/sales/user_daily_sales.php';
	   $page_title = 'User Daily sales :: '.GEN_TITLE;
	   break;
	}

         case 'division-sale-summary': //for addition of new users
	{
	   $page = 'reports/sales/division_wise_secondary_sale_summary.php';
	   $page_title = 'Division Sales Summary Report :: '.GEN_TITLE;
	   break;
	}

         case 'depot-ss-report': //for addition of new users
	{
	   $page = 'reports/sales/depot-ss-report.php';
	   $page_title = 'Depot SS Report :: '.GEN_TITLE;
	   break;
	}

         case 'sales-man-secondary': //for addition of new users
	{
	   $page = 'reports/sales/salesman_secondary_sale.php';
	   $page_title = 'Sales Man Secondary Report :: '.GEN_TITLE;
	   break;
	}

        case 'advance-summary-report': //for addition of new users
	{

	   $page = 'reports/sales/advance_summary_report.inc.php';
	   $page_title = 'Advance Summary Report :: '.GEN_TITLE;
	   break;


	}
	case 'sale-summary-report': //for addition of new users
	{

	   $page = 'reports/sales/sale_summary_report.inc.php';
	   $page_title = 'Sale Summary Report :: '.GEN_TITLE;
	   break;


	}
	 case 'user_count': //for addition of new users
	{

	   $page = 'reports/sales/user_count.inc.php';
	   $page_title = 'User Count :: '.GEN_TITLE;
	   break;


	}

         case 'rds-wise': //for addition of new users
	{

	   $page = 'reports/sales/rds-wise-sales-report.php';
	   $page_title = 'RDS WISE SALE :: '.GEN_TITLE;
	   break;
	}

         case 'dsr-monthly': //for addition of new users
	{

	   $page = 'reports/sales/dsr-month-wise.php';
	   $page_title = 'Dsr Monthly sales :: '.GEN_TITLE;
	   break;
	}

         case 'classification-wise-sales': //for addition of new users
	{
	   $page = 'reports/sales/classification-wise-sales-report.inc.php';
	   $page_title = 'Classification Wise Sales Report :: '.GEN_TITLE;
	   break;
	}

        case 'detail-report-advanced': //for addition of new users
	{
	   $page = 'reports/sales/detail_report_advanced.php';
	   $page_title = 'Dealer Sale Order :: '.GEN_TITLE;
	   break;
	}

        case 'damage-replace': //for addition of new users
	{

	   $page = 'reports/sales/damage-replace-report.php';
	   $page_title = 'Damage Replace Report :: '.GEN_TITLE;
	   break;
	}
	case 'damage-replace-retailer': //for addition of new users
	{

	   $page = 'reports/sales/damage-replace-retailer-report.php';
	   $page_title = 'Damage Replace Retailer Report :: '.GEN_TITLE;
	   break;
	}
	 case 'dealer-payment-reports': //for addition of new users
	{

	   $page = 'reports/sales/dealer_payment_report.php';
	   $page_title = 'Dealer Paymentt Report :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-payment-reports': //for addition of new users
	{

	   $page = 'reports/sales/retailer_payment_report.php';
	   $page_title = 'Retailer Paymentt Report :: '.GEN_TITLE;
	   break;
	}
    case 'user-leave-request-reports': //for leave request
    {

        $page = 'reports/sales/user_leave_request_reports.php';
        $page_title = 'User Leave Request :: '.GEN_TITLE;
        break;
    }
    case 'tour-and-advance-expense-reports': //for tour and advance expense report
    {

        $page = 'reports/sales/tour_and_advance_expense_report.php';
        $page_title = 'Tour And Advance Expense Report :: '.GEN_TITLE;
        break;
    }
        case 'no-attendance': //for addition of new users
	{
	   $page = 'reports/sales/no-attendance-report.inc.php';
	   $page_title = 'No Attendence Report :: '.GEN_TITLE;
	   break;
        }

         case 'no-sale': //for addition of new users
	{
	   $page = 'reports/sales/no-sale-report.inc.php';
	   $page_title = 'No Booking Report :: '.GEN_TITLE;
	   break;
	}
	case 'used-function-report': //for addition of new users
	    {
	       $page = 'reports/sales/used-function-report.inc.php';
	       $page_title = 'Used Function Report :: '.GEN_TITLE;
	       break;
	    }

        case 'user-expanse-report': //for addition of new users
	{
	   $page = 'reports/sales/user-expanse-report.inc.php';
	   $page_title = 'User Expanse Report :: '.GEN_TITLE;
	   break;
	}
	case 'user-sale-yearly-report': //for addition of new users
	{
	   $page = 'reports/sales/user-sale-yearly-report.inc.php';
	   $page_title = 'User Sale Report :: '.GEN_TITLE;
	   break;
	}

	case 'new-rep': //for addition of new users
		{
		   $page = 'reports/sales/new-rep.inc.php';
		   $page_title = 'New Report :: '.GEN_TITLE;
		   break;
		}
		case 'ret-new-rep': //for addition of new users
		{
		   $page = 'reports/sales/retailer-billing-new-rep.inc.php';
		   $page_title = 'New Report :: '.GEN_TITLE;
		   break;
		}
        case 'user-tracking-report': //for addition of new users
	{
	   $page = 'reports/sales/user-tracking-report.inc.php';
	   $page_title = 'User Tracking Report :: '.GEN_TITLE;
	   break;
	}
	case 'user-tracking-report-new': //for addition of new users
	{
	   $page = 'reports/sales/user-tracking-report-new.inc.php';
	   $page_title = 'User Tracking Report :: '.GEN_TITLE;
	   break;
	}
        case 'branch-staff-details': //for addition of new users
	{
	   $page = 'reports/branch/branch-staff-details.inc.php';
	   $page_title = 'Branch Staff Details :: '.GEN_TITLE;
	   break;
	}
        case 'sale-order-details': //for addition of new users
	{
	   $page = 'reports/sales/sale-order-details.inc.php';
	   $page_title = 'Sale Order Details :: '.GEN_TITLE;
	   break;
	}
        //sale-order-details
         case 'test': //for addition of new users
	{
	   $page = 'master/testfile.php';
	   $page_title = 'Test File :: '.GEN_TITLE;
	   break;
	}
        //test
	case 'history-log':
	{
	   $page = 'master/history-log.inc.php';
	   $page_title = 'History Log :: '.GEN_TITLE;
	   break;
	}
	case 'session-year':
	{
	   $page = 'master/session/session-year.inc.php';
	   $page_title = 'Session Year :: '.GEN_TITLE;
	   break;
	}
	case 'location':
	{
	   $page = 'location/location-master.inc.php';
	   $page_title = 'Location Master :: '.GEN_TITLE;
	   break;
	}
        #location-category
        case 'location-category':
	{
	   $page = 'location/location-category.inc.php';
	   $page_title = 'Location Category :: '.GEN_TITLE;
	   break;
	}
	#Location position
    case 'location-position':
    {
        $page = 'location/location-position.inc.php';
        $page_title = 'Location wise position :: '.GEN_TITLE;
        break;
    }
    #Position Master
    case 'position-master':
    {
        $page = 'location/position-master.inc.php';
        $page_title = 'Position Master:: '.GEN_TITLE;
        break;
    }
	case 'change-password': //for viewing the customers
	{
	   $page = 'misc/change-password.inc.php';
	   $page_title = 'Change A/c Password :: '.GEN_TITLE;
	   break;
	}
	 //------------------------------------EXPORT TO EXCEL---------------------------------------------------//
        case 'export-retailer': //for addition of new users
	{
	   $page = 'excel/export_retailer.php';
	   $page_title = 'Export Retailer Data :: '.GEN_TITLE;
	   break;
	}
         case 'export-sec-sales': //for addition of new users
	{
	   $page = 'excel/export_sec_sales_report.php';
	   $page_title = 'Export Secondary Sale Report :: '.GEN_TITLE;
	   break;
	}
        case 'export-attn-data': //for addition of new users
	{
	   $page = 'excel/export_attn_data_report.php';
	   $page_title = 'Export Attendance Report :: '.GEN_TITLE;
	   break;
	}

	//----------------------------- Essential Options ends HERE --------------------------------------------
        case 'scheme': //for viewing the customers
	{
	   $page = 'master/scheme/create-scheme.inc.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
	case 'circular': //for viewing the customers
	{
	   $page = 'circular/user-circular.inc.php';
	   $page_title = 'Circular :: '.GEN_TITLE;
	   break;
	}
        case 'scheme-on-sale':
	{
	   $page = 'master/users/scheme-on-sale.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
	 case 'purchase-order': //for addition of new users
	{
	   $page = 'sales/order-details/purchase-order.inc.php';
	   $page_title = 'Purchase Order :: '.GEN_TITLE;
	   break;
	}
	case 'user-detailed-report': //for addition of new users
{

$page = 'reports/sales/user_detailed_report.inc.php';
$page_title = 'User Detailed Report :: '.GEN_TITLE;
break;
}

	case 'dealer-report':
	{
	   $page = 'reports/sales/dealer-report.inc.php';
	   $page_title = 'Distributor Report :: '.GEN_TITLE;
	   break;
	}
	case 'circular-report': //for viewing the customers
	{
	   $page = 'circular/user-circular-report.inc.php';
	   $page_title = 'Circular Report :: '.GEN_TITLE;
	   break;
	}
        case 'scheme': //for viewing the customers
	{
	   $page = 'scheme/scheme.inc.php';
	   $page_title = 'Scheme :: '.GEN_TITLE;
	   break;
	}
        //scheme
        case 'switch-company':
	{
	   $page = 'login/switch-company.php';
	   $page_title = 'Switch Company :: '.GEN_TITLE;
	   break;
	}
	case 'logout':
	{
	   $page = 'login/logout.php';
	   $page_title = 'Logout :: '.GEN_TITLE;
	   break;
	}
	//----------------------------- Essential Options ends HERE --------------------------------------------

	// ----------------------------- Master for Software Starts --------------------------------------------
	#- Party SUBMENU start here -- #
	case 'party-vendor':
	{
	   $page = 'master/party/vendor.inc.php';
	   $page_title = 'Vendor :: '.GEN_TITLE;
	   break;
	}
         case 'item-import': //for addition of new users
	{

	   $page = 'catalog/inputform.php';
	   $page_title = 'Item Import :: '.GEN_TITLE;
	   break;
	}
	case 'party-customer':
	{
	   $page = 'master/party/customer.inc.php';
	   $page_title = 'Customer :: '.GEN_TITLE;
	   break;
	}
	#- Item SUBMENU start here -- #
	case 'item':
	{
	   $page = 'master/item/item.inc.php';
	   $page_title = 'Item :: '.GEN_TITLE;
	   break;
	}
	case 'item-unit':
	{
	   $page = 'master/item/item-unit.inc.php';
	   $page_title = 'Item Unit :: '.GEN_TITLE;
	   break;
	}
	case 'process-plan':
	{
	   $page = 'master/item/process-plan.inc.php';
	   $page_title = 'Process Plan :: '.GEN_TITLE;
	   break;
	}
	case 'nesting':
	{

	   $page = 'master/item/nesting.inc.php';
	   $page_title = 'Nesting :: '.GEN_TITLE;
	   break;
	}
	case 'plate-planner':
	{
	   $page = 'master/item/plate-planner.inc.php';
	   $page_title = 'Plate Planner :: '.GEN_TITLE;
	   break;
	}
	case 'job-order':
	{
	   $page = 'annexure/job-order.inc.php';
	   $page_title = 'Job order :: '.GEN_TITLE;
	   break;
	}
	case 'annexure':
	{
	   $page = 'annexure/annexure.inc.php';
	   $page_title = 'Annexure :: '.GEN_TITLE;
	   break;
	}

	case 'make-invoice':
	{
	   $page = 'sales/invoice/invoice.inc.php';
	   $page_title = 'Invoice :: '.GEN_TITLE;
	   break;
	}
	case 'retailer-stock': //for addition of new users
{

$page = 'sales/order-details/retailer-stock.inc.php';
$page_title = 'Retailer Stock :: '.GEN_TITLE;
break;
}
	// ----------------------------- Sales for Software Ends --------------------------------------------
	// ----------------------------- Report for Software Starts --------------------------------------------
	##### stock reports

        case 'item-stock':
	{
	   $page = 'reports/stock/item-stock.inc.php';
	   $page_title = 'Product Stock Report :: '.GEN_TITLE;
	   break;
	}
	 case 'time-report': //for addition of new users
    {

       $page = 'reports/sales/time_report.php';
       $page_title = 'Time report :: '.GEN_TITLE;
       break;
    }
    case 'time-report-attd': //for addition of new users
    {

       $page = 'reports/sales/time_report_attd.php';
       $page_title = 'Time report :: '.GEN_TITLE;
       break;
    }
     case 'isr-sales-report': //for addition of new users
    {

       $page = 'reports/sales/isr_sale_report.php';
       $page_title = 'ISR Sales Report :: '.GEN_TITLE;
       break;
    }
    case 'utilization':
    {
       $page = 'reports/d_utilization.php';
       $page_title = 'Distributor Utilization';
       break;
    }
    case 'dealer-billing-report':
    {
       $page = 'reports/dealer_billing_report.php';
       $page_title = 'Dealer Billing Report';
       break;
    }
    case 'dealer-billing-report-lite':
    {
       $page = 'reports/dealer_billing_report_lite.php';
       $page_title = 'Dealer Billing Report - Lite App';
       break;
    }
    case 'product-billing-report':
    {
       $page = 'reports/product_billing_report.php';
       $page_title = 'Product Billing Report';
       break;
    }

    case 'challan-report':
    {
       $page = 'reports/challan_report.php';
       $page_title = 'Challan Report';
       break;
    }
    case 'dealer-stock-report':
    {
       $page = 'reports/sales/dealer_stock_report.php';
       $page_title = 'Dealer Stock Report';
       break;
    }
    case 'dealer-stock-report-new':
    {
       $page = 'reports/sales/dealer_stock_report_new.php';
       $page_title = 'Dealer Stock Report';
       break;
    }
    case 'dealer-stock-report-pulse':
    {
       $page = 'reports/sales/dealer_stock_report_pulse.php';
       $page_title = 'Dealer Stock Report Pulse';
       break;
    }

    case 'stock-details':
    {
       $page = 'reports/sales/stock_detail_report.php';
       $page_title = 'Stock Report';
       break;
    }

    case 'challan-report-print':
    {
       $page = 'reports/challan_report_print.php';
       $page_title = 'Challan Report';
       break;
    }

    case 'user-sale-details':
    {
       $page = 'reports/sales/user-sale-details.php';
       $page_title = 'Whatsapp format report';
       break;
    }
    case 'user-product-sale-details':
    {
       $page = 'reports/sales/user-product-sale-details.php';
       $page_title = 'Whatsapp product format report';
       break;
    }
    case 'user-product-sale-details-pulse':
    {
       $page = 'reports/sales/user-product-sale-details-pulse.php';
       $page_title = 'User Performance Report';
       break;
    }
    case 'challan-sale':
    {
       $page = 'sales/order-details/order-vs-challan.inc.php';
       $page_title = 'Challan vs Sale';
       break;
    }
    case 'user-sku-report': //for addition of new users
       {
          $page = 'reports/sales/sku-report.php';
          $page_title = 'User Sku Report :: '.GEN_TITLE;
          break;
       }

    case 'user-retailer-report': //for addition of new users
       {
          $page = 'reports/sales/retailer-report.php';
          $page_title = 'User Retailer Report :: '.GEN_TITLE;
          break;
       }
	# ----------------------------- Miscellaneous for OFP Ends --------------------------------------------
   //Default is to include the main page.
   default:
   {
	   if(!isset($_SESSION[SESS.'user']))
	   {
	   	   $page = 'login/login.php';
	   	   $page_title = 'Login :: '.GEN_TITLE;
	   }
	   elseif(basename($_SERVER['REQUEST_URI']) == 'index.php')
	   {
               if($_SESSION[SESS.'data']['role_group_id'] == 11)
	   	   $page = 'login/welcome.php';
               else if($_SESSION[SESS.'data']['role_group_id'] == 22 && empty($_SESSION[SESS.'data']['company_id']))
                   $page = 'login/welcome-dealer.php';
                else if($_SESSION[SESS.'data']['role_group_id'] == 22 && !empty($_SESSION[SESS.'data']['company_id']))
                   $page = 'login/welcome.php';

                $page_title = 'Welcome '.$_SESSION[SESS.'data']['name'].' :: '.GEN_TITLE;
	   }
	   else
	   {
		   $page = 'login/notfound.php';
	   	   $page_title = 'Error Occured '.$_SESSION[SESS.'data']['name'].' :: '.GEN_TITLE;
	   }
	   break;
   }
} // End of main switch.

//Make sure the file exists:
//echo './modules/' . $page;die;
if (!file_exists('./modules/' . $page)){
	//echo 'jhgkhfkghdfkg';die;
	$page = 'login/notfound.php';
	$page_title = GEN_TITLE;
}
//Calculating the page showmode
$showmode = 3;
if($_SESSION[SESS.'data']['role_group_id'] == 22 && empty($_SESSION[SESS.'data']['company_id'])) {
 $showmode = 1;
}

if(isset($_GET['showmode'])){
	$tshowmode = (int)$_GET['showmode'];
	if($tshowmode == 1 || $tshowmode == 2 || $tshowmode == 3) $showmode = $tshowmode;
}

// Include the header file:
$showmode == 1 ? require_once ('./include/headerpop.php'): require_once ('./include/header.php');
// Include the content-specific module:
// $page is determined from the above switch.
include ('./modules/' . $page);

// Include the footer file to complete the template:
$showmode == 1 ? require_once ('./include/footerpop.php'): require_once ('./include/footer.php');
?>
