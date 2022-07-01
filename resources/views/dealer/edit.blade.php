@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.dealer')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
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
                        <a href="{{url('dealer')}}">{{Lang::get('dealer')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.dealer_module')}}</li>
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
                        {!! Form::open(array('route'=>['dealer.update',$id] , 'method'=>'PUT','id'=>'edit-dealer-form','role'=>'form' ))!!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">

                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name"> Name </label>
                                            <input type="text" id="name" name="name"
                                                   value="{{$dealer->name}}"
                                                   placeholder="Name|Required|Min:2|max: 50"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="dealer_code"> Dealer Code </label>

                                            <input type="text" id="dealer_code" name="dealer_code"
                                                   value="{{$dealer->dealer_code}}"
                                                   placeholder="Dealer Code|Required|Min:2|Max:40"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label" for="contact_person">Contact Person</label>
                                            <input type="text" name="contact_person" id="contact_person"
                                                   value="{{$dealer->contact_person}}"
                                                   class="form-control"
                                                   placeholder="Contact Person|Min:2|Max:100">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="email"> Email </label>
                                            <input type="text" id="email" value="{{$dealer->email}}"
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
                                            <input type="text" name="address" value="{{$dealer->address}}"
                                                   class="form-control" id="address" placeholder="Address|max:200">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="ownership">Ownership</label>
                                            <select id="owner" name="owner" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($ownerships as $key=>$ownership)
                                                    <option {{$key==$dealer->ownership_type_id?'selected':''}} value="{{$key}}">{{$ownership}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right" for="superstockist">Super-Stockist</label>
                                            <select name="superstockist[]" id="superstockist"
                                                    class="form-control chosen-select" multiple>
                                                <option name="superstockist" value="" id="superstockist"
                                                        class="form-control">Select
                                                </option>
                                                @foreach($superstockist_data as $key=>$superstockist)

                                                    <option {{ !empty($super_stock) && in_array($key,$super_stock)?'selected':''}} value="{{$key}}">{{$superstockist}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="outlet">Landline</label>
                                            <input type="text" name="landline" value="{{$dealer->landline}}"
                                                   id="landline" class="form-control"
                                                   placeholder="Contact Person Name|Required|Max:20|Min:6">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="">
                                            <label class="control-label no-padding-right" for="other_number">Other
                                                Number</label>
                                            <input type="text" name="other_number"
                                                   value="{{$dealer->other_numbers}}" id="contact_person_name"
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
                                            <label class="control-label"
                                                   for="location_1">{{Lang::get('common.l5')}}</label>
                                            <select readonly="readonly" name="location_1" id="location_1"
                                                    class="form-control">
                                                <option value="1">India</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label"
                                                   for="location_2">State</label>
                                            <select name="location_2" id="location_2" class="form-control">
                                                <option value="">select</option>
                                                @if(isset($location2))
                                                    @foreach($location2 as $key=>$state)
                                                        <option {{isset($dealer->location_2_id) && $dealer->location_2_id==$key?'selected':''}} value="{{$key}}">{{$state}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label"
                                                   for="location_3">Head Quarter</label>
                                            <select name="location_3" id="location_3"
                                                    class="chosen-select form-control">
                                                <option value="">select HQ</option>
                                                @foreach($hqArr as $id=>$hqdata)
                                                    <option {{isset($dealer->location_3_id) && $dealer->location_3_id==$id?'selected':''}} value="{{$id}}">{{$hqdata}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label"
                                                   for="location_4">District</label>
                                            <select name="location_4[]" id="location_4"
                                                    class="chosen-select form-control" multiple="multiple">
                                                <option value="">select</option>
                                                @foreach($distArr as $id=>$distdata)
                                                    <option {{!empty($district) && in_array($id,$district)?'selected':''}} value="{{$id}}">{{$distdata}}</option>
                                                @endforeach
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
                                                <option value="">select</option>
                                                @foreach($beatArr as $id=>$beat)
                                                    @php
                                                        $t=[];
                                                        $t=explode('-',$id);
                                                    @endphp
                                                    <option {{ isset($t[0]) && !empty($dealer_location) && in_array($t[0],$dealer_location)?'selected':'' }} value="{{$id}}">{{$beat}}</option>
                                                @endforeach
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
                                            Update
                                        </button>
                                        <button class="btn" type="button"
                                                onclick="document.location.href='{{url('dealer')}}'">
                                            <i class="ace-icon fa fa-close bigger-110"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--</form>--}}
                        {!! Form::close() !!}

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
    <script src="{{asset('msell/page/edit.dealer.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection