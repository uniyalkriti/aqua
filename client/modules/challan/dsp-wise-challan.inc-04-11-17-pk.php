
<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
$forma = 'SALE WISE INVOICE'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'dealer_sale'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS . 'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$userid = $_SESSION[SESS . 'data']['id'];
$sesId = $_SESSION[SESS . 'data']['id'];
$role_id = $_SESSION[SESS . 'data']['urole'];
//here we get dealer id
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$location_id = $_SESSION[SESS . 'data']['state_id'];
//echo $surcharge=  myrowval('state', 'surcharge', 'stateid='.$location_id); 
//$dealer_id = $myobj->get_dealer_id($sesId, $role_id);
//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_list($dealer_id);
?>
<div id="breadcumb"><a href="#">Invoice</a> &raquo; <a></a>  &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
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
if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {

if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
    //calculating the user authorisastion for the operation performed, function is defined in common_function
  list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
  if ($checkpass) {
        // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
       // magic_quotes_check($dbc, $check = true);
        //$funcname = $cls_func_str.'_save'; 
        $action_status = $myobj->multi_challan_save(); // $myobj->item_category_save()
        if ($action_status['status']) {
          echo '<span class="asm" style="margin-top:200px;background-color:#D50714; color:#fff;">' . $action_status['myreason'] . '</span>';
          unset($_POST);
          ?>
          <script>
            setTimeout("window.parent.location = 'index.php?option=sale-order-detailes'", 2000);
                //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
              </script>
              <?php
            } else
            echo '<span class="awm">' . $action_status['myreason'] . '</span>';
          } else
          echo'<span class="awm">' . $fmsg . '</span>';
        } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
      }

//$filterstr = array();
      if (isset($_GET['mode']) && $_GET['mode'] == 1) {
        if (isset($_GET['order_id'])) {
          $order_id = $_GET['order_id'];
          $order_id = rtrim($order_id, ',');
    // h1($order_id);
          $filter[] = "order_id IN ($order_id)";
          $user_data = $myobj->get_dsp_wise_user_data($sesId, $role_id, $dealer_id);

          if (!empty($user_data)) {
            $user_data_str = implode(',', $user_data);
        // $filter[] = "user_id IN ($user_data_str)";
    } // if(!empty($user_data)) end here
    //  $filter[] = "call_status = '1'";
    //  h1($funcname);
    $rs = $myobj->$funcname($filter, $records = '', $orderby = '',true);
    if (empty($rs)) {
       // echo '<span class="warn">Sorry no more order found</span>';
      exit();
//header("Location:index.php?option=make-challan&showmode=1&mode=1&id=' . $_GET[ch_no] . '&actiontype=print");          
//exit();

    } //if(empty($rs)) { end here
    }
  }
  ?>
  <!-- This function don't allow  product will be inserted. -->
<script type="text/javascript">
  function total_amount_with_discount(){

    var dis = Number(document.getElementById('dis').value);
    var total_disc = document.getElementsByName('total_disc');
    var totalamt = Number(document.getElementById('total_amount_a').value);
    var total = Number(document.getElementById('total').value);


    var discount = total*dis/100;
     //disc = discount;
    /// alert(discount);
    totalamt = total-discount;

    document.getElementById('total_disc').value = discount.toFixed(2);
    document.getElementById('total_amount_a').value = totalamt.toFixed(2);


  }

