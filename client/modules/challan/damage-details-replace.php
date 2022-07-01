<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
$forma = 'Damage Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new damage_sale();
$cls_func_str = 'Damage'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS . 'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$userid = $_SESSION[SESS . 'data']['id'];
$sesId = $_SESSION[SESS . 'data']['id'];
$role_id = $_SESSION[SESS . 'data']['urole']; //vat_amt  surcharge taxable_amt
$state_id = $_SESSION[SESS . 'data']['state_id'];
$surcharge=  myrowval('state', 'surcharge', 'stateid='.$state_id);
//here we get dealer id
$dealer_id = $myobj->get_dealer_id($sesId, $role_id);

//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_id_list($dealer_id);
$location_list = implode(',', $location_list);
?>
<div id="breadcumb"><a href="#">Damage</a> &raquo; <a></a>  &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
    <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/sale-order.php'); 
    ?>  
</div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################

function checkform($mode = 'add', $id = '') {
    global $dbc;
    return array(TRUE, '');
    if ($mode == 'filter')
        return array(TRUE, '');
    if ($mode == 'delete')
        return array(TRUE, '');
    $field_arry = array('firm_name' => $_POST['firm_name']); // checking for  duplicate Unit Name

    if ($mode == 'add') {
        if (uniqcheck_msg($dbc, $field_arry, 'retailer', false, ""))
            return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
    }elseif ($mode == 'edit') {
        if (uniqcheck_msg($dbc, $field_arry, 'retailer', false, " id != '$_GET[id]'"))
            return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
    }
    return array(TRUE, '');
}
?>
<?php
$rs = array();
$filterused = '';
$funcname = 'get_' . $cls_func_str . '_list';
$filter = array();
############## SAVE CODE START END HERE ####################
if(isset($_POST['submit']) && $_POST['submit'] == 'Save')
{
    if(valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
        if ($checkpass){
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_save';
            $action_status = $myobj->direct_replace_challan_save(); // $myobj->item_category_save()
            if ($action_status['status']) {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                unset($_POST);
                ?>
                <script>
                //                                 setTimeout("window.location = 'index.php?option=sale-order-detailes'",500);
                //                              window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>
                <?php
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
        $funcname = 'get_challan_list';
        $mystat = $myobj->$funcname($filter = "challan_order.id ='$id'", $records = '', $orderby = ''); // $myobj->get_item_category_list()
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



//$filterstr = array();
if (isset($_POST['order_id']) && !empty($_POST['order_id'])){
    $order_id_str = implode(',', $_POST['order_id']);
    $filter[] = "order_id IN ($order_id_str)";
    $user_data = $myobj->get_dsp_wise_user_data($sesId, $role_id, $dealer_id);
    if (!empty($user_data)) {
        $user_data_str = implode(',', $user_data);
        $filter[] = "user_id IN ($user_data_str)";
    }
    $filter[] = "call_status = '1'";
    $rs = $myobj->$funcname($filter, $records = '', $orderby = '');
    //pre($rs);
}
?>
<!-- This function don't allow  product will be inserted. -->

<script type="text/javascript">
var i = 1;
$(document).on('click', '.addbutton', function () {
    //if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
    var p = $('#mytable').find('.item_details').length;
    var cnt = p+1;
    $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
        $(this).val('').attr('id', function(_, id) { return id + i });
    }).end().appendTo("#mytable");

    $('#mytable tr:last').find('.item_details').removeClass('chzn-done').css({'display':'block'}).removeAttr('id').val('').next('div').remove();

        var x = cnt;
        $('#mytable tr:last').find('.item_details').each(function(index){
            x++;
            $(this).attr('id','product_id'+x);
            $('#product_id'+x+'').chosen();
        })

    i++;
    $('#mytable tr.tdata').each(function(j){
            $(this).find('td.myintrow:first').html((j+1)*1);
    });
});
$(document).on('click', '.removebutton', function () { 
     var tot_row = $('#mytable').find('.item_details').length;
           if(tot_row>2)
           {
             $(this).closest('tr').remove();
           }
     return false;
 });
    function checkuniquearray(name)
    {
        var arr = document.getElementsByName('vat[]');
        var len = arr.length;
        var v = checkForm('genform');
        if (v)
        {
            for (var i = 0; i < len; i++)
            {                        // outer loop uses each item i at 0 through n
                for (var j = i + 1; j < len; j++)
                {
                    // inner loop only compares items j at i+1 to n
//                    if (arr[i].value =='')
//                    {
//                        alert('Vat amount is empty;');
//                        return false;
//                    }
                }
            }
            return true;
        }
        return false;
    }

    function check_greater_value(fieldid)
    {
        var qty = document.getElementById('ch_qty' + fieldid).value;
        var stock = document.getElementById('ostock' + fieldid).value;
        if (qty > stock) {
            alert('Challan Item cannot be greter than opening stock;');
            document.getElementById('ch_qty' + fieldid).value = '';
            document.getElementById('ch_qty' + fieldid).style.focus();
            return false;
        }
    }

    function custum_function(pid, pvalue, event) {
        var batchno = $("#" + pid).closest("td").next().find("select").attr("id");
        getajaxdata('get_retailer_rate', 'mytable', event);
        setTimeout(function() {
            fetch_location(pvalue, 'progress_div', batchno, 'get_product_mrp');
            
        }, 400);
    }


function get_available_stock(mrp_id,mrp_value,event){
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
        //setTimeout(function() {
        var pvalue = document.getElementById(prod_id).value;
            getajaxdata('get-stock', 'mytable', event,pvalue);
        //}, 800);
        
//         setTimeout(function() {
//         //   getajaxdata('get-calculate-rate', 'mytable', event);
//        }, 1000);
        mrp_change(mrp_value);
    
}

function get_available_stock(mrp_id,mrp_value,event){
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
        //setTimeout(function() {
        var pvalue = document.getElementById(prod_id).value;
           // getajaxdata('get-stock', 'mytable', event,pvalue);
        //}, 800);
        
//         setTimeout(function() {
//         //   getajaxdata('get-calculate-rate', 'mytable', event);
//        }, 1000);
        mrp_change(mrp_value);
    
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
        tds_amt[i].value = res.toFixed(3);
                ttl_amt[i].value = ( r[i].value - tds_amt[i].value ) * qty[i].value;
            }else{
                var res = tds_val[i].value;
        tds_amt[i].value = res;
                ttl_amt[i].value = ( r[i].value - tds_amt[i].value ) * qty[i].value;
            }
        }
}

/*function mrp_change()
{
    var mrp = document.getElementsByName('mrp[]');
        var r = document.getElementsByName('rate[]');
       //prodvalue
    // alert(qty.length);
    for(var i = 0; i<mrp.length; i++)
    {
                var res = (mrp[i].value - ( mrp[i].value * (18/100) ))*100/105;
        r[i].value = res.toFixed(2);
        }
}*/
function check_complaint_type(get_val){
var str=get_val.options[get_val.selectedIndex].text;

if(str != 'Replace'){
    var retailer_id = document.getElementById('retailer').value;
    var sale_non_sale = document.getElementById('saleable_non_saleable').value;
    
    window.location.href="index.php?option=damage-details&saleable_non_saleable="+sale_non_sale+"&retailer_id="+retailer_id+"&complaint_id=4";
}

}

function credit_debit_amt(){
         var amount = document.getElementsByName('amount[]');
        var replace_amt = document.getElementsByName('replace_amount[]');
        var actual_amount = document.getElementsByName('actual_amount[]');
       //prodvalue
    // alert(qty.length);
    for(var i = 0; i<amount.length; i++)
    {
                var res = (replace_amt[i].value - amount[i].value);
        actual_amount[i].value = res.toFixed(2);
        }


}

function mrp_change_replace()
{
    var mrp = document.getElementsByName('replace_mrp[]');
        var r = document.getElementsByName('replace_rate[]');
       //prodvalue
    // alert(qty.length);
    for(var i = 0; i<mrp.length; i++)
    {
                var res = (mrp[i].value - ( mrp[i].value * (18/100) ))*100/105;
        r[i].value = res.toFixed(2);
        }
}

