@if(count($records)>0)
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
    <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th>S.No.</th>
        <th>DATE OF SUBMISSION</th>
        <th>SUPER DISTRIBUTOR / DISTRIBUTOR</th>
        <th>TOWN</th>
        <th>NATURE OF CLAIM/ ISSUE</th>
        <th>INV. NO.</th>
        <th>CLAIM PAPERS</th>
        <th>REMARKS</th>
        <th>EXPECTED RESOLUTION DATE</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$record)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{date('Y-m-d',strtotime($record->created_at))}}</td>
            <td></td>
            <td>{{$record->nature_of_claim}}</td>
            <td>{{$record->in_date}}</td>
            <td>{{$record->in_no}}</td>
            <td>{{$record->claim_paper}}</td>
            <td>{{$record->remarks}}</td>
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