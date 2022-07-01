<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php

$forma = 'SALE WISE INVOICE'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'order_against'; //The name of the function in the class that will do the job
$myorderby = 'demand_order.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'demand_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
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
// $location_list = $myobj->get_dealer_location_list($dealer_id);
?>
<script type="text/javascript">
  // WRITE THE VALIDATION SCRIPT IN THE HEAD TAG.
  function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
      return false;

    return true;
  }
</script>
<div id="breadcumb"><a href="#">Invoice</a> &raquo; <a></a>  &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> 
<span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/sale-order.php'); 
?>  
</div>
<div class="loader" style="margin: 0 auto;text-align:center;">
  <!-- <img src="images/loader1.gif"/> -->
  <b style="font-size: 50px;color: gray;">Loading...</b>
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
/*pre($_POST);
die;*/
if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
    //calculating the user authorisastion for the operation performed, function is defined in common_function
  list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
  if ($checkpass) {
        // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
       // magic_quotes_check($dbc, $check = true);
        //$funcname = $cls_func_str.'_save';
        $fdate = $_POST['fdate'];
        $tdate = $_POST['tdate']; 
        $action_status = $myobj->multi_challan_save(); // $myobj->item_category_save()
        if ($action_status['status']) {
           echo '<span class="asm">' . $action_status['myreason'] . '</span>';
          /*echo '<span class="asm" style="margin-top:200px;background-color:#D50714; color:#fff;">' . $action_status['myreason'] . '</span>';*/
          unset($_POST);
          if($action_status['invoice_url']!='')
          {
            header('location:index.php?option=make-challan&showmode=1&mode=1&actiontype=print&id='.$action_status['invoice_url']);
            die;
          }
          ?>
          <script>
            // setTimeout("window.parent.location = 'index.php?option=sale-order-detailes'", 500);
            var f = '<?php echo $fdate ?>';
            var t = '<?php echo $tdate ?>';
            setTimeout("window.parent.location = 'index.php?option=sale-order-detailes&fdate="+f+"&tdate="+t+"'", 500);
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
if (isset($_GET['mode']) && $_GET['mode'] == 1)
{
    if (isset($_GET['order_id']))
    {
          $order_id = $_GET['order_id'];
          $order_id = rtrim($order_id, ',');
          $order_id = "'".implode("','",explode(',',$order_id))."'";

          $filter[] = "order_id IN ($order_id)";
          // $user_data = $myobj->get_dsp_wise_user_data($sesId, $role_id, $dealer_id);
          // $user_data = $myobj->get_dsp_wise_user_data($dealer_id);

          /*if (!empty($user_data)) {
            $user_data_str = implode(',', $user_data);
        // $filter[] = "user_id IN ($user_data_str)";
          } // if(!empty($user_data)) end here*/
    //  $filter[] = "call_status = '1'";
    //  h1($funcname);
    $rs = $myobj->$funcname($filter, $records = '', $orderby = '',true); //get_dealer_sale_list
    /*pre($rs);
    die;*/
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

  function total_amount_with_discount(disc,total_disc,total_amount_a,total,oid){

    // var dis = Number(document.getElementById('dis').value);
    var dis = Number(disc);
    var total_disc = document.getElementById(total_disc);
    var totalamt = Number(document.getElementById(total_amount_a).value);
    var total = Number(document.getElementById(total).value);

    var discount = total*dis/100;
    totalamt = total-discount;

    if(totalamt<0)
    {
        alert('Invalid value');
        $('.dis'+oid+'').val(0);
        total_disc.value = 0;
        document.getElementById(total_amount_a).value = total;
        return false;
    }

    total_disc.value = discount.toFixed(2);
    document.getElementById(total_amount_a).value = totalamt.toFixed(2);

  }


  function total_amount_invoice(order_id,id){
// alert('amount['+order_id+']');
//var arr = document.getElementsByName('amount['+order_id+']');
var total =  document.getElementById('total'+order_id+'').value;
var qty = document.getElementById('ch_qty'+id).value;
var rate = document.getElementById('r'+id).value;
var vat = document.getElementById('vat'+id).value;
var vatamt = document.getElementById('vat_amt'+id).value;
var tda = document.getElementById('trade_disc_amt'+id).value;
var cda = document.getElementById('cd_amt'+id).value;
// var tot_td = Number(document.getElementById('total_td').value);
var tot_cd = Number(document.getElementById('total_cd').value);
var tot_taxa = Number(document.getElementById('total_taxable').value);
var tot_vat = Number(document.getElementById('total_vat').value);

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
// tot_td = td+tot_td;
tot_cd = tot_cd+cd;
tot_vat = tot_vat+taxamt;
tot_taxa = total-tot_vat;

document.getElementById('total'+order_id+'').value = total.toFixed(2);
// document.getElementById('total_td').value = tot_td.toFixed(2);
document.getElementById('total_cd').value = tot_cd.toFixed(2);
document.getElementById('total_taxable').value = tot_taxa.toFixed(2);
document.getElementById('total_vat').value = tot_vat.toFixed(2);
}


  var i = 1;
  $(document).on('click', '.addbutton', function (){
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
  $(document).on('click', '.removebutton', function (){
    $(this).closest('tr').remove();
    return false;
  });

  function checkuniquearray(name)
  {
    var arr = document.getElementsByName('product[]');
    var len = arr.length;

    var table = document.getElementById('product');
    var table = document.getElementById('mytableabc');

    var rows = $('tr.ihighlight');
    
    var low_stock = Array();
    for(var i=0; i<rows.length;i++)
    {
      var aval_stock = document.getElementsByClassName("aval_stock");
      var ch_qty = document.getElementsByClassName("ch_qty");
      var product_id = document.getElementsByClassName("item_details");

      
      if(parseInt(aval_stock[i].value)==0)
      {
        // alert(product_id[i].options[product_id[i].selectedIndex].text);
        low_stock.push(product_id[i].options[product_id[i].selectedIndex].text);
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
  
  function custum_function(pid, pvalue, event)
  {
    //var batchno = $("#"+pid).closest("td").next().find("select").attr("id");
    getajaxdata('get_mrp_vat', 'mytable1', event);
    setTimeout(function () {
      getajaxdata('get-stock-extra', 'mytable1', event);
    }, 300);
    setTimeout(function () {
      getajaxdata('get-calculate-rate-extra', 'mytable1', event);
    }, 600);

  }

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
          var  cd_amt = res.toFixed(2);
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
      document.getElementById('vat_amt'+order_id).value = vat_amt.toFixed(2);  
      document.getElementById('taxable_amt'+order_id).value = ttl_amt.toFixed(2);  
      document.getElementById('amount'+order_id).value = (ttl_amt+vat_amt).toFixed(2);
    }    

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
    var  tds_amt = res.toFixed(2);     
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
  // document.getElementById('ttl_amt'+order_id).value = ttldsp-wise-challan_amt;
  document.getElementById('vat_amt'+order_id).value = vat_amt.toFixed(2);  
  document.getElementById('taxable_amt'+order_id).value = ttl_amt.toFixed(2);  
  document.getElementById('amount'+order_id).value = (ttl_amt+vat_amt).toFixed(2);
}

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
    // var final_amt = document.getElementById('final_amt').value;      
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
    var cd_amt = cd_dis.toFixed(2);        
    var taxablamt = ttl_amt - cd_dis;
    var taxable_amt = taxablamt.toFixed(2);        
    var va = taxable_amt * vat;          
  }

  document.getElementById('cd_amt'+order_id).value = cd_amt;      
  document.getElementById('taxable_amt'+order_id).value = taxable_amt;      
  document.getElementById('vat_amt'+order_id).value = va.toFixed(2);

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

}

  function calc_total_amount(total_val,oid)
  {
    var sub_total = 0;
    var tot_tx = 0;
    var tot_vt = 0;
    var trade_disc = 0;
    var cd_amt = 0;

    var dicount_percent = $('.dis'+oid+'').val(); 

    $('.'+total_val+'').each(function(){
      sub_total += parseFloat($(this).val());
    })

    // alert(sub_total);

    $('.taxable_amt'+oid+'').each(function(){
      tot_tx += parseFloat($(this).val());
    })

    $('.vat_amt'+oid+'').each(function(){
      tot_vt += parseFloat($(this).val());
    })

    $('.tdamt'+oid+'').each(function(){
      var td = parseFloat($(this).val());
      if(td)
      {
        trade_disc += td;
      }
    })

    $('.cd_amt'+oid+'').each(function(){
      var cd = parseFloat($(this).val());
      if(cd)
      {
        cd_amt += cd;
      }
    })

    if(dicount_percent>0)
    {
     var dicount_amount = sub_total*dicount_percent/100
     $('.total_disc'+oid+'').val(dicount_amount.toFixed(2));
     $('.total_amount_a'+oid+'').val((sub_total-dicount_amount).toFixed(2));
   }else{
    $('.total_amount_a'+oid+'').val(sub_total.toFixed(2));
  }

  $('.totalkp'+oid+'').val(sub_total.toFixed(2));
  $('.total_tx_kp'+oid+'').val(tot_tx.toFixed(2));
  $('.total_vat_kp'+oid+'').val(tot_vt.toFixed(2));
  $('.total_td_kp'+oid+'').val(trade_disc.toFixed(2));
  $('.total_cd_kp'+oid+'').val(cd_amt.toFixed(2));
}
</script>

<form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" class="iform" name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
        <fieldset>
          <legend class="legend" style=""><?php echo $forma; ?></legend>
<?php
if (!empty($rs)) {
  $inc = 1;
  $loop=count($rs);


  $query = "select `ch_no` from `challan_order` where `ch_dealer_id`=$dealer_id order by `ch_no` DESC";
  $q = mysqli_query($dbc,$query);
  $row = mysqli_fetch_row($q);

  $ch = $row[0];
  $ch_value = explode('/',$ch);
  $value_inv = $ch_value[3];
  $value_year = $ch_value[2];
  
  // $chno = $value_inv+1;
  $d = date('Y').'-';
  if (strpos($value_inv, $d) !== false) {
       $chno = $ch_value[2]+1;
       $value_year = $ch_value[3];
  }else{
       $chno = $value_inv+1;
       $value_year = $ch_value[2];
  }

  /* For retailer dropdown */
  /*$q = "SELECT retailer.id as id, CONCAT(retailer.name,' [',retailer.address,'] ')as name FROM retailer where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' group by retailer.id ORDER BY retailer.name ASC  ";
  $ret_q = mysql_query($q);
  $ret_data = array();
  while($ret = mysqli_fetch_assoc($ret_q[1]))
  {
    $ret_data[$ret['id']] = $ret['name'];
  }*/
  // pre($ret_data);
  
  foreach ($rs as $key => $value) { 

    /*if(empty($value['order_item'])  && $_SERVER['REQUEST_METHOD']=='GET')
    {       
       die('<div class="alert alert-danger">Please updated stock for this order.</div>');
    }*/

    // <statecheck for="For checking wether retailer and dealer both are from same state.">
      $pk=0;
    // <statecheck">
    ?>
    <div id="workarea">
      
          <input type="hidden" name="fdate" value="<?php echo $_GET['fd'] ?>">
          <input type="hidden" name="tdate" value="<?php echo $_GET['td'] ?>">

          <input type="hidden" name="hf" value="<?php echo $securetoken; ?>" />
          <input type="hidden" id="loc_level" name="loc_level[<?=$value['order_id'];?>]" value="<?php echo $loc_level; ?>">
          <input type="hidden" name="dealer_id[<?=$value['order_id'];?>]" id="dealer_id" value="<?php if (isset($dealer_id)) echo $dealer_id; ?>">
          <input type="hidden" name="date[<?=$value['order_id'];?>]" id="date" value="<?=$value['date']; ?>">
          <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform" id="mytable">
          <!--<tr><td colspan="15"><hr></td></tr>-->
          <tr>
                <td colspan="18">
                <img src="./images/pink.jpg" style="width:28px; height: 26px"/>&nbsp;&nbsp;<strong>Available Quantity less than Required Quantity</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/green.jpeg" style="width:28px; height: 28px"/>&nbsp;&nbsp;<strong>Focus Product</strong>

                <input type="radio" name="wsr" class="wsr" id="mrps" value="1" />MRP Wise Billing
                <input type="radio" name="wsr" class="wsr" id="wsr" value="2" />WSP Wise Billing
              </td>
            </tr>
           <!-- MY DESIGN -->
           <tr>
            <td style=" border:1px solid #f5f5f5;"><strong>Invoice Date:</strong>
             <input class="datepicker" type="text" name="ch_date[<?=$value['order_id'];?>]" value="<?php
             if (isset($value['sale_date']))
              echo $value['sale_date'];
            else
              echo date('d/M/Y');
            ?>">
          </td>

          <!-- <td width="30%" style=" border:1px solid #f5f5f5;">
            <strong><span class="star">*</span>Salesman</strong><br>
            <?php 

            $order_id = $value['order_id']; 
            $u_q = "SELECT d.user_id,CONCAT_WS(' ',p.first_name,p.last_name, '[',r.rolename,']') FROM `user_dealer_retailer` d INNER JOIN person p ON d.user_id=p.id INNER JOIN _role r ON p.role_id=r.role_id  WHERE d.dealer_id = '" . $_SESSION[SESS . 'data']['dealer_id'] . "'";

            db_pulldown170($dbc, "user_id[$order_id]", $u_q, true, true, 'class="chosen-select retailer" id="user_id'.$order_id.'"','==Please Select==',$value['user_id']);

            ?>
          </td>
          <td width="30%" style=" border:1px solid #f5f5f5;">
            <strong><span class="star">*</span>Retailer Name</strong><br>

            <?php

            /*$q = "SELECT retailer.id as id ,concat(retailer.name,' [',location_5.name,']') as `name` from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where dealer_id='$dealer_id' order by retailer.name asc";*/
            // $q = "SELECT retailer.id as id, CONCAT(retailer.name,' [',retailer.address,'] ')as name FROM retailer where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' group by retailer.id ORDER BY retailer.name ASC  ";

             $q = "SELECT retailer.id as id, CONCAT(retailer.name,' [',retailer.address,'] ')as name FROM retailer inner join location_5 on location_5.id = retailer.location_id inner join dealer_location_rate_list on dealer_location_rate_list.location_id = location_5.id where dealer_location_rate_list.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' group by retailer.id ORDER BY retailer.name ASC  ";



            db_pulldown170($dbc, "retailer_id[$order_id]", $q, true, true, 'class="chosen-select" id="retailer_id'.$order_id.'"','==Please Select==',$value['retailer_id']);
            ?>
          </td> -->


           <td style=" border:1px solid #f5f5f5;"><strong>Firm Name:</strong>
              <input style="width:80%" id="firm_name" name="firm_name[<?=$value['order_id'];?>]" type="text" value="<?php echo $value['firm_name']; ?>" readonly>
           </td>

              <input style="width:80%" id="firm_id" name="firm_id[<?=$value['order_id'];?>]" type="text" value="<?php echo $value['firm_id']; ?>" readonly>


        </tr>
   
            <tr>
              <td style=" border:1px solid #f5f5f5;"><strong>Invoice Number:</strong>
                <?php
                $dealer_id1=$_SESSION[SESS . 'data']['dealer_id'];
                $query = "select `ch_no` from `challan_order` where `ch_dealer_id`=$dealer_id1 AND book_status='0' and ch_no like '%BDN%' order by `ch_no` DESC";
                $q = mysqli_query($dbc,$query);
                $row = mysqli_fetch_row($q);
                // print_r($row);die;
                $ch = $row[0];
                $ch_value = explode('/',$ch);
                $value_inv = $ch_value[3];
                $value_year = $ch_value[2];
            ////////////////////////////////////FOR SESSION///////////////////////////////
                $query1 = "select `session` from `session` where `action`='1'";
                $q1 = mysqli_query($dbc,$query1);
                $row1 = mysqli_fetch_row($q1);
                $year = $row1[0];

                $d = date('Y').'-';                

              if (strpos($value_inv, $d) !== false) {
                   $jj = $ch_value[3];
                   $value_year = $ch_value[3];
              }else{
                   // $jj = $value_inv+1;
                   $jj = $value_inv;
                   $value_year = $ch_value[2];
              }

                //$num = str_pad($chno++,6,'0',STR_PAD_LEFT);
                $num = str_pad($jj+$inc,6,'0',STR_PAD_LEFT);
                if($year == $value_year)
                {
                   /*for($i=1;$i<=$loop;$i++)
                   {
                      $val = $value_inv;
                      $jj[$i] = $value_inv+$i;
                      $value_inv = $val;
                   }*/
                $ch_id[$inc] = "BDN/".$dealer_id."/".$year."/".$num; 
               
              }
              else
              {
               $ch_id[$inc] = "BDN/".$dealer_id."/".$year."/".$num; 
             }

             ?>

             <input style="width:40%" id="ch_no" name="ch_no[<?=$value['order_id'];?>]" type="text" value="<?php echo $ch_id[$inc] ?>" readonly>
             
           </td>
           <td width="225px">
            <input readonly type="text" value="<?php
           if (isset($value['remarks']))
            echo $value['remarks'];
          else
            echo 'NO REMARKS';
          ?>">
        </td>
        <td><strong>Outstanding Value:</strong>
         <?php
         // $outs="SELECT 
         // sum(`challan_order_details`.`taxable_amt`) AS AmountPaid 
         // FROM `challan_order_details`
         // INNER JOIN `challan_order` ON   `challan_order`.`id`=`challan_order_details`.`ch_id` WHERE `challan_order`.`ch_retailer_id`='$value[retailer_id]'";
         $outs="SELECT 
         sum(`challan_order`.`amount_round`) AS AmountPaid 
         FROM  `challan_order` WHERE `challan_order`.`ch_retailer_id`='$value[retailer_id]'";
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
              <table width="100%" id="mytableabc" class="table<?php echo $value['order_id']?>">
                  <tr style="font-weight:bold; background-color: #acb3b7;">
                    <td style=" border:1px solid #000000;">#</td>
                    <td style=" border:1px solid #000000;">Items Name</td>
                    <!-- <td style=" border:1px solid #000000;">Bill of Supply</td> -->
                    <td style="width: 60px; border:1px solid #000000;">M.R.P/Avl. Stock/Batch No./Exp Date</td>
                    <td style=" border:1px solid #000000;">Avlb. Stock</td>
                    <td style=" border:1px solid #000000;">Qty</td>
                    <td style=" border:1px solid #000000;">Free Qty</td>
                    <td style=" border:1px solid #000000;">Rate</td>
                    <!--<td>Sch. Quantity</td>-->
                    <td style=" border:1px solid #000000;">T.D.Type</td>
                    <td style=" border:1px solid #000000;">T.D.</td>
                    <td style=" border:1px solid #000000;">T.D.Amt</td>

                    <td style=" border:1px solid #000000;">CD Type</td>
                    <td style=" border:1px solid #000000;">CD Disc</td>
                    <td style=" border:1px solid #000000;">CD Amt</td>

                    <td style=" border:1px solid #000000;">Spl Disc Amt</td>

                    <td style=" border:1px solid #000000;">ATD Type</td>
                    <td style=" border:1px solid #000000;">ATD</td>
                    <td style=" border:1px solid #000000;">ATD Amt</td>
                    <td style=" border:1px solid #000000;">Taxable Amt.</td>
                    <td style=" border:1px solid #000000;">CGST%</td>
                    <td style=" border:1px solid #000000;">CGST Amt</td>
                    <td style=" border:1px solid #000000;">SGST%</td>
                    <td style=" border:1px solid #000000;">SGST Amt</td>
                    <td style=" border:1px solid #000000;">IGST%</td>
                    <td style=" border:1px solid #000000;">IGST Amt</td>
                   <td style=" border:1px solid #000000;">Amount
                      
                      <input type="hidden" name="user_id[<?=$value['order_id'];?>]" value="<?php echo $value['user_id']; ?>">
                      <input type="hidden" name="uso_order_id[<?=$value['order_id'];?>]" value="<?php echo $value['order_id']; ?>">
                      <input type="hidden" name="order_id[<?=$value['order_id'];?>]">
                      <input type="hidden" name="dealer_id[<?=$value['order_id'];?>]" value="<?php echo $dealer_id; ?>">
                      <input type="hidden" name="discount[<?=$value['order_id'];?>]" value="<?php echo $value['discount']; ?>">
                      <input type="hidden" name="deleted_order_item[<?=$value['order_id'];?>]" value="" class="deleted_order_item">

                    </td>
                    <td style="width:10px;border:1px solid #000000;" class="options">#</td>
                  </tr>

                <?php
                // print_r($value['order_item']);die;
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
                   $qty=($invalue['remaining_qty']>0)?$invalue['remaining_qty']:$invalue['quantity'];
                   // <statecheck for="For checking wether retailer and dealer both are from same state.">
                   $gst="select cgst,sgst,igst from catalog_product cp INNER JOIN _gst ON cp.hsn_code=_gst.hsn_code where cp.id='$product_id'";
                   $gstq= mysqli_query($dbc, $gst);
                   $rowg = mysqli_fetch_assoc($gstq);
                   $igst = '';
                   $cgst = '';
                   $sgst = '';

                   $r_id = $value['retailer_id'];
                   $rs_q = "SELECT DISTINCT l1_id as r_state FROM retailer INNER JOIN location_view ON l5_id=location_id WHERE retailer.id=$r_id LIMIT 1";    

                   $q1_e = mysqli_query($dbc, $rs_q);
                   $rt = mysqli_fetch_assoc($q1_e);

                   $st = 0;
                   $gstamt = '';

                 $where_mrp=" product_id=$product_id AND dealer_id = $dealer_id AND qty!='0' ORDER BY mrp ASC,mfg ASC";
                 $select_mrp=myrowval('stock','mrp',$where_mrp);
                // h1($select_mrp);
                 $where_rate=" product_id=$product_id AND dealer_id = $dealer_id AND mrp='$select_mrp' AND qty!='0' ORDER BY mrp ASC,mfg ASC";
                 $rates=myrowval('stock','rate',$where_rate);
                 //h1($rates);
                 //  $rates = $invalue['rate'];
                   $amount = $rates*$qty;

                   if($rt['r_state']==$location_id)
                   {
                       $cgst = $rowg['cgst'];
                       $sgst = $rowg['sgst'];
                       $pkigst = 1;
                       $gstamt = (($amount*$cgst)/100)*2;
                       $f_amt = ($amount+$gstamt);
                   }else{
                       $pkigst = 0;
                       $igst = $rowg['igst'];
                       $vat_amt = ($amount*$igst)/100;
                       $f_amt = ($amount+$vat_amt);
                   }

                   // </statecheck>

                  /* $q = 'SELECT mrp,retailer_rate as rate from `product_rate_list` cp WHERE  state_id = '.$_SESSION[SESS.'data']['state_id'].' AND cp.product_id = '.$product_id.' Group by rate';

                   $rlist = mysqli_query($dbc, $q);
                   $row = mysqli_fetch_assoc($rlist);*/

                   $w = "product_id=$product_id AND dealer_id=$dealer_id AND mrp='$select_mrp' AND qty!='0' ORDER BY mrp ASC,mfg ASC";
                   $a_qty = myrowval('stock','qty',$w);

                   $avl_item=$a_qty;//$invalue['aval_qty']; //$myobj->calculate_available_stock($invalue['product_id']);
                   
                   $uid1 = $invalue['product_id'] . $value['order_id'];
                   $uidname1 = $invalue['name'];
                  // $item = $myobj->calculate_order_item($filter = "order_id = '$value[order_id]' AND product_id = '$invalue[product_id]'", $records = '', $orderby = '');
                   //$item_rate = $myobj->item_details($filter = " product_id = '$invalue[product_id]' AND state_id ='$location_id' ", $records = '', $orderby = ''); 

                   // $aval_qty = $invalue['aval_qty'];
                   $aval_qty = $a_qty;
                   $trclass = $value['order_id'].$i;

                   /*$deletelink1 = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="challan_delete(\'Challan Item Update\', \'' . $uid1 . '\',\'Challan Item\',\'' . addslashes($uidname1) . '\',\'' . $value['order_id'] . '<$>' . $invalue['product_id'] . '\');"><img src="./images/b_drop.png"></a>';*/

                   $deletelink1 = '';
                   $deletelink1 = '<img title="more" src="images/more.png" onclick="addnewrow(this,\''.$value['order_id'].'\','.$i.');"><br/>';
                   $deletelink1 .= ' <img title="less" src="images/less.png" class="removenewrow" data-pid="'.$invalue['id'].'" data-oid="'.$value['order_id'].'">';
                   
                   if($avl_item<$qty)
                    echo '<tr id="tr'.$uid1.'" class="ihighlight tdata tr'.$trclass.'" bgcolor="pink">';
                  else
                   echo '<tr id="tr'.$uid1.'" class="ihighlight tdata tr'.$trclass.'">';
                 ?> 

                 <td valign="top" class="introw"><?php echo $i; ?>
                 <input type="hidden" name="order_id[<?=$value['order_id'];?>][]" value="<?php echo $value['order_id']; ?>" class="oid">
               </td>
    

    <?php
    $q1="select `product_id` AS pid from focus where product_id='$invalue[product_id]'";
    $flist= mysqli_query($dbc, $q1);
    $row1 = mysqli_fetch_assoc($flist);
    $focus = $row1['pid'];
    $prod_id=$invalue['product_id'];
    $pname= '';//wordwrap($invalue['name'],5,"<br/>\n");

    $o_id=$value['order_id'];

    if($focus==$prod_id) 
      echo '<td bgcolor="#32c403">'.$pname;
    else
      echo '<td>'.$pname;

   // $prod_q = 'SELECT cp.id, cp.name from catalog_product cp INNER JOIN stock s ON cp.id = s.product_id where s.dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' ORDER BY cp.id';
    $prod_q = 'SELECT cp.id, cp.name from catalog_product cp  ORDER BY cp.id';
  
     db_pulldown170($dbc, "product_id[$o_id][]", $prod_q, TRUE, TRUE,"id='p$i' class='item_details chosen-select' data-placeholder='Select an item' ",'',$prod_id);

    ?> 

    <?php if($pk++ == 0){ ?>
          <input type="hidden" class="pkigst<?php echo $order_id?>" value="<?php echo $pkigst ?>">
    <?php }?>

   <!--  <input type="hidden" id="p<?php echo $i; ?>" name="product_id[<?=$o_id;?>][]" value="<?php echo $prod_id; ?>" class="item_details"> -->
    <div style="display:none" id="delDiv<?php echo $uid1; ?>"></div>
    </td>
    <!-- <td><input type="checkbox" name="bos[<?=$value['order_id'];?>][]" value="1"/></td> -->
    <td>

      <?php 
        $mrp_list = get_product_multiple_mrp($dbc,$product_id,$value['dealer_id'],true);
        //print_r($mrp_list);
        //h1($invalue['mrp']);
        $mrp = min($mrp_list);
        ?>

      <select style="width:98%;" name="mrp[<?=$value['order_id'];?>][]" class="mrp mrp_dd calcpk" lang="mrp" id="mrp<?php echo $i; ?>">
        <?php 
              foreach($mrp_list as $m_val)
              {
                 $selected = ($m_val['mrp']==$invalue['mrp']) ? "selected":"";
                 echo "<option value='".$m_val['mrp_disp']."' $selected>".$m_val['mrp_disp']."</option>";
              }
         ?>
      </select>

      <!-- <input readonly type="text" lang="mrp" id="mrp<?php echo $i; ?>" name="mrp[<?=$value['order_id'];?>][]" value="<?php echo $row['mrp']; ?>" > -->

    </td>
    <td>     
     <input style="width:43px;" readonly id="aval_stock<?php echo $i; ?>" class="aval_stock avlb_quantity" type="text" name="aval_stock[<?=$value['order_id'];?>][]" value="<?php echo $aval_qty ?>"  />
   </td>
   <td><input id="ch_qty<?php echo $i; ?>" class="ch_qty quantitycl calcpk" type="text" lang="quantity" onkeypress="javascript:return isNumber(event)" name="quantity[<?=$value['order_id'];?>][]" value="<?php echo $qty; ?>"></td>
   <td>
    <input type="text" name="freeqty[<?=$value['order_id'];?>][]" value="">
  </td>
    <?php    

    // $mrp_calc = new_rate_calc($igst,$product_id,$mrp);
    // $mrp_calc = new_rate_calc2($igst,$product_id,$mrp,$value['sale_date']);
    

    /////////////////////////////CHECK DISCOUNTS/////////////////////

      $cdtype = '';
      $cd = '0';
      $tradetype = '';
      $trade = '0';

    //}
    //echo "TRADE TYPE".$tradetype;                
    ?>

    <td>
    <input  id="r<?php echo $i; ?>" type="text" class="rate" name="rate[<?=$value['order_id'];?>][]" value="<?php echo $rates ?>"></td>
    <input  id="hr<?php echo $i; ?>" type="hidden" class="hrate" name="hrate[<?=$value['order_id'];?>][]" value="<?php echo $rates ?>"></td>
    <!--<td><input readonly type="text" id="scheme<?php echo $i; ?>" name="scheme[<?=$value['order_id'];?>][]" value="<?php echo $invalue['scheme_qty']; ?>"></td>-->
    <td>
      <select id="trade_disc_type<?php echo $i; ?>" name="trade_disc_type[<?=$value['order_id'];?>][]" lang="trade_disc" class="trade_disc_type calcpk">
        <option value="1" <?php if($tradetype==1) echo"selected" ?> >%</option>dis_type
        <option value="2" <?php if($tradetype==2) echo"selected" ?>>Amount</option>
      </select>
    </td>
    <td>
      <input type="text" id="trade_disc_val<?php echo $i; ?>" name="trade_disc_val[<?=$value['order_id'];?>][]" value="<?php if(!empty($trade)) echo $trade;  ?>" class="trade_disc_val calcpk" onkeypress="javascript:return isNumber(event)"/>
    </td>   
    <?php  $tda = ($trade/100)*$qty*$rates;  ?>
    <td>
      <input readonly type="text" id="trade_disc_amt<?php echo $i; ?>" class="tdamt<?php echo $key?> trade_disc_amt" name="trade_disc_amt[<?=$value['order_id'];?>][]" value="<?php if(!empty($trade)){ echo $tda;   }  ?>" />

      <input type="hidden" id="ttl_amt<?php echo $i; ?>" name="ttl_amt[<?=$value['order_id'];?>][]" value="<?=$amount?>"   />
    </td>
    <td>
      <select name="spl_disc_type[<?=$value['order_id'];?>][]" lang="spl_disc" class="spl_disc_type calcpk">
        <option value="1">%</option>
        <option value="2">Amount</option>
      </select>
    </td>
    <td>
      <input placeholder="CD Val" type="text" name="spl_disc_val[<?=$value['order_id'];?>][]" value="" class="spl_disc_val calcpk" onkeypress="javascript:return isNumber(event)">
    </td>
    <td>
      <input placeholder="CD Amt." type="text" name="spl_disc_amt[<?=$value['order_id'];?>][]" value="" class="spl_disc_amt" readonly="">
    </td>
    <td>
      <input placeholder="Spl Amt." type="text" name="cash_amt[]" value="" class="cash_amt">
    </td>
    <td width="50px">
      <select id="cd_type<?php echo $i; ?>" name="cd_type[<?=$value['order_id'];?>][]" lang="cdtype" class="cd_type calcpk">
        <option value="1" <?php if($cdtype==1) echo"selected" ?>>%</option>
        <option value="2" <?php if($cdtype==2) echo"selected" ?>>Amount</option>
      </select>
    </td>
    <td><input type="text" id="cd<?php echo $i; ?>" name="cd[<?=$value['order_id'];?>][]" value="<?php if(!empty($cd)) echo $cd;  ?>" class="cd calcpk" onkeypress="javascript:return isNumber(event)"></td>
    <?php  $cda = ($cd/100)*$qty*$rates;  ?>
    <td><input readonly type="text" id="cd_amt<?php echo $i;?>" class="cd_amt<?php echo $key; ?> cd_amt" name="cd_amt[<?=$value['order_id'];?>][]" value="<?php if(!empty($cd)){ echo $cda;   } ?>"></td>
    <?php
      $amt = $amount-$cda-$tda;
      $famt = $f_amt-$cda-$tda;
    ?>
    <td>
      <input readonly type="text" class="taxable_amt<?php echo $key; ?> taxable_amt" id="taxable_amt<?php echo $i; ?>" name="taxable_amt[<?=$value['order_id'];?>][]" value="<?php echo $rates*$qty;?>"   />
    </td>
    <td><input type="text" name="cgst[<?=$value['order_id'];?>][]" class="cgst" value="<?php echo round($cgst,2); ?>"></td>
    <td><input type="text" name="cgst_amt[<?=$value['order_id'];?>][]" class="cgst_amt" value="<?php echo $gstamt ?>"></td>
    <td><input type="text" name="sgst[<?=$value['order_id'];?>][]" class="sgst" value="<?php echo round($sgst,2); ?>"></td>
    <td><input type="text" name="sgst_amt[<?=$value['order_id'];?>][]" class="sgst_amt" value="<?php echo $gstamt ?>"></td>
    <td>
      <input type="hidden" id="state<?php echo $i; ?>" name="state[]" id="state" value="<?=$location_id?>"  />
      <input readonly type="text" id="vat<?php echo $i; ?>" name="vat[<?=$value['order_id'];?>][]" value="<?php echo round($igst,2); ?>" class="vat"></td>
    <td>
        <input readonly type="text" class="vat_amt<?php echo $key; ?> vat_amt" id="vat_amt<?php echo $i; ?>" name="vat_amt[<?=$value['order_id'];?>][]" value="<?php echo round($vat_amt,2); ?>">
        <input type="hidden" name="surcharge[]" id="surcharge<?php echo $i; ?>" value="<?php echo $surcharge;?>"  />
      </td>
    <td><input readonly type="text" class="amountpk<?php echo $key; ?> amount" id="amount<?php echo $i; ?>" name="amount[<?=$value['order_id'];?>][]" value="<?php if(!empty($tda) || !empty($cda)) echo round($famt,2); else echo round($f_amt,2);?>"></td>

      <td style="width:10px"><?php echo $deletelink1; ?></td>

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

            <tr class="xyz">
              <td colspan="16">
                <strong>Remark</strong><br/><textarea  name="remark" style="width: 210px; height: 46px;" rows="2" cols="100" style="width:200px;"><?php echo $value['remarks'] ?></textarea>
              </td>
              <td colspan="9">
                <table width="100%">
                 <tbody><tr>
                   <td><strong>Total Trade Discount</strong></td>
                   <td><strong>Total Cash Discount</strong></td>
                   <td><strong>Total Taxable Amount</strong></td>
                   <td><strong>Total Tax Amount</strong></td>
                   <td><strong>Total Amount</strong></td>
                 </tr>
                 <tr>
                  <td align="right">
                   <strong>
                     <input   type="text" name="total_td[<?php echo $key ?>]" class="total_td_kp<?php echo $key?>" id="total_td<?php echo $key?>" value="<?php  echo $total_td; ?>" readonly></strong>
                   </td>
                   <td align="right"><strong>
                     <input  type="text" name="total_cd[<?php echo $key ?>]" class="total_cd_kp<?php echo $key?>" id="total_cd<?php echo $key?>" value="<?=$total_cd?>" readonly></strong>
                   </td>
                   <td align="right"><strong>
                     <input  type="text" name="total_taxable[<?php echo $key ?>]" class="total_tx_kp<?php echo $key?>" id="total_taxable<?php echo $key?>" value="<?=round(($total-$total_vat),2)?>" readonly></strong>
                   </td>
                   <td align="right"><strong>
                     <input  type="text" name="total_vat[<?php echo $key ?>]" class="total_vat_kp<?php echo $key?>" id="total_vat<?php echo $key?>" value="<?=round($total_vat,2)?>" readonly></strong>
                   </td>
                   <td align="right"><strong>
                     <input type="text" class="totalkp<?php echo $key?>" id="total<?php echo $key?>" name="total[<?php echo $key ?>]" value="<?=round($total,2)?>" readonly></strong>
                   </td>
                 </tr>
               </tbody></table>
</td>
           </tr>
            <tr><td colspan="25"><hr/></td></tr> 
            <tr>
             <td colspan="18"></td>
             <td colspan="8">
              <table style="width: 100%;">
                <tr>
                  <td><strong> Enter Discount % </strong></td>
                  <td>
                                 <!-- <select name="dis[<?=$value['order_id'];?>]" id="dis" class="dis dis<?php //echo $key?>" onchange="total_amount_with_discount(this.value,'total_disc<?php //echo $i?>','total_amount_a<?php //echo $i?>','total<?php //echo $key?>')" style="width:100%">
                                  <option value="0">SELECT DISCOUNT</option>
                                  <option value="2"> 2% </option>
                                  <option value="3"> 3% </option>
                                  <option value="4"> 4% </option>
                                  <option value="5"> 5% </option>
                                </select> -->
                                <input type="text" name="dis[<?=$value['order_id'];?>]" id="dis<?php echo $key?>" class="dis dis<?php echo $key?>" onkeyup="total_amount_with_discount(this.value,'total_disc<?php echo $key?>','total_amount_a<?php echo $key?>','total<?php echo $key?>','<?php echo $key?>')" style="width:100%" value="0"/>
                              </td>
                            </tr>
                            <tr>
                              <td><strong>Discount Amount</strong></td>
                              <td>
                                <strong>
                                 <input  style="width:100%" type="text" name="total_disc[<?=$value['order_id'];?>]" class="total_disc<?php echo $key?>" id="total_disc<?php echo $key?>" value="0" readonly></strong>
                               </td>
                             </tr>
                             <tr>
                              <td><strong>Total Amount</strong></td>
                              <td>
                                <strong> 
                                 <input  style="width:100%" type="text" class="total_amount_a<?php echo $key?>" name="total_amount_a[<?=$key;?>]" id="total_amount_a<?php echo $key?>" value="<?=round($total,2)?>" readonly>    </strong> 
                               </td>
                             </tr>
                           </table>
                         </td>

                       </tr>            
            <tr><td colspan="25"><hr></td></tr>

  <?php } 
        $inc++;
              } }
                     ?>
              <tr>
                      <td align="center" colspan="18">
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
       <tr><td></td></tr>
       <script src="assets/js/ace.min.js"></script>
       <script type="text/javascript" src="js/pkcalculation.js"></script>  
       <script type="text/javascript">
       $(function(){
         if(!ace.vars['touch']) {
              $('.chosen-select').chosen({allow_single_deselect:true});
         }
       })
        function addnewrow(ts,oid,pid)
        {
            var table = $('.table'+oid+'');
            var cb = $('#mytable').find('input[type="checkbox"]').length;
            //alert(cb);
            var timestamp = new Date().getUTCMilliseconds();
            var i = timestamp*parseInt(cb);//parseInt(cb)+2;

            var $c_row = $('.tr'+oid+pid+'').clone();

            $c_row.find('.item_details').removeClass('chzn-done').css({'display':'block'}).attr('id','p'+i).val('').next('div').remove();

            $c_row.attr('class','tdata tr'+oid+i+'');
            $c_row.removeAttr('bgcolor');
            // $c_row.find('.item_details').attr('id','p'+i);
            $c_row.find('.mrp').html('').attr('id','mrp'+i);

            $c_row.find("input").each(function(){
                    $(this).val('');
                })

            $c_row.find("td").removeAttr('bgcolor');
            $c_row.find("input[type='checkbox']").val(1);
            $c_row.find('.aval_stock').removeAttr('value').attr('id','aval_stock'+i);
            $c_row.find('.ch_qty').removeAttr('value').attr({id:'ch_qty'+i});
            $c_row.find('.rate').removeAttr('value').attr({id:'r'+i});
            $c_row.find('select[name="trade_disc_type['+oid+'][]"]').attr({id:'trade_disc_type'+i});
            $c_row.find('input[name="trade_disc_val['+oid+'][]"]').attr({id:'trade_disc_val'+i});
            $c_row.find('input[name="trade_disc_amt['+oid+'][]"]').attr({id:'trade_disc_amt'+i});
            $c_row.find('input[name="ttl_amt['+oid+'][]"]').removeAttr('value').attr({id:'ttl_amt'+i});
            $c_row.find('select[name="cd_type['+oid+'][]"]').attr({id:'cd_type'+i});
            $c_row.find('input[name="cd['+oid+'][]"]').attr({id:'cd'+i});
            $c_row.find('input[name="cd_amt['+oid+'][]"]').attr({id:'cd_amt'+i});
            $c_row.find('input[name="taxable_amt['+oid+'][]"]').removeAttr('value').attr({id:'taxable_amt'+i});
            $c_row.find('input[name="state[]"]').attr({id:'state'+i});
            $c_row.find('input[name="vat['+oid+'][]"]').removeAttr('value').attr({id:'vat'+i});
            $c_row.find('input[name="vat_amt['+oid+'][]"]').removeAttr('value').attr({id:'vat_amt'+i});
            $c_row.find('input[name="amount['+oid+'][]"]').removeAttr('value').attr({id:'amount'+i});
            $c_row.find('img[title="more"]').attr({onclick:'addnewrow(this,\''+oid+'\','+i+')'});
            $c_row.find('img[title="less"]').attr('data-pid','0');
            // $c_row.find('img[title="less"]').attr('del-data','0');

            table.find('.xyz').before($c_row); 
            $('#p'+i+'').chosen();

            $('#mytable td.introw').each(function(j){
              var new_index = j+1;
              var hid_oid = new_index+'<input type="hidden" name="order_id['+oid+'][]" value="'+oid+'" class="oid">';
              $(this).html(hid_oid);
            });            
        }

        //Updated By Deepak at 31-05-2019
        //Because in case of remove row calculation is not working
        $('#mytable').on('click','.removenewrow',function()
        { 
            var tot_row = $('#mytable').find('.item_details').length;
            var oid = $(this).attr('data-oid');

              if(tot_row==1)
              {
                alert('At least one item should remain in the order.');
                return false;
              }
                
                if(tot_row>1)
                {
                  $(this).closest('tr').remove();
                  calc_total_amount('amountpk'+oid+'',''+oid+'');
                }
            return false;
        });

        // $('#mytable').on('click','.removenewrow',function()
        // {

        //     var table = $(this).closest('table');
        //     var i = table.find('input[type="checkbox"]').length;

        //     var del_items = table.find('.deleted_order_item');
        //     var usod_id = $(this).attr('data-pid');
        //     var oid = $(this).attr('data-oid');
            
        //     if(usod_id!=0 && i>1)
        //     {
        //       del_items.val(del_items.val()+','+usod_id)
        //     }

        //     if(i==1)
        //     {
        //        alert('At least one item should remain in the order.');
        //        return false;
        //     }
        //    $(this).closest('tr').remove();
        //    $('#mytable td.introw').each(function(j){
        //                   $(this).html(j+1);
        //               });
        //     calc_total_amount('amountpk'+oid+'',''+oid+'');
        // })
         <?php if(!empty($rs) && $inc>1) { ?>
                         $('.loader').hide();
                 <?php } ?>
                 $('.datepicker').datepicker({ minDate: 0,dateFormat: 'dd/mm/yy'});
       </script>
       <script type="text/javascript">setfocus('name');</script>
<script>
        $(".wsr").change(function () {
    var rate=0;
      if ($("#mrps").is(":checked")) {
       // alert('ss');
       var expl=[];
       var mrate=0;
        $(".mrp").each(function(k,v){
        	 expl=[];
        	 mrate=0;
     rate=$(this).closest('tr').find('.mrp').val(); 
     if(!rate){
     	rate='0/0';
     }
    //alert(rate);
     expl=rate.split('/');
   // alert(expl[0]);
     mrate=expl[0];
    $(this).closest('tr').find('.rate').val(mrate); 
    });     
    }
   if ($("#wsr").is(":checked")) {
    //alert('ss1');
      $(".hrate").each(function(k,v){
    rate1=$(this).closest('tr').find('.hrate').val(); 
    $(this).closest('tr').find('.rate').val(rate1);
    }); 
    }
    });
 </script>     
