<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<?php 
$forma = 'Change Email'; // to indicate what type of form this is
$formaction = 'change-email';
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
	  <h1 style=""><?php echo $forma;?></h1>
      <div id="breadcumb"><a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Change Email</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
//list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'itemId', $table = 'item', $formaction);	
# -------------------------------- code for handling of the previous, first and next button ends here

function checkform($mode='add', $id='')
{
	global $dbc;
	// checking whether the name is left empty or not
	if(empty($_POST['cpass'])) return array(FALSE, 'Please enter the Old Email');
	if(empty($_POST['npass'])) return array(FALSE, 'Please select the New Email');
	if(empty($_POST['vpass'])) return array(FALSE, 'Please enter the Verify Email');
	if($_POST['vpass'] != $_POST['npass']) return array(FALSE, 'New Email & verify Email do not match');
	$q = "SELECT stvalue FROM settings WHERE stname = 'email' AND stvalue = '$_POST[cpass]' LIMIT 1";
	list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='sorry no  record found');
	if(!$opt)
	{
		return array(FALSE, 'Sorry, <b>Current Email</b> not valid');
	}
	return array(TRUE, '');
}

// code to edit region starts here
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		$id = $_SESSION[SESS.'data']['id'];
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id);
		if($checkpass)
		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			
			if(mysqli_query($dbc, "UPDATE settings SET `stvalue` = '$_POST[npass]' WHERE stname ='email' LIMIT 1"))
			{
				$msg = '<span class="asm"><b>Email</b> successfull <b>'.$_POST['submit'].'d</b>. <a href="'.basename($_SERVER['REQUEST_URI']).'">click to continue</a></span>';				
				//updation of user activity in history log table starts here
				$particular = 'Email update for user </b> '.$_SESSION[SESS.'data']['uname'].' <b></b> in system';
				history_log($dbc, 'Update', $particular);
				//updation of user activity in history log table ends here
				unset($_POST);
				echo $msg;
			}
			else
				echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
?>

      <div id="workarea">
      <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style="">New <?php echo $forma; ?> Form</legend>
        <table width="100%" border="0" cellspacing="5" cellpadding="5">
          <tr>
            <td><span class="star">*</span> Current Email<br />
              <input type="text" id="cpass" name="cpass" value="<?php if(isset($_POST['cpass'])) echo $_POST['cpass']; ?>" lang="Current Email" />
              <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />			  
            </td>            
            <td><span class="star">*</span> New Email<br />
              <input type="text" name="npass" value="<?php if(isset($_POST['npass'])) echo $_POST['npass']; ?>" lang="New Email" />			  
            </td> 
            <td><span class="star">*</span> Verify Email<br />
              <input type="text" name="vpass" value="<?php if(isset($_POST['vpass'])) echo $_POST['vpass']; ?>" lang="Verify Email" />			  
            </td>   
          </tr>
          <tr>
            <td colspan="3" align="center">
             <input type="submit" name="submit" value="Update" />
             <script type="text/javascript">setfocus('cpass');</script>
            </td>
          </tr>          
        </table>
      </fieldset>
      </form>
      </div><!-- workarea div ends here -->