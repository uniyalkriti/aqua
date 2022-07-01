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
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <td colspan="24"><h3>{{Lang::get('common.distributer_sales_trend')}}</h3></td>
    </tr>
    <tr>
        <th rowspan="3"> {{Lang::get('common.s_no')}}</th>
        <th rowspan="3"> {{Lang::get('common.location3')}}</th>
        <th rowspan="3"> {{Lang::get('common.location4')}}</th>
        <th rowspan="3"> {{Lang::get('common.location5')}}</th>
        <th rowspan="3"> {{Lang::get('common.location6')}}</th>
        <th rowspan="3"> {{Lang::get('common.distributor')}} NAME</th>


        <th rowspan="3">F.Year</th>
        <th rowspan="3">April</th>
        <th rowspan="3">May</th>
        <th rowspan="3">June</th>
        <th rowspan="3">July</th>
        <th rowspan="3">August</th>
        <th rowspan="3">September</th>
        <th rowspan="3">October</th>
        <th rowspan="3">November</th>
        <th rowspan="3">December</th>
        <th rowspan="3">January</th>
        <th rowspan="3">February</th>
        <th rowspan="3">March</th>
        <th rowspan="3">{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</th>

        <th colspan="4">Growth</th>
        {{-- <th colsspan="4">{{Lang::get('common.month')}} Balance {{Lang::get('common.day')}} & {{Lang::get('common.month')}} {{Lang::get('common.total')}}</th> --}}
    </tr>
    <tr>
        <th colspan="2">{{Lang::get('common.month')}}</th>
        <th colspan="2">Cummulative</th>


    </tr>
    <tr>
        <th>Growth over last year same {{Lang::get('common.month')}} (RV)</th>
        <th>%</th>
        <th>Growth over last year same period (RV)</th>
        <th>%</th>

    </tr>
    <tbody>
    <?php $i = 1;?>
    {{--#f1->current year,f2->last year,f3->2 year back--}}
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php
         $dealer_id = Crypt::encryptString($data->id); 
        ?>
            <tr>
                <?php $a = []; $b = [];$c = [];?>
                <td rowspan="3">{{$i}}</td>
                <td rowspan="3">{{$data->l3_name}}</td>
                <td rowspan="3">{{$data->l4_name}}</td>
                <td rowspan="3">{{$data->l5_name}}</td>
                <td rowspan="3">{{$data->l6_name}}</td>
                <td rowspan="3"><a href="{{url('distributor/'.$dealer_id)}}">{{$data->name}}</a></td>
                <td>{{$y3.'-'.($y3+1)}}</td>
                @foreach($monthArr as $mk=>$md)
                    <td>{{$a[]=!empty($arr[$data->id]['f3'][$md] )?$arr[$data->id]['f3'][$md] :'0'}}</td>
                    <?php $c[] = !empty($arr[$data->id]['f1'][$md] ) ? $arr[$data->id]['f1'][$md]  : '0';?>
                @endforeach
                <?php
                $x1 = !empty($arr[$data->id]['f3'][$md] ) ? $arr[$data->id]['f3'][$md]  : 0;
                $x2 = !empty($arr[$data->id]['f1'][$md] ) ? $arr[$data->id]['f1'][$md]  : 0;

                $c1 = !empty($c) ? array_sum($c) : 0;
                $c2 = !empty($a) ? array_sum($a) : 0;
                ?>
                <td>{{!empty($a)?array_sum($a):'0'}}</td>
                <td>{{$t1=$x1-$x2}}</td>
                <td>{{$x2>0?($x1/$x2)*100-100:0}}</td>
                <td>{{$c1-$c2}}</td>
                <td>{{$c2>0?($c1/$c2)*100-100:0}}</td>
            </tr>
            <tr>
                <td>{{$y2.'-'.($y2+1)}}</td>
                <?php $b = [];$c=[];?>
                @foreach($monthArr as $mk=>$md)
                    <td>{{$b[]=!empty($arr[$data->id]['f2'][$md] )?$arr[$data->id]['f2'][$md] :'0'}}</td>
                    <?php $c[] = !empty($arr[$data->id]['f1'][$md] ) ? $arr[$data->id]['f1'][$md]  : '0';?>
                @endforeach
                <?php
                $x1 = !empty($arr[$data->id]['f2'][$md] ) ? $arr[$data->id]['f2'][$md]  : 0;
                $x2 = !empty($arr[$data->id]['f1'][$md] ) ? $arr[$data->id]['f1'][$md]  : 0;

                $c1 = !empty($c) ? array_sum($c) : 0;
                $c2 = !empty($b) ? array_sum($b) : 0;
                ?>
                <td>{{!empty($b)?array_sum($b):'0'}}</td>
                <?php $x1 = !empty($arr[$data->id]['f2'][$md] ) ? $arr[$data->id]['f2'][$md]  : 0;
                $x2 = !empty($arr[$data->id]['f1'][$md] ) ? $arr[$data->id]['f1'][$md]  : 0;
                ?>
                <td>{{$t2=$x1-$x2}}</td>
                <td>{{$x2>0?($x1/$x2)*100-100:0}}</td>
                <td>{{$c1-$c2}}</td>
                <td>{{$c2>0?($c1/$c2)*100-100:0}}</td>
            </tr>
            <tr>
                <td>{{$y1.'-'.($y1+1)}}</td>
                <?php $a=[];?>
                @foreach($monthArr as $mk=>$md)
                    <td>{{$a[]=!empty($arr[$data->id]['f1'][$md] )?$arr[$data->id]['f1'][$md] :'0'}}</td>
                @endforeach
                <td>{{!empty($a)?array_sum($a):0}}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <?php $i++;?>
        @endforeach
    @endif
    </tbody>
</table>