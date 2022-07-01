
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
        <td colspan="28"><h3> Managers Daily Report [{{date('Y-M-d',strtotime($yesterday))}}]</h3></td>
    </tr>
    <tr>
        <th>{{Lang::get('common.s_no')}}</th>
      
        
        <th>{{Lang::get('common.username')}}</th>
        <th>Working Status</th>
        <th>Remarks</th>
        <th>Location</th>
        <th>Reporting Timing</th>
        <th>Primary Target</th>
        <th>Secondary Target</th>


     
    </tr>
 
    <tbody>
        <?php
        // $i=0; 
        $i=1; 
        $sn = 1;
        ?>

    @if(!empty($managerDailyReporting) && count($managerDailyReporting)>0)
        @foreach($managerDailyReporting as $key=>$value)
            <?php
                $forwardKey = $key-1;
              $finalCount = COUNT($managerDailyReporting);

            ?>




           
            <tr>

              {{--   @if($i > 0)
                    @if($value->user_id == $managerDailyReporting[$forwardKey]->user_id)
                           <td>{{$i++}}</td>
                            <td></td>
                            <td>{{$value->work_status}}</td>
                            <td>{{$value->remarks}}</td>
                            <td>{{$value->attn_address}}</td>
                            <td>{{$value->time}}</td>
                            <td></td>
                            <td></td>

                    @else
                            <td>{{$i++}}</td>
                            <td>{{$value->user_name}}</td>
                            <td>{{$value->work_status}}</td>
                            <td>{{$value->remarks}}</td>
                            <td>{{$value->attn_address}}</td>
                            <td>{{$value->time}}</td>
                            <td></td>
                            <td></td>                            
                    @endif



                @else

                @endif --}}



                <td>{{$i++}}</td>
                <td>{{$value->user_name}}</td>
                <td>{{$value->work_status}}</td>
                <td>{{$value->remarks}}</td>
                <td>{{$value->attn_address}}</td>
                <td>{{$value->time}}</td>
                <td>{{$value->primary_target}}</td>
                <td>{{$value->secondary_target}}</td>

               
            </tr>

           


        @endforeach
     
  
    @endif
    </tbody>
</table>

