@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />

<style>
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
    #simple-table td {
        height: 30px;
    }
</style>
<style>

</style>
@endsection

@section('body')
 <form class="form-horizontal open collapse in"  method="get" id="manualAttandence" enctype="multipart/form-data">

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
            </div> <!-- /.nav-search -->

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    
                                    <input type="hidden" name="flag" value="1">

                                 <div class="col-md-2">
                                    <label class="control-label no-padding-right" for="id-date-range-picker-1">Date Range Picker</label>       
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar bigger-110"></i> 
                                        </span>

                                        <input class="form-control" {{ Request::get('date_range_picker')?'selected':''}} type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                    </div>
                                </div>
                               
                                
                                 <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"  name="find" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                        Find
                                    </button>
                                </div>
                                <br>
                                <br>
                                <br>
                                <br>
                             </form>
                        <div class="hr hr-18 dotted hr-double"></div>

                        @if(!empty($records))
<!-- ......................table contents........................................... -->
                                <div class="main-container ace-save-state" id="main-container">
                                    <div class="main-content">
                                        <div class="main-content-inner">
                                            <div class="page-content">
                                            <!--  -->
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="clearfix">
                                                            <div class="pull-right tableTools-container"></div>
                                                        </div>
                                                        <!-- div.table-responsive -->
                                                        <!-- div.dataTables_borderWrap -->
                                                            <div>
                                                                <table id="dynamic-table" class="table table-striped table-bordered table-hover" >
                                                                    <thead>
                                                                    <tr>
                                                                        <th>S.No.</th>
                                                                        <th>Current Status</th>
                                                                        <th>Action</th>
                                                                        <th>Retailer Name</th>
                                                                        <th>Retailer Mobile No.</th>
                                                                        <th>Order Date</th> 
                                                                        <th>Order No</th>
                                                                        <th>Order Value</th>
                                                                        <th>Order Cases</th>
                                                                        <th>Order PCS</th>
                                                                        <th>Details</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $value12 = array(); ?>

                                                                    @if(!empty($records) && count($records)>0)
                                                                    
                                                                    @foreach($records as $k=> $r)
                                                                    @if(count($r->order_id)>0)
                                                                   
                                                                        <?php
                                                                        $encid = Crypt::encryptString($r->order_id); 
                                                                        $retailer_id = Crypt::encryptString($r->retailer_id); 
                                                                        ?>
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td>{{!empty($dms_status_order_query[$r->dms_order_reason_id])?$dms_status_order_query[$r->dms_order_reason_id]:'Pending'}}</td>
                                                                            <td>
                                                                            <a title="Assign Rate List"  onclick="confirmAction('Action','IMEI','{{$r->order_id}}','product_rate_list_template','clear');">
                                                                                <button type="button" class="btn btn-default btn-round btn-white">
                                                                                    <i class="ace-icon fa fa-send green"></i>
                                                                                    Action 
                                                                                </button>
                                                                            </a>
                                                                            </td>
                                                                            <td><a href="{{url('Retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
                                                                            <td>{{$r->mobile_number}}</td>
                                                                            <td>{{$r->sale_date}}</td>
                                                                            <td>{{$r->order_id}}</td>
                                                                            <td>{{$r->total_vale}}</td>
                                                                            <td>{{$r->cases}}</td>
                                                                            <td>{{$r->pcs}}</td>
                                                                            <td>
                                                                                <table class="table">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Product Name</th> 
                                                                                            <th>Pcs rate</th>
                                                                                            <th>Quantity</th>
                                                                                            <th>Cases Rate</th>
                                                                                            <th>Cases</th>
                                                                                            <th>Value</th>  
                                                                                        </tr>
                                                                                    </thead>
                                                                                    @if(!empty($r))
                                                                                        <?php  $i++; $total=0;
                                                                                        $totalqty=0;
                                                                                        $totacases=0;
                                                                                        $totalweight=0; ?>
                                                                                        @foreach($details[$r->order_id] as $k1=>$data1)
                                                                                        <?php $value = 0; ?>
                                                                                            <tr>
                                                                                                <td>{{$data1->product_name}}</td>
                                                                                                <td>{{$data1->rate}}</td>
                                                                                                <td>{{$data1->quantity}}</td>
                                                                                                
                                                                                                <td>{{$data1->cases_rate}}</td>
                                                                                                <td>{{$data1->cases}}</td>
                                                                                               
                                                                                                <td>{{($data1->rate*$data1->quantity)+($data1->cases_rate*$data1->cases)}}</td> 
                                                                                            </tr>
                                                                                             <?php 
                                                                                             $total+=($data1->rate*$data1->quantity)+($data1->cases_rate*$data1->cases);
                                                                                             $totalqty+=$data1->quantity;
                                                                                             $totacases+=$data1->cases;
                                                                                             // $totalweight+=$data1->weight;

                                                                                           
                                                                                             ?>
                                                                                        @endforeach
                                                                                    @else
                                                                                        <tr>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td> 
                                                                                            <td>-</td> 
                                                                                        </tr>
                                                                                   @endif
                                                                                   <tfoot>
                                                                                     
                                                                                        <tr>
                                                                                            <th>Total</th>
                                                                                            <th></th>
                                                                                            <th>{{$totalqty}}</th>
                                                                                            <th></th>
                                                                                            <th>{{$totacases}}</th>
                                                                                            <th>{{$total}}</th>
                                                                                        </tr>
                                                                                    </tfoot>
                                                                                </table>
                                                                            </td>
                                                                         

                                                                                
                                                                            </tr>
                                                                            @endif
                                                                            <?php $i++; ?>
                                                                            @endforeach  
                                                                             
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                        </div>
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
    <!-- ......................table ends contents...........................................  -->
                        @endif
                            </div><!-- /.span -->



                        </div><!-- /.row -->

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div>
</form>
@endsection

