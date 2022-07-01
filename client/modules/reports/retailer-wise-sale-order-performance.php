<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
//include'../client/modules/table.php';
$forma = 'Sales Performance '; // to indicate what type of form this is
$formaction = $p;
$myobj = new report();
$cls_func_str = 'sales_purchase'; //The name of the function in the class that will do the job
$myorderby = 'ORDER BY invdate ASC'; // The orderby clause for fetching of the data
$myfilter = 'invoiceId = '; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$dea_id=$_SESSION[SESS.'data']['dealer_id'];
$dname=$_SESSION[SESS.'data']['dealer_name'];
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

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_sales_purchase_list';
if (isset($_POST['filter']) && $_POST['filter'] == 'Filter') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
if ($checkpass) {
    // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
    magic_quotes_check($dbc, $check = true);
    $filter = array();
    $filterstr = array();
    if(!empty($_POST['from_date'])){
        $start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`date`,'".MYSQL_DATE_SEARCH."') >= '$start'";
        $filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
      }
      if(!empty($_POST['to_date'])){
        $end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`date`,'".MYSQL_DATE_SEARCH."') <= '$end'";
        $filterstr[] = '<b>End : </b>'.$_POST['to_date'];
                                 
      }
     $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');		
            $myresult = $myobj->$funcname($filter, $records = '', $orderby = "GROUP BY uso.user_id ASC",$ch_filter); // $myobj->get_item_category_list()
            //pre($myresult);
            if (empty($myresult))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    $myresult = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = 'GROUP BY uso.user_id ASC');
} else {
   // $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
  //  $month=date('Ym');
    //$filter[] = "DATE_FORMAT(`date`,'Ym') <= '$month'";
   // $myresult = $myobj->$funcname($filter, $records = '', $orderby = 'GROUP BY uso.user_id ASC',$ch_filter);
}

dynamic_js_enhancement();
?>
<form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
        <fieldset>
<!--               <legend class="legend">Search <?php echo $forma;?></legend>-->
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
              
               <div class="col-xs-2">From Date<br />
                    <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else  echo date('d/M/Y');?>" />
                </div>
               <div class="col-xs-2">To Date<br />
                     <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/M/Y');?>" />
                </div>                
                 <div class="col-xs-6">
                     <br/>
                  <input id="mysave" class="btn btn-sm btn-primary" type="submit" name="filter" value="Filter" />
                  <!--<input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->
