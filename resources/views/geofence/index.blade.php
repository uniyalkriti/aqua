@extends('layouts.master') 
 
@section('title')
    <title>{{Lang::get('common.geofence')}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>

@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.geofence')}}</li>
                </ul><!-- /.breadcrumb -->
              
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal open collapse in" action="" method="GET" id="geofenceData"
                              role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
                                        <select multiple name="region[]" id="region" class="form-control chosen-select input-sm">
                                            <option disabled="disabled" value="">select</option>
                                            @if(!empty($region))
                                                @foreach($region as $k=>$r)
                                                     <option {{ is_array(Request::get('region')) && !empty(Request::get('region')) && in_array($r['id'],Request::get('region'))?'selected':'' }} value="{{$r['id']}}">{{$r['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="name">{{Lang::get('common.location3')}}</label>
                                        <select multiple name="area[]" id="area" class="form-control chosen-select input-sm">
                                            <option disabled="disabled" value="">select</option>
                                            @if(!empty($state))
                                                @foreach($state as $k=>$r)
                                                    <option {{ is_array(Request::get('area')) && !empty(Request::get('area')) && in_array($k,Request::get('area'))?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="name">Town</label>
                                        <select multiple name="territory[]" id="territory" class="form-control chosen-select input-sm">
                                            <option disabled="disabled" value="">Select</option>
                                            @if(!empty($town))
                                                @foreach($town as $k=>$r)
                                                 <option {{ is_array(Request::get('territory')) && !empty(Request::get('territory')) && in_array($k,Request::get('territory'))?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>
                                </div>
                                
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                            style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                    </button>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                 <a href='createGeofence'><button type="button" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                            style="margin-top: 28px;"><i class="fa fa-map mg-r-10"></i>
                                        Create GeoFence
                                    </button></a>
                                </div>
                            </div>
                        </form>
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

                                <div class="row">
                                    <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">


                                    </div>
                                </div>

                            </div>
                        </div>

                    

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
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
                            Geo Fence Details
                        </div>
                        <!-- div.table-responsive -->
                        <!-- div.dataTables_borderWrap -->
                        <div>
                            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><strong>Sr.no</strong></th>
                                        <th><strong>Region</strong></th>
                                        <th><strong>State</strong></th>
                                        <th><strong>Town</strong></th>
                                        <th><strong>Options</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(!empty($geofenceData))
                                @foreach($geofenceData as $key => $value)
                                <?php $encid = Crypt::encryptString($value->id);?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$value->region_name}}</td>
                                        <td>{{$value->state_name}}</td>
                                        <td>{{$value->town_name}}</td>
                                        <td>
                                            <div class="hidden-sm hidden-xs action-buttons">
                                                <a  title="view" href="{{url($current_menu.'/'.$encid.'/')}}">
                                                    <i class="ace-icon fa fa-eye bigger-120"></i>
                                                </a>
                                               <a  title="view" href="{{url($current_menu.'/'.$encid.'/edit')}}">
                                                    <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                </a>                            
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
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
    <script src="{{asset('msell/page/report32.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <!-- ............................scripts for table ............................ -->
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
                      { "bSortable": false },
                      null, null,null,
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

@endsection