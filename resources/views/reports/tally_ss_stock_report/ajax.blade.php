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
      <th class="sno">S.No</th>
      <th>Date</th>
      <th>State</th>
      <th>SS Name</th>
      <th>SS Code</th>
      <th>Product Name</th>
      <th>Opening(PCS)</th>
      <th>Inward(PCS)</th>
      <th>Outward(PCS)</th>
      <th>Closing(PCS)</th>
    </tr>
    <tbody>

    <?php 
        $gtotal=array();
        $i=1;
    ?>

    @if(!empty($records))
    @foreach($records as $k=> $r)
    
          @php
         
           @endphp
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->to_date}}</td>
            <td>{{$r->l3_name}}</td>
            <td>{{$r->csa_name}}</td>
            <td>{{$r->csa_code}}</td>
            <td>{{$r->pname}}</td>
            <td>{{$r->opening}}</td>
            <td>{{$r->inward}}</td>
            <td>{{$r->outward}}</td>
            <td>{{$r->closing}}</td>
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