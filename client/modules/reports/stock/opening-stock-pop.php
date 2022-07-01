<?php if(!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Primary Stock'; // to indicate what type of form this is
$formaction = $p;
$myobj = new report();
$cls_func_str = 'primary_stock'; //The name of the function in the class that will do the job
$myorderby = ' ORDER BY product_id'; // The orderby clause for fetching of the data
$myfilter = 'product_id = '; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
      <div id="breadcumb"><a href="#">Report</a> &raquo; <a href="#">Order</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/billing.php');  ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	return array(TRUE, '');
}

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $id = $_GET['id'];
        $rs = $myobj->$funcname($filter="product_id = '$id'",  $records = '', $orderby="$myorderby");
        if(empty($rs)){
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        exit();
        }
    }
}
dynamic_js_enhancement();
?>
 <div id="workarea">
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm_alert('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
	<?php
	if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
    	  <tr>
            <td>
              <div class="subhead1"><!-- this portion indicate the print options -->
                <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
<!--           <a href="javascript:pdf('searchlistdiv');" title="save as pdf document" style="margin-right:10px;"><img src="./icons/pdf.png" /></a>-->
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
                <div><b><?php echo $forma; ?> : <span id="totCounter"><?php //echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php//echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php //echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Name</td>
                      <td>Batch No</td>
                      <td>Order Qty</td>
                      <td>Mfg Date</td>
                      <td>Expiry Date</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $$rows['product_id'];				  
		      $uidname = $rows['name'];
                  $gtaotalqty = $gtotalval = array();
                 
                  $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['itemId'];
                      $uidname = $rows['itemname'];
                      echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td>'.$rows['name'].'</td>
                        <td>'.$rows['batch_no'].'</td>
                        <td>'.$rows['ostock'].'</td>
                        <td>'.($rows['mfg_date']).'</td>
                        <td>'.($rows['expiry_date']).'</td>
                </tr>';
		
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
      <?php //}//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>
   