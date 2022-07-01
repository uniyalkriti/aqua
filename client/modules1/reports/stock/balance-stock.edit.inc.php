<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
//include'../client/modules/table.php';
$forma = 'Balance Stock'; // to indicate what type of form this is
$formaction = $p;
$myobj = new report();
$cls_func_str = 'balance_stock'; //The name of the function in the class that will do the job
$myorderby = 'ORDER BY invdate ASC'; // The orderby clause for fetching of the data
$myfilter = 'invoiceId = '; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$d_id =  $_SESSION[SESS.'data']['dealer_id'];
//pre($_SESSION);
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
?>
<div id="breadcumb"><a href="#">Report</a> &raquo; <a href="#">Stock</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
    <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/billing.php');  ?>
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


############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_balance_stock_report';
$mymatch['datepref'] = array('invdate' => 'Invoice Date', 'created' => 'Created');
if (isset($_POST['filter']) && $_POST['filter'] == 'Filter') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $filter = array();
            $filterstr = array();
            if(!empty($_POST['product_name'])){
				$filter[] = "product_name LIKE '%$_POST[product_name]%'";
				$filterstr[] = '<b>Product Name  : </b>'.$_POST['product_name'];
			}
           // $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            //pre($filter);		
            $myresult = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY product_name ASC",$ch_filter); // $myobj->get_item_category_list()
            
            //pre($myresult);
            //echo $funcname;
            if (empty($myresult))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    $myresult = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} else {
   // $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
  $myresult = $myobj->$funcname($filter, $records = '', $orderby = '',$ch_filter);
  //get_balance_stock_report

  //pre($myresult);
  // die;
}


if (isset($_POST['Update']) && $_POST['Update'] == 'Update') { 
    
    $pid = $_POST['p_id_pk'];// pid
    $mrp = $_POST['mrp'];// mrp
    $rate = $_POST['rate'];// rate
    $bstock = $_POST['balance_stock'];// balance_stock
    $nstock = $_POST['non_salable_stock'];// non_salable_stock
    $dealer_rate = $_POST['dealer_rate'];// dealer_rate
    $r_rate = $_POST['retailer_rate'];
    $cmrp = $_POST['current_mrp'];// current_mrp
    $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
    $user_id = $_SESSION[SESS . 'data']['id'];
    $csa_id = $_SESSION[SESS . 'data']['csa_id'];
    $company_id=$_SESSION[SESS . 'data']['company_id'];
    
    foreach($pid as $i=>$val)
    {
        if($mrp[$i]==$cmrp[$i] || $cmrp[$i]==0)
        {
            $stock_q = "SELECT product_id FROM `stock` WHERE `product_id`='$pid[$i]' AND `dealer_id`='$dealer_id'";
            $stock_e = mysqli_query($dbc, $stock_q);
            $stock   = mysqli_num_rows($stock_e);

            if($stock>0)
            {
              $mrp_chunk = ($cmrp[$i]>0) ? "AND `mrp`='$cmrp[$i]'" : '';

              $up_stock="UPDATE `stock` SET `rate`='$r_rate[$i]',`dealer_rate`='$rate[$i]',`mrp`='$mrp[$i]',`qty`='$bstock[$i]',`nonsalable_damage`='$nstock[$i]',`date`=NOW() WHERE `product_id`='$pid[$i]' AND `dealer_id`='$dealer_id' $mrp_chunk";
              
            }else{
               $mfgdate = date('Y-m-d');
               $expdate = date('Y-m-d', strtotime('+1 years'));
               
               $nss = ($nstock[$i])?$nstock[$i]:0;

               $up_stock="INSERT INTO `stock` (`product_id`, `rate`, `dealer_rate`, `mrp`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`) VALUES ('$pid[$i]','$r_rate[$i]','$rate[$i]','$mrp[$i]','$csa_id','$dealer_id','$bstock[$i]','0','$nss','0','$mfgdate','$expdate',NOW(),'0','$company_id','1')";
            }
            
           $run_up_stock = mysqli_query($dbc, $up_stock); 
        }else{

          $mfgdate = date('Y-m-d');
          $expdate = date('Y-m-d', strtotime('+1 years'));
          
          $nss = ($nstock[$i])?$nstock[$i]:0;
          $qry="INSERT INTO `stock` (`product_id`, `rate`,`dealer_rate`, `mrp`,`csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`) VALUES ('$pid[$i]','$r_rate[$i]','$rate[$i]','$mrp[$i]','$csa_id','$dealer_id','$bstock[$i]','0','$nss','0','$mfgdate','$expdate',NOW(),'0','$company_id','1')";
          $new_mrp_e = mysqli_query($dbc, $qry);
        }
    } 
    header('Location: index.php?option=balance-stock'); 

    }


