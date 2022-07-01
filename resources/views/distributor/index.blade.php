@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.dealer_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('body')
<div class="main-container ace-save-state" id="main-container" style="overflow-x: scroll;">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <ul class="breadcrumb">
                    <li style="color: white">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="#">{{Lang::get('common.dealer_module')}} {{Lang::get('common.master')}}</a>
                    </li>

                    <li class="active" style="color: white">{{Lang::get('common.dealer_detail')}}</li>
                </ul><!-- /.breadcrumb -->
            </div><!-- /.nav-search -->
            <div class="page-content">
                <form class="form-horizontal open collapse in" action="" method="GET" id="user-search" role="form"
                                          enctype="multipart/form-data">
                  {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location3')}}</label>
                                <select multiple name="state[]" id="state" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($state))
                                        @foreach($state as $sk=>$sr) 
                                        <?php if(empty($_GET['state']))
                                         $_GET['state']=array();
                                         ?>
                                            <option @if(in_array($sk,$_GET['state'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2">
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
                            <div class="col-xs-2">
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
                            <div class="col-xs-2 ">
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
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.user')}}</label>
                                <select multiple name="user[]" id="user" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($user))
                                        @foreach($user as $k=>$r)
                                         <?php if(empty($_GET['user']))
                                         $_GET['user']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['user'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.csa')}}</label>
                                <select multiple name="csa[]" id="csa" class="form-control chosen-select">
                                    <option value="">Select</option>
                                    @if(!empty($csa))
                                        @foreach($csa as $sk=>$sr) 
                                        <?php if(empty($_GET['csa']))
                                         $_GET['csa']=array();
                                         ?>
                                            <option @if(in_array($sk,$_GET['csa'])){{"selected"}} @endif value="{{$sk}}" > {{$sr}} 
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>

                            
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-xs-2 ">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.distributor')}}</label>
                                <select multiple name="distributor[]" id="distributor" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($dealer_name))
                                        @foreach($dealer_name as $k=>$r)
                                         <?php if(empty($_GET['distributor']))
                                         $_GET['distributor']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['distributor'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2 ">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.role_key')}}</label>
                                <select multiple name="role_id[]" id="role" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($role_array))
                                        @foreach($role_array as $k=>$r)
                                         <?php if(empty($_GET['role_id']))
                                         $_GET['role_id']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['role_id'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.status')}}</label>
                                <select name="status" id="status" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    <option {{ Request::get('status')==1?'selected':'' }} value='1'>Active</option>
                                    <option {{ Request::get('status')==2?'selected':'' }} value='2'>De-Active</option>
                                </select>
                            </div>
                             <div class="col-xs-2">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.per_page')}}</label>

                                <select name="perpage" id="perpage" class="form-control cursor" onchange="form.submit()">
                                    <option value="">{{Lang::get('common.per_page')}}</option>
                                    <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                    <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                    <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                    <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                    <option {{ Request::get('perpage')==500?'selected':'' }} value="500">500</option>
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                    {{Lang::get('common.find')}}
                               </button> 
                            </div>
                           
                            <?php
                                $add_status = !empty($permissions->add_status)?$permissions->add_status:'0'; 
                            ?>
                            @if($add_status == 1 || $is_admin == 1)
                            <div class="col-xs-2">
                                <span id="checkTrial">
                                <a href="{{url($current_menu.'/create')}}" class="form-control btn btn-sm btn-primary" style="margin-top: 25px">
                                    <i class="fa fa-plus mg-r-10"></i> {{Lang::get('common.add')}}  {{Lang::get('common.dealer_detail')}}
                                </a>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </form><br>
                <div class="row">
                    <div class="col-xs-12" style="overflow-x: scroll;">
                        <div class="table-header center">
                            {{Lang::get('common.dealer_detail')}}
                            <div class="pull-right tableTools-container"></div>
                           
                        </div>
                        <table id="dynamic-table" class="table table-bordered table-hover">
                            <thead>
                                    <th class="center">
                                        {{Lang::get('common.s_no')}}
                                    </th>
                                    <th>{{Lang::get('common.distributor')}} Code</th>
                                    <th>{{Lang::get('common.distributor_name')}}</th>
                                    <th>{{Lang::get('common.username')}}</th>

                                    <th>{{Lang::get('common.csa')}}</th>
                                    <th>Contact Person</th>
                                    <th>{{Lang::get('common.email')}}</th>
                                    <th>{{Lang::get('common.user_contact')}}</th>
                                    <th>{{Lang::get('common.address')}}</th>
                                    <th>Created At</th>
                                    <?php $null = ''; ?>
                                    @if($assign_price_list>0)
                                        <?php $null = 'null,'; ?>
                                        <th>Price List Template Name</th>
                                    @endif
                                    <th>{{Lang::get('common.location7')}} Count</th>
                                    <th>Location Details</th>
                                    <th>{{Lang::get('common.status')}}</th>
                                    <th>{{Lang::get('common.action')}}</th>
                            </thead>
                            <tbody>
                                @foreach($records as $key=>$data)
                                    <?php $encid = Crypt::encryptString($data->id);?>
                                    <tr>
                                        <td class="center">
                                            {{ 1 + $key }}
                                        </td>
                                        <td>{{$data->dealer_code}}</td>
                                        <td><a href="{{url('distributor/'.$encid)}}">{{$data->name}}</a></td>
                                        <td>{{!empty($user_assign[$data->id])?$user_assign[$data->id]:'Not Assign Yet to any user'}}</td>

                                        <td> {{!empty($csa_name_details[$data->csa_id])?$csa_name_details[$data->csa_id]:'-'}}</td>
                                        <td>{{$data->contact_person}}</td>
                                        <td>{{$data->email}}</td>
                                        <td>{{!empty($data->landline)?$data->landline:$data->other_numbers}}</td>
                                        <td>{{$data->address}}</td>
                                        <td>{{$data->created_at}}</td>
                                        @if($assign_price_list>0)
                                            <td>{{!empty($assigned_dealer_data[$data->template_id])?$assigned_dealer_data[$data->template_id]:''}}</td>
                                        @endif
                                        <td>{{!empty($beat_count[$data->id])?$beat_count[$data->id]:'0'}}</td>
                                        <td>
                                            @if(!empty($arr2[$data->id]))
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr style="background-color: #4287ba;">
                                                        <th>{{Lang::get('common.s_no')}}</th>
                                                        <th>{{Lang::get('common.location3')}}</th>
                                                        <th>{{Lang::get('common.location5')}}</th>
                                                        <th>{{Lang::get('common.location6')}}</th>
                                                        <th>{{Lang::get('common.location7')}}</th>
                                                    </tr>
                                                    </thead>
                                                    @foreach($arr2[$data->id] as $a=>$b)
                                                        <tr>
                                                            <td>{{$a+1}}</td>
                                                            <td>{{$b->l3_name}}</td>
                                                            <td>{{$b->l5_name}}</td>
                                                            <td>{{$b->l6_name}}</td>
                                                            <td>{{$b->l7_name}}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            @endif
                                        </td>
                                        <td class="hidden-480">
                                            @if($data->dealer_status==1)
                                                <span class="label label-sm label-success">Active</span>
                                            @elseif($data->dealer_status==9)
                                                <span class="label label-sm label-danger">Deleted</span>
                                            @else
                                                <span class="label label-sm label-warning">In-active</span>
                                            @endif
                                        </td>
                                        <td>

                                            <div class="hidden-sm hidden-xs btn-group">
                                                <?php
                                                    $delete_status = !empty($permissions->delete_status)?$permissions->delete_status:'0'; 
                                                    $edit_status = !empty($permissions->edit_status)?$permissions->edit_status:'0'; 
                                                ?>
                                                @if($edit_status == 1 || $is_admin == 1)
                                                    @if($data->dealer_status==1)
                                                        <button title="Inactive" class="btn btn-xs btn-warning"
                                                                onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button title="Active" class="btn btn-xs btn-success"
                                                                onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif
                                                    <?php echo $data->user_id;  ?>
                                                    <a title="Edit" class="btn btn-xs btn-info"
                                                       href="{{url($current_menu.'/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>
                                                    @if(!empty($data->user_login))
                                                        <button title="Distributor Credentials" userid="{{$data->id}}"
                                                                data-toggle="modal" data-target="#myModal2"
                                                                class="user-modal2 btn btn-xs btn-success">
                                                            <i class="ace-icon fa fa-user bigger-120"></i>
                                                        </button>

                                                    @else
                                                        <button title="Distributor Credentials" userid="{{$data->id}}"
                                                                data-toggle="modal" data-target="#myModal"
                                                                class="user-modal btn btn-xs btn-default">
                                                            <i class="ace-icon fa fa-user-plus bigger-120"></i>
                                                        </button>
                                                    @endif
                                                    
                                                
                                                    @if($delete_status == 1 || $is_admin == 1)

                                                        <button title="Delete" class="btn btn-xs btn-danger"
                                                                onclick="confirmAction('{{Lang::get('common.'.$current_menu)}}','{{Lang::get('common.'.$current_menu)}}','{{$data->id}}','{{$status_table}}','delete');">
                                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                        </button>
                                                    
                                                    @endif

                                                @endif
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
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>


<!-- modal starts here  -->
 <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                {{--<div class="widget-header widget-header-small">--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">@Lang('common.dealer_module') Credentials</h4>
                {{--</div>--}}
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif
                <form method="post" id="filter_distributor"
                      action="{{route('addDealerUser')}}">
                    {!! csrf_field() !!}
                    <input type="hidden" id="uuid" name="uuid" value="">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right" for="person">
                                    {{Lang::get('common.distributor')}} Name </label>
                                <input required="required" type="text"
                                       placeholder="Distributor Name"
                                       name="person_name" id="person_name"
                                       class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="username"> {{Lang::get('common.user_name')}} </label>
                                <input required="required" type="text"
                                       placeholder="Username" name="username"
                                       id="username" class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="user_password">{{Lang::get('common.password')}}</label>
                                <input required="required" type="password"
                                       placeholder="Password"
                                       name="user_password" id="user_password"
                                       class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="phone"> {{Lang::get('common.user_contact')}} </label>
                                <input type="text" placeholder="Mobile no" name="phone"
                                       id="phone" class="form-control input-sm">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="email"> {{Lang::get('common.email')}} </label>
                                <input required="required" type="email" placeholder="Email"
                                       name="email"
                                       id="email" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="role_name"> {{Lang::get('common.role_key')}} </label>
                                <select required="required" name="role_name" id="role_name"
                                        class="form-control input-sm">
                                    <option value="">Select</option>
                                   @if(!empty($role))
                                        @foreach($role as $l3_key=>$l3_data)
                                            <option value="{{$l3_key}}">{{$l3_data}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="role_name"> {{Lang::get('common.location3')}} </label>
                                <select name="state" id="state" required="required"
                                        class="form-control input-sm">
                                    <option>Select</option>
                                    @if(!empty($location3))
                                        @foreach($location3 as $l3_key=>$l3_data)
                                            <option value="{{$l3_key}}">{{$l3_data}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Save
                                </button>
                            </div>
                        </div>
                    </div>
                  
                </form>
                <div class="row">
                    <div class="col-md-12" id="result">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                {{--<div class="widget-header widget-header-small">--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">{{Lang::get('common.distributor')}} Credentials</h4>
                {{--</div>--}}
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif
                <form method="post" id="filter_distributor2"
                      action="{{route('updateDealerUser')}}">
                    {!! csrf_field() !!}
                    <input type="hidden" id="uuid2" name="uuid2" value="">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right" for="person">
                                    {{Lang::get('common.distributor')}} Name </label>
                                <input required="required" type="text"
                                       placeholder="Distributor Name"
                                       name="person_name" id="person_name2"
                                       class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="username"> {{Lang::get('common.user_name')}} </label>
                                <input required="required" type="text"
                                       placeholder="Username" name="username2"
                                       id="username2" class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="user_password">{{Lang::get('common.password')}}</label>
                                <input type="password" name="password" style="display: none">
                                <input type="text"
                                       placeholder="New Password"
                                       name="user_password" id="user_password2"
                                       class="form-control input-sm">
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="phone"> {{Lang::get('common.user_contact')}} </label>
                                <input type="text" placeholder="Phone no" name="phone"
                                       id="phone2" class="form-control input-sm">
                            </div>
                        </div>
                        

                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="email"> {{Lang::get('common.email')}} </label>
                                <input required="required" type="email" placeholder="Email"
                                       name="email"
                                       id="email2" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="role_name"> {{Lang::get('common.role_key')}} </label>
                                <select required="required" name="role_name" id="role_name2"
                                        class="form-control input-sm">
                                    <option value="">{{Lang::get('common.distributor')}}</option>
                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="role_name"> {{Lang::get('common.location3')}} </label>
                                <select name="state" id="state2" required="required"
                                        class="form-control input-sm">
                                    
                                </select>
                            </div>
                        </div>
                       

                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-3">
                            {{-- <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Update
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12" id="result">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- modal box ends here -->
@endsection

@section('js')

    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
  

    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('js/dealer.js')}}"></script> 

    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    @include('DashboardScript.commonModalScript')
    
    <script>
        $(".chosen-select").chosen();
            $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
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


        $('.user-modal2').click(function() {
            var dealer_id = $(this).attr('userid');
            $('.mytbody').html('');
          
            if (dealer_id != '') 
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/get_dealer_person_details',
                    dataType: 'json',
                    data: "dealer_id=" + dealer_id,
                    success: function (data) 
                    {
                        $('#person_name2').val('');
                        $('#username2').val('');
                        $('#user_password2').val('');
                        $('#phone2').val('');
                        $('#email2').val('');
                        $('#email2').val('');
                        $('#state2').html('');
                        if (data.code == 401) 
                        {
                            
                        }
                        else if (data.code == 200) 
                        {
                            $('#person_name2').html('');
                            $('#username2').html('');
                            $('#user_password2').html('');
                            $('#phone2').html('');
                            $('#email2').html('');
                            $('#email2').html('');
                            
                            $('#person_name2').val(data.result.person_name);
                            $('#username2').val(data.result.uname);
                            $('#user_password2').val(data.result.person_password);
                            $('#phone2').val(data.result.phone);
                            $('#email2').val(data.result.email);
                            $('#state2').html('<option>'+data.result.l3_name+'<option>');
                            // $('#email2').val(data.result.pass);
                            
                            
                        }

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

    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
     <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                {"bSortable": false},
                                                null,null,null,null,null,null,null,null,null,null,null,null,<?= $null;?>
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "1300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            "bScrollCollapse": true,
                                            "iDisplayLength": 1000000,


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

<script>
    $(document).on('change', '#location_1', function () {
    _current_val = $(this).val();
    location_data(_current_val,2);
});

$(document).on('change', '#location_2', function () {
    _current_val = $(this).val();
    location_data(_current_val,3);
});

$(document).on('change', '#state', function () {
    _current_val = $(this).val();
    csa(_current_val);
    // location_data(_current_val,4);
});

$(document).on('change', '#location_4', function () {
    _current_val = $(this).val();
    location_data(_current_val,5);
});
$(document).on('change', '#location_5', function () {
    _current_val = $(this).val();
    location_data(_current_val,6);
});
$(document).on('change', '#location_6', function () {
    _current_val = $(this).val();
    location_data(_current_val,7);
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
                            // console.log(value);
                            template += '<option value="' + key + '" >' + (value) + '</option>';
                        }
                    });
                    _append_box.empty();
                    _append_box.append(template).trigger("chosen:updated");

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

$(document).on('change', '#state', function () {
    _current_val = $(this).val();
    dealer_data(_current_val,4);
});


function dealer_data(val,level) {
    _append_box=$('#distributor');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getDistributor',
            dataType: 'json',
            data: "id=" + val,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {

                    //Location 3
                    template = '<option value="" >Select</option>';
                    
                    $.each(data.result, function (key, value) {
                        if (value.name != '') {
                            template += '<option value="' + key + '" >' + (value) + '</option>';
                        }
                    });
                    _append_box.empty();
                     _append_box.append(template).trigger("chosen:updated");


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

function csa(val) {
    _append_box=$('#csa');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getCSA',
            dataType: 'json',
            data: "id=" + val,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    var level=4;
                    // alert(level);
                    $.ajax({
                        type: "POST",
                        url: domain + '/getLocation',
                        dataType: 'json',
                        data: "id=" + val+"&type="+level+"&flag=85",
                        success: function (data2) {
                            if (data2.code == 401) {
                                //  $('#loading-image').hide();
                            }
                            else if (data2.code == 200) {

                                //Location 3
                                template2 = '<option value="" >Select</option>';
                                $.each(data2.result, function (key2, value2) {
                                    if (value2.name != '') {
                                        template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                                    }
                                });
                                // console.log()
                                $('#location_4').empty();
                                $('#location_4').append(template2).trigger("chosen:updated");

                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });


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
@endsection