@extends('layouts.master')

@section('title')
    <title>{{$companyDetails->name}}</title>
@endsection
@section('css')
        <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
        <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
        <style>
        .modal-lg2{
            width: 1230px;
        }
        .modal-lg4{
            width: 1300px;
        }
        .modal-lg3{
            width: 1000px;
        }
        .paddding10{
            padding-top: 10px;
        }
        .paddding20{
            padding-top: 20px;
        }
        .paddding40{
            padding-top: 40px;
        }
        .border-class{
            border-radius: 8px;
        }
        .border-btn{
    border-radius: 25px;
    border: solid 1px #b0d5f7;
    padding: 0px 13px;
    font-size: 14px;
    margin-top: -4px !important;
}
.canvasjs-chart-credit{
    display: none;
}
.float-right {
  float: right !important; }

 

  .shadow-sm {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }

  .text-white {
  color: #fff !important; }

  .font16{
    font-family: 16px !important;
  }
  .content-right{
    display: flex !important;
    justify-content: space-evenly !important;
  }
  body{
    font-family: 'Noto Sans JP', sans-serif;
    background-color: #f5f5f7 !important;
  }
  .margin-box{
    margin-top: 10px;
    margin-right: 10px;
    margin-bottom: 10px;
    width: 260px;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.27);
  }
  .shadow-box{
    
    box-shadow: none;
  }
  .margin-box:hover{

  box-shadow: none;
}
.shadow-box:hover{

  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.27);
}
.editable{
    border-radius: 100%;
}
.star{
    color: #ff8c1a;
}
        </style>
