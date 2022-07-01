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
    #simple-table th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
    <thead>
    <tr>
        <th>S.No.</th>
        <th>Complaint Product</th>
        <th>Nature Of Complaint Mentioned</th>
        <th>Quantity Lying</th>
        <th>Complaint With Retailer</th>
        <th>Cases With Complaint</th>
        <th>Cases Rv</th>
        <th>Packers Slip</th>
        <th>Bill No</th>
        <th>Date</th>
        <th>Amount Of Bill</th>
        <th>Product Dispatched</th>
        <th>Manufacturing Unit</th>
        <th>sampleClosed</th>
        <th>Concerned Super Distributor Address</th>
        <th>Concerned Retailer Address</th>
        <th>Concerned Consumer Address</th>
        <th>Action Taken</th>
        <th>Comments</th>
        <th>Date Time</th>
        <th>Created At</th>
  
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$record)
        
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$record->complaint_product}}</td>
                {{-- <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td> --}}
                <td>{{$record->natureOfComplaintMentioned}}</td>
                <td>{{$record->quantityLying	}}</td>
                <td>{{$record->complaintWithRetailer}}</td>
                <td>{{$record->casesWithComplaint}}</td>
                <td>{{$record->casesRv}}</td>
                <td>{{$record->packersSlip	}}</td>
                <td>{{$record->billNo}}</td>
                <td>{{$record->date}}</td>
                <td>{{$record->amountOfBill}}</td>
                <td>{{$record->productDispatched	}}</td>
                <td>{{$record->manufacturingUnit}}</td>
                <td>{{$record->sampleClosed}}</td>
                <td>{{$record->concernedSuperDistributorAddress}}</td>
                <td>{{$record->concernedRetailerAddress}}</td>
                <td>{{$record->concernedConsumerAddress}}</td>
                {{-- <td>{{!empty($record->gst_registrtion_date)?date('d-M-Y',strtotime($record->gst_registrtion_date)):'NA'}}</td> --}}
              
                <td>{{$record->actionTaken}}</td>
                <td>{{$record->comments}}</td>
               
                <td>{{$record->date_time}}</td>

                 <td>{{!empty($record->created_at)?date('d-M-Y',strtotime($record->created_at)):'NA'}}</td>

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