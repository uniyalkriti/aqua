@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.import')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <style type="text/css">
        .blink {
          animation: blink 2s steps(10, start) infinite;
          -webkit-animation: blink 1s steps(5, start) infinite;
        }
        @keyframes blink {
          to {
            visibility: hidden;
          }
        }
        @-webkit-keyframes blink {
          to {
            visibility: hidden;
          }
        }
        </style>
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
                    <li class="active">{{Lang::get('common.import')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
              
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <form class="form-horizontal open in" action="UploadData" method="POST" id="compliant" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="name">File</label>
                                      <input type="file" name="excelFile" id="file" multiple="multiple">
                                    </div>
                                </div>
                                <span class="errr"></span>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <label class="control-label no-padding-right" for="name">Title</label>
                                    <input type="text" name="title" value="" class="form-control"
                                            >
                                    
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <label class="control-label no-padding-right" for="name">Upload Video</label>
                                    <input type="submit" name="submit" value="UploadVideo" class="form-control btn btn-sm btn-primary btn-block mg-b-10 "
                                            >
                                    
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                @if(Session::has('message'))
                                    <div class="alert alert-block {{ Session::get('alert-class', 'alert-info') }}">
                                        <button type="button" class="close" data-dismiss="alert">
                                            <i class="ace-icon fa fa-times"></i>
                                        </button>
                                        <i class="ace-icon fa fa-check green"></i>
                                        {{ Session::get('message') }}
                                    </div>
                                @endif

                                
                                    <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">
                                    </div>
                            </div>
                        </div>


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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <!-- <script src="{{asset('msell/page/report81.js')}}"></script> -->
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection