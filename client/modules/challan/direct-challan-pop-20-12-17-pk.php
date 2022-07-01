<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>

 <script>
        // WRITE THE VALIDATION SCRIPT IN THE HEAD TAG.
        function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
        return false;

        return true;
        }
    </script>
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
//pre($_SESSION);
//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_id_list($dealer_id);
$location_list = implode(',', $location_list);
?>
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
if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
// pre($_POST);
// die;
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

               $challan_id = $action_status['challan_id'];
               
                ?>
                <script>
                //setTimeout("window.location = 'index.php?option=sale-order-detailes'",500);
                window.open("index.php?option=make-challan&showmode=1&id=<?php echo $challan_id; ?>&mode=1&actiontype=print","_blank");

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
    //pre($rs);
}
?>
             <script type="text/javascript">
function total_amount_invoice(){
   
    var arr = document.getElementsByName('amount[]');
    var arr_td = document.getElementsByName('trade_disc_amt[]');
    var arr_cd = document.getElementsByName('cd_amt[]');
    var arr_ta = document.getElementsByName('taxable_amt[]');
    var arr_va = document.getElementsByName('vat_amt[]');
  var di = document.getElementById('dis').value;
   // alert(arr.length);
    var tot=0;
    var tot_ta=0;
    var tot_cd = 0;
    var tot_td = 0;
    var tot_vat = 0;
    for(var i=0;i<arr.length;i++){
     //   alert(arr.length);
        if(parseFloat(arr[i].value))
            tot += parseFloat(arr[i].value);
        if(parseFloat(arr_td[i].value))
            tot_td += parseFloat(arr_td[i].value);
        if(parseFloat(arr_cd[i].value))
            tot_cd += parseFloat(arr_cd[i].value);
        if(parseFloat(arr_ta[i].value))
            tot_ta += parseFloat(arr_ta[i].value);
        if(parseFloat(arr_va[i].value))
            tot_vat += parseFloat(arr_va[i].value);
    }
     document.getElementById('total_cd').value = tot_cd.toFixed(2);
     document.getElementById('total_td').value = tot_td.toFixed(2);
     document.getElementById('total_vat').value = tot_vat.toFixed(2);
     document.getElementById('total_taxable').value = tot_ta.toFixed(2);
    document.getElementById('total').value = tot.toFixed(2); 
  var discount1 = (tot*di)/100;
  var totamt = tot-discount1;
  document.getElementById('total_disc').value = discount1.toFixed(2);
  document.getElementById('total_amount_a').value = totamt.toFixed(2);
}

    </script>
    <script type="text/javascript">
function total_amount_with_discount(){
   
    var dis = Number(document.getElementById('dis').value);
    var total_disc = document.getElementsByName('total_disc');
    var totalamt = Number(document.getElementById('total_amount_a').value);
    var total = Number(document.getElementById('total').value); 
  
    var discount = total*dis/100;           
    totalamt = total-discount;
    if(totalamt<0)
    {
      alert('Invalid value');
      document.getElementById('total_disc').value = 0;
      document.getElementById('total_amount_a').value = total;
      return false;
    }
            
    document.getElementById('total_disc').value = discount.toFixed(2);
    document.getElementById('total_amount_a').value = totalamt.toFixed(2);
     
    
}

    </script>
    
             <div class="widget-box" >
                 <div class="widget-header widget-header-blue widget-header-flat">
                        <h4 class="widget-title lighter">Direct Invoice Details</h4>
                </div>
                 <form class="form-horizontal" method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
        
              <div class="widget-body" >
                        <div class="widget-main">
                            <div id="fuelux-wizard-container" class="no-steps-container">
                                <div class="step-content pos-rel">
                                  <div class="step-pane active" data-step="1" >
                                   <!------------------FORM--> 
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
                               
                                <?php
                                $q = 'select id,name FROM company ';
                                //h1($q);
                                $st_res = mysqli_query($dbc, $q);
                                while ($row = mysqli_fetch_array($st_res)) {
                                   // pre($row);
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if ($_POST['company_id'] == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
        <?php
    }
    ?>
                            </select>
                        <?php } //echo $q;
                        ?>
                    </td>-->
  
                    <td><strong>Date</strong><br>
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

                    <td><strong>Inv. No. </strong><br>
                         <?php
                
                $query = "select `ch_no` from `challan_order` where `ch_dealer_id`=$dealer_id1 order by `ch_no` DESC";
                $q = mysqli_query($dbc,$query);
                $row = mysqli_fetch_row($q);
                $ch = $row[0];
                $ch_value = explode('/',$ch);
                $value_inv = $ch_value[3];
                $value_year = $ch_value[2];
                /////////////////////////////// FOR SESSION ///////////////////////////////
                $query1 = "select `session` from `session` where `action`='1'";
                $q1 = mysqli_query($dbc,$query1);
                $row1 = mysqli_fetch_row($q1);
                $year = $row1[0];

              $d = date('Y').'-';                

              if (strpos($value_inv, $d) !== false) {
                   $jj = $ch_value[2]+1;
                   $value_year = $ch_value[3];
              }else{
                   $jj = $value_inv+1;
                   $value_year = $ch_value[2];
              }
              
              $num = str_pad($jj,6,'0',STR_PAD_LEFT);

              if($year == $value_year)
              {
                  // $jj= $value_inv+1;
                 $ch_id = "CATC/".$dealer_id1."/".$year."/".$num; 
              }
              else
              {
                 $ch_id = "CATC/".$dealer_id1."/".$year."/".$num;
              }
                  
              ?>
          <input  type="text" name="ch_no" style="width:220px" value="<?php
          echo $ch_id ?>" readonly >
                       
          <input type="hidden" name="ch_no_prifix" value="<?php echo 'CATC/' . $sesId = $_SESSION[SESS . 'data']['dealer_id'] . '/'; ?>">
                    </td>
                    <td> 
                        <span class="star">*</span><strong>Retailer Name</strong><br>
                        
<?php

/*$qrt = "SELECT retailer.id as id, CONCAT(retailer.name,' [',location_5.name,'] ')as name FROM retailer INNER JOIN location_5 ON retailer.location_id = location_5.id where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' group by retailer.id ORDER BY retailer.name ASC  ";*/

$qrt = "SELECT retailer.id as id, CONCAT(retailer.name,' [',retailer.address,'] ')as name FROM retailer where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' AND retailer_status='1' group by retailer.id ORDER BY retailer.name ASC  ";

db_pulldown($dbc, 'retailer_id', $qrt, true, true, 'id="retailer" class="form-control chosen-select" style="margin-left:10px;" lang="Retailer" onchange="getdata(this.value, \'progress_div\', \'get-retailer-location\', \'location_id\');"','=====Please Select=====',$_POST['retailer_id']);
?>
 <input type="hidden" name="location_id" id="location_id" value="<?php if (isset($_POST['location_id'])) echo $_POST['location_id']; ?>">
                    </td>
                    <td>
                      <strong><span class="star">*</span>User Name</strong><br>
                      <?php 

                      $u_q = "SELECT d.user_id,CONCAT_WS(' ',p.first_name,p.middle_name,p.last_name, '[',r.rolename,']') FROM `user_dealer_retailer` d INNER JOIN person p ON d.user_id=p.id INNER JOIN _role r ON p.role_id=r.role_id  WHERE d.dealer_id = '" . $_SESSION[SESS . 'data']['dealer_id'] . "' ORDER BY p.first_name";

                      db_pulldown170($dbc, "user_id", $u_q, true, true, 'class="chosen-select" style="width:250px;"','==Please Select==','');

                      ?>
                    </td>
                </tr>
                <tr>
                  <td colspan="6"><hr></td>
                </tr>
                <tr>
                    <td colspan="18">
                        <div id="product" >
<table width="100%" >
    <tr>
        <td colspan="5">
            <table width="100%" id="mytable" class="">
                <tr class="thead" style="font-weight:bold;">
                    <th style="background-color:#C7CDC8; color:#000;">S.NO</th>
                    <th style="background-color:#C7CDC8; color:#000;">Item Name</th> 
                    <th style="background-color:#C7CDC8; color:#000;">Bill of Supply</th>
                    <th style="background-color:#C7CDC8; color:#000;">M.R.P</th>
                    <!-- <th style="background-color:#C7CDC8; color:#000;">HSN Code</th> -->
                    <!-- <th style="background-color:#C7CDC8; color:#000;">Com. Code</th> -->
                    <th style="background-color:#C7CDC8; color:#000;">Avlb. Stock</th>
                    <th style="background-color:#C7CDC8; color:#000;">Quantity</th>
