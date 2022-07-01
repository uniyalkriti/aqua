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
      <th>User Name</th>
      <th>Product Name</th>
      <th>Landing Cost</th>
      <th>Pcs Cost</th>
      <th>Stock Qty(pieces)</th>
      <th>Stock QTY(cases)</th>
      <th>Total Amount</th>
      <th>Mfg Date</th>
      <th>Exp Date</th>
      <th>Submit Date</th>
      {{--<th>Server Date Time</th>--}}
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
                <td>{{$record->user_name}}</td>
                <td>{{$record->product_name}}</td>
                <td>{{$a=$record->mrp}}</td>
                <td>{{$b=$record->pcs_mrp}}</td>
                <td>{{$aa=$record->stock_qty}}</td>
                <td>{{$bb=$record->cases}}</td>
                <td>{{($a*$aa)+($b*$bb)}}</td>
                <td>{{!empty($record->mfg_date) && $record->mfg_date!='0000-00-00'?date('d-M-Y',strtotime($record->mfg_date)):'NA'}}</td>
                <td>{{!empty($record->exp_date) && $record->exp_date!='0000-00-00'?date('d-M-Y',strtotime($record->exp_date)):'NA'}}</td>
                <td>{{!empty($record->submit_date_time)?date('d-M-Y',strtotime($record->submit_date_time)):'NA'}}</td>
{{--                <td>{{!empty($record->server_date_time)?date('d-M-Y',strtotime($record->server_date_time)):'NA'}}</td>--}}
              

                {{-- <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td> --}}
                {{-- <td>{{$record->arrivalTime}}</td>
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
                {{-- <td>{{!empty($record->gst_registrtion_date)?date('d-M-Y',strtotime($record->gst_registrtion_date)):'NA'}}</td> --}}
              
                {{-- <td>{{$record->geo_address}}</td>
                <td>{{$record->order_id}}</td>
               
                <td>{{$record->user_id}}</td> --}}

                 

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