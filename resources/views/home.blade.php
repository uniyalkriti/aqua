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
                        <form class="form-horizontal open collapse in" action="home" method="GET" id="homedata1" role="form"
                                enctype="multipart/form-data">
                                {!! csrf_field() !!}
                        
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                                       
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar bigger-110"></i>
                                                </span>

                                                <input class="form-control input-sm" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly style="background-color: white !important;"/>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4 ">
                                            <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.location3')}}</label>
                                            <select multiple name="location3[]" id="location3" class="form-control chosen-select " >
                                                <option value="">Select</option>
                                                @if(!empty($location3))
                                                    @foreach($location3 as $sk=>$sr) 
                                                    <?php if(empty($_GET['location3']))
                                                     $_GET['location3']=array();
                                                     ?>
                                                        <option @if(in_array($sk,$_GET['location3'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                                        </option>
                                                    @endforeach 
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="control-label no-padding-right" for="id-date-range-picker-1" style="visibility: hidden;">..</label>
                                            <button type="submit" class="form-control btn btn-sm btn-inverse  btn-block mg-b-10 input-sm"
                                                    ><i class="fa fa-search mg-r-10"></i>
                                                {{Lang::get('common.find')}}
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6" style=" display: flex; justify-content: right; margin-top: 28px; ">
                                    <span  style="display: flex;align-items: center; justify-content: center; border:1px #438eb9 solid; border-radius: 20px; font-size:14px; padding:7px; background-color:white; "><i class="fa fa-calendar "></i>&nbsp;&nbsp;<b>{{ date('d-M-y',strtotime($from_date)) .' To '. date('d-M-y',strtotime($to_date)) }}</b></span>
                                </div> 
                            </div>
                         </form>
                        

                        @if($company_id == 49 || $company_id == 56)
                        <div class="row">
                            <div class="space-12"></div>
                                {{-- <div class="col-sm-1"></div> --}}

                                <div class="col-sm-12 infobox-container">

                                <div class="infobox infobox-orange infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <a title="Sales Teams" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#user_details_modal" class="user-modal totalSalesTeam">
                                            <div class="infobox-content">{{Lang::get('common.total')}} Sales Team</div>
                                            </a>
                                            <div class="infobox-data-number">{{$totalSalesTeam}}</div>

                                        </div>
                                </div>

                                <div class="infobox infobox-blue infobox-large infobox-dark">
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

                                <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-map-marker"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{$location_5}}</div>
                                            <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.location7')}}</div>
                                            
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

                                <div class="infobox infobox-purple infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-phone"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{$totalMeeting->meetings}}</div>
                                        <div class="infobox-content">Total Meetings</div>
                                        
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
                               
                                <div class="infobox infobox-orange infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(to left, #50c98b 0%, #1db59c 100%) !important;">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            
                                            <div class="infobox-content">{{Lang::get('common.total')}} Active Users({{date('M',strtotime($from_date))}})</div>
                                            <!-- </a> -->
                                            
                                            <div class="infobox-data-number paddding10"  >{{$totalSalesTeam}}
                                                <span class="float-right my-auto ml-auto border-btn shadow-sm">
                                                    <a href="" title="Sales Teams" state_id="{{ $location_3_filter }}" mdate="{{ $mdate }}" from_date="{{ $from_date }}" to_date="{{ $to_date }}" data-toggle="modal" data-target="#user_details_modal" class="user-modal totalSalesTeam text-white">
                                                        <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                        <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                          
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                </div>
                          

                                <div class="infobox infobox-blue infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(45deg, #f93a5a, #f7778c) !important; ">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.distributor')}} </div> 
                                        
                                        <div class="infobox-data-number paddding10">{{$totalDistributor}} &nbsp;&nbsp;
                                            <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-modal-common" class="distributor-modal-common dealerDetailsHomeCommon  text-white">
                                                     <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                            <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                </a>
                                            </span>
                                        </div>
                                       
                                        
                                    </div>
                                </div>

    
                            


                                <div class="infobox infobox-red infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(to left, #48d6a8 0%, #029666 100%) !important; ">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</div>
                                        <div class="infobox-data-number paddding10">{{$totalOutlet}}&nbsp;&nbsp;
                                            <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                <a ttitle="Outlet List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#retailer-modal-neha" class="retailer-modal-neha retailerNehaDetailsHome  text-white">
                                                     <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                            <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                </a>
                                            </span>
                                        </div>
                                      
                                        
                                    </div>
                                </div>

                    


                                <div class="infobox infobox-grey infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(to left, #efa65f, #f76a2d) !important; ">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-map-marker"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-content">{{Lang::get('common.total')}} {{Lang::get('common.location7')}}</div>
                                            <div class="infobox-data-number paddding10">{{$location_5}} &nbsp;&nbsp;
                                                <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                    <a title="Beat List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#beat-modal" class="beat-modal beatDetailsHome text-white">
                                                         <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                </div>
                               
                                <div class="infobox infobox-purple infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(to right, #673ab7 0%, #884af1 100%) !important; ">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-phone"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-content">{{Lang::get('common.total_call')}}</div>

                                        <div class="infobox-data-number paddding10">{{$totalCall->total_call}} &nbsp;&nbsp;
                                             <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                <a title="List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#totalcall-modal" class="totalcall-modal totalCallDetails text-white">
                                                     <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                            <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                </a>
                                            </span>
                                        </div>
                                      
                                    </div>
                                </div>

                                    <div class="infobox infobox-pink infobox-large infobox-dark border-class margin-box" style="background-image: linear-gradient(to right, #f10075 0%, #f36eae 100%) !important; ">
                                           
        
                                            <div class="infobox-data" >
                                                <div class="infobox-content">{{Lang::get('common.secondary_sale')}}</div>

                                                
                                                <div class="infobox-data-number paddding10" id="after_click">{{!empty($totalOrderValue)?array_sum($totalOrderValue):'0'}}&nbsp;&nbsp;&nbsp;
                                                    <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                        <a title="State wise sale" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#myModal" class="user-modal stateWiseDetails text-white">
                                                             <span class="iconify" data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>
                                                    <div class="infobox-data-number" id="on_load"></div>

                                                </div>
                                                
                                            </div>
                                        </div>

                                

                                <div class="infobox infobox-orange infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #edc140 0%, #f2d273 100%) !important; ">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-mobile"></i>
                                    </div>

                                    <div class="infobox-data ">
                                        <div class="infobox-content text-white">Today's {{Lang::get('common.attendance')}}</div>

                                        <div class="infobox-data-number paddding10">{{$totalAttd}}&nbsp;&nbsp;

                                            <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                <a title=" Today's attendance log" mdate="{{ $mdate }}" data-toggle="modal" data-target="#attendance_modal" class="user-modal text-white">
                                                     <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                            <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                

                                    <div class="infobox infobox-blue infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #8cc2e6 0%, #abd3ed 100%) !important; ">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-content text-white">{{Lang::get('common.distributor')}} Coverage</div>
                                            <div class="infobox-data-number paddding10">{{$totalDistributorSale->dealersale}}&nbsp;&nbsp;
                                                <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                    <a title="Distributor List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#distributor-coverage-modal" class="distributor-coverage-modal dealerCoverageDetailsHome text-white">
                                                         <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="infobox infobox-red infobox-large infobox-light text-white border-class margin-box" style="background-image: linear-gradient(to right, #de6566 0%, #e48181 100%) !important;">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-mobile"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div class="infobox-content text-white">{{Lang::get('common.retailer')}} Coverage</div>
                                                <div class="infobox-data-number paddding10">{{$totalOutletSale->outletsale}}&nbsp;&nbsp;
                                                   <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                        <a title="Outlet List" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#retailer-coverage-modal" class="retailer-coverage-modal retailerCovergaeDetailsHome text-white">
                                                             <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="infobox infobox-grey infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #737373 0%, #bfbfbf 100%) !important;">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-mobile"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div class="infobox-content text-white">{{Lang::get('common.location7')}} Coverage</div>

                                                <div class="infobox-data-number paddding10">{{$totalBeatSale->beatsale}}
                                                     <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                        <a title="Beat Coverage Modal" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#beat_coverage_modal" class="user-modal totalBeatCoverage text-white">
                                                             <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>

                                                </div>
                                                
                                            </div>
                                        </div> 
                                        <div class="infobox infobox-purple infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #8c63d0 0%, #b79de2 100%) !important;">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-phone"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div class="infobox-content text-white">{{Lang::get('common.productive_call')}}</div>
                                                <div class="infobox-data-number paddding10">{{$productiveCall->productive_call}}&nbsp;&nbsp;
                                                    <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                        <a title="Productive Call Modal" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#product_call_modal" class="user-modal totalProductiveCoverage text-white">
                                                             <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="infobox infobox-pink infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #d68cdf 0%, #e2afe9 100%) !important;">
                                            <!-- <div class="infobox-icon">
                                                <i class="ace-icon fa fa-shopping-basket"></i>
                                            </div> -->
        
                                            <div class="infobox-data">
                                                <div class="infobox-content text-white">{{Lang::get('common.primary_sale')}}</div>
                                                <div class="infobox-data-number paddding10">{{!empty($totalPrimaryOrder->total_sale_value)?$totalPrimaryOrder->total_sale_value:'0'}}&nbsp;&nbsp;
                                                    <span class="float-right my-auto ml-auto border-btn shadow-sm" style="align-content: right;">
                                                        <a title="State wise sale" from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" data-toggle="modal" data-target="#primaryModal" class="user-modal primaryStateWiseDetails text-white">
                                                             <span class="iconify " data-icon="typcn:arrow-back-outline"></span>&nbsp;More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>

                                                </div>
                                               
                                                
                                            </div>
                                        </div>
                                        {{-- <div class="infobox infobox-purple infobox-large infobox-light border-class text-white margin-box" style="background-image: linear-gradient(to right, #8c63d0 0%, #b79de2 100%) !important;">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-phone"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div class="infobox-content text-white">Inactivity {{Lang::get('common.retailer')}}</div>
                                                <div class="infobox-data-number paddding10">
                                                    <span class="float-right my-auto ml-auto border-btn shadow-sm text-white" style="align-content: right;">
                                                        <a title="Outlet" mdate="{{ $mdate }}" data-toggle="modal" data-target="#inactiver_retailer text-white" class="text-white">
                                                             <span class="iconify " data-icon="typcn:arrow-back-outline"></span>More 
                                                                    <i class="typcn typcn-arrow-back-outline text-white"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> --}}
                                              
                                               
                               
                                

                            </div>
                      
                            <div class="vspace-12-sm"></div>
                           
                            <!-- /.col -->
                        </div><!-- /.row -->
                        @endif

                        <div class="hr hr32 hr-dotted"></div>
                        <div class="row">
                                <div class="col-sm-7">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Sale Stats
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                   
                                           
    
                                            
                                        </div>
    
                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="BarChart_on_load"  class="img-responsive" style="height: 380px; width: 100%;" ></div>
                                                    <!-- <canvas id="BarChart_on_load" height="100%" class="img-responsive" style="height: 370px; width: 100%;" ></canvas> -->
                                                    <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div><!-- /.col -->
                                <div class="col-sm-5">
                                    <div class="widget-box border-class shadow-box" style="padding:8px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-star"></i>
                                                Perfomance
                                            </h4>
                                            <div class="widget-toolbar ">
                                                <a href="#" data-action="collapse">
                                                    <i class="ace-icon fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                            
                                           
    
                                            
                                        </div>
    
                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                            <table class="table " style="height: 370px; width: 100%;">
                                                @if(!empty($user_perfomance_data))
                                                    @foreach($user_perfomance_data as $p_key => $p_value)
                                                        <tr style="border-top: none;" >
                                                            <td><img id="user_image" style="height: 35px;" class="editable img-responsive" src="{{asset('users-profile/'.$p_value->person_image)}}" onerror="this.onerror=null;this.src='http://demo.msell.in/public/msell/images/avatars/profile-pic.jpg';"></td>
                                                            <td style="text-align: left;">{{!empty($p_value->user_name)?$p_value->user_name:'-'}}<br>{{!empty($p_value->rolename)?$p_value->rolename:'-'}}</td>
                                                            <td style="text-align: left;">{{!empty($p_value->mobile)?$p_value->mobile:'-'}}</td>
                                                            <td style="text-align: right;">{{!empty($p_value->sale_value)?round($p_value->sale_value,2):'-'}}</td>
                                                            
                                                            <td style="text-align:right;">
                                                                @if($p_key == 0)
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                @endif
                                                                @if($p_key == 1)
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                @endif
                                                                @if($p_key == 2)
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                @endif
                                                                @if($p_key == 3)
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                @endif
                                                                @if($p_key == 4)
                                                                <i class="ace-icon fa fa-star star"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="5" style="text-align:right;"><a href="{{url('user-sale')}}" target="_">View More&nbsp;<i class="ace-icon fa fa-arrow-right "></i></a></td>
                                                </tr>
                                                
                                            </table>
                                        </div>
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <div class="hr hr32 hr-dotted"></div>

                            <div class="row">

                            {{--<div class="col-sm-12">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Beat Wise Call Analysis
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="beatWiseAnalysis" class="border-class" style="height: 370px; width: 100%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}


                                {{--<div class="col-sm-12">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Beat Wise Call Analysis
                                            </h4>
                                            <div class="widget-toolbar ">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-up"></i>
                                                    </a>
                                                </div>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-6">
                                                    <div id="beatWiseAnalysisGraph" class="border-class" style="height: 370px; width: 100%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                

                            </div>



                            <div class="hr hr32 hr-dotted"></div>



                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Monthly Progress
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
                                <div class="col-sm-5">
                                    <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                        <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                            <h4 class="widget-title lighter">
                                                <i class="ace-icon fa fa-signal"></i>
                                                Category Sale
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
                                
                                
                            </div>

                            <div class="hr hr32 hr-dotted"></div>

                            <div class="row">

                            <div class="col-sm-6">
                                <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                                    <div class="widget-header widget-header-flat" style="background-color:white; border-bottom: none;">
                                        <h4 class="widget-title lighter">
                                            <i class="ace-icon fa fa-signal"></i>
                                            Primary Sales Stats
                                        </h4>
                                        <div class="widget-toolbar ">
                                                <a href="#" data-action="collapse">
                                                    <i class="ace-icon fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-6">
                                                <div from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" id="showGraphPrimary" class="border-class" style="height: 370px; width: 100%;"></div>
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
                                            Primary Sales Monthly Progress
                                        </h4>
                                        <div class="widget-toolbar ">
                                                <a href="#" data-action="collapse">
                                                    <i class="ace-icon fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-6">
                                                <div from_date="{{ $from_date }}" to_date="{{ $to_date }}" mdate="{{ $mdate }}" state_id="{{ $location_3_filter }}" id="showGraphPrimaryProgress" class="border-class" style="height: 370px; width: 100%;"></div>
                                                <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                        </div><!-- /.widget-main -->
                                    </div><!-- /.widget-body -->
                                </div><!-- /.widget-box -->
                            </div>
                            
                        </div>

                           
                       
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
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <i class="ace-icon fa fa-signal"></i>
                            {{Lang::get('common.location3')}} Wise Sale
            </div>
            <div class="modal-body" >
               <div class="row">
                    <div class="col-sm-12">
                        <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                            

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                 <div id="piechart-placeholder" style="height: 400px; width: 100%;"></div>
                                        
                                        <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div><!-- /.widget-box -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ends here for sale -->
<!-- Modal  for secondary sale -->
<div class="modal fade" id="primaryModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <i class="ace-icon fa fa-signal"></i>
                            {{Lang::get('common.location3')}} Wise Sale
            </div>
            <div class="modal-body" >
               <div class="row">
                    <div class="col-sm-12">
                        <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                            

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                 <div id="piechart-placeholder-primary" style="height: 400px; width: 100%;"></div>
                                        
                                        <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div><!-- /.widget-box -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ends here for sale -->
<!-- Modal here for attendance detals -->
<div class="modal fade" id="attendance_modal" role="dialog">
    <div class="modal-dialog" style="width:800px" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >{{Lang::get('common.attendance')}} Details
                     <a onclick="fnExcelReportAtt()" href="javascript:void(0)" class="nav-link">
                            <i class="fa fa-file-excel-o"></i> Export Excel</a>
                </h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id="simple-table">
                    <thead>
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>Emp Code</th>
                        <th>{{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.user_contact')}}</th>
                        <th>Check In time</th>
                        <th>{{Lang::get('common.working')}} {{Lang::get('common.status')}}</th>
                        <th>Remarks</th>
                    </thead>
                    @foreach($attendance_details as $key => $value)
                    <?php 
                        $encid = Crypt::encryptString($value->user_id);
                    ?>
                        <tbody>
                            <td>{{$key+1}}</td>
                            <td>{{$value->emp_code}}</td>

                            <td><a href="{{url('user/'.$encid)}}" >{{$value->user_name}}</a></td>
                            <td>{{$value->mobile}}</td>
                            <td>{{$value->work_date}}</td>
                            <td>{{$value->work_status_name}}</td>
                            <td>{{$value->remarks}}</td>
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
    <div class="modal-dialog" style="width:800px" >
    
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
                        <th>{{Lang::get('common.status')}}</th>
                        <th>{{Lang::get('common.deactivate_date')}}</th>
                        <th>Attendance Count</th>
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
<div class="modal fade border-class" id="modal_pie_chart" role="dialog">
    <div class="modal-dialog " >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="title_pi_chart" ></h4>
            </div>
            <div class="modal-body" >
               <div class="row">
                    <div class="col-sm-12">
                        <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                            

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                 <div id="modalChartContainer" style="height: 400px; width: 100%;"></div>
                                        
                                        <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div><!-- /.widget-box -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade border-class" id="BarChartContainerModal" role="dialog">
    <div class="modal-dialog " >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="title_bar_chart" ></h4>
            </div>
            <div class="modal-body" >
               <div class="row">
                    <div class="col-sm-12">
                        <div class="widget-box border-class shadow-box" style="padding:12px 0; background-color:white;">
                            

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                 <div id="BarChartContainer" ></div>
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;">Sr.no</th>
                                                    <th style="text-align:left;">User Name</th>
                                                    <th style="text-align:left;">Rolename</th>
                                                    <th style="text-align:left;">Mobile</th>
                                                    <th style="text-align:left;">Sale Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="mytbodyBar">
                                                
                                            </tbody>
                                        </table>
                                        <!-- <canvas id="BarChart_after_load"  class="img-responsive" ></canvas> -->
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div><!-- /.widget-box -->
                    </div>
                </div>
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
    <script src="{{asset('/nice/js/BarChart.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script> -->

    @include('DashboardScript.dashboard_script')
    
    <script type="text/javascript">
    function fnExcelReportAtt() {
    var filename = "Attendance Report"
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('simple-table'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    var a = document.createElement('a');
    var data_type = 'data:application/vnd.ms-excel';
    a.href = data_type + ', ' + encodeURIComponent(tab_text);
    a.download = filename + '.xls';
    a.click();
}

</script>


 <script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
    <!-- ############### PIE Chart Script Ends Here ################### -->
@endsection