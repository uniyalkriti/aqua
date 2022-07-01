<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'MMD LIST'; // to indicate what type of form this is
$formaction = $p;
$myobj = new srf();
$cls_func_str = 'mmd'; //The name of the function in the class that will do the job
$myorderby = 'itemdesc ASC'; // The orderby clause for fetching of the data
$myfilter = 'srfId='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
<div id="breadcumb"><a href="#">Calibration</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php //require_once(BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'calibration'.SYM.'breadcum'.SYM.'calibration.php');  ?>
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
$mymatch['datepref'] = array('srfdate'=>'SRF Date', 'po_challan_date'=>'PO/Challan Date', 'created'=>'Created');
$curyear = date('Y');
$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
foreach($shortmonth as $key=>$value)
	$shortmonth[$key] = $curyear.str_pad($value, 2, 0, STR_PAD_LEFT);
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
			if(!empty($_POST['srfcode'])){
				$filter[] = "srfcode = '$_POST[srfcode]'";
				$filterstr[] = '<b>SRF No : </b>'.$_POST['srfcode'];
			}
			if(!empty($_POST['partyId'])){
				$filter[] = "partyId = '$_POST[partyId]'";
				$filterstr[] = '<b>Customer : </b>'.myrowval('party', 'partyname', "partyId = ".$_POST['partyId']);
			}
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>'); 
			$funcname = 'get_mmd_new_list';
			//$funcname = 'get_'.$cls_func_str.'_list'; 
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
$(".custname").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-customer-name.php"
		});
