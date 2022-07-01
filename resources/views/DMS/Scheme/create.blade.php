<?php 

// include('../client/include/menu-by-role/copy-admin.inc.php');

?>
@extends('layouts.adminMenuDms')

@section('body')

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
	<link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
	<link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
	<link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/multi-select.css')}}"/>
<style type="text/css">

.multiselect-container > li > a > label {
  padding: 3px; 20px 3px 0;
}
</style>

<div class="main-content" >
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #438eb9;">
            <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">Scheme Details </a>
                    </li>

                    <li class="active" style="color: black;">Scheme Details</li>
                </ul><!-- /.breadcrumb -->
            <!-- /.nav-search -->
        </div>

        <div class="page-content" style="padding-top: 0;">
            <form  action="{{route($current_menu.'.store')}}" method="post" role="form" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <fieldset><legend>Circular &amp; Scheme</legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Circular No.</label>
                                    <input placeholder="Circular No." required="required" type="text" id="first_name" name="circular_no"
                                           class="form-control input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Circular Date</label>
                                    <input placeholder="Circular Date" required="required" type="text" id="circular_date" name="circular_date"
                                           class="form-control input-sm date-picker"/>
                                </div>

                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Scheme No.</label>
                                    <input placeholder="Scheme No." required="required" type="text" id="first_name" name="scheme_no"
                                           class="form-control input-sm "/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> From Date</label>
                                    <input placeholder="From Date" required="required" type="text" id="scheme_from_date" name="scheme_from_date"
                                           class="form-control input-sm date-picker"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> To Date</label>
                                    <input placeholder="To Date." required="required" type="text" id="scheme_to_date" name="scheme_to_date"
                                           class="form-control input-sm date-picker" />
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Scheme Type</label>
                                    <select class="form-control" name="scheme_type" style="background-color: #438eb9; color:black;"> 
                                        <option value="1">Target Base Incentive Scheme</option>
                                        <option value="2">Target Base Free Scheme</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Scheme Beneficially</label>
                                    <select class="form-control" name="scheme_benefically" style="background-color: #438eb9; color:black;">
                                        <option value="1">For Dealer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Scheme Desc</label>
                                    <textarea name="scheme_description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset><legend>Item Selection </legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> MKTG CATG ITEM</label>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No." type="checkbox" id="first_name" name="mktg_all"
                                           class="checkboxmktg"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="mktg_selected"
                                           class="checkboxmktg" data-toggle="modal" data-target="#mktg_selected_modal" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> MKTG CATG GROUP<br><small>(Mainline(GEN+CLA+GLD))</small></label>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No." type="checkbox" id="first_name" name="mainline_all"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            <!-- <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="mainline_selected"
                                           class=" input-sm"/>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> PROD CATG ITEM</label>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No." type="checkbox" id="first_name" name="prod_catg_all"
                                           class="prod_catg_checkbox"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="prod_selected"
                                           class="prod_catg_checkbox" data-toggle="modal" data-target="#prod_selected_modal"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> PROD CATG GROUP</label>
                                </div>
                            </div>
                            <!-- <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No." type="checkbox" id="first_name" name="prod_group_all"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="prod_group_selected"
                                           class=" input-sm"/>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    
                    
                </fieldset>
                <fieldset><legend> Selection </legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Dealer</label>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="dealer_all"
                                           class="123"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="dealer_selected"
                                           class=" input-sm"/>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Headquater</label>
                                    
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="head_quater"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="head_quater_selected"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Territory</label>
                                    
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> All</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="territory"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="module"> Selected</label>
                                    <input placeholder="Circular No."  type="checkbox" id="first_name" name="territory_selected"
                                           class=" input-sm"/>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                    
                    
                </fieldset>
                <fieldset><legend>Item Details </legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <select name="sale_type">
                                <option value="BOX">BOX</option>
                                <option value="PCS">PCS</option>
                                <option value="AMT">AMT</option>
                            </select>
                            <br>

                             <table id="dynamic-table1" class=" table-bordered table-hover">
                                <thead>
                                <tr>
                                    
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Tgt/Sale From Period </th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Tgt/Sale To Period </th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Inc. On From Date</th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Inc. On To Date</th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">From %</th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">To %</th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Rate %</th>
                                    <th style="background-color: #438eb9; color: black; width: 100px;">Action</th>
                                    
                                    
                                </tr>
                                </thead>
                                <tbody class="mytbody_demand_order">

                                    <?php
                                        $array_incr = array(1,2,3,4,5,6,7,8,9,10);
                                    ?>
                                    @foreach($array_incr as $key => $value)
                                 
                                        <tr>
                                           <td width="200px"><input type="text" class="date-picker" id = "target_sale_from_date{{$value}}" name="target_sale_from_date[]"></td>
                                           <td width="200px"><input type="text" class="date-picker" id = "target_sale_to_date{{$value}}" name="target_sale_to_date[]"></td>
                                           <td width="200px"><input type="text" class="date-picker" id = "incentive_sale_from_date{{$value}}" name="incentive_sale_from_date[]"></td>
                                           <td width="200px"><input type="text" class="date-picker" id = "incentive_sale_to_date{{$value}}" name="incentive_sale_to_date[]"></td>
                                           <td width="200px"><input type="text" id = "base_from{{$value}}" name="base_from[]"></td>
                                           <td width="200px"><input type="text" id = "base_to{{$value}}" name="base_to[]"></td>
                                           <td width="200px"><input type="text" id = "free_final_rate{{$value}}" name="free_final_rate[]"></td>
                                            @if($key == 9)
                                            <td width="30px"><i class="fa fa-plus addrow" id="sr_no{{$value}}" onclick=" addfunction(this.id);"></i> / <i  title="Less"  class="removenewrow fa fa-minus"/></i> </td>
                                            @else
                                            <td width="30px"><i  title="Less"  class="removenewrow fa fa-minus"/></i></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                                
                            </table>
                            
                        </div>
                    </div>
                    
                    
                </fieldset> 
                <!-- modal starts here for mktg  -->
                <div class="modal fade rotate" data-keyboard="false" id="mktg_selected_modal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header widget-header widget-header-small" >
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    ×
                                </button>
                                <h4 class="modal-title">Select MKTG Category </h4>

                            </div>
                            <div class="modal-body ">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="control-label no-padding-right" for="name"></label>
                                        <select  multiple name="mktg_catg_dropdown[]" id="mktg_catg_dropdown" class="form-control input-sm" style="overflow-y: scroll; text-align: left;">
                                            @if(!empty($mktg_drop_down_data))
                                                @foreach($mktg_drop_down_data as $sk=>$sr) 
                                                    
                                                    <option value="{{$sr->MKTG_CATG}}" > {{$sr->MKTG_CATG_NAME}} 
                                                    </option>
                                                @endforeach 
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- modal for mktg ends here  -->

                <!-- modal starts here for prod catg  -->
                <div class="modal fade rotate"  data-keyboard="false" id="prod_selected_modal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header widget-header widget-header-small" >
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    ×
                                </button>
                                <h4 class="modal-title">Select PROD Category </h4>

                            </div>
                            <div class="modal-body ui-dialog-content ui-widget-content">
                                <div class="modal-body ">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="control-label no-padding-right" for="name"></label>
                                            <div style="overflow-y: scroll; text-align: left; height:280px;">

                                               <table border="1" width="800px">
                                                    <thead>
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    @if(!empty($prod_drop_down_data))
                                                        @foreach($prod_drop_down_data as $sk=>$sr) 
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                {{$sr->PROD_CATG_NAME}}
                                                                </td>
                                                                <td>
                                                                <input type="checkbox" name="prod_catg_dropdown[]" value="{{$sr->PROD_CATG}}"> 
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endforeach 
                                                    @endif
                                                </table>


                                            </div>

                                           <!--  <select  multiple name="prod_catg_dropdown[]" id="prod_catg_dropdown" class="form-control input-sm" style="overflow-y: scroll; text-align: left; height:280px;"> -->

                                                <!-- <select  multiple name="prod_catg_dropdown[]" id="prod_catg_dropdown" class="form-control input-sm chosen-select-modal" > -->

                                               {{-- @if(!empty($prod_drop_down_data))
                                                    @foreach($prod_drop_down_data as $sk=>$sr)  --}}
                                                       <!--  <option value="{{$sr->PROD_CATG}}" > {{$sr->PROD_CATG_NAME}} 
                                                        </option> -->
                                                   {{-- @endforeach 
                                                @endif --}}
                                            <!-- </select> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- modal for prod catg ends here  -->
                <input type="submit" name="submit" value="submit">

            </form>
        </div>
    </div>
</div>










<!--  -->


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


    <script src="{{asset('assets/js/custom_jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
   
    <script src="{{asset('nice/js/jszip.min.js')}}"></script>
    <script src="{{asset('nice/js/vfs_fonts.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-multiselect.min.js')}}"></script>


<script type="text/javascript">

    $(function () {
        $('#mktg_catg_dropdown').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select MKTG Catg',
            buttonWidth:'400px',
            maxHeight: 400,
            // align:left;


        });
    });
    $(function () {
        $('#prod_catg_dropdown').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select PROD Catg',
            buttonWidth:'400px',
            maxHeight: 400,
            // align:left;


        });
    });
    
    // $("input:checkbox").on('click', function() {
    //   // in the handler, 'this' refers to the box clicked on
    //   var $box = $(this);
    //   if ($box.is(":checked")) {
    //     // the name of the box is retrieved using the .attr() method
    //     // as it is assumed and expected to be immutable
    //     var group = "input:checkbox[class='" + $box.attr("class") + "']";
    //     // the checked state of the group/box on the other hand will change
    //     // and the current value is retrieved using .prop() method
    //     $(group).prop("checked", false);
    //     $box.prop("checked", true);
    //   } else {
    //     $box.prop("checked", false);
    //   }
    // });

    
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

            var target_sale_from_date = `<input type="text" class="date-picker" name="target_sale_from_date[]" id="target_sale_from_date${cust_id}">`;
            var target_sale_to_date = `<input type="text" class="date-picker" name="target_sale_to_date[]" id="target_sale_to_date${cust_id}">`;
            var incentive_sale_from_date = `<input type="text" class="date-picker" name="incentive_sale_from_date[]" id="incentive_sale_from_date${cust_id}">`;
            var incentive_sale_to_date = `<input type="text" class="date-picker" name="incentive_sale_to_date[]" id="incentive_sale_to_date${cust_id}">`;
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
        jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": false},
                            null,null, null, null, null, null, 
                            
                            {"bSortable": false}
                        ],
                        "aaSorting": [],
                        "sScrollY": "1000px",
                        //"bPaginate": false,

                        // "sScrollX": "100%",
                        "sScrollXInner": "120%",
                        "bScrollCollapse": true,
                        "iDisplayLength": 10000,


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
            myTable.buttons().container().appendTo($('.tableTools-container'));

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
                $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
            });

            ////

            setTimeout(function () {
                $($('.tableTools-container')).find('a.dt-button').each(function () {
                    var div = $(this).find(' > div').first();
                    if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);


            myTable.on('select', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on('deselect', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });


            /////////////////////////////////
            //table checkboxes
            // $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

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
            $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

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


<script>

      $('#prod_selected_modal').on('shown.bs.modal', function () {
          $('.chosen-select-modal', this).chosen();
        });

    $(function () {
        $('#scheme_from_date,#scheme_to_date').datetimepicker({
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: true,
            // maxDate: moment()
        });

    });
	$(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('.date-picker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            useCurrent: true,
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
    