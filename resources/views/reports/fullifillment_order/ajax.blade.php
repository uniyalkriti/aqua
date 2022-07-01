@if(!empty($main_query_data))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif
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
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <td colspan="20"><h3>Order Fulfillment List Report</h3></td>
    </tr>
    <tr>
        <th >S.No.</th>
        <th >Reciept Image</th>
        <th >Invoice No.</th>
        <th >Invoice Date</th>
        <th >Action</th>
        <th >Date</th>
        <th >Time</th>
        <th >Order No</th>
        <th >{{Lang::get('common.location2')}}</th>
        <th >{{Lang::get('common.location3')}}</th>
        <th >Distributor Name</th>
        <th >Distributor No</th>
        <th >Details</th>
    </tr>
  
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();?>

    @if(!empty($main_query_data))
    
    @foreach($main_query_data as $k=> $r)
    @if(!empty($r->order_id))
   
        <?php 
        // $user_id = Crypt::encryptString($r->user_id); 
        // $retailer_id = Crypt::encryptString($r->retailer_id); 
        $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>

            <td class="profile-activity clearfix">
                <a id="user_image" class="“cboxElement”"  data-toggle="modal" data-rel="“colorbox”">
                    <img id="user_image" width="50" height="50"  src="{{$r->reciept_image}}" alt=" " />
                </a>
            </td>
            <td>{{$r->invoice_no_p}}</td>
            <td>{{$r->invoice_date}}</td>
            <td>
                <button type="button" dealerid="{{$r->dealer_id}}" orderid="{{$r->order_id}}"
                    data-toggle="modal" data-target="#reciept_modal" class="btn btn-default reciept_modal btn-round btn-white">
                    <i class="ace-icon fa fa-send green"></i>
                    Action 
                </button>
            </td>
            <td>{{$r->date}}</td>
            <td>{{$r->time}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l2_name}}</td>
            <td>{{$r->l3_name}}</td>
            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
            <td>{{!empty($r->dealer_no)?$r->dealer_no:''}}    </td>
  
            <td>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th> 
                            <th>Rate</th>
                            <th>Scheme Qty <br>(CASES)</th>
                            <th>Cases</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                @if(!empty($r))
                    <?php  $i++; $total=0;
                    $totalqty=0;
                    $totalweight=0; ?>
                    @foreach($sub_query_data[$r->order_id] as $k1=>$data1)
                         <tr><td>{{$data1->product_name}}</td>
                         <!-- <td></td> -->
                         <td>{{$data1->product_case_rate}}</td>
                         <td>{{$data1->product_fullfiment_scheme_qty}}</td>
                         <td>{{$data1->product_fullfiment_cases}}</td>
                         <td>{{($data1->sale_value)}}</td> </tr>
                         <?php 
                         $total+=$data1->sale_value; 
                         $totalqty+=$data1->product_fullfiment_cases;
                         

                         $gtotal+=$data1->sale_value; 
                         $gqty+=$data1->product_fullfiment_cases;
                         
                         ?>
                    @endforeach
                @else
                    <tr>
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
                        <th></th>
                        <th>{{$totalqty}}</th>
                        <th>{{$total}}</th>
                    </tr>
                </tfoot>
                </table>
            </td>
        </tr>
            @endif
            @endforeach  

            <tr>
            <th colspan="12"><strong>Grand Total</strong></th>
            <!-- <td></td> -->
           
            <td>
                <table class="table">
                    <tr>
                        <th>Total Qty</th>
                        <th>Total Value</th></tr>
                    <tr>
                    <td>{{$gqty}}</td>
                    <td>{{$gtotal}}</td></tr>
                </table>
            </td>
            </tr>  
            @else
               <tr>
            <td colspan="12">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
        @endif
    </tbody>
</table>

<div class="modal fade" id="reciept_modal" role="dialog">
    <div class="modal-dialog" style="width:400px;">
    
        <!-- Modal content-->
        <div class="modal-content" id ="modalDiv">
            
            <div class="modal-body" id="qwerty">
                <form action="dms_submit_reciept_no" method="post" id="reciept_modal_form" enctype="multipart/form-data">
                    {!! csrf_field() !!}

                    <input type="hidden" id="dms_reject_order" name="dms_reject_order" value="">
                    <input type="hidden" id="dealer_id_c" name="dealer_id" value="">
                    <input type="hidden" id="order_id_c" name="order_id" value="">
                    
                    <table class="table-bordered" >
                        <th style="background-color:#fcf8e3; color:black; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>
                        <th style="background-color:#fcf8e3; color:black; width:560px; height: 30px; text-align:right;"> Preview&nbsp&nbsp&nbsp  </th>

                    </table>
                   

                    <table border="0" cellspacing="0" cellpadding="0" width="100%">

                        <tr>
                            <td style="text-align: left; ">&nbsp;&nbsp;Upload Image :</td>
                            <td style="text-align: left;">&nbsp;&nbsp;<input style="top-margin:100px;" type="file" class="form-control-file" name="imageFile" id="imageFile" aria-describedby="fileHelp" onchange="readURL(this);"></td>
                        </tr>
                      
                    </table>
                    <div class="row">
                        <div class="col-xs-12">
                                <div class="">
                                    <div class="col-xs-2">
                                            <label class="control-label no-padding-right"
                                                   for="head_quarter"> </label>
                                             
                                             
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
<script type="text/javascript">
    
    $('.reciept_modal').click(function() {
            var dealer_id = $(this).attr('dealerid');
            var order_id = $(this).attr('orderid');
            // $('.mytbody').html('');
            

            $('#dealer_id_c').html('');
            $('#order_id_c').html('');
            
            $('#dealer_id_c').val(dealer_id);
            $('#order_id_c').val(order_id);

        });


        $(document).ready(function (e) {
            $('#reciept_modal_form').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        alert('Reciept Submitted SuccessFully');
                        $('#reciept_modal').modal('toggle');
                       
                    },
                    error: function(data){
                        // console.log("error");
                        // console.log(data);
                    }
                });
            }));

        });
</script>


