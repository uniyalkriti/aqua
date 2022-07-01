@if(!empty($dealerDetails))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
    <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif

<div class="table-header center" style="background-color: white; color: black; text-align: center; font-size: 40px; font-weight:100px;">
     <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;">Main Line Sale Statement</b>
    <div class="pull-left">
        
    </div>
    <div class="pull-right tableTools-container1"></div>
   
</div>
<table id="simple-table" class="table table-bordered ">

   
    <thead >
        <tr>
            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >S.No.</th>
            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >Stockist Name</th>
            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >City</th>
            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >Period</th>

            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >ASAV<br>
                (CASE)
            </th>

            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >ASAV<br>
                (VALUE)
            </th>

            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >MAINLINE<br>
                (VALUE)
            </th>



            @foreach($finalProducts as $fpkey => $fpval)
            <?php 
            $null[] = 'null';
            ?>
            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >{{$fpval}}<br>
                (CASE)

            </th>
            @endforeach


              <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >OTC-|<br>
                (VALUE)
            </th>


               <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >OTC-||<br>
                (VALUE)
            </th>



            <th width="200px;" style=" background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" >Total Sale<br>
                (VALUE)
            </th>



        </tr>
    </thead>
    <tbody >
        @if(!empty($dealerDetails))
            @foreach($dealerDetails as $key=>$value)
            <?php

            $targetValue = array(); 
            $monthValue = array(); 
            $cprdValue = array(); 

            ?>
                <tr>
                    <td  rowspan="3" style="text-align: left;" width="200px;" >{{$key+1}}</td>
                    <td  rowspan="3" style="text-align: left;" width="200px;" >{{$value->ACC_NAME}}</td>
                    <td rowspan="3" style="text-align: left;" width="200px;" >{{$value->DEALER_CITY}}</td>
                     @foreach($arrayPeriod as $apkey => $apval)

                    <td  style="text-align: left;" width="200px;" >{{$apval}}</td>


                   

                    <!-- for ASAV start -->
                    @if($apkey == 0)
                    <td width="200px;" style="text-align: left;" >0</td>
                    <td width="200px;" style="text-align: left;" >0</td>

                    @elseif($apkey == 1)
                    <?php  
                    $finalASAVTargetCase = !empty($finalASAVTargetDataCase[$value->ACC_CODE.'1'])?$finalASAVTargetDataCase[$value->ACC_CODE.'1']:'0';
                    $finalASAVTargetValue = !empty($finalASAVTargetDataValue[$value->ACC_CODE.'1'])?ROUND($finalASAVTargetDataValue[$value->ACC_CODE.'1']/100000,2):'0';

                    $targetValue[] = $finalASAVTargetValue; 

                    $finalASAVTargetCaseArray[] = $finalASAVTargetCase;
                    $finalASAVTargetValueArray[] = $finalASAVTargetValue;


                      ?>
                    <td width="200px;" style="text-align: left;" >{{$finalASAVTargetCase}}</td>
                    <td width="200px;" style="text-align: left;" >{{$finalASAVTargetValue}}</td>

                     @elseif($apkey == 2)
                     <?php  
                    $finalASAVSaleCase = !empty($finalASAVMonthSaleOut[$value->ACC_CODE.'2']['QTYISSUED'])?$finalASAVMonthSaleOut[$value->ACC_CODE.'2']['QTYISSUED']:'0';
                    $finalASAVSaleValue = !empty($finalASAVMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED'])?ROUND($finalASAVMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED']/100000,2):'0';

                    $monthValue[] = $finalASAVSaleValue; 


                    $finalASAVSaleCaseArray[] = $finalASAVSaleCase;
                    $finalASAVSaleValueArray[] = $finalASAVSaleValue;

                      ?>
                    <td width="200px;" style="text-align: left;" >{{$finalASAVSaleCase}}</td>
                    <td width="200px;" style="text-align: left;" >{{$finalASAVSaleValue}}</td>


                     @elseif($apkey == 3)
                    <?php  
                    $finalASAVCummMonthSaleCase = !empty($finalASAVCummMonthSaleOut[$value->ACC_CODE.'3']['QTYISSUED'])?$finalASAVCummMonthSaleOut[$value->ACC_CODE.'3']['QTYISSUED']:'0';
                    $finalASAVCummMonthSaleValue = !empty($finalASAVCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED'])?ROUND($finalASAVCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED']/100000,2):'0';

                    $finalASAVCummMonthSaleCaseArray[] = $finalASAVCummMonthSaleCase;
                    $finalASAVCummMonthSaleValueArray[] = $finalASAVCummMonthSaleValue;

                    $cprdValue[] = $finalASAVCummMonthSaleValue; 


                     ?>

                    <td width="200px;" style="text-align: left;" >{{$finalASAVCummMonthSaleCase}}</td>
                    <td width="200px;" style="text-align: left;" >{{$finalASAVCummMonthSaleValue}}</td>

                    @else
                    <td width="200px;" style="text-align: left;" ></td>
                    <td width="200px;" style="text-align: left;" ></td>

                    @endif

                    <!-- for ASAV ends -->



                    <!-- for MAINLNE start -->
                    @if($apkey == 0)
                    <td width="200px;" style="text-align: left;" >0</td>

                    @elseif($apkey == 1)
                    <?php  
                    $finalMainlineTargetValue = !empty($finalMainlineTargetDataValue[$value->ACC_CODE.'1'])?ROUND($finalMainlineTargetDataValue[$value->ACC_CODE.'1']/100000,2):'0';

                    $finalMainlineTargetValueArray[] = $finalMainlineTargetValue;

                    $targetValue[] = $finalMainlineTargetValue; 


                      ?>
                    <td width="200px;" style="text-align: left;" >{{$finalMainlineTargetValue}}</td>

                     @elseif($apkey == 2)
                     <?php  
                    $finalMainlineSaleValue = !empty($finalMainlineMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED'])?ROUND($finalMainlineMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED']/100000,2):'0';

                    $finalMainlineSaleValueArray[] = $finalMainlineSaleValue;

                    $monthValue[] = $finalMainlineSaleValue; 


                      ?>
                    <td width="200px;" style="text-align: left;" >{{$finalMainlineSaleValue}}</td>


                     @elseif($apkey == 3)
                    <?php  
                    $finalMainlineCummMonthSaleValue = !empty($finalMainlineCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED'])?ROUND($finalMainlineCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED']/100000,2):'0';

                    $finalMainlineCummMonthSaleValueArray[] = $finalMainlineCummMonthSaleValue;

                    $cprdValue[] = $finalMainlineCummMonthSaleValue; 


                     ?>
                    <td width="200px;" style="text-align: left;" >{{$finalMainlineCummMonthSaleValue}}</td>

                    @else
                    <td width="200px;" style="text-align: left;" ></td>

                    @endif

                    <!-- for MAINLNE ends -->




                       


                        @foreach($finalProducts as $fpkey => $fpval)

                                    @if($apkey == 0)
                                    <td width="200px;" style="text-align: left;" >0</td>

                                    @elseif($apkey == 1)
                                    <?php  $finalTarget = !empty($finalTargetData[$value->ACC_CODE.$apkey.$fpval])?$finalTargetData[$value->ACC_CODE.$apkey.$fpval]:'0'; 
                                    $finalTargetArray[$fpval][] = $finalTarget;

                                     ?>
                                    <td width="200px;" style="text-align: left;" >{{$finalTarget}}</td>

                                     @elseif($apkey == 2)
                                    <?php  $finalMonthSale = !empty($finalMonthSaleOut[$value->ACC_CODE.$apkey.$fpval]['QTYISSUED'])?$finalMonthSaleOut[$value->ACC_CODE.$apkey.$fpval]['QTYISSUED']:'0';

                                    $finalMonthSaleArray[$fpval][] = $finalMonthSale;


                                      ?>
                                    <td width="200px;" style="text-align: left;" >{{$finalMonthSale}}</td>


                                     @elseif($apkey == 3)
                                    <?php  $finalCummMonthSale = !empty($finalCummMonthSaleOut[$value->ACC_CODE.$apkey.$fpval]['QTYISSUED'])?$finalCummMonthSaleOut[$value->ACC_CODE.$apkey.$fpval]['QTYISSUED']:'0'; 

                                    $finalCummMonthSaleArray[$fpval][] = $finalCummMonthSale;
                                     ?>

                                    <td width="200px;" style="text-align: left;" >{{$finalCummMonthSale}}</td>

                                    @else
                                    <td width="200px;" style="text-align: left;" ></td>

                                    @endif



                        @endforeach




                         <!-- for otcOne start -->
                        @if($apkey == 0)
                        <td width="200px;" style="text-align: left;" >0</td>

                        @elseif($apkey == 1)
                        <?php  
                        $finalotcOneTargetValue = !empty($finalotcOneTargetDataValue[$value->ACC_CODE.'1'])?ROUND($finalotcOneTargetDataValue[$value->ACC_CODE.'1']/100000,2):'0';

                        $finalotcOneTargetValueArray[] = $finalotcOneTargetValue;

                        $targetValue[] = $finalotcOneTargetValue; 


                          ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcOneTargetValue}}</td>

                         @elseif($apkey == 2)
                         <?php  
                        $finalotcOneSaleValue = !empty($finalotcOneMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED'])?ROUND($finalotcOneMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED']/100000,2):'0';

                        $finalotcOneSaleValueArray[] = $finalotcOneSaleValue;

                        $monthValue[] = $finalotcOneSaleValue; 

                          ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcOneSaleValue}}</td>


                         @elseif($apkey == 3)
                        <?php  
                        $finalotcOneCummMonthSaleValue = !empty($finalotcOneCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED'])?ROUND($finalotcOneCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED']/100000,2):'0';

                        $finalotcOneCummMonthSaleValueArray[] = $finalotcOneCummMonthSaleValue;

                        $cprdValue[] = $finalotcOneCummMonthSaleValue; 


                         ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcOneCummMonthSaleValue}}</td>

                        @else
                        <td width="200px;" style="text-align: left;" ></td>

                        @endif

                        <!-- for otcOne ends -->



                        <!-- for otcTwo start -->
                        @if($apkey == 0)
                        <td width="200px;" style="text-align: left;" >0</td>

                        @elseif($apkey == 1)
                        <?php  
                        $finalotcTwoTargetValue = !empty($finalotcTwoTargetDataValue[$value->ACC_CODE.'1'])?ROUND($finalotcTwoTargetDataValue[$value->ACC_CODE.'1']/100000,2):'0';

                        $finalotcTwoTargetValueArray[] = $finalotcTwoTargetValue;

                        $targetValue[] = $finalotcTwoTargetValue; 


                          ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcTwoTargetValue}}</td>

                         @elseif($apkey == 2)
                         <?php  
                        $finalotcTwoSaleValue = !empty($finalotcTwoMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED'])?ROUND($finalotcTwoMonthSaleOut[$value->ACC_CODE.'2']['VALISSUED']/100000,2):'0';
                        $finalotcTwoSaleValueArray[] = $finalotcTwoSaleValue;

                        $monthValue[] = $finalotcTwoSaleValue; 


                          ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcTwoSaleValue}}</td>


                         @elseif($apkey == 3)
                        <?php  
                        $finalotcTwoCummMonthSaleValue = !empty($finalotcTwoCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED'])?ROUND($finalotcTwoCummMonthSaleOut[$value->ACC_CODE.'3']['VALISSUED']/100000,2):'0';
                        $finalotcTwoCummMonthSaleValueArray[] = $finalotcTwoCummMonthSaleValue;

                        $cprdValue[] = $finalotcTwoCummMonthSaleValue; 


                         ?>
                        <td width="200px;" style="text-align: left;" >{{$finalotcTwoCummMonthSaleValue}}</td>

                        @else
                        <td width="200px;" style="text-align: left;" ></td>

                        @endif

                        <!-- for otcTwo ends -->


                        <!-- final value sum starts -->

                            @if($apkey == 0)
                            <td width="200px;" style="text-align: left;"></td>
                            @elseif($apkey == 1)
                            <td width="200px;" style="text-align: left;">{{array_sum($targetValue)}}</td>
                            @elseif($apkey == 2)
                            <td width="200px;" style="text-align: left;">{{array_sum($monthValue)}}</td>
                            @elseif($apkey == 3)
                            <td width="200px;" style="text-align: left;">{{array_sum($cprdValue)}}</td>
                            @else
                            <td width="200px;" style="text-align: left;"></td>
                            @endif


                        <!-- final value sum  -->








                        </tr>

                    @endforeach

            @endforeach
        @endif


<?php
$implode = implode(',',$null); 
?>

    </tbody>
    <tfoot>
         <tr>
            <th rowspan="3" colspan="3">Grand Total</th>

            @foreach($arrayPeriod as $apkey => $apval)

            <td width="200px;" style="text-align: left;">{{$apval}}</td>

            <!-- for ASAV start -->

                @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{array_sum($finalASAVTargetCaseArray)}}</td>
                <td width="200px;" style="text-align: left;">{{$finalTrgt[] = array_sum($finalASAVTargetValueArray)}}</td>
                @elseif($apkey == 2)
                 <td width="200px;" style="text-align: left;">{{array_sum($finalASAVSaleCaseArray)}}</td>
                <td width="200px;" style="text-align: left;">{{$finalMonth[] = array_sum($finalASAVSaleValueArray)}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{array_sum($finalASAVCummMonthSaleCaseArray)}}</td>
                <td width="200px;" style="text-align: left;">{{$finalcprd[] = array_sum($finalASAVCummMonthSaleValueArray)}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                <td width="200px;" style="text-align: left;"></td>
                @endif


               
            <!-- for ASAV ends -->

            <!-- for Mainline start -->
                @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{$finalTrgt[] = array_sum($finalMainlineTargetValueArray)}}</td>
                @elseif($apkey == 2)
                <td width="200px;" style="text-align: left;">{{$finalMonth[] = array_sum($finalMainlineSaleValueArray)}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{$finalcprd[] = array_sum($finalMainlineCummMonthSaleValueArray)}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                @endif

            <!-- for Mainline ends -->

            @foreach($finalProducts as $fpkey => $fpval)
                @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{array_sum($finalTargetArray[$fpval])}}</td>
                @elseif($apkey == 2)
                <td width="200px;" style="text-align: left;">{{array_sum($finalMonthSaleArray[$fpval])}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{array_sum($finalCummMonthSaleArray[$fpval])}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                @endif
            @endforeach


            <!-- for otc one start -->
                @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{$finalTrgt[] = array_sum($finalotcOneTargetValueArray)}}</td>
                @elseif($apkey == 2)
                <td width="200px;" style="text-align: left;">{{$finalMonth[] = array_sum($finalotcOneSaleValueArray)}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{$finalcprd[] = array_sum($finalotcOneCummMonthSaleValueArray)}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                @endif
            <!-- for otc one ends -->

            <!-- for otc two start -->
                 @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{$finalTrgt[] = array_sum($finalotcTwoTargetValueArray)}}</td>
                @elseif($apkey == 2)
                <td width="200px;" style="text-align: left;">{{$finalMonth[] = array_sum($finalotcTwoSaleValueArray)}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{$finalcprd[] = array_sum($finalotcTwoCummMonthSaleValueArray)}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                @endif
            <!-- for otc two ends -->


             <!-- for grand final start -->
                 @if($apkey == 0)
                <td width="200px;" style="text-align: left;"></td>
                @elseif($apkey == 1)
                <td width="200px;" style="text-align: left;">{{array_sum($finalTrgt)}}</td>
                @elseif($apkey == 2)
                <td width="200px;" style="text-align: left;">{{array_sum($finalMonth)}}</td>
                @elseif($apkey == 3)
                <td width="200px;" style="text-align: left;">{{array_sum($finalcprd)}}</td>
                @else
                <td width="200px;" style="text-align: left;"></td>
                @endif
            <!-- for grand final ends -->


        </tr>


        @endforeach
    </tfoot>
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

    <script type="text/javascript">
         jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table1')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": true},
                            null,null, null, null, null,null,null,<?=$implode?>,
                            
                            {"bSortable": false}
                        ],
                        "aaSorting": [],
                        "sScrollY": "1000px",
                        //"bPaginate": false,

                        // "sScrollX": "100%",
                        "sScrollXInner": "120%",
                        "bScrollCollapse": true,
                        "iDisplayLength": 10,


                        select: {
                            style: 'multi'
                        }
                    });


            $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

            new $.fn.dataTable.Buttons(myTable, {
                buttons: [
                    {
                        "extend": "colvis",
                        "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        columns: ':not(:first):not(:last)'
                    },
                    {
                        "extend": "copy",
                        "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "csv",
                        "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "excel",
                        "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    
                    {
                        "extend": "print",
                        "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        autoPrint: true,
                        message: 'This print was produced using the Print button for DataTables'
                    }
                ]
            });
            myTable.buttons().container().appendTo($('.tableTools-container1'));

            //style the message box
            var defaultCopyAction = myTable.button(1).action();
            myTable.button(1).action(function (e, dt, button, config) {
                defaultCopyAction(e, dt, button, config);
                $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
            });


            var defaultColvisAction = myTable.button(0).action();
            myTable.button(0).action(function (e, dt, button, config) {

                defaultColvisAction(e, dt, button, config);


                if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                    $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                }
                $('.dt-button-collection').appendTo('.tableTools-container1 .dt-buttons')
            });

            ////

            


            myTable.on('select', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on('deselect', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                }
            });


            /////////////////////////////////
            //table checkboxes
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

            //select/deselect all rows according to table header checkbox
            $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $('#dynamic-table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
            });


            $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                e.stopImmediatePropagation();
                e.stopPropagation();
                e.preventDefault();
            });


            //And for the first simple table, which doesn't have TableTools or dataTables
            //select/deselect all rows according to table header checkbox
            var active_class = 'active';
            $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $(this).closest('table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if ($row.is('.detail-row ')) return;
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
            });


            /********************************/
            //add tooltip for small view action buttons in dropdown menu
           

            //tooltip placement on right or left
            function tooltip_placement(context, source) {
                var $source = $(source);
                var $parent = $source.closest('table')
                var off1 = $parent.offset();
                var w1 = $parent.width();

                var off2 = $source.offset();
                //var w2 = $source.width();

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }


            /***************/
            // $('.show-details-btn').on('click', function (e) {
            //     e.preventDefault();
            //     $(this).closest('tr').next().toggleClass('open');
            //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
            // });
            /***************/


            /**
             //add horizontal scrollbars to a simple table
             $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
             {
               horizontal: true,
               styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
               size: 2000,
               mouseWheelLock: true
             }
             ).css('padding-top', '12px');
             */


        })
  // /***************/
    // $('.show-details-btn').on('click', function (e) {
    //     e.preventDefault();
    //     $(this).closest('tr').next().toggleClass('open');
    //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    // });
    /***************/
    </script>


