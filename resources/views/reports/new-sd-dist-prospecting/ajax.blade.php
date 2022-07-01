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

    #simple-table th {
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
  <tr><td colspan="30"><h3>Daily Prospecting Data</h3></td></tr>
    <tr>
        <th>S.No.</th>
        <th>Date</th>
        <th>State</th>
        <th>District</th>
        <th>Town</th>
        <th>Party Name	</th>
        <th>Party Address</th>
        <th>Phone No</th>
        <th>Residence Phone	</th>
        <th>Mobile No</th>
        <th>Email Id</th>
        <th>Person Met_and Status</th>
        <th>Established Since</th>
        <th>Annual Turn Over</th>
        <th>Reputation Trade Relation</th>
        <th>Financial Position</th>
        <th>Level Ofinterst</th>
        <th>From Time</th>
        <th>To Time</th>
        <th>Units Availble And Qty</th>
        <th>Gst No</th>
        <th>Gst Registrtion Date</th>

        <th>Pan Card No</th>
        <th>Pan Card Date	</th>
        <th>Godown Size</th>
        <th>No Of Employee</th>
        <th>Terms Condition</th>
        <th>Assured Investment</th>
        <th>Stockiest From Filled</th>
        <th>Comments</th>
    </tr>
    <tbody>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)

            <tr>
                <td>{{$key+1}}</td>
                <td>{{!empty($record->cur_date_time)?date('d-M-Y',strtotime($record->cur_date_time)):'NA'}}</td>
                <td>{{$record->state}}</td>
                <td>{{$record->district}}</td>
                <td>{{$record->town}}</td>
                <td>{{$record->party_name}}</td>
                {{-- <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td> --}}
                <td>{{$record->party_address}}</td>
                <td>{{$record->phone_no}}</td>
                <td>{{$record->residence_phone}}</td>
                <td>{{$record->mobile_no}}</td>
                <td>{{$record->email_id}}</td>
                <td>{{$record->person_met_and_status}}</td>
                <td>{{$record->established_since}}</td>
                <td>{{$record->annual_turn_over}}</td>
                <td>{{$record->reputation_trade_relation}}</td>
                <td>{{$record->financial_position}}</td>
                <td>{{$record->level_ofinterst}}</td>
                <td>{{$record->from_time}}</td>
                <td>{{$record->to_time}}</td>
                <td>{{$record->units_availble_and_qty}}</td>
                <td>{{$record->gst_no}}</td>
                <td>{{!empty($record->gst_registrtion_date)?date('d-M-Y',strtotime($record->gst_registrtion_date)):'NA'}}</td>

                <td>{{$record->pan_card_no}}</td>
                <td>{{$record->pan_card_date}}</td>
                <td>{{$record->godown_size}}</td>
                <td>{{$record->no_of_employee}}</td>
                <td>{{$record->terms_condition}}</td>
                <td>{{$record->assured_investment}}</td>
                <td>{{$record->stockiest_from_filled}}</td>
                <td>{{$record->comments}}</td>
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
