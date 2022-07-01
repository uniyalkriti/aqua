@extends('layouts.masterLayoutTest')

@section('title')
    <title>{{Lang::get('common.user-mgmt')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />

@endsection

@section('body')
    <div class="main-content" >
        <div class="main-content-inner" style="background-color: #dff0d8;">
            <div class="breadcrumbs ace-save-state center" id="breadcrumbs" style="background-color: #fcf8e3;">
                <ul class="breadcrumb">
                    <li style="text-align: center;">
                        <i class="ace-icon fa fa-users "></i>
                        <a href="{{url('#')}}" style="text-align: center;"><b>Expense</b> </a>
                    </li>

                    <li class="active" style="text-align: center;"><b>Approval List</b></li>
                </ul>

            </div>

            <div class="page-content" style="background-color: #dff0d8;">
                <div class="clearfix" style="margin-top: 5px"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        @if(Session::has('message'))
                        <?php
                         $class = Session::get('class');
                        ?>
                            <div class="alert alert-block {{ 'alert-'.Session::get('class') }}">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>
                                <i class="ace-icon fa fa-check "></i>
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        @if(count($errors)>0)
                            @foreach ($errors->all() as $error)
                                <div class="help-block">{{ $error }}</div>
                            @endforeach
                        @endif
                        <form class="form-horizontal open collapse in" action="expense_webview" method="GET" id="" role="form"
                              enctype="multipart/form-data">
                              <!-- <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                 Details
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <input type="hidden" name="user_id" value="{{$user_id}}">    
                            <input type="hidden" name="company_id" value="{{$company_id}}">    
                            <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-lg-2">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>
                                                   
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar bigger-110"></i>
                                            </span>

                                            <input class="form-control input-sm" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                     <div class="col-xs-12 col-sm-12 col-lg-2">
                                        <button type="submit" class="form-control btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                                style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                            Find
                                        </button>
                                    </div>
                            </div>
                        </form>      

                        <br>

                        <form class="form-horizontal" action="{{url('register_civvillian_submit')}}" method="get"
                              id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                          
                          

                            <!-- ssdb table starts here -->
                            <!-- <div class="clearfix">
                                    <div class="pull-right tableTools-container"></div>
                            </div> -->
                            <div class="table-header center">
                            Expense Approval List Report
                            
                            </div>
                            <table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
                                <thead>
                                    <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>HQ</th>
                                    <th>Distance(KM)</th>
                                    <th>Fare</th>
                                    <th>DA</th>
                                    <th>Total Amount</th>
                                  
                                    <th>Edit/Approve</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php $i=1;?>

                                     @foreach($expense_list as $k=> $r)
                                     <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$r->user_name}}</td>
                                        <td>{{$r->rolename}}</td>
                                        <td>{{$r->travellingDate}}</td>
                                        <td>{{$r->head_quarter}}</td>
                                        <td>{{$r->distance}}</td>
                                        <td>{{$r->fare}}</td>
                                        <td>{{$r->da}}</td>
                                        <td>{{round($r->total,3)}}</td>
                                    
                                        <td><a href="{{url('expense_approve/'. $r->id . '/' . $user_id . '/' . $company_id)}}" class="sign"><i class="fa fa-eye" style="font-size:24px"></i></a></td>
                                     <?php $i++;  ?>

                                     </tr>   

                                     @endforeach

                                </tbody>
                            </table>
                            <!-- ssdb table ends here -->
                           
                           
                          
                          
                            <div class="hr hr-18 dotted hr-double"></div>
                            <!-- <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('user')}}'"
                                            type="button">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div> -->
                        </form>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')

<script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>

    

<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-multiselect.min.js')}}"></script>	

    <script>
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
                                                 null,null,null,null,null,null,null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            "sScrollXInner": "120%",
                                            "bScrollCollapse": true,
                                            "iDisplayLength": 50,


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
        $(document).on('change', '#owner_tenant', function () {
            _current_val = $(this).val();
            // alert(_current_val);
            custom_location_data(_current_val);
            $('#append_tenant_part').html('');
          
            console.log(_current_val);
            if(_current_val == 2)
            {
                                
                template = '<div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"><div class="" ><label class="control-label no-padding-right" for="module">Owner name  <b style="color: red;">*</b></label><input required  placeholder="If Tenant then owner name" type="text" name="tenant_owner_name" id="tenant_owner_name" class="form-control input-sm"></div></div><div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"><div class="" ><label class="control-label no-padding-right" for="module"> Address (Owner)</label><textarea name="owner_address" id="owner_address" class="form-control input-sm"></textarea></div></div><div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"><div class="" ><label class="control-label no-padding-right" for="module">Owner Contact  <b style="color: red;">*</b></label><input  required placeholder="Enter Contact Number" type="number" name="owner_number" id="owner_number" class="form-control input-sm"></div></div> ';
               
          
               
                $('#append_tenant_part').append(template);
            }
        });
        $('#timepicker1').timepicker({
                        minuteStep: 1,
                        showSeconds: true,
                        showMeridian: false,
                        disableFocus: true,
                        icons: {
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down'
                        }
                    }).on('focus', function() {
                        $('#timepicker1').timepicker('showWidget');
                    }).next().on(ace.click_event, function(){
                        $(this).prev().focus();
                    });
    </script>
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
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#user_image')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#residence_since").datetimepicker  ( {

    format: 'YYYY-MM'
    });
    $("#sticker_date").datetimepicker  ( {

    format: 'YYYY-MM-DD'
    });
    </script>
     <script>


        $(document).on('change', '#mobile', function () {
            _current_val = $(this).val();
            custom_location_data(_current_val);
        });

       

        function custom_location_data(val) {
            _append_box = $('#appen_data_validation');
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/check_validation_user_by_number',
                    dataType: 'json',
                    data: "id=" + val,
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                            //Location 3
                            if(data.result == 0)
                            {
                                template = 'Mobile No already Saved !!  Please contact SERWA office for Updation';
                           
                                _append_box.empty();
                                $('#mobile').val('');
                                _append_box.append(template);
                            }
                            else
                            {
                                template = '';
                           
                                _append_box.empty();
                                _append_box.append(template);
                            }
                            

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }
            else{
                _append_box.empty();
            }
        }

        $('.vnumerror').keyup(function()
        {
            var yourInput = $(this).val();
            re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(yourInput);
            if(isSplChar)
            {
                var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });

    </script>
@endsection