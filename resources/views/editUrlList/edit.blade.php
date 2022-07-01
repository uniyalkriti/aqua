@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.editurllist')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('editUrlList')}}">{{Lang::get('common.editurllist')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.editurllist')}}</li>
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
                        {!! Form::open(array('route'=>['editUrlList.update',$encrypt_id] , 'method'=>'PUT','id'=>'editappmodule-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label" for="status">Company</label>
                                            <select name="company" id="company" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($company as $key=>$name)
                                                    <option {{ $assignurl_data->company_id==$key ? 'selected':'' }} value="{{$key}}">{{$name}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Assigned Url List Code</label>

                                                <input type="text" id="code" name="code"
                                                       value="{{$assignurl_data->code}}"
                                                       placeholder="Enter Sign In URL"
                                                       class="form-control"/>
                                            </div>
                                        </div>

                                         <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Assigned Url List</label>

                                                <input type="text" id="urllist" name="urllist"
                                                       value="{{$assignurl_data->url_list}}"
                                                       placeholder="Enter Sign In URL"
                                                       class="form-control"/>
                                            </div>
                                        </div>


                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Created On</label>

                                                <input type="text" id="con" name="con"
                                                       value="{{$assignurl_data->created_at}}"
                                                       placeholder="Enter Sync Post URL"
                                                       class="form-control" readonly/>
                                            </div>
                                        </div>
                                </div>

                                 <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Version</label>
                                                <select name="version" id="version" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($version as $vkey=>$vname)
                                                        <option {{ $assignurl_data->v_name==$vkey ? 'selected':'' }} value="{{$vkey}}">{{$vname}}</option>

                                                    @endforeach
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
                                <button class="btn" type="button" onclick="document.location.href='{{url('editUrlList')}}'">
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
    <script>
    $('#company').attr('disabled', true);
    </script>
@endsection