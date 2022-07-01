@extends('layouts.master')

@section('title')
    <title>Division Master</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('division_master')}}">Division Master</a>
                    </li>

                    <li class="active">Edit Division</li>
                </ul>

            </div>

            <div class="page-content">
                <div class="clearfix" style="margin-top: 5px"></div>
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
                        @if(count($errors)>0)
                            @foreach ($errors->all() as $error)
                                <div class="help-block">{{ $error }}</div>
                            @endforeach
                        @endif

                            {!! Form::open(array('route'=>[$current_menu.'.update',$encrypt_id] , 'method'=>'PUT','id'=>$current_menu.'-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_4"> Division Code</label>
                                               <input type="text" placeholder="Enter Division Code" name="division_code" class="form-control input-sm" value="{{$ss->division_code}}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="retailer_name"> Division Name </label>
                                                <input type="text" placeholder="Enter Division Name" name="division_name"
                                               id="division_name" class="form-control input-sm" value="{{$ss->division_name}}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                <div class="">
                                        <label class="control-label no-padding-right"
                                               for="retailer_name"> Location </label>
                                        <input type="text" placeholder="Enter Location" name="location"
                                               id="location" class="form-control input-sm" value="{{$ss->location}}">
                                    </div>
                                </div>    
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="address">Operator Name</label>
                                        <input type="text" placeholder="Enter Operator Name" name="operator_name"
                                               id="operator_name" class="form-control input-sm" value="{{$ss->operator_name}}"> 
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="mobile">active_status</label>
                                        <select required="required" class="form-control input-sm" name="active_status"
                                                id="active_status">
                                        @if($ss->active_status == 1)
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        @elseif($ss->active_status == 0)
                                            <option value="1">Active</option>
                                            <option value="0" selected>Inactive</option>
                                        @else
                                        @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="address">Sequence</label>
                                        <input type="text" placeholder="Enter Sequence" name="sequence"
                                               id="sequence" class="form-control input-sm" value="{{$ss->sequence}}">
                                    </div>
                                </div>
                            </div>
                            

                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('division_master')}}'"
                                            type="button">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>

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
    <script src="{{asset('js/retailer.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script>
        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert('Done!');
                        window.setTimeout(function () {
                            location.reload()
                        }, 3000);
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function takeAction(module, action_id, tab, act) {

            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
@endsection