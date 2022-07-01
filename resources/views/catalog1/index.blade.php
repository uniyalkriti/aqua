@extends('layouts.master') 
 
@section('title')
    <title>{{Lang::get('common.catalog_1')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>

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
                        <div class="table-header center">
                        {{Lang::get('common.catalog_1')}}
                            <p class="bs-component pull-right">
                            <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-success pull-right ActionCreate"><i
                            class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.'.$current_menu)}}</a>
                            </p>
                        </div>
                        <!-- div.table-responsive -->
                        <!-- div.dataTables_borderWrap -->
                        <div>
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <th class="center">
                                            {{Lang::get('common.s_no')}}
                                        </th>
                                        <th>{{Lang::get('common.catalog_1')}} ID</th>
                                        <th>{{Lang::get('common.catalog_1')}} Name</th>
                                        <th>
                                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                            {{Lang::get('common.created_date')}}
                                        </th>
                                        <th>
                                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                            {{Lang::get('common.updated_date')}}
                                        </th>
                                        <th>{{Lang::get('common.status')}}</th>
                                        <th>{{Lang::get('common.action')}}</th>
                                        
                                        </thead>
                                    <tbody>
                                    @foreach($records as $key=>$data)
                                        <?php $encid = Crypt::encryptString($data->id);?>
                                        <tr>
                                            <td class="center">
                                                {{$key+1}}
                                            </td>
                                            <td>
                                                    {{$data->id}}
                                            </td>
                                            <td>{{$data->name}}</td>
                                            <td>{{!empty($data->created_at)?date('d-M-Y',strtotime($data->created_at)):'-'}}</td>
                                            <td>{{!empty($data->updated_at)?date('d-M-Y',strtotime($data->updated_at)):'-'}}</td>

                                            <td class="hidden-480">
                                                @if($data->status==1)
                                                <span class="label label-sm label-success">Active</span>
                                                @elseif($data->status==2)
                                                <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($data->status==1)
                                                    <button title="Inactive" class="btn btn-xs btn-warning ActionData"
                                                            onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$table}}','inactive');">
                                                        <i class="ace-icon fa fa-ban bigger-120"></i>
                                                    </button>
                                                    @else
                                                    <button title="Active" class="btn btn-xs btn-success ActionData"
                                                            onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$table}}','active');">
                                                        <i class="ace-icon fa fa-check bigger-120"></i>
                                                    </button>
                                                    @endif
                                                    <?php echo $data->user_id;  ?>
                                                    <a title="Edit" class="btn btn-xs btn-info ActionData"
                                                       href="{{url($current_menu.'/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button title="Delete" class="btn btn-xs btn-danger ActionDelete"
                                                            onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$table}}','delete');">
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

                                                            @if($data->status==1)
                                                                <li>
                                                                    <a href="javascript:void(0)"
                                                                       onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$current_menu}}','inactive');"
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
                                                                       onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$current_menu}}','active');"
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
                                                                   onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$current_menu}}','delete');"
                                                                   class="tooltip-error" data-rel="tooltip"
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
    <!-- ......................table ends contents...........................................  -->
    
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/catalog.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
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
                      null, null,null,null,null,
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
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
    <script>
        toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
    </script>
    @endif


@endsection