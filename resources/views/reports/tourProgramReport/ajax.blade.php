@if(!empty($datearray))
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
    <tr><td colspan="{{$dynamicCount*6+2}}"><h3>{{Lang::get('common.tour_program')}}</h3></td></tr>
    
    <tr>
        <th rowspan="2">Month :-</th> 
        <th>Name/Designation</th> 
        @foreach($userData as $uKey => $uValue)
        <th colspan="6">{{$uValue->user_name}}/{{$uValue->rolename}}</th>
        @endforeach
    </tr>
        <th>HQ/Town</th> 
    @foreach($userData as $uKey => $uValue)
    <th colspan="6">{{!empty($headQuarter[$uValue->head_quater_id])?$headQuarter[$uValue->head_quater_id]:''}}/{{!empty($headQuarter[$uValue->town_id])?$headQuarter[$uValue->town_id]:''}}</th>
    @endforeach


    <tr>
        <th>Day</th>
        <th>Date</th>
        @foreach($userData as $uKey => $uValue)
        <th>Town Name</th>
        <th>Distributor Name</th>
        <th>Distributor Contact</th>
        <th>Beat/Market Name</th>

        <th>Actual Distributor</th>
        <th>Actual Beat/Market</th>

        @endforeach
    </tr>

    <tbody>
    <?php ?>

    @if(!empty($datearray) && count($datearray)>0)
    
    @foreach($datearray as $dateKey=> $dateVal)
   
        <tr>
            <td>{{date('l',strtotime($dateVal))}}</td>
            <td>{{$dateVal}}</td>
            @foreach($userData as $uKey => $uValue)
            <td>{{!empty($finalOut[$dateVal.$uValue->user_id]['mtpTown'])?$finalOut[$dateVal.$uValue->user_id]['mtpTown']:''}}</td>
            <td>{{!empty($finalOut[$dateVal.$uValue->user_id]['mtpDealer'])?$finalOut[$dateVal.$uValue->user_id]['mtpDealer']:''}}</td>
            <td>{{!empty($finalOut[$dateVal.$uValue->user_id]['mtpDealerContact'])?$finalOut[$dateVal.$uValue->user_id]['mtpDealerContact']:''}}</td>
            <td>{{!empty($finalOut[$dateVal.$uValue->user_id]['mtpBeat'])?$finalOut[$dateVal.$uValue->user_id]['mtpBeat']:''}}</td>

            <td>{{!empty($finalSaleOut[$dateVal.$uValue->user_id]['saleDealer'])?$finalSaleOut[$dateVal.$uValue->user_id]['saleDealer']:''}}</td>
            <td>{{!empty($finalSaleOut[$dateVal.$uValue->user_id]['saleBeat'])?$finalSaleOut[$dateVal.$uValue->user_id]['saleBeat']:''}}</td>

            
            @endforeach
        </tr>
      
            
    @endforeach
    @endif
    </tbody>
</table>

    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
  