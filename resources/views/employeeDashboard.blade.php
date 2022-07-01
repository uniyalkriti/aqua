@extends('layouts.master')

@section('title')
    <title>{{ config('app.name', '') }}</title>
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

        </style>
@endsection
@section('body')

    <div class="main-content" style="overflow-x: scroll;">
        <div class="main-content-inner">
            

            <div class="page-content">
                {{--<div class="ace-settings-container" id="ace-settings-container">
                    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                        <i class="ace-icon fa fa-cog bigger-130"></i>
                    </div>

                    <div class="ace-settings-box clearfix" id="ace-settings-box">
                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <div class="pull-left">
                                    <select id="skin-colorpicker" class="hide">
                                        <option data-skin="no-skin" value="#438EB9">#438EB9</option>
                                        <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                        <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                        <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                    </select>
                                </div>
                                <span>&nbsp; Choose Skin</span>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
                                <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
                                <label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
                                <label class="lbl" for="ace-settings-add-container">
                                    Inside
                                    <b>.container</b>
                                </label>
                            </div>
                        </div><!-- /.pull-left -->

                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
                                <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
                                <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
                                <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                            </div>
                        </div><!-- /.pull-left -->
                    </div><!-- /.ace-settings-box -->
                </div><!-- /.ace-settings-container -->--}}

                <div class="page-header">
                    <h1>
                        {{Lang::get('common.dashboard')}}
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            overview &amp; stats
                        </small>
                    </h1>
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
                            // $from_date = $year."-01";
                            // $to_date = date('Y-m-d');
                            // $yearMonth = date('M-Y',strtotime($year));
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
                        
                        <div class="row">
                           <div class="col-md-12" style="text-align: center">
                                <span class="label label-success arrowed-in arrowed-in-right" style="width:200px">{{ $from_date .' To '. $to_date }}</span>
                           </div>

                                <form class="form-horizontal open collapse in" action="home" method="GET" id="homedata1" role="form"
                                enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                                
                                            {{-- <div class="">
                                                <label class=" control-label no-padding-right"
                                                       for="name">{{Lang::get('common.month')}}</label>
                                                <input value="{{$mdate}}" type="text" placeholder="Select Month" name="year" id="year" class="form-control date-picker input-sm">
                                            </div> --}}

                                                <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                                           
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar bigger-110"></i>
                                                    </span>

                                                    <input class="form-control input-sm" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                                </div>
                                        
                                    </div>
                               
                                    
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <button type="submit" class="form-control btn btn-sm btn-inverse  btn-block mg-b-10 input-sm"
                                                style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                            {{Lang::get('common.find')}}
                                        </button>
                                    </div>

                                </form>

                        </div>
                        @if($company_id == 49 || $company_id == 37)
                        <div class="row">
                            <div class="space-12"></div>
                                {{-- <div class="col-sm-1"></div> --}}

                                <div class="col-sm-12 infobox-container">

                                <div class="infobox infobox-orange infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$totalSalesTeam}}</div>
                                            <a title="Sales Teams" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#user_details_modal" class="user-modal totalSalesTeam">
                                            <div class="infobox-content">{{Lang::get('common.total')}}  Team</div>
                                            </a>
                                        </div>
                                </div>

                                <div class="infobox infobox-blue infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$leave_count}}</div>
                                        <!-- <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal" class="distributor-modal dealerDetailsHome"> -->
                                        <div class="infobox-content">Total Leave</div>
                                        <!-- </a> -->
                                    </div>
                                </div>

                                <div class="infobox infobox-red infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$leave_count_a}}</div>
                                        <!-- <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal" class="distributor-modal dealerDetailsHome"> -->
                                        <div class="infobox-content">Total Leave Approved</div>
                                        <!-- </a> -->
                                    </div>
                                </div>

                                <div class="infobox infobox-green infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$leave_count-$leave_count_a}}</div>
                                        <!-- <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal" class="distributor-modal dealerDetailsHome"> -->
                                        <div class="infobox-content">Total Leave N.Approve</div>
                                        <!-- </a> -->
                                    </div>
                                </div>

                                <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-map-marker"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$expense_details}}</div>
                                            <div class="infobox-content">Total Expense Filled</div>
                                            
                                        </div>
                                </div>


                                <div class="infobox infobox-orange infobox-large infobox-light">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-mobile"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalAttd}}</div>
                                         <a title=" Today's attendance log" mdate="{{ $mdate }}" data-toggle="modal" data-target="#attendance_modal" class="user-modal">
                                        <div class="infobox-content">Today's {{Lang::get('common.attendance')}}</div>
                                        </a>
                                    </div>
                                </div>

                                

                                </div>
                            </div>
                        </div>

                        @else
                        <div class="row">
                            <div class="space-12"></div>
                            {{-- <div class="col-sm-1"></div> --}}

                            <div class="col-sm-12 infobox-container">
                               
                                <div class="infobox infobox-orange infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$totalSalesTeam}}</div>
                                            <a title="Sales Teams" state_id="{{ $location_3_filter }}" mdate="{{ $mdate }}" data-toggle="modal" data-target="#user_details_modal" class="user-modal totalSalesTeam">
                                            <div class="infobox-content">{{Lang::get('common.total')}} Sales Team</div>
                                            </a>
                                        </div>
                                </div>
                                {{-- <div class="infobox infobox-green infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{ $totalSS}}</div>
                                            <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.csa')}}</div>
                                            
                                        </div>
                                    </div> --}}

                                <div class="infobox infobox-blue infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalDistributor}}</div>
                                        <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal-common" class="distributor-modal-common dealerDetailsHomeCommon">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.distributor')}}</div>
                                        </a>
                                    </div>
                                </div>

    
                             {{--   <div class="infobox infobox-blue infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalDistributor}}</div>
                                        <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal" class="distributor-modal dealerDetailsHome">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.distributor')}}</div>
                                        </a>
                                    </div>
                                </div>

                                --}}


                                <div class="infobox infobox-red infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalOutlet}}</div>
                                        <a title="Outlet List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#retailer-modal-neha" class="retailer-modal-neha retailerNehaDetailsHome">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</div>
                                        </a>
                                        
                                    </div>
                                </div>

                                
                            {{--    <div class="infobox infobox-red infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalOutlet}}</div>
                                        <a title="Outlet List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#retailer-modal" class="retailer-modal retailerDetailsHome">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</div>
                                        </a>
                                        
                                    </div>
                                </div>  --}}


                                <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-map-marker"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$location_5}}</div>
                                            <a title="Beat List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#beat-modal" class="beat-modal beatDetailsHome">
                                            <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.location7')}}</div>
                                            </a>
                                        </div>
                                </div>
                               
                                <div class="infobox infobox-purple infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-phone"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$totalCall->total_call}}</div>
                                            <a title="List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#totalcall-modal" class="totalcall-modal totalCallDetails">
                                            <div class="infobox-content">{{Lang::get('common.total_call')}}</div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="infobox infobox-pink infobox-large infobox-dark">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-shopping-basket"></i>
                                            </div>
        
                                            <div class="infobox-data" >

                                                
                                                <div class="infobox-data-number" id="after_click">{{!empty($totalOrderValue)?array_sum($totalOrderValue):'0'}}</div>
                                                <div class="infobox-data-number" id="on_load"></div>
                                                <a title="State wise sale" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#myModal" class="user-modal stateWiseDetails">   
                                                <div class="infobox-content">{{Lang::get('common.secondary_sale')}}</div>
                                                </a>
                                            </div>
                                        </div>

                                <br>

                                <div class="infobox infobox-orange infobox-large infobox-light">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-mobile"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalAttd}}</div>
                                         <a title=" Today's attendance log" mdate="{{ $mdate }}" data-toggle="modal" data-target="#attendance_modal" class="user-modal">
                                        <div class="infobox-content">Today's {{Lang::get('common.attendance')}}</div>
                                        </a>
                                    </div>
                                </div>

                                {{-- <div class="infobox infobox-green infobox-large infobox-light">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$totalSSSale->csasale}}</div>
                                            <div class="infobox-content">{{Lang::get('common.csa')}} Coverage</div>
                                            
                                        </div>
                                    </div> --}}
                                

                                    <div class="infobox infobox-blue infobox-large infobox-light">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$totalDistributorSale->dealersale}}</div>
                                            <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-coverage-modal" class="distributor-coverage-modal dealerCoverageDetailsHome">
                                                <div class="infobox-content">{{Lang::get('common.distributor')}} Coverage</div>
                                            </a>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="infobox infobox-red infobox-large infobox-light">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-mobile"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div class="infobox-data-number">{{$totalOutletSale->outletsale}}</div>
                                                <a title="Outlet List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#retailer-coverage-modal" class="retailer-coverage-modal retailerCovergaeDetailsHome">
                                                    <div class="infobox-content">{{Lang::get('common.retailer')}} Coverage</div>
                                                </a>
                                                
                                            </div>
                                        </div>
                                        <div class="infobox infobox-grey infobox-large infobox-light">
                                                <div class="infobox-icon">
                                                    <i class="ace-icon fa fa-mobile"></i>
                                                </div>
            
                                                <div class="infobox-data">
                                                    <div class="infobox-data-number">{{$totalBeatSale->beatsale}}</div>
                                                    <a title="Beat Coverage Modal" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#beat_coverage_modal" class="user-modal totalBeatCoverage">
                                                    <div class="infobox-content">{{Lang::get('common.location7')}} Coverage</div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="infobox infobox-purple infobox-large infobox-light">
                                                    <div class="infobox-icon">
                                                        <i class="ace-icon fa fa-phone"></i>
                                                    </div>
                
                                                    <div class="infobox-data">
                                                        <div class="infobox-data-number">{{$productiveCall->productive_call}}</div>
                                                        <a title="Productive Call Modal" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#product_call_modal" class="user-modal totalProductiveCoverage">
                                                        <div class="infobox-content">{{Lang::get('common.productive_call')}}</div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="infobox infobox-pink infobox-large infobox-light">
                                                        <div class="infobox-icon">
                                                            <i class="ace-icon fa fa-shopping-basket"></i>
                                                        </div>
                    
                                                        <div class="infobox-data">
                                                            <div class="infobox-data-number">{{$totalPrimaryOrder->total_sale_value}}</div>
                                                            <a title="State wise sale" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#primaryModal" class="user-modal primaryStateWiseDetails">
                                                            <div class="infobox-content">{{Lang::get('common.primary_sale')}}</div>
                                                            </a>
                                                            
                                                        </div>
                                                </div>
                                            <div class="infobox infobox-purple infobox-large infobox-light">
                                                    <div class="infobox-icon">
                                                        <i class="ace-icon fa fa-phone"></i>
                                                    </div>
                
                                                    <div class="infobox-data">
                                                        <div class="infobox-data-number"></div>
                                                        <a title="Outlet" mdate="{{ $mdate }}" data-toggle="modal" data-target="#inactiver_retailer" >
                                                        <div class="infobox-content">Inavtivity {{Lang::get('common.retailer')}}</div>
                                                        </a>
                                                    </div>
                                                </div>
                                              
                                               
                               
                                

                            </div>
                      
                            <div class="vspace-12-sm"></div>
                           
                            <!-- /.col -->
                        </div><!-- /.row -->
                        @endif

                        <div class="hr hr32 hr-dotted"></div>
                        <div class="row">
                            <div class="col-sm-1"></div>
                                <div class="col-sm-12">
                                    <div class="widget-box">
                                        <div class="widget-header widget-header-flat">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Monthly Attendance/Leave
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                            <h5 class="widget-title lighter pull-right">
                                                    <i class="ace-icon fa fa-circle blue"></i>
                                                    Monthly Attendance/Leave
                                            </h5>
    
                                            
                                        </div>
    
                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <canvas id="BarChart_on_load" height="100%" class="img-responsive" ></canvas>
                                                    <canvas id="BarChart_after_load" height="100%" class="img-responsive" ></canvas>
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <div class="hr hr32 hr-dotted"></div>
    
                        <div class="row">
                                
                            <div class="col-sm-12">
                                <div class="widget-box ">
                                    <div class="widget-header widget-header-flat">
                                        <h4 class="widget-title lighter">
                                            <i class="ace-icon fa fa-star orange"></i>
                                            Employee
                                        </h4>

                                        <div class="widget-toolbar">
                                            <a href="#" data-action="collapse">
                                                <i class="ace-icon fa fa-chevron-up"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thin-border-bottom">
                                                    <tr>
                                                        <th>
                                                            <i class="ace-icon fa fa-caret-right blue"></i>{{Lang::get('common.role_key')}}
                                                        </th>

                                                        <th>
                                                            <i class="ace-icon fa fa-caret-right blue"></i>Count
                                                        </th>

                                                        {{-- <th class="hidden-480">
                                                            <i class="ace-icon fa fa-caret-right blue"></i>{{Lang::get('common.status')}}
                                                        </th> --}}
                                                        @foreach($work_status_array as $wkey=>$work_status_value)
                                                        <th><i class="ace-icon fa fa-caret-right blue"></i>{{$work_status_value}}</th>
                                                        @endforeach
                                                        <th>Absent</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach($roleWiseTeam as $rkey=>$role)
                                                    <?php 
                                                    $count = $role->count;
                                                    ?>
                                                    <tr>
                                                        <td>{{$role->rolename}}</td>

                                                        <td>
                                                            
                                                            <span class="label label-info arrowed-right arrowed-in"><a title="User Details" work_status="0" role_id="{{$role->role_id}}" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#user_details_on_role" class="user-modal user_details_on_role" style="color: white;">{{$role->count}}</a></span>
                                                            
                                                        </td>

                                                        {{-- <td class="hidden-480">
                                                            <span class="label label-info arrowed-right arrowed-in">on sale</span>
                                                        </td> --}}
                                                        <?php $absent_count = []; ?>
                                                        @foreach($work_status_array as $wkey_new=>$work_status_value_new)
                                                        <?php 
                                                        $absent_count[] = !empty($role_wise_attendance[$role->role_id.$wkey_new])?$role_wise_attendance[$role->role_id.$wkey_new]:0;
                                                         // dd($role_wise_attendance)   
                                                         ?>
                                                        <td>
                                                            
                                                                <span class="label label-info arrowed-right arrowed-in">
                                                                    <a title="User Details" work_status="{{$wkey_new}}" role_id="{{$role->role_id}}" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#user_details_on_role" class="user-modal user_details_on_role" style="color: white;">{{!empty($role_wise_attendance[$role->role_id.$wkey_new])?$role_wise_attendance[$role->role_id.$wkey_new]:'0'}}</a>
                                                                </span>

                                                        </td>
                                                        @endforeach
                                                        <?php
                                                        $final_array_count = $count-(array_sum($absent_count));
                                                         ?>
                                                        <td><span class="label label-info arrowed-right arrowed-in"><a title="User Details" work_status="Absent" role_id="{{$role->role_id}}" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#user_details_on_role" class="user-modal user_details_on_role" style="color: white;">{{$final_array_count}}</a></span></td>

                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div><!-- /.widget-main -->
                                    </div><!-- /.widget-body -->
                                </div><!-- /.widget-box -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                       
                       {{--  <div class="hr hr32 hr-dotted"></div>
                            <div class="widget-box">
                                <div class="widget-header widget-header-flat widget-header-small">
                                    <h5 class="widget-title">
                                        <i class="ace-icon fa fa-signal"></i>
                                        Category Wise Order Booking ({{ $from_date .' To '. $to_date }})
                                    </h5>

                                    
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main">
                                            {{-- <div id="piechart"></div>

                                        <div class="hr hr8 hr-double"></div> --}}

                                        <div class="clearfix">
                                            <ul id="tasks" class="item-list">
                                                    
                                                @foreach($catalog1Sale as $c1key=>$cval)
                                                @if($cval->sale >0)
                                                <li class="item-orange clearfix">
                                                    <label class="inline">
                                                    <span class="lbl">{{$cval->c1_name}}</span>
                                                    </label>

                                                    <div class="pull-right " data-size="30" data-color="">
                                                            <i class="ace-icon fa fa-rupee"></i>
                                                        <span class="percent" style="color:{{$cval->color_code}}">{{$cval->sale}}</span>
                                                    </div>
                                                </li>
                                                @endif

                                                @endforeach
                                            </ul>

                                            
                                        </div>
                                    </div><!-- /.widget-main -->
                                </div><!-- /.widget-body -->
                            </div><!-- /.widget-box --> 
                       
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div>

<!-- Modal  for secondary sale -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
<!--             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <!-- <h4 class="modal-title" >Details</h4> -->
            <!-- </div> -->
            <!-- <div class="modal-body"> -->
            <!--  -->
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-signal"></i>
                                {{Lang::get('common.location3')}} Wise Sale
                            </h5>

                        </div>

                        <div class="widget-body">
                            <div class="widget-main">
                                <div id="viewer">
                                <div id="piechart-placeholder"></div>
                                </div>


                            
                            </div><!-- /.widget-main -->
                        </div><!-- /.widget-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger pull-left"  data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.widget-box -->
                <!--  -->
            <!-- </div> -->
        </div>
    </div>
</div>
<!-- ends here for sale -->
<!-- Modal  for secondary sale -->
<div class="modal fade" id="primaryModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
<!--             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <!-- <h4 class="modal-title" >Details</h4> -->
            <!-- </div> -->
            <!-- <div class="modal-body"> -->
            <!--  -->
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-signal"></i>
                                {{Lang::get('common.location3')}} Wise Sale
                            </h5>

                        </div>

                        <div class="widget-body">
                            <div class="widget-main">
                                <div id="viewer">
                                <div id="piechart-placeholder-primary"></div>
                                </div>


                            
                            </div><!-- /.widget-main -->
                        </div><!-- /.widget-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger pull-left"  data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.widget-box -->
                <!--  -->
            <!-- </div> -->
        </div>
    </div>
</div>
<!-- ends here for sale -->
<!-- Modal here for attendance detals -->
<div class="modal fade" id="attendance_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >{{Lang::get('common.attendance')}} Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.user_contact')}}</th>
                        <th>Check In time</th>
                        <th>{{Lang::get('common.working')}} {{Lang::get('common.status')}}</th>
                    </thead>
                    @foreach($attendance_details as $key => $value)
                    <?php 
                        $encid = Crypt::encryptString($value->user_id);
                    ?>
                        <tbody>
                            <td>{{$key+1}}</td>
                            <td><a href="{{url('user/'.$encid)}}" >{{$value->user_name}}</a></td>
                            <td>{{$value->mobile}}</td>
                            <td>{{$value->work_date}}</td>
                            <td>{{$value->work_status_name}}</td>
                        </tbody>  
                    @endforeach
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal here for user detals -->
<div class="modal fade" id="user_details_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Sales Team Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.user_contact')}}</th>
                        <th>{{Lang::get('common.role_key')}}</th>
                        <th>{{Lang::get('common.location3')}}</th>
                    </thead>
                    <tbody class="mytbody_totalsalesteam">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->
 <!-- Modal here for user detals -->
<div class="modal fade" id="product_call_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >{{Lang::get('common.productive_call')}} {{Lang::get('common.details')}}</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.user_contact')}}</th>
                        <th>No Of {{Lang::get('common.productive_call')}}</th>
                        <th>{{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th>
                    </thead>
                    <tbody class="mytbody_productive_details">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->
  <!-- Modal here for user detals -->
<div class="modal fade" id="beat_coverage_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >{{Lang::get('common.location7')}} coverage Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.location7')}} Name</th>
                        <th>{{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th>
                    </thead>

                    <tbody class="tbody_totalbeat_coverage">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
 <!-- end modal -->
<!-- division wise sale modal starts here -->
<div class="modal fade" id="division_sale_modal11" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >{{Lang::get('common.user')}} Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead class = "mythead">

                    </thead>
                    <tbody class="mytbody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- division wise sale modal ends here -->
      
<div class="modal fade"  id="inactiver_retailer" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Inactivity {{Lang::get('common.retailer')}} Details</h4>
            </div>
            <div class="modal-body ">
                <table class="table table-bordered table-hover">
                    <thead class = "">
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.location3')}}</th>
                        <th>Last 15 Days From {{ $from_date }}</th>
                        <th>Last 30 Days From {{ $from_date }}</th>
                        <th>Last 45 Days From {{ $from_date }}</th>
                    </thead>
                    <tbody class="">
                        <?php $i=1; ?>

                        @if(!empty($records) && count($records)>0)
                            @foreach($records as $key=>$data)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$data}}</td> 
                                <td>  <a  style="margin-left: 5px;" title="Not Contacted Retailers" state_id_data="{{ $key }}"  from_date="{{ $from_date }}" flag="{{ 1 }}" data-toggle="modal" data-target="#sluggish_retailer_modal" class="sluggish_retailer">{{!empty($not_visit_list_query_15[$key])?$not_visit_list_query_15[$key]:0}}</a></td>
                                <td> <a  style="margin-left: 5px;" title="Not Contacted Retailers" state_id_data="{{ $key }}"  from_date="{{ $from_date }}" flag="{{ 2 }}" data-toggle="modal" data-target="#sluggish_retailer_modal" class="sluggish_retailer">{{!empty($not_visit_list_query_30[$key])?$not_visit_list_query_30[$key]:0}}</a></td>
                                <td> <a  style="margin-left: 5px;" title="Not Contacted Retailers" state_id_data="{{ $key }}"  from_date="{{ $from_date }}" flag="{{ 3 }}" data-toggle="modal" data-target="#sluggish_retailer_modal" class="sluggish_retailer">{{!empty($not_visit_list_query_45[$key])?$not_visit_list_query_45[$key]:0}}</a></td>
                                <?php $i++;
                                    $last_15_sum[] = !empty($not_visit_list_query_15[$key])?$not_visit_list_query_15[$key]:0;
                                    $last_30_sum[] = !empty($not_visit_list_query_30[$key])?$not_visit_list_query_30[$key]:0;
                                    $last_45_sum[] = !empty($not_visit_list_query_45[$key])?$not_visit_list_query_45[$key]:0;


                                 ?>
                            </tr>
                            @endforeach
                        

                             <tr>
                                <td colspan="2"><b>{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</b></td>
                                <td><b>{{array_sum($last_15_sum)}}</b></td>
                                <td><b>{{array_sum($last_30_sum)}}</b></td>
                                <td><b>{{array_sum($last_45_sum)}}</b></td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sluggish_retailer_modal" role="dialog">
    <div class="modal-dialog modal-lg3" >
    
        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title center" ><b><u>Inactive {{Lang::get('common.retailer')}}</u></b></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>Sno</td>
                            
                            <td>{{Lang::get('common.location3')}} Name</td>
                            <td>{{Lang::get('common.distributor')}} Name</td>
                            <td>{{Lang::get('common.location7')}} Name</td>
                            <td>{{Lang::get('common.retailer')}} Name</td>
                            <td>Contact Person Name</td>
                            <td>{{Lang::get('common.retailer')}} Number</td>

                        </tr>
                    </thead>
                    <tbody class="mytbody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- common distributor details -->
<div class="modal fade" id="distributor-modal-common" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Distributor Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.status')}}</th>
                        <th>Count</th>
                    </thead>

                    <tbody class="tbody_distributor_details_home_common">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- common distributor details -->



<div class="modal fade" id="distributor-modal" role="dialog">
    <div class="modal-dialog modal-lg2" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >{{Lang::get('common.distributor')}} Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.location6')}}</th>
                        <th>{{Lang::get('common.distributor')}} Name</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.user_contact')}}</th>
                        <th>{{Lang::get('common.distributor')}} Wise {{Lang::get('common.location7')}} Count</th>
                        <th>{{Lang::get('common.distributor')}} Wise {{Lang::get('common.retailer')}} Count</th>
                        <th>{{Lang::get('common.distributor')}} Assigned {{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.secondary_sale')}}</th>
                    </thead>

                    <tbody class="tbody_dealer_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="distributor-coverage-modal" role="dialog">
    <div class="modal-dialog modal-lg2" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >{{Lang::get('common.distributor')}} Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.location6')}}</th>
                        <th>{{Lang::get('common.distributor')}} Name</th>
                        <th>{{Lang::get('common.distributor')}} {{Lang::get('common.user_contact')}}</th>
                        <th>{{Lang::get('common.distributor')}} Wise {{Lang::get('common.location7')}} Count</th>
                        <th>{{Lang::get('common.distributor')}} Wise {{Lang::get('common.retailer')}} Count</th>
                        <th>{{Lang::get('common.distributor')}} Assigned {{Lang::get('common.username')}}</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_dealer_coverage_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="retailer-modal" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Outlet Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location6')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location7')}}</th>
                        <th>{{Lang::get('common.distributor')}} Name</th>
                        <th>{{Lang::get('common.retailer')}} Name</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.user_contact')}}</th>
                        <th>{{Lang::get('common.retailer')}} Assigned {{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.retailer')}} Added By</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.created_date')}}</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_retailer_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- retailer details for neha -->
<div class="modal fade" id="retailer-modal-neha" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Outlet Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.status')}}</th>
                        <th>Count</th>
                    </thead>

                    <tbody class="tbody_retailer_details_neha_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- retailers details for neha ends -->
<div class="modal fade" id="retailer-coverage-modal" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >{{Lang::get('common.retailer')}} Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location6')}}</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.location7')}}</th>
                        <th>{{Lang::get('common.distributor')}} Name</th>
                        <th>{{Lang::get('common.retailer')}} Name</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.user_contact')}}</th>
                        <th>{{Lang::get('common.retailer')}} Assigned {{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.retailer')}} Added By</th>
                        <th>{{Lang::get('common.retailer')}} {{Lang::get('common.created_date')}}</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_retailer_coverage_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="beat-modal" role="dialog">
    <div class="modal-dialog modal-lg3" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >{{Lang::get('common.location7')}} Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.location3')}}</th>
                        <th>{{Lang::get('common.location6')}}</th>
                        <th>{{Lang::get('common.location7')}}</th>
                        <th>{{Lang::get('common.distributor')}} Count</th>
                        <th>{{Lang::get('common.retailer')}} Count</th>
                        <th>{{Lang::get('common.secondary_sale')}}</th>
                    </thead>

                    <tbody class="tbody_beat_details">
                        
                    </tbody>
                    <!-- <tfoot class="tfoot_beat_details">
                        
                    </tfoot> -->
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="user_details_on_role" role="dialog">
    <div class="modal-dialog modal-lg3" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >{{Lang::get('common.user')}} Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{lang::get('common.location3')}}</th>
                        <th>{{lang::get('common.username')}}</th>
                        <th>{{lang::get('common.role_key')}}</th>
                        <th>{{lang::get('common.user_contact')}}</th>
                        <th>Check In time</th>
                    </thead>

                    <tbody class="tbody_user_on_role">
                        
                    </tbody>
                    <!-- <tfoot class="tfoot_beat_details">
                        
                    </tfoot> -->
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="totalcall-modal" role="dialog">
    <div class="modal-dialog " >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Calls Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.date')}}</th>
                        <th>{{Lang::get('common.total_call')}} Count</th>
                        <th>{{Lang::get('common.productive_call')}} Count</th>
                        <th>{{Lang::get('common.secondary_sale')}}</th>
                    </thead>

                    <tbody class="tbody_total_calls_details">
                        
                    </tbody>
                    <!-- <tfoot class="tfoot_beat_details">
                        
                    </tfoot> -->
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
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
    <script>
    $(".chosen-select").chosen();

        $( document ).ready(function() {
            // console.log( "after_click" );
            // document.getElementById('after_click').hide;
      

            var barChartData = {
            
                labels:<?=json_encode($datesArr)?> ,
                datasets: [
                    {
                        //SET COLORS BELOW
                        fillColor: "rgba(76,194,88,0.5)",
                        strokeColor: "rgba(76,194,88,0.8)",
                        data:<?=json_encode($totalOrderValue)?>
                        // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
                    }
                ]

            }



            window.onload = function () {
                var ctx = document.getElementById("BarChart_on_load").getContext("2d");
                window.myLine = new Chart(ctx).Bar(barChartData, {
                    responsive: true,
                    showTooltips: false,
                    onAnimationComplete: function () {

                            var ctx = this.chart.ctx;
                            ctx.font = this.scale.font;
                            ctx.fillStyle = this.scale.textColor
                            ctx.textAlign = "center";
                            ctx.textBaseline = "bottom";

                            this.datasets.forEach(function (dataset) {
                                dataset.bars.forEach(function (bar) {
                                    ctx.fillText(bar.value, bar.x, bar.y - 7);
                                });
                            })
                        }
                });
                
            };
        });
       
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
                    dateLimit: {
                                    "month": 1
                                },

        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        });
    </script>
    <script>
        // var barChartData = {
            
        //     labels:<?=json_encode($datesArr)?> ,
        //     datasets: [
        //         {
        //             //SET COLORS BELOW
        //             fillColor: "rgba(76,194,88,0.5)",
        //             strokeColor: "rgba(76,194,88,0.8)",
        //             data:<?=json_encode($totalOrderValue)?>
        //             // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
        //         }
        //     ]

        // }



        // window.onload = function () {
        //     var ctx = document.getElementById("BarChart").getContext("2d");
        //     window.myLine = new Chart(ctx).Bar(barChartData, {
        //         responsive: true,
        //         showTooltips: false,
        //         onAnimationComplete: function () {

        //                                 var ctx = this.chart.ctx;
        //                                 ctx.font = this.scale.font;
        //                                 ctx.fillStyle = this.scale.textColor
        //                                 ctx.textAlign = "center";
        //                                 ctx.textBaseline = "bottom";

        //                                 this.datasets.forEach(function (dataset) {
        //                                     dataset.bars.forEach(function (bar) {
        //                                         ctx.fillText(bar.value, bar.x, bar.y - 7);
        //                             });
        //                         })
        //                   }
        //     });
            
        // };
    </script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


   <script>
        $('#to_date').datetimepicker
        ({
        format: 'YYYY-MM-DD'
        }).on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(0, 'days');
            $('#from_date').data('DateTimePicker').maxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });
        $("#year").datetimepicker  ( {

            format: 'YYYY-MM'
        });
    </script>
       <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function($) {
        $('.stateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                        $('.easy-pie-chart.percentage').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                                var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                                var size = parseInt($(this).data('size')) || 50;
                                $(this).easyPieChart({
                                    barColor: barColor,
                                    trackColor: trackColor,
                                    scaleColor: false,
                                    lineCap: 'butt',
                                    lineWidth: parseInt(size/10),
                                    animate: ace.vars['old_ie'] ? false : 1000,
                                    size: size
                                });
                            })
                        
                            $('.sparkline').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                                $(this).sparkline('html',
                                                 {
                                                    tagValuesAttribute:'data-values',
                                                    type: 'bar',
                                                    barColor: barColor ,
                                                    chartRangeMin:$(this).data('min') || 0
                                                 });
                            });
                        
                        
                          //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
                          //but sometimes it brings up errors with normal resize event handlers
                          $.resize.throttleWindow = false;
                        
                          var placeholder = $('#piechart-placeholder').css({'width':'50%' , 'height':'300px'});
                          var data = data.beat_query;
                          function drawPieChart(placeholder, data, position) {
                              $.plot(placeholder, data, {
                                series: {
                                    pie: {
                                        show: true,
                                        tilt:0.8,
                                        highlight: {
                                            opacity: 0
                                        },
                                        stroke: {
                                            color: '#fff',
                                            width: 2
                                        },
                                        startAngle: 2
                                    }
                                },
                                legend: {
                                    show: true,
                                    position: position || "ne",
                                    labelBoxBorderColor: null,
                                    margin:[-200,3]
                                }
                                ,
                                grid: {
                                    hoverable: true,
                                    clickable: true
                                }
                             })
                         }
                         drawPieChart(placeholder, data);
                        
                         /**
                         we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
                         so that's not needed actually.
                         */
                         placeholder.data('chart', data);
                         placeholder.data('draw', drawPieChart);
                        
                        
                          //pie chart tooltip example
                          var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
                          var previousPoint = null;
                        
                        placeholder.on('plothover', function (event, pos, item) {
                            if(item) {
                                if (previousPoint != item.seriesIndex) {
                                    previousPoint = item.seriesIndex;
                                    var tip = item.series['label'] + " : " + Math.round(item.series['percent'])+'%';
                                    $tooltip.show().children(0).text(tip);
                                }
                                $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
                            } else {
                                $tooltip.hide();
                                previousPoint = null;
                            }
                            
                        });
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script type="text/javascript">
        jQuery(function($) {
        $('.primaryStateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_primary_booking_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                        $('.easy-pie-chart.percentage').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                                var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                                var size = parseInt($(this).data('size')) || 50;
                                $(this).easyPieChart({
                                    barColor: barColor,
                                    trackColor: trackColor,
                                    scaleColor: false,
                                    lineCap: 'butt',
                                    lineWidth: parseInt(size/10),
                                    animate: ace.vars['old_ie'] ? false : 1000,
                                    size: size
                                });
                            })
                        
                            $('.sparkline').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                                $(this).sparkline('html',
                                                 {
                                                    tagValuesAttribute:'data-values',
                                                    type: 'bar',
                                                    barColor: barColor ,
                                                    chartRangeMin:$(this).data('min') || 0
                                                 });
                            });
                        
                        
                          //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
                          //but sometimes it brings up errors with normal resize event handlers
                          $.resize.throttleWindow = false;
                        
                          var placeholder = $('#piechart-placeholder-primary').css({'width':'50%' , 'height':'300px'});
                          var data = data.beat_query;
                          function drawPieChart(placeholder, data, position) {
                              $.plot(placeholder, data, {
                                series: {
                                    pie: {
                                        show: true,
                                        tilt:0.8,
                                        highlight: {
                                            opacity: 0
                                        },
                                        stroke: {
                                            color: '#fff',
                                            width: 2
                                        },
                                        startAngle: 2
                                    }
                                },
                                legend: {
                                    show: true,
                                    position: position || "ne",
                                    labelBoxBorderColor: null,
                                    margin:[-200,3]
                                }
                                ,
                                grid: {
                                    hoverable: true,
                                    clickable: true
                                }
                             })
                         }
                         drawPieChart(placeholder, data);
                        
                         /**
                         we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
                         so that's not needed actually.
                         */
                         placeholder.data('chart', data);
                         placeholder.data('draw', drawPieChart);
                        
                        
                          //pie chart tooltip example
                          var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
                          var previousPoint = null;
                        
                        placeholder.on('plothover', function (event, pos, item) {
                            if(item) {
                                if (previousPoint != item.seriesIndex) {
                                    previousPoint = item.seriesIndex;
                                    var tip = item.series['label'] + " : " + Math.round(item.series['percent'])+'%';
                                    $tooltip.show().children(0).text(tip);
                                }
                                $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
                            } else {
                                $tooltip.hide();
                                previousPoint = null;
                            }
                            
                        });
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script>

    $('.sluggish_retailer').click(function() {
          var state_id = $(this).attr('state_id_data'); 
          var from_date = $(this).attr('from_date'); 
          var flag = $(this).attr('flag'); 

        $('.mytbody').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/sluggish_retailer_list',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&flag=" + flag,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.retailer_result, function (key, value){
                                // console.log(value);
                                $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.dealer_name+"</td><td>"+value.l7_name+"</td><td>"+value.retailer_name+"</td><td>"+value.contact_per_name+"</td><td>"+(value.landline)+"</td></tr>");
                                Sno++;
                            });
                       

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.totalSalesTeam').click(function() {
          var state_id = $(this).attr('state_id'); 
          

        $('.mytbody_totalsalesteam').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getTotalSalesTeamHome',
                dataType: 'json',
                data: "state_id=" + state_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.user_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_totalsalesteam').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.role+"</td><td>"+value.state+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.totalBeatCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_totalbeat_coverage').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_beat_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.beat_coverage_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_totalbeat_coverage').append("<tr><td>"+Sno+"</td><td>"+value.beat+"</td><td>"+value.total_sale_value+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.totalProductiveCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_productive_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_productive_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.productve_call_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_productive_details').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.call_status_count+"</td><td>"+value.total_sale_value+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.user_details_on_role').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          var work_status = $(this).attr('work_status'); 
          var role_id = $(this).attr('role_id'); 
          

        $('.tbody_user_on_role').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/user_details_on_roles',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+"&work_status=" + work_status+ "&role_id=" + role_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.attendance_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_user_on_role').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.rolename+"</td><td>"+value.mobile+"</td><td>"+value.work_date+"</td></tr>");
                                Sno++;
                            });
                   
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

       $('.dealerDetailsHomeCommon').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 

         
          

        $('.tbody_distributor_details_home_common').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_distributor_details_home_common',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               // var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){


                                var url = "<a title='Distributor List' from_date='"+from_date+"' to_date='"+to_date+"' state_id='"+state_id+"' data-toggle='modal' data-target='#distributor-modal' class='distributor-modal dealerDetailsHome"+value.dealer_status+"'>";

                                 $('.tbody_distributor_details_home_common').append("<tr><td>"+Sno+"</td><td>"+value.status+"</td><td>"+url+value.count+"</a></td></tr>");
                                Sno++;

                            ////////////////////////////////////  for another modal ///////////////////////////////

                            $('.dealerDetailsHome'+value.dealer_status).click(function() {
                                // alert('1');
                                  var state_id = $(this).attr('state_id'); 
                                  var from_date = $(this).attr('from_date'); 
                                  var to_date = $(this).attr('to_date'); 
                                  var status = value.dealer_status;
                                  

                                $('.tbody_dealer_details_home').html('');
                             
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });
                                    $.ajax({
                                        type: "get",
                                        url: domain + '/get_dealer_details_home',
                                        dataType: 'json',
                                        data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&status=" + status,
                                        success: function (data) {

                                            if (data.code == 401) {

                                            }
                                            else if (data.code == 200) {

                                                       var Sno = 1;
                                  
                                                    $.each(data.dealer_details, function (key, value){
                                                        // console.log(value);
                                                        // user_n = `Crypt::encryptString(${value.user_id})` ;
                                                        $('.tbody_dealer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
                                                        Sno++;
                                                    });
                                           

                                                   
                                               
                                             }      

                                        },
                                        complete: function () {
                                            // $('#loading-image').hide();
                                        },
                                        error: function () {
                                        }
                                    });
                            });

                            ////////////////////////////////////for another modal ends ///////////////////////////





                            });

                         
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });



    // $('.dealerDetailsHome').click(function() {
    //       var state_id = $(this).attr('state_id'); 
    //       var from_date = $(this).attr('from_date'); 
    //       var to_date = $(this).attr('to_date'); 
          

    //     $('.tbody_dealer_details_home').html('');
     
    //         $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });
    //         $.ajax({
    //             type: "get",
    //             url: domain + '/get_dealer_details_home',
    //             dataType: 'json',
    //             data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
    //             success: function (data) {

    //                 if (data.code == 401) {

    //                 }
    //                 else if (data.code == 200) {

    //                            var Sno = 1;
          
    //                         $.each(data.dealer_details, function (key, value){
    //                             // console.log(value);
    //                             // user_n = `Crypt::encryptString(${value.user_id})` ;
    //                             $('.tbody_dealer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
    //                             Sno++;
    //                         });
                   

                           
                       
    //                  }      

    //             },
    //             complete: function () {
    //                 // $('#loading-image').hide();
    //             },
    //             error: function () {
    //             }
    //         });
    // });
    $('.dealerCoverageDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_dealer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_dealer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_dealer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.retailerDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    // for retailer of neha
    $('.retailerNehaDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          var state_string = "";
          

        $('.tbody_retailer_details_neha_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_details_neha_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               // var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){


                                if (typeof value.state_id !== 'undefined' && value.state_id.length > 0) {
                                  $.each(value.state_id, function (skey, svalue){
                                     state_string +=  "&location_3[]="+svalue+"";
                                });
                              }

                                // console.log(state_string);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                // $('.tbody_retailer_details_neha_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");


                               

                                var url = "<a href=retailer?status="+value.retailer_status+state_string+">";



                                 $('.tbody_retailer_details_neha_home').append("<tr><td>"+Sno+"</td><td>"+value.status+"</td><td>"+url+value.count+"</a></td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    // for retailer of neha 
    $('.retailerCovergaeDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.beatDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_beat_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_beat_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.beat_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.sale_value);
                                $('.tbody_beat_details').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td>"+value.dealer_count+"</td><td>"+value.retailer_count+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);
                            
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.totalCallDetails').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_total_calls_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_total_call_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.total_call_details, function (key, value){
                                // console.log(value);
                                // total_sale +=  parseInt(value.sale_value);
                                $('.tbody_total_calls_details').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.retailer_count+"</td><td>"+value.productive_count+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);
                            
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });


 

     

    </script>



 
    <!-- ############### PIE Chart Script Ends Here ################### -->
@endsection