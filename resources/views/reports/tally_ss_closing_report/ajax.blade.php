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
        <th>S.No.</th>
        <th>State</th>
        <th>SS Name</th>
        <th>SS Code.</th>
        <th>Product Code</th>
        <th>Product Name</th>
        <th>Opening (Case)</th>
        <th>Opening (PCS)</th>
        <th>Inward (Case)</th>
        <th>Inward (PCS)</th>
        <th>Outward (Case)</th>
        <th>Outward (PCS)</th>
        <th>Closing (Case)</th>
        <th>Closing (PCS)</th>
        <th>Current Rate(Case)</th>
        <th>Current Rate(PCS)</th>
        <th>Closing Value</th>
        <th>GST%</th>
        <th>GST Amt.</th>
        <th>Grand Total</th>
        
    </tr>
    <tbody>

    <?php 
        $gtotal=array();
        $i=1;
        $closing_value = 0;
        $gst = 0;
        $gstvalue = 0;
        $gtamt = 0;
        $total_opening = 0;
        $total_opening_pcs = 0;
        $total_inward = 0;
        $total_inward_pcs = 0;
        $total_outward = 0;
        $total_outward_pcs = 0;
        $total_closing = 0;
        $total_closing_pcs = 0;
        $total_rate = 0;
        $total_rate_pcs = 0;
        $total_closing_value = 0;
        $total_gst_rate = 0;
        $total_gst = 0;
        $grandtotal = 0;
    ?>

    @if(!empty($records))
        @foreach($records as $k=> $r)
            @if($details[$r->id]['opening']!=0 || $details[$r->id]['inward']!=0 || $details[$r->id]['outward']!=0 || $details[$r->id]['opening_pcs']!=0 || $details[$r->id]['inward_pcs']!=0 || $details[$r->id]['outward_pcs']!=0)
                @php
                  $closing_value = ($details[$r->id]['closing']*$details[$r->id]['rate'])+($details[$r->id]['closing_pcs']*$details[$r->id]['rate_pcs']);
                  $gst = $closing_value*$details[$r->id]['gst_rate'];
                  $gstvalue=  $gst/100;
                  $gtamt= $closing_value+$gstvalue;
                  $total_opening +=$details[$r->id]['opening']; 
                  $total_opening_pcs +=$details[$r->id]['opening_pcs']; 
                  $total_inward +=$details[$r->id]['inward']; 
                  $total_inward_pcs +=$details[$r->id]['inward_pcs']; 
                  $total_outward +=$details[$r->id]['outward']; 
                  $total_outward_pcs +=$details[$r->id]['outward_pcs']; 
                  $total_closing +=$details[$r->id]['closing']; 
                  $total_closing_pcs +=$details[$r->id]['closing_pcs']; 
                  $total_rate +=$details[$r->id]['rate']; 
                  $total_rate_pcs +=$details[$r->id]['rate_pcs']; 
                  $total_closing_value +=($details[$r->id]['closing']*$details[$r->id]['rate'])+($details[$r->id]['closing_pcs']*$details[$r->id]['rate_pcs']); 
                  $total_gst_rate +=$details[$r->id]['gst_rate']; 
                  $total_gst +=$closing_value*$details[$r->id]['gst_rate']; 
                  $grandtotal +=$closing_value+$gstvalue; 
                @endphp
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$r->l3_name}}</td>
                    <td>{{$r->csa_name}}</td>
                    <td>{{$r->csa_code}}</td>
                    <td>{{$r->itemcode}}</td>
                    <td>{{$r->pname}}</td>
                    <td>{{$details[$r->id]['opening']}}</td>
                    <td>{{$details[$r->id]['opening_pcs']}}</td>
                    <td>{{$details[$r->id]['inward']}}</td>
                    <td>{{$details[$r->id]['inward_pcs']}}</td>
                    <td>{{$details[$r->id]['outward']}}</td>
                    <td>{{$details[$r->id]['outward_pcs']}}</td>
                    <td>{{$details[$r->id]['closing']}}</td>
                    <td>{{$details[$r->id]['closing_pcs']}}</td>
                    <td>{{$details[$r->id]['rate']}}</td>
                    <td>{{$details[$r->id]['rate_pcs']}}</td>
                    <td>{{$closing_value}}</td>
                    <td>{{$details[$r->id]['gst_rate']}}</td>
                    <td>{{$gst}}</td>
                    <td>{{$gtamt}}</td>
                    </tr>
                    <?php $i++; ?>
            @endif
        @endforeach 
            <tr>
                <th colspan="6">Grand Total</th>
                <th>{{$total_opening}}</th>
                <th>{{$total_opening_pcs}}</th>
                <th>{{$total_inward}}</th>
                <th>{{$total_inward_pcs}}</th>
                <th>{{$total_outward}}</th>
                <th>{{$total_outward_pcs}}</th>
                <th>{{$total_closing}}</th>
                <th>{{$total_closing_pcs}}</th>
                <th>{{$total_rate}}</th>
                <th>{{$total_rate_pcs}}</th>
                <th>{{$total_closing_value}}</th>
                <th>{{$total_gst_rate}}</th>
                <th>{{$total_gst}}</th>
                <th>{{$grandtotal}}</th>
            </tr> 
        @else
               <tr>
            <td colspan="20">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
    @endif
    </tbody>
</table>