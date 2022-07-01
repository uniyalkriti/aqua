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
        <td colspan="13"><h3>Primary Sale</h3></td>
    </tr>
    <tr>
        <th rowspan="2">S.No.</th>
        <th rowspan="2">Order Id</th>
        <th rowspan="2">Customer Name</th>
        <th rowspan="2">Sales Person Name</th>
        <th rowspan="2">Order Date</th>

        <th rowspan="2">Order Amount</th>
        <th rowspan="2">Discount Type</th>
        <th rowspan="2">Discount Amount</th>
        <th rowspan="2">Order Total</th>

        <th colspan="6">Order Details</th>
    </tr>
    
    <tbody>
    @if(!empty($records) && count($records)>0)
        <?php $i = 1 ?>
        @foreach($records as $record)
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

                    {{--<table class="table">--}}

                                <tr>
                                    <td>{{$i++}}</td>
                                    <td><a title="PDF Generation" order_id="{{$record->order_id}}" class="myModal2" id="order_id" data-toggle="modal" data-target="#myModal2">{{$record->order_id}}</a></td>
                                    <td><a href="{{url('distributor/'.$did)}}">{{$record->dealer_name}}</a></td>
                                    <td><a href="{{url('user/'.$uid)}}">{{$record->user_name}}</a></td>
                                    <td>{{!empty($record->sale_date)?date('d-M-Y',strtotime($record->sale_date)):'NA'}}</td>

                                    <td>{{!empty($record->amount_before_discount)?$record->amount_before_discount:0}}</td>
                                    <td>{{$text}}</td>
                                    <td>{{$record->discount_value}}</td>
                                    <td>{{!empty($record->amount_after_discount)?$record->amount_after_discount:0}}</td>
                                    

                                    
                                    <td>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                            @if(!empty($record))
                                                <?php  $i++; $total=0;
                                                $totalqty=0;
                                                $totalcases=0;
                                                $gtotal = 0;
                                                $gqty = 0; ?>
                                                @foreach($order_detial_arr[$record->order_id] as $k1=>$data1)
                                                <?php $value = 0; ?>
                                                    <tr>
                                                        <td>{{$data1->name}}</td>
                                                        <td>{{$case_rate=$data1->pr_rate}}</td>                                                        
                                                        <td>{{$cases=$data1->cases}}</td>
                                                     {{--   <td>{{($cases*$case_rate)}}</td> --}}
                                                       
                                                    </tr>
                                                     <?php 
                                                     $total+=($cases*$case_rate); 
                                                     // $totalqty+=$data1->pcs;
                                                     $totalcases+=$data1->cases;
                                                     // $totalweight+=$data1->weight;

                                                     // $gtotal+=$data1->rate*$data1->pcs; 
                                                     // $gqty+=$data1->pcs;
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
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                           
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
                url: domain + '/order_wise_pdf_format_primary',
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