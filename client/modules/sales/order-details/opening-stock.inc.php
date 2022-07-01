<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
//print_r($_SESSION);
$forma = 'Stock Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new opening_stock();
$primary_sale = new dealer_sale();
$cls_func_str = 'opening_stock'; //The name of the function in the class that will do the job
$myorderby = 'stock.date DESC'; // The orderby clause for fetching of the data
$myfilter = 'stock.id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS.'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$userid = $_SESSION[SESS.'data']['id'];
$sesId =  $_SESSION[SESS.'sess']['sesId'];
$role_id = $_SESSION[SESS.'data']['urole'];
$dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
//pre($_SESSION);
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$user_data = $primary_sale->get_dsp_wise_user_data($dealer_id);

?>
<script>   
            $(document).ready(function () {
                   $('#btn').click(function () {
                       window.opener.location.reload(true);
                       window.close();
                   });
               });
        </script>
<script>
function showamt(str) {
   // alert(str);
  if (str=="") {
    document.getElementById("base").value="0";
    return;
  }
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("base").value=this.value;
    }
  }
  //var a = "../client/sales/order-details/get_amount.php?q="+str;
 var a = "index.php?option=get_amount.php&q="+str;
  //alert(a);
  xmlhttp.open("GET",a,true);
  xmlhttp.send();
}
</script>

  <div id="breadcumb"><a href="#">Stock</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/party.php');  ?>
 </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
// include "table.php";
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
  global $dbc;
  if($mode == 'filter') 
  return array(TRUE, '');

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
     // echo $funcname;die;
      $action_status =  $myobj->$funcname(); // $myobj->item_category_save()
      if($action_status['status'])
      {
        echo '<span class="asm">'.$action_status['myreason'].'</span>';
        //show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
        unset($_POST);
	?> <script>
                    setTimeout("window.parent.location = 'index.php?option=opening-stock'", 500);
                    //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>
                <?php
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
    //echo "manisha";
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
        //show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
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
                //pre($mystat);
    if(!empty($mystat))
    {
      //geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
      geteditvalue_class($eid=$id, $in = $mystat);
      //This will create the post multidimensional array
      //create_multi_post($mystat[$id]['pr_item'], array('itemId'=>'itemId', 'qty'=>'qty'));
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
$mymatch['datepref'] = array('podate'=>'PO Date', 'created'=>'Created');
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
        $filter[] = "DATE_FORMAT(`date`,'".MYSQL_DATE_SEARCH."') >= '$start'";
        $filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
      }
      if(!empty($_POST['to_date'])){
        $end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`date`,'".MYSQL_DATE_SEARCH."') <= '$end'";
        $filterstr[] = '<b>End : </b>'.$_POST['to_date'];
                                 
      }
                        if(!empty($_POST['order_no'])){
        $filter[] = "order_id = '$_POST[order_no]'";
        $filterstr[] = '<b>Order No  : </b>'.$_POST['order_no'];
      }
                       
                         //pre($user_data);
//                         if(!empty($user_data)){
//                            $user_data_str = implode(',' , $user_data);
//                            $filter[] = "created_person_id IN ($user_data_str)";
//                            
//                        }
			 $filter[]="action='0'";
                        $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
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
           // $user_data_str = implode(',' , $user_data);
            $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
             $filter[]="action='0'";
            $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");
}
dynamic_js_enhancement();
?>

<div id="workarea">
 <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
 <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style="background-color: #438eb9;font-size: 150%;font-family: Arial, Georgia, Serif;"><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />  
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
            <tr>
<!--                <td><strong>Dealer</strong><span class="star">*</span><br>
                <?php 
               //  if(isset($_SESSION[SESS.'data']['company_id']) && $_SESSION[SESS.'data']['company_id'] == 1) {
                         $dealer_name = myrowval ('dealer', 'name', "id = '{$_SESSION[SESS.'data']['dealer_id']}'");
                          echo '<input type="text" value="'.$dealer_name.'">
                               <input type="hidden" id="dealer_id" name="dealer_id" value="'.$_SESSION[SESS.'data']['dealer_id'].'">';
                         
