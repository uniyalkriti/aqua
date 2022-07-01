<?php 

// include('../client/include/menu-by-role/copy-admin.inc.php');

?>
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
<style type="text/css">
    .ui-autocomplete {
    overflow: auto;
    height: 200px;
    width: 200px;
}
.button {
  display: inline-block;
  padding: 7px 20px;
  font-size: 20px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  outline: none;
  color: #fff;
  background-color: #4CAF50;
  border: none;
  border-radius: 15px;
  box-shadow: 0 6px #999;
}

.button:hover {background-color: #3e8e41}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

tbody tr:nth-child(odd){
  background-color: #e6ffcc;
  color: black;
}

.chosen-container ul.chosen-results  {
  /*background-color: #90d781;*/
  color:black;
  font-weight:bold;
  background-image: none;
  font-size: 13px;
}

.chosen-container ul.chosen-results li.highlighted {
  background-color: #90d781;
  color:black;
  font-weight:bold;
  background-image: none;
}
</style>

    <div class="main-content" style="background-color: #ffffcc;">
        <div class="main-content-inner" style="background-color: #ffffcc;">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781;">
                <ul class="breadcrumb" >
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">Report Section </a>
                    </li>

                    <li class="active" style="color: black;">Report</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content" style="padding-top: 0px; background-color: #ffffcc;  ">
                <form class="form-horizontal open collapse in" action="" method="GET" id="user-search" role="form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-xs-3 col-sm-3">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
                                <select  name="terr_id[]" id="terr_id" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($location_2_arr_cus))
                                        @foreach($location_2_arr_cus as $k=>$r)
                                         <?php if(empty($_GET['terr_id']))
                                         $_GET['terr_id']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['terr_id'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-3 col-sm-2">
                                <label class="control-label no-padding-right" for="name">Div Code</label>
                                <select  name="div_code_cus[]" id="div_code_cus" class="form-control chosen-select">
                                    <option  value="">Select</option>
                                    @if(!empty($div_code_array))
                                        @foreach($div_code_array as $k=>$r)
                                         <?php if(empty($_GET['div_code_cus']))
                                         $_GET['div_code_cus']=array();
                                         ?>
                                        <option value="{{$k}}" @if(in_array($k,$_GET['div_code_cus'])){{"selected"}} @endif> {{$r}} 
                                        </option>                                             
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-4">
                                <label class="control-label no-padding-right" for="name">Distributor</label>
                                <select multiple name="dealer_id_cus[]" id="dealer_id_cus" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($dealer_arr_cus))
                                        @foreach($dealer_arr_cus as $k=>$r)
                                         <?php if(empty($_GET['dealer_id_cus']))
                                         $_GET['dealer_id_cus']=array();
                                         ?>
                                         @if(!empty($dealer_code_filter_cus[$k]))
                                            <option value="{{$k}}" @if(in_array($k,$_GET['dealer_id_cus'])){{"selected"}} @endif>{{$dealer_code_filter_cus[$k]}} - {{$r}} </option>
                                            @else
                                            <option value="{{$k}}" @if(in_array($k,$_GET['dealer_id_cus'])){{"selected"}} @endif> {{$r}} </option>
                                         @endif
                                                                                     
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-3 col-sm-3">
                                <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                    Find
                               </button> 
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        
                                
                                <table id="dynamic-1table" class=" table-bordered table" style="height: 286px;">
                                    <thead >
                                        <tr>
                                            <th colspan="6" style="background-color: #ffffcc;color: black;text-align: center; font-size: 24px; font-weight:100px; ">
                                                <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;"> Current Status</b>
                                            </th>
                                        </tr>
                                        <tr >
                                            <th class="center" rowspan ="2" style="background-color: #90d781;color: black; font-size: 20px; width: 100px;" >
                                                {{Lang::get('common.s_no')}}
                                            </th>
                                            <th rowspan="2" width= "200px" style="background-color: #90d781; color: black ; font-size: 20px;"  >Category</th>
                                            <th style="background-color: #539639; color: black; font-size: 20px; " colspan="3">Annual</th>
                                            <!-- <th style="background-color: #80ccff; color: black;" colspan="3">Current Quarter</th> -->
                                            <th style="background-color: #539639; color: black; font-size: 20px; "  width= "150px">Current Month</th>
                                            
                                        </tr>
                                        <tr>
                                            <th style="background-color: #90d781; color: black; width: 100px;"> Target</th>
                                            <th style="background-color: #90d781; color: black; width: 100px;" >Achieved</th>
                                            <th style="background-color: #90d781; color: black; width: 100px;" >Achieved (%)</th>
                                            <!-- <th style="background-color: #80ccff; color: black;">Target</th>
                                            <th style="background-color: #80ccff; color: black;">Sales</th>
                                            <th style="background-color: #80ccff; color: black;">Achieved (%)</th> -->
                                            <th style="background-color: #90d781; color: black; width: 100px;" >Sales</th>
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
                                          
                                            <td style="height:30px; text-align: left; "><a href="{{url('otc?check_status=1&terr_id='.$terr_id.'&'.'dealer_id_cus='.$dealer_id_cus.'&'.'div_code_cus='.$div_code_cus)}}" target="_blank">OTC Category-I(Value)</a></td>
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
                                          
                                            <td style="height:30px; text-align: left; background-color: #e6ffcc;"><a href="{{url('ot2?check_status=1&terr_id='.$terr_id.'&'.'dealer_id_cus='.$dealer_id_cus.'&'.'div_code_cus='.$div_code_cus)}}" target="_blank">OTC Category-II(Value)</a></td>
                                            <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_targets['OT2'])?number_format(round($mktg_catg_wise_targets['OT2']/100000,2),2):"0.00"}}</td>
                                            <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($mktg_catg_wise_sales['OT2']->VALISSUED)?number_format(round($mktg_catg_wise_sales['OT2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                            <?php
                                                $achieved = (empty($mktg_catg_wise_sales['OT2']->VALISSUED) || empty($mktg_catg_wise_targets['OT2'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['OT2']->VALISSUED/$mktg_catg_wise_targets['OT2'])*100),2),2) ;
                                            ?>
                                            <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{$achieved."%"}}</td>
                                            <td style="height:30px; text-align: right; background-color: #e6ffcc; color:black;">{{!empty($month_mktg_catg_wise_sales['OT2']->VALISSUED)?number_format(round($month_mktg_catg_wise_sales['OT2']->VALISSUED/100000,2),2):"0.00"}}</td>
                                        </tr>
                                        <tr style="text-align: left;">
                                            <td style="height:30px;  color:black;">6</td>
                                          
                                            <td style="height:30px; text-align: left; "><a href="{{url('ethical?check_status=1&terr_id='.$terr_id.'&'.'dealer_id_cus='.$dealer_id_cus.'&'.'div_code_cus='.$div_code_cus)}}" target="_blank">ETHICAL</a></td>
                                            <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_targets['JPS'])?round($mktg_catg_wise_targets['JPS']/100000,2):"0.00"}}</td>
                                            <td style="height:30px; text-align: right;  color:black;">{{!empty($mktg_catg_wise_sales['JPS']->VALISSUED)?round($mktg_catg_wise_sales['JPS']->VALISSUED/100000,2):"0.00"}}</td>
                                            <?php
                                                $achieved = (empty($mktg_catg_wise_sales['JPS']->VALISSUED) || empty($mktg_catg_wise_targets['JPS'])) ? "0.00" : number_format(round((($mktg_catg_wise_sales['JPS']->VALISSUED/$mktg_catg_wise_targets['JPS'])*100),2),2) ;
                                            ?>
                                            <td style="height:30px; text-align: right;  color:black;">{{$achieved."%"}}</td>
                                            <td style="height:30px; text-align: right;  color:black;">{{!empty($month_mktg_catg_wise_sales['JPS']->VALISSUED)?round($month_mktg_catg_wise_sales['JPS']->VALISSUED/100000,2):"0.00"}}</td>
                                        </tr>
                                        <tr style="text-align: left;">
                                            <td style="height:30px; background-color: #e6ffcc;  color:black;">7</td>
                                          
                                            <td style="height:30px; text-align: left; background-color: #e6ffcc; color:black;"><a href="{{url('fmcg?check_status=1&terr_id='.$terr_id.'&'.'dealer_id_cus='.$dealer_id_cus.'&'.'div_code_cus='.$div_code_cus)}}" target="_blank">FMCG</a></td>
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
                                                $tt = round(array_sum($total_target_array)/100000,2);
                                                $ts = round(array_sum($total_sales_array)/100000,2);
                                                $divide_val = $tt;
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
                            


                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
                
                
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


<div class="modal fade rotate" data-backdrop="static" data-keyboard="false" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title">Cart Details </h4>

            </div>
            <div class="modal-body" id="modal-body">
                
            </div>
        </div>
    </div>
</div>


<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('msell/js/common.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('msell/js/jquery-ui.min.js')}}"></script>

<script>
function myFunction(str) {
  // alert(str);

        var result;
        var url_name;
        var qty_id=str.substr(3,3);

        
        var item_code = $('#item_code'+qty_id).val();
        var unit_conf = $('#unit_conf'+qty_id).val();
        src = "{{ route('autocomplete_search_url') }}";
        $("#qty"+qty_id).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {

                        term: request.term,
                        item_code:item_code,
                        unit_conf:unit_conf
                    },
                    success: function (data) {
                        result = data.result;
                        // console.log(result);
                        // url_name = result[0].title;
                        response(result);
                        var schem_hidden_variable = data.slabs;
                        $("#scheme_qty_with_free_qty_hidden"+qty_id).html(''); 
                        $("#scheme_qty_with_free_qty_hidden"+qty_id).html(schem_hidden_variable); 
                        $("#qty_remove_span"+qty_id).find('span').empty();

                    }
                });
            },
            select: function (e, ui) {

                           // alert();
            },

            change: function (e, ui) {

//                            alert("changed!");
            },
            minLength: 1,

        }).on('autocompleteselect', function (e, ui) {
            // console.log(ui.item.label);
            $("#qty_remove_span"+qty_id).find('span').empty();
            $('.ui-helper-hidden-accessible').empty();
            $("#qty_remove_span"+qty_id).find('div').empty();
            
            var t = $(this),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label ),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label );
            url_name2 = ( e.type == 'autocompleteresponse' ? ui.content[0].title : ui.item.title );
            free_qty1 = ( e.type == 'autocompleteresponse' ? ui.content[0].free_qty : ui.item.free_qty );
            // t.val(value);
            // var url_name = ui.item.label;
            // alert(url_name);
            // console.log(result);
            $("#qty"+qty_id).val(url_name2);
            
                var free_qty= document.getElementById("free_qty"+qty_id).value  = free_qty1 ;
            $('#scheme_qty_with_free_qty_hidden'+qty_id).html(url_name);
            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).val('');

            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).val(url_name);

            mulfunc("qty"+qty_id);

            // var $this = $(this);
            // // var $tr = $this.closest("tr");
            // if(event.keyCode == 13)
            // {
            //     $('.remarks'+qty_id).focus();
            //     event.preventDefault();
            //     return false;   
            // }


            var $this = $(this);
            var $tr = $this.closest("tr");
            if(event.keyCode == 13)
            {



                event.preventDefault();
                // Get all focusable elements on the page
                var $canfocus = $(':focusable');
                var index = $canfocus.index(this) + 1;
                if (index >= $canfocus.length) index = 0;
                $canfocus.eq(index).focus();
                // alert(1);
                // $tr.next().find('select.form-control.chosen-select.ui-autocomplete-input.item_name').focus();
                // // var te = $('select').chosen();
                //   // chosen-sel…omplete-input item_name
                // // console.log(te);
                // // $tr.next().te.focus();
                // // $tr.next().te.focus();
                // event.preventDefault();
                // return false;   
            }
            // onchange=" mulfunc(this.id);" 

            // $.ajaxSetup({
            //   headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //   }
            // });
            // $.ajax({
            //     type: "POST",
            //     url: domain + '/search_url_details',
            //     dataType: 'json',
            //     data: "url_name=" + url_name,
            //     success: function (data) 
            //     {
            //         if (data.code == 401) 
            //         {
            //            alert('Enter Module Name Doesnt Exist');
            //         }
            //         else if (data.code == 200) 
            //         {
            //             window.location = domain+'/'+ data.data_return;
                        
            //         }
            //     },
            //     complete: function () {
            //         // $('#loading-image').hide();
            //     },
            //     error: function () {
            //     }
            // });

            return false;
        }).autocomplete({
          
          multiselect: true,
          autoFocus: true,
          selectedControlId: 'txtKeyword_NewQuestion'
      });
}
</script>

