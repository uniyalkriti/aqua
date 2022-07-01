@extends('layouts.retailer_dashbord')

@section('title')
    <title>{{Lang::get('common.retailer_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <style>
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            background-color: #7BB0FF !important;
            color: black;
        }
    </style>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            @if(Auth::user()->is_admin==1)
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('retailer')}}">{{Lang::get('common.retailer_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.retailer')}} {{Lang::get('common.order_booking')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>
            @else
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('user_public_data')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('user_public_data')}}">{{Lang::get('common.retailer_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.retailer')}} {{Lang::get('common.order_booking')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>
            @endif
            <div class="page-content">
                <div class="row">
                <form id="month_form" method="get">
                        <div class="col-sm-2">
                            <div class="center">
                                    <input data-date-format="YYYY-MM-DD" value="{{ !empty(Request::get('start_date'))?date('Y-m-d',strtotime(Request::get('start_date'))):date('Y-m-d') }}" type="text" class="form-control input-sm date-picker" name="start_date" id="start_date">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="center">
                                    <input data-date-format="YYYY-MM-DD" value="{{ !empty(Request::get('end_date'))?date('Y-m-d',strtotime(Request::get('end_date'))):date('Y-m-d') }}" type="text" class="form-control input-sm date-picker" name="end_date" id="end_date">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-sm btn-primary">{{Lang::get('common.find')}}</button>
                        </div>
                        </form>
                        </div>
                    <div class="row">

 <div class="clearfix">