//                   } else {
//                        $q = "SELECT partyId, partyname FROM party WHERE company_id = '{$_SESSION[SESS.'data']['company_id']}'  ORDER BY partyname ASC";
//                        db_pulldown($dbc,'dealer_id',$q,true,true,'lang="dealer_id"'); 
//                   }
                 ?>
                </td>-->
                <td><span class="star">*</span>Company<br> 
    <?php
    $js_attr = ' lang="company" onchange="fetch_location(this.value, \'progress_div\', \'product_id\', \'company-catalog\');" ';
    if (!isset($_POST['company_id'])) {
        $q = 'SELECT id, name from company where id = 1';
        db_pulldown($dbc, 'company_id', $q, TRUE, TRUE, $js_attr,'',1);
    } else {
        ?>
        <select name="company_id" id="state_id" lang="company">
            <option value="">== Please Select ==</option>
        <?php
        $q = 'select id,name FROM company ';
        $st_res = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($st_res)) {
            ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if ($_POST['company_id'] == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
            <?php
        }
        ?>
                                </select>
                            <?php } //echo $q;
                            ?>
                        </td>
<!--                <td><strong>User</strong><br>
                    <select name="created_person_id" id="user_id">
                        <option>== Please Select ==</option>
                <?php 
                 if(!empty($user_data)){
                     $user_data_str = implode(',' , $user_data);
                     $q = "SELECT id, CONCAT_WS(' ', first_name, middle_name,last_name) AS name FROM person WHERE id IN ($user_data_str)";
                     $selected = '';
                     if(isset($heid)) $selected = $_POST['created_person_id'];
                     echo option_builder($dbc, $q, $selected);
                 }
                 ?>
                    </select>
                </td>-->
                </td>
                <td><strong>Date</strong><br>
                    <input type="text" id="podate" name="date"  value="<?php if(isset($_POST['receive_date'])) echo $_POST['receive_date']; else echo date('d/m/Y'); ?>"  class="qdatepicker" />
                </td>
                
                </td>
<td><strong>CSA Name</strong><br>
                     <?php
                   $q = 'SELECT c_id, csa_name FROM csa where c_id IN('.$_SESSION[SESS.'data']['csa_id'].')';
        db_pulldownstart($dbc, 'csa_id', $q, TRUE, TRUE,'','','');
        ?>
                  </td>              

            </tr>
<tr>
     <td colspan="5"><div style="background-color: #438eb9;font-size: 150%;font-family: Arial, Georgia, Serif;">Order Detail</div></td>
         </tr>
         <tr>
           <td colspan="4">
             <!-- table to capture the address field starts -->
             <div id="progdiv"></div>
             <div id="ss">
             <table width="100%"  id="mytable">
               <tr class="thead" style="font-weight:bold;">
                 <td>S.No</td>
                 <td>Product</td>
<!--                 <td>Batch No</td>-->
                 <td>M.R.P</td>
