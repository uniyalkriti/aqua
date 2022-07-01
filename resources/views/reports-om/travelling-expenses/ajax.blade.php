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
        <th>S.No.</th>
        <th>User</th>
        <th>Travelling Date</th>
        <th>Arrival Time</th>
        <th>Departure Time</th>
        <th>Distance</th>
        <th>Fare</th>
        <th>D.A.</th>
        <th>Hotel</th>
        <th>Postage</th>
        <th>TelephoneExpense</th>
        <th>Conveyance</th>
        <th>Misc</th>
        <th>Total</th>
        <th>ArrivalID</th>
        <th>DepartureID</th>
        <th>Travel ModeID</th>
        <th>Date Time</th>
        <th>Geo Dddress</th>
        <th>Order Id</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)
        
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$record->user_name}}</td>
                <td>{{!empty($record->travellingDate)?date('d-M-Y',strtotime($record->travellingDate)):'N/A'}}</td>
                {{-- <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td> --}}
                <td>{{$record->arrivalTime}}</td>
                <td>{{$record->departureTime	}}</td>
                <td>{{$record->distance}}</td>
                <td>{{$record->fare}}</td>
                <td>{{$record->da}}</td>
                <td>{{$record->hotel}}</td>
                <td>{{$record->postage}}</td>
                <td>{{$record->telephoneExpense	}}</td>
                <td>{{$record->conveyance}}</td>
                <td>{{$record->misc}}</td>
                <td>{{$record->total}}</td>
                <td>{{$record->arrivalID}}</td>
                <td>{{$record->departureID}}</td>
                <td>{{$record->travelModeID}}</td>
                <td>{{!empty($record->date_time)?date('d-M-Y',strtotime($record->date_time)):'NA'}}</td>
                <td>{{$record->geo_address}}</td>
                <td>{{$record->order_id}}</td>

                 

            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="30">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>