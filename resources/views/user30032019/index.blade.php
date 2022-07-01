@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.user-mgmt')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
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

                    <li class="active">@Lang('common.user-mgmt')</li>
                </ul>

                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#filterForm" class="btn btn-sm btn-default"><i
                                class="fa fa-navicon mg-r-10"></i> Filter</a>
                    <a href="{{url('user-management/create')}}" class="btn btn-sm btn-info"><i
                                class="fa fa-plus mg-r-10"></i> Add User</a>
                </p>

                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                {{--<div class="row">--}}
                {{--<div class="col-lg-3 col-sm-12">--}}

                {{--</div>--}}
                {{--</div>--}}
                <div class="clearfix" style="margin-top: 5px"></div>
                <form method="get" class="collapse in" id="filterForm">
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group pull-left">
                                <div class="input-group">
                                    @if(empty(Request::get('search')))
                                        <input type="text" placeholder="Search by name or email" id="search"
                                               name="search" value="{{ Request::get('search') }}"
                                               class="form-control"/>
                                        <span onclick="search()" class="input-group-addon cursor">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    @else
                                        <input type="text" readonly="readonly" placeholder="Search by name or email"
                                               id="search" name="search" value="{{ Request::get('search') }}"
                                               class="form-control"/>
                                        <span onclick="searchReset();" class="input-group-addon cursor">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    @endif
                                </div>
                                {{--<input type="submit" value="search" class="btn btn-sm btn-primary">--}}
                            </div>
                        </div>
                        <div class="col-lg-9 col-sm-12">
                            <div class="form-group pull-right">
                                <select name="perpage" id="perpage" class="form-control cursor"
                                        onchange="form.submit()">
                                    <option value="">Per Page</option>
                                    <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                    <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                    <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                    <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100
                                    </option>
                                </select>
                            </div>

                        </div>
                        {{--                        @if ( isset($menu_arr['modulelevel']['user-management']['add']) && $menu_arr['modulelevel']['user-management']['add']==1)--}}
                        {{--<div class="col-lg-1 col-sm-12">--}}
                        {{--<a href="{{url('user-management/create')}}" class="btn btn-sm btn-info"><i class="fa fa-plus mg-r-10"></i> Add User</a>--}}
                        {{--</div>--}}
                        {{--@endif--}}
                    </div>

                    <div class="row">
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="state" class="control-label">State</label>
                                        <select name="state" id="state" class="form-control cursor">
                                            <option value="">All</option>
                                            @foreach($state_data as $state)
                                                <option {{ !empty(Request::get('state')) && ($state->id==Request::get('state'))?'selected':'' }} value="{{$state->code}}">{{$state->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="city" class="control-label">City</label>
                                        <select name="city[]" id="city" class="chosen-select form-control"
                                                multiple="multiple">
                                            <option value="">All</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="role" class="control-label">Role</label>
                                        <select name="role" id="role" class="form-control cursor">
                                            <option value="">All</option>
                                            @foreach($role_data as $role)
                                                <option value="{{$role->id}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                            style="margin-top: 28px;"><i class="fa fa-filter mg-r-10"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-lg-6">
                                    <button type="button" onclick="formReset()"
                                            class="btn btn-sm btn-danger btn-block mg-b-10" style="margin-top: 28px;"><i
                                                class="fa fa-refresh mg-r-10"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

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

                        <div class="row">
                            <div class="col-xs-12">
                                <table id="simple-table" class="table  table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center">
                                            S.No.
                                            {{--<label class="pos-rel">--}}
                                            {{--<input type="checkbox" class="ace"/>--}}
                                            {{--<span class="lbl"></span>--}}
                                            {{--</label>--}}
                                        </th>
                                        <th class="detail-col">Details</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Email</th>
                                        <th class="hidden-480">Mobile</th>

                                        <th>
                                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                            Registered On
                                        </th>
                                        <th class="hidden-480">Status</th>

                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($users as $key=>$user)
                                        <?php $encid = Crypt::encryptString($user->user_id);?>
                                        <tr>
                                            <td class="center">
                                                {{ $users->firstItem() + $key }}
                                                {{--<label class="pos-rel">--}}
                                                {{--<input type="checkbox" class="ace"/>--}}
                                                {{--<span class="lbl"></span>--}}
                                                {{--</label>--}}
                                            </td>

                                            <td class="center">
                                                <div class="action-buttons">
                                                    <a href="#" class="green bigger-140 show-details-btn"
                                                       title="Show Details">
                                                        <i class="ace-icon fa fa-angle-double-down"></i>
                                                        <span class="sr-only">Details</span>
                                                    </a>
                                                </div>
                                            </td>

                                            <td>
                                                <a href="#">{{ucwords(strtolower($user->name))}}</a>
                                            </td>
                                            <td>{{$user->gender==1?'Male':'Female'}}</td>
                                            <td>{{$user->email}}</td>
                                            <td class="hidden-480">{{$user->mobile}}</td>
                                            <td>{{date('d-M-Y',strtotime($user->created_at))}}</td>

                                            <td class="hidden-480">
                                                @if($user->status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($user->status==2)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($user->status==1)
                                                        <button class="btn btn-xs btn-warning"
                                                                onclick="confirmAction('{{Lang::get('common.user-mgmt')}}','user','{{$user->user_id}}','users_details,users','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-xs btn-success"
                                                                onclick="confirmAction('{{Lang::get('common.user-mgmt')}}','user','{{$user->user_id}}','users_details,users','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif

                                                    <a class="btn btn-xs btn-info"
                                                       href="{{url('user-management/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button class="btn btn-xs btn-danger"
                                                            onclick="confirmAction('{{Lang::get('common.user-mgmt')}}','user','{{$user->user_id}}','users_details,users','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button>

                                                    <a class="btn btn-xs btn-info" title="Assign {{Lang::get('common.dealer_module')}}"
                                                       href="{{url('user-management/'.$encid)}}">
                                                        <i class="ace-icon fa fa-bullseye bigger-120"></i>
                                                    </a>

                                                    {{--<button class="btn btn-xs btn-warning">--}}
                                                    {{--<i class="ace-icon fa fa-flag bigger-120"></i>--}}
                                                    {{--</button>--}}
                                                </div>

                                                <div class="hidden-md hidden-lg">
                                                    <div class="inline pos-rel">
                                                        <button class="btn btn-minier btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" data-position="auto">
                                                            <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                        </button>

                                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                                            <li>
                                                                <a href="#" class="tooltip-info" data-rel="tooltip"
                                                                   title="View">
																			<span class="blue">
																				<i class="ace-icon fa fa-search-plus bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="#" class="tooltip-success" data-rel="tooltip"
                                                                   title="Edit">
																			<span class="green">
																				<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="#" class="tooltip-error" data-rel="tooltip"
                                                                   title="Delete">
																			<span class="red">
																				<i class="ace-icon fa fa-trash-o bigger-120"></i>
																			</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="detail-row">
                                            <td colspan="9">
                                                <div class="table-detail">
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="text-center">
                                                                <img height="150"
                                                                     class="thumbnail inline no-margin-bottom"
                                                                     alt="Domain Owner's Avatar"
                                                                     onerror="null;this.src='msell/images/avatars/profile-pic.jpg'"
                                                                     src="{{asset($user->profile_image)}}"/>
                                                                <br/>
                                                                <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                                                    <div class="inline position-relative">
                                                                        <a class="user-title-label" href="#">
                                                                            @if($user->status==1)
                                                                                <i class="ace-icon fa fa-circle light-green"></i>
                                                                                &nbsp;
                                                                                <span class="white">Active</span>
                                                                            @elseif($user->status==2)
                                                                                <i class="ace-icon fa fa-circle light-red"></i>
                                                                                &nbsp;
                                                                                <span class="white">Deleted</span>
                                                                            @else
                                                                                <i class="ace-icon fa fa-circle light-orange"></i>
                                                                                &nbsp;
                                                                                <span class="white">In-active</span>
                                                                            @endif

                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-xs-12 col-sm-7">
                                                            <div class="space visible-xs"></div>

                                                            <div class="profile-user-info profile-user-info-striped">
                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Name</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{ucwords(strtolower($user->name))}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Location</div>

                                                                    <div class="profile-info-value">
                                                                        <i class="fa fa-map-marker light-orange bigger-110"></i>
                                                                        <span> {{!empty($user->address)?$user->address:'N/A'}} </span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">

                                                                    <div class="profile-info-name"> Age</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{date_diff(date_create($user->dob), date_create('today'))->y}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Gender</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$user->gender==1?'Male':'Female'}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Email</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$user->email}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Registered On</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{date('d-M-Y',strtotime($user->created_at))}}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-xs-12 col-sm-3">
                                                            <div class="space visible-xs"></div>
                                                            <h4 class="header blue lighter less-margin">Send an
                                                                email</h4>

                                                            <div class="space-6"></div>

                                                            <form>
                                                                <fieldset>
                                                                    <textarea class="width-100" resize="none"
                                                                              placeholder="Type something…"></textarea>
                                                                </fieldset>

                                                                <div class="hr hr-dotted"></div>

                                                                <div class="clearfix">
                                                                    <label class="pull-left">
                                                                        <input type="checkbox" class="ace"/>
                                                                        <span class="lbl"> Email me a copy</span>
                                                                    </label>

                                                                    <button class="pull-right btn btn-sm btn-primary btn-white btn-round"
                                                                            type="button">
                                                                        Submit
                                                                        <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                <div class="col-xs-6">
                                    <div class="dataTables_info">
                                        Showing {{($users->currentpage()-1)*$users->perpage()+1}}
                                        to {{(($users->currentpage()-1)*$users->perpage())+$users->count()}}
                                        of {{$users->total()}} entries
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{--Larvel default pagination with custom view --}}
                                        {{$users->appends(request()->except('page'))}}
                                    </div>
                                </div>
                            </div><!-- /.span -->
                        </div><!-- /.row -->

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')

    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/user-management.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
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
//                    somethingElse: {
//                        text: 'Something else',
//                        btnClass: 'btn-blue',
//                        keys: ['enter', 'shift'],
//                        action: function(){
//                            $.alert('Something else?');
//                        }
//                    }
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
    </script>
@endsection