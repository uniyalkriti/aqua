<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Catalog Product'; // to indicate what type of form this is
$formaction = $p;
$myobj = new catalog_product();
$cls_func_str = 'product_details'; //The name of the function in the class that will do the job
$myorderby = 'catalog_id DESC'; // The orderby clause for fetching of the data
$myfilter = 'catalog_product_details.id='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);

?>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a> catalog Product</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) //require_once('breadcum/item.php');  ?>
</div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('name' => $_POST['name']);// checking for  duplicate Unit Name
	
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'catalog_product', false, "unit='$_POST[unit]'"))
			return array(FALSE, '<b>Product</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'catalog_product', false," id != '$_GET[id]' AND unit='$_POST[unit]'"))
			return array(FALSE, '<b>Product</b> already exists, please provide a different value.');
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
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
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
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
		//This will containt the pr no, pr date and other values	
	}									 
}
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
if(isset($_GET['mode']) && $_GET['mode'] == 2)
{
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['pid'];
                $pdid = $_GET['id'];
		//This will containt the pr no, pr date and other values
		$funcname = 'get_'.$cls_func_str.'_list';
		$mystat = $myobj->$funcname($filter="$myfilter'$pdid'",  $records = '', $orderby=''); // $myobj->get_item_category_list()
           
               // pre($mystat);
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$pdid, $in = $mystat);
			$heid = '<input type="hidden" name="eid" value="'.$pdid.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}
############################# Code to handle the user search starts here ###############################

$rs = $myobj->$funcname($filter= "product_id = '$id'", $records = '', $orderby = '');
dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function() {
	$(".product").autocomplete({
		source: "./modules/ajax-autocomplete/product/ajax-product-name.php"
	});
	$("#itemname").autocomplete({
		source: "./modules/ajax-autocomplete/item/ajax-itemname.php"
	});
});
</script>
    <div id="workarea">
     
        <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="old_file" value="<?php if(isset($_POST['image_name'])) echo $_POST['image_name']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
          <td><span class="star">*</span>Name <br>
          <?php db_pulldown($dbc, 'product', $q = "SELECT id, name FROM catalog_product ORDER BY name ASC", true, true, $jsfunction='disabled="disabled"','',$id); ?>
              <input type="hidden" name="product_id" value="<?php echo $id; ?>" >
          </td>
         
            <td>Batch<br>
                <input type="text" name="batch_no" value="<?php if(isset($_POST['batch_no'])) echo $_POST['batch_no']; ?>">
            </td>
            <td>Mfg Date<br>
                <input type="text" class="qdatepicker" name="mfg_date" value="<?php if(isset($_POST['mfg_date'])) echo $_POST['mfg_date']; ?>"
            </td>
          <td>Expiry Date<br>
                <input type="text" class="qdatepicker" name="expiry_date" value="<?php if(isset($_POST['expiry_date'])) echo $_POST['expiry_date']; ?>">
            </td>
        
          </tr>
          <tr>
              <td>Opening Stock<br>
                  <input type="text" name="ostock" value="<?php if(isset($_POST['ostock'])) echo $_POST['ostock']; ?>">
              </td>
               <td>Rate<br>
                  <input type="text" name="rate" value="<?php if(isset($_POST['rate'])) echo $_POST['rate']; ?>">
              </td>
          </tr>
         <tr>
           <td align="center" colspan="4">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
			<?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input onclick="window.document.location = 'indexpop.php?option=<?php echo $formaction; ?>&showmode=1&mode=1&id=<?php echo $id; ?>';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
            <?php //edit_links_via_js($id, $jsclose=true, $options=array());?>            
			<?php }else{?>
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         
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
                      <td>Product Name</td>
                      <td>Batch No</td>
                      <td>Opening Stock</td>
                      <td>Rate</td>
                      <td>Mfg Date</td>
                      <td>Expiry Date</td>
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
                      $uidname = $rows['product_name'];
					  
                      $editlink = '<a  href="indexpop.php?option='.$formaction.'&mode=2&id='.$uid.'&pid='.$id.'"><img src="./images/b_edit.png"></a>';
                      $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Catalog Product Delete\', \''.$uid.'\',\'Product\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                     
                      $deletelink = '';
                      if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>						
                        <td>'.$rows['batch_no'].'</td>
                        <td>'.$rows['ostock'].'</td>
                        <td>'.$rows['rate'].'</td>
                        <td>'.$rows['mfg_date'].'</td>
                        <td>'.$rows['expiry_date'].'</td>
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
     
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('partycode');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>