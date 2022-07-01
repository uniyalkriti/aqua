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

            
            <div class="row">
                <div class="col-xs-12">
                    
                    @if(!empty($product_rate_list_fetch))
                        {!! Form::open(array('route'=>[$current_menu.'.update',$encrypt_id] , 'method'=>'PUT','id'=>'catalog-form','enctype'=>'multipart/form-data','role'=>'form' ))!!}
                            <div class="hr hr-16 hr-dotted"></div>

                            <div class="row" style="overflow-x: scroll;">
                                <table id="simple-table" align="center" class="table table-bordered table-hover">
                                    <thead><tr><td colspan="13"><h3>Edit Product Details</h3></td></tr></thead>
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
                                        @if(count($check_assign_edit)>0)
                                            <th>Product Type</th>
                                            <th>Other {{Lang::get('common.retailer')}} Rate</th>
                                            <th>Other {{Lang::get('common.distributor')}} Rate</th>
                                        @endif


                                    </tr>
                                    <tbody>
                                      
                                        <?php $inc=1; ?>
                                         @if(!empty($product_rate_list_fetch))
                                       
                                            <tr>
                                                <td>{{$inc}}</td>

                                                <td>{{$product_rate_list_fetch->product_name}}</td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->mrp}}"  name="mrp_case_rate" placeholder="Cases M.R.P."></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->mrp_pcs}}"  name="mrp_pcs_rate" placeholder="M.R.P."></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->ss_case_rate}}"  name="ss_case_rate" placeholder="Super Stokiest Case Rate"></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->ss_pcs_rate}}" name="ss_pcs_rate" placeholder="Super Stokiest Rate"></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->dealer_rate}}"  name="dealer_case_rate" placeholder="Distributor Case Rate"></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->dealer_pcs_rate}}" name="dealer_pcs_rate" placeholder="Distributor Rate"></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->retailer_rate}}"  name="retailer_cases_rate" placeholder="Retailer case Rate"></td>
                                                <td><input type="text" required='required' value="{{$product_rate_list_fetch->retailer_pcs_rate}}" name="retailer_pcs_rate" placeholder="Retailer Rate"></td>
                                                @if(count($check_assign_edit)>0)
                                                    <td>{{!empty($type_name_details->type_name)?$type_name_details->type_name:'Not Seleted type in SKU'}} <input type="hidden" name="product_type_id" value="{{!empty($type_name_details->type_id)?$type_name_details->type_id:'0'}}" placeholder="{{!empty($type_name_details->type_name)?$type_name_details->type_name:'Not Seleted type in SKU'}}"></td>
                                                    <td><input type="text" required='required' value="{{$product_rate_list_fetch->other_retailer_rate}}" name="other_retailer_rate" placeholder="Retailer Rate"></td>
                                                    <td><input type="text" required='required' value="{{$product_rate_list_fetch->other_dealer_rate}}" name="other_dealer_rate" placeholder="Retailer Rate"></td>
                                                    
                                                @endif
                                            </tr>
                                         <?php $inc++;?>
                                             
                                            @endif
                                    </tbody>
                                </table>
                            </div><!-- /.span -->
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
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