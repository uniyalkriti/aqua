@if(!empty($main_query_data))
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
        <td colspan="20"><h3>Order Fulfillment List Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Date</th>
        <th>Time</th>
        <th>Order No</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.location7')}}</th>
        <th>User Name</th>
        <th>Designation</th>
        <th>Mobile</th>
        <th>Distributor Name</th>
        <th>Division</th>
        <th>Retailer Name</th>
        <th>Retailer Number</th>
        <th>Details</th>
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();?>

    @if(!empty($main_query_data))
    
    @foreach($main_query_data as $k=> $r)
    @if(!empty($r->order_id))
   
        <?php 
        $user_id = Crypt::encryptString($r->user_id); 
        $retailer_id = Crypt::encryptString($r->retailer_id); 
        $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->date}}</td>
            <td>{{$r->time}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l3_name}}</td>
            <td>{{$r->l4_name}}</td>
            <td>{{$r->l5_name}}</td>
            <td>{{$r->l6_name}}</td>
            <td>{{$r->l7_name}}</td>
            <td><a href="{{url('user/'.$user_id)}}"> {{$r->user_name}}</a></td>
            <td>{{$r->rolename}}</td>
            <td>{{$r->mobile}}</td>
            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
            <td>{{$r->division_name}}</td>
            <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
            <td>{{!empty($r->retailer_other_number)?$r->retailer_other_number:$r->retailer_landline_number}}    </td>
  
            <td>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th> 
                            <th>Quantity</th>
                            <th>Rate</th>
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
                             <td>{{$data1->product_fullfiment_qty}}</td>
                             <td>{{$data1->product_rate}}</td>
                             <td>{{($data1->sale_value)}}</td> </tr>
                             <?php 
                             $total+=$data1->sale_value; 
                             $totalqty+=$data1->product_fullfiment_qty;
                             

                             $gtotal+=$data1->sale_value; 
                             $gqty+=$data1->product_fullfiment_qty;
                             
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
            <th colspan="16"><strong>Grand Total</strong></th>
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