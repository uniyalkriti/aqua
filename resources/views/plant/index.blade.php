@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.plant')}} - {{config('app.name')}}</title>
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
                            {{Lang::get('common.plant')}}
                            <a href="{{url('plant/create')}}" class="btn btn-sm btn-success pull-right"><i
                            class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.plant')}}</a>
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
                                        <th>{{Lang::get('common.plant')}}</th>
                                        <th>Sequence</th>
                                        <th class="hidden-480">Status</th>

                                        <th> Action </th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($working_type as $key=>$working_type_data)
                                        <?php $encid = Crypt::encryptString($working_type_data->id);?>
                                        <tr>
                                            <td class="center">
                                                {{ $key+1 }}
                                            </td>
                                            <td>
                                                <a href="#">{{ucwords(strtoupper($working_type_data->name))}}</a>
                                            </td>
                                            <td>{{$working_type_data->sequence}}</td>
                                            <td class="hidden-480">
                                                @if($working_type_data->status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($working_type_data->status==2)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($working_type_data->status==1)
                                                        <button class="btn btn-xs btn-warning"
                                                                onclick="confirmAction('{{Lang::get('common.plant')}}','{{Lang::get('common.plant')}}','{{$working_type_data->id}}','_dms_plant_master','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-xs btn-success"
                                                                onclick="confirmAction('{{Lang::get('common.plant')}}','{{Lang::get('common.plant')}}','{{$working_type_data->id}}','_dms_plant_master','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif

                                                    <a class="btn btn-xs btn-info"
                                                       href="{{url('plant/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button class="btn btn-xs btn-danger"
                                                            onclick="confirmAction('{{Lang::get('common.plant')}}','{{Lang::get('common.plant')}}','{{$working_type_data->id}}','_dms_plant_master','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button>
                                                    <button type="button" plantid="{{$working_type_data->id}}"
                                                        data-toggle="modal" data-target="#reciept_modal" class="btn btn-default reciept_modal btn-round btn-white">
                                                        <i class="ace-icon fa fa-send green"></i>
                                                        Action 
                                                    </button>
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
    <!-- ......................table ends contents...........................................  -->

<div class="modal fade" id="reciept_modal" role="dialog">
    <div class="modal-dialog" style="width:900px;">
    
        <!-- Modal content-->
        <div class="modal-content" id ="modalDiv">
            
            <div class="modal-body" id="qwerty">
                <form action="dms_plant_stock_submit" method="post" id="reciept_modal_form" enctype="multipart/form-data">
                    {!! csrf_field() !!}

                    <input type="hidden" id="plantid_n" name="plant_id" value="">
                    
                    <table class="table-bordered" >
                        <th style="background-color:#fcf8e3; color:black; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                        <th style="background-color:#fcf8e3; color:black; width:560px; height: 30px; text-align:right;"> Preview&nbsp&nbsp&nbsp  </th>

                    </table>
                    <br>
                   

                    <table class="table table-bordered">

                        <thead>
                            <th>Sr.no</th>
                            <th>Product Name</th>
                            <th>Pack Size</th>
                            <th>Pack Type</th>
                            <th>Bacth No.</th>
                            <th>MFG Date</th>
                            <th>Case</th>
                        </thead>
                       
                       <tbody>
                            <?php 
                                $inc = 1;
                            ?>
                           @foreach($product_data as $key=>$value)
                            <tr>
                                <td>{{$inc}}</td>
                                <td>{{$value}}</td>
                                <input style="width: 70px;" type="hidden" name="product_id[]" value="{{$key}}">
                                <td><input style="width: 70px;" type="text" id="pack_size" name="pack_size[{{$key}}]" autocomplete="off"></td>
                                <td><input  style="width: 70px;"type="text" id="pack_type" name="pack_type[{{$key}}]" autocomplete="off"></td>
                                <td><input style="width: 70px;" type="text" id="batch_no" name="batch_no[{{$key}}]" autocomplete="off"></td>
                                <td><input style="width: 70px;" type="text" id="mfg_date" name="mfg_date[{{$key}}]" autocomplete="off"></td>
                                <td><input style="width: 70px;" type="text" id="case" name="case[{{$key}}]" autocomplete="off"></td>
                            </tr>
                            <?php 
                                $inc++;
                            ?>
                           @endforeach
                           
                       </tbody>
                      
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-lg-3">
                                <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                            </div>
                            
                            <div class="col-lg-3" id="submit">
                                <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>       
@endsection

@section('js')
    <script type="text/javascript">
        $('.reciept_modal').click(function() {
            var plantid = $(this).attr('plantid');

            $('#plantid_n').html('');
            
            $('#plantid_n').val(plantid);

        });
        $(document).ready(function (e) {
            $('#reciept_modal_form').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        alert('Submitted SuccessFully');
                        $("#pack_size").val('');
                        $("#pack_type").val('');
                        $("#batch_no").val('');
                        $("#mfg_date").val('');
                        $("#case").val('');
                        $('#reciept_modal').modal('toggle');
                       
                    },
                    error: function(data){
                        // console.log("error");
                        // console.log(data);
                    }
                });
            }));

        });
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