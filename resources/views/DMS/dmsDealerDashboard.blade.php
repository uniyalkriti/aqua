@extends('layouts.core_php_heade')

@section('dms_body')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
<style>
table, th, td {
 
}


tbody tr:nth-child(odd){
  background-color: #e6ffcc;
  color: black;
}
</style>

    <div class="main-content" style="   ">
        <div class="main-content-inner">
            
            <div class="row fluid-container">
                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner">
                            
                           
                            
                            @foreach($image_dyn as $ban_key => $ban_value)
                                @if($ban_key == '0')
                                <div class="item active ">
                                    <img src="{{url('baidyanath_images/'.$ban_value->img)}}" id="ctl00_ContentPlaceHolder1_img{{$ban_key+1}}" class="img-responsive" style="width: 100%;" />
                                </div>
                                @else
                                <div class="item  ">
                                    <img src="{{url('baidyanath_images/'.$ban_value->img)}}" id="ctl00_ContentPlaceHolder1_img{{$ban_key+1}}" class="img-responsive" style="width: 100%;" />
                                </div>
                                @endif
                            @endforeach
                            
                        </div>
                        <!-- Left and right controls -->
                        <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span
                                class="glyphicon glyphicon-chevron-left">
                            </span><span class="sr-only">Previous</span> </a><a class="right carousel-control"
                            href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right">
                            </span><span class="sr-only">Next</span> </a>
                    </div>
            </div>
            
            <div class="page-content"  style=" font-family: 'Times New Roman', Times, serif; ">
               
               <div class="row">
                   <div class="col-xs-12" >
                        <table id="dynamic-1table" class=" table-bordered " style="width: 100%;">
                            <thead >
                                <tr>
                                    <th colspan="6" style="background-color: #ffffcc;color: black;text-align: center; font-size: 24px; font-weight:100px; ">
                                        <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;"> Current Status</b>
                                    </th>
                                </tr>
                                <tr >
                                    <th class="center" rowspan ="2" style="background-color: #90d781;color: black; font-size: 20px; " >
                                        {{Lang::get('common.s_no')}}
                                    </th>
                                    <th rowspan="2"  style="background-color: #90d781; color: black ; font-size: 20px;"  >Category</th>
                                    <th style="background-color: #539639; color: black; font-size: 20px; " colspan="3">Annual</th>
                                    <!-- <th style="background-color: #80ccff; color: black;" colspan="3">Current Quarter</th> -->
                                    <th style="background-color: #539639; color: black; font-size: 20px; "  >Current Month</th>
                                    
                                </tr>
                                <tr>
                                    <th style="background-color: #90d781; color: black; "> Target</th>
                                    <th style="background-color: #90d781; color: black; " >Achieved</th>
                                    <th style="background-color: #90d781; color: black; " >Achieved (%)</th>
                                    <!-- <th style="background-color: #80ccff; color: black;">Target</th>
                                    <th style="background-color: #80ccff; color: black;">Sales</th>
                                    <th style="background-color: #80ccff; color: black;">Achieved (%)</th> -->
                                    <th style="background-color: #90d781; color: black; " >Sales</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: left;">
                                <tr style="text-align: left; background-color: #e6ffcc;" >
                                    <td style="height:30px; background-color: #e6ffcc; color: black;">1</td>
                                    <td style="height:30px; text-align: left; background-color: #e6ffcc; color: black;">Asav Box</td>
                                     <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($asv_wise_target)?number_format($asv_wise_target,2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($asv_wise_sale_data->QTYISSUED)?number_format($asv_wise_sale_data->QTYISSUED,2):"0.00"}}</td>
                                    <?php
                                    $achieved = (empty($asv_wise_target) || empty($asv_wise_sale_data->QTYISSUED)) ? "0.00" : number_format(round((($asv_wise_sale_data->QTYISSUED/$asv_wise_target)*100),2),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($montha_sv_wise_sale_data->QTYISSUED)?number_format($montha_sv_wise_sale_data->QTYISSUED,2):"0.00"}}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td style="height:30px; ">2</td>
                                    <td style="height:30px; text-align: left;">Asav</td>
                                     <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_targets['ASV'])?round($mktg_catg_wise_targets['ASV']/100000,2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_sales['ASV']->VALISSUED)?round($mktg_catg_wise_sales['ASV']->VALISSUED/100000,2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">0.00%</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($month_mktg_catg_wise_sales['ASV']->VALISSUED)?round($month_mktg_catg_wise_sales['ASV']->VALISSUED/100000,2):"0.00"}}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td style="height:30px; background-color: #e6ffcc; color: black;" >3</td>
                                    <td style="height:30px; text-align: left; background-color: #e6ffcc; color: black;">MAIN LINE <span style="font-size: 12px;">(CLA+GEN+GLD)(VALUE)</span></td>
                                    <?php
                                    $a = !empty($mktg_catg_wise_targets['CLA'])?($mktg_catg_wise_targets['CLA']):0;
                                    $b = !empty($mktg_catg_wise_targets['GEN'])?($mktg_catg_wise_targets['GEN']):0;
                                    $c = !empty($mktg_catg_wise_targets['GLD'])?($mktg_catg_wise_targets['GLD']):0;
                                    $mainline_targets = $a+$b+$c;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mainline_targets)?number_format(round($mainline_targets/100000,2),2):"0.00"}}</td>
                                    <?php
                                    $a_1 = !empty($mktg_catg_wise_sales['CLA']->VALISSUED)?($mktg_catg_wise_sales['CLA']->VALISSUED):0;
                                    $b_1 = !empty($mktg_catg_wise_sales['GEN']->VALISSUED)?($mktg_catg_wise_sales['GEN']->VALISSUED):0;
                                    $c_1 = !empty($mktg_catg_wise_sales['GLD']->VALISSUED)?($mktg_catg_wise_sales['GLD']->VALISSUED):0;
                                    $mainline_sales = $a_1+$b_1+$c_1;

                                    $a_1_m = !empty($month_mktg_catg_wise_sales['CLA']->VALISSUED)?($month_mktg_catg_wise_sales['CLA']->VALISSUED):0;
                                    $b_1_m = !empty($month_mktg_catg_wise_sales['GEN']->VALISSUED)?($month_mktg_catg_wise_sales['GEN']->VALISSUED):0;
                                    $c_1_m = !empty($month_mktg_catg_wise_sales['GLD']->VALISSUED)?($month_mktg_catg_wise_sales['GLD']->VALISSUED):0;
                                    $mainline_sales_m = $a_1_m+$b_1_m+$c_1_m;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mainline_sales)?number_format(round($mainline_sales/100000,2),2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mainline_targets) || empty($mainline_sales)) ? "0.00" : round((($mainline_sales/$mainline_targets)*100),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mainline_sales_m)?number_format(round($mainline_sales_m/100000,2),2):"0.00"}}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td style="height:30px; ">4</td>
                                  
                                    <td style="height:30px; text-align: left; "><a href="{{url('otc')}}" target="_blank">OTC Category-I(Value)</a></td>
                                     <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_targets['OTC'])?number_format(round($mktg_catg_wise_targets['OTC']/100000,2),2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_sales['OTC']->VALISSUED)?number_format(round($mktg_catg_wise_sales['OTC']->VALISSUED/100000,2),2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['OTC']->VALISSUED) || empty($mktg_catg_wise_targets['OTC'])) ? "0.00" : round((($mktg_catg_wise_sales['OTC']->VALISSUED/$mktg_catg_wise_targets['OTC'])*100),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right;  color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($month_mktg_catg_wise_sales['OTC']->VALISSUED)?number_format(round($month_mktg_catg_wise_sales['OTC']->VALISSUED/100000,2),2):"0.00"}}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td style="height:30px; background-color: #e6ffcc; color: black;">5</td>
                                  
                                    <td style="height:30px; text-align: left; background-color: #e6ffcc;"><a href="{{url('ot2')}}" target="_blank">OTC Category-II(Value)</a></td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_targets['OT2'])?number_format(round($mktg_catg_wise_targets['OT2']/100000,2),2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_sales['OT2']->VALISSUED)?number_format(round($mktg_catg_wise_sales['OT2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['OT2']->VALISSUED) || empty($mktg_catg_wise_targets['OT2'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['OT2']->VALISSUED/$mktg_catg_wise_targets['OT2'])*100),2),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($month_mktg_catg_wise_sales['OT2']->VALISSUED)?number_format(round($month_mktg_catg_wise_sales['OT2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                </tr>
                             <!--    <tr style="text-align: left;">
                                    <td style="height:30px;  color:black;">6</td>
                                  
                                    <td style="height:30px; text-align: left; "><a href="{{url('ethical')}}" target="_blank">ETHICAL</a></td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_targets['JPS'])?round($mktg_catg_wise_targets['JPS']/100000,2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_sales['JPS']->VALISSUED)?round($mktg_catg_wise_sales['JPS']->VALISSUED/100000,2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['JPS']->VALISSUED) || empty($mktg_catg_wise_targets['JPS'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['JPS']->VALISSUED/$mktg_catg_wise_targets['JPS'])*100),2),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right;  color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($month_mktg_catg_wise_sales['JPS']->VALISSUED)?round($month_mktg_catg_wise_sales['JPS']->VALISSUED/100000,2):"0.00"}}</td>
                                </tr> -->


                                <tr style="text-align: left;">
                                    <td style="height:30px; ">4</td>
                                  
                                    <td style="height:30px; text-align: left; "><a href="{{url('JPS')}}" target="_blank">Ethical Category-I(Value)</a></td>
                                     <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_targets['JPS'])?number_format(round($mktg_catg_wise_targets['JPS']/100000,2),2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_sales['JPS']->VALISSUED)?number_format(round($mktg_catg_wise_sales['JPS']->VALISSUED/100000,2),2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['JPS']->VALISSUED) || empty($mktg_catg_wise_targets['JPS'])) ? "0.00" : round((($mktg_catg_wise_sales['JPS']->VALISSUED/$mktg_catg_wise_targets['JPS'])*100),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right;  color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right;  color:black;">{{!empty($month_mktg_catg_wise_sales['JPS']->VALISSUED)?number_format(round($month_mktg_catg_wise_sales['JPS']->VALISSUED/100000,2),2):"0.00"}}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td style="height:30px; background-color: #e6ffcc; color: black;">5</td>
                                  
                                    <td style="height:30px; text-align: left; background-color: #e6ffcc;"><a href="{{url('JP2')}}" target="_blank">Ethical Category-II(Value)</a></td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_targets['JP2'])?number_format(round($mktg_catg_wise_targets['JP2']/100000,2),2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_sales['JP2']->VALISSUED)?number_format(round($mktg_catg_wise_sales['JP2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['JP2']->VALISSUED) || empty($mktg_catg_wise_targets['JP2'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['JP2']->VALISSUED/$mktg_catg_wise_targets['JP2'])*100),2),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($month_mktg_catg_wise_sales['JP2']->VALISSUED)?number_format(round($month_mktg_catg_wise_sales['JP2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                </tr>



                                <tr style="text-align: left;">
                                    <td style="height:30px; background-color: #e6ffcc;  color:black;">7</td>
                                  
                                    <td style="height:30px; text-align: left; background-color: #e6ffcc; color:black;"><a href="{{url('fmcg')}}" target="_blank">FMCG</a></td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_targets['FMC'])?round($mktg_catg_wise_targets['FMC']/100000,2):"0.00"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_sales['FMC']->VALISSUED)?round($mktg_catg_wise_sales['FMC']->VALISSUED/100000,2):"0.00"}}</td>
                                    <?php
                                        $achieved = (empty($mktg_catg_wise_sales['FMC']->VALISSUED) || empty($mktg_catg_wise_targets['FMC'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['FMC']->VALISSUED/$mktg_catg_wise_targets['FMC'])*100),2),2) ;
                                    ?>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                    <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($month_mktg_catg_wise_sales['FMC']->VALISSUED)?round($month_mktg_catg_wise_sales['FMC']->VALISSUED/100000,2):"0.00"}}</td>
                                </tr>
                                <tr>
                                    <?php
                                        $total_target_array = [];
                                        $total_sales_array = [];

                                        $total_target_array[] = !empty($mktg_catg_wise_targets['ASV'])?$mktg_catg_wise_targets['ASV']:0;
                                        $total_target_array[] = !empty($mainline_targets)?$mainline_targets:0;
                                        $total_target_array[] = !empty($mktg_catg_wise_targets['OTC'])?$mktg_catg_wise_targets['OTC']:0;
                                        $total_target_array[] = !empty($mktg_catg_wise_targets['OT2'])?$mktg_catg_wise_targets['OT2']:0;
                                        $total_target_array[] = !empty($mktg_catg_wise_targets['JPS'])?$mktg_catg_wise_targets['JPS']:0;
                                        $total_target_array[] = !empty($mktg_catg_wise_targets['FMC'])?$mktg_catg_wise_targets['FMC']:0;

                                        $total_sales_array[] = !empty($mktg_catg_wise_sales['ASV']->VALISSUED)?$mktg_catg_wise_sales['ASV']->VALISSUED:0;
                                        $total_sales_array[] = !empty($mainline_sales)?$mainline_sales:0;
                                        $total_sales_array[] = !empty($mktg_catg_wise_sales['OTC']->VALISSUED)?$mktg_catg_wise_sales['OTC']->VALISSUED:0;
                                        $total_sales_array[] = !empty($mktg_catg_wise_sales['OT2']->VALISSUED)?$mktg_catg_wise_sales['OT2']->VALISSUED:0;
                                        $total_sales_array[] = !empty($mktg_catg_wise_sales['JPS']->VALISSUED)?$mktg_catg_wise_sales['JPS']->VALISSUED:0;
                                        $total_sales_array[] = !empty($mktg_catg_wise_sales['FMC']->VALISSUED)?$mktg_catg_wise_sales['FMC']->VALISSUED:0;


                                        $total_sales_array_m[] = !empty($month_mktg_catg_wise_sales['ASV']->VALISSUED)?$month_mktg_catg_wise_sales['ASV']->VALISSUED:0;
                                        $total_sales_array_m[] = !empty($mainline_sales_m)?$mainline_sales_m:0;
                                        $total_sales_array_m[] = !empty($month_mktg_catg_wise_sales['OTC']->VALISSUED)?$month_mktg_catg_wise_sales['OTC']->VALISSUED:0;
                                        $total_sales_array_m[] = !empty($month_mktg_catg_wise_sales['OT2']->VALISSUED)?$month_mktg_catg_wise_sales['OT2']->VALISSUED:0;
                                        $total_sales_array_m[] = !empty($month_mktg_catg_wise_sales['JPS']->VALISSUED)?$month_mktg_catg_wise_sales['JPS']->VALISSUED:0;
                                        $total_sales_array_m[] = !empty($month_mktg_catg_wise_sales['FMC']->VALISSUED)?$month_mktg_catg_wise_sales['FMC']->VALISSUED:0;
                                        // dd($total_sales_array);
                                    ?>
                                    <td colspan="2" >TOTAL</td>
                                    <!-- <td style="text-align: right;">{{$tt = number_format(round(array_sum($total_target_array)/100000),2),2}}</td> -->
                                    <td style="text-align: right;">{{$tt = number_format(round(array_sum($total_target_array)/100000,2),2)}}</td>
                                    <td style="text-align: right;">{{$ts = number_format(round(array_sum($total_sales_array)/100000,2),2)}}</td>

                                    <?php
                                    // dd('1');
                                    $ts = round(array_sum($total_sales_array)/100000,2);
                                        $divide_val = round(array_sum($total_target_array)/100000,2);
                                        if($divide_val != 0){
                                            $achieved = round(($ts/$divide_val)*100,2);
                                        }
                                        else{
                                            $achieved = 0;
                                        }
                                    ?>
                                    <td style="text-align: right;">{{$achieved."%"}}</td>
                                    <td style="text-align: right;">{{number_format(round(array_sum($total_sales_array_m)/100000,2),2)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.span -->
               </div>
               <div class="row">
                    <div class="col-xs-12" id="ajax-table-invoice" >


                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="ajax-table-target" >

                        <!-- @include('DMS/InvoiceDetails.ajax') -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="ajax-credit-debit" >

                        <!-- @include('DMS/CreditDebitNote.credit_debit_note_ajax') -->

                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
<!--  -->
    </div>
</div>
    
</body>
<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('msell/js/common.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>




<script type="text/javascript">
    // $("html, body").animate({ scrollTop: 300 }, 20000);
    function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }

        $(document).ready(function() { /* code here */ 
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "post",
                    url: "{{route('credit_debit_notes_ajax')}}",
                    dataType: 'json',
                    data: {'range': 40},
                    success: function (datar) {
                        // alert('1');
                        $('#ajax-credit-debit').html(data.responseText);
                        
                        // alert('1');

                    },
                    complete: function (data) {
                        // $('#loading-image').hide();
                        // console.log(data.responseText);
                        $('#ajax-credit-debit').html(data.responseText);
                        // alert('1');
                    },
                    error: function (data) {
                        

                    }
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "post",
                    url: "{{route('invoice_details_ajax')}}",
                    dataType: 'json',
                    data: {'range': 40},
                    success: function (datar) {
                        // alert('1');
                        $('#ajax-credit-debit').html(data.responseText);
                        
                        // alert('1');

                    },
                    complete: function (data) {
                        // $('#loading-image').hide();
                        // console.log(data.responseText);
                        $('#ajax-table-invoice').html(data.responseText);
                        // alert('1');
                    },
                    error: function (data) {
                        

                    }
                });



                
        });
        

        
</script>
<script type="text/javascript">
    $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        //show datepicker when clicking on the icon
        .next().on(ace.click_event, function(){
            $(this).prev().focus();
        });

        //or change it into a date range picker
        $('.input-daterange').datepicker({autoclose:true});
        //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $('input[name=date_range_picker]').daterangepicker({
            'applyClass' : 'btn-sm btn-success',
            'cancelClass' : 'btn-sm btn-default',
                showDropdowns: true,
            // showWeekNumbers: true,             
            minDate: '2017-01-01',
            maxDate: moment().add(2, 'years').format('YYYY-01-01'),
            locale: {
                format: 'YYYY/MM/DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
            },
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    dateLimit: {
                                        "month": 1
                                    },

        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        }); 
        $(document).ready(function()
        {
          $("tr:odd").css({
            "background-color":"#000",
            "color":"#fff"});
        });
    </script>
</script>
@endsection