<!--                    <th style="background-color:#C7CDC8; color:#000;">Sch. Quantity</th>-->
                    <th style="background-color:#C7CDC8; color:#000;">Rate</th>
                    <th style="background-color:#C7CDC8; color:#000;">Trade Type</th>
                    <th style="background-color:#C7CDC8; color:#000;">Trade/Sch. Disc.</th>                                                
                    <th style="background-color:#C7CDC8; color:#000;">Trade Amt.</th>
                    <th style="background-color:#C7CDC8; color:#000;">C.D Type</th>
                    <th style="background-color:#C7CDC8; color:#000;">C.D.</th>                                                
                    <th style="background-color:#C7CDC8; color:#000;">CD.Amt</th>
                    <th style="background-color:#C7CDC8; color:#000;">Taxable Amt.</th>
                    <th style="background-color:#C7CDC8; color:#000;">GST%</th>
                    <th style="background-color:#C7CDC8; color:#000;">GST. Amt</th>
                    <th style="background-color:#C7CDC8; color:#000;">Amount</th>
                    <th style="background-color:#C7CDC8; color:#000;">&nbsp;</th>
                </tr>
                <?php
                
                $keys = array_keys( $_POST['challan_item']);
                
                if(isset($_POST['challan_item'])){ $num_rows = count($_POST['challan_item']); }else{ $num_rows=8; }
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
                   // $tax = 5;
                    $vat_amt = $taxable_amt*($tax/100); 
                    $amount = $taxable_amt + $vat_amt;
                    
                    
                    ?>
                <!--fetch_location(document.getElementById('product_id' + <?php echo $z; ?>).value, 'progress_div', 'hsn_code<?=$z?>', 'get_product_hsn');-->
                <tr class="tdata">
                    <td class="myintrow"><?=$z?></td>
                <td width="10%">
                 <?php
                   /*$q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY name ASC';*/

                   $q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN stock s ON cp.id = s.product_id where s.dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' AND s.qty>0 ORDER BY cp.id';

                    db_pulldown170($dbc,  'product_id[]', $q, TRUE, TRUE,'id="product_id'.$z.'" class="item_details chosen-select" ','',$_POST['challan_item'][$keys[$z-1]]['product_id']);
                    ?>   
                </td>                
                 <td style="text-align: center;"><input type="checkbox" name="bos[<?php echo ($z-1) ?>]" value="1"></td>
                 
                 <td>                     
                     <select  style="width:60px" name="mrp[]" class="mrp mrp_dd calcpk" id="mrp<?=$z?>" placeholder="MRP">
                   <option value="<?=$_POST['challan_item'][$keys[$z-1]]['mrp']?>" selected="selected"><?=$_POST['challan_item'][$keys[$z-1]]['mrp']?></option>
                    </select>
                 </td>
                    
                    
<!--                 <td>                    
                      <select  style="width:60px" name="hsn_code[]" id="hsn_code<?=$z?>" placeholder="HSN Code"  >
                   <option value="<?=$_POST['challan_item'][$keys[$z-1]]['hsn_code']?>" selected="selected"><?=$_POST['challan_item'][$keys[$z-1]]['hsn_code']?></option>
                    </select>   
                </td>
                       <td>
                        <input style="width:98%" placeholder="Comunity Code" id="comunity_code" type="text" name="comunity_code[]" id="comunity_code" value="<?=$comunity_code?>"/>                 
                      </td>  -->
                    <td>
                        <input style="width:98%" placeholder="Avlb. Stock" id="aval_stock" type="text" name="aval_stock[]" onchange="challan_calculate();" value="<?=$avlb_stock?>" readonly class="avlb_quantity"/>
                 
                    </td>
                                                              
                    <td><input style="width:98%" placeholder="Quantity" onkeypress="javascript:return isNumber(event)" type="text"  name="quantity[]" id="quantity" value="<?=$_POST['challan_item'][$keys[$z-1]]['qty']?>" class="quantitycl calcpk"/>
                    </td>
