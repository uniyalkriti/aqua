@extends('layouts.master')

@section('title')
    <title>Division Master</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('body')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="{{url('csa')}}">Division {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active" style="color: white">Division</li>
                </ul><!-- /.breadcrumb -->
            </div> <!-- /.nav-search -->

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="clearfix">
                                <strong>{{Lang::get('common.search_by_name')}}</strong></br>
                                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for anything.." title="Type in a name">
                                    <span id="checkTrial">
                                    <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-info pull-right" style="margin-left: 5px">
                                        <i class="fa fa-plus mg-r-10"></i> Add Division
                                    </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12" style="overflow-x: scroll;">
                                 <div class="table-header center">
                                    Division
                                    <div class="pull-right tableTools-container"></div>
                                   
                                </div>
                                <table id="dynamic-table" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            {{Lang::get('common.s_no')}}
                                        </th>
                                        <th>Division Code</th>
                                        <th>Division Name</th>
                                        <th>Location</th>
                                        <th>Operator Name</th>
                                        <th>Sequence</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records as $key=>$data)
                                        <?php $encid = Crypt::encryptString($data->id);?>
                                        <tr>
                                            <td class="center">
                                                {{ 1 + $key }}
                                            </td>
                                            <td>{{$data->division_code}}</td>
                                            <td>{{$data->division_name}}</td>
                                            <td>{{$data->location}}</td>
                                            <td>{{$data->operator_name}}</td>
                                            <td>{{$data->sequence}}</td>

                                            <td class="hidden-480">
                                                @if($data->active_status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($data->active_status==2)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>

                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($data->active_status==1)
                                                        <button title="Inactive" class="btn btn-xs btn-warning"
                                                                onclick="confirmAction('division_master','division_master','{{$data->id}}','{{$active_status_table}}','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button title="Active" class="btn btn-xs btn-success"
                                                                onclick="confirmAction('division_master','division_master','{{$data->id}}','{{$active_status_table}}','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a title="Edit" class="btn btn-xs btn-info"
                                                       href="{{url($current_menu.'/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button title="Delete" class="btn btn-xs btn-danger"
                                                            onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$active_status_table}}','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button>


                                                </div>

                                                <div class="hidden-md hidden-lg">
                                                    <div class="inline pos-rel">
                                                        <button class="btn btn-minier btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" data-position="auto">
                                                            <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                        </button>

                                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">

                                                            @if($data->active_status==1)
                                                                <li>
                                                                    <a href="javascript:void(0)"
                                                                       onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$active_status_table}}','inactive');"
                                                                       class="tooltip-warning" data-rel="tooltip"
                                                                       title="Inactive">
																			<span class="orange">
																				<i class="ace-icon fa fa-ban bigger-120"></i>
																			</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a href="javascript:void(0)"
                                                                       onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$active_status_table}}','active');"
                                                                       class="tooltip-success" data-rel="tooltip"
                                                                       title="Active">
																			<span class="blue">
																				<i class="ace-icon fa fa-check bigger-120"></i>
																			</span>
                                                                    </a>
                                                                </li>
                                                            @endif

                                                            <li>
                                                                <a href="{{url($current_menu.'/'.$encid.'/edit')}}"
                                                                   class="tooltip-success" data-rel="tooltip"
                                                                   title="Edit">
																			<span class="green">
																				<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                   onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$active_status_table}}','delete');"
                                                                   class="tooltip-error" data-rel="tooltip"
                                                                   title="Delete">
																			<span class="red">
																				<i class="ace-icon fa fa-trash-o bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                   class="tooltip-error" data-rel="tooltip"
                                                                   title="Assign Distributor">
																			<span class="default">
																				<i class="ace-icon fa fa-users bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                {{--<div class="col-xs-6">--}}
                                {{--<div class="dataTables_info">--}}
                                {{--Showing {{($records->currentpage()-1)*$records->perpage()+1}}--}}
                                {{--to {{(($records->currentpage()-1)*$records->perpage())+$records->count()}}--}}
                                {{--of {{$records->total()}} entries--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-xs-6">--}}
                                {{--<div class="dataTables_paginate paging_simple_numbers">--}}
                                {{--{{$records->appends(request()->except('page'))}}--}}
                                {{--</div>--}}
                                {{--</div>--}}
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
@endsection

@section('js')

<script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>

     <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>


    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.select.min.js')}}"></script>
    <script src="{{asset('js/dealer.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    @include('DashboardScript.commonModalScript')
    <script>
        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert({
                            title: 'Alert!',
                            content: 'Done!',
                            buttons: {
                                ok: function () {
                                    setTimeout("window.parent.location = ''", 50);
                                }
                            }
                        });
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
                        // console.log(data);
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

        function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }

        function search() {
            if ($('#search').val() != '') {
                $('#user-search').submit();
            }
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#openBtn').click(function () {
                $('#myModal').modal({
                    show: true
                })
            });

            $(document).on('show.bs.modal', '.modal', function (event) {
                var zIndex = 1040 + (10 * $('.modal:visible').length);
                $(this).css('z-index', zIndex);
                setTimeout(function () {
                    $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
                }, 0);
            });


        });
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
       

   <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                null,null,null,null,null,null,
                                                  
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            //"sScrollXInner": "120%",
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
                                            "text": "<i class='fa fa-database bigger-110 orange'></i> <span class=''>CSV</span>",
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





                         /*       myTable.on('select', function (e, dt, type, index) {
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
                                        if (th_checked)
                                            myTable.row(row).select();
                                        else
                                            myTable.row(row).deselect();
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                                    var row = $(this).closest('tr').get(0);
                                    if (this.checked)
                                        myTable.row(row).deselect();
                                    else
                                        myTable.row(row).select();
                                });
*/


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
         <script>
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#dynamic-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>
@endsection