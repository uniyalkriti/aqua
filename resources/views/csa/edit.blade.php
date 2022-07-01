@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('csa')}}">{{Lang::get('common.csa')}} {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.csa')}}</li>
                </ul>

            </div>

            <div class="page-content">
                <div class="clearfix" style="margin-top: 5px"></div>
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
                        @if(count($errors)>0)
                            @foreach ($errors->all() as $error)
                                <div class="help-block">{{ $error }}</div>
                            @endforeach
                        @endif

                            {!! Form::open(array('route'=>[$current_menu.'.update',$encrypt_id] , 'method'=>'PUT','id'=>$current_menu.'-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_3"> {{Lang::get('common.location3')}} </label>
                                        <select name="location_3" id="location_3" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($location3))
                                                @foreach($location3 as $l3_key=>$l3_data)
                                                    <option {{(!empty($location->id)?$location->id:'')==$l3_key?'selected':''}} value="{{$l3_key}}">{{$l3_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="location_4"> {{Lang::get('common.location4')}} </label>
                                             <input type="text" placeholder="Enter Town Name" name="location_4"
                                               id="location_4" class="form-control input-sm" value="{{$ss->town}}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="retailer_name"> {{Lang::get('common.csa')}} Name </label>
                                        <input type="text" value="{{$ss->csa_name}}" placeholder="Enter SS Name" name="ss_name"
                                               id="ss_name" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                <div class="">
                                        <label class="control-label no-padding-right"
                                               for="retailer_name"> {{Lang::get('common.csa')}} Code </label>
                                        <input type="text" placeholder="Enter SS Code" name="ss_code"
                                               id="ss_code" class="form-control input-sm" value="{{$ss->csa_code}}">
                                    </div>
                                </div>    
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="address">{{Lang::get('common.address')}}</label>
                                        <textarea name="address" id="address" class="form-control input-sm">{{$ss->adress}}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="mobile">{{Lang::get('common.user_contact')}}</label>
                                        <input placeholder="Enter Mobile" value="{{$ss->mobile}}" type="text" name="mobile" id="mobile" class="form-control input-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="email">{{Lang::get('common.email')}}</label>
                                        <input placeholder="Enter Email" value="{{$ss->email}}" type="email" name="email" id="email" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="email">{{Lang::get('common.retailer_owner_name')}}</label>
                                        <input placeholder="Enter name" type="text" name="contact_person" id="contact_person" class="form-control input-sm" value="{{$ss->contact_person}}" >
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module">{{Lang::get('common.status')}}</label>
                                    <select required="required" class="form-control input-sm" name="status" id="status">
                                        <option {{$ss->active_status==1?'selected':''}} value="1">Active</option>
                                        <option {{$ss->active_status==1?'':'selected'}} value="0">Inactive</option>
                                    </select>
                                </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="image">{{Lang::get('common.image')}}</label>
                                        <input type="file" accept="Image/*" name="retailer_image" id="retailer_image" class="form-control input-sm">
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="tin_no">{{Lang::get('common.gst_no')}}</label>
                                        <input type="text" value="{{$ss->gst_no}}" name="tin_no" id="tin_no" class="form-control input-sm" placeholder="GSTin No">
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="division"> Division </label>
                                        <select name="division" id="division" class="form-control input-sm">
                                            <option value="">Select</option>
                                            @if(!empty($division))
                                                @foreach($division as $div_key=>$div_data)
                                                    <option {{(!empty($ss->division_id)?$ss->division_id:'')==$div_key?'selected':''}} value="{{$div_key}}">{{$div_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location='{{url('csa')}}'"
                                            type="button">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>

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
    <script src="{{asset('js/retailer.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script>
        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert('Done!');
                        window.setTimeout(function () {
                            location.reload()
                        }, 3000);
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function takeAction(module, action_id, tab, act) {

            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
@endsection