

@if(!empty($person))
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
    <tr><td colspan="107"><h3>{{Lang::get('common.dsr_monthly')}}</h3></td></tr>

    <tr>

        <th colspan="12">Information</th>
        <th colspan="2">Norms Per Day For SR/TSI</th>

         <th rowspan="2">Employee Target In Case</th>
        <th rowspan="2">Distributor Target In Case</th>


        @foreach($catalogTwoData as $catagoryKey => $catagoryValue)
        <th colspan="{{$catagoryValue->dynamicData}}"> {{$catagoryValue->categoryName}}</th>
        @endforeach
       


        <th rowspan="2">{{Lang::get('common.total_call')}}</th>
        <th rowspan="2">{{Lang::get('common.productive_call')}}</th>

        <th rowspan="2">Lines Sold</th>


        <th rowspan="2">% Productivity</th>

      <th rowspan="2">Amount</th>
      <th rowspan="2">Amount(With Scheme)</th>
      <th rowspan="2">Distributor Value</th>
      <th rowspan="2">Emp Cases Sold</th>

      <th rowspan="2">Distributor Cases Sold</th>
      <th rowspan="2">Line Per Sales Call</th>
      <th rowspan="2">Concern Stockist Name</th>
      <th rowspan="2">Concern Stockist Contact Number</th>
      <th rowspan="2">Working With</th>
      
      <th rowspan="2">Attendance Remarks</th>




    </tr>


    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.date')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        <th>{{Lang::get('common.location7')}}</th>
       

        <th>TC</th>
        <th>PC</th>

       



      @foreach($catalog_product as $ckey=>$cdata)
        <?php  

        $null[] = 'null';

         ?>

        <th>{{$cdata->name}}<br>({{!empty($finalProductTypeOut[$cdata->final_product_type])?$finalProductTypeOut[$cdata->final_product_type]:''}})</th>
      @endforeach
    </tr>
    <tbody>
        <?php 
        $i=0; 
        $r=0; 
        $total_qty = array();
        $total_amt = array(); 
        $total_sch_amt = array(); 
        $totalSecondaryCasesSold = array();
        $amt =0; 
        $senior = App\CommonFilter::senior_name('person');


        ?>



        @if(!empty($person) && count($person)>0)
            @foreach($person as $key=>$data)
            <?php
              $date = $from_date;
              $user_id = Crypt::encryptString($data->user_id); 
              $person_id_senior = Crypt::encryptString($data->person_id_senior); 
              $forwardKey = $key+1;
              $finalCount = COUNT($person);

              $totalCall[] =  !empty($productData[$data->user_id][$date]['total_call'])?$productData[$data->user_id][$date]['total_call']:'0';
              $productiveCall[] =  !empty($productData[$data->user_id][$date]['productive_call'])?$productData[$data->user_id][$date]['productive_call']:'0';
              $totalAmt[] = !empty($productData[$data->user_id][$date]['product_amount'])?$productData[$data->user_id][$date]['product_amount']:'0';


              $totalSchAmt[] = !empty($scheme_amount[$data->user_id.$date])?$scheme_amount[$data->user_id.$date]:'0';


              $linesSold[] =  !empty($productData[$data->user_id][$date]['linesSold'])?$productData[$data->user_id][$date]['linesSold']:'0';

              $secondaryCasesSold[] =  !empty($productData[$data->user_id][$date]['secondaryCasesSold'])?$productData[$data->user_id][$date]['secondaryCasesSold']:'0';

              $attRemarks = !empty($attendanceRemarks[$data->user_id.$date])?$attendanceRemarks[$data->user_id.$date]:'';

              $attWork = !empty($attendanceWorkStatus[$data->user_id.$date])?$attendanceWorkStatus[$data->user_id.$date]:'N/A';

              $attWorkWith = !empty($attendanceWorkWith[$data->user_id.$date])?$attendanceWorkWith[$data->user_id.$date]:'N/A';







            ?>

           






                <tr>

                    <td>{{$i++}}</td>
                    <td>{{$date}}</td>
                    <td>{{$data->l3_name}}</td>
                    <!-- <td>{{$data->l3_name}}/{{$data->l5_id}}/  @if($i < $finalCount) {{  $person[$forwardKey]['l5_id'] }} @endif </td> -->
                    <td>{{$data->l4_name}}</td>
                    <td>{{$data->l5_name}}</td>
                    <td>{{$data->l6_name}}</td>
                    <td>{{$data->emp_code}}</td>
                    <td><a href="{{url('user/'.$user_id)}}">{{$data->person_fullname}}</a></td>
                    <td>{{$data->rolename}}</td>
                    <td>{{$data->mobile}}</td>
                    <td><a href="{{url('user/'.$person_id_senior)}}">{{!empty($senior[$data->person_id_senior])?$senior[$data->person_id_senior]:''}}</a></td>

                  
                    
                    @if($attWork == 86 || $attWork == 83 || $attWork == 85 || $attWork == 90)
                    <td>{{!empty($taskOfTheDay[$attWork])?$taskOfTheDay[$attWork]:''}}</td>
                    @else
                    <td>{{!empty($productData[$data->user_id][$date]['market'])?$productData[$data->user_id][$date]['market']:''}}</td>
                    @endif


                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>

                       <?php
                      
                      ?>
                      @foreach($catalog_product as $ckey=>$cdata)
                      <?php //$product_Data = $data->dsrMonthly($cdata->id,$data->user_id,$date);
                      $total_qty[$cdata->id][]=!empty($dsr[$data->user_id.$cdata->id.$date])?$dsr[$data->user_id.$cdata->id.$date]:'0';
                      $totalQty[$cdata->id][]=!empty($dsr[$data->user_id.$cdata->id.$date])?$dsr[$data->user_id.$cdata->id.$date]:'0';
                       ?>
                      <td>{{!empty($dsr[$data->user_id.$cdata->id.$date])?$dsr[$data->user_id.$cdata->id.$date]:'0'}}</td>
                    @endforeach
                     

                    <td>{{!empty($productData[$data->user_id][$date]['total_call'])?$productData[$data->user_id][$date]['total_call']:''}}</td>
                    <td>{{!empty($productData[$data->user_id][$date]['productive_call'])?$productData[$data->user_id][$date]['productive_call']:''}}</td>

                    <td>{{!empty($productData[$data->user_id][$date]['linesSold'])?$productData[$data->user_id][$date]['linesSold']:''}}</td>


                    <td>{{!empty($productData[$data->user_id][$date]['total_call'])?round(((($productData[$data->user_id][$date]['productive_call'])/($productData[$data->user_id][$date]['total_call']))*100)).'%':'N/A'}}
                    </td>

                     <td>{{!empty($productData[$data->user_id][$date]['product_amount'])?round($productData[$data->user_id][$date]['product_amount']):'0'}}</td>
                      <?php $total_amt[]=!empty($productData[$data->user_id][$date]['product_amount'])?$productData[$data->user_id][$date]['product_amount']:'0';?>


                       <td>{{!empty($scheme_amount[$data->user_id.$date])?round($scheme_amount[$data->user_id.$date]):'0'}}</td>
                      <?php $total_sch_amt[]=!empty($scheme_amount[$data->user_id.$date])?$scheme_amount[$data->user_id.$date]:'0';?>

                      <td></td>

                      <td>{{!empty($productData[$data->user_id][$date]['secondaryCasesSold'])?round($productData[$data->user_id][$date]['secondaryCasesSold']):'0'}}</td>
                      <?php $totalSecondaryCasesSold[]=!empty($productData[$data->user_id][$date]['secondaryCasesSold'])?$productData[$data->user_id][$date]['secondaryCasesSold']:'0';?>



                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    <td>{{$attWorkWith}}</td>

                      <td>{{$attRemarks}}</td>


                </tr>


                    @if($data->l3_id == 48)
                        @if($i < $finalCount)

                            @if($data->l5_id == $person[$forwardKey]['l5_id'])

                            @else

                            <tr>
                                <td colspan="12"><b>{{$data->l5_name}} Total</b></td>
                              

                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>



                                @foreach($catalog_product as $catalogKey=>$catalogData)
                                 <?php $grandQty = !empty($totalQty[$catalogData->id])?$totalQty[$catalogData->id]:array(); ?>
                                <td><b>{{array_sum($grandQty)}}</b></td>
                                @endforeach
                                <?php $totalQty = array(); ?>



                              

                                <td><b>{{$arraySumTotalCall = array_sum($totalCall)}}</b></td>
                                <?php $totalCall = array(); ?>

                                <td><b>{{$arraySumProductiveCall = array_sum($productiveCall)}}</b></td>
                                <?php $productiveCall = array(); ?>

                                <td><b>{{array_sum($linesSold)}}</b></td>
                                <?php $linesSold = array(); ?>


                                @if($arraySumTotalCall != 0)
                                <td><b>{{ROUND((($arraySumProductiveCall/$arraySumTotalCall)*100))}}%</b></td>
                                @else
                                <td><b>0 %</b></td>
                                @endif


                                  <td><b>{{round(array_sum($totalAmt))}}</b></td>
                                <?php $totalAmt = array(); ?>

                                 <td><b>{{round(array_sum($totalSchAmt))}}</b></td>
                                <?php $totalSchAmt = array(); ?>

                                <td></td>

                                <td><b>{{round(array_sum($secondaryCasesSold))}}</b></td>
                                <?php $secondaryCasesSold = array(); ?>

                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                
                            </tr>
                            @endif
                            
                        @endif

                    @else
                        @if($i < $finalCount)
                             @if($data->l3_id == $person[$forwardKey]['l3_id'])

                              @else

                                <tr>
                                  
                                      <td colspan="12"><b>{{$data->l3_name}} Total</b></td>


                                    <td></td>
                                    <td></td>

                                    <td></td>
                                    <td></td>


                                     @foreach($catalog_product as $catalogKey=>$catalogData)
                                     <?php $grandQty = !empty($totalQty[$catalogData->id])?$totalQty[$catalogData->id]:array(); ?>
                                    <td><b>{{array_sum($grandQty)}}</b></td>
                                    @endforeach
                                    <?php $totalQty = array(); ?>


                                    

                                    <td><b>{{array_sum($totalCall)}}</b></td>
                                     <?php $totalCall = array(); ?>

                                    <td><b>{{array_sum($productiveCall)}}</b></td>
                                     <?php $productiveCall = array(); ?>

                                     <td><b>{{array_sum($linesSold)}}</b></td>
                                    <?php $linesSold = array(); ?>

                                    <td></td>

                                    <td><b>{{round(array_sum($totalAmt))}}</b></td>
                                    <?php $totalAmt = array(); ?>

                                      <td><b>{{round(array_sum($totalSchAmt))}}</b></td>
                                <?php $totalSchAmt = array(); ?>

                                    <td></td>
                                    
                                    <td><b>{{round(array_sum($secondaryCasesSold))}}</b></td>
                                    <?php $secondaryCasesSold = array(); ?>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>



                                </tr>

                             @endif
                        @else
                             <tr>
                                <td colspan="12"><b>{{$data->l3_name}} Total</b></td>
                              

                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>



                                   @foreach($catalog_product as $catalogKey=>$catalogData)
                                 <?php $grandQty = !empty($totalQty[$catalogData->id])?$totalQty[$catalogData->id]:array(); ?>
                                <td><b>{{array_sum($grandQty)}}</b></td>
                                @endforeach
                                <?php $totalQty = array(); ?>


                                  

                                  <td><b>{{array_sum($totalCall)}}</b></td>
                                <?php $totalCall = array(); ?>

                                <td><b>{{array_sum($productiveCall)}}</b></td>
                                <?php $productiveCall = array(); ?>
                                
                                 <td><b>{{array_sum($linesSold)}}</b></td>
                                <?php $linesSold = array(); ?>

                                <td></td>

                                <td><b>{{round(array_sum($totalAmt))}}</b></td>
                                <?php $totalAmt = array(); ?>

                                  <td><b>{{round(array_sum($totalSchAmt))}}</b></td>
                                <?php $totalSchAmt = array(); ?>

                                <td></td>
                                
                                <td><b>{{round(array_sum($secondaryCasesSold))}}</b></td>
                                <?php $secondaryCasesSold = array(); ?>

                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>


                                
                            </tr>
                        @endif     

                    @endif


           @endforeach
        
    @endif
    </tbody>
    <tfoot>
        <tr>
          <th colspan="12">{{Lang::get('common.total')}} - ALL India</th>

          <th></th>
          <th></th>

          <th></th>
          <th></th>

          
          @foreach($catalog_product as $ckey1=>$cdata1)

          <?php $final_total_qty = !empty($total_qty[$cdata1->id])?$total_qty[$cdata1->id]:array(); ?>

            <th>{{array_sum($final_total_qty)}}</th>
          @endforeach

          <th></th>
          <th></th>
          <th></th>
          <th></th>

          <th>{{round(array_sum($total_amt))}}</th>
          <th>{{round(array_sum($total_sch_amt))}}</th>

          <th></th>
          
          <th>{{round(array_sum($totalSecondaryCasesSold))}}</th>


          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>


        </tr>
    </tfoot>
</table>
<?php
 $implode = implode(",",$null);
  ?>

<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
  