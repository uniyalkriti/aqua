@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.daily_attendance')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
  
   
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
		<link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
		<link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
		<link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
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
                    <li class="active">{{Lang::get('common.daily_attendance')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#daily-attendance" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
                <!-- /.nav-search -->
            </div>

            <div class="page-content" style="padding-top: 0;">
                @include('layouts.settings')
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        
                        <form class="form-horizontal open collapse in" action="" method="GET" id="daily-attendance" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}

                            @include('common_filter.filter')
                                    @if($company_id == 65)
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Work From</label>
                                            <select name="work_from" id="work_from" class="form-control chosen-select">
                                                <option value="">select</option>
                                             
                                                        <option value="1">Home Location</option>
                                                        <option value="2">OtherLocation</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    @endif



                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                               
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar bigger-110"></i>
                                            </span>

                                            <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                        </div>
                                    </div>
                                          

                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                                style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                            {{Lang::get('common.find')}}
                                        </button>
                                    </div>
                                </div>
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

                        <div class="hr hr-18 dotted hr-double"></div>

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
    <script src="{{asset('msell/page/report4.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>	
	<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    @include('common_filter.filter_script')

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
 
@endsection