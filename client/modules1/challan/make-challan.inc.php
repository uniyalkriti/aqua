<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>

<?php
//include'../client/modules/table.php';
$forma = 'INVOICE'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
$myorderby = 'challan_order.ch_no DESC'; // The orderby clause for fetching of the data
$myfilter = 'challan_order.id = '; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
?>
<div id="breadcumb"><a href="#">Sales</a> <?php echo $incraquo ?> <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
    <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/po.php');  ?>
</div>

<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################

function checkform($mode = 'add', $id = '') {
    global $dbc;
    if ($mode == 'filter')
        return array(TRUE, '');
   
    return array(TRUE, '');
}

############################# code for SAVING data starts here ########################
if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_save';

            $action_status = $myobj->$funcname(); // $myobj->item_category_save()
            if ($action_status['status']) {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                //show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
                unset($_POST);
                /* echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>'; */
                //unset($_SESSION[SESS.'sec$myobjuretoken']); 		
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code for editing starts here ########################
if (isset($_POST['submit']) && $_POST['submit'] == 'Update') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'edit', $id = $_POST['eid']);
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = 'direct_challan_edit';
            $action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
            if ($action_status['status']) {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                //unset($_SESSION[SESS.'securetoken']); 
                //show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
                unset($_POST);
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code to get the stored info for editing starts here ########################
if (isset($_GET['mode']) && $_GET['mode'] == 1) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
       $id = $_GET['id'];
      // h1($id);exit;
        //This will containt the pr no, pr date and other values
        $funcname = 'get_' . $cls_func_str . '_list';
        $mystat = $myobj->$funcname($filter = "$myfilter'$id'", $records = '', $orderby = ''); // $myobj->get_item_category_list()
        // pre($mystat);
        if (!empty($mystat)) {
            //geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
            geteditvalue_class($eid = $id, $in = $mystat);
            //This will create the post multidimensional array
            //create_multi_post($mystat[$id]['pr_item'], array('itemId'=>'itemId', 'qty'=>'qty'));
            $heid = '<input type="hidden" name="eid" value="' . $id . '" />';
        } 
    }
}

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_' . $cls_func_str . '_list';
$mymatch['datepref'] = array('ch_date' => 'Challan Date', 'created' => 'Created');
if (isset($_POST['filter']) && $_POST['filter'] == 'Filter') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $filter = array();
            $filterstr = array();
            if (!empty($_POST['datepref'])) {
                $filterstr[] = '<b>DatePref : </b>' . $mymatch['datepref'][$_POST['datepref']];
            }
            if (!empty($_POST['start'])) {
                $start = get_mysql_date($_POST['start'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT(ch_date,'" . MYSQL_DATE_SEARCH . "') >= '$start'";
                $filterstr[] = '<b>Start : </b>' . $_POST['start'];
            }
            if (!empty($_POST['end'])) {
                $end = get_mysql_date($_POST['end'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT(ch_date,'" . MYSQL_DATE_SEARCH . "') <= '$end'";
                $filterstr[] = '<b>End : </b>' . $_POST['end'];
            }
            
            if (!empty($_POST['retailer_id'])) {
                $filter[] = "ch_retailer_id = '".$_POST['retailer_id']."'";
                //$filterstr[] = '<b>Retailer : </b>' . $_POST['ch_no'];
            }
            if (!empty($_POST['ch_no'])) {
                $filter[] = "ch_no like  '%$_POST[ch_no]%'";
                $filterstr[] = '<b>Challan No.  : </b>' . $_POST['ch_no'];
            }
           // pre($filter);
             $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby"); // $myobj->get_item_category_list()
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    
    $rs = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} else {
     $filter = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
    $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby");
    
}
dynamic_js_enhancement();

?>

<script type="text/javascript">
    $(function() {
        $("#partycode").autocomplete({
            source: "./modules/ajax-autocomplete/party/ajax-vendor-code.php"
        });
        $("#partyname").autocomplete({
            source: "./modules/ajax-autocomplete/party/ajax-vendor-name.php"
        });
    });

    function get_wpoId(idata)
    {
        if (idata == '')
            return;
        var pullId = idata;
        //filling the pulldown
        fetch_location(pullId, '', 'wpoId', 'get_wpoId');
    }
    function get_wpoId_item(idata)
    {
        if (idata == '')
            return;
        var pullId = idata;

        //getdata_div(pullId, 'po_item_div', 'wpoId_item_invoice', 'po_item_div');
    }
//    function custom_function(pullId, id) {
//        var batchno = 'batch' + id;
//        fetch_location(pullId, '', batchno, 'get_batch_no');
//    }
    $('#itemId').change(function() {
        //    alert( $(this).closest("td").next().find('select').attr("id") );
    });

    var i = 1;

$(document).on('click', '.addbutton', function(){
        //if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
        $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
            $(this).val('').attr('id', function(_, id) {
                return id + i
            });
        }).end().appendTo("#mytable");
        i++;
        $('#mytable tr.tdata').each(function(j) {
            $(this).find('td.myintrow:first').html((j + 1) * 1);
        });
    });
$(document).on('click', '.removebutton', function(){
        $(this).closest('tr').remove();
        return false;
    });
function checkuniquearray()
{
    var arr = document.getElementsByName('product_id[]');
    var len = arr.length;
    var v = checkForm('genform');
    if (v)
    {
        for (var i = 0; i < len; i++)
        {                        // outer loop uses each item i at 0 through n
            for (var j = i + 1; j < len; j++)
            {
                // inner loop only compares items j at i+1 to n
                if (arr[i].value == arr[j].value)
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

function custum_function(pid, pvalue, event) 
{
        var batchno = $("#" + pid).closest("td").next().find("select").attr("id");
        getajaxdata('get_product_vat', 'mytable', event);
//        setTimeout(function() {
//            fetch_location(pvalue, 'progress_div', batchno, 'get_batch_no');
//        }, 300);
        setTimeout(function() {
            getajaxdata('get_product_mrp', 'mytable', event);
        }, 400);
        
         setTimeout(function() {
            getajaxdata('get-stock', 'mytable', event);
        }, 800);
         setTimeout(function() {
            getajaxdata('get-calculate-rate', 'mytable', event);
        }, 1000);
    }

function trade_disc_calculate()
{
	var qty = document.getElementsByName('quantity[]');
	var r = document.getElementsByName('rate[]');
        var tds_amt = document.getElementsByName('trade_disc_amt[]');
        var tds_type = document.getElementsByName('trade_disc_type[]');
        var tds_val = document.getElementsByName('trade_disc_val[]');
        var ttl_amt = document.getElementsByName('ttl_amt[]');
       //prodvalue
	// alert(qty.length);

	for(var i = 0; i<qty.length; i++)
	{
            if(tds_type[i].value == 1){
                var res = ( r[i].value  ) * (tds_val[i].value/100);
		tds_amt[i].value = res.toFixed(2);
                ttl_amt[i].value = ( r[i].value - tds_amt[i].value ) * qty[i].value;
            }else{
                var res = tds_val[i].value;
		tds_amt[i].value = res;
                ttl_amt[i].value = ( r[i].value - tds_amt[i].value ) * qty[i].value;
            }
      	}
}

function get_available_rate(mrp_id,mrp_value,event)
{
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
     //	alert(prod_id);
        var pvalue = document.getElementById(prod_id).value;
            getajaxdata('get-product-rate', 'mytable', event,pvalue);
        mrp_change(mrp_value);
    
}

function mrp_change()
{
	var base_price = document.getElementsByName('base_price[]');
        var r = document.getElementsByName('rate[]');
       //prodvalue
	
	for(var i = 0; i<base_price.length; i++)
	{
                var res = (base_price[i].value - ( base_price[i].value * (18/100) ))*100/105;
		r[i].value = res.toFixed(2);
              //  alert(r[i].value);
      	}
}


function print_all_selected(id,chkval,chkname){
    var chkid = id;
    var chkval = chkval;
   // alert(chkval);
    var printall_href = document.getElementById('print_all');

    if(document.getElementById(chkid).checked){
       // alert(chkval);
        printall_href.href += "-"+chkval ;
    }else{
                //var phref = printall_href.href;
        printall_href.href = printall_href.href.replace("-"+chkval,'');
        printall_href.href = printall_href.href.replace(chkval,'');
        var p = printall_href.href;
    }
}


</script>

<div id="workarea">
<?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
            require_once('challan-print.inc.php');
            exit();
            break;
        default:
            $filepath = BASE_URI_ROOT . ADMINFOLDER . SYM . 'modules' . SYM . 'sales' . SYM . 'invoice' . SYM . 'invoice-print.inc.php';
            if (is_file($filepath))
                require_once($filepath);
            exit();
            break;
    }//switch($_GET['actiontype']){ ends
}
//This block of code will help in the print work ens
?>
<form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform1" onsubmit="return checkForm('genform1');">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr id="mysearchfilter">
            <td>
                  <fieldset>                          
            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
            <table>
                <tr>
                    <td width="10%" class="hidden"><span class="star">*</span>DatePref<br />	
<?php echo arr_pulldown('datepref', $mymatch['datepref'], '', true, true, '', false, 'Select Pref Date'); ?>
                    </td>
                    <td width="10%"><span class="star">*</span>Start<br />	
                        <input type="text" class="qdatepicker" name="start" value="<?php if (isset($_POST['start'])) echo $_POST['start'];
else echo '01/' . date('m/Y'); ?>"/>
                    </td>
                    <td width="10%"><span class="star">*</span>End<br />	
                        <input type="text" class="qdatepicker" name="end" value="<?php if (isset($_POST['end'])) echo $_POST['end'];
else echo date('d/m/Y'); ?>" onblur="this.value = ucwords(trim(this.value));"/>
                    </td>                  
                    <td width="10%">Retailer<br/>
                        <?php
                       
                         $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                            $q = "SELECT retailer.id as id ,concat(retailer.name,' [',location_5.name,']') as `name` from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where dealer_id='$dealer_id' order by retailer.name asc";
                            db_pulldownsmall($dbc, 'retailer_id', $q, true, true, '','==Please Select==',$_POST['retailer_id']);
                        ?>
                    </td>
                    <td width="10%">Challan No<br />
                        <input type="text" name="ch_no" id="invnum" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no']; ?>" /> 
                    </td>
                    <td width="25%"><br/>
                        <input class="btn btn-sm btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
                        <!--<input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->
<!--                                        <input onclick="$.colorbox({href: 'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe: true, width: '95%', height: '95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                <!-- this table will contain our form filter code ends -->           
            </td>
        </tr>
    </table>
</form>    
    
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
               
                <div class="table-header">
                    <a class="iframef" title="print All Selected Invoice" id="print_all" href="index.php?option=make-challan&showmode=1&mode=1&actiontype=print&id="><i class="fa fa-print" style="color:#fff;"></i><span style="color:#fff;"> Invoice List</span></a>
                   
                    <div class="pull-right tableTools-container"></div>
                </div>

                <!-- div.table-responsive -->
                
                  <!-- div.dataTables_borderWrap -->
                <div>
                    <style> th { background-color: #C7CDC8; color:#000; }</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>                           
                            <tr>       
                                <th class="center">
                                   #
                                </th>
                                <th>S.No</th>
                                <th>Invoice Date</th>
                                <th>Invoice No.</th>
                                <th>Retailer</th>
                                <th>Item Details</th>
                                <th>Discount</th>
                                <th>Amount</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        
                        
                         <tbody>
                              <?php
                              $inc = 1;
                             $surcharge=  myrowval('state', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
                            foreach ($rs as $key => $rows) {
                                $bg = ($bg == TR_ROW_COLOR1 ? TR_ROW_COLOR2 : TR_ROW_COLOR1); // to provide different row colors(member_contacted table)
                                $uid = $rows['id'];                
                                $uidname = $rows['ch_no'];                              
                                 $frz_date = date("Y-m-d", strtotime($rows['ch_date'].'+2 days'));
                                 if($frz_date>=date('Y-m-d')){
                                 $editlink = '<a target="_blank" class="iframef" href="index.php?option=challan-edit&showmode=1&mode=1&page=Edit Invoice/Challan&id='. $uid . '"><img src="./images/b_edit.png"></a>';
                                 }  else {
                                 $editlink = '';    
                                 }
                                //$schedulelink = $rows['potype'] == 2 ? '<span class="seperator">|</span> <a class="iframef" title="Material Schedule against PO '.$uidname.'" href="index.php?option=po-material-schedule&showmode=1&poid='.$uid.'"><img src="../icon-system/i16X16/osent.png"></a>' : '';

$printlink = '<span class="seperator">|</span> <a class="iframef" title="print Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>';

//$rows['postat'] == 1 ? '<span class="seperator">|</span> <a class="iframef" title="print PO '.$uidname.'" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>' : '';
                                $deletelink = ''; 
                                if ($auth['del_opt'] != 1)
                                    $deletelink = '';
                            ?>
                                <tr>
                                   <td>
                                     
                                     <input type="checkbox" name="printinvid[]" id="chk<?=$uid?>" value="<?=$uid?>" onclick="print_all_selected(this.id,this.value,this.name)"/>
                                 </td>
                                    
                                <td>
                                    <?=$inc?>
                                </td>
                                 <td>
                                    <?=$rows['ch_date']?>
                                </td>
                                 <td>
                                    <?=$uidname?><div style="display:none" id="delDiv<?=$uid?>"></div>
                                </td> 
                                <td>
                                    <?=$rows['retailer_id']?>
                                </td>
                                <td>
                                   <table style="border:1px; border-collapse:collapse">
                                    <tr style="font-weight:bold;">
                                     <td style="border:1px; width:100px;background-color:#1E82DA; color:#fff; padding:3px;">Item Name</td>
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;">Rate</td>                               
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;">Qty(In Pcs.)</td>
                                     <!--                                    <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;">Sch.Qty</td>                                -->
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;"> TD.Amt </td>
                                     
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;"> CD.Amt </td>
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;">GST.Amt</td>
                                     <td style="border:1px; background-color:#1E82DA; color:#fff; padding:3px;">Total Amt.</td>

                                     <?php   $taxamount = 0;
                                     $total = 0;
                                     foreach ($rows['challan_item'] as $inkey => $invalue) {
                                        // pre($invalue);
                                         $amt = ($invalue['qty'] * $invalue['product_rate']);
                                         if ($invalue['cd_type'] == 1) {
                                             $cd_amt = ($amt * $invalue['cd']) / 100;
                                         } elseif ($invalue['cd_type'] == 2) {
                                             $cd_amt = ($invalue['cd']);
                                         }
                                         if ($invalue['dis_type'] == 1) {
                                             $td_amt = ($amt * $invalue['dis_percent']) / 100;
                                         } elseif ($invalue['dis_type'] == 2) {
                                             $td_amt = ($invalue['dis_amt']);
                                         }


                                           //$vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;
                                         if ($invalue['tax'] == 0) {
                                             $vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;
                                         } else {
                                             $vat_amt1 = $amt * 5 / 100;
                                         }

                                         $vat_amt = $vat_amt1 * ($surcharge / 100);
                                         $surcharge_amt = $vat_amt;
                                         ?> 
                                         <style>tr.bordered {border-bottom: 1px solid #000;}</style>
                                         <tr class="bordered">
                                           <td style="border:1px;"><strong><?=$invalue['name']?></strong></td>  
                                           <td style="border:1px;text-align: center;"><?=$invalue['product_rate']?></td>  
                                           <td style="border:1px;text-align: center;"><?=$invalue['qty']?></td>  
                                           <!--                                     <td style="border:1px;"><?=$invalue['free_qty']?></td> -->
                                           <td style="border:1px;text-align: center;"><?=my2digit($invalue['dis_amt'])?></td> 
                                           <td style="border:1px;text-align: center;"><?=my2digit($invalue['cd_amt'])?></td>

                                           <td style="border:1px;text-align: center;"><?=my2digit($invalue['vat_amt'])?></td>  
                                           <td style="border:1px;text-align: center;"><?=my2digit($invalue['taxable_amt'])?></td>  

                                       </tr>
                                       <?php   $total += $invalue['taxable_amt'] ;
                                   }?>
                               </table>
                                    </td>
                            <td><strong><?=my2digit($rows['discount_amt'])?></strong></td>
                            <td><strong><?=my2digit($rows['amount'])?></strong></td>
                            <td class="options"><?=$editlink . $printlink?></td>
                                   
                             </tr>                                
                          
                             <?php $inc++; } ?>
                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
</div><!-- /.page-content -->
</div>
</div><!-- /.main-content -->

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div><!-- /.main-container -->       
  

    </body>
</html>
     
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
                                               // {"bSortable": false}
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


//
//
//
//                                myTable.on('select', function (e, dt, type, index) {
//                                    if (type === 'row') {
//                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
//                                    }
//                                });
//                                myTable.on('deselect', function (e, dt, type, index) {
//                                    if (type === 'row') {
//                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
//                                    }
//                                });
//
//
//
//
//                                /////////////////////////////////
//                                //table checkboxes
//                                $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
//
//                                //select/deselect all rows according to table header checkbox
//                                $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
//                                    var th_checked = this.checked;//checkbox inside "TH" table header
//
//                                    $('#dynamic-table').find('tbody > tr').each(function () {
//                                        var row = this;
//                                        if (th_checked)
//                                            myTable.row(row).select();
//                                        else
//                                            myTable.row(row).deselect();
//                                    });
//                                });

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
