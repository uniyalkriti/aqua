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
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="14" align="center"><h3>Daily Sale Report({{$yesterday}})</h3></td></tr>

     <tr style="border: 1px solid black; padding: 10px;">

        <td style="border: 1px solid black; padding: 10px; background-color: #5b9bd5" colspan="10" align="center">
            <h3>Values</h3>
        </td>
        <td style="border: 1px solid black; padding: 10px; background-color: #a9d08e" colspan="4" align="center">
            <h3>Average</h3>
        </td>
    </tr>




    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sub State</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Manager</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sum Of New Retailer</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of Total Retailer</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of TC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of PC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of Sale Amount</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum of Retailing Employees</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Count of Total Manpower </th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of New Retailer </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of TC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of PC </th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of Sale Amount </th>

    
   
            
        
    </tr>
  
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php $i = 1;
        $newRetailerArray = array();
        $TotalRetailerArray = array();
        $TotalCallArray = array();
        $ProductiveCallArray = array();
        $SalesArray = array();
        $RetailingEmployeeArray = array();
        $TotalEmployeeArray = array();
        
        $avgNewRetailerArray = array();
        $avgTCArray = array();
        $avgPCArray = array();
        $avgSalesArray = array();

    
    ?>
    @if(!empty($locationFour))
        @foreach($locationFour as $key=>$data)
        <?php 

        $manager = !empty($managerEmployee[$key])?$managerEmployee[$key]:'NA';
        $NewRetailer = !empty($sumOfNewRetailer[$key])?$sumOfNewRetailer[$key]:'0';
        $TotalRetailer = !empty($sumOfTotalRetailer[$key])?$sumOfTotalRetailer[$key]:'0';
        $TotalCall = !empty($sumOfTotalCall[$key])?$sumOfTotalCall[$key]:'0';
        $ProductiveCall = !empty($sumOfProductiveCall[$key])?$sumOfProductiveCall[$key]:'0';
        $Sales = !empty($sumOfSales[$key])?$sumOfSales[$key]:'0';
        $RetailingEmployee = !empty($sumOfRetailingEmployee[$key])?$sumOfRetailingEmployee[$key]:'0';
        $TotalEmployee = !empty($countOfTotalEmployee[$key])?$countOfTotalEmployee[$key]:'0';

        if($RetailingEmployee != 0){
        $avgNewRetailer = round($NewRetailer/$RetailingEmployee);
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
        $TotalRetailerArray[] = $TotalRetailer;
        $TotalCallArray[] = $TotalCall;
        $ProductiveCallArray[] = $ProductiveCall;
        $SalesArray[] = $Sales;
        $RetailingEmployeeArray[] = $RetailingEmployee;
        $TotalEmployeeArray[] = $TotalEmployee;
        
        $avgNewRetailerArray[] = $avgNewRetailer;
        $avgTCArray[] = $avgTC;
        $avgPCArray[] = $avgPC;
        $avgSalesArray[] = $avgSales;




         ?>
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                  
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$i}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$data}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$manager}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$NewRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$ProductiveCall}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$Sales}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$RetailingEmployee}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$TotalEmployee}}</td>

                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgNewRetailer}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgTC}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgPC}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$avgSales}}</td>

                          
                          
                            <?php $i++; ?>
                       
                </tr>
                
            @endforeach
    @endif

    <tr>
        <td colspan="3">Grand Total</td>

        <td>{{array_sum($newRetailerArray)}}</td>
        <td>{{array_sum($TotalRetailerArray)}}</td>
        <td>{{array_sum($TotalCallArray)}}</td>
        <td>{{array_sum($ProductiveCallArray)}}</td>
        <td>{{array_sum($SalesArray)}}</td>
        <td>{{array_sum($RetailingEmployeeArray)}}</td>
        <td>{{array_sum($TotalEmployeeArray)}}</td>
        <td>{{array_sum($avgNewRetailerArray)}}</td>
        <td>{{array_sum($avgTCArray)}}</td>
        <td>{{array_sum($avgPCArray)}}</td>
        <td>{{array_sum($avgSalesArray)}}</td>
    </tr>

    </tbody>
</table>
