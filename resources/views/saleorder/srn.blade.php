@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />

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
    #simple-table td {
        height: 30px;
    }
</style>
<style>

</style>
@endsection

@section('body')
 <form class="form-horizontal open collapse in"  method="get" id="manualAttandence" enctype="multipart/form-data">

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="{{url('home')}}">Dashboard</a>
                    </li>

                    <li class="active" style="color: white">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->
            </div> <!-- /.nav-search -->

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    
                                    <input type="hidden" name="flag" value="1">

                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location3')}}</label>
                                                <select  name="location_3" id="location_3" class="form-control chosen-select">
                                                    <option value="">select</option>
                                                    @if(!empty($location3))
                                                        @foreach($location3 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.distributor')}}</label>
                                                <select  name="dealer" id="dealer" required class="form-control chosen-select">
                                                    <option value="">select</option>
                                                    @if(!empty($distributor))
                                                        @foreach($distributor as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>       
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar bigger-110"></i> 
                                                </span>

                                                <input class="form-control" {{ Request::get('date_range_picker')?'selected':''}} type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                            </div>
                                        </div>
                               
                                
                                 <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                    </button>
                                </div>
                                <br>
                                <br>
                                <br>
                                <br>
                             </form>
                        <div class="hr hr-18 dotted hr-double"></div>

                        @if(!empty($records))
<!-- ......................table contents........................................... -->
                                <div class="main-container ace-save-state" id="main-container">
                                    <div class="main-content">
                                        <div class="main-content-inner">
                                            <div class="page-content">
                                            <!--  -->
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="clearfix">
                                                            <div class="pull-right tableTools-container"></div>
                                                        </div>
                                                        <!-- div.table-responsive -->
                                                        <!-- div.dataTables_borderWrap -->
                                                            <div>
                                                                <table id="dynamic-table" class="table table-striped table-bordered table-hover" >
                                                                    <thead>
                                                                    <tr>
                                                                        <th>S.No.</th>
                                                                        <th>Action</th>
                                                                        <th>Distributor Name</th>
                                                                        <th>Distributor Mobile No.</th>
                                                                        <th>Order Date</th> 
                                                                        <th>Order No</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Invoice Date</th>
                                                                        <th>Order Value</th>
                                                                        <th>Order Cases</th>
                                                                        <th>Order Scheme Qty</th>

                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $value12 = array(); ?>

                                                                    @if(!empty($records) && count((array)$records)>0)
                                                                    
                                                                    @foreach($records as $k=> $r)
                                                                    @if(count((array)$r->order_id)>0)
                                                                   
                                                                        <?php
                                                                        $encid = Crypt::encryptString($r->order_id); 
                                                                        $dealer_id = Crypt::encryptString($r->dealer_id); 
                                                                        ?>
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                           <td>
                                                                            <a title="Assign Rate List"  onclick="confirmAction('Action','IMEI','{{$r->order_id}}','product_rate_list_template','clear');">
                                                                                <button type="button" class="btn btn-default btn-round btn-white">
                                                                                    <i class="ace-icon fa fa-send green"></i>
                                                                                    Action 
                                                                                </button>
                                                                            </a>
                                                                            </td>
                                                                            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
                                                                            <td>{{$r->mobile_number}}</td>
                                                                            <td>{{$r->sale_date}}</td>
                                                                            <td>{{$r->order_id}}</td>
                                                                            <td>{{$r->invoice_no}}</td>
                                                                            <td>{{$r->invoice_date}}</td>
                                                                            <td>{{$r->order_value}}</td>
                                                                            <td>{{$r->cases}}</td>
                                                                            <td>{{$r->scheme_qty}}</td>
                                                                
                                                                                
                                                                            </tr>
                                                                            @endif
                                                                            <?php $i++; ?>
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
                        @endif
                            </div><!-- /.span -->



                        </div><!-- /.row -->

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>
</form>
@endsection

@section('js')

     <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/page/report90.js')}}"></script>
    <script src="{{asset('msell/page/print.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>


    <script src="{{asset('msell/js/common.js')}}"></script>
    <!-- Modal -->
    <div class="modal fade" id="dms_sale_return" role="dialog">
        <div class="modal-dialog" style="width:1200px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_sale_return" method="post" id="dms_sale_return_form" enctype="multipart/form-data">
                        <input type="hidden" id="dealer_id_return" name="dealer_id" value="">
                        <input type="hidden" id="order_id_return" name="order_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:#fcf8e3; color:black; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:#fcf8e3; color:black; width:560px; height: 30px; text-align:right;"> Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <br>
                        <div class="table-header center">
                            Sales Return
                           
                        </div>
                        <table  class="table table-bordered table-hover">
                        
                           <thead class = "mythead">

                            <tr> 
                               <th rowspan="2">Sr.no</th>
                                <th colspan="6">Order Details</th>
                                <th colspan="4">Return Details</th>
                            </tr>                            
                                <tr>
                                    <th>{{Lang::get('common.distributor')}} Code</th>
                                    <th>{{Lang::get('common.distributor')}}</th>
                                    <th>Invoice No.</th>   
                                    <th>Order Value</th>
                                    <th>Cases</th>
                                    <th>Scheme</th>


                                    <th>Cases</th>
                                    <th>Scheme</th>
                                    <th>Value</th>
                                    <th>Remarks</th>   
                                </tr>
                            </thead>

                            <tbody class="mytbody_sale_return">

                            </tbody>
                        </table>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit_return1">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                                <div class="col-lg-3" id="submit_return">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="dms_damage_modal" role="dialog">
        <div class="modal-dialog" style="width:1200px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_dms_damge" method="post" id="dms_damge_form" enctype="multipart/form-data">
                        <input type="hidden" id="dealer_id_damage" name="dealer_id" value="">
                        <input type="hidden" id="order_id_damage" name="order_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:#fcf8e3; color:black; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:#fcf8e3; color:black; width:560px; height: 30px; text-align:right;"> Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <br>
                        <div class="table-header center">
                            Damage
                           
                        </div>
                        <table  class="table table-bordered table-hover">
                        
                           <thead class = "mythead">

                            <tr> 
                               <th rowspan="2">Sr.no</th>
                                <th colspan="6">Order Details</th>
                                <th colspan="3">Damage Details</th>
                            </tr>                            
                                <tr>
                                    <th>{{Lang::get('common.distributor')}} Code</th>
                                    <th>{{Lang::get('common.distributor')}}</th>
                                    <th>Invoice No.</th>   
                                    <th>Order Value</th>
                                    <th>Cases</th>
                                    <th>Scheme</th>


                                    <th>Cases</th>
                                    <th>Value</th>
                                    <th>Remarks</th>   
                                </tr>
                            </thead>

                            <tbody class="mytbody_damage">

                            </tbody>
                        </table>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit_damage1">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                                <div class="col-lg-3" id="submit_damage">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>

        function confirmAction(heading, name, action_id, tab, act) {
            // alert(action_id);
            $.confirm({
                title: heading,
                buttons: {
                     SalesReturn: function () {
                       $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_srn_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data) 
                            {
                                $('.mytbody_sale_return').html('');
                                $('#dealer_id_return').html('');
                                $('#order_id_return').html('');

                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    if(data.sale_return_status == 1)
                                    {
                                        $.alert('Sale Return Already Filled For this order!!');
                                        $('#dms_sale_return').modal('toggle');
                                    }
                                    var inc = 1;
                                    var template = '';
                                    $('#dealer_id_return').val(data.dealer_id);
                                    $('#order_id_return').val(data.order_id);
                                    $.each(data.result_down, function (u_key, u_value) {
                                        
                                        template += ('<tr><td>'+inc+'</td><td>'+u_value.dealer_code+'</td><td>'+u_value.dealer_name+'</td><td>'+u_value.invoice_no+'</td><td>'+u_value.order_value+'</td><td>'+u_value.cases+'</td><td>'+u_value.scheme_qty+'</td><td><input style="width:90px;" type="text" name="cases_return[]" id='+"cases_return"+inc+' onkeyup="return mulfunc(this.id)" value="0" autocomplete="off"></td><td><input style="width:90px;" type="text" name="scheme_return[]"  value="0" autocomplete="off"></td><td><input id='+"total_value"+inc+' style="width:90px;" type="text" value="0" readonly autocomplete="off"></td><td><input style="width:90px;" type="text" name="remarks_return[]" value="NA" autocomplete="off"></td></tr><input type="hidden" id='+"case_rate"+inc+' name="case_rate[]" value='+u_value.product_case_rate+'>');
                                        inc++;
                                    });   
                                    $('.mytbody_sale_return').append(template);
                                }
                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        $("#dms_sale_return").modal();

                        
                    },
                    Damage: function () {
                       $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_srn_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data) 
                            {
                                $('.mytbody_damage').html('');
                                $('#dealer_id_damage').html('');
                                $('#order_id_damage').html('');

                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    if(data.damage_status == 1)
                                    {
                                        $.alert('Damge Already Filled For this order!!');
                                        $('#dms_damage_modal').modal('toggle');
                                    }
                                    var inc = 1;
                                    var template = '';
                                    $('#dealer_id_damage').val(data.dealer_id);
                                    $('#order_id_damage').val(data.order_id);
                                    $.each(data.result_down, function (u_key, u_value) {
                                        
                                        template += ('<tr><td>'+inc+'</td><td>'+u_value.dealer_code+'</td><td>'+u_value.dealer_name+'</td><td>'+u_value.invoice_no+'</td><td>'+u_value.order_value+'</td><td>'+u_value.cases+'</td><td>'+u_value.scheme_qty+'</td><td><input style="width:90px;" type="text" name="cases_return[]" id='+"cases_damge"+inc+' onkeyup="return mulfuncDamage(this.id)" value="0" autocomplete="off"></td><td><input id='+"total_value_damage"+inc+' style="width:90px;" type="text" value="0" readonly autocomplete="off"></td><td><input style="width:90px;" type="text" name="remarks_return[]" value="NA" autocomplete="off"></td></tr><input type="hidden" id='+"cases_rate_damage"+inc+' name="case_rate[]" value='+u_value.product_case_rate+'>');
                                        inc++;
                                    });   
                                    $('.mytbody_damage').append(template);
                                }
                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        $("#dms_damage_modal").modal();
                        
                    },
                    
                    Cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function mulfunc(str2)
        {
            var d=str2.substr(12,3);
            var x= document.getElementById("cases_return"+d).value;
            var z= document.getElementById("case_rate"+d).value;
            var toatl_amount = x*z;
            // console.log(toatl_amount);
            document.getElementById("total_value"+d).value= toatl_amount;


   
        }
        function mulfuncDamage(str2)
        {
            var d=str2.substr(11,3);
            var x= document.getElementById("cases_damge"+d).value;
            var z= document.getElementById("cases_rate_damage"+d).value;
            var toatl_amount = x*z;
            // console.log(toatl_amount);
            document.getElementById("total_value_damage"+d).value= toatl_amount;


   
        }
        $("#dms_sale_return_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            // $('#submit_return').html('');
            $('#submit_return').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $.alert('Sales Return Submitted SuccessFully !!');
                    $('#dms_sale_return').modal('toggle');
                   
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function (data) {
                    $.alert('Somthing went wrong Error-code: '+data.status+' !!');
                    $('#dms_sale_return').modal('toggle');
                    $('#m-spinner').remove();
                }

            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
        $("#dms_damge_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            // $('#submit_damage').html('');
            $('#submit_damage').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $.alert('Damage Submitted SuccessFully !!');
                    $('#dms_damage_modal').modal('toggle');
                   
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function (data) {
                    $.alert('Somthing went wrong Error-code: '+data.status+' !!');
                    $('#dms_damage_modal').modal('toggle');
                    $('#m-spinner').remove();
                }

            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
       
    </script>
   
    <script>
            
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
                      null, null,null,null,null, null,null,null,null,
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
        
    $(document).on('click', '#print_button', function () {
        
   
      
            $("#modalDiv").printThis({ 
            debug: false,              
            importCSS: true,             
            importStyle: true,         
            printContainer: true,       
            loadCSS: "../css/style.css", 
            pageTitle: "My Modal",             
            removeInline: false,        
            printDelay: 333,            
            header: null,             
            formValues: true          
             }); 
 });
        
        </script>
@endsection