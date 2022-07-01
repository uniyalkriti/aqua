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
                                                                        <th>{{Lang::get('common.location3')}}</th>
                                                                        <th>{{Lang::get('common.location6')}}</th>
                                                                        <th>Distributor Name</th>
                                                                        <th>Distributor E-mail</th>
                                                                        <th>Distributor Mobile No.</th>
                                                                        <th>Order Date</th> 
                                                                        <th>Order No</th>
                                                                        <th>Order Value</th>
                                                                        <th>Order Cases</th>
                                                                        <th>Order PCS</th>
                                                                        <th>PDF</th>
                                                                        <th>Uploaded PDF</th>
                                                                        <th>History</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $value12 = array(); ?>

                                                                    @if(!empty($records) && count(array($records))>0)
                                                                    
                                                                    @foreach($records as $k=> $r)
                                                                    @if(count(array($r->order_id))>0)
                                                                   
                                                                        <?php
                                                                        $encid = Crypt::encryptString($r->order_id); 
                                                                        $dealer_id = Crypt::encryptString($r->dealer_id); 
                                                                        $edit_status = !empty($permissions->edit_status)?$permissions->edit_status:'';
                                                                        // dd($edit_status);
                                                                        ?>
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td id = 'dms_current_status'>{{!empty($dms_status_order_query[$r->dms_order_reason_id])?$dms_status_order_query[$r->dms_order_reason_id]:'Pending'}}</td>
                                                                            <td>
                                                                                @if($edit_status == 1 || $is_admin == 1)
                                                                                    <a title="Assign Rate List"  onclick="confirmAction('Action','IMEI','{{$r->order_id}}','product_rate_list_template','clear');">
                                                                                        <button type="button" class="btn btn-default btn-round btn-white">
                                                                                            <i class="ace-icon fa fa-send green"></i>
                                                                                            Action 
                                                                                        </button>
                                                                                    </a>
                                                                                    @else
                                                                                    <a style="color: red;">{{'Unauthorized Request Please Contact To Administrator !!'}}</a>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{!empty($r->l3_name)?$r->l3_name:''}}</a></td>
                                                                            <td>{{!empty($location_data[$r->dealer_id])?$location_data[$r->dealer_id]:''}}</a></td>
                                                                            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
                                                                            <td>{{$r->d_email}}</a></td>
                                                                            <td>{{$r->mobile_number}}</td>
                                                                            <td>{{$r->sale_date}}</td>
                                                                            <td>{{$r->order_id}}</td>
                                                                            <td>{{$r->total_vale}}</td>
                                                                            <td>{{$r->cases}}</td>
                                                                            <td>{{$r->pcs}}</td>
                                                                            <!-- <td>
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
                                                                                        <?php   $total=0;
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
                                                                            </td>  -->
                                                                            <td>
                                                                                @if($r->pdf_name != 0)
                                                                                
                                                                                

                                                                                <a href="{{url('pdf/'.$r->pdf_name)}}" class="fa fa-file-pdf-o" style="font-size:30px;color:red" src="{{url('pdf/'.$r->pdf_name)}}" ></a>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($r->pdf_name2 != 0)
                                                                                
                                                                                

                                                                                <a href="{{url('pdf/'.$r->pdf_name2)}}" class="fa fa-file-pdf-o" style="font-size:30px;color:red" src="{{url('pdf/'.$r->pdf_name2)}}" ></a>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <a href="#" order_id="{{ $r->order_id }}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="History">
                                                                                    <div><i class="fa fa-history"></i></div>
                                                                                </a>
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
                    <form action="submit_dms_order_details" method="post" id="dms_payment_modal_form" enctype="multipart/form-data">
                        <input type="hidden" id="dms_payment_collection" name="dms_payment_collection" value="">
                        <input type="hidden" id="dealer_id" name="dealer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="">
                                   <label class="control-label no-padding-right" for="joining_date"> Dispatch Date <b style="color: red;">*</b></label>

                                    <input  required="required" type="text" placeholder="Select Date" name="dispatch_date" id="dispatch_date"  class="form-control date-picker input-sm" >
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Number </label>
                                    <input type="text" id="order_id" value=""  readonly name="order_id" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Date </label>
                                    <input type="text" id="order_date" value=""  readonly name="order_date" placeholder="Order Date" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Name </label>
                                    <input type="text" id="dealer_name" value=""  readonly name="dealer_name" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Address </label>
                                    <input type="text" id="dealer_address" value=""  readonly name="dealer_address" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Email </label>
                                    <input type="text" id="dealer_email"  value="" readonly name="dealer_email" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Mobile No. </label>
                                    <input type="text" id="dealer_number" value=""  readonly name="dealer_number" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor GST No. </label>
                                    <input type="text" id="dealer_gst"  value="" readonly name="dealer_gst" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Vehicle Name. </label>
                                    <input type="text" id="payment_vehical_name"  value="na" readonly name="payment_vehical_name" placeholder="Vehicle Name" class="form-control "/>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <table  class="table table-bordered table-hover">
                            <thead class = "mythead">
                                <th>S.No</th>
                                <th>Description</th>
                                <th>Case Rate</th>   
                                <th>Cases</th>
                                <th>Scheme <br>(Cases)</th>
                                <th>Total Weight<br>(Kg)</th> 
                                <th>Value <br>(₹)</th>
                                
                               

                            </thead>
                            <tbody class="mytbody_payment">

                            </tbody>
                        </table>
                        
                        
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-2">
                                    <div class="">
                                       <label class="control-label no-padding-right" for="joining_date"> Date <b style="color: red;">*</b></label>

                                        <input  required="required" type="text" placeholder="Select Date" name="payment_date" id="payment_date"  class="form-control date-picker input-sm" >
                                    </div>
                                </div>
                                <div class="col-md-2">
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
                                <div class="col-md-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Amount Recieved  <b style="color: red;">*</b></label>
                                        <input type="text" readonly id="amount_by_distributor" value="0" autocomplete="off"  name="amount_recieved" placeholder="Amount" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Payment Remarks  <b style="color: red;">*</b></label>
                                        <input type="text" readonly id="payment_remarks_app" value="0" autocomplete="off"  name="payment_remarks_app" placeholder="Remarks" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Amount <b style="color: red;">*</b></label>
                                        <input type="text" id="amount" value="" autocomplete="off"  name="amount" placeholder="Amount" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Remarks <b style="color: red;">*</b></label>
                                        <input type="text" id="remarks" value="" autocomplete="off"   name="remarks" placeholder="Remarks" class="form-control "/>
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
                                
                                <div class="col-lg-3" id="submit_payment">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- for order confirmation control -->
    <div class="modal fade" id="dms_order_confirm_modal" role="dialog">
        <div class="modal-dialog" style="width:1200px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="dms_order_confirm_submit" method="post" id="dms_order_confirm_modal_form">
                        <input type="hidden" id="dms_order_confirm" name="dms_order_confirm_action_id" value="">
                        <input type="hidden" id="dealer_id_confirm" name="dealer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="">
                                   <label class="control-label no-padding-right" for="joining_date"> Dispatch Date <b style="color: red;">*</b></label>

                                    <input readonly required="required" type="text" placeholder="Select Date" name="dispatch_date" id="dispatch_date_order_confirm"  class="form-control date-picker input-sm" >
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Number </label>
                                    <input type="text" id="order_id_confirm" value=""  readonly name="order_id" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Date </label>
                                    <input type="text" id="order_date_confirm" value=""  readonly name="order_date" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Name </label>
                                    <input type="text" id="dealer_name_confirm" value=""  readonly name="dealer_name" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Address </label>
                                    <input type="text" id="dealer_address_confirm" value=""  readonly name="dealer_address" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Email </label>
                                    <input type="text" id="dealer_email_confirm"  value="" readonly name="dealer_email" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Mobile No. </label>
                                    <input type="text" id="dealer_number_confirm" value=""  readonly name="dealer_number" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor GST No. </label>
                                    <input type="text" id="dealer_gst_confirm"  value="" readonly name="dealer_gst" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Vehicle Name. </label>
                                    <input type="text" id="order_vehical_name"  value="na" readonly name="order_vehical_name" placeholder="Vehicle Name" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <br>

                         <table id="order_confirm_table_id" class="table table-bordered ">
                            <thead class = "mythead">
                            <tr> 
                               <th rowspan="2">Sr.no</th>
                                <th rowspan="2">Description</th>
                                <th colspan="5">Order Details</th>
                                <th colspan="4">FullFillment</th>
                                <th rowspan="2">Action</th>
                            </tr>                            
                                <tr>
                                    <th>Cases Rate</th>
                                    <th>Cases</th>
                                    <th>Scheme Qty</th>
                                    <th>Total Weight</th>
                                    <th>Vale</th>


                                    <th>Cases</th>
                                    <th>Scheme</th>
                                    <th>Total Weight</th>
                                    <th>Value</th>   
                                </tr>
                            </thead>
                            <tbody class="mytbody_order_confirm">

                            </tbody>
                            <tfoot class="mytfoot_order_confirm">
                                
                            </tfoot>
                        </table>
                        
                        
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username"> Remarks <b style="color: red;">*</b></label>
                                        <input type="text" id="remarks" value=""  autocomplete="off" name="remarks" placeholder="Remarks" class="form-control "/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="control-label no-padding-right"
                                               for="username">Order Remarks <b style="color: red;">*</b></label>
                                        <input type="text" id="order_app_remarks" value="" readonly="" autocomplete="off" name="order_app_remarks" placeholder="Remarks" class="form-control "/>
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
                                
                                <div class="col-lg-3" id="submit_order_confirm">
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
                    <form action="submit_dms_order_dispatch" method="post" id="dms_order_dispatch_form">
                        <input type="hidden" id="dms_order_dispatch" name="dms_order_dispatch" value="">
                        <input type="hidden" id="dealer_id_dispatch" name="dealer_id" value="">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Order Preview&nbsp&nbsp&nbsp  </th>

                        </table>
                        <div class="row">
                            
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Number </label>
                                    <input type="text" id="order_id_dispatch" value=""  readonly name="order_id" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Order Date </label>
                                    <input type="text" id="order_date_dispatch" value=""  readonly name="order_date" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Name </label>
                                    <input type="text" id="dealer_name_dispatch" value=""  readonly name="dealer_name" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Address </label>
                                    <input type="text" id="dealer_address_dispatch" value=""  readonly name="dealer_address" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Email </label>
                                    <input type="text" id="dealer_email_dispatch"  value="" readonly name="dealer_email" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor Mobile No. </label>
                                    <input type="text" id="dealer_number_dispatch" value=""  readonly name="dealer_number" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                           
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Distributor GST No. </label>
                                    <input type="text" id="dealer_gst_dispatch"  value="" readonly name="dealer_gst" placeholder="Order Number" class="form-control "/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id"> Plant </label>
                                    <select required="required" class="form-control input-sm chosen-select" name="plant_id"
                                                id="payment_mode">
                                        <option value="">Select Plant</option>
                                        @if(!empty($dms_plant_master))
                                            @foreach($dms_plant_master as $p_key=>$p_value)
                                                <option value="{{$p_key}}">{{$p_value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id"> Transport Name </label>
                                    <input type="text" required="required" id="transport_name" autocomplete="off" value=""  name="transport_name" placeholder="Order Number" class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id"> Transport mode </label>
                                    <select required="required" class="form-control input-sm chosen-select" name="travelling_id"
                                                id="payment_mode">
                                        <option value="">Select Mode</option>
                                        @if(!empty($travell_mode))
                                            @foreach($travell_mode as $p_key=>$p_value)
                                                <option value="{{$p_key}}">{{$p_value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id"> GR no. </label>
                                    <input type="text" id="fr_no"  value="" autocomplete="off" name="gr_no" placeholder="Gr no." class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id"> Freight</label>
                                    <input type="text" id="frieght_no"  value="" autocomplete="off" name="freight" placeholder="Freight" class="form-control "/>
                                                                        
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id">  Driver Name</label>
                                    <input type="text" id="frieght_no" required="required" value="" autocomplete="off" name="driver_name" placeholder="Driver Name" class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id">  Driver Contact No.</label>
                                    <input type="text" id="frieght_no" required="required" value=""autocomplete="off"name="driver_number" placeholder="Driver Number" class="form-control vnumerror" maxlength="10"/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id">  Vehical No. </label>
                                    <input type="text" id="vehical_number" required="required" value="" autocomplete="off" name="vehical_number" placeholder="Vehical No." class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id" id ="carrying_capacity_lable">  Carrying Capacity</label>
                                    <!-- onchange="return carrying_capacity_greater()" function will check the carrying capacity is greater then weight or not so if in case reopen this clause then copy and put in any where in in below input tag !! -->
                                    <input type="text" id="carrying_capacity" required="required"  value="" autocomplete="off" name="carrying_capacity"  placeholder="Carrying Capacity" class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id">  Payment Recieved (₹)</label>
                                    <input type="text" id="payemt_recieved" required="required" value="" autocomplete="off" name="payment_recieved" placeholder="Payment Recieved" class="form-control "/>
                                                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="control-label no-padding-right" for="plant_id">  Payment Remarks </label>
                                    <input type="text" id="payment_remarks" required="required" value="" autocomplete="off" name="payment_remarks" placeholder="Payment Remarks" class="form-control "/>
                                                                        
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Vehicle Name. </label>
                                    <input type="text" id="dispatch_vehical_name"  value="na" readonly name="dispatch_vehical_name" placeholder="Vehicle Name" class="form-control "/>
                                </div>
                            </div>

                             <div class="col-lg-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="username"> Upload File </label>
                                    <input type="file" name="excelFile">
                                </div>
                            </div>
                            
                        </div>

                            
                            

                        <br>
                        <table id="table_details_dispatch" class="table table-bordered ">
                            <thead class = "mythead">
                                <th>Sr.no</th>
                                <th>Sku Name</th>
                                <th>Cases Rate</th>

                               
                                <th> Cases</th>
                               
                           
                               
                                <th> Scheme </th>
                                
                                <th>Total Weight</th>
                                <th>Value</th>
                                
                                <th>Mfg Date</th>
                                <th>Batch No.</th>
                                <th>Action</th>


                            </thead>
                            <tbody class="mytbody_dispatch">

                            </tbody>
                            <tfoot class="mytfoot_dispatch">
                                
                            </tfoot>
                        </table>

                        <br>
                        
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-lg-6">
                                    <label class="control-label no-padding-right" for="module"> Dispatch Remarks</label>
                                    <textarea name="dispatch_remarks" id="dispatch_remarks" required class="form-control input-sm"></textarea>
                                </div>
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel" style="margin-top: 25px;"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit_dispatch">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit" style="margin-top: 25px;"><b>Submit</b></button>
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
                    <form action="submit_dms_reject_order" method="post" id="dms_reject_order_form">
                        <input type="hidden" id="dms_reject_order" name="dms_reject_order" value="">
                        <input type="hidden" id="dealer_id_dispatch" name="dealer_id" value="">
                        
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

     <!-- Modal invoice generation-->
    <div class="modal fade" id="dms_invoice_modal" role="dialog">
        <div class="modal-dialog" >
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_dms_invoice_genrate" method="post" id="dms_invoice_form" enctype="multipart/form-data">
                            {!! csrf_field() !!}

                        <input type="hidden" id="dms_invoice" name="dms_invoice_action_id" value="">
                        <input type="hidden" id="dealer_id_invoice" name="dealer_id" value="">
                        
                        <div class="searchlistdiv watermark" id="searchlistdiv"     style="border: solid 1px #000;"> 
                            <p align="center" id="invoice_address"><strong>Patanjali Peya Pvt. Ltd.<br>
                                LG-01, Aggarwal Cyber Plaza 1, Plot no. C 4,5 & 6,<br>
                                District Center, Netaji Subhash Place, Wazirpur, Delhi, 110034</strong>
                            </p>
                            <table border="1" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <th style="text-align: left;">Party Information</th>
                                </tr>
                            </table>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">

                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Party Name:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="dealer_name_invoice"></a></td>
                                </tr>
                              
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;GST No</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="gst_no_invoice"></a></td>
                                    <td style="text-align: left;">&nbsp;&nbsp;State: </td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="state_name_invoice"></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Contact No.</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="dms_mobile_invoice"></a></td> 
                                    <td style="text-align: left;">&nbsp;&nbsp;Order Date:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="order_date_invoice"></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Email ID:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="email_id_invoice"></a></td>
                                
                                    <td style="text-align: left;">&nbsp;&nbsp;Order No:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="order_no_invoice"></a></td>
                                </tr>
                              
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Party Type: </td>
                                    <td style="text-align: left;">&nbsp;&nbsp;Distributor</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;Order Booked By:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="fullname_invoice"></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Remaks</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id="remarks_invoice"></a></td>
                                </tr>
                            </table>
                            <table border="1" cellspacing="0" cellpadding="0" width="100%">

                                <tr>
                                    <th style="text-align: left;">Transport Detail</th>
                                </tr>
                            </table>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">

                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Plant Name:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='plant_name_invoice'></a></td>
                                    <td style="text-align: left;">&nbsp;&nbsp;GR No.:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='gr_no_invoice'></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Transport Type:  </td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='travell_mode'></a></td>
                                    <td style="text-align: left;">&nbsp;&nbsp;Driver Name:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='driver_name_invoice'></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Transport Name:</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='transport_name_invoice'></a></td>
                                    <td style="text-align: left;">&nbsp;&nbsp;Driver Contact No.</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='driver_number_invoice'></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">&nbsp;&nbsp;Vehicle No.</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='vehical_number_invoice'></a></td>
                                    <td style="text-align: left;">&nbsp;&nbsp;Freight: Rs.</td>
                                    <td style="text-align: left;">&nbsp;&nbsp;<a id='freight_invoice'></a></td>
                                </tr>
                            </table>
                            <table id="mytable" width="100%" border="1" cellspacing="2" cellpadding="2" class="tableform">

                                <th>S.No</th>
                                <th>Description</th>
                                <th>Rate</th>   
                                <th>Qty <br>(Cases)</th>
                                <th>Scheme <br>(Cases)</th>
                                <th>Total Amt <br>(₹)</th>
                                
                                <th>Weight<br>(Kg)</th> 
                                <th>MFG Date</th> 
                                <th>Batch No</th>


                               
                                <tbody class="dms_invoice_body">
                                    
                                </tbody>
                            </table>     

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-6">
                                    <div class="">
                                        <!-- <label class="control-label no-padding-right" for="email_sent">Enter Multiples E-mail with comma (,)</label> -->
                                        <label class="control-label no-padding-right" for="email_sent">E-mail </label>
                                        <input type="text" id="email_sent" required="required" value="" autocomplete="off" name="email_sent" placeholder="Enter Email" class="form-control "/>
                                                                            
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="control-label no-padding-right" for="email_sent">Invoice No. </label>
                                    <input type="text" id="invoice_no" required="required" value="" autocomplete="off" name="invoice_no" placeholder="Enter Invoice No." class="form-control"/>
                                    <!-- <input type="file" name="file_name" value="" > -->

                                </div>
                            </div>
                        </div>
                      <div class="row">
                            <div class="col-xs-12">
                                                                
                                <div class="col-md-6">
                                    <label class="control-label no-padding-right" for="email_sent">Invoice Date </label>
                                    <input type="text" id="invoice_date" required="required" value="" autocomplete="off" name="invoice_date" placeholder="Enter Invoice Date." class="date-picker form-control datetimepicker" />
                                    <!-- <input type="file" name="file_name" value="" > -->

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-md-4">
                                    <label class="control-label no-padding-right" for="email_sent">Upload Pdf </label>
                                    <input type="file" required='required' class="form-control-file" name="imageFile" id="imageFile"  accept="application/pdf" aria-describedby="fileHelp" onchange="readURL(this);">
                                    <!-- <input type="file" name="file_name" value="" > -->

                                </div>
                                 <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel" style="margin-top: 25px;"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit_invoice">
                                    <button class="btn btn-success form-control" id="submit" type="submit" name="submit" style="margin-top: 25px;"><b>Submit</b></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">

                               
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- logs modal starts here  -->

<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg2">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >Status Log</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="table-header center">
                            Status Log
                           
                        </div>
                        <table class="table table-bordered table-hover" >
                            <thead class = "mythead_distibutor_list">
                                <th>Sr.No</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Time</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
                            </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <script type="text/javascript">
    $('.vnumerror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.vnumerror_invoice_no').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });

    function carrying_capacity_greater()
    {
        var x= document.getElementById("carrying_capacity").value;
        var y= document.getElementById("grand_fullfilmentrowweight_total").innerHTML;
            
        if(x<y)
        {
            // document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity <br><b style="color:red;"> !! Please Enter Carrying Capacity Greater Then Total Weight !! </b>';

            document.getElementById("carrying_capacity").value = '';
            document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity <br><b style="color:red;"> !! Please Enter Carrying Capacity Greater Then Total Weight !! </b>';
        }
        else
        {
            document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity';

        }

    }
    </script>
    <script>
        $('#dms_payment_modal').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });
        function confirmAction(heading, name, action_id, tab, act) {
            // alert(action_id);
            $.confirm({
                title: heading,
                buttons: {
                     OrderConfirm: function () {
                       $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data) 
                            {
                                // console.log(data);
                                // console.log(data);
                                $('.mytbody_order_confirm').html('');
                                $('#dispatch_date_order_confirm').html('');
                                $('#order_id_confirm').html('');
                                $('#order_date_confirm').html('');
                                $('#dealer_name_confirm').html('');
                                $('#dealer_address_confirm').html('');
                                $('#dealer_email_confirm').html('');
                                $('#dealer_number_confirm').html('');
                                $('#dealer_gst_confirm').html('');
                                $('#dealer_id_confirm').html('');
                                $('#order_vehical_name').html('');
                                $('#order_app_remarks').html('');
                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    if(data.role_id == 239 || data.is_admin == 1)
                                    {

                                    
                                        if(data.recject_status == 1) // for reject status
                                        {
                                            $.alert('Order Rejected');
                                            $('#dms_order_confirm_modal').modal('toggle');

                                        }
                                        else if(data.cacncel_status == 1)
                                        {
                                            $.alert('Order Cancelled By Distributor');
                                            $('#dms_order_confirm_modal').modal('toggle');

                                        }

                                        else if(data.order_confirm_status == 1)
                                        {
                                            $.alert('Order Confirmed Already!!');
                                            $('#dms_order_confirm_modal').modal('toggle');

                                        }
                                        $("#dispatch_date_order_confirm").val(data.dispatch_date);
                                        $("#order_vehical_name").val(data.result_top.vehicle_name);
                                        $("#dealer_number_confirm").val(data.result_top.dealer_mobile);
                                        $("#dealer_email_confirm").val(data.result_top.dealer_email);
                                        $("#dealer_name_confirm").val(data.result_top.dealer_name);
                                        $("#dealer_address_confirm").val(data.result_top.dealer_address);
                                        $("#dealer_gst_confirm").val(data.result_top.dealer_gst_no);
                                        $("#order_id_confirm").val(data.result_top.order_id);
                                        $("#order_date_confirm").val(data.result_top.sale_date);
                                        $("#dealer_id_confirm").val(data.result_top.dealer_id);
                                        $('#order_app_remarks').val(data.payment_remarks_app);


                                        var Sno = 1;
                                        var inc = 1;
                                        var template = '';
                                        var template2 = '';
                                        var total_count = data.count_toat_order;
                                        var total_cases_order_confirm = 0;
                                        var weight_t_order_confirm = 0;
                                        var total_scheme_qty_order_confirm = 0;
                                        var total_amt_order_confirm = 0;
                                        var total_weight_order_confrim =0;
                                        var row_amt_order_confirm = 0;
                                        var row_weight_order_confrim =0;
                                        var range_first_upper = 0;
                                        var range_second_uper = 0;
                                        var free_qty_upper = 0;
                                        // console.log(total_count);
                                        $.each(data.result_down, function (u_key, u_value) {

                                            total_cases_order_confirm += parseInt(u_value.cases);
                                            weight_t_order_confirm += parseFloat(u_value.weight/1000);
                                            total_scheme_qty_order_confirm += parseInt(u_value.scheme_qty);
                                            total_amt_order_confirm += parseInt(u_value.cases*u_value.cases_rate);
                                            total_weight_order_confrim += parseInt((u_value.weight/1000)*(parseInt(u_value.cases)+parseInt(u_value.scheme_qty)));

                                            row_amt_order_confirm = parseInt(u_value.cases*u_value.cases_rate);
                                            row_weight_order_confrim = parseInt(u_value.weight/1000)*(parseInt(u_value.cases)+parseInt(u_value.scheme_qty));

                                             range_first_upper = (data.range_first[u_value.product_id])?data.range_first[u_value.product_id]:'0';
                                             range_second_uper = (data.range_second[u_value.product_id])?data.range_second[u_value.product_id]:'0';
                                             free_qty_upper = (data.free_qty[u_value.product_id])?data.free_qty[u_value.product_id]:'0';

                                            template += ('<tr><td>'+Sno+'</td><td ><select   name="product_id[]"><option value='+u_value.product_id+'>'+u_value.product_name+'</option></select></td><td ><input style="width:70px;" type="text" readonly name="product_rate_cases[]" id='+"case_r"+inc+' value='+u_value.cases_rate+'></td><input style="width:70px;" type="hidden" id='+"weight"+inc+' readonly name="weight[]" value='+u_value.weight/1000+'><td><input style="width:70px;" type="text" readonly name="product_cases[]"  value='+u_value.cases+'></td><td><input type="text" readonly name="product_scheme_qty[]" style="width:70px;" value='+u_value.scheme_qty+'></td><td><input type="text" name="order_weight_custom[]" readonly  style="width:70px;" value='+row_weight_order_confrim+'></td><td><input type="text" readonly  style="width:70px;"   name="order_value_custom[]" value='+row_amt_order_confirm+'></td><td><input   style="width:70px;" value="0" autocomplete="off" name="fullfillment_cases[]" onkeyup="return mulfuncOrderConfirm(this.id)" id='+"case_f"+inc+'></td><td><input value="0" autocomplete="off" name="fullfillment_scheme_qty[]" style="width:70px;" onkeyup="return mulfunc_casesOrderConfirm(this.id)" id='+"sche_f"+inc+'></td><td><input style="width:70px;" type="text" name="row_total_weight[]" readonly id='+"fin_we"+inc+' value="0" ></td><input style="width:70px;" name="row_total_cases[]" type="hidden" readonly id='+"fin_ca"+inc+' value="0" ><td><input style="width:70px;" name="row_total_order_amt[]" type="text" readonly id='+"fin_or"+inc+' value="0" ></td><input style="width:70px;" type="hidden" readonly name="test_scheme[]" id='+"test_scheme"+inc+' value='+free_qty_upper+'> <input style="width:70px;" type="hidden" readonly name="range_first[]" id='+"range_first"+inc+' value='+range_first_upper+'> <input style="width:70px;" type="hidden" readonly name="range_second[]" id='+"range_second"+inc+' value='+range_second_uper+'>');
                                            Sno++;
                                            inc++;
                                        });   
                                        template += ('<td width="70px" ><i  title="more" id=sr_no'+inc+' class="fa fa-plus" onclick="return addfunction(this.id)" ></i></tr>')
                                        // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                        $('.mytbody_order_confirm').append(template);
                                        // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                        // $('.mytbody_order_confirm').append(template);
                                        // $('.mytbody_beat_details').append(template_beat);
                                        $(".mytfoot_order_confirm").html('');
                                        template2 += "<tr><td colspan = '3'>Grand Total</td><td id='grand_cases_total'>"+total_cases_order_confirm+"</td><td id='grand_schemeqty_total'>"+total_scheme_qty_order_confirm+"</td><td id='grand_order_weight_custom'>"+total_weight_order_confrim+"</td><td id='grand_order_value_custom'>"+total_amt_order_confirm+"</td><td id='grand_fullfillment_cases_total'>0</td><td id='grand_fullfilmentschemqty_total'>0</td><td id='grand_fullfilmentrowweight_total'>0</td><td id='grand_fullfilmentorderamt_total'>0</td></tr>";
                                        $('.mytfoot_order_confirm').append(template2);
                                       
                                    }
                                    else
                                    {
                                        $.alert('Unauthorized Request Please Contact To Administrator !!');
                                        $('#dms_order_confirm_modal').modal('toggle');

                                    }
                            

                                   
                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        $("#dms_order_confirm").val(action_id);
                        $("#dms_order_confirm_modal").modal();

                        
                    },
                    Payment: function () {
                    

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({


                            type: "post",
                            url: domain + '/dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,


                            success: function (data) 
                            {
                                // console.log(data);
                                $('.mytbody_payment').html('');
                                $("#dispatch_date").html('');
                                $("#dealer_number").html('');
                                $("#dealer_email").html('');
                                $("#dealer_name").html('');
                                $("#dealer_address").html('');
                                $("#dealer_gst").html('');
                                $("#order_id").html('');
                                $("#order_date").html('');
                                $("#amount_by_distributor").html('');
                                $("#payment_remarks_app").html('');
                                $("#payment_vehical_name").html();

                                if (data.code == 401) 
                                {
                                   
                                }
                                else if (data.code == 200) 
                                {
                                    if(data.role_id == 238 || data.is_admin == 1)
                                    {
                                        // console.log(data.payemt_status);
                                        if(data.recject_status == 1)
                                        {
                                            $.alert('Order Rejected!!');
                                            $('#dms_payment_modal').modal('toggle');
                                        }
                                        else if(data.cacncel_status == 1)
                                        {
                                            $.alert('Order Cancelled By Distributor!!');
                                            $('#dms_payment_modal').modal('toggle');
                                        }
                                        else if(data.order_confirm_status == 1)
                                        {
                                            if(data.payemt_status == 1)
                                            {
                                                $.alert('Already Payment Confirmed for this order');
                                                $('#dms_payment_modal').modal('toggle');
                                                // $.alert('Canceled!');
                                            }
                                            
                                            $("#dispatch_date").val(data.dispatch_date);
                                            $("#payment_vehical_name").val(data.result_top.vehicle_name);
                                            $("#dealer_number").val(data.result_top.dealer_mobile);
                                            $("#dealer_email").val(data.result_top.dealer_email);
                                            $("#dealer_name").val(data.result_top.dealer_name);
                                            $("#dealer_address").val(data.result_top.dealer_address);
                                            $("#dealer_gst").val(data.result_top.dealer_gst_no);
                                            $("#order_id").val(data.result_top.order_id);
                                            $("#order_date").val(data.result_top.sale_date);
                                            $("#dealer_id").val(data.result_top.dealer_id);
                                            $('#amount_by_distributor').val(data.payemt_details);
                                            $('#payment_remarks_app').val(data.payment_remarks_app);

                                            var Sno = 1;
                                            var template = '';
                                            var total_cases = 0;
                                            var weight_t = 0;
                                            var total_scheme_qty = 0;
                                            var total_amt = 0;
                                            var total_weight = 0;
                                            $.each(data.invoice_data, function (u_key, u_value) {
                                                total_cases += u_value.cases;
                                                weight_t += u_value.weight/1000;
                                                total_scheme_qty += u_value.scheme_qty;
                                                total_amt += u_value.cases*u_value.cases_rate;
                                                total_weight += ((u_value.weight/1000)*(u_value.cases+u_value.scheme_qty));
                                                template += ('<tr><td>'+Sno+'</td><td>'+u_value.product_name+'</td><td>'+u_value.cases_rate+'</td><td>'+u_value.cases+'</td><td>'+u_value.scheme_qty+'</td><td>'+((u_value.weight/1000)*(u_value.cases+u_value.scheme_qty)).toFixed(3)+'</td><td>'+u_value.cases*u_value.cases_rate+'</td></tr>');
                                                Sno++;
                                            });   
                                            template += "<tr><td colspan = '2'>Grand Total</td><td></td><td>"+total_cases+"</td><td>"+total_scheme_qty+"</td><td>"+(total_weight).toFixed(3)+"</td><td>"+total_amt+"</td></tr>";
                                            $('.mytbody_payment').append(template);
                                            // $('.mytbody_beat_details').append(template_beat);
                                        }
                                        else
                                        {
                                            $.alert('Order Confirm First!!');
                                            $('#dms_payment_modal').modal('toggle');
                                        }
                                    }
                                    else
                                    {
                                        $.alert('Unauthorized Request Please Contact To Administrator !!');
                                        $('#dms_payment_modal').modal('toggle');

                                    }
                                   
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
                   
                    Dispatch: function () {
                      
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data2) 
                            {
                                // console.log(data);
                                // console.log(data);
                                if(data2.role_id == 237 || data2.is_admin == 1)
                                {
                                    if(data2.recject_status == 1) // for reject status
                                    {
                                        $.alert('Order Rejected');
                                        $('#dms_order_dispatch_modal').modal('toggle');

                                    }
                                    else if(data2.cacncel_status == 1)
                                    {
                                        $.alert('Order Cancelled By Distributor');
                                        $('#dms_order_dispatch_modal').modal('toggle');

                                    }
                                    else if(data2.order_confirm_status == 1) // for payment confirmation 
                                    {
                                        if(data2.payemt_status == 1)
                                        {
                                            if(data2.dispatch_status == 1)
                                            {
                                                $.alert('Already Order Dispatched');
                                                $('#dms_order_dispatch_modal').modal('toggle');
                                                // $.alert('Canceled!');
                                            }
                                            else
                                            {
                                                $('.mytbody_dispatch').html('');
                                                $("#dispatch_date_time").val('');
                                                $("#dealer_number_dispatch").val('');
                                                $("#dealer_email_dispatch").val('');
                                                $("#dealer_name_dispatch").val('');
                                                $("#dealer_address_dispatch").val('');
                                                $("#dealer_gst_dispatch").val('');
                                                $("#order_id_dispatch").val('');
                                                $("#order_date_dispatch").val('');
                                                $("#payemt_recieved").val('');
                                                $("#payment_remarks").val('');
                                                $("#dispatch_vehical_name").val('');
                                                if (data2.code == 401) 
                                                {
                                                   
                                                }
                                                else if (data2.code == 200) 
                                                {
                                                    // console.log(result_top);
                                                    $("#dispatch_date_time").val(data2.dispatch_date);
                                                    $("#dispatch_vehical_name").val(data2.result_top.vehicle_name);
                                                    $("#dealer_number_dispatch").val(data2.result_top.dealer_mobile);
                                                    $("#dealer_email_dispatch").val(data2.result_top.dealer_email);
                                                    $("#dealer_name_dispatch").val(data2.result_top.dealer_name);
                                                    $("#dealer_address_dispatch").val(data2.result_top.dealer_address);
                                                    $("#dealer_gst_dispatch").val(data2.result_top.dealer_gst_no);
                                                    $("#order_id_dispatch").val(data2.result_top.order_id);
                                                    $("#order_date_dispatch").val(data2.result_top.sale_date);
                                                    $("#dealer_id_dispatch").val(data2.result_top.dealer_id);
                                                    $("#payment_remarks").val(data2.payment_remarks);
                                                    if(data2.payemt_recieved)
                                                    {
                                                        $("#payemt_recieved").val(data2.payemt_recieved);
                                                    }
                                                    else
                                                    {
                                                        $("#payemt_recieved").val(0);
                                                    }
                                                 


          
                                                    var Sno = 1;
                                                    var inc = 1;
                                                    var template = '';
                                                    var template2 = '';
                                                    var total_count = data2.count_toat_order;

                                                    var total_cases = 0;
                                                    var weight_t = 0;
                                                    var total_scheme_qty = 0;
                                                    var total_amt = 0;
                                                    var total_weight = 0;

                                                    var row_total_cases = 0;
                                                    var row_total_scheme_qty = 0;
                                                    var row_total_amt = 0;
                                                    var row_total_weight = 0;
                                                    // console.log(total_count);
                                                    $.each(data2.invoice_data, function (u_key, u_value) {

                                                        // total_cases += u_value.cases;
                                                        weight_t += u_value.weight/1000;
                                                        total_scheme_qty += u_value.scheme_qty;
                                                        total_cases += u_value.cases;
                                                        total_amt += u_value.cases*u_value.cases_rate;
                                                        total_weight += ((u_value.weight/1000)*(u_value.cases+u_value.scheme_qty));



                                                        row_total_scheme_qty = u_value.scheme_qty;
                                                        row_total_cases = u_value.cases;
                                                        row_total_amt = u_value.cases*u_value.cases_rate;
                                                        row_total_weight = ((u_value.weight/1000)*(u_value.cases+u_value.scheme_qty));


                                                        template += ('<tr><td>'+Sno+'</td><td><select   name="product_id[]"><option value='+u_value.product_id+'>'+u_value.product_name+'</option></select></td><td ><input style="width:70px;" type="text" readonly name="product_rate_cases[]" id='+"case_r"+inc+' value='+u_value.cases_rate+'></td><input style="width:70px;" type="hidden" id='+"weight"+inc+'  readonly name="weight[]" value='+u_value.weight/1000+'><td><input style="width:70px;" autocomplete="off" name="fullfillment_cases[]" onkeyup="return mulfunc(this.id)" id='+"case_f"+inc+' value='+u_value.cases+'></td><td><input autocomplete="off" name="fullfillment_scheme_qty[]" value='+u_value.scheme_qty+' style="width:70px;" onkeyup="return mulfunc_cases(this.id)" id='+"sche_f"+inc+'></td><td><input style="width:70px;" type="text" name="row_total_weight[]" readonly id='+"fin_we"+inc+' value='+row_total_weight.toFixed(3)+' ></td><input style="width:70px;" type="hidden" name="row_fullfillment_cases[]" readonly id='+"fin_ca"+inc+' value='+(row_total_scheme_qty+row_total_cases)+' ><td><input style="width:70px;" type="text" name="row_fullfillment_order_amt[]" readonly id='+"fin_or"+inc+' value='+row_total_amt+' ></td><td><input style="width:70px;" autocomplete="off" class="mgf_date date-picker" onclick="return datepicker_function(this.id)" id='+"mfg_date"+inc+' type="text"  name="mgf_date[]" value="" required></td><td><input autocomplete="off" style="width:70px;" type="text" required name="batch_no[]" value="0" ></td>');
                                                        Sno++;
                                                        inc++;
                                                    });   
                                                    template += ('<td width="70px" ><i id=sr_no'+inc+' title="more" class="fa fa-plus" aria-hidden="true" onclick="return addfunctionDispatch(this.id)"></i></tr>')
                                                    // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                                    $('.mytbody_dispatch').append(template);
                                                    $('.mytfoot_dispatch').html('');
                                                    template2 = "<tr><td colspan = '3'>Grand Total</td><td id='grand_fullfillment_cases_total'>"+total_cases+"</td><td id='grand_fullfilmentschemqty_total'>"+total_scheme_qty+"</td><td id='grand_fullfilmentrowweight_total'>"+(total_weight).toFixed(3)+"</td><td id='grand_fullfilmentorderamt_total'>"+total_amt+"</td></tr>";
                                                    $('.mytfoot_dispatch').append(template2);
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $.alert('Firstly Confirm Payment');
                                            $('#dms_order_dispatch_modal').modal('toggle');   
                                        }
                                        
                                    }
                                    else
                                    {
                                        $.alert('Firstly Confirm Order');
                                        $('#dms_order_dispatch_modal').modal('toggle');   
                                    }
                               
                                }
                                else
                                {
                                    $.alert('Unauthorized Request Please Contact To Administrator !!');
                                    $('#dms_order_dispatch_modal').modal('toggle');

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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_order_details',
                            dataType: 'json',
                            data: "order_id=" + action_id,
                            success: function (data2) 
                            {
                                // console.log(data);
                                // console.log(data);
                                if(data2.role_id == 299 || data2.is_admin == 1)
                                {
                                    if(data2.recject_status == 1) // for reject status
                                    {
                                        $.alert('Order Rejected');
                                        $('#dms_invoice_modal').modal('toggle');

                                    }
                                    else if(data2.cacncel_status == 1)
                                    {
                                        $.alert('Order Cancelled By Distributor');
                                        $('#dms_invoice_modal').modal('toggle');

                                    }
                                    else if(data2.order_confirm_status == 1) // for payment confirmation 
                                    {
                                        if(data2.payemt_status == 1)
                                        {
                                            if(data2.dispatch_status == 1)
                                            {
                                                if(data2.invoice_generate_status == 1)
                                                {
                                                    $.alert('Invoice Generated SuccessFully');
                                                    $('#dms_invoice_modal').modal('toggle'); 
                                                   
                                                }
                                                else
                                                {
                                                    $('#dealer_name_invoice').html('');
                                                    $('#invoice_address').html('');
                                                    $('#gst_no_invoice').html('');
                                                    $('#state_name_invoice').html('');
                                                    $('#dms_mobile_invoice').html('');
                                                    $('#order_date_invoice').html('');
                                                    $('#email_id_invoice').html('');
                                                    $('#order_no_invoice').html('');
                                                    $('#fullname_invoice').html('');
                                                    $('#remarks_invoice').html('');
                                                    $('#plant_name_invoice').html('');
                                                    $('#gr_no_invoice').html('');
                                                    $('#travell_mode').html('');
                                                    $('#driver_name_invoice').html('');
                                                    $('#transport_name_invoice').html('');
                                                    $('#driver_number_invoice').html('');
                                                    $('#vehical_number_invoice').html('');
                                                    $('#freight_invoice').html('');
                                                    $('.dms_invoice_body').html('');

                                                    if (data2.code == 401) 
                                                    {
                                                       
                                                    }
                                                    else if (data2.code == 200) 
                                                    {
                                                        // console.log(result_top);
                                                        // $("#dispatch_date_invoice").html("<a >"+data2.dispatch_date+"</a>");
                                                        $("#dms_mobile_invoice").html(data2.result_top.dealer_mobile);
                                                        $("#email_id_invoice").html(data2.result_top.dealer_email);
                                                        $("#dealer_name_invoice").html(data2.result_top.dealer_name);
                                                        $("#invoice_address").html(data2.address_invoice);
                                                        $("#state_name_invoice").html(data2.result_top.dealer_address);
                                                        $("#gst_no_invoice").html(data2.result_top.dealer_gst_no);
                                                        $("#order_no_invoice").html(data2.result_top.order_id);
                                                        $("#order_date_invoice").html(data2.result_top.sale_date);
                                                        $("#remarks_invoice").html(data2.dispatch_payment_remarks);
                                                        $("#fullname_invoice").html(data2.result_top.dealer_name);
                                                        $("#transport_name_invoice").html(data2.transport_details.transport_name);
                                                        $("#driver_number_invoice").html(data2.transport_details.driver_number);
                                                        $("#driver_name_invoice").html(data2.transport_details.driver_name);
                                                        $("#travell_mode").html(data2.transport_details.tavel_mode_name);
                                                        $("#plant_name_invoice").html(data2.transport_details.plant_name);
                                                        $("#freight_invoice").html(data2.transport_details.freight);
                                                        $("#vehical_number_invoice").html(data2.transport_details.vehical_number);
                                                        $("#gr_no_invoice").html(data2.transport_details.gr_no);

                                                        // $("#dealer_id_dispatch").val(data2.result_top.dealer_id);
                                                    
                                                        var Sno = 1;
                                                        var template = '';
                                                        var weight_invoice = '';
                                                        var mfg_date_invoice = '';
                                                        $.each(data2.invoice_data, function (u_key, u_value) {
                                                            weight_invoice = ((u_value.weight/1000)*(u_value.cases+u_value.scheme_qty));
                                                            if(u_value.mfg_date == null)
                                                            {
                                                                mfg_date_invoice = '';
                                                            }
                                                            else
                                                            {
                                                                mfg_date_invoice = u_value.mfg_date;
                                                            }
                                                            template += ('<tr><td>'+Sno+'</td><td>'+u_value.product_name+'</td><td>'+u_value.cases_rate+'</td><td>'+u_value.cases+'</td><td>'+u_value.scheme_qty+'</td><td>'+u_value.cases*u_value.cases_rate+'</td><td>'+weight_invoice.toFixed(3)+'</td><td>'+mfg_date_invoice+'</td><td>'+u_value.batch_no+'</td></tr>');
                                                            Sno++;
                                                        });   
                                                        // template += "<tr><td colspan = '2'>Grand Total</td><td>"+beat_total+"</td><td>"+dealer_retailer_total+"</td><td>"+beat_retailer_total+"</td><td></td></tr>";
                                                        $('.dms_invoice_body').append(template);
                                                    }
                                                }
                                               
                                            }
                                            else
                                            {
                                                
                                                $.alert('Firstly Dispatched the Order');
                                                $('#dms_invoice_modal').modal('toggle'); 
                                            }
                                        }
                                        else
                                        {
                                            $.alert('Firstly Confirm Payment');
                                            $('#dms_invoice_modal').modal('toggle');   
                                        }
                                        
                                    }
                                    else
                                    {
                                        $.alert('Firstly Confirm Order');
                                        $('#dms_invoice_modal').modal('toggle');   
                                    }
                               
                                }
                                else
                                {
                                    $.alert('Unauthorized Request Please Contact To Administrator !!');
                                    $('#dms_invoice_modal').modal('toggle');

                                }

                                

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });
                        $("#dms_invoice").val(action_id);
                        $("#dms_invoice_modal").modal();
                    },
                    RejectOrder: function () {
                         $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "post",
                            url: domain + '/dms_get_order_details',
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
                                        $.alert('Order Dispatched So now this Order cannot be reject');
                                        $('#dms_reject_order_modal').modal('toggle');
                                        // $.alert('Canceled!');
                                    }
                                    
                                    else if(data2.cacncel_status == 1)
                                    {
                                        $.alert('Order Cancelled By Distributor!!');
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
        $("#invoice_date").datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
        $("#payment_date").datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
        $(".mgf_date").datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
        $(".mgf_date").datepicker();

        $('#order_confirm_table_id').on('click','.removenewrow',function(){

              var table = $(this).closest('table');
              var i = table.find('.mytbody_dispatch').length;                 

              if(i==1)
              {
                 return false;
              }

             $(this).closest('tr').remove();
                var grand_fullfillment_cases_total = 0;
                var grand_fullfilmentschemqty_total = 0;
                var grand_fullfilmentweight_total = 0;
                var grand_fullfilmentrowweight_total = 0;
                var grand_fullfilmentrowcases_total = 0;
                var grand_fullfilmentorderamt_total = 0;
                var grand_product_case_total = 0;
                var grand_product_scheme_qty_total = 0;
                var grand_order_value_custom = 0;
                var grand_order_weight_custom = 0;
                
                var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
                var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
                // var g_fullfilmentweight = document.getElementsByName('weight[]');
                var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
                // var g_fullfilmentrowcases = document.getElementsByName('row_total_cases[]');
                var g_fullfilmentorderamt = document.getElementsByName('row_total_order_amt[]');
                var g_product_case = document.getElementsByName('product_cases[]');
                var g_product_scheme_qty = document.getElementsByName('product_scheme_qty[]');
                var g_order_value_custom = document.getElementsByName('order_value_custom[]');
                var g_order_weight_custom = document.getElementsByName('order_weight_custom[]');




                for (var po = 0; po < g_fullfilmentcases.length; po++)
                {
                    // var gtweight = Weight[po].value;
                    grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                    grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                    // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                    grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                    // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                    grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
                    grand_product_case_total += parseInt(g_product_case[po].value);
                    grand_product_scheme_qty_total += parseInt(g_product_scheme_qty[po].value);
                    grand_order_value_custom += parseInt(g_order_value_custom[po].value);
                    grand_order_weight_custom += parseInt(g_order_weight_custom[po].value);
                }
                // console.log(grand_fullfilmentschemqty_total);
                document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
                document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
                // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
                document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
                // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
                document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
                document.getElementById('grand_cases_total').innerHTML=grand_product_case_total; 
                document.getElementById('grand_schemeqty_total').innerHTML=grand_product_scheme_qty_total; 
                document.getElementById('grand_order_value_custom').innerHTML=grand_order_value_custom; 
                document.getElementById('grand_order_weight_custom').innerHTML=grand_order_weight_custom;

        });
        $('#table_details_dispatch').on('click','.removenewrowdispatch',function(){

              var table = $(this).closest('table');
              var i = table.find('.mytbody_dispatch7').length;                 

              if(i==1)
              {
                 return false;
              }

             $(this).closest('tr').remove();
                var grand_fullfillment_cases_total = 0;
                var grand_fullfilmentschemqty_total = 0;
                var grand_fullfilmentweight_total = 0;
                var grand_fullfilmentrowweight_total = 0;
                var grand_fullfilmentrowcases_total = 0;
                var grand_fullfilmentorderamt_total = 0;
               
                var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
                var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
                // var g_fullfilmentweight = document.getElementsByName('weight[]');
                var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
                // var g_fullfilmentrowcases = document.getElementsByName('row_fullfillment_cases[]');
                var g_fullfilmentorderamt = document.getElementsByName('row_fullfillment_order_amt[]');




                for (var po = 0; po < g_fullfilmentcases.length; po++)
                {
                    // var gtweight = Weight[po].value;
                    grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                    grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                    // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                    grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                    // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                    grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
                }
                // console.log(grand_fullfilmentschemqty_total);
                document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
                document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
                // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
                document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
                // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
                document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
        });
    </script>
    <script type="text/javascript">
        $("#dms_payment_modal_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            $('#submit_payment').html('');
            $('#submit_payment').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {   
                    // $('#dms_current_status').html('');
                    $.alert('Payment Submit SuccessFully');
                    $('#dms_payment_modal').modal('toggle');
                    // $('#dms_payment_modal').html('Payment Confirm');

                 
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }
                // complete:
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        }); // submit jquery for payment collection modal
         $("#dms_order_dispatch_form").submit(function(e) {
            var form = $(this);
            console.log(form);
            var url = form.attr('action');
            // var target=$('#result_state');
            $('#submit_dispatch').html('');
            $('#submit_dispatch').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            event.preventDefault();
            $.ajax({
                type: "POST",
                contentType: false,       
                processData:false,
                url: url,
                // data: form.serialize(), // serializes the form's elements.
                data: new FormData(this), // serializes the form's elements.
                success: function(data)
                {
                    $.alert('Order Dispatched SuccessFully');
                    $('#dms_order_dispatch_modal').modal('toggle');
                   
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        }); // submit jquery for order dispatch modal
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
                    $.alert('Order Rejected SuccessFully');
                    $('#dms_reject_order_modal').modal('toggle');
                   
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });

        $("#dms_order_confirm_modal_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            $('#submit_order_confirm').html('');
            $('#submit_order_confirm').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $.alert('Order Confirmed SuccessFully');
                    $('#dms_order_confirm_modal').modal('toggle');
                   
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }

            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });

        $(document).ready(function (e) {
            $('#dms_invoice_form').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('#submit_invoice').html('');
                $('#submit_invoice').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        $.alert('Invoice Generated SuccessFully');
                        $('#dms_invoice_modal').modal('toggle');
                                               
                    },
                    complete: function (data) {
                    $('#m-spinner').remove();
                    },
                    error: function (data) {
                        $.alert('Error Found Refresh And try Again Or Contact To Administrator');
                        $('#dms_invoice_modal').modal('toggle');
                        $('#m-spinner').remove();
                    }
                });
            }));

        });
        
    </script>   
    <script type="text/javascript">
        var cust_id = 1;
        // console.log(cust_id);
        function addfunction(str)
        {
            var y=str.substr(5,3);
            var x=1;
            if(cust_id > 1)
            {
                var d = parseInt(y)+parseInt(x);
            }
            else
            {
                var d = parseInt(y);
            }
            
            
            // console.log(sr_no);
            var product_filter = `<select  required="required" onchange="return product_function(this.id)"  name="product_id[]"
                                                id="product_id${d}">
                                            <option value="">Select Mode</option>
                                            @if(!empty($product_filter_array))
                                                @foreach($product_filter_array as $p_key=>$p_value)
                                                    <option value="{{$p_key}}">{{$p_value}}</option>
                                                @endforeach
                                            @endif
                                        </select>`;

            var case_rate = `<input  autocomplete="off" style="width:70px;" type="text" readonly name="product_rate_cases[]" id="case_r${d}" value='0'>`;
            var weight = `<input  autocomplete="off" style="width:70px;" type="hidden" name="weight[]" id="weight${d}" readonly value="0">`;
            var test_scheme = `<input style="width:70px;" type="hidden" readonly name="test_scheme[]" id="test_scheme${d}" value='0'>`;
            var range_first = `<input style="width:70px;" type="hidden" readonly name="range_first[]" id="range_first${d}" value='0'>`;
            var range_second = `<input style="width:70px;" type="hidden" readonly name="range_second[]" id="range_second${d}" value='0'>`;
            var product_case = `<input  autocomplete="off" style="width:70px;" type="text" readonly name="product_cases[]"  value='0'>`;
            var fullfillment_cases = `<input  autocomplete="off" style="width:70px;" value="0" name="fullfillment_cases[]" onkeyup="return mulfuncOrderConfirm(this.id)" id="case_f${d}">`;

            var scheme_qty = `<input type="text" autocomplete="off" value="0" readonly name="product_scheme_qty[]" style="width:70px;"  id="p_sche_f${d}">`;
            var order_weight_custom = `<input type="text" autocomplete="off" value="0" name="order_weight_custom[]" readonly  style="width:70px;" >`;
            var order_value_custom = `<input type="text" autocomplete="off" value="0" name="order_value_custom[]" readonly  style="width:70px;"  >`;
            var fullfillment_scheme_qty = `<input  autocomplete="off" value="0" name="fullfillment_scheme_qty[]" style="width:70px;" onkeyup="return mulfunc_casesOrderConfirm(this.id)" id="sche_f${d}">`;
            var final_weight = `<input autocomplete="off"  style="width:70px;" name="row_total_weight[]" type="text" readonly id="fin_we${d}" value="0" >`;
            var final_cases = `<input  autocomplete="off" style="width:70px;" name="row_total_cases[]" type="hidden" readonly id="fin_ca${d}" value="0" >`;
            var final_order = `<input  autocomplete="off" style="width:70px;" name="row_total_order_amt[]" type="text" readonly id="fin_or${d}" value="0" >`;


            var template = ('<tr><td>'+d+'</td><td>'+product_filter+'</td><td>'+case_rate+'</td>'+weight+'<td>'+product_case+'</td><td>'+scheme_qty+'</td><td>'+order_weight_custom+'</td><td>'+order_value_custom+'</td><td>'+fullfillment_cases+'</td><td>'+fullfillment_scheme_qty+'</td><td>'+final_weight+'</td>'+final_cases+' <td>'+final_order+'</td><td width="70px" ><i id=sr_no'+d+' title="more" class="fa fa-plus" aria-hidden="true" onclick="return addfunction(this.id)"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></tr>');
           d++;

            $('.mytbody_order_confirm').append(template);
            var template2 ='';
            var total_cases_order_confirm = 0;
            var weight_t_order_confirm = 0;
            var total_scheme_qty_order_confirm = 0;
            $(".mytfoot_order_confirm").html('');
            template2 += "<tr><td colspan = '3'>Grand Total</td><td id='grand_cases_total'>"+total_cases_order_confirm+"</td><td id='grand_schemeqty_total'>"+total_scheme_qty_order_confirm+"</td><td id='grand_order_weight_custom'>0</td><td id='grand_order_value_custom'>0</td><td id='grand_fullfillment_cases_total'>0</td><td id='grand_fullfilmentschemqty_total'>0</td><td id='grand_fullfilmentrowweight_total'>0</td><td id='grand_fullfilmentorderamt_total'>0</td></tr>";
            $('.mytfoot_order_confirm').append(template2);
            // template += ('')
           cust_id++;

            var grand_fullfillment_cases_total = 0;
            var grand_fullfilmentschemqty_total = 0;
            var grand_fullfilmentweight_total = 0;
            var grand_fullfilmentrowweight_total = 0;
            var grand_fullfilmentrowcases_total = 0;
            var grand_fullfilmentorderamt_total = 0;
            var grand_product_case_total = 0;
            var grand_product_scheme_qty_total = 0;
            var grand_order_value_custom = 0;
            var grand_order_weight_custom = 0;
            
            var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
            var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
            // var g_fullfilmentweight = document.getElementsByName('weight[]');
            var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
            // var g_fullfilmentrowcases = document.getElementsByName('row_total_cases[]');
            var g_fullfilmentorderamt = document.getElementsByName('row_total_order_amt[]');
            var g_product_case = document.getElementsByName('product_cases[]');
            var g_product_scheme_qty = document.getElementsByName('product_scheme_qty[]');
            var g_order_value_custom = document.getElementsByName('order_value_custom[]');
            var g_order_weight_custom = document.getElementsByName('order_weight_custom[]');




            for (var po = 0; po < g_fullfilmentcases.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
                grand_product_case_total += parseInt(g_product_case[po].value);
                grand_product_scheme_qty_total += parseInt(g_product_scheme_qty[po].value);
                grand_order_value_custom += parseInt(g_order_value_custom[po].value);
                grand_order_weight_custom += parseInt(g_order_weight_custom[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
            document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
            // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
            document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
            // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
            document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
            document.getElementById('grand_cases_total').innerHTML=grand_product_case_total; 
            document.getElementById('grand_schemeqty_total').innerHTML=grand_product_scheme_qty_total; 
            document.getElementById('grand_order_value_custom').innerHTML=grand_order_value_custom; 
            document.getElementById('grand_order_weight_custom').innerHTML=grand_order_weight_custom;
        }

    </script>

    <script type="text/javascript">
        var cust_id = 1;
        // console.log(cust_id);
        function addfunctionDispatch(str)
        {
            var y=str.substr(5,3);
            var x=1;
            if(cust_id > 1)
            {
                var d = parseInt(y)+parseInt(x);
            }
            else
            {
                var d = parseInt(y);
            }
            
            var template2 = '';
            var total_cases = 0;
            var weight_t = 0;
            var total_scheme_qty = 0;
            var total_amt = 0;
            var total_weight = 0;
            // console.log(sr_no);
            var product_filter = `<select  required="required" onchange="return product_function_dispatch(this.id)"  name="product_id[]"
                                                id="product_id${d}">
                                            <option value="">Select Mode</option>
                                            @if(!empty($product_filter_array))
                                                @foreach($product_filter_array as $p_key=>$p_value)
                                                    <option value="{{$p_key}}">{{$p_value}}</option>
                                                @endforeach
                                            @endif
                                        </select>`;

            var case_rate = `<input  autocomplete="off" style="width:70px;" type="text" readonly name="product_rate_cases[]" id="case_r${d}" value='0'>`;
            var weight = `<input  autocomplete="off"  style="width:70px;" type="hidden" name="weight[]" id="weight${d}" readonly value="0">`;
            // var weight = `<input style="width:70px;" type="text" readonly name="product_rate_cases[]" id='+"weight"+inc+' value=''>`;
            var product_case = `<input  autocomplete="off" style="width:70px;" type="text" readonly name="product_cases[]"  value='0'>`;
            var fullfillment_cases = `<input  autocomplete="off" style="width:70px;" name="fullfillment_cases[]"  value="0" onkeyup="return mulfunc(this.id)" id="case_f${d}">`;

            var fullfillment_scheme_qty = `<input  autocomplete="off" name="fullfillment_scheme_qty[]" style="width:70px;" value="0"  onkeyup="return mulfunc_cases(this.id)" id="sche_f${d}">`;
            var final_weight = `<input  autocomplete="off" style="width:70px;" name="row_total_weight[]" type="text" readonly id="fin_we${d}" value="0" >`;
            var final_cases = `<input  autocomplete="off" style="width:70px;" name="row_fullfillment_cases[]" type="hidden" readonly id="fin_ca${d}" value="0" >`;
            var final_order = `<input  autocomplete="off" style="width:70px;" name="row_fullfillment_order_amt[]" type="text" readonly id="fin_or${d}" value="0" >`;

            var mgf_date = `<input required autocomplete="off"  style="width:70px;" class="mgf_date date-picker" type="text" onclick="return datepicker_function(this.id)" id="mfg_date${d}"  name="mgf_date[]" value="" >`;
            var batch_no = `<input required  autocomplete="off" style="width:70px;" type="text"  name="batch_no[]" value="0" >`;

            var template = ('<tr><td>'+d+'</td><td>'+product_filter+'</td><td>'+case_rate+'</td><td>'+fullfillment_cases+'</td><td>'+fullfillment_scheme_qty+'</td><td>'+final_weight+'</td> <td>'+final_order+'</td><td>'+mgf_date+'</td><td>'+batch_no+'</td><td width="70px" ><i id=sr_no'+d+' title="more" class="fa fa-plus" aria-hidden="true" onclick="return addfunctionDispatch(this.id)"></i>&nbsp&nbsp<i  title="Less"  class="removenewrowdispatch fa fa-minus"/></i></tr>');
           d++;

            $('.mytbody_dispatch').append(template);
            template2 += "<tr>"+weight+"<td colspan = '3'>Grand Total</td><td id='grand_fullfillment_cases_total'>"+total_cases+"</td><td id='grand_fullfilmentschemqty_total'>"+total_scheme_qty+"</td><td id='grand_fullfilmentrowweight_total'>"+total_weight+"</td><td id='grand_fullfilmentorderamt_total'>"+total_amt+"</td>"+final_cases+"</tr>";
            $('.mytfoot_dispatch').html('');
            $('.mytfoot_dispatch').append(template2);
            // template += ('')
           cust_id++;

            var grand_fullfillment_cases_total = 0;
            var grand_fullfilmentschemqty_total = 0;
            var grand_fullfilmentweight_total = 0;
            var grand_fullfilmentrowweight_total = 0;
            var grand_fullfilmentrowcases_total = 0;
            var grand_fullfilmentorderamt_total = 0;
           
            var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
            var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
            // var g_fullfilmentweight = document.getElementsByName('weight[]');
            var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
            // var g_fullfilmentrowcases = document.getElementsByName('row_fullfillment_cases[]');
            var g_fullfilmentorderamt = document.getElementsByName('row_fullfillment_order_amt[]');




            for (var po = 0; po < g_fullfilmentcases.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
            document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
            // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
            document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
            // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
            document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 

            var x_gre= document.getElementById("carrying_capacity").value;
            var y_gre= document.getElementById("grand_fullfilmentrowweight_total").innerHTML;
                
            if(x_gre<y_gre)
            {
                document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity <br><b style="color:red;"> !! Please Enter Carrying Capacity Greater Then Total Weight !! </b>';
                document.getElementById("carrying_capacity").value = '';
            }
            else
            {
                document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity';

            }
        }

    </script>


    
    <script>
    function mulfunc(str2)
    {
        var d=str2.substr(6,3);
        var x= document.getElementById("weight"+d).value;
        var y= document.getElementById("case_f"+d).value;
        var z= document.getElementById("case_r"+d).value;
        var total = x*y;
        var toatl_amount = y*z;
        document.getElementById("fin_we"+d).value= total.toFixed(3);
        // document.getElementById("fin_ca"+d).value= y;
        document.getElementById("fin_or"+d).value= toatl_amount;
        var grand_fullfillment_cases_total = 0;
        var grand_fullfilmentschemqty_total = 0;
        var grand_fullfilmentweight_total = 0;
        var grand_fullfilmentrowweight_total = 0;
        var grand_fullfilmentrowcases_total = 0;
        var grand_fullfilmentorderamt_total = 0;
        
        var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
        var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
        // var g_fullfilmentweight = document.getElementsByName('weight[]');
        var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
        // var g_fullfilmentrowcases = document.getElementsByName('row_fullfillment_cases[]');
        var g_fullfilmentorderamt = document.getElementsByName('row_fullfillment_order_amt[]');




        for (var po = 0; po < g_fullfilmentcases.length; po++)
        {
            // var gtweight = Weight[po].value;
            grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
            grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
            // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
            grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
            // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
            grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
        }
        // console.log(grand_fullfilmentschemqty_total);
        document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
        document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
        // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
        document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
        // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
        document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 


        var x_gre= document.getElementById("carrying_capacity").value;
        var y_gre= document.getElementById("grand_fullfilmentrowweight_total").innerHTML;
            
        if(x_gre<y_gre)
        {
            document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity <br><b style="color:red;"> !! Please Enter Carrying Capacity Greater Then Total Weight !! </b>';
            document.getElementById("carrying_capacity").value = '';
        }
        else
        {
            document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity';

        }
        
    }
    function mulfuncOrderConfirm(str2)
    {
        var d=str2.substr(6,3);
        var x= document.getElementById("weight"+d).value;
        var y= document.getElementById("case_f"+d).value;
        var z= document.getElementById("case_r"+d).value;
        var range_first= document.getElementById("range_first"+d).value;
        var range_second= document.getElementById("range_second"+d).value;
        var test_scheme= document.getElementById("test_scheme"+d).value;
        var total = x*y;
        var toatl_amount = y*z;
        document.getElementById("fin_we"+d).value= total.toFixed(3);
        document.getElementById("fin_ca"+d).value= y;
        document.getElementById("fin_or"+d).value= toatl_amount;

        if(y >= range_first && y <= range_second)
        {

            // console.log('q');
            document.getElementById("sche_f"+d).value= test_scheme;

        }



        var grand_fullfillment_cases_total = 0;
        var grand_fullfilmentschemqty_total = 0;
        var grand_fullfilmentweight_total = 0;
        var grand_fullfilmentrowweight_total = 0;
        var grand_fullfilmentrowcases_total = 0;
        var grand_fullfilmentorderamt_total = 0;
        var grand_product_case_total = 0;
        var grand_product_scheme_qty_total = 0;
        var grand_order_value_custom = 0;
        var grand_order_weight_custom = 0;
       
        var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
        var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
        // var g_fullfilmentweight = document.getElementsByName('weight[]');
        var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
        // var g_fullfilmentrowcases = document.getElementsByName('row_total_cases[]');
        var g_fullfilmentorderamt = document.getElementsByName('row_total_order_amt[]');
        var g_product_case = document.getElementsByName('product_cases[]');
        var g_product_scheme_qty = document.getElementsByName('product_scheme_qty[]');
        var g_order_value_custom = document.getElementsByName('order_value_custom[]');
        var g_order_weight_custom = document.getElementsByName('order_weight_custom[]');




        for (var po = 0; po < g_fullfilmentcases.length; po++)
        {
            // var gtweight = Weight[po].value;
            grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
            grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
            // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
            grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
            // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
            grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
            grand_product_case_total += parseInt(g_product_case[po].value);
            grand_product_scheme_qty_total += parseInt(g_product_scheme_qty[po].value);
            grand_order_value_custom += parseInt(g_order_value_custom[po].value);
            grand_order_weight_custom += parseInt(g_order_weight_custom[po].value);
        }
        // console.log(grand_fullfilmentschemqty_total);
        document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
        document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
        // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
        document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
        // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
        document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
        document.getElementById('grand_cases_total').innerHTML=grand_product_case_total; 
        document.getElementById('grand_schemeqty_total').innerHTML=grand_product_scheme_qty_total; 
        document.getElementById('grand_order_value_custom').innerHTML=grand_order_value_custom; 
        document.getElementById('grand_order_weight_custom').innerHTML=grand_order_weight_custom; 
        
    }
    function mulfunc_casesOrderConfirm(str2)
    {
        var d=str2.substr(6,3);
        var x= document.getElementById("weight"+d).value;
        var y= document.getElementById("case_f"+d).value;
        var z= document.getElementById("sche_f"+d).value;
        // var total = x*y;
        if(z != '')
        {
            var total_cases = parseInt(y)+parseInt(z);
            var total = x*total_cases;

            document.getElementById("fin_we"+d).value= total.toFixed(3);
            document.getElementById("fin_ca"+d).value= total_cases;

            var grand_fullfillment_cases_total = 0;
            var grand_fullfilmentschemqty_total = 0;
            var grand_fullfilmentweight_total = 0;
            var grand_fullfilmentrowweight_total = 0;
            var grand_fullfilmentrowcases_total = 0;
            var grand_fullfilmentorderamt_total = 0;
            var grand_product_case_total = 0;
            var grand_product_scheme_qty_total = 0;
            var grand_order_value_custom = 0;
            var grand_order_weight_custom = 0;
            var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
            var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
            // var g_fullfilmentweight = document.getElementsByName('weight[]');
            var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
            // var g_fullfilmentrowcases = document.getElementsByName('row_total_cases[]');
            var g_fullfilmentorderamt = document.getElementsByName('row_total_order_amt[]');
            var g_product_case = document.getElementsByName('product_cases[]');
            var g_product_scheme_qty = document.getElementsByName('product_scheme_qty[]');
            var g_order_value_custom = document.getElementsByName('order_value_custom[]');
            var g_order_weight_custom = document.getElementsByName('order_weight_custom[]');




            for (var po = 0; po < g_fullfilmentcases.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
                grand_product_case_total += parseInt(g_product_case[po].value);
                grand_product_scheme_qty_total += parseInt(g_product_scheme_qty[po].value);
                grand_order_value_custom += parseInt(g_order_value_custom[po].value);
                grand_order_weight_custom += parseInt(g_order_weight_custom[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
            document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
            // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
            // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
            document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
            document.getElementById('grand_cases_total').innerHTML=grand_product_case_total; 
            document.getElementById('grand_schemeqty_total').innerHTML=grand_product_scheme_qty_total; 
            document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
            document.getElementById('grand_order_value_custom').innerHTML=grand_order_value_custom; 
            document.getElementById('grand_order_weight_custom').innerHTML=grand_order_weight_custom; 

            // document.getElementById("fin_or"+d).value= y;
        }
      
    }
    function datepicker_function(str)
    {
        var d=str.substr(8,3);

        $("#mfg_date"+d).datetimepicker  ( {
        format: 'YYYY-MM-DD'
        });
    }
    function mulfunc_cases(str2)
    {
        var d=str2.substr(6,3);
        var x= document.getElementById("weight"+d).value;
        var y= document.getElementById("case_f"+d).value;
        var z= document.getElementById("sche_f"+d).value;
        // var total = x*y;
        if(z != '')
        {
            var total_cases = parseInt(y)+parseInt(z);
            var total = x*total_cases;

            document.getElementById("fin_we"+d).value= total.toFixed(3);
            // document.getElementById("fin_ca"+d).value= total_cases;

            var grand_fullfillment_cases_total = 0;
            var grand_fullfilmentschemqty_total = 0;
            var grand_fullfilmentweight_total = 0;
            var grand_fullfilmentrowweight_total = 0;
            var grand_fullfilmentrowcases_total = 0;
            var grand_fullfilmentorderamt_total = 0;
        
            var g_fullfilmentcases = document.getElementsByName('fullfillment_cases[]');
            var g_fullfilmentschemqty = document.getElementsByName('fullfillment_scheme_qty[]');
            // var g_fullfilmentweight = document.getElementsByName('weight[]');
            var g_fullfilmentrowweight = document.getElementsByName('row_total_weight[]');
            // var g_fullfilmentrowcases = document.getElementsByName('row_fullfillment_cases[]');
            var g_fullfilmentorderamt = document.getElementsByName('row_fullfillment_order_amt[]');
        




            for (var po = 0; po < g_fullfilmentcases.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_fullfillment_cases_total += parseInt(g_fullfilmentcases[po].value);
                grand_fullfilmentschemqty_total += parseInt(g_fullfilmentschemqty[po].value);
                // grand_fullfilmentweight_total += parseInt(g_fullfilmentweight[po].value);
                grand_fullfilmentrowweight_total += parseInt(g_fullfilmentrowweight[po].value);
                // grand_fullfilmentrowcases_total += parseInt(g_fullfilmentrowcases[po].value);
                grand_fullfilmentorderamt_total += parseInt(g_fullfilmentorderamt[po].value);
               
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_fullfillment_cases_total').innerHTML=grand_fullfillment_cases_total; 
            document.getElementById('grand_fullfilmentschemqty_total').innerHTML=grand_fullfilmentschemqty_total; 
            // document.getElementById('grand_fullfilmentweight_total').innerHTML=grand_fullfilmentweight_total; 
            document.getElementById('grand_fullfilmentrowweight_total').innerHTML=grand_fullfilmentrowweight_total; 
            // document.getElementById('grand_fullfilmentrowcases_total').innerHTML=grand_fullfilmentrowcases_total; 
            document.getElementById('grand_fullfilmentorderamt_total').innerHTML=grand_fullfilmentorderamt_total; 
            
            // document.getElementById("fin_or"+d).value= y;
            var x_gre= document.getElementById("carrying_capacity").value;
            var y_gre= document.getElementById("grand_fullfilmentrowweight_total").innerHTML;
                
            if(x_gre<y_gre)
            {
                document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity <br><b style="color:red;"> !! Please Enter Carrying Capacity Greater Then Total Weight !! </b>';
                document.getElementById("carrying_capacity").value = '';
            }
            else
            {
                document.getElementById("carrying_capacity_lable").innerHTML = 'Carrying Capacity';

            }
        }
      
    }
    
    function product_function(str)
    {
        var d=str.substr(10,3);
        var product_id= document.getElementById("product_id"+d).value;

        // var e=str.substr(6,3);
        // var product_id= document.getElementById("weight"+e).value;
        // var dealer_id= document.getElementById("product_id"+d).value;
        var dealer_id = $('#dealer_id_confirm').val();
        // alert(product_id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url: domain + '/dms_rate_bhelaf_product_id',
            dataType: 'json',
            data: {'product_id': product_id, 'dealer_id': dealer_id},
            success: function (data) 
            {
                if(data.code == 401)
                {

                }
                else if(data.code == 200)
                {   
                    // con
                    $('#weight'+d).val(data.product_details.weight/1000);
                    $('#case_r'+d).val(data.product_details.dealer_rate);
                    $('#test_scheme'+d).val(data.scheme_details.free_qty);
                    $('#range_first'+d).val(data.scheme_details.range_first);
                    $('#range_second'+d).val(data.scheme_details.range_second);
                }
            }
        });

    }
    function product_function_dispatch(str)
    {
        var d=str.substr(10,3);
        var product_id= document.getElementById("product_id"+d).value;

        // var e=str.substr(6,3);
        // var product_id= document.getElementById("weight"+e).value;
        // var dealer_id= document.getElementById("product_id"+d).value;
        var dealer_id = $('#dealer_id_dispatch').val();
        // alert(product_id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url: domain + '/dms_rate_bhelaf_product_id',
            dataType: 'json',
            data: {'product_id': product_id, 'dealer_id': dealer_id},
            success: function (data) 
            {
                if(data.code == 401)
                {

                }
                else if(data.code == 200)
                {   
                    // con
                    $('#weight'+d).val(data.product_details.weight/1000);
                    $('#case_r'+d).val(data.product_details.dealer_rate);
                }
            }
        });

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
                          
                            // console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        // console.log(template);
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
     <script>
    $('.logs_modal').click(function() {
            var order_id = $(this).attr('order_id');
            
            if (order_id != '') 
            {
                $('.tbody_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/dms_status_logs',
                    dataType: 'json',
                    data: "order_id=" + order_id,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            var template = '';
                            var Sno = 1;
                            $.each(data.data_return, function (u_key, u_value) {
                               
                                template += ('<tr><td>'+Sno+'</td><td>'+u_value.tiltle+'</td><td>'+u_value.date+'</td><td>'+u_value.time+'</td></tr>');
                                Sno++;
                            });   
                            $('.tbody_logs').append(template);

                            
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
                      null, null,null,null,null, null,null,null,null,null,null,null,null,null,
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