@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.distributer_sales_trend')}}</title>
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
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.distributer_sales_trend')}}</li>
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

                        <form class="form-horizontal open collapse in" action="" method="GET" id="competitive-price" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                                @php
                                    $var = 'location_3';
                                    $location3 = App\CommonFilter::comon_data('location_3');
                                    $location4 = App\CommonFilter::comon_data('location_4');
                                    $location5 = App\CommonFilter::comon_data('location_5');
                                    $location6 = App\CommonFilter::comon_data('location_6');
                                    $location7 = App\CommonFilter::comon_data('location_7');
                                    $product = App\CommonFilter::comon_data('catalog_product');
                                    $role = App\CommonFilter::role_name('_role');
                                    $users = App\CommonFilter::user_filter('person');
                                    $dealer = App\CommonFilter::dealer_filter('dealer');
                                @endphp
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            
                                            
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                           for="name">{{Lang::get('common.location3')}}</label>
                                                    <select multiple name="location_3[]" id="location_3" class="form-control chosen-select">
                                                        <option disabled="disabled" value="">select</option>
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
                                                           for="name">{{Lang::get('common.location4')}}</label>
                                                    <select multiple name="location_4[]" id="location_4" class="form-control chosen-select">
                                                        <option disabled="disabled" value="">select</option>
                                                        @if(!empty($location4))
                                                            @foreach($location4 as $k=>$r)
                                                                <option value="{{$k}}">{{$r}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                           for="name">{{Lang::get('common.location5')}}</label>
                                                    <select multiple name="location_5[]" id="location_5" class="form-control chosen-select">
                                                        <option disabled="disabled" value="">select</option>
                                                        @if(!empty($location5))
                                                            @foreach($location5 as $k=>$r)
                                                                <option value="{{$k}}">{{$r}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                           for="name">{{Lang::get('common.location6')}}</label>
                                                    <select multiple name="location_6[]" id="location_6" class="form-control chosen-select">
                                                        <option disabled="disabled" value="">select</option>
                                                        @if(!empty($location6))
                                                            @foreach($location6 as $k=>$r)
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
                                                    <select multiple name="dealer[]" id="dealer" class="form-control chosen-select">
                                                        <option disabled="disabled" value="">Select</option>
                                                        @if(!empty($dealer))
                                                            @foreach($dealer as $k=>$r)
                                                                <option value="{{$k}}">{{$r}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            
                                           
                                            
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                           for="name">{{Lang::get('common.month')}}</label>
                                                    <input value="" autocomplete="off" type="text" placeholder="Select Month" name="year" id="year" class="form-control date-picker input-sm">
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
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report27.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    @include('common_filter.filter_script_sale')
    
@endsection