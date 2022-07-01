<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'MMD List2'; // to indicate what type of form this is
$formaction = $p;
$myobj = new srf();
$cls_func_str = 'mmdlist'; //The name of the function in the class that will do the job
$myorderby = 'mmdId ASC'; // The orderby clause for fetching of the data
$myfilter = 'mmdId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
<script type="text/javascript">
$(function() {
$(".custname").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-customer-name.php"
		});

 // $('.hid_txt').on('change', function(){alert('hi');});
});


/*$(function(){
	$('.hid_txt').myhidden_el();	
	$('.hid_txt').on('change', function(){
		var selname = this.name;
		var selvalue = this.value;
		var seltext = selvalue == '' ? '' : this.options[this.selectedIndex].text;
		var hidname = 'hid_'+selname;
		//alert(document.getElementsByName(hidname).length);
		if(document.getElementsByName(hidname).length == 0)
			$(this).after('<input type="hidden" name="hid_'+this.name+'" value="'+seltext+'">' );
		else
			$('input[name="'+hidname+'"]').val(seltext);
	});	
	
	//hide_when_zero();
});*/
</script>
      <div id="breadcumb"><a href="#">Master</a> &raquo; <a href="#">Item</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //require_once('breadcum/item.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc; 
	return array(TRUE, '');
	if(empty($_POST['forstudent'])) return array(FALSE, 'Please select a forstudent');
	if(empty($_POST['cname'])) return array(FALSE, 'Please enter cname');
	$_POST['cname'] = ucwords($_POST['cname']);
	//$andpart = " cityId = {$_POST['cityId']}";
	$field_arry = array('cname' => $_POST['cname']);// checking for  duplicate Unit Name	
	if($mode == 'add')
	{
		//if(uniqcheck_msg($dbc,$field_arry,'course', false, $andpart))
		if(uniqcheck_msg($dbc,$field_arry,'course', false))
			return array(FALSE, '<b>Course</b> already exists, please provide a different value.');
	}
	elseif($mode == 'edit')
	{
		//if(uniqcheck_msg($dbc,$field_arry,'course', false," cId != '$_GET[id]' AND ".$andpart))
		if(uniqcheck_msg($dbc,$field_arry,'course', false," cId != '$_GET[id]'"))
			return array(FALSE, '<b>Course</b> already exists, please provide a different value.');
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
			$action_status = $myobj->$funcname(); // $myobj->item_category_save()
			if($action_status['status'])
			{
				echo '<span class="asm">'.$action_status['myreason'].'</span>';
				unset($_POST);
				//unset($_SESSION[SESS.'securetoken']);
				echo'<script type="text/javascript">ajax_refresher(\'irange_unit\', \'getUnit\', \'\');</script>'; 	
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
				unset($_POST);
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

//To call our function directly without the need to click
if(isset($_GET['reportmode']) && isset($_GET['reporttype']) && !isset($_POST['filter'])){ 
	$mcr = new customreport; $mcr->customreport($_GET['reporttype']);
}

$filterused = '';
$mymatch['datepref'] = array('cal_due_date'=>'Cal. Due Date');
if(isset($_POST['filter']) && $_POST['filter'] == 'Filter')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = checkform();	
		if($checkpass)
		{
			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			$filter = array();
			$filterstr = array();
			if(!empty($_POST['start']) && !empty($_POST['datepref'])){
				$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(".$_POST['datepref'].",'".MYSQL_DATE_SEARCH."') >= '$start'";
				$filterstr[] = '<b>Date Selector : </b>'.$mymatch['datepref'][$_POST['datepref']];
				$filterstr[] = '<b>Start : </b>'.$_POST['start'];
			}
			if(!empty($_POST['end'])&& !empty($_POST['datepref'])){
				$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(".$_POST['datepref'].",'".MYSQL_DATE_SEARCH."') <= '$end'";
				$filterstr[] = '<b>End : </b>'.$_POST['end'];
			}
			//echo $_POST['partyId'];
			if(!empty($_POST['partyId'])){
				$filter[] = "partyId = '$_POST[partyId]'";
				$filterstr[] = '<b>Customer : </b>'.myrowval('party', 'partyname', "partyId = $_POST[partyId]");
			}
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>'); 
			//pre($filter);
			$funcname = 'get_'.$cls_func_str.'_list';
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby"); // $myobj->get_item_category_list()
			if(empty($rs))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
?>
<script type="text/javascript">
$(function() {
$(".equipno").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-equipno.php"
		});
});
</script>
    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
      <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" enctype="multipart/form-data" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend"  style=""><?php echo $forma; ?></legend>
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
          <tr>
          	<input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
            <span class="star">*</span>Party <br />
          	<td><?php db_pulldown($dbc, 'partyId', "SELECT partyId, partyname FROM  party WHERE ptype = 2 ORDER BY partyname ASC", true, true, 'lang="Party"');?></td>
            <td>
            	<input type="file" name="mmdlist" lang="Section File" />
            </td>
            <td>
            	<a href="../myuploads/default/mmdfile.xls">Download sample file</a>
            </td>
	      </tr>
          	
          <tr>
            <td align="center" colspan="3">
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
         <tr>
           <td>
             <!-- this table will contain our form filter code starts -->
             <?php if(!isset($_GET['reportmode'])){?>
			 <fieldset>
              <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	
               <input type="hidden" name="partyId" value="<?php echo $_SESSION[SESS.'id']?>"  />
               <legend class="legend">Search <?php echo $forma;?></legend>
               <table>
              <tr>
                <td><span class="star">*</span>Date Selector<br />
                 <?php arr_pulldown('datepref', array('cal_due_date'=>'Cal. Due Date'), '', true, false, 'class="hid_txt"', '', ''); ?>
                </td>
                <td><span class="star">*</span>Start<br />
                    <input type="text" class="qdatepicker" name="start" value="<?php if(isset($_POST['start'])) echo $_POST['start']; else echo '01/'.date('m/Y'); ?>"/>
                </td>
                <td><span class="star">*</span>End<br />	
                    <input type="text" class="qdatepicker" name="end" value="<?php if(isset($_POST['end'])) echo $_POST['end']; else echo date('d/m/Y'); ?>"/>
                <!--</td>
                <td>Equipment No.<br />
                 <input type="text" class="equipno" name="equipno" value="<?php if(isset($_POST['equipno'])) echo $_POST['equipno'];?>" />
				 
                </td>
                <td>Party<br />
                <?php db_pulldown($dbc, 'partyId', "SELECT  partyId, partyname FROM  party WHERE ptype = 2 ORDER BY partyname ASC", true, false, 'class="hid_txt"');  ?> 
                </td>-->
               
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <!--<input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
                </td>
              </tr>
             </table>
             </fieldset>
             <?php } // if(!isset($_GET['reportmode'])){?>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
	<?php
    //$filter = array();
    //$filter[] = "ptype=1 AND DATE_FORMAT(NOW(),'".MYSQL_DATE_SEARCH."') = DATE_FORMAT(created,'".MYSQL_DATE_SEARCH."')"; 
   // $rs = $myobj->get_item_category_list($filter='',  $records = '', $orderby ="ORDER BY icname ASC");
	//pre($rs);
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
    	  <tr>
            <td>
              <div class="subhead1"><!-- this portion indicate the print options -->
                <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="../icon-system/i16X16/print.png" /></a>
                <a href="javascript:pdf('searchlistdiv');" title="save as pdf document" style="margin-right:10px;"><img src="../icon-system/i16X16/pdf.png" /></a>
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
                <div><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong>)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Callibration Due Date</td>
                      <td>Equipment No.</td>
                      <td>Equipment Name</td>
                      <td>Certificate No.</td>
                      <td>Party.</td>
                      <td>Range/Size.</td>
                      <td>Leastcount</td>
                      <td>Callibration Date</td>
                      
                      <!--<td class="options">Options</td>-->
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
				  
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['mmdId'];
                      $uidname = $rows['equipno'];
					  $editlink = '';//'<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
					  $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'MMDLIST Delete\', \''.$uid.'\',\'MMDLIST\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
					  if(false && $rows['locked'] == 1) $editlink = $deletelink = '';
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
					    <td>'.$inc.'</td>
						<td>'.$rows['cal_due_date'].'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
						<td>'.$rows['itemdesc'].'</td>
						<td>'.$rows['certificate_no'].'</td>
						<td>'.$rows['partyId_val'].'</td>
						<td>'.$rows['range_size'].'</td>
						<td>'.$rows['least_count'].'</td>
						<td>'.$rows['calibration_date'].'</td>
						
						<!--<td class="options">'.$editlink.$deletelink.'</td>-->
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here
                  ?>
                </table>                
            </div> 
              <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
            </td>
          </tr>
          <?php } //if(!empty($rs)){?>
        </table>
        <?php if(isset($pgoutput)) echo $pgoutput['pglinks']; // showing the paginataion links to the user?>
      </fieldset>
      </form>
      <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('rclename');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>