@if(!empty($records))
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
<table id="simple-table" class="table table-bordered" >
    <tr><td colspan="{{10+$total_days*2}}"><h3>{{Lang::get('common.db_monthly')}}</h3></td></tr>
    <tr class="info" style="color: black;">
        <th rowspan="2">{{Lang::get('common.s_no')}}</th>
        <th rowspan="2">{{Lang::get('common.location3')}}</th>
        <th rowspan="2">{{Lang::get('common.csa')}} Name</th>
        <th rowspan="2">{{Lang::get('common.distributor')}} Name</th>
        <th rowspan="2">{{Lang::get('common.username')}}</th>
        <!-- <th rowspan="2">ADO Name</th> -->
    
        @if(!empty($datesDisplayArr))
            @foreach($datesDisplayArr as $keyd=>$datad)
                
                <th colspan="2">{{$datad}}</th>
            @endforeach
        @endif

        <th rowspan="2">MTD Target Amount</th>
        <th rowspan="2">MTD Achievement Amount</th>
        <th rowspan="2">MTD Achievement %</th>
        <th rowspan="2">Loss Amount</th>
        
        
    </tr>
     <tr class="info" style="color: black;">
    @if(!empty($datesDisplayArr))
        @foreach($datesDisplayArr as $keyde=>$datade)
        <td>Target(Cases)</td>
        <td>Achievement(Cases)</td>
        @endforeach
    @endif
    </tr>
    <tbody>
    <?php $i = 1;
    ?>
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php // $encid = Crypt::encryptString($data["user_id"]); ?>

                <tr  class="">
                    <td style="background-color:#f5d0a9">{{$i}}</td>
                    <td style="background-color:#f5d0a9">{{$data->state}}</td>
                    <td style="background-color:#f5d0a9">{{$data->csa_name}}</td>
                    <td style="background-color:#f5d0a9">{{$data->dealer_name}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($so_user_name[$data->dealer_id])?$so_user_name[$data->dealer_id]:'N/A'}}</td>
                    <!-- <td style="background-color:#f5d0a9">{{!empty($ado_user_name[$data->dealer_id])?$ado_user_name[$data->dealer_id]:'N/A'}}</td> -->



                  
                    @if(!empty($datesArr))
                            @foreach($datesArr as $keydd=>$datadd)
                              <td style="background-color:#f5d0a9">{{!empty($data->quantity_cases)?$data->quantity_cases/$total_days:'0'}}</td>
                              <td style="background-color:#f5d0a9">{{!empty($date_wise_achievement[$data->dealer_id.$datadd])?$date_wise_achievement[$data->dealer_id.$datadd]:'0'}}</td>
                                                        
                            @endforeach
                    @endif

                    <?php
                    $mtd_target = !empty($mtd_target_amount[$data->dealer_id])?$mtd_target_amount[$data->dealer_id]:'0';
                    $mtd_achievement = !empty($mtd_achievement_amount[$data->dealer_id])?$mtd_achievement_amount[$data->dealer_id]:'0';

                    ?>

                    <td style="background-color:#f5d0a9">{{$mtd_target}}</td>
                    <td style="background-color:#f5d0a9">{{$mtd_achievement}}</td>

                    @if($mtd_target == 0)
                    <td style="background-color:#f5d0a9">
                        {{0}}
                    </td>
                    @else
                    <td style="background-color:#f5d0a9">
                        <?php $percent = ($mtd_achievement/$mtd_target)*100;  ?>
                        {{ $percent }}
                    </td>    
                    @endif

                    
                    <td style="background-color:#f5d0a9">
                        <?php $less = $mtd_target-$mtd_achievement; ?>
                        {{$less}}
                    </td>


               

                </tr>
                <?php $i++; ?>
            @endforeach

            @else
               <tr>
            <td colspan="{{10+$total_days*2}}">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>      
    @endif
 
    </tbody>
</table>