<!--                    <td>
                        <input style="width:98%" placeholder="Sch. Qty" type="text" name="scheme[]"  onblur="total_amount_invoice()"  id="scheme" value="<?=$_POST['challan_item'][$keys[$z-1]]['free_qty']?>" readonly />
                    </td>-->
                    <td>
                     <input style="width:90%" placeholder="Rate" type="text" name="rate[]" id="rate" class="rate" value="<?=$_POST['challan_item'][$keys[$z-1]]['product_rate']?>" readonly/>
                    </td>
                    <td>
                        <select name="trade_disc_type[]" lang="trade_disc" style="width:98%" class="trade_disc_type calcpk">
                            <option value="1" <?php if($_POST['challan_item'][$keys[$z-1]]['dis_type']=='1'){ echo "selected='selected'"; }?>>%</option>dis_type
                            <option value="2" <?php if($_POST['challan_item'][$keys[$z-1]]['dis_type']=='2'){ echo "selected='selected'"; }?>>Amount</option>
                      </select>
                    </td>
                    <td>
                        <input style="width:98%" placeholder="Trade" type="text" name="trade_disc_val[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['dis_percent']?>"  class="trade_disc_val calcpk" onkeypress="javascript:return isNumber(event)"/>
                    </td>   

                    <td>
                        <input style="width:90%" placeholder="Trade Amt." type="text" name="trade_disc_amt[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['dis_amt']?>" class="trade_disc_amt" readonly />
                        <input type="hidden" name="ttl_amt[]" value="<?=$ttl_amt?>"   />
                    </td>
                    <td>
                        <select name="cd_type[]" lang="cdtype" class="cd_type calcpk" style="width:98%"> 
                            <option value="1" <?php if($_POST['challan_item'][$keys[$z-1]]['cd_type']=='1'){ echo "selected='selected'"; }?>>%</option>
                            <option value="2" <?php if($_POST['challan_item'][$keys[$z-1]]['cd_type']=='2'){ echo "selected='selected'"; }?>>Amount</option>
