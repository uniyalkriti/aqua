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

                    <li class="active">{{Lang::get('common.add')}} {{Lang::get('common.user_detail')}}</li>
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

                        <form class="form-horizontal" action="{{route('user.store')}}" method="POST"
                              id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> First Name <b style="color: red;">*</b></label>
                                        <input placeholder="First Name" required="required" type="text" id="first_name" name="first_name"
                                               class="form-control input-sm"/>
                                    </div>
                                </div>
                               <!--  <div class="col-xs-2">
                                    <div class=""> -->
                                     <!--    <label class="control-label no-padding-right"
                                               for="module"> Middle Name</label> -->
                                        <input placeholder="Middle Name" type="hidden" id="middle_name" name="middle_name"
                                               class="form-control input-sm"/>
                                   <!--  </div>
                                </div> -->
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Last Name <b style="color: red;">*</b></label>
                                        <input placeholder="Last Name" required="required" type="text" id="last_name" name="last_name"
                                               class="form-control input-sm"/>
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
                                                    <option value="{{$cid}}">{{$cdata}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> {{Lang::get('common.user_name')}} <b style="color: red;">*</b></label>
                                        <input type="text" id="username" name="email"
                                               placeholder="User Name" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="Password"> {{Lang::get('common.password')}} <b style="color: red;">*</b></label>
                                        <input type="text" id="password" name="password"
                                               placeholder="Password" class="form-control input-sm"/>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> {{Lang::get('common.role_key')}} <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm chosen-select" name="designation"
                                                id="designation">
                                            <option value="">Select Designation</option>
                                            @if(!empty($roles))
                                                @foreach($roles as $role_key=>$role_data)
                                                    <option value="{{$role_key}}">{{$role_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Senior {{Lang::get('common.role_key')}} <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm chosen-select" name="senior"
                                                id="senior">
                                            <option value="">Select Senior</option>
                                            @if(!empty($roles))
                                                @foreach($roles as $role_key=>$role_data)
                                                    <option value="{{$role_key}}">{{$role_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="senior_person"> {{Lang::get('common.senior_name')}} <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm chosen-select" name="senior_person"
                                                id="senior_person">
                                            <option value="">Select Senior</option>
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
                                               for="module"> {{Lang::get('common.status')}}</label>
                                        <select required="required" class="form-control input-sm chosen-select" name="status"
                                                id="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> {{Lang::get('common.email')}} <b style="color: red;">*</b></label>
                                        <input placeholder="Enter Email" type="email" id="email" name="email_o"
                                               class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> {{Lang::get('common.user_contact')}} <b style="color: red;">*</b></label>
                                        <input required="required" placeholder="Enter Mobile Number" type="text"
                                               id="mobile" name="mobile" class="form-control input-sm vnumerror" maxlength="10" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.location3')}} <b style="color: red;">*</b></label>
                                        <select required="required" name="state" id="location_3" class="form-control chosen-select input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($state))
                                                @foreach($state as $sid=>$skey)
                                                    <option value="{{$sid}}">{{$skey}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quarter">{{Lang::get('common.location5')}} <b style="color: red;">*</b></label>
                                        <select required="required" name="head_quater" id="location_5" class="form-control chosen-select input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($head_quater))
                                                @foreach($head_quater as $sid=>$skey)
                                                    <option value="{{$sid}}">{{$skey}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quarter">{{Lang::get('common.location6')}}<b style="color: red;">*</b></label>
                                        <select required="required" name="location_6" id="location_6" class="form-control chosen-select input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($location_6))
                                                @foreach($location_6 as $sid=>$skey)
                                                    <option value="{{$sid}}">{{$skey}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.emp_code')}}</label>
                                        <input placeholder="Employee Code" type="text" name="emp_code" id="emp_code" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="module"> {{Lang::get('common.user_address')}}</label>
                                        <textarea name="address" id="address" class="form-control input-sm"></textarea>
                                    </div>
                                </div>

                                 <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="residential_lat_lng"> Residential Latitude/Longitude (Lat,Lng)</label>
                                            <input type="text" placeholder="Select (Lat,Lng)" name="residential_lat_lng" id="residential_lat_lng"  class="form-control input-sm vnumerrorcus" >
                                        </div>
                                    </div>
                             
                           
                               
                                </div>
                                <div class="row">

                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="joining_date"> {{Lang::get('common.joining_date')}} <b style="color: red;">*</b></label>
                                           <!--  <input required="required" type="date" id="joining_date" name="joining_date"
                                                   class="form-control input-sm"/> -->

                                            <input  required="required" type="text" placeholder="Select Date" name="joining_date" id="joining_date"  class="form-control date-picker input-sm" >
                                        </div>
                                    </div>


                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="module"> Weekly Off <b style="color: red;">*</b></label>
                                            <select required="required" class="chosen-select form-control input-sm" name="weekly">
                                              
                                                        <option value="1">Monday</option>
                                                        <option value="2">Tuesday</option>
                                                        <option value="3">Wednesday</option>
                                                        <option value="4">Thrusday</option>
                                                        <option value="5">Friday</option>
                                                        <option value="6">Saturday</option>
                                                        <option value="7" selected="selected">Sunday</option>
                                                  
                                            </select>
                                        </div>
                                    </div>
                                    @if(COUNT($product_rate_list_assign_part)>0)
                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="module"> Rate List Asign <b style="color: red;">*</b></label>
                                            <select required="required" class="form-control input-sm chosen-select" name="rate_list_flag"
                                                    id="status">
                                                <option value="">Select</option>
                                                <option value="1">State</option>
                                                <option value="2">Distributor</option>
                                                <option value="3">SS</option>
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
                                            <img id="user_image" src="#" height="200" width="150" />
                                        </div>
                                    </div>
                            </div>
                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('user')}}'"
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
    <script src="{{asset('js/user.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script>
        $('.vnumerror').keyup(function()
        {
            var yourInput = $(this).val();
            re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(yourInput);
            if(isSplChar)
            {
                var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
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