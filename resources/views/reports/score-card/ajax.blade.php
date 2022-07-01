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
        <td colspan="17"><h3>Score Card</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <!-- <th>Date</th> -->
        <th>HQ</th>
        <th>User Name</th>
        <th>Working Station</th>
        <th>Name of secondary party</th>
        <th>Cont. No.</th>
        <th>Total Calls</th>
        <th>Effective Calls</th>
        <th>Secondary Order Value</th>
        <!-- <th>Total</th> -->
        <th>Name of the Primary Party in Station</th>
          @foreach($catalog_product as $ckey=>$cvalue)
        <th>{{$cvalue}}</th>
        @endforeach
        <th>Stock Value</th>
        <th>Primary Order</th>
        <th>Primary Order Received From</th>
        <th>Remarks</th>


    </tr>
    <tbody>
            @foreach($records as $key => $value)
            <?php
            $user_id = Crypt::encryptString($value->user_id); 
            $dealer_id = Crypt::encryptString($value->dealer_id); 
          
            ?>
            <tr>
                <td>{{$key+1}}</td>
                <!-- <td>{{$value->sale_date}}</td> -->
                <td>{{$value->hq}}</td>
                <td>{{$value->user_name}}</td>
                <td>{{$value->working_station}}</td>
                <td>{{$value->retailer_name}}</td>
                <td>{{$value->mobile}}</td>
                <!-- <td>{{$value->call_status}}</td> -->
                <td>-</td>
                <td>{{!empty($eff_calls[$value->retailer_id.$value->date])?$eff_calls[$value->retailer_id.$value->date]:'0'}}</td>
                <td>{{$value->total}}</td>

                <!-- <td>{{!empty($user_sale_value[$value->user_id])?$user_sale_value[$value->user_id]:'0'}}</td> -->


                <td>{{$value->dealer_name}}</td>
                @foreach($catalog_product as $ckey=>$cvalue)
                <td>{{!empty($stock_qty[$value->dealer_id.$ckey])?$stock_qty[$value->dealer_id.$ckey]:'0'}}</td>
                @endforeach
                <td>{{!empty($stock_value[$value->dealer_id])?$stock_value[$value->dealer_id]:'0'}}</td>
                <td>{{!empty($primary_order[$value->dealer_id.$value->date])?$primary_order[$value->dealer_id.$value->date]:'0'}}</td>
                <td>{{!empty($primary_order_rec_from[$value->dealer_id.$value->date])?$primary_order_rec_from[$value->dealer_id.$value->date]:'NA'}}</td>
                <td>-</td>

            </tr>
                @php
                $count[] = count(array($value->user_id));
                $array_count = array_sum($count);
                @endphp
            

                @if($array_count == $user_ret_count[$value->user_id])
                   <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{$value->user_name}}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{!empty($user_productive_calls[$value->user_id])?$user_productive_calls[$value->user_id]:'0'}}</b></td>
                        <td></td>
                        <td><b>{{!empty($user_sale_value[$value->user_id])?$user_sale_value[$value->user_id]:'0'}}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                   </tr> 
                   @php
                   $count = array();
                   $array_count = 0;
                   @endphp
                @endif


            @endforeach
    </tbody>
</table>