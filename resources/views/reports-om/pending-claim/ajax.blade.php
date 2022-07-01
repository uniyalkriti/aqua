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
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <thead>
    <tr>
        <th>S.No.</th>
        <th>Date Of Submission</th>
        <th>Super Distributor / Distributor</th>
        <th>Town</th>
        <th>Nature Of Claim/ Issue</th>
        <th>Inv. No.</th>
        <th>Claim Papers</th>
        <th>Remark</th>
        <th>Expected Resolution Date</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$record)
{{--        @php dd($record->nature_of_claim) @endphp--}}
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$record->submission_date}}</td>
            <td>{{$record->dealer_name}}</td>
            <td>{{$record->town}}</td>
            <td>{{$record->nature_of_claim}}</td>
            <td>{{$record->invoice_number}}</td>
            <td>{{$record->claim_paper}}</td>
            <td>{{$record->remark}}</td>
            <td>{{$record->expected_resolution_date}}</td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="23">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>