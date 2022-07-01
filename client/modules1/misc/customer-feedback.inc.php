<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<script type="text/javascript">
function get_feedback(val)
{
	if(val == '')	
	{
		var arr = new Array('feedback','action','resolution');
		for(var i=0;i<3;i++)
		document.getElementById(arr[i]).value = '';
		return;
	}
	getdata(val, 'pdiv', 'feedback', 'feedback<$>action<$>resolution<$>status');
}
</script>
<?php 
$forma = 'Customer Feedback'; // to indicate what type of form this is
$formaction = 'customer-feedback';
// Getting the user credentials for this page access
$iid = $_SESSION[SESS.'data']['id'];
$auth = user_auth($dbc, $iid, $formaction);
?>
<?php
if(isset($_POST['submit']) && $_POST['submit'] == 'Save')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		list($condi,$fmsg) = checkform();
		if(!$condi)
		{
			echo '<span class="awm">'.$fmsg.'</span>';
		}
		else
		{
			//calculating the user authorisastion for the operation performed, function is defined in common_function
			list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');		
			if($checkpass)
			{
				// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
				magic_quotes_check($dbc, $check=true);
			  $q = "INSERT INTO `customer-feedback` (`cfId`, `saleId`, `feedback`, `action`, `resolution`, `id`, `created`, `modified`) VALUES (NULL, '$_POST[billno]', '$_POST[feedback]', '$_POST[action]', '$_POST[resolution]', '$iid', 'NOW()', '');";
				if(mysqli_query($dbc, $q))
				{
					$msg = '<span class="asm"><b>'.$forma.'</b> successfull <b>'.$_POST['submit'].'d</b>. <a href="'.basename($_SERVER['REQUEST_URI']).'">click to continue</a></span>';				
					//updation of user activity in history log table starts here
					$particular = 'New '.$forma.' added to system';
					history_log($dbc, 'Add', $particular);
					//updation of user activity in history log table ends here
					unset($_SESSION[SESS.'securetoken']); unset($_POST);
					echo $msg;
				}
				else
					echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
			}
			else
				echo'<span class="awm">'.$fmsg.'</span>';
		}
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		list($condi,$fmsg) = checkform();
		if(!$condi)
		{
			echo '<span class="awm">'.$fmsg.'</span>';
		}
		else
		{

			//calculating the user authorisastion for the operation performed, function is defined in common_function
			list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');		
			if($checkpass)
			{
				// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
				magic_quotes_check($dbc, $check=true);
			  $q = "UPDATE `customer-feedback` SET `feedback` = '$_POST[feedback]', `action` = '$_POST[action]', `resolution` = '$_POST[resolution]', `id` = '$iid', `modified` = NOW() WHERE saleId = '$_POST[billno]';";
				if(mysqli_query($dbc, $q))
				{
					$msg = '<span class="asm"><b>'.$forma.'</b> successfull <b>'.$_POST['submit'].'d</b>. <a href="'.basename($_SERVER['REQUEST_URI']).'">click to continue</a></span>';				
					//updation of user activity in history log table starts here
					$particular = $forma.' updated to system';
					history_log($dbc, 'Add', $particular);
					//updation of user activity in history log table ends here
					unset($_SESSION[SESS.'securetoken']); unset($_POST);
					echo $msg;
				}
				else
					echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
			}
			else
				echo'<span class="awm">'.$fmsg.'</span>';
		}
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
?>

	  <h1 style=""><?php echo $forma;?></h1>
      <div id="breadcumb"><a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Customer Feedback</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
//list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'itemId', $table = 'item', $formaction);	
# -------------------------------- code for handling of the previous, first and next button ends here

function checkform($mode='add', $id='')
{
	global $dbc;
	// checking whether the name is left empty or not
	if(empty($_POST['billno'])) return array(FALSE, 'Please select the Bill No');
	if(empty($_POST['feedback'])) return array(FALSE, 'Please enter the Feedback');
	if(empty($_POST['action'])) return array(FALSE, 'Please enter the action');
	return array(TRUE, '');
}

// code to edit region starts here
/*if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
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
			
			if(mysqli_query($dbc, "UPDATE admin SET `pass` = AES_ENCRYPT('$_POST[npass]', '".EDSALT."') WHERE id ='$id' LIMIT 1"))
			{
				$msg = '<span class="asm"><b>Password</b> successfull <b>'.$_POST['submit'].'d</b>. <a href="'.basename($_SERVER['REQUEST_URI']).'">click to continue</a></span>';				
				//updation of user activity in history log table starts here
				$particular = 'Password update for user </b> '.$_SESSION[SESS.'data']['uname'].' <b></b> in system';
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
}*/
?>

      <div id="workarea">
      <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style="">New <?php echo $forma; ?> Form</legend>
        <table width="100%" border="0" cellspacing="5" cellpadding="5">
          <tr>
            <td colspan="3"><span class="star">*</span> Bill No<br />
              <?php db_pulldown($dbc, 'billno', "SELECT saleId,saleId FROM sales ORDER BY saleId DESC", true, true, 'lang="Bill No" onchange="get_feedback(this.value);" id="billno"'); ?>
              <div style="position:absolute;" id="pdiv"></div>
            </td>
          </tr>
          <tr>
            <td><span class="star">*</span> Feedback<br />
              <textarea name="feedback" id="feedback" lang="Feedback"><?php echo $_POST['feedback']; ?></textarea>
              <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />			  
            </td>            
            <td><span class="star">*</span> Action Taken<br />
              <textarea name="action" id="action" lang="Action Taken"><?php echo $_POST['action']; ?></textarea>
            </td> 
            <td> Resolution<br />
              <textarea name="resolution" id="resolution"><?php echo $_POST['resolution']; ?></textarea>
            </td>   
          </tr>
          <tr>
            <td colspan="3" align="center">
            <!--<input type="text" id="status" name="status" />-->
             <input type="submit" name="submit" id="status" value="Save" />
             <script type="text/javascript">setfocus('feedback');</script>
            </td>
          </tr> 
        </table>
      </fieldset>
      </form>
      </div><!-- workarea div ends here -->