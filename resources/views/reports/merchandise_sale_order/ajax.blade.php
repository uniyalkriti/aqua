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
    <tr><td colspan="16"><h3>Merchandise Report</h3></td></tr>
    <tr class="info" style="color: black;">
        <th>S.No.</th>
         <th>Date</th>
        <th>State</th>
        <th>Town</th>
          <th>Distributor</th>
        <th>Emp. Code</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Reporting Senior</th>
       
      
        <th>Retailer</th>
        <th>Merchandise </th>
        <th>Qty </th>
        <th>Time</th>
        <th>GPS coordinates of picture</th>
       
    </tr>
    <tbody>
    <?php $i = 1; $gtotal=0; $gqty=0;?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
            <?php    
                $encid = Crypt::encryptString($data->user_id);
                $dencid = Crypt::encryptString($data->dealer_id);
                $rencid = Crypt::encryptString($data->retailer_id);
            ?>
                <tr  class="">
                    <td>{{$i}}</td>
                    <td>{{$data->date}}</td>
                    <td>{{$data->l3_name}}</td>
                    <td>{{$data->l4_name}}</td>
                    <td><a href="{{'distributor/'.$dencid}}"> {{$data->dealername}} </a></td> 
                    <td>{{$data->emp_code}}</td>
                    <td><a href="{{'user/'.$encid}}">{{$data->user_name}}</a></td>

                    <td>{{$data->rolename}}</td> 

                    <td>{{$data->seniorname}}</td> 


                    <td><a href="{{'retailer/'.$rencid}}">{{$data->retailername}}</a></td>
                    <td>{{$data->merchandise_name}}</td>
                    <td>{{$data->qty}}</td>
                    <td>{{$data->time}}</td>
                    <td>{{$data->lat.','.$data->lng }}</td>
                </tr>
                <?php $i++; 
                $gqty+=$data->qty;
              //  $gtotal+=$data->total_sale_value;?>
            @endforeach
            <tr>
            <th colspan="11">Grand Total</th>
             <th>{{$gqty}}</th>
             <th></th>
              <th></th>
           {{-- <th>{{$gtotal}}</th> --}}
        </tr>
    @endif

    </tbody>
</table>