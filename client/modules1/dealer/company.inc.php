<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$forma = 'Company'; // to indicate what type of form this is
$formaction = $p;
$myobj = new company();
$dealer_sale = new dealer_sale();
$cls_func_str = 'company'; //The name of the function in the class that will do the job
$myorderby = 'id DESC'; // The orderby clause for fetching of the data
$myfilter = 'id ='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$retailer_level=$_SESSION[SESS.'constant']['retailer_level'];
$sesId = $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['urole'];
$dealer_id = $dealer_sale->get_dealer_id($sesId, $role_id);

?>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php 
//if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/userbread.php'); 
?>
</div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc, $dealer_id;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('name' => $_POST['name']);// checking for  duplicate Unit Name
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'company', false, " dealer_id = '$dealer_id'"))
			return array(FALSE, '<b>Dealer Name</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'company', false," id != '$_GET[id]' AND dealer_id = '$dealer_id'"))
			return array(FALSE, '<b>Dealer Name</b> already exists, please provide a different value.');
	}
	return array(TRUE, '');
}
############################# code for SAVING data starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Save')
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
				show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
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
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);		
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
				show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
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
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
		//This will containt the pr no, pr date and other values
		$funcname = 'get_'.$cls_func_str.'_list';
		$mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '', $orderby=''); // $myobj->get_item_category_list()
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$id, $in = $mystat);
			$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}


############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
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
			if(!empty($_POST['name'])){
				$filter[] = "name LIKE '%$_POST[name]%'";
				$filterstr[] = '<b>Name  : </b>'.$_POST['name'];
			}
                        $filter[] = "dealer_id = '$dealer_id'";
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
                        $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");
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
else { 
       $rs = $myobj->$funcname($filter="dealer_id = '$dealer_id'",  $records = '', $orderby="ORDER BY $myorderby");
}
dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function(){
	$(".retailer").autocomplete({
			source: "index.php?option=myajax-autocomplete&subauto=retailer-search&searchdomain=retailername",
			minLength: 1
		});
});

</script>
    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="dealer_id" value="<?php echo $dealer_id;?>" />
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr valign="top">
              <td><span class="star">*</span>Company Name<br>
                  <input style="width: 220px;" onChange="this.value = ucwords(trim(this.value));" type="text"  lang="Company Name" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
              </td>
              <td colspan="2">
                  Address<textarea name="address"><?php if(isset($_POST['address'])) echo $_POST['address']; ?></textarea>
              </td>
               <td>
              Email<Br/>
              <input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>"  />
             </td>      
          </tr>
          <tr valign="top">
              <td>
              Landline<Br/>
              <input type="text" onkeypress="return isNumberKey(event);" name="landline" value="<?php if(isset($_POST['landline'])) echo $_POST['landline']; ?>" maxlength="16" />
            </td>
             <td>Alternate Number <input type="text" name="other_numbers" id="alternate_number" value="<?php if(isset($_POST['other_numbers'])) echo $_POST['other_numbers']; ?>">
             </td>
          </tr>
          <tr>
              <td colspan="7"><div class="subhead1">Constant Details</div></td>
          </tr>
          <tr>
              <td>Catalog Level<br>
                  <input type="text" name="catalog_level" onchange="getdata_div(this.value, 'progdiv', 'get_catalog_title', 'catalog_title');" value="<?php if(isset($_POST['catalog_level'])) echo $_POST['catalog_level']; ?>">
              </td>
              <td>Location Level<br>
                    <input onchange="getdata_div(this.value, 'progdiv', 'get_location_title', 'location_title');" type="text" name="location_level" value="<?php if(isset($_POST['location_level'])) echo $_POST['location_level']; ?>">
              </td>
              <td>Dealer Level<br>
                   <input type="text" name="dealer_level" value="<?php if(isset($_POST['dealer_level'])) echo $_POST['dealer_level']; ?>">
              </td>
              <td>Retailer Level<br>
                <input type="text" name="retailer_level" value="<?php if(isset($_POST['retailer_level'])) echo $_POST['retailer_level']; ?>">  
              </td>
          </tr>
          <tr>
              <td colspan="7"><div class="subhead1">Catalog Title Details</div></td>
          </tr>
          <tr>
              <td colspan="7">
                  <div id="catalog_title"></div>
                 
              </td>
          </tr>
          <tr>
              <td colspan="7"><div class="subhead1">Location Title Details</div></td>
          </tr>
          <tr>
              <td colspan="7">
                  <div id="location_title"></div>
                 
              </td>
          </tr>
         <tr>
           <td align="center" colspan="7">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
			<?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
			<?php }else{?>
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
    <?php }else{//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
	      <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
		<td>Name<br />
                <input type="text"  name="name" id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /> 
                </td>
                 <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
                </td>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
	<?php
	if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
    	  <tr>
            <td>
              <div class="subhead1"><!-- this portion indicate the print options -->
                <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
                <a href="javascript:pdf('searchlistdiv');" title="save as pdf document" style="margin-right:10px;"><img src="./icons/pdf.png" /></a>
                <?php echo $forma; ?> List
              </div>
            </td>
          </tr>	
          <tr>
            <td>            
              <?php
           
	      ########################## pagination details fetch starts here ###################################
              $pgoutput = get_pagination_details($rs);	 
              echo $pgoutput['loader'];
              foreach($pgoutput['temp_result'] as $key=>$value){
                 $rs = $value; 	 
                 echo'<div class="mypages" id="mypages'.$key.'" style="display:none;">';
			  ########################## pagination details fetch ends here ###################################
			  $inc = 1+($key-1)*PGDISPLAY;
			  $lastinc = (($inc+PGDISPLAY-1) > $pgoutput['totrecords']) ? $pgoutput['totrecords'] : ($inc+PGDISPLAY-1);
			  ?>	 
              
              <div class="searchlistdiv" id="searchlistdiv"> 
                <div class="myprintheader"><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Name</td>
                      <td>Email</td>
                      <td>Mobile</td>
                      <td>Address</td>
                     
                      <td class="options">Options</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['name'];
                      $lat_lng = $rows['lat_long'];
                      $mcc = $rows['mncmcclatcellid'];		  
		      $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                     
		     $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Retailer Delete\', \''.$uid.'\',\'Retailer\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                     $editlink = $deletelink = '';
                      //if($rows['locked'] == 1) $editlink = $deletelink = '';
		      if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>						
                        <td>'.$rows['email'].'</td>
                        <td>'.$rows['landline'].'</td>
                        <td>'.$rows['address'].'</td>
                        
                        <td class="options">'.$editlink.$deletelink.'</td>
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here
                    if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row
                  ?>
                </table>                
            </div> 
              <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
            </td>
          </tr>
          <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>
        </table>
        <?php if(isset($pgoutput)) echo $pgoutput['pglinks']; // showing the paginataion links to the user?>
      </fieldset>
      </form>
      <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('name');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>