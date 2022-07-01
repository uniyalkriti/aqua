@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.company')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('location1')}}">{{Lang::get('common.company')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.company')}}</li>
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

                        <form class="form-horizontal" action="{{route('company.store')}}" method="POST" id="company-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status" style="text-align: left;">{{Lang::get('common.company')}} Name (<small style="color: red;">Please use one word and small as possible because it will attach with username </small>)</label>
                                                <input autocomplete="off" class="form-control" type="text" name="company_name" value="">
                                            </div>
                                        </div>


                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Title</label>
                                                <input autocomplete="off" class="form-control" type="text" name="title" maxlength="100" minlength="1" id="title">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Website</label>
                                                <input autocomplete="off" class="form-control" type="text" name="website" maxlength="100" minlength="1" id="website">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} E-mail</label>
                                                <input autocomplete="off" class="form-control" type="text" name="email" maxlength="100" minlength="1" id="email">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="control-label" for="contact_per_name">{{Lang::get('common.company')}} Contact Person Name</label>
                                            <input autocomplete="off" class="form-control" type="text" name="contact_per_name" maxlength="100" minlength="1" id="contact_per_name">
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="control-label" for="status">{{Lang::get('common.company')}} Address</label>
                                            <input autocomplete="off" class="form-control" type="text" name="address" maxlength="900" minlength="1" id="address">
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="control-label" for="status">{{Lang::get('common.company')}} Landline</label>
                                            <input autocomplete="off" class="form-control" type="number" name="landline" maxlength="900" minlength="1" id="landline">
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="control-label" for="status">{{Lang::get('common.company')}} Mobile Number</label>
                                            <input autocomplete="off" class="form-control" type="number" name="number" maxlength="100" minlength="1" id="number">
                                        </div>
                                        
                                        
                                    </div><br>
                                    <div class="row">
                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">Status</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3">
                                                <label class="control-label no-padding-right" for="user_name" style="text-align: left;">User Name(<small style="color: red;">Please Don't Use @ symbol because it take automatically so use one word </small>)</label>
                                                <input type="text" name="user_name" id="user_name" class="form-control">
                                            </div>
                                            
                                            <div class="col-xs-3">
                                                <label class="control-label " for="pass">Password</label>
                                                <input type="text" name="password" id="password" class="form-control">
                                            </div>
                                            <div class="col-xs-3">
                                                <label class="control-label no-padding-right" for="user_name">Designation</label>
                                                <input type="text" name="role" readonly="readonly" value="Super Admin" class="form-control">
                                            </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                            <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                    for="head_quarter"> Upload Image</label>
                                                <input type="file" class="form-control-file" name="imageFile" id="imageFile" aria-describedby="fileHelp" onchange="readURL(this);">
                                                
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                            <img id="company_image" src="#" height="200" width="150" />
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
                                    <button class="btn" type="button" onclick="document.location.href='{{url('company')}}'">
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
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#company_image')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

@endsection