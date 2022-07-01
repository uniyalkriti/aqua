@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th rowspan="3">S.No.</th>
        <th rowspan="3">ZONE</th>
        <th rowspan="3">REGION</th>
        <th rowspan="3">TOWN</th>
        <th rowspan="3">DISTRIBUTOR NAME</th>
        <th rowspan="3">F. YEAR</th>
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
        <th rowspan="3">Grand Total</th>
        <th colspan="4">Growth</th>
    </tr>
    <tr>
        <th colspan="2">Month</th>
        <th colspan="2">Cummulative</th>
    </tr>
    <tr>
        <th>Growth over last year same month (RV)</th>
        <th>%</th>
        <th>Growth over last year same period (RV)</th>
        <th>%</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0))

    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>