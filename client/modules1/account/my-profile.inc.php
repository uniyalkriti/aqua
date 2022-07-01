<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'My Profile'; // to indicate what type of form this is
$formaction = $p;
$myobj = new party();
$cls_func_str = 'customer'; //The name of the function in the class that will do the job
$myorderby = 'partyname ASC, state ASC'; // The orderby clause for fetching of the data
$myfilter = 'partyId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
      <div id="breadcumb"><a href="#">Account</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/party.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('partyname' => $_POST['partyname']);// checking for  duplicate Unit Name
	if($mode == 'add')
	{
		if(uniqcheck_msg($dbc,$field_arry,'party', false, " ptype=2"))
			return array(FALSE, '<b>Customer</b> already exists, please provide a different value.');
	}
	elseif($mode == 'edit')
	{
		if(uniqcheck_msg($dbc,$field_arry,'party', false," partyId != '$_GET[id]' AND ptype = 2"))
			return array(FALSE, '<b>Customer</b> already exists, please provide a different value.');
	}
	return array(TRUE, '');
}

############################# code for SAVING data starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Savee')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');		
		if($checkpass)
		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			$funcname = $cls_func_str.'_save'; 
			
			$action_status =  $myobj->$funcname(); // $myobj->item_category_save()
			if($action_status['status'])
			{
				echo '<span class="asm">'.$action_status['myreason'].'</span>';
				//show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
				unset($_POST);
				/*echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>';*/
				//unset($_SESSION[SESS.'securetoken']); 		
			}
			else
				echo '<span class="awm">'.$action_status['myreason'].'</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code for editing starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Updatee')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'edit', $id = $_POST['eid']);		
		if($checkpass)
		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			$funcname = $cls_func_str.'_edit';
			$action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
			if($action_status['status'])
			{
				echo '<span class="asm">'.$action_status['myreason'].'</span>';				
				//unset($_SESSION[SESS.'securetoken']); 
				//show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
				unset($_POST);
			}
			else
				echo '<span class="awm">'.$action_status['myreason'].'</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code to get the stored info for editing starts here ########################
$_GET['mode'] = 1;
$_GET['id'] = $_SESSION[SESS.'id'];
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
		//This will containt the pr no, pr date and other values
		$funcname = 'get_party_list';//'get_'.$cls_func_str.'_list';
		$mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '', $orderby=''); // $myobj->get_item_category_list()
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$id, $in = $mystat);
			//loading the username and password for the customer
			list($optl, $rsl) = run_query($dbc, "SELECT uname, AES_DECRYPT(pass, '".EDSALT."') AS pass FROM partylogin WHERE partyId = $id LIMIT 1", 'single');
			if($optl){
				$_POST['username'] = $rsl['uname'];
				$_POST['pass'] = $rsl['pass'];
			}
			
			//This will create the post multidimensional array
			create_multi_post($mystat[$id]['party_contact'], array('cname'=>'cname', 'cdepartment'=>'cdepartment', 'cdesignation'=>'cdesignation', 'cmobile'=>'cmobile', 'cemail'=>'cemail', 'cphone'=>'cphone', 'cremark'=>'cremark'));
			$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_party_list';//'get_'.$cls_func_str.'_list';