function mrp_change_replace()
{
    var mrp = document.getElementsByName('replace_mrp[]');
        var r = document.getElementsByName('replace_rate[]');
       //prodvalue
    // alert(qty.length);
    for(var i = 0; i<mrp.length; i++)
    {
                var res = (mrp[i].value - ( mrp[i].value * (18/100) ))*100/105;
        r[i].value = res.toFixed(2);
        }
}

$(function() {
    $("#retailer").autocomplete({
        source: "./modules/ajax-autocomplete/retailer/ajax-retailer.php"
    });
});




/*function check_stock_qty()
{
    $('#mytable').on('change','.quantitycl',function())
      {
          var avbl_qty = $(this).closest("input[name=avlb_quantity]");
      }
    alert(avbl_qty);
}*/


   


</script>

<div id="workarea">
    <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
        <fieldset>
<!--            <legend class="legend" style=""><?php echo $forma; ?></legend>-->
            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
            <input type="hidden" id="loc_level" name="loc_level" value="<?php echo $loc_level; ?>">
            <input type="hidden" name="dealer_id" id="dealer_id" value="<?php echo $_SESSION[SESS . 'data']['dealer_id'] ?>">
            <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">

                <tr>
                    <td width="10%"><span class="star">*</span>Company<br> 
                        <?php
                        $js_attr = ' lang="company" onchange="fetch_location(this.value, \'progress_div\', \'product_id\', \'company-catalog\');" ';
                        if (!isset($_POST['company_id'])) {
                            $q = 'SELECT id, name from company';
                            db_pulldownsmall($dbc, 'company_id', $q, TRUE, TRUE, $js_attr, '', 1);
                        } else {
                            ?>
                            <select name="company_id" id="company_id" lang="company">
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

                    <td width="10%"><strong>Date</strong><br>
                        <input class="datepicker" type="text" name="ch_date" value="<?php
                        if (isset($_POST['ch_date']))
                            echo $_POST['ch_date'];
                        else
                            echo date('d/M/Y');
                        ?>">
                    </td>
                     <?php
                   //  echo 'ldskjvjdfdsjflksdjf';die;
                     //echo $_SESSION[SESS . 'data']['dealer_id'];die;
                  
                   if(isset($_POST['ch_no']) && $_POST['ch_no'] !=''){
                    $exp_array=explode('/',$_POST['ch_no']); 
                     $invoice_id = isset($exp_array[2])?$exp_array[2]:0;

                   }else{

                     $invoice_id = $myobj->get_invoice_no($_SESSION[SESS . 'data']['dealer_id']);
                   }
                   

                    ?>
 
                    <td width="10%"> 
                        <span class="star">*</span><strong>Retailer Name</strong><br>
                        
<?php

//$q = "SELECT retailer.id as id, CONCAT(retailer.name,' [',location_5.name,'] ')as name FROM retailer INNER JOIN user_dealer_retailer udr ON retailer.id = udr.retailer_id INNER JOIN location_5 ON retailer.location_id = location_5.id where udr.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' ORDER BY name ASC  ";
$qrt = "SELECT retailer.id as id, CONCAT(retailer.name,' [',retailer.address,'] ')as name FROM retailer where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' group by retailer.id ORDER BY retailer.name ASC  ";

db_pulldownsmall($dbc, 'retailer_id', $qrt, true, true, 'id="retailer" class="chosen-select retailer" lang="Retailer" onchange="getdata(this.value, \'progress_div\', \'get-retailer-location\', \'location_id\');"','=Please Select=',$_GET['retailer_id']);
?>
                        <input type="hidden" name="location_id" id="location_id" value="<?php if (isset($_POST['location_id'])) echo $_POST['location_id']; ?>">
                    </td>
                   
                    <td width="10%"><span class="star">*</span><strong>Saleable/Non-Saleable</strong><br>
                                <?php
                                 db_pulldownsmall($dbc,'saleable_non_saleable', $q='select id,name FROM saleable_non_saleable', true, true, 'lang="saleable_non_saleable", id="saleable_non_saleable" onchange="fetch_location(this.value, \'progress_div\', \'complaint_id\', \'get_saleable_non_saleable\');"','=Please Select=',$_GET['saleable_non_saleable']);
                               
                                
                                ?>
                        </td>
                    <!-- <td>
                         <span class="star">*</span><strong>Saleable/Non-Saleable</strong><br>
                        <select name="saleable_non_saleable" onchange="get_saleable_non_saleable(this.value,'get_saleable_non_saleable','complaint_id')" id="saleable_non_saleable" lang="saleable_non_saleable">
                                <option value="">== Please Select ==</option>
                                <?php
                                $q = 'select id,name FROM saleable_non_saleable';
                                $st_res = mysqli_query($dbc, $q);
                                while ($row = mysqli_fetch_array($st_res)) {
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if ($_POST['Saleable_non_saleable'] == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                    </td> -->

                    <!--<td width="10%">
                         <span class="star">*</span><strong>Complaint Type</strong><br>
                        <select name="complaint_id" id="complaint_id" lang="Complaint" onChange="check_complaint_type(this);">
                                <option value="">== Please Select ==</option>

      <!--                           <?php
                                $q = 'select id,name FROM complaint_type ';
                                $st_res = mysqli_query($dbc, $q);
                                while ($row = mysqli_fetch_array($st_res)) {
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if ($_POST['complaint_id'] == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
        <?php
    }
    ?> -->
                            <!--</select>
                    </td>-->
 <td width="10%"> 
                    <?php
                    $Saleable_non_saleable=isset($_GET['saleable_non_saleable'])?$_GET['saleable_non_saleable']:'';
                    $complaint_id=isset($_GET['complaint_id'])?$_GET['complaint_id']:'';
                    ?>
                         <span class="star">*</span><strong>Complaint Type</strong><br>
                        <select name="complaint_id" id="complaint_id" lang="Complaint" onChange="check_complaint_type(this);">
                                <option value="">== Please Select ==</option>

                               <?php
                                $q = 'select id,name FROM complaint_type where saleable_non_saleable='.$Saleable_non_saleable;
                                //echo $q;die;
                                $st_res = mysqli_query($dbc, $q);
                                while ($row = mysqli_fetch_array($st_res)) {
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if ($complaint_id == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
        <?php
    }
    ?>
                            </select>
                    </td>

                </tr>
                <tr>
                  
                    <td colspan="5"><div class="table-header">Replace Product</div></td>
                
                </tr>
                <tr>
                    <td colspan="5">
                        <div id="product" >
<table width="100%" id="mytable1">
    <tr>
        <td colspan="5">
            <table width="100%" id="mytable" class="">
                <tr class="thead" style="font-weight:bold;">
                    <td>S.No</td>
                    <td>Item Name</td> 
                    <td>M.R.P</td>
                    <td>Rate</td>
                    <td>Avlb. Stock</td>
                    <td>Quantity</td>
                    <td>Amount</td>
                    <td>To Replace</td>
                    <td>MRP</td>
                    <td class="hidden">VAT(%)</td>
                    <td>Rate</td>
                    <td>quantity</td>
                    <td>Amount</td>
                    <td>Credit/Debit</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                
                if(isset($_POST['challan_item'])){ $num_rows = count($_POST['challan_item']); }else{ $num_rows=8; }
                // print_r($_POST['challan_item']);
                for($z=1;$z<=$num_rows;$z++){
                    $avlb_stock = 0;
                    $ttl_amt = 0;
                    $avlb_stock = get_avlb_stock_by_prodid_mrp($_POST['challan_item'][$keys[$z-1]]['product_id'],$_POST['challan_item'][$keys[$z-1]]['mrp']);
                    $qty = $_POST['challan_item'][$keys[$z-1]]['qty'];
                    $actual_amount = $_POST['challan_item'][$keys[$z-1]]['actual_amount'];
                    $product_rate = $_POST['challan_item'][$keys[$z-1]]['product_rate'];
                    $dis_amt = $_POST['challan_item'][$keys[$z-1]]['dis_amt'];
                    $comunity_code= $_POST['challan_item'][$keys[$z-1]]['comunity_code'];
                    $dis_type = $_POST['challan_item'][$keys[$z-1]]['dis_type'];
                    $dis_percent = $_POST['challan_item'][$keys[$z-1]]['dis_percent'];
                    //$ttl_amt = get_trade_disc_calculate($qty,$product_rate,$dis_amt,$dis_type,$dis_percent);
                    $taxable_amt = $_POST['challan_item'][$keys[$z-1]]['taxable_amt'];
                    $tax = $_POST['challan_item'][$keys[$z-1]]['tax'];
                    $vat_amt = $taxable_amt*($tax/100); 
                    $amount = $product_rate * $qty;
                    ?>
                <tr class="tdata">
                    <td class="myintrow"><?=$z?></td>
                    <td>
                    <?php
                   /* $q = ' SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where cp.company_id =1 AND dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY name ASC';
                    db_pulldown($dbc, 'product_id[]', $q, TRUE, TRUE, 'id="product_id'.$z.'" onchange=getajaxdata(\'get_product_mrp\',\'mytable\',event) ','',$_POST['challan_item'][$keys[$z-1]]['product_id']);*/

                   
                    //SELECT cp.id,cp.name FROM catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) WHERE dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' order by name

                    // onchange=getajaxdata(\'get_product_mrp\',\'mytable\',event);
                    db_pulldownsmall($dbc , 'product_id[]', 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN stock s ON cp.id = s.product_id where s.dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY cp.name ASC',TRUE,TRUE,' id="product_id'.$z.'"  class="item_details chosen-select"');
                    ?>
                    </td>
                   <!--  custum_function(this.id,this.value,event) -->

                    <td width="10%">
                        <select name="mrp[]" class="mrp mrp_dd" style="width:100%"></select>
                        <!-- <input style="width:80px" type="text" name="mrp[]" id="mrp" value=""  /> -->
                    </td>
                    <td width="10%">
                        <input style="width:80px" type="text" name="rate[]" class="rate" id="rate" onblur="damage_product_calculate();" value="<?=$_POST['challan_item'][$keys[$z-1]]['product_rate']?>" readonly />
                    </td>

                                                              
                    <td width="10%"><input style="width:80px" type="text" name="avlb_quantity[]" class="avlb_quantity" readonly="1" id="avlb_quantity" onblur="damage_product_calculate();"  value="" />
                    </td>

                    <td  width="10%"><input style="width:80px" type="text" name="quantity[]" id="quantity" class="quantitycl" onblur="damage_product_calculate();" value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>" />
                    </td>


                        <input type="hidden" name="trade_disc_amt[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['dis_amt']?>"  />
                        <input type="hidden" name="ttl_amt[]" value="<?=$ttl_amt?>"   />


                    <td  width="10%"><input style="width:100px"  type="text" name="amount[]" id="amount"  value="<?=number_format($amount,2,'.',','); ?>"  /></td>
                    <td><?php
//                      $q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where cp.company_id =1 AND dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY name ASC';
//                    db_pulldown($dbc, 'replace_product_id[]', $q, TRUE, TRUE, 'id="product_id'.$z.'" onchange=getajaxdata(\'get_product_mrp_replace\',\'mytable\',event) ','',$_POST['challan_item'][$keys[$z-1]]['product_id']);

                // $q = ' SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where cp.company_id =1 AND dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY name ASC';

                //puneet
                $q = "SELECT cp.id, cp.name from catalog_product cp INNER JOIN stock s ON cp.id = s.product_id where s.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ORDER BY cp.name ASC";


                    db_pulldownsmall($dbc, 'replace_product_id[]', $q, TRUE, TRUE, 'id="product_id'.($z+8).'" onchange="custum_function(this.id,this.value,event)" class="item_details1 chosen-select"','=Please Select=',$_POST['challan_item'][$keys[$z-1]]['product_id']);
                          /*db_pulldown($dbc , 'product_id[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN catalog_2 ON catalog_product.catalog_id = catalog_2.id WHERE catalog_product.company_id = 1 order by name",TRUE,TRUE,' id="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);');*/
                    ?></td>
                    <td  width="10%">
                        <select style="width:80px" name="replace_mrp[]" id="mrp<?=$z?>" >
                            <option value="<?=$_POST['challan_item'][$keys[$z-1]]['mrp']?>" selected="selected"><?=$_POST['challan_item'][$keys[$z-1]]['mrp']?></option>
                        </select>

                    </td>
                    <td class="hidden" width="10%">
                       
                        <input type="hidden" name="state[]" id="state" value="<?=$state_id?>"  />
                        <input style="width:100px"  type="hidden" name="vat[]" id="vat"  value="<?=$_POST['challan_item'][$keys[$z-1]]['tax']?>"/></td>
                    </td>
                    <td  width="10%">
                        <input style="width:80px" type="text" name="replace_rate[]" id="replace__rate<?=$z?>" onblur="damage_product_calculate_replace();" value="<?=$_POST['challan_item'][$keys[$z-1]]['product_rate']?>" readonly />
                    </td>

                                                              
                   

                    <td  width="10%"><input style="width:80px" type="text" name="replace_quantity[]" id="quantity" onblur="damage_product_calculate_replace();"  value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>"  />
                    </td>

                    <td  width="10%"><input style="width:100px" type="text" name="replace_amount[]" id="replace_amount" onblur="credit_debit_amt();"  value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>"  />
                    </td>

                    <td  width="10%"><input style="width:100px" type="text" name="actual_amount[]" id="actual_amount" value="<?=$_POST['challan_item'][$keys[$z-1]]['actual_amount']?>"  />
                   <td  width="10%"><img title="more" src="images/more.png" class="addbutton"/><img  title="more" src="images/less.png" class="removebutton"/>
                </tr>
                <?php } ?>
            </table>
        </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" colspan="7">
                               <?php //form_buttons(); // All the form control button, defined in common_function ?>
                        <input id="mysave" type="submit" name="submit" value="<?php
                               if (isset($heid))
                                   echo'Update';
                               else
                                   echo'Save';
                               ?>" class="btn btn-primary" />
                        <?php
                        if (isset($heid)) {
                            echo $heid; //A hidden field name eid, whose value will be equal to the edit id. 
                            ?>
                            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>"  />  
                            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
    <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
<?php } else { ?>
                            <!-- <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" /> -->
                            <input onclick="location.href='index.php?option=damage-challan'" class="btn btn-success" type="button" value="Damage Details"/>
<?php } ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div><!-- workarea div ends here -->
<script type="text/javascript">
  if(!ace.vars['touch']) {
       $('.chosen-select').chosen({allow_single_deselect:true});
  }
</script>
<script type="text/javascript">setfocus('name');</script>
<?php
if (isset($pgoutput))
    pagination_js($pgoutput);?>



