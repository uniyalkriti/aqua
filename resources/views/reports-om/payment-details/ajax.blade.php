@if(!empty($data))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<style>
    table {
        border-collapse: collapse !important;
    }

    table, th, td {
        border: 1px solid black !important;
    }
    th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    <thead>
    <tr>
        <th colspan="2">PAYMENT RECEIVABLE DETAILS</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($data) )
        <tr>
            <td>Zone</td>
            <td>{{$data->zone}}</td>
        </tr>
        <tr>
            <td>USER NAME</td>
            <td>{{$data->name}}</td>
        </tr>
        <tr>
            <td>EMP ID</td>
            <td>{{$data->emp_id}}</td>
        </tr>
        <tr>
            <td>HQ</td>
            <td>{{$data->hq}}</td>
        </tr>
        <tr>
            <td>DESIGNATION</td>
            <td>{{$data->rolename}}</td>
        </tr>
        <tr>
            <td>SD/D NAME</td>
            <td>{{$data->dealer_name}}</td>
        </tr>
        <tr>
            <td>TOWN</td>
            <td>{{$data->town}}</td>
        </tr>
        <tr>
            <td>DATED</td>
            <td>{{$data->payment_recevied_date}}</td>
        </tr>
        <tr>
            <td>Total Amount</td>
            <td>{{!empty($data->amount)?$data->amount:0}}</td>
        </tr>

    @else
        <tr>
            <td colspan="10">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>