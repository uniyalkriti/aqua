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
############################# Here We get all user to related this dealer ###############
$user_data = $myobj->get_dsp_wise_user_data($dealer_id);
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

############################# code for editing starts here ########################
if (isset($_POST['submit']) && $_POST['submit'] == 'Update') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_edit';
            $action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
            if ($action_status['status']) {
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
            $heid = '<input type="hidden" name="eid" value="' . $id . '" />';
        } else
            echo '<span class="awm">Sorry, no such ' . $forma . ' found.</span>';
    }
}
############################# Code to handle the user search starts here #############################
$rs = array();
$filterused = '';
$funcname = 'get_' . $cls_func_str . '_list';
$filter = array();
if (isset($_POST['filter']) && $_POST['filter'] == 'Filter') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            
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
            $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'  AND user_sales_order_details.status !=2 ";

            if (!empty($user_data)) {
                $user_data_str = implode(',', $user_data);
                //$filter[] = "user_id IN ($user_data_str)";
            }
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname($filter, $records = '', $orderby = ''); // $myobj->get_item_category_list()
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    $rs = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} 
else {
    if (!empty($user_data)) {
        $d=  date('Ymd');
        $user_data_str = implode(',', $user_data);
        $filter[] = "call_status = '1' AND company_id=1 AND user_sales_order_details.status !=2  ";
        $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
        $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') <= '$d'";
        $filter[] = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') <= '$d' LIMIT 0,10 ";
        $rs = $myobj->$funcname($filter, $records = '', $orderby = '');
    }
    //$rs = $myobj->$funcname($filter="",  $records = '', $orderby='');
}
dynamic_js_enhancement();

