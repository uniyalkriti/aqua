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
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />

@endsection

@section('body')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('retailer')}}">{{Lang::get('common.retailer_detail')}}</a>
                    </li>
                    <li class="active"> {{Lang::get('common.retailer')}} {{Lang::get('common.dashboard')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">

                        {{-- <div class="hr dotted"></div>

                        <div>
                            
                        </div> --}}


                        <form action="{{$id}}" method="get"> 
                                <div class="row">
                               
                                     <div class="col-xs-6 col-sm-6 col-lg-3">
                                                        
                                            <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>
                                                       
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar bigger-110"></i>
                                                </span>

                                                <input class="form-control input-sm" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                            </div>     
                                    </div>


                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <div class="">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                                    style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                Find
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="hr dotted"></div>

                     <!--    <div class="show">
                            <div id="user-profile-2" class="user-profile">
                                <div class="tabbable">
                                <form class="form-horizontal open collapse in" action="" method="GET" id="distributor_dashboard" role="form" enctype="multipart/form-data">
                                        {!! csrf_field() !!}
                                    <ul class="nav nav-tabs padding-18">
                                        <li class="active">
                                            <a  href="{{url('retailer/'.$id)}}">
                                                <i class="green ace-icon fa fa-calendar bigger-120"></i>
                                                Today
                                            </a>
                                        </li>
                                    </ul>
                                </form>
                               
                            </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-xs-12 col-sm-3 center">
                                <div>
                                    <span class="profile-picture">
                                          <img id="user_image" style="height: 80px;" class="editable img-responsive"  src="http://bambinoagro.msell.in/bambinoagro_api/webservices/mobile_images/Retailer/{{!empty($RetailerData->image_name)?$RetailerData->image_name:''}}" onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}';" />
                                    </span>

                                    <div class="space-4"></div>

                                    <div class="width-100 label label-info label-xlg arrowed-in arrowed-in-right">
                                        <div class="inline position-relative">
                                                <i class="ace-icon fa fa-circle light-green"></i>
                                                &nbsp;
                                        <span class="white">{{$RetailerData->name}}</span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="space-6"></div>
                            </div>
    
                            <div class="col-xs-12 col-sm-9">
    
                            <div class="col-md-6">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.retailer')}} Name</div>
                                        <div class="profile-info-value">
                                        <span class="editable" id="username">{{!empty($RetailerData->name)?$RetailerData->name:''}}</span>
                                        </div>
                                    </div>

                                    

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.email')}} </div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="age">
                                               
                                                {{!empty($RetailerData->email)?$RetailerData->email:'N/A'}}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">{{Lang::get('common.distributor')}} Name</div>
                                        <div class="profile-info-value">
                                            <span class="editable" id="signup">{{!empty($RetailerData->dealer_name)?$RetailerData->dealer_name:'N/A'}}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.user_contact')}}/{{Lang::get('common.landline')}} </div>
                                        <div class="profile-info-value">
                                            <span class="editable" id="signup">{{!empty($RetailerData->mobile)?$RetailerData->mobile:$RetailerData->landline}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.retailer')}} Code </div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="signup">{{!empty($RetailerData->retailer_code)?$RetailerData->retailer_code:'N/A'}}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Tin No. </div>
                                        <div class="profile-info-value">
                                            <i class="fa fa-star light-orange bigger-110"></i>
                                            <span class="editable" id="about">{{!empty($RetailerData->gst_in_no)?$RetailerData->gst_in_no:'N/A'}}</span>
                                        </div>
                                    </div> 
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.location3')}} </div>
                                        <div class="profile-info-value">
                                            <span class="editable" id="city">{{!empty($RetailerData->l3_name)?$RetailerData->l3_name:'N/A'}}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.retailer_type')}} </div>
                                        <div class="profile-info-value">
                                            <span class="editable" id="city">{{!empty($RetailerData->outlet_type_name)?$RetailerData->outlet_type_name:'N/A'}}</span>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                        </div>
                        </div>


                        <div class="row">
                                <div class="col-sm-6 infobox-container" style="margin-left:250px">
                                    <div class="space-6"></div>
                                    <div class="infobox infobox-green infobox-large infobox-dark">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-phone"></i>
                                            </div>
    
                                            <div class="infobox-data">
                                                <div class="infobox-data-number">{{$lastVistDate}}</div>
                                            <div class="infobox-content">Last visited</div>
                                            </div>
                                        </div>

                                    <div class="infobox infobox-pink infobox-large infobox-dark">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-rupee"></i>
                                            </div>
    
                                            <div class="infobox-data">
                                                <div class="infobox-data-number">{{$total_sale_value}}</div>
                                                <div class="infobox-content">
                                                <a title="Secondary Sales" from_date="{{ $from_date }}" to_date="{{ $to_date }}" retailer_id="{{ $dashboard_retailer_id }}" data-toggle="modal" data-target="#secondarySaleModal" class="user-modal secondarySaleModal">
                                                 <font color="black">
                                                {{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}
                                                </font>
                                                </a>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="infobox infobox-blue infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-rupee"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalSku}}</div>
                                            <div class="infobox-content">
                                            <a title="SKU Details" from_date="{{ $from_date }}" to_date="{{ $to_date }}" retailer_id="{{ $dashboard_retailer_id }}" data-toggle="modal" data-target="#skuDetailModal" class="user-modal skuDetailModal">
                                             <font color="black">
                                            {{Lang::get('common.total')}} {{Lang::get('common.catalog_4')}}
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-orange infobox-large infobox-dark">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-rupee"></i>
                                            </div>
    
                                            <div class="infobox-data">
                                                <div class="infobox-data-number">{{$total_sale_value}}</div>
                                                <div class="infobox-content">
                                                <a title="Secondary Sales" from_date="{{ $from_date }}" to_date="{{ $to_date }}" retailer_id="{{ $dashboard_retailer_id }}" data-toggle="modal" data-target="#secondarySaleDateSKUModal" class="user-modal secondarySaleDateSKUModal">
                                                 <font color="black">
                                                Date Wise SKU Sale
                                                </font>
                                                </a>
                                                </div>
                                            </div>
                                    </div>



                                </div>
       
                            </div><!-- /.row -->

                            <div class="hr dotted"></div>
                            <div class="row">
                                <div class="col-md-8">
                                <div class="panel-group">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <i class="fa fa-bookmark"> Sale Stats -  <?=date("M")?></i>
                                            <h5 class="widget-title lighter pull-right">
                                                    <i class="ace-icon fa fa-circle blue"></i>
                                                    Sale Stats
                                            </h5>
                                        </div>
                                        <canvas id="BarChart" height="100" class="img-responsive" ></canvas>
                       
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="panel-group">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    <i class="fa fa-bookmark green">  </i> 
                                                    {{Lang::get('common.stock')}} Qty Category Wise
                                                </div>
                                                
                                                <div id="piechart-placeholder"></div>
                                            </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="panel-group">
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <i class="fa fa-bookmark red">  </i> {{Lang::get('common.stock')}} Item Less Than Threshold
                                            </div>
                                        
                                            <marquee behavior="scroll" direction="up" scrolldelay="300">
                                            <ul id="tasks" class="item-list">
                                                        
                                                    @foreach($thresholdItem as $tkey=>$thval)
                                                    <li class="item-orange clearfix">
                                                        <label class="inline">
                                                        <span class="lbl">{{$thval->product_name}}</span>
                                                        </label>

                                                        <div class="pull-right easy-pie-chart percentage">
                                                                <i class="ace-icon"></i>
                                                            <span class="percent" style="color:{{$thval->color_code}}">{{$thval->stockQty}}</span>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </marquee>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->



<!-- Modal here for user detals -->
<div class="modal fade" id="secondarySaleDateSKUModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <a onclick="fnExcelReport('secondarySaleDateSKUTable')" href="javascript:void(0)" class="nav-link">
                  <i class="fa fa-file-excel-o"></i> Export Excel</a>
                <h4 class="modal-title" >Secondary Sales Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id="secondarySaleDateSKUTable">
                    <thead>
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>SKU</th>
                        <th>Qty</th>
                        <th>Sale Value</th>
                    </thead>
                    <tbody class="mytbody_secondarySaleDateSKUModal">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->





<!-- Modal here for user detals -->
<div class="modal fade" id="secondarySaleModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <a onclick="fnExcelReport('secondarySaleTable')" href="javascript:void(0)" class="nav-link">
                  <i class="fa fa-file-excel-o"></i> Export Excel</a>
                <h4 class="modal-title" >Secondary Sales Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id="secondarySaleTable">
                    <thead>
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>Sale Value</th>
                    </thead>
                    <tbody class="mytbody_secondarySaleModal">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->
    


<!-- Modal here for user detals -->
<div class="modal fade" id="skuDetailModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <a onclick="fnExcelReport('skuDetailTable')" href="javascript:void(0)" class="nav-link">
                  <i class="fa fa-file-excel-o"></i> Export Excel</a>
                <h4 class="modal-title" >SKU Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id="skuDetailTable">
                    <thead>
                        <th>Sr.no</th>
                        <th>SKU Name</th>
                        <th>Sale Value</th>
                    </thead>
                    <tbody class="mytbody_skuDetailModal">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->
    

  
@endsection

@section('js')

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('js/user.js')}}"></script>
    <script src="{{asset('nice/js/BarChart.js')}}"></script>
   
   
        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="{{asset('nice/js/jquery.easypiechart.min.js')}}"></script>

    <script src="{{asset('nice/js/jquery.sparkline.index.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.flot.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.flot.resize.min.js')}}"></script>

            <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>  
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>

     <script type="text/javascript">
        $('.skuDetailModal').click(function() {
          var retailer_id = $(this).attr('retailer_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
        $('.mytbody_skuDetailModal').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getRetailerSkuDetails',
                dataType: 'json',
                data: "retailer_id=" + retailer_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {
                    if (data.code == 401) {
                    }
                    else if (data.code == 200) {
                               var Sno = 1;
                               var sum = 0;
                            $.each(data.user_details, function (key, value){
                                sum += parseFloat(value.sale);
                                 $('.mytbody_skuDetailModal').append("<tr><td>"+Sno+"</td><td>"+value.product_name+"</td><td>"+value.sale+"</td></tr>");
                                Sno++;
                            });
                                 $('.mytbody_skuDetailModal').append("<tr><td colspan = '2'> Total </td><td>"+Math.round(sum)+"</td></tr>");
                     }      
                },
                complete: function () {
                },
                error: function () {
                }
            });
    });
    </script>

      <script type="text/javascript">
        $('.secondarySaleModal').click(function() {
          var retailer_id = $(this).attr('retailer_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
        $('.mytbody_secondarySaleModal').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getRetailerSecondarySales',
                dataType: 'json',
                data: "retailer_id=" + retailer_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {
                    if (data.code == 401) {
                    }
                    else if (data.code == 200) {
                               var Sno = 1;
                               var sum = 0;
                            $.each(data.user_details, function (key, value){
                                sum += parseFloat(value.sale);
                                 $('.mytbody_secondarySaleModal').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.sale+"</td></tr>");
                                Sno++;
                            });
                                 $('.mytbody_secondarySaleModal').append("<tr><td colspan = '2'> Total </td><td>"+Math.round(sum)+"</td></tr>");
                     }      
                },
                complete: function () {
                },
                error: function () {
                }
            });
    });
    </script>


<script type="text/javascript">
        $('.secondarySaleDateSKUModal').click(function() {
          var retailer_id = $(this).attr('retailer_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
        $('.mytbody_secondarySaleDateSKUModal').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getRetailerSecondarySalesDateSku',
                dataType: 'json',
                data: "retailer_id=" + retailer_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {
                    if (data.code == 401) {
                    }
                    else if (data.code == 200) {
                               var Sno = 1;
                               var sum = 0;
                               var sumqty = 0;
                            $.each(data.user_details, function (key, value){
                                sum += parseFloat(value.sale);
                                sumqty += parseFloat(value.quantity);
                                 $('.mytbody_secondarySaleDateSKUModal').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.product_name+"</td><td>"+value.quantity+"</td><td>"+value.sale+"</td></tr>");
                                Sno++;
                            });
                                 $('.mytbody_secondarySaleDateSKUModal').append("<tr><td colspan = '3'> Total </td><td>"+Math.round(sumqty)+"</td><td>"+Math.round(sum)+"</td></tr>");
                     }      
                },
                complete: function () {
                },
                error: function () {
                }
            });
    });
    </script>


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




        var barChartData = {
    
            labels:<?=json_encode($datesArr)?> ,
            
    datasets: [
        {
            //SET COLORS BELOW
            fillColor: "rgba(76,194,88,0.5)",
            strokeColor: "rgba(76,194,88,0.8)",
            highlightFill: "rgba(76,194,88,0.75)",
            highlightStroke: "rgba(76,194,88,1)",
            data:<?=json_encode($totalOrderValueArr)?> 
            // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
        },

    ]

}

