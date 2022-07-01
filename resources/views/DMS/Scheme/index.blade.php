<?php 

// include('../client/include/menu-by-role/copy-admin.inc.php');

?>
@extends('layouts.adminMenuDms')

@section('body')

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
	<link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
	<link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
	<link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />


    <div class="main-content" >
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #438eb9;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">Scheme Details </a>
                    </li>

                    <li class="active" style="color: black;">Scheme Details</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content" style="padding-top: 0;">
                <form class="form-horizontal open collapse in" action="" method="get" id="user-search" role="form"
                                          enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <!-- <div class="row">
                        <div class="col-lg-12">
                        
                            <div class="col-lg-2 ">
                                <label class="control-label no-padding-right" for="name">Search By Scheme No</label>
                                <div class="input-group" style="cursor: pointer;">
                                    @if(empty(Request::get('order_no')))
                                     <input type="text" placeholder="Search by Scheme No" id="search"
                                               name="order_no" value="{{ Request::get('order_no') }}"
                                             class="form-control input-sm"/>
                                       <span onclick="search()" class="input-group-addon cursor">
                                           <i class="fa fa-search"></i>
                                     </span>
                                   @else
                                     <input type="text" readonly="readonly" placeholder="Search by Scheme No"
                                             id="search" name="order_no" value="{{ Request::get('order_no') }}"
                                               class="form-control input-sm"/>
                                      <span onclick="searchReset();" class="input-group-addon cursor">
                                           <i class="fa fa-times"></i>
                                       </span>
                                    @endif
                                </div>
                                {{-- <input type="text" id="myInput" class = "form-control" onkeyup="myFunction()" placeholder="Search for anything.." title="Type in a name"> --}}
                            </div>
                            <div class="col-lg-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.per_page')}}</label>
                                   <select name="perpage" id="perpage" class="form-control cursor input-sm "
                                           onchange="form.submit()">
                                        <option value="">{{Lang::get('common.per_page')}}</option>
                                        <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                        <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                        <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                        <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                        <option {{ Request::get('perpage')==500?'selected':'' }} value="500">500</option>
                                        <option {{ Request::get('perpage')==1000?'selected':'' }} value="1000">1000</option>
                                   </select>
                            </div>
                           	<div class="col-lg-2">
                                <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                       
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>

                                    <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                </div>
                            </div>
                            
                            <div class="col-lg-2">
                                <button type="submit" class="  btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                    {{Lang::get('common.find')}}
                                </button>
                            </div>
                           
                        </div>
                    </div> -->
                    
                </form>
                <br> 
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" >
                                <form action="send_order_to_erp" method="post">

                                <div class="table-header center" style="background-color: #438eb9; color: black; font-weight: bolder;">
                                    Scheme Details                                    
                                    <div class="pull-right tableTools-container">
                                            <a href="{{url($current_menu.'/create')}}" class="  form-control btn  btn-success" style=" vertical-align: top;">
                                                <i class="fa fa-plus mg-r-10" style="vertical-align: center;"></i> Add New Scheme 
                                            </a>
                                    </div>
                                   
                                </div>
                                    {!! csrf_field() !!}

                                    <table id="dynamic-table" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Circular No</th>
                                            <th>Circular Date</th>
                                            <th>Scheme No</th>
                                            <th>Scheme From Date</th>
                                            <th>Scheme To Date</th>
                                            <th>Scheme Des</th>
                                            
                                           
                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($scheme_catg_title_data))
                                                @foreach($scheme_catg_title_data as $key => $value)
                                                    <tr>
                                                        <td>{{$value->circular_no}}</td>
                                                        <td>{{$value->circular_date}}</td>
                                                        <td>{{$value->scheme_no}}</td>
                                                        <td>{{$value->scheme_from_date}}</td>
                                                        <td>{{$value->scheme_to_date}}</td>
                                                        <td>{{$value->scheme_des}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            
                                        </tfoot>
                                    </table>
                                </form>
                            </div><!-- /.span -->
                        </div><!-- /.row -->
                    	
                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->















<!--  -->
 

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
    