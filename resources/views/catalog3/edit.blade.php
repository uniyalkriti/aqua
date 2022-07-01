@extends('layouts.master')
@section('title')
    <title>{{Lang::get('common.catalog_3')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>

    <link rel="stylesheet" href="{{asset('nice/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-colorpicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('nice/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />

    <style>
        .has-error, .help-block {
            color: #a94442;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endsection
@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li>
                        <a style="color: white" href="{{url('catalog_3')}}">{{Lang::get('common.catalog_3')}}</a>
                    </li>

                    <li class="active" style="color: white">Add {{Lang::get('common.catalog_3')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>
            <div class="page-content">
                {{--@include('layouts.settings')--}}

                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                            {!! Form::open(array('route'=>[$current_menu.'.update',$encrypt_id] , 'method'=>'PUT','id'=>'catalog-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="catalog_1"> {{Lang::get('common.catalog_1')}} Name </label>
                                                <select name="catalog_1" id="catalog_0" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    @if(!empty($cat1))
                                                        @foreach($cat1 as $c1_key=>$c1_data)
                                                            <option {{$c1_key==$catalog_data->catalog_0_id?'selected':''}} value="{{$c1_key}}">{{$c1_data}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="catalog_2"> {{Lang::get('common.catalog_2')}} Name </label>
                                                <select name="catalog_2" id="catalog_1" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    @if(!empty($cat2))
                                                        @foreach($cat2 as $c2_key=>$c2_data)
                                                            <option {{$c2_key==$catalog_data->catalog_1_id?'selected':''}} value="{{$c2_key}}">{{$c2_data}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="{{$current_menu}}"> {{Lang::get('common.catalog_3')}} </label>
                                                <input type="text" id="{{$current_menu}}" name="{{$current_menu}}"
                                                       value="{{$catalog_data->name}}"
                                                       placeholder="Enter {{Lang::get('common.'.$current_menu)}}"
                                                       class="form-control input-sm"/>
                                            </div>
                                        </div>


                                         <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="sequence"> {{Lang::get('common.catalog_3')}} Sequence </label>
                                                <input type="text" id="sequence" name="sequence"
                                                       value="{{$catalog_data->sequence}}"
                                                       placeholder="Enter {{Lang::get('common.'.$current_menu)}} Sequence"
                                                       class="form-control input-sm"/>
                                            </div>
                                        </div>


                                        <div class="col-lg-2">
                                            <div class="widget-box">
                                                <div class="widget-header">
                                                    <h4 class="widget-title">
                                                        <i class="ace-icon fa fa-tint"></i>
                                                        Color Picker
                                                    </h4>
                                                </div>

                                                <div class="widget-body">
                                                    <div class="widget-main">
                                                        <div class="clearfix">
                                                            <label for="colorpicker1">Color Picker</label>
                                                        </div>

                                                        <div class="control-group">
                                                            <div class="bootstrap-colorpicker">
                                                                <input id="colorpicker1" type="text" autocomplete="off" name="color_picker" value="{{$catalog_data->color_code}}" class="input-large form-control" />
                                                            </div>
                                                        </div>

                                                        <hr />

                                                        <div>
                                                            <label for="simple-colorpicker-1">Custom Color Picker</label>

                                                            <select id="simple-colorpicker-1" class="hide">
                                                                <option value="#ac725e">#ac725e</option>
                                                                <option value="#d06b64">#d06b64</option>
                                                                <option value="#f83a22">#f83a22</option>
                                                                <option value="#fa573c">#fa573c</option>
                                                                <option value="#ff7537">#ff7537</option>
                                                                <option value="#ffad46" >#ffad46</option>
                                                                <option value="#42d692">#42d692</option>
                                                                <option value="#16a765">#16a765</option>
                                                                <option value="#7bd148">#7bd148</option>
                                                                <option value="#b3dc6c">#b3dc6c</option>
                                                                <option value="#fbe983">#fbe983</option>
                                                                <option value="#fad165">#fad165</option>
                                                                <option value="#92e1c0">#92e1c0</option>
                                                                <option value="#9fe1e7">#9fe1e7</option>
                                                                <option value="#9fc6e7">#9fc6e7</option>
                                                                <option value="#4986e7">#4986e7</option>
                                                                <option value="#9a9cff">#9a9cff</option>
                                                                <option value="#b99aff">#b99aff</option>
                                                                <option value="#c2c2c2">#c2c2c2</option>
                                                                <option value="#cabdbf">#cabdbf</option>
                                                                <option value="#cca6ac">#cca6ac</option>
                                                                <option value="#f691b2">#f691b2</option>
                                                                <option value="#cd74e6">#cd74e6</option>
                                                                <option value="#a47ae2">#a47ae2</option>
                                                                <option value="#555">#555</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div><!-- /.span -->
                            </div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Reset
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
    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/catalog.js')}}"></script>

    <script src="{{asset('nice/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-colorpicker.min.js')}}"></script>
    <!-- ace scripts -->
    <script src="{{asset('nice/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('nice/js/ace.min.js')}}"></script>

    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function($) {

            $('#colorpicker1').colorpicker();
            //$('.colorpicker').last().css('z-index', 2000);//if colorpicker is inside a modal, its z-index should be higher than modal'safe
        
            $('#simple-colorpicker-1').ace_colorpicker();
            // $('#simple-colorpicker-1').ace_colorpicker('pick', 2);//select 2nd color
            // $('#simple-colorpicker-1').ace_colorpicker('pick', '#fbe983');//select #fbe983 color
            //var picker = $('#simple-colorpicker-1').data('ace_colorpicker')
            //picker.pick('red', true);//insert the color if it doesn't exist
        
        });
    </script>

@endsection