window.onload = function () {
    var ctx = document.getElementById("BarChart").getContext("2d");
    window.myLine = new Chart(ctx).Bar(barChartData, {
        responsive: true
    });
};

    </script>


    <script type="text/javascript">
            jQuery(function($) {
              //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
              //but sometimes it brings up errors with normal resize event handlers
              $.resize.throttleWindow = false;
            
              var placeholder = $('#piechart-placeholder').css({'width':'70%' , 'min-height':'230px'});
              var data = <?=json_encode($stockCategoryWise)?>
            
              function drawPieChart(placeholder, data, position) {
                  $.plot(placeholder, data, {
                    series: {
                        pie: {
                            show: true,
                            tilt:0.8,
                            highlight: {
                                opacity: 0.25
                            },
                            stroke: {
                                color: '#fff',
                                width: 2
                            },
                            startAngle: 2
                        }
                    },
                    legend: {
                        show: true,
                        position: position || "ne", 
                        labelBoxBorderColor: null,
                        margin:[-100,10]
                    }
                    ,
                    grid: {
                        hoverable: true,
                        clickable: true
                    }
                 })
             }
             drawPieChart(placeholder, data);
            
             /**
             we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
             so that's not needed actually.
             */
             placeholder.data('chart', data);
             placeholder.data('draw', drawPieChart);

            })
        </script>

        <script type="text/javascript">
function fnExcelReport(table_id) {    
    var tab_id = table_id;
    if(tab_id == "secondarySaleTable"){
    var filename = "Secondary Sale Details";
    }
    if(tab_id == "skuDetailTable"){
    var filename = "SKU Details";
    }
   

    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById(tab_id); // id of table
    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
    }
    tab_text = tab_text + "</table>";
    var a = document.createElement('a');
    var data_type = 'data:application/vnd.ms-excel';
    a.href = data_type + ', ' + encodeURIComponent(tab_text);
    a.download = filename + '.xls';
    a.click();
}
        </script>
@endsection
