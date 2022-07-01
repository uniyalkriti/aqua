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
        <td colspan="15"><h3>User Meeting Order Report</h3></td>
    </tr>
    <tr>
        <th>S.No.</th>
        <th>Date</th>
        <th>User Name</th>
        <th>User Contact</th>
        <th>State</th>

        <th>Meeting With</th>
        <th>Contact No.</th>
        <th>Meeting Address</th>
        <th>Meet Name</th>
        <th>Meeting Type</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Remarks</th>
        <th>Follow Up Date</th>
        <th>Follow Up Time</th>

      
    </tr>
    <tbody>
    <?php $gtotal=0; $gqty=0; $i=1;?>

    @if(!empty($records) && count($records)>0)
    
    @foreach($records as $k=> $r)
        <?php    
            $encid = Crypt::encryptString($r->user_id);
          
         ?>
   
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->current_date_m}}</td>
            <td><a href="{{'user/'.$encid}}"> {{$r->user_name}}</a></td>
            <td>{{$r->mobile}}</td>

            <td>{{$r->state}}</td>
            <td>{{$r->meeting_with}}</td>
            <td>{{$r->contact_no}}</td>
            <td>{{$r->meet_address}}</td>
            <td>{{$r->meet_name}}</td>
            <td>{{!empty($meeting_type[$r->type_of_meet])?$meeting_type[$r->type_of_meet]:'NA'}}</td>
            <td>{{$r->time_in}}</td>
            <td>{{$r->time_out}}</td>
            <td>{{$r->meeting_remark}}</td>
            <td>{{$r->followup_date}}</td>
            <td>{{$r->followup_time}}</td>
         
                
               
  
                <?php  $i++; $total=0;
                $totalqty=0; ?>
            
               
               
           
             
            </tr>
           
           
            @endforeach  

       
         
        @endif
    </tbody>
</table>