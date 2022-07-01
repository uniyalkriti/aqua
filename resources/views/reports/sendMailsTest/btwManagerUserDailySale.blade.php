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
    table, th, td {
  border: 1px solid black; padding: 10px;
}
</style>
<table style="border: 1px solid black; border-style: collapse;">
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="26" align="center"><h3>Manager Users Daily Sale Report({{$yesterday}})</h3></td></tr>

     <tr style="border: 1px solid black; padding: 10px;">

        <td style="border: 1px solid black; padding: 10px; background-color: #5b9bd5" colspan="18" align="center">
            <h3>Values</h3>
        </td>
        <td style="border: 1px solid black; padding: 10px; background-color: #a9d08e" colspan="4" align="center">
            <h3>Average</h3>
        </td>

        <td style="border: 1px solid black; padding: 10px; background-color: #a9d08e" colspan="4" align="center">
            <h3>{{$startDate}} To {{$yesterday}}</h3>
        </td>

    </tr>




    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Manager</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sub State</th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">User Name</th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Role</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Head Quarter</th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Today Task</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Beat Number</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Beat Name</th>


        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Total BTW Counter On Beat</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Total New Productive Retailer</th>
        <!-- <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of Total Retailer</th> -->
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total TC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total PC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Sale Amount</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total Secondary Sale Target</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Achievement In %</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Retailing Employees</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Total Manpower Only SR/JSO/SSO/SO </th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total New Productive Retailer </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total TC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Total PC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Sale Amount </th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Total New Productive Retailer </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Total TC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Total PC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;"> Sale Amount </th>


    
   
            
        
    </tr>
  
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php $i = 1;
        $newRetailerArray = array();
        $newProductiveRetailerArray = array();
        // $TotalRetailerArray = array();
        $TotalCallArray = array();
        $ProductiveCallArray = array();
        $SalesArray = array();
        $RetailingEmployeeArray = array();
        $SecondaryTargetArray = array();
        $TotalEmployeeArray = array();
        
        $avgNewRetailerArray = array();
        $avgTCArray = array();
        $avgPCArray = array();
        $avgSalesArray = array();

         $TotalCallComArray = array();
        $ProductiveCallComArray = array();
        $SalesComArray = array();
        $newComProductiveRetailerArray = array();


    
    ?>
    @if(!empty($juniorDetails))
        @foreach($juniorDetails as $key=>$data)
        <?php 

        $today_task = !empty($todayTask[$key])?$todayTask[$key]:'NA';
        $beat_name = !empty($beatName[$key])?$beatName[$key]:'NA';

        $beat_number = !empty($beatNumber[$key])?$beatNumber[$key]:'';



        $role = !empty($juniorRoleDetails[$key])?$juniorRoleDetails[$key]:'NA';
        $head_quarter = !empty($juniorHeadQuarterDetails[$key])?$juniorHeadQuarterDetails[$key]:'NA';

        // $manager = !empty($managerEmployee[$key])?$managerEmployee[$key]:'NA';
        $NewRetailer = !empty($sumOfNewRetailer[$key])?$sumOfNewRetailer[$key]:'0';
        $NewProductiveRetailer = !empty($sumOfNewProductiveRetailer[$key])?$sumOfNewProductiveRetailer[$key]:'0';
        // $TotalRetailer = !empty($sumOfTotalRetailer[$key])?$sumOfTotalRetailer[$key]:'0';
        $TotalCall = !empty($sumOfTotalCall[$key])?$sumOfTotalCall[$key]:'0';
        $ProductiveCall = !empty($sumOfProductiveCall[$key])?$sumOfProductiveCall[$key]:'0';
        $Sales = !empty($sumOfSales[$key])?$sumOfSales[$key]:'0';
        $RetailingEmployee = !empty($sumOfRetailingEmployee[$key])?$sumOfRetailingEmployee[$key]:'0';
        $TotalEmployee = !empty($countOfTotalEmployee[$key])?$countOfTotalEmployee[$key]:'0';

        // $secondaryTarget = 12500*$RetailingEmployee;
        $secondaryTarget = 12500*$TotalEmployee;


        $TotalComCall = !empty($sumOfComTotalCall[$key])?$sumOfComTotalCall[$key]:'0';
        $ProductiveComCall = !empty($sumOfComProductiveCall[$key])?$sumOfComProductiveCall[$key]:'0';
        $SalesCom = !empty($sumOfComSales[$key])?$sumOfComSales[$key]:'0';

        $NewComProductiveRetailer = !empty($sumOfComNewProductiveRetailer[$key])?$sumOfComNewProductiveRetailer[$key]:'0';



          if($secondaryTarget != 0){
        $achievePer = round(($Sales/$secondaryTarget)*100);
        }else{
        $achievePer = '0';
        }




        if($RetailingEmployee != 0){
        $avgNewRetailer = round($NewProductiveRetailer/$RetailingEmployee);
        }else{
        $avgNewRetailer = '0';
        }
        
        if($RetailingEmployee != 0){
        $avgTC = round($TotalCall/$RetailingEmployee);
        }else{
        $avgTC = '0';
        }

        if($RetailingEmployee != 0){
        $avgPC = round($ProductiveCall/$RetailingEmployee);
        }else{
        $avgPC = '0';
        }   

        if($RetailingEmployee != 0){
        $avgSales = round($Sales/$RetailingEmployee);
        }else{
        $avgSales = '0';
        }


        $newRetailerArray[] = $NewRetailer;
        $newProductiveRetailerArray[] = $NewProductiveRetailer;
        // $TotalRetailerArray[] = $TotalRetailer;
        $TotalCallArray[] = $TotalCall;
        $ProductiveCallArray[] = $ProductiveCall;
        $SalesArray[] = $Sales;
        $RetailingEmployeeArray[] = $RetailingEmployee;
        $SecondaryTargetArray[] = $secondaryTarget;
        $TotalEmployeeArray[] = $TotalEmployee;
        
        $avgNewRetailerArray[] = $avgNewRetailer;
        $avgTCArray[] = $avgTC;
        $avgPCArray[] = $avgPC;
        $avgSalesArray[] = $avgSales;

        $TotalCallComArray[] = $TotalComCall;
        $ProductiveCallComArray[] = $ProductiveComCall;
        $SalesComArray[] = $SalesCom;
        $newComProductiveRetailerArray[] = $NewComProductiveRetailer;





         ?>
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                  
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$i}}</td>

                            @if($status ==1)
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$manager_name}}</td>
                            @else
                            <?php
                                $mngrs = !empty($juniorSenior[$key])?$juniorSenior[$key]:'';

                            ?>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$mngrs}}</td>

                            @endif

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$head_quarter}}</td>


                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$role}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$head_quarter}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$today_task}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$beat_number}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$beat_name}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$NewRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$NewProductiveRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$ProductiveCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$Sales}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$secondaryTarget}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$achievePer}} %</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$RetailingEmployee}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalEmployee}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgNewRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgTC}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgPC}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgSales}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$NewComProductiveRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalComCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$ProductiveComCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$SalesCom}}</td>

                          
                          
                            <?php $i++; ?>
                       
                </tr>
                
            @endforeach
    @endif

    <tr>
        <td colspan="9">Grand Total</td>

        <td>{{array_sum($newRetailerArray)}}</td>
        <td>{{array_sum($newProductiveRetailerArray)}}</td>
        <td>{{array_sum($TotalCallArray)}}</td>
        <td>{{array_sum($ProductiveCallArray)}}</td>
        <td>{{array_sum($SalesArray)}}</td>
        <td>{{array_sum($SecondaryTargetArray)}}</td>
        <td></td>
        <td>{{array_sum($RetailingEmployeeArray)}}</td>
        <td>{{array_sum($TotalEmployeeArray)}}</td>
        <td>{{array_sum($avgNewRetailerArray)}}</td>
        <td>{{array_sum($avgTCArray)}}</td>
        <td>{{array_sum($avgPCArray)}}</td>
        <td>{{array_sum($avgSalesArray)}}</td>


          <td>{{array_sum($newComProductiveRetailerArray)}}</td>
          <td>{{array_sum($TotalCallComArray)}}</td>
        <td>{{array_sum($ProductiveCallComArray)}}</td>
        <td>{{array_sum($SalesComArray)}}</td>
    </tr>

    </tbody>
</table>

