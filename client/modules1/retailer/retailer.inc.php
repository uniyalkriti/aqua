<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<link rel="stylesheet" href="../../css/global.css">
<?php
//include 'table.php';
$forma = 'Retailer'; // to indicate what type of form this is
$formaction = $p;
$myobj = new retailer();
$mydealer = new dealer();
$find_dealer = new dealer_sale();
$cls_func_str = 'retailer'; //The name of the function in the class that will do the job
$myorderby = 'retailer.name ASC'; // The orderby clause for fetching of the data
$myfilter = ' retailer.id ='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$retailer_level=$_SESSION[SESS.'constant']['retailer_level'];
$sesId = $_SESSION[SESS.'data']['id'];
$dealer = $_SESSION[SESS.'data']['dealer_id'];
$state = $_SESSION[SESS.'data']['state_id'];
$role_id = $_SESSION[SESS.'data']['urole'];
$dealer_id = $find_dealer->get_dealer_id($sesId, $role_id);
$dealername = $mydealer->get_dealer_list($filter="id = '$dealer_id'", $records='', $orderby='');


?>
<script type="text/javascript">
function showmapall(latlng)
{
        //alert(latlng);
        if(confirm('Do you want to View Map ?')==true)
        {
        window.open('map.php?lat_lng='+latlng,'popup','width=800,height=600,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=350,top=180');
                return true;
        }else{
                return false;
        }
}
</script>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php 

if($_SESSION[SESS.'data']['role_group_id'] != 22) {
    
  //  if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) 
        // echo "manisha";
    ///    require_once('breadcum/userbread.php'); 
   
}

