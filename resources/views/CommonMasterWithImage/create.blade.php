@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$table_name)}} - {{config('app.name')}}</title>
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
                        <a href="{{url($table_name)}}">{{Lang::get('common.'.$table_name)}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.'.$table_name)}}</li>
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

                        <form class="form-horizontal" action="{{route($table_name.'.store')}}" method="POST" id="location2-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="">
                                                @if($table_name!='dms_contact_details')

                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.'.$table_name)}}</label>
                                                @else
                                                <label class="control-label no-padding-right"
                                                       for="name">Title</label>
                                                @endif
                                                <input required="" class="form-control" type="text" name="workType" maxlength="100" minlength="1" id="workType">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                @if($table_name!='dms_contact_details')

                                                    <label class="control-label no-padding-right"
                                                           for="name">Url</label>
                                                    <input required="" class="form-control" type="text" id="link" name="link"
                                                       
                                                       placeholder="Enter  "
                                                       class="form-control"/>
                                                    @else
                                                    <label class="control-label no-padding-right"
                                                           for="name">Detail</label>
                                                    <textarea required class="form-control" id="article-ckeditor" rows="9" name="link"></textarea>

                                                @endif

                                                
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Sequence</label>
                                                <input required="" class="form-control" type="text" name="sequence" maxlength="100" minlength="1" id="sequence">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                @if($table_name!='dms_contact_details')
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="head_quarter"> Upload Image</label>
                                                 <input type="file" required="" class="form-control-file" name="imageFile" id="imageFile" aria-describedby="fileHelp" >
                                                 
                                            </div>
                                        </div>
                                @endif

                                    </div>

                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url($table_name)}}'">
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
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection