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
        case 'todays-scheme': 
	{
	   $page = 'dealer/dealer-scheme.inc.php';
	   $page_title = 'Today Scheme :: '.GEN_TITLE;
	   break;
	}
           //todays-scheme
	case 'college-information-pop': //for business-development
	{
	   $page = 'master/colleges/college-information-pop.php';
	   $page_title = 'College Information :: '.GEN_TITLE;
	   break;
	}
	//course-lable-details
	case 'college-course-assign-pop': //for business-development
	{
	   $page = 'master/colleges/college-course-assign-pop.php';
	   $page_title = 'College Course Assign :: '.GEN_TITLE;
	   break;
	}
	case 'college-course-selection-lable-pop': //for business-development
	{
	   $page = 'master/colleges/college-course-selection-lable-pop.php';
	   $page_title = 'College Course Selection Label :: '.GEN_TITLE;
	   break;
	}
        case 'gift_details': 
	{
	   $page = 'sales/order-details/gift-details.pop.php';
	   $page_title = 'Gift details :: '.GEN_TITLE;
	   break;
	}
         case 'daily-dispatch-report': //for addition of new users
	{
	   $page = 'dispatch/daily-dispatch-details-report.inc.php';
	   $page_title = 'Daily Dispatch :: '.GEN_TITLE;
	   break;
	}
	case 'college-course-lable-details-pop': //for business-development
	{
	   $page = 'master/colleges/college-course-lable-details-pop.php';
	   $page_title = 'Course Lable Information :: '.GEN_TITLE;
	   break;
	}
	
	case 'college-album-pop': //for business-development
	{
	   $page = 'master/colleges/college-album-pop.php';
	   $page_title = 'College Album :: '.GEN_TITLE;
	   break;
	}
	case 'college-image-pop': //for business-development
	{
	   $page = 'master/colleges/college-image-pop.php';
	   $page_title = 'College photo :: '.GEN_TITLE;
	   break;
	}
	case 'college-video-pop': //for business-development
	{
	   $page = 'master/colleges/college-video-pop.php';
	   $page_title = 'College photo :: '.GEN_TITLE;
	   break;
	}
	case 'autonomous-pop': //for business-development
	{
	   $page = 'master/colleges/autonomous_college.pop.php';
	   $page_title = 'College Course Information :: '.GEN_TITLE;
	   break;
	}
	case 'course-branch-university-pop':
	{
	   $page = 'master/university/course_branch_university.pop.php';
	   $page_title = 'University :: '.GEN_TITLE;
	   break;
	}
	case 'course-domain-content':
	{
	   $page = 'master/references/course_content_domain.inc.php';
	   $page_title = 'Course Domain Content :: '.GEN_TITLE;
	   break;
	}
        case 'direct-challan':
	{
	   $page = 'challan/direct-challan-pop.php';
	   $page_title = 'Direct Challan :: '.GEN_TITLE;
	   break;
	}
	case 'college-contact-pop':
	{
	   $page = 'master/colleges/college_contact.inc-pop.php';
	   $page_title = 'Course Domain Content :: '.GEN_TITLE;
	   break;
	}
	case 'why-join-pop':
	{
	   $page = 'master/colleges/whyto_join-pop.php';
	   $page_title = 'Course Domain Content :: '.GEN_TITLE;
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