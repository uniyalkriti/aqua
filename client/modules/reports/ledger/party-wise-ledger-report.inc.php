<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Party Wise Ledger'; // to indicate what type of form this is
$formaction = $p;
$myobj = new report();
$cls_func_str = ''; //The name of the function in the class that will do the job
$myorderby = ''; // The orderby clause for fetching of the data
$myfilter = ' = '; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
      <div id="breadcumb"><a href="#">Report</a> &raquo; <a href="#">Billing</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/billing.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	/*$field_arry = array('partyname' => $_POST['partyname']);// checking for  duplicate Unit Name
	if($mode == 'add')
	{
		if(uniqcheck_msg($dbc,$field_arry,'party', false, " ptype=1"))
			return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
	}
	elseif($mode == 'edit')
	{
		if(uniqcheck_msg($dbc,$field_arry,'party', false," partyId != '$_GET[id]' AND ptype = 1"))
			return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
	}*/
	return array(TRUE, '');
}


############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_party_wise_ledger_report_list';
$mymatch['datepref'] = array('invdate'=>'Invoice Date', 'created'=>'Created');
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
                       
//			if(!empty($_POST['start'])){
//				$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
//				$filter[] = "DATE_FORMAT({$_POST['datepref']},'".MYSQL_DATE_SEARCH."') >= '$start'";
//				$filterstr[] = '<b>Start : </b>'.$_POST['start'];
//			}
//			if(!empty($_POST['end'])  && !empty($_POST['datepref'])){
//				$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
//				$filter[] = "DATE_FORMAT({$_POST['datepref']},'".MYSQL_DATE_SEARCH."') <= '$end'";
//				$filterstr[] = '<b>End : </b>'.$_POST['end'];
//			}
			
		       $rs = $myobj->get_party_wise_ledger_report_list($_POST['retailer_id']); // $myobj->get_item_category_list()
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
dynamic_js_enhancement();
?>

 <div id="workarea">
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm_alert('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
            <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table width="100%">
               <tr>
                <td><span class="star">*</span>Start<br />	
                    <input type="text" class="qdatepicker" name="start" value="<?php if(isset($_POST['start'])) echo $_POST['start']; else echo '01/'.date('m/Y'); ?>"/>
                </td>
                <td><span class="star">*</span>End<br />	
                    <input type="text" class="qdatepicker" name="end" value="<?php if(isset($_POST['end'])) echo $_POST['end']; else echo date('d/m/Y'); ?>" onblur="this.value = ucwords(trim(this.value));"/>
                </td>                  
                
                <td>Retailer<br />
                <?php db_pulldown($dbc, 'retailer_id', "SELECT id, name FROM retailer  ORDER BY name ASC", true, true, '');  ?> 
                </td>
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
           
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
                <div><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Date</td>
                      <td>Challan No/Payment No</td>
                      <td>DR</td>
                      <td>CR</td>
                      
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  //pre($rs);
                  $gtaotaldr = $gtotalcr = array();
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['challan_no'];
                      $uidname = $rows['cdate'];
                     
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
                        <td>'.$rows['challan_no'].'</td>
                        <td>'.$rows['dr'].'</td>
                        <td>'.$rows['cr'].'</td>
                      </tr>';
                       $gtaotaldr[] = $rows['dr'];
                       $gtotalcr[] = $rows['cr'];
                      $inc++;
                  }// foreach loop ends here				   
                    if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row
                  ?>
                    <tr style="text-align: left;">
                        <th  colspan="3"></th>
                        <th colspan="1">Total DR :-<?php echo my2digit(array_sum($gtaotaldr)); ?></th>
                        <th >Total CR :- <?php echo my2digit(array_sum($gtotalcr)); ?><br><br>
                            Toatal Balance:- <?php echo my2digit(array_sum($gtaotaldr) - array_sum($gtotalcr)); ?>
                        
                        </th>
                    </tr>
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
      <?php //}//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>