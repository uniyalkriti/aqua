<?php # config.inc.php

 /*
 * File name: config.inc.php
 * Last modified: February 8, 2012
 *
 * Configuration file does the following things:
 * - Has site settings in one location.
 * - Stores URLs and URIs as constants.
 * - Sets how errors will be handled.
 */

 # ******************** #
 # ***** SETTINGS ***** #

 // Errors are emailed here.
 $contact_email = 'bhoopendera.pandey@manacleindia.com';

// Determine whether we're working on a local server
// or on the real server:
if(stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168') || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'local:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080')
	$local = TRUE;
else
	$local = FALSE;
	
// Defining some basic constants
$year = date('Y');
define('PANEL_NAME', 'mSELL Distributor Panel'); // The text that will be displayed across the ws logo.
define('GEN_TITLE', 'Powered by Manacle Technologies (P) Ltd');
define('FOOTER', 'Copyright &copy; 2013 - 2021 DS Spiceco Pvt. Ltd. & Co. All rights reserved. ');
define('SESS', 'iclientdigimet'); // to allow multiple session of admin of different sites simultaneously open without conflict
define('EDSALT', 'demo'); // this salt will be used to store the encrypted & decrypted password for users & admin mywerp//myclientdigimet
define('TR_ROW_COLOR1','#efede8');
define('TR_ROW_COLOR2','#ffffff');
define('JS_OPEN',false); // to decide whether the new button to enable the form with javascript should be present or not
define('INDV_MODULE_CONTROL', true); // Whether to have the user control on the individual module level
define('MSG_AUTH_EDIT', 'Sorry, You <strong>do not have enough rights</strong> to perform the <b>EDIT</b> operation.');
define('MSG_AUTH_ADD', 'Sorry, You <strong>do not have enough rights</strong> to perform the <b>ADD</b> operation.');
define('MSG_AUTH_VIEW', 'Sorry, You <strong>do not have enough rights</strong> to perform the <b>VIEW</b> operation.');
define('MSG_AUTH_DEL', 'Sorry, You <strong>do not have enough rights</strong> to perform the <b>DELETE</b> operation.');
define('MSG_AUTH_NO', 'You do <strong>not</strong> have <strong>authorization to access</strong> this page.');
define('EMAIL_PAT',"#^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$#");
define('IMAGEDOMAIN','');
define('ADMINFOLDER', 'client');
define('ENABLESEO', FALSE);

//If user has logged in then getting the constants for current session and user
if(isset($_SESSION[SESS.'id'])){
	define('CSESS', 1);
	define('MYUID', $_SESSION[SESS.'id']);
}

$DEFAULT_JQUERY =  array('jQuery'=>1,'jQueryUI'=>1,'jwerty'=>1, 'maskedinput'=>1, 'chosen'=>1,'colorbox'=>1,'zoomer'=>0,'phtozoom'=>0);
$NO_WIDGETS = array('jQuery'=>1,'jQueryUI'=>0,'jwerty'=>1,'chosen'=>0,'colorbox'=>0,'zoomer'=>0,'phtozoom'=>0);

//setting the timezone
$timezone = "Asia/Calcutta";
date_default_timezone_set($timezone);
// Determine location of files and the URL of the site:

// Allow for development on different servers.
if($local)
{
	  // error_reporting(0);
	$curdir = dirname(dirname(__FILE__)); // This will point to the admin folder
	$curdir_root = dirname($curdir); // will point to parent folder of admin
	$curhost = dirname($_SERVER['PHP_SELF']); //to set the base URL path to admin folder automatically
	$curhost_root = dirname($curhost); //to set the base URL path to parent of admin folder automatically
	// Always debug when running locally:
 	$debug = TRUE;

   // Define the constants: 
   
   define('MSYM','/');  // For window check gives(//) sign
   define ('BASE_URI', $curdir.MSYM);
   define ('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].$curhost.'/');
   
   define ('BASE_URI_ROOT', $curdir_root.MSYM);
   define ('BASE_URL_ROOT', 'https://'.$_SERVER['HTTP_HOST'].$curhost_root.'/');
   
   define ('BASE_URI_A', BASE_URI_ROOT.ADMINFOLDER.MSYM);
   define ('BASE_URL_A', BASE_URL_ROOT.ADMINFOLDER.'/');
   
   define ('DB', BASE_URI_ROOT.ADMINFOLDER.'/include/conectdb.php');
   define('LOCAL_HOST_MAIL',true); // whether to send the mail from the localhost or not
}
else
{
   /*ini_set('display_errors', 'On');
   error_reporting(E_ALL);*/
   // error_reporting(0);

   $curdir = dirname(dirname(__FILE__)); // This will point to the admin folder
   $curdir_root = dirname($curdir); // will point to parent folder of admin
   $curhost = dirname($_SERVER['PHP_SELF']); //to set the base URL path to admin folder automatically
   $curhost_root = dirname($curhost); //to set the base URL path to parent of admin folder automatically
   define('MSYM','/');  
   define ('BASE_URI', $curdir.MSYM);
   define ('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].$curhost.'/');
   
   define ('BASE_URI_ROOT', $curdir_root.MSYM);
   define ('BASE_URL_ROOT', 'https://'.$_SERVER['HTTP_HOST'].$curhost_root.'/');
   
   define ('BASE_URI_A', BASE_URI_ROOT.ADMINFOLDER.MSYM);
   define ('BASE_URL_A', BASE_URL_ROOT.ADMINFOLDER.'/');
   
   define ('DB', BASE_URI_ROOT.ADMINFOLDER.'/include/conectdb.php');
   define('LOCAL_HOST_MAIL',false); // whether to send the mail from the localhost or not   
}

/*
* Most important setting...
* The $debug variable is used to set error management.
* To debug a specific page, add this to the index.php page:

if ($p == 'thismodule') $debug = TRUE;
require_once('./includes/config.inc.php');
* To debug the entire site, do

$debug = TRUE;

* before this next conditional.
*/

// Assume debugging is off.
if (!isset($debug))
	$debug = FALSE;

# ***** SETTINGS ***** #
# ******************** #

# **************************** #
# ***** ERROR MANAGEMENT ***** #

// Create the error handler.
function my_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) 
{
	global $debug, $contact_email;

	// Build the error message.
	$message = "An error occurred in script '$e_file' on line $e_line: \n<br />$e_message\n<br/>";

	// Add the date and time.
	$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n<br />";

	// Append $e_vars to the $message.
	$message .= "<pre>" . print_r ($e_vars, 1) . "</pre>\n<br />";

	if ($debug) 
	{	// Show the error.
		echo '<p class="error">' . $message . '</p>';
	} 
	else
	{
		// Log the error:
		error_log ($message, 1, $contact_email); // Send email.

		// Only print an error message if the error isn't a notice or strict.
		if (($e_number != E_NOTICE) && ($e_number < 2048)) 
			echo '<p class="error">A system error occurred. We apologize for the inconvenience.</p>';
	} // End of $debug IF.

} // End of my_error_handler() definition.

// Use my error handler:
//set_error_handler ('my_error_handler');

# ***** ERROR MANAGEMENT ***** #
# **************************** #
# ***** Date AND time related Constants ***** #
define('DTS', '%d/%b/%Y at %r');
define('DTSB', '%d/%b/%Y <br/> %r');
define('DC', '%d/%b/%Y');
define('MASKDATE', '%d/%m/%Y');
define('OT', '%r');
define('MYSQL_DATE_SEARCH', '%Y%m%d'); 
define('ADD_REFRESHER','<img src="'.BASE_URL_ROOT.'icon-system/i16X16/add_refresher.png"/>');
# ***** Upload Related Constants ***** #
define('MYUPLOADS',BASE_URI_ROOT.'myuploads');
# ***** PROJECT SPECIFIC CONSTANTS & ARRAYS DEFINED HERE ***** #
define('PGDISPLAY', '50');
# ***** All Global ARRAYS DEFINED HERE ***** #
$GLOBALS['rdutype'] = array(1=>'Hour', 'Days', 'Week', 'Month', 'Year');
$GLOBALS['order_type'] = array(0=>'Fresh Order', 'Challan Generated', 'Challan Cancel', 'Invoice Generated', 'invoice Cancel', 'Order Dispatch', 'challan Closed');
$GLOBALS['order_source']= array('1'=>'Telephonic Conversation','2'=>'Email Confirmation','3'=>'Others');
$GLOBALS['deal_status']= array('1'=>'old','2'=>'New');
$GLOBALS['job_type'] = array(1=>'At Lab', 'At Client Site');
$GLOBALS['istatus'] = array(1=>'Active', 'Hold');
$GLOBALS['challan_or_po_based'] = array(1=>'PO', 'Challan'); // used during filling or SRF form
//These controls the available input format
$GLOBALS['cal_step_type'] = array(1=>'Our Standard', 2=>'Customer Specific');
$GLOBALS['inputFormat'] = array(1=>array('name'=>'', 'certificateAllwed'=>array(1)));
$GLOBALS['certificateFormat'] = array(1=>'xyz');

?>
