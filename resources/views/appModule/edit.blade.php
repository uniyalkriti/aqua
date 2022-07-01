@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.app_module')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
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
                        <a href="{{url('appModule')}}">{{Lang::get('common.app_module')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.app_module')}}</li>
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
                        {!! Form::open(array('route'=>['appModule.update',$encrypt_id] , 'method'=>'PUT','id'=>'appModule-form1','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                        <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                             id="widget-box-3">
                                            <div class="widget-header widget-header-small">
                                                <h6 class="widget-title">
                                                    <i class="ace-icon fa fa-map-pin"></i>
                                                     App Master Details 
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">{{Lang::get('common.app_module')}}</label>

                                            <input type="text" id="appModule" name="appModule"
                                                   value="{{$appModule_data->name}}"
                                                   placeholder="Enter outlet type"
                                                   class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Title Name</label>
                                                <input class="form-control" type="text" name="apptitle" value="{{$appModule_data->title_name}}" maxlength="100" minlength="1" id="apptitle">
                                            </div>
                                        </div>

                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label" for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{$appModule_data->status==1 ?'selected':''}} value="1">Active</option>
                                                <option {{$appModule_data->status==0 ?'selected':''}} value="0">Inactive</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                    <div class="hr hr-16 hr-dotted"></div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                            <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                                 id="widget-box-3">
                                                <div class="widget-header widget-header-small">
                                                    <h6 class="widget-title">
                                                        <i class="ace-icon fa fa-map-pin"></i>
                                                         Image
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-xs-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                           for="head_quarter"> Upload Image</label>
                                                     <input type="file" class="form-control-file" name="imageFile" id="imageFile" aria-describedby="fileHelp" onchange="readURL(this);">
                                                     
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <div class="">
                                                    <img id="user_image" src="{{ ($appModule_data->icon_image) }}" height="200" width="150" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('appModule')}}'">
                                    <i class="ace-icon fa fa-close bigger-110"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>

                        {!! Form::close() !!}

                        <div class="hr hr-18 dotted hr-double"></div>

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
    <script src="{{asset('msell/page/location2.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    
@endsection