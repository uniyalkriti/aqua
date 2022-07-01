<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$forma = 'Payment'; // to indicate what type of form this is
$formaction = $p;
$myobj = new admin_payment();
$dealer = new dealer();
$cls_func_str = 'admin_payment'; //The name of the function in the class that will do the job
$myorderby = 'payment_enrollment.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'payment_enrollment.id ='; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
$csess = $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['role_id'];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = PGDISPLAY; //10
$startpoint = ( $page * $limit ) - $limit ;
?>
<div id="breadcumb"><a href="#">Primary Sales Order</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
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
        $pay_date = get_mysql_date($_POST['pay_date']);
        
	$field_arry = array('pay_date' => $pay_date);// checking for  duplicate Unit Name      
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'payment_enrollment', false, " dealer_id='$_POST[dealer_id]' AND retailer_id = '$_POST[retailer_id]' AND location_id = '$_POST[location_id]'"))
                return array(FALSE, '<b>Payment</b> already done, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'payment_enrollment', false," id != '$_GET[id]' AND dealer_id='$_POST[dealer_id]' AND retailer_id = '$_POST[retailer_id]' AND location_id = '$_POST[location_id]'"))
                return array(FALSE, '<b>Payment</b> already done, please provide a different value.');
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
				show_row_changer_dynamic(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
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
				show_row_changer_dynamic(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
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
		$funcname = 'get_admin_payment_list';
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
//$cls_func_str = '';
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
                        $pagination_filter = '';
			if(!empty($_POST['start_date'])){
				$start = get_mysql_date($_POST['start_date'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(pay_date,'".MYSQL_DATE_SEARCH."') >= '$start'";
                               
				$filterstr[] = '<b>Start : </b>'.$_POST['start_date'];
			}
			if(!empty($_POST['end_date'])){
				$end = get_mysql_date($_POST['end_date'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(pay_date,'".MYSQL_DATE_SEARCH."') <= '$end'";
				$filterstr[] = '<b>End : </b>'.$_POST['end_date'];
			}
		       $data = $dealer->get_user_wise_dealer_data($csess , $role_id);
                       if(!empty($data)) {
                           $data_str = implode(',' , $data);
                           $filter[] = "dealer_id IN ($data_str)";
                       }
                       $pagination_filter = ' WHERE '.implode(' AND ', $filter);
                       $paginationop =  pagination('payment_enrollment',$limit, $page ,'index.php?option=payment-enrollment&activefilter=1&', $pagination_filter);
                       $_SESSION['pagefilter'] = $filter;
		       $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');		
                       $rs = $myobj->$funcname($filter,  $records = "$startpoint, $limit", $orderby ="ORDER BY $myorderby"); 
			if(empty($rs))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif(isset($_GET['activefilter']) && $_GET['activefilter'] == 1){
        $filter = $_SESSION['pagefilter'];
        $pagination_filter = ' WHERE '.implode(' AND ', $filter);
        $paginationop =  pagination('payment_enrollment',$limit, $page ,'index.php?option=payment-enrollment&activefilter=1&', $pagination_filter);
	$rs = $myobj->$funcname($filter,  $records = "$startpoint, $limit", $orderby=" ORDER BY $myorderby");
}elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])){
	$ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
	$rs = $myobj->$funcname($filter="$myfilter'$ajaxshowid'",  $records = '', $orderby='');
}
else {
        $paginationop =  pagination('payment_enrollment',$limit, $page ,'index.php?option=payment-enrollment&', $pagination_filter='');
        $rs = $myobj->$funcname($filter="",  $records = "$startpoint, $limit", $orderby='');
}

dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function() {  
	$(".dealer").autocomplete({
		source: "./modules/ajax-autocomplete/user/ajax-dealer-name.php"
	});
	
});

function show_payment(value)
{
   
    if(value == 'By Cash')
    {
        document.getElementById('amount').style.display = 'block';
        document.getElementById('bank_branch').style.display = 'none';
        document.getElementById('cheque_number').style.display = 'none';
        document.getElementById('cheque_date').style.display = 'none';
    }
    if(value == 'By Cheque')
    {
        document.getElementById('amount').style.display = 'block';
        document.getElementById('bank_branch').style.display = 'block';
        document.getElementById('cheque_number').style.display = 'block';
        document.getElementById('cheque_date').style.display = 'block';
    }
}

</script>

    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed 
         ?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="ses_id" value="<?php echo $csess;?>" />
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
             <td><span class="star">*</span><strong>Date</strong><br>
                  <input  class="qdatepicker"  type="text"  lang="Date" name="pay_date" value="<?php if(isset($_POST['pay_date'])) echo $_POST['pay_date']; else echo date('d/m/Y'); ?>">
              </td>
          
             <td><span class="star">*</span>
                 <strong>Dealer(C and F)</strong><Br/>
              <?php
                 $data = $dealer->get_user_wise_dealer_data($csess , $role_id);
                
                 $filterbucket = '';
                 if(!empty($data))
                 {
                      $data_str = implode(',',$data);
                      $filterbucket = " WHERE dealer_id IN ($data_str) ";
                 }
                
                 db_pulldown($dbc,'dealer_id',$q="SELECT d.id,d.name AS name FROM user_dealer_retailer udr INNER JOIN dealer d ON d.id = udr.dealer_id $filterbucket ",true,true,'lang="dealer" onchange="fetch_location(this.value, \'progress_div\', \'beat\', \'get-dealer-wise-location\');"'); 
                //echo $q;
                 ?>
            </td>
          
             <td>
                 <span class="star">*</span><strong>Location</strong><Br/>
              <?php if(!isset($heid)) { ?>
                 <select lang="Location" onchange="fetch_location(this.value, 'progress_div', 'retailer_id', 'get-retailer');" name="location_id" id="beat" ><option>==please select dealer==</option></select>
              <?php 
              }
               else 
               {
                   $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
                   $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
                   $loop = $dealer_level + 1;
                   $q = "SELECT location_$retailer_level.id, location_$retailer_level.name FROM dealer_location_rate_list  INNER JOIN location_$dealer_level ON location_$dealer_level.id = dealer_location_rate_list.location_id";
                   for($i = $loop; $i <= $retailer_level;$i++)
                   {
                       $j = $i - 1;
                       $q .= " INNER JOIN location_$i ON location_$i.location_".$j."_id = location_$j.id";
                   }
                   $q .= " WHERE dealer_id = '$_POST[dealer_id]'";
                   //h1($q);
                   db_pulldown($dbc,'location_id',$q,true,true,'lang="location" id="beat"');
               }
               ?>
             </td>
         </tr>
         <tr>
             <td>
                 <strong><span class="star">*</span>Retailer</strong><Br/>
                 <Select name="retailer_id" id="retailer_id" lang="Retailer">
                     <option>==Please Select==</option>
                 <?php if(isset($heid)) {
                        $retailer = new retailer();
                        $retailer_data = $retailer->get_user_wise_retailer_data($csess , $role_id);
                       
                        if(!empty($retailer_data)){
                            $retailer_data_str = implode(',' ,$retailer_data);
                            $q = "SELECT id,name FROM retailer WHERE retailer.location_id = '$_POST[location_id]' AND id IN ($retailer_data_str)";
                        }
                   echo  option_builder($dbc, $q, $selected =$_POST['retailer_id']);
                     ?>
                     
                 <?php } ?>
                 </select>
                 <?php //echo $q; ?>
             </td>
            <?php //pre($rs); ?>
              <td><strong>Payment Mode</strong><br/>
                  <input <?php if(isset($_POST['pay_mode']) && $_POST['pay_mode'] == 'By Cash') echo 'checked="checked"'; ?> onclick="show_payment(this.value);" type="radio" name="pay_mode" value="By Cash">&nbsp;By Cash
                  <input <?php if(isset($_POST['pay_mode']) && $_POST['pay_mode'] == 'By Cheque') echo 'checked="checked"'; ?> onclick="show_payment(this.value);" type="radio" name="pay_mode" value="By Cheque">&nbsp;By Cheque
              </td>
          </tr>
          <tr>
              <td colspan="3"><div class="subhead1">Payment details</div></td>
          </tr>
          <tr>
              <td>
                  <div id="amount" style="display: <?php if(isset($heid)) echo 'block'; else echo 'none;';  ?>;">
                  <strong>Amount</strong><br>
                  <input type="text" name="amount" value="<?php if(isset($_POST['amount'])) echo $_POST['amount']; ?>"
              </td>
                  <td>
                      <div id="bank_branch" style="display: <?php if(isset($heid) && $_POST['pay_mode'] == 'By Cheque') echo 'block'; else echo 'none;';  ?>">
                          <strong>Bank Branch</strong><br>
                  <input type="text" name="bank_name" value="<?php if(isset($_POST['bank_name'])) echo $_POST['bank_name']; ?>">
                      </div>
              </td>
              <td>
                   <div id="cheque_number" style="display: <?php if(isset($heid) && $_POST['pay_mode'] == 'By Cheque') echo 'block'; else echo 'none;';  ?>">
                    <strong>Cheque Number</strong><br>
                   <input type="text" name="cheque_number" value="<?php if(isset($_POST['cheque_number'])) echo $_POST['cheque_number']; ?>">
                   </div>
              </td>
          </tr>
          <tr>
              <td>
                   <div id="cheque_date" style="display:<?php if(isset($heid) && $_POST['pay_mode'] == 'By Cheque') echo 'block'; else echo 'none;';  ?>">
                 <strong>Cheque date</strong><br>
                  <input type="text" class="datepicker" name="cheque_date" value="<?php if(isset($_POST['cheque_date'])) echo $_POST['cheque_date']; ?>">
                  </div>
              </td>
          </tr>
         <tr>
           <td align="center" colspan="4">
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
                 <td>Start Date<br />
                 <input type="text" class="qdatepicker" name="start_date" value="<?php if(isset($_POST['start_date'])) echo $_POST['start_date']; else echo date('d/m/Y');?>" /> 
                </td>
                <td>End Date<br />
                 <input type="text" class="qdatepicker" name="end_date"  value="<?php if(isset($_POST['end_date'])) echo $_POST['end_date']; else echo date('d/m/Y');?>" /> 
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
              $inc = 1+($page-1) * $limit;
              $lastinc = (($inc + $limit-1) > $paginationop['totrecords']) ? $paginationop['totrecords'] : ($inc + $limit-1);
             
             ?>	 
             
              <div class="searchlistdiv" id="searchlistdiv"> 
                  <div class="myprintheader"><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $paginationop['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> out of <strong><?php echo $paginationop['totrecords']; ?></strong>)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Date</td>
                      <td>Dealer Name</td>
                      <td>Retailer Name</td>
                      <td>Created by</td>
                      <td>Location</td>
                      <td>Payment Mode</td>
                      <td>Amount</td>
                      <td class="options">Options</td>
                    </tr>
                     
                  <?php 
                  $bg = TR_ROW_COLOR1;              
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['dealer_name'];		  
		      $editlink = '<a onclick="setinc('.$uid.');" class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                     
		    // $deletelink = '<span class="seperator">|</span><a title=Assign Rate According To location class="iframef" href="indexpop.php?option=assign-rate&mode=1&id='.$uid.'"><img src="./images/rupee.png"></a>';
                     $deletelink = '';
                   
                       //if($rows['locked'] == 1) $editlink = $deletelink = '';
		     if($auth['del_opt'] !=1) $deletelink = '';
                     
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow"><span id="ss'.$uid.'">'.$inc.'</span></td>
                        <td>'.$rows['pay_date'].'</td>
                        <td>'.$rows['dealer_name'].'<div style="display:none" id="delDiv'.$uid.'"></div></td>                     <td>'.$rows['retailer_name'].'</td>
                        <td>'.$rows['user'].'</td>
                        <td>'.$rows['location_name'].'</td>
                        <td>'.$rows['pay_mode'].'</td>
                        <td>'.$rows['amount'].'</td>
                        <td class="options printhide">'.$editlink.'</td>
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here
                    if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row
                  ?>
                </table>                
            </div>    
            </td>
          </tr>
          
          <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>
           
        </table>
         <?php echo $paginationop['pagination_link']; ?>          
                
      </fieldset>
      </form>
      <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('name');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>
      <script>
          function setinc(id)
           {
             document.getElementById('totCount').value = id;
           }
     </script>