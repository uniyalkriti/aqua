
@extends('layouts.master')

@section('title')
    <title>{{ config('app.name', '') }}</title>
@endsection
@section('css')
        <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
        <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
        <style>
        .modal-lg2{
            width: 1230px;
        }
        .modal-lg4{
            width: 1300px;
        }
        .modal-lg3{
            width: 1000px;
        }
        

        </style>
@endsection
@section('body')


<body>

<div class="main-content" style="overflow-x: scroll;">
    <div class="main-content-inner">
        <div class="page-content" >
            <div class="table-header center">
               <strong>Training Master</strong>
                <div class="pull-right tableTools-container"></div>
               
            </div>
            <table class="table  table-striped" border='1'>
                <th align="left" style="width: 60%;"   >
                    
                   
                    <div class="row">
                        <iframe type='text/html' src="{{url(!empty($main_vedio->vedio_name)?$main_vedio->vedio_name:'')}}" width='97%' height='600' frameborder='0' allowfullscreen='true'></iframe>
                    <br>
                       
                    </div>

                    <div class="row">
                       <h4 style="text-align: left;">&nbsp;&nbsp;&nbsp;{!! !empty($main_vedio->title)?$main_vedio->title:'' !!}</h4>
                    </div>
                    
                </th>
                
                <th >
                   
                     <div class="search-filter-header bg-primary">
                            <h5 class="smaller no-margin-bottom">
                                <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; <strong>Module Tutorials</strong>
                            </h5>
                        </div>
                    <div class="search-area well well-sm" style="overflow-y:scroll; height: 600px; ">
                       
                        <div class="hr hr-dotted"></div>
                        @if(empty($videos))
                        <div class="row" style="text-align: left;">
                            
                           Upload Some Vedios
                        </div>
                        @else
                        @foreach($videos as $key=> $video)

                        
                        <div class="row" style="text-align: left;">
                            
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <strong>{{$key+1}}.</strong>  &nbsp;&nbsp;&nbsp;
                                <a href="{{url('TraningModule/'.$video->id)}}" title="primaryOrderBooking" target="_blank" style="color: black;">
                                    <iframe type='text/html' style="color: black;" src="{{url('/advertisement/primaryOrderBooking.mp4')}}"  frameborder='1' allowfullscreen='false'></iframe>
                                    <h5 class="blue smaller">
                                        <i class="fa fa-tags"></i>
                                        <strong>{!! $video->title !!}</strong>
                                    </h5>
                                </a>
                            </div>
                        </div>
                        <div class="hr hr-dotted"></div>
                        @endforeach
                       
                        @endif
                        
                        
                        
                    </div>
                    
                    
                </th>

            </table>
        </div>
    </div>
</div>





{{-- <video width="560" height="315" src="{{url('/advertisement/primaryOrderBooking.mp4')}}"  controls></video> --}}
{{-- <div class="bs-example">
    <a href="#myModal" class="btn btn-lg btn-primary" data-toggle="modal">Launch Demo Modal</a>
    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">YouTube Video</h4>
                </div>
                <div class="modal-body">
                    <iframe id="cartoonVideo" width="560" height="315" src="{{url('/advertisement/WhatsApp Video 2020-07-30 at 12.20.48 PM.mp4')}}" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div> --}}     
</body>
@endsection
@section('js')
        <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.easypiechart.min.js')}}"></script>    
    <script src="{{asset('msell/js/jquery.flot.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.resize.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


    <script src="{{asset('msell/js/common.js')}}"></script>
   
   

   
@endsection
