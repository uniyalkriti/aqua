@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> {{Lang::get('common.export_excel')}}</a>
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
    <tr><td colspan="{{24+$total_days*12}}"><h3>{{Lang::get('common.time_report')}}</h3></td></tr>
    <tr class="info" style="color: black;">
        <th rowspan="4">{{Lang::get('common.s_no')}}</th>
        <th rowspan="4">{{Lang::get('common.location3')}}</th>
        <th rowspan="4">{{Lang::get('common.location4')}}</th>
        <th rowspan="4">{{Lang::get('common.location5')}}</th>
        <th rowspan="4">{{Lang::get('common.location6')}}</th>
        <th rowspan="4">{{Lang::get('common.emp_code')}}</th>
        <th rowspan="4">{{Lang::get('common.username')}}</th>
        <th rowspan="4">{{Lang::get('common.role_key')}}</th>
        <th rowspan="4">{{Lang::get('common.user_contact')}}</th>
        <th rowspan="4">{{Lang::get('common.senior_name')}}</th>

          <th rowspan="4">{{Lang::get('common.status')}}</th>
        <th rowspan="4">{{Lang::get('common.deactivate_date')}}</th>


        <th rowspan="4">{{Lang::get('common.distributor')}} {{Lang::get('common.count')}}</th>
        <th rowspan="4">{{Lang::get('common.outlet')}} {{Lang::get('common.count')}}</th>
        <th rowspan="4">{{Lang::get('common.outlet')}} {{Lang::get('common.count')}} {{Lang::get('common.user')}} Wise In a {{Lang::get('common.month')}} </th>
        <th rowspan="4">{{Lang::get('common.total')}} Days</th>
        <th rowspan="4">{{Lang::get('common.total')}} {{Lang::get('common.attendance')}}</th>
        <th rowspan="4">Upto 9:30</th>
        <th rowspan="4">After 9:30</th>
        <th rowspan="3">{{Lang::get('common.grand')}} {{Lang::get('common.total_call')}}</th>
        <th rowspan="3">{{Lang::get('common.grand')}} {{Lang::get('common.productive_call')}}</th>
        <th rowspan="3">{{Lang::get('common.grand')}} {{Lang::get('common.secondary_sale')}}</th>
        <th rowspan="3">{{Lang::get('common.grand')}} {{Lang::get('common.primary_sale')}}</th>
        <th rowspan="3">{{Lang::get('common.grand')}} TA/DA Expenses</th>

        @if(!empty($datesDisplayArr))
        @foreach($datesDisplayArr as $keyd=>$datad)
        <th colspan="12">{{$datad}}</th>
        @endforeach
        
        @endif
        
        <tr>
            @foreach($datesDisplayArr as $keyd=>$datad)
                <th rowspan="3">Newly {{Lang::get('common.outlet')}} {{Lang::get('common.add')}}</th>
                <th rowspan="3">{{Lang::get('common.attendance')}} {{Lang::get('common.time')}}</th>
                <th rowspan="3">{{Lang::get('common.attendance')}} {{Lang::get('common.location')}}</th>
                <th rowspan="3">{{Lang::get('common.check_out')}} {{Lang::get('common.time')}}</th>
                <th rowspan="3">{{Lang::get('common.check_out')}} {{Lang::get('common.location')}}</th>
                <th>{{Lang::get('common.total_call')}}</th>
                <th>{{Lang::get('common.productive_call')}}</th>
                <th>{{Lang::get('common.secondary_sale')}}</th>
                <th rowspan="3">{{Lang::get('common.secondary_sale')}} {{Lang::get('common.working')}} {{Lang::get('common.location6')}}</th>
                <th>{{Lang::get('common.primary_sale')}}</th>
                <th rowspan="3">{{Lang::get('common.primary_sale')}} {{Lang::get('common.working')}} {{Lang::get('common.location6')}}</th>
                <th>TA/DA Expenses</th>
            @endforeach
        </tr>
        <tr>
            @foreach($datesArr as $n_key=>$n_date)
                <th rowspan="2">{{!empty($total_call_t_data_grand_date[$n_date])?$total_call_t_data_grand_date[$n_date]:''}}</th>
                <th rowspan="2">{{!empty($total_call_data_grand_date[$n_date])?$total_call_data_grand_date[$n_date]:''}}</th>
                <th rowspan="2">{{!empty($sale_data_grand_date[$n_date])?round($sale_data_grand_date[$n_date],2):''}}</th>
                <th rowspan="2">{{!empty($primary_sale_data_grand_date[$n_date])?round($primary_sale_data_grand_date[$n_date],2):''}}</th>
                <th rowspan="2">{{!empty($travelling_expense_data_grand_date[$n_date])?round($travelling_expense_data_grand_date[$n_date],2):''}}</th>
            @endforeach
        </tr>
        

        
    </tr>
    <tr>
        <th>{{!empty($grand_total_call_t_data_grand_date->tc)?$grand_total_call_t_data_grand_date->tc:''}}</th>
        <th>{{!empty($grand_total_call_data_grand_date->pc)?$grand_total_call_data_grand_date->pc:''}}</th>
        <th>{{!empty($grand_sale_data_grand_date->sale_value)?$grand_sale_data_grand_date->sale_value:''}}</th>
        <th>{{!empty($grand_primary_sale_data_grand_date->sale_value)?$grand_primary_sale_data_grand_date->sale_value:''}}</th>
        <th>{{!empty($grand_travelling_expense_data_grand_date->sale_value)?round($grand_travelling_expense_data_grand_date->sale_value,2):''}}</th>
    </tr>
    <tbody>
    <?php $i = 1;
        $tc=0;
        $pc=0;
        $tq=0;
        $tv=0;
        $total_attd=0;

        $emp_code = App\CommonFilter::emp_code('person');
        $senior_name = App\CommonFilter::senior_name('person');
    ?>
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php 
         $encid = Crypt::encryptString($data['user_id']); 
         $sencid = Crypt::encryptString($data['person_id_senior']); 
         ?>
                <tr  class="">
                    <td style="background-color:#f5d0a9">{{$i}}</td>
                    <td style="background-color:#f5d0a9">{{$data['state_name']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['l4_name']}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($location_5[$data['user_id']])?$location_5[$data['user_id']]:'NA'}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($location_6[$data['user_id']])?$location_6[$data['user_id']]:'NA'}}</td>
                    <td style="background-color:#f5d0a9">{{$data['emp_code']}}</td>

                  
                    <td style="background-color:#f5d0a9"><a href="{{url('user/'.$encid)}}">{{$data['user_name']}}</a></td>
                    <td style="background-color:#f5d0a9">{{$data['role_name']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['mobile']}}</td>
                    <td style="background-color:#f5d0a9"><a href="{{url('user/'.$sencid)}}">{{!empty($senior_name[$data['person_id_senior']])?$senior_name[$data['person_id_senior']]:'NA'}}</a></td>

                     <td style="background-color:#f5d0a9">{{($data['person_status']==1)?'Active':'Deactivated/Deleted'}}</td>

                     @if($data['person_status'] == 1)
                    <td style="background-color:#f5d0a9"></td>
                     @else
                    <td style="background-color:#f5d0a9">{{$data['deleted_deactivated_on']}}</td>
                     @endif





                    <td style="background-color:#f5d0a9">{{!empty($dealer_count_user_wise[$data['user_id']])?$dealer_count_user_wise[$data['user_id']]:''}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($retailer_count_user_wise[$data['user_id']])?$retailer_count_user_wise[$data['user_id']]:''}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($retailer_count_added_month[$data['user_id']])?$retailer_count_added_month[$data['user_id']]:''}}</td>
                    <td style="background-color:#f5d0a9">{{$total_days}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($count_total_att[$data['user_id']])?$count_total_att[$data['user_id']]:''}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($upto_check_in[$data['user_id']])?$upto_check_in[$data['user_id']]:''}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($upto_check_out[$data['user_id']])?$upto_check_out[$data['user_id']]:''}}</td>
                  
                    @if(empty($total_call_t_data_grand[$data['user_id']]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($total_call_t_data_grand[$data['user_id']])?$total_call_t_data_grand[$data['user_id']]:''}}</td>
                    @endif

                    @if(empty($total_call_data_grand[$data['user_id']]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($total_call_data_grand[$data['user_id']])?$total_call_data_grand[$data['user_id']]:''}}</td>
                    @endif

                    @if(empty($sale_data_grand[$data['user_id']]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($sale_data_grand[$data['user_id']])?round($sale_data_grand[$data['user_id']],2):''}}</td>
                    @endif

                     @if(empty($primary_sale_data_grand[$data['user_id']]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($primary_sale_data_grand[$data['user_id']])?round($primary_sale_data_grand[$data['user_id']],2):''}}</td>
                    @endif

                     @if(empty($travelling_expense_data_grand[$data['user_id']]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($travelling_expense_data_grand[$data['user_id']])?round($travelling_expense_data_grand[$data['user_id']],2):''}}</td>
                    @endif

                    @if(!empty($datesArr))
                    <?php 
                    $total_attd=0; 
                    $count_work_status=array();
                    ?>
                    @foreach($datesArr as $keydd=>$datadd)
                        <td>{{!empty($retailer_count_added_per_day[$data['user_id'].$datadd])?$retailer_count_added_per_day[$data['user_id'].$datadd]:''}}</td>
                        <td>{{!empty($first_time[$data['user_id'].$datadd])?$first_time[$data['user_id'].$datadd]:''}}</td>
                        <td>{{!empty($first_address[$data['user_id'].$datadd])?$first_address[$data['user_id'].$datadd]:''}}</td>
                        <td>{{!empty($last_time[$data['user_id'].$datadd])?$last_time[$data['user_id'].$datadd]:''}}</td>
                        <td>{{!empty($last_address[$data['user_id'].$datadd])?$last_address[$data['user_id'].$datadd]:''}}</td>
                    @if(empty($total_t_call_data[$data['user_id'].$datadd]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($total_t_call_data[$data['user_id'].$datadd])?$total_t_call_data[$data['user_id'].$datadd]:''}}</td>
                    @endif

                    @if(empty($total_call_data[$data['user_id'].$datadd]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($total_call_data[$data['user_id'].$datadd])?$total_call_data[$data['user_id'].$datadd]:''}}</td>
                    @endif

                     @if(empty($sale_data[$data['user_id'].$datadd]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($sale_data[$data['user_id'].$datadd])?round($sale_data[$data['user_id'].$datadd],2):''}}</td>
                    @endif

                        <td>{{!empty($sale_data_working_town[$data['user_id'].$datadd])?$sale_data_working_town[$data['user_id'].$datadd]:''}}</td>



                     @if(empty($primary_sale_data[$data['user_id'].$datadd]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($primary_sale_data[$data['user_id'].$datadd])?round($primary_sale_data[$data['user_id'].$datadd],2):''}}</td>
                    @endif

                        <td>{{!empty($primary_sale_data_working_town[$data['user_id'].$datadd])?$primary_sale_data_working_town[$data['user_id'].$datadd]:''}}</td>


                      @if(empty($travelling_expense_data[$data['user_id'].$datadd]))
                        <td style="background-color:#ff3333;"></td>
                    @else
                        <td style="background-color:#4dff4d;">{{!empty($travelling_expense_data[$data['user_id'].$datadd])?round($travelling_expense_data[$data['user_id'].$datadd],2):''}}</td>
                    @endif
                      
                    @endforeach
                    @endif 
                     
                </tr>
                <?php $i++; ?>
            @endforeach
    @endif

    </tbody>
</table>
