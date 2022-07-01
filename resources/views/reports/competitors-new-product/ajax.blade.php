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
@if(!empty($records) && count($records)>0)
    <tr><td colspan="15"><h3>Competitors New Product</h3></td></tr>
@endif
    <tr>
        <th rowspan="2">S.No.</th>
        <th rowspan="2">BRAND</th>
        <th rowspan="2">PRODUCT NAME</th>
        <th rowspan="2">WEIGHT</th>
        <th colspan="2">CONSUMER PRICE</th>
        <th colspan="3">COST TO RETAILER INCLUDING ALL TAXES PER CASE / BAG</th>
        <th rowspan="2">UNITS PER CASE / BAG</th>
        <th rowspan="2">NET COST PRICE TO RETAILER ( PER UNIT AFTER SCHEME)</th>
        <th rowspan="2">RETAILER MARGIN PER UNIT</th>
        <th rowspan="2">CONSUMER SCHEME IF ANY</th>
        <th rowspan="2">CASH MEMO  NO.</th>
        <th rowspan="2">DATE</th>
    </tr>
    <tr>
        <th>MRP</th>
        <th>BEING SOLD TO CONSUMER INCLUDING ALL TAXES</th>
        <th>BEFORE TRADE SCHEME</th>
        <th>TRADE SCHEME</th>
        <th>AFTER TRADE SCHEME</th>
    </tr>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$record)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$record->brand}}</td>
            <td>{{$record->product_and_brand_name}}</td>
            <td>{{$record->weight}}</td>
            <td>{{$record->mrp}}</td>
            <td>{{$record->being_sold_to_consumer}}</td>
            <td>{{$record->before_trade_scheme}}</td>
            <td>{{$record->trade_scheme}}</td>
            <td>{{$record->after_trade_scheme}}</td>

            <td>{{$record->units_per_case_bag}}</td>
            <td>{{$record->net_cost_price_to_retailer}}</td>
            <td>{{$record->retailer_margin_per_unit}}</td>
            <td>{{$record->consumer_scheme}}</td>
            <td>{{$record->must_enclose_cash_memo}}</td>
            <td>{{$record->launch_date}}</td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="15">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>