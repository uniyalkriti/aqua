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
<tr><td colspan="15"><h3>Payment Collection</h3></td></tr>
<tr>
<th>S.No.</th>
<th>zone Name</th>
<th>User name</th>
<th>Emp Id</th>
<th>Region</th>
<th>User Designation</th>
<th>Dealer Name</th>
<th>Town</th>
<th>Payment Mode</th>
<th>Invoice</th>
<th>Payment Recevied Date</th>
<th>Amount</th>
<th>Drawn from Bank</th>
<th>Deposited Bank</th>
<th>Deposited Date</th>


</tr>
<tbody>
@if(!empty($records) && count($records)>0)
<?php $i=1 ?>
@foreach($records as $record)
       <?php 
        $user_id = Crypt::encryptString($record->user_id); 
        $dealer_id = Crypt::encryptString($record->did); 
        ?>

<tr>
<td>{{$i++}}</td>
<td>{{$record->zone}}</td>
<td><a href="{{url('user/'.$user_id)}}">{{$record->user_name}}</a></td>
<td>{{$record->emp_code}}</td>
<td>{{$record->region}}</td>
<td>{{$record->user_designation}}</td>
<td><a href="{{url('distributor/'.$dealer_id)}}">{{$record->dealer_name}}</a></td>
<td>{{$record->town_name}}</td>
<td>
<?php
$arr=[1=>'Cash',2=>'Cheque',3=>'NEFT/RGTDS',4=>'Demand Draft'];
?>
{{!empty($arr[$record->payment_mode])?$arr[$record->payment_mode]:'N/A'}}</td>
<td>{{$record->invoice_number}}</td>
<td>{{!empty($record->payment_recevied_date)?date('d-M-Y',strtotime($record->payment_recevied_date)):'NA'}}</td>
<td>{{$record->amount}}</td>
<td>{{!empty($record->drawn_from_bank)?$record->drawn_from_bank:'NA'}}</td>
<td>{{!empty($record->deposited_bank)?$record->deposited_bank:'NA'}}</td>
<td>{{!empty($record->deposited_date)?date('d-M-Y',strtotime($record->deposited_date)):'NA'}}</td>

</tr>
@endforeach
@else
<tr>
<td colspan="18">
<p class="alert alert-danger">No data found</p>
</td>
</tr>
@endif
</tbody>
</table>
