@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.assign_module')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
@endsection

@section('body')
<!-- ......................table contents........................................... -->
<!-- main container starts here  -->
<div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.assign_module')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#assignModule" class="btn btn-sm btn-default"><i
                        class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
                <!-- /.nav-search -->
            </div>
            <div class="page-content">
            

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <form class="form-horizontal open collapse in"  method="get" id="assignModule" enctype="multipart/form-data">
                            <!-- {!! csrf_field() !!} -->
                            <input type="hidden" name="flag" value='1'>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="name">Companies</label>
                                        <select name="company_id" id="company" class="form-control chosen-select input-sm" required>
                                            <option  value="">select</option>
                                            @if(!empty($company))
                                                @foreach($company as $k=>$r)
                                                    <option {{ Request::get('company')==$k?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <!-- PAGE CONTENT BEGINS -->
                                        @if(Session::has('message'))
                                            <div class="alert alert-block {{ Session::get('alert-class', 'alert-info') }}">
                                                <button type="button" class="close" data-dismiss="alert">
                                                    <i class="ace-icon fa fa-times"></i>
                                                </button>
                                                <i class="ace-icon fa fa-check green"></i>
                                                {{ Session::get('message') }}
                                            </div>
                                        @endif
                                         

                                        <div class="hr hr-18 dotted hr-double"></div>
                                      
                       
                                    </div>
                                </div>
                        <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </form>
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>

    @if(!empty($flag))
        <div id="form-wrapper" style="max-width:700px;margin:auto;">
            <form  method="get" action="SubmitWebAssigning" >
                    <input type="text" hidden="hidden" name="company_id" value="{{$company_id}}">
                     
                        <table class="table table-striped table-bordered table-hover" width="50%">
                        <tr>
                            <th>Sr.no</th>
                            <th>Details</th>
                            <th>Assigning Name</th>
                            <th>Sequence</th>
                            <th>Assign<br> <input type="checkbox" onchange="checkAll(this)"></th>
                        </tr>
                            @if(!empty($module_bucket_query))
                                <?php $inc=0;?>
                                @foreach($module_bucket_query as $k=>$d)
                                    <tr>
                                        
                                            
                                        <td>{{$inc+1}}</td>
                                        @if(!empty($check[$k]))
                                        <td class="center">
                                            <div class="action-buttons">
                                                <a href="#" class="green bigger-140 show-details-btn" title="Show Details">
                                                    <i class="ace-icon fa fa-angle-double-down"></i>
                                                    <span class="sr-only">Details</span>
                                                </a>
                                            </div>
                                        </td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td >
                                            <label class="control-label bolder blue">
                                                <span class="lbl">{{$d}} </span>
                                            </label>
                                        </td>
                                        <td><input type="text" name="sequence[]" value="{{$inc+1}}"></td>
                                        <td>
                                            <input type="checkbox"  name="module_id[]" {{in_array($k,$web_module)?'checked':''}} value="{{$k}}">
                                        </td>
                                       
                                            
                                    </tr>
                                    <tr class="detail-row">
                                        <td colspan="15">
                                            <div class="table-detail">
                                                <div class="row">
                                                    <div class="col-xs-12 col-md-2">
                                                        <div class="text-center">
                                                
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-md-10">
                                                        <div class="space visible-xs"></div>

                                                        <table class="table table-bordered table-detail table-responsive table-hover table-stipped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.no</th>
                                                                    <th>Assigning Name</th>
                                                                    <th>Assign</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(!empty($sub_web_module_bucket_query))
                                                                  <?php $sub_inc = 1; ?>
                                                                    @foreach($sub_web_module_bucket_query as $key=>$data)
                                                                        @if($data->module_id==$k)
                                                                        
                                                                            @if(empty($check_2[$data->id]))
                                                                                <tr>
                                                                                    <td>{{$sub_inc}}</td>
                                                                                    <td>
                                                                                        <label>
                                                                                            <span class="lbl">{{$data->sub_module_name}}</span>      
                                                                                        </label>
                                                                                    </td>
                                                                                    <td>
                                                                                        
                                                                                        <input type="checkbox"  {{in_array($data->id,$web_sub_module)?'checked':''}} name="sub_module_id[]" value="{{$data->id}}">
                                                                                    </td>

                                                                                </tr>
                                                                                 <?php  $sub_inc++; ?>
                                                                            @endif
                                                                            @foreach($sub_sub_web_module_bucket_query as $s_key => $s_value)
                                                                                @if($s_value->sub_web_module_id == $data->id)
                                                                                <tr>
                                                                                    <td>{{$sub_inc}}</td>
                                                                                    <td>
                                                                                        <label>
                                                                                            <span class="lbl">{{$data->sub_module_name}} -> {{$s_value->sub_module_name}}</span>      
                                                                                        </label>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="checkbox" name="sub_sub_module_id[]" {{in_array($s_value->id,$web_sub_sub_module)?'checked':''}} value="{{$data->id}}-{{$s_value->id}}">
                                                                                    </td>

                                                                                </tr>
                                                                                 <?php  $sub_inc++; ?>
                                                                                @endif
                                                                               
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                              <br/>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                  <?php  $inc++; ?>
                                @endforeach
                              
                                  </table>
                            @endif
                   
                        <div class="row">
                            <div class="col-md-3" align="center"><br>
                               <input class="form-control btn btn-primary" type="submit" name="submit" value="Submit">
                             </div>
                        </div>
                 
               
            </form>
        </div>
    @endif
</div>
    <!-- ......................table ends contents...........................................  -->
                               
@endsection

@section('js')
<script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
    <script type="text/javascript">
        function checkAll(ele) 
        {
            var checkboxes = document.getElementsByTagName('input');
            if (ele.checked) 
            {
                 for (var i = 0; i < checkboxes.length; i++) 
                {
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = true;
                    }
                }
            } 
            else 
            {
                for (var i = 0; i < checkboxes.length; i++) 
                {
                     console.log(i)
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = false;
                    }
                }
            }
        }
    </script>
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
                      null,
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
    <script type="text/javascript">  /***************/
    $('.show-details-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('tr').next().toggleClass('open');
        $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    });
    /***************/
        </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
@endsection