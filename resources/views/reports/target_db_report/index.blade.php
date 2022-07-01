@extends('layouts.master')

@section('title') 
    <title>{{Lang::get('common.target_db')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/multi-select.css')}}"/>

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
                    <li class="active">{{Lang::get('common.target_db')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#sale-order" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
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

                        <form class="form-horizontal open collapse in" action="" method="GET" id="sale-order" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
                                                <select multiple name="location_2[]" id="location_2" class="form-control chosen-select">
                                                    @if(!empty($location2))
                                                        @foreach($location2 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.location3')}}</label>
                                                <select multiple name="location_3[]" id="location_3" class="form-control chosen-select">
                                                    @if(!empty($state))
                                                        @foreach($state as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>


                                        <!-- <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">User</label>
                                                <select multiple name="user[]" id="user" class="form-control chosen-select">
                                                    <option disabled="disabled" value="">Select</option>
                                                    @if(!empty($users))
                                                        @foreach($users as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div> -->

                                       
                                        <!-- <div class="col-xs-6 col-sm-6 col-lg-3">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>
                                                   
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar bigger-110"></i>
                                                </span>

                                                <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                            </div>
                                        </div> -->

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.month')}}</label>
                                                <input autocomplete="off" type="text" name="month" id="month" class="form-control" placeholder="Month">
                                            </div>
                                        </div>



                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                                    style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                {{Lang::get('common.find')}}
                                            </button>
                                        </div>
                                    </div>
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

                                <div class="row">
                                    <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">


                                    </div>
                                </div>

                            </div>
                        </div>

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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report158.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/common.js')}}"></script>

    <script src="{{asset('msell/js/bootstrap-multiselect.min.js')}}"></script>



    <script>
     $(document).on('change', '#state', function () {
        val = $(this).val();
        _hq = $('#user');
        //alert(_current_val);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_user_name',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data, function (key, value) {
                          
                            console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq.append(template).trigger("chosen:updated");
               

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });  
       }
        
    });
    </script>

<script>

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

<script>
        $(document).on('change', '#location_1', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        // $(document).on('change', '#location_3', function () {
        //     _current_val = $(this).val();
        //     location_data(_current_val,4);
        // });
        // $(document).on('change', '#location_4', function () {
        //     _current_val = $(this).val();
        //     location_data(_current_val,5);
        // });
        // $(document).on('change', '#location_5', function () {
        //     _current_val = $(this).val();
        //     location_data(_current_val,6);
        // });
	    // $(document).on('change', '#location_6', function () {
        //     _current_val = $(this).val();
        //     location_data(_current_val,7);
        // });
       

        function location_data(val,level) {
            _append_box=$('#location_'+level);
            _my_append=$('location_'+level);
            _id_name = _my_append.selector;
            _remove_box=$('#test_remove_'+level);


            var loc_name = '';
            var loc_name = document.getElementById('location'+level).innerHTML;
            label_array = $('location'+level);
            label_id = label_array.selector;
            
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/getLocationForStandaradFilter',
                    dataType: 'json',
                    data: "id=" + val+"&type="+level,
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                            //Location 3
                            var template ='';
                            template = '<label class="control-label no-padding-right" for="name" id='+label_id+'>'+loc_name+'</label><select multiple name="'+_id_name+'[]" id="'+_id_name+'" class="form-control input-sm" style="overflow-y: scroll; "> ';
                            // var template = '';
                                                
                            $.each(data.result, function (key, value) {
                                if (value.name != '') {
                                    template += '<option value="' + key + '" >' + stripslashes(value) + '</option> ';
                                }
                            });
                            template += '</select>';
                            if(data.dealer_flag == 1)
                            {
                                dealer_template = '<option value="" >Select</option>';
                                $.each(data.dealer, function (key, value) {
                                    if (value.name != '') {
                                        dealer_template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                                    }
                                });
                                $('#dealer').empty();
                                $('#dealer').append(dealer_template).trigger("chosen:updated");

                            }

                         	_remove_box.empty();
                            _remove_box.append(template);
                            $(function () {
					            $('#'+_id_name).multiselect({
					                includeSelectAllOption: true,
					                enableFiltering: true,
					                enableCaseInsensitiveFiltering: true,
					              nonSelectedText: 'Select',
					                buttonWidth:'200px',
					                maxHeight: 400,


					            });
					        });
                       


                        }

                    },
                    complete: function () {
                    },
                    error: function () {
                    }
                });
            }
            else{
                _remove_box.empty();
            }
        }

    </script>

<script>
    $("#month").datetimepicker  ( {
        clear: "Clear",
        format: 'YYYY-MM'
    });
    </script>

@endsection