<script>
   

    $(function(){
        $(document).on('keypress', 'input,select', function(e) {
          // var $this = $(this);
          // var $tr = $this.closest("tr");
          if(e.keyCode == 13)
            {


                e.preventDefault();
                // Get all focusable elements on the page
                var $canfocus = $(':focusable');
                var index = $canfocus.index(this) + 1;
                if (index >= $canfocus.length) index = 0;
                $canfocus.eq(index).focus(); 
            }
         });
     });

  
    $(document).ready(function() {
        // $(window).keydown(function(event){
        //     if(event.keyCode == 13) {
        //         console.log('successful');
        //         event.preventDefault(); // if it's inside <form> tag, you don't want to submit it
        //         // var $tr = $this.closest("tr");
        //         // $(this).next('input').focus();
        //         // $tr.next().find('input.item_name.ui-autocomplete-input').focus();
        //       event.preventDefault();
        //       return false;
        //     }
        // });
    });
    function test1(str_dat)
    {
        var str_dat = str_dat;
        var qty_id=str_dat.substr(3,3);
        // alert('1');$this
        var qt = $('#final_cal_qty_pcs'+qty_id).val();
        // alert(qt);
        if(qt == '')
        {
        // var qt = $('#qty'+qty_id).val('');
            // alert('1');
        }
        // if (final_cal_qty_pcs) {}
    }
    function autocompleteFunction(str)
    {
        // alert(str);
        

        var result;
        var url_name;
        var qty_id=str.substr(3,3);

        
        var item_code = $('#item_code'+qty_id).val();
        var unit_conf = $('#unit_conf'+qty_id).val();
        src = "{{ route('autocomplete_search_url') }}";
        $("#qty"+qty_id).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {

                        term: request.term,
                        item_code:item_code,
                        unit_conf:unit_conf
                    },
                    success: function (data) {
                        result = data.result;
                        // console.log(result);
                        // url_name = result[0].title;
                        response(result);
                        var schem_hidden_variable = data.slabs;
                        $("#scheme_qty_with_free_qty_hidden"+qty_id).html(''); 
                        $("#scheme_qty_with_free_qty_hidden"+qty_id).html(schem_hidden_variable); 
                        $("#qty_remove_span"+qty_id).find('span').empty();

                    }
                });
            },
            select: function (e, ui) {

                           // alert();
            },

            change: function (e, ui) {

//                            alert("changed!");
            },
            minLength: 1,

        }).on('autocompleteselect', function (e, ui) {
            // console.log(ui.item.label);
            $("#qty_remove_span"+qty_id).find('span').empty();
            $('.ui-helper-hidden-accessible').empty();
            $("#qty_remove_span"+qty_id).find('div').empty();
            
            var t = $(this),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label ),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label );
            url_name2 = ( e.type == 'autocompleteresponse' ? ui.content[0].title : ui.item.title );
            free_qty1 = ( e.type == 'autocompleteresponse' ? ui.content[0].free_qty : ui.item.free_qty );
            // t.val(value);
            // var url_name = ui.item.label;
            // alert(url_name);
            // console.log(result);
            $("#qty"+qty_id).val(url_name2);
            
                var free_qty= document.getElementById("free_qty"+qty_id).value  = free_qty1 ;
            $('#scheme_qty_with_free_qty_hidden'+qty_id).html(url_name);
            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).val('');

            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).val(url_name);

            mulfunc("qty"+qty_id);

            // var $this = $(this);
            // // var $tr = $this.closest("tr");
            // if(event.keyCode == 13)
            // {
            //     $('.remarks'+qty_id).focus();
            //     event.preventDefault();
            //     return false;   
            // }


            var $this = $(this);
            var $tr = $this.closest("tr");
            if(event.keyCode == 13)
            {



                event.preventDefault();
                // Get all focusable elements on the page
                var $canfocus = $(':focusable');
                var index = $canfocus.index(this) + 1;
                if (index >= $canfocus.length) index = 0;
                $canfocus.eq(index).focus();
                // alert(1);
                // $tr.next().find('select.form-control.chosen-select.ui-autocomplete-input.item_name').focus();
                // // var te = $('select').chosen();
                //   // chosen-sel…omplete-input item_name
                // // console.log(te);
                // // $tr.next().te.focus();
                // // $tr.next().te.focus();
                // event.preventDefault();
                // return false;   
            }
            // onchange=" mulfunc(this.id);" 

            // $.ajaxSetup({
            //   headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //   }
            // });
            // $.ajax({
            //     type: "POST",
            //     url: domain + '/search_url_details',
            //     dataType: 'json',
            //     data: "url_name=" + url_name,
            //     success: function (data) 
            //     {
            //         if (data.code == 401) 
            //         {
            //            alert('Enter Module Name Doesnt Exist');
            //         }
            //         else if (data.code == 200) 
            //         {
            //             window.location = domain+'/'+ data.data_return;
                        
            //         }
            //     },
            //     complete: function () {
            //         // $('#loading-image').hide();
            //     },
            //     error: function () {
            //     }
            // });

            return false;
        }).autocomplete({
          
          multiselect: true,
          autoFocus: true,
          selectedControlId: 'txtKeyword_NewQuestion'
      });
    }
    $('selector').autocomplete({selectFirst:true});
