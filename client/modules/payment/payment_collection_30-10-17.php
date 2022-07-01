<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
$forma = 'Payment Collection'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$myobj_pay = new payment();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
$myorderby = 'challan_order.ch_no ASC'; // The orderby clause for fetching of the data
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

if(isset($_POST['Submit2']) && $_POST['Submit2']=='Save Payment'){

    $data = $myobj_pay->save_payment();

                ?><script>
                    setTimeout("window.parent.location = 'index.php?option=payment'", 500);
                </script>
<?php
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
            /*if (!empty($_POST['datepref'])) {
                $filterstr[] = '<b>DatePref : </b>' . $mymatch['datepref'][$_POST['datepref']];
            }*/
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
             $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
             $filter[] = "payment_status != '1'";
            //echo $funcname;die;
            //$rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby"); // $myobj->get_item_category_list()
            $rs = $myobj->get_payment_data($filter, $records = '', $orderby = "GROUP BY ch_retailer_id"); // $myobj->get_item_category_list()
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
    //echo"hgfjh";
       $filter[] = "ch_dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
             $filter[] = "payment_status != '1'";
      $rs = $myobj->get_payment_data($filter, $records = '', $orderby = "GROUP BY ch_retailer_id"); // $myobj->get_item_category_list()
            
    // echo $funcname;die;
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

  function FormSubmit()
    {
       // alert('kkkkkkkkkkkkkkkkkkkkkk');exit;
        var order = document.getElementsByName('chlan_id[]');
        var retailer_id = document.getElementById('retailerId').value;
        var len = order.length;
        var str = '';
        //alert(order);
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
       //alert(total_val);
       console.log(total_val);
       
        if (str != '') {
            $.colorbox({href: 'index.php?option=payment-collection&showmode=1&mode=1&total_val=' +total_val+'&order_id='+str+'&retailer_id='+retailer_id, iframe: true, width: '95%', height: '95%'});
            return true;
        }
        else
            return false;
    }

function get_available_rate(mrp_id,mrp_value,event){
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
     // alert(prod_id);
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

function popupClosing() {
  alert('About to refresh');
  location.reload();100.00
}

 function hideDiv(value){
    if(value == 0) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='hidden';
        document.getElementById('td2').style.visibility='hidden';
         document.getElementById('td3').style.visibility='hidden';
         document.getElementById('td5').style.visibility='hidden';
         document.getElementById('td6').style.visibility='hidden';
    }
     if(value == 1) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='visible';
        document.getElementById('td2').style.visibility='visible';
         document.getElementById('td3').style.visibility='visible';
         document.getElementById('td5').style.visibility='hidden';
         document.getElementById('td6').style.visibility='hidden';
       
       
    }
     if(value == 2) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='visible';
        document.getElementById('td5').style.visibility='visible';
         document.getElementById('td6').style.visibility='visible';
        document.getElementById('td2').style.visibility='hidden';
         document.getElementById('td3').style.visibility='hidden';
       
       
       
    }
}
</script>
<div id="workarea">
<?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
       // echo 'hiiiiiiiiiiiiii';die;
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
    <?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1) { // to show the form when and only when needed?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
         <!--<td><span class="star">*</span>Total Amount<br>-->
             <td>
            <!--<input type="text" name="total_amount_pay" value="<?php echo $_GET['total_val']?>" disabled="disabled">-->
              <input type="hidden" name="total_amount" value="<?php echo $_GET['total_val']?>" >
              <input type="hidden" name="challan_id" value="<?php echo $_GET['order_id']?>" >
               <input type="hidden" name="retailer_id" value="<?php echo $_GET['retailer']?>" >
               
               <table border="3" height="100%" width="250%"> <tr>
             <td width='33%'>
                 <strong>Invoice No.</strong>
             </td>
             <td width='33%'>
                 <strong>Amount</strong>
             </td >
             <td width='33%'> <strong>Remaining</strong></td>
         </tr>
         
              <?php
              $retailer = trim($_GET['retailer']);
              $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
            //  $ch = rtrim($_GET['order_id'],',');
             // $challan_id=explode(',',$ch);?>
               <?php //$size=  sizeof($challan_id);
              // for($i=0;$i<$size;$i++){
               //  pre($challan_id[0]);
               $str= array();
               $total_amount=0;
               $qwer="SELECT  id,`challan_order`.`ch_no` as ch_no ,`challan_order`.`remaining` as remaining
                    FROM  `challan_order` 
                    WHERE  `ch_retailer_id`='$retailer' AND `ch_dealer_id`='$dealer_id' AND `payment_status` != '1' order by auto ASC";
               $rs1=mysqli_query($dbc, $qwer);
             //  h1($qwer);
                while ($row1 = mysqli_fetch_assoc($rs1)){
                   // echo "Ankush";
                    $ch_no=$row1['ch_no'];
                    $remain_amt1=$row1['remaining'];
                    $remain_amt=round($remain_amt1,2);
                    $total_amount = $total_amount+$remain_amt;
                    //$remain_amt=number_format((float)$remain_amt1, 2, '.', '');
                  echo '<tr>'
                    .'<td>'.$ch_no.'</td>'.'<td>'.'₹ '.$remain_amt.'</td>
                        <td>₹<input type="text" style="width:100px" name="remain_amount[]" value="'.$remain_amt.'" readonly/> </td>'
                    . '</tr>';
                    echo'<input type="hidden" name="ch_id[]" value="'.$row1['id'].'" >';
//                  $str = $row1['id'];
                }//}
               
                       ?>
          
        <input type="hidden" name="dealer_id" value="<?php echo $dealer_id?>" >
        
         <style type="text/css">
            input {font-weight:bold;}
         </style>
         <tr><td><strong>Total Amount:</strong></td>
            <?php $total_amt1=number_format((float)$total_amount, 2, '.', '');
            $total_amt1=round($total_amount,2);
            ?>
             <td> <b><input type="text" name="total_amount_pay" class="total_amount_pay" value="<?php echo '₹ '.$total_amt1?>" disabled="disabled" style="color:RED"></b></td>
             <td>
                ₹ <input type="text" name="remain_amt" class="remain_amt" placeholder="Enter Amount" style="width:150px" required/>
             </td>
         </tr>
               </table>
                <!--<input type="text" name="challan_id" value="<?php print_r( $challan_id);?>" >-->
         </td>
         <tr>
           <td><span class="star">*</span>Payment Type<br />
               <select onchange="hideDiv(this.value)" name="pay_mode" width="20px" required>
                   <option value="">== Please Select ==</option>
                   <option value="0">By Cash</option>
                   <option value="1">By Cheque</option>
                   <option value="2">By RTGS</option>
               </select>
            </td> 
         </tr>
           
            <tr> 
                 <td id="td4" style="visibility: hidden">Remark<br />
                <input  name="Remark" id="Remark"  value="<?php if(isset($_POST['amount'])) echo $_POST['amount']; ?>"  />
            </td> 
                <td id="td1" style="visibility: hidden">Bank Name<br />
                    <input   name="bank_name" id="bank_name" value="<?php if(isset($_POST['bank_name'])) echo $_POST['bank_name']; ?>" > </td> 
            <td id="td2" style="visibility: hidden">Cheque No<br />
                <input id="chq_no"  name="chq_no"  value="<?php if(isset($_POST['chq_no'])) echo $_POST['chq_no']; ?>"  />            </td> 
             <td id="td3" style="visibility: hidden">Cheque Date<br />
                 <input class="qdatepicker" id="chq_date"  name="chq_date"  value="<?php if(isset($_POST['cheque_date'])) echo $_POST['cheque_no']; ?>"  />            </td> 
            
            </tr><tr> 
           <td id="td5" style="visibility: hidden">TXN No<br />
                <input id="txn_no"  name="txn_no"  value="<?php if(isset($_POST['txn_no'])) echo $_POST['txn_no']; ?>"  />            </td> 
             <td id="td6" style="visibility: hidden">TXN Date<br />
                 <input class="qdatepicker" id="txn_date"  name="txn_date"  value="<?php if(isset($_POST['txn_date'])) echo $_POST['txn_date']; ?>"  />            </td> 
            </tr>
                      
         <tr>
             <td colspan="5" align="center">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="Submit2" class="submit2" style="background-color: #438EB9" value="<?php if(isset($heid)) echo'Update'; else echo'Save Payment';?>" />
            <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
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
    <?php } else {//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here  ?>

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
                    <!-- <td><span class="star">*</span>DatePref<br />   
<?php echo arr_pulldown('datepref', $mymatch['datepref'], '', true, true, '', false, 'Select Pref Date'); ?>
                    </td> -->
                    <td><span class="star">*</span>Start<br />  
                        <input type="text" class="qdatepicker" name="start" value="<?php if (isset($_POST['start'])) echo $_POST['start'];
else echo '01/' . date('m/Y'); ?>"/>
                    </td>
                    <td><span class="star">*</span>End<br />    
                        <input type="text" class="qdatepicker" name="end" value="<?php if (isset($_POST['end'])) echo $_POST['end'];
else echo date('d/m/Y'); ?>" onblur="this.value = ucwords(trim(this.value));"/>
                    </td>
                    <td>
                    <strong>Beat</strong><br />
                <!-- <div style="float:left; "> <input type="radio" style="float:left" name="search"  onclick="return search_user(2)" />  -->

                 
                      <?php

                      $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                       db_pulldown($dbc, 'location_id', "SELECT location_id as beat_id,(select name from location_5 where id=dealer_location_rate_list.location_id) as beat_name FROM `dealer_location_rate_list` WHERE `dealer_id` = '$dealer_id'", true, true, ''); ?> 
                    </td>                  
                    <td>Retailer<br/>
                        <?php
                       
                         
                            $q = "SELECT retailer.id as id ,concat(retailer.name,' [',location_5.name,']') as `name` from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where dealer_id='$dealer_id' order by retailer.name asc";
                            db_pulldown($dbc, 'retailer_id', $q, true, true, 'id="retailerId"','==Please Select==',$_POST['retailer_id']);
                        ?>
                    </td>
                   <!--  <td>Challan No<br />
                        <input type="text" name="ch_no" id="invnum" value="<?php if (isset($_POST['ch_no'])) echo $_POST['ch_no']; ?>" /> 
                    </td> -->
                    <td>
                        <input id="mysave" type="submit" name="filter" value="Filter"  class="btn btn-sm btn-info"/>
                        <!--<input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->
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
                    Payment Collection &nbsp; &nbsp;&nbsp; 
                    <!--<span class="label label-lg label-yellow arrowed-in arrowed-in-right"><a style="color:red" href="javascript:void(0)" onclick="FormSubmit();" >Make Payment</a></b></span>--> 
                    <div class="pull-right tableTools-container"></div>
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
                                            <th class="sno" style="width:6%;">S.No </th>  <!--<input type="checkbox" id="myselect" onclick="check_all_for_print()" />-->
                                            <th>Retailer Name</th>
                                            <th>Last Payment Date</th>
                                            <th>Last Payment</th>
                                            <th>Outstanding</th>
<!--                                             <th>Claim</th>-->
                                           <!--  <td>Item Details</td> -->
                                            <th>Action</th>
                                            <!-- <td class="options">Options</td> -->
                                        </tr>
                        </thead>
            <?php
              
            $bg = TR_ROW_COLOR1;
            //$inc = 1;
            if (isset($_GET['ajaxshow']))
                ob_end_clean(); // to help refresh a single row
        $surcharge=  myrowval('state', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
//$result = $myobj->print_looper_invoice($multiId=1, $options=array());
            //pre($rs);
       
            foreach ($rs as $key => $rows) {
                //pre($rs);
                  $ret=mysqli_query($dbc,"SELECT retailer.name from retailer WHERE id=$rows[retailer_id] ");
                   $rowss= mysqli_fetch_assoc($ret);
                   $name=$rowss['name']; 
                $bg = ($bg == TR_ROW_COLOR1 ? TR_ROW_COLOR2 : TR_ROW_COLOR1); // to provide different row colors(member_contacted table)
                $uid = $rows['ch_id'];
                $uidname = $rows['ch_no'];
                $dispatch = $rows['dispatch_status'];
                if($dispatch==1)
                {
                 $dispatch='Dispatched';   
                }
               else if($dispatch==0)
                {
                 $dispatch='Pending';   
                }
                else $dispatch='Pending';  
                
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
                  $retailer =  $rows['retailer_id'];
               
                echo'
                      <tr BGCOLOR="' . $bg . '" id="tr' . $uid . '" class="ihighlight">
                        <td class="myintrow myresultrow">' . $inc .'</td>
                        <td><strong>' . $rows['retailer_name'] . '</strong></td>
                        <td>' . $rows['last']['lastdate'] . '<div style="display:none" id="delDiv' . $uid . '"></div></td>
                         <td>₹ ' . my2digit($rows['last']['lastamt']) . '</td>
                        <td>₹ ' . my2digit($rows['total']) . '</td>
<!--                            <td></td>  -->
                       <td><span class="btn btn-sm btn-warning"><a style="color:#fff;"
                        class="iframef" href="index.php?option=payment&showmode=1&mode=1&retailer='.$retailer.'">Collect Payment</a></b></span></td>
                        ';
              
                  
                      echo'</tr>
                      ';
                $inc++;
            }// foreach loop ends here
            if (isset($_GET['ajaxshow']))
                exit(); // to help refresh a single row
            ?>
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
                                                null,null,null,null,null,null,
                                                 
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


        $('.submit2').click(function(){

            var r_amt = parseInt($('.remain_amt').val());
            var str = $('.total_amount_pay').val();
            var t_amt = parseInt(str.replace ('₹', ""));

            if(r_amt > t_amt)
            {
                alert('Remaining amount is greater then total amount.');
                return false;
            }

        })

        </script>
     
    