<!--                            <option value="3"> Kg </option>-->
                        </select>
                    </td>
                    <td>
                        <input type="text" style="width:98%" placeholder="CD" name="cd[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['cd']?>" class="cd calcpk" onkeypress="javascript:return isNumber(event)" />
                    </td>

                    <td>
                        <input type="text" style="width:90%" placeholder="cd amt" name="cd_amt[]" class="cd_amt" value="<?=$_POST['challan_item'][$keys[$z-1]]['cd_amt']?>" readonly/>
                    </td>
                    <td>
                        <input type="text" style="width:98%" placeholder="Taxable" name="taxable_amt[]" value="<?=$_POST['challan_item'][$keys[$z-1]]['taxable_amt']?>" class="taxable_amt" readonly />
                    </td>
                    <td>
                        <input type="hidden"  style="width:98%" placeholder="State" name="state[]" id="state" value="<?=$state_id?>"  />
                        <!--<input   type="text"  style="width:98%" placeholder="VAT" name="vat[]" id="vat"  value="<?=$_POST['challan_item'][$keys[$z-1]]['tax']?>"/></td>-->
                   <input   type="text"  style="width:98%" placeholder="GST" name="vat[]" class="vat" id="vat<?=$z?>" readonly/>
                    <td>
                        <input type="text" name="vat_amt[]" style="width:98%" placeholder="Vat" id="vat_amt" class="vat_amt" value="<?=$vat_amt?>" readonly/>
                        <input type="hidden" name="surcharge[]" id="surcharge"  value="<?php echo $surcharge;?>" readonly/>
                    </td>
                    <td>
                        <input type="text" style="width:98%" class="<?php if($z-1>=7){?>addbutton<?php } ?> amount" placeholder="Amount" name="amount[]" id="amount" value="<?=number_format($amount,2,'.',','); ?>"  readonly/>
                    </td>
                    <td>
                        <a tabindex="0"><img  title="more" src="images/more.png" class="addrow" /></a>
                        <?php if($z!=1){ ?>
                            <a tabindex="0"><img  title="Less" src="images/less.png" class="removebutton"/></a>
                        <?php } ?>
                    </td> 
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
                     <td colspan="18"><hr/></td>                     
                </tr>
                <tr>
                 <td colspan="2" style="border-bottom:1px solid #efefef;"></td>
                 <td colspan="16" style="border-bottom:1px solid #efefef;">
                   <table width="100%">
                     <tr>
                       <td><strong>Total Trade Discount</strong></td>
                       <td><strong>Total Cash Discount</strong></td>
                       <td><strong>Total Taxable Amount</strong></td>
                       <td><strong>Total Tax Amount</strong></td>
                       <td><strong>Total Amount</strong></td>
                     </tr>
                     <tr>
                      <td align="right">
                       <strong>
                         <input  style="width:98%" type="text" name="total_td" id="total_td" value="0" readonly>    </strong>                
                       </td>                        
                       <td align="right"><strong>
                         <input  style="width:98%" type="text" name="total_cd" id="total_cd" value="0" readonly>    </strong>                
                       </td>
                       <td align="right"><strong>
                         <input  style="width:98%" type="text" name="total_taxable" id="total_taxable" value="0" readonly>    </strong>                
                       </td>
                       <td align="right"><strong>
                         <input  style="width:98%" type="text" name="total_vat" id="total_vat" value="0" readonly>    </strong>                
                       </td>
                       <td align="right"><strong>
                         <input style="width:98%" type="text" name="total" id="total" value="0" readonly></strong>
                       </td>
                     </tr>
                   </table>
                 </td>                 
               </tr>
               <hr>
               <tr>
                 <td colspan="2" style="border-bottom:1px solid #efefef; "></td>
                 <td colspan="16" align="right" style="border-bottom:1px solid #efefef; ">
                  <table>
                    <tr>
                      <td><strong>Enter Discount % : </strong></td>                    
                      <td>
                                            <!-- <select name="dis" id="dis" onchange="total_amount_with_discount(this.value)">
                                                <option value="0">SELECT DISCOUNT</option>
                                                <option value="2"> 2% </option>
                                                <option value="3"> 3% </option>
                                                <option value="4"> 4% </option>
                                                <option value="5"> 5% </option>
                                              </select> -->
                                              <strong>
                                                <input type="text" name="dis" id="dis" onkeyup="total_amount_with_discount(this.value)"/></strong>
                                              </td>
                                          </tr>
                                        </table>
                                      </td>

                                    </tr>
                                    <tr>
                                     <td colspan="2" style="border-bottom:1px solid #efefef; "></td>
                                     <td colspan="16" align="right" style="border-bottom:1px solid #efefef; ">
                                      <table>
                                        <tr>
                                          <td><strong>Discount Amount : </strong></td>                                        
                                          <td style="border-bottom:1px solid #efefef;">
                                            <strong><input type="text" name="total_disc" id="total_disc" value="0" readonly> </strong></td>
                                          </tr>
                                        </table>                       
                                      </td>

                                    </tr>
                                    <tr>
                                     <td colspan="2" style="border-bottom:1px solid #efefef;"></td>
                                     <td colspan="16" align="right" style="border-bottom:1px solid #efefef;">
                                      <table>
                                        <tr>
                                          <td><strong>Total Amount : </strong></td>
                                          <td><strong>
                                            <input  style="width:98%" type="text" name="total_amount_a" id="total_amount_a" value="0" readonly>    </strong></td>
                                          </tr>
                                        </table>                                       
                                      </td>                     
                                    </tr>               
            </table>   
            
            <div class="clearfix form-actions">
               
                <div class="col-md-offset-4 col-md-9">
                    <span style="margin-left:150px">    <?php //form_buttons(); // All the form control button, defined in common_function ?>
                        <input id="mysave" class="btn btn-sm btn-info" type="submit" name="submit" value="<?php
                               if (isset($heid))
                                   echo'Update';
                               else
                                   echo'Save';
                               ?>" />
                        <?php
                        if (isset($heid)) {
                            echo $heid; //A hidden field name eid, whose value will be equal to the edit id. 
                            ?>
                            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button"  class="btn btn-success" value="New" title="add new <?php echo $forma; ?>" />  
                            <input onclick="parent.$.fn.colorbox.close();" class="btn btn-danger" type="button" value="Exit" /> <br />
    <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
<?php } else { ?>
                            <input onclick="parent.$.fn.colorbox.close();"  class="btn btn-sm btn-danger" type="button" value="Close" />
                            <!--<input onclick="location.href='index.php?option=make-challan'" class="btn btn-success" type="button" value="Invoice Details"/>-->
<?php } ?>  </span>
                </div>
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
//function qtyscheme($product){
//        $query = "SELECT * FROM `catalog_product_rate_list_test` where catalog_product_id='$product'";                                
//        $q = mysqli_query($dbc,$query);
//        $row = mysqli_fetch_assoc($q); 
//        $tax = $row['tax'];
//       
//        return $tax;                               
//                               
//}
     ?>
 
 <!-- Get all details of a item for DirectChallan (PUNEET)-->