</script>
<script type="text/javascript">
    $(".chosen-select").chosen().on('chosen:showing_dropdown', function() {
        var test_variable = $(this).closest("select").attr('id');
        var acc_id=test_variable.substr(9,5);
        // alert(test_variable);
        
        var item_codes = document.getElementsByName('item_code[]');
        var catg = $('#catg').val();
        var catg = $('#catg').val();

        var item_codes_str = '';
        for (let i = 0; i < item_codes.length; i++)
        {
            item_codes_str += "," + item_codes[i].value;

        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url:  '{{route("dms_return_rate_on_the_behalf_of_product")}}',
            dataType: 'json',
            data: {'item_code': 0, 'dealer_id': 0,'item_codes_str': item_codes_str,'catg':catg},
            success: function (data) 
            {
                if(data.code == 401)
                {
                    $('#item_code'+acc_id).html('');
                    var template1 = ''; 
                    var template1 = "<option value=''>==Select item (name or code)==</option>";
                    $.each(data.item_aum_mast_array, function (key, value) {
                                    template1 += '<option style="text-align:left;" value="' + key + '" >' + value + '</option>';
                            });
                    $('#item_code'+acc_id).append(template1).trigger('chosen:updated');




                }
                else if(data.code == 200)
                {   
                    $('#item_code_custom'+acc_id).html('');
                    var template1 = ''; 

                    template1 = "<select class='form-control chosen-select-test qwerty' style='width:360px; color:black; font-weight:bold;' name='item_code[]' id='item_code"+acc_id+"' onchange='return return_rate_details(this.id)'><option value=''>==Select item (name or code)==</option>";
                    $.each(data.item_aum_mast_array, function (key, value) {
                                    template1 += '<option value="' + key + '" >' + value + '</option>';
                            });
                    template1 += '</select>'; 
                    $('#item_code_custom'+acc_id).html(template1);
                }
            }
        });

        // alert(1);
        
    });
