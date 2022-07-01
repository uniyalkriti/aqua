<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php

$forma = 'Paid Payment'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$myobj_pay = new payment();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
$myorderby = 'challan_order.ch_no DESC'; // The orderby clause for fetching of the data
$myfilter = 'challan_order.id = '; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
?>

<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################

function checkform($mode = 'add', $id = '') {
    global $dbc;
    if ($mode == 'filter')
        return array(TRUE, '');
    /* $field_arry = array('partyname' => $_POST['partyname']);// checking for  duplicate Unit Name
      if($mode == 'add')
      {
      if(uniqcheck_msg($dbc,$field_arry,'party', false, " ptype=1"))
      return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
      }
      elseif($mode == 'edit')
      {
      if(uniqcheck_msg($dbc,$field_arry,'party', false," partyId != '$_GET[id]' AND ptype = 1"))
      return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
      } */
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
            
            if (!empty($_POST['start'])) {
                $start = get_mysql_date($_POST['start'], '/', $time = false, $mysqlsearch = true);
               // $filter[] = "DATE_FORMAT({$_POST['datepref']},'" . MYSQL_DATE_SEARCH . "') >= '$start'";
                $filter[] = "DATE_FORMAT(pay_date_time,'%Y%m%d') >= '$start'";
                $filterstr[] = '<b>Start : </b>' . $_POST['start'];
            }
            if (!empty($_POST['end'])) {
                $end = get_mysql_date($_POST['end'], '/', $time = false, $mysqlsearch = true);
               // $filter[] = "DATE_FORMAT({$_POST['end']},'" . MYSQL_DATE_SEARCH . "') <= '$end'";
                $filter[] = "DATE_FORMAT(pay_date_time,'%Y%m%d') <= '$end'";
                $filterstr[] = '<b>End : </b>' . $_POST['end'];
            }
            
            if (!empty($_POST['retailer_id'])) {
                $filter[] = "ch_retailer_id = '".$_POST['retailer_id']."'";
                //$filterstr[] = '<b>Retailer : </b>' . $_POST['ch_no'];
            }
             $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
             $filter[] = "payment_status != '0'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            //echo implode(',',$filter);die;
           // echo $funcname;
           // $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby"); 
            $rs = $myobj->get_paid_challan_list($filter, $records = '', $orderby = "ORDER BY $myorderby"); 
               // echo "<pre>";
            //print_r($rs);
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
   // echo $funcname;
    // $filter = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
    //$rs = $myobj->get_challan_list_for_payment($filter, $records = '', $orderby = "ORDER BY $myorderby");
}
dynamic_js_enhancement();
//pre($rs);

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

      function FormSubmit()
    {
        var order = document.getElementsByName('chlan_id[]');
        var retailer_id = document.getElementById('retailerId').value;
        var len = order.length;
        var str = '';
        //alert(len);
         var total_val=0;
        for (var i = 0; i < len; i++)
        {
            if (order[i].checked) {
                str += order[i].value + ',';
                total= document.getElementById('total_'+order[i].value).innerHTML;
               total_val += parseFloat(total);
              // total_val.push(parseInt(total));
             // total_val = total;
                //alert(total_val);
                //console.log(total_val);
            }
        }
       //var fnl_total =  total_val.reduce(getSum);
       //console.log(total_val);
       console.log(total_val);
       
        if (str != '') {
            $.colorbox({href: 'index.php?option=payment-collection&showmode=1&mode=1&total_val=' +total_val+'&order_id='+str+'&retailer_id='+retailer_id, iframe: true, width: '95%', height: '95%'});
            return true;
        }
        else
            return false;
    }
    function getSum(total, num) {
    return total + num;
}

   function custum_function(pid, pvalue, event) {
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

function popupClosing() {
  alert('About to refresh');
  window.location.href = window.location.href;
}

/*function print_all_selected(id,chkval,chkname){
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
}*/
</script>
<div id="workarea">
<?php
//This block of code will help in the print work
/*if (isset($_GET['actiontype'])) {
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
}*/
//This block of code will help in the print work ens
?>
    <?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1){ ?>

 <form method="post" action="" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
            
                      
            <td>
            <span class="star"></span>Payment Amount<br />
            <input type="text" name="total_amount_view" value="<?php echo $total_val; ?>" disabled>
            <input type="hidden" name="total_amount" value="<?php echo $total_val; ?>">
            <input type="hidden" name="retailer_id" value="<?php echo $retailer_id; ?>">
            <input type="hidden" name="challan_id" value="<?php echo $order_id; ?>">
            </td>            
           <td><span class="star">*</span>Payment Type<br />
               <select onchange="hideDiv(this.value)" name="pay_mode">
                   <option value="">== Please Select ==</option>
                   <option value="0" selected="selected">By Cash</option>
                   <option value="1">By Cheque</option>
               </select>
            </td>  
            </tr>
            <tr> 
                <!--  <td id="td4" style="visibility: hidden">Amount<br />
                <input type="text"  name="amount" id="amount"  value="<?php if(isset($_POST['amount'])) echo $_POST['amount']; ?>"  />
            </td> --> 
             <td id="td4" style="">Remark<br />
                    <input   name="Remark" id="Remark" value="" >            </td> 
                <td id="td1" style="visibility: hidden">Bank Name<br />
                    <input   name="bank_name" id="bank_name" value="<?php if(isset($_POST['bank_name'])) echo $_POST['bank_name']; ?>" >            </td> 
            <td id="td2" style="visibility: hidden">Cheque No<br />
                <input  type="text" id="chq_no"  name="chq_no"  value="<?php if(isset($_POST['chq_no'])) echo $_POST['chq_no']; ?>"  />            </td> 
             <td id="td3" style="visibility: hidden">Cheque Date<br />
                 <input  type="text"  class="qdatepicker" id="chq_date"  name="chq_date"  value="<?php if(isset($_POST['cheque_date'])) echo $_POST['cheque_no']; ?>"  />            </td>

          
             
            
         </tr>                 
         <tr>
             <td colspan="5" align="center">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="Submit2" value="<?php if(isset($heid)) echo'Update'; else echo'Save Payment';?>" />
            <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input onclick="after_close()" type="button" value="Exit" /> <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
            <?php }else{?>
            <input onclick="after_close();" type="button" value="Close" />
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
 <script>
 function after_close(){
//alert('close');

    parent.$.fn.colorbox.close();
    //parent.parent.$.fn.colorbox.reload();
   // window.location = "index.php?option=payment-collection";
   // header("Location: index.php?option=payment-collection");  
   /* $.colorbox.onunload=function () {
         alert('hii closed');
      window.parent.popupClosing()
    };*/
   
    //window.opener.location.reload();
 }

function popupClosing() {
  alert('About to refresh');
  location.reload();
}

 function hideDiv(value){
    if(value == 0) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='hidden';
        document.getElementById('td2').style.visibility='hidden';
         document.getElementById('td3').style.visibility='hidden';
    }
     if(value == 1) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='visible';
        document.getElementById('td2').style.visibility='visible';
         document.getElementById('td3').style.visibility='visible';
       
       
    }
}
 </script>



