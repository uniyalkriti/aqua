@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <style>
        .modal-lg2{
            width: 1070px;
        }
        .modal-lg3{
            width: 700px;
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

                    <li class="active" style="color: white">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>
        <?php 
        if(!empty($_GET['template']))
            $cstate_id=$_GET['template'];
        // dd($cstate_id)
            else
            $cstate_id[]=0;
        if(!empty($_GET['division']))
            $division_id=$_GET['division'];
            else
            $division_id=0;

        // dd($cstate_id);
        ?>
                

<!-- ......................table contents........................................... -->

        <div class="main-container ace-save-state" id="main-container">
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                        <form id="month_form" method="get">
                                <div class="col-md-2">
                                    <label>Template</label>
                                    <div class="">
                                       <select   name="template[]" id="template" class="form-control input-sm chosen-select" multiple="">
                                            <option value="">Select</option>
                                            @if(!empty($template_array))
                                                @foreach($template_array as $l1_key=>$l1_data)
                                                    <option {{in_array($l1_key,$cstate_id)?'selected':''}} value="{{$l1_key}}">{{$l1_data}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                 

                                
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                            style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                   </button> 
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

                                    
                                        <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">
                                        </div>
                                </div>
                            </div>
                            <div class="col-xs-12" >
                                <div class="clearfix">
                                    
                                    <div class="pull-right tableTools-container"></div>
                                </div>
                                <form class="form-horizontal open in" action="UploadTemmplate" method="POST" id="compliant" role="form"
                                  enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                    <div class="table-header center">
                                        <div class="pull-left">
                                            <input type="file" name="excelFile" id="file" multiple="multiple">
                                            
                                        </div>
                                        <div class="pull-left">
                                            <div class="">
                                                <input type="submit" name="submit" value="Upload" class="btn btn-sm btn-success btn-block mg-b-10"
                                                   >
                                            </div>
                                            
                                        </div>
                                 </form>
                                        <div class="col-xs-1"></div>
                                        <div class="pull-left">
                                            <a href="productRateListTemplateFormat" class="fa fa-file-excel-o" style="color: white;">
                                                Export Format
                                            </a>    
                                        </div>
                                        Product Rate List
                                        <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-success pull-right "><i
                                        class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.'.$current_menu)}}</a>
                                    </div>

                                
                                <?php 
                                $null_array = array();
                                ?>
                                @if(!empty($records))
                                    <table id="dynamic-table" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="center" rowspan="2" style="background-color: #fcf8e3;">S.No.</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">{{Lang::get('common.catalog_1')}}</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">{{Lang::get('common.catalog_2')}}</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">{{Lang::get('common.catalog_3')}}</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">{{Lang::get('common.catalog_product')}}</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">Mrp.</th>
                                                <th  rowspan="2" style="background-color: #fcf8e3;">Mrp. Pcs</th>
                                                @foreach($records as $h_key => $h_data)
                                                    <th colspan="9"  style="background-color: #fcf8e3; text-align: center;"> 
                                                        <a title="Assign Rate List"  onclick="confirmAction('Price List Assign ','IMEI','{{$h_data->id}}','product_rate_list_template','clear');">
                                                            <button type="button" class="btn btn-default btn-round btn-white">
                                                                <i class="ace-icon fa fa-send green"></i>
                                                                Assign {{$h_data->template_type}}
                                                            </button>
                                                        </a>

                                                        
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr>

                                                @foreach($records as $h_key => $h_data)
                                                    
                                                    @if($company_id != '52')
                                                    <?php 
                                                         $null_array[] = 'null,null,null,null,null,null,null,null,null';
                                                    ?>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.csa')}} Cases Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.csa')}} Pcs Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.csa')}} Primary Rate</th>
                                                    @else
                                                    <?php 
                                                         $null_array[] = 'null,null,null';
                                                    ?>
                                                    @endif
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.distributor')}} Cases Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.distributor')}} Pcs Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.distributor')}} Primary Rate</th>
                                                    @if($company_id != '52')
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.retailer')}} Cases Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.retailer')}} Pcs Rate</th>
                                                    <th style="background-color: #d9edf7;">{{Lang::get('common.retailer')}} Primary Rate</th>
                                                    @endif

                                                @endforeach
                                            </tr>
                                            <?php
                                                $null = implode(',', $null_array);
                                            ?>

                                        </thead>
                                        <tbody style="background-color: #dff0d8;">
                                            @foreach($sku_details as $b_key => $b_value)

                                            <tr>
                                                <td>{{$b_key+1}}</td>
                                                <td><span class="label label-xlg label-danger arrowed-in arrowed-in-right">{{$b_value->c0_name}}</td>
                                                <td>{{$b_value->c1_name}}</td>
                                                <td>{{$b_value->c2_name}}</td>
                                                <td><span class="label label-xlg label-info arrowed-in arrowed-in-right">{{$b_value->sku_name}}</span></td>

                                                <td>{{!empty($mrp_case_rate[$b_value->sku_id])?$mrp_case_rate[$b_value->sku_id]:'-'}}</td>
                                                <td>{{!empty($mrp_pcs_rate[$b_value->sku_id])?$mrp_pcs_rate[$b_value->sku_id]:'-'}}</td>
                                                @foreach($records as $b_r__key => $b_r_data)
                                                
                                                    @if($company_id != '52')
                                                    <td>{{!empty($csa_case_rate[$b_value->sku_id.$b_r_data->id])?$csa_case_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($csa_pcs_rate[$b_value->sku_id.$b_r_data->id])?$csa_pcs_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($csa_primary_rate[$b_value->sku_id.$b_r_data->id])?$csa_primary_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    @endif

                                                    <td>{{!empty($distributor_case_rate[$b_value->sku_id.$b_r_data->id])?$distributor_case_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($distributor_pcs_rate[$b_value->sku_id.$b_r_data->id])?$distributor_pcs_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($distributor_primary_rate[$b_value->sku_id.$b_r_data->id])?$distributor_primary_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>


                                                    @if($company_id != '52')
                                                    <td>{{!empty($retailer_case_rate[$b_value->sku_id.$b_r_data->id])?$retailer_case_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($retailer_pcs_rate[$b_value->sku_id.$b_r_data->id])?$retailer_pcs_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    <td>{{!empty($retailer_primary_rate[$b_value->sku_id.$b_r_data->id])?$retailer_primary_rate[$b_value->sku_id.$b_r_data->id]:'-'}}</td>
                                                    @endif
                                             

                                                
                                                @endforeach
                                                
                                            </tr>
                                                

                                                {{-- <a title="View Price List" template_type="{{$data->id}}"
                                                        data-toggle="modal" data-target="#view_pricelist"
                                                        class="btn btn-xs btn-warning view_pricelist">
                                                    <i class="ace-icon fa fa-eye bigger-120"></i>
                                                </a> --}}

                                            @endforeach
                                        </tbody>

                                    </table>
                                @endif
                                
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
<!-- ......................table ends contents...........................................  -->
    </div>
</div><!-- /.main-content -->

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="template_modal_state" role="dialog" style="overflow:scroll;">
    <div class="modal-dialog modal-lg2">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                {{--<div class="widget-header widget-header-small">--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">Assign Price List To @Lang('common.location3')</h4>
                {{--</div>--}}
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="post" id="filter_state_template" action="filter_state_template" enctype="multipart/form-data">
                    <input type="hidden" id="product_rate_list_template_type_state" name="product_rate_list_template_type_state" value="">
                    <div class="row">
                   
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right" for="location_1">{{Lang::get('common.location1')}}</label>
                                <select multiple name="location_1[]" id="location_1_state" class="form-control chosen-select-modal">
                                    <option value="">select</option>
                                    @if(!empty($location1))
                                        @foreach($location1 as $k=>$r)
                                            <option value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location2')}} </label>
                                <select multiple  name="location_2[]"
                                        id="location_2_state" class="form-control input-sm chosen-select-modal">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="name">{{Lang::get('common.location3')}}</label>
                                <select multiple name="location_3[]"
                                        id="location_3_state" class="form-control input-sm chosen-select-modal">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        

                    
                       
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                {{--<label class="control-label no-padding-right"></label>--}}
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <form action="state_template_assign" id="template_state_assign" method="POST">

                        <div class="col-md-12" id="result_state">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="template_modal_ss" role="dialog" style="overflow:scroll;">
    <div class="modal-dialog modal-lg2">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                {{--<div class="widget-header widget-header-small">--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">Assign Price List To  @Lang('common.csa')</h4>
                {{--</div>--}}
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="post" id="filter_csa_template" action="filter_csa_template" enctype="multipart/form-data">
                    <input type="hidden" id="product_rate_list_template_type_csa" name="product_rate_list_template_type_csa" value="">
                    <div class="row">
                   
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right" for="location_1">{{Lang::get('common.location1')}}</label>
                                <select multiple name="location_1[]" id="location_1_csa" class="form-control chosen-select-modal">
                                    <option value="">select</option>
                                    @if(!empty($location1))
                                        @foreach($location1 as $k=>$r)
                                            <option value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location2')}} </label>
                                <select multiple  name="location_2[]"
                                        id="location_2_csa" class="form-control input-sm chosen-select-modal">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location3')}} </label>
                                <select multiple  name="location_3[]"
                                        id="location_3_csa" class="form-control input-sm chosen-select-modal">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <label class="control-label no-padding-right"
                                   for="location_6"> {{Lang::get('common.csa')}} </label>
                            <select multiple name="csa[]" id="csa" class="form-control chosen-select-modal">
                                <option value="">select</option>
                                @if(!empty($csa))
                                    @foreach($csa as $k=>$r)
                                        <option value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                       
                       
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                {{--<label class="control-label no-padding-right"></label>--}}
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <form action="csa_template_assign" id="template_csa_assign" method="POST">
                        <div class="col-md-12" id="result_csa">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="template_modal_distributor" role="dialog" style="overflow:scroll;">
    <div class="modal-dialog modal-lg2">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                {{--<div class="widget-header widget-header-small">--}}
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">Assign Price List To  @Lang('common.distributor')</h4>
                {{--</div>--}}
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="post" id="filter_distributor_template" action="filter_distributor_template" enctype="multipart/form-data">
                    <input type="hidden" id="product_rate_list_template_type_distributor" name="product_rate_list_template_type_distributor" value="">
                    <div class="row">
                   
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right" for="location_1">{{Lang::get('common.location1')}}</label>
                                <select multiple name="location1[]" id="location_1" class="form-control chosen-select-modal-distributor">
                                    <option value="">select</option>
                                    @if(!empty($location1))
                                        @foreach($location1 as $k=>$r)
                                            <option value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location2')}} </label>
                                <select multiple  name="location2[]"
                                        id="location_2" class="form-control input-sm chosen-select-modal-distributor">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location3')}} </label>
                                <select multiple  name="location3[]"
                                        id="location_3" class="form-control input-sm chosen-select-modal-distributor">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location4')}} </label>
                                <select multiple  name="location4[]"
                                        id="location_4" class="form-control input-sm chosen-select-modal-distributor">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location5')}} </label>
                                <select multiple  name="location5[]"
                                        id="location_5" class="form-control input-sm chosen-select-modal-distributor">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="location_2"> {{Lang::get('common.location6')}} </label>
                                <select multiple  name="location_6[]"
                                        id="location_7" class="form-control input-sm chosen-select-modal-distributor">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <label class="control-label no-padding-right"
                                   for="location_6"> {{Lang::get('common.csa')}} </label>
                            <select multiple name="csa[]" id="csa" class="form-control chosen-select-modal-distributor">
                                <option value="">select</option>
                                @if(!empty($csa))
                                    @foreach($csa as $k=>$r)
                                        <option value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <label class="control-label no-padding-right"
                                   for="location_6"> {{Lang::get('common.distributor')}} </label>
                            <select multiple name="distributor[]" id="distributor" class="form-control chosen-select-modal-distributor">
                                <option value="">select</option>
                                @if(!empty($distributor))
                                    @foreach($distributor as $k=>$r)
                                        <option value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        
                        <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                {{--<label class="control-label no-padding-right"></label>--}}
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <form action="distributor_template_assign" id="template_distributor_assign" method="POST">
                        <div class="col-md-12" id="result_distributor">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade"  data-keyboard="false" id="view_pricelist" role="dialog" style="overflow:scroll;">
    <div class="modal-dialog modal-lg2">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="smaller">Price List</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="clearfix">
                    <div class="pull-right "></div>
                </div>
                <table  class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sr.no</th>
                            <th>{{Lang::get('common.catalog_product')}} Code</th>
                            <th>{{Lang::get('common.catalog_product')}} Name</th>
                            <th>{{Lang::get('common.catalog_3')}} </th>
                            <th>{{Lang::get('common.catalog_2')}} </th>
                            <th>{{Lang::get('common.catalog_1')}} </th>
                            <th>Cases MRP.</th>
                            <th>MRP.</th>
                            <th>{{Lang::get('common.csa')}} Cases Rate</th>
                            <th>{{Lang::get('common.csa')}} Rate</th>
                            <th>{{Lang::get('common.distributor')}} Cases Rate</th>
                            <th>{{Lang::get('common.distributor')}} Rate</th>
                            <th>{{Lang::get('common.retailer')}} Cases Rate</th>
                            <th>{{Lang::get('common.retailer')}} Rate</th>
                        </tr>
                    </thead>
                    <tbody class="mytbody_view_price_list">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
	        </div>
        </div>

    </div>
</div>


@endsection

@section('js')

    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <!-- ............................scripts for table ............................ -->
    <script type="text/javascript">
            if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.select.min.js')}}"></script>
    <!-- ace scripts -->
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>

    <script>
    $('.view_pricelist').click(function() {
        var template_type = $(this).attr('template_type');
        $('.mytbody_view_price_list').html('');
        // $('.mytbody_beat_details').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: domain + '/get_template_type',
                dataType: 'json',
                data: "template_type=" + template_type,
                success: function (data) 
                {
                    console.log(data);

                    if (data.code == 401) 
                    {
                       
                    }
                    else if (data.code == 200) 
                    {
                        var Sno = 1;
                        var template = '';
                        $.each(data.result, function (u_key, u_value) {
                            template += ('<tr><td>'+Sno+'</td><td>'+u_value.item_code+'</td><td>'+u_value.sku_name+'</td><td>'+u_value.c2_name+'</td><td>'+u_value.c1_name+'</td><td>'+u_value.c0_name+'</td><td>'+u_value.cases_mrp+'</td><td>'+u_value.mrp+'</td><td>'+u_value.ss_cases_rate+'</td><td>'+u_value.ss_rate+'</td><td>'+u_value.dealer_cases_rate+'</td><td>'+u_value.dealer_rate+'</td><td>'+u_value.retailer_cases_rate+'</td><td>'+u_value.retailer_rate+'</td></tr>');
                            Sno++;
                        });   
                        // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                        $('.mytbody_view_price_list').append(template);
                        // $('.mytbody_beat_details').append(template_beat);
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
    <script>
        $("#filter_state_template").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    target.html(data); // show response from the php script.
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });

        $("#filter_csa_template").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_csa');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    target.html(data); // show response from the php script.
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
        $("#filter_distributor_template").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_distributor');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    target.html(data); // show response from the php script.
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
        $("#template_distributor_assign").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    // alert('qwqw'); // show response from the php script.
                    // console.log(data);
                    if (data.code == 200) {
                        toastr.success(data.message);
                    }
                    else if(data.code == 234)
                    {
                        // toastr.success(data.message);
                    }
                    else{
                        toastr.success(data.message);
                        // toastr.error(data.message);
                    }
                    $('#template_modal_distributor').modal('toggle');

                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
        $("#template_csa_assign").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#template_modal_ss').modal('toggle');

                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
        $("#template_state_assign").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#template_modal_state').modal('toggle');
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });

        $(document).on('change', '#location_1', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

         $(document).on('change', '#location_3', function () {
            _current_val = $(this).val();
            dealer_data(_current_val,4);
        });
       
        $(document).on('change', '#location_4', function () {
            _current_val = $(this).val();
            location_data(_current_val,5);
        }); 
        $(document).on('change', '#location_5', function () {
            _current_val = $(this).val();
            location_data(_current_val,6);
        }); 
    
        // for state starts here
        $(document).on('change', '#location_1_state', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2_state', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        $(document).on('change', '#location_3_state', function () {
            _current_val = $(this).val();
            location_data(_current_val,4);
        });
        $(document).on('change', '#location_1_csa', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2_csa', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        $(document).on('change', '#location_3_csa', function () {
            _current_val = $(this).val();
            location_data(_current_val,4);
        });




        function location_data(val,level) {
            _append_box=$('#location_'+level);
            location_6 = $('#location_7');
            // console.log(location_6);
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/getLocationForAssign',
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
                               
                                    template += '<option value="' + key + '" >' + (value) + '</option>';
                             
                            });
                            _append_box.empty();
                            location_6.empty();
                            if(level == 6)
                            {
                                
                                location_6.append(template).trigger("chosen:updated");
                            }
                            else
                            {
                                 _append_box.empty();
                                _append_box.append(template).trigger("chosen:updated");
                            }
                            

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

                            $.ajax({
                                type: "POST",
                                url: domain + '/getLocationForAssign',
                                dataType: 'json',
                                data: "id=" + val+"&type="+level,
                                success: function (data) {
                                    if (data.code == 401) {
                                        //  $('#loading-image').hide();
                                    }
                                    else if (data.code == 200) {

                                        //Location 3
                                        $('#location_4').empty();
                                        template = '<option value="" >Select</option>';
                                        $.each(data.result, function (key, value) {
                                           
                                                template += '<option value="' + key + '" >' + (value) + '</option>';
                                         
                                        });
                                        
                                        $('#location_4').append(template).trigger("chosen:updated");
                                        
                                        

                                    }

                                },
                                complete: function () {
                                    // $('#loading-image').hide();
                                },
                                error: function () {
                                }
                            });
                            //Location 3
                            var template2 = '';
                            template2 = '<option value="" >Select</option>';
                            
                            $.each(data.result, function (key, value) {
                                if (value.name != '') {
                                    template2 += '<option value="' + key + '" >' + (value) + '</option>';
                                }
                            });
                            _append_box.empty();
                             _append_box.append(template2).trigger("chosen:updated");


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
    <script type="text/javascript">
      
        $('#template_modal_distributor').on('shown.bs.modal', function () {
          $('.chosen-select-modal-distributor', this).chosen();
        });
        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('#template_modal_ss').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
        $('#template_modal_state').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
            jQuery(function($) {
                //initiate dataTables plugin
                var myTable = 
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                .DataTable( {
                    bAutoWidth: false,
                    "aoColumns": [
                      { "bSortable": true },
                      null, null,null,null,null,<?= $null ?>,
                      { "bSortable": false }
                    ],
                    "aaSorting": [],
                    // "sScrollY": "1000px",
                        //"bPaginate": false,

                    "sScrollX": "100%",
                    // "sScrollXInner": "120%",
                    // "bScrollCollapse": true,
                    "iDisplayLength": 50,
                    
                    select: {
                        style: 'multi'
                    }
                } );
            
                
                
                $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
                
                new $.fn.dataTable.Buttons( myTable, {
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
                        "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
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
                        autoPrint: true,
                        message: 'This print was produced using the Print button for DataTables'
                      }       
                    ]
                } );
                myTable.buttons().container().appendTo( $('.tableTools-container') );
                
                //used for copy to clipboard
                var defaultCopyAction = myTable.button(1).action();
                myTable.button(1).action(function (e, dt, button, config) {
                    defaultCopyAction(e, dt, button, config);
                    $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
                });
                // end here copy clipboard option
                
                // used for search option
                var defaultColvisAction = myTable.button(0).action();
                myTable.button(0).action(function (e, dt, button, config) {
                    
                    defaultColvisAction(e, dt, button, config);
                    
                    
                    if($('.dt-button-collection > .dropdown-menu').length == 0) {
                        $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                    }
                    $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
                });
            // end here search option
            })
        </script>
        <!-- ends here  -->
    <script>
        function confirmAction(heading, name, action_id, tab, act) {
            // alert(action_id);
            $.confirm({
                title: heading,
                buttons: {
                    State: function () {
                        $("#product_rate_list_template_type_state").val(action_id);

                        $("#template_modal_state").modal();
                    },
                    SS: function () {
                        $("#template_modal_ss").modal();
                        $("#product_rate_list_template_type_csa").val(action_id);

                        
                    },
                    Distributor: function () {
                        $("#product_rate_list_template_type_distributor").val(action_id);
                        $("#template_modal_distributor").modal();
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
            if($('#search').val()!='')
            {
                $('#user-search').submit();
            }
        }
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
    <script>
        toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
    </script>
    
    @endif
@endsection