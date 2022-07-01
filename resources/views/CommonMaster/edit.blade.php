@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$table_name)}} - {{config('app.name')}}</title>
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
                        <a href="{{url($table_name)}}">{{Lang::get('common.'.$table_name)}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.'.$table_name)}}</li>
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
                        {!! Form::open(array('route'=>[$table_name.'.update',$encrypt_id] , 'method'=>'PUT','id'=>'workType-form','role'=>'form' ))!!}
                        <input type="hidden" name="table_name" value="{{$table_name}}">
                        @if($flag == 1)
                            <div class="form-group">
                                <label for="article-ckeditor">Description</label>
                                <textarea required class="form-control" id="article-ckeditor" rows="3" name="workType">{{$workType_data->name}}</textarea>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    @if($flag !== 1)
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.'.$table_name)}}</label>

                                                <input type="text" id="workType" name="workType"
                                                       value="{{$workType_data->name}}"
                                                       placeholder="Enter work type"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Sequence</label>

                                            <input type="text" id="sequence" name="sequence"
                                                   value="{{$workType_data->sequence}}"
                                                   placeholder="Enter Sequence type"
                                                   class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label" for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{$workType_data->status==1 ?'selected':''}} value="1">Active</option>
                                                <option {{$workType_data->status==0 ?'selected':''}} value="0">Inactive</option>

                                            </select>
                                        </div>
                                    </div>
                                     @if(($table_name == '_retailer_filter_master_details'))
                                        
                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">Filter</label>
                                                    <select name="retailer_filer_id" id="retailer_filer_id" class="form-control">
                                                        @if(!empty($drop_down))
                                                            @foreach($drop_down as $key => $value)
                                                                <option {{$workType_data->retailer_filer_id==$value->id ?'selected':''}} value="{{$value->id}}">{{$value->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                </div>

                                
                            </div>
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url($table_name)}}'">
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