@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.sales-team-attendance')}} - {{config('app.name')}}</title>
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
                    <li class="active">{{Lang::get('common.sales-team-attendance')}}</li>
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

                            @include('common_filter.filter')
                            <!-- <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
                                                <select multiple name="region[]" id="region" class="form-control chosen-select">
                                                    <option disabled="disabled" value="">select</option>
                                                    @if(!empty($region))
                                                        @foreach($region as $k=>$r)
                                                            <option value="{{$r->id}}">{{$r->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location3')}}</label>
                                                <select multiple name="area[]" id="state" class="form-control chosen-select">
                                                    <option disabled="disabled" value="">select</option>
                                                    @if(!empty($state))
                                                        @foreach($state as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-xs-6 col-sm-6 col-lg-2">
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

                                       
                                        {{--</div>--}}
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">From Date</label>
                                                <input value="{{ !empty(Request::get('from_date'))?date('Y-m-d',strtotime(Request::get('from_date'))):date('Y-m-d') }}" autocomplete="off" type="text" placeholder="From Date" name="from_date" id="from_date" class="form-control date-picker">
                                            </div>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">To Date</label>
                                                <input value="{{ !empty(Request::get('to_date'))?date('Y-m-d',strtotime(Request::get('to_date'))):date('Y-m-d') }}" value="" autocomplete="off" type="text" placeholder="From Date" name="to_date" id="to_date" class="form-control date-picker">
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
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
    <script src="{{asset('msell/page/report143.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    @include('common_filter.filter_script')
    
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
@endsection