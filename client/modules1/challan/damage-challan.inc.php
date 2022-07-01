<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
//include'../client/modules/table.php';
$forma = 'INVOICE'; // to indicate what type of form this is
$formaction = $p;
$myobj = new damage_sale();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
$myorderby = 'damage_order.ch_no DESC'; // The orderby clause for fetching of the data
$myfilter = 'damage_order.id = '; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
?>
<div id="breadcumb"><a href="#">Sales</a> &raquo; <a href="index.php?option=<?php  $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
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
        //pre($mystat);
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
$mymatch['datepref'] = array('ch_date' => 'Challan Date', 'dispatch_date' => 'Created');
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
            if (!empty($_POST['start']) && !empty($_POST['datepref'])) {
                $start = get_mysql_date($_POST['start'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT({$_POST['datepref']},'" . MYSQL_DATE_SEARCH . "') >= '$start'";
                $filterstr[] = '<b>Start : </b>' . $_POST['start'];
            }
            if (!empty($_POST['end']) && !empty($_POST['datepref'])) {
                $end = get_mysql_date($_POST['end'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT({$_POST['datepref']},'" . MYSQL_DATE_SEARCH . "') <= '$end'";
                $filterstr[] = '<b>End : </b>' . $_POST['end'];
            }
            
            if (!empty($_POST['retailer_id'])) {
                $filter[] = "ch_retailer_id = '".$_POST['retailer_id']."'";
                //$filterstr[] = '<b>Retailer : </b>' . $_POST['ch_no'];
            }
//            if (!empty($_POST['ch_no'])) {
//                $filter[] = "ch_no like  '%$_POST[ch_no]%'";
//                $filterstr[] = '<b>Challan No.  : </b>' . $_POST['ch_no'];
//            }
             $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby");
            // echo $funcname;
            // $myobj->get_item_category_list()
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}
elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    
    $rs = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} else {
     $filter = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
    $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby");
    // pre($rs);
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
    $('#itemId').change(function() {
        //    alert( $(this).closest("td").next().find('select').attr("id") );
    });

    var i = 1;
    $(document).on('click', '.addbutton', function() {
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
    $(document).on('click', '.removebutton', function() {
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
   function custum_function(pid, pvalue, event) {
        var batchno = $("#" + pid).closest("td").next().find("select").attr("id");
        getajaxdata('get_product_vat', 'mytable', event);

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


function get_available_rate(mrp_id,mrp_value,event){
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
    var printall_href = document.getElementById('print_all');

    if(document.getElementById(chkid).checked){
        printall_href.href += "-"+chkval ;
    }else{
        //var phref = printall_href.href;
        printall_href.href = printall_href.href.replace("-"+chkval,'');
        printall_href.href = printall_href.href.replace(chkval,'');
        var p = printall_href.href;
    }
}
</script>
<?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
            require_once('damage-print.inc.php');
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
                            
                                <table width="100%"  id="mytable">
                                    <tr class="thead" style="font-weight:bold;">
                                        <td>S.NO</td>
                                        <td>Item Name</td>
                                        <td>Avlb. Stock</td>
                                        <td style="width: 50px;">M.R.P</td>
                                        <td>Quantity</td>
                                        <td style="width: 20px;">Sch. Quantity</td>
                                        <td style="width: 10px;">Rate</td>
                                        <td>Trade/Sch. Disc. Type</td>
                                        <td>Trade/Sch. Disc.</td>                                                
                                        <td>Trade/Sch. Disc. Amt</td>
                                        <td style="width: 50px;">C.D Type</td>
                                        <td>C.D.</td>                                                
                                        <td>CD.Amt</td>
                                        <td>Taxable Amt.</td>
                                        <td style="width: 10px;">VAT%</td>
                                        <td>VAT. Amt</td>
                                        <td>Amount</td>
                                        <td style="width: 36px;">&nbsp;</td>
                                    </tr>
    <?php
    if (isset($heid)) {
        $inc = 1;
        foreach ($_POST['challan_item'] as $key => $value) { //pre($value); 
            ?>
                <tr class="tdata">
                    <td class="myintrow"><?=$inc?></td>
                    <td>
            <?php //echo $value['pid'];
            //$q = "SELECT id, name from catalog_product where company_id = '$_POST[company_id]' ORDER BY id ASC";
            $q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) WHERE dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].'';
            db_pulldown($dbc , 'product_id[]', "$q",TRUE,TRUE,' id ="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);', '', $value['pid']); ?>
          
                <input type="hidden" name="user_id[]" value="<?php echo $value['user_id']; ?>">
                    </td>

                 <td>
                 <input id="aval_stock<?=$inc?>" type="text" name="aval_stock[]" onchange="challan_calculate();" value="<?php echo $myobj->calculate_available_stock($value['product_id']); ?>"  />
                </td>
               <td>
                   
                   <input type="text" id="base_price<?=$inc?>" name="base_price[]" onblur="mrp_change()" value="<?php echo $value['mrp']; ?>" />
                 </td>   
                
                    <td><input type="text" id="quantity<?=$inc?>"  onchange="product_calculate_edit(<?=$inc?>);" onblur="mrp_change()" lang="quantity" name="quantity[]" value="<?php echo $value['qty']; ?>"></td>
                    <td><input id="scheme<?=$inc?>" type="text" name="scheme[]" value="<?php echo $value['free_qty']; ?>"></td>
                   
                    <td><input id="rate<?=$inc?>" onblur="product_calculate_edit(<?=$inc?>);" type="text" name="rate[]" value="<?php echo $value['product_rate']; ?>"></td>
                    
                    <td>
                        <select name="trade_disc_type[]" lang="trade_disc" id="trade_disc_type<?=$inc?>">
                            <option value="1">%</option>
                            <option value="2">Amount</option>
                      </select>
                    </td>
                    <td>
                        <input type="text" id="trade_disc_val<?=$inc?>" name="trade_disc_val[]" value="0"  onblur="trade_disc_calculate();" />
                    </td>

                    <td>
                        <input type="text" name="trade_disc_amt[]" value="0"  />
                        <input type="hidden" name="ttl_amt[]" value="0"   />
                    </td>
                    <td>
                    <select name="cd_type[]" id="cd_type<?=$inc?>" lang="cdtype" onchange="product_calculate_edit()">
                        <option value="">== Select ==</option>
                        <option value="1" <?php if($value['cd_type']==1) echo 'selected = "selected"'; ?> >%</option>
                        <option value="2" <?php if($value['cd_type']==2) echo 'selected = "selected"'; ?>>Amount</option>
                        <option value="3" <?php if($value['cd_type']==3) echo 'selected = "selected"'; ?>> Kg </option>
                    </select>
                    </td>
                    <td><input type="text" id="cd<?=$inc?>" name="cd[]" onchange="product_calculate_edit()" value="<?php echo $value['cd']; ?>"></td>

                    <?php 
                        if($value['cd_type']==1){
                            $cd = (($value['qty']*$value['product_rate'])*$value['cd'])/100;
                        }elseif($value['cd_type']==2){
                             $cd = ($value['cd']);
                        }
                             $vat = ((($value['qty']*$value['product_rate'])-$cd)*$value['tax'])/100;
                             $total = ($value['qty']*$value['product_rate'])-$cd+$vat;
                        ?>
                    <td><input type="text" id="cd_amt<?=$inc?>" name="cd_amt[]" value="<?php echo $cd ; ?>"></td>
                    <td>
                        <input type="text" id="taxable_amt<?=$inc?>" name="taxable_amt[]"  value="<?php echo $value['taxable_amt']; ?>"   />
                    </td>
                    <td><input type="text" id="vat<?=$inc?>" name="vat[]" onchange="product_calculate_edit()" value="<?php echo $value['tax']; ?>"></td>
                    <td><input type="text" id="vat_amt<?=$inc?>" name="vat_amt[]" value="<?php echo $vat; ?>">
                        <input   type="hidden" name="surcharge[]" id="surcharge"  value="<?php echo $surcharge;?>"  />
                    </td>
                    <td><input type="text" id="amount<?=$inc?>" name="amount[]" value="<?php echo $total; ?>"></td>
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/><img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/>
                    </td>
                </tr>
                                            
        <?php $inc++;
        }
    } else { // foreach($_POST['gate_item'] as $key=>$value){ ends ?>
                                        <!-- getitem('batch1',this.value,'qty1','rate1');-->
        <tr class="tdata">
            <td class="myintrow">1</td>
            <td>
                <select name="product_id[]" id="1" onchange="custom_function(this.value, this.id);" onblur="getajaxdata('get_product_qty', 'mytable', event);">
                      <option>== Please Select ==</option>
                          </select>
                          <input type="hidden" name="user_id[]" value="">
                                            </td>
                 <td><select name="batch[]" id="batch1" onchange="getajaxdata('get-stock', 'mytable', event);" onblur="getajaxdata('get_product_tax', 'mytable', event);"  >
                      <option>== Please Select ==</option>
    </select>
                       </td>
                       <td>
                           <input id="aval_stock" class="read" readonly="readonly" type="text" name="aval_stock[]" onchange="challan_calculate();" id="aval_stock1"  value=""  />
                       </td>
                       <td>
                           <input id="qty" type="text" name="qty[]" onchange="challan_calculate();" id="qty1"  value=""  />
                       </td>
                       <td>
                           <input id="rate" type="text" onchange="challan_calculate();" name="rate[]" id="rate1" value=""  />
                       </td>
                       <td>
                           <input id="goodvalue" type="text" name="total[]" id="goodvalue1"  value=""  />
                       </td>
                       <td><input id="goodvalue" type="text" name="taxId[]" id="tax"  value=""  /></td>
                       <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                   </tr>
<?php } ?>
                 <!--                <tr class="tfoot">
                                     <td colspan="10" align="right"><strong>Total Amount</strong></td>
                                     <td><input type="text" name="totalamount" id="totalamount" value="<?php echo $_POST['invamount']; ?>" /></td>                    
                                     <td>&nbsp;</td>
                                   </tr>-->
                            </table>
                            <!-- table to capture the address field ends -->
<?php // } //if(isset($heid))  ends ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6">
    <?php //form_buttons(); // All the form control button, defined in common_function ?>
                            <input id="mysave" type="submit" name="submit" value="<?php if (isset($heid)) echo'Update';
    else echo'Save'; ?>" />
    <?php if (isset($heid)) {
        echo $heid; //A hidden field name eid, whose value will be equal to the edit id. ?>
                                <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>" />  
                                <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Exit" /> <br />
                                    <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
    <?php } else { ?>
                                <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
    <?php } ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
<?php } else {//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here  ?>
<form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform1" onsubmit="return checkForm('genform1');">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr id="mysearchfilter">
            <td>
                   <fieldset>
                          
            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
            <table>
                <tr>
                    <td width="10%"><span class="star">*</span>DatePref<br />	
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
<!--                    <td width="10%">Challan No<br />
                        <input type="text" name="ch_no" id="invnum" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no']; ?>" /> 
                    </td>-->
                    <td width="20%"><br/>
                        <input class="btn btn-sm btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
                        <!--<input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->

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
                     <?php
                $total1 = 0;
                $total2 = 0;
          foreach ($rs as $key => $rows) {
             foreach ($rows['challan_item'] as $inkey => $invalue) {               

                 
                    $amt = ($invalue['qty'] * $invalue['product_rate']);

                    if($invalue['cd_type']==1){
                     $cd_amt = ($amt * $invalue['cd']) / 100;
                    }elseif($invalue['cd_type']==2){
                        $cd_amt = ($invalue['cd']);
                    }   

                    if($invalue['tax']==0){
                    $vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;}
                   else {
                       $vat_amt1=$amt*5/100;
                    }
                    $act_amt = $invalue['actual_amount'];
                    $vat_amt = $vat_amt1*($surcharge/100);
                   
                     $total += $amt - $cd_amt + $vat_amt1 ;
                    $total1 = $total1+$total;

                    $total2 += $invalue['actual_amount'];
                            
          }} ?>
                <div class="table-header">
                   Invoice List <strong><span style="margin-left:30%"> GRAND TOTAL : <?=my2digit($total2);?></span>
                    </strong><div class="pull-right tableTools-container"></div>
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
                                <th class="center">
                                    <label class="pos-rel">
                                        <!--<input type="checkbox" class="ace" />-->
                                        <span class="lbl"></span>
                                    </label>
                                </th>
                                <th>S.No</th>
                                <th>Damage Date</th>
                                <th>Retailer</th>
                                <th>Complaint Type</th>
                                <th>Item Details</th>
                                <th>Amount</th>
<!--                                <th>Options</th>-->
                            </tr>
                        </thead>
                        
                        
                         <tbody>
                            <?php
                              $inc = 1;
                              $surcharge=  myrowval('state', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
//pre($rs);
                            foreach ($rs as $key => $rows) {
                                //pre($rs);
                                 $where = 'id = '.$value['ch_retailer_id'];
                                $uid = $rows['id'];
                                $uidname = $rows['ch_no'];
                                $frz_date = date("Y-m-d", strtotime($rows['ch_date'] . '+2 days'));
                                if ($frz_date >= date('Y-m-d')) {
                                     $editlink = '<a class="iframef" href="index.php?option=damage-challan&showmode=1&mode=1&id='. $uid . '"><img src="./images/b_edit.png"></a><span class="seperator">|</span> ';
                
                                } else {
                                    $editlink = '';
                                }
                                $printlink = '<a class="iframef" title="print Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>';
                                $deletelink = '';
                                if ($auth['del_opt'] != 1)
                                    $deletelink = '';
                            ?>
                            <tr>
                                <td class="center">
                                    <label class="pos-rel">
                                        <!--<input type="checkbox" class="ace" />-->
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>
                                    <?=$inc?>
                                </td>
                                <td>
                                    <?=$rows['ch_date']?>
                                </td>
                                <td>
                                    <?=$rows['retailer_name']?>
                                </td>
                                <td>
                                    <?=$rows['complaint_name']?>
                                </td>
                                <td>
                                     <table style="border:none; border-collapse:collapse">
                                        <tr style="font-weight:bold;">
                                              <td style="border:none;width:350px;">Item Name</td>
                                              <td style="border:none;width:50px;">Rate</td>                                             
                                              <td style="border:none;width:50px;">Qty</td>                                        
                                              <td style="border:none;width:80px;">Amt.</td>
                                              <td style="border:none;width:100px;">Actual Amt.</td>  
                                        </tr>
                                <?php
                                   $taxamount = 0;
                                    $total = 0;
                                    foreach ($rows['challan_item'] as $inkey => $invalue) {
                                        $amt = ($invalue['qty'] * $invalue['product_rate']);
                                        if($invalue['cd_type']==1){
                                        $cd_amt = ($amt * $invalue['cd']) / 100;
                                        }elseif($invalue['cd_type']==2){
                                            $cd_amt = ($invalue['cd']);
                                        }                                      
                                        if($invalue['tax']==0){
                                        $vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;}
                                       else {
                                           $vat_amt1=$amt*5/100;
                                     }
                                        $act_amt = $invalue['actual_amount'];
                                        $vat_amt = $vat_amt1*($surcharge/100);
                                        $surcharge_amt= $vat_amt; 
                                        $actual_amt=round($invalue['actual_amount'],2);
                                     $actual_amt1=$invalue['actual_amount'];
                                     if($actual_amt<0){
                                         $actual_amt12=abs($actual_amt)."(C)";
                                     }
                                     if($actual_amt>0){
                                         $actual_amt12=$actual_amt."(D)";
                                     }
                                ?>
                                        <style>tr.bordered {border-bottom: 1px solid #000;}</style>
                                        <tr class="bordered">
                                        <td style="border:none;"><?=$invalue['name']?></td>
                                        <td style="border:none;"><?=$invalue['product_rate']?></td>                                          
                                        <td style="border:none;"><?=$invalue['qty']?></td>                                     
					<td style="border:none;"><?=my2digit($amt)?></td>
                                        <td style="border:none;"><?=$actual_amt12?></td>                                 
                                        </tr>
                                        <?php
                                         $total += $actual_amt1;
                                          
                                            }
                                            if($total<0){
                                       $total1=abs($total)."(C)";
                                     }
                                     if($total>0){
                                         $total1=abs($total)."(D)";
                                     }
                                        ?>
                                        </table>
                                    </td>
                                    <td><strong><img src="../icon-system/i16X16/rupee.png"><?=$total1?></strong></td>
                                    <!--<td class="options"><?=$printlink?></td>-->
                    
                                                                    
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
<?php } ?>
<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div><!-- /.main-container -->       
  
     
    </body>
</html>  
<!--      <script src="assets/js/jquery-2.1.4.min.js"></script>-->
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
                                                null, null, null, null, null,null,
                                                //{"bSortable": false}
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
