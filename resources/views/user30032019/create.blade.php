@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.user-mgmt')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('user-management')}}">{{Lang::get('common.user-mgmt')}}</a>
                    </li>

                    <li class="active">Create User</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')

                {{--<div class="page-header">--}}
                    {{--<h1>--}}
                        {{--{{Lang::get('common.user-mgmt')}}--}}
                    {{--</h1>--}}
                {{--</div>--}}
                {{--<!-- /.page-header -->--}}

                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" action="{{route('user-management.store')}}" method="POST" id="cf" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
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
                                                             src="{{asset('images/default.png')}}" id="render" class=""
                                                             alt="Image Preview" height="150px;">
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <input type="file" id="photo" onchange="renderimage(this);" name="photo" class="form-control">
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
                                                                   value="{{old('first_name')}}"
                                                                   placeholder="First Name|Required|Min:2"
                                                                   class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="">
                                                            <label class="control-label no-padding-right"
                                                                   for="last_name"> Last Name </label>

                                                            <input type="text" id="last_name" name="last_name"
                                                                   value="{{old('last_name')}}"
                                                                   placeholder="Last Name|Required|Min:2"
                                                                   class="form-control"/>
                                                        </div>
                                                    </div>
                                                       <div class="col-lg-2">
                                                        <div class="">
                                                            <label class="control-label" for="dob">Employee Code</label>
                                                            <input type="text" name="emp_code" id="emp_code"
                                                                   value="{{old('emp_code')}}"
                                                                   class="form-control"
                                                                   placeholder="Employee Code">
                                                        </div>
                                                    </div>
                                                 
                                                       <div class="col-lg-2">
                                                        <div class="">
                                                            <label class="control-label" for="dob">DOB</label>
                                                            <input type="text" name="dob" id="dob"
                                                                   value="{{old('dob')}}"
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
                                                                <option value="1">Male</option>
                                                                <option value="2">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="">
                                                            <label class="control-label no-padding-right"
                                                                   for="email"> Email </label>
                                                            <input type="text" id="email" value="{{old('email')}}"
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
                                                                        <option value="{{$key}}">{{$role}}</option>
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
                                                                        <option value="{{$key}}">{{$role}}</option>
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
                                                                <option value="">Select</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-4">
                                                        <div class="">
                                                            <label class="control-label no-padding-right"
                                                                   for="mobile_no">Mobile Number</label>
                                                            <input type="number" id="mobile_no" name="mobile_no"
                                                                   placeholder="Mobile No.|Required"
                                                                   class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="">
                                                            <label class="control-label no-padding-right"
                                                                   for="address">Address</label>
                                                            <textarea class="form-control" name="address"
                                                                      id="address"></textarea>
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
                                                                        <option value="{{$state->code}}">{{$state->name}}</option>
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
                                                                <option value="">Select</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="">
                                                            <label class="control-label no-padding-right"
                                                                   for="location4">District</label>
                                                            <select name="city" id="city" class="form-control">
                                                                <option value="">Select</option>
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
                                                                <option value="">select </option>
                                                            </select>
                                                        </div>                          
                                                    </div>                                                       
                                                </div>    

                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.span -->
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="widget-box collapsed">
                                        <div class="widget-header">
                                            <h4 class="widget-title">App Settings</h4>

                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="ace-icon fa fa-chevron-down"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <table class="table display responsive nowrap table-bordered">
                                                    <thead class="thead-colored thead-info">
                                                    <tr class="warning">
                                                        <td class="wd-5p">
                                                            Modules
                                                            {{--<label class="checkbox-inline"><input--}}
                                                            {{--onclick="checkUncheckAll('all', this.id);"--}}
                                                            {{--type="checkbox" name="all" id="all">&nbsp;Modules/Roles</label>--}}
                                                        </td>
                                                        <th class="wd-5p">
                                                            Permission 1
                                                        </th>
                                                        <th class="wd-5p">
                                                            Permission 2
                                                        </th>
                                                        <th class="wd-5p">
                                                            Permission 3
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($app_module_data)>0)
                                                        @foreach($app_module_data as $appData)
                                                            <tr class="info">
                                                                <td colspan="4"><span class="label label-xlg label-warning arrowed arrowed-right">{{$appData->title_name}}</span></td>
                                                            </tr>
                                                            @foreach($sub_module as $appSubData)
                                                                @if($appSubData->app_module_id!=$appData->id)
                                                                    @continue
                                                                @endif

                                                                <tr>
                                                                    <td><span class="label label-xlg label-info arrowed-in arrowed-in-right">{{$appSubData->title_name}}</span></td>
                                                                    <input name="hidden_val[{{$appData->id }}][{{$appSubData->id }}][]"
                                                                           type="hidden" value="1">
                                                                    <td>
                                                                        <div class="widget-toolbar no-border" style="margin: 0 30% 0 0;">
                                                                            <label>
                                                                                Add
                                                                                <input name="add_permissions[{{$appData->id }}][{{$appSubData->id }}][]"
                                                                                       type="checkbox"
                                                                                       class="ace ace-switch ace-switch-6"
                                                                                       checked="">
                                                                                <span class="lbl middle"></span>
                                                                            </label>
                                                                        </div>

                                                                    </td>
                                                                    <td>
                                                                        <div class="widget-toolbar no-border" style="margin: 0 30% 0 0;">
                                                                            <label>
                                                                                View
                                                                                <input name="view_permissions[{{$appData->id }}][{{$appSubData->id }}][]"
                                                                                       type="checkbox"
                                                                                       class="ace ace-switch ace-switch-6"
                                                                                       checked="">
                                                                                <span class="lbl middle"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="widget-toolbar no-border" style="margin: 0 30% 0 0;">
                                                                            <label>
                                                                                Edit
                                                                                <input name="edit_permissions[{{$appData->id }}][{{$appSubData->id }}][]"
                                                                                       type="checkbox"
                                                                                       class="ace ace-switch ace-switch-6"
                                                                                       checked="">
                                                                                <span class="lbl middle"></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
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
                                    <button class="btn" type="button" onclick="document.location.href='{{url('user-management')}}'">
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
    <script>
        $(function () {
            $('.date-picker').datetimepicker({
                viewMode: 'days',
                format: 'YYYY-MM-DD',
                useCurrent: true,
                maxDate: moment()
            });
        });
    </script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script>
        $('#cf').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            rules: {
                first_name: {
                    required: true,
                    letterswithbasicpunc: true,
                    maxlength: 70,
                    minlength: 2
                },
                last_name: {
                    required: true,
                    letterswithbasicpunc: true,
                    maxlength: 70,
                    minlength: 2
                },
                emp_code: {
                    required: true,
                    maxlength: 100,
                    minlength: 2
                },
                mobile_no: {
                    required: true,
                    maxlength: 15,
                    minlength: 2
                },
                role: {
                    required: true,
                    maxlength: 10,
                    minlength: 1
                },
                photo: {
                    required: true
                },
                dob: {
                    required: true,
                    date: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 30
                },
                confirm_password: {
                    equalTo: '#password'
                },
                state: {
                    required: true
                },
                region: {
                    required: true
                },
                city: {
                    required: true
                },
                distributor: {
                    required: true
                },
                seniorrole: {
                    required: true
                },
                seniorname: {
                    required: true
                },
                address: {
                    required: true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    number: true,
                    min: 1
                },
                gender: {
                    required: true
                }
            },

            messages: {
                first_name: {
                    letterswithbasicpunc: "Please enter validate first name."
                },
                first_name: {
                    letterswithbasicpunc: "Please enter validate first name."
                },
                mobile: {
                    min: "Please enter valid mobile number"
                }
            },


            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            },

            errorPlacement: function (error, element) {
                if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                    var controls = element.closest('div[class*="col-"]');
                    if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                    else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                }
                else if (element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                }
                else if (element.is('.chosen-select')) {
                    error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
                }
                else error.insertAfter(element.parent());
            },

            submitHandler: function (form) {
                // $('#create-user-form').submit();
                form.submit();
            },
            invalidHandler: function (form) {
            }
        });
    </script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/create.user.js')}}"></script>

@endsection