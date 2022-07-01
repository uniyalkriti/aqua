<?php 

// include('../client/include/menu-by-role/copy-admin.inc.php');

?>


  

<style type="text/css">
    tbody tr:nth-child(odd){
      background-color: #f8f8f8;
      color: black;
      /*font-weight: bold;*/
       font-size: 16px;
    }
</style>
<div class="main-content" >
    <div class="main-content-inner">
        

        <div class="page-content" style="padding-top: 0;">
            <fieldset><legend style="text-align: center; color:black; font-weight: bolder; font-size: 16px;">Scheme Incentive Plan</legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered" style="font-size: 16px; border-color: black;">
                                <tbody>
                                    @if(!empty($scheme_catg_title_data))
                                        @foreach($scheme_catg_title_data as $catg_key => $catg_value)
                                            <tr>
                                                <th colspan="5">CIR NO:{{$catg_value->circular_no}}/DATE:{{date('d/m/y',strtotime($catg_value->circular_date))}}</th>
                                                <th colspan="5">PERIOD : FROM {{date('d/m/y',strtotime($catg_value->scheme_from_date))}} TO {{date('d/m/y',strtotime($catg_value->scheme_to_date))}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="10" class="scheme-catg" style="background-color: #90d781; font-size: 16px; font-weight: bold;">
                                                    @if($catg_value->mktg_catg == 'mainline')
                                                        Mainline<br><small>(GEN+GLD+CLA)</small>
                                                        @else
                                                        {{!empty($mktg_catg_return_array[$catg_value->mktg_catg])?$mktg_catg_return_array[$catg_value->mktg_catg]:''}}</th>

                                                    @endif
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th rowspan="3">Sale </th>
                                                <th rowspan="3">From Date</th>
                                                <th rowspan="3">To Date</th>
                                                <th colspan="6">Scheme Apllicable Sale Criteria ({{$catg_value->sale_type}})</th>
                                                <th rowspan="3">Proposed order <br>for availing scheme incentive<br>(AMT/BOX/PCS)</th>
                                            </tr>
                                            
                                            <tr class="tr-bgcolor">
                                                <th rowspan="2">Target</th>
                                                <th colspan="2">TGT PLAN %</th>
                                                <th colspan="2">Approx. Purchase Slab</th>
                                                <th rowspan="2">INCENTIVE %</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th>From</th>
                                                <th>To</th>
                                                <th>From</th>
                                                <th>To</th>
                                            </tr>
                                            @if(!empty($out[$catg_value->mktg_catg]))
                                                @foreach($out[$catg_value->mktg_catg] as $catg_key => $catg_value)
                                                    <tr>
                                                        <td>{{$sale_value = !empty($mktg_catg_wise_sales[$catg_value->mktg_catg.$catg_value->id])?round($mktg_catg_wise_sales[$catg_value->mktg_catg.$catg_value->id],2):'0'}}</td>
                                                        <td>{{date('d/m/y',strtotime($catg_value->target_sale_from_date))}}</td>
                                                        <td>{{date('d/m/y',strtotime($catg_value->target_sale_to_date))}}</td>
                                                        <td>{{$cal=!empty($mktg_target_out[$catg_value->mktg_catg])?$mktg_target_out[$catg_value->mktg_catg]:'0'}}</td>
                                                        <td>{{$step_from = $catg_value->base_from}}%</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$step_to = $catg_value->base_to}}%</td>
                                                        @endif
                                                        <td>{{$inc_step_from = round($cal*$step_from/100,2)}}</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$inc_step_to = round($cal*$step_to/100,2)}}</td>
                                                        @endif
                                                        <td>{{$catg_value->free_final_rate}}%</td>

                                                        @if( $sale_value > $inc_step_from)
                                                            <td>0</td>
                                                            @else
                                                            <td style="font-weight: bold; color: red;">{{round($inc_step_from - $sale_value,2)}}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif

                                    @if(!empty($prod_scheme_catg_title_data))
                                        @foreach($prod_scheme_catg_title_data as $catg_key => $catg_value)
                                            <tr>
                                                <th colspan="5">CIR NO:{{$catg_value->circular_no}}/DATE:{{date('d/m/y',strtotime($catg_value->circular_date))}}</th>
                                                <th colspan="5">PERIOD : FROM {{date('d/m/y',strtotime($catg_value->scheme_from_date))}} TO {{date('d/m/y',strtotime($catg_value->scheme_to_date))}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="10" class="scheme-catg" style="background-color: #90d781;">{{!empty($prod_catg_return_array[$catg_value->prod_catg])?$prod_catg_return_array[$catg_value->prod_catg]:''}}</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th rowspan="3">Sale </th>
                                                <th rowspan="3">From Date</th>
                                                <th rowspan="3">To Date</th>
                                                <th colspan="6">Scheme Apllicable Sale Criteria ({{$catg_value->sale_type}})</th>
                                                <th rowspan="3">Proposed order <br>for availing scheme incentive<br>(AMT/BOX/PCS)</th>
                                            </tr>
                                            
                                            <tr class="tr-bgcolor">
                                                <th rowspan="2">Target</th>
                                                <th colspan="2">TGT PLAN %</th>
                                                <th colspan="2">Approx. Purchase Slab</th>
                                                <th rowspan="2">INCENTIVE %</th>
                                            </tr>
                                            <tr class="tr-bgcolor">
                                                <th>From</th>
                                                <th>To</th>
                                                <th>From</th>
                                                <th>To</th>
                                            </tr>
                                            @if(!empty($prod_out[$catg_value->prod_catg]))
                                                @foreach($prod_out[$catg_value->prod_catg] as $catg_key => $catg_value)

                                                    <tr>
                                                        <td>{{$sale_value = !empty($prod_catg_wise_sales[$catg_value->prod_catg.$catg_value->id])?round($prod_catg_wise_sales[$catg_value->prod_catg.$catg_value->id],2):'0'}}</td>
                                                        <td>{{date('d/m/y',strtotime($catg_value->target_sale_from_date))}}</td>
                                                        <td>{{date('d/m/y',strtotime($catg_value->target_sale_to_date))}}</td>
                                                        <td>{{$cal=!empty($prod_target_out[$catg_value->prod_catg])?$prod_target_out[$catg_value->prod_catg]:'0'}}</td>
                                                        <td>{{$step_from = $catg_value->base_from}}%</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$step_to = $catg_value->base_to}}%</td>
                                                        @endif
                                                        <td>{{$inc_step_from = round($cal*$step_from/100,2)}}</td>
                                                        @if($catg_value->base_to == 'Above')
                                                            <td>Above</td>
                                                            @else
                                                            <td>{{$inc_step_to = round($cal*$step_to/100,2)}}</td>
                                                        @endif
                                                        <td>{{$catg_value->free_final_rate}}%</td>

                                                        @if( $sale_value > $inc_step_from)
                                                            <td>0</td>
                                                            @else
                                                            <td style="font-weight: bold; color: red;">{{round($inc_step_from - $sale_value,2)}}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </fieldset>
        </div>
    </div>