?>
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
    list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);   
    if($checkpass)
    {
      // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
      magic_quotes_check($dbc, $check=true);
      $funcname = $cls_func_str.'_edit'; //retailer_edit
      $action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
      if($action_status['status'])
      {
        echo '<span class="asm">'.$action_status['myreason'].'</span>'; 
        //unset($_SESSION[SESS.'securetoken']); 
        show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
        unset($_POST);
        ?> <script>
                    setTimeout("window.parent.location = 'index.php?option=retailer-add'", 500);
                    //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>
                <?php
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
    $funcname = 'get_'.$cls_func_str.'_list'; //get_retailer_list
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
      if(!empty($_POST['name'])){
        $filter[] = "name LIKE '%$_POST[name]%'";
        $filterstr[] = '<b>Name  : </b>'.$_POST['name'];
      }
      
                        $filter[] = "retailer.dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}' ";
      $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
                        $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");      
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
            $rs = $myobj->$funcname($filter=" retailer.dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}' ",  $records = '', $orderby=" ORDER BY $myorderby");
}
dynamic_js_enhancement();
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
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
     
        <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="dealer_id" value="<?php echo $_SESSION[SESS.'data']['dealer_id']; ?>">
        <input type="hidden" name="old_image" value="<?php if(isset($_POST['image_name'])) echo $_POST['image_name']; ?>" />  
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
             <td colspan="5"><div class="subhead1">Retailer Information Details</div></td>
         </tr>
         <tr valign="top">
<!--             <td><span class="star">*</span>Please tick, if retailer work for D.S. Group.<br>
                 <input type="checkbox"  lang="chk" name="chk" value="chk" checked="checked" ?>
              </td>-->
              <td><span class="star">*</span>Retailer Name<br>
               <input onChange="this.value = ucwords(trim(this.value));" type="text"  lang="Name" name="name" value="<?php echo (isset($_POST['name'])) ? $_POST['name']:''; ?>">
              </td>
              <td><span class="star">*</span>SO. Name<br>
              <?php
              if(!isset($heid)){
                    $q = "Select person.id,CONCAT_WS(' ',person.first_name,last_name) as name from person INNER JOIN user_dealer_retailer udr ON person.id = udr.user_id  where dealer_id ='$dealer'  ";
                    db_pulldown($dbc, "so", $q, true,true,'id="so" lang="out"');
              }else{
                  $q = "Select person.id,CONCAT_WS(' ',person.first_name,last_name) as name from person INNER JOIN user_dealer_retailer udr ON person.id = udr.user_id  where dealer_id ='$dealer'  ";
                    db_pulldown($dbc, "so", $q, true,true,'id="so" lang="out"','',TRUE,TRUE,$_POST['so']);
              }
              
              ?>
              </td>
              <td>Phone No <Br/>
                <input type="text" onkeypress="return isNumberKey(event);" name="landline" value="<?php if(isset($_POST['landline'])) echo $_POST['landline']; ?>" maxlength="16" />
              
            </td>
            <!--  <td>Mobile No<Br/>
              <input type="text" onkeypress="return isNumberKey(event);" name="alternate_number" value="<?php if(isset($_POST['alternate_number'])) echo $_POST['alternate_number']; ?>" maxlength="16" />
            </td> -->
                    
          </tr>
          <tr>
               <td colspan="2">Email<Br/>
              <input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" />
             </td> 
               <td colspan="2">Contact Person<Br/>
              <input type="text" name="contact_per_name" value="<?php if(isset($_POST['contact_per_name'])) echo $_POST['contact_per_name']; ?>" />
             </td> 
              <td colspan="5">Address<br>
                <textarea name="address"><?php if(isset($_POST['address'])) echo $_POST['address']; ?></textarea>
            </td> 
<!--            <td>Image <?php if(isset($heid) && !empty($_POST['image_name'])) { echo '<img height="20" width="20" src="../myuploads/retailer/create/'.$_POST['image_name'].'" height="30" width="30" >'; } ?><br>
             <input type="file" name="image_name" value="">
             </td>  -->
          </tr>
          <tr>
               <td colspan="5"><div class="subhead1">Retailer Transactional Details</div></td> 
          </tr>
          <tr>
            <td>
              <span class="star">*</span>Pin No<Br/>
              <input type="text" name="pin_no" value="<?php if(isset($_POST['pin_no'])) echo $_POST['pin_no']; ?>" maxlength="10" required/>
             </td>
             <td>
              GSTIN No<Br/>
              <input type="text" name="tin_no" value="<?php if(isset($_POST['tin_no'])) echo $_POST['tin_no']; ?>" maxlength="60" />
             </td>
             <td>Alternate Number <input type="text" name="other_numbers" id="alternate_number" value="<?php if(isset($_POST['other_numbers'])) echo $_POST['other_numbers']; ?>">
             </td>
             <td> Outlet Type  <?php //add_refresher('index.php?option=outlet-type&showmode=1');?><br>
            <?php db_pulldown($dbc, "outlet_type_id", "SELECT id as outlet_type_id, outlet_type FROM _retailer_outlet_type", true,true,'id="outlet_type_id" lang="out"'); ?>
             </td> 
             <td>Avg.Per Month Purchase : <input type="text" name="avg_per_month_pur" id="avg_per_month_pur" value="<?php if(isset($_POST['avg_per_month_pur'])) echo $_POST['avg_per_month_pur']; ?>">
               </td>
           </tr>
           <tr>
               <td colspan="6"><div class="subhead1">Retailer Location Details</div></td>
           </tr>
            <tr>
             <?php
            // pre($_SESSION);
        $loop = $retailer_level - 3;
      
        for($i = 3; $i<=$retailer_level; $i++)
        {
            
            $j = $i+1; //here we getnextplid
            if(isset($heid))
            {
                 $retailer_location = $myobj->get_retailer_location_list($filter="retailer.id=$id",  $records = '', $orderby='');
                 //pre($retailer_location);
                if(!empty($retailer_location))
                {
                    foreach($retailer_location as $key=>$value)
                    {
                      $_POST["location_".$i."_id"] = $value["location_".$i."_id"]; 
            ?>             
              <td> <span class="star">*</span><?php echo ucwords($_SESSION[SESS.'constant']["location_title_$i"]); ?><br>
         <?php
                     $js_attr = 'onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'location_'.$j.'\', \'location-subcategory\');" lang="location" id="location_'.$i.'"';
                   
                    if($i == $retailer_level) $js_attr = 'id="location_'.$i.'"';
                     //if($i == $retailer_level) $js_attr = 'lang="location" id="location_'.$i.'"';
                  //  echo "SELECT id, name FROM location_$i WHERE location_2_id=$state";
                    if($i == 3) db_pulldown($dbc, "location_".$i."_id", "SELECT l3_id as id, l3_name as name FROM location_view WHERE state_id=$state", true, true, $js_attr); 
            if($i > 3) {
                       $o = $i-1;
                       $setname = "location_".$o."_id";
                       $q = "SELECT id, name FROM location_$i WHERE location_".$o."_id = '$_POST[$setname]' ";
                       db_pulldown($dbc, "location_".$i."_id", $q, true, true, $js_attr); 
            }
       ?>
            </td>
       <?php
                    } // foreach end here
                }// !empty() end here
                else
                { 
                   ?>
                   <td><span class="star">*</span>
                   <?php echo ucwords($_SESSION[SESS.'constant']["location_title_$i"]); ?><br>
                        <?php
                           $js_attr = 'onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'location_'.$j.'\', \'location-subcategory\');" lang="location" id="location_'.$i.'"';
                           if($retailer_level > $dealer_level)
                             $js_attr1 = 'onblur="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'location_'.$j.'\', \'location-subcategory\');" lang="location" id="location_'.$i.'"';       else $js_attr1 = '';
                           
                             if($i == $dealer_level) $js_attr = 'onchange="fetch_location(this.value, \'progress_div\', \'dealer_id\', \'get-location-wise-dealer\');" '.$js_attr1.' lang="location" id="location_'.$i.'"';     
                             //if($i == $retailer_level) $js_attr = 'lang="location" id="location_'.$i.'"';
                           if($i ==3) db_pulldown($dbc, "location_".$i."_id", "SELECT id, name FROM location_$i", true, true, $js_attr);
                           if($i > 3) {
                       $o = $i-1;
                      $setname = "location_".$i."_id";
                       $q = "SELECT id, name FROM location_$i WHERE location_".$o."_id = '$_POST[$setname]'";
                       db_pulldown($dbc, "location_".$i."_id", $q, true, true, $js_attr); 
            }
            ?>
                   </td>
                   <?php
                } // else empty part end here
            } //if(isset($heid)) end here
            else 
                 {
                  ?>
                   <td><span class="star">*</span>
                   <?php echo ucwords($_SESSION[SESS.'constant']["location_title_$i"]); ?><br>
                        <?php
                        //$loc = $myobj->get_location_ids($dealer_id);
                           $js_attr = 'onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'location_'.$j.'\', \'location-subcategory\');" lang="location" id="location_'.$i.'"';                          
                           if($i == $retailer_level) $js_attr = 'id="location_'.$i.'"';
                           if($i == 3) db_pulldown($dbc, "location_".$i."_id", "SELECT l3_id, l3_name FROM location_view WHERE state_id=$state", true, true, $js_attr);
                           
                           if($i > 3) echo '<select name="location_'.$i.'_id" '.$js_attr.'><option value="">==Select==</option></</select>';                           
                           ?>
                   </td>
                    <?php
                 } //else part end here
        } // outer for loop end here
    ?>
          </tr>
           <tr>
           <td align="center" colspan="7">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
               <input style="background-color: #438eb9" id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
            <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
               
            <input class="btn btn-sm btn-success" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
<!--            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />-->
            <!--<a href="index.php?option=retailer-add"><input type="button" value="Exit" /> </a>-->
            <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
      <?php }else{?>
           <a href="index.php?option=retailer-add"> <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" /></a>
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
    <?php }else{ //if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
            
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
        <fieldset>
              <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
    <td>Name<br />
                <input type="text" placeholder="Enter Retailer Name" class="retailer" name="name" id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /> 
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                 <td>
                    <br>
                    <input id="mysave" type="submit" name="filter" class="btn btn-sm btn-primary" value="Filter" />
                  
                <!--<input class="btn btn-sm btn-success" onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" /> 
                <input class="btn btn-success" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />--> 

                 </td>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
         </table> 
         </form>
  <?php
  if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
                   
              <?php
             // pre($rs);
        ########################## pagination details fetch starts here ###################################
          ?>
              <div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header">
                   Retailer List <div class="pull-right tableTools-container"></div> 
                   
                </div>

                <!-- div.table-responsive -->
<?php
//pre($rs);
?>
                <!-- div.dataTables_borderWrap -->
                <div>
                    <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
<!--                                <th class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </th>-->
                                <th  class="sno">S.No</th>
                      <th >Name</th>
                      <th >Email</th>
                      <th >Mobile</th>
                      <th >Contact Person</th>
                      <th >Address</th>
                      <th >GSTIN</th>
                      <th >Pin No</th>
                      <th >Created By</th>
                      <th  class="options">Options</th>
                               </tr>
                        </thead>

                        <tbody>
                            <?php
                            $inc = 1;
                            foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['name'].' ['.$rows['retailer_location'].']';
                      $lat_lng = $rows['lat_long'];
                      $mcc = $rows['mncmcclatcellid'];
            
          $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                      $personlink = '<span class="seperator">|</span> <a title="Add Person" class="iframef" href="indexpop.php?option=add-user&showmode=1&mode=3&retailer_id='.$uid.'"><img src="./images/user.png"></a>';
         $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Retailer Delete\', \''.$uid.'\',\'Retailer\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                      
                      $person_dealer_icon = '';
                      $icon = $myobj->get_retailer_person_icon($uid);
                     if(!empty($icon)) $person_dealer_icon = '<a title="View Retailer Person Details" class="iframef" href="indexpop.php?option=retailer-person-list&mode=2&retailer_id='.$uid.'"><img src="./images/person.png"></a>';
                     if(!empty($lat_lng)) 
                          $location = '<a class="iframe" href="indexpop.php?option=retailer-location&mcc='.$rows['mncmcclatcellid'].'&lat_lng='.$lat_lng.'"><img width="18px;" height="18px;" src="./images/green.png"></a>';
          else if(!empty($mcc))  $location = '<a class="iframe" href="indexpop.php?option=retailer-location&mcc='.$rows['mncmcclatcellid'].'&lat_lng='.$lat_lng.'"><img width="18px;" height="18px;" src="./images/red.png"></a>';
                      else  $location = '<a title="Location Not Found" href="javascript:void(0);"><img  width="20" height="20px;" src="./images/red.png"></a>';
                      if(!empty($rows['pname'])){
                     $person = $rows['pname'];
                      }else{
                          $person = 'Ds Group';
                      }
                      //if($rows['locked'] == 1) $editlink = $deletelink = '';
          if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                          <!-- <td class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>-->
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td><a href=\'#\' onclick="showmapall(\''.$lat_lng.'\')"><strong>'.$uidname.'</strong></a>'.$person_dealer_icon.'<div style="display:none" id="delDiv'.$uid.'"></div></td>
                            
                        <td>'.$rows['email'].'</td>                       
                        <td>'.$rows['landline'].'</td>
                        <td>'.$rows['contact_per_name'].'</td>
                        <td>'.$rows['address'].'</td>
                        <td>'.$rows['tin_no'].'</td>
                        <td>'.$rows['pin_no'].'</td>
                        <td>'.$person.'</td>
                        <td class="options">'.$editlink.'</td>
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
</div><!-- /.page-content -->  
        <?php ########################## pagination details fetch ends here ###################################
         ?>   
                
                
          <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>

      <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      
<!--            <script src="assets/js/jquery-2.1.4.min.js"></script>-->
 <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
        <script src="assets/js/dataTables.buttons.min.js"></script>
        <script src="assets/js/buttons.flash.min.js"></script>
        <script src="assets/js/buttons.html5.min.js"></script>
        <script src="assets/js/buttons.print.min.js"></script>
        <script src="assets/js/buttons.colVis.min.js"></script>
        <script src="assets/js/dataTables.select.min.js"></script>

        <!-- ace scripts -->
        <script src="assets/js/ace-elements.min.js"></script>
        <script src="assets/js/ace.min.js"></script>

        <!-- inline scripts related to this page -->
        <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                //{"bSortable": false},
                                                null, null, null, null, null,null,null,null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],

                                            //"bProcessing": true,
                                            //"bServerSide": true,
                                            //"sAjaxSource": "http://127.0.0.1/table.php"	,

                                            //,
                                            //"sScrollY": "200px",
                                            //"bPaginate": false,

                                            //"sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            //"bScrollCollapse": true,
                                            //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                                            //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                                            //"iDisplayLength": 50


                                            select: {
                                                style: 'multi'
                                            }
                                        });



                                $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

                                new $.fn.dataTable.Buttons(myTable, {
                                    buttons: [
                                        {
                                            "extend": "colvis",
                                            "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            columns: ':not(:first):not(:last)'
                                        },
                                        {
                                            "extend": "copy",
                                            "text": "<i class='fa fa-copy bigger-110 pink'>Copy</i> <span class='hidden'>Copy to clipboard</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "csv",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'>Excel</i> <span class='hidden'>Export to CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
//                                        {
//                                            "extend": "excel",
//                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
//                                            "className": "btn btn-white btn-primary btn-bold"
//                                        },
//                                        {
//                                            "extend": "pdf",
//                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
//                                            "className": "btn btn-white btn-primary btn-bold"
//                                        },
                                        {
                                            "extend": "print",
                                            "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            autoPrint: false,
                                            message: 'This print was produced using the Print button for DataTables'
                                        }
                                    ]
                                });
                                myTable.buttons().container().appendTo($('.tableTools-container'));

                                //style the message box
                                var defaultCopyAction = myTable.button(1).action();
                                myTable.button(1).action(function (e, dt, button, config) {
                                    defaultCopyAction(e, dt, button, config);
                                    $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
                                });


                                var defaultColvisAction = myTable.button(0).action();
                                myTable.button(0).action(function (e, dt, button, config) {

                                    defaultColvisAction(e, dt, button, config);


                                    if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                                        $('.dt-button-collection')
                                                .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                                                .find('a').attr('href', '#').wrap("<li />")
                                    }
                                    $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
                                });

                                ////

                                setTimeout(function () {
                                    $($('.tableTools-container')).find('a.dt-button').each(function () {
                                        var div = $(this).find(' > div').first();
                                        if (div.length == 1)
                                            div.tooltip({container: 'body', title: div.parent().text()});
                                        else
                                            $(this).tooltip({container: 'body', title: $(this).text()});
                                    });
                                }, 500);





                                myTable.on('select', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                                    }
                                });
                                myTable.on('deselect', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                                    }
                                });




                                /////////////////////////////////
                                //table checkboxes
                                $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

                                //select/deselect all rows according to table header checkbox
                                $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $('#dynamic-table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            myTable.row(row).select();
                                        else
                                            myTable.row(row).deselect();
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                                    var row = $(this).closest('tr').get(0);
                                    if (this.checked)
                                        myTable.row(row).deselect();
                                    else
                                        myTable.row(row).select();
                                });



                                $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                                    e.stopImmediatePropagation();
                                    e.stopPropagation();
                                    e.preventDefault();
                                });



                                //And for the first simple table, which doesn't have TableTools or dataTables
                                //select/deselect all rows according to table header checkbox
                                var active_class = 'active';
                                $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $(this).closest('table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                                        else
                                            $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                                    var $row = $(this).closest('tr');
                                    if ($row.is('.detail-row '))
                                        return;
                                    if (this.checked)
                                        $row.addClass(active_class);
                                    else
                                        $row.removeClass(active_class);
                                });



                                /********************************/
                                //add tooltip for small view action buttons in dropdown menu
                                $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

                                //tooltip placement on right or left
                                function tooltip_placement(context, source) {
                                    var $source = $(source);
                                    var $parent = $source.closest('table')
                                    var off1 = $parent.offset();
                                    var w1 = $parent.width();

                                    var off2 = $source.offset();
                                    //var w2 = $source.width();

                                    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
                                        return 'right';
                                    return 'left';
                                }




                                /***************/
                                $('.show-details-btn').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).closest('tr').next().toggleClass('open');
                                    $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
                                });
                                /***************/





                                /**
                                 //add horizontal scrollbars to a simple table
                                 $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
                                 {
                                 horizontal: true,
                                 styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
                                 size: 2000,
                                 mouseWheelLock: true
                                 }
                                 ).css('padding-top', '12px');
                                 */


                            })
        </script>
      
