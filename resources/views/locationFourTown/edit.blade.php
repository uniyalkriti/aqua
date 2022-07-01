@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.locationFourTown')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
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
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li>
                        <a href="{{url('locationFourTown')}}">{{Lang::get('common.locationFourTown')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.locationFourTown')}}</li>
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
                        {!! Form::open(array('route'=>['locationFourTown.update',$encrypt_id] , 'method'=>'PUT','id'=>'locationFourTown-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location1')}} Name</label>
                                            <select name="location_1" id="location_1" class="form-control chosen-select">
                                                <option value="">Select</option>
                                                @foreach($location1_info as $key=>$country)
                                                    <option {{ $l1_code == $key ? 'selected':'' }} value="{{$key}}">{{$country}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location2')}} Name</label>
                                            <select name="location_2" id="location_2" class="form-control chosen-select">
                                                <option value="">select</option>
                                                @foreach($location2_info as $key=>$state)
                                                    <option {{ $l3_code == $key ? 'selected':'' }} value="{{$key}}">{{ $state }}</option>

                                                @endforeach

                                            </select>
                                        </div>
                                    </div>


                                </div>


                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.location3')}} Name</label>
                                            <select name="location_3" id="location_3" class="form-control chosen-select">
                                                <option value="">select</option>
                                                @foreach($location3_info as $key=>$hq)
                                                    <option {{ $location3_code == $key ? 'selected':'' }} value="{{$key}}">{{ $hq }}</option>

                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">{{Lang::get('common.locationFourTown')}} Name</label>

                                            <input type="text" id="name" name="name"
                                                   value="{{$l4_name}}"
                                                   placeholder="Enter District"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label" for="status">{{Lang::get('common.status')}}</label>
                                            <select name="status" id="status" class="form-control chosen-select">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
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
                                <button class="btn" type="button" onclick="document.location.href='{{url('locationFourTown')}}'">
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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/page/locationFourTown.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script>
        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $(document).on('change', '#location_1', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        $(document).on('change', '#location_3', function () {
            _current_val = $(this).val();
            location_data(_current_val,4);
        });

        function location_data(val,level) {
            _append_box=$('#location_'+level);
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/getLocation',
                    dataType: 'json',
                    data: "id=" + val+"&type="+level,
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                            //Location 3
                            template = '<option value="" >Select</option>';
                            $.each(data.result, function (key, value) {
                                if (value.name != '') {
                                    template += '<option value="' + key + '" >' + (value) + '</option>';
                                }
                            });
                            _append_box.empty();
                            _append_box.append(template).trigger('chosen:updated');

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }
            else{
                _append_box.empty();
            }
        }

    </script>
@endsection