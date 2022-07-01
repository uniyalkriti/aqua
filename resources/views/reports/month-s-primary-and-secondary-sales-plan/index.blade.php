@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.msp')}} - {{config('app.name')}}</title>
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
                    <li class="active">{{Lang::get('common.msp')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#daily-attendance" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
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

                        <form class="form-horizontal open collapse in" action="" method="GET" id="daily-attendance" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">


                                        <div class="col-lg-2">
                                            <div class="">
                                                <label for="belt" class="control-label">Town</label>
                                                <select name="belt[]" multiple class="form-control chosen-select" id="belt">
                                                    <option disabled="disabled" value="">Select</option>
                                                    @foreach($belt as $key=>$data)
                                                        <option value="{{$key}}">{{$data}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label for="distributor" class="control-label">Distributor</label>
                                                <select name="distributor[]" class="form-control chosen-select distributor" id="distributor">
                                                    <option disabled="disabled" value="">Select</option>
                                                    @if(!empty($distributor))
                                                        @foreach($distributor as $key=>$data)
                                                            <option value="{{$key}}">{{$data}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="distributor-err" class="val-error" style="color: red;display: none">Select distributor first.</span>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right input-sm"
                                                       for="name">From</label>
                                                <input value="" autocomplete="off" type="text" placeholder="From Date" name="from_date" id="from_date" class="form-control date-picker input-sm">
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right input-sm"
                                                       for="name">To</label>
                                                <input value="" autocomplete="off" type="text" placeholder="From Date" name="to_date" id="to_date" class="form-control date-picker input-sm">
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                                    style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                Find
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
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    {{-- <script src="{{asset('msell/page/report.js')}}"></script> --}}
    <script src="{{asset('msell/page/report.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection