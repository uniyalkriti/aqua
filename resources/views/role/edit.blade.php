@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.role')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('role')}}">{{Lang::get('common.role')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.role')}}</li>
                </ul><!-- /.breadcrumb -->
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
                        {!! Form::open(array('route'=>['role.update',$encrypt_id] , 'method'=>'PUT','id'=>'role-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    {{--<div class="col-lg-4">--}}

                                    {{--</div>--}}
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Role Name</label>

                                            <input type="text" id="name" name="name"
                                                   value="{{$role_data->rolename }}"
                                                   placeholder="Enter Role Name"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Senior</label>

                                            <select name="s_id" id="s_id" class="form-control">

                                                <option value="">select</option>
                                                @foreach($role_info as $key=> $val)
                                                    <option  {{$role_data->senior_role_id==$key?'selected':''}} value="{{$key}}">{{$val}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                  <!--   <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Role Group</label>

                                            <select name="g_id" id="g_id" class="form-control">

                                                <option value="">select</option>
                                                @foreach($role_group as $gkey=> $gval)
                                                    <option  {{$role_data->role_group_id==$gkey?'selected':''}} value="{{$gkey}}">{{$gval}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div> -->



                                </div>
                            </div>
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('role')}}'">
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
    <script src="{{asset('msell/page/role.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
@endsection