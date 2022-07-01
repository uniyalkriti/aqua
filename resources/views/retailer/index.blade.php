@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.retailer_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>


    <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('body')

<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="#">{{Lang::get('common.retailer')}} {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active" style="color: white">{{Lang::get('common.retailer_detail')}}</li>
                </ul><!-- /.breadcrumb -->
            </div><!-- /.nav-search -->

            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <form class="form-horizontal open collapse in" action="" method="GET" id="user-search" role="form"
                            enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row">
                                 <div class="col-xs-12">
                                   
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-xs-12">
                                    <div class="col-xs-2 ">
                                        <label class="control-label no-padding-right" for="name">{{Lang::get('common.search_by_name')}}</label>
                                        <div class="input-group" style="cursor: pointer;">
                                            @if(empty(Request::get('search')))
                                             <input autocomplete="off" type="text" placeholder="Search by Name" id="search"
                                                       name="search" value="{{ Request::get('search') }}"
                                                     class="form-control input-sm"/>
                                               <span onclick="search()" class="input-group-addon cursor">
                                                   <i class="fa fa-search"></i>
                                             </span>
                                           @else
                                             <input type="text" readonly="readonly" placeholder="Search by name or email"
                                                     id="search" name="search" value="{{ Request::get('search') }}"
                                                       class="form-control input-sm"/>
                                              <span onclick="searchReset();" class="input-group-addon cursor">
                                                   <i class="fa fa-times"></i>
                                               </span>
                                            @endif
                                        </div>
                                    </div>
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
                                    
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
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
                                        <label class="control-label no-padding-right"
                                                   for="name">From </label>
                                            <input  autocomplete="off" type="text" placeholder="From Date" value="{{$from_date}}" name="from_date" id="from_date" class="form-control date-picker input-sm">
                                                   
                                        
                                    </div>
                                    <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                                   for="name">To</label>
                                            <input  autocomplete="off" type="text" placeholder="To Date" value="{{$to_date}}" name="to_date" id="to_date" class="form-control date-picker input-sm">
                                                   
                                        
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
                                        for="name">{{Lang::get('common.status')}}</label>
                                            <select  name="status" id="status" class="form-control ">
                                                <option {{Request::get('status')==1?'selected':''}} value=1>Active</option>
                                                <option {{Request::get('status')==2?'selected':''}} value=2>DE-Active</option>
                                            </select>
                                    </div>

                                    
                                  
                                  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">


                                    <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                        for="name">Is Golden</label>
                                            <select  name="is_golden" id="is_golden" class="form-control ">
                                                <option value="">Select</option>
                                                <option {{Request::get('is_golden')==2?'selected':''}} value=2>Not Golden</option>
                                                <option {{Request::get('is_golden')==1?'selected':''}} value=1>Golden</option>
                                            </select>
                                    </div>



                                     <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                        for="name">Is Golden Aproved</label>
                                            <select  name="is_golden_approved" id="is_golden_approved" class="form-control ">
                                                <option value="">Select</option>
                                                <option {{Request::get('is_golden_approved')==2?'selected':''}} value=2> Not Approved</option>
                                                <option {{Request::get('is_golden_approved')==1?'selected':''}} value=1>Approved</option>
                                            </select>
                                    </div>



                                    <div class="col-xs-2">
                                        <label class="control-label no-padding-right"
                                        for="name">{{Lang::get('common.per_page')}}</label>
                                         <select name="perpage" id="perpage" class="cursor form-control" onchange="form.submit()"   >
                                            <option value="">Per Page</option>
                                            <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                            <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                            <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                            <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                            <option {{ Request::get('perpage')==500?'selected':'' }} value="500">500</option>
                                            <option {{ Request::get('perpage')==1000?'selected':'' }} value="1000">1000</option>
                                            <option {{ Request::get('perpage')==2000?'selected':'' }} value="2000">2000</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-2">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        {{Lang::get('common.find')}}
                                        </button>
                                    </div>
                                    
                                    <div class="col-xs-2">
                                        <span id="checkTrial">
                                        <a href="{{url($current_menu.'/create')}}" class="form-control btn btn-sm btn-info pull-right" style="margin-top: 28px; margin-left: 5px;">
                                            <i class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.retailer_detail')}}
                                        </a>
                                        </span>
                                    </div>
                                    <div class="col-xs-2">
                                        <a href="{{url('/retailer_map')}}"  class="form-control btn btn-sm btn-primary pull-right" style="margin-top: 28px;">
                                            <i class="fa fa-map "></i> {{Lang::get('common.retailer')}} Map
                                        </a>
                                    </div>
                                    <div class="col-xs-2">
                                    </div>
                                </div>
                            </div>
                        </form><br>
                        
                        <div class="row">
                            <div class="col-xs-12" style="overflow-x: scroll;">
                                <div class="table-header center">
                                    {{Lang::get('common.retailer_detail')}}
                                    <div class="pull-right tableTools-container"></div>
                                   
                                </div>
                                <table id="dynamic-table" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            {{Lang::get('common.s_no')}}
                                        </th>
                                        <th>{{Lang::get('common.created_date')}}</th>

                                        <th>{{Lang::get('common.image')}}</th>
                                        <th>{{Lang::get('common.location3')}}</th>
                                        <th>{{Lang::get('common.location4')}}</th>
                                        <th>{{Lang::get('common.location5')}}</th>
                                        <th>{{Lang::get('common.location6')}}</th>
                                        <th>{{Lang::get('common.'.$current_menu)}} Code</th>
                                        <th>{{Lang::get('common.retailer_owner_name')}}</th>
                                        <th>{{Lang::get('common.retailer')}} Name</th>
                                        <th>{{Lang::get('common.user_contact')}}</th>

                                        <th>{{Lang::get('common.retailer')}} Category</th>
                                        <th>{{Lang::get('common.retailer_type')}}</th>
                                        <th>Added By</th>
                                        <th>{{Lang::get('common.distributor')}}</th>
                                        <th>{{Lang::get('common.location7')}}</th>
                                        <th>Geo {{Lang::get('common.address')}}</th>
                                        <th>{{Lang::get('common.pin_no')}}</th>
                                        <th>{{Lang::get('common.email')}}</th>
                                        <th>{{Lang::get('common.gst_no')}}</th>
                                        <th>{{Lang::get('common.status')}}</th>
                                        <th>{{Lang::get('common.action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="dynamic-table" >
                                        <?php 
                                            $user_name = App\CommonFilter::user_filter('test');

                                         ?>
                                        @foreach($records as $key=>$data)
                                        <?php
                                            $comprasion = !empty($sale_reaon_mark[$data->id])?$sale_reaon_mark[$data->id]:''; 

                                            $strToLowerComparison = strtolower($comprasion);

                                            $sim = similar_text($strToLowerComparison, 'closed', $perc);

                                            if($perc >= '50'){
                                                $change_color = '#ff4d4d';
                                            }else{
                                                 $change_color = '#ffffff';
                                            }

                                            // dd($change_color);
                                        
                                            // if($comprasion =='Shop close ' ||  $comprasion =='Shop Close ' || $comprasion =='Shop close' || $comprasion =='shop closed' || $comprasion ==' Shop close')
                                            // {
                                            //     $change_color = '#ff0000';
                                            // }
                                            // else
                                            // {
                                            //     $change_color = '#ffffff';
                                            // }

                                        ?>
                                            <?php 
                                            $encid = Crypt::encryptString($data->id);
                                            $dencid = Crypt::encryptString($data->dealer_id);
                                            $uencid = Crypt::encryptString($data->user_id);



                                            ?>
                                            <tr bgcolor='{{$change_color}}'>
                                                <td class="center">
                                                    {{ 1 + $key }}
                                                </td>
                                                <td>{{$data->created_on}}</td>

                                                <td>
                                                <a class=“cboxElement” href="{{asset('retailer_image/')}}{{'/'.$data->image_name}}" data-rel=“colorbox”>
                                                    <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo" 
                                                    src="{{asset('retailer_image/')}}{{'/'.$data->image_name}}" 
                                                    alt=" " />
                                                </a>
                                                </td>   
                                                <td>{{$data->l3_name}}</td>
                                                <td>{{$data->l4_name}}</td>
                                                <td>{{$data->l5_name}}</td>
                                                <td>{{$data->l6_name}}</td>
                                                <td>R - {{$data->retailer_code}}</td>
                                                <td>{{$data->contact_per_name}}</td>
                                                <td><a href="{{url('retailer/'.$encid)}}">{{$data->name}}</a></td>
                                                <td>{{!empty($data->other_numbers)?$data->other_numbers:$data->landline}}</td>
                                                
                                                <td>{{!empty($class_outlet_category[$data->class])?$class_outlet_category[$data->class]:''}}</td>
                                                <td>{{$data->outlet_type}}</td>
                                                <td><a href="{{url('user/'.$uencid)}}">{{!empty($user_name[$data->user_id])?$user_name[$data->user_id]:'-'}}</a></td>
                                                <td><a href="{{url('distributor/'.$dencid)}}">{{$data->dealer_name}}</a></td>
                                                <td>{{$data->beat_name}}</td>
                                                <td>@if($data->track_address!='$$'){{$data->track_address}}  @else NA @endif</td>
                                                <td>{{$data->pin_no}}</td>
                                                <td>{{$data->email}}</td>
                                                <td>{{$data->tin_no}}</td>
                                                <td class="hidden-480">
                                                    @if($data->retailer_status==1)
                                                        <span class="label label-sm label-success">Active</span>
                                                    @elseif($data->retailer_status==9)
                                                        <span class="label label-sm label-danger">Deleted</span>
                                                    @else
                                                        <span class="label label-sm label-warning">In-active</span>
                                                    @endif
                                                </td> 
                                                <td>

                                                    <div class="hidden-sm hidden-xs btn-group">
                                                        @if($data->retailer_status==1)
                                                            <button class="btn btn-xs btn-warning"
                                                                    onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','inactive');">
                                                                <i class="ace-icon fa fa-ban bigger-120"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-xs btn-success"
                                                                    onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','active');">
                                                                <i class="ace-icon fa fa-check bigger-120"></i>
                                                            </button>
                                                        @endif
                                                        <?php  $data->user_id;  ?>
                                                        <a class="btn btn-xs btn-info"
                                                           href="{{url($current_menu.'/'.$encid.'/edit')}}">
                                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                        </a>
                                                        <button class="btn btn-xs btn-danger"
                                                                onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','delete');">
                                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                        </button>


                                                        <button title=
                                                        "Is Golden" class="btn btn-xs btn-warning"
                                                                onclick="confirmActionIsGolden('{{$data->id}}');">
                                                            <i class="ace-icon fa fa-inr bigger-120"></i>
                                                        </button>


                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="col-xs-6">
                                    <div class="dataTables_info">
                                        Showing {{($records->currentpage()-1)*$records->perpage()+1}}
                                        to {{(($records->currentpage()-1)*$records->perpage())+$records->count()}}
                                        of {{$records->total()}} entries
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                    {{$records->appends(request()->except('page'))}}
                                    </div>
                                </div>
                            </div><!-- /.span -->
                        </div>
                    </div>
                </div><!-- /.row -->
                <div class="hr hr-18 dotted hr-double"></div>
                        <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>
@endsection

@section('js')  
   <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('msell/page/retailer.js')}}"></script>

    @include('common_filter.filter_script_sale')
    

  

    <script>
         $('#myRetailerModal').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
        $(".chosen-select").chosen();
            $('button').click(function () {
                $(".chosen-select").trigger("chosen:updated");
            });


        function confirmActionIsGolden(id) {
            $.confirm({
                title: "Alert!!",
                content: 'Are you sure want to make this outlet golden ?',
                buttons: {
                    confirm: function () {
                        takeActionIsGolden(id);
                        // $.alert({
                        //     title: 'Alert!!',
                        //     content: 'Done!',
                        //     buttons: {
                        //         ok: function () {
                        //             setTimeout("window.parent.location = ''", 50);
                        //         }
                        //     }
                        // });
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }


        function takeActionIsGolden(id) {
            if (id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeActionIsGolden',
                    dataType: 'json',
                    data: {'module': id, 'id': id},
                    success: function (data) {
                        // console.log(data);
                        if (data.code == 401) {
                            $.alert({
                            title: 'Alert!!',
                            content: data.message,
                            buttons: {
                                    ok: function () {
                                        // setTimeout("window.parent.location = ''", 50);
                                    }
                                }
                            });
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


        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
                        $.alert({
                            title: 'Alert!',
                            content: 'Done!',
                            buttons: {
                                ok: function () {
                                    setTimeout("window.parent.location = ''", 50);
                                }
                            }
                        });
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
                        // console.log(data);
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

        function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }

        function search() {
            if ($('#search').val() != '') {
                $('#user-search').submit();
            }
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#openBtn').click(function () {
                $('#myModal').modal({
                    show: true
                })
            });

            $(document).on('show.bs.modal', '.modal', function (event) {
                var zIndex = 1040 + (10 * $('.modal:visible').length);
                $(this).css('z-index', zIndex);
                setTimeout(function () {
                    $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
                }, 0);
            });


        });
    </script>
         <script src="{{asset('nice/js/toastr.min.js')}}"></script>
        @if(Session::has('message'))
            <script>
                toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
            </script>
        @endif  
        <script>
          $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#dynamic-table tr").filter(function() {
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
          });
        </script>

  
    <script type="text/javascript">
   


    $('#from_date').datetimepicker({
        format: 'YYYY-MM-DD'
    }).on('dp.change', function (e) {
        var incrementDay = moment(new Date(e.date));
        incrementDay.add(0, 'days');
        $('#to_date').data('DateTimePicker').minDate(incrementDay);
        $(this).data("DateTimePicker").hide();
    });

    $('#to_date').datetimepicker({
        format: 'YYYY-MM-DD'
    }).on('dp.change', function (e) {
        var decrementDay = moment(new Date(e.date));
        decrementDay.subtract(0, 'days');
        $('#from_date').data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });
    </script>

    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    @include('DashboardScript.commonModalScript')
    
    <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                null,
                                                  null,null,null,null,null,null, null, null, 
                                                  null,null,null,null,null,null, null,  null,null,null,null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            // "sScrollY": "300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            "bScrollCollapse": true,
                                            "iDisplayLength": 10000,


                                            select: {
                                                style: 'multi'
                                            }
                                        });



                                $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

                                new $.fn.dataTable.Buttons(myTable, {
                                    buttons: [
                                        {
                                            "extend": "colvis",
                                            "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            columns: ':not(:first):not(:last)'
                                        },
                                        {
                                            "extend": "copy",
                                            "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "csv",
                                            "text": "<i class='fa fa-database bigger-110 orange'></i> <span class=''>CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "excel",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "pdf",
                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "print",
                                            "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            autoPrint: false,
                                            message: 'This print was produced using the Print button for DataTables'
                                        }
                                    ]
                                });
                                myTable.buttons().container().appendTo($('.tableTools-container'));

                                //style the message box
                                var defaultCopyAction = myTable.button(1).action();
                                myTable.button(1).action(function (e, dt, button, config) {
                                    defaultCopyAction(e, dt, button, config);
                                    $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
                                });


                                var defaultColvisAction = myTable.button(0).action();
                                myTable.button(0).action(function (e, dt, button, config) {

                                    defaultColvisAction(e, dt, button, config);


                                    if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                                        $('.dt-button-collection')
                                                .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                                                .find('a').attr('href', '#').wrap("<li />")
                                    }
                                    $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
                                });

                                ////

                                setTimeout(function () {
                                    $($('.tableTools-container')).find('a.dt-button').each(function () {
                                        var div = $(this).find(' > div').first();
                                        if (div.length == 1)
                                            div.tooltip({container: 'body', title: div.parent().text()});
                                        else
                                            $(this).tooltip({container: 'body', title: $(this).text()});
                                    });
                                }, 500);





                         /*       myTable.on('select', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                                    }
                                });
                                myTable.on('deselect', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                                    }
                                });




                                /////////////////////////////////
                                //table checkboxes
                                $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

                                //select/deselect all rows according to table header checkbox
                                $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $('#dynamic-table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            myTable.row(row).select();
                                        else
                                            myTable.row(row).deselect();
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                                    var row = $(this).closest('tr').get(0);
                                    if (this.checked)
                                        myTable.row(row).deselect();
                                    else
                                        myTable.row(row).select();
                                });
*/


                                $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                                    e.stopImmediatePropagation();
                                    e.stopPropagation();
                                    e.preventDefault();
                                });



                                //And for the first simple table, which doesn't have TableTools or dataTables
                                //select/deselect all rows according to table header checkbox
                                var active_class = 'active';
                                $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $(this).closest('table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                                        else
                                            $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                                    var $row = $(this).closest('tr');
                                    if ($row.is('.detail-row '))
                                        return;
                                    if (this.checked)
                                        $row.addClass(active_class);
                                    else
                                        $row.removeClass(active_class);
                                });



                                /********************************/
                                //add tooltip for small view action buttons in dropdown menu
                                $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

                                //tooltip placement on right or left
                                function tooltip_placement(context, source) {
                                    var $source = $(source);
                                    var $parent = $source.closest('table')
                                    var off1 = $parent.offset();
                                    var w1 = $parent.width();

                                    var off2 = $source.offset();
                                    //var w2 = $source.width();

                                    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
                                        return 'right';
                                    return 'left';
                                }




                                /***************/
                                $('.show-details-btn').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).closest('tr').next().toggleClass('open');
                                    $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
                                });
                                

                            })
        </script>
   
@endsection