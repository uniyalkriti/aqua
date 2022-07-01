@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.dealer')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
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
                        <a href="{{url('dealer')}}">{{Lang::get('common.dealer')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.dealer_module')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')

                {{--<div class="page-header">--}}
                    {{--<h1>--}}
                        {{--{{Lang::get('common.user-mgmt')}}--}}
                    {{--</h1>--}}
                {{--</div>--}}
                {{--<!-- /.page-header -->--}}

                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" action="{{route('dealer.store')}}" method="POST"
                              id="create-dealer-form" role="form">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> Name </label>
                                                <input type="text" id="name" name="name"
                                                       value="{{old('name')}}"
                                                       placeholder="Name|Required|Min:2|max: 50"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="dealer_code"> Dealer Code </label>

                                                <input type="text" id="dealer_code" name="dealer_code"
                                                       value="{{old('dealer_code')}}"
                                                       placeholder="Dealer Code|Required|Min:2|Max:40"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="contact_person">Contact Person</label>
                                                <input type="text" name="contact_person" id="contact_person"
                                                       value="{{old('contact_person')}}"
                                                       class="form-control"
                                                       placeholder="Contact Person|Min:2|Max:100">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="email"> Email </label>
                                                <input type="text" id="email" value="{{old('email')}}"
                                                       name="email"
                                                       placeholder="Email|Required"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="address">Address </label>
                                                <input type="text" name="address" value="{{old('address')}}"
                                                       class="form-control" id="address" placeholder="Address|max:200">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="ownership">Ownership</label>
                                                <select id="owner" name="owner" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($ownerships as $key=>$ownership)
                                                        <option value="{{$key}}">{{$ownership}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="superstockist">Super-Stockist</label>
                                                <select name="superstockist[]" id="superstockist" class="form-control chosen-select" multiple="multiple">
                                                    <option name="superstockist" value="" id="superstockist" class="form-control">Select</option>
                                                    @foreach($superstockist_data as $key=>$superstockist)

                                                        <option value="{{$key}}">{{$superstockist}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="outlet">Landline</label>
                                                <input type="text" name="landline" value="{{old('landline')}}"
                                                       id="landline" class="form-control"
                                                       placeholder="Contact Person Name|Required|Max:20|Min:6">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="other_number">Other Number</label>
                                                <input type="text" name="other_number"
                                                       value="{{old('contat_person_name')}}" id="contact_person_name"
                                                       class="form-control"
                                                       placeholder="Contact Person Name|Required|Max:20|Min:6">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                                        <ul class="breadcrumb">
                                            <li class="active">Location Details</li>
                                        </ul>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="location_1">{{Lang::get('common.l5')}}</label>
                                                <select readonly="readonly" name="location_1" id="location_1" class="form-control">
                                                    <option value="1">India</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                {{--  <label class="control-label"
                                                       for="location_2">{{Lang::get('common.l4')}}</label>  --}}
                                                <label class="control-label"
                                                       for="location_2">State</label>
                                                <select name="location_2" id="location_2" class="form-control">
                                                    <option value="">select</option>
                                                    @if(isset($location2))
                                                        @foreach($location2 as $key=>$state)
                                                            <option value="{{$key}}">{{$state}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label"
                                                       for="location_3">Head Quarter</label>
                                                {{--  <label class="control-label"
                                                       for="location_3">{{Lang::get('common.l3')}}</label>  --}}
                                                <select name="location_3" id="location_3" class="chosen-select form-control">
                                                    <option value="">select HQ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label"
                                                       for="location_4">District</label>
                                                <select name="location_4[]" id="location_4"
                                                        class="chosen-select form-control" multiple="multiple">
                                                    <option value="">select </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="">
                                                <label class="control-label"
                                                       for="location_7">Beat</label>
                                                <select name="location_7[]" id="location_7"
                                                        class="chosen-select form-control" multiple="multiple">
                                                    {{--<option value="">select</option>--}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="clearfix form-actions">
                                        <div class="col-md-offset-5 col-md-7">
                                            <button class="btn btn-info" type="submit">
                                                <i class="ace-icon fa fa-check bigger-110"></i>
                                                Submit
                                            </button>
                                            <button class="btn" type="button" onclick="document.location.href='{{url('dealer')}}'">
                                                <i class="ace-icon fa fa-close bigger-110"></i>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
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
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/create.dealer.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection