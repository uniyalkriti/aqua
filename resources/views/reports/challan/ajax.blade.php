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
        <td colspan="10"><h3>Distributor Actual Secondary Sale Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Invoice No</th>
        <th>Distributor Name</th>
        <th>Date</th>
        <th>Details</th>
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; ?>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $k=> $r)
        <tr>
            <td>{{$k+1}}</td>
            <td>{{$r->ch_no}}</td>
            <td>{{$r->dealer_name}}</td>
            <td>{{$r->created_date}}</td>
                 <td><table class="table">
                <thead>
                    <tr>
        <th>Product Name</th>
        <th>MRP</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Value</th>
    </tr>
                </thead>
    @if(!empty($r))
                <?php $total=0;
                $totalqty=0; ?>
                 @foreach($details[$r->order_id] as $k1=>$data1)
                 <tr><td>{{$data1->product_name}}</td>
                 <td>{{$data1->mrp}}</td>
                 <td>{{$data1->qty}}</td>
                 <td>{{$data1->product_rate}}</td>
                 <td>{{$data1->taxable_amt}}</td> </tr>
                 <?php 
                 $total+=$data1->taxable_amt; 
                 $totalqty+=$data1->qty;
                 $gtotal+=$total;
                 $gqty+=$totalqty;
                 ?>
                @endforeach
                @else
                <tr><td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td> </tr>
               @endif
               <tfoot>
                    <tr>
        <th>Total</th>
        <th></th>
        <th>{{$totalqty}}</th>
        <th></th>
        <th>{{$total}}</th>
            </tr>
                </tfoot>
              </table></td>
            </tr>
            @endforeach  
             <tr>
            <td colspan="4"><strong>Grand Total</strong></td>
            <td><table class="table">
            <tr>
            <th>Total Qty</th>
            <th>Total Value</th></tr>
            <tr>
            <td>{{$gqty}}</td>
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