<?php 
$curdir = dirname(__FILE__); //to set the base URIpath automatically
$curhost = dirname($_SERVER['PHP_SELF']); //to set the base URL path automatically
# index.php
/*
* This is the main page.
* This page includes the configuration file,
* the templates, and any content-specific modules.
*/
set_time_limit (660);// to increase the script execution time limit
@session_start();
//$_SESSION['user'] = true;
//unset($_SESSION['user']);
@ob_start();
// Require the configuration file before any PHP code:
require_once ('./include/config.inc.php');

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
	
//if user is not logged in then dont allow him to access anything
if(!isset($_SESSION[SESS.'user'])) $p = NULL;

require_once(DB);
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-functions.php');

// Determine what page to display:
switch ($p)
{	
	case 'history-pop': 
	{
	   $page = 'master/history-pop.php';
	   $page_title = 'History Log :: '.GEN_TITLE;
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
        case 'dealer-person-list': 
	{
	   $page = 'master/users/dealer-person-list.inc.php';
	   $page_title = 'Dealer Person List :: '.GEN_TITLE;
	   break;
	}
        case 'company-add': 
	{
	   $page = 'login/company-add-pop.php';
	   $page_title = 'Company Add :: '.GEN_TITLE;
	   break;
	}
         case 'lat-long': 
	{
	   $page = 'production/distance-map.inc.php';
	   $page_title = 'User Distance Map :: '.GEN_TITLE;
	   break;
	}
        case 'sale-qty': 
	{
	   $page = 'reports/stock/sale-qty-pop.php';
	   $page_title = 'Sale Quantity :: '.GEN_TITLE;
	   break;
	}
        case 'opening-stock': 
	{
	   $page = 'reports/stock/opening-stock-pop.php';
	   $page_title = 'Primary Stock :: '.GEN_TITLE;
	   break;
	}
        //opening-stock
        case 'retailer-person-list': 
	{
	   $page = 'master/users/retailer-person-list.inc.php';
	   $page_title = 'Retailer Person List :: '.GEN_TITLE;
	   break;
	}
        
          case 'dealer-claim-details': //for addition of new users
        {
        $page = 'sales/order-details/dealer-claim-details.php';
        $page_title = 'Dealer Claim :: '.GEN_TITLE;
        break;
        }
        
        case 'dealer-stock-details': //for addition of new users
        {
        $page = 'sales/order-details/dealer-stock-details.php';
        $page_title = 'Dealer Stock :: '.GEN_TITLE;
        break;
        }
       
        case 'person-dealer': 
	{
	   $page = 'master/users/person-dealer-list.inc.php';
	   $page_title = 'Person Dealer List :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-dealer-add': 
	{
	   $page = 'master/users/retailer-dealer-add.pop.php';
	   $page_title = 'Dealer Retailer Relation :: '.GEN_TITLE;
	   break;
	}
        case 'retailer-location': //for addition of new users
	{
                   
	   $page = 'master/users/retailer-location.inc.php';
	   $page_title = 'Retailer Location :: '.GEN_TITLE;
	   break;
	}
        case 'sub_catalog_product': //for addition of new users
	{
                   
	   $page = 'master/users/sub_catalog_product.php';
	   $page_title = 'Sub Catalog Product :: '.GEN_TITLE;
	   break;
	}
         case 'stock_sku': //for addition of new users
	{
                   
	   $page = 'reports/stock/stock_sku.php';
	   $page_title = 'SQU Wise Stock:: '.GEN_TITLE;
	   break;
	}
        case 'assign-rate': 
	{
	   $page = 'master/users/assign-dealer-rate.pop.php';
	   $page_title = 'Dealer Price Rate :: '.GEN_TITLE;
	   break;
	}
        case 'user-person': 
	{
	   $page = 'master/users/user-person.pop.php';
	   $page_title = 'User Person :: '.GEN_TITLE;
	   break;
	}
        
        case 'add-dealer-person': 
	{
	   $page = 'master/party/dealer_person_login.pop.php';
	   $page_title = 'Dealer Login Details :: '.GEN_TITLE;
	   break;
	}
         case 'gift_details': 
	{
	   $page = 'sales/order-details/gift-details.pop.php';
	   $page_title = 'Gift details :: '.GEN_TITLE;
	   break;
	}
        
        case 'module-rights': 
	{
	   $page = 'master/users/assign-modules-right-pop.php';
	   $page_title = 'Modules Right :: '.GEN_TITLE;
	   break;
	}
        case 'dealer-sales-person': 
	{
	   $page = 'master/users/dealer-sales-person-pop.php';
	   $page_title = 'Dealer Person Add :: '.GEN_TITLE;
	   break;
	}
        case 'selling-brand': 
	{
	   $page = 'master/users/retailer-selling-brand-pop.php';
	   $page_title = 'Selling Brand :: '.GEN_TITLE;
	   break;
	}
        //user-location
	case 'user-location':
	{
	   $page = 'reports/mtp/user-attendence-location.inc.php';
	   $page_title = 'User Attendence Location :: '.GEN_TITLE;
	   break;
	}
        case 'schemedetail':
	{
	   $page = 'misc/schemedetail.php';
	   $page_title = 'Scheme details :: '.GEN_TITLE;
	   break;
	}  
        case 'scheme-dealer':
	{
	   $page = 'misc/scheme_dealer.php';
	   $page_title = 'Scheme to Dealer :: '.GEN_TITLE;
	   break;
	} 
	case 'product-details': 
{
$page = 'reports/sales/product_details.inc.php';
$page_title = 'Product Details :: '.GEN_TITLE;
break;
}

case 'pro-product-details': 
{
$page = 'reports/sales/productive_product_details.inc.php';
$page_title = 'Productive Product Details :: '.GEN_TITLE;
break;
}
case 'non-product-details': 
{
$page = 'reports/sales/non_productive_product_details.inc.php';
$page_title = 'Non Productive Product Details :: '.GEN_TITLE;
break;
}
case 'non-contact-details': 
{
$page = 'reports/sales/non_contact_details.inc.php';
$page_title = 'Non contact Details :: '.GEN_TITLE;
break;
}
         case 'scheme-assign-dealer':
	{
	   $page = 'misc/scheme_assign_dealer.php';
	   $page_title = 'Scheme to Dealer :: '.GEN_TITLE;
	   break;
	}
        case 'product-details':
	{
	   $page = 'catalog/catalog-product-pop.php';
	   $page_title = 'Catalog Product :: '.GEN_TITLE;
	   break;
	}
        case 'direct-challan':
	{
	   $page = 'challan/direct-challan-pop.php';
	   $page_title = 'Direct Challan :: '.GEN_TITLE;
	   break;
	}      
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

// Make sure the file exists:
if (!file_exists('./modules/' . $page)) 
{
	$page = 'login/notfound.php';
	$page_title = GEN_TITLE;
}

// Include the header file:
include_once ('./include/headerpop.php');

// Include the content-specific module:
// $page is determined from the above switch.
include ('./modules/' . $page);

// Include the footer file to complete the template:
include_once ('./include/footerpop.php');
 ?>