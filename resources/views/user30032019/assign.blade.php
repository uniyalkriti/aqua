@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.user-mgmt')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
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
                    <li>
                        <a href="{{url('role')}}">{{Lang::get('common.user-mgmt')}}</a>
                    </li>

                    <li class="active">Assign {{Lang::get('common.dealer_module')}}</li>
                </ul><!-- /.breadcrumb -->
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

                        <form class="form-horizontal" action="" method="get" id="role-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-10">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location2')}}</label>
                                                <select name="location2" id="location2" class="form-control">
                                                    <option value="">Select</option>
                                                    @if(count($location2)>0)
                                                        @foreach($location2 as $key=>$value)
                                                            <option value="{{$key}}">{{$value}}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location3')}}</label>
                                                <select name="location3" id="location3" class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location4')}}</label>
                                                <select name="location4" id="location4" class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location5')}}</label>
                                                <select name="location5" id="location5" class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block" style="margin-top: 28px;"><i class="fa fa-filter mg-r-10"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" onclick="document.location.href='{{url('user-management')}}'" class="btn btn-sm btn-block" style="margin-top: 28px;"><i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

                            {{--<div class="clearfix form-actions">--}}
                                {{--<div class="col-md-offset-5 col-md-7">--}}
                                    {{--<button class="btn btn-info" type="submit">--}}
                                        {{--<i class="ace-icon fa fa-check bigger-110"></i>--}}
                                        {{--Submit--}}
                                    {{--</button>--}}
                                    {{--<button class="btn" type="button" onclick="document.location.href='{{url('user-management')}}'">--}}
                                        {{--<i class="ace-icon fa fa-close bigger-110"></i>--}}
                                        {{--Cancel--}}
                                    {{--</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                        </form>

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
    <script src="{{asset('msell/page/user-assign.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection