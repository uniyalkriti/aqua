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
        <th>Brand Product</th>
        <th>Pack Size</th>
        <th>Product Purchased From</th>
        <th>Product Purchased From Town</th>
        <th>Product Purchased From District</th>
        <th>Product Purchased From State</th>
        <th>Product Purchased From Phone No</th>
        <th>Product Purchased From Fax</th>
        <th>Product Purchased From Email</th>
        <th>Other Town Estimated Sales</th>
        <th>Manufacture Detail</th>
        <th>Manufacture Town</th>
        <th>Manufacture District</th>
        <th>Manufacture State</th>
        <th>Manufacture Godown Phone</th>
        <th>Manufacture Godown Mobile</th>
        <th>Manufacture Godown Fax</th>
        <th>Manufacture Godown Email</th>
        <th>Manufacture Godown Office Phone</th>
        <th>Manufacture Godown Office Mobile</th>
        <th>Manufacture Godown Office Fax</th>
        <th>Manufacture Godown Office Email</th>
        <th>Manufacture Godown Residence Phone</th>
        <th>manufacture Godown Residence Mobile</th>
        <th>Manufacture Godown Residence Fax</th>
        <th>Manufacture Godown Residence Email</th> 
        <th>Detail Of Stockiest</th>
        <th>Stockiest Town</th>
        <th>Stockiest District</th>
        <th>Stockiest State</th> 
        <th>Stockiest Godown Phone</th>
        <th>Stockiest Godown Mobile</th>
        <th>Stockiest Godown Fax</th>
        <th>Stockiest Godown Email</th> 
        <th>Stockiest Godown Office Phone</th>
        <th>Stockiest Godown Office Mobile</th>
        <th>Stockiest Godown Office Fax</th>
        <th>Stockiest Godown Office Email</th> 
        <th>Stockiest Godown Residence Phone</th>
        <th>Stockiest Godown Residence Mobile</th>
        <th>Stockiest Godown Residence Max</th> 
        <th>Stockiest Godown Residence Email</th>
        <th>Estimated Monthly Turnover</th>
        <th>Any Other Comment</th> 
        <th>Order Id</th>
        <th>Date Time</th>
        
      


    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $record)
        
            <tr>
                <td>{{$record->id}}</td>
                <td>{{$record->brand_product}}</td>
                {{-- <td>{{!empty($record->suggested_start_date)?date('d-M-Y',strtotime($record->suggested_start_date)):'NA'}}</td> --}}
                <td>{{$record->pack_size}}</td>
                <td>{{$record->product_purchased_from}}</td>
                <td>{{$record->product_purchased_from_town}}</td>
                <td>{{$record->product_purchased_from_district}}</td>
                <td>{{$record->product_purchased_from_state}}</td>
                <td>{{$record->product_purchased_from_phone_no}}</td>
                <td>{{$record->product_purchased_from_fax}}</td>
                <td>{{$record->product_purchased_from_email}}</td>
                <td>{{$record->other_town_estimated_sales}}</td>
                <td>{{$record->manufacture_detail	}}</td>
                <td>{{$record->manufacture_town}}</td>
                <td>{{$record->manufacture_district}}</td>
                <td>{{$record->manufacture_state}}</td>
                <td>{{$record->manufacture_godown_phone}}</td>
                <td>{{$record->manufacture_godown_mobile}}</td>
                {{-- <td>{{!empty($record->gst_registrtion_date)?date('d-M-Y',strtotime($record->gst_registrtion_date)):'NA'}}</td> --}}
              
                <td>{{$record->manufacture_godown_fax}}</td>
                <td>{{$record->manufacture_godown_email}}</td>
                <td>{{$record->manufacture_godown_office_phone}}</td>
                <td>{{$record->manufacture_godown_office_mobile}}</td>
                <td>{{$record->manufacture_godown_office_fax}}</td>
                <td>{{$record->manufacture_godown_office_email}}</td>
                <td>{{$record->manufacture_godown_residence_phone}}</td>
                <td>{{$record->manufacture_godown_residence_mobile}}</td>
                <td>{{$record->manufacture_godown_residence_fax}}</td>
                <td>{{$record->manufacture_godown_residence_email}}</td>
                <td>{{$record->detail_of_stockiest	}}</td>
                <td>{{$record->stockiest_town}}</td>
                <td>{{$record->stockiest_district}}</td>
                <td>{{$record->stockiest_state}}</td>

                <td>{{$record->stockiest_godown_phone}}</td>
                <td>{{$record->stockiest_godown_mobile}}</td>

                <td>{{$record->stockiest_godown_fax}}</td>
                <td>{{$record->stockiest_godown_email}}</td>
                <td>{{$record->stockiest_godown_office_phone}}</td>
                <td>{{$record->stockiest_godown_office_mobile}}</td>

                <td>{{$record->stockiest_godown_office_fax	}}</td>
                <td>{{$record->stockiest_godown_office_email}}</td>
                <td>{{$record->stockiest_godown_residence_phone}}</td>
                <td>{{$record->stockiest_godown_residence_mobile}}</td>
                <td>{{$record->stockiest_godown_residence_fax}}</td>
                <td>{{$record->stockiest_godown_residence_email}}</td>
                <td>{{$record->estimated_monthly_turnover}}</td>
                <td>{{$record->any_other_comment}}</td>
                <td>{{$record->order_id}}</td>
                <td>{{$record->date_time}}</td>
               
             

                {{-- <td>{{!empty($record->cur_date_time)?date('d-M-Y',strtotime($record->cur_date_time)):'NA'}}</td> --}}

            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="50">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>