<!--                 <td>Sale Rate</td>-->
                 <td>Qty.(in pieces)</td>
                 <td>Batch Number</td>
                 <td>Mfg Date.<br><span class="example">(dd/mm/yyyy)</span></td>
                 <td>Expire Date<br><span class="example">(dd/mm/yyyy)</span></td>
                 <td style="width:40px;">&nbsp;</td>
               </tr>
          <?php if(!isset($heid)){ ?>
               <tr class="tdata">
                 <td class="myintrow">1</td>
                 <td>
                 
 <?php 
                     db_pulldown($dbc , 'product_id[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN catalog_2 ON catalog_product.catalog_id = catalog_2.id WHERE catalog_product.company_id = 1 order by name",TRUE,TRUE,' id="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);');
                      ?>
                
                 </td>

                 <input type="hidden" name="dealer_id[]"  value="<?=$_SESSION[SESS.'data']['dealer_id']?>"  />
                  <input type="hidden" name="person_id[]"  value="<?=$_SESSION[SESS.'data']['id']?>"  />
                  <td>
                 <input type="text" name="base_price[]"  value=""  />
                 </td>

                 <td>
                     <input type="text" name="quantity[]" lang="quantity" placeholder="In Pieces"  value=""  />
                 </td>
                  <td>
                 <input type="text" name="purchase_inv[]"  value=""  />
                 </td>
                  <td>
                      <input type="text" class="datepicker"  name="mfg_date[]"  value=""  />
                 </td>
                  <td>
                      <input type="text" class="datepicker"  name="expiry_date[]"  value=""  />
                 </td>
                 
                 <td><img  title="more" src="images/more.png" onclick="javascript:addmore22('mytable', event,'');"/></td>
               </tr>
          <?php } else {  
              $inc = 1;
            //  pre($_POST['order_item']);
              foreach ($_POST['order_item'] as $inkey=>$invalue) { ?>
                <tr class="tdata">
                 <td class="myintrow">1</td>
                 <td>
                   <?php 
                      db_pulldown($dbc , 'product_id[]', "SELECT id,name FROM catalog_product ",TRUE,TRUE,' id ="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);', '', $invalue['product_id']); ?>
                 </td>

                  <td>
                 <input type="text" name="base_price[]"  value="<?php echo $invalue['rate']; ?>"  />
                 </td>
             
                 <td>
                     <input type="text" name="quantity[]" lang="quantity"  value="<?php echo $invalue['quantity']; ?>"  />
                 </td>
                  <td>
                     <input type="text" name="purchase_inv[]" lang="purchase_inv"  value="<?php echo $invalue['purchase_inv']; ?>"  />
                 </td>
                  <td>
                      <input type="text" class="datepicker"  name="mfg_date[]"  value="<?php echo $invalue['mfg_date']; ?>"  />
                 </td>
                  <td>
                      <input type="text" class="datepicker"  name="expiry_date[]"  value="<?php echo $invalue['expiry_date']; ?>"  />
                 </td>
                 
                 <td><img  title="more" src="images/more.png" onclick="javascript:addmore22('mytable', event,'');"/></td>
               </tr>
          <?php } 
          } ?>
               <tr><td colspan="9">&nbsp;</td></tr>
             </table>
             </div>
             <!-- table to capture the address field ends -->
           </td>
         </tr>
         <tr>
             <td colspan="4" align="center">
                  <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input style="background-color: #438eb9" id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
      <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input style="background-color: #87B87F" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
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
 <?php }
 else if($_GET['excel']==1)
 { ?>
    <br/>
    <form method="post" action="index.php?option=import" enctype="multipart/form-data" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style="background-color: #438eb9;font-size: 100%;font-family: Arial, Georgia, Serif; color:white;"><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />  
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       
       <strong>Upload CSV File</strong>
       <br/> <br/>
        <div class="row">
            <div class="col-xs-4">
               <br/><br/>
            </div>
            <div class="col-xs-6">
                <input type="file" name="upload">
                
            </div>
        </div>
        <div class="row">
             <div class="col-xs-12">
               
             </div>
           
        </div>
         <div class="row">
             <div class="col-xs-5">
               
            </div
            <div class="col-xs-6">
                <input type="submit" name="save" value="Submit" style="background-color:#438EB9 ">
                
            </div>
        </div>
      </fieldset>
 </form> 
<?php }
 else{//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here ?>
  <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
        <fieldset>
<!--               <legend class="legend">Search <?php echo $forma;?></legend>-->
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
            
               <div class="col-xs-2">From Date<br />
                    <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else  echo date('d/M/Y');?>" />
                </div>
               <div class="col-xs-2">To Date<br />
                     <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/M/Y');?>" />
                </div>                
                 <div class="col-xs-6">
                     <br/>
                  <input id="mysave" class="btn btn-primary" type="submit" name="filter" value="Filter" />
                  <!--<input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
<!--                  <input class="btn btn-success"  onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
               <a class="iframef" target="_blanck" href="index.php?option=<?php echo $formaction; ?>&showmode=1&mode=1"> <input class="btn btn-success" type="button" value="New" title="add new <?php echo $formaction; ?>" />           
       <a class="iframef" href="index.php?option=<?php echo $formaction; ?>&excel=1"> <input class="btn btn-warning" type="button" value="Import CSV" title="Import From CSV" />         
                 </div>
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
        </table>
       
      </fieldset>
      </form>
           <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
          <script>
          var a = document.getElementById('');
          </script>
        <div class="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>  
   <script type="text/javascript">
      function do_delete()
      {
            if (confirm("Delete Account?"))
                 location.href='linktoaccountdeletion';
      }
  
    //do_delete(\'Sale Delete\', \''.$uid.'\',\'Sale Order\',\''.addslashes($uidname).'\')
    </script> 
    
              <?php
      ########################## pagination details fetch starts here ###################################
              //pre($rs);
             // $pgoutput = get_pagination_details($rs);
              //echo $pgoutput['loader'];
               ########################## pagination details fetch ends here ###################################
     //   $inc = 1+($key-1)*PGDISPLAY;
       // $lastinc = (($inc+PGDISPLAY-1) > $pgoutput['totrecords']) ? $pgoutput['totrecords'] : ($inc+PGDISPLAY-1);
        ?>   
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header">
                   Remaining Stock Details Details<div class="pull-right tableTools-container"></div> 
                   
                </div>

                <!-- div.table-responsive -->
<?php
//pre($rs);
?>
                <!-- div.dataTables_borderWrap -->
                <div>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>
                   <tr>
                      <th class="sno">S.No</th>
                      <th>Created Date</th>
                      <th >Batch No</th>
                      <th >CSA Name</th>
                       <th style="width: 120px;">Product Name</th>
                        <th style="width: 120px;">Rate</th>
                        <th style="width: 120px;">Quantity</th>
                        <th style="width: 120px;">Remaining Qty</th>
                        <th style="width: 120px;">Mfg</th>
                        <th style="width: 120px;">Expire</th>
<!--                         <th rowspan="2" width="75px">Options</th>-->
                        
                    </tr>
                    </thead>
                    <tbody>
                   
                  <?php 
                 $bg = TR_ROW_COLOR1;
                  $inc = 1;
                 // pre($rs);
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['order_id'];
 $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>&nbsp;&nbsp;';
//      $editlink ='<a data-toggle="modal" href="#myModal" id"'.$rows['id'].'" ><img src="./images/b_edit.png"></a>&nbsp;&nbsp;';         
                      $deletelink = '<span class="Seperator">|</span> &nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete(\'Sale Delete\', \''.$uid.'\',\'Sale Order\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                     echo'
                      <tr  BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td>'.$inc.'</td>
                        <td>'.$rows['fdated'].'</td>
                   
                        <td>'.$rows['batch_no'].'</td>
                        <td>'.$rows['csa_name'].'</td>
                        <td>'.$rows['product'].'</td>
                        <td>'.$rows['rate'].'</td>
                        <td>'.$rows['qty'].'</td>
                        <td>'.$rows['remaining'].'</td>
                        <td>'.$rows['mfg'].'</td> 
                        <td>'.$rows['expire'].'</td>
                         
                        </tr>'; 
                    
                      $inc++;
                  }// foreach loop ends here
                     ?>

                 </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
