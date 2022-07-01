@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.catalog_product_master')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />
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

                    <li class="active">@Lang('common.catalog_product_master')</li>
                </ul><!-- /.breadcrumb -->

                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#filterForm" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                    <a href="{{url('catalog-product/create')}}" class="btn btn-sm btn-info"><i class="fa fa-plus mg-r-10"></i> @Lang('common.catalog_product')</a>
                </p>
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                <div class="clearfix" style="margin-top: 5px"></div>
                <form method="get" class="collapse" id="filterForm">
                    <div class="row">
                        <div class="col-lg-8 col-sm-12">
                            <div class="row">
                                <div class="col-xs-4 form-group">
                                    <div class="input-group">
                                        @if(empty(Request::get('search')))
                                            <input type="text" placeholder="Search by Product Name" id="search"
                                                   name="search" value="{{ Request::get('search') }}"
                                                   class="form-control"/>
                                            <span onclick="search()" class="input-group-addon cursor">
                                            <i class="fa fa-search"></i>
                                        </span>
                                        @else
                                            <input type="text" readonly="readonly" placeholder="Search by name"
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
                        <div class="col-lg-4 col-sm-12">
                            <div class="row">
                                <div class="col-lg-6 form-group">
                                    <select name="perpage" id="perpage" class="form-control cursor"
                                            onchange="form.submit()">
                                        <option value="">Per Page</option>
                                        <option {{ Request::get('perpage')==10?'selected':'' }} value="10">10</option>
                                        <option {{ Request::get('perpage')==25?'selected':'' }} value="25">25</option>
                                        <option {{ Request::get('perpage')==50?'selected':'' }} value="50">50</option>
                                        <option {{ Request::get('perpage')==100?'selected':'' }} value="100">100</option>
                                    </select>
                                </div>

                                <div class="col-lg-6 col-sm-12">
                                    <a href="{{url('catalog-product/create')}}" class="btn btn-sm btn-info btn-block mg-b-10"><i
                                                class="fa fa-plus mg-r-10"></i> @Lang('common.catalog_product_add_button')</a>
                                </div>
                                {{--@endif--}}
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
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Catalog-1 Name</th>
                                        <th>Hsn Code</th>

                                        <th>MRP</th>
                                        <th>Product Division</th>
                                        <th>Is Focus</th>
                                        <th>
                                            <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
                                            Registered On
                                        </th>
                                        <th class="hidden-480">Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($catalogproduct as $key=>$catalogproduct_data)

                                        <?php $encid = Crypt::encryptString($catalogproduct_data->id);?>
                                        <tr>
                                            <td class="center">
                                                {{ $catalogproduct->firstItem() + $key }}

                                            </td>
                                            <td>
                                                <a href="#">{{ucwords(strtolower($catalogproduct_data->product_code))}}</a>
                                            </td>
                                            <td>{{$catalogproduct_data->product_name}}</td>
                                            <td>{{$catalogproduct_data->catalog_1_name}}</td>
                                            <td>{{$catalogproduct_data->hsn_code}}</td>
                                            <td>{{$catalogproduct_data->mrp}}</td>

                                            <td>{{$catalogproduct_data->division_name}}</td>
                                            <td>@if($catalogproduct_data->is_focus==1)
                                                    <span class="label label-sm label-success">Yes</span>
                                                @elseif($catalogproduct_data->is_focus==0)
                                                    <span class="label label-sm label-danger">No</span>

                                                @endif</td>
                                            <td>{{date('d-M-Y',strtotime($catalogproduct_data->created_at))}}</td>



                                            <td class="hidden-480">
                                                @if($catalogproduct_data->status==1)
                                                    <span class="label label-sm label-success">Active</span>
                                                @elseif($catalogproduct_data->status==9)
                                                    <span class="label label-sm label-danger">Deleted</span>
                                                @else
                                                    <span class="label label-sm label-warning">In-active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    @if($catalogproduct_data->status==1)
                                                        <button class="btn btn-xs btn-warning" title= "Inactive" onclick="confirmAction('@Lang('common.catalog_product_master')','<strong>{{$catalogproduct_data->product_name}}</strong>','{{$catalogproduct_data->id}}','catalog_products','inactive');">
                                                            <i class="ace-icon fa fa-ban bigger-120"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-xs btn-success" title="Active" onclick="confirmAction('@Lang('common.catalog_product_master')','<strong>{{$catalogproduct_data->product_name}}</strong>','{{$catalogproduct_data->id}}','catalog_products','active');">
                                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                                        </button>
                                                    @endif

                                                    <a class="btn btn-xs btn-info"  title="Edit" href="{{url('catalog-product/'.$encid.'/edit')}}">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </a>

                                                    <button class="btn btn-xs btn-danger" title = "Delete" onclick="confirmAction('@Lang('common.catalog_product_master')','<strong>{{$catalogproduct_data->product_name}}</strong>','{{$catalogproduct_data->id}}','catalog_products','delete');">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </button>
                                                </div>

                                                {{--<div class="hidden-md hidden-lg">--}}
                                                {{--<div class="inline pos-rel">--}}
                                                {{--<button class="btn btn-minier btn-primary dropdown-toggle"--}}
                                                {{--data-toggle="dropdown" data-position="auto">--}}
                                                {{--<i class="ace-icon fa fa-cog icon-only bigger-110"></i>--}}
                                                {{--</button>--}}

                                                {{--<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">--}}
                                                {{--<li>--}}
                                                {{--<a href="#" class="tooltip-info" data-rel="tooltip"--}}
                                                {{--title="View">--}}
                                                {{--<span class="blue">--}}
                                                {{--<i class="ace-icon fa fa-search-plus bigger-120"></i>--}}
                                                {{--</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}

                                                {{--<li>--}}
                                                {{--<a href="#" class="tooltip-success" data-rel="tooltip"--}}
                                                {{--title="Edit">--}}
                                                {{--<span class="green">--}}
                                                {{--<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>--}}
                                                {{--</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}

                                                {{--<li>--}}
                                                {{--<a href="#" class="tooltip-error" data-rel="tooltip"--}}
                                                {{--title="Delete">--}}
                                                {{--<span class="red">--}}
                                                {{--<i class="ace-icon fa fa-trash-o bigger-120"></i>--}}
                                                {{--</span>--}}
                                                {{--</a>--}}
                                                {{--</li>--}}
                                                {{--</ul>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                            </td>
                                        </tr>


                                    @endforeach

                                </table>
                                <div class="col-xs-6">
                                    <div class="dataTables_info">
                                        Showing {{($catalogproduct->currentpage()-1)*$catalogproduct->perpage()+1}}
                                        to {{(($catalogproduct->currentpage()-1)*$catalogproduct->perpage())+$catalogproduct->count()}}
                                        of {{$catalogproduct->total()}} entries
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{--Larvel default pagination with custom view --}}
                                        {{$catalogproduct->appends(request()->except('page'))}}
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

    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/index.catalog.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script>
        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
@endsection