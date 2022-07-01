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

                            {!! Form::open(array('route'=>['company.update',$encrypt_id] , 'method'=>'PUT','id'=>'company-form','role'=>'form','enctype'=>'multipart/form-data' ))!!}


                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">{{Lang::get('common.company')}} Name (<small style="color: red;">Please use one word and small as possible because it will attach with username </small>)</label>
                                                    <input autocomplete="off"  class="form-control" type="text"  name="company_name" value="{{!empty($company->name)?$company->name:''}}">
                                                </div>
                                            </div>


                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">{{Lang::get('common.company')}} Title</label>
                                                    <input autocomplete="off"  class="form-control" type="text" name="title" value="{{!empty($company->title)?$company->title:''}}" maxlength="100" minlength="1" id="title">
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">{{Lang::get('common.company')}} Website</label>
                                                    <input  autocomplete="off" class="form-control" type="text" name="website" maxlength="100" value="{{!empty($company->website)?$company->website:''}}" minlength="1" id="website">
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="">
                                                    <label class="control-label" for="status">{{Lang::get('common.company')}} E-mail</label>
                                                    <input  autocomplete="off" class="form-control" type="text" name="email" value="{{!empty($company->email)?$company->email:''}}" maxlength="100" minlength="1" id="email">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Address</label>
                                                <input  autocomplete="off" class="form-control" type="text" name="address" value="{{!empty($company->address)?$company->address:''}}" maxlength="900" minlength="1" id="address">
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Mobile Number</label>
                                                <input  autocomplete="off" class="form-control" type="number" name="number" value="{{!empty($company->other_numbers)?$company->other_numbers:''}}" maxlength="100" minlength="1" id="number">
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Mobile Landline</label>
                                                <input  autocomplete="off" class="form-control" type="number" name="landline" value="{{!empty($company->landline)?$company->landline:''}}" maxlength="100" minlength="1" id="landline">
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="control-label" for="status">{{Lang::get('common.company')}} Contact Person Name</label>
                                                <input  autocomplete="off" class="form-control" type="text" name="contact_per_name" value="{{!empty($company->contact_per_name)?$company->contact_per_name:''}}" maxlength="100" minlength="1" id="contact_per_name">
                                            </div>
                                           
                                        </div>
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
                                                <label class="control-label no-padding-right" for="user_name">User Name (<small style="color: red;">Please Don't Use @ symbol because it take automatically so use one word </small>)</label>
                                                <?php
                                                    $user_name = !empty($company->user_name)?str_replace('@'.$company->name, '', $company->user_name):'';
                                                ?>
                                                <input type="text" name="user_name" id="user_name" value="{{$user_name}}" class="form-control">
                                            </div>
                                                <input type="hidden" name="user_id" value="{{$company->user_id}}">
                                            <div class="col-xs-3">
                                                <label class="control-label " for="pass">Password</label>
                                                <input type="text" name="password" id="password" value="{{!empty($company->pass)?$company->pass:''}}" class="form-control">
                                            </div>
                                            <div class="col-xs-3">
                                                <label class="control-label " for="pass">Forcefully Update Status (<small style="color: red;">Please Don't on before consult with Bhoopendera Sir It's Criticall </small>)</label>
                                                <div class="widget-toolbar no-border" style="margin: 0 60% 0 0;">
                                                    <input name="manual_on_off_forcefully" value="1" type="checkbox" {{($company->manual_on_off_forcefully == 1)?'checked':''}} class="ace ace-switch ace-switch-6" >
                                                    <span class="lbl middle"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-xs-3">
                                                <label class="control-label " for="pass">Display Message on app</label>
                                                    <input name="message_dynamic" class="form-control" type="text" {{!empty($company->message_dynamic)?$company->message_dynamic:''}} class="ace ace-switch ace-switch-6" >
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
                                            Update
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