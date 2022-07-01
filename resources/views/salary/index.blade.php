@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.salary')}} - {{config('app.name')}}</title>
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
                            {{Lang::get('common.salary')}}
                            <a href="{{url('salary_generation')}}" style="color:black;" class="btn btn-sm btn-warning pull-right"><i
                            class="fa fa-plus mg-r-10" style="color:black;"></i> <span style="color:black;">Process  {{Lang::get('common.salary')}}</span></a>
                            <a href="{{url('salary/create')}}" class="btn btn-sm btn-success pull-right"><i
                            class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.salary')}}</a>
                        </div>
                        <!-- div.table-responsive -->
                        <!-- div.dataTables_borderWrap -->
                        <div>
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            S.No.
                                        </th>
                                        <th>{{Lang::get('common.user')}}</th>
                                        <th>Salary Details Filled</th>
                                        <th>Last Salary PDF</th>

                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($working_type as $key=>$working_type_data)
                                        <?php 

                                        $encid = Crypt::encryptString($working_type_data->id);

                                        $pdf = !empty($salary_management_data[$working_type_data->user_id])?$salary_management_data[$working_type_data->user_id]:'';

                                        ?>
                                        <tr>
                                            <td class="center">
                                                {{ $key+1 }}
                                            </td>
                                            <td>
                                                <a href="#">{{ucwords(strtoupper($working_type_data->user_name))}}</a>
                                            </td>
                                            <td>{{$working_type_data->created_at}}</td>
                                            <td>
                                                <a href="{{url('pdf/'.$pdf)}}" target="_blank" class="fa fa-file-pdf-o" style="font-size:30px;color:red" src="{{url('pdf/'.$pdf)}}" ></a>
                                                
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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/index.location2.js')}}"></script>
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
                      null, null,
                      { "bSortable": false }
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
@endsection