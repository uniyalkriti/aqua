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
    <td colspan="{{count($datearray)+4}}"><h3>{{Lang::get('common.monthly_progressive')}} {{date('d-m-Y',strtotime($year))}} TO {{date('t-m-Y',strtotime($year))}}</h3>
    </td>
    </tr>
    <tr>
    <th>{{Lang::get('common.location3')}}</th>
    <th>{{Lang::get('common.retailer')}} {{Lang::get('common.type')}}</th>
  
<?php
 $start_date =date('Y-m-d',strtotime($year));
 $end_date= date("Y-m-t", strtotime($start_date));
$startTime = strtotime($start_date);
$endTime = strtotime($end_date);


while ($startTime <= $endTime) {
           
   ?>
   <th><?php echo date("d-m-Y",$startTime);?></th>
  <?php       

$startTime = $startTime+86400;
}

 ?>
   <th>{{Lang::get('common.total')}}</th>
</tr>

    <tbody>
    <?php
    $i = 1;
    $grand_total = array();
    $below_total = array();
    ?>
<tr>
    @if(!empty($statedata))
        @foreach($statedata as $key=>$data)
        <tr>
            <td rowspan="{{count($outlet_type_name)}}">{{$data->name}}</td>
            @if(!empty($outlet_type_name))
             @foreach($outlet_type_name as $key=>$value)
                <td>{{$value->name}}</td>
                    <?php 
                        $total = array();
                    ?>
                    @foreach($datearray as $datekey=>$date) 
                        @php
                        $total[]=!empty($details[$date][$value->id][$data->id]['outlet_count'])?$details[$date][$value->id][$data->id]['outlet_count']:'0';
                        $below_total[$date][]=!empty($details[$date][$value->id][$data->id]['outlet_count'])?$details[$date][$value->id][$data->id]['outlet_count']:'0';
                        @endphp
                        <td>{{!empty($details[$date][$value->id][$data->id]['outlet_count'])?$details[$date][$value->id][$data->id]['outlet_count']:'0'}}</td>
                    @endforeach
                <td>{{array_sum($total)}}</td>
                <?php $grand_total[] = array_sum($total); ?>
        </tr>
</tr>   
             @endforeach
            @endif
        @endforeach
        <tr>
            <th colspan="2"> {{Lang::get('common.grand')}} {{Lang::get('common.total')}} </th>
            @foreach($datearray as $datekey=>$date)
            <th>{{array_sum($below_total[$date])}}</th>
            @endforeach
            <th>{{array_sum($grand_total)}}</th>
        </tr>
    @endif
    </tbody>
</table>