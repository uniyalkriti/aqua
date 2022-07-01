@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif


<table id="simple-table" class="table table-bordered" >

<thead>
    <tr>
        <td colspan="500" class="table-header center"><h3 style="background-color: '#438eb9';" >{{Lang::get('common.target_db')}}</h3></td>
    </tr>
    <tr class="info" style="color: black;">
        <?php $rowspann = '3';?>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.s_no')}}</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.month')}}</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.user_name')}}</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.location3')}} Name</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.location4')}} Name</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.location5')}} Name</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.location6')}} Name</th>
        <th rowspan="{{$rowspann}}">{{Lang::get('common.distributor')}} Name</th>
       
        @foreach($f_out as $key => $value)

            <?php $count_colsapan = COUNT($value['details']); ?>
            <th colspan="{{$count_colsapan*2+2}}">{{$value['name']}}</th>
        @endforeach
        
    </tr>
    
    <tr>
        @foreach($f_out as $key => $value)
            @foreach($value['details'] as $inner_key => $inner_value)
            
                <th colspan="2">{{$inner_value}}</th>
            
            @endforeach
            <th rowspan="2">Total Target</th>
            <th rowspan="2">Total Achievement</th>
            
        @endforeach
    </tr>
    
     <tr>
        @foreach($f_out as $key => $value)
            @foreach($value['details'] as $inner_key => $inner_value)
            
                <th>Target</th>
                <th>Achievement</th>
            
            @endforeach
            
        @endforeach

    </tr>
    
    </thead>
    <tbody>
        @foreach($records as $key => $value )
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$month}}</td>
                <td>{{$value->user_name}}</td>
                <td>{{$value->l3_name}}</td>
                <td>{{$value->l4_name}}</td>
                <td>{{$value->l5_name}}</td>
                <td>{{$value->l6_name}}</td>
                <td>{{$value->dealer_name}}</td>
                
                @foreach($f_out as $c_key => $c_value)
                    <?php $total = array(); $total_2 = array(); ?>
                    @foreach($c_value['details'] as $inner_key => $inner_value)
                    
                        <td>{{!empty($master_target[$inner_key.$value->dealer_id])?$total[] = $master_target[$inner_key.$value->dealer_id]:'-'}}</td>
                        <td>{{!empty($achievement_sale[$inner_key.$value->dealer_id])?$total_2[] = $achievement_sale[$inner_key.$value->dealer_id]:'-'}}</td>
                    
                    @endforeach
                    <td>{{array_sum($total)}}</td>
                    <td>{{array_sum($total_2)}}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    
</table>


