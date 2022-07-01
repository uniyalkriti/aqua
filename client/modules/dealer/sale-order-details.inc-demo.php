<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php

$forma = 'Order Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'dealer_sale'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.retailer_id DESC'; //The orderby clause for fetching of the
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS . 'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$userid = $_SESSION[SESS . 'data']['id'];
$sesId = $_SESSION[SESS . 'data']['id'];
$role_id = $_SESSION[SESS . 'data']['urole'];
//here we get dealer id
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$state_id =  $_SESSION[SESS . 'data']['state_id'];

############################# Here We get all user to related this dealer ###############
//$user_data = $myobj->get_dsp_wise_user_data($dealer_id);
//dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_list($dealer_id);
?>
<div id="breadcumb"><a href="#">Master</a> &raquo; <a>Users</a>  &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
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

############################# code for SAVING data starts here ###########################
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
                show_row_change(BASE_URL_A . '?option=' . $formaction, $action_status['rId']);
                //$mytemp = $_POST;
                unset($_POST);
                //$_POST = key_value_saver($mytemp, array('dealer_id', 'location_id', 'retailer_id','submit','working_id'));
                //unset($_POST);
                /* echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>'; */
                //unset($_SESSION[SESS.'securetoken']); 		
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

################################ code for editing starts here ############################
if (isset($_POST['submit']) && $_POST['submit'] == 'Update') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);
        if ($checkpass) {
            
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_edit'; // dealer_sale_edit()

            $action_status = $myobj->$funcname($_POST['eid']); // dealer_sale_edit

            if ($action_status['status'])
            {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                show_row_change(BASE_URL_A . '?option=' . $formaction, $_POST['eid']);
                unset($_POST);
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

############# code to get the stored info for editing starts here ########################
if (isset($_GET['mode']) && $_GET['mode'] == 1) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        //This will containt the pr no, pr date and other values
        $funcname = 'get_' . $cls_func_str . '_list'; // get_dealer_sale_list() 
        $mystat = $myobj->$funcname($filter = "$myfilter'$id'", $records = '', $orderby = '', true);
        // get_dealer_sale_list
        /*pre($mystat);
        die;*/
        if (!empty($mystat)) {
            //geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
            geteditvalue_class($eid = $id, $in = $mystat);
            $heid = '<input type="hidden" name="eid" value="' . $id . '" />';
        } else
            echo '<span class="awm">Sorry, no such ' . $forma . ' found.</span>';
    }
}

####################### Code to handle the user search starts here #######################

$rs = array();
$filterused = '';
$funcname = 'get_' . $cls_func_str . '_list';
$funcname_new = 'get_' . $cls_func_str . '_details_list';
$filter = array();

if (isset($_POST['filter']) && $_POST['filter'] == 'Filter'  || (isset($_GET['fdate']) && $_GET['fdate']!=''))
{
    if (valid_token($_POST['hf']) || (isset($_GET['fdate']) && $_GET['fdate']!='')) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);

            if(isset($_GET['fdate']) && $_GET['fdate']!='')
            {
                $_POST['from_date'] = $_GET['fdate'];
                $_POST['to_date']   = $_GET['tdate'];
            }
            
            $filterstr = array();
            if (!empty($_POST['from_date'])) {
                $start = get_mysql_date($_POST['from_date'], '/', $time = false, $mysqlsearch = true);

                $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') >= '$start'";
                $filterstr[] = '<b>Start : </b>' . $_POST['from_date'];
            }
            if (!empty($_POST['to_date'])) {
                $end = get_mysql_date($_POST['to_date'], '/', $time = false, $mysqlsearch = true);
                $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') <= '$end'";
                $filterstr[] = '<b>End : </b>' . $_POST['to_date'];
            }
            $filter[] = " call_status = '1'";
            if (!empty($_POST['order_no'])) {
                $filter[] = "order_id = '$_POST[order_no]'";
                $filterstr[] = '<b>Order No  : </b>' . $_POST['order_no'];
            }
             if (!empty($_POST['company_id'])) {
                $filter[] = "company_id = '$_POST[company_id]'";
                $filterstr[] = '<b>Company  : </b>' . $_POST['company_id'];
            }
            // $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'  AND user_sales_order_details.status !=2 ";
            $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}' ";

           /* if (!empty($user_data)) {
                $user_data_str = implode(',', $user_data);
                //$filter[] = "user_id IN ($user_data_str)";
            }*/
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname_new($filter, $records = '', $orderby = ''); //   get_dealer_sale_details_list
            // pre($rs);
            
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank']))
{
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    $rs = $myobj->$funcname_new($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} 

else{

    if (!empty($user_data)) {

        $d=  date('Ymd');
        $user_data_str = implode(',', $user_data);
        $filter[] = "call_status = '1' AND company_id=1 AND user_sales_order_details.status !=2 ";
        $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
        $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') >= '$d'";
        $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') <= '$d'";
        $rs = $myobj->$funcname_new($filter, $records = '', $orderby = '');        
    }


    $today = date('Y-m-d');
    $rs = $myobj->$funcname_new($filter="user_sales_order.`date`='$today' AND dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}' ",  $records = '', $orderby='');
        //get_dealer_sale_list
        /*pre($rs);
        die('lfjsd');*/

}

dynamic_js_enhancement();

if (isset($_POST['catalog_1_id'])) {
    $catalog_1_id = $_POST['catalog_1_id'];
} else {
    $catalog_1_id = "";
}
?>
<script type="text/javascript">
function total_amount_invoice(){
   
    var arr = document.getElementsByName('amount[]');
   // alert(arr.length);
    var tot=0;
    for(var i=0;i<arr.length;i++){
     //   alert(arr.length);
        if(parseFloat(arr[i].value))
            tot += parseFloat(arr[i].value);
    }
    document.getElementById('total').value = tot.toFixed(2);
}

</script>
<script>
    var i = 1;
$(document).on('click', '.addbutton', function () {
//if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
$("#mytable tr:nth-child(2)").clone().find("select").each(function() {
$(this).val('').attr('id', function(_, id) { return id + i });
}).end().appendTo("#mytable");
i++;
$("#mytable tr:nth-child(2)").clone().find("input").each(function() {
$(this).val('').attr('id', function(_, id) { return id + j });
}).end().appendTo("#mytable");
j++;
$('#mytable tr.tdata').each(function(j){
$(this).find('td.myintrow:first').html((j+1)*1);
});
});
$(document).on('click', '.removebutton', function () {
$(this).closest('tr').remove();
return false;
});
</script>
<script type="text/javascript">    
    $(function() {
        $(".order_id").autocomplete({
            source: "index.php?option=myajax-autocomplete&subauto=order-search&searchdomain=orderno",
            minLength: 1
        });
    });

    $(function() {
        $(".product").autocomplete({
            source: "./modules/ajax-autocomplete/user/ajax-product-name.php",
            select: function(event, ui) {
                $('#productid').val(ui.item.id);
            }
        });

    });

    function checkuniquearray(name)
    {
        var arr = document.getElementsByName('product[]');
        var len = arr.length;
        var v = checkForm('genform');
        if (v)
        {
            for (var i = 0; i < len; i++)
            {                        // outer loop uses each item i at 0 through n
                for (var j = i + 1; j < len; j++)
                {                            // inner loop only compares items j at i+1 to n
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
    function hideDiv(value)
    {

        if (value == 'true') {
            document.getElementById('product').style.display = 'block';
            document.getElementById('gift').style.display = 'block';
            document.getElementById('gift1').style.display = 'block';
            document.getElementById('prod1').style.display = 'block';
        }
        if (value == 'false') {
            document.getElementById('product').style.display = 'none';
            document.getElementById('prod1').style.display = 'none';
            document.getElementById('gift').style.display = 'block';
            document.getElementById('gift1').style.display = 'block';
        }
    }
    function FormSubmit()
    {
        var order = document.getElementsByName('order_id[]');
        var fd = document.getElementById('fdate').value;
        var td = document.getElementById('tdate').value;
        var len = order.length;
        var str = '';
        
        for (var i = 0; i < len; i++)
        {
            if (order[i].checked) {
                str += order[i].value + ',';
                //document.getElementById('genform2').submit();
            }
        }

        if (str != '') {
            $.colorbox({href: 'index.php?option=dsp-wise-challan-demo&showmode=1&mode=1&order_id=' +str+'&fd='+fd+'&td='+td, iframe: true, width: '95%', height: '95%'});
                return true;
        }
        else
            return false;
    }
    
    function prompt_date()
    {
        var person = prompt("Please enter your name", '<select name=""></select>');
        if (person != null) {
        }
    }

    function FormSubmitCancel()
    {
        var order = document.getElementsByName('order_id[]');
        var len = order.length;
        var str = '';
        
        for (var i = 0; i < len; i++)
        {
            if (order[i].checked) {
                str += order[i].value + ',';
                //document.getElementById('genform2').submit();
            }
        }

        if (str != '') {
            $.colorbox({href: 'index.php?option=dsp-wise-challan-cancel&showmode=1&mode=1&order_id=' + str, iframe: true, width: '95%', height: '95%'});
            return true;
        }
        else
            return false;
    }
</script>

<div id="workarea">
<?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1) {  // to show the form when and only when needed ?>
        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">

            <fieldset>
                <legend class="legend" style=""><?php echo $forma; ?></legend>

                <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
                <input type="hidden" id="loc_level" name="loc_level" value="<?php echo $loc_level; ?>">
                <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if (isset($dealer_id)) echo $dealer_id; ?>">
                <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
                    <tr>
                        <td><span class="star">*</span>Company<br> 
    <?php
    $js_attr = ' lang="company" onchange="fetch_location(this.value, \'progress_div\', \'product_id\', \'company-catalog\');" ';
    if (!isset($_POST['company_id'])) {
        $q = 'SELECT id, name from company';
        db_pulldown($dbc, 'company_id', $q, TRUE, TRUE, $js_attr,'',1);
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
    <?php }
    ?>
                        </td>
                        <td><span class="star">*</span>User<br>
                            <?php
                            
                               // $user_data_str = implode(',', $user_data);
                            $qt = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM  person INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id WHERE dealer_id=$dealer_id GROUP By person.id ORDER BY name ASC";
                            db_pulldown($dbc, 'dspId', $qt, true, true, 'lang="dealer" id="dspId"');

                            ?>
                        </td>
                        <td><span class="star">*</span>Location<br>
                                <?php
                                 if (!isset($heid)) {                                
                                arr_pulldown('location_id', $location_list, $msg = '', true, true, 'lang="locations" onchange="fetch_location(this.value, \'progress_div\', \'retailer_id\', \'get-dealer-retailer\');"');
                                 }else{
                                     //h1($_POST['location_id']);
                                   $q= " SELECT location_5.id, location_5.name from location_5 INNER JOIN dealer_location_rate_list dlrl ON location_5.id = dlrl.location_id where dealer_id =".$_SESSION[SESS.'data']['dealer_id']." GROUP BY location_5.id";
                               db_pulldown($dbc,'location_id', $q, true, true, 'lang="locations" onchange="fetch_location(this.value, \'progress_div\', \'retailer_id\', \'get-dealer-retailer\');"','',$_POST['location_id']); 
                            }
                                ?>
                        </td>
                        <td><span class="star">*</span>Retailer/Client Name<br>
                            <?php if (!isset($heid)) { ?>
                                <select lang="retailer" name="retailer_id" id="retailer_id">
                                    <option>==Please Select==</option>
                                </select>  
                            <?php
                            } else {
			        //echo $_POST['location_id'];
                    db_pulldown($dbc, 'retailer_id', "SELECT id, name FROM retailer  WHERE id = '$_POST[retailer_id]' ", true, true);
//db_pulldown($dbc, 'retailer_id', "SELECT id, name FROM retailer  WHERE location_id = '$_POST[location_id]'", true, true, '', '',$_POST['retailer_id']);                      
      }
                            ?>
                        </td>
         
                    <input type="hidden" name="call_status" value="1">
                    </tr>
                    <tr>
                        <td colspan="5"><div id="prod1"  class="subhead1">Product Details</div></td>
                    </tr>

                    <tr>
                        <td colspan="7">
                            <div id="product">
                                <table width="100%" id="mytable">
                                    <tr class="thead" style="font-weight:bold;">
                                        <td>S no</td>
                                        <td>Product</td>
                                        <!--<td>Base Price</td>-->
                                        <td>MRP</td>
                                        <td>Rate</td>
                                        <td>Quantity</td>
                                        <!--<td>Sch Quantity</td> -->
                                        <!--<td>Sale Value</td>-->
                                        <td style="width:40px;">&nbsp;</td>
                                    </tr>
                                    <?php if (!isset($heid)) { ?>
                                    <tr class="tdata">
                                        <td class="myintrow">1</td>
                                        <input type="hidden" value="<?=$state_id?>" name='state_id'>
                                        <td>

                                            <?php
         //echo $q = "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' AND catalog_product.company_id = 1 ";
                                            db_pulldown($dbc , 'product_id[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' AND catalog_product.company_id = 1 ",TRUE,TRUE,' id="product_id" '); 
                                            ?>
<!--                                                <select style="width: 400px" id="product_id" name="product_id[]"  >
           <option value="">== Please Select ==</option>
       </select>-->
   </td>
   <!--                                          <td><input   type="text" name="base_price[]" onblur="product_calculate();" value=""  /></td>-->
   <td><input style="width: 400px"  type="text" id="quantity" name="quantity[]" onblur="get_scheme();" value=""  /></td>
   <td><input type="text" name="scheme[]"   value=""  /></td>
   <!--                    <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="" /></td>-->
   <td><img title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
</tr>
<?php
} else {
    
    
    
     if (!empty($_POST['order_item'])) {
        foreach ($_POST['order_item'] as $inkey => $invalue) {
            ?>
            
                <input type="hidden" name="hid_product[]" readonly=""  value="<?php echo $invalue['product_id']?>">
           
                <input type="hidden" name="hid_mrp[]" readonly=""  value="<?php echo $invalue['mrp']?>">
           
                <input type="hidden" name="hid_new_rate[]" readonly="" value="<?php echo $invalue['rate']?>">
            
                <input type="hidden" name="hid_quantity[]"  value="<?php echo $invalue['quantity']; ?>"  />
           
                        
                        <?php
                    }
                }
    
    

    if (!empty($_POST['order_item'])) {
        $inc = 1;
// pre($_POST['order_item']);
        foreach ($_POST['order_item'] as $inkey => $invalue) {
            ?>
            <tr class="tdata">
                <td class="myintrow"><?php echo $inc; ?></td>
                <td>
                <?php //db_pulldown($dbc, 'product[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ", true, true, '', '', $invalue['product_id']);

                db_pulldown($dbc, 'product[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product ", true, true, 'class="item_details"', '', $invalue['product_id']); 
                ?> 
                <!--<input type="text" name="hid_product[]" readonly=""  value="<?php echo $invalue['product_id']?>">-->
            </td>
            <!--                                                    <td><input   type="text" name="base_price[]" onblur="product_calculate();" value="<?php echo $value['rate']; ?>"  /></td>-->

            <td>
                <select class="mrp mrp_dd">
                    <option value="">== Please Select ==</option>
                <?php 
                        $product_id = $invalue['product_id'];                        
                        $mrps = mysqli_query($dbc,"SELECT mrp FROM stock WHERE product_id=$product_id AND dealer_id = $dealer_id");

                        while($mrp_row = mysqli_fetch_assoc($mrps))
                        { 
                            $selected = ($invalue['mrp']==$mrp_row['mrp']) ? "selected":"";
                            ?>
                            <option value="<?php echo $mrp_row['mrp'] ?>" <?php echo $selected ?>><?php echo $mrp_row['mrp'] ?></option>
                <?php   }
                 ?>
                </select>
                <!--<input type="text" name="hid_mrp[]" readonly=""  value="<?php echo $invalue['mrp']?>">-->
            </td>
            <td>
                <input type="text" name="new_rate[]" readonly="" class="rate" value="<?php echo $invalue['rate']?>">
                <!--<input type="text" name="hid_new_rate[]" readonly="" class="rate" value="<?php echo $invalue['rate']?>">-->
            </td>
            <td>
                <input type="text" name="quantity[]" onblur="product_calculate();"  value="<?php echo $invalue['quantity']; ?>"  />
                <!--<input type="text" name="hid_quantity[]"  value="<?php echo $invalue['quantity']; ?>"  />-->
            </td>
                                                   <!--<td>
                                                        <input type="text" name="scheme[]" value="<?php echo $invalue['scheme_qty']; ?>"  />
                                                    </td>
                                                    <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="<?php echo my2digit($value['quantity'] * $value['rate']); ?>" /></td>-->
                                                    <td><img title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/><?php if($inc > 1) { ?> <img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php } ?></td>
                                                </tr>
                                                <?php
                                                $inc++;
                                            }
                                        }
                                    }
                                    ?>  
                                </table>
                            </div>
                        </td>
                    </tr>          
                    <!-- <tr>
                        <td colspan="5"><div id="gift1" class="subhead1">Gift Details</div></td>
                    </tr> -->
                    
                                    <?php if (!isset($heid)) { ?>
                                        <tr class="tdata">
                                            <td class="myintrow">1</td>
                                            <td><?php db_pulldown($dbc, 'gift_id[]', "SELECT id, gift_name FROM _retailer_mkt_gift", TRUE, TRUE, ''); ?></td>

                                            <td><input   type="text" name="gift_qty[]"  value=""  /></td>

                                            <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable1', event);"/></td>
                                        </tr>
    <?php
    } else {
        if (isset($_POST['gift_item']) && !empty($_POST['gift_item'])) {
            $inc = 1;
            foreach ($_POST['gift_item'] as $inkey => $invalue) {
                ?>
                                                <tr class="tdata">
                                                    <td class="myintrow"><?php echo $inc; ?></td>
                                                    <td>
                <?php db_pulldown($dbc, 'gift_id[]', "SELECT id, gift_name FROM  _retailer_mkt_gift", true, true, '', '', $invalue['gift_id']); ?> 
                                                    </td>
                                                    <td>
                                                        <input type="text" name="gift_qty[]" value="<?php echo $invalue['quantity']; ?>"  />
                                                    </td>

                                                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable1', event);"/><?php if ($inc != 1) { ?><img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php } ?></td>
                                                </tr>
                <?php
                $inc++;
            }
        }
    }
    ?>   
                                </table>
                            </div>
                       <!-- form design for feeding gift details in UI PART End here --> 
                    <fieldset>
                        <legend class="legend">Remarks</legend>
                        <textarea style="width: 405px; height: 55px;float: left;" name="remarks"><?php if (isset($_POST['remarks'])) echo $_POST['remarks']; ?></textarea>
                        <div style="width: 256px;float: left;margin: 30px 0px 0 20px;">
                              <?php //form_buttons(); // All the form control button, defined in common_function ?>
                            <input id="mysave" style="cursor:pointer;background-color: #438eb9;width: 80px;color:white;height: 30px;border: 0;"  type="submit" name="submit" value="<?php if (isset($heid)) echo'Update';
                              else echo'Save'; ?>" />
                              <?php if (isset($heid)) {
                                  echo $heid; //A hidden field name eid, whose value will be equal to the edit id. ?>
                              <!-- <input style="cursor:pointer;background-color: #8B9AA3;width: 80px;color:white;height: 30px;border: 0;" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>" />   -->
                              <input style="cursor:pointer;background-color: #8B9AA3;width: 80px;color:white;height: 30px;border: 0;" onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
                                      <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
                                  <?php } else { ?>
                              <input style="cursor:pointer;background-color: #8B9AA3;width: 80px;color:white;height: 30px;border: 0;" onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
                                  <?php } ?>
                        </div>
                    </fieldset>                    
                   
            </fieldset>
        </form>
<?php } else {//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here ?>

        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform1" onsubmit="return checkForm('genform1');">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">
                <tr id="mysearchfilter">
                    <td>
                        <!-- this table will contain our form filter code starts -->
                        <fieldset>
<!--                            <legend class="legend">Search <?php echo $forma; ?></legend>-->
                            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
                            <table>
                                 <tr>
<!--                                    <td>Order<br />
                                        <input type="text" class="order_id"  name="order_no" class="order" value="<?php if (isset($_POST['order_no'])) echo $_POST['order_no']; ?>" /> 
                                    </td>-->
                                
               <div class="col-xs-2">From Date<br />
                                        <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if (isset($_POST['from_date'])) echo $_POST['from_date'];
                        else echo date('d/m/Y'); ?>" />
                        </div>
               <div class="col-xs-2">To Date<br />
                      <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if (isset($_POST['to_date'])) echo $_POST['to_date'];
                        else echo date('d/m/Y'); ?>" />
                                    </div>
               <div class="col-xs-6"> <br>
                   <input class="btn btn-sm btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
<!--                          <input class="btn btn-danger" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                          <input  class="btn btn-success" onclick="$.colorbox({href: 'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe: true, width: '95%', height: '95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->

    <!--                  <input onclick="$.colorbox({href:'indexpop.php?option=direct-challan', iframe:true, width:'95%', height:'95%'});" type="button" value="Direct Challan" title="create direct challan <?php echo $formaction; ?>" />-->

                                    </div>
                                </tr>
                            </table>
                        </fieldset>
                        <!-- this table will contain our form filter code ends -->           
                    </td>
                </tr>
            </table>
        </form>
        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" id="genform2" name="genform2" onsubmit="return checkForm('genform2');">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">

    <?php
    if (isset($_GET['ajaxshowblank']))
        ob_end_clean(); // to show the first row when parent table not avialable
    if (!empty($rs)) { //if no content available present no need to show the bottom part
        ?>
                   
                                <?php } //if(!empty($rs)){?>
                                <?php if (isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>
            </table>
                                </fieldset>
        </form>
    
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header"> 
                   Search Order Details &nbsp;&nbsp;&nbsp; <button name="button" onclick="FormSubmit()" >
<span class="label label-lg label-yellow arrowed-in arrowed-in-right">Make Challan/Invoice</span></button>
<button name="button" onclick="FormSubmitCancel()" >
<span class="label label-lg label-danger arrowed-in arrowed-in-right">Cancel Orders</span></button>
<div class="pull-right tableTools-container"> </div> 
                   
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
                              <th style="background-color:#C7CDC8; color:#000;"><input onclick="selectCheckBoxes('checkall', 'order_id[]');" type="checkbox" id="checkall"></th>
                              <th style="background-color:#C7CDC8; color:#000;" class="sno">S.No
                                <!--                               <input onclick="selectCheckBoxes('checkall', 'chk[]');" type="checkbox" id="checkall">-->
                            </th>
                            <!--                                            <th class="sno">S.No</th>-->
                            <th style="background-color:#C7CDC8; color:#000;">Date</th>

                            <th style="background-color:#C7CDC8; color:#000;">Salesman Name</th>
                            <!--                                            <th>Dealer Name</th>-->
                            <th style="background-color:#C7CDC8; color:#000;">Retailer Name</th>
                            <th style="background-color:#C7CDC8; color:#000;">Outstanding</th>
                            <!-- <th style="background-color:#C7CDC8; color:#000;">Order No</th> -->
                            <th style="background-color:#C7CDC8; color:#000;">Call Status</th>
                            <th style="background-color:#C7CDC8; color:#000;">Number Of Products</th>
                            <!--                                            <th style="background-color:#C7CDC8; color:#000;">Scheme Qty</th>-->
                            <th style="background-color:#C7CDC8; color:#000;">Taxable Amt.</th>
                            <th style="background-color:#C7CDC8; color:#000;">Discount Amt.</th>
                            <th style="background-color:#C7CDC8; color:#000;">Amount</th>
                            <!--<th align="center" colspan="5">Order Details</th>-->
                            <!-- <th style="background-color:#C7CDC8; color:#000;" width="50px">Options</th> -->
                        </tr>
<!--                                        <tr valign="top" class="search1tr">
                                            <th style="border:none;width: 110px;">Product Name</th>
                                         <th style="border:none;width: 80px;">Rate</th>
                                            <th style="border:none;width: 80px;">Quantity</th>
                                            <th style="border:none;width: 140px;">Sch Qty</th>
                                         <th style="border:none;width: 140px;">Total sale value</th>
                                        </tr>-->
                    </thead>
                    
                   
                  <?php
          //  $bg = TR_ROW_COLOR1;
          echo'<tbody>';
          $inc=1;

            foreach ($rs as $key => $rows) {
           //     $bg = ($bg == TR_ROW_COLOR1 ? TR_ROW_COLOR2 : TR_ROW_COLOR1); // to provide different row colors(member_contacted table)
                $uid = $rows['order_id'];
                $uidname = $rows['order_id'];
                $callstatus = $rows['call_status'] == '0' ? 'Non Productive' : 'Productive';
                $deletelink = '| <a href="javascript:void(0);" onclick="do_delete(\'Challan Delete\', \'' . $uid . '\',\'Sale Order\',\'' . addslashes($uidname) . '\');"><img src="./images/b_drop.png"></a>';
          
                $editlink = '<a class="iframef" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '"><img src="./images/b_edit.png"></a>';               
                
                 $outs="SELECT 
                        sum(`challan_order_details`.`taxable_amt`) AS AmountPaid 
                         FROM `challan_order_details`
                         INNER JOIN `challan_order` ON   `challan_order`.`id`=`challan_order_details`.`ch_id` WHERE `challan_order`.`ch_retailer_id`='$rows[retailer_id]'";
                 //h1($outs);                
                 $out1= mysqli_query($dbc, $outs);
                 $row2 = mysqli_fetch_assoc($out1);
                 $amount_paid = $row2['AmountPaid'];

                  $pay="SELECT sum(`payment_collection`.`total_amount`) AS PaymentCollect from `payment_collection` WHERE `retailer_id`='$rows[retailer_id]'";
                  $out2= mysqli_query($dbc, $pay);
                  $row_pay = mysqli_fetch_assoc($out2);
                  $payment_collect = $row_pay['PaymentCollect'];
                  if(empty($payment_collect))
                      $payment_collect ='0.00'; 
                     $oustanding=$amount_paid-$payment_collect;
                     $oustanding=  round($oustanding,2);
                     //echo $oustanding;
                     //echo"&nbsp;&nbsp;<strong>Payment Collection : </strong>".$payment_collect;
                   // h1($outs);
             echo'
             <tr>
             <td><input type="checkbox" name="order_id[]" value="'.$uid.'" </td>
             <td>' . $inc . '</td>
             <td>' . $rows['fdated'] . '</td>

             <!-- <td><strong><span style="color:green;">' . $GLOBALS['order_type'][$rows['order_status']] . '</span></strong></td>-->

             <td><strong>' . $rows['person_name'] . '</strong><div style="display:none" id="delDiv' . $uid . '"></div></td>
             <!--   <td>' . $rows['name'] . '</td>-->
             <td>' . $rows['firm_name'] . '</td>
             <td>' . $oustanding . '</td>
             <!-- <td>' . $rows['order_id'] . '</td> -->
             <td>' . $callstatus . '</td>';

             /*$product = 0;
             $schqty = 0;
             foreach ($rows['order_item'] as $inkey => $invalue) {
                 $product++;
                 $schqty = $schqty+$invalue['scheme_qty'];

             }*/

             echo'<td>'.$rows['order_item'].'</td>';
            //echo'<td>'.$schqty.'</td>'; 
             echo'<td>'. $rows['sale_value'].'</td>'; 
             echo'<td>'. $rows['discount'].'</td>';
             echo'<td>₹ '. $rows['sale_value'].'</td>';
            //echo'
//    <table> <tbody>';
//pre($rows['order_item']);<td colspan="5">   
             $count_value = count($rows['order_item']);

             $total_sale_value = 0;

             echo'
             </tr>
                      ';
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
    
                            <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
</div><!-- workarea div ends here -->
<!--  <script src="assets/js/jquery-2.1.4.min.js"></script>-->
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
                                                null, null, null, null,null,null,
                                                null,null,null, 
                                               
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
                                    // alert(this.checked);
                                    if (this.checked){
                                        //myTable.row(row).deselect();
                                        $(this).prop('checked', true);
                                    }else{
                                            //myTable.row(row).select();
                                        $(this).prop('checked', false);
                                    }
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
