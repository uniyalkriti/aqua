<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Observation Sheet'; // to indicate what type of form this is
$formaction = $p;
$myobj = new srf();
$cls_func_str = 'srf'; //The name of the function in the class that will do the job
$myorderby = 'srfdate DESC, srfno ASC'; // The orderby clause for fetching of the data
$myfilter = 'srfId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);

//$observation = new observation();
?>
      <div id="breadcumb"><a href="#">Calibration</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php require_once(BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'calibration'.SYM.'breadcum'.SYM.'calibration.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc; 
	if(empty($_POST['srfcode'])) return array(FALSE, 'Please enter <strong>SRF No.</strong>');
	return array(TRUE, '');
}

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
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
			$filterstr = array();			if(!empty($_POST['srfcode'])){
				$filter[] = "srfcode = '$_POST[srfcode]'";
				$filterstr[] = '<b>SRF No : </b>'.$_POST['srfcode'];
			}
			
			$funcname = 'get_'.$cls_func_str.'_list';
		    $rs1 = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");
			//getting the srfItemId			
			foreach($rs1 as $key=>$value) $id = $key;
			$rs = array();
			if(!empty($rs1)){
				$filterstr[] = '<b>SRF Date : </b>'.$rs1[$id]['srfdate'];	
				$rs = $rs1[$id]['srf_item'];
			}
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>'); 
			
			if(empty($rs))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}

//This will show the list of all pending items whose certificate has not been done
if(isset($_POST['pending']))
{
	$filterused = 'Listing all pending Items';
	$rs = get_my_reference_array_direct("SELECT srf_item.*, srfcode, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate FROM srf_item INNER JOIN srf USING(srfId) WHERE srfItemId NOT IN (SELECT srfItemId FROM observation_sheet)", 'srfItemId');
}
?>
<script type="text/javascript">
$(function() {
$("#srfcode").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-srfcode.php"
		});
$("#salecode, #barsalecode").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-salecode.php"
		});
});

//This function will print all the selected srf
function printallselected()
{
	var toprint = '';
	$('input[type="checkbox"][name="myval[]"]:checked').each(function(){
	 toprint += $(this).val()+'-';	
	});
	//alert(toprint);
	if(toprint != ''){
		$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1&mode=1&id='+toprint+'&actiontype=print-observation', iframe:true, width:'95%', height:'95%'});
		return true;
	}
	else	return false;	
}
</script>
<style type="text/css">
.cc{ color:white;cursor:pointer;font-size:14px; }
.cc:hover{ color:red;cursor:pointer;border:1px solid red; }
#mysaletable tr td{width:150px; vertical-align:top;}
input[autocomplete="off"] { width:200px; }
.tfoot{font-weight:bold;}
.grandrow{font-size:18px;}
</style>
    <div id="workarea">
      <?php 
	  //This block of code will help in the print work
	  if(isset($_GET['actiontype'])){
		switch($_GET['actiontype']){
			case'print-observation':
				require_once('observation-sheet-print.inc.php');
				exit();
				break;	
			default:
				$filepath = BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'calibration'.SYM.'observation-sheet'.SYM.'observation-sheet-srf-print.inc.php';
				if(is_file($filepath)) require_once($filepath);
				exit();
				break;
		}//switch($_GET['actiontype']){ ends
	  }
	  //This block of code will help in the print work ens
	  ?>
	  
    <?php {//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm_alert('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr>
           <td>
             <!-- this table will contain our form filter code starts -->
			 <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>                
                <td>Srf No.<br />
                 <input type="text" id="srfcode" name="srfcode" value="<?php if(isset($_POST['srfcode'])) echo $_POST['srfcode'];?>" />
				 <?php //db_pulldown($dbc, 'icId', "SELECT  icId, icname FROM  item_category ORDER BY icname ASC", true, true, 'class="hid_txt"');  ?> 
                </td>
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" /> 
                  <input id="mysave" type="submit" name="pending" value="List Pending" />
                  <input onclick="return printallselected();" type="button" value="Print ALL" title="Print selected <?php echo $formaction; ?>" />
                </td>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
	<?php
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
                <div><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="printhide"><input type="checkbox" id="myselect" onclick="selectCheckBoxes('myselect','myval[]');" /></td>
                      <td class="sno">S.No</td>                    
                      <td>Item Description</td>
                      <td>DMT/Lab Code</td>
                      <td>Equipment ID</td>
                      <td>Make</td>
                      <td>Model</td>
                      <td>Sr.No</td>
                      <td>Cal.Steps</td>
                      <td>Range/Size</td>
                      <td>Least Count</td>
                      <td><span title="Calibration Frequency">C.F.</span></td>
                      <td>Status</td>
                      <td class="options">Options</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
				  
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['srfItemId'];
					  $uidname = $rows['lab_code'];
					  $editlink = '<a class="iframef" title="Calculate Uncertainity" href="index.php?option=uncertainity-calculate&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
					  $printlink = '<a title="Print Observation Sheet '.$uidname.'" class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print-observation"><img src="../icon-system/i16X16/print.png"></a><span class="seperator">|</span>';
					  $deletelink = '';//'<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'SRF Delete\', \''.$uid.'\',\'SRF\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
					  //if(false && $rows['locked'] == 1) $editlink = $deletelink = '';
					  //checking whether the certificate created or not for the selected srfItemId
					  list($opt1, $rs1) = run_query($dbc,"SELECT obsId FROM observation_sheet WHERE srfItemId = $uid LIMIT 1");
					  $donestate = $opt1 ? '<span style="color:green; font-weight:bold;">Done</span>' : '<span style="color:red; font-weight:bold;">Pending</span>';
					  	
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
					    <td class="printhide"><input type="checkbox" name="myval[]" value="'.$uid.'"/></td>
						<td>'.$inc.'</td>						
						<td>'.$rows['itemdesc'].'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
						<td>'.$rows['equipno'].'</td>
						<td>'.$rows['make'].'</td>
						<td>'.$rows['model'].'</td>
						<td>'.$rows['serial_no'].'</td>
						<td>'.$GLOBALS['cal_step_type'][$rows['cal_step_type']].'</td>
						<td>'.$rows['range_size'].'</td>
						<td>'.$rows['least_count'].'</td>
						<td>'.$rows['calibration_frequency'].'</td>
						<td>'.$donestate.'</td>
						<td class="options">'.$printlink.$editlink.$deletelink.'</td>
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
	  <?php if(isset($heid)) echo'<script type="text/javascript">getvaluetotal(); setsnosale("mysaletable")</script>'?>