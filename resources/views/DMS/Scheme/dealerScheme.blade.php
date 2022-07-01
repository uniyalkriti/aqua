<?php 

// include('../client/include/menu-by-role/copy-admin.inc.php');

?>
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

<style type="text/css">
    tbody tr:nth-child(odd){
      background-color: #e6ffcc;
      color: black;
    }
</style>
<div class="main-content" >
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781;">
            <ul class="breadcrumb">
                <li style="color: black;">
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a style="color: black;" href="#">{{Lang::get('common.order_details_dms')}} </a>
                </li>

                <li class="active" style="color: black;">{{Lang::get('common.order_history')}}</li>
            </ul><!-- /.breadcrumb -->
            <!-- /.nav-search -->
        </div>

        <div class="page-content" style="padding-top: 0;">
            <fieldset><legend style="text-align: center; color:black; font-weight: bolder;">Scheme Incentive Plan</legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <tbody>
                                    @if(!empty($scheme_catg_title_data))
                                        @foreach($scheme_catg_title_data as $catg_key => $catg_value)
                                            <tr>
                                                <th colspan="5">CIR NO:{{$catg_value->circular_no}}/DATE:{{date('y-M-d',strtotime($catg_value->circular_date))}}</th>
                                                <th colspan="5">PERIOD : FROM {{date('y-M-d',strtotime($catg_value->scheme_from_date))}} TO {{date('y-M-d',strtotime($catg_value->scheme_to_date))}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="10" class="scheme-catg" style="background-color: #90d781;">
                                                    @if($catg_value->mktg_catg == 'mainline')
                                                        Mainline<br><small>(GEN+GLD+CLA)</small>
                                                        @else
                                                        {{!empty($mktg_catg_return_array[$catg_value->mktg_catg])?$mktg_catg_return_array[$catg_value->mktg_catg]:''}}</th>

                                                    @endif
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th rowspan="3">Sale (BOX)</th>
                                                <th rowspan="3">From Date</th>
                                                <th rowspan="3">To Date</th>
                                                <th colspan="6">Scheme Apllicable Sale Criteria ({{$catg_value->sale_type}})</th>
                                                <th rowspan="3">Proposed order <br>for availing scheme incentive<br>(AMT/BOX/PCS)</th>
                                            </tr>
                                            
                                            <tr class="tr-bgcolor">
                                                <th rowspan="2">Target</th>
                                                <th colspan="2">TGT PLAN %</th>
                                                <th colspan="2">Approx. Purchase Slab</th>
                                                <th rowspan="2">INCENTIVE %</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th>From</th>
                                                <th>To</th>
                                                <th>From</th>
                                                <th>To</th>
                                            </tr>
                                            @if(!empty($out[$catg_value->mktg_catg]))
                                                @foreach($out[$catg_value->mktg_catg] as $catg_key => $catg_value)
                                                    <tr>
                                                        <td>{{$sale_value = !empty($mktg_catg_wise_sales[$catg_value->mktg_catg.$catg_value->id])?round($mktg_catg_wise_sales[$catg_value->mktg_catg.$catg_value->id],2):'0'}}</td>
                                                        <td>{{date('d/m/y,strtotime($catg_value->target_sale_from_date))}}</td>
                                                        <td>{{$catg_value->target_sale_to_date}}</td>
                                                        <td>{{$cal=!empty($mktg_target_out[$catg_value->mktg_catg])?$mktg_target_out[$catg_value->mktg_catg]:'0'}}</td>
                                                        <td>{{$step_from = $catg_value->base_from}}%</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$step_to = $catg_value->base_to}}%</td>
                                                        @endif
                                                        <td>{{$inc_step_from = round($cal*$step_from/100)}}</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$inc_step_to = round($cal*$step_to/100)}}</td>
                                                        @endif
                                                        <td>{{$catg_value->free_final_rate}}%</td>

                                                        @if( $sale_value > $inc_step_from)
                                                            <td>0</td>
                                                            @else
                                                            <td style="font-weight: bold;">{{round($inc_step_from - $sale_value)}}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif

                                    @if(!empty($prod_scheme_catg_title_data))
                                        @foreach($prod_scheme_catg_title_data as $catg_key => $catg_value)
                                            <tr>
                                                <th colspan="5">CIR NO:{{$catg_value->circular_no}}/DATE:{{date('y-M-d',strtotime($catg_value->circular_date))}}</th>
                                                <th colspan="5">PERIOD : FROM {{date('y-M-d',strtotime($catg_value->scheme_from_date))}} TO {{date('y-M-d',strtotime($catg_value->scheme_to_date))}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="10" class="scheme-catg" style="background-color: #90d781;">{{!empty($prod_catg_return_array[$catg_value->prod_catg])?$prod_catg_return_array[$catg_value->prod_catg]:''}}</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th rowspan="3">Sale (BOX)</th>
                                                <th rowspan="3">From Date</th>
                                                <th rowspan="3">To Date</th>
                                                <th colspan="6">Scheme Apllicable Sale Criteria ({{$catg_value->sale_type}})</th>
                                                <th rowspan="3">Proposed order <br>for availing scheme incentive<br>(AMT/BOX/PCS)</th>
                                            </tr>
                                            
                                            <tr class="tr-bgcolor">
                                                <th rowspan="2">Target</th>
                                                <th colspan="2">TGT PLAN %</th>
                                                <th colspan="2">Approx. Purchase Slab</th>
                                                <th rowspan="2">INCENTIVE %</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th>From</th>
                                                <th>To</th>
                                                <th>From</th>
                                                <th>To</th>
                                            </tr>
                                            @if(!empty($prod_out[$catg_value->prod_catg]))
                                                @foreach($prod_out[$catg_value->prod_catg] as $catg_key => $catg_value)

                                                    <tr>
                                                        <td>{{$sale_value = !empty($prod_catg_wise_sales[$catg_value->prod_catg.$catg_value->id])?round($prod_catg_wise_sales[$catg_value->prod_catg.$catg_value->id],2):'0'}}</td>
                                                        <td>{{date('d/m/y,strtotime($catg_value->target_sale_from_date))}}</td>
                                                        <td>{{$catg_value->target_sale_to_date}}</td>
                                                        <td>{{$cal=!empty($prod_target_out[$catg_value->prod_catg])?$prod_target_out[$catg_value->prod_catg]:'0'}}</td>
                                                        <td>{{$step_from = $catg_value->base_from}}%</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$step_to = $catg_value->base_to}}%</td>
                                                        @endif
                                                        <td>{{$inc_step_from = round($cal*$step_from/100)}}</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$inc_step_to = round($cal*$step_to/100)}}</td>
                                                        @endif
                                                        <td>{{$catg_value->free_final_rate}}%</td>

                                                        @if( $sale_value > $inc_step_from)
                                                            <td>0</td>
                                                            @else
                                                            <td style="font-weight: bold;">{{round($inc_step_from - $sale_value)}}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </fieldset>
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

    $('#dynamic-table').on('click','.removenewrow',function(){

          var table = $(this).closest('table');
          var i = table.find('.mytbody_demand_order1').length;                 

          if(i==1)
          {
             return false;
          }

         $(this).closest('tr').remove();
    });

    var cust_id = 11;
        // console.log(cust_id);

        function addfunction(str)
        {
            // var grand_total_rs ;

            var target_sale_from_date = `<input type="text" name="target_sale_from_date[]" id="target_sale_from_date${cust_id}">`;
            var target_sale_to_date = `<input type="text" name="target_sale_to_date[]" id="target_sale_to_date${cust_id}">`;
            var incentive_sale_from_date = `<input type="text" name="incentive_sale_from_date[]" id="incentive_sale_from_date${cust_id}">`;
            var incentive_sale_to_date = `<input type="text" name="incentive_sale_to_date[]" id="incentive_sale_to_date${cust_id}">`;
            var base_from = `<input type="text" name="base_from[]" id="base_from${cust_id}">`;
            var base_to = `<input type="text" name="base_to[]" id="base_to${cust_id}">`;
            var free_final_rate = `<input type="text" name="free_final_rate[]" id="free_final_rate${cust_id}">`;

           
            var template = ('<tr><td>'+target_sale_from_date+'</td><td>'+target_sale_to_date+'</td><td>'+incentive_sale_from_date+'</td><td>'+incentive_sale_to_date+'</td><td>'+base_from+'</td><td>'+base_to+'</td><td>'+free_final_rate+'</td><td width="30px" ><i id=sr_no'+cust_id+' title="more" class="fa fa-plus addrow" aria-hidden="true" onclick=" addfunction(this.id); chosenFunction();"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></tr>');
            $('.mytbody_demand_order').append(template);
            cust_id++;


            var total_rs_above = document.getElementsByName('total_rs[]');

            for (var po = 0; po < total_rs_above.length; po++)
            {
                grand_total_rs += parseFloat(total_rs_above[po].value);

            }   
            // alert(grand_total_rs);

            $('#final_amont_value').html('');
            $('#final_amont_value').html(grand_total_rs);
            // document.getElementById("total_rs").innerHTML= '';
            // document.getElementById("total_rs").innerHTML= grand_total_rs;
        }
    function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }
        jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": false},
                            null,null, null, null, null, null, 
                            
                            {"bSortable": false}
                        ],
                        "aaSorting": [],
                        "sScrollY": "1000px",
                        //"bPaginate": false,

                        // "sScrollX": "100%",
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
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });


            /////////////////////////////////
            //table checkboxes
            // $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

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

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }


            /***************/
            // $('.show-details-btn').on('click', function (e) {
            //     e.preventDefault();
            //     $(this).closest('tr').next().toggleClass('open');
            //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
            // });
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
  // /***************/
    // $('.show-details-btn').on('click', function (e) {
    //     e.preventDefault();
    //     $(this).closest('tr').next().toggleClass('open');
    //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    // });
    /***************/
        
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
    