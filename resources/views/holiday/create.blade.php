@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.holiday')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('holiday')}}">{{Lang::get('common.holiday')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.holiday')}}</li>
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

                        <form class="form-horizontal" action="{{route('holiday.store')}}" method="POST" id="location2-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                       <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="{{$current_menu}}"> {{Lang::get('common.'.$current_menu)}} Name </label>
                                                <input type="text" id="{{$current_menu}}" name="{{$current_menu}}"
                                                       value="{{old($current_menu)}}"
                                                       placeholder="Enter {{Lang::get('common.'.$current_menu)}}"
                                                       class="form-control input-sm"/>
                                            </div>
                                        </div>

                                         <div class="col-xs-6 col-sm-6 col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Date</label>
                                                <input value="" autocomplete="off" type="text" placeholder="Date" name="date" id="date" class="form-control date-picker">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url('holiday')}}'">
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
      <script type="text/javascript">
        $('#date').datetimepicker({
            format: 'YYYY-MM-DD',
           
        });
    </script>

@endsection