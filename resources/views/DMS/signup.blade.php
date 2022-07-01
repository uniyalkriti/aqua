@extends('layouts.core_php_heade')
@section('dms_body')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
    <style type="text/css">
        .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    }
    .title-class{
            
            border-color: white; 
            color: black;
            font-weight: bolder; 
            font-variant-caps: all-small-caps; 
    }
    .control-label{
        font-weight: bold;
    }
    </style>
    

    <div class="main-content" >
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('user')}}"></a>
                    </li>

                    <li class="active">Signup</li>
                </ul>

            </div>

            <div class="page-content" style="font-family: 'Times New Roman',Times ,serif; background-color: #f8f8f8;">
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

                        <form class="form-horizontal" action="DmsSignup" method="POST"
                              id="{{$current_menu}}-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row" >
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3" >
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3" style="border-color: white;">
                                        <div class="widget-header widget-header-small title-class" style="background-color: #90d781; color: black; ">
                                            <h4 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Firm Info
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Full Name of firm <b style="color: red;">*</b></label>
                                        <input title="Max. Character 200 Only" placeholder="Full Name" required="required" type="text" id="first_name" name="full_name_of_firm"
                                               class="form-control input-sm" value="{{ old('full_name_of_firm') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Address of Firm with Phone No., Fax No <b style="color: red;">*</b></label>
                                        <textarea title="Max. Character 2000 Only" placeholder="Enter Address of Firm with Phone No., Fax No" required="required"  type="text" id="middle_name" name="address_of_firm"
                                               class="form-control input-sm" >{{ old('address_of_firm') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                               
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Firm Type: <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm" name="firm_type">
                                            <option value="">Select</option>
                                            
                                            <option value="Partnership">Partnership</option>
                                            <option value="Proprietorship">Proprietorship</option>
                                            <option value="Private Ltd Company">Private Ltd Company</option>

                                        </select>
                                    </div>
                                </div>
                                
                                
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3" style="border-color: white;"> 
                                        <div class="widget-header widget-header-small title-class" style="background-color: #90d781;  color: black;">
                                            <h4 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Firm Promoter Information
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Full Name (es) Of Proprietor/Partners/Managing Director: <b style="color: red;">*</b></label>
                                        <input title="Max. Character 200 Only" placeholder="Full Name" required="required" type="text" id="first_name" name="partner_manager_director_name"
                                               class="form-control input-sm" value="{{ old('partner_manager_director_name') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Residential Address(es) with Mobile No.& Email ID: <b style="color: red;">*</b></label>
                                        <textarea title="Max. Character 500 Only" placeholder="Enter Residential Address " required="required" type="text" id="middle_name" name="res_add"
                                               class="form-control input-sm" value="{{ old('res_add') }}"></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Permanent Address::  <b style="color: red;">*</b></label>
                                        <textarea title="Max. Character 500 Only" placeholder="Enter Permanent Address " required="required" type="text" id="middle_name" name="perm_add"
                                               class="form-control input-sm" value="{{ old('perm_add') }}"></textarea>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> PAN No.: <b style="color: red;">*</b></label>
                                        <input placeholder="Pan no" required="required" type="text" id="pan_no" name="pan_no"
                                               class="form-control input-sm vnumerrorpa" value="{{ old('pan_no') }}" maxlength="10"/><span id="messagepa"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3" style="border-color: white;">
                                        <div class="widget-header widget-header-small title-class" style="background-color: #90d781; color: black; ">
                                            <h4 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Contact Info
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Contact Person Name & Designation:: <b style="color: red;">*</b></label>
                                        <input title="Max. Character 50 Only" placeholder="Enter Contact Person Name" required="required" type="text" id="first_name" name="p_contact_person_name"
                                               class="form-control input-sm" value="{{ old('p_contact_person_name') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Phone no: </label>
                                        <input placeholder="Enter Phone no"  type="text" id="p_phone_no" name="p_phone_no"
                                               class="form-control input-sm vnumerrorp" value="{{ old('p_phone_no') }}" maxlength="15"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Mobile No.: <b style="color: red;">*</b></label>
                                        <input placeholder="Enter Mobile No.:" required="required" type="text" id="p_mobile_no" name="p_mobile_no" 
                                               class="form-control input-sm vnumerrorm" value="{{ old('p_mobile_no') }}" maxlength="10"/><span id="messagem"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Fax No.: </label>
                                        <input title="Max. Character 20 Only" placeholder="Enter Fax No.:"  type="text" id="first_name" name="p_fax_no"
                                               class="form-control input-sm" value="{{ old('p_fax_no') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> E-Mail: <b style="color: red;">*</b></label>
                                        <input title="Max. Character 50 Only" placeholder="Enter E-Mail" required="required" type="text" id="first_name" name="p_email"
                                               class="form-control input-sm" value="{{ old('p_email') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Type of products presently being handled: <b style="color: red;">*</b> </label>
                                        <textarea title="Max. Character 600 Only" placeholder="Enter Type of products presently being handled " required="required" type="text" id="middle_name" name="type_of_product_handled"
                                               class="form-control input-sm">{{ old('type_of_product_handled') }}</textarea>
                                    </div>
                                </div>
                                
                                
                            </div>



                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3" style="border-color: white;">
                                        <div class="widget-header widget-header-small title-class" style="background-color: #90d781; color: black; ">
                                            <h4 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Statutory Requirement
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> DL No. (Wholesale): </label>
                                        <input title="Max. Character 200 Only" placeholder="Enter DL No"  type="text" id="first_name" name="dl_no_wholesale"
                                               class="form-control input-sm" value="{{ old('dl_no_wholesale') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> DL No. (Retail): </label>
                                        <input title="Max. Character 200 Only" placeholder="Enter DL No"  type="text" id="first_name" name="dl_no_retail"
                                               class="form-control input-sm" value="{{ old('dl_no_retail') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Narcotic Drug Licence No.: </label>
                                        <input title="Max. Character 30 Only" placeholder="Enter Narcotic Drug Licence No"  type="text" id="first_name" name="narcotic_no"
                                               class="form-control input-sm" value="{{ old('narcotic_no') }}"/>
                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> GSTIN of the firm: <b style="color: red;">*</b></label>
                                        <input placeholder="Enter TIN No. / S.T. No. of the firm:" required="required" type="text" id="tin_no" name="tin_no"
                                               class="form-control input-sm vnumerrorg" value="{{ old('tin_no') }}" maxlength="15"/><span id="messageg"></span>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module">Central Trade Tax No.: </label>
                                        <input title="Max. Character 200 Only" placeholder="Enter Central Trade Tax No"  type="text" id="first_name" name="central_trade_no"
                                               class="form-control input-sm" value="{{ old('central_trade_no') }}" />
                                    </div>
                                </div>
                                <div class="col-xs-5">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Banker's Name Address with firm A/C no with Name of Account operating person:</label>
                                        <textarea title="Max. Character 300 Only" placeholder="Enter Banker's Name Address with firm A/C number with Name of Account operating person:" type="text" id="middle_name" name="banker_name"
                                               class="form-control input-sm">{{ old('banker_name') }}</textarea>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3" style="border-color: white;">
                                        <div class="widget-header widget-header-small title-class" style="background-color: #90d781;  color: black;">
                                            <h4 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Details Of Security Deposit Offered
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Amount </label>
                                        <input title="Max. Character 50 Only" placeholder="Enter Amount"  type="text" id="first_name" name="amount"
                                               class="form-control input-sm" value="{{ old('amount') }}" />
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> D.D. No.: </label>
                                        <input title="Max. Character 50 Only" placeholder="Enter D.D. No.:"  type="text" id="first_name" name="dd_no"
                                               class="form-control input-sm" value="{{ old('dd_no') }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Date </label>
                                        <input placeholder="Full Amount"  type="date" id="first_name" name="sec_deposit_date"
                                               class="form-control input-sm" value="{{ old('sec_deposit_date') }}"/>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Bank: </label>
                                        <input title="Max. Character 200 Only" placeholder="Enter Bank no"  type="text" id="Bank" name="bank_name"
                                               class="form-control input-sm" value="{{ old('bank_name') }}" />
                                    </div>
                                </div>
                            </div>
                            <br>
                            


                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-success btn-lg" type="button" data-toggle="modal" data-target="#myModal">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn btn-danger btn-lg" onclick="window.location='{{url('http://baidyanath.msell.in/client/index.php?option=logout')}}'"
                                            type="button">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade rotate" data-backdrop="static" data-keyboard="false" id="myModal" style="font-family: 'Times New Roman',Times ,serif;" >
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content" >
                                        <div class="modal-header  widget-header-small" style="background-color: #284760; color: white; "  >
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="background-color: white;">
                                                ×
                                            </button>
                                            <h4 class="modal-title" style="text-align: center; font-family: 'Times New Roman',Times ,serif; ">Memorendum of Mutual understanding of Terms & Conditions of Business.</h4>

                                        </div>
                                        <div class="modal-body ui-dialog-content ui-widget-content" style="font-size: 16px; text-align: justify-all;">
                                            {!! $details->name !!}
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row" >
                                                <div class="col-md-1" >
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal" ><b>Close</b></button>
                                                    
                                                </div>
                                     
                                                <div class="col-md-11" style="align-content: right;">
                                                    <button type="submit" class="btn btn-info" ><b>I Agree The Terms</b></button>
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->





<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('msell/js/common.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
   
    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>
    <script type="text/javascript">
    function validateFunc(){
        alert('yo');
    }
    $('.vnumerrorpa').keyup(function()
    {
        var pan_no = document.getElementById('pan_no');
        var messagepa = document.getElementById('messagepa');
        var yourInput = $(this).val();
        re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(pan_no.value.length<10){
            messagepa.innerHTML = "required 10 digits, match requested format!";
        }
        else{
            messagepa.innerHTML = "";
        }
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.vnumerrorp').keyup(function()
    {
        var phone = document.getElementById('p_phone_no');
        var message = document.getElementById('message');
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });

    $('.vnumerrorm').keyup(function()
    {
        var mobile = document.getElementById('p_mobile_no');
        var message = document.getElementById('messagem');
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(mobile.value.length<10){
            message.innerHTML = "required 10 digits, match requested format!";
        }
        else{
            message.innerHTML = "";
        }
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.vnumerrorg').keyup(function()
    {
        var pan_no = document.getElementById('tin_no');
        var message = document.getElementById('messageg');
        var yourInput = $(this).val();
        re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(pan_no.value.length<15){
            message.innerHTML = "required 15 digits, match requested format!";
        }
        else{
            message.innerHTML = "";
        }
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    </script>

@endsection