if(isset($_POST['filter']) && $_POST['filter'] == 'Filter')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = checkform('filter');	
		if($checkpass)
		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			$filter = array();
			$filterstr = array();
			$filter[] = 'ptype = 2';
			if(!empty($_POST['partycode'])){
				$filter[] = "partycode = '$_POST[partycode]'";
				$filterstr[] = '<b>Code  : </b>'.$_POST['partycode'];
			}
			if(!empty($_POST['partyname'])){
				$filter[] = "partyname LIKE '%$_POST[partyname]%'";
				$filterstr[] = '<b>PartyName  : </b>'.$_POST['partyname'];
			}
			if(!empty($_POST['city_district'])){
				$filter[] = "city_district = '$_POST[city_district]'";
				$filterstr[] = '<b>City/District  : </b>'.$_POST['city_district'];
			}
			if(!empty($_POST['state'])){
				$filter[] = "state = '$_POST[state]'";
				$filterstr[] = '<b>State  : </b>'.$_POST['state'];
			}
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');			
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby"); // $myobj->get_item_category_list()
			if(empty($rs))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])){
	$ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
	$rs = $myobj->$funcname($filter="$myfilter'$ajaxshowid'",  $records = '', $orderby='');
}
//dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function() {
	$("#partycode").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-customer-code.php"
	});
	$("#partyname").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-customer-name.php"
	});
	$("#locality").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-party-locality.php"
	});
	$("#city").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-party-city.php"
	});
	$("#state").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-party-state.php"
	});
	$("#pincode").autocomplete({
		source: "./modules/ajax-autocomplete/party/ajax-party-pincode.php"
	});
});
</script>
    <div id="workarea">
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
           <td>Customer Code<br />
             <input type="text" id="partycode" name="partycode"  value="<?php if(isset($_POST['partycode'])) echo $_POST['partycode']; ?>" class="readonly" readonly="readonly" />
            </td>           
            <td colspan="2"><span class="star">*</span>Customer Name <br />                    
              <input type="text" name="partyname" id="partyname"  value="<?php if(isset($_POST['partyname'])) echo $_POST['partyname']; ?>" lang="Customer Name" onblur="this.value = ucwords(trim(this.value));" class="readonly" readonly="readonly"/>
            </td>
           <td>Phone No.<br />
             <input type="text" name="phone" maxlength="40"  value="<?php if(isset($_POST['phone'])) echo $_POST['phone']; ?>" class="readonly" readonly="readonly"  />
           </td>
           <td>Email<br />
             <input type="text" name="email"  value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>"  class="readonly" readonly="readonly"/>
           </td>
           <td>Fax<br />
             <input type="text" name="fax"  value="<?php if(isset($_POST['fax'])) echo $_POST['fax']; ?>" class="readonly" readonly="readonly" />
           </td>           
         </tr>
         <tr>           
           <td>Website<br />
             <input type="text" name="website"  value="<?php if(isset($_POST['website'])) echo $_POST['website']; ?>" class="readonly" readonly="readonly" />
           </td>
           <td colspan="5">Remark<br />
             <input type="text" name="remark"  value="<?php if(isset($_POST['remark'])) echo $_POST['remark']; ?>" class="readonly" readonly="readonly" />
           </td>
         </tr>
         <tr>
           <td colspan="6">
              <div class="subhead1">Account Setting</div>
            </td>
         </tr>
         <tr>
           <td>Username<br />
             <input type="text" name="username"  value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" class="readonly" readonly="readonly"/>
            </td>           
            <td>Password <br />                    
              <input type="text" name="pass"  value="<?php if(isset($_POST['pass'])) echo $_POST['pass']; ?>" class="readonly" readonly="readonly"/>
            </td>
           <td>Discount<br />
             <input type="text" name="discount" maxlength="5"  value="<?php if(isset($_POST['discount'])) echo $_POST['discount']; ?>"  class="readonly" readonly="readonly"/>
           </td>
           <td>&nbsp;</td>
         </tr>
         <tr>
           <td colspan="6">
              <div class="subhead1">Contact Persons</div>
            </td>
         </tr>
         <tr>
           <td colspan="6">
           <!-- party contact person details start here -->
           <table width="100%">
             <tr>
               <td>Contact Person<br />
                 <input type="text" name="cname[]"  value="<?php if(isset($_POST['cname'][0])) echo $_POST['cname'][0]; ?>" onblur="this.value = ucwords(trim(this.value));" class="readonly" readonly="readonly"/>
                </td>
                <td>Department<br />
                 <input type="text" name="cdepartment[]"  value="<?php if(isset($_POST['cdepartment'][0])) echo $_POST['cdepartment'][0]; ?>" class="readonly" readonly="readonly"/>
                </td>
                <td>Desgination<br />
                 <input type="text" name="cdesignation[]"  value="<?php if(isset($_POST['cdesignation'][0])) echo $_POST['cdesignation'][0]; ?>" class="readonly" readonly="readonly"/>
                </td>
                <td>Mobile<br />
                 <input type="text" name="cmobile[]"  value="<?php if(isset($_POST['cmobile'][0])) echo $_POST['cmobile'][0]; ?>" class="readonly" readonly="readonly"/>
                </td> 
                <td>Email<br />
                 <input type="text" name="cemail[]"  value="<?php if(isset($_POST['cemail'][0])) echo $_POST['cemail'][0]; ?>" class="readonly" readonly="readonly"/>
                </td> 
                <td>Phone<br />
                 <input type="text" name="cphone[]"  value="<?php if(isset($_POST['cphone'][0])) echo $_POST['cphone'][0]; ?>" class="readonly" readonly="readonly"/>
                </td> 
             </tr>
             <tr>
               <td>Contact Person<br />
                 <input type="text" name="cname[]"  value="<?php if(isset($_POST['cname'][1])) echo $_POST['cname'][1]; ?>" onblur="this.value = ucwords(trim(this.value));" class="readonly" readonly="readonly"/>
                </td>
                <td>Department<br />
                 <input type="text" name="cdepartment[]"  value="<?php if(isset($_POST['cdepartment'][1])) echo $_POST['cdepartment'][1]; ?>" class="readonly" readonly="readonly"/>
                </td>
                <td>Desgination<br />
                 <input type="text" name="cdesignation[]"  value="<?php if(isset($_POST['cdesignation'][1])) echo $_POST['cdesignation'][1]; ?>" class="readonly" readonly="readonly"/>
                </td>
                <td>Mobile<br />
                 <input type="text" name="cmobile[]"  value="<?php if(isset($_POST['cmobile'][1])) echo $_POST['cmobile'][1]; ?>" class="readonly" readonly="readonly"/>
                </td> 
                <td>Email<br />
                 <input type="text" name="cemail[]"  value="<?php if(isset($_POST['cemail'][1])) echo $_POST['cemail'][1]; ?>" class="readonly" readonly="readonly"/>
                </td> 
                <td>Phone<br />
                 <input type="text" name="cphone[]"  value="<?php if(isset($_POST['cphone'][1])) echo $_POST['cphone'][1]; ?>"class="readonly" readonly="readonly" />
                </td>
               </tr>               
             </table>
             <!-- party contact person details ends here -->
           </td>
         </tr>         
         <tr>
           <td colspan="6"><div class="subhead1">Customer Address</div></td>
         </tr>
         <tr>
           <td colspan="6">
             <!-- table to capture the address field starts -->
             <table width="100%" class="valigntop">
               <tr>
                 <td>Address<br />
             	   <textarea style="height:50px;" name="adr" class="readonly" readonly="readonly"><?php if(isset($_POST['adr'])) echo $_POST['adr']; ?></textarea>
                 </td>
                 <td>Landmark<br />
             	   <input type="text" name="landmark"  value="<?php if(isset($_POST['landmark'])) echo $_POST['landmark']; ?>"  class="readonly" readonly="readonly"/>
                 </td>
                 <td>Locality<br />
             	   <input type="text" id="locality" name="locality"  value="<?php if(isset($_POST['locality'])) echo $_POST['locality']; ?>"  class="readonly" readonly="readonly"/>
                 </td>
                 <td>City/District<br />
             	   <input type="text" id="city" name="city_district"  value="<?php if(isset($_POST['city_district'])) echo $_POST['city_district']; ?>"  class="readonly" readonly="readonly"/>
                 </td>
                 <td>State<br />
             	   <?php db_pulldown($dbc, 'state', 'SELECT statename, statename FROM state ORDER BY statename ASC', true, true, 'disabled="disabled"');?>
                 </td>
                 <td>Pincode<br />
             	   <input type="text" id="pincode" name="pincode"  value="<?php if(isset($_POST['pincode'])) echo $_POST['pincode']; ?>" onkeypress="return isNumberKeyEvent();"  class="readonly" readonly="readonly"/>
                    <input type="hidden" id="country" name="country"  value="<?php if(isset($_POST['country'])) echo $_POST['country']; else echo'India' ?>"  />
                 </td> 
               </tr>
             </table>
             <!-- table to capture the address field ends -->
           </td>
         </tr>
         <tr>
           <td colspan="6"><div class="subhead1">Customer Range, Division, Tax Details</div></td>
         </tr>
         <tr>
           <td>Range<br />
             <input type="text" name="crange"  value="<?php if(isset($_POST['crange'])) echo $_POST['crange']; ?>" class="readonly" readonly="readonly"/>
            </td>
            <td>Division<br />
             <input type="text" name="cdivision"  value="<?php if(isset($_POST['cdivision'])) echo $_POST['cdivision']; ?>" class="readonly" readonly="readonly"/>
            </td>
            <td>TIN No.<br />
             <input type="text" name="tinno"  value="<?php if(isset($_POST['tinno'])) echo $_POST['tinno']; ?>" class="readonly" readonly="readonly"/>
            </td> 
            <td>ECC No.<br />
             <input type="text" name="eccno"  value="<?php if(isset($_POST['eccno'])) echo $_POST['eccno']; ?>" class="readonly" readonly="readonly"/>
            </td>
            <td>VAT No.<br />
             <input type="text" name="vatno"  value="<?php if(isset($_POST['vatno'])) echo $_POST['vatno']; ?>" class="readonly" readonly="readonly"/>
            </td>
            <td>CST No.<br />
             <input type="text" name="cstno"  value="<?php if(isset($_POST['cstno'])) echo $_POST['cstno']; ?>" class="readonly" readonly="readonly"/>
            </td> 
         </tr>   
         <!--<tr>
           <td align="center" colspan="6">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
			<?php if(isset($heid))echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            </td>
          </tr>-->
        </table>
      </fieldset>
    </form>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('partycode');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>