</script>
<script type="text/javascript">
    
    function return_rate_details(str)
    {
        var d=str.substr(9,3);
        var item_code= document.getElementById("item_code"+d).value;
        var auth_id = '{{$auth_id}}';

        // alert('1');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url:  '{{route("dms_return_rate_on_the_behalf_of_product")}}',
            dataType: 'json',
            data: {'item_code': item_code, 'dealer_id': auth_id},
            success: function (data) 
            {
                if(data.code == 401)
                {

                }
                else if(data.code == 200)
                {   
                    // con
                    // if()
                    // alert(d);
                     var template12 = "";
                    // $('#item_code'+d).find('option').remove().end();
                    // alert();
                   

                    $('#item_code_custom'+d).html('');
                    // var set_text =  $('#item_code'+d+' option:selected').text();
                    // alert(set_text);

                    var template1 = "<select class='form-control ' style='color:black; font-weight:bold;'  name='item_code[]' id='item_code"+d+"' onchange='return return_rate_details(this.id);'>";
                        
                        template1 += '<option selected value='+item_code+'>'+data.item_mast_name+'</option></select>'; 
                    $('#item_code_custom'+d).append(template1);

                    document.getElementById("qty"+d).value = '';
                    document.getElementById("free_qty"+d).value = '';
                    document.getElementById("final_cal_qty_pcs"+d).innerHTML = '';
                    $("#scheme_qty_with_free_qty_hidden"+d).html('');
                    document.getElementById("total_rs"+d).innerHTML= '';
                    document.getElementById("total_rs_hidden_v"+d).value= '0';
                    $('#product_rate'+d).val(data.result);
                    $('#unit_configuration_um'+d).html('');
                    var append_data = '<input type ="text" readonly  class="form-control" name="unit_configuration[]" id="unit_conf'+d+'" value='+data.selection_size_um+' style="width:50px; color:black; font-weight:bold;">';
                    $('#unit_configuration_um'+d).html(append_data);

                    // $('#qty'+d).val('').autocomplete({
                    //     source: function (request, response) {
                    //         $.ajax({
                    //             url: "wert",
                    //             type: "POST",
                    //             data: "{'input': '" + 0 + "', 'limit': '10', 'maxAlternateTerms' : '3', }"
                    //         });
                    //     }
                    // });
                    // test1('qty'+d);
                    $('#qty'+d).focus();



                // $tr.next().find('input.item_name.ui-autocomplete-input').focus();




                   
                }
            }
        });

    }

    function mulfunc(str2)
    {
        var d=str2.substr(3,5);
        var grand_total_qty = new Array();
        var unit = [];
        // alert('unit_conf');
        var unit_data_conf = $("#unit_conf"+d).val();
        // alert(unit_data_conf);
        var final_conversion_set = 0;
        // document.getElementById("product_rate"+d).value = '';
        // document.getElementById("qty"+d).value= '';
        // document.getElementById("free_qty"+d).value= '';
        var rate= document.getElementById("product_rate"+d).value;
        var qty= document.getElementById("qty"+d).value;
        var free_qty= document.getElementById("free_qty"+d).value;
        // alert(free_qty);
        // var qty = Array.from(qty);
        // var qty = JSON.parse("[" + qty + "]");
        // varqty = Object.assign([], string);
        // alert(qty)
        unit = <?= $converstion_unit_item_code ?>;
        if(unit_data_conf == 'PCS')
        {
             
             // console.log(unit);
             final_conversion_set = qty;

        }
        else
        {
            var item_code_custom = $('#item_code'+d).val();

             // final_conversion_set = unit[item_code_custom]*qty;
             final_conversion_set = qty;
            // $('#'+d).val() = final_conversion_set;
            // alert(final_conversion_set);
        }
        
        if(free_qty == '')
        {
            free_qty = 0;
        // alert(free_qty);

        }
        var f_qty = final_conversion_set-free_qty;
        var total = (rate*f_qty);
        // alert(total);
        document.getElementById("final_cal_qty_pcs"+d).innerHTML = '';
        document.getElementById("total_rs"+d).innerHTML = '';
        document.getElementById("total_rs_hidden_v"+d).innerHTML = '';
        // $("#scheme_qty_with_free_qty_hidden"+d).html('');

        // document.getElementById("scheme_qty_with_free_qty_hidden"+d).innerHTML = '';
        // var schem_hidden_variable = qty+ ' ( '+final_conversion_set+' + '+free_qty+' = '+ (parseInt(final_conversion_set)+parseInt(free_qty)) +' )';
        // // alert(schem_hidden_variable);
        // $("#scheme_qty_with_free_qty_hidden"+d).val(schem_hidden_variable); 
        document.getElementById("final_cal_qty_pcs"+d).innerHTML= final_conversion_set;
        document.getElementById("total_rs"+d).innerHTML= total.toFixed(2);
        document.getElementById("total_rs_hidden_v"+d).value= total.toFixed(2);


        var total_qty = document.getElementsByName('qty[]');
        grand_total_qty = new Array();
        for (var po = 0; po < total_qty.length; po++)
        {
            if( total_qty[po].value != 0)
            {
                grand_total_qty.push(parseInt(total_qty[po].value));

            }

        }   

        // var sale_value_cart = 0;
        // var count_prod_cart = 0;

        var sale_value_cart = <?= $sale_value_cart ?>;
        var count_prod_cart = <?= $count_prod_cart ?>;

        var final_append_value =parseInt(grand_total_qty.length)+parseInt(count_prod_cart); 
        // document.getElementByI'  d("final_qty").innerHTML= '';
        $('#final_qty').html('');
        $('#final_qty').html(final_append_value);
        // document.getElementById("final_qty").innerHTML= grand_total_qty;
    }

        var cust_id = 11;
        // console.log(cust_id);

        function addfunction(str)
        {
            var grand_total_rs ;
            // var y=str.substr(5,3);
            // var x=1;
            // if(cust_id > 1)
            // {
            //     var d = parseInt(y)+parseInt(x);
            // }
            // else
            // {
            //     var d = parseInt(y);


            // console.log(sr_no);

            var product_filter = `<select class='form-control  chosen-select' style="width:360px; color:black; font-weight:bold;" name="item_code[]" id="item_code${cust_id}"  onchange="return return_rate_details(this.id)" >
                                        
                                    </select>`;

            var product_rate = `<input type="text" readonly="" name="product_rate[]"  id="product_rate${cust_id}" class="form-control " style="color:black; font-weight:bold;">`;
            var unit_configuration = `<td width="50px" id="unit_configuration_um${cust_id}" ></td>`;
            var qty = `<input style="width:230px;" type="text" autocomplete="off" name="qty[]" id="qty${cust_id}"  class="form-control item_qty ui-autocomplete-input" onkeypress ="autocompleteFunction(this.id);" onchange="return test1(this.id);" onkeydown ="return autocompleteFunction(this.id);" style="color:black; font-weight:bold;">`;
            var free_qty_hidden = `<input type="hidden" name="free_qty[]" id="free_qty${cust_id}"  style="color:black; font-weight:bold;">`;
            var final_cal_qty_pcs_hidden = `<input type="hidden" name="final_cal_qty_pcs[]"  style="color:black; font-weight:bold;">`;
            var total_rs_hidden =  `<input type="hidden" name="total_rs[]" id = "total_rs_hidden_v${cust_id}" style="color:black; font-weight:bold;">`;
            var final_cal_qty_pcs = ``;
            var total_rs =  ``;

            var final_cal_qty_pcs_temp = `final_cal_qty_pcs${cust_id}`;
            var total_rs_temp = `total_rs${cust_id}`;


            var scheme_qty_with_free_qty = `<input type="hidden"  name="scheme_qty_with_free_qty[]" id="input_scheme_qty_with_free_qty_hidden${cust_id}" style="color:black; font-weight:bold;" >`;
            var remarks = `<input type="text" name="remarks[]" autocomplete="off"  id="product_rate${cust_id}" class="form-control "  style="color:black; font-weight:bold;">`;

            var template = ('<tr>'+free_qty_hidden+final_cal_qty_pcs_hidden+total_rs_hidden+scheme_qty_with_free_qty+'<td width="300px" id="item_code_custom'+cust_id+'">'+product_filter+'</td><td width="70px">'+product_rate+'</td>'+unit_configuration+'<td  id="qty_remove_span'+cust_id+'">'+qty+'</td><td style="color:black; font-weight:bold;" width="50px" id ='+final_cal_qty_pcs_temp+'>'+final_cal_qty_pcs+'</td><td style="color:black; font-weight:bold;" width="50px" id='+total_rs_temp+'>'+total_rs+'</td><td width="300px" id="scheme_qty_with_free_qty_hidden'+cust_id+'" style="color:black; font-weight:bold;"> </td><td  width="300px">'+remarks+'</td><td width="30px" ><i id=sr_no'+cust_id+' title="more" class="fa fa-plus addrow" aria-hidden="true" onclick=" addfunction(this.id); chosenFunction();"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></tr>');
            $('.mytbody_demand_order').append(template);
            cust_id++;


            var total_rs_above = document.getElementsByName('total_rs[]');

            for (var po = 0; po < total_rs_above.length; po++)
            {
                grand_total_rs += parseFloat(total_rs_above[po].value);

            }   
            // alert(grand_total_rs);

            $('#final_amont_value').html('');
            $('#final_amont_value').html(grand_total_rs);
            // document.getElementById("total_rs").innerHTML= '';
            // document.getElementById("total_rs").innerHTML= grand_total_rs;
        }


