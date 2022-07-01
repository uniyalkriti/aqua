@extends('layouts.core_php_heade')

@section('dms_body')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="{{url('dms_dealer_dashboard')}}">Home</a>
                    </li>

                    <li class="active" style="color: black;">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->
            </div><!-- /.nav-search -->
            <div class="page-content">
                <form class="form-horizontal open collapse in" action="" method="GET" id="user-search" role="form"
                                          enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-xs-12">
                            @if($role_id == 37 || $role_id == 1)
                            <div class="col-xs-3 col-sm-3">
                                <label class="control-label no-padding-right" for="name">Distributor</label>
                                <select multiple name="distributor[]" id="distributor" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($dealer_name))
                                        @foreach($dealer_name as $k=>$r)
                                         <?php if(empty($_GET['distributor']))
                                         $_GET['distributor']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['distributor'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            @endif
                            <div class="col-xs-3 col-sm-3">
                                <select name="perpage" id="perpage" class="form-control cursor" onchange="form.submit()" style="margin-top: 25px;">
                                    <option value="">Per Page</option>
                                    <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                    <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                    <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                    <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                </select>
                            </div>

                            <div class="col-xs-3 col-sm-3">
                                <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                    Find
                               </button> 
                            </div>

                            <div class="col-xs-3 col-sm-3">
                                <a href="{{url($current_menu.'/create')}}" class="form-control btn btn-sm btn-info" style="margin-top: 25px">
                                    <i class="fa fa-plus mg-r-10"></i> Add  {{Lang::get('common.'.$current_menu)}}
                                </a>
                            </div>
                        </div>
                    </div>
                </form><br>
                <div class="row">
                    <div class="pull-right tableTools-container"></div>
                    <div class="col-xs-12" style="overflow-x: scroll;">
                        

                        <div class="table-header center">
                          
                           
                        </div>
                        <table id="dynamic-table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="center" style="background-color: #90d781; color: black;">
                                        S.No.
                                    </th>
                                    <th style="background-color: #90d781; color: black;">Dealer Code</th>
                                    <th style="background-color: #90d781; color: black;">Dealer Name</th>
                                    <th style="background-color: #90d781; color: black;">Amount</th>
                                    <th style="background-color: #90d781; color: black;">Payment Mode</th>
                                    <th style="background-color: #90d781; color: black;">Deposit Bank Name</th>
                                    <th style="background-color: #90d781; color: black;">Transaction No</th>
                                    <th style="background-color: #90d781; color: black;">CH/DD Bank Details</th>
                                    <th style="background-color: #90d781; color: black;">Cheque/Draft No</th>
                                    <th style="background-color: #90d781; color: black;">Date</th>
                                    <th style="background-color: #90d781; color: black;">Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment_mast as $key=>$data)
                                    <tr>
                                        <?php $encid = Crypt::encryptString($data->id);?>
                                        <td class="center">
                                                {{ 1 + $key }}
                                        </td>
                                        <td>{{$data->dealer_code}}</td>
                                        <td>{{$data->dealer_name}}</td>
                                        <td>{{$data->amount}}</td>
                                        <td>{{$data->payment_mode}}</td>
                                        <td>{{substr($data->deposit_bank_name,0,15)}}</td>
                                        <td>{{$data->transaction_no}}</td>
                                        <td>{{substr($data->bank_detail,0,15)}}</td>
                                        <td>{{$data->cheque_draft_no}}</td>
                                        <td>{{$data->date}}</td>
                                        <td>{{$data->remark}}</td>    
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-xs-6">
                            <div class="dataTables_info">
                                Showing {{($payment_mast->currentpage()-1)*$payment_mast->perpage()+1}}
                                to {{(($payment_mast->currentpage()-1)*$payment_mast->perpage())+$payment_mast->count()}}
                                of {{$payment_mast->total()}} entries
                            </div>
                        </div>
                        <div class="col-xs-6">
                           <div class="dataTables_paginate paging_simple_numbers">
                           {{$payment_mast->appends(request()->except('page'))}}
                           </div>
                        </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>

  


    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('js/dealer.js')}}"></script> 
    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>


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


          $('.user-modal2').click(function() {
            var dealer_id = $(this).attr('userid');
            $('.mytbody').html('');
          
            if (dealer_id != '') 
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/get_dealer_person_details',
                    dataType: 'json',
                    data: "dealer_id=" + dealer_id,
                    success: function (data) 
                    {
                        $('#person_name2').val('');
                        $('#username2').val('');
                        $('#user_password2').val('');
                        $('#phone2').val('');
                        $('#email2').val('');
                        $('#email2').val('');
                        $('#state2').html('');
                        if (data.code == 401) 
                        {
                            
                        }
                        else if (data.code == 200) 
                        {
                            $('#person_name2').html('');
                            $('#username2').html('');
                            $('#user_password2').html('');
                            $('#phone2').html('');
                            $('#email2').html('');
                            $('#email2').html('');
                            
                            $('#person_name2').val(data.result.person_name);
                            $('#username2').val(data.result.uname);
                            $('#user_password2').val(data.result.person_password);
                            $('#phone2').val(data.result.phone);
                            $('#email2').val(data.result.email);
                            $('#state2').html('<option>'+data.result.l3_name+'<option>');
                            // $('#email2').val(data.result.pass);
                            
                            
                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
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
                                                {"bSortable": true},
                                                null,null,null,null,null,null,null,null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "1300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            "bScrollCollapse": true,
                                            "iDisplayLength": 1000000,


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



                                /************/
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




                                /*****/
                                $('.show-details-btn').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).closest('tr').next().toggleClass('open');
                                    $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
                                });
                                

                            })
        </script>
@endsection