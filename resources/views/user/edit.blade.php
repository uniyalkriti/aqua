@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.user_detail')}}</title>
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
                        <a href="{{url('user')}}">{{Lang::get('common.user')}} {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active">{{Lang::get('common.edit')}} {{Lang::get('common.user_detail')}}</li>
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

                        {{--<form class="form-horizontal" action="{{route('user.store')}}" method="POST" id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">--}}
                            {{--{!! csrf_field() !!}--}}
                        <div class="row">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> First Name <b style="color: red;">*</b></label>
                                    <input required="required" placeholder="First Name" value="{{$person->first_name}}" type="text" id="first_name" name="first_name" class="form-control input-sm"/>
                                </div>
                            </div>
                          <!--   <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Middle Name</label> -->
                                    <input placeholder="Middle Name" value="{{$person->middle_name}}" type="hidden" id="middle_name" name="middle_name" class="form-control input-sm"/>
                             <!--    </div>
                            </div> -->
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Last Name <b style="color: red;">*</b></label>
                                    <input required="required" placeholder="Last Name" value="{{$person->last_name}}" type="text" id="last_name" name="last_name" class="form-control input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> {{Lang::get('common.company')}} <b style="color: red;">*</b></label>
                                    <select required="required" class="form-control input-sm" name="company">
                                        <option value="">Select</option>
                                        @if(!empty($company))
                                            @foreach($company as $cid=>$cdata)
                                                <option {{$person->company_id==$cid?'selected':''}} value="{{$cid}}">{{$cdata}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @php
                            $user_name_first_part = '';
                            $user_name = !empty($personLogin->person_username)?$personLogin->person_username:'';
                            if(!empty($user_name))
                            {
                                $explode_user_name = explode('@',$user_name);
                                $user_name_first_part = $explode_user_name[0];
                            }
                            @endphp
                            <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> {{Lang::get('common.user_name')}} <b style="color: red;">*</b></label>
                                        <input type="text" id="username" name="email"
                                               placeholder="User Name"  value="{{$user_name_first_part}}" class="form-control input-sm "/>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="Password"> {{Lang::get('common.password')}} <b style="color: red;">*</b></label>
                                        <input type="text" id="password" name="password"
                                               placeholder="Password" value="{{$personLogin->person_password}}" class="form-control input-sm"/>
                                    </div>
                                </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> {{Lang::get('common.role_key')}} <b style="color: red;">*</b></label>
                                    <select required="required" class="form-control input-sm chosen-select" name="designation" id="designation">
                                        <option value="">Select Designation</option>
                                        @if(!empty($roles))
                                            @foreach($roles as $role_key=>$role_data)
                                                <?php
                                                    $role_id_p = !empty($person->role_id)?$person->role_id:'0';
                                                ?>
                                                <option {{$role_id_p==$role_key?'selected':''}} value="{{$role_key}}">{{$role_data}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Senior {{Lang::get('common.role_key')}} <b style="color: red;">*</b></label>
                                    <select required="required" class="form-control input-sm chosen-select" name="senior" id="senior">
                                        <option value="">Select Senior</option>
                                        @if(!empty($roles))
                                            @foreach($roles as $role_key=>$role_data)
                                            <?php
                                                    $role_id_s = !empty($person->role_id)?$person->role_id:'0';
                                                ?>
                                               <option {{$role_id_s==$role_key?'selected':''}} value="{{$role_key}}">{{$role_data}}</option> 
                                               {{-- <option value="{{$role_key}}">{{$role_data}}</option> --}}
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="senior_person"> {{Lang::get('common.senior_name')}} <b style="color: red;">*</b></label>
                                    <select required="required" class="form-control input-sm chosen-select" name="senior_person" id="senior_person">
                                        <option value="">Select Senior</option>
                                        <option value="{{!empty($senior->id)?$senior->id:''}}" selected>{{!empty($senior->senior_name)?$senior->senior_name:''}}</option> 
                                        {{--@if(!empty($user))--}}
                                            {{--@foreach($user as $u_key=>$u_data)--}}
                                                {{--<option value="{{$u_data->id}}">{{$u_data->first_name.' '.$u_data->middle_name.' '.$u_data->last_name}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--@endif--}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> {{Lang::get('common.status')}} </label>
                                    <select required="required" class="form-control input-sm chosen-select" name="status" id="status">
                                        <option {{$person->status==1?'selected':''}} value="1">Active</option>
                                        <option {{$person->status==1?'':'selected'}} value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> {{Lang::get('common.email')}} <b style="color: red;">*</b></label>
                                    <input value="{{$person->email}}"    placeholder="Enter Email" type="email" id="email" name="email_o" class="form-control input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> {{Lang::get('common.user_contact')}} <b style="color: red;">*</b></label>
                                    <input value="{{$person->mobile}}" required="required" placeholder="Enter Mobile Number" type="text" id="mobile" name="mobile" class="form-control input-sm"/>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.location3')}} <b style="color: red;">*</b></label>
                                        <select name="state" id="location_3" class="form-control input-sm chosen-select">
                                            <option value="">Select</option>
                                            @if(!empty($state))
                                                @foreach($state as $sid=>$skey)
                                                    <option {{$person->state_id==$sid?'selected':''}} value="{{$sid}}">{{$skey}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quater"> {{Lang::get('common.location5')}} <b style="color: red;">*</b></label>
                                            <select name="head_quater" id="location_5" class="form-control input-sm chosen-select">
                                                <option value="">Select</option>
                                                @if(!empty($head_quater))
                                                    @foreach($head_quater as $sid=>$skey)
                                                        <option {{$person->head_quater_id==$sid?'selected':''}} value="{{$sid}}">{{$skey}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quater"> {{Lang::get('common.location6')}} <b style="color: red;">*</b></label>
                                            <select required="required" name="location_6" id="location_6" class="form-control input-sm chosen-select">
                                                <option value="">Select</option>
                                                @if(!empty($location_6))
                                                    @foreach($location_6 as $sid=>$skey)
                                                        <option {{$person->town_id==$sid?'selected':''}} value="{{$sid}}">{{$skey}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.emp_code')}} </label>
                                        <input type="text" value="{{$person->emp_code}}" name="emp_code" id="emp_code" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.user_address')}}</label>
                                        <textarea name="address" id="address" class="form-control input-sm">{{$personDetails->address}}</textarea>
                                    </div>
                                </div>


                                  <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="residential_lat_lng"> Residential Latitude/Longitude (Lat,Lng)</label>
                                            <input value="{{$personDetails->residential_lat_lng}}" type="text" placeholder="Select (Lat,Lng)" name="residential_lat_lng" id="residential_lat_lng"  class="form-control input-sm vnumerrorcus" >
                                        </div>
                                    </div>
                               
                            
                            

                            </div>
                            <div class="row">
                                 <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="joining_date"> {{Lang::get('common.joining_date')}} <b style="color: red;">*</b></label>
                              <!--       <input required="required" value="{{$person->joining_date}}" type="date" id="joining_date" name="joining_date"
                                           class="form-control input-sm"/> -->

                                   <input value="{{$person->joining_date}}" required="required" type="text" placeholder="Select Date" name="joining_date" id="joining_date"  class="form-control date-picker input-sm" >
                                </div>
                            </div>



                                 <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Weekly Off <b style="color: red;">*</b></label>
                                        <select required="required" class="chosen-select form-control input-sm" name="weekly">
                          

                                                    <option {{$person->weekly_off_data==1?'selected':''}} value="1">Monday</option>
                                                    <option {{$person->weekly_off_data==2?'selected':''}} value="2">Tuesday</option>
                                                    <option {{$person->weekly_off_data==3?'selected':''}} value="3">Wednesday</option>
                                                    <option {{$person->weekly_off_data==4?'selected':''}} value="4">Thursday</option>
                                                    <option {{$person->weekly_off_data==5?'selected':''}} value="5">Friday</option>
                                                    <option {{$person->weekly_off_data==6?'selected':''}} value="6">Saturday</option>
                                                    <option {{$person->weekly_off_data==7?'selected':''}} value="7">Sunday</option>
                                              
                                        </select>
                                    </div>
                                </div>
                                @if(COUNT($product_rate_list_assign_part)>0)
                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="module"> Rate List Asiign <b style="color: red;">*</b></label>
                                            <select required="required" class="form-control input-sm chosen-select" name="rate_list_flag"
                                                    id="status">
                                                <option  value="">Select</option>
                                                <option {{$person->rate_list_flag==1?'selected':''}} value="1">State</option>
                                                <option {{$person->rate_list_flag==2?'selected':''}} value="2">Distributor</option>
                                                <option {{$person->rate_list_flag==3?'selected':''}} value="3">SS</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quarter"> Upload {{Lang::get('common.image')}}</label>
                                         <input type="file" class="form-control-file" name="imageFile" id="imageFile" aria-describedby="fileHelp" onchange="readURL(this);">
                                         
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <img id="user_image" src="{{ ($personLogin->person_image) }}" height="200" width="150" />
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
                                <button class="btn btn-danger btn-sm" onclick="window.location='{{url('user')}}'" type="button">
                                    <i class="ace-icon fa fa-close bigger-110"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}

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
    <!-- <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script> -->
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('js/user.js')}}"></script>
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
    <script type="text/javascript">
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#user_image')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
     $("#joining_date").datetimepicker  ( {

    format: 'YYYY-MM-DD'
    });
    </script>

     <script>

        $(document).on('change', '#location_3', function () {
            _current_val = $(this).val();
            custom_location_data(_current_val,5);
        });

        $(document).on('change', '#location_5', function () {
            _current_val = $(this).val();
            custom_location_data(_current_val,6);
        });
       

        function custom_location_data(val,level) {
            _append_box=$('#location_'+level);
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/statndard_filter_onchange',
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
                                    template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
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
        $('.vnumerrorcus').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'"<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'"<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    </script>
@endsection