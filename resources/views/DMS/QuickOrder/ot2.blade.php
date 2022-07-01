@extends('layouts.core_php_heade')

@section('dms_body')

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
</style>

    <div class="main-content" style="   ">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781; color: black;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">{{Lang::get('common.order_details_dms')}} </a>
                    </li>

                    <li class="active" style="color: black;">{{Lang::get('common.quick_order')}}</li>
                    <li class="active" style="color: black;">Marketing Category OT2</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content"  style=" font-family: 'Times New Roman', Times, serif; ">
                <br>
                <div class="row container-fluid" >
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" >
                                <table id="dynamic-1table" class="table table-bordered ">
                                    <thead >
                                        <tr>
                                            <th colspan="11" style="background-color: #90d781;color: black;text-align: center; font-size: 40px; font-weight:100px; ">
                                                <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;">Marketing Category OT2</b>
                                            </th>
                                            <tr >
                                            <th class="center" rowspan ="2" style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">
                                                {{Lang::get('common.s_no')}}
                                            </th>
                                            <th style="width: 250px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" rowspan="2">Category</th>
                                            <th style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;" rowspan="2">UM</th>
                                            <th style="background-color: #237a00c4;color: black; height: 10px; " colspan="4"><b style="font-weight: bolder; font-size: 17px;">ANNUAL</b></th>
                                            <th style="background-color: #237a00c4;color: black; height: 10px; " colspan="2"><b style="font-weight: bolder; font-size: 17px;">CURRENT QUARTER</b></th>
                                            <th style="background-color: #237a00c4;color: black; height: 10px; " colspan="2"><b style="font-weight: bolder; font-size: 17px;">CURRENT MONTH</b></th>
                                        </tr>
                                        <tr>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Target<br> (in rupees)</th>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Sale<br> (in qty)</th>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Sales<br> (in rupees)  </th>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Achieved<br>(%)</th>
                                            <!-- quater starts here -->
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Achieved<br> (in qty)</th>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Sales<br> (in rupees)</th>
                                            <!-- current month starts here -->
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Achieved<br> (in qty)</th>
                                            <th style="width: 80px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">Sales<br> (in rupees)</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: left;">
                                        @if(!empty($prod_catg_mast_array))
                                        @foreach($prod_catg_mast_array as $key=>$value)
                                        <?php
                                        $total_ann_target[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        $total_ann_sale_qty[] = !empty($prod_wise_sales_mast_data_qty[$value->PROD_CATG])?$prod_wise_sales_mast_data_qty[$value->PROD_CATG]:0;
                                        $total_ann_sale_val[] = !empty($prod_wise_sales_mast_data_val[$value->PROD_CATG])?$prod_wise_sales_mast_data_val[$value->PROD_CATG]:0;
                                        
                                        $total_ann_sale_qty_m[] = !empty($month_prod_wise_sales_mast_data_qty[$value->PROD_CATG])?$month_prod_wise_sales_mast_data_qty[$value->PROD_CATG]:0;
                                        $total_ann_sale_val_m[] = !empty($month_prod_wise_sales_mast_data_val[$value->PROD_CATG])?$month_prod_wise_sales_mast_data_val[$value->PROD_CATG]:0;

                                        $total_ann_sale_qty_q[] = !empty($quad_prod_wise_sales_mast_data_qty[$value->PROD_CATG])?$quad_prod_wise_sales_mast_data_qty[$value->PROD_CATG]:0;
                                        $total_ann_sale_val_q[] = !empty($quad_prod_wise_sales_mast_data_val[$value->PROD_CATG])?$quad_prod_wise_sales_mast_data_val[$value->PROD_CATG]:0;

                                        $total_ann_achieved[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        $total_currq_target[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        $total_currq_sales[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        $total_currq_achieved[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        $total_currm_sales[] = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                        ?>
                                        <tr style="text-align: left;">
                                            <td>{{$key + 1}}</td>
                                            <td style="text-align: left;">{{$value->PROD_CATG_NAME}}</td>
                                            <td style="text-align: left;">{{!empty($um_array[$value->PROD_CATG])?$um_array[$value->PROD_CATG]:"N/A"}}</td>
                                            <td style="text-align: right;">{{!empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0}}</td>
                                            <td style="text-align: right;">{{!empty($prod_wise_sales_mast_data_qty[$value->PROD_CATG])?round($prod_wise_sales_mast_data_qty[$value->PROD_CATG],2):0}}</td>
                                            <td style="text-align: right;">{{!empty($prod_wise_sales_mast_data_val[$value->PROD_CATG])?round($prod_wise_sales_mast_data_val[$value->PROD_CATG]/100000,2):0}}</td>
                                            <?php
                                            $a = !empty($prod_wise_target_mast[$value->PROD_CATG])?$prod_wise_target_mast[$value->PROD_CATG]:0;
                                            $b = !empty($prod_wise_sales_mast_data_qty[$value->PROD_CATG])?$prod_wise_sales_mast_data_qty[$value->PROD_CATG]:0;
                                                $achieved = ($a === 0 || $b === 0) ? 0 : round((($b/$a)*100), 2)."%" ;
                                            ?>
                                            <td style="text-align: right;">{{$achieved}}</td>
                                            <!-- quater starts here -->
                                            <td style="text-align: right;">{{!empty($quad_prod_wise_sales_mast_data_qty[$value->PROD_CATG])?round($quad_prod_wise_sales_mast_data_qty[$value->PROD_CATG],2):0}}</td>
                                            <td style="text-align: right;">{{!empty($quad_prod_wise_sales_mast_data_val[$value->PROD_CATG])?round($quad_prod_wise_sales_mast_data_val[$value->PROD_CATG]/100000,2):0}}</td>
                                            <!-- current months starts here -->
                                            <td style="text-align: right;">{{!empty($month_prod_wise_sales_mast_data_qty[$value->PROD_CATG])?round($month_prod_wise_sales_mast_data_qty[$value->PROD_CATG],2):0}}</td>
                                            <td style="text-align: right;">{{!empty($month_prod_wise_sales_mast_data_val[$value->PROD_CATG])?round($month_prod_wise_sales_mast_data_val[$value->PROD_CATG]/100000,2):0}}</td>

                                        </tr>
                                        @endforeach
                                        @endif
                                        <tr style="text-align: left;">
                                            <td colspan="3">Total(Amount In Lacs)</td>
                                            <?php
                                                $total_ann_target_1 = array_sum($total_ann_target);
                                                // dd($ot2_wise_target_data);
                                                $total_ann_target_1 = ($total_ann_target_1 == 0) ? (!empty($ot2_wise_target_data)?round($ot2_wise_target_data/100000,2):0) : $total_ann_target_1;
                                                $total_ann_sale_qty_1 = array_sum($total_ann_sale_qty);
                                                $total_ann_sale_val_1 = array_sum($total_ann_sale_val);
                                                
                                                $total_ann_sale_val_1_m = array_sum($total_ann_sale_val_m);
                                                $total_ann_sale_qty_1_m = array_sum($total_ann_sale_qty_m);

                                                $total_ann_sale_val_1_q = array_sum($total_ann_sale_val_q);
                                                $total_ann_sale_qty_1_q = array_sum($total_ann_sale_qty_q);


                                                $total_ann_achieved_1 = array_sum($total_ann_achieved);
                                                $total_currq_target_1 = array_sum($total_currq_target);
                                                $total_currq_sales_1 = array_sum($total_currq_sales);
                                                $total_currq_achieved_1 = array_sum($total_currq_achieved);
                                                $total_currm_sales_1 = array_sum($total_currm_sales);
                                            ?>
                                            <td style="text-align: right;">{{$total_ann_target_1}}</td>
                                            <td style="text-align: right;">{{round($total_ann_sale_qty_1,2)}}</td>
                                            <td style="text-align: right;">{{round(($total_ann_sale_val_1/100000),2)}}</td>
                                            <?php
                                            $x = !empty($total_ann_target_1)?$total_ann_target_1:0;
                                            $y = !empty($total_ann_sale_val_1)?($total_ann_sale_val_1/100000):0;
                                            $achieved_1 = (empty($x) || empty($y)) ? 0 : round((($y/$x)*100), 2)."%" ;
                                            ?>
                                            <td style="text-align: right;">{{$achieved_1}}</td>
                                            
                                            <td style="text-align: right;">{{round($total_ann_sale_qty_1_q,2)}}</td>
                                            <td style="text-align: right;">{{round(($total_ann_sale_val_1_q/100000),2)}}</td>

                                            <td style="text-align: right;">{{round(($total_ann_sale_qty_1_m),2)}}</td>
                                            <td style="text-align: right;">{{round(($total_ann_sale_val_1_m/100000),2)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--  -->
    </div>
</div>
    
</body>


@endsection