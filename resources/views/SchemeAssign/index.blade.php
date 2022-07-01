@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.assign_module')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
		<link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
		<link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
		<link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
@endsection

@section('body')
<!-- ......................table contents........................................... -->
<!-- main container starts here  -->
<div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.assign_module')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#assignModule" class="btn btn-sm btn-default"><i
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

                        <form class="form-horizontal open collapse in"  method="get" id="assignModule" enctype="multipart/form-data">
                            <!-- {!! csrf_field() !!} -->
                            <input type="hidden" name="flag" value='1'>
                           
                                    <div class="col-xs-3 col-lg-3">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>
                                        
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar bigger-110"></i>
                                                </span>

                                                <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                            </div>
                                    </div>
                                    <div class="col-xs-3 col-lg-3">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1">Plan</label>
                                        <select name="plan_id" required id="plan_id" class="form-control chosen-select required">
                                            <option value=''>==Select==</option>
                                        @foreach($plan_array as $p_key => $p_value)
                                            <option  value="{{$p_key}}">{{$p_value}}</option>
                                        @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3">
                                            <div class="">
                                            <label class="control-label no-padding-right" for="name">State</label>
                                                <select multiple name="state[]"  id="state" class="form-control chosen-select">
                                                    <option value="">select</option>
                                                    @if(!empty($location_3))
                                                        @foreach($location_3 as $k=>$d)
                                                        <?php if(empty($_GET['state']))
                                                        $_GET['state']=array();
                                                    ?>
                                                            <option @if(in_array($k,$_GET['state'])){{"selected"}}  @endif value="{{$k}}">{{$d}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                              

                                <div class="col-xs-3  col-lg-2">
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

    @if(!empty($dealer_data))
    <div id="form-wrapper" style="max-width:1200px;margin:auto;">
        <form  method="post" action="{{route('schemeAssign.store')}}"  enctype="multipart/form-data">
             {!! csrf_field() !!}
                
             <input type ="hidden" name="plan_id" value="{{$plan_id}}">
            <input type ="hidden" name="to_date" value="{{$to_date}}">
            <input type ="hidden" name="from_date" value="{{$from_date}}">
                     <div class="row">
                  
                              @if(!empty($dealer_data))
                                    
                                    <div class="col-md-6">
                                        <table class="table table-striped table-bordered table-hover" width="50%">
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Dealer Name</th>
                                                <th>Assign<br> <input type="checkbox" onchange="checkAll(this)"></th>

                                            <tr>
                                            @foreach($dealer_data as $k=>$d)
                                            <tr>
                                                <td>{{$k+1}}</td>
                                                <td>{{$d->name}}</td>
                                                <td ><input type="checkbox" checked name="dealer_data_id[]" value="{{$d->id}}"></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-striped table-bordered table-hover" width="50%">
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Retailer Name</th>
                                                <th>Action</th>

                                            <tr>
                                            @foreach($retailer_data as $key=>$ddata)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$ddata->name}}</td>
                                                <td><input type="checkbox" checked name="retailer_data_id[]" value="{{$ddata->id}}"></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    
                                @endif
                              
                      </div>
                    <div class="row">
                        <div class="col-md-3" align="center"><br>
                           <input class="form-control btn btn-primary" type="submit" name="submit" value="Submit">
                         </div>
                    </div>
             
           
        </form>
    </div>
    @endif
</div>
    <!-- ......................table ends contents...........................................  -->
                               
@endsection

@section('js')
<script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report4.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>	
	<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        jQuery(function ($) {
            $('#assignModule').collapse('hide');
        });
    </script>
    <script type="text/javascript">
        function checkAll(ele) 
        {
            var checkboxes = document.getElementsByTagName('input');
            if (ele.checked) 
            {
                 for (var i = 0; i < checkboxes.length; i++) 
                {
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = true;
                    }
                }
            } 
            else 
            {
                for (var i = 0; i < checkboxes.length; i++) 
                {
                     console.log(i)
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = false;
                    }
                }
            }
        }
    </script>
   <script>
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        //show datepicker when clicking on the icon
        .next().on(ace.click_event, function(){
            $(this).prev().focus();
        });

        //or change it into a date range picker
        $('.input-daterange').datepicker({autoclose:true});


        //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $('input[name=date_range_picker]').daterangepicker({
            'applyClass' : 'btn-sm btn-success',
            'cancelClass' : 'btn-sm btn-default',
                showDropdowns: true,
            // showWeekNumbers: true,             
            minDate: '2017-01-01',
            maxDate: moment().add(2, 'years').format('YYYY-01-01'),
            locale: {
                format: 'YYYY/MM/DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
            },
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    
        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        });	
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    <script>
        $("#assignModule").validate({
            messages: {
            plan_id: {
            required: "Please select an option from the list, if none are appropriate please select 'Other'",
            },
            }
        });
    </script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>

@endsection