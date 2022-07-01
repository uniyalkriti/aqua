@if(!empty($outlet_type))
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
    #simple-table th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>

<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    <tr>
        <td colspan="80"><h3>{{Lang::get('common.outlet_opening_status')}}</h3></td>
    </tr>
        <tr>
            <th rowspan="2">{{Lang::get('common.location3')}}</th>
            <th rowspan="2">{{Lang::get('common.retailer')}} CATEGORY</th>
            @if(!empty($monthArr))
            @foreach($monthArr as $mk=>$mval)
            <th colspan="6">{{$mval}}</th>
            @endforeach
            @endif
            <th colspan="6">{{Lang::get('common.total')}}</th>
        </tr>
        <tr>
    {{-- Months Start Here--}}
        @if(!empty($monthArr))
        @foreach($monthArr as $mk=>$mval)
            <th>{{Lang::get('common.retailer')}} AS ON 1ST OF OF THIS MONTH</th>
            <th>{{Lang::get('common.retailer')}} ADDED DURING THE MONTH</th>
            <th>{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</th>
            <th>ACTIVE {{Lang::get('common.retailer')}}</th>
            <th>% ACTIVE</th>
            <th>UNIQUE INACTIVE {{Lang::get('common.retailer')}} MORE THAN 2 MONTH</th>
        @endforeach
        @endif
    {{-- Months End Here--}}
            <th>{{Lang::get('common.retailer')}} AS ON 1ST OF OF THIS MONTH</th>
            <th>{{Lang::get('common.retailer')}} ADDED DURING THE MONTH</th>
            <th>{{Lang::get('common.total')}} {{Lang::get('common.retailer')}}</th>
            <th>ACTIVE {{Lang::get('common.retailer')}}</th>
            <th>% ACTIVE</th>
            <th>UNIQUE INACTIVE {{Lang::get('common.retailer')}} MORE THAN 2 MONTH</th>
        </tr>
        <tbody>
    {{-- State Start Here--}}
    @if(!empty($state))
    @foreach($state as $sk=>$sVal)
        <tr>
        <td rowspan="{{count($outlet_type)+1}}">{{$sVal->name}}</td>
        </tr>

        {{-- Outlet Start Here--}}
            @if(!empty($outlet_type))
            @foreach($outlet_type as $ok=>$oVal)

            <tr>
                <td>{{$oVal->outlet_type}}</td>

                @foreach($monthArr as $mk=>$mval)
                <?php
                 $mkey=$mk-1;
                 $key=$mkey+1;
                 $nkey=1;

                if(isset($uniqueInActOutlet[$sVal->id][$key][$oVal->id]))
                {
                    $uniqInActOut= $uniqueInActOutlet[$sVal->id][$key][$oVal->id];
                }else{
                    $uniqInActOut=0;
                }

                if($mk==4){

                    if(isset($firstOutlet[$sVal->id][4][$oVal->id]))
                    {
                        $outletAsOnFirstMonth= $firstOutlet[$sVal->id][4][$oVal->id];
                    }else{
                        $outletAsOnFirstMonth=0;
                    }
                }
                else{
                    // echo $key+1;
                    if(isset($tot_outletAsOnFirstMonth[$sVal->id][$key][$oVal->id]))
                    {

                       if($mk==1){
                            $outletAsOnFirstMonth=$tot_outletAsOnFirstMonth[$sVal->id][$nkey][$oVal->id];
                       }else{
                            $outletAsOnFirstMonth=$tot_outletAsOnFirstMonth[$sVal->id][$key][$oVal->id];
                       }


                        // $outletAsOnFirstMonth= $tot_outletAsOnFirstMonth[$sVal->id][$mk][$oVal->id];
                    }else{
                        $outletAsOnFirstMonth=0;
                    }
                    // echo $tot_outletAsOnFirstMonth[$sVal->id][$mk+1][$oVal->id];
                    // // $outletAsOnFirstMonth=0;
                }
                // echo $outletAsOnFirstMonth;
                if(isset($outData[$sVal->id][$mk][$oVal->id]))
                {
                    $outletAddedMonth= !empty($outData[$sVal->id][$mk][$oVal->id])?array_sum($outData[$sVal->id][$mk][$oVal->id]):0;
                }else{
                    $outletAddedMonth=0;
                }

                if(isset($activeOutletArr[$sVal->id][$mk][$oVal->id]))
                {
                    $activeOutlet= $activeOutletArr[$sVal->id][$mk][$oVal->id];
                }else{
                    $activeOutlet=0;
                }
                $totalOutlet=$outletAddedMonth+$outletAsOnFirstMonth;
                if($totalOutlet>0){
                    $activePer=$activeOutlet/$totalOutlet*100;
                }else{
                    $activePer =0;
                }
                if($mk+1==13){
                    $tot_outletAsOnFirstMonth[$sVal->id][$nkey][$oVal->id]=$totalOutlet;
                }else{
                    $tot_outletAsOnFirstMonth[$sVal->id][$mk+1][$oVal->id]=$totalOutlet;
                }




                $tot_outletAsOnFirstMonth1[$sVal->id][$oVal->id][]=$outletAsOnFirstMonth;
                $tot_outletAddedMonth[$sVal->id][$oVal->id][]=$outletAddedMonth;
                $tot_activeOutlet[$sVal->id][$oVal->id][]=$activeOutlet;
                $tot_InactiveOutlet[$sVal->id][$oVal->id][]=$uniqInActOut;

                $grand_tot_outletAsOnFirstMonth[$sVal->id][$mk][]=$outletAsOnFirstMonth;
                $grand_tot_outletAddedMonth[$sVal->id][$mk][]=$outletAddedMonth;
                $grand_tot_activeOutlet[$sVal->id][$mk][]=$activeOutlet;
                $grand_tot_In_activeOutlet[$sVal->id][$mk][]=$uniqInActOut;

                ?>


                <td>{{ $outletAsOnFirstMonth }}</td>
                <td>{{ $outletAddedMonth }}</td>
                <td>{{ $outletAddedMonth+$outletAsOnFirstMonth }}</td>
                <td>{{$activeOutlet}}</td>
                <td>{{round($activePer,2)}}</td>
                <td>{{$uniqInActOut}}</td>
                @endforeach
               <?php
                    $tot_activeOut=array_sum($tot_activeOutlet[$sVal->id][$oVal->id]);
                    $tot_unq_INactiveOut=array_sum($tot_InactiveOutlet[$sVal->id][$oVal->id]);
                    $TotalOutlet=array_sum($tot_outletAsOnFirstMonth1[$sVal->id][$oVal->id])+array_sum($tot_outletAddedMonth[$sVal->id][$oVal->id]);
                    if($TotalOutlet>0){
                        $tot_activePer=$tot_activeOut/$TotalOutlet*100;
                    }else{
                        $tot_activePer =0;
                    }

                    $grand_outletAsOnFirstMonth[$sVal->id][]=array_sum($tot_outletAsOnFirstMonth1[$sVal->id][$oVal->id]);
                    $grand_outletAddedMonth[$sVal->id][]=array_sum($tot_outletAddedMonth[$sVal->id][$oVal->id]);
                    $grand_TotalOutlet[$sVal->id][]=$TotalOutlet;
                    $grand_tot_activeOut[$sVal->id][]=$tot_activeOut;
                    $grand_tot_in_active2Out[$sVal->id][]=$tot_unq_INactiveOut;


                ?>

                <td><strong>{{array_sum($tot_outletAsOnFirstMonth1[$sVal->id][$oVal->id])}}</strong></td>
                <td><strong>{{array_sum($tot_outletAddedMonth[$sVal->id][$oVal->id])}}</strong></td>
                <td><strong>{{$TotalOutlet}}</strong></td>
                <td><strong>{{$tot_activeOut}}</strong></td>
                <td><strong>{{round($tot_activePer,2)}}</strong></td>
                <td><strong>{{$tot_unq_INactiveOut}}</strong></td>


            </tr>
            @endforeach
            @endif

      
        {{-- Outlet End Here--}}

        {{-- Total Start Here--}}
            <tr style="background:yellow;">
                    <td colspan="2"><strong>{{Lang::get('common.total')}}</strong></td>
                    @foreach($monthArr as $mk=>$mval)
                <?php
                    $Grand_Sub_TotalOutlet=array_sum($grand_tot_outletAddedMonth[$sVal->id][$mk])+array_sum($grand_tot_outletAsOnFirstMonth[$sVal->id][$mk]);
                    $Grand_Sub_TotalActveOutlet=array_sum($grand_tot_activeOutlet[$sVal->id][$mk]);
                    $Grand_Sub_Total_In_ActveOutlet=array_sum($grand_tot_In_activeOutlet[$sVal->id][$mk]);

                    if($Grand_Sub_TotalOutlet>0){
                        $Grnd_Sub_tot_activePer=$Grand_Sub_TotalActveOutlet/$Grand_Sub_TotalOutlet*100;
                    }else{
                        $Grnd_Sub_tot_activePer =0;
                    }
                ?>
                    <td><strong>{{array_sum($grand_tot_outletAsOnFirstMonth[$sVal->id][$mk])}}</strong></td>
                    <td><strong>{{ array_sum($grand_tot_outletAddedMonth[$sVal->id][$mk])}}</strong></td>
                    <td><strong>{{ $Grand_Sub_TotalOutlet}}</strong></td>
                    <td><strong>{{$Grand_Sub_TotalActveOutlet}}</strong></td>
                    <td><strong>{{round($Grnd_Sub_tot_activePer,2)}}</strong></td>
                    <td><strong>{{$Grand_Sub_Total_In_ActveOutlet}}</strong></td>

                    @endforeach
            {{--  GRAND TOTAL START HERE----  --}}
           <?php
           $Grand_TotalOutlet=array_sum($grand_TotalOutlet[$sVal->id]);
           $Grand_TotalActveOutlet=array_sum($grand_tot_activeOut[$sVal->id]);
           $Grand_TotalInActve2MonthOutlet=array_sum($grand_tot_in_active2Out[$sVal->id]);
            if($Grand_TotalOutlet>0){
                $Grnd_tot_activePer=$Grand_TotalActveOutlet/$Grand_TotalOutlet*100;
            }else{
                $Grnd_tot_activePer =0;
            }
            ?>
                    <td><strong>{{array_sum($grand_outletAsOnFirstMonth[$sVal->id])}}</strong></td>
                    <td><strong>{{array_sum($grand_outletAddedMonth[$sVal->id])}}</strong></td>
                    <td><strong>{{$Grand_TotalOutlet}}</strong></td>
                    <td><strong>{{$Grand_TotalActveOutlet}}</strong></td>
                    <td><strong>{{round($Grnd_tot_activePer,2)}}</strong></td>
                    <td><strong>{{$Grand_TotalInActve2MonthOutlet}}</strong></td>

            {{--  GRAND TOTAL END HERE----  --}}
            </tr>
        {{-- Total End Here--}}

    @endforeach
    @endif
    {{-- State END Here--}}
    {{--{{dd($tot_outletAsOnFirstMonth)}}--}}



        </tbody>

</table>
