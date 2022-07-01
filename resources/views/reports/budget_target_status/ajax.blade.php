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
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>

<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">

  
    <tr>
    <td colspan="{{count($datearray)+4}}"><h3>Tablet Working Status Report {{date('d-m-Y',strtotime($year))}} TO {{date('t-m-Y',strtotime($year))}}</h3>
    </td>
    </tr>
    <tr>
   
    <th>ZONE</th>
    <th>STATE</th>
    <th>Particulars</th>
<?php
 $start_date =date('Y-m-d',strtotime($year));
 $end_date= date("Y-m-t", strtotime($start_date));
$startTime = strtotime($start_date);
$endTime = strtotime($end_date);


while ($startTime <= $endTime) {
           
   ?>
   <th><?php echo date("d-m-Y",$startTime);?></th>
  <?php       

$startTime = $startTime+86400;
}

 ?>
 
</tr>
      
    <tbody>
    <?php
     $i = 1; 
     $grand_total = array();

    ?>
   <tr>
    @if(!empty($records))

    @foreach($region as $rkey => $rvalue)
    <tr>
                <td rowspan="11">{{$rvalue->region_name}}</td>
                <td rowspan="11">{{$rvalue->state_name}}</td>
    </tr>
        @foreach($particularsnameData as $key=>$data)
    <tr>
                <td>{{$data->name}}</td>
                
                @foreach($datearray as $datekey=>$date) 
            
                @php
                
                $date_state_key=$rvalue->state_id.$date;
                
                 $grand_total[$date][$data->id][] = !empty($details[$data->id][$date_state_key])?$details[$data->id][$date_state_key]:'0'; 
                @endphp
                <td>
                {{ !empty($details[$data->id][$date_state_key])?$details[$data->id][$date_state_key]:'0' }}
                </td>
                @endforeach 
        </tr>
             </tr>
            <?php $i++; ?>   
          @endforeach 
    @endforeach 

          <tr>
          <th rowspan="12" colspan="2">Grand total</th>
         
    </tr>
        @foreach($particularsnameData as $key=>$value)
    <tr>
        <td>{{$value->name}}</td>
         @foreach($datearray as $datekey=>$date)
         <td>{{array_sum($grand_total[$date][$value->id])}}</td>
          @endforeach
    </tr>      
         @endforeach
           

          
          
    @endif
    </tbody>
</table>