@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.role')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
@endsection

@section('body')
    
            <!-- ......................table contents........................................... -->
<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="clearfix">
                            <div class="pull-right tableTools-container"></div>
                        </div>
                        <div class="table-header">
                            Role Master
                             <p class="bs-component pull-right">
                            <span id="checkTrialLocation">
                            <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-success pull-right"><i
                            class="fa fa-plus mg-r-10"></i> Add @Lang('common.role')</a>
                            </span>
                            </p>
                        </div>
                        <!-- div.table-responsive -->
                        <!-- div.dataTables_borderWrap -->
                        <div>
                            <table id="dynamic-table" class="table table-striped table-bordered table-hover">

                                <thead>
                                    <th class="center">
                                        S.No.
                                    </th>
                                    <th>Role Name</th>
                                    <th>Senior Name</th>

                                    <th>
                                        <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                        Registered On
                                    </th>
                                    <th class="hidden-480">Status</th>
                                    <th class="hidden-480">Action</th>

                                </thead>

                                <tbody>
                                @foreach($role as $key=>$role_data)
                                    <?php $encid = Crypt::encryptString($role_data->role_id);?>
                                    <tr>
                                        <td class="center">
                                            {{ $role->firstItem() + $key }}
                                        </td>
                                        <td>
                                            <a href="#">{{ucwords(strtolower($role_data->rolename))}}</a>
                                        </td>
                                        <td>{{ !empty($senior_name[$role_data->senior_role_id])?$senior_name[$role_data->senior_role_id]:'' }}</td>

                                        <td>{{!empty($role_data->created_at)?date('d-M-Y',strtotime($role_data->created_at)):'-'}}</td>    

                                        <td class="hidden-480">
                                            @if($role_data->status==1)
                                                <span class="label label-sm label-success">Active</span>
                                            @elseif($role_data->status==2)
                                                <span class="label label-sm label-danger">Deleted</span>
                                            @else
                                                <span class="label label-sm label-warning">In-active</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="hidden-sm hidden-xs btn-group">
                                                @if($role_data->status==1)
                                                    <button class="btn btn-xs btn-warning"
                                                            onclick="confirmAction('{{Lang::get('common.role')}}','{{Lang::get('common.role_key')}}','{{$role_data->role_id}}','_role','inactive');">
                                                        <i class="ace-icon fa fa-ban bigger-120"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-xs btn-success"
                                                            onclick="confirmAction('{{Lang::get('common.role')}}','{{Lang::get('common.role_key')}}','{{$role_data->role_id}}','_role','active');">
                                                        <i class="ace-icon fa fa-check bigger-120"></i>
                                                    </button>
                                                @endif

                                            @if($company_id == 43)
                                                <button title="Assign" roleid="{{$role_data->role_id}}"
                                                            data-toggle="modal" data-target="#myModal"
                                                            class="user-modal btn btn-xs btn-default">
                                                        <i class="ace-icon fa fa-users bigger-120"></i>
                                                </button>
                                            @endif    



                                                <div class="inline pos-rel">
                                                    <button class="btn btn-xs btn-primary dropdown-toggle"
                                                            data-toggle="dropdown" >
                                                        <i class="ace-icon fa fa-cog  bigger-120"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                                      

                                                        <li>

                                                            <!-- <a class="btn btn-xs btn-info"
                                                            href="{{url('role/'.$encid.'/edit')}}">
                                                                <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                            </a> -->
                                                            <a href="{{url('role/'.$encid.'/edit')}}" class="tooltip-success" data-rel="tooltip"
                                                               title="Edit">
                                                                        <span class="green">
                                                                            <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                                                        </span>
                                                            </a>
                                                        </li>

                                                        <li>

                                                            <!-- <button class="btn btn-xs btn-danger"
                                                                    onclick="confirmAction('{{Lang::get('common.role')}}','{{Lang::get('common.role_key')}}','{{$role_data->role_id}}','_role','delete');">
                                                                <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                            </button> -->

                                                            <a href="#" class="tooltip-error" data-rel="tooltip"
                                                               title="Delete" onclick="confirmAction('{{Lang::get('common.role')}}','{{Lang::get('common.role_key')}}','{{$role_data->role_id}}','_role','delete');">
                                                                        <span class="red">
                                                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                                        </span>

                                                        </li>
                                                    </ul>

                                                {{--<button class="btn btn-xs btn-warning">--}}
                                                {{--<i class="ace-icon fa fa-flag bigger-120"></i>--}}
                                                {{--</button>--}}
                                            </div>

                                            <div class="hidden-md hidden-lg">
                                                <div class="inline pos-rel">
                                                    <button class="btn btn-minier btn-primary dropdown-toggle"
                                                            data-toggle="dropdown" data-position="auto">
                                                        <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                                        <li>
                                                            <a href="#" class="tooltip-info" data-rel="tooltip"
                                                               title="View">
                                                                        <span class="blue">
                                                                            <i class="ace-icon fa fa-search-plus bigger-120"></i>
                                                                        </span>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a href="#" class="tooltip-success" data-rel="tooltip"
                                                               title="Edit">
                                                                        <span class="green">
                                                                            <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                                                        </span>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a href="#" class="tooltip-error" data-rel="tooltip"
                                                               title="Delete">
                                                                        <span class="red">
                                                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
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
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Assign</h4>
            </div>
            <div class="modal-body">
                <form method="get" id="filter_distributor" action="role_wise_assign" enctype="multipart/form-data">
                    <input type="hidden" id="role_id" name="role_id" value="">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- <div class="col-lg-4">
                                <div class="">
                                    <label class="control-label no-padding-right" for="location_1">{{Lang::get('common.task_of_the_day')}}</label>
                                    <select multiple name="task_of_the_day[]" id="task_of_the_day" class="form-control chosen-select">
                                        <option value="">select</option>
                                        @if(!empty($task_of_the_day))
                                            @foreach($task_of_the_day as $k=>$r)
                                                <option value="{{$k}}">{{$r}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> -->

                            <!-- <div class="col-lg-4">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="location_2"> {{Lang::get('common.working_type')}} </label>
                                    <select multiple name="working_with_type[]" id="working_with_type" class="form-control chosen-select">
                                        <option value="">select</option>
                                        @if(!empty($working_with_type))
                                            @foreach($working_with_type as $k=>$r)
                                                <option value="{{$k}}">{{$r}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> -->
                            <!-- <div class="col-lg-4">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="travelling_type"> {{Lang::get('common.travelling_type')}} </label>
                                    <select multiple name="travel_mode[]" id="travel_mode" class="form-control chosen-select">
                                        <option value="">select</option>
                                        @if(!empty($travel_mode))
                                            @foreach($travel_mode as $k=>$r)
                                                <option value="{{$k}}">{{$r}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> -->
                             
                            

                            <table class="table table-bordered">

                                <thead>
                                    <th>TA</th>
                                    <th>Telephone Expense</th>
                                </thead>

                                <tbody>
                                  
                                    <tr>
                                        <td style="align-content: center;"><input  type="number" step="0.01" class="form-control-file center" name="ta" id="ta" value="0" ></td>
                                     
                                        <td style="align-content: center;"><input  type="number" step="0.01" class="form-control-file center" name="te" id="te" value="0" ></td>
                                    </tr>
                                
                                </tbody>
                            </table>

                            <table class="table table-bordered">

                                <thead>
                                    <th>Sr.no</th>
                                    <th>Class Type</th>
                                    <th>Enter DA</th>
                                </thead>

                                <tbody>
                                    <?php 
                                        $inc = 1;
                                    ?>
                                @foreach($class_type as $key=>$value)
                                    <tr>
                                        <td>{{$inc}}</td>
                                        <td>{{$value}}</td>
                                        @if(empty($class_type_check[$key]))
                                        <input  type="hidden" class="form-control" name="class_type_id[]" value="{{$key}}">
                                        <td style="align-content: center;"><input  type="number" step="0.01" class="form-control-file center" name="da_for_class_type[]" id="da_for_class_type" value="0" ></td>
                                        @else
                                        <!-- <input  type="hidden" class="form-control" name="class_type_id_for_details_array[]" value="{{$key}}"> -->
                                        <td style="align-content: center;">
                                             @foreach($class_type_details as $keyd=>$valued)
                                                  <input  type="hidden" class="form-control" name="class_type_details_id[]" value="{{$key}}|{{$keyd}}">
                                                  {{$valued}}
                                                <input  type="number" step="0.01" class="form-control-file center" name="da_for_class_type_details[]" id="da_for_class_type_details" value="0" >
                                             @endforeach
                                        </td>     
                                        @endif
                                    </tr>
                                    <?php 
                                        $inc++;
                                    ?>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="col-lg-4">
                                <div class="">
                                    <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                            style="margin-top: 25px">Assign
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- ......................table ends contents...........................................  -->

@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/role.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

    <script>
        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
    <!-- ............................scripts for table ............................ -->
    <script type="text/javascript">
            if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.select.min.js')}}"></script>
    <!-- ace scripts -->
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    @include('DashboardScript.commonModalScript')

    <script type="text/javascript">
            jQuery(function($) {
                //initiate dataTables plugin
                var myTable = 
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                .DataTable( {
                    bAutoWidth: false,
                    "aoColumns": [
                      { "bSortable": true },
                      null,null,null,null,
                      { "bSortable": true }
                    ],
                    "aaSorting": [],
                    
                    select: {
                        style: 'multi'
                    }
                } );
            
                
                
                $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
                
                new $.fn.dataTable.Buttons( myTable, {
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
                } );
                myTable.buttons().container().appendTo( $('.tableTools-container') );
                
                //used for copy to clipboard
                var defaultCopyAction = myTable.button(1).action();
                myTable.button(1).action(function (e, dt, button, config) {
                    defaultCopyAction(e, dt, button, config);
                    $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
                });
                // end here copy clipboard option
                
                // used for search option
                var defaultColvisAction = myTable.button(0).action();
                myTable.button(0).action(function (e, dt, button, config) {
                    
                    defaultColvisAction(e, dt, button, config);
                    
                    
                    if($('.dt-button-collection > .dropdown-menu').length == 0) {
                        $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                    }
                    $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
                });
            // end here search option
            })
        </script>
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
            if($('#search').val()!='')
            {
                $('#user-search').submit();
            }
        }
        $('.user-modal').click(function () {
            $('#result').html('');
            $('#role_id').val($(this).attr('roleid'));
        });
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
@endsection