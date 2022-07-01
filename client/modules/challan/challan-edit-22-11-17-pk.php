<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
include '../../include/date-picker.php';
$forma = 'Direct Invoice Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
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
$dealer_id1 =  $_SESSION[SESS.'data']['dealer_id'];
//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_id_list($dealer_id);
$location_list = implode(',', $location_list);
?>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################

function checkform($mode = 'add', $id = '')
{
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
if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_save';
            $action_status = $myobj->direct_challan_save(); // $myobj->item_category_save()
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
if (isset($_POST['submit']) && $_POST['submit'] == 'Update'){

    /*pre($_POST);
    die;*/

    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'edit', $id = $_POST['eid']);
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = 'direct_challan_edit';
            $action_status = $myobj->$funcname($_POST['eid']); // direct_challan_edit

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


################### code to get the stored info for editing starts here ##################
if (isset($_GET['mode']) && $_GET['mode'] == 1) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        //This will containt the pr no, pr date and other values
        $funcname = 'get_challan_list';
        $mystat = $myobj->$funcname($filter = "challan_order.id ='$id'", $records = '', $orderby = ''); // $myobj->get_item_category_list()
        
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
if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {
    $order_id_str = implode(',', $_POST['order_id']);
    $filter[] = "order_id IN ($order_id_str)";
    $user_data = $myobj->get_dsp_wise_user_data($sesId, $role_id, $dealer_id);
    if (!empty($user_data)) {
        $user_data_str = implode(',', $user_data);
        $filter[] = "user_id IN ($user_data_str)";
    }
    $filter[] = "call_status = '1'";
    $rs = $myobj->$funcname($filter, $records = '', $orderby = '');
    // pre($rs);
}
?>

             <div class="widget-box" >
                 <div class="widget-header widget-header-blue widget-header-flat">
                        <h4 class="widget-title lighter"><?=$_GET['page']?></h4>
                </div>
                 <form class="form-horizontal" method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
        
              <div class="widget-body" >
                        <div class="widget-main">
                            <div id="fuelux-wizard-container" class="no-steps-container">
                                <div class="step-content pos-rel">
                                  <div class="step-pane active" data-step="1" >
                                   <!-- FORM --> 
            <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
            <input type="hidden" id="loc_level" name="loc_level" value="<?php echo $loc_level; ?>">
            <input type="hidden" name="dealer_id" id="dealer_id" value="<?php echo $_SESSION[SESS . 'data']['dealer_id'] ?>">
            <table>
                <tr>
<!--                    <td width="7%">
                        <span class="star">*</span>Company<br> 
                        <?php
                        $js_attr = ' lang="company" onchange="fetch_location(this.value, \'progress_div\', \'product_id\', \'company-catalog\');" ';
                        if (!isset($_POST['company_id'])) {
                            $q = 'SELECT id, name from company';
                            db_pulldown($dbc, 'company_id', $q, TRUE, TRUE, $js_attr, '', 1);
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
                    </td>-->
  
                    <td width="7%"><strong>Date</strong><br>
                        <input class="datepicker-challan-edit" type="text" name="ch_date" value="<?php
                        if (isset($_POST['ch_date']))
                            echo $_POST['ch_date'];
                        else
                            echo date('d/M/Y');
                        ?>">
                    
                        </td>
                     <?php
                 
                   if(isset($_POST['ch_no']) && $_POST['ch_no'] !=''){
                     $exp_array=explode('/',$_POST['ch_no']); 
                     $invoice_id = isset($exp_array[2])?$exp_array[2]:0;

                   }else{

                     $invoice_id = $myobj->get_invoice_no($_SESSION[SESS . 'data']['dealer_id']);
                   }
                   

                    ?>
                    <!-- <td><strong>Inv. No. </strong><br>
                        <input  type="text" name="ch_no" value="<?php echo 'CATC/' . $sesId = $_SESSION[SESS . 'data']['dealer_id'] . '/' . $invoice_id; ?>" >
                    </td> -->

                    <td width="7%"><strong>Inv. No. </strong><br>
                         <?php
                
                $query = "select `ch_no`,`ch_retailer_id` from `challan_order` where `id`='$_GET[id]'";
                //echo $query;

                $q = mysqli_query($dbc,$query);
                $roww = mysqli_fetch_assoc($q);
                $ch_no  = $roww['ch_no'];
                $retailer_id  = $roww['ch_retailer_id'];
                  
              ?>
<!--                        <span><?php// echo 'CATC/' . $sesId = $_SESSION[SESS . 'data']['dealer_id'] . '/'; ?></span>-->
<!--                        <input  type="text" name="ch_no" value="<?php //echo $invoice_id; ?>" >-->
                        <input  type="text" name="ch_no"  value="<?=$ch_no?>" readonly >
                       
                        <input type="hidden" name="ch_no_prifix" value="<?=$ch_no?>">
                    </td>
                    <td width="20%"> 
                        <span class="star">*</span><strong>Retailer Name</strong><br>
                        
<?php
//echo $retailer_id;
//$q = "SELECT retailer.id as id, CONCAT(retailer.name,' [',location_5.name,'] ')as name FROM retailer INNER JOIN user_dealer_retailer udr ON retailer.id = udr.retailer_id INNER JOIN location_5 ON retailer.location_id = location_5.id where udr.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' retailer.id='$retailer_id' ORDER BY name ASC  ";
$qrt = "SELECT retailer.id as id, retailer.name as name FROM retailer WHERE id='$retailer_id'";
$qrtm = mysqli_query($dbc, $qrt);
$qrtf = mysqli_fetch_assoc($qrtm);
?>
                        <select name="retailer_id">
                            <option value="<?=$qrtf['id']?>"><?=$qrtf['name']?></option>
                        </select>
                        <input type="hidden" name="location_id" id="location_id" value="<?php if (isset($_POST['location_id'])) echo $_POST['location_id']; ?>">
                    </td><?php //echo $q;  
               
                   $ch = "SELECT discount_amt,discount_per,amount FROM challan_order WHERE id='$_POST[id]'";
                  // echo $ch;
                    $ch_m = mysqli_query($dbc,$ch);
                    $ch_row = mysqli_fetch_assoc($ch_m);
                    $discount_amt = $ch_row['discount_amt'];
                    $discount_per = $ch_row['discount_per'];
                    $amount = $ch_row['amount'];
                    ?>   
                    
                </tr>
                <tr>
                    <td colspan="5"><hr></td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div id="product" >
<table width="100%" >
    <tr>
        <td colspan="5">
            <table width="100%" id="mytable">
                <tr class="thead" style="font-weight:bold;">
                    <th>S.NO</th>
                    <th>Item Name</th> 
                    <th>Bill of Supply</th> 
                    <th>M.R.P</th>
                    <!--<th>Com. Code</th>-->
                    <th>Avlb. Stock</th>
                    <th>Quantity</th>
                    <!-- <th>Sch. Quantity</th> -->
                    <th>Rate</th>
                    <th>Trade Type</th>
                    <th>Trade/Sch. Disc.</th>                   
                    <th>Trade Amt.</th>
                    <th>C.D Type</th>
                    <th>C.D.</th>                                                
                    <th>CD.Amt</th>
                    <th>Taxable Amt.</th>
                    <th>GST%</th>
                    <th>GST. Amt</th>
                    <th>Amount</th>
                    <th>&nbsp;</th>
                </tr>
                <?php

                $keys = array_keys($_POST['challan_item']);

                if(isset($_POST['challan_item']))
                { 
                    $num_rows = count($_POST['challan_item']);
                }
                else{
                    $num_rows=8;
                }
                
                $id_str = array();
                $qty_str = array();
                $pp = 0;
                for($z=1;$z<=$num_rows;$z++){
                    $avlb_stock = 0;
                    $ttl_amt = 0;                   
                    

                    $avlb_stock = get_avlb_stock_by_prodid_mrp($_POST['challan_item'][$keys[$z-1]]['product_id'],$_POST['challan_item'][$keys[$z-1]]['mrp']);
                    $qty = $_POST['challan_item'][$keys[$z-1]]['qty'];
                    $product_rate = $_POST['challan_item'][$keys[$z-1]]['product_rate'];
                    $dis_amt = $_POST['challan_item'][$keys[$z-1]]['dis_amt'];
                    $comunity_code= $_POST['challan_item'][$keys[$z-1]]['comunity_code'];
                    $dis_type = $_POST['challan_item'][$keys[$z-1]]['dis_type'];
                    $dis_percent = $_POST['challan_item'][$keys[$z-1]]['dis_percent'];
                    $ttl_amt = get_trade_disc_calculate($qty,$product_rate,$dis_amt,$dis_type,$dis_percent);
                    $taxable_amt = $_POST['challan_item'][$keys[$z-1]]['taxable_amt'];
                    $tax = $_POST['challan_item'][$keys[$z-1]]['tax'];
                    $vat_amt1 = $_POST['challan_item'][$keys[$z-1]]['vat_amt'];
                    $cd_amt=$_POST['challan_item'][$keys[$z-1]]['cd_amt'];
                    $trade_dis=$_POST['challan_item'][$keys[$z-1]]['dis_amt'];
                   // $tax = 5;
                    $vat_amt = $taxable_amt*($tax/100); 
                    $amount = $taxable_amt + $vat_amt;
                   // $productamt = $product_rate*$qty-$vat_amt-$cd_amt-$trade_dis;
                    $productamt =$taxable_amt-$vat_amt1;
                      //$taxable_amt1=
                    //  echo $vat_amt1;

                    $qty_str[] = $qty;
                    $id_str[]  = $_POST['challan_item'][$keys[$z-1]]['product_id'];
                    $mrp_str[] = $_POST['challan_item'][$keys[$z-1]]['mrp'];

                    // pre($_POST);
                    if($_POST['challan_item'][$keys[$z-1]]['supply_status']>0)
                    {
                        $supply_status = "checked";
                        $bos_val = 1;
                    }else{
                        $supply_status = "";                        
                        $bos_val = 0;
                    }


                    $amount1 += $taxable_amt;
                    $pid = $_POST['challan_item'][$keys[$z-1]]['product_id'];
                    ?>
                    <tr class="tdata">
                        <td class="myintrow"><?=$z?></td>
                        <td width="10%">
                           <?php
                           /*$q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY name ASC';*/

                           $q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN stock s ON cp.id = s.product_id where s.dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' AND s.qty>=0 ORDER BY cp.name ASC';

                           db_pulldown170($dbc,  'product_id[]', $q, TRUE, TRUE,'id="product_id'.$z.'" class="item_details" ','',$_POST['challan_item'][$keys[$z-1]]['product_id']);
                           ?>   
                       </td>
                       <td><input type="checkbox" name="bos[<?php echo $pp++ ?>]" <?php echo $supply_status ?> value="1"></td>
                       <td>
                        <!-- onblur="get_available_stock(this.id,this.value,event);" -->

                        <select style="width:80px" name="mrp[]" id="mrp<?=$z?>" placeholder="MRP" class="mrp mrp_dd" >
                          <?php 
                          $mrp_list = $myobj->get_mrp_list($pid,$dealer_id1);
                          $mrp = $_POST['challan_item'][$keys[$z-1]]['mrp'];

                          foreach($mrp_list as $m)
                          { 
                            $selected = ($mrp==$m)?"selected='selected'":'';
                            ?>
                            <option value="<?php echo $m ?>" <?php echo $selected ?>>
                                <?php echo $m ?>
                            </option>
                            <?php }  ?>
                        </select>
                    </td>
                       <!--<td>
                        <input style="width:90%" placeholder="Comunity Code" id="comunity_code" type="text" name="comunity_code[]" value="<?=$comunity_code?>"/>
                 
                    </td> -->
                    <td>
                        <input style="width:98%" placeholder="Avlb. Stock" id="aval_stock" type="text" name="aval_stock[]" onchange="challan_calculate();" id="aval_stock2" value="<?=$avlb_stock?>" class="avlb_quantity" readonly="true"/>

                    </td>

                    <td><input style="width:98%" placeholder="Quantity" type="text"  name="quantity[]" id="quantity" onchange="updateInput(<?=$z?>)" onblur="product_calculate();calc_total_amount();" value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>"  class="quantitycl"/>
                        <input type="hidden" name="ordered_qty[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>"  />
                    </td>
                    <!-- <td>
                        <input style="width:98%" placeholder="Sch. Qty" type="text" name="scheme[]" id="scheme" value="<?=$_POST['challan_item'][$keys[$z-1]]['free_qty']?>"  />
                    </td> -->
                    <td>
                       <input style="width:90%" placeholder="Rate" type="text" name="rate[]" id="rate" onblur="product_calculate();calc_total_amount();" value="<?=$_POST['challan_item'][$keys[$z-1]]['product_rate']?>" class="rate"/>
                   </td>
                   <td>
                    <select name="trade_disc_type[]" lang="trade_disc" style="width:98%" >
                        <option value="1" <?php if($_POST['challan_item'][$keys[$z-1]]['dis_type']=='1'){ echo "selected='selected'"; }?>>%</option>dis_type
                        <option value="2" <?php if($_POST['challan_item'][$keys[$z-1]]['dis_type']=='2'){ echo "selected='selected'"; }?>>Amount</option>
                    </select>
                </td>
                <td>
                    <input style="width:98%" placeholder="Trade" type="text" name="trade_disc_val[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['dis_percent']?>"  onblur="trade_disc_calculate();product_calculate();calc_total_amount();"  />
                </td>   

                <td>
                    <input style="width:90%" placeholder="Trade Amt." type="text" name="trade_disc_amt[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['dis_amt']?>"  />
                    <input type="hidden" name="ttl_amt[]" value="<?=$ttl_amt?>"   />
                </td>
                <td>
                    <select name="cd_type[]" lang="cdtype" onchange="product_calculate()" style="width:98%"> 
                        <option value="1" <?php if($_POST['challan_item'][$keys[$z-1]]['cd_type']=='1'){ echo "selected='selected'"; }?>>%</option>
                        <option value="2" <?php if($_POST['challan_item'][$keys[$z-1]]['cd_type']=='2'){ echo "selected='selected'"; }?>>Amount</option>
                        <!--                            <option value="3"> Kg </option>-->
                    </select>
                </td>
                <td>
                    <input type="text" style="width:98%" placeholder="CD" name="cd[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['cd']?>"  onblur="product_calculate();calc_total_amount();" />
                </td>

                <td>
                    <input type="text" style="width:90%" placeholder="cd amt" name="cd_amt[]" onblur="product_calculate();calc_total_amount();" value="<?=$_POST['challan_item'][$keys[$z-1]]['cd_amt']?>"  />
                </td>
                <td>
                    <input type="text"  style="width:98%" placeholder="Taxable" name="taxable_amt[]"  value="<?=number_format($productamt,2)?>"   />
                </td>
                <td>
                    <input type="hidden"  style="width:98%" placeholder="State" name="state[]" id="state" value="<?=$state_id?>"  />

                    <!--<input   type="text"  style="width:98%" placeholder="VAT" name="vat[]" id="vat"  value="<?=$_POST['challan_item'][$keys[$z-1]]['tax']?>"/></td>-->
                    <input   type="text"  style="width:98%" placeholder="GST" name="vat[]" id="vat<?=$z?>" value="<?=number_format($_POST['challan_item'][$keys[$z-1]]['tax'],2)?>" class="vat"/>
                    <td>
                        <input   type="text" name="vat_amt[]" style="width:98%" placeholder="Gst"  id="vat_amt"  value="<?=$vat_amt1?>"  />
                        <input   type="hidden" name="surcharge[]" id="surcharge"  value="<?php echo $surcharge;?>"  />


                    </td>
                    <td><input type="text" style="width:98%" placeholder="Amount" name="amount[]" id="amount" value="<?=$_POST['challan_item'][$keys[$z-1]]['taxable_amt']?>" class="amount" /></td>
                    <td><img  title="more" src="images/more.png" class="addbutton"/>
                      <img  title="more" src="images/less.png" class="removebutton"/></td>

                  </tr>
                  <?php } ?>
              </table>            
</td>
    </tr>
    <tr><td colspan="16"><hr/></td></tr>
    <tr>
            <td colspan="3" style="width: 850px"></td>
            <td colspan="3">

                <table style="width: 100%;">
                    <tr>
                        <td><strong>Amount</strong></td>
                        <td><strong><input type="text" name="" id="total_disc2" class="" value="<?php echo $amount1 ?>" readonly="" style="width: 150px;"></strong> </td>
                    </tr>
                    <tr>
                        <td><strong> Enter Discount % </strong></td>
                        <td>
                            <!-- <select name="dis" id="dis" onchange="getTotal(this.value);" style="width: 150px;">
                             <option value="">SELECT DISCOUNT</option>
                             <option value="2" <?php //if($discount_per==2)echo "selected";?>> 2% </option>
                             <option value="3" <?php //if($discount_per==3) echo "selected";?>> 3% </option>
                             <option value="4" <?php //if($discount_per==4) echo "selected";?>> 4% </option>
                             <option value="5" <?php //if($discount_per==5) echo "selected";?>> 5% </option>
                             </select> -->
                             <?php $class = 'dis'.$id; ?>
                             <input type="text" name="dis" id="dis" class="<?php echo $class?>" onkeyup="getTotal(this.value,'<?php echo $class?>');" style="width: 150px;" value="<?php echo $discount_per ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount</strong></td>
                        <td>
                            <strong>
                                <?php $final_amt = $amount1-($amount1*$discount_per/100) ?>
                                <input type="text" readonly="" class="totamt" name="totamt" placeholder="<?php echo round($final_amt,2) ?>" value="<?php echo round($final_amt,2) ?>" style="width: 150px;"></strong>
                        </td>
                    </tr>
                </table>
                 
            </td>
        </tr>
                            </table>
                        </div>
                    </td>
                </tr>

            </table>   
            
            <div class="clearfix form-actions">
               
                <div class="col-md-offset-3 col-md-9">
              <center>     <?php //form_buttons(); // All the form control button, defined in common_function ?>
                        <input id="mysave" class="savebtn" type="submit" name="submit" value="<?php
                               if (isset($heid))
                                   echo'Update';
                               else
                                   echo'Save';
                               ?>" />
            <?php 
                $idstr = implode(',',$id_str);
                $qtystr = implode(',',$qty_str);
                $mrpstr = implode(',',$mrp_str);
                ?>
            <input type="hidden" name="idstr" value='<?php echo $idstr?>'/>
            <input type="hidden" name="qtystr" value='<?php echo $qtystr?>'/>
            <input type="hidden" name="mrpstr" value='<?php echo $mrpstr?>'/>
            
                        <?php
                        if (isset($heid)) {
                            echo $heid; //A hidden field name eid, whose value will be equal to the edit id. 
                            ?>
                            <!--<input style="background-color:#428BCA" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>" />-->  
                     <input class="exitbtn" onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
    <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
<?php } else { ?>
                            <input onclick="parent.$.fn.colorbox.close();"  class="btn btn-danger" type="button" value="Close" />
                            <input onclick="location.href='index.php?option=make-challan'" class="btn btn-success" type="button" value="Invoice Details"/>
<?php } ?>  
              </center>  </div>
            </div>
            
                                    </div>

                                </div>
                            </div>

                            <hr>
                            <div class="wizard-actions">
                             
                            </div>
                        </div><!-- /.widget-main -->
                    </div><!-- /.widget-body -->
                    </form>
                </div>
 </body>
   <?php  
function qtyscheme($product){
        $query = "SELECT * FROM `catalog_product_rate_list_test` where catalog_product_id='$product'";                                
        $q = mysqli_query($dbc,$query);
        $row = mysqli_fetch_assoc($q); 
        $tax = $row['tax'];
       
        return $tax;                               
                               
}
     ?>             
<script type="text/javascript">

    $('#mytable').on('change','.quantitycl',function()
     {
        var qty = parseInt($(this).val());
        var avbl_qty = parseInt($(this).closest("tr").find('.avlb_quantity').val());
        
        if(avbl_qty<qty)
        {
            alert('Available stock must be greater then input quantity');
            $(this).val('0');
            return false;
        }
     })

    function getTotal(val,ts)
    {        
        if(val)
        {
            var sum = parseFloat(0);
            var amt_array = document.getElementsByName('amount[]');

            for(var i=0; i<amt_array.length; i++)
            {
                sum += parseFloat(amt_array[i].value);
            }

            var dis_val = parseFloat(sum*val/100);
            var new_amt = sum-dis_val; 

            if(new_amt<0)
            {
                alert('Invalid value');
                $('.'+ts+'').val(0);
                $('.totamt').val($('#total_disc2').val());
                return false;
            }       
            
            document.getElementsByName('totamt')[0].value = new_amt.toFixed(3);
        }else{
            $('.totamt').val($('#total_disc2').val());
        }
    }





var i = 1;
$(document).on('keypress','.enter',function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
  if(keycode == '13'){
   $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
        $(this).val('').attr('id', function(_, id) { return id + i });
    }).end().appendTo("#mytable");
    $('#mytable tr:last').find('input').val('');
    i++;
    $('#mytable tr.tdata').each(function(j){
            $(this).find('td.myintrow:first').html((j+1)*1);
    });
  } 
});

