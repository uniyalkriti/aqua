@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
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
        <td colspan="14"><h3>Primary Sale</h3></td>
    </tr>
    <tr>
        <th rowspan="2">S.No.</th>
        <th rowspan="2">Order Id</th>
        <th rowspan="2">{{Lang::get('common.dealer_module')}} Name</th>
        <th rowspan="2">{{Lang::get('common.dealer_module')}} Town</th>
        {{--<th>Created Date</th>--}}
        <th rowspan="2">User Name</th>
        <th rowspan="2">Sale Date</th>
        <th rowspan="2">Receive Date</th>
        {{--<th>Date Time</th>--}}
        <th colspan="7">Order Details</th>
        {{--<th>Company Id</th>--}}
    </tr>
    <tr>
        <th>Product</th>
        <th>Pcs</th>
        <th>Pcs Rate</th>
        <th>Cases</th>
        <th>Cases Rate</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
    <tbody>
    @if(!empty($records) && count($records)>0)
        <?php $i = 1 ?>
        @foreach($records as $record)
        <?php
        // $did = Crypt::encryptString($record->did);
        // $uid = Crypt::encryptString($record->uid);
        ?>

                    {{--<table class="table">--}}
                        @if(!empty($order_detial_arr[$record->order_id]))
                            @foreach($order_detial_arr[$record->order_id] as $order_data)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$record->order_id}}</td>
                                    <td>{{$record->dealer_name}}</td>
                                    <td>{{$record->l4_name}}</td>
                                    {{--                <td>{{!empty($record->created_date)?date('d-M-Y',strtotime($record->created_date)):'NA'}}</td>--}}
                                    <td>{{$record->user_name}}</td>
                                    <td>{{!empty($record->sale_date)?date('d-M-Y',strtotime($record->sale_date)):'NA'}}</td>
                                    <td>{{!empty($record->receive_date)?date('d-M-Y',strtotime($record->receive_date)):'NA'}}</td>
                                    {{--                <td>{{!empty($record->date_time)?date('d-M-Y',strtotime($record->date_time)):'NA'}}</td>--}}
                                    <td>{{$order_data->name}}</td>
                                    <td>{{$pcs=$order_data->pcs}}</td>
                                    <td>{{$pcs_rate=$order_data->rate}}</td>
                                    <td>{{$cases=$order_data->cases}}</td>
                                    <td>{{$case_rate=$order_data->pr_rate}}</td>
                                    <td>{{($pcs_rate*$pcs)+($cases*$case_rate)}}</td>

                                     <td>
                                      <a href="#" orderid="{{ $record->order_id }}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="Update">
                                            <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div><br>
                                        </a>
                                    </td>


                                </tr>
                            @endforeach
                        @endif
                    {{--</table>--}}


        @endforeach
    @else
        <tr>
            <td colspan="14">
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
                <form method="get" id="filter_distributor" action="primaryOrderUpdate" enctype="multipart/form-data">

                 
                    <div class="table-header center">
                        <span>Order Details </span>
                    </div>
                  

                        <table id="simple-table-details" class="table table-bordered">
                        
                            <thead>
                                <th>Sr.No</th>
                                <th>Order No.</th>
                                <th>Product Name</th>
                                <th>Pcs</th>
                                <th>Pcs Rate</th>

                                  <th>Cases</th>
                                <th>Cases Rate</th>


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
                               
                                // template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.final_secondary_qty+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.final_secondary_rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');


                                  template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.pcs+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.rate+'></td><td><input type="text" class="case_qty_val" required="required" name="cases[]" value='+u_value.cases+'></td><td><input readonly type="text" class="case_rate_val" required="required" name="pr_rate[]" value='+u_value.pr_rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');


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

        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        stk_val.val(tval);
  })
  </script>



  <script type="text/javascript">
          $('#simple-table-details').on('keyup','.case_qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        var case_qty_val   =tr.find('.case_qty_val').val();
        var case_rate_val   =tr.find('.case_rate_val').val();

        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        stk_val.val(tval);
  })
  </script>
