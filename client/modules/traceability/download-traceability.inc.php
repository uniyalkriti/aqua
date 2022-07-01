<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'traceability'; // to indicate what type of form this is
$formaction = $p;
$myobj = new traceability();
$cls_func_str = 'traceability'; //The name of the function in the class that will do the job
$myorderby = 'traceId DESC'; // The orderby clause for fetching of the data
$myfilter = 'traceId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);

?>
      <div id="breadcumb"><a href="#">Master</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //require_once(BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'calibration'.SYM.'breadcum'.SYM.'calibration.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
if(isset($_GET['id']))
{
  $traceId = $_GET['id'];
  $rs = $myobj->get_traceability_list($filter="traceId = $traceId", $records='', $orderby='');
 
  $file_path='../myuploads/attachment/'.$rs[$traceId]['filename'];
  $ext = substr($rs[$traceId]['filename'], strrpos($rs[$traceId]['filename'],'.')+1);
  $name = $rs[$traceId]['certno'].'.'.$ext;
  output_file($file_path, $name , 'text/plain'); //this function is defined in common_function.php
}

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
			$action_status = $myobj->$funcname();  // $myobj->item_category_save()
			//pre($action_status);
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
		$mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '1', $orderby=''); // $myobj->get_item_category_list()
		$interest = $mystat[$id];
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$id, $in = $mystat);
			//This will create the post multidimensional array
			$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}


############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$mymatch['datepref'] = array('srfdate'=>'SRF Date', 'po_challan_date'=>'PO/Challan Date', 'created'=>'Created');
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
			if(!empty($_POST['certno'])){
				$filter[] = "certno = '$_POST[certno]'";
				$filterstr[] = '<b>Certificate No. : </b>'.$_POST['certno'];
			}
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>'); 
			$funcname = 'get_'.$cls_func_str.'_list';
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby"); // $myobj->get_item_category_list()
			//pre($rs);
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
$("#certno").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-traceability-certno.php"
		}); 
});
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
	  
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr>
           <td>
             <!-- this table will contain our form filter code starts -->
			 <fieldset>
             <legend class="legend">Search <?php echo $forma;?></legend>
             <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
                <!--<td><span class="star">*</span>Date Selector<br />
                    <?php echo arr_pulldown('datepref', $mymatch['datepref'], '', true, true, '', false, ' '); ?>
                </td>
                <td><span class="star">*</span>Start<br />
                    <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	
                    <input type="text" class="qdatepicker" name="start" value="<?php if(isset($_POST['start'])) echo $_POST['start']; else echo '01/'.date('m/Y'); ?>"/>
                </td>
                <td><span class="star">*</span>End<br />	
                    <input type="text" class="qdatepicker" name="end" value="<?php if(isset($_POST['end'])) echo $_POST['end']; else echo date('d/m/Y'); ?>"/>
                </td>-->
                <td>Certificate No.<br />
                 <input type="text" id="certno" name="certno" value="<?php if(isset($_POST['certno'])) echo $_POST['certno'];?>" />
                </td>
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <!--<input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
                </td>
              </tr>
             </table>
             </fieldset>
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
                <!--<a href="javascript:pdf('searchlistdiv');" title="save as pdf document" style="margin-right:10px;"><img src="../icon-system/i16X16/icons/pdf.png" /></a>-->
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
                      <td class="sno">S.No</td>                    
                      <td>Certificate nO.</td>
                      <td>Download</td>
                      <!--<td class="options">Options</td>-->
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
				  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['traceId'];
					  $uidname = $rows['certno'];
					  $editlink = '';// '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
					  $filelink = !empty($rows['filename']) ? '<a    title="Download Traceability" href="index.php?option=download-traceability&id='.$rows['traceId'].'"><img src="../icon-system/i16X16/b_view.png"></a>' : '';
					  $deletelink = '';//'<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Traceability Delete\', \''.$uid.'\',\'Traceability\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
					   echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
						<td>'.$inc.'</td>						
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
						<td>'.$filelink.'</td>
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
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('rclename');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>
	  <?php if(isset($heid)) echo'<script type="text/javascript">getvaluetotal(); setsnosale("mysaletable")</script>'?>