@if(!empty($catalog))
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
<table id="simple-table" class="table table-bordered" style="font-size: 10px">
    <thead>
    <tr>
        <th colspan="12"></th>
        <th colspan="2">1</th>
        <th colspan="2">2</th>
        <th colspan="2">3</th>
        <th colspan="2">4</th>
        <th colspan="2">5</th>
        <th colspan="2">6</th>
        <th colspan="2">7</th>
        <th colspan="4">IST WEEK STATUS 1ST - 7TH</th>
        <th colspan="14"></th>
        <th colspan="4">2ND  WEEK STATUS 8TH - 14TH</th>
        <th colspan="4">MONTH BALANCE DAYS & MONTH TOTAL</th>
    </tr>
    <tr>
        <th rowspan="2">PRODUCT</th>
        <th rowspan="2">SKU</th>
        <th rowspan="2">INVOICE PRICE</th>
        <th rowspan="2">PRIMARY SALES TARGET FOR THE MONTH</th>
        <th rowspan="2">PRIMARY SALES ACH JUL'17</th>
        <th rowspan="2">OPENING STOCK WITH DISTRIBUTORS ON 1.06.18</th>
        <th rowspan="2">PRIMARY SALES ACH JUN'18</th>
        <th rowspan="2">SECONDARY SALES ACH JUN'18</th>
        <th rowspan="2">OPENING STOCK WITH DISTRIBUTORS ON 1.07.18</th>
        <th rowspan="2">PRIMARY PLAN JUL'18</th>
        <th rowspan="2">SECONDARY PLAN JUL'18</th>
        <th rowspan="2">ESTIMATED CLOSING STOCK ON 31.07.18</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">TOTAL SEC.</th>
        <th rowspan="2">TOTAL PRI.</th>
        <th colspan="2">GAP AGAINST COMM</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">SEC.</th>
        <th rowspan="2">PRI.</th>
        <th rowspan="2">TOTAL SEC.</th>
        <th rowspan="2">TOTAL PRI.</th>
        <th colspan="2">GAP AGAINST COMM</th>
        <th rowspan="2">TOTAL SECONDARY</th>
        <th rowspan="2">TOTAL PRIMARY</th>
        <th colspan="2">MONTHLY GAP AGAINST COMM</th>
    </tr>
    <tr>
        <th>SEC GAP</th>
        <th>PRI GAP</th>
        <th>SEC GAP</th>
        <th>PRI GAP</th>
        <th>SEC GAP</th>
        <th>PRI GAP</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($catalog) && count($catalog)>0)
    @foreach($catalog as $key=>$data)
        @if(!empty($rows[$data->id]))
            @foreach($rows[$data->id] as $k=>$d)
        <tr>
            <td>{{$data->name}}</td>
            <td>{{$d->sku}}</td>
            <td>{{$d->base_price}}</td>
            <td>N/A</td>
            <td>N/A</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
                @endif
        @endforeach
    {{--<tr>--}}
        {{--<td>ZARDA</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>PAN CHATNI</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>MATCH BOX</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>B-KOOL</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>ELAICHI</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>PALLETS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">VALUE Rs.LACS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">GRAND TOTAL VALUE Rs.LACS</td>--}}
    {{--</tr>--}}

    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>