<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<?php 
include'../client/modules/table.php';
$forma = 'Help Desk'; // to indicate what type of form this is
$formaction = $p;
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
	 <!-- <h1 style=""><?php echo $forma;?></h1>-->
      <div id="breadcumb"><a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Change Password</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
//list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'itemId', $table = 'item', $formaction);	
# -------------------------------- code for handling of the previous, first and next button ends here

//function checkform($mode='add', $id='')
//{
//	global $dbc;
//	// checking whether the name is left empty or not
//	if(empty($_POST['cpass'])) return array(FALSE, 'Please enter the Old Password');
//	if(empty($_POST['npass'])) return array(FALSE, 'Please select the New Password');
//	if(empty($_POST['vpass'])) return array(FALSE, 'Please enter the Verify Password');
//	if($_POST['vpass'] != $_POST['npass']) return array(FALSE, 'New password & verify password do not match');
//	$q = "SELECT dpId FROM dealer_person_login WHERE dpId = '$id' AND pass = AES_ENCRYPT('$_POST[cpass]', '".EDSALT."') LIMIT 1";
//	list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='sorry no  record found');
//	if(!$opt)
//	{
//		return array(FALSE, 'Sorry, <b>current password</b> not valid');
//	}
//	return array(TRUE, '');
//}

// code to edit region starts here
//if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
//{
//	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
//	{
//		$id = $_SESSION[SESS.'data']['id'];
//		//calculating the user authorisastion for the operation performed, function is defined in common_function
//		list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id);
//		if($checkpass)
//		{
//			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
//			magic_quotes_check($dbc, $check=true);
//			
//			if(mysqli_query($dbc, "UPDATE dealer_person_login SET `pass` = AES_ENCRYPT('$_POST[npass]', '".EDSALT."') WHERE dpId ='$id' LIMIT 1"))
//			{
//				$msg = '<span class="asm"><b>Password</b> successfull <b>'.$_POST['submit'].'d</b>.</span>';				
//				//updation of user activity in history log table starts here
//				$particular = 'Password update for user </b> '.$_SESSION[SESS.'data']['uname'].' <b></b> in system';
//				history_log($dbc, 'Update', $particular);
//				//updation of user activity in history log table ends here
//				unset($_POST);
//				echo $msg;
//			}
//			else
//				echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
//		}
//		else
//			echo'<span class="awm">'.$fmsg.'</span>';
//	}
//	else
//		echo'<span class="awm">Please do not try to hack the system.</span>';
//}
?>

      <div id="row" style="align-content:center">
         
      
       
        <div class="row">
            <div class="col-md-12">
            
            <div class="panel panel-primary">

                <div class="panel-heading" style="text-align: center;">
                    
                    <h2 class="panel-title" style="padding-bottom: 20px; font-size: 20px;">Email ID :- support@manacleindia.com</h2>
                    
                    <h2 class="panel-title" style="font-size: 20px;">Contact No. :- +91- 8010819820</h2>
                    

                </div>
            </div>
            
            </div>
            
            
        </div>
        
         
       <hr/>

      
      </div><!-- workarea div ends here -->
