<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'DSP CHALLAN LIST'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'dsp_challan'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.retailer_id DESC'; // The orderby clause for fetching of the data
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS.'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$userid = $_SESSION[SESS.'data']['id'];
$sesId = $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['urole'];
// here we get dealer id
$dealer_id = $myobj->get_dealer_id($sesId, $role_id);
//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_list($dealer_id);

?>
<div id="breadcumb"><a href="#">Sale Order</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php  //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/sale-order.php'); 
          ?>  
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
        return array(TRUE, '');
	if($mode == 'filter') return array(TRUE, '');
        if($mode == 'delete') return array(TRUE, '');
	$field_arry = array('firm_name' => $_POST['firm_name']);// checking for  duplicate Unit Name
	
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false, ""))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false," id != '$_GET[id]'"))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
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
                                $mytemp = $_POST;
				unset($_POST);
				$_POST = key_value_saver($mytemp, array('dealer_id', 'location_id', 'retailer_id','submit','working_id'));
                                
				//unset($_POST);
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

$rs1 = array();
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
                        if(!empty($_POST['from_date'])){
				$start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(`ch_date`,'".MYSQL_DATE_SEARCH."') >= '$start'";
				$filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
			}
			if(!empty($_POST['to_date'])){
				$end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(`ch_date`,'".MYSQL_DATE_SEARCH."') <= '$end'";
				$filterstr[] = '<b>End : </b>'.$_POST['to_date']; 
			}
                        if(!empty($_POST['challan_no'])){
				$filter[] = "ch_no = '$_POST[challan_no]'";
				$filterstr[] = '<b>Challan No  : </b>'.$_POST['ch_no'];
			}
                         if(!empty($_POST['retailer_id'])){
				$filter[] = "ch_retailer_id = '$_POST[retailer_id]'";
				$filterstr[] = '<b>Retailer Name  : </b>'.myrowval('retailer', 'name', "id='$_POST[retailer_id]'");
			}
                        $filter[] = "ch_dealer_id = $dealer_id AND company_id = '{$_SESSION[SESS.'data']['company_id']}'";
                     
                    $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');		    
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby=''); // $myobj->get_item_category_list()
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
}  else {

        $rs = $myobj->$funcname($filter = "ch_dealer_id = $dealer_id AND company_id = '{$_SESSION[SESS.'data']['company_id']}'",  $records = '', $orderby='');           //pre($rs);

}
dynamic_js_enhancement();
if(isset($_POST['catalog_1_id'])){
    $catalog_1_id=$_POST['catalog_1_id'];
}else{
    $catalog_1_id="";
}
?>
<script type="text/javascript">
$(function(){
	$(".order_id").autocomplete({
			source: "index.php?option=myajax-autocomplete&subauto=order-search&searchdomain=orderno",
			minLength: 1
		});
});

$(function() {
    $(".product").autocomplete({
       source: "./modules/ajax-autocomplete/user/ajax-product-name.php",
       select: function( event, ui ) {$('#productid').val(ui.item.id);}
    });
	
});

