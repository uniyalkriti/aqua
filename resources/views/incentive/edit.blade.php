@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.location5')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('location5')}}">{{Lang::get('common.location5')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.location5')}}</li>
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
                        {!! Form::open(array('route'=>['location5.update',$encrypt_id] , 'method'=>'PUT','id'=>'location5-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location1')}}</label>
                                            <select name="location_1" id="location1" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($location1_info as $key=>$country)
                                                    <option {{ $id==$key ? 'selected':'' }} value="{{$key}}">{{$country}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location2')}}</label>
                                            <select name="location_2" id="location_2" class="form-control">
                                                <option value="">select</option>
                                                @foreach($location2_info as $key=>$state)
                                                    <option  {{ $c_code==$key ? 'selected':'' }} value="{{$key}}">{{$state}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location3')}}</label>
                                            <select name="location_3" id="location_3" class="form-control">
                                                <option value="">select</option>
                                                @foreach($location3_info as $key=>$hq)
                                                    <option  {{ $s_code==$key ? 'selected':'' }} value="{{$key}}">{{$hq}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">{{Lang::get('common.location4')}}</label>

                                            <select name="location_4" id="location_4" class="form-control">
                                                <option value="">select</option>
                                                @foreach($location4_info as $key=>$district)
                                                    <option  {{ $h_code==$key ? 'selected':'' }} value="{{$key}}">{{$district}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">{{Lang::get('common.location5')}}</label>

                                            <input type="text" id="town" name="town"
                                                   value="{{$town_data->name}}"
                                                   placeholder="Enter Town"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{$town_data->status==1 ? 'selected':''}} value="1">Active</option>
                                                <option {{$town_data->status==0 ? 'selected':''}} value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-7">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('location5')}}'">
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
    <script src="{{asset('msell/page/location5.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
@endsection