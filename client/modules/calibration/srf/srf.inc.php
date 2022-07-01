<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Calibration Order'; // to indicate what type of form this is
$formaction = $p;
$myobj = new srf();
$cls_func_str = 'srf'; //The name of the function in the class that will do the job
$myorderby = 'po_challan_dateo DESC, srfno ASC'; // The orderby clause for fetching of the data
$myfilter = 'srfId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);

?>
      <div id="breadcumb"><a href="#">Order</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php require_once(BASE_URI.'modules'.SYM.'calibration'.SYM.'breadcum'.SYM.'calibration.php');  ?>
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
			
			// creating a special array to provide the editing details
			$preserve = array();
			//$preserve[1] = array('itemdesc'=>'Vernier Caliper', 'lab_code'=>'', 'equipno'=>'VC-01', 'make'=>'Yamaki', 'model'=>'xyz', 'serial_no'=>'MM-01', 'range_size'=>'0-25MM', 'least_count'=>'0.01mm', 'cal_step_type'=>'1', 'cal_step_detail'=>'', 'calibration_frequency'=>'1');
			//pre($_POST['srf_item']);
			foreach($_POST['srf_item'] as $key=>$value){
				$preserve[$key] = array('itemId'=>$value['itemId'], 'itemdesc'=>$value['itemdesc'], 'lab_code'=>$value['lab_code'], 'equipno'=>$value['equipno'], 'make'=>$value['make'], 'model'=>$value['model'], 'serial_no'=>$value['serial_no'], 'range_size'=>$value['range_size'], 'least_count'=>$value['least_count'], 'cal_step_type'=>$value['cal_step_type'], 'cal_step_detail'=>$value['cal_step_detail'], 'calibration_frequency'=>$value['calibration_frequency'], 'rifId'=>$value['rifId'], 'tmpmasterId'=>$value['tmpmasterId']);			
			}
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
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
			$filterstr = array();
			if(!empty($_POST['datepref'])){
				if(!empty($_POST['start'])){
					$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
					$filter[] = "DATE_FORMAT(".$_POST['datepref'].",'".MYSQL_DATE_SEARCH."') >= '$start'";
					$filterstr[] = '<b>Date Selector : </b>'.$_POST['hid_datepref'];
					$filterstr[] = '<b>Start : </b>'.$_POST['start'];
				}
				if(!empty($_POST['end'])){
					$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
					$filter[] = "DATE_FORMAT(".$_POST['datepref'].",'".MYSQL_DATE_SEARCH."') <= '$end'";
					$filterstr[] = '<b>End : </b>'.$_POST['end'];
				}
			}//if(!empty($_POST['datepref'])){
			if(!empty($_POST['po_challan_no'])){
				$filter[] = "po_challan_no = '$_POST[po_challan_no]'";
				$filterstr[] = '<b>Po/Challan No. : </b>'.$_POST['po_challan_no'];
			}
			if(!empty($_POST['partyId'])){
				$filter[] = "partyId = '$_POST[partyId]'";
				//$filterstr[] = '<b>Customer : </b>'.$_POST['hid_partyId'];
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
/*$(function() {
   $(".custname").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-customer-name.php"
	});
});*/

//This function will print all the selected srf
function printallselected()
{
	var toprint = '';
	$('input[type="checkbox"][name="myval[]"]:checked').each(function(){
	 toprint += $(this).val()+'-';	
	});
	if(toprint != ''){
		$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1&mode=1&id='+toprint+'&actiontype=print-srf', iframe:true, width:'95%', height:'95%'});
		return true;
	}
	else	return false;	
}

function zip_all_certificate()
{
	var toprint = '';
	$('input[type="checkbox"][name="myval[]"]:checked').each(function(){
	 toprint += $(this).val()+'-';	
	});
	if(toprint != ''){
		var url_link = 'index.php?option=certificate-print-all&id='+toprint;
		window.location = url_link;
		//$.colorbox({href:url_link, iframe:true, width:'95%', height:'95%'});
		return true;
	}
	else	return false;	
}

function pdf_certificate(id)
{
	var toprint = id;
	if(toprint != ''){
		var url_link = 'index.php?option=certificate-print-all&id='+toprint+'&outformat=pdf';
		window.location = url_link;
		//$.colorbox({href:url_link, iframe:true, width:'95%', height:'95%'});
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
			case'srf-schedule':
				$filepath = BASE_URI.'modules'.SYM.'calibration'.SYM.'srf'.SYM.'srf-schedule-print.inc.php';
				if(is_file($filepath)) require_once($filepath);
				exit();
				break;	
			default:
				$filepath = BASE_URI.'modules'.SYM.'calibration'.SYM.'srf'.SYM.'srf-print.inc.php';
				if(is_file($filepath)) require_once($filepath);
				exit();
				break;
		}//switch($_GET['actiontype']){ ends
	  }
	  //This block of code will help in the print work ens
	  ?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr>
           <td>
             <!-- this table will contain our form filter code starts -->
			 <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	
               <input type="hidden" name="partyId" value="<?php echo $_SESSION[SESS.'id'];?>" />
             <table>
               <tr>
                <td><span class="star">*</span>Date Selector<br />
                    <?php arr_pulldown('datepref', array('po_challan_date'=>'PO/Challan Date'), '', true, true, 'class="hid_txt"'); ?>
                </td>
                <td><span class="star">*</span>Start<br />
                    
                    <input type="text" class="qdatepicker" name="start" value="<?php if(isset($_POST['start'])) echo $_POST['start']; else echo '01/'.date('m/Y'); ?>"/>
                </td>
                <td><span class="star">*</span>End<br />	
                    <input type="text" class="qdatepicker" name="end" value="<?php if(isset($_POST['end'])) echo $_POST['end']; else echo date('d/m/Y'); ?>"/>
                </td>
                <td>PO/Challan No<br />
                 <input type="text" name="po_challan_no" value="<?php if(isset($_POST['po_challan_no'])) echo $_POST['po_challan_no'];?>" />
				 <?php //db_pulldown($dbc, 'icId', "SELECT  icId, icname FROM  item_category ORDER BY icname ASC", true, true, 'class="hid_txt"');  ?> 
                </td>
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
                  
                  <input onclick="return printallselected();" type="button" value="Print ALL" title="Print selected <?php echo $formaction; ?>" />
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
                <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="../icon-system/i16X16/printo.png" /></a>
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
                      <td class="printhide" style="width:30px;"><input type="checkbox" id="myselect" onclick="selectCheckBoxes('myselect','myval[]');" /></td>
                      <td class="sno">S.No</td>                    
                      <!--<td>SRF Date</td>
                      <td>SRF No</td>-->
                      <td>Po/Challan</td>
                      <td>Po/Challan Date</td>
                      <td>Po/Challan No.</td>
                      <td>Total Equipments QTY</td>
                      <td class="options">Options</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
				  
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['srfId'];
					  $uidname = $rows['srfcode'];
					  
					  $printlink = '<a title="Print SRF '.$uidname.'" class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print-srf"><img src="../icon-system/i16X16/b_view.png"></a>';
					  $schedulelink = '<span class="seperator">|</span> <a class="iframef" title="View Schedule" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=srf-schedule"><img src="../icon-system/i16X16/osent.png"></a>';
					  $deletelink = '';//'<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'SRF Delete\', \''.$uid.'\',\'SRF\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
					  //if(false && $rows['locked'] == 1) $editlink = $deletelink = '';
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
					    <td class="printhide"><input type="checkbox" name="myval[]" value="'.$uid.'"/></td>
						<td>'.$inc.'</td>						
						<!--<td>'.$rows['srfdate'].'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>-->
						<td>'.$rows['challan_or_po_based_val'].'</td>
						<td>'.$rows['po_challan_date'].'</td>
						<td>'.$rows['po_challan_no'].'</td>
						<td>'.$rows['itemqty'].'</td>
						<td class="options">'.$printlink.$schedulelink.$deletelink.'</td>
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
	  <?php //if(isset($heid)) echo'<script type="text/javascript">getvaluetotal(); setsnosale("mysaletable")</script>'?>