</script>
<script type="text/javascript">
  function total_amount_invoice(order_id,id){
// alert('amount['+order_id+']');
//var arr = document.getElementsByName('amount['+order_id+']');
var total =  document.getElementById('total').value;
var qty = document.getElementById('ch_qty'+id).value;
var rate = document.getElementById('r'+id).value;
var vat = document.getElementById('vat'+id).value;
var vatamt = document.getElementById('vat_amt'+id).value;
var tda = document.getElementById('trade_disc_amt'+id).value;
var cda = document.getElementById('cd_amt'+id).value;
var tot_td = Number(document.getElementById('total_td').value);
var tot_cd = Number(document.getElementById('total_cd').value);
var tot_taxa = Number(document.getElementById('total_taxable').value);
var tot_vat = Number(document.getElementById('total_vat').value);
// alert(cda);
//alert(tda);
//   var total_td = 0;
//   var total_cd=0;
//   var total_taxable=0;
//   var total_vat = 0;
var qty1 = Number(qty);
var rate1 = Number(rate);
var tax = Number(vat);
var taxamt = Number(vatamt);
var td = Number(tda);
var cd = Number(cda);
var amount = ((qty1*rate1)*tax)/100;
var amt = Number(amount);
var total1 = Number(total);
total = (total1+taxamt)-(td+cd+amt);
tot_td = td+tot_td;
tot_cd = tot_cd+cd;
tot_vat = tot_vat+taxamt;
tot_taxa = total-tot_vat;

// alert(total);
document.getElementById('total').value = total.toFixed(2);
document.getElementById('total_td').value = tot_td.toFixed(2);
document.getElementById('total_cd').value = tot_cd.toFixed(2);
document.getElementById('total_taxable').value = tot_taxa.toFixed(2);
document.getElementById('total_vat').value = tot_vat.toFixed(2);
}

</script>
<script>
  var i = 1;
  $(document).on('click', '.addbutton', function () {
    //if(i >= 1) { document.getElementById('disdata').style.display = 'block'; }
    $("#mytable1  tr:nth-child(2)").clone().find("select").each(function () {
      $(this).val('').attr('id', function (_, id) {
        return id + i
      });
    }).end().appendTo("#mytable1");
    i++;
    $('#mytable1 tr.tdata').each(function (j) {
      $(this).find('td.myintrow:first').html((j + 1) * 1);
    });
  });
  $(document).on('click', '.removebutton', function () {
    $(this).closest('tr').remove();
    return false;
  });
</script>

