@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.salary')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
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
                        <a href="{{url('salary')}}">{{Lang::get('common.salary')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.salary')}}</li>
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

                        <form class="form-horizontal" action="{{route('salary.store')}}" method="POST" id="location2-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                User Details 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.user')}}</label>
                                                <select name="user_id"  id="user_id" class="form-control chosen-select">
                                                    <option value="">Select</option>
                                                    @if(!empty($user))
                                                        @foreach($user as $rk=>$rr)
                                                            <?php if(empty($_GET['division']))
                                                                $_GET['division']=array();
                                                            ?>
                                                            <option @if(in_array($rk,$_GET['division'])){{"selected"}} @endif   value="{{$rk}}"> {{$rr}} 
                                                            </option>                                                        
                                                        @endforeach 
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                       

                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Bank Details 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name"> Bank Name</label>
                                                <input  type="text" id="bank_name" name="bank_name" class="form-control input-sm"  />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name"> Bank A/C No</label>
                                                <input  type="text" id="bank_name" name="account_no" class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">PAN No.</label>
                                                <input  type="text" id="pan_no" name="pan_no" class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">UAN No.</label>
                                                <input  type="text" id="uan_no" name="uan_no" class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">IFSC CODE.</label>
                                                <input  type="text" id="pf_no" name="pf_no"  class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">ESIC No.</label>
                                                <input  type="text" id="esic_no" name="esic_no"  class="form-control input-sm " />
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Attendance Details 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                User Details 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name"> Basic Salary</label>
                                                <input  type="text" id="basic_salary" name="basic_salary"  class="form-control input-sm"  />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name"> TA</label>
                                                <input  type="text" id="ta" name="ta"  class="form-control input-sm"  />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">HRA</label>
                                                <input  type="text" id="hra_amount" name="hra_amount"  class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">Special Allowance</label>
                                                <input  type="text" id="special_amount"  name="special_amount" class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">Provident Fund(Employer)</label>
                                                <input  type="text" id="pf_amount" name="pf_amount"  class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">Provident Fund(Employee)</label>
                                                <input  type="text" id="pf_amount" name="pf_amount_employee"  class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">ESIC Fund(Employer)</label>
                                                <input  type="text" id="esic_amount" name="esic_amount"  class="form-control input-sm " />
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="bank_name">ESIC Fund(Employee)</label>
                                                <input  type="text" id="esic_amount" name="esic_amount_employee"  class="form-control input-sm " />
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
                                    <button class="btn" type="button" onclick="document.location.href='{{url('salary')}}'">
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
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script type="text/javascript">
        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
    </script>
    <script>
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        //show datepicker when clicking on the icon
        .next().on(ace.click_event, function(){
            $(this).prev().focus();
        });

        //or change it into a date range picker
        $('.input-daterange').datepicker({autoclose:true});


        //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $('input[name=date_range_picker]').daterangepicker({
            'applyClass' : 'btn-sm btn-success',
            'cancelClass' : 'btn-sm btn-default',
                showDropdowns: true,
            // showWeekNumbers: true,             
            minDate: '2017-01-01',
            maxDate: moment().add(2, 'years').format('YYYY-01-01'),
            locale: {
                format: 'YYYY/MM/DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
            },
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    dateLimit: {
                                        "month": 1
                                    },

        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        }); 
    </script>
@endsection