@endsection
@section('body')

    <div class="main-content" >
        <div class="main-content-inner">
            

            <div class="page-content" style="background-color: #f5f5f7;">
                

                <div class="page-header" style="font-weight: bold;">
                    <div class="row">
                        <div class="col-lg-12">

                            <h1>
                                <div class="col-lg-9 ">
                                    {{Lang::get('common.retailer')}} {{Lang::get('common.dashboard')}}
                                    <small style="color: black;">
                                        <i class="ace-icon fa fa-angle-double-right" ></i>
                                        overview &amp; stats
                                    </small>
                                </div>

                                <div class="col-lg-2 ">
                                 <p class="bs-component">
                                    <a href="/public/retailer" target="_blank">
                                        <span style="display: flex;align-items: center; justify-content: center; border:1px #438eb9 solid; border-radius: 20px; font-size:14px; padding:7px; background-color:white; width:190px "><i class="fa fa-share "></i>&nbsp;&nbsp;<b>Total Outlets : {{COUNT($retailer_name)}}</b></span>
                                    </a>
                                </p>
                                </div>

                                <div class="col-lg-1 ">
                                <p class="bs-component">
                                    <a href="#" data-toggle="collapse" data-target="#retailerAnalytics" >
                                        <span  style="display: flex;align-items: center; justify-content: center; border:1px #438eb9 solid; border-radius: 20px; font-size:14px; padding:7px; background-color:white; "><i class="fa fa-calendar "></i>&nbsp;&nbsp;<b>Filter</b></span>
                                    </a>
                                </p>
                                </div>

                            </h1>

                        </div>    
                    </div>
                </div>
                <!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                            <?php
                            if(isset($_GET['date_range_picker']))
                            {
                                $year = $mdate;
                                
                            }
                            else {
                                $year = date('Y-m');
                            }
                            $location_3_filter = !empty($location_3_filter)?$location_3_filter:array();
                            $location_3_filter = implode(',', $location_3_filter);


                            ?>
                        <!-- PAGE CONTENT BEGINS -->
                        {{-- <div class="alert alert-block alert-success">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>

                            <i class="ace-icon fa fa-check green"></i>

                            Welcome to
                            <strong class="green">
                                mSELL <small></small>
                            </strong>,<a href="https://msell.in/web/">Manacle Technologies Pvt Ltd</a>
                        </div> --}}
                        <form class="form-horizontal open collapse in" action="retailerAnalytics" method="GET" id="retailerAnalytics" role="form"
                                enctype="multipart/form-data">
                                {!! csrf_field() !!}
                        
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                       
                                        
                                    <div class="col-xs-1 col-sm-2">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.location3')}}</label>
                                        <select multiple name="location_3[]" id="location_3" class="form-control chosen-select">
                                            <option value="">Select</option>
                                            @if(!empty($location_3))
                                                @foreach($location_3 as $sk=>$sr) 
                                                <?php if(empty($_GET['location_3']))
                                                 $_GET['location_3']=array();
                                                 ?>
                                                    <option @if(in_array($sk,$_GET['location_3'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                                    </option>
                                                @endforeach 
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xs-1 col-sm-2">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.location4')}}</label>
                                        <select multiple name="location_4[]" id="location_4" class="form-control chosen-select">
                                            <option value="">Select</option>
                                            @if(!empty($location_4))
                                                @foreach($location_4 as $sk=>$sr) 
                                                <?php if(empty($_GET['location_4']))
                                                 $_GET['location_4']=array();
                                                 ?>
                                                    <option @if(in_array($sk,$_GET['location_4'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                                    </option>
                                                @endforeach 
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xs-1 col-sm-2">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.location5')}}</label>
                                        <select multiple name="location_5[]" id="location_5" class="form-control chosen-select">
                                            <option value="">Select</option>
                                            @if(!empty($location_5))
                                                @foreach($location_5 as $sk=>$sr) 
                                                <?php if(empty($_GET['location_5']))
                                                 $_GET['location_5']=array();
                                                 ?>
                                                    <option @if(in_array($sk,$_GET['location_5'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                                    </option>
                                                @endforeach 
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xs-1 col-sm-2">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.location6')}}</label>
                                        <select multiple name="location_6[]" id="location_6" class="form-control chosen-select">
                                            <option value="">Select</option>
                                            @if(!empty($location_6))
                                                @foreach($location_6 as $sk=>$sr) 
                                                <?php if(empty($_GET['location_6']))
                                                 $_GET['location_6']=array();
                                                 ?>
                                                    <option @if(in_array($sk,$_GET['location_6'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                                    </option>
                                                @endforeach 
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xs-2 ">
                                        <label class="control-label no-padding-right"
                                        for="name">{{Lang::get('common.user')}}</label>
                                        <select multiple name="user[]" id="user" class="form-control chosen-select">
                                            <option disabled="disabled" value="">Select</option>
                                            @if(!empty($user))
                                            @foreach($user as $k=>$r)
                                            <?php if(empty($_GET['user']))
                                             $_GET['user']=array();
                                             ?>
                                            <option @if(in_array($k,$_GET['user'])){{'selected'}} @endif value="{{$k}}">{{$r}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>


                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                            for="name">{{Lang::get('common.distributor')}}</label>
                                            <select multiple name="distributor[]" id="dealer" class="form-control chosen-select">
                                                <option disabled="disabled" value="">Select</option>
                                                @if(!empty($dealer_name))
                                                @foreach($dealer_name as $k=>$r)
                                                <?php if(empty($_GET['distributor']))
                                                 $_GET['distributor']=array();
                                                 ?>
                                                <option @if(in_array($k,$_GET['distributor'])){{'selected'}} @endif value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    

                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">

                                    
                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                            for="name">{{Lang::get('common.location7')}}</label>
                                            <select multiple name="beat[]" id="beat" class="form-control chosen-select">
                                                <option disabled="disabled" value="">Select</option>
                                                @if(!empty($beat))
                                                @foreach($beat as $k=>$r)
                                                <?php if(empty($_GET['beat']))
                                                 $_GET['beat']=array();
                                                 ?>
                                                <option @if(in_array($k,$_GET['beat'])){{'selected'}} @endif value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                  
                                    
                                    <div class="col-xs-2">
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                            for="name">{{Lang::get('common.retailer_type')}}</label>
                                            <select multiple name="outlet[]" id="outlet" class="form-control chosen-select">
                                                <option disabled="disabled" value="">select</option>
                                                @if(!empty($outlet_type))
                                                @foreach($outlet_type as $k=>$r)
                                                <?php if(empty($_GET['outlet']))
                                                 $_GET['outlet']=array();
                                                 ?>
                                                <option @if(in_array($k,$_GET['outlet'])){{'selected'}} @endif value="{{$k}}">{{$r}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                                   for="name">From </label>
                                            <input  autocomplete="off" type="text" placeholder="From Date" value="{{$from_date}}" name="from_date" id="from_date" class="form-control input-sm">
                                    </div>
                                    <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                                   for="name">To</label>
                                            <input  autocomplete="off" type="text" placeholder="To Date" value="{{$to_date}}" name="to_date" id="to_date" class="form-control input-sm">
                                    </div>


                                    <div class="col-md-2">
                                        <label class="control-label no-padding-right" for="id-date-range-picker-1" style="visibility: hidden;">..</label>
                                        <button type="submit" class="form-control btn btn-sm btn-inverse  btn-block mg-b-10 input-sm"
                                                ><i class="fa fa-search mg-r-10"></i>
                                            {{Lang::get('common.find')}}
                                        </button>
                                    </div>


                                    </div>
                                </div>
                            </div>


                           <!--  <div class="row">
                                <div class="col-md-6" style=" display: flex; justify-content: right; margin-top: 28px; ">
                                    <span  style="display: flex;align-items: center; justify-content: center; border:1px #438eb9 solid; border-radius: 20px; font-size:14px; padding:7px; background-color:white; "><i class="fa fa-calendar "></i>&nbsp;&nbsp;<b>{{ date('d-M-y',strtotime($from_date)) .' To '. date('d-M-y',strtotime($to_date)) }}</b></span>
                                </div> 
                            </div> -->
                         </form>
                        

                         <div class="row">
                                <div class="col-sm-6">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                User Retailer Creation Progress
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                           

                                            
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="showGraph" class="border-class" style="height: 370px; width: 100%;"></div>
                                                    
                                                    <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div>

                                <div class="col-sm-6">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                New Retailer Sale Stats
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                           

                                            
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="maxRetailerSales" class="border-class" style="height: 370px; width: 100%;"></div>
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                    <!-- <a href="{{url('user-sale')}}" target="_">View More&nbsp;<i class="ace-icon fa fa-arrow-right "></i></a> -->
                                </div>

                              


                                
                            </div>
                      
                            <div class="hr hr32 hr-dotted"></div>
                            

                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Retailer Creation Stats
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                           
    
                                            
                                        </div>
    
                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="BarChart_on_load"  class="img-responsive" style="height: 370px; width: 100%;" ></div>
                                                    <!-- <canvas id="BarChart_on_load" height="100%" class="img-responsive" style="height: 370px; width: 100%;" ></canvas> -->
                                                    <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div><!-- /.col -->


                                <div class="col-sm-5">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Retailer Category Progress
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                           

                                            
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                             <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                                                    
                                                    <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div>
                                
                            </div><!-- /.row -->

                            <div class="hr hr32 hr-dotted"></div>
                       
                       
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div>

@endsection
@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.easypiechart.min.js')}}"></script>    
    <script src="{{asset('msell/js/jquery.flot.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.resize.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/BarChart.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
     <script src="{{asset('nice/js/canvasjs.min.js')}}"></script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
    @include('common_filter.filter_script_sale')
    @include('DashboardScript.retailerAnalyticScript')
    

    <!-- <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
   


  
       <!-- inline scripts related to this page -->
   
   



    <!-- ############### PIE Chart Script Ends Here ################### -->
@endsection