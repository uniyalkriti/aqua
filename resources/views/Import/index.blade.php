@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.import')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <style type="text/css">
          .transparent{
               color: transparent !important;
               background-color: #428bca;
               border: none;
               width: 1px;

            }
            input:active{
              background-color: #428bca;
            }
            button:active{
              background-color: #428bca;
            }
            .transparent1{
               /*color: transparent !important;*/
               background-color: #428bca;
               color: white;
            }
        .blink {
          animation: blink 2s steps(10, start) infinite;
          -webkit-animation: blink 1s steps(5, start) infinite;
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
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.import')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
              
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <form class="form-horizontal open in" action="UploadData" method="POST" id="compliant" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">
                                    
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right" for="name">File</label>
                                                <span id="checkTrialImport">
                                                <input type="file" name="excelFile" id="file" multiple="multiple">
                                                </span>
                                            </div>
                                        </div>
                                        @if($company_id != 56)
                                        <span class="errr"></span>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.distributor')}}</label><br>
                                           <button type="submit" name="submit" value="UploadDealer" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}} <input type="submit" name="submit" value="UploadDealer" class="transparent mg-b-1">
                                           </button>
                                            
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.retailer')}}</label>
                                             <button type="submit" name="submit" value="UploadRetailer" class="transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadRetailer" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.catalog_4')}}</label>
                                             <button type="submit" name="submit" value="UploadProduct" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadProduct" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                           <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.dealer_beat_assign')}}</label>
                                             <button type="submit" name="submit" value="UploadDealerBeat" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadDealerBeat" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.product_rate_list')}}</label>
                                             <button type="submit" name="submit" value="UploadProductRateList" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadProductRateList" class="transparent mg-b-1">
                                            </button>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.location7')}}</label>
                                             <button type="submit" name="submit" value="UploadLocation7" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                             <input type="submit" name="submit" value="UploadLocation7" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.location6')}}</label>
                                             <button type="submit" name="submit" value="UploadLocation6" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadLocation6" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.location5')}}</label>
                                             <button type="submit" name="submit" value="UploadLocation5" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadLocation5" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.location4')}}</label>
                                             <button type="submit" name="submit" value="UploadLocation4" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadLocation4" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.location3')}}</label>
                                             <button type="submit" name="submit" value="UploadLocation3" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadLocation3" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.user')}}</label>
                                             <button type="submit" name="submit" value="UploadUser" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadUser" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.user')}} {{Lang::get('common.distributor')}} {{Lang::get('common.location7')}}</label>
                                             <button type="submit" name="submit" value="UploadUserDealerBeat" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadUserDealerBeat" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                              <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} SS {{Lang::get('common.sku')}} Target</label>
                                             <button type="submit" name="submit" value="SuperStockiestSkuTarget" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="SuperStockiestSkuTarget" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        @if($company_id == 61)
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                 <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.distributor')}} {{Lang::get('common.sku')}} Target</label>
                                             <button type="submit" name="submit" value="DistributorTarget" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="DistributorTarget" class="transparent mg-b-1">
                                            </button>
                                            </div>
                                            @else
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                 <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.distributor')}} {{Lang::get('common.sku')}} Target</label>
                                             <button type="submit" name="submit" value="DistributorSkuTarget" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="DistributorSkuTarget" class="transparent mg-b-1">
                                            </button>
                                            </div>
                                        @endif
                                        

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.user')}} {{Lang::get('common.sku')}} Target</label>
                                             <button type="submit" name="submit" value="UserSkuTarget" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UserSkuTarget" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        <!-- <div class="col-md-1"></div>
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <input type="submit" name="submit" value="UserSkuTarget" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                                style="margin-top: 28px; width:150px;">
                                        </div> -->


                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        @if($company_id == 52)

                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.distributor')}} Credentials</label>
                                             <button type="submit" name="submit" value="UploadDealerCredentials" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="UploadDealerCredentials" class="transparent mg-b-1">
                                            </button>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} Scheme Plan</label>
                                             <button type="submit" name="submit" value="UploadSchemePlan" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="UploadSchemePlan" class="transparent mg-b-1">
                                            </button>
                                            </div>
                                        @endif

                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} User Tour Plan</label>
                                             <button type="submit" name="submit" value="UserTourPlan" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UserTourPlan" class="transparent mg-b-1">
                                            </button>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.primary_sale')}}</label>
                                             <button type="submit" name="submit" value="UploadPrimarySale" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadPrimarySale" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        @if($company_id == 52)
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} {{Lang::get('common.distributor')}} Personal Data</label>
                                             <button type="submit" name="submit" value="UploadDealerPersonalData" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="UploadDealerPersonalData" class="transparent mg-b-1">
                                            </button>
                                            </div>
                                           
                                        @endif


                                       <!--  <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <label class="control-label no-padding-right" for="name">Update Residential cordinate</label>
                                             <button type="submit" name="submit" value="UploadCordinate" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadCordinate" class="transparent mg-b-1">
                                            </button>
                                        </div> -->

                                        @else
                                            <input type="hidden" qwer="wq" name="submit" value="RamanujanExcelSheet">
                                            <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} Attendance Data</label>
                                                <input type="submit"  style="width: 100%;height: 30px;" name="submit" value="RamanujanExcelSheet" class="form-control btn btn-primary mg-b-1">
                                                <!-- <button type="submit" value="RamanujanExcelSheet" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}</button> -->
                                            </div>
                                            
                                        @endif

                                        @if($company_id == 44)

                                         <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} Scheme Plan</label>
                                             <button type="submit" name="submit" value="UploadSchemePlanNeha" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                            <input type="submit" name="submit" value="UploadSchemePlanNeha" class="transparent mg-b-1">
                                            </button>
                                        </div>

                                        @endif
                                       
                                        

                                    </div>


                                    <div class="row">
                                        <div class="col-md-2"></div>

                                         <div class="col-xs-6 col-sm-6 col-lg-2">
                                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.upload')}} CFA Stock</label>
                                             <button type="submit" name="submit" value="UploadCsaStock" class=" transparent1" style="width: 100%;height: 30px;">{{Lang::get('common.upload_button')}}
                                                <input type="submit" name="submit" value="UploadCsaStock" class="transparent mg-b-1">
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

                                
                                    <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">
                                    </div>
                            </div>
                        </div>


                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                   

                </div><!-- /.row -->
                @if($company_id != 56)
                 <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                            <h3 class="header smaller red">{{Lang::get('common.export_data')}} {{Lang::get('common.format')}} (Please Use CSV {{Lang::get('common.format')}} For Upload) </h3>
                                <div class="row">  
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="userFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.user')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="RetailerFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.retailer')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="DealerFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="DlrlFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} {{Lang::get('common.location7')}} Assign {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="userDealerAssignFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.user')}} {{Lang::get('common.distributor')}} Assign {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="CatalogFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.catalog_4')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    
                                </div>
                                 <br>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="ProductRateListFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.product_rate_list')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="BeatFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.location7')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                 
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="TownFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.location6')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="location5Format" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.location5')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="location4Format" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.location4')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="location3Format" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.location3')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    
                                </div>
                                <br>
                                <div class="row">

                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="superStockiestSkuWiseTargetFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.csa')}} {{Lang::get('common.sku_wise_target')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    @if($company_id == 61)
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <a href="exportTargetFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} {{Lang::get('common.sku_wise_target')}} {{Lang::get('common.format')}}
                                            </a>                  
                                        </div>
                                    @else
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <a href="distributorSkuWiseTargetFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} {{Lang::get('common.sku_wise_target')}} {{Lang::get('common.format')}}
                                            </a>                  
                                        </div>
                                    @endif
                                    

                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="UserSkuWiseTargetFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} {{Lang::get('common.user')}} {{Lang::get('common.sku_wise_target')}} {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>
                                    @if($company_id == 52)
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <a href="DistributorPersonalFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} Personal {{Lang::get('common.format')}}
                                            </a>                  
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <a href="DealerCredentailsFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} {{Lang::get('common.distributor')}} Credentials {{Lang::get('common.format')}}
                                            </a>                  
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <a href="SchemePlanFormat" class="fa fa-file-excel-o">
                                            {{Lang::get('common.export_data')}} Scheme Plan {{Lang::get('common.format')}}
                                            </a>                  
                                        </div>
                                    @endif
                                     <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="TourPlanFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} Tour Plan {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>


                                      <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="SchemePlanUploadFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} Scheme Plan {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>


                                    <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="PrimaryUploadFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} Primary Order {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>



                                     <div class="col-xs-6 col-sm-6 col-lg-2">
                                        <a href="CsaStockFormat" class="fa fa-file-excel-o">
                                        {{Lang::get('common.export_data')}} CFA Stock {{Lang::get('common.format')}}
                                        </a>                  
                                    </div>


                                    
                                     

                                </div>    
                                <br>

                                <div class="row">

                                     
                                </div>
                               
                                

                                  
                        <h3 class="header smaller red">{{Lang::get('common.export_data')}} Reference Data </h3>
                        <div class="row">
                            <div class="col-xs-4 col-sm-3 pricing-span-header">
                                <div class="widget-box transparent">
                                    <div class="widget-header">
                                        <h5 class="widget-title bigger">{{Lang::get('common.details')}}</h5>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <ul class="list-unstyled list-striped pricing-table-header">
                                                <li style="color:purple">{{'Click Here For Download'}}
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <i class="fa fa-hand-o-right blink" aria-hidden="true" style="font-size:28px;"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-8 col-sm-9 pricing-span-body">
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.retailer')}}  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="RetailerData" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>  {{Lang::get('common.retailer')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.distributor')}}  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="DealerData" class="btn btn-block btn-sm btn-primary">
                                                    <span>{{Lang::get('common.export_data')}}  <br> {{Lang::get('common.distributor')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-orange">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter"> {{Lang::get('common.catalog_3')}}  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ProductData" class="btn btn-block btn-sm btn-warning">
                                                    <span>{{Lang::get('common.export_data')}}  <br> {{Lang::get('common.catalog_3')}} </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.catalog_4')}} <br>  {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="SKUExport" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>  {{Lang::get('common.catalog_4')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.ownership_type')}} <br>  {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="OwnerData" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>  {{Lang::get('common.ownership_type')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.csa')}}  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="CsaData" class="btn btn-block btn-sm btn-primary">
                                                    <span>{{Lang::get('common.export_data')}}  <br> {{Lang::get('common.csa')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-orange">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location7')}}  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="BeatData" class="btn btn-block btn-sm btn-warning">
                                                    <span>{{Lang::get('common.export_data')}}  <br> {{Lang::get('common.location7')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.retailer')}} Type  <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="OutletTypeData" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>  {{Lang::get('common.retailer')}} Type Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.retailer')}} Class Type <br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="OutletClassTypeData" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>  {{Lang::get('common.retailer')}} Class Type</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>

                        </div><!-- PAGE CONTENT ENDS -->
                        <div class="row">
                            <div class="col-xs-4 col-sm-3 pricing-span-header">
                                <div class="widget-box transparent">
                                    <div class="widget-header">
                                        <h5 class="widget-title bigger">{{Lang::get('common.details')}}</h5>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <ul class="list-unstyled list-striped pricing-table-header">
                                                <li style="color:purple">{{'Click Here For Download'}}
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <i class="fa fa-hand-o-right blink" aria-hidden="true" style="font-size:28px;"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8 col-sm-9 pricing-span-body">
                                
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location6')}} <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="TownData" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.location6')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter"> {{Lang::get('common.location5')}}<br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="StateData" class="btn btn-block btn-sm btn-primary">
                                                    <span>{{Lang::get('common.export_data')}} <br> {{Lang::get('common.location5')}} </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-orange">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location4')}}<br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="location4_data" class="btn btn-block btn-sm btn-warning">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.location4')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location3')}} <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="StateId" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.location3')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location1')}}<br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="location1_data" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.location1')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.location2')}} <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="location2_data" class="btn btn-block btn-sm btn-primary">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.location2')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-orange">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.user')}}<br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="personExport" class="btn btn-block btn-sm btn-warning">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.user')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.role_key')}}<br> {{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="roleExport" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.role_key')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.product_type')}} <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="productUnitType" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br> {{Lang::get('common.product_type')}} Data</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-sm-3 pricing-span-header">
                                <div class="widget-box transparent">
                                    <div class="widget-header">
                                        <h5 class="widget-title bigger">{{Lang::get('common.details')}}</h5>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <ul class="list-unstyled list-striped pricing-table-header">
                                                <li style="color:purple">{{'Click Here For Download'}}
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <i class="fa fa-hand-o-right blink" aria-hidden="true" style="font-size:28px;"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8 col-sm-9 pricing-span-body">
                                
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">{{Lang::get('common.weight_type')}} <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="weightTypeMaster" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>{{Lang::get('common.weight_type')}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                 <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Scheme Plan <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ExportSchemePlanDetailsMaster" class="btn btn-block btn-sm btn-primary">
                                                    <span>{{Lang::get('common.export_data')}} <br>Scheme Plan</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-orange">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Task Of The Day <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ExportTaskOfTheDay" class="btn btn-block btn-sm btn-warning">
                                                    <span>{{Lang::get('common.export_data')}} <br>Task Of The Day</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Work Status <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ExportWorkStatus" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>Work Status</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-green">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Stock Share <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ExportStockFormat" class="btn btn-block btn-sm btn-success">
                                                    <span>{{Lang::get('common.export_data')}} <br>Stock Share Format</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($company_id == 57)

                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Retail Tracker <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="MaheshDocs/RetailerTrackerMahesh.xlsx" class="btn btn-block btn-sm btn-danger" target="_blank">
                                                    <span>{{Lang::get('common.export_data')}} <br>Retail Tracker</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-blue">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Tour Plan <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="MaheshDocs/TourPlanMahesh.xlsx" class="btn btn-block btn-sm btn-primary" target="_blank">
                                                    <span>{{Lang::get('common.export_data')}} <br>Tour Plan</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                 <!-- <div class="pricing-span">
                                    <div class="widget-box pricing-box-small widget-color-red3">
                                        <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Retail Tracker <br>{{Lang::get('common.export_data')}}</h5>
                                        </div>
                                        <div class="widget-body">
                                            <div>
                                                <a href="ExportOutletDataForMaheshNamkeen" class="btn btn-block btn-sm btn-danger">
                                                    <span>{{Lang::get('common.export_data')}} <br>Retail Tracker</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                                @endif


                            </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                @endif
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
   
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <!-- <script src="{{asset('msell/page/report81.js')}}"></script> -->
    <script src="{{asset('msell/js/common.js')}}"></script>
    @include('DashboardScript.commonModalScript')
    

@endsection