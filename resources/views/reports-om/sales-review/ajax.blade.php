@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th colspan="5">PRIMARY SALES TARGET  VS ACHIEVEMENT (CASES)</th>
        <th colspan="3">GROWTH TREND (CASES)</th>
        <th colspan="3">PLAN-MONTH</th>
        <th>SUPER DISTRIBUTOR / DISTRIBUTOR / SUB DISTRIBUTORAPPOINTMENT / REPLACEMENT PLAN FOR THE MONTH</th>
        <th colspan="5">SUPER DISTRIBUTOR / DISTRIBUTOR / SUB DISTRIBUTORAPPOINTMENT / REPLACEMENT PLAN FOR THE MONTH</th>
    </tr>
    <tr>
        <th colspan="5">FOR THE MONTH OF FEB (1.02.18 TO 28.02.18)</th>
        <th colspan="3">W.R.T. LAST YEAR SAME MONTH</th>
        <th colspan="3">PRIMARY SALES PLAN (CASES)</th>
        <th rowspan="2">TOWN</th>
        <th rowspan="2">SUPER DISTRIBUTOR / DISTRIBUTOR/ SUB DISTRIBUTOR</th>
        <th rowspan="2">APPOINTMENT / REPLACEMENT</th>
        <th rowspan="2">RESP</th>
        <th rowspan="2">D.DATE</th>
    </tr>
    <tr>
        <th>PRODUCTS</th>
        <th>TARGET</th>
        <th>ACHIEVEMENT</th>
        <th>+ / - (CASES)</th>
        <th>+ / - (%)</th>
        <th>ACHIEVEMENT</th>
        <th>+ / - (CASES)</th>
        <th>+ / - (%)</th>
        <th>TGT THIS MONTH</th>
        <th>ACH MONTH LASY YEAR SAME MONTH</th>
        <th>PLAN THIS MONTH</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)

    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>