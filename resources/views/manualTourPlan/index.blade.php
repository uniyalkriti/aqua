@extends('layouts.master') 
  
@section('title')
    <title>{{Lang::get('common.manual_tour_plan')}}</title>
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
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.manual_tour_plan')}}</li>
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
                                        <label class="control-label no-padding-right" for="name">Role</label>
                                        <select  name="role" id="role" class="form-control chosen-select input-sm">
                                            <option value="">select</option>
                                            @if(!empty($role))
                                                @foreach($role as $k=>$r)
                                                     <option {{ Request::get('role')==$k?'selected':'' }} value="{{$r}}">{{$r}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <div class="">
                                        <label class="control-label no-padding-right" for="name">UserName</label>
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
                                        <label for="date" class="control-label">Date</label>
                                        <input required value="{{ Request::get('date') }}" type="text" placeholder="From Date" name="date" id="date" class="form-control date-picker">
                                </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
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
    <form action="updateManualTourPlan" method="get" action="submitManualTourPlan" enctype="multipart/form-data">
              <input type="hidden" name="user_id" id="user_id" value=<?php echo $user_id ?> >
              <input type="hidden" name="date" value=<?php echo $date ?> >
              <input type="hidden" name="primary_id" value="{{$query->id}}">
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-md-12">
                            <div class="search-area well well-sm">
                            <div class="search-filter-header bg-primary">
                                <h5 class="smaller no-margin-bottom">
                                    <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; Tour Plan Details
                                </h5>
                            </div>
                            <div class="hr hr-dotted"></div>

                            <h4 class="blue smaller">
                                <i class="fa fa-tags"></i>
                                Task For The Day
                            </h4>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <select name="category" class="form-control" id="category" >
                                    <option value="" >Select</option>
                                    @if(!empty($task_of_the_day))
                                        @foreach($task_of_the_day as $k=>$r)
                                            <option {{($query->working_status_id == $k)?'selected':"" }} value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                            </div>


                            <div id="town_detail">
                                <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="1common">
                                        <i class="fa fa-envelope"></i>
                                        Town
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <select name="town" class="form-control" id="town" >
                                            <option selected="true" value=""  >Select</option>
                                            @if(!empty($town))
                                                @foreach($town as $tk=>$tr)
                                                    <option {{($query->town == $tk)?'selected':"" }} value="{{$tk}}">{{$tr}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        </div>
                                    </div>
                            </div>



                            <div id="smstr">
                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Dealer
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <select name="distributor" class="form-control" id="distributor" >
                                    </select>
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Beat
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <select name="beat" class="form-control beat" id="beat" >
                                    </select>
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Productive Call
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <input type="number" name="productive_call" id="productive_call" >
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Secondary Sales(RV)
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <input type="number" name="secondary_sales" id="secondary_sales" >
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Collection(RV)
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <input type="number" name="collection" id="collection" >
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Primary Order(RV)
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <input type="number" name="primary_order" id="primary_order" >
                                    </div>
                                </div>

                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    New Outlet Opening
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <input type="number" name="new_outlet" id="new_outlet" >
                                    </div>
                                </div>
                            </div>

                            <div id=remark>
                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Remarks
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="text" name="remark" id="remark_custom" >
                                    </div>
                                </div>
                            </div>
                            <div id=remark>
                                <div class="hr hr-dotted"></div>
                                <h4 class="blue smaller" id="common">
                                    <i class="fa fa-envelope"></i>
                                    Status
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <select name="admin_approved" >
                                            <option {{($query->admin_approved == 1)?'selected':''}} value="1">Approve</option>
                                            <option {{($query->admin_approved == 0)?'selected':''}} value="0">Disapprove</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                       
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-default btn-round btn-white">
                                    <i class="ace-icon fa fa-send green"></i>
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
    
<!-- ............................manual attandence submit form start here......................................  -->

	@elseif(isset($_GET['find']))  
	<div id="form-wrapper" style="max-width:700px;margin:auto;">
	    <form action="submitManualTourPlan" method="get" action="submitManualTourPlan" enctype="multipart/form-data">
	              <input type="hidden" name="user_id" id="user_id" value=<?php echo $user_id ?> >
	              <input type="hidden" name="date" value=<?php echo $date ?> >
	        <div class="main-content">
	            <div class="main-content-inner">
	                <div class="row">
	                    <div class="col-md-12">
                                <div class="search-area well well-sm">
                                <div class="search-filter-header bg-primary">
                                    <h5 class="smaller no-margin-bottom">
                                        <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; Tour Plan Details
                                    </h5>
                                </div>
                                <div class="hr hr-dotted"></div>

                                <h4 class="blue smaller">
                                    <i class="fa fa-tags"></i>
                                    Task For The Day
                                </h4>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <select name="category" class="form-control" id="category" >
                                        <option value="" >Select</option>
                                        @if(!empty($task_of_the_day))
	                                        @foreach($task_of_the_day as $k=>$r)
	                                            <option value="{{$k}}">{{$r}}</option>
	                                        @endforeach
	                                    @endif
                                        </select>
                                    </div>
                                </div>


                                <div id=town_detail>
                                    <div class="hr hr-dotted"></div>
                                        <h4 class="blue smaller" id="common">
                                            <i class="fa fa-envelope"></i>
                                            Town
                                        </h4>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                            <select name="town" class="form-control" id="town" >
                                                <option selected="true" value="" disabled="disabled" >Select</option>
                                                @if(!empty($town))
                                                    @foreach($town as $tk=>$tr)
                                                        <option value="{{$tk}}">{{$tr}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            </div>
                                        </div>
                                </div>



                                <div id=smstr>
                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Dealer
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <select name="distributor" class="form-control" id="distributor" >
                                        </select>
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Beat
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <select name="beat" class="form-control" id="beat" >
                                        </select>
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Productive Call
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="number" name="productive_call" id="productive_call" >
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Secondary Sales(RV)
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="number" name="secondary_sales" id="secondary_sales" >
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Collection(RV)
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="number" name="collection" id="collection" >
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Primary Order(RV)
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="number" name="primary_order" id="primary_order" >
                                        </div>
                                    </div>

                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        New Outlet Opening
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="number" name="new_outlet" id="new_outlet" >
                                        </div>
                                    </div>
                                </div>

                                <div id=remark>
                                    <div class="hr hr-dotted"></div>
                                    <h4 class="blue smaller" id="common">
                                        <i class="fa fa-envelope"></i>
                                        Remarks
                                    </h4>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <input type="text" name="remark" id="remark" >
                                        </div>
                                    </div>
                                </div>
                           
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-default btn-round btn-white">
                                        <i class="ace-icon fa fa-send green"></i>
                                        Submit
                                    </button>
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

@if(!empty($query))
<script>

     $(document).ready(function() {
        val = $('#town').val();
        user_id = document.getElementById('user_id').value;
        _hq = $('#distributor');
        _hq1 = $('#beat');
        // alert(user_id);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_dealer_name',
                dataType: 'json',
                data: "id=" + val +"&user_id=" + user_id,
              
                success: function (data) {
                    
                        template = '<option selected="true" value=""  >Select</option>';
                        // var template ='';
                        var dealer_id = '{{!empty($query->dealer_id)?$query->dealer_id:''}}';
                        $.each(data.result, function (key, value) {
                          
                            if (value.name != '') {
                                if(dealer_id == key)
                                {
                                    template += '<option selected value="' + key + '" >' + value + '</option>';
                                }
                                else
                                {
                                    template += '<option   value="' + key + '" >' + value + '</option>';
                                }
                            }
                        });
                        $.ajax({
                            type: "GET",
                            url: domain + '/get_beat_name_new',
                            dataType: 'json',
                            data: "id=" + dealer_id +"&user_id=" + user_id,
                            
                            success: function (data) {
                                
                                    var beat_id = '{{!empty($query->locations)?$query->locations:''}}';
                                    var template2 = '<option selected="true" value="" disabled="disabled" >Select</option>';

                                    $.each(data.result, function (key, value) {
                                      
                                        if (value.name != '') {
                                    // console.log('{{$query->any_other_task}}');
                                           // data = $('.beat option:eq(17065)').prop('selected', true);
                                           // console.log(key);
                                           if(beat_id == key)
                                           {
                                                template2 += '<option  selected value="' + key + '" >' + value + '</option>';
                                           }
                                           else
                                           {
                                                template2 += '<option   value="' + key + '" >' + value + '</option>';
                                           }
                                                
                                            
                                            
                                        }
                                    });
                                    // console.log(template);
                                  //  alert(_hq.val());
                                    $('#beat').empty();
                                    $('#beat').append(template2).trigger("chosen:updated");
                           

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });
                        // console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq1.empty();
                        _hq.append(template).trigger("chosen:updated");
               
                        document.getElementById("productive_call").value = "{{$query->pc}}";
                        document.getElementById("secondary_sales").value = "{{$query->rd}}";
                        document.getElementById("collection").value = "{{$query->collection}}";
                        document.getElementById("primary_order").value = "{{$query->primary_ord}}";
                        document.getElementById("new_outlet").value = "{{$query->primary_ord}}";
                        document.getElementById("remark_custom").value = "{{$query->any_other_task}}";
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
@endif
<script>
    $(document).ready(function () {
        var valu = document.getElementById("category");
        var condition = valu.options[valu.selectedIndex].text;
        // alert(condition);
        if(condition == 'RETAILING' )
        {
            $('#town_detail').show('fast');

        }
        else if(condition == 'MEETING' || condition == 'PROSPECTING')
        {
            $('#smstr').hide('fast');
            // $('#remark').hide('fast');

        }
        else
        {
            $('#town_detail').hide('fast');
            $('#smstr').hide('fast');
            $('#remark').hide('fast');
        }
        
      
    });
        $(document).on('change', '#category', function () {

        _current_val = this.options[this.selectedIndex].text;
        // _current_val = $(this).val();
        // _current_val = document.getElementById("category").innerHTML;
        // alert(_current_val);
        get_category(_current_val);
        });

        function get_category(val) 
        {
            if(val=='RETAILING')
            {
                $('#town_detail').show('fast');
                $('#smstr').show('fast');
                $('#remark').show('fast');
              
            }
            else if(val=='MEETING')
            {   
                distributor = $('#distributor');
                beat = $('#beat');
                document.getElementById("productive_call").value = "";
                document.getElementById("secondary_sales").value = "";
                document.getElementById("collection").value = "";
                document.getElementById("primary_order").value = "";
                document.getElementById("new_outlet").value = "";
                distributor.empty();
                beat.empty();

                $('#town_detail').show('fast');
                $('#remark').show('fast');
                $('#smstr').hide('fast');
              
            }
            else if(val=='PROSPECTING')
            {
                distributor = $('#distributor');
                beat = $('#beat');
                document.getElementById("productive_call").value = "";
                document.getElementById("secondary_sales").value = "";
                document.getElementById("collection").value = "";
                document.getElementById("primary_order").value = "";
                document.getElementById("new_outlet").value = "";
                distributor.empty();
                beat.empty();
            

                $('#town_detail').show('fast');
                $('#remark').show('fast');
                $('#smstr').hide('fast');
              
            }
            else
            {   
                town = $('#town');
                distributor = $('#distributor');
                beat = $('#beat');
                document.getElementById("productive_call").value = "";
                document.getElementById("secondary_sales").value = "";
                document.getElementById("collection").value = "";
                document.getElementById("primary_order").value = "";
                document.getElementById("new_outlet").value = "";
                distributor.empty();
                beat.empty();
             


                $('#remark').show('fast');
                $('#smstr').hide('fast');
                $('#town_detail').hide('fast');

            }
        }
    </script>


<script>
     $(document).on('change', '#town', function () {
        val = $(this).val();
        user_id = document.getElementById('user_id').value;
        _hq = $('#distributor');
        _hq1 = $('#beat');
        // alert(user_id);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_dealer_name',
                dataType: 'json',
                data: "id=" + val +"&user_id=" + user_id,
              
                success: function (data) {
                    
                        template = '<option selected="true" value="" disabled="disabled" >Select</option>';

                        $.each(data.result, function (key, value) {
                          
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        // console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq1.empty();
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


<script>
     $(document).on('change', '#distributor', function () {
        val = $(this).val();
        user_id = document.getElementById('user_id').value;
        _hq = $('#beat');
        // alert(user_id);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_beat_name_new',
                dataType: 'json',
                data: "id=" + val +"&user_id=" + user_id,
              
                success: function (data) {
                    
                        template = '<option selected="true" value="" disabled="disabled" >Select</option>';

                        $.each(data.result, function (key, value) {
                          
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        // console.log(template);
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