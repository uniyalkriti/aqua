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
      <th>Replace Id</th>
      <th>User Name</th>
      <th>{{Lang::get('common.dealer_module')}}</th>
      <th>Product</th>
      <th>Retailer</th>
      <th>Product Qty</th>
      <th>Product value</th>
      <th>Date Time</th>
      {{--<th>Location</th>--}}
      {{--<th>Reason</th>--}}
      <th>Mrp</th>
      <th>Task</th>
      <th>Extra Amount</th>
    </tr>
  </thead>
    <tbody>
      @if(!empty($records) && count($records)>0)
      <?php $i=1 ?>
      @foreach($records as $record)
        
        <tr>
          <td>{{$i++}}</td>
          <td>{{$record->replaceid}}</td>
          <td>{{$record->user_name}}</td>
          <td>{{$record->dealer_name}}</td>
          <td>{{$record->product_name}}</td>
          <td>{{$record->rname}}</td>
          <td>{{$record->prod_qty}}</td>
          <td>{{$record->prod_value}}</td>
          <td>{{!empty($record->date_time)?date('d-M-Y',strtotime($record->date_time)):'NA'}}</td>
          {{--<td>{{$record->location}}</td>--}}
          {{--<td>{{$record->reason}}</td>--}}
          <td>{{$record->mrp}}</td>
          <td>{{$record->task}}</td>
          <td>{{$record->extra_amt}}</td>

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