<!-- <script type="text/javascript">
  $(function(){

    $('#mytable').on('change','.item_details',function(){
      var ths = $(this);
      var product_id = ths.val();
      
      $.ajax({
          type:'POST',
          url:'js/ajax_general/ajax_general_php.php',
          data:{pid:product_id,wcase:'getItemDetails'},
          success: function (data) {
            var response = $.parseJSON(data);
            if(response.exception)
            {
              alert(response.data);
            }else{
              var row = ths.parent().parent();
              var mrp = row.find('.mrp');

              mrp.html('<option value="" selected>== Please Select ==</option>');
              $.each(response.data.mrp, function(k, v) {
                 mrp.append('<option value="'+v+'">'+v+'</option>');
              });

              row.find('.rate').val(response.data.retailer_rate);
              row.find('.vat').val(parseFloat(response.data.gst).toFixed(2));             
            }
          }
      })
    })

    $('#mytable').on('change','.mrp_dd',function(){
       var id = $(this).closest('tr').find('.item_details').val();
       var avlb_quantity = $(this).closest('tr').find('.avlb_quantity');
       var rate = $(this).closest('tr').find('.rate');
       var m  = $(this).val();

       $.ajax({
          type:'POST',
          url:'js/ajax_general/ajax_general_php.php',
          data:{pid:id,mrp:m,wcase:'rateNstock'},
          success:function(data){
            var resp = $.parseJSON(data);
            avlb_quantity.val(resp.data.qty);
            rate.val(resp.data.rate);
          }
       })      
    })

  })
</script> -->
<script type="text/javascript">              

          $(function(){
              if(!ace.vars['touch']) {
                   $('.chosen-select').chosen({allow_single_deselect:true});
              }
          })
        </script>
<!--  Final calculation of entire page  -->
<script type="text/javascript" src="js/pkcalculation.js"></script>        
<script type="text/javascript">

