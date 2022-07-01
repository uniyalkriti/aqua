<!-- <style>
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
    table, th, td {
  border: 1px solid black; padding: 10px;
}
</style>
<br>
<br>
<br>
<br>
<table style="border: 1px solid black; border-style: collapse;">
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="15" align="center"><h3>Daily Report({{$yesterday}})</h3></td></tr>
    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">User Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Role</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Beat name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Beat Outlet</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Attendance Time</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">First Call Time</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Last Call Time</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Outlet Of User</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Call Of The Day </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Productive Call</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Non Productive Call</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Order Value</th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Distributor Visit</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Distributor Visit Remarks</th>

   
            
        
    </tr>
  
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php $i = 1;
        // $tc=0;
    
    ?>
    @if(!empty($userDetails))
        @foreach($userDetails as $key=>$data)
        <?php 
        $user_id = $data->user_id;

        $beats = !empty($finalSales[$user_id]['beat_name'])?$finalSales[$user_id]['beat_name']:'-';
        $beat_outlet = !empty($finalSales[$user_id]['beat_outlet'])?$finalSales[$user_id]['beat_outlet']:'0';
        $att_time = !empty($attendanceDetail[$user_id])?$attendanceDetail[$user_id]:'-';

        $first_call = !empty($finalSales[$user_id]['first_call'])?$finalSales[$user_id]['first_call']:'-';
        $last_call = !empty($finalSales[$user_id]['last_call'])?$finalSales[$user_id]['last_call']:'-';
        $total_call = !empty($finalSales[$user_id]['total_call'])?$finalSales[$user_id]['total_call']:'0';

        $totalOutletUser = !empty($totalOutlet[$user_id])?$totalOutlet[$user_id]:'0';


        $productive_call = !empty($finalSalesDetails[$user_id]['productive_call'])?$finalSalesDetails[$user_id]['productive_call']:'0';
        $sale = !empty($finalSalesDetails[$user_id]['sale'])?$finalSalesDetails[$user_id]['sale']:'0';

        $nonProdCall = $total_call-$productive_call;


        $db_visit = !empty($finalDailyDetails[$user_id]['did'])?'YES':'NO';


        $remarks = !empty($finalDailyDetails[$user_id]['remarks'])?$finalDailyDetails[$user_id]['remarks']:'-';




         ?>
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                  
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$i}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data->user_name}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data->rolename}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$beats}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$beat_outlet}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$att_time}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$first_call}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$last_call}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$totalOutletUser}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$total_call}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$productive_call}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$nonProdCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$sale}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$db_visit}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$remarks}}</td>




                          
                            <?php $i++; ?>
                       
                </tr>
                
            @endforeach
    @endif

    </tbody>
</table>
 -->