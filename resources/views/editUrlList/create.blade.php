@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.editurllist')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('editUrlList')}}">{{Lang::get('common.editurllist')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.editurllist')}}</li>
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

                        <form class="form-horizontal" action="{{route('editUrlList.store')}}" method="POST" id="editUrlList-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Company</label>
                                                <select name="company" id="company" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($company_id as $val)
                                                        <option value="{{$val->id}}">{{$val->title}}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Version</label>
                                                <select name="version" id="version" class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label" for="status">Master Url</label>
                                                <select name="url" id="url" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($url_list as $uval)
                                                        <option value="{{$uval->id}}">{{$uval->code}}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-3">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Assign Code</label>

                                                <input type="text" id="acode" name="acode"
                                                       value=""
                                                       placeholder="Enter assign code"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                       


                                        <div class="row">
                                         <div class="col-xs-12">
                                                     <div class="col-lg-3">
                                                      <div class="">
                                                        <label class="control-label no-padding-right"
                                                               for="name">Assign Url</label>

                                                        <input type="text" id="aurl" name="aurl"
                                                               value=""
                                                               placeholder="Enter assign URL"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
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
                                    <button class="btn" type="button" onclick="document.location.href='{{url('editUrlList')}}'">
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
    <script src="{{asset('msell/page/location5.js')}}"></script>
    <script src="{{asset('js/location.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
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
            location_data(_current_val,4);
        });

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
                    url: domain + '/getLocation',
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
                            _append_box.empty();
                            _append_box.append(template);

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

    </script>

    <script>
     $(document).on('change', '#company', function () {
        val = $(this).val();
        version = $('#version');
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
                url: domain + '/get_version_by_company',
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
                        version.empty();
                        version.append(template).trigger("chosen:updated");
               

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