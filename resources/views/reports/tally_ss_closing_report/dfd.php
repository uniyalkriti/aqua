<?php 
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
        <td colspan="10"><h3>User Sale Order Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Date</th>
        <th>Order No</th>
        <th>State</th>
        <th>User Name</th>
        <th>Distributor Name</th>
        <th>Retailer Name</th>
        <th>Call Status</th>
        <th>Details</th>
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1;?>

    @if(!empty($records) && count($records)>0)
    
    @foreach($records as $k=> $r)
    @if(count($details[$r->order_id])>0)
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->date}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l3_name}}</td>
            <td>{{$r->user_name}}</td>
            <td>{{$r->dealer_name}}</td>
            <td>{{$r->retailer_name}}</td>
            <td>{{$r->call_status=='1'?'Productive':'Non Productive'}}</td>
                 <td><table class="table">
                <thead>
                    <tr>
        <th>Product Name</th> 
        <th>Quantity</th>
        <th>Weight(in KG)</th>
        <th>Rate</th>
        <th>Value</th>
    </tr>
                </thead>
    @if(!empty($r))
                <?php  $i++; $total=0;
                $totalqty=0;
                $totalweight=0; ?>
                 @foreach($details[$r->order_id] as $k1=>$data1)
                 <tr><td>{{$data1->product_name}}</td>
                 <!-- <td></td> -->
                 <td>{{$data1->quantity}}</td>
                 <td>{{($data1->weight)}}</td>
                 <td>{{$data1->rate}}</td>
                 <td>{{($data1->rate*$data1->quantity)}}</td> </tr>
                 <?php 
                 $total+=$data1->rate*$data1->quantity; 
                 $totalqty+=$data1->quantity;
                 $totalweight+=$data1->weight;

                 $gtotal+=$data1->rate*$data1->quantity; 
                 $gqty+=$data1->quantity;
                 $gweight+=$data1->weight;
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
        
        <th>{{$totalqty}}</th>
        <th>{{$totalweight}}</th>
        <th></th>
        <th>{{$total}}</th>
            </tr>
                </tfoot>
              </table>

              </td>
            </tr>
            @endif
            @endforeach  

            <tr>
            <td colspan="8"><strong>Grand Total</strong></td>
            <td><table class="table">
            <tr>
            <th>Total Qty</th>
            <th>Total Weight(in KG)</th>
            <th>Total Value</th></tr>
            <tr>
            <td>{{$gqty}}</td>
            <td>{{$gweight}}</td>
            <td>{{$gtotal}}</td></tr>
            </table></td>
            </tr>  
            @else
               <tr>
            <td colspan="10">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
        @endif
    </tbody>
</table>