</div>










<!--  -->
    </div>
</div>
    
</body>


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

    var cust_id = 11;
        // console.log(cust_id);

        function addfunction(str)
        {
            // var grand_total_rs ;

            var target_sale_from_date = `<input type="text" name="target_sale_from_date[]" id="target_sale_from_date${cust_id}">`;
            var target_sale_to_date = `<input type="text" name="target_sale_to_date[]" id="target_sale_to_date${cust_id}">`;
            var incentive_sale_from_date = `<input type="text" name="incentive_sale_from_date[]" id="incentive_sale_from_date${cust_id}">`;
            var incentive_sale_to_date = `<input type="text" name="incentive_sale_to_date[]" id="incentive_sale_to_date${cust_id}">`;
            var base_from = `<input type="text" name="base_from[]" id="base_from${cust_id}">`;
            var base_to = `<input type="text" name="base_to[]" id="base_to${cust_id}">`;
            var free_final_rate = `<input type="text" name="free_final_rate[]" id="free_final_rate${cust_id}">`;

           
            var template = ('<tr><td>'+target_sale_from_date+'</td><td>'+target_sale_to_date+'</td><td>'+incentive_sale_from_date+'</td><td>'+incentive_sale_to_date+'</td><td>'+base_from+'</td><td>'+base_to+'</td><td>'+free_final_rate+'</td><td width="30px" ><i id=sr_no'+cust_id+' title="more" class="fa fa-plus addrow" aria-hidden="true" onclick=" addfunction(this.id); chosenFunction();"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></tr>');
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
    function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
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


        
  // /***************/
    // $('.show-details-btn').on('click', function (e) {
    //     e.preventDefault();
    //     $(this).closest('tr').next().toggleClass('open');
    //     $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    // });
    /***************/
        
    </script>


    