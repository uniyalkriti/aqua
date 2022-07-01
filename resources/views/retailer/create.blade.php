@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.retailer_detail')}}</title>
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
                        <a href="{{url('retailer')}}">{{Lang::get('common.retailer')}} {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active">Add {{Lang::get('common.retailer_detail')}}</li>
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

                        <form class="form-horizontal" action="{{route($current_menu.'.store')}}" method="POST"
                              id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Location Details
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_1"> {{Lang::get('common.location1')}} <b style="color:red;">*</b></label>
                                        <select name="location_1" id="location_1" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($location1))
                                                @foreach($location1 as $l1_key=>$l1_data)
                                                    <option value="{{$l1_key}}">{{$l1_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_2"> {{Lang::get('common.location2')}} <b style="color:red;">*</b></label>
                                        <select name="location_2" id="location_2" class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_3"> {{Lang::get('common.location3')}} <b style="color:red;">*</b></label>
                                        <select name="location_3" id="location_3" class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_4"> {{Lang::get('common.location4')}} <b style="color:red;">*</b></label>
                                        <select name="location_4" id="location_4" class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_5"> {{Lang::get('common.location5')}} <b style="color:red;">*</b></label>
                                        <select name="location_5" id="location_5"
                                                class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_6"> {{Lang::get('common.location6')}} <b style="color:red;">*</b></label>
                                        <select name="location_6" id="location_6"
                                                class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_7"> {{Lang::get('common.location7')}} <b style="color:red;">*</b></label>
                                        <select name="location_7" id="location_7"
                                                class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="distributor"> {{Lang::get('common.distributor')}} <b style="color:red;">*</b></label>
                                        <select name="distributor" id="distributor"
                                                class="form-control input-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                {{Lang::get('common.retailer_detail')}}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="retailer_name"> {{Lang::get('common.retailer')}} Name <b style="color:red;">*</b></label>
                                        <input type="text" placeholder="Enter Retailer Name" name="retailer_name"
                                               id="retailer_name" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="address">{{Lang::get('common.address')}} <b style="color:red;">*</b></label>
                                        <textarea name="address" id="address" class="form-control input-sm"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="landline">{{Lang::get('common.landline')}}</label>
                                        <input placeholder="Enter Landline" type="text" name="landline" id="landline" class="form-control input-sm vnumerror" maxlength="12">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="mobile">{{Lang::get('common.user_contact')}} <b style="color:red;">*</b></label>
                                        <input placeholder="Enter Mobile" type="text" required="required" name="mobile" id="mobile" class="form-control input-sm vnumerror" maxlength="10">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="email">{{Lang::get('common.email')}} <b style="color:red;">*</b></label>
                                        <input placeholder="Enter Email" type="email" required="required" name="email" id="email" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right">{{Lang::get('common.pin_no')}} <b style="color:red;">*</b></label>
                                        <input type="text" name="pin_no" required="required" id="pin_no" class="form-control input-sm vnumerror" placeholder="Pin No" maxlength="6">
                                        <span id="pin_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="image">{{Lang::get('common.image')}}</label>
                                        <input type="file" accept="Image/*" name="retailer_image" id="retailer_image" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="tin_no">{{Lang::get('common.gst_no')}} <b style="color:red;">*</b></label>
                                        <input type="text" name="tin_no" id="tin_no" class="form-control input-sm verror" placeholder="Tin No" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="avg_per_month">{{Lang::get('common.avg_month')}}</label>
                                        <input type="text" name="avg_per_month" id="avg_per_month" class="form-control input-sm rate" placeholder="Avg.Per Month Purchase">
                                    </div>
                                </div>
                                 <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="outlet_type">{{Lang::get('common.retailer_type')}} <b style="color:red;">*</b></label>
                                        <select name="outlet_type" id="outlet_type" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($outlet_type))
                                                @foreach($outlet_type as $oid=>$outlet)
                                                    <option value="{{$oid}}">{{$outlet}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right">{{Lang::get('common.user_name')}}</label>
                                        <input type="text" name="user_name"  class="form-control input-sm " placeholder="User name" >
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right">{{Lang::get('common.password')}}</label>
                                        <input type="text" name="password"  class="form-control input-sm " placeholder="Password" >
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="is_golden">Is Golden <b style="color:red;">*</b></label>
                                        <select name="is_golden" id="is_golden" class="form-control input-sm">
                                            <option value="0">NO</option>
                                            <option value="1">YES</option>
                                        </select>
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
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('retailer')}}'"
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
        <script type="text/javascript">
    $('.verror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
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
      $('.valphaerror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[0-9`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[0-9`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
      $('.rate').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
     $('#pin_no').change(function(){
        var pin_no = $(this).val();
        var color = 'red';
        if (pin_no!=''){
        if(pin_no.length<6){
        $('#pin_error').html('PIN No should be 6 Digit');
        $('#pin_error').css('color', color);
        }
       }
      });
    </script>
@endsection