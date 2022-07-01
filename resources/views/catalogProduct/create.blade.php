@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.catalog_product_master')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('catalog1')}}">{{Lang::get('common.catalog_product_master')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.catalog_product')}}</li>
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

                        <form class="form-horizontal" action="{{route('catalog-product.store')}}" method="POST" id="create-catalog_product-form" role="form">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        {{--<div class="col-lg-4">--}}

                                        {{--</div>--}}
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="code"> Product Code</label>
                                                <input type="text" id="code" name="code"
                                                       value="{{old('code')}}"
                                                       placeholder="Enter Product Code"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> Product Name</label>

                                                <input type="text" id="name" name="name"
                                                       value="{{old('name')}}"
                                                       placeholder="Enter Product Name"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        {{--<div class="col-lg-4">--}}

                                        {{--</div>--}}
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="code"> Hsn Code</label>
                                                <input type="text" id="hsn_code" name="hsn_code"
                                                       value="{{old('hsn_code')}}"
                                                       placeholder="Enter Hsn Code"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> Gst Per</label>

                                                <input type="text" id="gst_per" name="gst_per"
                                                       value="{{old('gst_per')}}"
                                                       placeholder="Enter Gst Per"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        {{--<div class="col-lg-4">--}}

                                        {{--</div>--}}
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="code"> Weight</label>
                                                <input type="text" id="weight" name="weight"
                                                       value="{{old('weight')}}"
                                                       placeholder="Enter Weight"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> Unit </label>

                                                <input type="text" id="unit" name="unit"
                                                       value="{{old('unit')}}"
                                                       placeholder="Enter Unit"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="location2">Catalog-1</label>
                                                <select name="catalog_id" id="catalog_id" class="form-control">
                                                    <option value="">select</option>
                                                    @if(!empty($catlog_1))
                                                        @foreach($catlog_1 as $catlog_1_data)
                                                            <option value="{{$catlog_1_data->id}}">{{$catlog_1_data->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name"> MRP </label>

                                                <input type="text" id="mrp" name="mrp"
                                                       value="{{old('mrp')}}"
                                                       placeholder="Enter MRP"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Product Division </label>

                                                <select name="pro_div" id="pro_div" class="form-control">
                                                    <option value="">select</option>
                                                    @if(!empty($catlog_pro_divi))
                                                        @foreach($catlog_pro_divi as $catlog_pro_divison)
                                                            <option value="{{$catlog_pro_divison->id}}">{{$catlog_pro_divison->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label" for="status">Is Focus</label>
                                                <select name="is_focus" id="is_focus" class="form-control">
                                                    <option value="1">YES</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">


                                        <div class="col-lg-6">
                                            <div class="">
                                                <label class="control-label" for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
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
                                    <button class="btn" type="button" onclick="document.location.href='{{url('catalog-product')}}'">
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
    <script src="{{asset('msell/page/create.catalogproduct.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

@endsection