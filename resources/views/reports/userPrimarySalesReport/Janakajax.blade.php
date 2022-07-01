@if(!empty($records))
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
        <td colspan="20"><h3>User Primary Sale Order Report</h3></td>
    </tr>
    <tr>
       <!-- <th>S.No.</th> -->
        <th>Order Sequence</th>
        <th>Order Id</th>
        <th>Customer Name</th>
        <th>Sales Person Name</th>
        <th>Order Date</th>

        <th>Order Amount</th>
        <th>Discount Type</th>
        <th>Discount Amount</th>
        <th>Order Total</th>

        <th>Dispatch Through</th>
        <th>Destination</th>
        <th>Comment</th>


        <th>Order Details</th>

        <th>Actions</th>

    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=[]; $count_call_0=[];?>

    @if(!empty($records) && count(array($records))>0)
    
    @foreach($records as $record)
    @if(count(array($record->order_id))>0)
   
        <?php 
     
        $did = Crypt::encryptString($record->did);
        $uid = Crypt::encryptString($record->uid);

        if($record->discount_type == 1){
            $text = "Percentage";
        }
        elseif($record->discount_type == 2){
            $text = "Value";
        }
        else{
            $text = "N/A";
        }
      
        ?>
        <tr>
            <!-- <td>{{$i++}}</td> -->
            <td>{{$record->janak_order_sequence}}</td>
            
            <td><a title="PDF Generation" order_id="{{$record->order_id}}" class="myModal2" id="order_id" data-toggle="modal" data-target="#myModal2">{{$record->order_id}}</a></td>
            <td><a href="{{url('distributor/'.$did)}}">{{$record->dealer_name}}</a></td>
            <td><a href="{{url('user/'.$uid)}}">{{$record->user_name}}</a></td>
            <td>{{!empty($record->sale_date)?date('d-M-Y',strtotime($record->sale_date)):'NA'}}</td>

            <td>{{!empty($record->amount_before_discount)?$record->amount_before_discount:0}}</td>
            <td>{{$text}}</td>
            <td>{{$record->discount_value}}</td>
            <td>{{!empty($record->amount_after_discount)?$record->amount_after_discount:0}}</td>

            <td>{{$record->dispatch_through}}</td>
            <td>{{$record->destination}}</td>
            <td>{{$record->comment}}</td>

            

            
            <td>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Rate</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    @if(!empty($record))
                        <?php  $i++; $total=0;
                        $totalqty=0;
                        $totalcases=0;
                        // $gtotal = 0;
                        // $gqty = 0; 
                        ?>
                        @foreach($order_detial_arr[$record->order_id] as $k1=>$data1)
                        <?php $value = 0; ?>
                            <tr>
                                <td>{{$data1->name}}</td>
                                <td>{{$case_rate=$data1->pr_rate}}</td>                                                        
                                <td>{{$cases=$data1->cases}}</td>
                                <td>{{$case_rate*$cases}}</td>
                             {{--   <td>{{($cases*$case_rate)}}</td> --}}

                            
                               
                            </tr>
                             <?php 
                             $total+=($cases*$case_rate); 
                             // $totalqty+=$data1->pcs;
                             $totalcases+=$data1->cases;
                             // $totalweight+=$data1->weight;

                             $gtotal+=$case_rate*$cases; 
                             $gqty+=$data1->cases;
                             // $gweight+=$data1->weight;
                             ?>
                        @endforeach
                    @else
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                          
                        </tr>
                   @endif
                   <tfoot>
                     
                        <tr>
                            <th>Total</th>
                            <th></th>
                            
                            <th>{{$totalcases}}</th>
                            <th>{{ROUND($total,3)}}</th>
                        </tr>
                    </tfoot>
                </table>
            </td>

            <td>
              <a href="#" orderid="{{ $record->order_id }}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="Update">
                    <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div><br>

                   <a title="PDF Generation" order_id="{{$record->order_id}}" class="myModal2" id="order_id" data-toggle="modal" data-target="#myModal2"> 
                    <i class="fa fa-download" aria-hidden="true" ></i>
                   </a>
                </a>

           
            </td>
        </tr>
    @endif
    @endforeach  


        <tr>
            <th colspan="12"><strong>Grand Total</strong></th>
          
            <td>
                <table class="table">

                    <tr>
                        <th>Total Qty</th>
                        <th>Total Sale</th>

                     
                    </tr>
                    <tr>
                        <td>{{$gqty}}</td>
                        <td>{{$gtotal}}</td>

                      
                    </tr>


                   


                </table>
            </td>
            <td></td>
        </tr> 

  
    @else
    <tr>
        <td colspan="20">
            <p class="alert alert-danger">No data found</p>
        </td>
    </tr>          
        @endif
    </tbody>
</table>



<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:1300px;">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReportDetails()" href="javascript:void(0)" class="nav-link">Order Details Export  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="get" id="filter_distributor" action="primaryOrderDetailsUpdate" enctype="multipart/form-data">

                 
                    <div class="table-header center">
                        <span>Order Details </span>
                    </div>
                  

                        <table id="simple-table-details" class="table table-bordered">
                        
                            <thead>
                                <th>Sr.No</th>
                                <th>Order No.</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
                            </tbody>
                    
                        </table>

                        <div class="col-lg-4">
                            <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Update
                                </button>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
    $('.logs_modal').click(function() {
            var orderid = $(this).attr('orderid');
            if (orderid != '') 
            {
                $('.tbody_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/primaryOrderDetails',
                    dataType: 'json',
                    data: "orderid=" + orderid,
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
                               
                                template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.final_secondary_qty+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.final_secondary_rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');
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



<script type="text/javascript">
          $('#simple-table-details').on('keyup','.qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        var case_qty_val   =tr.find('.case_qty_val').val();
        var case_rate_val   =tr.find('.case_rate_val').val();

        var tval=((rateTx*pieces)).toFixed(2);
        stk_val.val(tval);
  })
  </script>


<script type="text/javascript">
    $('.myModal2').click(function() {
        var order_id = $(this).attr('order_id');
        if (order_id != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/primaryOrderWisePdfFormat',
                dataType: 'json',
                data: "order_id=" + order_id,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {

                        var save = document.createElement('a');
                        save.href = domain+'/pdf/'+data.pdf_name;
                        save.target = '_blank';
                        save.download = 'order.pdf' || 'unknown';

                        var evt = new MouseEvent('click', {
                            'view': window,
                            'bubbles': true,
                            'cancelable': false
                        });
                        save.dispatchEvent(evt);


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
    });
</script>