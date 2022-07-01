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
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.location3')}}</label>
                                                <select multiple name="location_3[]" id="location_3" class="form-control chosen-select">
                                                    @if(!empty($location3))
                                                        @foreach($location3 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.location4')}}</label>
                                                <select multiple name="location_4[]" id="location_4" class="form-control chosen-select">
                                                    @if(!empty($location4))
                                                        @foreach($location4 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.location5')}}</label>
                                                <select multiple name="location_5[]" id="location_5" class="form-control chosen-select">
                                                    @if(!empty($location5))
                                                        @foreach($location5 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.location6')}}</label>
                                                <select multiple name="location_6[]" id="location_6" class="form-control chosen-select">
                                                    @if(!empty($location6))
                                                        @foreach($location6 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.distributor')}}</label>
                                                <select multiple name="dealer[]" id="dealer" class="form-control chosen-select">
                                                    @if(!empty($dealer))
                                                        @foreach($dealer as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.catalog_2')}}</label>
                                                <select multiple name="catalog_2[]" id="catalog_2" class="form-control chosen-select">
                                                    @if(!empty($catalog_2))
                                                        @foreach($catalog_2 as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="" id="test_remove_3">
                                                <label class="control-label no-padding-right"
                                                       for="name" id="location3">{{Lang::get('common.catalog_product')}}</label>
                                                <select multiple name="catalog_product[]" id="catalog_product" class="form-control chosen-select">
                                                    @if(!empty($catalog_product))
                                                        @foreach($catalog_product as $k=>$r)
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
    <script src="{{asset('msell/page/report172.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/common.js')}}"></script>

    <script src="{{asset('msell/js/bootstrap-multiselect.min.js')}}"></script>



    <script>
    $(document).on('change', '#location_1', function () {
        _current_val = $(this).val();
        location_data(_current_val,2);
    });

    $(document).on('change', '#location_2', function () {
        _current_val = $(this).val();
        location_data(_current_val,3);
    });

    $(document).on('change', '#location_3', function () {
        _current_val = $(this).val();
        custom_user_Data(_current_val,4);
    });
    $(document).on('change', '#dealer', function () {
        _current_val = $(this).val();
        custom_dealer_Data(_current_val,4);
    });
    $(document).on('change', '#location_4', function () {
        _current_val = $(this).val();
        location_data(_current_val,5);
    });
    $(document).on('change', '#location_5', function () {
        _current_val = $(this).val();
        location_data(_current_val,6);
    });
    $(document).on('change', '#location_6', function () {
        _current_val = $(this).val();
        location_data(_current_val,7);
    });
   
    function custom_user_Data(val) {
        _append_box2=$('#user');
        _append_box3=$('#dealer');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/statndard_filter_onchange_for_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data2) {
                    if (data2.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data2.code == 200) {
                        var level=4;
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
                                    $('#location_4').empty();
                                    $('#location_4').append(template).trigger('chosen:updated');

                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        //Location 3
                        template2 = '<option value="" >Select</option>';
                        $.each(data2.result, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + value2.user_id + '" >' + stripslashes(value2.user_name) + '</option>';
                            }
                        });
                        _append_box2.empty();
                        _append_box2.append(template2).trigger('chosen:updated');

                        template3 = '<option value="" >Select</option>';
                        $.each(data2.query_dealer, function (key2, value2) {
                            if (value2.name != '') {
                                template3 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _append_box3.empty();
                        _append_box3.append(template3).trigger('chosen:updated');

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
            _append_box3.empty();
            _append_box2.empty();
        }
    }
    function custom_dealer_Data(val) {
        _append_box2=$('#location_7');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/statndard_filter_onchange_for_dealer',
                dataType: 'json',
                data: "id=" + val,
                success: function (data2) {
                    if (data2.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data2.code == 200) {
                        var level=4;

                        //Location 3
                        template2 = '<option value="" >Select</option>';
                        $.each(data2.query_dealer, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _append_box2.empty();
                        _append_box2.append(template2).trigger('chosen:updated');

                        

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
            _append_box2.empty();
        }
    }
    function location_data(val,level) {
        _append_box=$('#location_'+level);
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
                        template = '<option value="" >Select</option>';
                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
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
                        _append_box.empty();
                        _append_box.append(template).trigger("chosen:updated");

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

    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }
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
    $("#month").datetimepicker  ( {
        clear: "Clear",
        format: 'YYYY-MM'
    });
    </script>

@endsection