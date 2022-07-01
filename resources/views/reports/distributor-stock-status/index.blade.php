@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.distributor_stock_status')}} - {{config('app.name')}}</title>
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
                    <li class="active">{{Lang::get('common.distributor_stock_status')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#stock-status" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
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

                        <form class="form-horizontal open collapse in" action="" method="GET" id="stock-status" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.dealer_module')}}</label>
                                                <select name="distributor" required="required" id="distributor" class="form-control input-sm">
                                                    <option value="">select</option>
                                                    @if(!empty($dealer))
                                                        @foreach($dealer as $k=>$d)
                                                            <option value="{{$k}}">{{$d}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Month</label>
                                                <input placeholder="Choose Month" required="required" type="text" name="month" id="month" autocomplete="off" class="form-control input-sm">
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
                                    <div class="col-xs-12" id="ajax-table">


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
    <script src="{{asset('msell/page/report11.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection