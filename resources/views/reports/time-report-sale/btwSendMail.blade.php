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
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="{{16+$total_days*9}}" align="center"><h3>Time Report</h3></td></tr>
    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="4">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="4">Employee Code</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="4">State Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="4">Head Quarter</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Town</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">User Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Monthly Target</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Distributor Count</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Outlet Count</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Outlet Count User Wise In a Month </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Designation</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Total Days</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="4">Total Attd</th>
       
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="3">Grand T.C</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="3">Grand P.C</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" rowspan="3">Grand Secondary Value</th>
       

        @if(!empty($datesDisplayArr))
        @foreach($datesDisplayArr as $keyd=>$datad)
        <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;" colspan="15">{{date('d-M-Y',strtotime($datad))}}</th>
        @endforeach
        
        @endif
        
        <tr style="border: 1px solid black; padding: 10px;">
            @foreach($datesDisplayArr as $keyd=>$datad)
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Check In Time</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Check In Location</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Check Out Time</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Check Out Location</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Working Status</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Working With</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Working Distributor Name</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Working Beat Name</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Target</th>

                <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " >T.C</th>
                <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " >P.C</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " >Secondary Value</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">New Outlet Added</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Sale Value From New Outlet Added</th>
                <th  style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; " rowspan="3">Secondary Sale Working Town</th>
                
            @endforeach
        </tr>
        <tr style="border: 1px solid black; padding: 10px;">
            @foreach($datesArr as $n_key=>$n_date)
                <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; "  rowspan="2">{{!empty($total_call_t_data_grand_date[$n_date])?$total_call_t_data_grand_date[$n_date]:''}}</th>
                <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; "  rowspan="2">{{!empty($total_call_data_grand_date[$n_date])?$total_call_data_grand_date[$n_date]:''}}</th>
                <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; "  rowspan="2">{{!empty($sale_data_grand_date[$n_date])?round($sale_data_grand_date[$n_date],2):''}}</th>
               
            @endforeach
        </tr>
        

        
    </tr>
    <tr style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">{{!empty($grand_total_call_t_data_grand_date->tc)?$grand_total_call_t_data_grand_date->tc:''}}</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">{{!empty($grand_total_call_data_grand_date->pc)?$grand_total_call_data_grand_date->pc:''}}</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">{{!empty($grand_sale_data_grand_date->sale_value)?round($grand_sale_data_grand_date->sale_value,2):''}}</th>
        
    </tr>
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php $i = 1;
        $tc=0;
        $pc=0;
        $tq=0;
        $tv=0;
        $total_attd=0;
    ?>
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php  $encid = Crypt::encryptString($data->user_id); ?>
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                    @if(!empty($location_5[$data['user_id']]))
                        @if($location_5[$data['user_id']] != 'Head Office')
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$i}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data['emp_code']}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data['state_name']}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($location_5[$data['user_id']])?$location_5[$data['user_id']]:'NA'}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($location_6[$data['user_id']])?$location_6[$data['user_id']]:'NA'}}</td>
                          
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;"><a href="#">{{$data['user_name']}}</a></td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($month_target[$data['user_id']])?$month_target[$data['user_id']]:'0'}}</td>
                           


                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($dealer_count_user_wise[$data['user_id']])?$dealer_count_user_wise[$data['user_id']]:''}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($retailer_count_user_wise[$data['user_id']])?$retailer_count_user_wise[$data['user_id']]:''}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($retailer_count_added_month[$data['user_id']])?$retailer_count_added_month[$data['user_id']]:''}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data['role_name']}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$total_days}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{!empty($count_total_att[$data['user_id']])?$count_total_att[$data['user_id']]:''}}</td>
                           
                          
                            @if(empty($total_call_t_data_grand[$data['user_id']]))
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" ></td>
                            @else
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" >{{!empty($total_call_t_data_grand[$data['user_id']])?$total_call_t_data_grand[$data['user_id']]:''}}</td>
                            @endif

                            @if(empty($total_call_data_grand[$data['user_id']]))
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" ></td>
                            @else
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" >{{!empty($total_call_data_grand[$data['user_id']])?$total_call_data_grand[$data['user_id']]:''}}</td>
                            @endif

                            @if(empty($sale_data_grand[$data['user_id']]))
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" ></td>
                            @else
                                <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" >{{!empty($sale_data_grand[$data['user_id']])?round($sale_data_grand[$data['user_id']],2):''}}</td>
                            @endif

                          

                            @if(!empty($datesArr))
                                <?php 
                                $total_attd=0; 
                                $count_work_status=array();
                                ?>
                                @foreach($datesArr as $keydd=>$datadd)
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($first_time[$data['user_id'].$datadd])?$first_time[$data['user_id'].$datadd]:''}}</td>
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($first_address[$data['user_id'].$datadd])?$first_address[$data['user_id'].$datadd]:''}}</td>
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($last_time[$data['user_id'].$datadd])?$last_time[$data['user_id'].$datadd]:''}}</td>
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($last_address[$data['user_id'].$datadd])?$last_address[$data['user_id'].$datadd]:''}}</td>
                                    @if(!empty($working_status[$datadd.$data['user_id']]))
                                        @if($working_status[$datadd.$data['user_id']] == 'RETAILING')
                                            <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">
                                            {{$working_status[$datadd.$data['user_id']]}}
                                            </td>
                                        @else
                                            <td style="border: 1px solid black; padding: 10px; background-color: #feb236;">
                                            {{$working_status[$datadd.$data['user_id']]}}
                                            </td>
                                        @endif
                                        @else
                                        <td style="border: 1px solid black; padding: 10px; background-color: #e60000;"></td>
                                    @endif
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($working_with[$datadd.$data['user_id']])?$working_with[$datadd.$data['user_id']]:''}}</td>
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($distributor_name[$datadd.$data['user_id']])?$distributor_name[$datadd.$data['user_id']]:''}}</td>
                                    <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($beat_name[$datadd.$data['user_id']])?$beat_name[$datadd.$data['user_id']]:''}}</td>

                                    <!--  -->
                                     <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($day_target[$datadd.$data['user_id']])?$day_target[$datadd.$data['user_id']]:'0'}}</td>
                                    <!--  -->

                                    @if(empty($total_t_call_data[$data['user_id'].$datadd]))
                                        <td style="border: 1px solid black; padding: 10px; background-color:#feb236;"></td>
                                    @else
                                        @if($total_t_call_data[$data['user_id'].$datadd]>='60')
                                            <td style="border: 1px solid black; padding: 10px; background-color:#ffffff;">{{!empty($total_t_call_data[$data['user_id'].$datadd])?$total_t_call_data[$data['user_id'].$datadd]:''}}</td>
                                        @else
                                            <td style="border: 1px solid black; padding: 10px; background-color:#e60000; color: white;"><strong>{{!empty($total_t_call_data[$data['user_id'].$datadd])?$total_t_call_data[$data['user_id'].$datadd]:''}}</strong></td>
                                        @endif
                                    @endif

                                    @if(empty($total_call_data[$data['user_id'].$datadd]))
                                        <td style="border: 1px solid black; padding: 10px; background-color:#feb236;"></td>
                                    @else
                                        @if($total_call_data[$data['user_id'].$datadd]>='30')
                                            <td style="border: 1px solid black; padding: 10px; background-color:#ffffff;">{{!empty($total_call_data[$data['user_id'].$datadd])?$total_call_data[$data['user_id'].$datadd]:''}}</td>
                                        @else
                                            <td style="border: 1px solid black; padding: 10px; background-color:#e60000; color: white;">{{!empty($total_call_data[$data['user_id'].$datadd])?$total_call_data[$data['user_id'].$datadd]:''}}</td>
                                        @endif
                                    @endif

                                     @if(empty($sale_data[$data['user_id'].$datadd]))
                                        <td style="border: 1px solid black; padding: 10px; background-color:#ffffff;"></td>
                                    @else
                                        <td style="border: 1px solid black; padding: 10px; background-color:#ffffff;">{{!empty($sale_data[$data['user_id'].$datadd])?round($sale_data[$data['user_id'].$datadd],2):''}}</td>
                                    @endif
                                        <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($retailer_count_added_per_day[$data['user_id'].$datadd])?$retailer_count_added_per_day[$data['user_id'].$datadd]:''}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($retailer_sale_added_per_day[$data['user_id'].$datadd])?round($retailer_sale_added_per_day[$data['user_id'].$datadd],2):''}}</td>


                                        <td style="border: 1px solid black; padding: 10px; background-color: #d6cbd3;">{{!empty($sale_data_working_town[$data['user_id'].$datadd])?$sale_data_working_town[$data['user_id'].$datadd]:''}}</td>

                                  
                                @endforeach
                            @endif 
                            <?php $i++; ?>
                        @endif 
                    @endif 
                     
                </tr>
                
            @endforeach
    @endif

    </tbody>
</table>
