<?php 
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?>
@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('body')
<div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="#">Retailer</a>
                    </li>

                    <li class="active" style="color: white">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="clearfix">
                                    <form class="form-horizontal open collapse in" action="" method="GET" id="sale-order" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                      <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Distributor</label>
                                                <select name="distributor[]" id="distributor" class="form-control chosen-select" required>
                                                    <option value="">Select</option>
                                                    @if(!empty($dealer_name))
                                                        @foreach($dealer_name as $k=>$r)
                                                          <?php 
                                                          if(empty($_GET['distributor']))
                                                          $_GET['distributor']=array();
                                                          ?>

                                                            <option value="{{$k}}" @if(in_array($k,$_GET['distributor'])) {{"selected"}} @endif >{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Beat</label>
                                                <select name="beat[]" id="beat" class="form-control chosen-select">
                                                    <option value="" required>Select</option>
                                                    @if(!empty($beat))
                                                        @foreach($beat as $k=>$r)
                                                          <?php 
                                                          if(empty($_GET['beat']))
                                                          $_GET['beat']=array();
                                                          ?>

                                                            <option value="{{$k}}" @if(in_array($k,$_GET['beat'])) {{"selected"}} @endif >{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                       
                                        {{--</div>--}}
                                      
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                                    style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                Find
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                            <div class="col-xs-12">
                                    <div class="pull-right tableTools-container"></div>
                                </div>

                                <form class="form-horizontal open collapse in" action="moveretailer" method="GET" id="sale-order" role="form"
                                    enctype="multipart/form-data">

                                <table class="table table-bordered table-hover" id="simple-table">
                                    <thead>
                                    <tr>
                                    <th>Select All <br><input type="checkbox" onchange="checkAll(this)"></th>
                                        <th class="center">
                                            S.No.
                                        </th>
                                        <th>Retailer Name</th>
                                        <th>Dealer Name</th>
                                        <th>Town Name</th>
                                        <th>Beat Name</th>
                                      
                                      
                                    </tr>
                                    </thead>
                                    <tbody id="dynamic-table" >
                                    @if(!empty($records))
                                    @foreach($records as $key=>$data)
                                        <?php $encid = Crypt::encryptString($data->id);?>
                                        <tr>
                                        <td><input type="checkbox" value="{{$data->rid}}" name="move[]"></td>  

                                            <td class="center">
                                                {{ 1 + $key }}
                                            </td>
                                            <td>{{$data->rname}}</td>
                                            <td>{{$data->dealer_name}}</td>
                                            <td>{{$data->l4_name}}</td>
                                            <td>{{$data->l5_name}}</td>
                                          
                                           
                                        </tr>
                                    @endforeach
                                   
                                    </tbody>

                                  

                              <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                      <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Distributor</label>
                                                <select name="distributormove[]" id="distributormove" class="form-control chosen-select" required>
                                                    <option value="">Select</option>
                                                    @if(!empty($dealer_name))
                                                        @foreach($dealer_name as $k=>$r)
                                                          <?php 
                                                          if(empty($_GET['distributor']))
                                                          $_GET['distributor']=array();
                                                          ?>

                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Beat</label>
                                                <select name="beatmove[]" id="beatmove" class="form-control chosen-select" required>
                                                    <option value="">Select</option>
                                                    @if(!empty($beat))
                                                        @foreach($beat as $k=>$r)
                                                          <?php 
                                                          if(empty($_GET['beat']))
                                                          $_GET['beat']=array();
                                                          ?>

                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                       
                                        {{--</div>--}}
                                      
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <button type="submit" class="btn btn-sm btn-success btn-block mg-b-10" style="margin-top: 28px;" name="action" value="move">
                                          <i class="fa fa-scissors mg-r-10"></i> Move</button>
                                         
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                        <button type="submit" class="btn btn-sm btn-success btn-block mg-b-10" style="margin-top: 28px;" name="action" value="copy">
                                         <i class="fa fa-files-o mg-r-10"></i> Copy</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif
                         </form>
                        </table>
                              
                               
                              
                            </div><!-- /.span -->



                        </div><!-- /.row -->

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')

    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.select.min.js')}}"></script>
    <script src="{{asset('js/dealer.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
    <script>
     $(document).on('change', '#distributor', function () {
        val = $(this).val();
        _hq = $('#beat');
        //alert(_current_val);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_beat_name',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data, function (key, value) {
                          
                            console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq.append(template).trigger("chosen:updated");
               

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });  
       }
        
    });
    </script>

<script>
     $(document).on('change', '#distributormove', function () {
        val = $(this).val();
        _hq = $('#beatmove');
        //alert(_current_val);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_beat_name',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data, function (key, value) {
                          
                            console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq.append(template).trigger("chosen:updated");
               

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });  
       }
        
    });
    </script>

<script type="text/javascript">
      function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
 </script>
    

@endsection