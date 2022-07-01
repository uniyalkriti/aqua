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
<?php $count=count($catalog_1); 
$supercol = ($count*2)+10; 

?>

<tr>
<td colspan="<?=$supercol?>"><h3>{{Lang::get('common.state_wise_product')}} FROM {{date('d-m-Y',strtotime($start_date))}} TO {{date('d-m-Y',strtotime($end_date))}}</h3></td>
</tr>
<tr>
<th rowspan="2">{{Lang::get('common.s_no')}}</th>
<th rowspan="2">{{Lang::get('common.location1')}}</th>
<th rowspan="2">{{Lang::get('common.location2')}}</th>
<th rowspan="2">{{Lang::get('common.location3')}}</th>
<th rowspan="2">{{Lang::get('common.total')}} {{Lang::get('common.retailer')}} Till {{Lang::get('common.date')}}</th>
<th rowspan="2"> New {{Lang::get('common.retailer')}} Added </th>
<th rowspan="2">{{Lang::get('common.total')}} Calls</th>
<th rowspan="2">{{Lang::get('common.productive_call')}}</th>
<th rowspan="2">{{Lang::get('common.secondary_sale')}}</th>
<th rowspan="2">{{Lang::get('common.total')}} {{Lang::get('common.user')}} Till {{Lang::get('common.date')}}</th>
<th rowspan="2">{{Lang::get('common.total')}} Active {{Lang::get('common.user')}}</th> 
@if(!empty($catalog_1))
<?php
foreach($catalog_1 as $key1=>$data1)
{
    echo "<th colspan=2>".$data1."</th>"; 
}
    ?>
    </tr>
    <tr>
    <?php
    for($i=0; $i<$count; $i++) 
    {
        ?>
        <th>{{Lang::get('common.productive_call')}}</th>
        <th>{{Lang::get('common.secondary_sale')}}</th>
         <?php
        }
        ?>
        </tr>
        @endif
        
    <tbody>
    <?php $i = 1;?>
    <?php
$totalOutlet = array();
$newOutlet = array();
$totalCall = array();
$productiveCall = array();
$totalSale = array();
$totalUser = array();
$totalActiveUser = array();
$totalpc = array();
$totalrv = array();
    ?>
    @if(!empty($records))
    
        @foreach($records as $key=>$data)
        
<?php

$totalOutlet[] = !empty($total_outlet[$data->l3_id])?$total_outlet[$data->l3_id]:'';
$newOutlet[] = !empty($new_outlet[$data->l3_id])?$new_outlet[$data->l3_id]:'';
$totalCall[] = !empty($total_call[$data->l3_id])?$total_call[$data->l3_id]:'';
$productiveCall[] = !empty($total_productive_call[$data->l3_id])?$total_productive_call[$data->l3_id]:'';
$totalSale[] = !empty($total_sale[$data->l3_id])?$total_sale[$data->l3_id]:'';
$totalUser[] = !empty($total_user[$data->l3_id])?$total_user[$data->l3_id]:'';
$totalActiveUser[] = !empty($total_active_user[$data->l3_id])?$total_active_user[$data->l3_id]:'';

?>
            <tr>
                <td>{{ $i}}</td>
                <td>{{$data->l1_name}}</td>
                <td>{{$data->l2_name}}</td>
                <td>{{$data->l3_name}}</td>
                <td>{{!empty($total_outlet[$data->l3_id])?$total_outlet[$data->l3_id]:''}}</td>
                <td>{{!empty($new_outlet[$data->l3_id])?$new_outlet[$data->l3_id]:''}}</td>
                <td>{{!empty($total_call[$data->l3_id])?$total_call[$data->l3_id]:''}}</td>
                <td>{{!empty($total_productive_call[$data->l3_id])?$total_productive_call[$data->l3_id]:''}}</td>
                <td>{{!empty($total_sale[$data->l3_id])?$total_sale[$data->l3_id]:''}}</td>
                <td>{{!empty($total_user[$data->l3_id])?$total_user[$data->l3_id]:''}}</td>
                <td>{{!empty($total_active_user[$data->l3_id])?$total_active_user[$data->l3_id]:''}}</td>
                @if(!empty($catalog_1))
        @foreach($catalog_1 as $key1=>$data1)  
 
        <?php
        $totalpc["pc"][$data1][] = !empty($pc_data[$data->l3_id][$key1]['pc_count'])?$pc_data[$data->l3_id][$key1]['pc_count']:'';
        $totalrv["rv"][$data1][] = !empty($rv_data[$data->l3_id][$key1]['rv_sum'])?$rv_data[$data->l3_id][$key1]['rv_sum']:'';
        ?>
        <td>{{!empty($pc_data[$data->l3_id][$key1]['pc_count'])?$pc_data[$data->l3_id][$key1]['pc_count']:''}}</td>
        <td>{{!empty($rv_data[$data->l3_id][$key1]['rv_sum'])?$rv_data[$data->l3_id][$key1]['rv_sum']:''}}</td>
        @endforeach
    @endif      
            </tr>
            <?php $i++;?>
        @endforeach
        <tr>
            
            <th>-</th>
            <th></th>
            <th><strong>{{Lang::get('common.total')}}</strong></th>
            <th></th>
            <th>{{ array_sum($totalOutlet) }}</th>
            <th>{{ array_sum($newOutlet) }}</th>
            <th>{{ array_sum($totalCall) }}</th>
            <th>{{ array_sum($productiveCall) }}</th>
            <th>{{ array_sum($totalSale) }}</th>
            <th>{{ array_sum($totalUser) }}</th>
            <th>{{ array_sum($totalActiveUser) }}</th>
            @foreach($catalog_1 as $key1=>$data1)      
                <th>{{ !empty($totalpc["pc"][$data1])?array_sum($totalpc["pc"][$data1]):array_sum($totalpc)}}</th>
                <th>{{ !empty($totalrv["rv"][$data1])?array_sum($totalrv["rv"][$data1]):array_sum($totalrv)}}</th>
            @endforeach
        </tr>

    @endif
    </tbody>
</table>