</div><!-- /.page-content -->
              <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
                   <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable
         
          ?>
       
      </div><!-- workarea div ends here -->
       <script type="text/javascript">setfocus('name');</script>

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
                                                {"bSortable": false},
                                                null, null, null, null, null, null, null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],

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
                                            "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "csv",
                                            "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "excel",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "pdf",
                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
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
                               


                            })
        </script>
        
        
<script>
    
$(document).keydown(function(e) {

  // Set self as the current item in focus
  var self = $(':focus'),
      // Set the form by the current item in focus
      form = self.parents('form:eq(0)'),
      focusable;

  // Array of Indexable/Tab-able items
  focusable = form.find('input,a,select,button,textarea,div[contenteditable=true]').filter(':visible');

  function enterKey(){
    if (e.which === 13 && !self.is('textarea,div[contenteditable=true]')) { // [Enter] key

      // If not a regular hyperlink/button/textarea
      if ($.inArray(self, focusable) && (!self.is('a,button'))){
        // Then prevent the default [Enter] key behaviour from submitting the form
        e.preventDefault();
      } // Otherwise follow the link/button as by design, or put new line in textarea

      // Focus on the next item (either previous or next depending on shift)
      focusable.eq(focusable.index(self) + (e.shiftKey ? -1 : 1)).focus();

      return false;
    }
  }
  // We need to capture the [Shift] key and check the [Enter] key either way.
  if (e.shiftKey) { enterKey() } else { enterKey() }
});
    </script>
    
