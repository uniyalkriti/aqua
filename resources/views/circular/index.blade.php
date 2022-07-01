@extends('layouts.master') 
  
@section('title')
    <title>{{Lang::get('common.circular')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}"/>
    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <style>
        .center {
          margin: auto;
          width: 100%;
        }
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            /*background-color: #438EB9 !important;*/
            background-color: #7BB0FF !important;
            color: black;
        }
    </style>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.circular')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#manualAttandence" class="btn btn-sm btn-default"><i
                        class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
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

                        <form class="form-horizontal open collapse in"  method="get" id="manualAttandence" enctype="multipart/form-data">
                            <!-- {!! csrf_field() !!} -->
                            <input type="hidden" name="flag" value='1'>
                          
                            @include('common_filter.filter')
                                
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        {{Lang::get('common.find')}}
                                    </button>
                                </div>
                            </div>
                        </form>
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
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                        <div class="row">
                            <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">

                            </div>
                        </div>
                    </div>
                </div>      
            </div>  <!-- PAGE CONTENT ENDS -->
        </div>
    </div><!-- /.main-content -->

<!--  sms email notification part starts here  -->
@if(!empty($user_data))
    <form method="post" action="{{url('send_sms_notification')}}" id="activity" enctype="multipart/form-data">
    {{csrf_field()}}
        <div class="main-container ace-save-state" id="main-container">
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-2">
                                            <div class="search-area well well-sm">
                                                <div class="search-filter-header bg-primary">
                                                    <h5 class="smaller no-margin-bottom">
                                                        <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; Circuler Details
                                                    </h5>
                                                </div>
                                                <div class="hr hr-dotted"></div>

                                                <h4 class="blue smaller">
                                                    <i class="fa fa-tags"></i>
                                                    Category
                                                </h4>
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <select name="category" class="form-control" id="category">
                                                            <option value="sms">SMS</option>
                                                            <option value="email">E-MAIL</option>
                                                            <option value="notifi">NOTIFICATION</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="hr hr-dotted"></div>
                                                <h4 class="blue smaller">
                                                    <i class="fa fa-book"></i>
                                                    SUBJECT
                                                </h4>
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <input type="text" name="subject">
                                                    </div>
                                                </div>
                                                <div id=smstr>
                                                    <div class="hr hr-dotted"></div>
                                                    <h4 class="blue smaller" id="common">
                                                        <i class="fa fa-envelope"></i>
                                                        SMS
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <input type="text" name="sms">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id=emailtr>
                                                    <div class="hr hr-dotted"></div>
                                                    <h4 class="blue smaller" id="common">
                                                        <i class="fa fa-send"></i>
                                                        EMAIL
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <textarea type="text" name="email"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id=notitr>
                                                    <div class="hr hr-dotted"></div>
                                                    <h4 class="blue smaller" id="common">
                                                        <i class="fa fa-bell"></i>
                                                        NOTIFICATION
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <textarea type="text" name="notifitext"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id=notiimg>
                                                    <div class="hr hr-dotted"></div>
                                                    <h4 class="blue smaller" id="common">
                                                        <i class="fa fa-image"></i>
                                                        Image
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <input type="file" name="notifiimage">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hr hr-dotted"></div>
                                                
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-default btn-round btn-white">
                                                        <i class="ace-icon fa fa-send green"></i>
                                                        Send
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <!-- table content starts here  -->
                                        <div class="col-xs-12 col-sm-10">
                                            <div class="row">
                                               <div class="col-xs-12">
                                                    <table id="simple-table" class="table table-bordered">
                                                        <tr>
                                                            <th><input type="checkbox" name="check_all" id="check_all" onchange="checkAll(this)"></th>
                                                            <th>S.No.</th>
                                                            <th>{{Lang::get('common.location3')}}</th>
                                                            <th>{{Lang::get('common.location4')}}</th>
                                                            <th>{{Lang::get('common.location5')}}</th>
                                                            <th>{{Lang::get('common.location6')}}</th>
                                                            <th>{{Lang::get('common.username')}}</th>
                                                            <th>{{Lang::get('common.role_key')}}</th>
                                                            <th>{{Lang::get('common.user_contact')}}</th>
                                                            <th>{{Lang::get('common.email')}}</th>
                                                          
                                                        </tr>
                                                        <tbody>
                                                            @foreach($user_data as $key => $value)
                                                                <tr>
                                                                    <td><input type="checkbox" name="person_id[]" id="person_id" value="{{$value->user_id}}"></td>
                                                                    <td>{{$key+1}}</td>
                                                                    <td>{{$value->l3_name}}</td>
                                                                    <td>{{$value->l4_name}}</td>
                                                                    <td>{{$value->l5_name}}</td>
                                                                    <td>{{$value->l6_name}}</td>
                                                                    <td>{{$value->fullname}}</td>
                                                                    <td>{{$value->designation}}</td>
                                                                    <td>{{$value->mobile}}</td>
                                                                    <td>{{$value->email}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- table content Ends here  -->
                                    </div>
                                </div>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->
                            
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->
        </div><!-- /.main-container -->
    </form>
@endif
<!--  sms email notification part ends here  -->
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>
    @include('common_filter.filter_script_sale')
    <script type="text/javascript">
        function checkAll(ele) 
        {
            var checkboxes = document.getElementsByTagName('input');
            if (ele.checked) 
            {
                 for (var i = 0; i < checkboxes.length; i++) 
                {
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = true;
                    }
                }
            } 
            else 
            {
                for (var i = 0; i < checkboxes.length; i++) 
                {
                     console.log(i)
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = false;
                    }
                }
            }
        }
    </script>
    <script>
        $('#timepicker1').timepicker({
                        minuteStep: 1,
                        showSeconds: true,
                        showMeridian: false,
                        disableFocus: true,
                        icons: {
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down'
                        }
                    }).on('focus', function() {
                        $('#timepicker1').timepicker('showWidget');
                    }).next().on(ace.click_event, function(){
                        $(this).prev().focus();
                    });
    $(".chosen-select").chosen();
    $('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
    });
    </script>
    <script>
    $(document).ready(function () {
        $('#smstr').show('fast');
        $('#emailtr').hide('fast');
        $('#notitr').hide('fast');
        $('#notiimg').hide('fast');
    });
        $(document).on('change', '#category', function () {
        _current_val = $(this).val();
        get_category(_current_val);
        });

        function get_category(val) 
        {
            if(val=='sms')
            {
                $('#smstr').show('fast');
                $('#emailtr').hide('fast');
                $('#notitr').hide('fast');
                $('#notiimg').hide('fast');
            }
            else if(val=='email')
            {
                $('#subtr').show('fast');
                $('#emailtr').show('fast');
                $('#smstr').hide('fast');
                $('#notitr').hide('fast');
                $('#notiimg').hide('fast');
            }
            else
            {
                $('#notitr').show('fast');
                $('#emailtr').hide('fast');
                $('#smstr').hide('fast');
                $('#notiimg').show('fast');
            }
        }
    </script>
@endsection