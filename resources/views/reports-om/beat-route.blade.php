@extends('layouts.master')

@section('title')
    <title>Beat Route - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
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

                    <li class="active">Beat Route</li>
                </ul>

                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#filterForm" class="btn btn-sm btn-default"><i
                                class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                <div class="clearfix" style="margin-top: 5px"></div>
                <form method="GET" action="" role="form" enctype="multipart/form-data"
                      class="form-horizontal open collapse in" id="filterForm">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                {{--<div class="col-xs-6 col-sm-6 col-lg-1">--}}
                                    {{--<div class="">--}}
                                        {{--<label class="control-label no-padding-right"--}}
                                               {{--for="name">From</label>--}}
                                        {{--<input value="" type="text" placeholder="From Date" name="from_date" id="from_date" class="form-control date-picker">--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                {{--<div class="col-xs-6 col-sm-6 col-lg-1">--}}
                                    {{--<div class="">--}}
                                        {{--<label class="control-label no-padding-right"--}}
                                               {{--for="name">To</label>--}}
                                        {{--<input value="" type="text" placeholder="From Date" name="to_date" id="to_date" class="form-control date-picker">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                <div class="col-lg-2">
                                    <div class="">
                                        <label for="belt" class="control-label">Town</label>
                                        <select name="belt[]" multiple class="form-control chosen-select" id="belt">
                                            <option disabled="disabled" value="">Select</option>
                                            @foreach($belt as $key=>$data)
                                                <option value="{{$key}}">{{$data}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label for="distributor" class="control-label">Distributor</label>
                                        <select name="distributor[]" multiple class="form-control chosen-select distributor" id="distributor">
                                            <option disabled="disabled" value="">Select</option>
                                            @if(!empty($distributor))
                                                @foreach($distributor as $key=>$data)
                                                    <option value="{{$key}}">{{$data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label for="beat" class="control-label">Beat</label>
                                        <select name="beat[]" multiple class="form-control chosen-select" id="beat">
                                            <option disabled="disabled" value="">Select</option>
                                            @if(!empty($beat))
                                                @foreach($beat as $key=>$data)
                                                    <option value="{{$key}}">{{$data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label for="dpd1" class="control-label">Beat Day</label>
                                        <select multiple class="form-control chosen-select" name="day[]" id="day">
                                            <option value="0">All</option>
                                            <option value="1">Sun</option>
                                            <option value="2">Mon</option>
                                            <option value="3">Tue</option>
                                            <option value="4">Wed</option>
                                            <option value="5">Thu</option>
                                            <option value="6">Fri</option>
                                            <option value="7">Sat</option>
                                        </select>
                                    </div>
                                </div>
                                {{--Retailer Data--}}
                                {{--<div class="col-lg-2">--}}
                                {{--<div class="">--}}
                                {{--<label for="outlet" class="control-label">OUTLET (O/L) IN THIS BEAT</label>--}}
                                {{--<select name="outlet" id="outlet" class="form-control">--}}
                                {{--<option value="">Select</option>--}}
                                {{--</select>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                <div class="col-lg-2">
                                    <div class="">
                                        <label for="outlet" class="control-label">Outlet</label>
                                        <select multiple name="outlet[]" id="outlet_new" class="form-control chosen-select">
                                            <option disabled="disabled" value="">Select</option>
                                            @if(!empty($outlet))
                                                @foreach($outlet as $key=>$odata)
                                                    <option value="{{$key}}">{{$odata}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                {{--<div class="col-lg-2">--}}
                                    {{--<div class="">--}}
                                        {{--<label for="dpd1" class="control-label">Active O/L In Beat</label>--}}
                                        {{--<select name="status" class="form-control chosen-select" id="status">--}}
                                            {{--<option value="0">All</option>--}}
                                            {{--<option selected="selected" value="1">Active</option>--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                            style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
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

                        <div class="hr hr-18 dotted hr-double"></div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
        @endsection

        @section('js')
            <script src="{{asset('msell/js/moment.min.js')}}"></script>
            <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
            <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
            <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
            <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
            <script src="{{asset('msell/page/report.js')}}"></script>
            <script src="{{asset('msell/js/common.js')}}"></script>
            <script>
                function fnExcelReport() {
                    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
                    var textRange;
                    var j = 0;
                    tab = document.getElementById('simple-table'); // id of table

                    for (j = 0; j < tab.rows.length; j++) {
                        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                        //tab_text=tab_text+"</tr>";
                    }

                    tab_text = tab_text + "</table>";
                    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
                    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
                    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

                    var ua = window.navigator.userAgent;
                    var msie = ua.indexOf("MSIE ");

                    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
                    {
                        txtArea1.document.open("txt/html", "replace");
                        txtArea1.document.write(tab_text);
                        txtArea1.document.close();
                        txtArea1.focus();
                        sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
                    }
                    else                 //other browser not tested on IE 11
                        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

                    return (sa);
                }
            </script>
@endsection