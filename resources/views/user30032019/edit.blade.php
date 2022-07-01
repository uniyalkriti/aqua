<?php foreach ($user as $val)
    ?>
{{ $user->userDetails->name }}
@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.user-mgmt')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
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
                        <a href="{{url('user-management')}}">{{Lang::get('common.user-mgmt')}}</a>
                    </li>

                    <li class="active">Edit User</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')

                <div class="page-header">
                    <h1>
                        {{Lang::get('common.user-mgmt')}}
                    </h1>
                </div><!-- /.page-header -->

                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        {!! Form::open(array('route'=>['user-management.update',$encrypt_id] , 'method'=>'PUT','id'=>'edit-user-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <h4 class="widget-title">Register User</h4>

                                        <div class="widget-toolbar">
                                            <a href="#" data-action="collapse">
                                                <i class="ace-icon fa fa-chevron-up"></i>
                                            </a>

                                            {{--<a href="#" data-action="close">--}}
                                            {{--<i class="ace-icon fa fa-times"></i>--}}
                                            {{--</a>--}}
                                        </div>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <div class="row">
                                                <div class="col-lg-2 col-lg-offset-4">
                                                    <img onerror="null;this.src='{{asset('msell/images/default.png')}}'"
                                                         src="{{asset($user->userDetails->image_name)}}"
                                                         id="render" class=""
                                                         alt="Image Preview" height="150px;">
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <input type="file" id="photo" onchange="renderimage(this);"
                                                               name="photo" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hr hr-18 dotted"></div>
                                            <div class="row">
                                                {{--<div class="col-lg-4">--}}

                                                {{--</div>--}}
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="first_name"> First Name </label>
                                                        <input type="text" id="first_name" name="first_name"
                                                               value="{{$user->userDetails->first_name}}"
                                                               placeholder="First Name|Required|Min:2"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="last_name"> Last Name </label>

                                                        <input type="text" id="last_name" name="last_name"
                                                               value="{{$user->userDetails->last_name}}"
                                                               placeholder="Last Name|Required|Min:2"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="">
                                                        <label class="control-label" for="dob">Employee Code</label>
                                                        <input type="text" name="emp_code" id="emp_code"
                                                               value="{{$user->userDetails->employee_code}}"
                                                               class="form-control"
                                                               placeholder="Employee Code">
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="">
                                                        <label class="control-label" for="dob">DOB</label>
                                                        <input type="text" name="dob" id="dob"
                                                               value="{{$user->userDetails->dob}}"
                                                               class="form-control date-picker"
                                                               placeholder="DOB|Valid Date">
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label" for="gender">Gender</label>
                                                        <select name="gender" id="gender" class="form-control">
                                                            <option {{$user->userDetails->gender==1?'selected':''}} value="1">
                                                                Male
                                                            </option>
                                                            <option {{$user->userDetails->gender==2?'selected':''}} value="2">
                                                                Female
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="email"> Email </label>
                                                        <input type="text" id="email"
                                                               value="{{$user->userDetails->email_id}}"
                                                               name="email"
                                                               placeholder="Email|Required"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="">
                                                        <label class="ontrol-label no-padding-right"
                                                               for="password"> Password </label>
                                                        <input type="password" id="password" name="password"
                                                               placeholder="Password|Required|Min:6"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="confirm_password"> Confirm Password </label>
                                                        <input type="password" id="confirm_password"
                                                               name="confirm_password"
                                                               placeholder="Confirm Password|Required|Min:6"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="role">Role</label>
                                                        <select name="role" id="role" class="form-control">


                                                            <option value="">Select</option>
                                                            @if(count($role_data)>0)
                                                                @foreach($role_data as $key=>$role)

                                                                    <option value="{{ $key }}"
                                                                            @if($key == $user->role_id) selected @endif>
                                                                        {{$role}}
                                                                    </option>

                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="seniorrole">Senior Role</label>
                                                        <select name="seniorrole" id="seniorrole" class="form-control">
                                                            <option value="">Select</option>
                                                            @if(count($role_data)>0)
                                                                @foreach($role_data as $key=>$role)
                                                                    <option value="{{$key}}"
                                                                            @if($key==$user->userDetails->senior_id) selected @endif >{{$role}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="seniorname">Senior Name</label>
                                                        <select name="seniorname" id="seniorname" class="form-control">
                                                            @if(count($role_data)>0)
                                                                @foreach($role_data as $key=>$role)
                                                                    <option value="{{$key}}"
                                                                            @if($key==$user->userDetails->senior_id) selected @endif >{{$role}}</option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="mobile_no">Mobile Number</label>
                                                        <input type="number" id="mobile_no" name="mobile_no"
                                                               value="{{$user->userDetails->mobile}}"
                                                               placeholder="Mobile No.|Required"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="address">Address</label>
                                                        <textarea class="form-control" name="address"
                                                                  id="address">{{ $user->userDetails->address }} </textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="location2">State</label>
                                                        <select name="state" id="state" class="form-control">
                                                            <option value="">select</option>
                                                            @if(!empty($state_data))
                                                                @foreach($state_data as $state)
                                                                    <option value="{{$state->code}}"
                                                                            @if($user->userDetails->location_2_id ==$state->code) selected @endif >{{$state->name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="location3">Head Quarter</label>
                                                        <select name="region" id="region" class="form-control">
                                                            @if(!empty($city))
                                                                @foreach($region as $key=>$region_data)
                                                                    <option value="{{$key}}"
                                                                            @if($user->userDetails->location_3_id ==$key) selected @endif>{{$region_data}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="location4">District</label>
                                                        <select name="city" id="city" class="form-control">
                                                            @if(!empty($city))
                                                                @foreach($city as $key=>$city)
                                                                    <option value="{{$key}}"
                                                                            @if($user->userDetails->location_4_id ==$key) selected @endif>{{$city}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="">
                                                        <label class="control-label"
                                                               for="distributor">Distributor</label>
                                                        <select name="distributor[]" id="distributor"
                                                                class="chosen-select form-control" multiple="multiple">
                                                            <option value="">select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.span -->
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-7">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('user-management')}}'">
                                    <i class="ace-icon fa fa-close bigger-110"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>

                        {!! Form::close() !!}

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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/edit.user.js')}}"></script>


@endsection