<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$forma = 'Gift'; // to indicate what type of form this is
$formaction = $p;
$myobj = new sale();
$cls_func_str = 'user_retailer_gift'; //The name of the function in the class that will do the job
$myorderby = 'id DESC'; // The orderby clause for fetching of the data
$myfilter = 'order_id ='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$retailer_level=$_SESSION[SESS.'constant']['retailer_level'];
$sesId = $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['urole'];

?>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>

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
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false, " dealer_id = '$_POST[dealer_id]'"))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false," id != '$_GET[id]' AND dealer_id = '$_POST[dealer_id]'"))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
	}
	return array(TRUE, '');
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
                //pre($mystat);
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			//geteditvalue_class($eid=$id, $in = $mystat);
			//$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}

?>
<script type="text/javascript">
$(function(){
	$(".retailer").autocomplete({
			source: "index.php?option=myajax-autocomplete&subauto=retailer-search&searchdomain=retailername",
			minLength: 1
		});
});
</script>
    <div id="workarea">
   
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
   
	<?php
       if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($mystat)){ //if no content available present no need to show the bottom part
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
             // pre($rs);
	      ########################## pagination details fetch starts here ###################################
              
			  ?>	 
              
              <div class="searchlistdiv" id="searchlistdiv"> 
                <div class="myprintheader"><b><?php echo $forma; ?> : <span id="totCounter"><?php echo count($mystat); ?></span></b>
               </div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>Order No</td>
                      <td>Gift Name</td>
                      <td>Quantity</td>
                      
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  $inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($mystat as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['order_id'];
                      $uidname = $rows['order_id'];
                    
                      //if($rows['locked'] == 1) $editlink = $deletelink = '';
		      if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td>'.$uidname.'<div style="display:none" id="delDiv'.$uid.'"></div></td>		
                        <td>'.$rows['gift_name'].'</td>
                        <td>'.$rows['quantity'].'</td>
                       
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
        <?php if(isset($pgoutput)) echo $pgoutput['pglinks']; // showing the paginataion links to the user?>
      </fieldset>
      </form>

      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('name');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>