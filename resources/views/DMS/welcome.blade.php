@extends('layouts.adminMenuDms')

@section('title')
    <title>{{ config('app.name', '') }}</title>
@endsection
@section('css')
        <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
        <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
        <style>
        .modal-lg2{
            width: 1230px;
        }
        .modal-lg4{
            width: 1300px;
        }
        .modal-lg3{
            width: 1000px;
        }

        </style>
@endsection
@section('body')

    <div class="main-content" style="overflow-x: scroll;">
        <div class="main-content-inner">
            

            <div class="page-content">
                {{--<div class="ace-settings-container" id="ace-settings-container">
                    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                        <i class="ace-icon fa fa-cog bigger-130"></i>
                    </div>

                    <div class="ace-settings-box clearfix" id="ace-settings-box">
                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <div class="pull-left">
                                    <select id="skin-colorpicker" class="hide">
                                        <option data-skin="no-skin" value="#438EB9">#438EB9</option>
                                        <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                        <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                        <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                    </select>
                                </div>
                                <span>&nbsp; Choose Skin</span>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
                                <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
                                <label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
                                <label class="lbl" for="ace-settings-add-container">
                                    Inside
                                    <b>.container</b>
                                </label>
                            </div>
                        </div><!-- /.pull-left -->

                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
                                <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
                                <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
                                <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                            </div>
                        </div><!-- /.pull-left -->
                    </div><!-- /.ace-settings-box -->
                </div><!-- /.ace-settings-container -->--}}

                <div class="page-header">
                    <h1>
                        Dashboard
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            overview &amp; stats
                        </small>
                    </h1>
                </div>
                <!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                            <?php
                            if(isset($_GET['date_range_picker']))
                            {
                                $year = $mdate;
                                
                            }
                            else {
                                $year = date('Y-m');
                            }
                            // $from_date = $year."-01";
                            // $to_date = date('Y-m-d');
                            // $yearMonth = date('M-Y',strtotime($year));
                            $location_3_filter = !empty($location_3_filter)?$location_3_filter:array();
                            $location_3_filter = implode(',', $location_3_filter);

                            $division_filter = !empty($division_filter)?$division_filter:array();
                            $division_filter = implode(',', $division_filter);

                            ?>
                        <!-- PAGE CONTENT BEGINS -->
                        {{-- <div class="alert alert-block alert-success">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>

                            <i class="ace-icon fa fa-check green"></i>

                            Welcome to
                            <strong class="green">
                                mSELL <small></small>
                            </strong>,<a href="https://msell.in/web/">Manacle Technologies Pvt Ltd</a>
                        </div> --}}
                        
                        <div class="row">
                           <div class="col-md-12" style="text-align: center">
                                <span class="label label-success arrowed-in arrowed-in-right" style="width:200px">{{ $from_date .' To '. $to_date }}</span>
                           </div>

                                <form class="form-horizontal open collapse in" action="home" method="GET" id="homedata1" role="form"
                                enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                
                                    

                                </form>

                        </div>
                       
                        <div class="row">
                            <div class="space-12"></div>
                            {{-- <div class="col-sm-1"></div> --}}

                            <div class="col-sm-12 infobox-container">
                               
                                <div class="infobox infobox-orange infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-users"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{!empty($depo_count)?$depo_count:'0'}}</div>
                                            <a title="Sales Teams" data-toggle="modal" data-target="#user_details_modal" class="user-modal totalSalesTeam">
                                            <div class="infobox-content">Total Depo</div>
                                            </a>
                                        </div>
                                </div>
                                
    
                                <div class="infobox infobox-blue infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{!empty($totalDistributor)?$totalDistributor:'0'}}</div>
                                        <a title="Distributor List"  data-toggle="modal" data-target="#distributor-modal" class="distributor-modal dealerDetailsHome">
                                        <div class="infobox-content">Total Distributors</div>
                                        </a>
                                    </div>
                                </div>

                                
                                <div class="infobox infobox-red infobox-large infobox-dark">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-users"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{!empty($totalDistributor)?$totalDistributor:'0'}}</div>
                                        <div class="infobox-content">Today's Login Details</div>
                                        
                                    </div>
                                </div>

                                <div class="infobox infobox-grey infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                             <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{!empty($dealer_active_status)?$dealer_active_status:'0'}}</div>
                                            <div class="infobox-content"> Active Distributors</div>
                                        </div>
                                </div>
                               
                                <div class="infobox infobox-purple infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                             <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div class="infobox-data-number">{{!empty($dealer_deactive_status)?$dealer_deactive_status:'0'}}</div>
                                            <div class="infobox-content"> De-Active Distributors</div>
                                        </div>
                                    </div>
                                    <div class="infobox infobox-pink infobox-large infobox-dark">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-shopping-basket"></i>
                                        </div>
    
                                        <div class="infobox-data" >

                                            <div class="infobox-data-number" id="after_click">{{!empty($demand_order_count)?($demand_order_count):'0'}}</div>
                                            <div class="infobox-data-number" id="on_load"></div>
                                            <div class="infobox-content">Today's Demand  Count</div>
                                        </div>
                                    </div>

                                <br>

                                <div class="infobox infobox-orange infobox-large infobox-light">
                                    <div class="infobox-icon">
                                        <i class="ace-icon fa fa-mobile"></i>
                                    </div>

                                    <div class="infobox-data">
                                        <div class="infobox-data-number">{{!empty($invoice_details_count)?$invoice_details_count:'0'}}</div>
                                         <a title=" Today's attendance log"  data-toggle="modal" data-target="#attendance_modal" class="user-modal">
                                        <div class="infobox-content">Today's Invoice Count</div>
                                        </a>
                                    </div>
                                </div>

                                

                                <div class="infobox infobox-blue infobox-large infobox-light">
                                        <div class="infobox-icon">
                                            <i class="ace-icon fa fa-mobile"></i>
                                        </div>
    
                                        <div class="infobox-data">
                                            <div id="dealersale" class="infobox-data-number">{{!empty($credit_note_count)?$credit_note_count:'0'}}</div>
                                           
                                                <div class="infobox-content">Today's Credit Note Count</div>
                                            
                                        </div>
                                    </div>
                                    
                                 <!--    <div class="infobox infobox-red infobox-large infobox-light">
                                            <div class="infobox-icon">
                                                <i class="ace-icon fa fa-mobile"></i>
                                            </div>
        
                                            <div class="infobox-data">
                                                <div id="outletsale" class="infobox-data-number">{{!empty($totalOutletSale->outletsale)?$totalOutletSale->outletsale:'0'}}</div>
                                                <a title="Outlet List"  data-toggle="modal" data-target="#retailer-coverage-modal" class="retailer-coverage-modal retailerCovergaeDetailsHome">
                                                    <div class="infobox-content">Outlet Covered</div>
                                                </a>
                                                
                                            </div>
                                        </div>
                                        <div class="infobox infobox-grey infobox-large infobox-light">
                                                <div class="infobox-icon">
                                                    <i class="ace-icon fa fa-mobile"></i>
                                                </div>
            
                                                <div class="infobox-data">
                                                    <div id="beatsale" class="infobox-data-number">{{!empty($totalBeatSale->beatsale)?$totalBeatSale->beatsale:'0'}}</div>
                                                    <a title="Beat Coverage Modal"  data-toggle="modal" data-target="#beat_coverage_modal" class="user-modal totalBeatCoverage">
                                                    <div class="infobox-content">Beat Coverage</div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="infobox infobox-purple infobox-large infobox-light">
                                                    <div class="infobox-icon">
                                                        <i class="ace-icon fa fa-phone"></i>
                                                    </div>
                
                                                    <div class="infobox-data">
                                                        {{-- <div class="infobox-data-number">{{!empty($productiveCall->productive_call)?$productiveCall->productive_call:'0'}}</div> --}}
                                                        <div class="infobox-data-number">{{!empty($productiveCall)?$productiveCall:'0'}}</div>
                                                        <a title="Productive Call Modal"  data-toggle="modal" data-target="#product_call_modal" class="user-modal totalProductiveCoverage">
                                                        <div class="infobox-content">Productive Call</div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- <div class="infobox infobox-pink infobox-large infobox-light">
                                                        <div class="infobox-icon">
                                                            <i class="ace-icon fa fa-shopping-basket"></i>
                                                        </div>
                    
                                                        <div class="infobox-data">
                                                            <div class="infobox-data-number">{{!empty($totalPrimaryOrder->total_sale_value)?$totalPrimaryOrder->total_sale_value:'0'}}</div>
                                                            <a title="State wise sale"  data-toggle="modal" data-target="#primaryModal" class="user-modal primaryStateWiseDetails">
                                                            <div class="infobox-content">Primary Booking</div>
                                                            </a>
                                                            
                                                        </div>
                                                </div> -->
                                              
                                          
                                              
                                               
                               
                                

                            </div>
                      
                            <div class="vspace-12-sm"></div>
                           
                            <!-- /.col -->
                        </div><!-- /.row -->

                        <div class="hr hr32 hr-dotted"></div>
                        
                       
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div>

