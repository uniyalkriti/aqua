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
        <td colspan="19"><h3>User Perfomance</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <!-- <th>Date</th> -->
        <th>User Name</th>
        <th>Working Town</th>
        <th>Date</th>
        <th>Check In</th>
        <th>Check Out</th>
       
        <th>Total Calls</th>
        <th>Productive Calls</th>
        <th>Non Productive Calls</th>
        <th>Secondary Sales Value</th>
        <th>Retailer Name</th>
        <th>Cont. No.</th>
        <!-- <th>Total</th> -->
        <th>Distributor Name</th>
        <th>Super Stockiest Name</th>
         
        <th>Primary Order</th>
        <th>TA/DA Expense</th>
        <th>New Outlet Added Today</th>


    </tr>
    <tbody>
            @foreach($records as $key => $value)
            <?php
            $user_id = Crypt::encryptString($value->user_id); 
            $dealer_id = Crypt::encryptString($value->dealer_id); 
          
            ?>
            <tr>
                <td>{{$key+1}}</td>
              
                <td>{{$value->user_name}}</td>
                <td>{{$value->working_station}}</td>
                <td>{{$value->date}}</td>

                <td>{{!empty($check_in[$value->user_id.$value->date])?$check_in[$value->user_id.$value->date]:'NA'}}</td>
                <td>{{!empty($check_out[$value->user_id.$value->date])?$check_out[$value->user_id.$value->date]:'NA'}}</td>



                <!-- <td>{{$value->call_status}}</td> -->
                <td>{{$value->call_status}}</td>
                <td>{{!empty($eff_calls[$value->retailer_id.$value->date])?$eff_calls[$value->retailer_id.$value->date]:'0'}}</td>
                <td>{{!empty($non_productive_call[$value->retailer_id])?$non_productive_call[$value->retailer_id]:'0'}}</td>
                <td>{{!empty($retailer_name_data[$value->retailer_id])?$retailer_name_data[$value->retailer_id]:'0'}}</td>

                <td>{{$value->retailer_name}}</td>
                <td>{{$value->mobile}}</td>

                <!-- <td>{{!empty($user_sale_value[$value->user_id])?$user_sale_value[$value->user_id]:'0'}}</td> -->


                <td>{{$value->dealer_name}}</td>
                <td>{{$value->csa_name}}</td>
           
               
                <td>-</td>

                <td>-</td>

                <td>-</td>



            </tr>



                @php
                $count[] = count(!empty($value->user_id)?$value->user_id:0);
                $array_count = array_sum($count);
                @endphp
            

                @if($array_count == $user_ret_count[$value->user_id])

                   <tr>
                        <td></td>
                        <td><b>{{$value->user_name}}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{!empty($user_productive_calls[$value->user_id])?$user_productive_calls[$value->user_id]:'0'}}</b></td>
                        <td><b>{{!empty($totalproductive_calls[$value->user_id])?$totalproductive_calls[$value->user_id]:'0'}}</b></td>
                        <td></td>
                        <td><b>{{!empty($user_sale_value[$value->user_id])?$user_sale_value[$value->user_id]:'0'}}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{!empty($primary_order[$value->user_id.$value->date])?$primary_order[$value->user_id.$value->date]:'0'}}</b></td>
                        <td><b>{{!empty($expense[$value->user_id.$value->date])?$expense[$value->user_id.$value->date]:'0'}}</b></td>
                        <td><b>{{!empty($new_outlet[$value->user_id.$value->date])?$new_outlet[$value->user_id.$value->date]:'0'}}</b></td>
                       
                   </tr> 
                   @php
                   $count = array();
                   $array_count = 0;
                   @endphp
                @endif


            @endforeach
    </tbody>
</table>