<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<?php 
include'../client/modules/table.php';
$forma = 'Update Profile'; // to indicate what type of form this is
$formaction = $p;
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$dea_id = $_SESSION[SESS.'data']['dealer_id'];
?>
	 <!-- <h1 style=""><?php echo $forma;?></h1>-->
      <!--<div id="breadcumb"><a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Update Profile</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>-->
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
//list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'itemId', $table = 'item', $formaction);	
# -------------------------------- code for handling of the previous, first and next button ends here

function checkform($mode='add', $id='')
{
	global $dbc;
	// checking whether the name is left empty or not
	if(empty($_POST['cpass'])) return array(FALSE, 'Please enter the Old Password');
	if(empty($_POST['npass'])) return array(FALSE, 'Please select the New Password');
	if(empty($_POST['vpass'])) return array(FALSE, 'Please enter the Verify Password');
	if($_POST['vpass'] != $_POST['npass']) return array(FALSE, 'New password & verify password do not match');
	$q = "SELECT dpId FROM dealer_person_login WHERE dpId = '$id' AND pass = AES_ENCRYPT('$_POST[cpass]', '".EDSALT."') LIMIT 1";
	list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='sorry no  record found');
	if(!$opt)
	{
		return array(FALSE, 'Sorry, <b>current password</b> not valid');
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
//		if($checkpass)
//		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
                        
                        $qdup="UPDATE `dealer` SET `name`='$_POST[name]',`terms`='$_POST[terms]'"
                                . ",`address`='$_POST[address]',`email`='$_POST[email]',`landline`='$_POST[landline]',"
                                . "`other_numbers`='$_POST[other_numbers]',`tin_no`='$_POST[tin_no]',`pin_no`='$_POST[pin_no]',`pan_no`='$_POST[pan_no]',`drug_lic_no`='$_POST[drug_lic_no]'"
                                . " WHERE id='$dea_id'";
			//h1($qdup);exit;
			if(mysqli_query($dbc,$qdup))
			{
				$msg = '<span class="asm"><b>Profile</b> successfull <b>'.$_POST['submit'].'d</b>.</span>';				
				//updation of user activity in history log table starts here
				$particular = 'Profile update for Dealer </b> '.$dea_id.' <b></b> in system';
				history_log($dbc, 'Update', $particular);
				//updation of user activity in history log table ends here
				unset($_POST);
				echo $msg;
			}
			else
				echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
//		}
//		else
//			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
?>
<?Php  $qup="SELECT * FROM dealer WHERE id='$dea_id'"; 
//h1($qup);
$rup=mysqli_query($dbc,$qup);
$s=mysqli_num_rows($rup);
$res_up= mysqli_fetch_assoc($rup);
        
?>
      <div id="row" style="background-color:#d1f2eb ">
      <form method="post"  class="form-horizontal" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>  
            
        
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
                <div class="row">
            <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading" style="text-align: center;">
                    <h2 class="panel-title" style="padding-bottom: 10px; font-size: 20px;">UPDATE PROFILE</h2>
                </div>
            </div>
            </div>
        </div>
      <div class="form-group">
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Distributor Name:</b></div>
            <div class="col-xs-4"><input type="text" size="35" id="name" name="name" value="<?php if(isset($res_up['name'])) echo $res_up['name']; ?>"  /></div>
            </div>
          </div> 
         <div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Landline.:</b></div>
            <div class="col-xs-2"><input type="text" name="landline" size="35" value="<?php if(isset($res_up['landline'])) echo $res_up['landline']; ?>"  /></div>
            </div>
         </div>
        <div class="form-group">
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Mobile No.:</b></div>
            <div class="col-xs-2"><input type="text" name="other_numbers" size="35" maxlength="10" value="<?php if(isset($res_up['other_numbers'])) echo $res_up['other_numbers']; ?>"  /></div>
            </div>
        </div>
        <div class="form-group">
        <div class="row">
            <div class="col-xs-3"></div>
           </div>
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Email Id:</b></div>
            <div class="col-xs-2"><input type="email" name="email" size="35" value="<?php if(isset($res_up['email'])) echo $res_up['email']; ?>"  /></div>
            </div>
        </div>
        <div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Address:</b></div>
            <div class="col-xs-2"><input type="text" name="address" size="35" value="<?php if(isset($res_up['address'])) echo $res_up['address']; ?>" /></div>
            </div>
        </div>
        <div class="form-group">
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Pin Code:</b></div>
            <div class="col-xs-2"><input type="text" name="pin_no" maxlength="6" size="35" value="<?php if(isset($res_up['pin_no'])) echo $res_up['pin_no']; ?>" /></div>
            </div>
        </div>
        <div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> GSTIN No:</b></div>
            <div class="col-xs-2"><input type="text" name="tin_no" size="35" maxlength="15" value="<?php if(isset($res_up['tin_no'])) echo $res_up['tin_no']; ?>"  /></div>
            </div>
        </div>
		<div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Drug License No:</b></div>
            <div class="col-xs-2"><input type="text" name="drug_lic_no" size="35" maxlength="40" value="<?php if(isset($res_up['drug_lic_no'])) echo $res_up['drug_lic_no']; ?>"  /></div>
            </div>
        </div>
		<div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> PAN No:</b></div>
            <div class="col-xs-2"><input type="text" name="pan_no" size="35" maxlength="15" value="<?php if(isset($res_up['pan_no'])) echo $res_up['pan_no']; ?>"  /></div>
            </div>
        </div>
        <div class="form-group">
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"><b> Terms & Conditions For Invoice:</b></div>
            <div class="col-xs-2"><textarea type="textarea" cols="50" rows="4" name="terms"><?php if(isset($res_up['terms'])) echo $res_up['terms']; ?></textarea></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3"></div>
           </div>
        <div class="row">
            <div class="col-xs-3"></div>
           </div>
        <div class="form-group">
         <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-2"></div>
            <div class="col-xs-2"><input class="btn btn-primary" type="submit" name="submit" value="Update" /></div>
            </div>
        </div>
      </fieldset>
      </form>
      </div><!-- workarea div ends here -->