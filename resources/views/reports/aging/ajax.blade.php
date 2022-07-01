@if(count($records)>0)
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
        <td colspan="11"><h3>{{Lang::get('common.aging')}}</h3></td>
    </tr>
    <tr>
        <th rowspan="2">{{Lang::get('common.aging')}}</th>
        <th rowspan="2">{{Lang::get('common.location3')}}</th>
        <th colspan="2">SUPER {{Lang::get('common.distributor')}} / {{Lang::get('common.distributor')}}</th>
        <th rowspan="2">{{Lang::get('common.location6')}}</th>
        <th rowspan="2">Last Invoice {{Lang::get('common.date')}}</th>
        <th rowspan="3">DR. Balance</th>
        <th rowspan="2">0-30</th>
        <th rowspan="2">31-45</th>
        <th rowspan="2">46-60</th>
        <th rowspan="2">61 ></th>
    </tr>
    <tr>
        <th>Code</th>
        <th>Name</th>
    </tr>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$record)
       <?php 
        $dealer_id = Crypt::encryptString($record->id); 
        ?>
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$record->state}}</td>
            <td>{{$record->dealer_code}}</td>
            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$record->name}}</a></td>
            <td>{{$record->town}}</td>
            <td>{{!empty($record->last_invoice)?$record->last_invoice:'N/A'}}</td>
            <td>{{!empty($record->total_remaining)?$record->total_remaining:0}}</td>
            <td>{{!empty($record->amount1)?$record->amount1:0 }}</td>
            <td>{{!empty($record->amount2)?$record->amount2:0 }}</td>
            <td>{{!empty($record->amount3)?$record->amount3:0 }}</td>
            <td>{{!empty($record->amount4)?$record->amount4:0 }}</td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="11">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>