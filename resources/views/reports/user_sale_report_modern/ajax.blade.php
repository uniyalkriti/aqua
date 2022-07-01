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
        <td colspan="11"><h3>Modern User Sale Order Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Image</th>
        <th>Date</th>
        <th>Order No</th>
        <th>State</th>
        <th>User Name</th>
        <th>Distributor Name</th>
        <th>Customer Name</th>
        <th>Customer Number</th>
        <th>Call Status</th>
        <th>Details</th>
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; ?>

    @if(!empty($records) && count($records)>0)
    
    @foreach($records as $k=> $r)
    @if(count($details[$r->order_id])>0)
        <tr>
            <td>{{$k+1}}</td>
             <td>
                    <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                    src="http://satmola.msell.in/satmola-api/webservices/mobile_images/sale/{{!empty($r->image_name)?$r->image_name:'N/A'}}" 
                    onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/avatar2.png')}}';" />
            </td>
            <td>{{$r->date}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l3_name}}</td>
            <td>{{$r->user_name}}</td>
            <td>{{$r->dealer_name}}</td>
            <td>{{$r->retailer_name}}</td>
            <td>{{$r->customer_number}}</td>
            <td>{{$r->call_status=='1'?'Productive':'Non Productive'}}</td>
                 <td><table class="table">
                <thead>
                    <tr>
        <th>Product Name</th> 
        <th>Quantity</th>
        <th>Rate</th>
        <th>Value</th>
    </tr>
                </thead>
    @if(!empty($r))
                <?php $total=0;
                $totalqty=0;
                $totalweight=0; ?>
                 @foreach($details[$r->order_id] as $k1=>$data1)
                 <tr><td>{{$data1->product_name}}</td>
                 <!-- <td></td> -->
                 <td>{{$data1->quantity}}</td>
                 <td>{{$data1->rate}}</td>
                 <td>{{round($data1->rate*$data1->quantity)}}</td> </tr>
                 <?php 
                 $total+=round($data1->rate*$data1->quantity); 
                 $totalqty+=$data1->quantity;
                 $gtotal+=round($data1->rate*$data1->quantity); 
                 $gqty+=$data1->quantity;
                 ?>
                @endforeach
                @else
                <tr>
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
        <th></th>
        <th>{{$total}}</th>
            </tr>
                </tfoot>
              </table></td>
            </tr>
            @endif
            @endforeach  

            <tr>
            <td colspan="10"><strong>Grand Total</strong></td>
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