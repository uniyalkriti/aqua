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
ul.ui-autocomplete {
    z-index: 1100;
}
    .ui-autocomplete {
    overflow: auto;
    height: 200px;
    width: 200px;
}
.pac-container {
    z-index: 9999999999 !important;
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
</style>

    <div class="main-content" style="   ">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781; color: black;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">{{Lang::get('common.order_details_dms')}} </a>
                    </li>

                    <li class="active" style="color: black;">{{Lang::get('common.order_history')}}</li>
                    <li class="active" style="color: black;">{{Lang::get('common.pro_forma_invoice')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content"  style=" font-family: 'Open Sans' ">
                
                <br> 
                <?php
                $party_name = !empty($party_name)?$party_name:"NA";
                $order_date = !empty($order_date)?date('d-M-Y',strtotime($order_date)):strtoupper(date('d-M-Y'));
                $order_id = !empty($order_id)?$order_id:0;
                $sum_rs_value = array();
                $sum_bx_value = array();
                $sum_pc_value = array();
                $gt_rs_value = array();
                $sum_trade_incentive = array();
                $sum_at_discount = array();
                $sum_trade_incentive_1 = 0;
                $sum_at_discount_1 = 0;
                $order_remark = !empty($order_remark)?$order_remark:"NA";
                ?>
                <div class="row container-fluid" >
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" >
                                
                                <table class="table table-bordered " style="background-color: #f8f8f8; " >
                                    <thead >
                                    <tr>
                                        <th colspan="12" style="background-color: white;color: black;text-align: center; font-size: 30px; font-weight:100px; ">
                                            <b style="background-color: ; color: black; font-family: 'Open Sans'"> <u>PROFORMA INVOICE</u> </b><br><span style="font-size: 15px;">"The Goods will be supplied subject to stock availability at the time of actual dispatch. Hence, Incentive/trade schemes if applicable and if due, will be given on the basis of final invoices only."</span><br><span style="font-size: 15px;"></span><span style="font-size: 15px;">Party Name: {{$party_name}}</span><br><span style="font-size: 15px;">Order Date: {{$order_date}}</span><br><span style="font-size: 15px;"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; " class="center" >
                                            {{Lang::get('common.s_no')}}
                                        </th>
                                        <th style="width:400px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;">{{Lang::get('common.item_name')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0;  ">{{Lang::get('common.wholesale_rate')}}</th>
                                        <th style="width: 100px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.sale_in_box_pcs')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.qty')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.pcs')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.billed_qty')}}</th>
                                        <th style="width: 60px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.free_qty')}}</th>
                                        <th style="width: 100px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.value')}}</th>
                                        <th style="width: 400px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">{{Lang::get('common.remark')}}</th>
                                        <th style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">Edit</th>
                                        <th style="width: 50px; background-color: #90d781; font-family: 'Open Sans';  color: #000000e0; ">Delete</th>
                                    </tr>
                                    </thead>
                                    @foreach($mktg_cat_array as $k => $v)
                                        @foreach($final_out as $fink => $finv)
                                            @if(!empty($finv[$k]) &&  COUNT($finv[$k])>0)
                            
                                                <tr>
                                                    <td colspan="12" align="left" style="text-align: left; background-color: #237a00c4;color: black; height: 10px; " ><b style="font-weight: bolder; font-size: 15px;">{{$v}}</b></td>
                                                </tr>
                                                
                                                <tbody style=" border: 1px solid black;">
                                                @foreach($finv[$k] as $set => $set_v)
                                                    <input type="hidden" name="update_order_id" id="update_order_id{{$set_v['ITEM_CODE']}}" value="{{$set_v['order_id']}}">
                                                    <?php
                                                    // dd($set_v);

                                                        // $sum_rs_value[] = !empty($set_v['total_rs'])?$set_v['total_rs']:0;
                                                        $a = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $b = !empty($item_aum_mast_array[$set_v['ITEM_CODE']])?$item_aum_mast_array[$set_v['ITEM_CODE']]:0;  
                                                        if ($b == 0) {
                                                            $sum_bx_value[] = 0;
                                                        }
                                                        else{
                                                            $sum_bx_value[] = round(((int)$a/$b),2);
                                                        }
                                                        $sum_pc_value[] = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $gt_rs_value[] = !empty($set_v['total_rs'])?$set_v['total_rs']:0;
                                                        $sum_trade_incentive[] = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $sum_at_discount[] = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;
                                                        $minus_trade_rate = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $minus_atd_rate = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;
                                                        $sum_rs_value[] = ($set_v['total_rs']-$minus_trade_rate-$minus_atd_rate);
                                                     ?>
                                                 <tr style="height: 10px;">
                                                     <td style="height: 10px;">{{$set+1}}</td>
                                                     <td style="text-align: left; height: 10px;" id="ITEM_NAME{{$set_v['ITEM_CODE']}}">{{$set_v['ITEM_NAME']}}</td>
                                                     <td  id="rate{{$set_v['ITEM_CODE']}}" style="height: 10px;">{{$set_v['rate']}}</td>
                                                     <td style="text-align: left; height: 10px;" id="order_unit{{$set_v['ITEM_CODE']}}">{{$set_v['order_unit']}}</td>
                                                     
                                                     @if($set_v['order_unit'] == 'BOX')
                                                     <td style="height: 10px;" id="quantity{{$set_v['ITEM_CODE']}}">{{(int)$set_v['quantity']/$converstion_unit_item_code[$set_v['ITEM_CODE']]}}</td>
                                                     @else
                                                     <td style="height: 10px;" id="quantity{{$set_v['ITEM_CODE']}}">{{(int)$set_v['quantity']}}</td>

                                                     @endif

                                                     <td style="height: 10px;" id="unit_conf{{$set_v['ITEM_CODE']}}">{{$set_v['quantity']}}</td>
                                                     <?php
                                                     $billed_qty = (int)$set_v['quantity'] - $set_v['free_qty'];
                                                     ?>
                                                     <td style="height: 10px;" >{{$billed_qty}}</td>
                                                     <td style="height: 10px;" id="free_qty_upper{{$set_v['ITEM_CODE']}}">{{$set_v['free_qty']}}</td>
                                                     <td style="text-align: right; height: 10px;" id="total_rs_upper{{$set_v['ITEM_CODE']}}">{{(number_format(round($set_v['total_rs'],2),2))}}</td>
                                                     <td style="text-align: left; padding-left: 20px; height: 10px;">{{$set_v['remarks']}}</td>
                                                     <td style="height: 10px;"><i  title="Edit Order" id="fetch_id{{$set_v['ITEM_CODE']}}" data-toggle="modal" onclick ="return modal_box(this.id);" data-target="#myModal"class="user-modal fa fa-pencil  btn-info btn-sm" ></i></td>
                                                    <td style="height: 10px;">
                                                        <form action="{{url('dms_order_delete_function')}}" method="post" enctype="multipart/form-data">
                                                            {!! csrf_field() !!}
                                                            <input id="id{{$set_v['ITEM_CODE']}}"  type="hidden" name="id" value="{{$set_v['pid']}}">
                                                            <input id="order_id{{$set_v['ITEM_CODE']}}"  type="hidden" name="order_id" value="{{$set_v['order_id']}}">
                                                            <input id="product_id{{$set_v['ITEM_CODE']}}"  type="hidden" name="product_id" value="{{$set_v['ITEM_CODE']}}">
                                                            <button type="submit" style="background-color: transparent; border-color: transparent;"><i class="fa fa-trash  btn-danger btn-sm"></i></button>
                                                        </form>
                                                    </td>
                                                 </tr>
                                                 @endforeach   
                                                
                                                <?php
                                                    $sum_rs_value_1 = array_sum($sum_rs_value);
                                                    $sum_bx_value_1 = array_sum($sum_bx_value);
                                                    $sum_pc_value_1 = array_sum($sum_pc_value);
                                                    $sum_trade_incentive_1 = array_sum($sum_trade_incentive);
                                                    $sum_at_discount_1 = array_sum($sum_at_discount);
                                                ?>
                                                <tr>
                                                    <td colspan="12" style="font-size: 17px;">
                                                    <span id="final_bx_value" >Total Pieces : {{$sum_pc_value_1}}</span>
                                                    <span id="final_amont_value" style="padding-left: 20px; padding-right: 20px;">Total Boxes : {{$sum_bx_value_1}}</span>
                                                    <span id="final_gt_value" >Net sale : {{number_format($sum_rs_value_1,2)}}</span>
                                                    </td>
                                                </tr>
                                                    <?php
                                                        $sum_rs_value = array();
                                                        $sum_bx_value = array();
                                                        $sum_pc_value = array();
                                                    ?>                                            
                                            @endif
                                        @endforeach
                                    @endforeach
                                         <tr>
                                            <td colspan="8"></td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; text-align: left;">
                                                <?php
                                                $gt_rs_value = array_sum($gt_rs_value);
                                                ?>
                                                <b style="text-align: right;">Grand Total Value : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format($gt_rs_value,2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" rowspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Discount Details</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Trade Incentive (-) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{$trade_incentive = number_format(round($sum_trade_incentive_1,2),2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>AT Discount (-) : <span></span></b>
                                            </td>
                                            <td align="right" style="text-align: right;"><span style="text-align: right;">{{$at_discount = number_format(round($sum_at_discount_1,2),2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                // dd($trade_incentive);
                                                $trade_incentive = $sum_trade_incentive_1;
                                                $at_discount = $sum_at_discount_1;
                                                $total_discount =($sum_trade_incentive_1) + ($sum_at_discount_1);
                                                ?>
                                                <b>Total Discount (-) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($sum_trade_incentive_1,2) + round($sum_at_discount_1,2),2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td  colspan="8" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Order Value to be consider for Incentive</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                $total_net_order_value = $gt_rs_value - $total_discount;
                                                ?>
                                                <b>Total Net Order Value : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($total_net_order_value,2),2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td  colspan="8" rowspan="2" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Note: Order Amount/Scheme applied may change at the time of final order processing.</b>
                                            </td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Total Tax : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($sum_at_discount_1,2),2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <?php
                                                $total_amount_appx = $total_net_order_value + ($sum_at_discount_1);
                                                ?>
                                                <b>Total Amount (Approx) : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format(round($total_amount_appx),2)}}</span></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="8" align="left" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Order Remark : <span>{{$order_remark}}</span> </b>
                                            </td>
                                            <td colspan="8">
                                                <form action="{{url('store_final')}}" method="post" enctype="multipart/form-data" id="dms_order_dispatch_form">
                                                    {!! csrf_field() !!}
                                                    <div class="fuild-container" style=" padding-top: -10px; ">
                                                        <div class="row">
                                                                <input type="hidden" placeholder ="Enter Remarks" class="form-control " name="order_id_for_use" value="{{$order_id_for_use}}" style="height: 45px; ">
                                                             
                                                            
                                                            <div class="col-md-12" style="padding-left: 150px; text-align: right;">
                                                                <a href="{{url('Order-details/create')}}">
                                                                    <button type="button" class="   button" name= "revert_to_order" value="1" >Add More
                                                                    </button>
                                                                </a>
                                                                <button type="submit" class="   button"  >Process Order 
                                                                 </button>
                                                                <div id="submit_dispatch">
                                                                  
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </form>
                                            </td>
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
<div class="modal fade rotate" data-backdrop="static" data-keyboard="false" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title">Update Order Quantity </h4>

            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="post" id="assign-beat" action="{{route($current_menu.'.store')}}">
                    {!! csrf_field() !!}
                    <table class="table table-bordered " style="background-color: #f8f8f8; " >
                        <tr>
                            <td style="background-color: #4caf50; color: white; width: 200px;">Item Name</td>
                            <td style="background-color: #4caf50; color: white;">Rate</td>
                            <td style="background-color: #4caf50; color: white;">Sale in Box/Pieces</td>
                            <td style="background-color: #4caf50; color: white;">Order Quantity</td>
                            <td style="background-color: #4caf50; color: white;">Qty (PCS)</td>
                            <td style="background-color: #4caf50; color: white;">Total (Rs.)</td>
                            <td style="background-color: #4caf50; color: white;">Scheme(Qty + FREE) IN PCS</td>
                        </tr>
                        <tbody>
                            <tr>
                                
                                <input type="hidden" name="update_qty_status" value="1" >
                                <input type="hidden" name="update_product_id" id="update_product_id"  >
                                <input type="hidden" name="update_order_id_c" id="update_order_id_c"  >
                                <input type="hidden" name="update_order_qty" id="id1" >
                                <input type="hidden" name="update_order_qty" id="order_id1" >
                                <input type="hidden" name="update_order_qty" id="item_code1" >
                                <input type="hidden" name="update_product_rate" id="product_rate1"  >
                                <td id="item_name" style="width: 200px;"></td>
                                <td id="product_rate_put"></td>
                                <td id="unit_conf1"></td>
                                <td id="qty_remove_span1"><input type="text" name="update_qty" id="qty1" onkeydown ="autocompleteFunction(this.id);"   ></td>
                                <input type="hidden" name="update_free_qty" id="free_qty1"  >
                                <input type="hidden" name="update_final_cal_qty_pcs"  >
                                <input type="hidden" name="update_total_rs" id="total_rs_hidden_v1">


                                
                                <td  id ="final_cal_qty_pcs1"></td>
                                <td  id="total_rs1"></td>
                                <td id="input_scheme_qty_with_free_qty_hidden1"></td>
                               
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <div class="col-md-6" style=" text-align: left;">
                                        
                                        <button type="submit" class="   button"  data-dismiss="modal">CLOSE 
                                         </button>
                                        
                                    </div>
                                    <div class="col-md-6" style="padding-left: 150px; text-align: right;">
                                        
                                        <button type="submit" class="   button"  >Update Order 
                                         </button>
                                        
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                                        
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function modal_box(str)
    {
        // fetch_id
        // alert(str);
        var qty_id=str.substr(8,16);
        // alert(qty_id);
        var ITEM_NAME = $('#ITEM_NAME'+qty_id).html();
        var rate = $('#rate'+qty_id).html();
        var order_unit = $('#order_unit'+qty_id).html();
        var quantity = $('#quantity'+qty_id).html();
        var id = $('#id'+qty_id).val();
        var order_id = $('#order_id'+qty_id).val();
        var product_id = $('#product_id'+qty_id).val();
        var unit_conf1 = $('#unit_conf'+qty_id).html();
        var ts_value = $('#total_rs_upper'+qty_id).html();
        var free_qty_upper = $('#free_qty_upper'+qty_id).html();
        var update_product_id = qty_id;
        var update_order_id = $('#update_order_id'+qty_id).val();
        // alert(unit_conf1);
       
         document.getElementById('id1').value = '';
        document.getElementById('order_id1').value = '';
        document.getElementById('item_code1').value = '';
        document.getElementById('product_rate1').value = '';
        document.getElementById('item_name').innerHTML = '';
        document.getElementById('product_rate_put').innerHTML = '';
        document.getElementById('unit_conf1').innerHTML = '';
        document.getElementById('final_cal_qty_pcs1').innerHTML = '';
        document.getElementById('total_rs1').innerHTML = '';
        document.getElementById('total_rs_hidden_v1').value = '';
        document.getElementById('free_qty1').value = '';
        document.getElementById('update_product_id').value = '';
        document.getElementById('update_order_id_c').value = '';
        // $('#unit_conf1') = ;
        document.getElementById('qty1').value = '';

        document.getElementById('id1').value = id;
        document.getElementById('order_id1').value = order_id;
        document.getElementById('item_code1').value = product_id;
        document.getElementById('product_rate1').value = rate;
        document.getElementById('item_name').innerHTML = ITEM_NAME;
        document.getElementById('product_rate_put').innerHTML = rate;
        document.getElementById('unit_conf1').innerHTML = order_unit;
        document.getElementById('final_cal_qty_pcs1').innerHTML = unit_conf1;
        document.getElementById('total_rs1').innerHTML = ts_value;
        document.getElementById('total_rs_hidden_v1').value = ts_value;
        document.getElementById('free_qty1').value = free_qty_upper;
        document.getElementById('update_product_id').value = update_product_id;
        document.getElementById('update_order_id_c').value = update_order_id;
        // $('#unit_conf1') = ;
        document.getElementById('qty1').value = quantity;
        // $('#free_qty1') = ;
        // $('#total_rs_hidden_v1') = ;
        // $('#final_cal_qty_pcs1') = ;
        // $('#total_rs1') = ;

        

    }
</script>
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


<script type="text/javascript">
    $("#dms_order_dispatch_form").submit(function(e) {
        var form = $(this);
        var url = form.attr('action');
        // alert(url);
        // var target=$('#result_state');
        $('#submit_dispatch').html('');
        $('#submit_dispatch').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(data)
            {
                $.alert('Order Inserted SuccessFully ');
                // window.location.reload();
                window.setTimeout(function(){
                    window.location = 'https://baidyanathjhansi.msell.in/public/Order-details';
                }, 2000);
                // $('#dms_order_dispatch_modal').modal('toggle');
               
            },
            complete: function () {
                $('#m-spinner').remove();
            },
            error: function () {
                $('#m-spinner').remove();
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    }); // submit jquery for order dispatch modal
    function autocompleteFunction(str)
    {
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
            $("#qty_remove_span"+qty_id).find('span').empty();

            var t = $(this),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label ),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label );
            url_name2 = ( e.type == 'autocompleteresponse' ? ui.content[0].title : ui.item.title );
            free_qty1 = ( e.type == 'autocompleteresponse' ? ui.content[0].free_qty : ui.item.free_qty );
      
            $("#qty"+qty_id).val(url_name2);
            
            var free_qty= document.getElementById("free_qty"+qty_id).value  = free_qty1 ;
            $('#scheme_qty_with_free_qty_hidden'+qty_id).html(url_name);
            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).html('');
            // alert(qty_id);
            $('#input_scheme_qty_with_free_qty_hidden'+qty_id).html(url_name);

            mulfunc("qty"+qty_id);
            

            return false;
        });
    }
    function mulfunc(str2)
    {
        var d=str2.substr(3,5);
        var grand_total_qty = 0;
        var unit = [];
        // alert('unit_conf');
        var unit_data_conf = $("#unit_conf"+d).html();
        // alert(unit_data_conf);
        var final_conversion_set = 0;
        // document.getElementById("product_rate"+d).value = '';
        // document.getElementById("qty"+d).value= '';
        // document.getElementById("free_qty"+d).value= '';
        var rate= document.getElementById("product_rate"+d).value;
        var qty= document.getElementById("qty"+d).value;
        var free_qty= document.getElementById("free_qty"+d).value;
        // alert(unit_data_conf);
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
        else if(unit_data_conf == 'BOX1')
        {
            var item_code_custom = $('#item_code'+d).val();

             final_conversion_set = unit[item_code_custom]*qty;
            // $('#'+d).val() = final_conversion_set;
            // alert(final_conversion_set);
        }
        else
        {
            final_conversion_set = qty;
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

        for (var po = 0; po < total_qty.length; po++)
        {
            grand_total_qty += parseInt(total_qty[po].value);

        }   

        // document.getElementByI'  d("final_qty").innerHTML= '';
        $('#final_qty').html('');
        $('#final_qty').html(grand_total_qty);
        // document.getElementById("final_qty").innerHTML= grand_total_qty;
    }

        
        
    </script>


<script>
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
    </script>
@endsection