<!-- ends here for sale -->


<!-- Modal here for user detals -->
<div class="modal fade" id="user_details_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Sales Team Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>User Name</th>
                        <th>Mobile</th>
                        <th>Designation</th>
                        <th>State</th>
                    </thead>
                    <tbody class="mytbody_totalsalesteam">
                        
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
<div class="modal fade" id="product_call_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Productive Call Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>User Name</th>
                        <th>Mobile</th>
                        <th>No Of Productive Call</th>
                        <th>Total sale Value</th>
                    </thead>
                    <tbody class="mytbody_productive_details">
                        
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
<div class="modal fade" id="beat_coverage_modal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Beat coverage Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Beat Name</th>
                        <th>Total Sale Value</th>
                    </thead>

                    <tbody class="tbody_totalbeat_coverage">
                        
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
<!-- division wise sale modal starts here -->
<div class="modal fade" id="division_sale_modal11" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Users Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead class = "mythead">

                    </thead>
                    <tbody class="mytbody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- division wise sale modal ends here -->

<div class="modal fade" id="sluggish_retailer_modal" role="dialog">
    <div class="modal-dialog modal-lg3" >
    
        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title center" ><b><u>Inactive Retailers</u></b></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>Sno</td>
                            
                            <td>{{Lang::get('common.location3')}} Name</td>
                            <td>{{Lang::get('common.distributor')}} Name</td>
                            <td>{{Lang::get('common.location7')}} Name</td>
                            <td>{{Lang::get('common.retailer')}} Name</td>
                            <td>Contact Person Name</td>
                            <td>Retailer Number</td>

                        </tr>
                    </thead>
                    <tbody class="mytbody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="distributor-modal" role="dialog">
    <div class="modal-dialog modal-lg2" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Distributor Details
                        <a onclick="fnExcelReportDistributorDetails()" href="javascript:void(0)" class="nav-link">
                        <i class="fa fa-file-excel-o "></i> Export Excel</a>
                </h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover" id="simple-table-distributor-details">
                    <thead >
                        <th>Sr.no</th>
                        <th>Distributor State</th>
                        <th>Distributor Town</th>
                        <th>Distributor Name</th>
                        <th>Distributor Mobile No.</th>
                        <th>Distributor Email</th>
                        <th>Distributor Code</th>
                        <th>Distributor Wise Beat Count</th>
                        <th>Distributor Wise Retailer Count</th>
                        <th>Distributor Assigned User Name</th>
                    </thead>

                    <tbody class="tbody_dealer_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="distributor-coverage-modal" role="dialog">
    <div class="modal-dialog modal-lg2" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Distributor Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>Sr.no</th>
                        <th>Distributor State</th>
                        <th>Distributor Town</th>
                        <th>Distributor Name</th>
                        <th>Distributor Mobile No.</th>
                        <th>Distributor Wise Beat Count</th>
                        <th>Distributor Wise Retailer Count</th>
                        <th>Distributor Assigned User Name</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_dealer_coverage_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="retailer-modal" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Outlet Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>Sr.no</th>
                        <th>Retailer State</th>
                        <th>Retailer Town</th>
                        <th>Retailer Beat</th>
                        <th>Distributor Name</th>
                        <th>Retailer Name</th>
                        <th>Retailer Mobile No.</th>
                        <th>Retailer Assigned User Name</th>
                        <th>Retailer Added By</th>
                        <th>Retailer Created On</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_retailer_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="retailer-coverage-modal" role="dialog">
    <div class="modal-dialog modal-lg4" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Outlet Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>Sr.no</th>
                        <th>Retailer State</th>
                        <th>Retailer Town</th>
                        <th>Retailer Beat</th>
                        <th>Distributor Name</th>
                        <th>Retailer Name</th>
                        <th>Retailer Mobile No.</th>
                        <th>Retailer Assigned User Name</th>
                        <th>Retailer Added By</th>
                        <th>Retailer Created On</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_retailer_coverage_details_home">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="beat-modal" role="dialog">
    <div class="modal-dialog modal-lg3" >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Beat Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>Sr.no</th>
                        <th>State</th>
                        <th>Town</th>
                        <th>Beat</th>
                        <th>Distributor Count</th>
                        <th>Outlet Count</th>
                    </thead>

                    <tbody class="tbody_beat_details">
                        
                    </tbody>
                    <!-- <tfoot class="tfoot_beat_details">
                        
                    </tfoot> -->
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="totalcall-modal" role="dialog">
    <div class="modal-dialog " >
    
        <!-- Modal content-->
        <div class="modal-content" style="overflow-y: scroll; overflow-x: scroll;">
            <div class="modal-header" style="background-color: #fcf8e3;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"  >Calls Details</h4>
            </div>
            <div class="modal-body" >
                <table class="table table-bordered table-hover">
                    <thead >
                        <th>Sr.no</th>
                        <th>Date</th>
                        <th>Total Call Count</th>
                        <th>Productive Call Count</th>
                        <th>Sale Value</th>
                    </thead>

                    <tbody class="tbody_total_calls_details">
                        
                    </tbody>
                    <!-- <tfoot class="tfoot_beat_details">
                        
                    </tfoot> -->
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal here for user detals -->
<div class="modal fade" id="distributorPrimaryModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Distributor Primary  Sales Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Distributor Name</th>
                        <th>Distributor Code</th>
                        <th>Mobile</th>
                        <th>Primary Sale Value</th>
                    </thead>
                    <tbody class="mytbody_distributorPrimaryModal">
                        
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
<div class="modal fade" id="distributorProductPrimaryModal" role="dialog">
    <div class="modal-dialog" >
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" >Distributor Product Primary  Sales Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sr.no</th>
                        <th>Product Name</th>
                        <th>Total Primary Sale</th>
                    </thead>
                    <tbody class="mytbody_distributorProductPrimaryModal">
                        
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
@endsection
@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.easypiechart.min.js')}}"></script>    
    <script src="{{asset('msell/js/jquery.flot.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.resize.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/BarChart.js')}}"></script>

     

    <script type="text/javascript">
        $('.distributorPrimaryModal').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_distributorPrimaryModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getTotalDistributorPrimarySales',
                dataType: 'json',
                data: "state_id=" + state_id+ "&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.user_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;

                                // $('.mytbody_distributorPrimaryModal').append("<tr><td>"+Sno+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.dealer_code+"</td><td>"+value.mobile+"</td><td>"+value.total_sale_value+"</td></tr>");
                                var cell ='<td id="attributes" onclick=product_details('+value.dealer_id+'); from_date='+from_date+' to_date='+to_date+'><a title="Product Wise Primary" data-toggle="modal" data-target="#distributorProductPrimaryModal" class="distributorProductPrimaryModal">'+value.total_sale_value+'</a></td>';

                                 $('.mytbody_distributorPrimaryModal').append("<tr><td>"+Sno+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.dealer_code+"</td><td>"+value.mobile+"</td>"+cell+"</tr>");
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
        function product_details(dealer_id) {

        // $('.distributorProductPrimaryModal').click(function() {
        //   // var state_id = $(this).attr('state_id'); 
          var from_date = $('#attributes').attr('from_date'); 
          var to_date = $('#attributes').attr('to_date'); 
          // var dealer_id = $('#attributes').attr('dealer_id'); 
          // alert(from_date);

        $('.mytbody_distributorProductPrimaryModal').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getTotalDistributorProductPrimarySales',
                dataType: 'json',
                data: "&from_date=" + from_date+ "&to_date=" + to_date+ "&dealer_id="+ dealer_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.user_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_distributorProductPrimaryModal').append("<tr><td>"+Sno+"</td><td>"+value.product_name+"</td><td>"+value.total_sale_value+"</td></tr>");
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
    };

    </script>



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
   
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


   <script>
        $('#to_date').datetimepicker
        ({
        format: 'YYYY-MM-DD'
        }).on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(0, 'days');
            $('#from_date').data('DateTimePicker').maxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });
        $("#year").datetimepicker  ( {

            format: 'YYYY-MM'
        });
    </script>
       <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function($) {
        $('.stateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var division_id = $(this).attr('division_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                        $('.easy-pie-chart.percentage').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                                var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                                var size = parseInt($(this).data('size')) || 50;
                                $(this).easyPieChart({
                                    barColor: barColor,
                                    trackColor: trackColor,
                                    scaleColor: false,
                                    lineCap: 'butt',
                                    lineWidth: parseInt(size/10),
                                    animate: ace.vars['old_ie'] ? false : 1000,
                                    size: size
                                });
                            })
                        
                            $('.sparkline').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                                $(this).sparkline('html',
                                                 {
                                                    tagValuesAttribute:'data-values',
                                                    type: 'bar',
                                                    barColor: barColor ,
                                                    chartRangeMin:$(this).data('min') || 0
                                                 });
                            });
                        
                        
                          //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
                          //but sometimes it brings up errors with normal resize event handlers
                          $.resize.throttleWindow = false;
                        
                          var placeholder = $('#piechart-placeholder').css({'width':'50%' , 'height':'300px'});
                          var data = data.beat_query;
                          function drawPieChart(placeholder, data, position) {
                              $.plot(placeholder, data, {
                                series: {
                                    pie: {
                                        show: true,
                                        tilt:0.8,
                                        highlight: {
                                            opacity: 0
                                        },
                                        stroke: {
                                            color: '#fff',
                                            width: 2
                                        },
                                        startAngle: 2
                                    }
                                },
                                legend: {
                                    show: true,
                                    position: position || "ne",
                                    labelBoxBorderColor: null,
                                    margin:[-200,3]
                                }
                                ,
                                grid: {
                                    hoverable: true,
                                    clickable: true
                                }
                             })
                         }
                         drawPieChart(placeholder, data);
                        
                         /**
                         we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
                         so that's not needed actually.
                         */
                         placeholder.data('chart', data);
                         placeholder.data('draw', drawPieChart);
                        
                        
                          //pie chart tooltip example
                          var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
                          var previousPoint = null;
                        
                        placeholder.on('plothover', function (event, pos, item) {
                            if(item) {
                                if (previousPoint != item.seriesIndex) {
                                    previousPoint = item.seriesIndex;
                                    var tip = item.series['label'] + " : " + Math.round(item.series['percent'])+'%';
                                    $tooltip.show().children(0).text(tip);
                                }
                                $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
                            } else {
                                $tooltip.hide();
                                previousPoint = null;
                            }
                            
                        });
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script type="text/javascript">
        jQuery(function($) {
        $('.primaryStateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var division_id = $(this).attr('division_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_primary_booking_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                        $('.easy-pie-chart.percentage').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                                var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                                var size = parseInt($(this).data('size')) || 50;
                                $(this).easyPieChart({
                                    barColor: barColor,
                                    trackColor: trackColor,
                                    scaleColor: false,
                                    lineCap: 'butt',
                                    lineWidth: parseInt(size/10),
                                    animate: ace.vars['old_ie'] ? false : 1000,
                                    size: size
                                });
                            })
                        
                            $('.sparkline').each(function(){
                                var $box = $(this).closest('.infobox');
                                var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                                $(this).sparkline('html',
                                                 {
                                                    tagValuesAttribute:'data-values',
                                                    type: 'bar',
                                                    barColor: barColor ,
                                                    chartRangeMin:$(this).data('min') || 0
                                                 });
                            });
                        
                        
                          //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
                          //but sometimes it brings up errors with normal resize event handlers
                          $.resize.throttleWindow = false;
                        
                          var placeholder = $('#piechart-placeholder-primary').css({'width':'50%' , 'height':'300px'});
                          var data = data.beat_query;
                          function drawPieChart(placeholder, data, position) {
                              $.plot(placeholder, data, {
                                series: {
                                    pie: {
                                        show: true,
                                        tilt:0.8,
                                        highlight: {
                                            opacity: 0
                                        },
                                        stroke: {
                                            color: '#fff',
                                            width: 2
                                        },
                                        startAngle: 2
                                    }
                                },
                                legend: {
                                    show: true,
                                    position: position || "ne",
                                    labelBoxBorderColor: null,
                                    margin:[-200,3]
                                }
                                ,
                                grid: {
                                    hoverable: true,
                                    clickable: true
                                }
                             })
                         }
                         drawPieChart(placeholder, data);
                        
                         /**
                         we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
                         so that's not needed actually.
                         */
                         placeholder.data('chart', data);
                         placeholder.data('draw', drawPieChart);
                        
                        
                          //pie chart tooltip example
                          var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
                          var previousPoint = null;
                        
                        placeholder.on('plothover', function (event, pos, item) {
                            if(item) {
                                if (previousPoint != item.seriesIndex) {
                                    previousPoint = item.seriesIndex;
                                    var tip = item.series['label'] + " : " + Math.round(item.series['percent'])+'%';
                                    $tooltip.show().children(0).text(tip);
                                }
                                $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
                            } else {
                                $tooltip.hide();
                                previousPoint = null;
                            }
                            
                        });
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script>

    $('.sluggish_retailer').click(function() {
          var state_id = $(this).attr('state_id_data'); 
          var from_date = $(this).attr('from_date'); 
          var flag = $(this).attr('flag'); 

        $('.mytbody').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/sluggish_retailer_list',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&flag=" + flag,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.retailer_result, function (key, value){
                                // console.log(value);
                                $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.dealer_name+"</td><td>"+value.l7_name+"</td><td>"+value.retailer_name+"</td><td>"+value.contact_per_name+"</td><td>"+(value.landline)+"</td></tr>");
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

    $('.totalSalesTeam').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          

        $('.mytbody_totalsalesteam').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getTotalSalesTeamHome',
                dataType: 'json',
                data: "state_id=" + state_id+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.user_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_totalsalesteam').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.role+"</td><td>"+value.state+"</td></tr>");
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
    $('.totalBeatCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_totalbeat_coverage').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_beat_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.beat_coverage_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_totalbeat_coverage').append("<tr><td>"+Sno+"</td><td>"+value.beat+"</td><td>"+value.total_sale_value+"</td></tr>");
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

    $('.totalProductiveCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_productive_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_productive_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.productve_call_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_productive_details').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.call_status_count+"</td><td>"+value.total_sale_value+"</td></tr>");
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

    $('.dealerDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_dealer_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_dealer_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_dealer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_email+"</td><td>"+value.dealer_code+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td></tr>");
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
    $('.dealerCoverageDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_dealer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_dealer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_dealer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
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
    $('.retailerDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.retailerCovergaeDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.beatDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_beat_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_beat_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.beat_details, function (key, value){
                                // console.log(value)<td>"+value.sale_value+"</td>;
                                total_sale +=  parseInt(value.sale_value);
                                $('.tbody_beat_details').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td>"+value.dealer_count+"</td><td>"+value.retailer_count+"</td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);
                            
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.totalCallDetails').click(function() {
          var state_id = $(this).attr('state_id'); 
          var division_id = $(this).attr('division_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_total_calls_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_total_call_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.total_call_details, function (key, value){
                                // console.log(value);
                                // total_sale +=  parseInt(value.sale_value);
                                $('.tbody_total_calls_details').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.retailer_count+"</td><td>"+value.productive_count+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);
                            
                           
                       
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
              $( document ).ready(function() {
            var state_id = <?=json_encode($location_3_filter)?>;
            var division_id = <?=json_encode($division_filter)?>;
            var from_date = <?=json_encode($from_date)?>;
            var to_date = <?=json_encode($to_date)?>;

                
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
       
                // console.log(value.role_id);
                $.ajax({
                    type: "get",
                    url: domain + '/get_dealer_outlet_beat_coverage',
                    dataType: 'json',
                    data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&division_id=" + division_id,
                    // data: "role_id=" + value.role_id,
                    success: function (data) {
                        $.each(data.result, function (r_key, r_value){
                            console.log(data);

                              $("#"+r_key).html('');
                                var append_value = r_value;
                                $("#"+r_key).append(append_value);

                            // if(r_value.year == '2019')
                            // {
                            //     $("#2019"+value.role_id).html('');
                            //     var append_value = r_value.sale_value;
                            //     $("#2019"+value.role_id).append(append_value);

                            // }
                            // else
                            // {
                            //     $("#2020"+value.role_id).html('');
                            //     var append_value2 = r_value.sale_value;
                            //     $("#2020"+value.role_id).append(append_value2);
                            // }
                        });
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
        });

    </script>
 
    <!-- ############### PIE Chart Script Ends Here ################### -->



    <script type="text/javascript">
        function fnExcelReportDistributorDetails() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('simple-table-distributor-details'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
    }
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}
    </script>
@endsection