$("#salecode, #barsalecode").autocomplete({
			source: "./modules/ajax-autocomplete/ajax-salecode.php"
		});
});
//This function is called when the user blurs the sale code box to fetch the item matching the sale code
// if the item is already in the tr then the focus will be given to the qty box of the TR
function salehandler(tableId, salecode, myhandlermode)
{
	//alert('salehandler called');
	var mtarget = document.getElementById(salecode);
	if(mtarget.value == '') return;
	salehandler_ajax(mtarget, progress_div = 'hd_salecode', wcase='item-stock-receive', tableId, myhandlermode);
}
// This function is called from salehandler() func above to perform the ajax request
function salehandler_ajax(mtarget, progress_div, wcase, tableId, myhandlermode)
{
	if(progress_div == '') progress_div = 'hd_salecode';
	if(wcase == '') wcase='item-stock-receive';
	if(tableId == '') tableId='mysaletable';
	//alert('salehandler_ajax called');
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_mobile/ajax_mobile_php.php?pid='+ encodeURIComponent(mtarget.value)+'&wcase='+wcase);
		
		//Function that handles the response
		ajax.onreadystatechange =function () { 
                                    resp_salehandler_ajax(progress_div, tableId, myhandlermode);
                                    }
		//send the request
		ajax.send(null);
	}
	return;
}
// This function is called from salehandler_ajax() func above to handle the ajax response
function resp_salehandler_ajax(progress_div, tableId, myhandlermode)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{	
			var mtable = document.getElementById(tableId);
			var trows = mtable.rows.length;	
			var itemid = document.getElementsByName('itemId[]');
			//the item details
			var ajaxitemid = datafetch[1];
			var itemname = datafetch[2];
			var leastcount = datafetch[3];
			var range_size = datafetch[4];
			/*if(itemid)
			{
				//looping through each of the added tr to check if this item has been already entered or not
				for(var i = 0; i<itemid.length; i++)
				{
					if(itemid[i].value == ajaxitemid)
					{
						var mtarget = itemid[i];
						var currentRow = mtarget.parentNode.parentNode;
						currentRow.cells[6].childNodes[0].focus();
						if(myhandlermode == 'barcode') currentRow.cells[6].childNodes[0].focus();
						
						document.getElementById(progress_div).style.display = 'none';
						setsnosale(tableId);
						//if user is entering the data via the barcode mode
						if(myhandlermode == 'barcode')
						{
							document.getElementById('barsalecode').value = '';
							document.getElementById('barsalecode').focus();
							var qty = document.getElementsByName('qty[]');
							qty[i].value = qty[i].value*1 + 1*1;
							getvaluetotal();
						}
						return;
					}
				}
			}*/
			  
			var htmlcode = '';
			htmlcode += '<td>&nbsp;</td>';
			htmlcode += '<td><input type="hidden" name="srfItemId[]" value="NULL"/> <input type="hidden" name="rifId[]" value=""/> <input type="hidden" name="tmpmasterId[]" value=""> <input type="hidden" name="itemId[]" value="'+ajaxitemid+'"> <input type="hidden" name="itemdesc[]" value="'+itemname+'">'+itemname+'</td>';
			htmlcode += '<td><input type="text" name="lab_code[]" value="" class="readonly" readonly="readonly"></td>';
			htmlcode += '<td><input type="text" name="equipno[]" value="" /></td>';
			htmlcode += '<td><input type="text" name="make[]" value="" /></td>';
			htmlcode += '<td><input type="text" name="model[]" value="" /></td>';
			htmlcode += '<td><input type="text" name="serial_no[]" value="" /></td>';
			htmlcode += '<td> <select name="cal_step_type[]"><option value="1">Our Standard</option><option value="2">Customer Specific</option></select>  <input type="text" name="cal_step_detail[]" value="" /></td>';
			htmlcode += '<td><input type="text" name="range_size[]" value="'+range_size+'" /></td>';
			htmlcode += '<td><input type="text" name="least_count[]" value="'+leastcount+'" /></td>';
			htmlcode += '<td> <select name="calibration_frequency[]"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select></td>';
			htmlcode += '<td><img title="less" src="images/less.png" onclick="javascript:doless(\'mysaletable\', event);"></td>';
			<?php if(!isset($heid)){ // the new rows are inserted from the top onwards?>
			var newRow = mtable.insertRow(1); // insert new row
			<?php }else{ //if(!issest($heid)){ // but during edit the new row to be appended to the last row?>
			var existrowcount = $('tr.datarow').size();
			var newRow = mtable.insertRow(existrowcount+1); // insert new row
			<?php } // else part ends here?>
			newRow.innerHTML = htmlcode;
			newRow.setAttribute("class", "datarow"); //to help us in the numbering
			setsnosale(tableId);
			newRow.cells[3].childNodes[0].focus();
			document.getElementById(progress_div).style.display = 'none';
			//if user is entering the data via the barcode mode
			if(myhandlermode == 'barcode')
			{
				document.getElementById('barsalecode').value = '';
				document.getElementById('barsalecode').focus();
				/*var qty = document.getElementsByName('qty[]');
				qty[0].value = 1;*/
				getvaluetotal();
				return;
			}
		}
		else
		{
			//alert(datafetch[1]);
			document.getElementById(progress_div).innerHTML = '<span style="color:red;">'+datafetch[1]+'</span>';
			//document.getElementById(progress_div).style.display = 'none';
			//document.getElementById('salecode').select();
			//if we are using barcode mode
			if(myhandlermode == 'barcode')
				document.getElementById('barsalecode').select();
		}
	}
	else
	{
		document.getElementById(progress_div).style.display = 'inline';
		document.getElementById(progress_div).innerHTML = '<img src="images/loader.gif" />fetching data ...';
	}
}
// This will set the numbers of S.No 
function setsnosale(tableId)
{
	var mtable = document.getElementById(tableId);
	for(var i = 1, z=1; i<mtable.rows.length; i++)
	{
		if((' ' + mtable.rows[i].className + ' ').indexOf(' ' + 'datarow' + ' ')	> -1)
		{
			mtable.rows[i].cells[0].innerHTML = z;
			//alert(i);
			if(z%2 == 1)
			mtable.rows[i].style.background = '#EEE';
			else
			mtable.rows[i].style.background = '#FFF';
			z += 1;
		}
		
	}
	
}
// This function will remove a TR when clicked on the minus image
function doless(tableId, event)
{
	var mtable = document.getElementById(tableId);
	var trows = mtable.rows.length;
	if(typeof event.target != 'undefined') // for firefox and other browsers
		var mtarget = event.target;
	else if(event.srcElement) // indicating it is Internet Explorer family
		var mtarget = event.srcElement;
	else 
		return;
	var currentRow = mtarget.parentNode.parentNode;
	mtable.deleteRow(currentRow.rowIndex); // delete current row
	getvaluetotal();
	setsnosale(tableId);
}
// This function will do the actual totaling
function getvaluetotal()
{
	var itemId = document.getElementsByName('itemId[]');
	document.getElementById('totqty').value = itemId.length;
	hide_when_zero();	
}
// This function help us control the movement on the enter keypress, if user is on sale code txtbox he will be move the QTY box after fetching
// the data corresponding to sale code and if user is on QTY txtbox, he will be moved back to the sale code box.
function getkeypress(event, wcase)
{
	var charCode = (event.which) ? event.which : event.keyCode;
	if(charCode == 13)
	{
		if(wcase == 'salecode')
		{
			event.preventDefault();
			salehandler('mysaletable', 'salecode', 'normal');
		}
		if(wcase == 'qty')
		{
			event.preventDefault();
			document.getElementById('salecode').select();
		}
		if(wcase == 'barsalecode')
		{
			event.preventDefault();
			salehandler('mysaletable', 'barsalecode', 'barcode');
		}
	}
	return true;
}

