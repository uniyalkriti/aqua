@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.Menu')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('Menu')}}">{{Lang::get('common.Menu')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.Menu')}}</li>
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
                        {!! Form::open(array('route'=>['Menu.update',$encrypt_id] , 'method'=>'PUT','id'=>'location2-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                     <div class="col-lg-6">
                                        <div class="">
                                          
                                            <label class="control-label no-padding-right"
                                                   for="name">{{Lang::get('common.Menu')}}</label>
                                            <select name="module_id" id="module_id" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($main_menu as $key=> $val)
                                                    <option {{$loc_data->module_id==$key ? 'selected':''}} value="{{$key}}">{{$val}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Menu</label>

                                            <input type="text" id="name" name="sub_module_name"
                                                   value="{{$loc_data->sub_module_name}}"
                                                   placeholder="Enter Name"
                                                   class="form-control"/>
                                        </div>
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Title</label>

                                            <input type="text" id="title" name="title"
                                                   value="{{$loc_data->title}}"
                                                   placeholder="Enter Title"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                   

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{$loc_data->status==1 ?'selected':''}} value="1">Active</option>
                                                <option {{$loc_data->status==0 ?'selected':''}} value="0">Inactive</option>

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
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('Menu')}}'">
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