function checkuniquearray(name)
{
	var arr = document.getElementsByName('product[]');
	var len = arr.length;
	var v = checkForm('genform');
        if(v)
        {
            for (var i=0; i<len; i++)
            {                        // outer loop uses each item i at 0 through n
                    for (var j=i+1; j<len; j++)
                    {
                                          // inner loop only compares items j at i+1 to n
                            if (arr[i].value==arr[j].value)
                            {
                                    alert('Same Item cannot be selected multiple time;');
                                    return false;
                            }
                    }
            }
            return true;
        }
	return false;
}
function hideDiv(value)
{
    
    if(value == 'true') {
       document.getElementById('product').style.display='block';
       document.getElementById('gift').style.display='block';
       document.getElementById('gift1').style.display='block';
       document.getElementById('prod1').style.display='block';
    }
     if(value == 'false') {
       document.getElementById('product').style.display='none';
       document.getElementById('prod1').style.display='none';
       document.getElementById('gift').style.display='block';
       document.getElementById('gift1').style.display='block';
    }
}
function FormSubmit()
{
    var order = document.getElementsByName('order_id[]');
    var len = order.length;
    
    for(var i = 0; i < len; i++)
    {
        if(order[i].checked){
            document.getElementById('genform2').submit();
        }
    }
}
</script>
    <div id="workarea">
       <?php 
	  //This block of code will help in the print work
	  if(isset($_GET['actiontype'])){
		switch($_GET['actiontype']){
			case'print':
				require_once('challan-print.inc.php');
				exit();
				break;	
			default:
				$filepath = BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'challan'.SYM.'challan-print.inc.php';
				if(is_file($filepath)) require_once($filepath);
				exit();
				break;
		}//switch($_GET['actiontype']){ ends
	  }
	  //This block of code will help in the print work ens
	  ?>
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" id="loc_level" name="loc_level" value="<?php echo $loc_level; ?>">
        <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if(isset($dealer_id)) echo $dealer_id; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
              <td><span class="star">*</span>DSP/USER<br>
                <?php 
                  $user_data = $myobj->get_dsp_wise_user_data($sesId , $role_id,$dealer_id);
                  //pre($user_data);
                  if(!empty($user_data)) {
                      $user_data_str = implode(',' , $user_data);
                      //$q = "SELECT id, CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM dealer_person INNER JOIN person ON person.id = dealer_person.person_id WHERE person_id IN ($user_data_str) ORDER BY name ASC";
                      $q = "SELECT id, CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM  person WHERE id IN ($user_data_str) ORDER BY name ASC";
                       db_pulldown($dbc,'dspId',$q,true,true,'lang="dealer" id="dspId"'); 
                  } 
                  else {
                      echo '<select name="" lang="Create DSP first"><option>== No Any DSP Found ==</option></select>';
                  }
                ?>
              </td>
              <td><span class="star">*</span>Location<br>
                  <?php 
                     arr_pulldown('location_id', $location_list, $msg='', true, true,'lang="locations" onchange="fetch_location(this.value, \'progress_div\', \'retailer_id\', \'get-dealer-retailer\');"');
                  ?>
               
              </td>
               <td><span class="star">*</span>Retailer/Client Name<br>
             <?php if(!isset($heid)) { ?>
                   <select lang="retailer" name="retailer_id" id="retailer_id">
                      <option>==Please Select==</option>
                  </select>  
             <?php } else {
                 db_pulldown($dbc, 'retailer_id', "SELECT id, name FROM retailer  WHERE location_id = '$_POST[location_id]'",true, true);
             }  ?>
              </td>
              <td colspan="2">
                  Image<br>
                  <input type="file" name="image_name" value="">
              </td>  
         <input type="hidden" name="call_status" value="true">
         </tr>
         
         <tr>
             <td colspan="5"><div id="prod1"  class="subhead1">Product Details</div></td>
         </tr>
         
         <tr>
             <td colspan="5">
                <div id="product">
                  <table width="100%" id="mytable">
                     <tr class="thead" style="font-weight:bold;">
                         <td>S no</td>
                          <td>Product</td>
                          <td>Base Price</td>
                          <td>Quantity</td>
                          <td>Sch Quantity</td>
                          <td>Value</td>
                          <td style="width:40px;">&nbsp;</td>
                      </tr>
                   <?php if(!isset($heid)) { ?>
                     <tr class="tdata">
                          <td class="myintrow">1</td>
                          <td>
                           <?php db_pulldown($dbc , 'product[]', "SELECT id,name FROM catalog_product",TRUE,TRUE,'onchange=getajaxdata(\'get_product_details\',\'mytable\',event);'); ?></td>
                          <td><input   type="text" name="base_price[]" onblur="product_calculate();" value=""  /></td>
                          <td><input   type="text" name="quantity[]" onblur="product_calculate();" value=""  /></td>
                          <td><input   type="text" name="scheme[]"  value=""  /></td>
                          <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="" /></td>
                          <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                      </tr>
                    <?php } 
                    else {  
                       if(!empty($_POST['order_item'])) {
                           $inc = 1;
                           foreach($_POST['order_item'] as $inkey=>$invalue)
                           {
                               ?>
                               <tr class="tdata">
                                <td class="myintrow"><?php echo $inc;?></td>
                                <td>
                                  <?php db_pulldown($dbc, 'product[]', "SELECT id, name FROM  catalog_product", true, true, 'onchange=getajaxdata(\'get_product_details\',\'mytable\',event);', '', $invalue['product_id']);  ?> 
                                </td>
                                <td>
                                  <input type="text" onblur="product_calculate();" name="base_price[]"  value="<?php echo $invalue['rate']; ?>"  />
                                </td>
                                <td>
                                  <input type="text" name="quantity[]" onblur="product_calculate();"  value="<?php echo $invalue['quantity']; ?>"  />
                                </td>
                                  <td>
                                  <input type="text" name="scheme[]" value="<?php echo $invalue['scheme_qty']; ?>"  />
                                </td>
                                <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="<?php echo $invalue['rate'] * $invalue['quantity']; ?>" /></td>
                                <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/><?php if($inc != 1){?><img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php }?></td>
                            </tr>
                               <?php
                               $inc++;
                           }
                       }
                       
                    } ?>  
                  </table>
                 </div>
             </td>
          </tr>
          <tr>
              <td colspan="5"><div id="gift1" class="subhead1">Gift Details</div></td>
          </tr>
          <!-- form design for feeding gift details in UI PART -->
           <tr>
             <td colspan="5">
                  <div id="gift">
                  <table width="100%" id="mytable1">
                     <tr class="thead" style="font-weight:bold;">
                         <td>Sno</td>
                          <td>Gift Name</td>
                          <td>Quantity</td>
                          <td style="width:40px;">&nbsp;</td>
                      </tr>
                   <?php if(!isset($heid)) { ?>
                     <tr class="tdata">
                          <td class="myintrow">1</td>
                          <td><?php db_pulldown($dbc , 'gift_id[]', "SELECT id, gift_name FROM _retailer_mkt_gift",TRUE,TRUE,''); ?></td>
                          
                          <td><input   type="text" name="gift_qty[]"  value=""  /></td>
                         
                          <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable1', event);"/></td>
                      </tr>
                    <?php } 
                    else
                         { 
                           if(isset($_POST['gift_item']) && !empty($_POST['gift_item']))
                           {
                           $inc = 1;
                           foreach($_POST['gift_item'] as $inkey=>$invalue)
                           {
                               ?>
                               <tr class="tdata">
                                <td class="myintrow"><?php echo $inc;?></td>
                                <td>
                                  <?php db_pulldown($dbc, 'gift_id[]', "SELECT id, gift_name FROM  _retailer_mkt_gift", true, true, '', '', $invalue['gift_id']);  ?> 
                                </td>
                                <td>
                                  <input type="text" name="gift_qty[]" value="<?php echo $invalue['quantity']; ?>"  />
                                </td>
                               
                                <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable1', event);"/><?php if($inc != 1){?><img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php }?></td>
                            </tr>
                               <?php
                               $inc++;
                                }
                            }
                         }
?>  
                  </table>
               </div>
             </td>
          </tr>
           <!-- form design for feeding gift details in UI PART End here -->
          <tr>
              <td colspan="7">Remarks<br>
                 <textarea name="remarks"><?php if(isset($_POST['remarks'])) echo $_POST['remarks']; ?></textarea>
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
                 <td>From Date<br />
                    <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else  echo date('d/M/Y');?>" />
                </td>
                 <td>To Date<br />
                     <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/M/Y');?>" />
                </td>             
                <td>Challan No<br />
                    <input type="text" class="challan_no"  name="challan_no" class="order" value="<?php if(isset($_POST['challan_no'])) echo $_POST['challan_no'];?>" /> 
                </td>
                <td>Retailer<br>
                    <?php db_pulldown($dbc, 'retailer_id', "SELECT retailer.id, retailer.name FROM retailer INNER JOIN challan_order ON challan_order.ch_retailer_id = retailer.id ", true,true); ?>
                </td>
               
                 <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
<!--                  <input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
                </td>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
       </table>
     </form>
        <form method="post" action="index.php?option=dsp-wise-challan" class="iform" id="genform2" name="genform2" onsubmit="return checkForm('genform2');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
        
	<?php
	if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
    	  <tr>
            <td>
              <div class="subhead1"><!-- this portion indicate the print options -->
                <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
<!--                <a href="javascript:pdf('searchlistdiv');" title="save as pdf document" style="margin-right:10px;"><img src="./icons/pdf.png" /></a>-->
                
                <?php echo $forma; ?>
<!--                <input type="button" name="button" onclick="FormSubmit();" value="Make Invoice">-->
              </div>
            </td>
          </tr>	
          <tr>
            <td>            
              <?php
	    ########################## pagination details fetch starts here ###################################
              //pre($rs);
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
                      <td rowspan="2" class="sno">S.No</td>
                      <td style="width: 50px" rowspan="2">Challan<br> Date</td>
                      <td style="width: 50px" rowspan="2">Dispatch Date</td>
                      <td style="width: 50px" rowspan="2">Dealer Name</td>
                      <td style="width: 50px" rowspan="2">Retailer Name</td>
                      <td style="width: 50px" rowspan="2">Challan No</td>
                      <td align="center" colspan="7">Challan Details</td>
                      <td style="width: 50px"  rowspan="2" class="options">Options</td>
                    </tr>
                     <tr class="search1tr">

                         <td style="border:none #ffffff;width: 100px;text-align:center" >Product Name</td>
                          <td style="border:none #ffffff;width: 60px;text-align:center">Org. Qty</td>
                                <td style="border:none #ffffff;width: 60px;text-align:center">Quantity</td>
                                <td style="border:none #ffffff;width: 60px;text-align:center">Rate</td>
                                <td style="border:none #ffffff;width: 50px;text-align:center">Sch Qty</td>
                                <td style="border:none #ffffff;width: 100px;text-align:center"> Order No</td>
                                <td style="border:none #ffffff;width: 60px;text-align:center">Total Sale Value</td>
                               

                              </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['ch_no'];
                      $uidname = $rows['ch_no'];
                      $deletelink = '<a href="javascript:void(0);" onclick="do_delete(\'Challan Delete\', \''.$uid.'\',\'Challan Number\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                      $printlink = '<span class="seperator">|</span> <a class="iframef" title="print Challan No '.$uidname.'" href="index.php?option='.$formaction.'&showmode=1&id='.$uid.'&dealer_id='.$rows['ch_dealer_id'].'&company_id='.$rows['company_id'].'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>';
                      
                     $editlink = '<a class="iframef" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '"><img src="./images/b_edit.png"></a>';
                     $editlink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                       <td class="myintrow myresultrow">'.$inc.'</td>
                        <td>'.$rows['ch_date'].'</td>
                        <td><strong>'.$rows['dispatch_date'].'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
                        <td>'.$rows['dealer_name'].'</td>
                        <td>'.$rows['retailer_name'].'</td>
                        <td>'.$rows['ch_no'].'</td>
             
                        <td colspan="7">';
                        echo'
                        <table>';
                    //pre($rows['order_item']);   
                    $count_value = count($rows['order_item']);
                    
                    $total_sale_value = 0;
                    if(!empty($rows['challan_order_details']))
                     {
                        foreach($rows['challan_order_details'] as $inkey=>$invalue){
                            //$total_sale_value = $myobj->get_sale_value($invalue['catalog_1_id'],$invalue['metric_ton']);
                           $rs1 = $myobj->get_sale_order_details_list($filter=' order_id = '.$invalue[order_id].' AND product_id = '.$invalue[product_id].'', $records='', $orderby='');
                          foreach($rs1 as $val);
                           $total_sale_value=$invalue['product_rate']*$invalue['ch_qty'];
                        echo'
                        <tr>
                          <td style="border:none; width: 108px;text-align:center">'.$invalue['name'].'</td>
                          <td style="border:none;width: 68px;text-align:center;">'.$val['quantity'].'</td>
                          <td style="border:none;width: 68px;text-align:center;">'.$invalue['ch_qty'].'</td>
                          <td style="border:none;width:75px;text-align:center">'.$invalue['product_rate'].'</td>
                          <td style="border:none;width:58px;text-align:center">'.$invalue['free_qty'].'</td>
                          <td style="border:none;width:118px;text-align:center">'.$invalue['order_id'].'</td>
                          <td style="border:none;width:68px;text-align:center">'.my2digit($total_sale_value).'</td>  
                        </tr>';				
                             }
                     }
            
                 echo'</table>';
                     echo'</td>
                       <td class="options">'.$deletelink.$printlink.'</td>
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
