<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Daily Dispatch'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dispatch();
$objchallan = new dealer_sale();
$cls_func_str = 'dispatch'; //The name of the function in the class that will do the job
$myorderby = 'daily_dispatch.dispatch_id DESC'; // The orderby clause for fetching of the data
$myfilter = 'dispatch_id = '; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$sesId =  $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['urole'];
//pre($_SESSION[SESS.'sess']);
$dispatch_num = $myobj->next_dispatch_num();
$dis_num = "BDN{$_SESSION[SESS.'data']['dealer_id']}/{$_SESSION[SESS.'sess']['short_period']}/$dispatch_num";
$dea_id = $_SESSION[SESS.'data']['dealer_id'];

?>

<?php
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	
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
				//unset($_POST);
                                ?>
<!--<script>
                    setTimeout("window.parent.location = 'indexpop.php?option=daily-dispatch-report&showmode=1&mode=1&id=<?=$action_status['rId']?>&actiontype=print'", 500);
                    //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>-->

                                
<?php				/*echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>';*/
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
		$mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '', $orderby='');
                //pre($mystat);
		if(!empty($mystat))
		{
			//echo 'not empty';die;
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$id, $in = $mystat);
			//This will create the post multidimensional array
			//create_multi_post($mystat[$id]['pr_item'], array('itemId'=>'itemId', 'qty'=>'qty'));
			$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
	}									 
}
############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
//echo $funcname;
$mymatch['datepref'] = array('invdate'=>'Invoice Date', 'created'=>'Created');
if(isset($_POST['filter']) && $_POST['filter'] == 'Filter')
{
    //h1('ANKUSH');
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = checkform('filter');	
		if($checkpass)
		{
			//triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
			magic_quotes_check($dbc, $check=true);
			$filter = array();
			$filterstr = array();
			
			if(!empty($_POST['start'])){
				$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(daily_dispatch.dispatch_date,'".MYSQL_DATE_SEARCH."') >= '$start'";
				$filterstr[] = '<b>Start : </b>'.$_POST['start'];
			}
			if(!empty($_POST['end'])){
				$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(daily_dispatch.dispatch_date,'".MYSQL_DATE_SEARCH."') <= '$end'";
				$filterstr[] = '<b>End : </b>'.$_POST['end'];
			}
                    $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
                  //  pre($filter);
		    $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');	
		    $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");  //$myobj->get_item_category_list()
			if(empty($rs))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}

//pre($rs);
//elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])){
//	$ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
//	$rs = $mycurobj->$funcname($filter="$myfilter'$ajaxshowid'",  $records = '', $orderby='');
//}else{
//      $filter = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
//    $rs = $myobj->$funcname($filter,  $records = '', $orderby='');
//}
$rs1 = array();
if(isset($_POST['submit']) && $_POST['submit'] == 'Search')
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
				$filter[] = "DATE_FORMAT(ch_date,'".MYSQL_DATE_SEARCH."') >= '$start'";
				$filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
			}
			if(!empty($_POST['to_date'])){
                            $end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
				$filter[] = "DATE_FORMAT(ch_date,'".MYSQL_DATE_SEARCH."') <= '$end'";
				$filterstr[] = '<b>End : </b>'.$_POST['to_date'];
			}
			if(!empty($_POST['retailer_id'])){  
                            
                         $filter[] = "ch_retailer_id = '$_POST[retailer_id]'";
			}
                       // $filter[] = "ch_retailer_id = '$_POST[retailer_id]'";
                        if(!empty($_POST['location_id'])){                          
                            $filter[] = "dlrl.location_id = '$_POST[location_id]'";
			}                       
                        $filter[] = "dispatch_status = '0'";
                        $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
                       
	  $rs1 = $objchallan->get_challan_list($filter,  $records = '', $orderby =''); //$myobj->get_item_category_list()
	  
	//	echo "ANKUSH PANDEY";exit; 	
                    if(empty($rs1))
				echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
		}
		else
			echo'<span class="awm">'.$fmsg.'</span>';
	}
	else
		echo'<span class="awm">Please do not try to hack the system.</span>';
}
elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])){
	$ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
	$rs1 = $mycurobj->$funcname($filter="$myfilter'$ajaxshowid'",  $records = '', $orderby='');
}
 else {
  $date = date('Ymd');
$filter1[] = "DATE_FORMAT(ch_date,'".MYSQL_DATE_SEARCH."') >= '$date'";
$filter1[] = "DATE_FORMAT(ch_date,'".MYSQL_DATE_SEARCH."') <= '$date'";
$filter1[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
$filter1[] = "dispatch_status = '0'";
$rs1 = $objchallan->get_challan_list($filter1,  $records = '', $orderby =''); //$myobj->get_item_category_list()
	
     
 }

?>
    
<script type="text/javascript">
   
function checkuniquearray(name)
{
	var arr = document.getElementsByName(name);
	var len = arr.length;
	//alert(len);
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
function get_wpoId_item(idata)
{
	if(idata == '') return;
	var pullId = idata;
	getdata_div(pullId, '', 'dealer_challan_order_no', 'po_item_div');
}
window.onload=function (){ search_user(1) };

function search_user(id)
{
	if(id ==1)
	{
		document.getElementById('search1').disabled='';
		//document.getElementById('search1').focus();
		document.getElementById('search2').disabled='true';
		document.getElementById('search3').disabled='true';
                document.getElementById('search4').disabled='true';
	}
	if(id ==2)
	{
		document.getElementById('search1').disabled='true';
		document.getElementById('search2').disabled='';
		document.getElementById('search3').disabled='true';
                document.getElementById('search4').disabled='true';
		document.getElementById('search2').focus();
	}
	if(id ==3)
	{
		document.getElementById('search1').disabled='true';
		document.getElementById('search2').disabled='true';
		document.getElementById('search3').disabled='';
                document.getElementById('search4').disabled='';
		document.getElementById('search3').focus();
	}
	
}
</script>
    <div id="workarea">
  
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
<!--        <legend class="legend" style=""><?php echo $forma; ?></legend>-->
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="dealer_id" value="<?php echo $_SESSION[SESS.'data']['dealer_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
             <td>Dispatch No<br>
               
             <input type="text"  name="dispatch_no"  value="<?php if(isset($_POST['dispatch_no'])) echo $_POST['dispatch_no']; else echo $dis_num; ?>"/>
             </td>
            <td><span class="star">*</span>Dispatch Date<br />
             <input type="text" id="invdate" name="dispatch_date"  value="<?php if(isset($_POST['dispatch_date'])) echo $_POST['dispatch_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="Dispatch Date" />
            </td>
<!--             <td><span class="star">*</span>Dispatch Beat<br />
            <?php db_pulldown($dbc, 'dispatch_beat', "Select `route_id`,`route_name` from `dispatch_beat` where `dealer_id` ='$dea_id'", true, true, ''); ?> 
             
             </td>-->
            <td><span class="star">*</span> Van Number<br />
                <!--<input placeholder="VAN Number" type="text" class="van" name="van_no" value="<?php// if(isset($_POST['van_no'])) echo $_POST['van_no']; ?>">-->
            
             
              
                <select name="van_no">
             <?php
             
                $q = "Select van_no from van where dealer_id ='$dea_id'";
               // echo $q;
                $rr = mysqli_query($dbc,$q);
                while($re = mysqli_fetch_assoc($rr))
                {
             echo'<option value="'.$re['van_no'].'">'.$re['van_no'].'</option>';  
                }  
             // db_pulldown($dbc, "van_no", $q, true,true,'id="van_no" lang="out"','',TRUE,TRUE,$_POST['van_no']);
              ?>
                  </select>
            <input type="hidden" id="vanId" name="vanId" value="<?php if(isset($_POST['vanId'])) echo $_POST['vanId']; ?>">
            </td>
         </tr>
         <tr>
           <td colspan="6"><br></td>
         </tr>
         <tr>
            <tr>
                <td><strong>Name</strong><br />
                   <!-- <div style="float:left; "><input type="radio" style="float:left" name="search" checked="checked"  onclick="return search_user(1)" /> -->
                 
                        
                        <?php db_pulldown($dbc, 'retailer_id', "SELECT id AS retailer_id, name AS retailer_name FROM `retailer` WHERE `dealer_id` = '$dea_id'", true, true, 'class="chosen-select"'); ?> 
                    </div>
                </td>
                
                 <td><strong>Beat</strong><br />
                <!-- <div style="float:left; "> <input type="radio" style="float:left" name="search"  onclick="return search_user(2)" />  -->
                 
                      <?php db_pulldown($dbc, 'location_id', "SELECT location_id as beat_id,(select name from location_5 where id=dealer_location_rate_list.location_id) as beat_name FROM `dealer_location_rate_list` WHERE `dealer_id` = '$dea_id'", true, true, ''); ?> 
            </div>
                </td>
                
            <td><strong>From Date</strong><br />
                <div style="float:left; "> 
                    <input type="text" id="from_date" name="from_date"  value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="Form Date" />
                </div>
            </td>
            
            <td><strong>To Date</strong><br />
                <div style="float:left; "> 
                    <input type="text" id="to_date" name="to_date"  value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="To Date" />
                </div>
            </td>
           
             <td colspan="2"><br>
                 <input id="mysave"class="btn btn-sm btn-primary"  type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Search';?>" />
                 <!-- <input class="btn btn-sm btn-success" onclick="window.document.location = 'index.php?option=daily-dispatch-report';" type="button" value="Report" title="Daily Dispatch Report" /> -->
             </td>
            </tr>
            </table>
   <?php if(!empty($rs1))  { 
       ?> 
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header"> 
                   Search Order Details &nbsp;&nbsp;&nbsp; 
         
                </div>

                <!-- div.dataTables_borderWrap -->
                <div>
                    <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr class="search1tr">
                            <th><input onclick="selectCheckBoxes('checkall', 'chk[]');" type="checkbox" id="checkall"></th>
                        <th>S.No</th>
                      <th>Invoice No</th>
                      
                      <th>Invoice Date</h>
                      <th>Retailer Name</th>
                      <th>Quantity In Gram</th>
                       <th>Quantity In Kg.</th>
                    </tr>
                    </thead>  <tbody> 
                   <?php
                 $inc = 1;
             $quantity=0;
                      foreach($rs1 as $key=>$value)
                      {
                         $quantity =  $value['challan_item']['qty']+$value['challan_item']['free_qty']+$quantity;
                          $where = 'id = '.$value['ch_retailer_id'];
                          echo'
                      <tr>
                      <td><input type="checkbox" name="chk[]" value="'.$value['id'].'"></td>
                        <td>'.$inc.'</td>
                        <td><strong>'.$value['ch_no'].'</strong></td>
                        
                        <td>'.date('d-m-Y',strtotime($value['ch_date'])).'</td>
                        <td>'. myrowval('retailer', 'name',$where).'</td>';
                       //   pre($value);
                          $ch = $value['ch_no'];
                     $query = "select id from challan_order where ch_no = '$ch'"; 
                   //  h1($query);
                       $result = mysqli_query($dbc,$query);
                       $row = mysqli_fetch_assoc($result);
                      $chid = $row['id'];
                      
                       $query1 = "select qty,free_qty,weight from `challan_order_details` inner join catalog_product on 
                           challan_order_details.product_id=catalog_product.id
                           WHERE `ch_id` = '$chid'"; 
                   // h1($query1);
                       $quan = array();
                       $result1 = mysqli_query($dbc,$query1);
                     while($row1 = mysqli_fetch_assoc($result1)){
                          $qtyy = $row1['qty'];
                     $fqtyy = $row1['free_qty'];
                     $weight = $row1['weight'];
                     $quan[] = ($qtyy+$fqtyy)*$weight;
                     }
                    $quant = array_sum($quan);
                    $quanti = $quant/1000;
                         echo' <td width="100px">'.$quant.'Gm.</td>';
                          echo' <td width="100px">'.$quanti.'Kg.</td>';     
                      echo' </tr>';
                        $inc++;
                      //  $quantity = 0;
                      }
                     ?>
                        
                    <tr>
                        <td colspan="7" style="float:center">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input id="mysave" class="btn btn-primary" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
<!--                          <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />-->
                        </td>
                    </tr>
                </tbody>
                    </table>
                </div>
            </div>
        </div>
   <?php }?>
        
        </fieldset>
    </form>

        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
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

        <script type="text/javascript">
        	$(function(){
        	  if(!ace.vars['touch']) {
        	       $('.chosen-select').chosen({allow_single_deselect:true});
        	  }
        	})
        </script>

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
                                                null, null, null, null,
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
        
    