<div class="pull-right tableTools-container"></div>
</div>
    <table id="dynamic-table" class="table table-bordered table-hover">
        <thead>
    <tr>
        <th class="center">{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.date')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.distributor')}} Name</th>
        <th>{{Lang::get('common.location7')}}</th>
        <th>{{Lang::get('common.retailer')}} Name</th>
        <th>{{Lang::get('common.order_id')}}</th>
        <th>Order Book Difference Time</th>
        <th>Call {{Lang::get('common.status')}}</th>
        <th>{{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th>
        <th>Lat Lng</th>
        <th>{{Lang::get('common.address')}}</th>
        <th>{{Lang::get('common.details')}}</th>
      

        

    </tr>
    </thead>
    <tbody>
   <?php 
   $inc=0;
   
   ?>
    @if(!empty($sale_data))
        @foreach($sale_data as $k=>$data)
             <?php 
             $encid = Crypt::encryptString($data->user_id);
             $dencid = Crypt::encryptString($data->dealer_id);
             $rencid = Crypt::encryptString($data->retailer_id);

             // dd($diffrence);
                if($inc>0)
                {

                $first_date = $diffrence[$inc-1]->time;
                $last_date = $diffrence[$inc]->time;   


                // Declare and define two dates 
                $date1 = strtotime($first_date);
                $date2 = strtotime($last_date); 


                // Formulate the Difference between two dates 
                $diff = abs($date2 - $date1); 

                $years = floor($diff / (365*60*60*24));  
                  
                  
                // To get the month, subtract it with years and 
                // divide the resultant date into 
                // total seconds in a month (30*60*60*24) 
                $months = floor(($diff - $years * 365*60*60*24) 
                                               / (30*60*60*24));  
                  
                  
                // To get the day, subtract it with years and  
                // months and divide the resultant date into 
                // total seconds in a days (60*60*24) 
                $days = floor(($diff - $years * 365*60*60*24 -  
                             $months*30*60*60*24)/ (60*60*24)); 
                  

                // To get the hour, subtract it with years, 
                // months & seconds and divide the resultant 
                // date into total seconds in a hours (60*60) 
                $hours = floor(($diff - $years * 365*60*60*24 
                    - $months*30*60*60*24 - $days*60*60*24) 
                                                / (60*60)); 


                // To get the minutes, subtract it with years, 
                // months, seconds and hours and divide the 
                // resultant date into total seconds i.e. 60 
                $minutes = floor(($diff - $years * 365*60*60*24 
                        - $months*30*60*60*24 - $days*60*60*24 
                                        - $hours*60*60)/ 60); 


                }
                else
                {
                    $hours = 0;
                    $minutes =0;
                }

                // dd($last_date);

?> 

            <tr>
                <td>{{$k+1}}</td>
                <td>{{!empty($data->dates)?$data->dates.' '.$data->time:'N/A'}}</td>
                <td>{{!empty($data->l3_name)?$data->l3_name:'N/A'}}</td>
                <td>
                <a href="{{url('distributor/'.$dencid)}}" >
                {{!empty($data->dealer_name)?$data->dealer_name:'N/A'}}
                </a>
                </td>
                <td>{{!empty($data->l5_name)?$data->l5_name:'N/A'}}</td>
                <td>
                <a href="{{url('retailer/'.$rencid)}}" >

                {{!empty($data->retailer_name)?$data->retailer_name:'N/A'}}
                </a>
                </td>
                <td>{{!empty($data->order_id)?$data->order_id:'N/A'}}</td>
               

                @if($minutes>=10)
                 <td style="color:green;">
                <strong>{{$hours.' Hours '.$minutes.' Minutes'}}</strong>
                </td>
                @else
                <td style="color:red;">
                {{$hours.' Hours '.$minutes.' Minutes'}}
                </td>
                @endif
                <td>{{$data->call_status==1?'Productive':'Non-Productive'}}</td>
                <td>{{!empty($data->total_sale_value)?$data->total_sale_value:'N/A'}}</td>
                <td><i class="menu-icon fa fa-map-marker"></i>


                <a href="{{url('user_tracking/'.$encid.'?date='.$data->date.'&order_id='.$data->order_id)}}">{{!empty($data->lat_lng)?$data->lat_lng:'N/A'}}</a>
                </td>
                <td>{{!empty($data->track_address)?$data->track_address:'N/A'}}</td>
                
                <td>
                    <button class="btn btn-info btn-sm"type='button' data-toggle="modal" data-target="#myModal{{$data->order_id}}" >Details</button> 
                </td>
              
       </tr>
       <!-- Modal START -->
        <div class="modal fade" id="myModal{{$data->order_id}}" role="dialog">
            <div class="modal-dialog" style="width:800px;">
      
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title" align="center" >{{Lang::get('common.catalog_4')}} Wise Details </h3>
                    </div>
                      <?php $total=0;
                                $totalqty=0;
                                $totalweight=0; 
                                ?>
                    <div class="modal-body"> 
                        <div class="row heading" style="border: 1px solid;">
                            <div class="col-md-5">{{Lang::get('common.catalog_4')}} Name</div>
                                

                            <div class="col-md-2">Quantity</div>
                            <div class="col-md-2">{{Lang::get('common.rate')}}</div>
                            <div class="col-md-2">Value</div>

                        </div>
                           
                            @if(!empty($details[$data->order_id]))

                                @foreach($details[$data->order_id] as $k1=>$data1)
                                 
                                  
                                  <?php  
                                    $total+=$data1->rate*$data1->quantity;
                                    $totalqty+=$data1->quantity;
                                   ?>
                                    <div class="row" style="border: 1px solid;">
                                        <div class="col-md-5">{{!empty($data1->product_name)?$data1->product_name:'N/A'}}</div>
                                            

                                        <div class="col-md-2">{{!empty($data1->quantity)?$data1->quantity:'0'}}</div>
                                        <div class="col-md-2">{{!empty($data1->rate)?$data1->rate:'0'}}</div>
                                        <div class="col-md-2">{{($data1->rate*$data1->quantity)}}</div>


                                    </div>
                                            
                                @endforeach
                              @endif
                            <div class="row"style="border: 1px solid;" >
                                <div class="col-md-5">{{Lang::get('common.total')}}</div>
                                <div class="col-md-4">{{($totalqty)}}</div>
                              

                                <div  class="col-md-1"> {{($total)}}</div>
                            
                            </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- modal ends here  -->
        <?php  $inc++;
       
?>
    @endforeach
    @endif
       
    </tbody>
</table>
        </div>
    </div>
        </div>
    </div>
@endsection

@section('js')

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.select.min.js')}}"></script>
    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/pdfmake.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>
    <script src="{{asset('js/user.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
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
        jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": true},
                            null,   null, null, null, null,null,null,null,null,null,null,
                            {"bSortable": false}
                        ],
                        "aaSorting": [],


                        //"bProcessing": true,
                        //"bServerSide": true,
                        //"sAjaxSource": "http://127.0.0.1/table.php"   ,

                        //,
                        //"sScrollY": "200px",
                        //"bPaginate": false,

                        //"sScrollX": "100%",
                        //"sScrollXInner": "120%",
                        //"bScrollCollapse": true,
                        //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                        //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                        //"iDisplayLength": 50


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
                    if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);


            myTable.on('select', function (e, dt, type, index) {
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
                    if (th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
            });


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
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if ($row.is('.detail-row ')) return;
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
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

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }



            /**
             //add horizontal scrollbars to a simple table
             $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
             {
               horizontal: true,
               styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
               size: 2000,
               mouseWheelLock: true
             }
             ).css('padding-top', '12px');
             */


        })
    </script>
@endsection
