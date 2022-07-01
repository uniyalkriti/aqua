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
    <tr>
      <th>S.No.</th>
      <th>Dealer Name</th>
      {{-- <th>zone Name</th> --}}
      <th>User name</th>
      <th>User Designation</th>
      <th>user Hq</th>
      <th>Town</th>
      <th>Payment_mode</th>
      {{--<th>Accout Number</th>--}}
      <th>Amount</th>
      {{--<th>Drawn From Bank</th>--}}
      {{--<th>Deposited Bank</th>--}}
      <th>Payment Recevied Date</th>
      <th>Deposited Date</th>
      

    </tr>
  </thead>
    <tbody>
      @if(!empty($records) && count($records)>0)
      <?php $i=1 ?>
      @foreach($records as $record)
        
        <tr>
          <td>{{$i++}}</td>
          <td>{{$record->dealer_name}}</td>
          {{-- <td>{{$record->zone_name}}</td> --}}
          <td>{{$record->user_name}}</td>
          <td>{{$record->user_designation}}</td>
          <td>{{$record->region_name	}}</td>
          <td>{{$record->town_name}}</td>
          <td>
            <?php
                  $arr=[1=>'Cash',2=>'Cheque',3=>'NEFT/RGTDS',4=>'Demand Draft'];
              ?>
            {{!empty($arr[$record->payment_mode])?$arr[$record->payment_mode]:'N/A'}}</td>
         
          {{--<td>{{$record->accout_number}}</td>--}}
          <td>{{$record->amount}}</td>
{{--          <td>{{$record->drawn_from_bank}}</td>--}}
          {{--<td>{{$record->deposited_bank}}</td>--}}
          <td>{{!empty($record->payment_recevied_date)?date('d-M-Y',strtotime($record->payment_recevied_date)):'NA'}}</td>
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