if (isset($_POST['catalog_1_id'])) {
    $catalog_1_id = $_POST['catalog_1_id'];
} else {
    $catalog_1_id = "";
}
?>
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
        var len = order.length;
        var str = '';
        //alert(len);
        for (var i = 0; i < len; i++)
        {
            if (order[i].checked) {
                str += order[i].value + ',';
                //document.getElementById('genform2').submit();
            }
        }

        if (str != '') {
            $.colorbox({href: 'index.php?option=dsp-wise-challan&showmode=1&mode=1&order_id=' + str, iframe: true, width: '95%', height: '95%'});
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
</script>
<div id="workarea">
<?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1) {  // to show the form when and only when needed ?>
        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">

            <fieldset>
                <legend class="legend" style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;"><?php echo $forma; ?></legend>

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
    <?php } //echo $q;
    ?>
                        </td>
                        <td><span class="star">*</span>User<br>
                            <?php
                            if (!empty($user_data)) {
                                $user_data_str = implode(',', $user_data);
                                $q = "SELECT id, CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM  person WHERE id IN ($user_data_str) ORDER BY name ASC";
                                db_pulldown($dbc, 'dspId', $q, true, true, 'lang="dealer" id="dspId"');
                            } else {
                                echo '<select name="" lang="Create DSP first"><option>== No Any DSP Found ==</option></select>';
                            }
                            ?>
                        </td>
                        <td><span class="star">*</span>Location<br>
                                <?php
                                 if (!isset($heid)) {
                                arr_pulldown('location_id', $location_list, $msg = '', true, true, 'lang="locations" onchange="fetch_location(this.value, \'progress_div\', \'retailer_id\', \'get-dealer-retailer\');"');
                                 }else{ 
                                     //h1($_POST['location_id']);
                                    $q= " SELECT id, name from location_5 INNER JOIN dealer_location_rate_list dlrl ON location_5.id = dlrl.location_id where dealer_id =".$_SESSION[SESS.'data']['dealer_id']." ORDER BY name";
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
                                db_pulldown($dbc, 'retailer_id', "SELECT id, name FROM retailer  WHERE location_id = '$_POST[location_id]' ", true, true);
                            }
                            ?>
                        </td>
          <!--              <td colspan="2">
                            Image<br>
                            <input type="file" name="image_name" value="">
                        </td>  -->
                    <input type="hidden" name="call_status" value="1">
                    </tr>
                    <tr>
                        <td colspan="5"><div id="prod1" style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;">Product Details</div></td>
                    </tr>

                    <tr>
                        <td colspan="7">
                            <div id="product">
                                <table width="100%" id="mytable">
                                    <tr class="thead" style="font-weight:bold;">
                                        <td>S no</td>
                                        <td>Product</td>
              <!--                       <td>Base Price</td>-->
                                        <td>Quantity</td>
                                       <td>Sch Quantity</td>
                    <!--                <td>Sale Value</td>-->
                                        <td style="width:40px;">&nbsp;</td>
                                    </tr>
    <?php if (!isset($heid)) { ?>
                                        <tr class="tdata">
                                            <td class="myintrow">1</td>
                                            <td>
        <?php
         //echo $q = "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' AND catalog_product.company_id = 1 ";
         db_pulldown($dbc , 'product_id[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' AND catalog_product.company_id = 1 ",TRUE,TRUE,' id="product_id" '); 
        ?>
<!--                                                <select style="width: 400px" id="product_id" name="product_id[]"  >
           <option value="">== Please Select ==</option>
           </select>-->
                                            </td>
                  <!--                         <td><input   type="text" name="base_price[]" onblur="product_calculate();" value=""  /></td>-->
                                            <td><input style="width: 400px"  type="text" id="quantity" name="quantity[]" onblur="get_scheme();" value=""  /></td>
                                            <td><input type="text" name="scheme[]"   value=""  /></td>
                         <!--                    <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="" /></td>-->
                                            <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                                        </tr>
    <?php
    } else {
        if (!empty($_POST['order_item'])) {
            $inc = 1;
            foreach ($_POST['order_item'] as $inkey => $invalue) {
                ?>
                                                <tr class="tdata">
                                                    <td class="myintrow"><?php echo $inc; ?></td>
                                                    <td>
                <?php db_pulldown($dbc, 'product[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN user_primary_sales_order_details upsod ON upsod.product_id = catalog_product.id INNER JOIN user_primary_sales_order USING(order_id) Where dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ", true, true, '', '', $invalue['product_id']); ?> 
                                                    </td>
<!--                                                    <td><input   type="text" name="base_price[]" onblur="product_calculate();" value="<?php echo $value['rate']; ?>"  /></td>-->

                                                    <td>
                                                        <input type="text" name="quantity[]" onblur="product_calculate();"  value="<?php echo $invalue['quantity']; ?>"  />
                                                    </td>
                                                   <td>
                                                        <input type="text" name="scheme[]" value="<?php echo $invalue['scheme_qty']; ?>"  />
                                                    </td>
                                                <!--    <td><input   type="text" name="prodvalue[]" onblur="product_calculate();" value="<?php echo my2digit($value['quantity'] * $value['rate']); ?>" /></td>-->
                                                     <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/><?php if($inc > 1) { ?> <img  title="less" src="images/less.png" onclick="javascript:addmore_deep('mytable', event);"/> <?php } ?></td>
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
                    <tr>
                        <td colspan="5"><div id="gift1" style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;">Gift Details</div></td>
                    </tr>
                    
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
                        </td>
                    </tr>
                    <!-- form design for feeding gift details in UI PART End here -->
                    <tr>
                        <td colspan="7">Remarks<br>
                            <textarea name="remarks"><?php if (isset($_POST['remarks'])) echo $_POST['remarks']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="7">
    <?php //form_buttons(); // All the form control button, defined in common_function ?>
                            <input style="background-color: #428bca;width:6%;" id="mysave" type="submit" name="submit" value="<?php if (isset($heid)) echo'Update';
    else echo'Save'; ?>" />
    <?php if (isset($heid)) {
        echo $heid; //A hidden field name eid, whose value will be equal to the edit id. ?>
                                <input style="background-color: #87B87F;width:6%;" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>" />  
                                <input style="background-color: #ba403a;width:6%;" onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
                                        <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
                                    <?php } else { ?>
                                <input style="background-color: #ba403a;width:6%;" onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
                                    <?php } ?>
                        </td>
                    </tr>
                </table>
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
                                <div class="col-xs-2">Company<br>
                                        <?php
                                        $q = "Select id,name from company ";
                                    db_pulldownsmall($dbc,'company_id', $q,TRUE,TRUE,'','',1);
                                        ?>
                                   </div>
               <div class="col-xs-2">From Date<br />
                                        <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if (isset($_POST['from_date'])) echo $_POST['from_date'];
                        else echo date('d/M/Y'); ?>" />
                        </div>
               <div class="col-xs-2">To Date<br />
                      <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if (isset($_POST['to_date'])) echo $_POST['to_date'];
                        else echo date('d/M/Y'); ?>" />
                                    </div>
               <div class="col-xs-6"> <br>
                   <input class="btn btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
                          <input class="btn btn-danger" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                          <input  class="btn btn-success" onclick="$.colorbox({href: 'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe: true, width: '95%', height: '95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />

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
                   Search Order Details &nbsp;&nbsp;&nbsp; <button name="button" onclick="FormSubmit();" >
<span class="label label-lg label-yellow arrowed-in arrowed-in-right">Make Challan</span></button><div class="pull-right tableTools-container"> </div> 
                   
                </div>

                <!-- div.table-responsive -->
<?php
}
//pre($rs);}
?>
                <!-- div.dataTables_borderWrap -->
                <div>
                   
                                            