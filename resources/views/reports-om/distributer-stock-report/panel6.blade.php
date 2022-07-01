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
      <th>Order Id</th>
      <th>Dealer Name</th>
      <th>Created Date</th>
      <th>User Name</th>
      <th>Sale Date</th>
      <th>Receive Date</th>
      <th>Date Time</th>
      {{--<th>Company Id</th>--}}
     
     
    </tr>
  </thead>
    <tbody>
      @if(!empty($records) && count($records)>0)
      <?php $i=1 ?>
      @foreach($records as $record)
     
        <tr>
          <td>{{$i++}}</td>
          <td>{{$record->order_id}}</td>
          <td>{{$record->dealer_name}}</td>
         
          <td>{{!empty($record->created_date)?date('d-M-Y',strtotime($record->created_date)):'NA'}}</td>
          <td>{{$record->user_name}}</td>
          <td>{{!empty($record->sale_date)?date('d-M-Y',strtotime($record->sale_date)):'NA'}}</td>
          <td>{{!empty($record->receive_date)?date('d-M-Y',strtotime($record->receive_date)):'NA'}}</td>
          <td>{{!empty($record->date_time)?date('d-M-Y',strtotime($record->date_time)):'NA'}}</td>
         
          {{--<td>{{$record->company_id}}</td>--}}
        </tr>
      @endforeach
      @else
          <tr>
              <td colspan="10">
                <p class="alert alert-danger">No data found</p>
              </td>
          </tr>
      @endif
  </tbody>
</table>