</script>
<script type="text/javascript">


        $('#dynamic-table').on('click','.removenewrow',function(){

              var table = $(this).closest('table');
              var i = table.find('.mytbody_demand_order1').length;                 

              if(i==1)
              {
                 return false;
              }

             $(this).closest('tr').remove();
        });
        
        
    </script>

<script type="text/javascript">              

          $(function(){
              if(!ace.vars['touch']) {
                   $('.chosen-select').chosen({allow_single_deselect:true});
              }
              else
              {
                   $('.chosen-select').chosen({allow_single_deselect:true});

              }
          })
        </script>
<script>
    function chosenFunction(id)
    {
        // alert('1');
            // $(".chosen-select").chosen();
            // $(".chosen-select").trigger("chosen:updated");
            $(".chosen-select").chosen().on('chosen:showing_dropdown', function() {
              // alert('No need to go crazy');;
                var test_variable = $(this).closest("select").attr('id');
                var acc_id=test_variable.substr(9,5);
                var catg = $('#catg').val();
                // alert(acc_id);
                
                var item_codes = document.getElementsByName('item_code[]');

                var item_codes_str = '';
                for (let i = 0; i < item_codes.length; i++)
                {
                    item_codes_str += "," + item_codes[i].value;

                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "post",
                    url:  '{{route("dms_return_rate_on_the_behalf_of_product")}}',
                    dataType: 'json',
                    data: {'item_code': 0, 'dealer_id': 0,'item_codes_str': item_codes_str,'catg':catg},
                    success: function (data) 
                    {
                        if(data.code == 401)
                        {
                            $('#item_code'+acc_id).html('');
                            var template1 = ''; 
                            template1 = "<option value=''>==Select item (name or code)==</option>";
                            $.each(data.item_aum_mast_array, function (key, value) {
                                            template1 += '<option style="text-align:left;" value="' + key + '" >' + value + '</option>';
                                    });
                            $('#item_code'+acc_id).append(template1).trigger('chosen:updated');
                        }
                        else if(data.code == 200)
                        {   
                            $('#item_code_custom'+acc_id).html('');
                            var template1 = ''; 
                             template1 = "<select class='form-control chosen-select-test qwerty' style='width:360px;' name='item_code[]' id='item_code"+acc_id+"' onchange='return return_rate_details(this.id)'><option value=''>==Select item (name or code)==</option>";
                            $.each(data.item_aum_mast_array, function (key, value) {
                                            template1 += '<option value="' + key + '" >' + value + '</option>';
                                    });
                            template1 += '</select>'; 
                            $('#item_code_custom'+acc_id).html(template1);
                        }
                    }
                });


            });
            // console.log('1');
        // });
    }
    //     $('.addrow').click(function () {
    // // alert('1');
            $(".chosen-select12").chosen();
            $(".chosen-select12").trigger("chosen:updated");
    //         $(".chosen-select").chosen().on('chosen:showing_dropdown', function() {
    //           alert('No need to go crazy');;
    //         });
    //         // console.log('1');
    //     });

    // $(".chosen-select").chosen();
       
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


        $('.vnumerror').keyup(function()
        {
            var yourInput = $(this).val();
            re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(yourInput);
            if(isSplChar)
            {
                // var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                // $(this).val(no_spl_char);
            }
        });
        $('.cart-modal').click(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "{{route('return_cart_data_for_modal')}}",
                dataType: 'json',
                data: {'range': 40},
                success: function (datar) {
                    // alert('1');
                    $('#modal-body').html(data.responseText);
                    
                    // alert('1');

                },
                complete: function (data) {
                    // $('#loading-image').hide();
                    // console.log(data.responseText);
                    $('#modal-body').html(data.responseText);
                    // alert('1');
                },
                error: function (data) {
                    

                }
            });
        });
    </script>

@endsection
