@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.dealer_detail')}}</title>
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
                        <a href="{{url('distributor')}}">{{Lang::get('common.distributor')}}</a>
                    </li>

                    <li class="active">Add {{Lang::get('common.dealer_detail')}}</li>
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
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                {{Lang::get('common.dealer_detail')}}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="code"> {{Lang::get('common.distributor')}} Code <b style="color: red;">*</b></label>
                                        <input required="required" type="text" id="code" name="code"
                                               class="form-control input-sm"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> {{Lang::get('common.distributor')}} Name<b style="color: red;">*</b></label>
                                        <input required="required" type="text" id="firm_name" name="firm_name"
                                               class="form-control input-sm"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Contact Person<b style="color: red;">*</b></label>
                                        <input required="required" type="text" id="contact_person" name="contact_person"
                                               class="form-control input-sm valphaerror"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> {{Lang::get('common.address')}}</label>
                                        <textarea name="address" class="form-control input-sm" id="address"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="joining_date"> {{Lang::get('common.landline')}}</label>
                                        <input  type="text" id="landline" name="landline"
                                               class="form-control input-sm vnumerror" maxlength="12" />
                                    </div>
                                </div>
                                @if($company_id == 52)
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quarter">{{Lang::get('common.email')}} </label>
                                        <input  type="email" id="email" name="email" class="form-control input-sm"/>
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="head_quarter">{{Lang::get('common.email')}} <b style="color: red;">*</b></label>
                                        <input  required="required" type="email" id="email" name="email" class="form-control input-sm"/>
                                    </div>
                                </div>
                                @endif
                                
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="pin_no">{{Lang::get('common.pin_no')}} <b style="color: red;">*</b></label>
                                        <input required="required" type="text" id="pin_no" name="pin_no" class="form-control input-sm vnumerror" maxlength="6"/>
                                        <span id="pin_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="tin_no">{{Lang::get('common.gst_no')}} </label>
                                        <input type="text" id="tin_no" name="tin_no" class="form-control input-sm"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="mobile">{{Lang::get('common.user_contact')}} <b style="color: red;">*</b></label>
                                        <input required="required" type="text" id="mobile" name="mobile" class="form-control input-sm vnumerror" maxlength="10"/>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="ownership_type"> {{Lang::get('common.ownership_type')}} <b style="color: red;">*</b></label>
                                        <select class="form-control input-sm" name="ownership_type" id="ownership_type">
                                            <option value="">Select</option>
                                            @if(!empty($ownership))
                                                @foreach($ownership as $k=>$d)
                                                    <option value="{{$k}}">{{$d}}</option>
                                                    @endforeach
                                                @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="avg_per_month">{{Lang::get('common.avg_month')}}</label>
                                        <input placeholder="Enter Avg. Per Month Purchase" type="text" id="avg_per_month"
                                               name="avg_per_month" class="form-control input-sm rate"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="town_id"> {{Lang::get('common.location6')}} <b style="color: red;">*</b></label>
                                        <select required="required" name="town_id" id="town_id" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($location6))
                                                @foreach($location6 as $l6_key=>$l6_data)
                                                    <option value="{{$l6_key}}">{{$l6_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($company_id == 52)
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="pin_no">Pan No </label>
                                            <input  type="text" id="pan_no" name="pan_no" value="0" class="form-control input-sm " />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Aadhar No </label>
                                            <input  type="text" id="aadar_no" name="aadar_no" value="0" class="form-control input-sm" maxlength="15"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="avg_per_month"> Food License </label>
                                            <input  placeholder="Enter  Food License" type="text" id="food_license"
                                                   name="food_license" class="form-control input-sm "value="NA"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="town_id"> Vehicle Details <b style="color: red;">*</b></label>
                                            <select required="required" multiple name="vehicle_details_array[]" id="vehicle_details_array" class="form-control input-sm chosen-select">
                                                <option value="">Select</option>
                                                @if(!empty($vehicle_details))
                                                    @foreach($vehicle_details as $l6_key=>$l6_data)
                                                        <option value="{{$l6_key}}">{{$l6_data}}</option>
                                                    @endforeach
                                                @endif
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
                                                    Security Details
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="pin_no">Bank Name </label>
                                            <input  type="text" placeholder="Enter Bank Name" id="bank_name" name="bank_name" value="NA" class="form-control input-sm " />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Security Amount</label>
                                            <input  type="text" id="security_amt" name="security_amt" value="0" class="form-control input-sm" maxlength="15"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Reference no.</label>
                                            <input  type="text" id="refrence_no" name="refrence_no"  value="0"class="form-control input-sm" maxlength="30"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Security Date</label>
                                            <input  type="text" id="security_date" name="security_date" class="form-control input-sm date-picker" maxlength="15"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Receipt Issue Date</label>
                                            <input  type="text" id="reciept_issue_date" name="reciept_issue_date" class="form-control input-sm date-picker" maxlength="15"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="avg_per_month"> Remarks</label>
                                            <input  placeholder="Enter  Remarks" type="text" id="security_remarks"
                                                   name="security_remarks" class="form-control input-sm " value="NA"/>
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
                                                    Agreement/Certificate Status
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Commencement Date</label>
                                            <input  type="text" id="commencement_date" name="commencement_date" class="form-control input-sm date-picker" maxlength="15"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Termination Date</label>
                                            <input  type="text" id="termination_date" name="termination_date" class="form-control input-sm date-picker" maxlength="15"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Certificate Issue Date</label>
                                            <input  type="text" id="certificate_issue_date" name="certificate_issue_date" class="form-control input-sm date-picker" maxlength="15"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="avg_per_month"> Remarks</label>
                                            <input  placeholder="Enter  Remarks" type="text" id="agreement_remarks"
                                                   name="agreement_remarks" class="form-control input-sm " value="NA"/>
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
                                                    Security Refund Details
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Refund Amount<</label>
                                            <input  type="text" id="refund_amt" name="refund_amt" class="form-control input-sm" value="0"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Refund Ref No.</label>
                                            <input  type="text" id="refund_ref_no" name="refund_ref_no" class="form-control input-sm "  value="0"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="tin_no">Refund Date</label>
                                            <input  type="text" id="refund_date" name="refund_date" class="form-control input-sm date-picker" />
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="avg_per_month"> Remarks </label>
                                            <input  placeholder="Enter  Remarks" type="text" id="refund_remarks"
                                                   name="refund_remarks" class="form-control input-sm " value="NA"/>
                                        </div>
                                    </div>

                                </div>


                            @endif
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
                                <div class="col-lg-12">
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_1"> {{Lang::get('common.location1')}} <b style="color: red;">*</b></label>
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
                                                for="location_2"> {{Lang::get('common.location2')}} <b style="color: red;">*</b></label>
                                            <select name="location_2" id="location_2" class="form-control input-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_3"> {{Lang::get('common.location3')}} <b style="color: red;">*</b></label>
                                            <select name="location_3" id="location_3" class="form-control input-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_4"> {{Lang::get('common.location4')}} <b style="color: red;">*</b></label>
                                            <select name="location_4" id="location_4" class="form-control input-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_5"> {{Lang::get('common.location5')}} <b style="color: red;">*</b></label>
                                            <select  name="location_5" id="location_5"
                                                    class="form-control input-sm ">
                                                <option disabled="disabled" value="">Select</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_6"> {{Lang::get('common.location6')}} <b style="color: red;">*</b></label>
                                            <select required="required" multiple name="location_6[]" id="location_6"
                                                    class="form-control input-sm chosen-select">
                                                <option disabled="disabled" value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="location_7"> {{Lang::get('common.location7')}} <b style="color: red;">*</b></label>
                                            <select required="required" multiple name="location_7[]" id="location_7"
                                                    class="form-control input-sm chosen-select">
                                                <option disabled="disabled" value="">Select</option>
                                            </select>
                                        </div>
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
                                                {{Lang::get('common.csa')}} {{Lang::get('common.details')}} 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                for="csa"> {{Lang::get('common.csa')}} Name <b style="color: red;">*</b></label>
                                            <select name="csa" id="csa_id" class="form-control input-sm">
                                                <option value="">Select</option>
                                                @if(!empty($csa))
                                                    @foreach($csa as $csa_key=>$csa_data)
                                                        <option value="{{$csa_key}}">{{$csa_data}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @if($assign_price_list>0)
                                        @if($company_id == 52)
                                            <div class="col-lg-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                        for="template_product_id"> Template Name </label>
                                                    <select  name="template_product_id" id="template_product_id" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                        @if(!empty($template_product))
                                                            @foreach($template_product as $csa_key=>$csa_data)
                                                                <option value="{{$csa_key}}">{{$csa_data}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-2">
                                                <div class="">
                                                    <label class="control-label no-padding-right"
                                                        for="template_product_id"> Template Name <b style="color: red;">*</b></label>
                                                    <select required="required" name="template_product_id" id="template_product_id" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                        @if(!empty($template_product))
                                                            @foreach($template_product as $csa_key=>$csa_data)
                                                                <option value="{{$csa_key}}">{{$csa_data}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                   
                                </div>
                            </div>
                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('distributor')}}'"
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
    <script src="{{asset('js/dealer.js')}}"></script>
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
       $("#refund_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#security_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#reciept_issue_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#commencement_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#termination_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#certificate_issue_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
    </script>

@endsection