(function( $ ) {
  $.fn.myhidden_el = function() {
	return this.each(function() {
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
  };
}( jQuery ));

//This will show or hide the actual sale amount box on page load
$(function(){
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
	
	hide_when_zero();
});

function mycheckForm_alert(frmname)
{
	if(!checkForm_alert(frmname)) return false;
	var itemId = document.getElementsByName('itemId[]');
	if(itemId.length == 0){
		alert('Please enter atleast 1 item');
		document.getElementById('barsalecode').focus();
		return false;
	}
	if(document.getElementById('totqty').value*1 == 0){
		alert('Item received quantity cannot be zero');
		document.getElementById('barsalecode').focus();
		return false;
	}
	return true;
}
//To hide the grand total and other summary row when no item row exists
function hide_when_zero()
{
	var itemId = document.getElementsByName('srfItemId[]');
	if(itemId.length == 0)
		$('.tfoot, .headrow').hide();
	else
		$('.tfoot, .headrow').show();	
	
}

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
		var url_link = 'index.php?option=certificate-print-pdf&id='+toprint+'&outformat=pdf';
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
			case'print-history-card':
				require_once('data/history-card-items.inc.php');
				exit();
				break;	
			default:
				$filepath = BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'calibration'.SYM.'srf'.SYM.'srf-print.inc.php';
				if(is_file($filepath)) require_once($filepath);
				exit();
				break;
		}//switch($_GET['actiontype']){ ends
	  }
	  //This block of code will help in the print work ens
	  ?>
	  <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
      <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return mycheckForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>        
        <table  width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
          <!--<tr>
            <td colspan="5"><div class="subhead1">Bill Details</div></td>
          </tr>-->
          <tr>
            <td><span class="star">*</span>SRF Date<br />
              <input type="text" class="qdatepicker"  name="srfdate" value="<?php if(isset($_POST['srfdate'])) echo $_POST['srfdate']; ?>" lang="SRF Date"  />    
              <input type="hidden" name="osrfdate" value="<?php if(isset($_POST['srfdate'])) echo $_POST['srfdate']; ?>" />
              <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	              
            </td>
             <td><span class="star">*</span>SRF No.<br />
              <input type="text" name="srfcode" value="<?php if(isset($_POST['srfcode'])) echo $_POST['srfcode']; ?>" class="readonly" readonly="readonly" />   
              <input type="hidden" name="srfno" value="<?php if(isset($_POST['srfno'])) echo $_POST['srfno']; ?>" />   
                                
            </td>
            <td><span class="star">*</span> Customer <?php //add_refresher('index.php?option=vendor&showmode=1');?><br />
              <?php db_pulldown($dbc, 'partyId', "SELECT  partyId, partyname FROM  party ORDER BY partyname ASC", true, true, 'id="vendorId"');  ?> 
            </td> 
            <td><span class="star">*</span>PO/Challan<br />
              <?php arr_pulldown('challan_or_po_based', $GLOBALS['challan_or_po_based'], '', true, true, 'lang="PO/Challan"'); ?>
            </td>
            <td><span class="star">*</span>PO/Challan No.<br />
              <input type="text" name="po_challan_no" value="<?php if(isset($_POST['po_challan_no'])) echo $_POST['po_challan_no']; ?>"  lang="PO/Challan No." />                
            </td>
            <td><span class="star">*</span>PO/Challan Date<br />
              <input type="text" class="qdatepicker" name="po_challan_date" value="<?php if(isset($_POST['po_challan_date'])) echo $_POST['po_challan_date']; ?>" lang="PO/Challan Date"  />                
            </td>
          </tr>
          <tr>
            <td colspan="6">Remark<br />
              <input type="text" name="remark" value="<?php if(isset($_POST['remark'])) echo $_POST['remark']; ?>" />                
            </td>
          </tr>
          <tr>
            <td colspan="6"><div class="subhead1">Details of Equipments</div></td>
          </tr>
          <tr>
            <td colspan="6" align="center" >
            <strong>ITEM :</strong> 
            <input type="text" id="barsalecode" name="barsalecode" value="" style="width:450px;"  onkeypress="getkeypress(event, 'barsalecode');" /><span style="margin-left:5px;"><img style="margin-top:opx" title="Add New Item" onclick="$.colorbox({href:'index.php?option=item&showmode=1', iframe:true, width:'95%', height:'95%'});" src="../icon-system/i16X16/more.png"  /></span><div id="hd_salecode" style="margin-bottom:5px; margin-left:10px;">&nbsp;</div>
       		<!-- table to hold the sale detail starts here -->
            <div id="imageContainer1">
            <div style="max-height:280px; min-height:100px; overflow:auto;">
            <table border="0" width="100%" cellpadding="1" cellspacing="1" class="tableform" id="mysaletable" style="margin-top:5px;">
              <tr style="font-weight:bold;" class="headrow" >
                <td class="sno">S.no</td>
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
                <td class="options">&nbsp;</td>
              </tr>
              <?php 
			  //$preserve = array();
			  //$preserve[1] = array('itemdesc'=>'Vernier Caliper', 'lab_code'=>'', 'equipno'=>'VC-01', 'make'=>'Yamaki', 'model'=>'xyz', 'serial_no'=>'MM-01', 'range_size'=>'0-25MM', 'least_count'=>'0.01mm', 'cal_step_type'=>'1', 'cal_step_detail'=>'', 'calibration_frequency'=>'1');
			  // if user is editing data
			  if(isset($heid)) {$i = 1; foreach($preserve as $key=>$value) { ?>
              <tr  class="datarow" >
                <td><?php echo $i;?></td>
                <td><input type="hidden" name="srfItemId[]" value="<?php echo $key;?>">
                  <input type="hidden" name="tmpmasterId[]" value="<?php echo $value['tmpmasterId'];?>">
                  <input type="hidden" name="rifId[]" value="<?php echo $value['rifId'];?>">
                  <input type="hidden" name="itemId[]" value="<?php echo $value['itemId'];?>">
                  <input type="hidden" name="itemdesc[]" value="<?php echo $value['itemdesc'];?>">
				  <?php echo $value['itemdesc'];?>
                </td>
                <td><input type="text" name="lab_code[]" value="<?php echo $value['lab_code'];?>" class="readonly" readonly="readonly"></td>
                <td><input type="text" name="equipno[]" value="<?php echo $value['equipno'];?>" /></td>
                <td><input type="text" name="make[]" value="<?php echo $value['make'];?>" /></td>
                <td><input type="text" name="model[]" value="<?php echo $value['model'];?>" /></td>
                <td><input type="text" name="serial_no[]" value="<?php echo $value['serial_no'];?>" /></td>
                <td>
                  <?php arr_pulldown('cal_step_type[]', array(1=>'Our Standard', 2=>'Customer Specific'),'', true,false);?>
                  <input type="text" name="cal_step_detail[]" value="<?php echo $value['cal_step_detail'];?>" />
                </td>
                <td><input type="text" name="range_size[]" value="<?php echo $value['range_size'];?>" /></td>
                <td><input type="text" name="least_count[]" value="<?php echo $value['least_count'];?>" /></td>
                <td>
                  <?php arr_pulldown('calibration_frequency[]', range(0,12),'', false,false);?>
                </td>
                
                <td><?php /*if($value['soldunit'] > 0)echo 'sold : '.$value['soldunit'].' Pcs'; else*/{?><img  title="less" src="images/less.png" onclick="javascript:doless('mysaletable', event);" /> <?php }?></td>
              </tr> 
			  <?php $i++;}
			  } //if(isset($heid)) ends ?>
              <tr class="tfoot">
                <td colspan="10" align="right" ><strong>Total Items</strong></td>
                <td><input class="readonly" type="text" id="totqty" name="totqty" readonly="readonly"  /></td>
                <td>&nbsp;</td>
              </tr>
            </table>
            </div>
            </div><!-- #imagecontainer ends -->
            <!-- table to hold the sale detail ends here -->
            </td> 
            </tr>
          <tr>
            <td align="center" colspan="6">
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo 'Update';else echo'Save';?>" />
            <?php if(isset($heid)) echo $heid;?>
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            </td>
          </tr>
          <tr>
            <td colspan="6"><span class="star" style="font-weight:bold;">Please Note</span><br />
              <span class="example"><strong>1. SRF No.</strong> will be automatically assigned by the system</span><Br />
              <span class="example"><strong>2. DMT/LAB code</strong> will be automatically assigned by the system</span>
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
			 <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table width="100%">
               <tr>
                <!--<td><span class="star">*</span>Date Selector<br />
                    <?php echo arr_pulldown('datepref', $mymatch['datepref'], '', true, true, '', false, ' '); ?>
                </td>
                <td><span class="star">*</span>Start<br />
                    	
                    <input type="text" class="qdatepicker" name="start" value="<?php if(isset($_POST['start'])) echo $_POST['start']; else echo '01/'.date('m/Y'); ?>"/>
                </td>
                <td><span class="star">*</span>End<br />	
                    <input type="text" class="qdatepicker" name="end" value="<?php if(isset($_POST['end'])) echo $_POST['end']; else echo date('d/m/Y'); ?>"/>
                </td>
                <td>Srf No<br />
                 <input type="text" name="srfcode" value="<?php if(isset($_POST['srfcode'])) echo $_POST['srfcode'];?>" />
                 
				 <?php //db_pulldown($dbc, 'icId', "SELECT  icId, icname FROM  item_category ORDER BY icname ASC", true, true, 'class="hid_txt"');  ?> 
                </td>-->
                <input type="hidden" name="year" value="<?php echo date('Y');?>"  />
                <input type="hidden" name="partyId" value="<?php echo $_SESSION[SESS.'id']; ?>"  />
               <!-- <td><span class="star">*</span>Customer<br />
				 <?php db_pulldown($dbc, 'partyId', "SELECT  partyId, partyname FROM  party WHERE partyId IN (SELECT DISTINCT partyId FROM srf) ORDER BY partyname ASC", true, true, 'lang="Please Select Customer"');  ?> 
                </td>-->
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <!--<input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
                  
                  <input onclick="return printallselected();" type="button" value="Print ALL" title="Print selected <?php echo $formaction; ?>" />-->
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
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <!--<td class="printhide"><input type="checkbox" id="myselect" onclick="selectCheckBoxes('myselect','myval[]');" /></td>-->
                      <td class="sno">S.No</td>                    
                      <td>Instrument Name</td>
                      <td>INST. Code</td>
                      <td>Customer</td>
                      <td>Range/Size</td>
                      <td>Least Count</td>
                      <td>Cali Frequency</td>
                      <?php					  
					  foreach($shortmonth as $key9=>$value9)
					  	echo '<td>'.$key9.' '.$curyear.'</td>';
					  ?>
                      <td class="options">Options</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
				  
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $key;//$rows['srfId'];
					  $uidname = $rows['equipno'];
					  $editlink = '';//'<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
					  $printlink = '<a title="History Card of  '.$uidname.'" class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&partyId='.$rows['partyId'].'&equipno='.$uidname.'&actiontype=print-history-card"><img src="../icon-system/i16X16/outcome.png"></a>';
					  $deletelink = '';//'<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'SRF Delete\', \''.$uid.'\',\'SRF\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
					  //if(false && $rows['locked'] == 1) $editlink = $deletelink = '';
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
					    <!--<td class="printhide"><input type="checkbox" name="myval[]" value="'.$uid.'"/></td>-->
						<td>'.$inc.'</td>						
						<td>'.$rows['itemdesc'].'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
						<td>'.$rows['partyId_val'].'</td>
						<td>'.$rows['range_size'].'</td>
						<td>'.$rows['least_count'].'</td>
						<td>'.$rows['calibration_frequency'].'</td>';
						//Showing the indication
						$custome = $icon = array();
						foreach($rows['view'] as $key1=>$value1) {
							$custome[$key1] = $key1;
							$icon[$key1] = $value1['icon'];
						}
						//pre($custome);
						//pre($icon);
						foreach($shortmonth as $key9=>$value9){
							if(!empty($custome[$value9])) {
								echo '<td>'.$icon[$value9].'</td>';
								//if($custome[$value9] ==$value9) echo 'hii';
							} else {
							//foreach($rows['view'] as $key=>)
						  // $value9 = $value9 == $rows['cal_due_datef'] ? '<img title="'.$rows['cal_due_date'].'" src="../icon-system/i16X16/yes.png"> ' : '&nbsp;';	
					  			echo '<td>&nbsp;</td>';
							}
						}echo'
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