$(document).on('click', '.addbutton', function () {
    //if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
    var i = $('#mytable').find('input[type="checkbox"]').length;
    var cnt = i+1;
    $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
        $(this).val('').attr('id', function(_, id) { return id + i });
    }).end().appendTo("#mytable");
    $('#mytable tr:last').find('input').val('');
    $('#mytable tr:last').find('.mrp').html('');

    var ss = $('#mytable tr:last').find('input[type="checkbox"]');
    ss.attr('name','bos['+cnt+']').removeAttr('checked');
    
    i++;
    $('#mytable tr.tdata').each(function(j){
            $(this).find('td.myintrow:first').html((j+1)*1);
    });
});

$(document).on('click', '.removebutton', function () { 
     $(this).closest('tr').remove();
     calc_total_amount();
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
    function updateInput(id){
         var product_id = document.getElementById('product_id' + id).value;
         
  //  var product_id = document.getElementsById("product_id")[id].value;
  //  alert(id);
    //var cost = document.getElementsByName("cost")[0].value;
	if(product_id==1 || product_id==2)
{
    document.getElementById('vat' + id).value=0;
 }
else{
    document.getElementById('vat' + id).value=5;
}   
}
    function custum_function(pid, pvalue, event) {
        var batchno = $("#" + pid).closest("td").next().find("select").attr("id");
       // getajaxdata('get_product_vat', 'mytable', event);
        //  var vat= "<?php //qtyscheme(pid)?>";
        setTimeout(function() {
           getajaxdata('get_comunity_code', 'mytable', event);
           getajaxdata('get_product_gst', 'mytable', event);
        }, 300);

        setTimeout(function() {
            fetch_location(pvalue, 'progress_div', batchno, 'get_product_mrp');
            //getajaxdata('get_product_mrp', 'mytable', event);
        }, 400);
    }

function get_available_stock(mrp_id,mrp_value,event){
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
        // alert(prod_id);
        //setTimeout(function() {
        var pvalue = document.getElementById(prod_id).value;
      //  var product_id = document.getElementById(product_id).value;
      // alert(pvalue);
       getajaxdata('get_product_vat', 'mytable', event);
        //}, 800);
      //  getajaxdata('get_product_vat', 'mytable', event,pvalue);
        //setTimeout(function() {
       getajaxdata('get-retailer-rate-edit', 'mytable', event,pvalue);
     //  }, 1000);
       // mrp_change(mrp_value);       
    
}

function calc_total_amount()
{  

    var total_amt = 0;
    var sub_total = 0;
    var final_amt = 0;


    $('.amount').each(function(){
        sub_total += parseFloat($(this).val());
    })
    // console.log(sub_total);
    $('#total_disc2').val(sub_total.toFixed(2));
    var dicount_percent = $('#dis').val(); 

    var sub_total1 = $('#total_disc2').val();

    if(dicount_percent)
    {
        final_amt = sub_total1-(sub_total1*dicount_percent/100);
    }else{
        final_amt = parseFloat(sub_total1);
    }

    $('.totamt').val(final_amt.toFixed(2));
}
    
function trade_disc_calculate()
{
    var qty = document.getElementsByName('quantity[]');
    var r = document.getElementsByName('rate[]');
        var tds_amt = document.getElementsByName('trade_disc_amt[]');
        var tds_type = document.getElementsByName('trade_disc_type[]');
        var tds_val = document.getElementsByName('trade_disc_val[]');
        var ttl_amt = document.getElementsByName('ttl_amt[]');
         var tax_amt = document.getElementsByName('taxable_amt[]');
       //prodvalue
    // alert(qty.length);

    for(var i = 0; i<qty.length; i++)
    {
            if(tds_type[i].value == 1){
                var res = (r[i].value*qty[i].value)* (tds_val[i].value/100);
              tds_amt[i].value = res.toFixed(2);
                ttl_amt[i].value =   (r[i].value*qty[i].value) - tds_amt[i].value;
              //  tax_amt[i].value =   (r[i].value*qty[i].value) - tds_amt[i].value;
            }else{
                var res = tds_val[i].value;
                tds_amt[i].value = res;
                 ttl_amt[i].value =   (r[i].value*qty[i].value) - tds_amt[i].value;
               //  tax_amt[i].value =  ttl_amt[i].value - tds_amt[i].value;
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
}
*/
$(function() {
    $("#retailer").autocomplete({
        source: "./modules/ajax-autocomplete/retailer/ajax-retailer.php"
    });
});

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
    


