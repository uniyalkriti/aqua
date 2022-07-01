<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
$forma = 'GST RETURN SUMMARY'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'gst_wise'; //The name of the function in the class that will do the job
$myorderby = 'challan_order.ch_date DESC'; // The orderby clause for fetching of the data
$myfilter = 'challan_order.id = '; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
?>
<div id="breadcumb"><a href="#">Sales</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
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
                //unset($_SESSION[SESS.'securetoken']);         
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
        //This will containt the pr no, pr date and other values
        $funcname = 'get_' . $cls_func_str . '_list';
        $mystat = $myobj->$funcname($filter = "$myfilter'$id'", $records = '', $orderby = ''); // $myobj->get_item_category_list()
        
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
$mymatch['datepref'] = array('=Select=','ch_date' => 'Challan Date', 'created' => 'Created');
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
                $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') >= '$start'";
                $filterstr[] = '<b>Start : </b>' . $_POST['start'];
            }
            if (!empty($_POST['end'])) {
                $end = get_mysql_date($_POST['end'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') <= '$end'";
                $filterstr[] = '<b>End : </b>' . $_POST['end'];
            }
            if (!empty($_POST['ch_no'])) {
               /* $filter[] = "ch_no = '$_POST[ch_no]'";
                $filterstr[] = '<b>Challan No.  : </b>' . $_POST['ch_no'];*/
            }
             //$filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname($filter,$key, $records = '', $orderby = ""); // $myobj->get_item_category_list()
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    
    $rs = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'",$key, $records = '', $orderby = '');
} else {

        $filter= array();
     $start = get_mysql_date(date('d/m/Y'), '/', $time = false, $mysqlsearch = true);
     $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') >= '$start'";
      $end = get_mysql_date(date('d/m/Y'), '/', $time = false, $mysqlsearch = true);
    $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') <= '$end'";
     /*$filter = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";*/
    $rs = $myobj->$funcname($filter,$key, $records = '', $orderby = "");
}
dynamic_js_enhancement();
//pre($rs);
?>
<?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
            require_once('challan-gst-print.inc.php');
            exit();
            break;
            case'printbill':
        //echo getcwd();
          require_once('modules/dispatch/challan-print.inc.php');
            exit();
            break;
        default:
            $filepath = BASE_URI_ROOT . ADMINFOLDER . SYM . 'modules' . SYM . 'sales' . SYM . 'invoice' . SYM . 'tax_inv_sale_report.php';
            if (is_file($filepath))
                require_once($filepath);
            exit();
            break;
    }//switch($_GET['actiontype']){ ends
}
//This block of code will help in the print work ens
?>
    <?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1) { // to show the form when and only when needed ?>
        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray();">
            <fieldset>
                <legend class="legend" style=""><?php echo $forma; ?></legend>
                <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />   
                <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
                    <tr>
                        <td><span class="star">*</span>Invoice no<br>
                            <input type="text" class="read" readonly="readonly" name="ch_no" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no'];
    else echo 'ch_' . $_SESSION[SESS . 'data']['dealer_id'] . '_' . $myobj->next_challan_num(); ?>">
                        </td>
                         <td>Retailer<br>                       
                        <input id="retailer_name" name="retailer_name" type="text" value="<?php echo $_POST['retailer_name']; ?>">
                        <input id="retailer_id" name="retailer_id" type="hidden" value="<?php echo $_POST['ch_retailer_id']; ?>">
                    </td>
                    <td>Invoice Date<br> 
                            <input type="text" class="datepicker" name="ch_date" value="<?php if (isset($_POST['ch_date'])) echo date('d-m-Y',strtotime($_POST['ch_date']));
    else echo date('d/m/Y'); ?>">
                             <input id="company_id" name="company_id" type="hidden" value="<?php echo $_POST['company_id']; ?>">
                        </td>
                        <td colspan="2">Remarks<br>
                            <textarea name="remark"><?php if (isset($_POST['remark'])) echo $_POST['remark']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><div class="subhead1">Challan Item Details</div></td>
                    </tr>

                    <tr>
                        <td colspan="5">
                            <div id="po_item_div">
                            <?php // if(isset($heid)){?>
                                <!-- table to capture the address field starts -->
                                <table width="100%"  id="mytable">
                                    <tr class="thead" style="font-weight:bold;">
                                        <td>S.NO</td>
                                        <td>Item Name</td>
<!--                                         <td>Batch No.</td>-->
                                        <td>Avlb. Stock</td>
                                        <td>M.R.P</td>
                                        <td>Quantity</td>                                               
                                        <td>Rate</td>                                               
                                        <td>Sch. Quantity</td>
                                        <td>C.D.</td>
                                        <td>C.D Type</td>
                                        <td>CD.Amt</td>
                                        <td>VAT%</td>
                                        <td>VAT. Amt</td>
                                        <td>Amount</td>
                                        <td style="width:40px;">&nbsp;</td>
                                    </tr>
    <?php
    if (isset($heid)) {
        $inc = 1;
        foreach ($_POST['challan_item'] as $key => $value) { //pre($value); 
            ?>
                                            <tr class="tdata">
                                                <td class="myintrow"><?php echo $inc; ?></td>
                                                <td>
            <?php
            //$q = "SELECT id, name from catalog_product where company_id = '$_POST[company_id]' ORDER BY id ASC";
         echo   $q = ' SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where cp.company_id ="'.$_POST[company_id].'" AND dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].'';
            db_pulldown($dbc, 'product_id[]', $q, true, true, ' id="1" onchange="custom_function(this.value,this.id);" onblur="getajaxdata(\'get_product_qty\', \'mytable\',event);"', '', $value['pid']);
            ?> 
                                                    <input type="hidden" name="user_id[]" value="<?php echo $value['user_id']; ?>">
                                                </td>
<!--                                                <td>
                                                        <?php
                                                       $q = "SELECT batch_no,CONCAT(batch_no,' (',quantity,')') as batch  from user_primary_sales_order_details where product_id ='$value[product_id]' AND expiry_date > CURDATE() ";
                                                        db_pulldown($dbc, 'batch_no[]', $q, TRUE, TRUE, 'onchange="getajaxdata(\'get-stock\', \'mytableabc\',event);"','',$value['batch_no']);
                                                        ?>
                                                    </td>-->
                                                    <td>
                                                        <input id="aval_stock" type="text" name="aval_stock[]" onchange="challan_calculate();" id="aval_stock1"  value="<?php echo $myobj->calculate_available_stock($value['product_id']); ?>"  />
                                                    </td>

                                                <td><input id="mrp<?php echo $i; ?>" type="text"  lang="mrp" name="mrp[]" value="<?php echo $value['mrp']; ?>"></td>
                                                <td><input id="ch_qty<?php echo $i; ?>" onchange="product_calculate()" type="text"  lang="quantity" name="quantity[]" value="<?php echo $value['qty']; ?>"></td>
                                                <td><input id="r<?php echo $i; ?>" onblur="product_calculate();" type="text" name="rate[]" value="<?php echo $value['product_rate']; ?>"></td>
                                                <td><input type="text" name="scheme[]" value="<?php echo $value['free_qty']; ?>"></td>
                                                <td><input type="text" name="cd[]" onchange="product_calculate()" value="<?php echo $value['cd']; ?>"></td>
                                                <td>
                                                    <select name="cd_type[]" lang="cdtype" onchange="product_calculate()">
                                                        <option value="">== Select ==</option>
                                                        <option value="1" <?php if($value['cd_type']==1) echo 'selected = "selected"'; ?> >%</option>
                                                        <option value="2" <?php if($value['cd_type']==2) echo 'selected = "selected"'; ?>>Amount</option>
                                                        <option value="3" <?php if($value['cd_type']==3) echo 'selected = "selected"'; ?>> Kg </option>
                                                    </select>
                                                </td>
                                                <?php 
                                                if($value['cd_type']==1){
                                                    $cd = (($value['qty']*$value['product_rate'])*$value['cd'])/100;
                                                }elseif($value['cd_type']==2){
                                                     $cd = ($value['cd']);
                                                }
                                                     $vat = ((($value['qty']*$value['product_rate'])-$cd)*$value['tax'])/100;
                                                     $total = ($value['qty']*$value['product_rate'])-$cd+$vat;
                                                ?>
                                                <td><input type="text" name="cd_amt[]" value="<?php echo $cd ; ?>"></td>
                                                <td><input type="text" name="vat[]" onchange="product_calculate()" value="<?php echo $value['tax']; ?>"></td>
                                                <td><input type="text" name="vat_amt[]" value="<?php echo $vat; ?>"></td>
                                                <td><input type="text" name="amount[]" value="<?php echo $total; ?>"></td>

            <!--                 <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/><?php if ($inc != 1) { ?><img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php } ?></td>-->
                                                <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/>
                                            </tr>
            <?php $inc++;
        }
    } 
    }else { ?>


<!--------------------------Showmode 1 End------------------------------------------>

<form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform1" onsubmit="return checkForm('genform1');">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr id="mysearchfilter">
            <td>
                <fieldset>                            
                    <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
                    <table>
                        <tr>
                            <?php /*<td width="10%"><span class="star">*</span>DatePref<br />

                                <?php echo arr_pulldown('datepref', $mymatch['datepref'], '', true, true, '', false, ' '); ?>
                            </td>*/ ?>
                            <td width="15%"><span class="star">*</span>Start<br />  
                                <input type="text" class="qdatepicker" name="start" value="<?php if (isset($_POST['start']))
                                    echo $_POST['start'];
                                else
                                    echo date('d/m/Y');
                                ?>"/>
                            </td>
                            <td width="15%"><span class="star">*</span>End<br />    
                                <input type="text" class="qdatepicker" name="end" value="<?php if (isset($_POST['end']))
                                    echo $_POST['end'];
                                else
                                    echo date('d/m/Y');
                                ?>" onblur="this.value = ucwords(trim(this.value));"/>
                            </td>                  
<?php /*
                            <td width="10%">Challan No<br />
                                <input type="text" name="ch_no" id="invnum" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no']; ?>" /> 
                            </td>*/?>
                            <td width="41%"><br>
                                <input class="btn btn-sm btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
                                <input class="btn btn-sm btn-danger" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
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
                   GST Wise Summary List
                    <div class="pull-right tableTools-container"></div>
                </div>

                <!-- div.table-responsive -->
                
                  <!-- div.dataTables_borderWrap -->
                   <div>
                       <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>                            
                            <tr>       
                               
                                <th class="sno">S.No</th>
                                <th>Description</th>
                                
                                <th>Taxable Amount</th>
                                <th>Rate Of Tax</th>
                                <th>CGST Tax Amt.</th>
                                <th style="text-align:center;">
                                    SGST Tax Amount
                                </th>
                                <th style="text-align:center;">
                                    Total Tax Amount
                                </th>
                                <th>Total Amount</th>  
                            </tr>


                          <?php /* <tr valign="top" class="search1tr">
                                <th >CGST %</th> 
                               <th >CGST Amt</th> 
                                <th >SGST %</th> 
                               <th >SGST Amt</th> 
                            </tr>*/ ?>
                        </thead>
                        
                        <tbody>
                            <?php
                            $inc = 1;   
                            /*pre($rs);  */                       
                            foreach ($rs as $key => $rows) {
                              // pre($rows);
                             $uid = $rows['id'];
                             $cid = $rows['cid'];
                             $cgst = ( $rows['vat_amt']/2 );
                            $sgst = ( $rows['vat_amt'] - $cgst );
                $total_amount = ($rows['vat_amt'] + $rows['taxable_amt'] );

             $printlink = '<a class="iframef" title="print Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&start=' . $start . '&end=' . $end . '&status='.$inc.'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>'; 

             //$rows['postat'] == 1 ? '<span class="seperator">|</span> <a class="iframef" title="print PO '.$uidname.'" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>' : '';
                
//                             $printlink = '<span class="seperator"></span> <a class="iframef" target="_blank" title="Edit Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '&actiontype=print"><img src="./images/b_edit.png"></a>';
                             
                            ?>
                                <tr>
                               
                                <td>
                                    <?=$inc?>
                                </td>
                                <td>
                                   <?=$rows['Perticular']?>
                                </td>
                                
                                <td><?=$rows['taxable_amt']?></td>
                                  <td>
                                    <?php echo '<a target="_blank" href="index.php?option=bill-detailed-gst&start='.$start.'&end='.$end.'&tax='.$rows['tax'].'">'; ?><?php echo $rows['tax'];?></a>
                                    </td>
                                <td><?php echo my2digit($cgst); ?></td>
                                <td><?php echo my2digit($sgst); ?></td>
                                <td><?php echo my2digit($rows['vat_amt']); ?></td>
                                <td><?php echo round($total_amount);?></td>
                                
                                </tr>
                            <?php  $inc++;
            } ?>

                         
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
<?php } ?>
<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div><!-- /.main-container -->       
  
     
    </body>
</html>
   <script type="text/javascript">setfocus('partycode');</script>
<!--      <script src="assets/js/jquery-2.1.4.min.js"></script>-->
 <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
        <script src="assets/js/dataTables.buttons.min.js"></script>
        <script src="assets/js/buttons.flash.min.js"></script>
        <script src="assets/js/buttons.html5.min.js"></script>
        <script src="assets/js/buttons.print.min.js"></script>
        <script src="assets/js/buttons.colVis.min.js"></script>
        <script src="assets/js/dataTables.select.min.js"></script>
        <script src="assets/js/jszip.min.js"></script>
        <script src="assets/js/pdfmake.min.js"></script>
        <script src="assets/js/vfs_fonts.js"></script>
        <!-- ace scripts -->
        <script src="assets/js/ace-elements.min.js"></script>
        <script src="assets/js/ace.min.js"></script>
<script>   
            $(document).ready(function () {
                   $('#btn').click(function () {
                       window.opener.location.reload(true);
                       window.close();
                   });
               });
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
                                                null, null, null, null, null,null,
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
                                            autoPrint: true,
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
