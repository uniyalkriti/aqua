<?php 
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

// Validate what page to show:
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
require_once(BASE_URI_A.'/include/my-functions.php');

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
	case 'change-password': //for viewing the customers
	{
	   $page = 'misc/change-password.inc.php';
	   $page_title = 'Change A/c Password :: '.GEN_TITLE;
	   break;
	}
        case 'backup-db': //for viewing the customers
	{
	   $page = 'misc/backupdb.inc.php';
	   $page_title = 'BackUp DB:: '.GEN_TITLE;
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
	case 'company-add':
	{
           
	   $page = 'company/company-add.inc.php';
	   $page_title = 'Company Add :: '.GEN_TITLE;
	   break;
	}
        case 'daily-dispatch-details': //for addition of new users
	{
	   $page = 'dispatch/daily-dispatch-details.inc.php';
	   $page_title = 'Daily Dispatch :: '.GEN_TITLE;
	   break;
	}
        
        case 'stock-age-report': //for addition of new users
        {
            $page = 'dispatch/stock-age.inc.php';
            $page_title = 'Stock Age :: '.GEN_TITLE;
            break;
        }
	    case 'daily-dispatch-report': //for addition of new users
	{
	   $page = 'dispatch/daily-dispatch-details-report.inc.php';
	   $page_title = 'Daily Dispatch :: '.GEN_TITLE;
	   break;
	}
        case 'intransit-dispatch-report': //for addition of new users
        {
            $page = 'dispatch/intransit-dispatch-details-report.inc.php';
            $page_title = 'Intransit Dispatch Report:: '.GEN_TITLE;
            break;
        }
        case 'payment-collection': //for addition of new users
	{
	   $page = 'payment/payment-collection.inc.php';
	   $page_title = 'Payment Collection :: '.GEN_TITLE;
	   break;
	}
        case 'payment': //for addition of new users
	{
	   $page = 'payment/payment_collection.php';
	   $page_title = 'Payment Collection :: '.GEN_TITLE;
	   break;
	}
	 case 'help': //for viewing the customers
    {
       $page = 'misc/help-desk.inc.php';
       $page_title = 'Help Desk :: '.GEN_TITLE;
       break;
    }
	   case 'payment-collection-save': //for addition of new users
	{
	   $page = 'payment/payment-save.php';
	   $page_title = 'Payment Collection :: '.GEN_TITLE;
	   break;
	}
	
	    case 'payment-collection-report': //for addition of new users
	{
	   $page = 'payment/payment-collection-report.inc.php';
	   $page_title = 'Payment Collection :: '.GEN_TITLE;
	   break;
	}
        case 'sku-wise-report': //for addition of new users
	{
	   $page = 'reports/ledger/sku-wise-report.inc.php';
	   $page_title = 'SKU Wise Report :: '.GEN_TITLE;
	   break;
	}
	case 'payment-enrollment': //for addition of new users
	{
	   $page = 'payment_enrollment/payment-enrollment.inc.php';
	   $page_title = 'Payment Enrolment :: '.GEN_TITLE;
	   break;
	}
         case 'balance-stock': //for addition of new users
	{
	   $page = 'reports/stock/balance-stock.inc.php';
	   $page_title = 'Balance Stock :: '.GEN_TITLE;
	   break;
	}
        case 'balance-stock-edit': //for addition of new users
	{
	   $page = 'reports/stock/balance-stock.edit.inc.php';
	   $page_title = 'Balance Stock :: '.GEN_TITLE;
	   break;
	}
        case 'tax_inv_sale_report': //for addition of new users
	{
	   $page = 'reports/stock/tax_inv_sale_report.php';
	   $page_title = 'Tax_inv_sale_report :: '.GEN_TITLE;
	   break;
	}
        case 'tax_register_report': //for addition of new users
	{
	   $page = 'reports/stock/tax_register_report.php';
	   $page_title = 'Tax Register Report :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-reach': 
        {
            $page = 'login/retailer-reach.php';
            $page_title = 'Reatiler Reach :: '.GEN_TITLE;
            break;
        }
         case 'party-wise-ledger-report': //for addition of new users
	{
	   $page = 'reports/ledger/party-wise-ledger-report.inc.php';
	   $page_title = 'Party Wise Ledger Report :: '.GEN_TITLE;
	   break;
	}
         case 'purchase-order': //for addition of new users
	{
	   $page = 'sales/order-details/purchase-order.inc.php';
	   $page_title = 'Purchase Order :: '.GEN_TITLE;
	   break;
	}
        
        case 'claim-pop': //for addition of new users
	{
	   $page = 'login/claim_pop.php';
	   $page_title = 'Claim :: '.GEN_TITLE;
	   break;
	}
         case 'import': //for addition of new users
	{
	   $page = 'sales/order-details/import.php';
	   $page_title = 'Import :: '.GEN_TITLE;
	   break;
	}
          case 'received-order': //for addition of new users
	{
	   $page = 'sales/order-details/received-order.inc.php';
	   $page_title = 'Receive Order :: '.GEN_TITLE;
	   break;
	}
	    case 'focus-product': //for addition of new users
	{
	   $page = 'catalog/focus-product.inc.php';
	   $page_title = 'Focus Product :: '.GEN_TITLE;
	   break;
	}

 case 'catalog-rate-list': //for addition of new users
	{
	   $page = 'catalog/catalog_rate_list.php';
	   $page_title = 'Catalog Rate List :: '.GEN_TITLE;
	   break;
	}
 case 'user-aging-report': //for challan & achievment
    {
       $page = 'reports/ledger/user-aging-report.php';
       $page_title = 'User Aging Report :: '.GEN_TITLE;
       break;
    }
case 'user-sale-performance': //for challan & achievment
{
$page = 'reports/user-sales-performance.php';
$page_title = 'User Sales Performance :: '.GEN_TITLE;
break;
}
case 'retailer-sale-performance': //for challan & achievment
{
$page = 'reports/retailer-sales-performance.php';
$page_title = 'Retailer Sales Performance :: '.GEN_TITLE;
break;
} 
        case 'opening-stock': //for addition of new users
	{
	   $page = 'sales/order-details/opening-stock.inc.php';
	   $page_title = 'Stock :: '.GEN_TITLE;
	   break;
	}
        case 'primary-sale-details': //for addition of new users
	{
	   $page = 'sales/order-details/primary-sales.inc.php';
	   $page_title = 'Primary Sales :: '.GEN_TITLE;
	   break;
	}
        case 'intransit-details': //for addition of new users
	{
	   $page = 'sales/order-details/intransit.inc.php';
	   $page_title = 'Intransit Report :: '.GEN_TITLE;
	   break;
	}
        case 'company-import': //for addition of new users
	{
	   $page = 'company/company-import.inc.php';
	   $page_title = 'Company Import :: '.GEN_TITLE;
	   break;
	}
        case 'complaint-report': //for addition of new users
	{
	   $page = 'login/complaint_report.inc.php';
	   $page_title = 'Complaint Report :: '.GEN_TITLE;
	   break;
	}
            case 'sync': 
	{
	   //$page = 'sync/sync.php';
          $page = 'sync/sync_json.php';      
	   $page_title = 'SYNC PROCESS... :: '.GEN_TITLE;
	   break;
	}
        case 'sync-data': 
	{
	   //$page = 'sync/sync.php';
          $page = 'sync/sync-step.inc.php';      
	   $page_title = 'SYNC PROCESS... :: '.GEN_TITLE;
	   break;
	}
        
        case 'sale-order-detailes': //for addition of new users
	{
	   $page = 'dealer/sale-order-details.inc.php';
	   $page_title = 'Dealer Sale Order :: '.GEN_TITLE;
	   break;
	}
        case 'add-van': //for addition of new users
	{
	   $page = 'vendor/add-van.inc.php';
	   $page_title = 'Add Van :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-reach':
{
$page = 'login/retailer-reach.php';
$page_title = 'Reatiler Reach :: '.GEN_TITLE;
break;
}
case 'total-bill':
{
$page = 'login/total-bill.php';
$page_title = 'Total Bill :: '.GEN_TITLE;
break;
}
case 'total-recieve':
{
$page = 'login/total-recieve.php';
$page_title = 'Total Recieve :: '.GEN_TITLE;
break;
}
case 'total-payment-collection':
{
$page = 'login/total-payment-collection.php';
$page_title = 'Total Payment Collection :: '.GEN_TITLE;
break;
} 
        //add-van
        case 'location': 
	{
	   $page = 'location/location-master.inc.php';
	   $page_title = 'Location Master :: '.GEN_TITLE;
	   break;
	}
        case 'dsp-wise-challan': //for addition of new users
	{
	   $page = 'challan/dsp-wise-challan.inc.php';
	   $page_title = 'DSP Wise Challan :: '.GEN_TITLE;
	   break;
	}
         case 'claim-order': //for addition of new users
	{
	   $page = 'challan/claim-order.php';
	   $page_title = 'Claim On Sale :: '.GEN_TITLE;
	   break;
	}
         case 'welcome': //for addition of new users
	{
	 $page = 'login/welcome.php';
	 $page_title = 'welcome :: '.GEN_TITLE;
	   break;
	}
        case 'challan-report-details': //for challan & achievment
    {
       $page = 'reports/stock/challan_achievement.php';
       $page_title = 'Challan Achievemnet :: '.GEN_TITLE;
       break;
    }
    case 'aging-report': //for challan & achievment
    {
       $page = 'reports/ledger/aging-report.php';
       $page_title = 'Aging Report :: '.GEN_TITLE;
       break;
    }
     case 'sale-report': //for challan & achievment
    {
       $page = 'reports/ledger/sale-report.php';
       $page_title = 'Sale Report :: '.GEN_TITLE;
       break;
    }
      case 'purchase-report': //for challan & achievment
    {
       $page = 'reports/ledger/purchase-report.php';
       $page_title = 'Purchase Report :: '.GEN_TITLE;
       break;
    }
        case 'claim-target': //for addition of new users
	{
	   $page = 'challan/claim-target.php';
	   $page_title = 'Claim On Target :: '.GEN_TITLE;
	   break;
	}
		
         case 'retailer-claim': //for addition of new users
	{
	   $page = 'challan/retailer_claim.php';
	   $page_title = 'Retailer Claim :: '.GEN_TITLE;
	   break;
	}
        case 'claim-report': //for addition of new users
	{
	   $page = 'reports/ledger/claim-report.php';
	   $page_title = 'Claim Challan :: '.GEN_TITLE;
	   break;
	}
        
        
	case 'make-payment': //for addition of new users
	{
	   $page = 'challan/make-payment.inc.php';
	   $page_title = 'Make Payment :: '.GEN_TITLE;
	   break;
	}
        case 'location-category': 
	{
	   $page = 'location/location-category.inc.php';
	   $page_title = 'Location Category :: '.GEN_TITLE;
	   break;
	}
         case 'dispatch-beat': 
	{
	   $page = 'location/dispatch_beat.php';
	   $page_title = 'Dispatch Beat :: '.GEN_TITLE;
	   break;
	}
        case 'vendor-purchase': 
	{
	   $page = 'vendor/vendor.inc.php';
	   $page_title = 'Vendor Purchase :: '.GEN_TITLE;
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
        case 'catalog-product': //for addition of new users
	{
	   $page = 'catalog/catalog-product.inc.php';
	   $page_title = 'Catalog Product :: '.GEN_TITLE;
	   break;
	}
        case 'pie-achieved': //for addition of new users
	{
	   $page = 'login/pie-achieved.inc.php';
	   $page_title = 'Catalog Product :: '.GEN_TITLE;
	   break;
	}
        case 'make-challan': //for addition of new users
	{
	   $page = 'challan/make-challan.inc.php';
	   $page_title = 'Make Challan :: '.GEN_TITLE;
	   break;
	}
         case 'return-challan': //for addition of new users
	{
	   $page = 'challan/return-challan.php';
	   $page_title = 'Return Challan :: '.GEN_TITLE;
	   break;
	}
        case 'direct-make-challan': //for addition of new users
	{
	   $page = 'challan/direct_make_challan.php';
	   $page_title = 'Make Challan :: '.GEN_TITLE;
	   break;
	}
        
       case 'damage-challan': //for addition of new users
	{
	   $page = 'challan/damage-challan.inc.php';
	   $page_title = 'Damage Challan :: '.GEN_TITLE;
	   break;
	}
	    case 'damage-Replace': //for addition of new users
	{
	   $page = 'challan/damage-details-replace.php';
	   $page_title = 'Damage Replace :: '.GEN_TITLE;
	   break;
	}
	   case 'Invoice-Summary': 
	{
	   $page = 'challan/Invoice-Summary.inc.php';
	   $page_title = 'Invoice Summary :: '.GEN_TITLE;
	   break;
	}
        case 'add-user': //for addition of new users
	{
	   $page = 'dealer/add-dealer-user.inc.php';
	   $page_title = 'Add Dealer :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-multi-wise-location': //for addition of new users
	{
	   $page = 'dealer/dealer-multi-wise-location.inc.php';
	   $page_title = 'Dealer Wise Location :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-add': //for addition of new users
	{
	   $page = 'retailer/retailer.inc.php';
	   $page_title = 'Retailer :: '.GEN_TITLE;
	   break;
	}
        case 'profile-detail': //for addition of new users
    {
       $page = 'login/welcome.php';
       $page_title = 'Profile Details :: '.GEN_TITLE;
       break;
    }
         case 'dsp-challan-list': //for addition of new users
	{
	   $page = 'challan/dsp-challan-list.inc.php';
	   $page_title = 'DSP Challan List :: '.GEN_TITLE;
	   break;
	}
        case 'direct-challan': //for addition of new users
	{
	   $page = 'challan/direct-challan-pop.php';
	   $page_title = 'Direct Challan :: '.GEN_TITLE;
	   break;
	}
        
        case 'test': //for addition of new users
	{
	   $page = 'challan/test.php';
	   $page_title = 'Test Challan :: '.GEN_TITLE;
	   break;
	}
        
         case 'challan-edit': //for addition of new users
	{
	   $page = 'challan/challan-edit.php';
	   $page_title = 'Direct Challan :: '.GEN_TITLE;
	   break;
	}
       case 'damage-details': //for addition of new users
	{
	   $page = 'challan/damage-details-pop.php';
	   $page_title = 'Damage Details :: '.GEN_TITLE;
	   break;
	}
        
	#- Item SUBMENU start here -- #
	
	#- item SUBMENU ENDS here -- #	
	
	#- Company SUBMENU start here -- #
	#- Company SUBMENU ENDS here -- #
	// ----------------------------- Master for Software Ends --------------------------------------------	
	
	// ----------------------------- Calibration for Software Starts --------------------------------------------	
	case 'my-alerts': 
	{
	   $page = 'alert/my-alerts.inc.php';
	   $page_title = 'My Alerts :: '.GEN_TITLE;
	   break;
	}
	case 'download-traceability': 
	{
	   $page = 'traceability/download-traceability.inc.php';
	   $page_title = 'Download Traceability :: '.GEN_TITLE;
	   break;
	}
	case 'calibration-order': 
	{
	   $page = 'calibration/srf/srf.inc.php';
	   $page_title = 'Calibration Orders :: '.GEN_TITLE;
	   break;
	}
        case 'add-threshold': 
	{
	   $page = 'vendor/add-threshold.php';
	   $page_title = 'Threshold :: '.GEN_TITLE;
	   break;
	}
	case 'certificate': 
	{
	   $page = 'calibration/certificate/certificate.inc.php';
	   $page_title = 'Certificate :: '.GEN_TITLE;
	   break;
	}
	case 'mmd-list2': 
	{
	   $page = 'calibration/mmd-list/mmd-list2.inc.php';
	   $page_title = 'MMD List2 :: '.GEN_TITLE;
	   break;
	}
	case 'certificate-print-all': 
	{
	   $page = 'calibration/certificate/certificate-print-all.inc.php';
	   $page_title = 'Certificate Print :: '.GEN_TITLE;
	   break;
	}
	case 'certificate-print-pdf': 
	{
	   $page = 'calibration/mmd-list/certificate-print-pdf.inc.php';
	   $page_title = 'Certificate Print :: '.GEN_TITLE;
	   break;
	}	
	case 'mmd-list':
	{
	   $page = 'calibration/mmd-list/mmd-list.inc.php';
	   $page_title = 'MMD :: '.GEN_TITLE;
	   break;
	}
	//my-alerts
	// ----------------------------- Calibration for Software Ends --------------------------------------------	
	# ----------------------------- Miscellaneous for OFP Ends --------------------------------------------		
   // Default is to include the main page.
   default:
   {
	   if(!isset($_SESSION[SESS.'user']))
	   { 
	   	   $page = 'login/login.php';
	   	   $page_title = 'Login :: '.GEN_TITLE;
	   }
	   elseif(basename($_SERVER['REQUEST_URI']) == 'index.php')
	   { 
	   	if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080' || substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168' || substr($_SERVER['HTTP_HOST'], 0, 7) == '172.168')
	   		{
	   			$connected = @fsockopen("dsdsr.com", 80);
	   		}

               
                if ($connected){
                   fclose($connected);
                   $date=date('Y-m-d');
                   $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
                 $sync_status_q="SELECT sync_status FROM sync_status WHERE DATE_FORMAT(date_time,'%Y-%m-%d')='$date' AND dealer_id=$dealer_id";
                   $run_q_sync_status= mysqli_query($dbc, $sync_status_q);
                   $result_sync_status= mysqli_fetch_assoc($run_q_sync_status);
                  //print_r($result_sync_status);exit;
                   //h1($result_sync_status['sync_status']);exit;
                  if (!isset($result_sync_status['sync_status'])) {
                    $page = 'sync/sync-step.inc.php';
	   	    $page_title = 'Sync';
                    }elseif($result_sync_status['sync_status']==0){
                    $page = 'login/welcome.php';
	   	   $page_title = 'Welcome '.$_SESSION[SESS.'data']['name'].' :: '.GEN_TITLE;
                   }else{
                   $page = 'sync/sync-step.inc.php';
	   	   $page_title = 'Sync ';
                   }
                   // header('Location:../client/index.php?option=sync-data&step=1');
                }else{
	   	   $page = 'login/welcome.php';
	   	   $page_title = 'Welcome '.$_SESSION[SESS.'data']['name'].' :: '.GEN_TITLE;
                   }
           
                }
	   else
	   {
		   $page = 'login/notfound.php';
	   	   $page_title = 'Error Occured '.$_SESSION[SESS.'data']['name'].' :: '.GEN_TITLE;
	   }
	   break;
   }
} // End of main switch.

// Make sure the file exists:
if (!file_exists('./modules/' . $page)){
	$page = 'login/notfound.php';
	$page_title = GEN_TITLE;
}

//Calculating the page showmode
$showmode = 3;
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



<script type="text/javascript">
    
    $('#mytable').on('change','.quantitycl',function()
     {
        var qty = parseInt($(this).val());
        var avbl_qty = parseInt($(this).closest("tr").find('.avlb_quantity').val());
        
        if(avbl_qty<qty)
        {
        	alert('Available stock must be greater then input quantity');
        	$(this).val('0');
        	$(this).focus();
        	return false;
        }
     })

    $(function() {
       $(".datepicker-challan-edit").datepicker({  maxDate: 0 });
    });
</script>
