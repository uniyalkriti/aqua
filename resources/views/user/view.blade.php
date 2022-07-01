@extends('layouts.common_dashboard')

@section('title')
    <title>{{Lang::get('common.user_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <style type="text/css">
          .blink {
          animation: blink 2s steps(10, start) infinite;
          -webkit-animation: blink 5s steps(5, start) infinite;
        }
        @keyframes blink {
          to {
            visibility: hidden;
          }
        }
        @-webkit-keyframes blink {
          to {
            visibility: hidden;
          }
        }
    </style>
@endsection

@section('body')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('user')}}">{{Lang::get('common.user_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.user')}} {{Lang::get('common.dashboard')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="page-content">
                <form action="{{$id}}" method="get"> 
                    <div class="row">
                        <!-- <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="name">{{Lang::get('common.date')}}</label>
                                <input value="{{$date}}" type="text" placeholder="Select Date" name="date" id="date"  class="form-control date-picker input-sm" >
                            </div>
                        </div> -->
                          <div class="col-xs-6 col-sm-6 col-lg-3">
                                            
                                <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                           
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>

                                    <input class="form-control input-sm" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                </div>     
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <button type="submit" class="form-control btn btn-sm btn-primary input-sm btn-block mg-b-10"
                                        style="margin-top: 27px;"><i class="fa fa-search mg-r-10"></i>
                                    {{Lang::get('common.find')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3 pull-right">
                            <div class="">
                                <a title="Junior Details"  data-toggle="modal" data-target="#junior_listing" class="user-modal1">
                                
                                    <button type="button" class=" btn btn-sm btn-grey btn-block mg-b-10"
                                            style="margin-top: 28px;"><i class="fa fa-plus mg-r-10"></i>
                                        <b class="blink">Assign Junior {{Lang::get('common.distributor')}} & {{Lang::get('common.location7')}}</b>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                         
                        <div class="hr dotted"></div>

                        <div>
                    <div id="user-profile-1" class="user-profile row">
                                <div class="col-xs-12 col-sm-3 center">
                                    <div>
										<span class="profile-picture">
                                        

                                            <img id="user_image" style="height: 80px;" class="editable img-responsive"  src="http://demo.msell.in/public/users-profile/{{$user->person_image}}" onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}';" />
										</span>

                                        <div class="space-4"></div>

                                        <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                            <div class="inline position-relative">
                                                    <i class="ace-icon fa fa-circle light-green"></i>
                                                    &nbsp;
                                                    <span class="white">{{$user->first_name.' '.$user->middle_name.' '.$user->last_name}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-6"></div>
                                </div>

                                <div class="col-xs-12 col-sm-9">

                                <div class="col-md-6">
                                    <div class="profile-user-info profile-user-info-striped">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.username')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="username">{{!empty($user->person_username)?$user->person_username:''}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.role_key')}} </div>

                                            <div class="profile-info-value">
                                                <i class="fa fa-star light-orange bigger-110"></i>
                                                <span class="editable" id="city">{{!empty($user->rolename)?$user->rolename:''}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.email')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="age">{{!empty($user->email)?$user->email:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.user_contact')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="signup">{{!empty($user->mobile)?$user->mobile:''}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="profile-user-info profile-user-info-striped">

                                      <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.emp_code')}}</div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="signup">{{!empty($user->emp_code)?$user->emp_code:''}}</span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.senior_name')}}</div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="username">{{!empty($senior_person->senior_name)?$senior_person->senior_name:'NA'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.location3')}} </div>

                                            <div class="profile-info-value">
                                                <i class="fa fa-star light-orange bigger-110"></i>
                                                <span class="editable" id="city">{{!empty($state->name)?$state->name:''}}</span>
                                            </div>
                                        </div>
                                       <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.location5')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="about">{{!empty($user->head_quar)?$user->head_quar:'N/A'}}</span>
                                            </div>
                                        </div> 

                                      


                                  
                                    </div>
                                    </div>

                                    <div class="hr hr2 hr-double"></div>

                                </div>
                            </div>

                        <div class="row center">
                                    <div class="infobox infobox-green infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="percent">{{$attd_per}}</span>%
                                            <div class="infobox-content">{{Lang::get('common.attendance')}}</div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-blue infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$pc->pc}}</span>
                                            <div class="infobox-content">
                                            <a title="Productive Calls" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#userProductiveCallModal" class="user-modal userProductiveCallModal">
                                            <font color="black">
                                            {{Lang::get('common.productive_call')}}
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-pink infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$npc->npc}}</span>
                                            <div class="infobox-content">
                                            <a title="Non Productive Calls" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#userNonProductiveCallModal" class="user-modal userNonProductiveCallModal">
                                            <font color="black">
                                            Non {{Lang::get('common.productive_call')}}
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-black infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-rupee"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{ROUND($tv->tv,2)}}</span>
                                            <div class="infobox-content">
                                            <a title="Secondary Sales Details" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#userSecondarySaleModal" class="user-modal userSecondarySaleModal">
                                            <font color="white">
                                            Total Sale
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-orange infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$nr->nr}}</span>
                                            <div class="infobox-content">
                                            <a title="New Retailer Details" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#newRetailerDetailsModal" class="user-modal newRetailerDetailsModal">
                                            <font color="black">
                                            New {{Lang::get('common.retailer')}} Created
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="infobox infobox-purple infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$outlet_coverage->toc}}</span>
                                            <div class="infobox-content">
                                            <a title="Outlet Coverage Details" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#outletCoverageModal" class="user-modal outletCoverageModal">
                                            <font color="black">
                                            {{Lang::get('common.retailer')}} Coverage
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$unique_sku_billed->unique_sku}}</span>
                                            <div class="infobox-content">
                                            <a title="SKU Details" from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#skuSaleModal" class="user-modal skuSaleModal">
                                            <font color="black">
                                            No. Of SKU Billed
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-content">{{!empty($user->last_mobile_access_on)?$user->last_mobile_access_on:'N/A'}}</span>
                                            <div class="infobox-content"> Last Login SFA</div>
                                        </div>
                                    </div> --}}

                                    <div class="infobox infobox-blue infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-list-alt"></i>
                                        </div>

                                        <div class="infobox-data">
                                        <span class="infobox-data-number">{{$assign_beat->assign_beat}}</span>
                                            <div class="infobox-content">
                                            <a title="Total Beat Details"from_date="{{ $from_date }}" to_date="{{ $to_date }}" user_id="{{ $dashboard_user_id }}" data-toggle="modal" data-target="#total_beat_modal" class="user-modal total_beat_modal">
                                            <font color="black">
                                            Total assigned beats
                                            </font>
                                            </a>
                                            </div>
                                        </div>
                                    </div>

                                    </div>

                <div class="hr dotted"></div>
                        <div class="col-md-5">
                        </div>
                        <div class="row">

                                <div class="col-md-2">
                                    <span class="label label-success arrowed-in arrowed-in-right" style="width:100px"><font style="color:black;">{{ 'Total Days -'. $day_count }}</font></span>
                                </div>
                        </div>
                        <br>
                        <div class="row center">




                             <!--  <div class="infobox infobox-purple infobox-large infobox-dark"  style="height:50px; width:100px;" >
                                    <div class="infobox-data">
                                    <span class="infobox-data-number">{{$day_count}}</span>
                                        <div class="infobox-content">Total Days</div>
                                    </div>
                                </div> -->

                             @foreach($work_status as $wkey => $wvalue)
                                 <?php
                                  $color = $wvalue->color_status;
                                  $work_count = !empty($work_status_attendance[$wvalue->id])?$work_status_attendance[$wvalue->id]:0;
                                  $total_count[] = $work_count;
                                   ?>

                                <div class="col-md-1" style="text-align: center">
                                    <span class="label " style="width:10    0px; background-color:{{$color}};"><font style="color:black;">{{  $wvalue->name.'-'. $work_count }}</font></span>
                                </div>

                                    {{--    <div class="infobox infobox-large infobox-dark"  style="height:50px; width:120px; background-color:{{$color}}" >
                                            <div class="infobox-data">
                                            <span class="infobox-data-number"><font style="color:black;">{{!empty($work_status_attendance[$wvalue->id])?$work_status_attendance[$wvalue->id]:0}}</font></span>
                                                <div class="infobox-content"><font color="black">{{$wvalue->name}}</font></div>
                                            </div>
                                        </div>--}}
                                @endforeach 
                                <?php
                                $final_array_sum = array_sum($total_count);
                                $not_punched = $day_count-$final_array_sum;
                                ?>
                                <div class="col-md-1" style="text-align: center">
                                    <span class="label " style="width:100px; background-color:'#f1bcf5';"><font style="color:black;">{{  'Not Punched-'. $not_punched }}</font></span>
                                </div>
                        </div>
             <div class="hr dotted"></div>
                <div class="row">
                   <div class="col-xs-12 col-sm-12">
                               <div class="col-md-4">
                                    <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                    <div class="profile-info-name" >
                                           <strong>DEVICE </strong>
                                    </div>
                                    <div class="profile-info-name" style="text-align: left">
                                     <strong>INFO </strong>
                                    </div>
                                    </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Device</div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="username">{{!empty($device_info->device_manuf)?$device_info->device_manuf:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Model </div>

                                            <div class="profile-info-value">
                                                <i class="fa fa-star light-orange bigger-110"></i>
                                                <span class="editable" id="city">{{!empty($device_info->device_manuf)?$device_info->device_name:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Device Version </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="age">{{!empty($device_info->device_version)?$device_info->device_version:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Last Mobile Login </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="signup">{{!empty($user->last_mobile_access_on)?$user->last_mobile_access_on:'N/A'}}</span>
                                            </div>
                                        </div>
                                    </div>
                               </div>

                               <div class="col-md-4">
                                    <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                    <div class="profile-info-name" >
                                           <strong>Today's </strong>
                                    </div>
                                    <div class="profile-info-name" style="text-align: left">
                                     <strong>Visit </strong>
                                    </div>
                                    </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.distributor')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="username">{{!empty($today_visit->dealer_name)?$today_visit->dealer_name:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.location7')}} </div>

                                            <div class="profile-info-value">
                                                <i class="fa fa-star light-orange bigger-110"></i>
                                                <span class="editable" id="city">{{!empty($today_visit->beat)?$today_visit->beat:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.working')}} {{Lang::get('common.status')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="age">{{!empty($today_visit->working_status)?$today_visit->working_status:'N/A'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.secondary_sale')}} </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="signup">{{!empty($today_visit->rd)?$today_visit->rd:'N/A'}}</span>
                                            </div>
                                        </div>
                                    </div>
                               </div>

                        <div class="col-md-4">
                                    <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                    <div class="profile-info-name" >
                                           <strong>Today's </strong>
                                    </div>
                                    <div class="profile-info-name" style="text-align: left">
                                     <strong>Order Booking </strong>
                                    </div>
                                    </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> 
                                         
                                            {{Lang::get('common.total')}} {{Lang::get('common.productive_call')}}

                                            </div>

                                            <div class="profile-info-value">
                                             <i class="fa fa-star light-orange bigger-110"></i>
                                                <span class="editable" id="username">{{!empty($today_booking->pc)?$today_booking->pc:'0'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.total')}} Non {{Lang::get('common.productive_call')}} </div>

                                            <div class="profile-info-value">
                                               
                                                <span class="editable" id="city">{{!empty($today_booking->npc)?$today_booking->npc:'0'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.total')}} Sale Quantity </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="age">{{!empty($today_booking->total_sale_qty)?$today_booking->total_sale_qty:'0'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{Lang::get('common.total')}} Sale Value </div>

                                            <div class="profile-info-value">
                                                <span class="editable" id="signup">{{!empty($today_booking->total_sale_value)?$today_booking->total_sale_value:'0.00'}}</span>
                                            </div>
                                        </div>
                                    </div>
                               </div>

                            </div>                 
                        </div>
                          <div class="hr dotted"></div>
                            <div class="row">
                                <div class="panel-group">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <i class="fa fa-bookmark"> Target vs Achievment <?=date("F-Y")?></i>
                                        </div>
                                        <div id="chart_div"></div>
                       
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


<!-- Modal here for user detals -->
<div class="modal fade" id="userProductiveCallModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Productive Calls Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>No. Of Productive Calls</th>
                    </thead>
                    <tbody class="mytbody_userProductiveCallModal">
                        
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
<div class="modal fade" id="userNonProductiveCallModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Non Productive Calls Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>No. Of Non Productive Calls</th>
                    </thead>
                    <tbody class="mytbody_userNonProductiveCallModal">
                        
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
<div class="modal fade" id="userSecondarySaleModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Secondary Sales Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>Total Secondary Sales</th>
                    </thead>
                    <tbody class="mytbody_userSecondarySaleModal">
                        
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
<div class="modal fade" id="newRetailerDetailsModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >New Retailer Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Retailer Name</th>
                        <th>Retailer Mobile</th>
                        <th>Retailer Address</th>
                        <th>Beat Name</th>
                    </thead>
                    <tbody class="mytbody_newRetailerDetailsModal">
                        
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
<div class="modal fade" id="outletCoverageModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Outlet Coverage Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Retailer Name</th>
                        <th>Retailer Mobile</th>
                        <th>Retailer Address</th>
                        <th>No. Of Visit</th>
                    </thead>
                    <tbody class="mytbody_outletCoverageModal">
                        
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
<div class="modal fade" id="skuSaleModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >SKU Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>SKU Name</th>
                        <th>Total Qty</th>
                        <th>Total Sale Value</th>
                       
                    </thead>
                    <tbody class="mytbody_skuSaleModal">
                        
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



<!-- #################################################### modal box start shere -->
<div class="modal fade" id="junior_listing" role="dialog">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Junior Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead class = "">
                        <th>{{Lang::get('common.s_no')}}</th>
                        <th>{{Lang::get('common.username')}}</th>
                        <th>{{Lang::get('common.role_key')}}</th>
                        <th>{{Lang::get('common.distributor')}}</th>
                        <th>{{Lang::get('common.location7')}}</th>
                        <th>Action<br><input type="checkbox" onchange="checkAll(this)"></th>
                    </thead>
                    <form action="../junior_list_assign_to_senior" method="get" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="hidden" name="senior_user_id" value="{{$senior_user_id}}">
                        <tbody class="mytbody">
                            <?php $i=1; ?>

                            @if(!empty($user_details) && count($user_details)>0)
                                @foreach($user_details as $key=>$data)
                                <tr>
                                   
                                    <input type="hidden" name="user_id[]" value="{{$data->user_id}}">
                                    <td>{{$key+1}}</td>
                                    <td>{{$data->user_name}}</td>
                                    <td>{{$data->rolename}}</td>
                                    <td>{{!empty($dealer_count[$data->user_id])?$dealer_count[$data->user_id]:''}}</td>
                                    <td>{{!empty($beat_count[$data->user_id])?$beat_count[$data->user_id]:''}}</td>
                                    <td><input type="checkbox" ></td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <input type="submit" name="submit" value="submit">
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal for total beat -->
<div class="modal fade" id="total_beat_modal" role="dialog">
    <div class="modal-dialog" style="width:800px" >
    
        
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h4 class="modal-title" >Total Beat Sale</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>S.no</th>
                        <th>Beat Id</th>
                        <th>Beat Name</th>
                        <th>Town</th>
                        <th>District</th>
                        <th>Depot</th>
                        <th>State</th>
                        <th>Region</th>
                        <th>Zone</th>
                    </thead>
                    <tbody class="mytbody_total_beat_modal">
                        
                    </tbody>
                    
                </table>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
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

    <script type="text/javascript">
        $('.skuSaleModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_skuSaleModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/public/getSKUSalesModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;
                               var sum_qty = 0;

          
                            $.each(data.user_details, function (key, value){
                                
                                sum += parseFloat(value.sale_value);
                                sum_qty += parseInt(value.sale_quantity);

                                 $('.mytbody_skuSaleModal').append("<tr><td>"+Sno+"</td><td>"+value.product_name+"</td><td>"+value.sale_quantity+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });

                                 $('.mytbody_skuSaleModal').append("<tr><td colspan = '2'> Total </td><td>"+sum_qty+"</td><td>"+Math.round(sum)+"</td></tr>");
                                

                           
                       
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


     <script type="text/javascript">
        $('.userProductiveCallModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_userProductiveCallModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/public/getUserProductiveCallModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;
          
                            $.each(data.user_details, function (key, value){
                             
                                sum += parseInt(value.pc);
                                 $('.mytbody_userProductiveCallModal').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.pc+"</td></tr>");
                                Sno++;
                            });

                                 $('.mytbody_userProductiveCallModal').append("<tr><td colspan = '2'> Total </td><td>"+sum+"</td></tr>");

                   

                           
                       
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

    <script type="text/javascript">
        $('.userNonProductiveCallModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_userNonProductiveCallModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/public/getUserNonProductiveCallModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;
          
                            $.each(data.user_details, function (key, value){
                             
                                sum += parseInt(value.pc);
                                 $('.mytbody_userNonProductiveCallModal').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.pc+"</td></tr>");
                                Sno++;
                            });

                                 $('.mytbody_userNonProductiveCallModal').append("<tr><td colspan = '2'> Total </td><td>"+sum+"</td></tr>");

                   

                           
                       
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

     <script type="text/javascript">
        $('.userSecondarySaleModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_userSecondarySaleModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/public/getUserSecondarySaleModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;
          
                            $.each(data.user_details, function (key, value){
                             
                                sum += parseFloat(value.sale_value);
                                 $('.mytbody_userSecondarySaleModal').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                                 $('.mytbody_userSecondarySaleModal').append("<tr><td colspan = '2'> Total </td><td>"+Math.round(sum)+"</td></tr>");

                   

                           
                       
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

    <script type="text/javascript">
        $('.newRetailerDetailsModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_newRetailerDetailsModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/public/getNewRetailersDetailsModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;
          
                            $.each(data.user_details, function (key, value){
                             
                                sum += parseFloat(value.sale_value);
                                 $('.mytbody_newRetailerDetailsModal').append("<tr><td>"+Sno+"</td><td><a href=../retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.landline+"</td><td>"+value.track_address+"</td><td>"+value.beat_name+"</td></tr>");
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

    </script>


    <script type="text/javascript">
        $('.outletCoverageModal').click(function() {
          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_outletCoverageModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getOutletCoverageModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var sum = 0;

          
                            $.each(data.user_details, function (key, value){
                                
                                sum += parseInt(value.visit_number);

                                 $('.mytbody_outletCoverageModal').append("<tr><td>"+Sno+"</td><td><a href=../retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.landline+"</td><td><a href=https://www.google.com/maps/place/"+value.lat_long+">"+value.track_address+"</a></td><td>"+value.visit_number+"</td></tr>");
                                Sno++;
                            });

                                 $('.mytbody_outletCoverageModal').append("<tr><td colspan = '4'> Total </td><td>"+sum+"</td></tr>");
                                

                           
                       
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

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
      <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>  
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
 
    <script src="{{asset('js/user.js')}}"></script>
    {{--             BAR Chart    --}}
    <script src="{{asset('nice/js/targetAchivement.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>


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

        // Load the Visualization API and the chart package.
    google.load('visualization', '1', {'packages':['corechart']});
    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

        function drawChart() 
        {

        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(<?=$jsonTable?>);
            var options = {
            title: 'Target Vs Acheivement',
            width: 1350,
            height: 400
            };
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
        
        }

    $("#date").datetimepicker  ( {

    format: 'YYYY-MM-DD'
    });

    </script>

<script>
    $(document).ready(function(){
        $("#popup").click(function(){
            $("#myModal").modal('show');
        });
    });
</script>
<script type="text/javascript">
        $('.total_beat_modal').click(function() {


          var user_id = $(this).attr('user_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 

     

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain+'/TotalBeatModal',
                dataType: 'json',
                data: "user_id=" + user_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                              
          
                            $.each(data.assign_total_beat, function (key, value){
                                
                               

                                 $('.mytbody_total_beat_modal').append("<tr><td>"+Sno+"</td><td>"+value.beat_id+"</td><td>"+value.beat_name+"</td><td>"+value.town+"</td><td>"+value.district+"</td><td>"+value.depot+"</td><td>"+value.state+"</td><td>"+value.region+"</td><td>"+value.zone+"</td></tr>");
                                Sno++;
                            });

                                
                                

                           
                       
                     }      

                },
                complete: function () {
                    
                },
                error: function () {
                }
            });
    });

    </script>

  
@endsection
