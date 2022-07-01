@extends('layouts.common_dashboard')

@section('title')
    <title>{{Lang::get('common.user_detail')}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <style>
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            background-color: #7BB0FF !important;
            color: black;
        }
    </style>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            @if(Auth::user()->role_id==1)
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('user')}}">{{Lang::get('common.user_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.travelling_expenses')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>
            @else
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('user_public_data')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('user_public_data')}}">{{Lang::get('common.user_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.travelling_expenses')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>
            @endif
            <div class="page-content">
                <div class="row">
                <form id="month_form" method="get">
                        <div class="col-sm-2">
                            <div class="center">
                                    <input data-date-format="YYYY-MM-DD" value="{{ !empty(Request::get('from_date'))?date('Y-m-d',strtotime(Request::get('from_date'))):date('Y-m-d') }}" type="text" class="form-control input-sm date-picker" name="from_date" id="from_date">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="center">
                                    <input data-date-format="YYYY-MM-DD" value="{{ !empty(Request::get('to_date'))?date('Y-m-d',strtotime(Request::get('to_date'))):date('Y-m-d') }}" type="text" class="form-control input-sm date-picker" name="to_date" id="to_date">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-sm btn-primary">{{Lang::get('common.find')}}</button>
                        </div>
                        </form>
                </div>
                <div class="row">
                    <div class="clearfix">
                        <div class="pull-right tableTools-container"></div>
                    </div>
                    <!-- table starts here -->
                    <table id="dynamic-table" class="table table-bordered table-hover" >
                    <thead>

                            <th rowspan="2">{{Lang::get('common.s_no')}}</th>
                            <th rowspan="2">{{Lang::get('common.date')}}</th>
                            <th rowspan="2">{{Lang::get('common.username')}}</th>
                            <th colspan="2">Departure From</th>
                            <th colspan="2">Arrival At</th>
                            <th rowspan="2">Class & Mode Of Travel</th>
                            <th rowspan="2">{{Lang::get('common.remarks')}}</th>
                            <th rowspan="2">Distance KM</th>
                            <th>FARE</th> 
                            <th>D.A.</th>
                            <th>HOTEL</th>
                            <th>POSTAGE</th>
                            <th>TELEPHONE EXPENSES</th>
                            <th>CONVEYANCE</th>
                            <th>MISC.</th>
                            <th>{{Lang::get('common.total')}}</th>
                        <tr>
                            <th>{{Lang::get('common.location6')}}</th>
                            <th>{{Lang::get('common.time')}}</th>
                            <th>{{Lang::get('common.location6')}}</th>
                            <th>{{Lang::get('common.time')}}</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>
                            <th>Rs.</th>    
                        </tr> 
                    </thead>
                            <tbody>
                                @if(!empty($records) && count($records)>0)
                                    @foreach($records as $key=>$record)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{!empty($record->travellingDate)?date('d-M-Y',strtotime($record->travellingDate)):'N/A'}}</td>
                                            <td>{{$record->user_name}}</td>
                                            <td>{{$record->aname}}</td>
                                            <td>{{$record->arrivalTime}}</td>
                                            <td>{{$record->dname}}</td>
                                            <td>{{$record->departureTime}}</td>
                                            <td>{{!empty($arr[$record->travelModeID])?$arr[$record->travelModeID]:'N/A'}}</td>
                                            <td>{{$record->remarks}}</td>
                                            <td>{{$record->distance}}</td>
                                            <td>{{$record->fare}}</td>
                                            <td>{{$record->da}}</td>
                                            <td>{{$record->hotel}}</td>
                                            <td>{{$record->postage}}</td>
                                            <td>{{$record->telephoneExpense }}</td>
                                            <td>{{$record->conveyance}}</td>
                                            <td>{{$record->misc}}</td>
                                            <td>{{round($record->total,2)}}</td>
                                        </tr>
                                    @endforeach
                               
                                @endif
                            </tbody>
                    </table>
                    <!-- table ends here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.select.min.js')}}"></script>
    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/pdfmake.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>
    <script src="{{asset('js/user.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script>
        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert('Done!');
                        window.setTimeout(function () {
                            location.reload()
                        }, 3000);
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function takeAction(module, action_id, tab, act) {

            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
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
                            null,null, null, null, null,null,null,null,null,null,null,null,null,null,null,null,
                            {"bSortable": false}
                        ],
                        "aaSorting": [],


                        //"bProcessing": true,
                        //"bServerSide": true,
                        //"sAjaxSource": "http://127.0.0.1/table.php"   ,

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
@endsection
