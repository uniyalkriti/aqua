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
function moneyFormatIndia($num1) {
	//$num1='';
  $explrestunits = "" ;
  $number = explode('.',$num1);
  $num = $number[0];
	
  if(strlen($num)>3) {
    $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
          if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
              } else {
                $explrestunits .= $expunit[$i].",";

              }

            }
       //  print_r($explrestunits);
       // exit;
            if($number[1]!= ''){
              $thecash = $explrestunits.$lastthree.".".$number[1];
            }
            else{
              $thecash = $explrestunits.$lastthree;
            }
          } else {
            $thecash = $num;
          }
  //  echo $thecash;
    return $thecash; // writes the final format where $currency is the currency symbol.
  }
############################# Code to handle the user search starts here ###############################
  $rs = array();
  $filterused = '';
  $funcname = 'get_balance_stock_report';
  $mymatch['datepref'] = array('invdate' => 'Invoice Date', 'created' => 'Created');

  if (isset($_POST['filter']) && $_POST['filter'] == 'Filter')
  {
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
      }
      elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank']))
      {
        $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
        $myresult = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
      }
      else{
       // $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
        $myresult = $myobj->$funcname($filter, $records = '', $orderby = '',$ch_filter); //get_balance_stock_report        
      }

      dynamic_js_enhancement();
      ?>


      <script>
        $(document).ready(function() {

         $('.image-popup-vertical-fit').magnificPopup({
          type: 'image',
          closeOnContentClick: true,
          mainClass: 'mfp-img-mobile',
          image: {
           verticalFit: true
         }

       });
     </script>
     <form action="index.php?option=balance-stock-edit" method="POST">
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
               Balance Stock List &nbsp;&nbsp; 
               <input type="submit" name="edit" value="Edit" class="btn btn-warning"/>
               <strong><span style="margin-left: 20%">Total Stock : <?=
               $stock?> &nbsp; Total Value : <?=$value_stock?></span></strong>
               <div class="pull-right tableTools-container"></div>
             </div>


             <div class="table-responsive" style="width:100%">
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
                <b>Specialities Spices</b>
              </div>

              <div class="col-md-1" style="background-color:#D2DC13; color:#000;">
                <b>Hing</b>
              </div>
              <div class="col-md-1" style="background-color:#4D59F0; color:#fff;">
               <b> Whole</b>
             </div>
             <div class="col-md-1" style="background-color:#4D051D; color:#fff;">
               <b> Mini Master</b>
             </div>
             <div class="col-md-2">
             </div>

           </div>           

           <div class="row">
            <div class="col-md-2">

            </div>
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
     <div class="col-md-1" style="background-color:#4D051D; color:#fff;">

      <!-- For Mini Master -->
             <b><?='₹ '.array_sum($value_sale['Mini_Master'])?></b>
     </div>
     <div class="col-md-2">
     </div>

   </div>         

   <table id="dynamic-table" class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
       <th style="text-align: center; background-color:#000; color:#fff;" colspan="3">Product Details</th>
       <th style="text-align: center; background-color:#000; color:#fff;" colspan="4" > Stock Details</th>
     </tr>
     <tr>       
      <th class="center" style="background-color:#307ECC; color:#fff;">S.No</th>
      <th style="background-color:#307ECC; color:#fff;">Product Name</th>
      <th style="background-color:#307ECC; color:#fff;">Product Category</th>
      <th class="hidden">Purchase Quantity</th>
      <th style="background-color:#307ECC; color:#fff;">Salable Stock</th>
      <th style="background-color:#307ECC; color:#fff;">Non Salable Stock</th>
      <th style="background-color:#307ECC; color:#fff;">Intransit Stock</th>
      <th style="background-color:#307ECC; color:#fff;">Stock Value</th>
    </tr>
  </thead>
  <tbody>

    <?php
    $totalqty = 0;
    $totalamt = 0;
    $totalnon = 0;
    $totalint = 0;
    $inc = 1;

    $rupee='<img src="./images/rupee.png">';
    foreach ($myresult as $key => $rows) {
      
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
       $font = "#FFF";
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

     ?>
  <style>/* padding-bottom and top for image */
     .mfp-no-margins img.mfp-img {
       padding: 0;
     }
     /* position of shadow behind the image */
     .mfp-no-margins .mfp-figure:after {
       top: 0;
       bottom: 0;
     }
     /* padding for main container */
     .mfp-no-margins .mfp-container {
       padding: 0;
     }

   </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script type = 'text/javascript'>
    $(document).ready(function() {

     $('.image-popup-vertical-fit').magnificPopup({
      type: 'image',
      closeOnContentClick: true,
      mainClass: 'mfp-img-mobile',
      image: {
       verticalFit: true
     }

   });
 </script>
 <tr style="background-color:<?=$color?>; color:<?=$font?>">
  <td class="center">
    <?=$inc?>
  </td>

  <td>
   <strong><?=$rows['product_name']?><?php         
   if(file_exists('images/3D packs/'.$rows['pid'].'.png'))
   {
     echo'<span style="float:right;"> <image src="images/3D packs/'.$rows['pid'].'.png" height="42" width="42"  /></span>'; 
   }
   ?></strong>


 </td>
 <td class="hidden"><?=($rows['purchase_stock'])?></td>
 <td><b><?=$rows['cat']?></b></td>

 <!-- <td><b><?=($rows['balance_stock']+$rows['salable_only']+$rows['manual'])?></b></td> -->
 <td><b><?=($rows['qty'])?></b></td>

 <!-- <td><b><?=($rows['non_salable_stock']+$rows['nonsale'])?></b></td> -->
 <td><b><?=($rows['nonsale'])?></b></td>

 <td><b><?=$rows['intransit']?></b></td>
 <td><b><?php 
// $amt = my2digit(($rows['balance_stock']+$rows['salable_only']+$rows['manual'])* $rows['rate']);
 $amt = my2digit(($rows['qty'])* $rows['dealer_rate']);

 echo'₹  ' .moneyFormatIndia($amt);?></b></td>
</tr>

<?php $inc++;
                          /*$totalqty = $totalqty+($rows['balance_stock']+$rows['salable_only']+$rows['manual']);
                          $totalamt = my2digit(($rows['balance_stock']+$rows['salable_only'])* $rows['rate']+$totalamt);*/
                          $totalqty = $totalqty+($rows['qty']);
                          $totalamt = my2digit(($rows['qty'])* $rows['rate']+$totalamt);
                          // $totalnon = $rows['non_salable_stock']+$totalnon;
                          $totalnon = $rows['non_salable_stock']+$totalnon;
                          $totalint = $totalint+$rows['intransit']; 
                        }
                        ?>
                        <tr style="background-color:#BBB5A9; color:#000;">
                         <td><strong>*</strong></td>
                         <td><strong>TOTAL STOCK </strong></td>
                         <td><strong>-</strong></td>
                         <td><strong><?=$totalqty?></strong></td>
                         <td><strong><?=$totalnon?></strong></td>
                         <td><strong><?=$totalint?></strong></td>
                         <td><strong><?='&#8377;  '.moneyFormatIndia($totalamt);?></strong></td>
                       </tr>
                       <?php
                       $cp = "SELECT id,name from catalog_product INNER JOIN catalog_view ON catalog_view.product_id=catalog_product.id Where id NOT IN ($product_id) ORDER BY catalog_view.c1_id";
                       $c = mysqli_query($dbc,$cp);
                       while($rowc = mysqli_fetch_assoc($c)) {
                         $cat = $myobj->category($rowc['id']); 
                         $intransit = $myobj->intransit_stock($rowc['id']); 
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
                            <?=$inc?>
                          </td>
                          <td>
                            <strong> <?=$rowc['name']?><?php    
                            if(file_exists('images/3D packs/'.$rows['pid'].'.png'))
                            {
                              echo'<span style="float:right;"> <image src="images/3D packs/'.$rows['pid'].'.png" height="42" width="42"/></span>'; 
                            }
                           ?></strong>
                         </td>
                         <td><b><?=$cat?></b></td>
                         <td class="hidden">0</td>
                         <td><strong>0</strong></td>
                         <td><strong>0</strong></td>
                         <td><b><?=$intransit?></b></td>
                         <td><strong>₹ 0.00</strong></td>
                       </tr>

                       <?php $inc++;
                     }
                          //  echo $product_id;

                     ?>

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

<script src="assets/js/jquery-2.1.4.min.js"></script>
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
