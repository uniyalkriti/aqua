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
    table, th, td {
  border: 1px solid black; padding: 10px;
}
</style>
<table style="border: 1px solid black; border-style: collapse;">
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="13" align="center"><h3>Sale Report</h3></td></tr>
    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Manager Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">State</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Employee name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Rank</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Beat Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Distributor Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">TC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">PC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Sale Amount </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">New Counter Create</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Km Visit</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Remarks</th>
   
            
        
    </tr>
  
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php $i = 1;
        $tc=0;
    
    ?>
    @if(!empty($junior_person_details))
        @foreach($junior_person_details as $key=>$data)
        <?php 
        $junior_id = $data->junior_id;

        $beats = !empty($concatinated_beat[$junior_id])?$concatinated_beat[$junior_id]:'';
        $dealers = !empty($concatinated_dealer[$junior_id])?$concatinated_dealer[$junior_id]:'';

        // $beats = !empty($final_sale_details[$junior_id]['concatinated_beat'])?$final_sale_details[$junior_id]['concatinated_beat']:'';
        // $dealers = !empty($final_sale_details[$junior_id]['concatinated_dealer'])?$final_sale_details[$junior_id]['concatinated_dealer']:'';
        $total_sale = !empty($final_sale_details[$junior_id]['total_sale'])?$final_sale_details[$junior_id]['total_sale']:'';
        $productive_calls = !empty($final_sale_details[$junior_id]['productive_calls'])?$final_sale_details[$junior_id]['productive_calls']:'';
        $concatinated_remarks = !empty($final_sale_details[$junior_id]['concatinated_remarks'])?$final_sale_details[$junior_id]['concatinated_remarks']:'';

        $total_call = !empty($total_calls[$junior_id])?$total_calls[$junior_id]:'';
        $new_counters = !empty($new_retailer[$junior_id])?$new_retailer[$junior_id]:'';

        $dist = !empty($distance[$junior_id])?$distance[$junior_id]:'';

         ?>
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                  
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$i}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$manager_name}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data->junior_state}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data->junior_name}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data->rolename}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$beats}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$dealers}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$total_call}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$productive_calls}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$total_sale}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$new_counters}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$dist}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$concatinated_remarks}}</td>
                          
                            <?php $i++; ?>
                       
                </tr>
                
            @endforeach
    @endif

    </tbody>
</table>
