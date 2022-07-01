@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
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
    <tr>
        <td colspan="20"><h3> SS Stock List </h3></td>
    </tr>
    <tr>
        <th rowspan="2">S.No.</th>
        <th rowspan="2">Date</th>
        <th rowspan="2">Invoice No.</th>
        <th rowspan="2">Prev. Invoice No.</th>
        <th rowspan="2">Invoice Status</th>
        <th rowspan="2">State</th>
        <th rowspan="2">Town</th>
        <th rowspan="2">SS Code</th>
        <th rowspan="2">SS Name</th>
        <th rowspan="2">Distributor Code</th>
        <th rowspan="2">Distributor Name</th>
        <th colspan="7">Item Details</th>
        <tr>
            <th style="width:200px;">Product Name</th> 
            <th style="width:80px;">Quantity</th>
            <th style="width:80px;">Rate</th>
            <th style="width:80px;">Total</th>
            <th style="width:80px;">Gst%</th>
            <th style="width:80px;">Gst Amt.</th>
            <th style="width:80px;">Grand Total</th>
        </tr>
    </tr>
    <tbody>

    <?php 
        $gtotal=array();
        $i=1;
    ?>

    @if(!empty($records))
    
    @foreach($records as $k=> $r)

          @php
           if($r->invoice_type==1){
                $inv_status="Invoice";
              }elseif($r->invoice_type==0){
                $inv_status="Cancel";
              }elseif($r->invoice_type==2){
                $inv_status="Credit Note";
              }elseif($r->invoice_type==3){
                $inv_status="Cancel Credit Note";
              }elseif($r->invoice_type==4){
                $inv_status="Purchase";
              }elseif($r->invoice_type==5){
                $inv_status="Cancel Purchase";
              }elseif($r->invoice_type==6){
                $inv_status="Debit Note";
              }elseif($r->invoice_type==7){
                $inv_status="Cancel Debit Note";
              }
           @endphp
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->ch_date}}</td>
            <td>{{$r->ch_no}}</td>
            <td>{{$r->first_ch_no}}</td>
            <td>{{$inv_status}}</td>
            <td>{{$r->state_name}}</td>
            <td>{{$r->town_name}}</td>
            <td>{{$r->csa_code}}</td>
            <td>{{$r->csa_name}}</td>
            <td>{{$r->dealer_code}}</td>
            <td>{{$r->dname}}</td>
           <td colspan="7">
           <table class="table table-bordered">
           <thead> 

         @foreach($details[$r->id] as $Dkey => $Dvalue)
           <tr>
            <td style="width:200px;" >{{$Dvalue->pname}}</td>
            <td style="width:80px;">{{$Dvalue->qty}}</td>
            <td style="width:80px;">{{$Dvalue->rate}}</td>
            <td style="width:80px;">{{$Dvalue->rate*$Dvalue->qty}}</td>
            <td style="width:80px;">{{$Dvalue->gst}}</td>
            <td style="width:80px;">{{$Dvalue->gst_amt}}</td>
            <td style="width:80px;">{{$Dvalue->qty+$Dvalue->rate+($Dvalue->rate*$Dvalue->qty)+$Dvalue->gst+$Dvalue->gst_amt}}</td>
           </tr>
         @endforeach
         </thead>
         </table>
           </td>
        
        
   
         
        </tr>
        <?php $i++; ?>
            @endforeach  

            @else
               <tr>
            <td colspan="20">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
        @endif
    </tbody>
</table>