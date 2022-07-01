@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.super_stock_mgmt')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('super-stockist')}}">{{Lang::get('common.super_stock_mgmt')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.super_stock_mgmt')}}</li>
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

                        <form class="form-horizontal" action="{{route('super-stockist.store')}}" method="POST" id="create-stock-form" role="form">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        {{--<div class="col-lg-4">--}}

                                        {{--</div>--}}
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="ss_code"> SS Code</label>
                                                <input type="text" id="ss_code" name="ss_code"
                                                       value="{{old('ss_code')}}"
                                                       placeholder="Enter SS Code"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> Name </label>

                                                <input type="text" id="name" name="name"
                                                       value="{{old('name')}}"
                                                       placeholder="Enter NAme"
                                                       class="form-control"/>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="location2">State</label>
                                                <select name="state" id="state" class="form-control">
                                                    <option value="">select</option>
                                                    @if(!empty($state))
                                                        @foreach($state as $state1)
                                                            <option value="{{$state1->id}}">{{$state1->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label" for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-9">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url('super-stockist')}}'">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

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
    <script src="{{asset('msell/page/create.superstockist.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection