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
    <tr style="border: 1px solid black; padding: 10px;"><td style="border: 1px solid black; padding: 10px; background-color: #6b5b95" colspan="14" align="center"><h3>Daily Sale Report Of Sub State({{$yesterday}})</h3></td></tr>

     <tr style="border: 1px solid black; padding: 10px;">

        <td style="border: 1px solid black; padding: 10px; background-color: #5b9bd5" colspan="14" align="center">
            <h3>Values</h3>
        </td>
       
    </tr>




    <tr  style="border: 1px solid black; padding: 10px;">
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">S.No.</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sub State</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Manager</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">User Name</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Role</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Head Quarter</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Today Task</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sum Of Total Retailer</th>

        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f; ">Sum Of New Retailer</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of TC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of PC</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Sum Of Sale Amount</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Count of Retailing Employees</th>
        <th style="border: 1px solid black; padding: 10px; background-color: #b2ad7f;">Count of Total Manpower </th>

     
        
    </tr>
  
    <tbody style="border: 1px solid black; padding: 10px;">
    <?php 
    $i = 1;
    ?>


    @if(!empty($locationFour))
        @foreach($locationFour as $key=>$data)
         <?php
            $grandNewRetailer = array();
            $grandTotalRetailer = array();
            $grandTotalCall = array();
            $grandProductiveCall = array();
            $grandSaleAmount = array();
            $grandRetailingEmployee = array();
            $grandManPower = array();

        $dynamicRowSpan = !empty($finalOut[$key][0]['juniorDetails'])?COUNT($finalOut[$key][0]['juniorDetails']):'1';

        ?>

      
                <tr  class="" style="border: 1px solid black; padding: 10px;">
                  
                            

                            @if(!empty($finalOut[$key]))
                                @foreach($finalOut[$key] as $managerDataKey=>$managerDataValue)
                                    <?php 

                                    ?>
                                   
                                    @foreach($managerDataValue['juniorDetails'] as $juniorkey => $juniorValue)

                                    <?php
                                    $grandNewRetailer[] = $juniorValue['newRetailer'];
                                    $grandTotalRetailer[] = $juniorValue['totalRetailer'];
                                    $grandTotalCall[] = $juniorValue['totalCall'];;
                                    $grandProductiveCall[] = $juniorValue['productiveCall'];;
                                    $grandSaleAmount[] = $juniorValue['saleAmount'];;
                                    $grandRetailingEmployee[] = $juniorValue['retailingEmployee'];;
                                    $grandManPower[] = $juniorValue['totalManPower'];;
                                    ?>
                                    <tr>
                                        @if($juniorkey == 0)
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$i}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$data}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$managerDataValue['managerName']}}</td>
                                        @endif


                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['user_name']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['rolename']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['head_quarter_name']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['today_task']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['totalRetailer']}}</td>

                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['newRetailer']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['totalCall']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['productiveCall']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['saleAmount']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['retailingEmployee']}}</td>
                                        <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;">{{$juniorValue['totalManPower']}}</td>
                                    </tr>
                                    @endforeach

                                 


                                @endforeach



                            @else
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$i}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$data}}</td>
                            <td style="border: 1px solid black; padding: 10px; background-color: lightgrey;" rowspan="{{$dynamicRowSpan}}">{{$managerDataValue['managerName']}}</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            @endif


                            <?php $i++; ?>

                            <tr>
                                <td colspan="7">Total</td>
                                <td>{{array_sum($grandTotalRetailer)}}</td>

                                <td>{{array_sum($grandNewRetailer)}}</td>
                                <td>{{array_sum($grandTotalCall)}}</td>
                                <td>{{array_sum($grandProductiveCall)}}</td>
                                <td>{{array_sum($grandSaleAmount)}}</td>
                                <td>{{array_sum($grandRetailingEmployee)}}</td>
                                <td>{{array_sum($grandManPower)}}</td>
                            </tr>
                       
                
            @endforeach
    @endif


                                    </tr>
    

    </tbody>
</table>


