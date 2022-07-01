<?php
if (!defined('BASE_URL')) die('direct script access not allowed');
?>
<?php
$forma = 'Company'; // to indicate what type of form this is
$formaction = $p;
$myobj = new company();
$cls_func_str = 'company'; //The name of the function in the class that will do the job
$myorderby = 'csa.c_id DESC'; // The orderby clause for fetching of the data
$myfilter = 'csa.c_id='; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
//pre($_SESSION[SESS.'data']);
?>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('name' => $_POST['name']);// checking for  duplicate Unit Name

	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'company', false, ""))
			return array(FALSE, '<b>Name</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'company', false," id != '$_GET[id]'"))
			return array(FALSE, '<b>Name</b> already exists, please provide a different value.');
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
				show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
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
				//unset($_SESSION[SESS.'securetoken']);
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

############################# code to get the stored info for editing starts here ########################
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
		//This will containt the pr no, pr date and other values
		$funcname = 'get_'.$cls_func_str.'_list';
		$mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '', $orderby=''); // $myobj->get_item_category_list()
                //pre($catlog_id);
                //$catlog_id = $mystat[$id]['catalog_id'];

                $mystat = $myobj->$funcname($filter="company.id = $id",  $records = '', $orderby='');
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
			if(!empty($_POST['cname'])){
				$filter[] = "name LIKE '%$_POST[cname]%'";
				$filterstr[] = '<b>Company Name  : </b>'.$_POST['cname'];
			}

			//$filter[] = "company_id = '{$_SESSION[SESS.'data']['company_id']}'";

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
        $rs = $myobj->$funcname($filter= "",  $records = '', $orderby="ORDER BY $myorderby");
}
dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function() {
	$(".product").autocomplete({
		source: "./modules/ajax-autocomplete/product/ajax-product-name.php"
	});
	$("#itemname").autocomplete({
		source: "./modules/ajax-autocomplete/item/ajax-itemname.php"
	});
});
</script>
<script type="text/javascript">
function set_company_session(id)
{

    if(window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange=function()
    {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {

    var r1 = xmlhttp.responseText;

    if(r1 !='') location.reload(true);
    // document.getElementById('show').innerHTML = r1;

    }
    }
    xmlhttp.open("GET","js/ajax_general/company_session.php?pid="+id ,true);
    xmlhttp.send();
}
</script>
<!--<script>
var s = document.createElement('script'); s.setAttribute('src','http://developer.quillpad.in/static/js/quill.js?lang=Hindi&key=421aa90e079fa326b6494f812ad13e79'); s.setAttribute('id','qpd_script'); document.head.appendChild(s);
</script>-->

    <div id="workarea">
    <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1) {   ?>
         <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="dealer_id" value="<?php echo $dealer_id;?>" />
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
            <tr>
                <td colspan="7"><div class="subhead1">Company Details:</div></td>
            </tr>
         <tr valign="top">
              <td><span class="star">*</span>Company Name<br>
                  <input style="width: 220px;" onChange="this.value = ucwords(trim(this.value));" type="text"  lang="Company Name" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
              </td>
               <td>
              Email<Br/>
              <input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>"  />
             </td>
              <td>
              Landline<Br/>
              <input type="text" onkeypress="return isNumberKey(event);" name="landline" value="<?php if(isset($_POST['landline'])) echo $_POST['landline']; ?>" maxlength="16" />
            </td>
             <td>Alternate Number <input type="text" name="other_numbers" id="alternate_number" value="<?php if(isset($_POST['other_numbers'])) echo $_POST['other_numbers']; ?>">
             </td>
             <td colspan="2">Website<br>
               <input type="text"  name="website" value="<?php if(isset($_POST['website'])) echo $_POST['website']; ?>"  />
              </td>
          </tr>
          <tr valign="top">
               <td colspan="7">
                  Address<br>
                  <textarea id="qpd_script" name="address"><?php if(isset($_POST['address'])) echo $_POST['address']; ?></textarea>
              </td>
          </tr>
          <tr>
              <td colspan="7"><div class="subhead1">Company Contact Person Details</div></td>
          </tr>
          <tr>
              <td>Contact Person</td>
              <td>Designation</td>
              <td>Mobile</td>
              <td>Email</td>
              <td>Phone</td>
              <td>Remark</td>
          </tr>
       <?php if(!isset($heid)) { ?>
          <tr>
              <td><input type="text" lang="Contact Person" name="cname[]"  value="" onblur="this.value = ucwords(trim(this.value));" /></td>
              <td> <input type="text" name="cdesignation[]"  value="" /></td>
              <td><input type="text" name="cmobile[]"  value="" /></td>
              <td><input type="text" name="cemail[]"  value="" /></td>
              <td> <input type="text" name="cphone[]"  value="" /></td>
              <td> <input type="text" name="cremark[]"  value="" /></td>
          </tr>
          <tr>
              <td><input type="text" lang="Contact Person" name="cname[]"  value="" onblur="this.value = ucwords(trim(this.value));" /></td>
              <td> <input type="text" name="cdesignation[]"  value="" /></td>
              <td><input type="text" name="cmobile[]"  value="" /></td>
              <td><input type="text" name="cemail[]"  value="" /></td>
              <td> <input type="text" name="cphone[]"  value="" /></td>
              <td> <input type="text" name="cremark[]"  value="" /></td>
          </tr>
       <?php } else {
                if(!empty($_POST['company_contact']))
                {
                    foreach($_POST['company_contact'] as $inkey=>$invalue) {
           ?>
          <tr>
              <td><input type="text" lang="Contact Person" name="cname[]"  value="<?php echo $invalue['cname']; ?>" onblur="this.value = ucwords(trim(this.value));" /></td>
              <td> <input type="text" name="cdesignation[]"  value="<?php echo $invalue['cdesignation']; ?>" /></td>
              <td><input type="text" name="cmobile[]"  value="<?php  echo $invalue['cmobile']; ?>" /></td>
              <td><input type="text" name="cemail[]"  value="<?php   echo $invalue['cemail']; ?>" /></td>
              <td> <input type="text" name="cphone[]"  value="<?php  echo $invalue['cphone']; ?>" /></td>
              <td> <input type="text" name="cremark[]"  value="<?php echo $invalue['cremark']; ?>" /></td>
          </tr>
       <?php } // foreach end here
                }
       }
       ?>
<!--          <tr>
              <td colspan="7"><div class="subhead1">CProduct Details</div></td>
          </tr>-->
<!--          <tr>
              <td>Product Category<br>
                  <?php arr_pulldown('catalog_status',array(1=>'Active', 2=>'Deactive'), '', true, true, ''); ?>
                  <input type="text" name="catalog_level" onchange="getdata_div(this.value, 'progdiv', 'get_catalog_title', 'catalog_title');" value="<?php if(isset($_POST['catalog_level'])) echo $_POST['catalog_level']; ?>">
              </td>
              <td>No Of Category<br>
                  <div id="pc"><input type="text" name="catalog_level" onchange="getdata_div(this.value, 'progdiv', 'get_catalog_title', 'ctitle');" value="<?php if(isset($_POST['catalog_level'])) echo $_POST['catalog_level']; ?>"></div>
              </td>
            <td colspan="4">
                  <div id="ctitle">
                          <?php if(isset($heid)){
                             $j= $_POST['catalog_level'];
                             echo '<table><tr>';
                              for($i=1;$i<=$j;$i++){

                               echo '<td><span class="star">*</span>Catalog Level&nbsp;'.$i.'<input type="text" name="catalog_title[]" lang="Catalog Level" value='.$_POST["catalog_title_$i"].'></td>';
                            }
                          echo '</tr></table>';
            }

            ?>

                  </div>
            </td></tr>-->
<!--              <td>Location Level<br>
                    <input onchange="getdata_div(this.value, 'progdiv', 'get_location_title', 'location_title');" type="text" name="location_level" value="<?php if(isset($_POST['location_level'])) echo $_POST['location_level']; ?>">
              </td>
          -->


<!--          <tr>
              <td colspan="7"><div class="subhead1">Dealer Level</div></td>
          </tr><tr>
          <td>Dealer Level<br>
                   <input type="text" name="dealer_level" value="<?php if(isset($_POST['dealer_level'])) echo $_POST['dealer_level']; ?>">
              </td>
          <td>Retailer Level<br>
                   <input type="text" name="retailer_level" value="<?php if(isset($_POST['retailer_level'])) echo $_POST['retailer_level']; ?>">
        </td>
        </tr>-->

              <!--
              <td>Retailer Level<br>
                <input type="text" name="retailer_level" value="<?php if(isset($_POST['retailer_level'])) echo $_POST['retailer_level']; ?>">
              </td>-->

<!--          <tr>
              <td colspan="7"><div class="subhead1">Location</div></td>
          </tr>
          <tr>
              <td lang="location"><span class="star">*</span>location<br>
                  <?php arr_pulldown('location_status',array(1=>'Active', 2=>'Deactive'), '', true, false, ''); ?>
                  <input type="text" name="catalog_level" onchange="getdata_div(this.value, 'progdiv', 'get_catalog_title', 'catalog_title');" value="<?php if(isset($_POST['catalog_level'])) echo $_POST['catalog_level']; ?>">
              </td>
              <td>Level of location<br>
                  <div id="pc"><input type="text" name="location_level" onchange="getdata_div(this.value, 'progdiv', 'get_location_title', 'ltitle');" value="<?php if(isset($_POST['location_level'])) echo $_POST['location_level']; ?>"></div>
              </td>
              <td lang="location" colspan="4">
                  <div id="ltitle">
                        <?php if(isset($heid)){
                              $j=$_POST['location_level'];
                             echo '<table><tr>';
                              for($i=1;$i<=$j;$i++){

                              echo '<td><span class="star">*</span>Location Level&nbsp;'.$i.'<input type="text" name="location_title[]" lang="Location Level" value='.$_POST["location_title_$i"].'></td>';

                            }
                          echo '</tr></table>';
            }

            ?>

                  </div>
              </td>
              <td>Location Level<br>
                    <input onchange="getdata_div(this.value, 'progdiv', 'get_location_title', 'location_title');" type="text" name="location_level" value="<?php if(isset($_POST['location_level'])) echo $_POST['location_level']; ?>">
              </td>
              <td>Dealer Level<br>
                   <input type="text" name="dealer_level" value="<?php if(isset($_POST['dealer_level'])) echo $_POST['dealer_level']; ?>">
              </td>
              <td>Retailer Level<br>
                <input type="text" name="retailer_level" value="<?php if(isset($_POST['retailer_level'])) echo $_POST['retailer_level']; ?>">
              </td>
          </tr>

          <tr>
              <td colspan="7">
                  <div id="catalog_title"></div>
              </td>
          </tr>
          <tr>
              <td colspan="7"><div class="subhead1">Vendor  Details</div></td>
          </tr>
          <tr>
              <td colspan="7">
                  <div id="location_title"></div>

              </td>
          </tr>-->
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
    <?php } else { ?>

     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
             <td>
                 <fieldset>
               <legend class="legend">Search <?php echo $forma;?></legend>
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
                   <td>Name<br />
                       <input type="text" name="cname" value="<?php if(isset($_POST['cname'])) echo $_POST['cname']?>"/>
                   </td>
<!--                <td>
                  <input id="mysave" type="submit" name="filter" value="Filter" />
                  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                  <input onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
                </td>-->
              </tr>
             </table>
             </fieldset>

             </td>
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
                <div class="myprintheader"><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> <!--out of <strong><?php echo $pgoutput['totrecords']; ?></strong>-->)</span>
                <br /><?php echo $filterused; ?></div>
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                    <tr class="search1tr">
                      <td class="sno">S.No</td>
                      <td>CSA Name</td>
                      <td>email</td>
                      <td>Phone</td>
                      <td>Address</td>
                      
                      
                    </tr>
                  <?php
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['c_id'];
                      $uidname = $rows['csa_name'];

                      $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                      $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Company Delete\', \''.$uid.'\',\'Company\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                      $company_session = '<span class="seperator">|</span> <a title="login" href="javascript:void(0);" onclick="set_company_session(\''.$uid.'\')"><img src="./images/login.png"  height="16" width="16"></a>';

                      //if($rows['locked'] == 1) $editlink = $deletelink = '';

                      if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
                        <td>'.$rows['Email'].'</td>
                        <td>'.$rows['mobile'].'</td>
                        <td>'.$rows['adress'].'</td>
                       
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
    <?php } // $_GET['showmode'] end here ?>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('partycode');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>
   