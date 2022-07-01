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
    <thead>
    <tr class="info" style="color: black;">
        <th>S.No.</th>
        <th>Zone</th>
        <th>Region</th>
        <th>Emp. Code</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Date</th>
        <th>Attendance Status</th>
        <th>Status</th>
        <th>Attendance In Time</th>
        <th>As Per Tour Program Town</th>
        <th>Attendance Location</th>
        <th>Attendance Out Time</th>
        <th>Attendance Out Location</th>
        <th>Working Hours</th>
        <th>Productive Hours</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 1;?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
            @foreach($users as $uk=>$u)
                {{--{{dd($data['2'])}}--}}
                <tr style="background: {{ !empty($data[$u['person_id']]->work_date)?'#81FF5B':'#FFA5A5' }}"  class="">
                    <td>{{$i}}</td>
                    <td>{{$u['zone']}}</td>
                    <td>{{$u['region']}}</td>
                    <td>{{$u['emp_code']}}</td>
                    <td>{{$u['uname']}}</td>
                    <td>{{$u['role']}}</td>
                    <td>{{!empty($key)?date('d-M-Y',strtotime($key)):'N/A'}}</td>
                    <td><?php
                            if (!empty($data[$u['person_id']]->work_date))
                                {
                        if ($data[$u['person_id']]->work_date == 0) {
                            echo "Absent";
                        } elseif (strtotime(date('H:i:s', strtotime($data[$u['person_id']]->work_date))) > strtotime('10:30:00')) {
                            echo "Half Day";
                        } else {
                            echo "Present";
                        }
                        }
                        ?>
                    </td>
                    <td>{{!empty($data[$u['person_id']]->work)?$data[$u['person_id']]->work:'N/A'}}</td>
                    <td>{{ !empty($data[$u['person_id']]->work_date)?$data[$u['person_id']]->work_date:'N/A' }}</td>
                    <td>{{!empty($data[$u['person_id']]->mtp_towm)?$data[$u['person_id']]->mtp_towm:'N/A'}}</td>
                    <td>{{!empty($data[$u['person_id']]->track_addrs)?$data[$u['person_id']]->track_addrs:'N/A'}}</td>
                    <td>{{ !empty($data[$u['person_id']]->check_out_date)?date('d-M-Y H:i:s',strtotime($data[$u['person_id']]->check_out_date)):'00:00:00' }}</td>
                    <td>{{!empty($data[$u['person_id']]->check_out_address)?$data[$u['person_id']]->check_out_address:'NA'}}</td>
                    <td>
                        @if (!empty($data[$u['person_id']]->work_date) && !empty($data[$u['person_id']]->check_out_date))
                            @php
                                $c11 = new DateTime($data[$u['person_id']]->work_date);
                                $c21 = new DateTime($data[$u['person_id']]->check_out_date);
                                $interval1 = $c11->diff($c21);
                                echo $interval1->format('%h') . " Hours " . $interval1->format('%i'). " Minutes";
                            @endphp
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @if (!empty($data[$u['person_id']]->first_call) && !empty($data[$u['person_id']]->last_call))
                            @php
                                $c1 = new DateTime($data[$u['person_id']]->first_call);
                                $c2 = new DateTime($data[$u['person_id']]->last_call);
                                $interval = $c1->diff($c2);
                                echo $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
                            @endphp
                        @else
                            0
                        @endif
                    </td>
                </tr>
                <?php $i++?>
            @endforeach
        @endforeach
    @endif

    </tbody>
</table>