<!--                  <input class="btn btn-success"  onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
               <!--<a class="iframef" target="_blanck" href="index.php?option=<?php echo $formaction; ?>&showmode=1&mode=1"> <input class="btn btn-success" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->           
                  
           </div>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
  <?php
    if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
     //if no content available present no need to show the bottom part
       
    ?>
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
                   <?=$forma?> List
                    <div class="pull-right tableTools-container"></div>
                    <input type="button" onclick="tableToExcel('dynamic-table', 'W3C Example Table')" value="Export to Excel" class="btn btn-success">
                     <input type="button" value="Print" class="btn btn-warning" onclick="PrintDiv();"> 
                </div>

                <!-- div.table-responsive -->
                
                  <!-- div.dataTables_borderWrap -->
                <div id="divToPrint">
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <?php 
                      $start1 = date("d/M/Y", strtotime($start));
                      $end1 = date("d/M/Y", strtotime($end));
                    ?>
                    <caption>
                    <h3><strong>MKT Catg Wise Retailer Sale Vs Order Report-<?=$dname?> From <?=$start1?> To <?=$end1?></strong></h3>
                    </caption>
                        <thead>
                           
                             <style> th {
                        background-color: #C7CDC8;
                         color:#000;
                                }</style>
                            <tr> 
                            <th>Retailer Name</th>
                            <th></th>
                            <th colspan="2">CTR-----ASV-Value</th>
                                <?php foreach ($myresult as $key => $rows) {
                                    if($rows['cat']=='JPS'){
                                      $rows['cat']='ETHICAL';
                                    }
                                    ?>
                                <th  style="text-align: center;"><?=$rows['cat']?></th> 
                                <?php } ?>
                                <th>BAID Total</th>
                                <th>Other Brand</th>
                                <th>Grand Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $outr=array(); 
                            $qr="SELECT r.id,CONCAT_WS(',',r.name,lv.l5_name) AS name FROM retailer AS r INNER JOIN challan_order AS co ON r.id=co.ch_retailer_id INNER JOIN challan_order_details AS cod ON cod.ch_id=co.id INNER JOIN location_view AS lv ON lv.l5_id=r.location_id WHERE co.ch_dealer_id='$dea_id' AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='$end'  GROUP BY r.id  ORDER BY name";
                            //h1($qr);
                            $rr=mysqli_query($dbc,$qr);
                            while($rowr=mysqli_fetch_assoc($rr)){
                                $rid=$rowr['id'];
                                $outr[$rid]=$rowr;
                            }
                            $qro="SELECT r.id,CONCAT_WS(',',r.name,lv.l5_name) AS name FROM retailer AS r INNER JOIN user_sales_order AS uso ON r.id=uso.retailer_id INNER JOIN user_sales_order_details AS usod ON uso.order_id=usod.order_id INNER JOIN location_view AS lv ON lv.l5_id=r.location_id  WHERE uso.dealer_id='$dea_id' AND DATE_FORMAT(`date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`date`,'%Y%m%d')<='$end' AND call_status=1  GROUP BY r.id ORDER BY name";
                            //h1($qro);
                            $rro=mysqli_query($dbc,$qro);
                            while($rowro=mysqli_fetch_assoc($rro)){
                                $roid=$rowro['id'];
                                $outr[$roid]=$rowro;
                            }
                         //   $outr=arsort($outr);
                            //pre($outr);exit;
                            ?>
                            <?php 
                                $asvc=0;
                                $asvv=0;
                                $baidm=0;
                                $obv=0;
                                $orderasvc=0;
                                $orderasvv=0;
                                $orderbaidm=0;
                                $obov=0;
                            foreach ($outr as $k => $v) { ?>
                            <tr>
                                 <th rowspan='2'><?=strtoupper($v['name'])?></th>
                                 <th>Sale</th>

                              <?php 
                              $total_sale_baid=0;
                              $total_sale=0;
                              $asvctr=$myobj->get_rbilled_stock_ctr('2',$k,$start,$end); 
                              $asvc+=$asvctr;
                              
                                ?>
                                 <td style="text-align: right;"><?=$asvctr?></td>
                                 <?php 
                                 $asvsale=$myobj->get_rbilled_stock('2',$k,$start,$end); 
                                  $total_sale_baid=$total_sale_baid+$asvsale;
                                  $asvv+=$asvsale; 
                                ?>
                                 <td style="text-align: right;"><?=my2digit($asvsale)?></td>
                                
                    <?php 
                    foreach ($myresult as $key => $rows) { 
                      $sale=$myobj->get_rbilled_stock($rows['pid'],$k,$start,$end);
                      if($rows['cat']=='CLA'){
                                $catv+=$sale; 
                      }elseif($rows['cat']=='FMCG'){
                                $fmcgv+=$sale; 
                      }elseif($rows['cat']=='GEN'){
                                $genv+=$sale; 
                      }elseif($rows['cat']=='GLD'){
                                $gldv+=$sale;  
                      }elseif($rows['cat']=='JPS'){
                                $jpsv+=$sale; 
                      }elseif($rows['cat']=='OT2'){
                                $ot2v+=$sale; 
                      }elseif($rows['cat']=='OTC'){
                                $otcv+=$sale; 
                      }elseif($rows['cat']=='OTH'){
                                $othv+=$sale; 
                      }
                      $gtotal_sale_baid=$asvv+$catv+$fmcgv+$genv+$gldv+$jpsv+$ot2v+$otcv+$othv;
                      $total_sale_baid+=$sale;
                      ?>  
                                <td style="text-align: right;"><?=my2digit($sale)?></td>
                               
                                <?php  }?> 
                                <td style="text-align: right;"><?=my2digit($total_sale_baid)?></td>
                                
                                <?php 
                                $obsale=$myobj->get_rbilled_stock('8',$k,$start,$end); 
                                $obv+=$obsale;
                                  $total_sale=$total_sale_baid+$obsale;
                                  $gtotal_sale+=$total_sale;
                               
                                ?>
                                 <td style="text-align: right;"><?=my2digit($obsale)?></td>
                                 <td style="text-align: right;"><?=my2digit($total_sale)?></td>
                                 
                            </tr> 
                            <tr bgcolor="yellow">
                            <th>Order</th>
                            <?php 
                            $total_order_baid=0;
                              $total_order=0;
                              $orderasvctr=$myobj->get_rorder_stock_ctr('2',$k,$start,$end); 
                              $orderasvc+=$orderasvctr;
                            ?>
                            <td style="text-align: right;"><?=$orderasvctr?></td>
                            
                            <?php
                            $asvorder=$myobj->get_rorder_stock('2',$k,$start,$end); 
                                  $total_order_baid=$total_order_baid+$asvorder;
                                  $orderasvv+=$asvorder;
                            ?>
                             <td style="text-align: right;"><?=my2digit($asvorder)?></td>
                              <?php 
                    foreach ($myresult as $key1 => $rows1) { 
                      $order=$myobj->get_rorder_stock($rows1['pid'],$k,$start,$end);
                      if($rows1['cat']=='CLA'){
                                $catov+=$order;
                      }elseif($rows1['cat']=='FMCG'){
                                $fmcgov+=$order;
                      }elseif($rows1['cat']=='GEN'){
                                $genov+=$order; 
                      }elseif($rows1['cat']=='GLD'){
                                $gldov+=$order; 
                      }elseif($rows1['cat']=='JPS'){
                                $jpsov+=$order; 
                      }elseif($rows1['cat']=='OT2'){
                                $ot2ov+=$order;
                      }elseif($rows1['cat']=='OTC'){
                                $otcov+=$order;
                      }elseif($rows1['cat']=='OTH'){
                                $othov+=$order;
                      }
                      $gtotal_order_baid=$orderasvv+$catov+$fmcgov+$genov+$gldov+$jpsov+$ot2ov+$otcov+$othov;
                      $total_order_baid+=$order;
                      ?>  
                       <td style="text-align: right;"><?=my2digit($order)?></td>
                        <?php } ?> 
                        <td style="text-align: right;"><?=my2digit($total_order_baid)?></td>
                        <?php 
                         $oborder=$myobj->get_rorder_stock('8',$k,$start,$end); 
                                $obov+=$oborder;
                                  $total_order=$total_order_baid+$oborder;
                                  $gtotal_order+=$total_order;
                        ?>
                        <td style="text-align: right;"><?=my2digit($oborder)?></td>
                                 <td style="text-align: right;"><?=my2digit($total_order)?></td>
                                 </tr>
                            <?php } ?>                    
                         </tbody>
                         <tr>
                        <th rowspan="2"><b>Grand Total</b></th>
                        <th>Sale</th>
                        <td style="text-align: right;"><b><?=$asvc?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($asvv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($catv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($genv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($gldv)?></b></td>
                         <td style="text-align: right;"><b><?=my2digit($otcv)?></b></td>
                         <td style="text-align: right;"><b><?=my2digit($ot2v)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($fmcgv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($othv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($jpsv)?></b></td> 
                      <td style="text-align: right;"><b><?=my2digit($gtotal_sale_baid)?></b></td>
                      <td style="text-align: right;"><b><?=my2digit($obv)?></b></td>
                       <td style="text-align: right;"><b><?=my2digit($gtotal_sale)?></b></td>
                         </tr>
                        <th>Order</th>
                        <td style="text-align: right;"><b><?=$orderasvc?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($orderasvv)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($catov)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($genov)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($gldov)?></b></td>
                         <td style="text-align: right;"><b><?=my2digit($otcov)?></b></td>
                         <td style="text-align: right;"><b><?=my2digit($ot2ov)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($fmcgov)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($othov)?></b></td>
                        <td style="text-align: right;"><b><?=my2digit($jpsov)?></b></td> 
                      <td style="text-align: right;"><b><?=my2digit($gtotal_order_baid)?></b></td>
                      <td style="text-align: right;"><b><?=my2digit($obov)?></b></td>
                       <td style="text-align: right;"><b><?=my2digit($gtotal_order)?></b></td>
                         </tr>
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
  
     
    </body>
</html>
      <script src="assets/js/jquery-2.1.4.min.js"></script>
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
    function PrintDiv() {    
       var divToPrint = document.getElementById('divToPrint');
       var popupWin = window.open('', '_blank', 'width=300,height=300');
       popupWin.document.open();
       var htmlToPrint = '' +
        '<style type="text/css">' +
        'table th, table td {' +
        'border:1px solid #000;' +
        'padding;0.5em;' +
        '}' +
        '</style>';
    htmlToPrint += divToPrint.innerHTML;
       popupWin.document.write('<html><body onload="window.print()">' + htmlToPrint + '</html>');
        popupWin.document.close();
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
                                                {"bSortable": false},
                                                <?php 
                                                foreach ($myresult as $key => $rows) {
                                                ?>
                                                null,null,
                                                <?php }
                                                 ?>
                                               // {"bSortable": false}
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
   