<script type="text/javascript">
  function checkuniquearray(name)
  {
    var arr = document.getElementsByName('product[]');
    var len = arr.length;

    var table = document.getElementById('product');
    var table = document.getElementById('mytableabc');

    var rows = $('tr.ihighlight');
    

    for(var i=0; i<rows.length;i++)
    {
      var aval_stock = document.getElementsByClassName("aval_stock");
      var ch_qty = document.getElementsByClassName("ch_qty");

      if(parseInt(ch_qty[i].value) > parseInt(aval_stock[i].value))
      {
        alert('Available stock must be greater then input quantity');
        return false;
      }
    }

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

        function check_greater_value(fieldid)
        {
          var qty = document.getElementById('ch_qty' + fieldid).value;
          var stock = document.getElementById('ostock' + fieldid).value;
          if (qty > stock) {
            alert('Invoice Item cannot be greter than opening stock;');
            document.getElementById('ch_qty' + fieldid).value = '';
            document.getElementById('ch_qty' + fieldid).style.focus();
            return false;
          }
        }
        function custum_function(pid, pvalue, event) {
    //var batchno = $("#"+pid).closest("td").next().find("select").attr("id");
    getajaxdata('get_mrp_vat', 'mytable1', event);
    setTimeout(function () {
      getajaxdata('get-stock-extra', 'mytable1', event);
    }, 300);
    setTimeout(function () {
      getajaxdata('get-calculate-rate-extra', 'mytable1', event);
    }, 600);

  }
</script>
<script>
  function cd_disc_calculate(order_id)
  {   
// alert('ANKUSH PANDEY');
//  alert (order_id);
var cd_amt = 0;
var trade_disc_amt = 0;
var qty = document.getElementById('ch_qty'+order_id).value;    
var r = document.getElementById('r'+order_id).value;  
var cd_type = document.getElementById('cd_type'+order_id).value;    
var cd_val = document.getElementById('cd'+order_id).value;
var taxable_amt = document.getElementById('taxable_amt'+order_id).value;
var trade_disc_amt = document.getElementById('trade_disc_amt'+order_id).value;
var vat = document.getElementById('vat'+order_id).value;
  //  alert(taxable_amt);
  if(cd_type == 1){
    var res = (r*qty) * (cd_val/100);
          //var cdtd = res-trade_disc_amt;
          var  cd_amt = res.toFixed(3);
         // alert(cd_amt);
         var cd_disc_amt = Number(cd_amt) + Number(trade_disc_amt);
         var  ttl_amt =  (r*qty) - cd_disc_amt;
       //  alert(cd_disc_amt);
     }else{
       var res = cd_val;
       var cd_amt = res;
       var cd_disc_amt = Number(cd_amt) + Number(trade_disc_amt);
       var ttl_amt = (r*qty) - cd_disc_amt;
     }
    //  alert('ANKUSH');
        //alert(ttl_amt);
      // var taxable_amt = ttl_amt;
      var vat_amt = (vat*ttl_amt)/100;
      // alert('ANKUSH ROXX');
      document.getElementById('cd_amt'+order_id).value = cd_amt;
      document.getElementById('ttl_amt'+order_id).value = ttl_amt;
      document.getElementById('vat_amt'+order_id).value = vat_amt;  
      document.getElementById('taxable_amt'+order_id).value = ttl_amt;  
      document.getElementById('amount'+order_id).value = ttl_amt+vat_amt;
    }
  </script>
<script>
  function trade_disc_calculate(order_id)
  {   
   var qty = document.getElementById('ch_qty'+order_id).value;    
   var r = document.getElementById('r'+order_id).value;  
   var tds_type = document.getElementById('trade_disc_type'+order_id).value;    
   var tds_val = document.getElementById('trade_disc_val'+order_id).value;
   var vat = document.getElementById('vat'+order_id).value;
   var taxable_amt = document.getElementById('taxable_amt'+order_id).value;
   var cd_disc_amt = document.getElementById('cd_amt'+order_id).value;

   if(tds_type == 1){
    var res = (r*qty) * (tds_val/100);
    var  tds_amt = res.toFixed(3);     
    var td_disc_amt = Number(tds_amt) + Number(cd_disc_amt);
    var  ttl_amt = (r*qty)- td_disc_amt;
  }else{
   var res = tds_val;
   var tds_amt = res;
   var td_disc_amt = Number(tds_amt) + Number(cd_disc_amt);
   var ttl_amt = (r*qty) - td_disc_amt;
 }
  //    alert (order_id);
  //    alert(ttl_amt);
    // var taxable_amt = ttl_amt;
    var vat_amt = (vat*ttl_amt)/100;

    document.getElementById('trade_disc_amt'+order_id).value = tds_amt;
    document.getElementById('ttl_amt'+order_id).value = ttldsp-wise-challan_amt;
    document.getElementById('vat_amt'+order_id).value = vat_amt;  
    document.getElementById('taxable_amt'+order_id).value = ttl_amt;  
    document.getElementById('amount'+order_id).value = ttl_amt+vat_amt;
  }
</script>
<script>
  function cd_product_calculate(order_id)
  {   

    var state = document.getElementById('state'+order_id).value;  
    var qty = document.getElementById('ch_qty'+order_id).value; 
    var rate = document.getElementById('r'+order_id).value;    
    var cd_type = document.getElementById('cd_type'+order_id).value;
    var cd = document.getElementById('cd'+order_id).value; 
    var vat = document.getElementById('vat'+order_id).value;
    var tds_amt = document.getElementById('trade_disc_amt'+order_id).value;
    var ttl_amt = document.getElementById('ttl_amt'+order_id).value; 
    var final_amt = document.getElementById('final_amt').value;      
    var res = (qty * rate) - tds_amt;
// alert(res);
var amount = res;     
document.getElementById('amount'+order_id).value = amount.toFixed(2);
if(cd_type==1){
var cd_dis = (cd * res) / 100;  
    //  alert(cd_dis);
    var cd_amt = cd_dis.toFixed(2);  
    //  alert(cd_amt);
    var taxablamt = ttl_amt - cd_dis;
   //   alert(taxablamt);
   var taxable_amt = taxablamt.toFixed(2);
    //  alert(taxable_amt);
    var va = taxable_amt * vat;
     // alert(va);
   }
   if(cd_type==2){
    cd_dis = cd;
    var cd_amt = cd_dis;        
    var taxablamt = ttl_amt - cd_dis;
    var taxable_amt = taxablamt.toFixed(2);        
    var va = taxable_amt * vat;          
  }

  document.getElementById('cd_amt'+order_id).value = cd_amt;      
  document.getElementById('taxable_amt'+order_id).value = taxable_amt;      
  document.getElementById('vat_amt'+order_id).value = va;

  var v_amt
  if(vat==0){
   v_amt = 0;
 }
 else
 {
  v_amt= taxable_amt*(vat/100);
}

var va = v_amt.toFixed(2);      
document.getElementById('vat_amt'+order_id).value = va;       

var taxableamt = taxable_amt*1; 
var gamt = v_amt + taxableamt;

if(gamt!=0){
  amount = gamt.toFixed(2);
  document.getElementById('amount'+order_id).value = amount;

}

//	var qty_val = qty;
//	if(qty_val.length>=1){
//            var d = qty_val.length-1;
//            if(qty_val>=10){
//            var sch_val = qty_val.substring(0, d);
//                var    scheme = sch_val;
//            }else{
//
//              var  scheme=0;
//            }
//            document.getElementById('scheme'+order_id).value = scheme;
//	}


}
</script>
<?php
if (!empty($rs)) {
  $inc = 1;
  $loop=count($rs);
  foreach ($rs as $key => $value) {

    ?>
    <div id="workarea">
      <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
        <fieldset>
          <legend class="legend" style=""><?php echo $forma; ?></legend>
          <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
          <input type="hidden" id="loc_level" name="loc_level[<?=$value['order_id'];?>]" value="<?php echo $loc_level; ?>">
          <input type="hidden" name="dealer_id[<?=$value['order_id'];?>]" id="dealer_id" value="<?php if (isset($dealer_id)) echo $dealer_id; ?>">
          <input type="hidden" name="date[<?=$value['order_id'];?>]" id="date" value="<?=$value['date']; ?>">



          <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
            <tr>
              <td colspan="15" ><hr></td>
            </tr>
            <tr>
              <td>
                <img src="./images/pink.jpg" style="width:28px; height: 26px"/>&nbsp;&nbsp;<strong>Available Quantity less than Required Quantity</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/green.jpeg" style="width:28px; height: 35px"/>&nbsp;&nbsp;<strong>Focus Product</strong>
              </td>
            </tr>
            <tr>
              <td><strong>Invoice Date:</strong>
                <input  style="width:40%" class="datepicker" type="text" name="ch_date[<?=$value['order_id'];?>]" value="<?php
                if (isset($value['sale_date']))
                  echo $value['sale_date'];
                else
                  echo date('d/M/Y');
                ?>">
              </td>
              <td><strong>Remarks By User</strong>

              </td>
              <td><strong><span class="star">*</span>Retailer Name</strong><br>

               <select lang="retailer" name="retailer_id" id="retailer_id">
                <option>==Please Select==</option>
                <?php
                if(!isset($heid)) {
                    //  $myobjR = new retailer();
                      //$retailer_data = $myobjR->get_user_wise_retailer_data($sesId , $role_id);
                    //  if(!empty($retailer_data))  $retailer_data_str = implode(',' ,$retailer_data);
                  echo option_builder($dbc, $q="SELECT id,name FROM retailer WHERE retailer.id = '$value[retailer_id]'", $value['retailer_id']);
                  } //if(isset($heid))  end here
                  ?>
                </select>  
                <?php //echo $q; ?>
              </td>
            </tr>
            <tr>
              <td><strong>Invoice Number:</strong>
                <?php

                $query = "select `ch_no` from `challan_order` where `ch_dealer_id`=$dealer_id order by `auto` DESC,`id` DESC";
            // echo $query;
                $q = mysqli_query($dbc,$query);
                $row = mysqli_fetch_row($q);
          // pre($row);
                $ch = $row[0];
                $ch_value = explode('/',$ch);
            //print_r($ch_value);
                $value_inv = $ch_value[2];
                $value_year = $ch_value[3];
            ////////////////////////////////////FOR SESSION///////////////////////////////
                $query1 = "select `session` from `session` where `action`='1'";
                $q1 = mysqli_query($dbc,$query1);
                $row1 = mysqli_fetch_row($q1);
                $year = $row1[0];

                if($year == $value_year)
                {
                 for($i=1;$i<=$loop;$i++)
                 {
                  $val = $value_inv;
                  $jj[$i] = $value_inv+$i;                 
                  $value_inv = $val;

                }
                $num = str_pad($jj[$inc],6,'0',STR_PAD_LEFT);
             // $jj= $value_inv+1;
            // echo $jj[$inc];
                $num = str_pad($jj[$inc],6,'0',STR_PAD_LEFT);
                $ch_id[$inc] = "CATC/".$dealer_id."/".$num."/".$year; 
            //  h1($inc) ;
              }
              else
              {
               $ch_id[$inc] = "CATC/".$dealer_id."/000001/".$year; 
             }

             ?>

             <input style="width:40%" id="ch_no" name="ch_no[<?=$value['order_id'];?>]" type="text" value="<?php
    //  for($)
             echo $ch_id[$inc] ?>" readonly>
             <?php   


             ?>

           </td>
           <td width="225px">   <input  readonly  type="text" name="ch_date[<?=$value['order_id'];?>]" value="<?php
           if (isset($value['remarks']))
            echo $value['remarks'];
          else
            echo 'NO REMARKS';
          ?>">
        </td>
        <td><strong>Outstanding Value:</strong>
         <?php
         $outs="SELECT 
         sum(`challan_order_details`.`taxable_amt`) AS AmountPaid 
         FROM `challan_order_details`
         INNER JOIN `challan_order` ON   `challan_order`.`id`=`challan_order_details`.`ch_id` WHERE `challan_order`.`ch_retailer_id`='$value[retailer_id]'";
             //h1($outs);                
         $out1= mysqli_query($dbc, $outs);
         $row2 = mysqli_fetch_assoc($out1);
         $amount_paid = $row2['AmountPaid'];
         $pay="SELECT sum(`payment_collection`.`total_amount`) AS PaymentCollect from `payment_collection` WHERE `retailer_id`='$value[retailer_id]'";
         $out2= mysqli_query($dbc, $pay);
         $row_pay = mysqli_fetch_assoc($out2);
         $payment_collect = $row_pay['PaymentCollect'];
         if(empty($payment_collect))
           $payment_collect ='0.00'; 
         $oustanding=$amount_paid-$payment_collect;
         echo $oustanding;
         echo"&nbsp;&nbsp;<strong>Payment Collection : </strong>".$payment_collect;
                           // h1($outs);
         ?>
       </td>
     </tr>
            <tr>
              <td colspan="3"><div class="subhead1">SALE WISE INVOICE DETAILS</div></td>
            </tr>
            <tr>
          <td colspan="3">
            <div id="product">
              <table width="100%" id="mytableabc">
                  <tr style="font-weight:bold;">
                    <td>S.NO</td>
                    <td>Items Name</td>
                    <td style="width: 70px;">M.R.P</td>                          
                    <td>Avlb. Stock</td>
                    <td>Quantity</td>                             
                    <td>Rate</td>                                               
                    <!--<td>Sch. Quantity</td>-->
                    <td>Trade/Sch. Disc. Type</td>
                    <td>Trade/Sch. Disc.</td>                                                
                    <td>Trade/Sch. Disc. Amt</td>                           
                    <td>C.D Type</td>
                    <td>C.D.</td>
                    <td>CD.Amt</td>
                    <td>Taxable Amt.</td>
                    <td>GST%</td>
                    <td>GST. Amt</td>
                    <td>Amount

                      <input type="hidden" name="retailer_id[<?=$value['order_id'];?>]" value="<?php echo $value['retailer_id']; ?>">
                      <input type="hidden" name="user_id[<?=$value['order_id'];?>]" value="<?php echo $value['user_id']; ?>">
                      <input type="hidden" name="uso_order_id[<?=$value['order_id'];?>]" value="<?php echo $value['order_id']; ?>">
                      <input type="hidden" name="dealer_id[<?=$value['order_id'];?>]" value="<?php echo $dealer_id; ?>">
                      <input type="hidden" name="discount[<?=$value['order_id'];?>]" value="<?php echo $value['discount']; ?>">


                    </td>
                    <!--<td style="width:10px" class="options">Options</td>-->
                  </tr>

                <?php

                $company_id = $value['company_id'];
                if (!empty($value['order_item']))
                {
                  static $i = 1;
                  $total = 0;
                  $total_td = 0;
                  $total_cd = 0;
                  $total_vat = 0;

                  foreach ($value['order_item'] as $inkey => $invalue)
                  {
                   $product_id = $invalue['product_id'];
                   $q = 'SELECT mrp,retailer_rate as rate from `product_rate_list` cp WHERE  state_id = '.$_SESSION[SESS.'data']['state_id'].' AND cp.product_id = '.$product_id.' Group by rate';

                   $rlist = mysqli_query($dbc, $q);
                   $row = mysqli_fetch_assoc($rlist);
                   $avl_item=$myobj->calculate_available_stock($invalue['product_id']);
                   $qty=$invalue['quantity'];
                   $uid1 = $invalue['product_id'] . $value['order_id'];
                   $uidname1 = $invalue['name'];
                   $item = $myobj->calculate_order_item($filter = "order_id = '$value[order_id]' AND product_id = '$invalue[product_id]'", $records = '', $orderby = '');
                   $item_rate = $myobj->item_details($filter = " product_id = '$invalue[product_id]' AND state_id ='$location_id' ", $records = '', $orderby = '');

                   $deletelink1 = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="challan_delete(\'Challan Item Update\', \'' . $uid1 . '\',\'Challan Item\',\'' . addslashes($uidname1) . '\',\'' . $value['order_id'] . '<$>' . $invalue['product_id'] . '\');"><img src="./images/b_drop.png"></a>';
                   
                   if($avl_item<$qty)
                    echo '<tr id="tr'.$uid1.'" class="ihighlight" bgcolor="pink">';
                  else
                   echo '<tr id="tr'.$uid1.'" class="ihighlight">';     
                 ?> 

                 <td valign="top"><?php echo $i; ?><input type="hidden" name="order_id[<?=$value['order_id'];?>][]" value="<?php echo $value['order_id']; ?>"></td>
    

    <?php
    $q1="select `product_id` AS pid from focus where product_id='$invalue[product_id]'";
    $flist= mysqli_query($dbc, $q1);
    $row1 = mysqli_fetch_assoc($flist);
    $focus = $row1['pid'];
    $prod_id=$invalue[product_id];
    $pname= wordwrap($invalue['name'],5,"<br/>\n");

    $o_id=$value['order_id'];

    if($focus==$prod_id) 
      echo '<td bgcolor="#32c403">'.$pname;
    else
      echo '<td>'.$pname;
    ?> 
    <input type="hidden" id="p<?php echo $i; ?>" name="product_id[<?=$o_id;?>][]" value="<?php echo $prod_id; ?>">
    <div style="display:none" id="delDiv<?php echo $uid1; ?>"></div>
    </td>

    <td>
      <input readonly type="text"  lang="mrp" id="mrp<?php echo $i; ?>" name="mrp[<?=$value['order_id'];?>][]" value="<?php echo $row['mrp']; ?>" >

    </td>
    <td>
     <input readonly id="aval_stock<?php echo $i; ?>" class="aval_stock" type="text" name="aval_stock[<?=$value['order_id'];?>][]" value="<?php echo $myobj->calculate_available_stock($invalue['product_id']); ?>"  />
    </td>
    <td><input readonly id="ch_qty<?php echo $i; ?>" class="ch_qty" onblur="cd_product_calculate(<?=$i;?>);" type="text" lang="quantity" name="quantity[<?=$value['order_id'];?>][]" value="<?php echo $invalue['quantity']; ?>"></td>
    <?php

    $gst="select igst from catalog_product cp INNER JOIN _gst ON cp.hsn_code=_gst.hsn_code where cp.id='$prod_id'";
                                        //h1($q1);
    $gstq= mysqli_query($dbc, $gst);
    $rowg = mysqli_fetch_assoc($gstq);
    $igst = $rowg['igst'];

                                    // print_r($item_rate);
    $rates = $row['rate'];
    $d_amt = ($rates * 18) / 100;
    $rate = $rates - $d_amt;
    $d_amt = ($rates * 5) / 100;
    $amount = $rates* $item['qty'];
    $vat_amt = ($amount * $igst) / 100;
    $f_amt = ($amount + $vat_amt);
    /////////////////////////////CHECK DISCOUNTS/////////////////////
    $cdr = "SELECT * FROM `retailer_cd` where `product_id`='$prod_id' AND retailer_id='$value[retailer_id]'"; 
    $cdrms=mysqli_query($dbc,$cdr);
    if(mysqli_num_rows($cdrms)>0)
    {
     $cdrow = mysqli_fetch_assoc($cdrms);
     $cdtype = $cdrow['cd_type'];
     $cd = $cdrow['cd'];
    }
    else {
      $cdtype = '';
      $cd = '0';

    }

    $tdr = "SELECT * FROM `retailer_trade` where `product_id`='$prod_id' AND retailer_id='$value[retailer_id]'"; 
              // h1($tdr);
    $tdrms=mysqli_query($dbc,$tdr);
    if(mysqli_num_rows($tdrms)>0)
    {
     $tdrow = mysqli_fetch_assoc($tdrms);
     $tradetype = $tdrow['trade_type'];
     $trade = $tdrow['trade_disc'];
    }
    else {
      $tradetype = '';
      $trade = '0';

    }


                    //echo "TRADE TYPE".$tradetype;                
    ?>

    <td><input readonly id="r<?php echo $i; ?>" onblur="cd_product_calculate(<?=$i;?>);" type="text" name="rate[<?=$value['order_id'];?>][]" value="<?php echo $rates; ?>"></td>
    <!--                                    <td><input readonly type="text" id="scheme<?php echo $i; ?>" name="scheme[<?=$value['order_id'];?>][]" value="<?php echo $invalue['scheme_qty']; ?>"></td>-->
    <td>
      <select id="trade_disc_type<?php echo $i; ?>" name="trade_disc_type[<?=$value['order_id'];?>][]" lang="trade_disc">
        <option value="1" <?php if($tradetype==1) echo"selected" ?> >%</option>dis_type
        <option value="2" <?php if($tradetype==2) echo"selected" ?>>Amount</option>
      </select>
    </td>
    <td>
      <input type="text" id="trade_disc_val<?php echo $i; ?>" name="trade_disc_val[<?=$value['order_id'];?>][]" value="<?php if(!empty($trade)) echo $trade;  ?>"  onblur="trade_disc_calculate(<?=$i;?>);"  />
    </td>   
    <?php  $tda = ($trade/100)*$invalue['quantity']*$rates;  ?>
    <td>
      <input type="text" id="trade_disc_amt<?php echo $i; ?>" name="trade_disc_amt[<?=$value['order_id'];?>][]" value="<?php if(!empty($trade)){ echo $tda;   }  ?>" />

      <input type="hidden" id="ttl_amt<?php echo $i; ?>" name="ttl_amt[<?=$value['order_id'];?>][]" value="<?=$amount?>"   />
    </td>
    <td width="50px">
      <select id="cd_type<?php echo $i; ?>" name="cd_type[<?=$value['order_id'];?>][]" lang="cdtype" >
        <option value="1" <?php if($cdtype==1) echo"selected" ?>>%</option>
        <option value="2" <?php if($cdtype==2) echo"selected" ?>>Amount</option>
      </select>
    </td>
    <td><input type="text" id="cd<?php echo $i; ?>" name="cd[<?=$value['order_id'];?>][]" value="<?php if(!empty($cd)) echo $cd;  ?>" onblur="cd_disc_calculate(<?=$i;?>);" ></td>
    <?php  $cda = ($cd/100)*$invalue['quantity']*$rates;  ?>
    <td><input type="text" id="cd_amt<?php echo $i; ?>" name="cd_amt[<?=$value['order_id'];?>][]" value="<?php if(!empty($cd)){ echo $cda;   }  ?>" onblur="total_amount_invoice(<?=$value['order_id'];?>,<?=$i;?>);"></td>
    <?php
    $amt = $amount-$cda-$tda;
    $famt = $f_amt-$cda-$tda;
    ?>
    <td>
      <input readonly type="text" id="taxable_amt<?php echo $i; ?>" name="taxable_amt[<?=$value['order_id'];?>][]"  value="<?php echo $rates*$invalue['quantity'];  ?>"   />
    </td>
    <td>
      <input type="hidden" id="state<?php echo $i; ?>" name="state[]" id="state" value="<?=$location_id?>"  />
      <input type="text" id="vat<?php echo $i; ?>" name="vat[<?=$value['order_id'];?>][]" onchange="cd_product_calculate(<?=$i;?>);" value="<?php echo $igst; ?>"></td>
    <td>
        <input readonly type="text" id="vat_amt<?php echo $i; ?>" name="vat_amt[<?=$value['order_id'];?>][]" value="<?php echo $vat_amt; ?>">
        <input   type="hidden" name="surcharge[]" id="surcharge<?php echo $i; ?>"  value="<?php echo $surcharge;?>"  />
      </td>
    <td><input readonly type="text" id="amount<?php echo $i; ?>" name="amount[<?=$value['order_id'];?>][]" value="<?php if(!empty($tda) || !empty($cda)) echo $famt; else echo $f_amt;?>"></td>

      <!--<td style="width:10px"><?php //echo $deletelink1; ?></td>-->

</tr>

            <?php
              $total_td = $tda+$total_td;
              $total_cd = $cda+$total_cd;
              $total_vat = $total_vat+$vat_amt;
              $total = $total+$famt;
              $i++;
              $totalamt = $totalamt+$f_amt;
              }
            ?>

            <tr>
              <td colspan="8">
                <strong>Remark</strong><br/><textarea  name="remark" style="width: 210px; height: 46px;" rows="2" cols="100"style="width:200px;"></textarea>
              </td>
              <td><strong>
                <input  style="width:98%" type="text" name="total_td" id="total_td" value="<?php  echo $total_td;   }  ?>" readonly>    </strong>                
              </td>
              <td></td><td></td>
              <td><strong>
                <input  style="width:98%" type="text" name="total_cd" id="total_cd" value="<?=$total_cd?>" readonly>    </strong>                
              </td>
              <td><strong>
                <input  style="width:98%" type="text" name="total_taxable" id="total_taxable" value="<?=($total-$total_vat)?>" readonly>    </strong>                
              </td>
              <td></td>
              <td><strong>
                <input  style="width:98%" type="text" name="total_vat" id="total_vat" value="<?=$total_vat?>" readonly>    </strong>                
              </td>
              <td style="width:100px"><input type="text" id="total" name="total" value="<?=$total?>"></td>

</tr>
            <tr><td colspan="18"><hr/></td></tr> 
            <tr>
             <td colspan="14"></td>
             <td colspan="2">
               <strong> Select Discount </strong><br/>
               <select name="dis" id="dis" onchange="total_amount_with_discount(this.value)">
                <option value="0">SELECT DISCOUNT</option>
                <option value="2"> 2% </option>
                <option value="3"> 3% </option>
                <option value="4"> 4% </option>
                <option value="5"> 5% </option>
              </select>  
            </td>

</tr>
            <tr>
             <td colspan="14"></td>
             <td colspan="2">
               <strong>Discount Amount</strong>
               <strong>
                <input  style="width:98%" type="text" name="total_disc" id="total_disc" value="0" readonly>    </strong>                


              </td>

</tr>
            <tr>
             <td colspan="14"></td>
             <td colspan="2">
               <strong>Total Amount</strong>
               <strong>
                <input  style="width:98%" type="text" name="total_amount_a" id="total_amount_a" value="<?=$total?>" readonly>    </strong>                


              </td>

</tr>


  <?php } ?>

<?php
$inc++;
                    } 
                     ?>
              <tr>
                      <td align="center" colspan="15">
                        <?php //form_buttons(); // All the form control button, defined in common_function   ?>
                        <input class="savebtn" id="mysave" type="submit" name="submit" value="<?php
                        if (isset($heid))
                          echo'Update';
                        else
                          echo'Save';
                        ?>" />
                        <?php
                        if (isset($heid)) {
                   echo $heid; //A hidden field name eid, whose value will be equal to the edit id. 
                   ?>
                   <input class="savebtn" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma; ?>" />  
                   <input onclick="parent.$.fn.colorbox.close();" class="exitbtn" type="button" value="Exit" /> <br />
                   <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
                   <?php } else { ?>
                   <input class="exitbtn" onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
                   <?php } ?>
                 </td>
               </tr>
             </table>
           </fieldset>
         </form>
       </div><!-- workarea div ends here -->
       <script type="text/javascript">setfocus('name');</script>
