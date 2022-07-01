@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.product_rate_list')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li class="active" style="color: white">{{Lang::get('common.product_rate_list')}}</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>
        <?php 
        if(!empty($_GET['state_id']))
            $cstate_id=$_GET['state_id'];
            else
            $cstate_id=0;
        if(!empty($_GET['template_type_id']))
            $template_id=$_GET['template_type_id'];
            else
            $template_id=0;
        ?>
                

<!-- ......................table contents........................................... -->
        <div class="main-container ace-save-state" id="main-container">
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                        <form id="month_form" method="get">
                                <div class="col-md-2">
                                    <label>{{Lang::get('common.location3')}}</label>
                                    <div class="center">
                                       <select   name="state_id" id="state_id" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($stateList))
                                                @foreach($stateList as $l1_key=>$l1_data)
                                                    <option {{$cstate_id==$l1_key?'selected':''}} value="{{$l1_key}}">{{$l1_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @if(!empty($template_array))
                                <div class="col-md-2">
                                    <label>{{Lang::get('common.template')}}</label>
                                    <div class="center">
                                       <select   name="template_type_id" id="template_type_id" class="form-control input-sm">
                                            @if(!empty($template_array))
                                                @foreach($template_array as $l1_key=>$l1_data)
                                                    <option {{$template_id==$l1_key?'selected':''}} value="{{$l1_key}}">{{$l1_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @endif
                 

                                
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                            style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                        {{Lang::get('common.find')}}
                                   </button> 
                                </div>
                                
                            </form>
                            <div class="col-xs-12">
                                <div class="clearfix">
                                    <div class="pull-right tableTools-container"></div>
                                </div>
                                <div class="table-header center">
                                    {{Lang::get('common.product_rate_list')}}
                                    <p class="bs-component pull-right">
                                    <span id="checkTrialLocation">
                                    <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-success pull-right"><i
                                    class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.'.$current_menu)}}</a>
                                    </span>
                                    </p>
                                </div>
                                <!-- div.table-responsive -->
                                <!-- div.dataTables_borderWrap -->
                                <div>
                                @if(!empty($records))
                                    <table id="dynamic-table" class="table table-bordered table-hover">
                                        <thead>
                                            <th class="center">
                                                {{Lang::get('common.s_no')}}
                                            </th>
                                            <th>{{Lang::get('common.location3')}} ID</th>
                                            <th>{{Lang::get('common.location3')}} </th>
                                            <th>{{Lang::get('common.csa')}} </th>
                                            <th>{{Lang::get('common.distributor')}} </th>
                                            <th>{{Lang::get('common.catalog_4')}} ID</th>
                                            <th>{{Lang::get('common.catalog_4')}} Code</th>
                                            <th>{{Lang::get('common.catalog_4')}} Name</th>
                                    
                                            
                                            <th>{{Lang::get('common.catalog_3')}} Name</th>
                                            <th>{{Lang::get('common.catalog_2')}} Name</th>
                                            <th>{{Lang::get('common.catalog_1')}} Name</th>
                                            <th>{{Lang::get('common.case')}} {{Lang::get('common.mrp')}}</th>
                                            <th>{{Lang::get('common.mrp')}}</th>
                                            <th>{{Lang::get('common.csa')}} {{Lang::get('common.case')}} {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.csa')}} {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.distributor')}} {{Lang::get('common.case')}} {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.distributor')}} {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.retailer')}} {{Lang::get('common.case')}} {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.retailer')}} {{Lang::get('common.rate')}}</th>

                                            <th>{{Lang::get('common.retailer')}} Other {{Lang::get('common.rate')}}</th>
                                            <th>{{Lang::get('common.distributor')}} Other {{Lang::get('common.rate')}}</th>

                                            <th>{{Lang::get('common.status')}}</th>
                                            <th>Actions</th>
                                            <!-- <th>Action</th> -->
                                        </thead>
                                        <tbody>
                                        <?php $inc=1;?>
                                        @foreach($records as $key=>$data)
                                            <?php 
                                            $encid = Crypt::encryptString($data['state_id']);
                                            $id = Crypt::encryptString($data['id']);
                                            $primaryid = $data['id'];
                                            // dd($data);
                                            ?>
                                            <tr>
                                                <td class="center">
                                                 {{$inc}}</td>
                                                <td>{{$data['state_id']}}</td>
                                                <td>{{!empty($state_name[$data['state_id']])?$state_name[$data['state_id']]:'-'}}</td>
                                                <td>-</td>
                                                <td>{{!empty($distribuor_name[$data['product_id']])?$distribuor_name[$data['product_id']]:'-'}}</td>
                                                <td>{{$data['product_id']}}</td>
                                                <td>{{$data['item_code']}}</td>
                                                <td>{{$data['name']}}</td>
                                              
                                                <td>{{$data['cat3']}}</td>
                                                <td>{{$data['cat2']}}</td>
                                                <td>{{$data['cat1']}}</td>
                                                <td>{{$data['mrp']}}</td>
                                                <td>{{$data['mrp_pcs']}}</td>
                                                <td>{{$data['ss_case_rate']}}</td>
                                                <td>{{$data['ss_pcs_rate']}}</td>
                                                <td>{{$data['dealer_rate']}}</td>
                                                <td>{{$data['dealer_pcs_rate']}}</td>
                                                <td>{{$data['retailer_rate']}}</td>
                                                <td>{{$data['retailer_pcs_rate']}}</td>

                                                <td>{{$data['other_retailer_rate']}}</td>
                                                <td>{{$data['other_dealer_rate']}}</td>



                                                <td class="hidden-480" id="{{$primaryid.'status_written'}}">
                                                    @if($data['rate_list_status']==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                    @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                    @endif
                                                    
                                                </td>

                                                <td>
                                                     <a title="Edit" class="btn btn-xs btn-info"
                                                       href="{{url($current_menu.'/'.$id.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>


                                                    <!-- for active and inactive -->
                                                    <div id="{{$primaryid.'active_incative'}}"> 
                                                        @if($data['rate_list_status']==1)
                                                            <a title="Inactive" class="btn btn-xs btn-warning" onclick="confirmAction('sku_rate_list','sku_rate_list','{{$primaryid}}','{{$status_table}}','inactive');">
                                                                <i class="ace-icon fa fa-ban bigger-120"></i>
                                                            </a>
                                                            
                                                        @else
                                                            <a title="Active" class="btn btn-xs btn-success" onclick="confirmAction('sku_rate_list','sku_rate_list','{{$primaryid}}','{{$status_table}}','active');">
                                                                <i class="ace-icon fa fa-check bigger-120"></i>
                                                            </a>
                                                            
                                                        @endif
                                                    </div>






                                                </td>

                                            </tr>
                                            <?php $inc++; ?>
                                        @endforeach

                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
<!-- ......................table ends contents...........................................  -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')

    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('js/user-management.js')}}"></script>
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
                      null, null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,
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
        <!-- ends here  -->
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
                                    if(heading == 'sku_rate_list')
                                    {
                                        // alert(heading);
                                        concat = action_id+'active_incative';
                                        concat_status = action_id+'status_written';

                                        $('#'+concat).empty('');
                                        $('#'+concat_status).empty('');
                                        

                                        if(act == 'inactive')
                                        {
                                            $('#'+concat).append("<button title='active' class='btn btn-xs btn-success' onclick=confirmAction('sku_rate_list','sku_rate_list',"+action_id+",'product_rate_list','active'); > <i class='ace-icon fa fa-check bigger-120'></i> </button>");
                                            $('#'+concat_status).append("<span class='label label-sm label-warning'>In-active</span>");
                                        }
                                        else if(act == 'active')
                                        {
                                            $('#'+concat).append("<button title='Inactive' class='btn btn-xs btn-warning' onclick=confirmAction('sku_rate_list','sku_rate_list',"+action_id+",'product_rate_list','inactive'); > <i class='ace-icon fa fa-ban bigger-120'></i> </button>");
                                            $('#'+concat_status).append("<span class='label label-sm label-success'>Active</span>");

                                        } 

                                    }else{
                                    setTimeout("window.parent.location = ''", 50);
                                    }
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