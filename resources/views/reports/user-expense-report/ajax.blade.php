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
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr><td colspan="20"><h3>{{Lang::get('common.expense_report')}}</h3></td></tr>
    <tr class="info" style="color: black;">
        <th>S.No.</th>
        <th>Date</th>
        <th>State</th>
        <th>User Name</th>
        <th>Designation</th>
        <th>Traveling Mode</th>
        <th>Start Journey</th>
        <th>End Journey</th>
        <th>Total Calls</th>
        <th>Traveling Allowance</th>
        <th>Dearness Allowance</th>
        <th>Staying Rent</th>
        <th>Other Expense</th>
        <th>Total Expense</th>
        <th>Image 1</th>
        <th>Image 2</th>
        <th>Image 3</th>
        <th>Remarks</th>
    </tr>
    <tbody>
    <?php $i=0; ?>
    @if(!empty($expense_query) && count($expense_query)>0)
        @foreach($expense_query as $key=>$data)
               <?php 
                $proper_date = date("d-m-Y",strtotime($data->submit_date));
               ?>
                <tr  class="">
                    <td>{{$i}}</td>
                    <td>{{$proper_date}}</td>
                    <td>{{$data-> state_name }}</td>
                    <td>{{$data->user_name }}</td>
                    <td>{{$data->rolename }}</td>
                    <td>{{$data->travelling_mode }}</td>
                    <td>{{$data->start_journey }}</td>
                    <td>{{$data->end_journey }}</td>
                    <td>{{$data->total_calls }}</td>
                    <td>{{$data->travelling_allowance }}</td>
                    <td>{{$data->drawing_allowance }}</td>
                    <td>{{$data->rent }}</td>
                    <td>{{$data->other_expense }}</td>
                    <td>{{$data->travelling_allowance+$data->travelling_allowance+$data->drawing_allowance+$data->rent+$data-> other_expense }}
                    </td>
                    <td>{{$data->image_name1}}</td>
                    <td>{{$data->image_name2}}</td>
                    <td>{{$data->image_name3}}</td>
                    <td>{{$data->remarks}}</td>

                </tr>
                <?php $i++; ?>
            @endforeach
    @endif

    </tbody>
</table>