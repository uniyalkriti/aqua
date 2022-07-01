@extends('layouts.master')
@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>

    <style>
        .has-error, .help-block {
            color: #a94442;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            text-align: center;
            vertical-align: middle;
        }
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            background-color: #7BB0FF !important;
            color: black;
        }
    </style>
@endsection
@section('body')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
            <ul class="breadcrumb">
                <li style="color: white">
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a style="color: white" href="{{url('home')}}">Dashboard</a>
                </li>
                <li>
                    <a style="color: white" href="{{url('product_rate_list')}}">{{Lang::get('common.'.$current_menu)}}</a>
                </li>

                <li class="active" style="color: white">Create {{Lang::get('common.'.$current_menu)}}</li>
            </ul><!-- /.breadcrumb -->
            <!-- /.nav-search -->
        </div>
        <div class="page-content">
            {{--@include('layouts.settings')--}}

            @if(count($errors)>0)
                @foreach ($errors->all() as $error)
                    <div class="help-block">{{ $error }}</div>
                @endforeach
            @endif

            <?php
            // dd($_GET); 
            if(!empty($_GET['template_name']))
                $template_name=$_GET['template_name'];
                else
                   $template_name=0;

                if(!empty($_GET['cat_id']))
                $cat_id=$_GET['cat_id'];
                else
                   $cat_id=0;

                if(!empty($_GET['template_name']))
                $template_name=$_GET['template_name'];
                else
                   $cat_id=0;
           
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form id="month_form" method="get">
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right">Template Name</label>
                                           
                                            <select name="template_name" id="template_name" class="form-control input-sm" required="">
                                                <option value="">Select</option>
                                                @if(!empty($template_arary))
                                                    @foreach($template_arary as $c1_key=>$c1_data)
                                                        <option {{$template_name==$c1_key?'selected':''}} value="{{$c1_key}}">{{$c1_data}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            <label class="control-label no-padding-right">Product Name</label>
                                            <select name="cat_id" id="cat_id" class="form-control input-sm" >
                                                <option value="">Select</option>
                                                @if(!empty($catList))
                                                    @foreach($catList as $c1_key=>$c1_data)
                                                        <option {{$cat_id==$c1_key?'selected':''}} value="{{$c1_key}}">{{$c1_data}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                   
                                    <div class="col-lg-3">
                                        <div class="">
                                            <br>
                                            <button type="submit" class="btn btn-sm btn-primary form-control" value="find">Find</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @if(!empty($sku))
                        <form class="form-horizontal" action="{{route($current_menu.'.store')}}" method="POST" id="catalog-form3" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="hr hr-16 hr-dotted"></div>

                            <div class="row" style="overflow-x: scroll;">
                                <table id="simple-table" align="center" class="table table-bordered table-hover">
                                    <thead><tr><td colspan="14"><h3>Product Details</h3></td></tr></thead>
                                    <tr>
                                        <th>Sno.</th>
                               
                                        <th>{{Lang::get('common.catalog_product')}}</th>
                                        <th>Cases MRP</th>
                                        <th>MRP</th>
                                        <th>{{Lang::get('common.csa')}}  Case Rate</th>
                                        <th>{{Lang::get('common.csa')}} Rate</th>
                                        <th>{{Lang::get('common.distributor')}} Case Rate</th>
                                        <th>{{Lang::get('common.distributor')}} Rate</th>
                                        <th>{{Lang::get('common.retailer')}} Case Rate</th>
                                        <th>{{Lang::get('common.retailer')}} Rate</th>
                                        @if(!empty($check_assign))
                                            <th>Primary Unit</th>
                                            <th>Primary {{Lang::get('common.retailer')}} Rate</th>
                                            <th>Primary {{Lang::get('common.distributor')}} Rate</th>
                                            <th>Primary {{Lang::get('common.csa')}} Rate</th>
                                        @endif


                                    </tr>
                                    <tbody>
                                        <input type="hidden" name="template_name"  value="{{$template_name}}">
                                       
                                        <?php $inc=1; ?>
                                        @if(!empty($sku))
                                            @foreach($sku as $k=>$data)
                                                <?php 
                                                    if(!empty($product_rate_list_template_fixed_data[$data->id]))
                                                    {
                                                        // dd($product_rate_list_template_fixed_data[$data->id]);
                                                        $explode_data = explode('|', $product_rate_list_template_fixed_data[$data->id]);
                                                        // dd($explode_data);
                                                    }
                                                    else
                                                    {
                                                        $explode_data = '';
                                                    }
                                                ?>
                                                <tr>

                                                    <td>{{$inc}}
                                                    <input type="hidden" name="product_id[]"  value="{{$data->id}}"></td>
                                                    <td>{{$data->name}}</td>
                                                    <td><input type="text" name="mrp_case_rate[]" value="{{!empty($explode_data[0])?$explode_data[0]:'0'}}" placeholder="Cases M.R.P."></td>
                                                    <td><input type="text" name="mrp_pcs_rate[]" value="{{!empty($explode_data[1])?$explode_data[1]:'0'}}" placeholder="M.R.P."></td>
                                                    <td><input type="text" name="ss_case_rate[]" value="{{!empty($explode_data[6])?$explode_data[6]:'0'}}" placeholder="Super Stockiest Case Rate"></td>
                                                    <td><input type="text" name="ss_pcs_rate[]" value="{{!empty($explode_data[7])?$explode_data[7]:'0'}}" placeholder="Super Stockiest Rate"></td>
                                                    <td><input type="text" name="dealer_case_rate[]" value="{{!empty($explode_data[2])?$explode_data[2]:'0'}}" placeholder="Distributor Case Rate"></td>
                                                    <td><input type="text" name="dealer_pcs_rate[]"  value="{{!empty($explode_data[3])?$explode_data[3]:'0'}}" placeholder="Distributor Rate"></td>
                                                    <td><input type="text" name="retailer_cases_rate[]" value="{{!empty($explode_data[4])?$explode_data[4]:'0'}}" placeholder="Retailer case Rate"></td>
                                                    <td><input type="text" name="retailer_pcs_rate[]" value="{{!empty($explode_data[5])?$explode_data[5]:'0'}}" placeholder="Retailer Rate"></td>

                                                
                                                    @if($data->product_type == 0)
                                                        <input type="hidden" name="other_retailer_rate_type[]" value="0" placeholder=" Rate">
                                                        <input type="hidden" name="other_dealer_rate_type[]" value="0" placeholder=" Rate">
                                                        <input type="hidden" name="product_type_id[]" value="0" placeholder=" Rate">

                                                    @else
                                                        @if(!empty($other_rate_ist_data[$data->id]))
                                                            <td>{{$other_rate_ist_data[$data->id]}} <input type="hidden" name="product_type_id[]" value="{{!empty($other_rate_ist_id_data[$data->id])?$other_rate_ist_id_data[$data->id]:''}}" placeholder="{{$other_rate_ist_data[$data->id]}} Rate"></td>
                                                            <td><input type="text" name="other_retailer_rate_type[]" value="{{!empty($explode_data[10])?$explode_data[10]:'0'}}" placeholder="{{$other_rate_ist_data[$data->id]}} Rate"></td>
                                                            <td><input type="text" name="other_dealer_rate_type[]" value="{{!empty($explode_data[8])?$explode_data[8]:'0'}}" placeholder="{{$other_rate_ist_data[$data->id]}} Rate"></td>
                                                            <td><input type="text" name="other_ss_rate_type[]" value="{{!empty($explode_data[9])?$explode_data[9]:'0'}}" placeholder="{{$other_rate_ist_data[$data->id]}} Rate"></td>
                                                        @endif
                                                    @endif
                                            
                                          
                                                </tr>
                                                     <?php $inc++;?>
                                             @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div><!-- /.span -->
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                </div>
                <div class="hr hr-18 dotted hr-double"></div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.page-content -->
</div>
@endsection

@section('js')
    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/catalog.js')}}"></script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>

    @if(Session::has('message'))
    <script>
        toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
    </script>
    @endif
@endsection