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
    #simple-table th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    <caption><h3></h3></caption>
    <tr>
        <th rowspan="3">S.No.</th>
        <th rowspan="3">Day</th>
        <th rowspan="3">Date</th>
        <th rowspan="3">User Name</th>
        <th rowspan="3">Designation</th>
        <th rowspan="3">Worked With</th>
        <th rowspan="3">As Per Tour Program</th>
        <th rowspan="3">Actual</th>
        <th rowspan="3">Today's Tour Programme: FOLLOWED / CHANGED</th>
        <th rowspan="3">Start Time</th>
        <th rowspan="3">Today's Task</th>
        <th rowspan="3">Distributor</th>
        <th rowspan="3">Beat Name</th>
        <th rowspan="3">Time Of First Call</th>
        <th rowspan="3">Time Of Last Call</th>
        <th rowspan="3">End Time</th>
        <th rowspan="3">Total Outlets</th>
        <th rowspan="3">Visit Today</th>
        <th rowspan="3">Today's Productive Calls</th>
        <th rowspan="3">New Outlets Added Today</th>
        <th rowspan="2" colspan="2">SEC SALES</th>
        <th rowspan="2" colspan="2">INT. DIST SALE</th>
        <th rowspan="2" colspan="2">TOTAL SALE SEC + ID</th>
        <th colspan="{{!empty($catalog)?count($catalog)*3:10}}">PRODUCTWISE PRODUCTIVITY DETAIL</th>
    </tr>
    <tr>
        @if(!empty($catalog))
            @foreach($catalog as $key=>$data)
                <th colspan="3">{{$data}}</th>
            @endforeach
        @endif
    </tr>
    <tr>
        <th>KG</th>
        <th>RV</th>
        <th>KG</th>
        <th>RV</th>
        <th>KG</th>
        <th>RV</th>
        @if(!empty($catalog))
            @foreach($catalog as $key=>$data)
                <th>CALLS</th>
                <th>RD KG</th>
                <th>RV</th>
            @endforeach
        @endif
    </tr>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{date('l',strtotime($record->working_date))}}</td>
                <td>{{$record->working_date}}</td>
                <td>{{!empty($record->middle_name)?$record->first_name.' '.$record->middle_name.' '.$record->last_name:$record->first_name.' '.$record->last_name}}</td>
                <td>{{$record->rolename}}</td>
                <td>{{$record->working_with}}</td>
                <td>{{$a=$record->town_name}}</td>
                <td>{{$b=!empty($townArr[$key])?implode(',',array_unique($townArr[$key])):'N/A'}}</td>
                <td>{{$a!=$b?'CHANGED':'FOLLOWED'}}</td>
                <td>{{!empty($record->attendance_time)?date('H:i:s',strtotime($record->attendance_time)):'N/A'}}</td>
                <td>{{!empty($working_status[$record->work_status])?$working_status[$record->work_status]:'N/A'}}</td>
                <td>{{!empty($dealer_name[$key])?implode(',',array_unique($dealer_name[$key])):'N/A'}}</td>
                <td>{{!empty($beatArr[$key])?implode(',',array_unique($beatArr[$key])):'N/A'}}</td>
                <td>{{!empty($record->first_call1)?$record->first_call1:'N/A'}}</td>
                <td>{{!empty($record->last_call)?$record->last_call:'N/A'}}</td>
                <td>{{!empty($record->checkout_time)?date('H:i:s',strtotime($record->checkout_time)):'N/A'}}</td>
                <td>{{!empty($total_outlet[$key])?$total_outlet[$key]:0}}</td>
                <td>{{!empty($visit_count[$key])?$visit_count[$key]:0}}</td>
                <td>{{!empty($productive_call[$key])?$productive_call[$key]:0}}</td>
                <td>{{!empty($new_outlet[$key])?$new_outlet[$key]:0}}</td>
                <td>{{$alpha=!empty($rv[$key])?$rv[$key]:0}}</td>
                <td>{{$beta=!empty($record->total_sale_value)?$record->total_sale_value:0}}</td>
                <td>{{$alpha}}</td>
                <td>{{$beta}}</td>
                <td>{{$alpha*2}}</td>
                <td>{{$beta*2}}</td>
                @if(!empty($rv_product[$key]))
                    @foreach($rv_product[$key] as $key=>$data)
                        <td>{{!empty($data['calls'])?$data['calls']:0}}</td>
                        <td>{{!empty($data['rd'])?$data['rd']:0}}</td>
                        <td>{{!empty($data['sale'])?$data['sale']:0}}</td>
                    @endforeach
                    @else
                    @foreach($catalog as $key=>$data)
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    @endforeach
                @endif
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="50">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
</table>