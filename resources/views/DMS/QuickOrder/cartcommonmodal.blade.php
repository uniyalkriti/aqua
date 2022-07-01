
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
</style>

            
                
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
                <div class="row " >
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" >
                                
                                <table class="table-bordered " style="background-color: #f8f8f8; " >
                                    <thead >
                                    
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
                                        
                                    </tr>
                                    </thead>
                                    @foreach($mktg_cat_array as $k => $v)
                                        @foreach($final_out as $fink => $finv)
                                            @if(!empty($finv[$k]) &&  COUNT($finv[$k])>0)
                            
                                                <tr>
                                                    <td colspan="10" align="left" style="text-align: left; background-color: #237a00c4;color: black; height: 10px; " ><b style="font-weight: bolder; font-size: 15px;">{{$v}}</b></td>
                                                </tr>
                                                
                                                <tbody style=" border: 1px solid black;">
                                                @foreach($finv[$k] as $set => $set_v)
                                                    <?php
                                                        // $sum_rs_value[] = !empty($set_v['total_rs'])?$set_v['total_rs']:0;
                                                        $a = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $b = !empty($item_aum_mast_array[$set_v['ITEM_CODE']])?$item_aum_mast_array[$set_v['ITEM_CODE']]:0;  
                                                        if ($b == 0) {
                                                            $sum_bx_value[] = 0;
                                                        }
                                                        else{
                                                            // dd($a,$b);
                                                            $sum_bx_value[] = round(($a / $b),2);
                                                        }
                                                        $sum_pc_value[] = !empty($set_v['quantity'])?$set_v['quantity']:0;
                                                        $gt_rs_value[] = !empty($set_v['total_rs'])?$set_v['total_rs']:0;
                                                        $sum_trade_incentive[] = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $sum_at_discount[] = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;
                                                        $minus_trade_rate = !empty($set_v['t1_rate'])?$set_v['t1_rate']:0;
                                                        $minus_atd_rate = !empty($set_v['atd_rate'])?$set_v['atd_rate']:0;
                                                        $sum_rs_value[] = ($set_v['total_rs']-$minus_trade_rate-$minus_atd_rate+$minus_atd_rate);
                                                     ?>
                                                 <tr>
                                                     <td >{{$set+1}}</td>
                                                     <td style="text-align: left;" id="ITEM_NAME{{$set}}">{{$set_v['ITEM_NAME']}}</td>
                                                     <td  id="rate{{$set}}">{{$set_v['rate']}}</td>
                                                     <td style="text-align: left;" id="order_unit{{$set}}">{{$set_v['order_unit']}}</td>
                                                     <?php
                                                        $quantity_cus = !empty($set_v['quantity'])?$set_v['quantity']:'0';
                                                        $free_qty_cus = !empty($set_v['free_qty'])?$set_v['free_qty']:'0';

                                                     ?>
                                                     @if($set_v['order_unit'] == 'BOX')
                                                     <?php 
                                                        $converstion_unit_cus = !empty($converstion_unit_item_code[$set_v['ITEM_CODE']])?$converstion_unit_item_code[$set_v['ITEM_CODE']]:'0';
                                                     ?>
                                                     <td id="quantity{{$set}}">{{$quantity_cus/$converstion_unit_cus}}</td>
                                                     @else
                                                     <td id="quantity{{$set}}">{{$quantity_cus}}</td>

                                                     @endif
                                                     <td id="unit_conf{{$set}}">{{$quantity_cus}}</td>
                                                     <?php
                                                     $billed_qty = $quantity_cus - $free_qty_cus;
                                                     ?>
                                                     <td >{{$billed_qty}}</td>
                                                     <td >{{!empty($set_v['free_qty'])?$set_v['free_qty']:'0'}}</td>
                                                     <td style="text-align: right;">{{(number_format(round($set_v['total_rs'],2),2))}}</td>
                                                     <td style="text-align: left; padding-left: 20px;">{{$set_v['remarks']}}</td>
                                                     
                                                    
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
                                                    <span id="final_gt_value" >Total Value : {{number_format($sum_rs_value_1,2)}}</span>
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
                                            <td colspan="6"></td>
                                            <td colspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; text-align: left;">
                                                <?php
                                                $gt_rs_value = array_sum($gt_rs_value);
                                                ?>
                                                <b style="text-align: right;">Grand Total Value : </b>
                                            </td>
                                            <td style="text-align: right;"><span style="text-align: right;">{{number_format($gt_rs_value,2)}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" rowspan="3" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
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
                                            <td  colspan="6" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
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
                                            <td  colspan="6" rowspan="2" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
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
                                            <td colspan="10" align="left" style=" width: 60px; text-align:left; font-family: 'Open Sans'; ">
                                                <b>Order Remark : <span>{{$order_remark}}</span> </b>
                                            </td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                                
                            </div>
                        </div>
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
                <form method="post" id="assign-beat" action="{{'dms_update_qty_cart'}}">
                    {!! csrf_field() !!}
                    <table class="table table-bordered " style="background-color: #f8f8f8; " >
                        <tr>
                            <td style="background-color: #4caf50; color: white;">Item Name</td>
                            <td style="background-color: #4caf50; color: white;">Rate</td>
                            <td style="background-color: #4caf50; color: white;">Sale in Box/Pieces</td>
                            <td style="background-color: #4caf50; color: white;">Order Quantity</td>
                            <td style="background-color: #4caf50; color: white;">Qty (PCS)</td>
                            <td style="background-color: #4caf50; color: white;">Total (Rs.)</td>
                            <td style="background-color: #4caf50; color: white;">Scheme(Qty + FREE) IN PCS</td>
                        </tr>
                        <tbody>
                            <tr>
                                
                                <input type="hidden" name="order_qty" id="id1" >
                                <input type="hidden" name="order_qty" id="order_id1" >
                                <input type="hidden" name="order_qty" id="item_code1" >
                                <input type="hidden" name="product_rate[]" id="product_rate1"  >
                                <td id="item_name"></td>
                                <td id="product_rate_put"></td>
                                <td id="unit_conf1"></td>
                                <td><input type="text" name="qty" id="qty1" onkeydown ="autocompleteFunction(this.id);"   ></td>
                                <input type="hidden" name="free_qty" id="free_qty1"  >
                                <input type="hidden" name="final_cal_qty_pcs"  >
                                <input type="hidden" name="total_rs"id = "total_rs_hidden_v1">
                                <td  id ="final_cal_qty_pcs1"></td>
                                <td  id="total_rs1"></td>
                               
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="col-md-12" style="padding-left: 150px; text-align: right;">
                        
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
        var qty_id=str.substr(8,3);
        // alert(qty_id);
        var ITEM_NAME = $('#ITEM_NAME'+qty_id).html();
        var rate = $('#rate'+qty_id).html();
        var order_unit = $('#order_unit'+qty_id).html();
        var quantity = $('#quantity'+qty_id).html();
        var id = $('#id'+qty_id).val();
        var order_id = $('#order_id'+qty_id).val();
        var product_id = $('#product_id'+qty_id).val();
        var unit_conf1 = $('#unit_conf'+qty_id).html();
        // alert(unit_conf1);
       
         document.getElementById('id1').value = '';
        document.getElementById('order_id1').value = '';
        document.getElementById('item_code1').value = '';
        document.getElementById('product_rate1').value = '';
        document.getElementById('item_name').innerHTML = '';
        document.getElementById('product_rate_put').innerHTML = '';
        document.getElementById('unit_conf1').innerHTML = '';
        // $('#unit_conf1') = ;
        document.getElementById('qty1').value = '';

        document.getElementById('id1').value = id;
        document.getElementById('order_id1').value = order_id;
        document.getElementById('item_code1').value = product_id;
        document.getElementById('product_rate1').value = rate;
        document.getElementById('item_name').innerHTML = ITEM_NAME;
        document.getElementById('product_rate_put').innerHTML = rate;
        document.getElementById('unit_conf1').innerHTML = unit_conf1;
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
                $.alert('Order Submitted SuccessFully');
                // window.location.reload();
                window.setTimeout(function(){
                    window.location = 'https://baidyanathjhansi.msell.in/public/Order-details';
                }, 1000);
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
            // console.log(ui.item.label);
            $("#qty_remove_span"+qty_id).find('span').empty();

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
        });
    }
    function mulfunc(str2)
    {
        var d=str2.substr(3,5);
        var grand_total_qty = 0;
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

             final_conversion_set = unit[item_code_custom]*qty;
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
