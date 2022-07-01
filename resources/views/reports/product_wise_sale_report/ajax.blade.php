@if(!empty($first_part))
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
  <td colspan="{{count($datearray)+49}}">
      <h3>{{Lang::get('common.date_wise_product_wise_report').' ('}}{{date('d-m-Y',strtotime($year))}} TO {{date('t-m-Y',strtotime($year)).')'}}</h3>
  </td>
</tr>
<tr>
  <tr>
   
    <th rowspan="3">Sr.No</th>
    <th rowspan="3">ZONE</th>
    <th rowspan="3">STATE</th>
    <th rowspan="3">BELT CODE</th>
    <th rowspan="3">USER NAME</th>
    <?php
      $start_date =date('Y-m-d',strtotime($year));
      $end_date= date("Y-m-t", strtotime($start_date));
      $startTime = strtotime($start_date);
      $endTime = strtotime($end_date);
      while ($startTime <= $endTime) 
      {
               
    ?>
      <th  colspan="21">
          <?php echo date("d-m-Y",$startTime);?>
      </th>

    <?php       
        $startTime = $startTime+86400;
      
      }
    ?>
      <th  colspan="21" >
          Total
      </th>


  </tr>
   <tr>
     
      @foreach($datearray as $dkey => $dvalue)
         @foreach($catalog_data as $key => $value)
           <th colspan="3">{{$value}}</th>
        @endforeach
     @endforeach
     <!-- For Total -->
        @foreach($catalog_data as $key => $value)
           <th colspan="3">{{$value}}</th>
        @endforeach
        <!-- For Total End -->
     </tr>

      <tr>

      @foreach($datearray as $dkey => $dvalue)
        @foreach($catalog_data as $key => $value)
           <th>CALLS</th> <th>RD CASES</th> <th>RV</th>
        @endforeach
      @endforeach
      <!-- For Total -->
      @foreach($catalog_data as $key => $value)
       
             <th>CALLS</th> <th>RD CASES</th> <th>RV</th>
      @endforeach
      <!-- For Total End-->
    
      </tr>

</tr>

    <tbody>
      @php
        $Gtotal_row = array();
        $Gtotal_cases = array();
        $Gtotal_price = array();
        $sub_total_row = [];
        $sub_total_cases = [];
        $sub_total_price = [];
        $grand_sub_total_row = array();
        $grand_sub_total_cases = array();
        $grand_sub_total_price = array();
      @endphp
      @foreach($first_part as $first_key => $first_value)
      <tr>
        <td>{{$first_key+1}}</td>
        <td>{{$first_value->zone_name}}</td>
        <td>{{$first_value->state_name}}</td>
        <td>{{$first_value->region_txt}}</td>
        <td>{{$first_value->user_name}}</td>
        @foreach($datearray as $dkey => $dvalue)
          @foreach($catalog_data as $key=>$value)
          <?php
             // for below grand total all aray store data behalf of date and particular product starts
             $Gtotal_row[$dvalue][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_row'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_row']:'';

             $Gtotal_cases[$dvalue][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases']:'';

             $Gtotal_price[$dvalue][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_price'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_price']:'';
            // for below grand total all aray store data behalf of date and particular product ends 

             // for last part of table all array store data behalf of only for particular product starts here  
             $sub_total_row[$first_value->user_id][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_row'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_row']:'';

             $sub_total_cases[$first_value->user_id][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases']:'';

             $sub_total_price[$first_value->user_id][$key][] = !empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_price'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_price']:'';
             // for last part of table all array store data behalf of only for particular product ends here  

          ?>
            <td>{{!empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_row'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_row']:'0'}}</td>
            <td>{{!empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_cases']:'0'}}</td>
            <td>{{!empty($product_wise_data[$first_value->user_id.$dvalue][$key]['total_price'])?$product_wise_data[$first_value->user_id.$dvalue][$key]['total_price']:'0'}}</td>
          @endforeach
        @endforeach
        @foreach($catalog_data as $key=>$value)
        <!-- show total data on behalf of particuler product starts  -->
          @php
            $grand_sub_total_row[$key][]= array_sum($sub_total_row[$first_value->user_id][$key]);
            $grand_sub_total_cases[$key][]= array_sum($sub_total_cases[$first_value->user_id][$key]);
            $grand_sub_total_price[$key][]= array_sum($sub_total_price[$first_value->user_id][$key]);
          @endphp
          <td>{{array_sum($sub_total_row[$first_value->user_id][$key])}}</td>
          <td>{{array_sum($sub_total_cases[$first_value->user_id][$key])}}</td>
          <td>{{array_sum($sub_total_price[$first_value->user_id][$key])}}</td>
        <!-- show total data on behalf of particuler product ends  -->

        @endforeach
      </tr>
      @endforeach
    <tr>
    <th colspan="5">Grand Total</th>
    <!-- grand total starts data show on behalf of date and product key starts here  -->
        @foreach($datearray as $dkey => $dvalue)
          @foreach($catalog_data as $key=>$value)
            <th>{{array_sum($Gtotal_row[$dvalue][$key])}}</th>
            <th>{{array_sum($Gtotal_cases[$dvalue][$key])}}</th>
            <th>{{array_sum($Gtotal_price[$dvalue][$key])}}</th>
          @endforeach
        @endforeach
    <!-- grand total starts data show on behalf of date and product key ends here  -->
        @foreach($catalog_data as $key => $value)
          <th>{{array_sum($grand_sub_total_row[$key])}}</th>
          <th>{{array_sum($grand_sub_total_cases[$key])}}</th>
          <th>{{array_sum($grand_sub_total_price[$key])}}</th>
        @endforeach
    </tr>      

    </tbody>
</table>