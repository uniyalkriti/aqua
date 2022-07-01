@extends('layouts.master') 
  
@section('title')
    <title>{{Lang::get('common.manual_attendance')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}"/>
    <style>
    .center {
      margin: auto;
      width: 50%;
      padding: 10px;
    }
    </style>
@endsection

@section('body')
<!-- main container starts here  -->
<div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.manual_attendance')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#manualAttandence" class="btn btn-sm btn-default"><i
                        class="fa fa-navicon mg-r-10"></i> Filter</a>
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

                        <form class="form-horizontal open collapse in"  method="get" id="manualAttandence" enctype="multipart/form-data">
                            <!-- {!! csrf_field() !!} -->
                            <input type="hidden" name="flag" value='1'>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="name">{{Lang::get('common.location3')}}</label>
                                        <select name="area" id="area" class="form-control chosen-select input-sm">
                                            <option  value="">select</option>
                                            @if(!empty($state))
                                                @foreach($state as $k=>$r)
                                                    <option {{ Request::get('area')==$k?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.role_key')}}</label>
                                        <select  name="role" id="role" class="form-control chosen-select input-sm">
                                            <option value="">select</option>
                                            @if(!empty($role))
                                                @foreach($role as $k=>$r)
                                                     <option {{ Request::get('role')==$k?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.user')}}</label>
                                        <select name="user" id="user" class="form-control chosen-select input-sm" required="required">
                                            <option value="">Select</option>
                                            @if(!empty($user))
                                                @foreach($user as $k=>$r)
                                                 <option {{ Request::get('user')==$k?'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                <div class="form-group">
                                        <label for="date" class="control-label">{{Lang::get('common.date')}}</label>
                                        <input required value="{{ Request::get('date') }}" type="text" placeholder="From Date" name="date" id="date" class="form-control date-picker">
                                </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        {{Lang::get('common.find')}}
                                    </button>
                                </div>
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
                                         

                                        <div class="hr hr-18 dotted hr-double"></div>
                                        <div class="row">
                                            <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">

                                            </div>
                                        </div>
                       
                                    </div>
                                </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                    </form>
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@if(!empty($query))
    <div id="form-wrapper" style="max-width:700px;margin:auto;">
        <form  method="get" action="updateManualAttandence"  enctype="multipart/form-data">
             {!! csrf_field() !!}
            <div class="row-1">
                <div class="col-md-12">
                    <input type="hidden" name="user_id" value=<?php echo $user_id ?> >
                    <input type="hidden" name="date" value=<?php echo $date ?> >
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="control-label no-padding-right"
                                   for="name"><strong>Work Status</strong></label>
                            <select name="work" required id="work" class="form-control chosen-select input-sm">
                                @if(!empty($work))
                                    @foreach($work as $k=>$r)
                                        <option {{$query->work_status ==$k?'selected' : ''}} value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>


                        <div class="col-lg-3">
                            <label class="control-label no-padding-right"
                                   for="name"><strong>Working With</strong></label>
                            <select name="work_with" required id="work_with" class="form-control chosen-select input-sm">
                                <option {{$query->working_with == 0 ?'selected' : ''}} value="0"> Self </option>
                                @if(!empty($workingWithUser))
                                    @foreach($workingWithUser as $wk=>$wr)
                                        <option {{$query->working_with ==$wk?'selected' : ''}} value="{{$wk}}">{{$wr}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>


                      <!--   <div class="col-lg-3">
                            <label class="control-label no-padding-right" for="name"><strong>Town</strong></label>
                            <select name="town_id" required id="town_id" class="form-control chosen-select input-sm">
                               <option value="">Select</option>
                                @if(!empty($town_id))
                                    @foreach($town_id as $k=>$r)
                                        <option {{$query->mtp_town_id==$r->id}} value="{{$r->id}}">{{$r->town_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div> -->
                        <div class="col-lg-3">
                            <label class="control-label no-padding-right" for="name"><strong>Reason</strong></label>
                            <select name="reason" required id="reason" class="form-control chosen-select input-sm">
                                <option  value="">select</option>
                                @if(!empty($reason_display))
                                    @foreach($reason_display as $k=>$r)
                                        <option {{$query->reason_id == $k?'selected' : ''}} value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="control-label no-padding-right" for="name"><strong>{{Lang::get('common.remarks')}}</strong></label>
                            <textArea  class="form-control"  required name="remarks" id="remarks" style="margin: 0px; width: 300px; height: 74px;">{{$query->remarks}}</textArea>
                        </div>

                        


                    </div>
                    <div class="row">

                        <div class="col-lg-3">
                           <label class="control-label no-padding-right" for="name"><strong>Check Out {{Lang::get('common.time')}}</strong></label>
                           <div class="input-group bootstrap-timepicker">
                                <input required="required" class= "form-control" name="checkouttime" id="timepicker1" type="text" class="form-control" />
                                <span class="input-group-addon">
                                    <i class="fa fa-clock-o "></i>
                                </span>
                            </div>
                        </div>


                        <div class="col-md-3 center"><br>
                           <input class="form-control btn btn-primary" type="submit" name="submit" value="Update">
                         </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<!-- ............................manual attandence submit form start here......................................  -->

	@elseif(isset($_GET['find']))  
	<div id="form-wrapper" style="max-width:700px;margin:auto;">
	    <form action="submitManualAttandence" method="get" action="submitManualAttandence" enctype="multipart/form-data">
	             {!! csrf_field() !!}
	              <input type="hidden" name="user_id" value=<?php echo $user_id ?> >
	              <input type="hidden" name="date" value=<?php echo $date ?> >
	        <div class="main-content">
	            <div class="main-content-inner">
	                <div class="row">
	                    <div class="col-md-12">
	                        <div class="row">
	                             <div class="col-lg-3">
	                                <label class="control-label no-padding-right" for="name"><strong>Work {{Lang::get('common.status')}}</strong></label>
	                                <select name="work" required id="work" class="form-control chosen-select input-sm">
	                                    <option  value="">select</option>
	                                    @if(!empty($work))
	                                        @foreach($work as $k=>$r)
	                                            <option value="{{$k}}">{{$r}}</option>
	                                        @endforeach
	                                    @endif
	                                </select>
	                            </div>


                                 <div class="col-lg-3">
                                    <label class="control-label no-padding-right" for="name"><strong>Working With</strong></label>
                                    <select name="work_with" required id="work_with" class="form-control chosen-select input-sm">
                                        <option value="0">Self</option>
                                        @if(!empty($workingWithUser))
                                            @foreach($workingWithUser as $wk=>$wr)
                                                <option value="{{$wk}}">{{$wr}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>


	                          <!--   <div class="col-lg-3">
	                                <label class="control-label no-padding-right" for="name"><strong>Town</strong></label>
	                                <select name="town_id" required id="town_id" class="form-control chosen-select input-sm">
	                                 <option value="">Select</option>
	                                    @if(!empty($town_id))
	                                        @foreach($town_id as $k=>$r)
	                                            <option value="{{$r->id}}">{{$r->town_name}}</option>
	                                        @endforeach
	                                    @endif
	                                </select>
	                            </div> -->
	                            <div class="col-lg-3">
	                                <label class="control-label no-padding-right" for="name"><strong>Reason</strong></label>
	                                <select name="reason" required id="reason" class="form-control chosen-select input-sm">
	                                    <option  value="">select</option>
	                                    @if(!empty($reason_display))
	                                        @foreach($reason_display as $k=>$r)
	                                            <option value="{{$k}}">{{$r}}</option>
	                                        @endforeach
	                                    @endif
	                                </select>
	                            </div>
	                            <div class="col-lg-3">
	                               <label class="control-label no-padding-right" for="name"><strong>Check In {{Lang::get('common.time')}}</strong></label>
	                               <div class="input-group bootstrap-timepicker">
	                                    <input required="required" class= "form-control" name="time" id="timepicker1" type="text" class="form-control" />
	                                    <span class="input-group-addon">
	                                        <i class="fa fa-clock-o "></i>
	                                    </span>
	                                </div>
	                            </div>
	                        </div>
	                        <br>
	                        <div class="row">
	                            <div class="col-lg-2">
	                                <label class="control-label no-padding-left" for="name"><strong>{{Lang::get('common.remarks')}}</strong></label>
	                                <textArea  class="form-control" required name="remarks" id="remarks" style="margin: 0px; width: 300px; height: 74px;">
	                                </textArea>
	                            </div>
	                            <br>
	                            <div class="col-lg-6 pull-right" >
	                               <input  class="form-control btn btn-primary" type="submit" name="submit" value="submit">
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </form>
	</div>
	@endif
</div>  
<!-- main conterner enda here  -->
<!-- ............................manual attandence submit form edns here.......................................  -->
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report33.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>
    <script>
        $('#timepicker1').timepicker({
                        minuteStep: 1,
                        showSeconds: true,
                        showMeridian: false,
                        disableFocus: true,
                        icons: {
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down'
                        }
                    }).on('focus', function() {
                        $('#timepicker1').timepicker('showWidget');
                    }).next().on(ace.click_event, function(){
                        $(this).prev().focus();
                    });
    </script>

     <script>
     $(document).on('change', '#role', function () {
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
                type: "POST",
                url: domain + '/get_user_by_role',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data.user_data, function (key, value) {
                          
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