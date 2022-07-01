@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <td colspan="20"><h3>Product Tracker</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Item Code</th>
        <th>Number of Units</th>
        <th>Weight of Each Unit</th>
        <th>Serial Number of the Box</th>
        <th>Lattitude</th>
        <th>Longitude</th>
        <th>Location Identifier</th>
        <th>Date Time of Scan</th>
        <th>In_Out_Indicator</th>
        <th>Product Code</th>
    </tr>
    <tbody>
        <tr>
            @if(!empty($records))
                @foreach($records as $key => $value)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$value->item_code}}</td>
                    <td>{{$value->number_of_units}}</td>
                    <td>{{$value->weight_of_each_unit}}</td>
                    <td>{{$value->serial_number_box}}</td>
                    <td>{{$value->lat}}</td>
                    <td>{{$value->lng}}</td>
                    <td>{{$value->location_identifier}}</td>
                    <td>{{$value->date_time_scan}}</td>
                    <td>{{$value->in_out_indicator}}</td>
                    <td>{{$value->product_code}}</td>
                </tr>
                @endforeach
            @endif
        </tr>
    </tbody>
</table>