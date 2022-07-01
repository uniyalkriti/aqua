<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 

$forma = 'Catalog'; // to indicate what type of form this is
$formaction = $p;
$mtype = $_GET['mtype'];
$formactionpop = $p."&mtype=$mtype";
$myobj = new catalog();
$cls_func_str = 'catalog_category'; //The name of the function in the class that will do the job
$myorderby = "catalog_$mtype.id DESC"; // The orderby clause for fetching of the data
$myfilter = "catalog_$mtype.id="; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$category = $_SESSION[SESS.'constant']["catalog_title_$mtype"];
$catlevel = $_SESSION[SESS.'constant']['catalog_level']; 
$catname = "name$mtype";

?>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>&mtype=<?php echo $mtype; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a><a>&raquo; <?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/catalog_bread.php'); 
          ?>
      </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc, $category;
        
	if($mode == 'filter') return array(TRUE, '');
        $mtype = $_POST['mtype'];
        $loc = $mtype - 1;
        $cat_id = "catalog_".$loc."_id";
        $cat_table_value = $_POST[$cat_id];
        $catname = "name$mtype";
	$field_arry = array("name" => $_POST[$catname]);// checking for  duplicate Unit Name
	
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,"catalog_$mtype", false, " catalog_".$loc."_id = '$cat_table_value'"))
			return array(FALSE, '<b>'.$category.'</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,"catalog_$mtype", false," id != '$_GET[id]' AND catalog_".$loc."_id = '$cat_table_value'"))
			return array(FALSE, '<b>'.$category.'</b> already exists, please provide a different value.');
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
				show_row_change(BASE_URL_A.'?option='.$formactionpop, $action_status['rId']);
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
				show_row_change(BASE_URL_A.'?option='.$formactionpop, $_POST['eid']);
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
                        $mtype = $_POST['mtype'];
                        
			if(!empty($_POST[$catname])){
				$filter[] = "catalog_$mtype.name LIKE '%$_POST[$catname]%'";
				$filterstr[] = '<b>Name  : </b>'.$_POST[$catname];
			}
			$filter[] = "catalog_$mtype.company_id = '{$_SESSION[SESS.'data']['company_id']}'";
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');			
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby"); // $myobj->get_item_category_list()
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
 else {
        $rs = $myobj->$funcname($filter="catalog_$mtype.company_id = '{$_SESSION[SESS.'data']['company_id']}'",  $records = '', $orderby ="ORDER BY $myorderby");
 }
dynamic_js_enhancement(); //This function is used for edit operation
?>
<script type="text/javascript">
$(function() {
	var mtype = $('#mtype').val();
        var company_id = $('#company_id').val();
        
	$(".catalog").autocomplete({
		source: "./modules/ajax-autocomplete/user/ajax-catalog-name.php?mtype="+mtype+"&company_id="+company_id
	});
	$("#itemname").autocomplete({
		source: "./modules/ajax-autocomplete/item/ajax-itemname.php"
	});
});
</script>
    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />	
        <input type="hidden" id="mtype" name="mtype" value="<?php if(isset($mtype)) echo $mtype;?>" />
        <input type="hidden" id="company_id" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id'];?>" />
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
            <input type="hidden" name="what" value="<?php echo $_SESSION[SESS.'constant']["catalog_title_$_GET[mtype]"]; ?>">
<!--            <div id="progress_div"></div>-->
       <?php
        $loop = $_GET['mtype'] - 1;
        for($i = 1; $i<=$loop; $i++)
        {
            $j = $i+1; //here we getnextplid
            ?>
             <tr>
                 <td><strong><?php echo $_SESSION[SESS.'constant']["catalog_title_$i"]; ?></strong><br>
                 
         <?php
             $js_attr = 'lang="subcategory'.$i.'" onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'catalog_'.$j.'\', \'catalog-subcategory\');" id="catalog_'.$i.'"';
            if($i == $loop) $js_attr = 'lang="subcategory'.$i.'" id="catalog_'.$i.'"';
            db_pulldown($dbc, "catalog_".$i."_id", "SELECT id, name FROM catalog_$i order by name", true, true, $js_attr);
       ?>
            </td>
             </tr>
       <?php 
        }
   
          ?>
           <tr>
               <td><strong><?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?></strong><br>
                <input type="text" lang="subcategory" name="<?php echo $catname; ?>" value="<?php if(isset($_POST[$catname])) echo $_POST[$catname]; ?>"></td>
         </tr>
        
         <tr>
             <td colspan="<?php echo $_SESSION[SESS.'catlevel']; ?>" align="center">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
			<?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            
            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1&mtype=<?php echo $mtype; ?>';" type="button" value="New" title="add new <?php echo $forma;?>" />  
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
               <legend class="legend">Search <?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
               <input type="hidden" id="mtype" name="mtype" value="<?php if(isset($mtype)) echo $mtype; ?>">
                <input type="hidden" id="company_id" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id'];?>" />
             <table>
               <tr>
		<td><?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?><br />
                    <input type="text" class="catalog" name="<?php echo $catname; ?>" id="name" value="<?php if(isset($_POST[$catname])) echo $_POST[$catname];?>" /> 
                </td>
               
                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&mtype=<?php echo $mtype; ?>';" type="button" value="Close" />
                  <input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1&mtype=<?php echo $mtype; ?>', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
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
                <div class="myprintheader"><b><?php echo $_SESSION[SESS.'constant']["catalog_title_$mtype"]; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div> 
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <?php   
                      for($i = $mtype; $i>=1; $i--)
                      {
                      ?>
                       <td><?php echo $_SESSION[SESS.'constant']["catalog_title_$i"]; ?></td>
                      <?php
                      }
                      ?>
                      <td class="options">Options</td>
                    </tr>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                 // $inc = 1;
		  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $key;                 
                      $uidname = $rows[$catname];
					  
		$editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&mtype='.$mtype.'"><img src="./images/b_edit.png"></a>';
		$deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete_special(\'Category Delete\', \''.$uid.'\',\'Category\',\''.addslashes($uidname).'\',\''.$mtype.'\');"><img src="./images/b_drop.png"></a>';
                    //if($rows['locked'] == 1) $editlink = $deletelink = '';
                    if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'<div style="display:none" id="delDiv'.$uid.'"></div></td>';
                        for($k = $mtype ; $k >= 1; $k--)
                        {
                        
                            echo'<td><strong>'.$rows["name$k"].'</strong></td>';
                        }
			
                     echo '<td class="options">'.$editlink.$deletelink.'</td>
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
      <script type="text/javascript">setfocus('partycode');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>

