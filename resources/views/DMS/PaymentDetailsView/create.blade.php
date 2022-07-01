@extends('layouts.core_php_heade')
  
@section('dms_body')
    <title>{{Lang::get('common.circular')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <style>
        .center {
          margin: auto;
          width: 100%;
        }
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            /background-color: #438EB9 !important;/
            background-color: #7BB0FF !important;
            color: black;
        }
        .bg-primary
        {
            background-color: #90d781;
        }
        .blue
        {
            color:#90d781; 
        }
        .widget-color-blue21
        {
            background-color:#90d781;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.circular')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#manualAttandence" class="btn btn-sm btn-default"><i
                        class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
                <!-- /.nav-search -->
            </div>
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" action="{{route($current_menu.'.store')}}" method="POST"
                              id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="main-container ace-save-state" id="main-container">
                                <div class="main-content">
                                    <div class="main-content-inner">
                                        <div class="page-content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="col-xs-12">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6">
                                                                <div class="search-area well well-sm">
                                                                    <div class="search-filter-header bg-primary">
                                                                        <h5 class="smaller no-margin-bottom" style="color: black; font-weight: bolder;">
                                                                            <i class="ace-icon fa fa-sliders black bigger-130"></i>&nbsp; Payment Details
                                                                        </h5>
                                                                    </div>
                                                                    <div class="hr hr-dotted"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-rupee"></i>
                                                                                Amount
                                                                            </h4>
                                                                            <input class="form-control" autocomplete="off" type="text" name="amount" required="">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-credit-card-alt"></i>
                                                                                Payment Mode
                                                                            </h4>
                                                                            <select class="form-control" name="payment_mode" class="form-control" id="category" value="{{ old('payment_mode') }}" required="">
                                                                                <option value="" selected>==Select Payement Mode==</option>
                                                                                <option value="RTGS/NEFT">RTGS/NEFT</option>
                                                                                <option value="Cheque">Cheque</option>
                                                                                <option value="Draft">Draft</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="hr hr-dotted"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-8">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-bank"></i>
                                                                                Deposit Bank Name
                                                                            </h4>
                                                                            <input class="form-control" autocomplete="off" type="text" name="deposit_bank_name" value="{{ old('deposit_bank_name') }}" >
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div id=tran_no>
                                                                                <h4 class="blue smaller" id="common">
                                                                                    <i class="fa fa-hashtag"></i>
                                                                                    Transaction No
                                                                                </h4>
                                                                                <div class="row">
                                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                                        <input autocomplete="off" type="text" name="transaction_no" value="{{ old('transaction_no') }}" required="">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="hr hr-dotted"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-bars"></i>
                                                                                CH/DD Bank Details
                                                                            </h4>
                                                                            <input class="form-control" autocomplete="off" type="text" name="bank_detail" value="{{ old('bank_detail') }}" required="">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-hashtag"></i>
                                                                                Cheque/Draft No
                                                                            </h4>
                                                                            <input class="form-control" autocomplete="off" type="text" name="cheque_draft_no" value="{{ old('cheque_draft_no') }}" required="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="hr hr-dotted"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-calendar"></i>
                                                                                Date
                                                                            </h4>
                                                                           <!--  <input class="form-control" autocomplete="off" type="date" name="date" value="{{ old('date') }}" required=""> -->

                                                                             <input value="" autocomplete="off" type="text" name="date" id="from_date" class="form-control date-picker input-sm" placeholder="DD/MM/YYYY">


                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-edit"></i>
                                                                                Remark
                                                                            </h4>
                                                                            <input class="form-control" autocomplete="off" type="text" name="remark" value="{{ old('remark') }}" style="height: 110px;">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    
                                                                    <div class="hr hr-dotted"></div>
                                                                    
                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-default btn-round btn-white">
                                                                            <i class="ace-icon fa fa-ban red"></i>
                                                                            Cancel
                                                                        </button>
                                                                        <button type="submit" class="btn btn-default btn-round btn-white">
                                                                            <i class="ace-icon fa fa-send green"></i>
                                                                            Submit
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            <!-- table content starts here  -->

                                                            <div class="col-xs-12 col-sm-6">
                                                                
                                                            <fieldset  id="fieldset-Payment_Detail"><div class="row">
                                                               <div class="row">
                                                                    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                                                         id="widget-container-col-3">
                                                                        <div class="widget-box widget-color-blue21 collapsed ui-sortable-handle bg-primary"
                                                                             id="widget-box-3">
                                                                            <div class="widget-header widget-header-small ">
                                                                                <h6 class="widget-title " style="color:black; font-weight: bold;">
                                                                                    <i class="ace-icon fa fa-bank"></i>
                                                                                    Our Bank Details
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>                                
                                                            @foreach($bank_detail_mast as $key=>$data)
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-tag"></i>
                                                                                A/C. Name:
                                                                                <span style="color:black; ">
                                                                                {{$data->account_name}}</span>
                                                                            </h5>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-tag"></i>
                                                                                A/C. No.:
                                                                            <span style="color:black; ">
                                                                           
                                                                                {{$data->account_no}}</span>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                            

                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-tag"></i>
                                                                                IFSC:
                                                                            <span style="color:black; ">
                                                                            
                                                                                {{$data->ifsc_code}}</span>
                                                                            </h4>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-tag"></i>
                                                                                BRANCH:
                                                                            <span style="color:black; ">
                                                                            
                                                                                {{$data->branch}}</span>
                                                                            </h4>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <h4 class="blue smaller">
                                                                                <i class="fa fa-tag"></i>
                                                                                ADDRESS:
                                                                            <span style="color:black; ">
                                                                            
                                                                                {{$data->address}}</span>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                        </fieldset>
                                                            </div>
                                                            <!-- table content Ends here  -->
                                                        </div>
                                                    </div>
                                                    <!-- PAGE CONTENT ENDS -->
                                                </div><!-- /.col -->
                                                
                                            </div><!-- /.row -->
                                        </div><!-- /.page-content -->
                                    </div>
                                </div><!-- /.main-content -->
                            </div><!-- /.main-container -->
                        </form>
                    </div>
                </div>      
            </div>  <!-- PAGE CONTENT ENDS -->
        </div>
    </div><!-- /.main-content -->

       


    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>


    @include('common_filter.filter_script')

    <script type="text/javascript">
       



           $('#from_date').datetimepicker({
                    format: 'DD/MM/YYYY'
                }).on('dp.change', function (e) {
                    var incrementDay = moment(new Date(e.date));
                    incrementDay.add(0, 'days');
                    // $('#to_date').data('DateTimePicker').minDate(incrementDay);
                    $(this).data("DateTimePicker").hide();
                });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
          $(window).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
          });
        });
        function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }
    </script>

    <script>
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
    $(".chosen-select").chosen();
    $('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
    });
    </script>
    <script>
    $(document).ready(function () {
        let val = $('#category').val();
        if(val=='RTGS/NEFT')
        {
            $('#tran_no').show('fast');
        }
        else
        {
            $('#tran_no').hide('fast');
        }
        
        
    });
        $(document).on('change', '#category', function () {
        _current_val = $(this).val();
        get_category(_current_val);
        });
        var val = '';
        function get_category(val) 
        {
            if(val=='RTGS/NEFT')
            {
                $('#tran_no').show('fast');
                
            }
            
            else
            {
                $('#tran_no').hide('fast');
                
            }
        }
    </script>
@endsection