function calc_total_amount()
  {
    var sub_total = 0;
    var tot_tx = 0;
    var tot_vt = 0;
    var trade_disc = 0;
    var cd_amt = 0;

    var dicount_percent = $('#dis').val(); 

    $('.amount').each(function(){
      if($(this).val()!=''){
        sub_total += parseFloat($(this).val());
      }
        })

    $('.taxable_amt').each(function(){
      if($(this).val()!='')
      {
        tot_tx += parseFloat($(this).val());
      }
    })

    $('.vat_amt').each(function(){
     if($(this).val()!=''){
        tot_vt += parseFloat($(this).val());
            }
        })

    $('.trade_disc_amt').each(function(){
        var td = parseFloat($(this).val());
        if(td)
        {
          trade_disc += td;
        }
      })

    $('.cd_amt').each(function(){
        var cd = parseFloat($(this).val());
        if(cd)
        {
          cd_amt += cd;
        }
      })

    if(dicount_percent>0)
    {
       var dicount_amount = sub_total*dicount_percent/100
       $('#total_disc').val(dicount_amount.toFixed(2));
       $('#total_amount_a').val((sub_total-dicount_amount).toFixed(2));
    }else{
      $('#total_amount_a').val(sub_total.toFixed(2));
    }

    $('#total').val(sub_total.toFixed(2));
    $('#total_taxable').val(tot_tx.toFixed(2));
    $('#total_vat').val(tot_vt.toFixed(2));
    $('#total_td').val(trade_disc.toFixed(2));
    $('#total_cd').val(cd_amt.toFixed(2));
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


/*$(document).on('blur', '.addbutton', function () {
    
  var j = $("#mytable").find('.item_details').length;
  j = j+1; 
    $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
        $(this).val('').attr('id', function(_, id) { return id + i });
    }).end().appendTo("#mytable");
    $('#mytable tr:last').find('input').val('');
    $('#mytable tr:last').find('.item_details').removeClass('chzn-done').css({'display':'block'}).removeAttr('id').val('').next('div').remove();
    $('#mytable tr:last').find('.item_details').attr('id','product_id'+j);
    $('#product_id'+j+'').chosen();
    i++;
    $('#mytable tr.tdata').each(function(j){
            $(this).find('td.myintrow:first').html((j+1)*1);
    });
});*/

$(document).on('click', '.addrow', function () {
    
    //if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
    var j = $("#mytable").find('.item_details').length;
    j = j+1; 
    $("#mytable  tr:nth-child(2)").clone().find("select").each(function() {
        $(this).val('').attr('id', function(_, id) { return id + i });
    }).end().appendTo("#mytable");
    $('#mytable tr:last').find('input').val('');
    $('#mytable tr:last').find('.mrp').html('');
    $('#mytable tr:last').find('.item_details').removeClass('chzn-done').css({'display':'block'}).removeAttr('id').val('').next('div').remove();
    $('#mytable tr:last').find('td:last').append('<img title="Less" src="images/less.png" class="removebutton calcpk">');
    $('#mytable tr:last').find('.item_details').attr('id','product_id'+j);
    $('#product_id'+j+'').chosen();
    i++;
    $('#mytable tr.tdata').each(function(j){
            $(this).find('td.myintrow:first').html((j+1)*1);
    });
});


$(document).on('click', '.removebutton', function () { 
     var tot_row = $('#mytable').find('.item_details').length;
         
         if(tot_row>1)
         {
           $(this).closest('tr').remove();
           calc_total_amount();
         }
     return false;
 });
    function checkuniquearray(name)
    {
        var arr = document.getElementsByName('vat[]');
        var qty = document.getElementsByName('quantity[]');
                var prd = document.getElementsByName('product_id[]');
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
						if (prd[i].value !='' && (qty[i].value =='' || qty[i].value =='0'))
						                   {
						                       alert('0 quantity not allowed');
						                       return false;
						                   }
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
        document.getElementById('vat' + id).value=5;    
    }

    function custum_function(pid, pvalue, event) {

        var batchno = $("#" + pid).closest("td").next().find("select").attr("id");
        get_retailer_rate(pid, pvalue, event);
        setTimeout(function() {
           // getajaxdata('get_comunity_code', 'mytable', event);
           // getajaxdata('get_product_gst', 'mytable', event);
           // getajaxdata('get-retailer-rate', 'mytable', event,pvalue);
        }, 300);
        
        setTimeout(function() {
           getajaxdata('get_product_gst', 'mytable', event);
        }, 300);
         
         setTimeout(function() {
            fetch_location(pvalue, 'progress_div', batchno, 'get_product_mrp');
            //getajaxdata('get_product_mrp', 'mytable', event);
        }, 400); 
    }

function get_available_stock(mrp_id,mrp_value,event){
        var prod_id = $("#" + mrp_id).closest("td").prev().find("select").attr("id");
        //setTimeout(function() {
        var pvalue = document.getElementById(prod_id).value;
        getajaxdata('get-stock', 'mytable', event,pvalue);        
      //  mrp_change(mrp_value);
    
}

function get_retailer_rate(pid, pvalue, event){
    
    getajaxdata('get-retailer-rate', 'mytable', event,pvalue);
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

function mrp_change()
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

$(function() {
    $("#retailer").autocomplete({
        source: "./modules/ajax-autocomplete/retailer/ajax-retailer.php"
    });
});

</script>  

<script>


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
    
// $(document).keydown(function(e) {

//   // Set self as the current item in focus
//   var self = $(':focus'),
//       // Set the form by the current item in focus
//       form = self.parents('form:eq(0)'),
//       focusable;

//   // Array of Indexable/Tab-able items
//   focusable = form.find('input,a,select,button,textarea,div[contenteditable=true]').filter(':visible');

//   function enterKey(){
//     if (e.which === 13 && !self.is('textarea,div[contenteditable=true]')) { // [Enter] key

//       // If not a regular hyperlink/button/textarea
//       if ($.inArray(self, focusable) && (!self.is('a,button'))){
//         // Then prevent the default [Enter] key behaviour from submitting the form
//         e.preventDefault();
//       } // Otherwise follow the link/button as by design, or put new line in textarea

//       // Focus on the next item (either previous or next depending on shift)
//       focusable.eq(focusable.index(self) + (e.shiftKey ? -1 : 1)).focus();

//       return false;
//     }
//   }
//   // We need to capture the [Shift] key and check the [Enter] key either way.
//   if (e.shiftKey) { enterKey() } else { enterKey() }
// });
$('.datepicker').datepicker({ minDate: 0,dateFormat: 'dd/mm/yy'});
</script>
   

