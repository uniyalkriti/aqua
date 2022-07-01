@extends('layouts.master')
@section('title')
    <title>{{Lang::get('common.catalog_1')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>

    <style>
        .has-error, .help-block {
            color: #a94442;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            text-align: center; 
            vertical-align: middle;
        }
    </style>
@endsection
@section('body')
    <div class="main-content"> 
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                     <li>
                        <a style="color: white" href="{{url('catalog_1')}}">{{Lang::get('common.catalog_1')}}</a>
                    </li>

                    <li class="active" style="color: white">Edit {{Lang::get('common.catalog_1')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>
            <div class="page-content">
                {{--@include('layouts.settings')--}}

                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                            {!! Form::open(array('route'=>[$current_menu.'.update',$encrypt_id] , 'method'=>'PUT','id'=>'catalog-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="{{$current_menu}}"> {{Lang::get('common.'.$current_menu)}} Name</label>
                                                <input type="text" id="{{$current_menu}}" name="{{$current_menu}}"
                                                       value="{{$catalog_data->name}}"
                                                       placeholder="Enter {{Lang::get('common.'.$current_menu)}}"
                                                       class="form-control input-sm"/>
                                            </div>
                                        </div>
                                    </div>

                                </div><!-- /.span -->
                            </div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Reset
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
    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/catalog.js')}}"></script>

@endsection