dynamic_js_enhancement();
?>
<form method="POST" action=""  name="genform">
<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                 <?php
                 $stock =0;
                 $value_sd = 0;
                 $cat_value = array();
                // $value_sale = 0;
                 $rupee='<img src="./images/rupee.png">';
                 foreach ($myresult as $key => $rows)
                 {
                          
                   $product[]=$rows['pid'];
                   $product_id = implode($product,",");
                   $stock =  $stock + ($rows['qty']);

                   $value_sd = $value_sd+my2digit(($rows['qty'])* $rows['dealer_rate']);
                  
                   $value_stock = '₹ ' .$value_sd;
                   $cat = $rows['c1_id'];
                   $value_sale[$cat][] = my2digit(($rows['qty'])* $rows['dealer_rate']);
                 }
                 
                 ?>
               
                <div class="table-header">
                   Balance Stock List &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="Update" value="Update" class="btn btn-success"/>
                    <div class="pull-right tableTools-container"></div>
                </div>

                <!-- div.table-responsive -->
                
                  <!-- div.dataTables_borderWrap -->
                <div>
                    <div class="row">
                        <div class="col-md-2">
                            
                        </div>
                         <div class="col-md-1" style="background-color:#AD0606; color:#fff;">
                            <b>Blends</b>
                        </div>
                         <div class="col-md-1" style="background-color:#F67C0F; color:#000;">
                            <b>Sprinkler</b>
                        </div>
                        
                          <div class="col-md-2" style="background-color:#46154D; color:#fff;">
                             <b>Straight Premium</b>
                        </div>
                         <div class="col-md-2" style="background-color:#0A87B2; color:#fff;">
                            <b>Straight</b>
                        </div>
                        
                         <div class="col-md-1" style="background-color:#D2DC13; color:#000;">
                        <b>Hing</b>
                        </div>
                         <div class="col-md-1" style="background-color:#4D59F0; color:#fff;">
                           <b> Whole</b>
                        </div>
                          <!-- <div class="col-md-1" style="background-color:#4D051D; color:#fff;">
                           <b> Mini Master</b>
                        </div> -->
                         <div class="col-md-2">
                        </div>
                        
                    </div> 
                    <div class="row">
                     <div class="col-md-2"></div>
                     <div class="col-md-1" style="background-color:#AD0606; color:#fff;">
                      <!-- For Blends -->

                      <b><?='₹ '.array_sum($value_sale['120150507011222'])?></b>
                    </div>
                    <div class="col-md-1" style="background-color:#F67C0F; color:#000;">
                     
                     <!-- For sprinkler -->
                     <b><?='₹ '.array_sum($value_sale['120150507011140'])?></b>
                   </div>

                   <div class="col-md-2" style="background-color:#46154D; color:#fff;">
                    
                     <!-- For Straight Premium -->
                     <b><?='₹ '.array_sum($value_sale['120150507011152'])?></b>
                   </div>
                   <div class="col-md-2" style="background-color:#0A87B2; color:#fff;">
                    
                    <!-- For Specialities Spices -->
                    <b><?='₹ '.array_sum($value_sale['120150507011211'])?></b>
                  </div>

                  <div class="col-md-1" style="background-color:#D2DC13; color:#000;">

                   <!-- For Hing -->
                   <b><?='₹ '.array_sum($value_sale['120150507011301'])?></b>
                 </div>
                 <div class="col-md-1" style="background-color:#4D59F0; color:#fff;">        

                   <!-- For Whole -->
                   <b><?='₹ '.array_sum($value_sale['120150622100550'])?></b>
                 </div>
                   <!-- <div class="col-md-1" style="background-color:#4D051D; color:#fff;">

                     <b><?='₹ '.array_sum($value_sale['Mini_Master'])?></b>
                   </div> -->
                 <div class="col-md-2">
                 </div>

               </div>  
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr style="background-color:#AD0606; color:#fff;">
                         <th style="text-align: center; background-color:#000; color:#fff;" colspan="3"><strong>Product Details</th>
                           <th style="text-align: center; background-color:#000; color:#fff;" colspan="5"><strong> Stock Details</th>
                           </tr>
                           <tr style="background-color:<?=$color?>; color:<?=$font?>">
                            <th style="background-color:#307ECC; color:#fff;">S.No</th>
                            <th style="background-color:#307ECC; color:#fff;">Product Name</th>
                            <th style="background-color:#307ECC; color:#fff;">Product Category</th>
                            <th style="background-color:#307ECC; color:#fff;">MRP</th>
                            <th style="background-color:#307ECC; color:#fff;">Rate</th>
                            <th style="background-color:#307ECC; color:#fff;">Salable Stock Exclude Free/Scheme Qty</th>
                            <th style="background-color:#307ECC; color:#fff;">Non Salable Stock</th>
                            <th style="background-color:#307ECC; color:#fff;">Stock Value</th>
                          </tr>
                        </thead>
                        
                        <tbody>
                          <?php
                          $inc = 1;
                          $rupee='<img src="./images/rupee.png">';
                          foreach ($myresult as $key => $rows)
                          {
                            $product[]=$rows['pid'];
                            $product_id = implode($product,",");

                            $in=$rows['pid']; 
                            if($rows['cat']=='Sprinklers')
                            {
                             $color = "#F67C0F";
                             $font = "#000";
                           }
                           else if($rows['cat']=='Straight Premium')
                           {
                             $color = "#46154D";
                             $font = "#FFF";
                           }
                           else if($rows['cat']=='Specialities Spices')
                           {
                             $color = "#0A87B2";
                             $font = "#000";
                           }
                           else if($rows['cat']=='Blends')
                           {
                             $color = "#AD0606";
                             $font = "#fff";
                           }
                           else if($rows['cat']=='Hing')
                           {
                             $color = "#D2DC13";
                             $font = "#000";
                           }
                           else if($rows['cat']=='Whole')
                           {
                             $color = "#4D59F0";
                             $font = "#fff";
                           }
                           else if($rows['cat']=='Focus')
                           {
                             $color = ""; 
                             $font = "#000";
                           } 


                           if($rows['mrp'])
                           {
                              $mrp = $rows['mrp'];
                              $r_rate = $rows['rate'];
                              $d_rate = $rows['dealer_rate'];
                           }else{
                                 $mrp = $rows['product_mrp'];

                                 if($rows['product_gst'])
                                 {
                                   $r_rate = $mrp-($mrp*25/100);
                                   $d_rate = $r_rate-($r_rate*7.33/100);
                                 }else{
                                  $r_rate = $mrp-($mrp*18/100);
                                  $d_rate = $r_rate-($r_rate*7/100);
                                 }
                           } 


                           ?>
                           <tr style="background-color:<?=$color?>; color:<?=$font?>">
                            <td>
                             <?=$inc?>
                             <input type="hidden" name="p_id_pk[]" value="<?php echo (int)$rows['pid']?>"/>
                           </td>
                           <td>
                            <strong><?=$rows['product_name']?><?php  echo' <span style="float:right;"><image src="images/3D packs/'.$rows['pid'].'.png" height="42" width="42"/></span>'; ?>
                            </td>
                           <td><b><?=($rows['cat'])?></b></td>
                            <td>
                                <strong><input type="text" onkeypress="javascript:return isNumber(event)" name="mrp[]" value="<?=number_format($mrp,2)?>" class="mrp"/>
                                <input type="hidden" class="pid" value="<?php echo $rows['pid']?>">
                                <input type="hidden" name="current_mrp[]" value="<?php echo number_format($mrp,2)?>">
                                <input type="hidden" name="dealer_rate[]" value="<?php echo $d_rate?>">
                                <input type="hidden" name="retailer_rate[]" class="retailer_rate" value="<?php echo $r_rate?>">
                            </td>
                           <td><strong><input type="text" onkeypress="javascript:return isNumber(event)" class="rate" name="rate[]" value="<?=number_format($d_rate,2)?>"/ readonly=""></td>
                            <td><strong><input type="text" onkeypress="javascript:return isNumber(event)" class="balance_stock" name="balance_stock[]" value="<?=($rows['qty'])?>"/></td>
                            <td><strong><input type="text" onkeypress="javascript:return isNumber(event)" name="non_salable_stock[]" value="<?=$rows['nonsale']?>"/></strong></td>
                            <td class="stk_val"><?='₹ ' .my2digit(($rows['qty'])* $rows['dealer_rate']) ?></td>
                              </tr>

                              <?php 
                              $inc++;

                            }

                            $cp = "SELECT id,name from catalog_product INNER JOIN catalog_view ON catalog_view.product_id=catalog_product.id Where id NOT IN ($product_id) ORDER BY catalog_view.c1_id";
                            $c = mysqli_query($dbc,$cp);

                            while($rowc = mysqli_fetch_assoc($c)) {
                             $cat = $myobj->category($rowc['id']); 
                             $rate = $myobj->product_rate($rowc['id']); 
                             if($cat=='Sprinklers')
                             {
                               $color = "#F67C0F";
                               $font = "#000";
                             }
                             else if($cat=='Straight Premium')
                             {
                               $color = "#46154D";
                               $font = "#FFF";
                             }
                             else if($cat=='Specialties Spices')
                             {
                               $color = "#0A87B2";
                               $font = "#000";
                             }
                             else if($cat=='Blends')
                             {
                               $color = "#AD0606";
                               $font = "#fff";
                             }
                             else if($cat=='Hing')
                             {
                               $color = "#D2DC13";
                               $font = "#000";
                             }
                             else if($cat=='Whole')
                             {
                               $color = "#4D59F0";
                               $font = "#fff";
                             }
                             else if($cat=='Focus')
                             {
                               $color = ""; 
                               $font = "#000";
                             }
                             ?>
                             <tr style="background-color:<?=$color?>; color:<?=$font?>">
                              <td>
                                <input type="hidden" name="pid<?php echo $inc;?>" value="<?=$rowc['id']?>"/>
                                <?=$inc?>
                              </td>
                              
                              <td>
                               <strong>  <?=$rowc['name']?><?php echo'<span style="float:right;"><image src="images/3D packs/'.$rowc['id'].'.png" height="42" width="42"/></span>'; ?>
                               </td>
                               <td><b><?=$cat?></b></td>
                               <td class="hidden">0</td>

                               <td><strong><input type="text"  name="rate<?php echo $inc;?>"  value="<?=$rate?>"/></strong></td>
                               <td> <strong><input type="text" onkeypress="javascript:return isNumber(event)" name="balance_stock<?php echo $inc;?>"  value="0"/></strong></td>
                               <td><strong><input type="text" onkeypress="javascript:return isNumber(event)" name="non_salable_stock<?php echo $inc;?>"  value="<?=$rows['non_salable_stock']?>"/></strong></td>

                               <td>₹ 0.00</td>
                             </tr>

                             <?php $inc++;
                           }                        


                           ?>
                           <input type="hidden"  name="inc"  value="<?=$inc?>"/>
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

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div><!-- /.main-container -->       
</form> 
     
    </body>
</html>
<script>
    // WRITE THE VALIDATION SCRIPT IN THE HEAD TAG.
    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }    
</script>
<script src="assets/js/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
  /* Auto calculation of dealer rate */
  $('#dynamic-table').on('change','.mrp',function(){
    var mrp = $(this).val();
    var id = $(this).siblings('.pid').val();
    var rateTx   = $(this).parent().parent().parent().find('.rate');
    var av_stock = $(this).parent().parent().parent().find('.balance_stock');
    var stk_val  = $(this).parent().parent().parent().find('.stk_val');
    var r_rate = $(this).siblings('.retailer_rate');
    var mrp_dec = parseFloat(mrp).toFixed(2);

    $.ajax({
        type:'POST',
        url:'js/ajax_general/ajax_general_php.php',
        data:{pid:id,wcase:'get_product_gst'},
        success:function(data){
          var resp = data.split('<$>');
          if(parseFloat(resp[1]))
          {
            var rate = mrp-(mrp*25)/100;
            var d_rate = rate-(rate*7.33/100);
          }else{
            var rate = mrp-(mrp*18)/100;
            var d_rate = rate-(rate*7/100);
          }

          // alert(rateTx.val();
          // stk_val.html(av_stock.val()*rateTx.val());
          r_rate.val(rate);
          rateTx.val(d_rate.toFixed(2));
          stk_val.html('₹ ' +(rateTx.val()*av_stock.val()).toFixed(2));

          $(this).val(mrp_dec);
        }
    })
  })

  $('#dynamic-table').on('change','.balance_stock',function(){

        var rateTx   = $(this).parent().parent().parent().find('.rate');
        var stk_val  = $(this).parent().parent().parent().find('.stk_val');
        stk_val.html('₹ ' +(rateTx.val()*$(this).val()).toFixed(2));
  })

</script>
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
    null, null, null, null, null,null,null,
    {"bSortable": false}
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
})
</script>
   