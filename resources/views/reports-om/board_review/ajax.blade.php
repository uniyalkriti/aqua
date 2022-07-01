@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th rowspan="3">DISTRIBUTOR / SO / ASM / RSM</th>
        <th rowspan="3">TOWN / H.Q.</th>
        <th rowspan="3">BRAND</th>
        <th colspan="6">W.R.T. TARGETS</th>
        <th colspan="6">W.R.T LAST YEAR</th>
        <th colspan="6">W.R.T. LAST MONTH</th>
        <th colspan="6">POA FOR NEXT MONTH</th>
    </tr>
    <tr>
        <th colspan="3">MONTH</th>
        <th colspan="3">CUMMULATIVE</th>
        <th colspan="3">MONTH</th>
        <th colspan="3">CUMMULATIVE</th>
        <th colspan="2" rowspan="2">IMMEDIATE PREECEDING PERIOD</th>
        <th colspan="2" rowspan="2">THIS MONTH</th>
        <th colspan="2" rowspan="2">% GROWTH</th>
        <th colspan="2" rowspan="2">LAST YEAR NEXT MONTH</th>
        <th colspan="2" rowspan="2">TARGET</th>
        <th colspan="2" rowspan="2">PLAN</th>
    </tr>
    <tr>
        <th>TARGET</th>
        <th>ACHIEVEMENT</th>
        <th>% VAR</th>
        <th>TARGET</th>
        <th>ACHIEVEMENT</th>
        <th>% VAR</th>
        <th>LAST YEAR SAME MONTH</th>
        <th>THIS YEAR</th>
        <th>% GROWTH</th>
        <th>LAST YEAR SAME PERIOD</th>
        <th>THIS YEAR</th>
        <th>% GROWTH</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0))
    <tr>
        <td></td>
        <td></td>
        <td>FCT</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>FPC</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>MATCH BOX</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>ELAICHI</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>B-KOOL</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>PALLETS</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>AGARBATTI</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>SILVER LEAVES</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>WAFFERS</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>RINGS & PUFFS</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>TEDA MEDA</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>NAMKEEN</td>
    </tr>
    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>