<?php
 } else {//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here  ?>

        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">
                <tr id="mysearchfilter">
                    <td>
                        <!-- this table will contain our form filter code starts -->
                        <fieldset>
                            <!--<legend class="legend">Search <?php echo $forma; ?></legend>-->
            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
            <table>
                <tr>
                    
                    <td><span class="star">*</span>Start<br />	
                        <input type="text" class="qdatepicker" name="start" value="<?php if (isset($_POST['start'])) echo $_POST['start'];
else echo '01/' . date('m/Y'); ?>"/>
                    </td>
                    <td><span class="star">*</span>End<br />	
                        <input type="text" class="qdatepicker" name="end" value="<?php if (isset($_POST['end'])) echo $_POST['end'];
else echo date('d/m/Y'); ?>" onblur="this.value = ucwords(trim(this.value));"/>
                    </td>                  
                    <td width="50%">Retailer<br/>
                        <?php
                       
                         $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                            /*$q = "SELECT retailer.id as id ,concat(retailer.name,' [',location_5.name,']') as `name` from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where dealer_id='$dealer_id' order by retailer.name asc";*/
                            $q = "SELECT retailer.id as id ,concat(retailer.name,' [',retailer.address,']') as `name` from retailer where dealer_id='$dealer_id' AND retailer.retailer_status='1' order by retailer.name asc";
                            db_pulldown($dbc, 'retailer_id', $q, true, true, 'id="retailerId" class="chosen-select"','==Please Select==',$_POST['retailer_id']);
                        ?>
                    </td>
                    <!-- <td>Challan No<br />
                        <input type="text" name="ch_no" id="invnum" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no']; ?>" /> 
                    </td> -->
                    <td>
                        <input id="mysave" type="submit" name="filter" value="Filter"  class="btn btn-sm btn-info"/>
                        <!-- <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" /> -->
<!--                                        <input onclick="$.colorbox({href: 'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe: true, width: '95%', height: '95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                        <!-- this table will contain our form filter code ends -->           
                    </td>
                </tr>
    <?php
    if (isset($_GET['ajaxshowblank']))
        ob_end_clean(); // to show the first row when parent table not avialable
    if (!empty($rs)) { //if no content available present no need to show the bottom part
        ?>
                   
                    <tr>
                        <td>    
                            <div class="row">
    <div class="col-xs-12">
         <div class="row">
            <div class="col-xs-12">
              
                <br>
              
                <div class="table-header">
                Payment Collection Report <div class="pull-right tableTools-container"></div>
                </div>
                </div>
        <?php
        ########################## pagination details fetch starts here ###################################
$inc=1;
            ?>	 

                          
                                    <div>
                                        <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead> 
                                        <tr class="search1tr">
                                            <th class="sno">S.No </th>  <!--<input type="checkbox" id="myselect" onclick="check_all_for_print()" />-->
                                            <th>Invoice Date</th>
                                            <th>Payment Date</th>
                                            <th>Invoice No.</th>
                                            <th>Dispatch Status</th>
                                            <th>Payment Status</th>
                                            <th>Retailer</th>                 
                                            <th>Amount</th>
                                           
                                        </tr>
                        </thead>
                        <tbody>
            <?php
            $bg = TR_ROW_COLOR1;
            //$inc = 1;
            if (isset($_GET['ajaxshow']))
                ob_end_clean(); // to help refresh a single row
        $surcharge=  myrowval('state', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
//$result = $myobj->print_looper_invoice($multiId=1, $options=array());
           // pre($rs);
        $gtotal=0;
            foreach ($rs as $key => $rows) {
                $bg = ($bg == TR_ROW_COLOR1 ? TR_ROW_COLOR2 : TR_ROW_COLOR1); // to provide different row colors(member_contacted table)
                $uid = $rows['id'];
                $uidname = $rows['ch_no'];
                $pstatus=$rows['payment_status'];
                 $total=$rows['amount']-$rows['remaining'];
                 $gtotal+=$total;
                if($pstatus==1)
                    $pstat="Paid";
                 if($pstatus==2)
                     $pstat="Partially Paid";
                //$temp_date = date('Y-m-d',  strtotime($rows['ch_date']));                
                 $frz_date = date("Y-m-d", strtotime($rows['ch_date'].'+2 days'));
                 if($frz_date>=date('Y-m-d')){
                 $editlink = '<a class="iframef" href="index.php?option=direct-challan&showmode=1&mode=1&id='. $uid . '"><img src="./images/b_edit.png"></a>';
                 }  else {
                 $editlink = '';    
                 }
                //$schedulelink = $rows['potype'] == 2 ? '<span class="seperator">|</span> <a class="iframef" title="Material Schedule against PO '.$uidname.'" href="index.php?option=po-material-schedule&showmode=1&poid='.$uid.'"><img src="../icon-system/i16X16/osent.png"></a>' : '';
                $printlink = '<span class="seperator">|</span> <a class="iframef" title="print Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>'; //$rows['postat'] == 1 ? '<span class="seperator">|</span> <a class="iframef" title="print PO '.$uidname.'" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>' : '';
                $deletelink = ''; //'<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Vendor Delete\', \''.$uid.'\',\'Vendor\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                //if($rows['locked'] == 1) $editlink = $deletelink = '';
                if ($auth['del_opt'] != 1)
                    $deletelink = '';
                echo'
                      <tr BGCOLOR="' . $bg . '" id="tr' . $uid . '" class="ihighlight">
                        <td class="myintrow myresultrow">' . $inc . '</td>
                        <td><strong>' . date('d-m-Y',strtotime($rows['ch_date'])) . '</strong></td>
                        <td><strong>' . date('d-m-Y',strtotime($rows['payment_date'])) . '</strong></td>
                        <td>' . $uidname . '<div style="display:none" id="delDiv' . $uid . '"></div></td>
                        <td>' . $rows['is_dispatch'] . '</td>
                        <td>' . $pstat . '</td>
                            
                        <td>' . $rows['retailer_name'] . '</td>
                           
                       ';
                echo'<td><strong><img src="../icon-system/i16X16/rupee.png"><span id="total_'.$uid.'">' . my2digit($total) . '</span></strong></td>
                           </tr>';
                // showing the po item details starts here 
            /*    echo'
                        <table style="border:none; border-collapse:collapse">
                          <tr style="font-weight:bold;">
                                <td style="border:none;">Item Name</td>
                                <td style="border:none;">Rate</td>
                                <td style="border:none;">Com.Code</td>
                                <td style="border:none;">Qty</td>
                                 <td style="border:none;">Sch.</td>                                
                                 <td style="border:none;"> CD.Amt </td>
                                  <td style="border:none;">VAT Amt</td>
				<td style="border:none;">Tax - able Amt.</td>

                                <td style="border:none;">Surcharge</td>
                                   
                              </tr>';*/
                $taxamount = 0;
                //$total = 0;
                foreach ($rows['challan_item'] as $inkey => $invalue) {
                    $amt = ($invalue['qty'] * $invalue['product_rate']);
                    if($invalue['cd_type']==1){
                    $cd_amt = ($amt * $invalue['cd']) / 100;
                    }elseif($invalue['cd_type']==2){
                        $cd_amt = ($invalue['cd']);
                    }
                    //$vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;
                    if($invalue['tax']==0){
                    $vat_amt1 = (($amt - $cd_amt) * $invalue['tax']) / 100;}
                   else {
                       $vat_amt1=$amt*5/100;
                       
                 }
                    
                    $vat_amt = $vat_amt1*($surcharge/100);
                    $surcharge_amt= $vat_amt;
                   /* echo'<tr>
                                        <td style="border:none;">' . $invalue['name'] . '</td>
                                        <td style="border:none;">' . $invalue['product_rate'] . '</td>
                                          <td style="border:none;">' .$invalue['comunity_code'] . '</td>
                                        <td style="border:none;">' . $invalue['qty'] . '</td>
                                        <td style="border:none;">' . $invalue['free_qty'] . '</td>
                                        <td style="border:none;">' . $cd_amt. '</td>  
                                        <td style="border:none;">' . my2digit($vat_amt1) . '</td> 
					<td style="border:none;">' . my2digit($amt) . '</td>
                                        <td style="border:none;">' . my2digit($surcharge_amt) . '</td> 
                                      </tr>';*/
                    //$taxamount +=  $invalue['qty'] * $invalue['product_rate'] * $invalue['tvalue']/100;
                   // $total += $amt - $cd_amt  + $surcharge_amt + $vat_amt1 ;
                    //$total=$invalue['amount']-$invalue['remaining'];
                }
                //echo'</table>';
                // showing the po item details ends here 
//                echo'<td><strong><img src="../icon-system/i16X16/rupee.png"><span id="total_'.$uid.'">' . my2digit($total) . '</span></strong></td>
//                           </tr>';
                $inc++;
            }// foreach loop ends here
            if (isset($_GET['ajaxshow']))
                exit(); // to help refresh a single row
            ?>
            </tbody>
            <tr>
                <td colspan="7"><b>Grand Total:</b></td>
                <td><b><?=$gtotal?></b></td>
            </tr>
                                    </table>                
                                </div> 
                                        <?php echo'</div>';
                                    } // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
                        </td>
                    </tr>
                        <?php } //if(!empty($rs)){ ?>
                        <?php if (isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>
                        

            </table>
              
            </fieldset>
        </form>
        <?php //}//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here ?>
   </div> </div> </div> </div>
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
        <script src="assets/js/jszip.min.js"></script>
        <script src="assets/js/pdfmake.min.js"></script>
        <script src="assets/js/vfs_fonts.js"></script>
        <!-- ace scripts -->
        <script src="assets/js/ace-elements.min.js"></script>
        <script src="assets/js/ace.min.js"></script>

        <script type="text/javascript">
          if(!ace.vars['touch']) {
               $('.chosen-select').chosen({allow_single_deselect:true});
          }
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
                                                null,null,null,null,null,null,null,null,
                                                 
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
     
    
