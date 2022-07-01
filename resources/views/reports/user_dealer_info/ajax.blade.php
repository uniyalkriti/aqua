@if(!empty($dealerInfo))
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
    <tr><td colspan="23"><h3>Dealer User report List</h3></td></tr>
    <tr class="info" style="color: black;">
        <th>S.No.</th>
        <th>Dealer Name</th>
        <th>User Name</th>
        <th>Role</th>
        <th>Mobile</th>
        <th>State</th>
        <th>Number Of Beat</th>
        <th>Number Of Retailer</th>
    </tr>
    <tbody>
        <?php $i=1;  ?>
        @if(!empty($dealerInfo) && count($dealerInfo)>0)
            @foreach($dealerInfo as $key=>$data)
             <?php $encid = Crypt::encryptString($data->user_id);?>
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$data->dealer_name}}</td>
                    <td>{{$data->user_name}}</td>
                    <td>{{$data->role_name}}</td>
                    <td>{{$data->mobile}}</td>
                    <td>{{$data->l2_name}}</td>
                    <td>{{$data->beat_count}}</td>
                    <td>{{$data->retalier_count}}</td>
                   </tr>
            @endforeach
        @endif
    </tbody>
</table>