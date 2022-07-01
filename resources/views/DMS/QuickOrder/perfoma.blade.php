@extends('layouts.core_php_heade')

@section('dms_body')

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
<style>
table, th, td {
 
}
</style>
<style media="print">
 @page {
  size: auto;
  margin: 0;
       }



</style>


    <div class="main-content" style="   ">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781; color: black;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">{{Lang::get('common.order_details_dms')}} </a>
                    </li>

                    <li class="active" style="color: black;">{{Lang::get('common.order_history')}}</li>
                    <li class="active" style="color: black;">{{Lang::get('common.pro_forma_invoice')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content"  style=" font-family: 'Open Sans' ">
                
                <br> 
                <?php
                $party_name = !empty($party_name)?$party_name:"NA";
                $order_date = !empty($order_date)?date('d-M-Y',strtotime($order_date)):strtoupper(date('d-M-Y'));
                $order_id = !empty($order_id)?$order_id:0;
                $sum_rs_value = array();
                $sum_bx_value = array();
                $sum_pc_value = array();
                $gt_rs_value = array();
                $sum_trade_incentive = array();
                $sum_at_discount = array();
                $sum_trade_incentive_1 = 0;
                $sum_at_discount_1 = 0;
                $order_remark = !empty($order_remark)?$order_remark:"NA";
                ?>
                <div class="row container-fluid" style="text-align: right;">
                    <div class="col-xs-12">
                        <input type="button"  onclick="printDiv('print-content')" value="Print!" class=" btn-lg" />
                    </div>
                </div>
                <div class="row container-fluid" id="print-content">


                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" >
                                
                                <table id="table" class="table table-bordered " style="background-color: #f8f8f8; " >
                                    <thead >
                                    <tr>
                                        <th colspan="10" style="background-color: white;color: black;text-align: center; font-size: 30px; font-weight:100px; ">
                                            <b style="background-color: ; color: black; font-family: 'Open Sans'"> <u>PROFORMA INVOICE</u> </b><br><span style="font-size: 15px;">"The Goods will be supplied subject to stock availability at the time of actual dispatch. Hence, Incentive/trade schemes if applicable and if due, will be given on the basis of final invoices only."</span><br><span style="font-size: 15px;"></span><span style="font-size: 18px; font-weight: bold;">Party Name: {{$party_name}}</span><br><span style="font-size: 15px;">Order Date: {{$order_date}}</span><br><span style="font-size: 18px; font-weight: bold;">Order ID: {{$order_id}}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; " class="center" >
                                            {{Lang::get('common.s_no')}}
                                        </th>
                                        <th style="width:400px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">{{Lang::get('common.item_name')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;  ">{{Lang::get('common.wholesale_rate')}}</th>
                                        <th style="width: 100px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.sale_in_box_pcs')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.qty')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.pcs')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.billed_qty')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.free_qty')}}</th>
                                        <th style="width: 100px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.value')}}</th>
                                        <th style="width: 400px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.remark')}}</th>
                                    </tr>
                                    </thead>
                                    @foreach($mktg_cat_array as $k => $v)
                                        @foreach($final_out as $fink => $finv)
                                            @if(!empty($finv[$k]) &&  COUNT($finv[$k])>0)
                            
                                                <tr>
                                                    <td colspan="10" align="left" style="text-align: left; background-color: #237a00c4;color: black; height: 10px; " ><b style="font-weight: bolder; font-size: 15px;">{{$v}}</b></td>
                                                </tr>
                                                
                                                <tbody style=" border: 1px solid black;">
                                                @foreach($finv[$k] as $set => $set_v)
                                                    <?php
                                                        
                                                        $a = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $b = !empty($item_aum_mast_array[$set_v['ITEM_CODE']])?$item_aum_mast_array[$set_v['ITEM_CODE']]:0;  
                                                        if ($b == 0) {
                                                            $sum_bx_value[] = 0;
                                                        }
                                                        else{
                                                            $sum_bx_value[] = round(($a / $b),2);
                                                        }
                                                        $sum_pc_value[] = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $gt_rs_value[] = !empty($set_v['total_rs'])?$set_v['total_rs']:0;
                                                        $sum_trade_incentive[] = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $sum_at_discount[] = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;

                                                        $minus_trade_rate = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $minus_atd_rate = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;
                                                        
                                                        $sum_rs_value[] = ($set_v['total_rs']-$minus_trade_rate-$minus_atd_rate);
                                                     ?>
                                                 <tr>
                                                     <td >{{$set+1}}</td>
                                                     <td style="text-align: left;">{{$set_v['ITEM_NAME']}}</td>
                                                     <td >{{$set_v['rate']}}</td>
                                                     <td style="text-align: left;">{{$set_v['order_unit']}}</td>
                                                    @if($set_v['order_unit'] == 'BOX')
                                                     <td >{{$set_v['quantity']/$converstion_unit_item_code[$set_v['ITEM_CODE']]}}</td>
                                                     @else
                                                     <td >{{$set_v['quantity']}}</td>

                                                     @endif
                                                     <td >{{$set_v['quantity']}}</td>
                                                     <?php
                                                     $billed_qty = $set_v['quantity'] - $set_v['free_qty'];
                                                     ?>
                                                     <td >{{$billed_qty}}</td>
                                                     <td >{{$set_v['free_qty']}}</td>
                                                     <td style="text-align: right;">{{number_format(round($set_v['total_rs'],2),2)}}</td>
                                                     <td style="text-align: left; padding-left: 20px;">{{$set_v['remarks']}}</td>
                                                 </tr>
                                                 @endforeach   
                                                
                                                <?php
                                                    $sum_rs_value_1 = array_sum($sum_rs_value);
                                                    $sum_bx_value_1 = array_sum($sum_bx_value);
                                                    $sum_pc_value_1 = array_sum($sum_pc_value);
                                                    $sum_trade_incentive_1 = array_sum($sum_trade_incentive);
                                                    $sum_at_discount_1 = array_sum($sum_at_discount);
                                                ?>
                                                <tr>
                                                    <td colspan="10" style="font-size: 17px;">
                                                    <span id="final_bx_value" >Total Pieces : {{$sum_pc_value_1}}</span>
                                                    <span id="final_amont_value" style="padding-left: 20px; padding-right: 20px;">Total Boxes : {{$sum_bx_value_1}}</span>
                                                    <span id="final_gt_value" >Net Sale (Approx) : {{number_format($sum_rs_value_1,2)}}</span>
                                                    </td>
                                                </tr>
                                                    <?php
                                                        $sum_rs_value = array();
                                                        $sum_bx_value = array();
                                                        $sum_pc_value = array();
                                                    ?>                                            
                                            @endif
                                        @endforeach
                                    @endforeach
                                        <tr class="remove_border">
                                            <td colspan="5" class="remove_border"></td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; text-align: left;" class="remove_border">
                                                <?php
                                                $gt_rs_value = array_sum($gt_rs_value);
                                                ?>
                                                <b style="text-align: right;">Grand Total Value : </b>
                                            </td>
                                            <td style="text-align: right;" class="remove_border"><span style="text-align: right;" >{{number_format($gt_rs_value,2)}}</span></td>
                                            <td class="remove_border"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" rowspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Discount Details</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Trade Incentive (-) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{$trade_incentive = number_format(round($sum_trade_incentive_1,2),2)}}</span></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>AT Discount (-) : <span></span></b>
                                            </td>
                                            <td align="right" style="text-align: right;"><span style="text-align: right;">{{$at_discount = number_format(round($sum_at_discount_1,2),2)}}</span></td>
                                            <td></td>

                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                // dd($trade_incentive);
                                                $trade_incentive = $sum_trade_incentive_1;
                                                $at_discount = $sum_at_discount_1;
                                                $total_discount =($sum_trade_incentive_1) + ($sum_at_discount_1);
                                                // $total_discount =round($sum_trade_incentive_1,2) + round($sum_at_discount_1,2);
                                                ?>
                                                <b>Total Discount (-) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($sum_trade_incentive_1,2) + round($sum_at_discount_1,2),2)}}</span></td>
                                            <td></td>

                                        </tr>
                                        <tr>
                                            <td  colspan="5" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Order Value to be consider for Incentive</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                $total_net_order_value = $gt_rs_value - $total_discount;
                                                ?>
                                                <b>Total Net Order Value : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($total_net_order_value,2),2)}}</span></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td  colspan="5" rowspan="2" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Note: Order Amount/Scheme applied may change at the time of final order processing.</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Total Tax : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($sum_at_discount_1,2),2)}}</span></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                $total_amount_appx = $total_net_order_value + ($sum_at_discount_1);
                                                ?>
                                                <b>Total Amount (Approx) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($total_amount_appx),2)}}</span></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" align="left" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Order Remark : <span>{{$order_remark}}</span> </b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
















<!--  -->
    </div>
</div>
    
</body>

<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('msell/js/common.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
   
    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>

<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;

        var divToPrint = document.getElementById('table');
        var htmlToPrint = '' +
            '<style type="text/css">' +
           

            'table {'+
                'border:solid #000 ;'+
                'border-width:1px 0 0 1px ;'+
            '}'+
            'th, td {'+
                'border:solid #000 ;'+
                'border-width:0 1px 1px 0 ;'+
            '}'+
                
            '.remove_border{'+
                'table {'+
                'border: ;'+
                'border-width:0px 0 0 0px ;'+
                '}'+
                'th, td {'+
                    'border: ;'+
                    'border-width:0 0px 0px 0 ;'+
                '}'+
            
            '}'+
            '</style>';
        htmlToPrint += divToPrint.outerHTML;
        newWin = window.open("");
        newWin.document.write(htmlToPrint);
        newWin.print();
        newWin.close();
        // w=window.open();
        // w.document.write(printContents);
        // w.print();
        // w.close();
    }

</script>
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
                            null,null, null, null, 
                            
                            {"bSortable": false}
                        ],
                        "aaSorting": [],
                        "sScrollY": "1000px",
                        //"bPaginate": false,

                        "sScrollX": "100%",
                        "sScrollXInner": "120%",
                        "bScrollCollapse": true,
                        "iDisplayLength": 10000,


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
                    if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);


            myTable.on('select', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on('deselect', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                }
            });


            /////////////////////////////////
            //table checkboxes
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

            //select/deselect all rows according to table header checkbox
            $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $('#dynamic-table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
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
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if ($row.is('.detail-row ')) return;
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
            });


            /****/
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

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }


            /***/
            // $('.show-details-btn').on('click', function (e) {
            //     e.preventDefault();
            //     $(this).closest('tr').next().toggleClass('open');
            //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
            // });
            /***/


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
  // /***/
    // $('.show-details-btn').on('click', function (e) {
    //     e.preventDefault();
    //     $(this).closest('tr').next().toggleClass('open');
    //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    // });
    /***/
        
    </script>


<script>
    $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        //show datepicker when clicking on the icon
        .next().on(ace.click_event, function(){
            $(this).prev().focus();
        });

        //or change it into a date range picker
        $('.input-daterange').datepicker({autoclose:true});


        //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $('input[name=date_range_picker]').daterangepicker({
            'applyClass' : 'btn-sm btn-success',
            'cancelClass' : 'btn-sm btn-default',
                showDropdowns: true,
            // showWeekNumbers: true,             
            minDate: '2017-01-01',
            maxDate: moment().add(2, 'years').format('YYYY-01-01'),
            locale: {
                format: 'YYYY/MM/DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
            },
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    dateLimit: {
                                        "month": 1
                                    },

        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        }); 
    </script>
@endsection