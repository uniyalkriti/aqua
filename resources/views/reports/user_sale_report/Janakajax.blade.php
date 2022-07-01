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
        <td colspan="20"><h3>User Sale Order Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Order No</th>
        <th>Customer Name</th>
        <th>Sales Person Name</th>
        <th>Order Date</th>
        <th>Order Amount</th>
        <th>Discount Type</th>
        <th>Discount Amount</th>
        <th>Order Total</th>
        <th>Call Status</th>
        <th>Remarks</th>
        <th>No Productive Reason</th>
        <th>Details</th>
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=[]; $count_call_0=[];?>

    @if(!empty($records) && count(array($records))>0)
    
    @foreach($records as $k=> $r)
    @if(count(array($r->order_id))>0)
   
        <?php 
        $user_id = Crypt::encryptString($r->user_id); 
        $retailer_id = Crypt::encryptString($r->retailer_id); 
        $dealer_id = Crypt::encryptString($r->dealer_id); 

        if($r->discount_type == 1){
            $type = "Percentage";
        }elseif($r->discount_type == 2){
            $type = "Value";
        }else{
            $type = "N/A";
        }
        ?>
        <tr>
            <td>{{$i}}</td>
            @if($r->call_status == '0' || Auth::user()->company_id !== '50')
                <td><a href="#">{{$r->order_id}}</a></td>
            @else
                <td><a title="PDF Generation" order_id="{{$r->order_id}}" class="myModal2" id="order_id" data-toggle="modal" data-target="#myModal2">{{$r->order_id}}</a></td>
            @endif
            <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
            <td><a href="{{url('user/'.$user_id)}}"> {{$r->user_name}}</a></td>
            <td>{{$r->date}}</td>

        

            @if(!empty($r))
                 <?php 
                 $ctotal=0;
                   ?>
                    @foreach($details[$r->order_id] as $k1=>$data1)
                         <?php 
                         $crate = !empty($data1->rate)?$data1->rate:'0';
                         $cquantity = !empty($data1->quantity)?$data1->quantity:'0';
                         $ctotal+=$crate*$cquantity; 
                         ?>
                    @endforeach
            @else
                        $ctotal = 0;
            @endif




            <td>{{$ctotal}}</td>
            <td>{{$type}}</td>
            <td>{{$r->discount}}</td>
            <td>{{$ctotal-$r->discount}}</td>
            <!-- <td>{{($r->total_sale_value)}}</td> -->

            @php
                if($r->call_status=='1')
                {
                    $count_call_status_1[] = '1';
                }
                else
                {
                    $count_call_0[] = '1';
                }
            @endphp
                @if($r->call_status=='1')
                    <td style="color:green"><b>Productive</b></td>
                @else
                    <td style="color:red"><b>Non Productive</b></td>
                @endif

            <td>{{$r->remarks}}</td>



            @if($r->non_productive_reason_id == 0)
                <td>{{''}}</td>
                @else
                    <td>{{!empty($non_productive_reason_name[$r->non_productive_reason_id])?$non_productive_reason_name[$r->non_productive_reason_id]:''}}</td>
            @endif

            <td>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th> 
                            <th>Price</th> 
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    @if(!empty($r))
                        <?php  $i++; $total=0;
                        $totalqty=0;
                        $totalweight=0; ?>
                        @foreach($details[$r->order_id] as $k1=>$data1)
                        <?php $value = 0; ?>
                            <tr>
                                <td>{{$data1->product_name}}</td>
                                <td>{{$data1->rate}}</td>
                                <td>{{$data1->quantity}}</td>
                             
                            </tr>
                             <?php 
                             $c_rate = !empty($data1->rate)?$data1->rate:'0';
                             $c_quantity = !empty($data1->quantity)?$data1->quantity:'0';
                             $total+=$c_rate*$c_quantity - $value; 
                             $totalqty+=$c_quantity;
                             $totalweight+=$data1->weight;

                             $gtotal+=$c_rate*$c_quantity - $value; 
                             $gqty+=$c_quantity;
                             $gweight+=$data1->weight;
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
                            <th>{{$totalqty}}</th>
                          
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
            @endif
            @endforeach  

            <tr>
                <th colspan="11"><strong>Grand Total</strong></th>

                <td>
                    <b style="color:red">Non Productive Call : </b> <b style="color:red">{{array_sum($count_call_0)}}</b><br>
                    <b style="color:green">Productive Call : </b><b style="color:green">{{array_sum($count_call_status_1)}}</b><br>
                </td>
              
                <td>
                    <table class="table">
                        <tr>
                            <th>Total Qty</th>
                         
                        </tr>
                        <tr>
                            <td>{{$gqty}}</td>
                          
                        </tr>
                    </table>
                </td>
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
                url: domain + '/order_wise_pdf_format',
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