@section('js')

     <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/page/report90.js')}}"></script>
    <script src="{{asset('msell/page/print.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>


    <script src="{{asset('msell/js/common.js')}}"></script>
    <!-- Modal -->
    <div class="modal fade" id="dms_payment_modal" role="dialog">
        <div class="modal-dialog" style="width:1200px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_retailer_dms_order_details" method="post" id="dms_payment_modal_form">
                        <input type="hidden" id="dms_payment_collection" name="dms_payment_collection" value="">
                        <input type="hidden" id="retailer_id" name="retailer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="">
                                   <label class="control-label no-padding-right" for="joining_date"> Dispatch Date <b style="color: red;">*</b></label>

                                    <input  required="required" type="text" placeholder="Select Date" name="dispatch_date" id="dispatch_date"  class="form-control date-picker input-sm" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Number </label>
                                    <input type="text" id="order_id" value=""  readonly name="order_id" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Date </label>
                                    <input type="text" id="order_date" value=""  readonly name="order_date" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Name </label>
                                    <input type="text" id="retailer_name" value=""  readonly name="retailer_name" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Address </label>
                                    <input type="text" id="retailer_address" value=""  readonly name="retailer_address" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Email </label>
                                    <input type="text" id="retailer_email"  value="" readonly name="retailer_email" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Mobile No. </label>
                                    <input type="text" id="retailer_number" value=""  readonly name="retailer_number" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer GST No. </label>
                                    <input type="text" id="retailer_gst"  value="" readonly name="retailer_gst" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <br>
                        <table  class="table table-bordered table-hover">
                            <thead class = "mythead">
                                <th>Sr.no</th>
                                <th>Sku Name</th>
                                <th>Cases Rate</th>
                                <th>Cases</th>
                                <th>Pcs Rate</th>
                                <th>Pcs </th>
                                <th>Scheme Qty</th>

                            </thead>
                            <tbody class="mytbody_payment">

                            </tbody>
                        </table>
                        
                        
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-3">
                                    <div class="">
                                       <label class="control-label no-padding-right" for="joining_date"> Date <b style="color: red;">*</b></label>

                                        <input  required="required" type="text" placeholder="Select Date" name="payment_date" id="payment_date"  class="form-control date-picker input-sm" >
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Payment Mode <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm chosen-select" name="payment_mode"
                                                id="payment_mode">
                                            <option value="">Select Mode</option>
                                            @if(!empty($payment_modes))
                                                @foreach($payment_modes as $p_key=>$p_value)
                                                    <option value="{{$p_key}}">{{$p_value}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Amount <b style="color: red;">*</b></label>
                                        <input type="text" id="amount" value=""   name="amount" placeholder="Amount" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Remarks <b style="color: red;">*</b></label>
                                        <input type="text" id="remarks" value=""   name="remarks" placeholder="Remarks" class="form-control "/>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="dms_order_dispatch_modal" role="dialog">
        <div class="modal-dialog" style="width:1200px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_retailer_dms_order_dispatch" method="post" id="dms_order_dispatch_form">
                        <input type="hidden" id="dms_order_dispatch" name="dms_order_dispatch" value="">
                        <input type="hidden" id="retailer_id_dispatch" name="retailer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <div class="row">
                            
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Number </label>
                                    <input type="text" id="order_id_dispatch" value=""  readonly name="order_id" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Date </label>
                                    <input type="text" id="order_date_dispatch" value=""  readonly name="order_date" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Name </label>
                                    <input type="text" id="retailer_name_dispatch" value=""  readonly name="retailer_name" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Address </label>
                                    <input type="text" id="retailer_address_dispatch" value=""  readonly name="retailer_address" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Email </label>
                                    <input type="text" id="retailer_email_dispatch"  value="" readonly name="retailer_email" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer Mobile No. </label>
                                    <input type="text" id="retailer_number_dispatch" value=""  readonly name="retailer_number" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Retailer GST No. </label>
                                    <input type="text" id="retailer_gst_dispatch"  value="" readonly name="retailer_gst" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <br>
                        <table  class="table table-bordered table-hover">
                            <thead class = "mythead">
                                <th>Sr.no</th>
                                <th>Sku Name</th>
                                <th>Cases Rate</th>
                                <th>Cases</th>
                                <th>FullFillment Cases</th>
                                <th>Pcs Rate</th>
                                <th>Pcs </th>
                                <th>FullFillment Pcs</th>
                                <th>Scheme Qty</th>
                                <th>FullFillment Scheme Qty</th>

                            </thead>
                            <tbody class="mytbody_dispatch">

                            </tbody>
                        </table>
                        
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="dms_reject_order_modal" role="dialog">
        <div class="modal-dialog" style="width:400px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_retailer_dms_reject_order" method="post" id="dms_reject_order_form">
                        <input type="hidden" id="dms_reject_order" name="dms_reject_order" value="">
                        <input type="hidden" id="retailer_id_dispatch" name="retailer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                       
                        
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-12">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="module"> Rejected Reasons <b style="color: red;">*</b></label>
                                        <select required="required" class="form-control input-sm chosen-select" name="reason_order"
                                                id="payment_mode">
                                            <option value="">Select Reasons</option>
                                            @if(!empty($reject_reason_dms))
                                                @foreach($reject_reason_dms as $p_key=>$p_value)
                                                    <option value="{{$p_key}}">{{$p_value}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#dms_payment_modal').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
        function confirmAction(heading, name, action_id, tab, act) {
            // alert(action_id);
            $.confirm({
                title: heading,
                buttons: {
                    Payment: function () {
                    

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({


                            type: "post",
                            url: domain + '/retailer_dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,


                            success: function (data) 
                            {
                                // console.log(data);
                                 $('.mytbody_payment').html('');
                                $("#dispatch_date").html('');
                                $("#retailer_number").html('');
                                $("#retailer_email").html('');
                                $("#retailer_name").html('');
                                $("#retailer_address").html('');
                                $("#retailer_gst").html('');
                                $("#order_id").html('');
                                $("#order_date").html('');
                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    // console.log(data.payemt_status);
                                    if(data.payemt_status == 1)
                                    {
                                        alert('Already Payment Confirmed for this order');
                                        $('#dms_payment_modal').modal('toggle');
                                        // $.alert('Canceled!');
                                    }
                                    else if(data.recject_status == 1)
                                    {
                                        alert('Order Rejected!!');
                                        $('#dms_payment_modal').modal('toggle');
                                    }
                                    else if(data.cacncel_status == 1)
                                    {
                                        alert('Order Cancelled By Retailer!!');
                                        $('#dms_payment_modal').modal('toggle');
                                    }
                                    $("#dispatch_date").val(data.dispatch_date);
                                    $("#retailer_number").val(data.result_top.retailer_mobile);
                                    $("#retailer_email").val(data.result_top.retailer_email);
                                    $("#retailer_name").val(data.result_top.retailer_name);
                                    $("#retailer_address").val(data.result_top.retailer_address);
                                    $("#retailer_gst").val(data.result_top.retailer_gst_no);
                                    $("#order_id").val(data.result_top.order_id);
                                    $("#order_date").val(data.result_top.sale_date);
                                    $("#retailer_id").val(data.result_top.retailer_id);

                                    var Sno = 1;
                                    var template = '';
                                    $.each(data.result_down, function (u_key, u_value) {
                                        template += ('<tr><td>'+Sno+'</td><td>'+u_value.product_name+'</td><td>'+u_value.cases_rate+'</td><td>'+u_value.cases+'</td><td>'+u_value.pcs_rate+'</td><td>'+u_value.pcs+'</td><td>'+u_value.scheme_qty+'</td></tr>');
                                        Sno++;
                                    });   
                                    // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+retailer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                    $('.mytbody_payment').append(template);
                                    // $('.mytbody_beat_details').append(template_beat);
                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });
                        $("#dms_payment_collection").val(action_id);
                        $("#dms_payment_modal").modal();
                    },
                    OrderConfirm: function () {
                       $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_retailer_order_confirm_submit',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data) 
                            {
                                // console.log(data);
                                // console.log(data);
                              
                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    if(data.result == 1)
                                    {
                                        $.alert('Order Confirmed');
                                        // $('#dms_order_dispatch_modal').modal('toggle');
                                        // $.alert('Canceled!');
                                    }
                                    else if(data.result == 2)
                                    {
                                       
                                        $.alert('Successfully Confirmed');
                                            // $('#dms_reject_order_modal').modal('toggle');
                                          
                                    }
                                    else if(data.result == 4)
                                    {
                                       
                                        $.alert('Order Canceled By Retailer');
                                            // $('#dms_reject_order_modal').modal('toggle');
                                          
                                    }
                                    else
                                    {
                                        $.alert('Error!!');

                                    }
                                    
                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        // $("#dms_order_confirm").val(action_id);
                        // $("#dms_order_confirm_modal").modal();

                        
                    },
                    Dispatch: function () {
                      
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/retailer_dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data2) 
                            {
                                // console.log(data);
                                // console.log(data);
                                if(data2.dispatch_status == 1)
                                {
                                    $.alert('Already Order Dispatched');
                                    $('#dms_order_dispatch_modal').modal('toggle');
                                    // $.alert('Canceled!');
                                }
                                else if(data2.recject_status == 1)
                                {
                                    alert('Order Rejected!!');
                                    $('#dms_order_dispatch_modal').modal('toggle');
                                }
                                else if(data2.cacncel_status == 1)
                                {
                                    alert('Order Cancelled By Retailer!!');
                                    $('#dms_order_dispatch_modal').modal('toggle');
                                }
                                $('.mytbody_dispatch').html('');
                                $("#dispatch_date_time").val('');
                                $("#retailer_number_dispatch").val('');
                                $("#retailer_email_dispatch").val('');
                                $("#retailer_name_dispatch").val('');
                                $("#retailer_address_dispatch").val('');
                                $("#retailer_gst_dispatch").val('');
                                $("#order_id_dispatch").val('');
                                $("#order_date_dispatch").val('');
                                if (data2.code == 401) 
                                {
                                   
                                }
                                else if (data2.code == 200) 
                                {
                                    // console.log(result_top);
                                    $("#dispatch_date_time").val(data2.dispatch_date);
                                    $("#retailer_number_dispatch").val(data2.result_top.retailer_mobile);
                                    $("#retailer_email_dispatch").val(data2.result_top.retailer_email);
                                    $("#retailer_name_dispatch").val(data2.result_top.retailer_name);
                                    $("#retailer_address_dispatch").val(data2.result_top.retailer_address);
                                    $("#retailer_gst_dispatch").val(data2.result_top.retailer_gst_no);
                                    $("#order_id_dispatch").val(data2.result_top.order_id);
                                    $("#order_date_dispatch").val(data2.result_top.sale_date);
                                    $("#retailer_id_dispatch").val(data2.result_top.retailer_id);

                                    var Sno = 1;
                                    var template = '';
                                    $.each(data2.result_down, function (u_key, u_value) {
                                        template += ('<tr><td>'+Sno+'</td><input type="hidden" name="product_cases[]" value='+u_value.cases+'><input type="hidden" name="product_qty[]" value='+u_value.pcs_rate+'><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="product_name[]" value='+u_value.product_name+'><td>'+u_value.product_name+'</td><td >'+u_value.cases_rate+'</td><td>'+u_value.cases+'</td><td><input name="fullfillment_cases[]" id="fullfillment_cases"></td><td>'+u_value.pcs_rate+'</td><td>'+u_value.pcs+'</td><td><input name="fullfillment_pcs[]" id="fullfillment_pcs"></td><td>'+u_value.scheme_qty+'</td><td><input name="fullfillment_scheme_qty[]" id="fullfillment_scheme_qty"></td></tr>');
                                        Sno++;
                                    });   
                                    // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+retailer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                    $('.mytbody_dispatch').append(template);
                                    // $('.mytbody_beat_details').append(template_beat);
                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });
                        $("#dms_order_dispatch").val(action_id);
                        $("#dms_order_dispatch_modal").modal();
                    },

                    Invoice: function () {
                        $.alert('Canceled!');
                    },
                    RejectOrder: function () {
                         $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/retailer_dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data2) 
                            {
                                // console.log(data);
                                // console.log(data);
                              
                                if (data2.code == 401) 
                                {
                                   
                                }
                                else if (data2.code == 200) 
                                {
                                    if(data2.dispatch_status == 1)
                                    {
                                        $.alert('Order Dispatched So now this Order cannot reject');
                                        $('#dms_reject_order_modal').modal('toggle');
                                        // $.alert('Canceled!');
                                    }
                                    
                                    else if(data2.cacncel_status == 1)
                                    {
                                        $.alert('Order Cancelled By Retailer!!');
                                        $('#dms_reject_order_modal').modal('toggle');
                                    }
                                    else
                                    {
                                        if(data2.recject_status == 1)
                                        {
                                             $.alert('Order Rejected');
                                            $('#dms_reject_order_modal').modal('toggle');
                                            // $.alert('Canceled!');
                                        }
                                    }
                                    
                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        $("#dms_reject_order").val(action_id);
                        $("#dms_reject_order_modal").modal();
                    },
                    Cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        $("#dispatch_date").datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
        $("#payment_date").datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
    </script>
    <script type="text/javascript">
        $("#dms_payment_modal_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#dms_payment_modal').modal('toggle');
                    // if (data.code == 401) 
                    // {
                       
                    // }
                    // else if (data.code == 200) 
                    // {
                    //     $('#dms_payment_modal').modal('toggle');

                    // }
                    // target.html(data); // show response from the php script.
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
         $("#dms_order_dispatch_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#dms_order_dispatch_modal').modal('toggle');
                   
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
         $("#dms_reject_order_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#dms_reject_order_modal').modal('toggle');
                   
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
    </script>
     <script>
                $('.user-modal').click(function() {
                      var order_id = $(this).attr('order_id'); 
                      var flag = $(this).attr('flag'); 
                      $('.mytbody').html('');
                      $('.mythead').html('');
                      $('.mytbody_top').html('');
                      $('.mythead_top').html('');
                      $('#submit').html('');
                      if (order_id != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/edit_order_details',
                dataType: 'json',
                data: "order_id=" + order_id,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                        // alert('qwertyh');
                    }
                    else if (data.code == 200) {
                        
                            var roundoff = '';
                            var Sno = 1;
                            if(flag==1)
                            {
                                $.each(data.result_top, function (key, value){
                                    var status = (value.status_approval==1)?'Approved':'Pending';
                                $('.mytbody_top').append("<tr><td>"+"<b>Customer Name</b>"+"</td><td style='color:blue;'>"+value.retailer_name+"</td><td>"+"<b>Order No.</b>"+"</td><td style='color:blue;'>"+value.order_id+"</td><td>"+"<b>Order Date</b>"+"</td><td style='color:blue;'>"+value.date+" "+value.time+"</td><td>"+"<b>Order Value</b>"+"</td><td style='color:blue;'>"+value.total_sale_value+"</td><td>"+"<b>Fulfillment Value</b>"+"</td><td style='color:blue;'>"+value.fullfillment_value+"</td></tr><tr><td>"+"<b>Area</b>"+"</td><td style='color:blue;'>"+value.l4_name+"</td><td>"+"<b>Mobile No.</b>"+"</td><td style='color:blue;'>"+value.mobile+"</td><td>"+"<b>Address</b>"+"</td><td style='color:blue;'>"+value.track_address+"</td><td>"+"<b>Status</b>"+"</td><td style='color:blue;'>"+status+"</td><td>"+"<b>Gst No.</b>"+"</td><td style='color:blue;'>"+value.gst_no+"</td></tr>");
                                Sno++;
                                });

                                $('.mythead').append("<tr><td>"+"<b>Sno</b>"+"</td><td>"+"<b>Code</b>"+"</td><td>"+"<b>Product Name</b>"+"</td><td>"+"<b>Rate</b>"+"</td><td>"+"<b>Quantity</b>"+"</td><td>"+"<b>Order Value</b>"+"</td><td>"+"<b>Supply Rate</b>"+"</td><td>"+"<b>Supply Quantity</b>"+"</td><td>"+"<b>Amount</b>"+"</td></tr>");
                                var Sno = 1;
                                var inc = 1;
                                console.log(data.remarks);
                                $.each(data.result, function (key, value){
                                     remarks = value.remarks;
                                     roundoff = value.rate*value.quantity;
                                    $('.mytbody').append("<tr><td style='height:20px;'>"+Sno+"</td><td>"+value.itemcode+"</td><td>"+value.product_name+"</td><td>"+value.rate+"</td><td>"+value.quantity+"</td><td>"+roundoff.toFixed(2)+"</td><td><input type='text' id="+"rate"+inc+" readonly name='rate[]' value="+value.rate+"></td><td><input type='number' readonly  id="+"qty"+inc+" name='qty[]'  value="+value.product_fullfiment_qty+"></td><td><input type='text' readonly id="+"total"+inc+" name='total[]' value="+value.product_value+"></td></tr>");
                                    Sno++;
                                    inc++;
                                });

                                $('#submit').append('<button class="btn btn-success form-control" id="submit" type="button" name="submit"><b>Submit</b></button>');
                                document.getElementById('remarks').innerHTML=data.remarks;
                            }
                            else
                            {
                                $.each(data.result_top, function (key, value){
                                    var status = (value.status_approval==1)?'Approved':'Pending';
                                $('.mytbody_top').append("<tr><td>"+"<b>Customer Name</b>"+"</td><td style='color:blue;'>"+value.retailer_name+"</td><td>"+"<b>Order No.</b>"+"</td><td style='color:blue;'>"+value.order_id+"</td><td>"+"<b>Order Date</b>"+"</td><td style='color:blue;'>"+value.date+" "+value.time+"</td><td>"+"<b>Order Value</b>"+"</td><td style='color:blue;'>"+value.total_sale_value+"</td><td>"+"<b>Fulfillment Value</b>"+"</td><td style='color:blue;'>"+value.fullfillment_value+"</td></tr><tr><td>"+"<b>Area</b>"+"</td><td style='color:blue;'>"+value.l4_name+"</td><td>"+"<b>Mobile No.</b>"+"</td><td style='color:blue;'>"+value.mobile+"</td><td>"+"<b>Address</b>"+"</td><td style='color:blue;'>"+value.track_address+"</td><td>"+"<b>Status</b>"+"</td><td style='color:blue;'>"+status+"</td><td>"+"<b>Gst No.</b>"+"</td><td style='color:blue;'>"+value.gst_no+"</td></tr>");
                                Sno++;
                                });

                                $('.mythead').append("<tr><td>"+"<b>Sno</b>"+"</td><td>"+"<b>Code</b>"+"</td><td>"+"<b>Product Name</b>"+"</td><td>"+"<b>Rate</b>"+"</td><td>"+"<b>Quantity</b>"+"</td><td>"+"<b>Order Value</b>"+"</td><td>"+"<b>Supply Rate</b>"+"</td><td>"+"<b>Supply Quantity</b>"+"</td><td>"+"<b>Amount</b>"+"</td><td style='display:none;'>"+"<b>product_id</b>"+"</td></tr>");
                                var Sno = 1;
                                var inc = 1;
                                $.each(data.result, function (key, value){
                                     roundoff = value.rate*value.quantity;
                                    $('.mytbody').append("<tr><td style='height:20px;'>"+Sno+"</td><td>"+value.itemcode+"</td><td>"+value.product_name+"</td><td>"+value.rate+"</td><td>"+value.quantity+"</td><td>"+roundoff.toFixed(2)+"</td><td><input type='text' id="+"rate"+inc+" readonly name='rate[]' value="+value.rate+"></td><td><input type='number' onkeyup='return mulfunc(this.id)' id="+"qty"+inc+" name='qty[]'  value="+value.quantity+"></td><td><input type='text' readonly id="+"total"+inc+" name='total[]' value="+roundoff.toFixed(2)+"></td><td style='display:none;'><input name='product_id[]' type='hidden' value="+value.product_id+"></td><input name='order_id[]' type='hidden' value="+value.order_id+"><input name='user_id[]' type='hidden' value="+value.user_id+"><input name='retailer_id[]' type='hidden' value="+value.retailer_id+"><input name='order_date[]' type='hidden' value="+value.order_date+"><input name='product_name[]' type='hidden' value="+value.product_name+"><input name='retailer_name[]' type='hidden' value="+value.retailer_name+"><input name='old_qty[]' type='hidden' value="+value.quantity+"></tr>");
                                    Sno++;
                                    inc++;
                                });
                                $('#submit').append('<button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>');
                                document.getElementById('remarks').innerHTML=data.remarks;

                            }
                            
                       

                           
                       
                        // _user.empty();
                        
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
          <!-- END MODAL -->
    
    <script>
    function mulfunc(str2)
    {
        var d=str2.substr(3,3);
        var x= document.getElementById("rate"+d).value;
        var y= document.getElementById("qty"+d).value;
        var total = x*y;
        document.getElementById("total"+d).value= total.toFixed(2);

        
    }

    </script>

    <script>
     $(document).on('change', '#state', function () {
        val = $(this).val();
        _hq = $('#user');
        //alert(_current_val);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_user_name',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data, function (key, value) {
                          
                            console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq.append(template).trigger("chosen:updated");
               

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
    <script>
            
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
    <script type="text/javascript">
            jQuery(function($) {
                //initiate dataTables plugin
                var myTable = 
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                .DataTable( {
                    bAutoWidth: false,
                    "aoColumns": [
                      { "bSortable": true },
                      null, null,null,null,null, null,null,null,
                      { "bSortable": true }
                    ],
                    "aaSorting": [],
                    
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
        <script>
        
    $(document).on('click', '#print_button', function () {
        
   
      
            $("#modalDiv").printThis({ 
            debug: false,              
            importCSS: true,             
            importStyle: true,         
            printContainer: true,       
            loadCSS: "../css/style.css", 
            pageTitle: "My Modal",             
            removeInline: false,        
            printDelay: 333,            
            header: null,             
            formValues: true          
             }); 
 });
        
        </script>
@endsection