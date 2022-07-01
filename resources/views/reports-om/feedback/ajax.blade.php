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
    <thead>
    <tr>
        <th>S.no.</th>
        <th>Feedback / Suggestion</th>
        <th>Suggested Start Date</th>
        <th>Estimated Volume Growth (rv)</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)
        
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$record->suggestion}}</td>
                <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td>
                <td>{{$record->estimated_volume_growth}}</td>
                <td>{{!empty($record->cur_date_time)?date('d-M-Y',strtotime($record->cur_date_time)):'NA'}}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>