@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.dealer')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
@endsection

@section('body')
<?php $cdata = App\CommonFilter::companyData()->source; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>

                    <li class="active">@Lang('common.dealer')</li>
                </ul><!-- /.breadcrumb -->

                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#filterForm" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                    @if($cdata == 2)
                    <a href="{{url('dealer/create')}}" class="btn btn-sm btn-info"><i class="fa fa-plus mg-r-10"></i> @Lang('common.add_dealer')</a>
                    @endif
                </p>
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                {{--<div class="row">--}}
                    {{--<div class="col-lg-3 col-sm-12">--}}
                        {{----}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="clearfix" style="margin-top: 5px"></div>
                <form method="get" class="collapse" id="filterForm">
                    <div class="row">
                        <div class="col-lg-9 col-sm-12">
                            <div class="row">
                                <div class="col-xs-4 form-group">
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
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="row">
                                <div class="col-lg-offset-6 col-lg-6 form-group">
                                    <select name="perpage" id="perpage" class="form-control cursor"
                                            onchange="form.submit()">
                                        <option value="">Per Page</option>
                                        <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                        <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                        <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                        <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                    </select>
                                </div>
                                {{--                        @if ( isset($menu_arr['modulelevel']['user-management']['add']) && $menu_arr['modulelevel']['user-management']['add']==1)--}}

                                {{--@endif--}}

                            </div>


                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="dpd1" class="control-label">From Date</label>
                                        <input value="{{ Request::get('fromdate') }}" type="text" placeholder="From Date" name="fromdate" id="dpd1" class="form-control date-picker">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="dpd2" class="control-label">To Date</label>
                                        <input value="{{ Request::get('todate') }}" type="text" placeholder="To Date" name="todate" id="dpd2" class="form-control date-picker">
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
                                        </th>
                                        <th class="detail-col">Details</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Person</th>
                                        <th>
                                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                            Registered On
                                        </th>
                                        <th class="hidden-480">Status</th>

                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($dealers as $key=>$dealer)
                                        <?php $encid = Crypt::encryptString($dealer->id);?>
                                        <tr>
                                            <td class="center">
                                                {{ $dealers->firstItem() + $key }}
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
                                                <a href="#">{{ucwords(strtolower($dealer->name))}}</a>
                                            </td>
                                            <td>{{$dealer->email}}</td>
                                            <td>{{$dealer->contact_person}}</td>
                                            <td>{{$dealer->created_at==null?'N/A':date('d-M-Y',strtotime($dealer->created_at))}}</td>

                                            <td class="hidden-480">
                                                @if($dealer->status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($dealer->status==2)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($dealer->status==1)
                                                        <button class="btn btn-xs btn-warning"
                                                                onclick="confirmAction('{{Lang::get('common.dealer')}}','dealer','{{$dealer->id}}','dealers','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-xs btn-success"
                                                                onclick="confirmAction('{{Lang::get('common.dealer')}}','dealer','{{$dealer->id}}','dealers','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif

                                                    <a class="btn btn-xs btn-info"
                                                       href="{{url('dealer/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button class="btn btn-xs btn-danger"
                                                            onclick="confirmAction('{{Lang::get('common.dealer')}}','dealer','{{$dealer->id}}','dealers','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button>

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
                                            <td colspan="8">
                                                <div class="table-detail">
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="text-center">
                                                                <img height="150" style="max-width: 136px;"
                                                                     class="thumbnail inline no-margin-bottom"
                                                                     alt="Domain Owner's Avatar"
                                                                     onerror="null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}'"
                                                                     src="{{asset('dealer-profile/'.$dealer->image_name)}}"/>
                                                                <br/>
                                                                <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                                                    <div class="inline position-relative">
                                                                        <a class="user-title-label" href="#">
                                                                            @if($dealer->status==1)
                                                                                <i class="ace-icon fa fa-circle light-green"></i>
                                                                                &nbsp;
                                                                                <span class="white">Active</span>
                                                                            @elseif($dealer->status==2)
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
                                                                        <span>{{ucwords(strtolower($dealer->name))}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Location</div>

                                                                    <div class="profile-info-value">
                                                                        <i class="fa fa-map-marker light-orange bigger-110"></i>
                                                                        <span>{{$dealer->address.' '.$dealer->address2.' '.$dealer->address3}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">

                                                                    <div class="profile-info-name"> dealer Code</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$dealer->dealer_code}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Contact Person</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$dealer->contact_person}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Email</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$dealer->email}}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="profile-info-row">
                                                                    <div class="profile-info-name"> Registered On</div>

                                                                    <div class="profile-info-value">
                                                                        <span>{{$dealer->created_at==null?'N/A':date('d-M-Y',strtotime($dealer->created_at))}}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                        Showing {{($dealers->currentpage()-1)*$dealers->perpage()+1}}
                                        to {{(($dealers->currentpage()-1)*$dealers->perpage())+$dealers->count()}}
                                        of {{$dealers->total()}} entries
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{--Larvel default pagination with custom view --}}
                                        {{$dealers->appends(request()->except('page'))}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/dealer.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script>
        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
@endsection