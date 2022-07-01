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
        <th rowspan="2">S.no</th>
        <th rowspan="2">BRAND</th>
        <th rowspan="2">WEIGHT</th>
        <th colspan="2">CONSUMER PRICE</th>
        <th colspan="3">COST TO RETAILER INCLUDING ALL TAXES PER CASE / BAG</th>
        <th rowspan="2">UNITS PER CASE / BAG</th>
        <th rowspan="2">NET COST PRICE TO RETAILER ( PER UNIT AFTER SCHEME)</th>
        <th rowspan="2">RETAILER MARGIN PER UNIT</th>
        <th rowspan="2">CONSUMER SCHEME IF ANY</th>
        <th colspan="2">MUST ENCLOSE CASH MEMO</th>
    </tr>
    <tr>
        <th>MRP</th>
        <th>BEING SOLD TO CONSUMER INCLUDING ALL TAXES</th>
        <th>BEFORE TRADE SCHEME</th>
        <th>TRADE SCHEME</th>
        <th>AFTER TRADE SCHEME</th>
        <th>CASH MEMO NO.</th>
        <th>DATE</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$data)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$data->brand}}</td>
            <td>{{$data->weight}}</td>
            <td>{{$data->mrp}}</td>
            <td>{{$data->being_sold_to_consumer}}</td>
            <td>{{$data->before_trade_scheme}}</td>
            <td>{{$data->trade_scheme}}</td>
            <td>{{$data->after_trade_scheme}}</td>
            <td>{{$data->units_per_case_bag}}</td>
            <td>{{$data->net_cost_price_to_retailer}}</td>
            <td>{{$data->retailer_margin_per_unit}}</td>
            <td>{{$data->consumer_scheme}}</td>
            <td>{{$data->must_enclose_cash_memo_no}}</td>
            {{--<td>{{$data->consumer_scheme}}</td>--}}
            {{--<td>{{$data->memo}}</td>--}}
            <td>{{!empty($data->cur_date_time)?date('d-M-Y',strtotime($data->cur_date_time)):'N/A'}}</td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>