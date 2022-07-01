<?php 
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?>

@if(!empty($person))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif

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
</style>


<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <?php $count=count($catalog_product); 
$myColsp = count($finalProductTypeOut)+3; 

$supercol = ($count*$myColsp)+15; 


?>
    <thead>
        <tr>
        <td colspan="<?=$supercol?>"><h3> {{Lang::get('common.dsrMonthlyForNehaReport')}}</h3></td>
        </tr>

    <tr class="info" style="color: black;">
        <th rowspan="2">{{Lang::get('common.s_no')}}</th>
        <th rowspan="2">{{Lang::get('common.location3')}}</th>
        <th rowspan="2">{{Lang::get('common.location4')}}</th>
        <th rowspan="2">{{Lang::get('common.location5')}}</th>
        <th rowspan="2">{{Lang::get('common.location6')}}</th>
        <th rowspan="2">{{Lang::get('common.emp_code')}}</th>
        <th rowspan="2">{{Lang::get('common.username')}}</th>
        <th rowspan="2">{{Lang::get('common.role_key')}}</th>
        <th rowspan="2">{{Lang::get('common.user_contact')}}</th>
        <th rowspan="2">{{Lang::get('common.senior_name')}}</th>
        <th rowspan="2">Total Counter</th>
        <th rowspan="2">No Visit Counters Yet</th>
        <th rowspan="2">Unique Total Call</th>
        <th rowspan="2">Unique Productive Call</th>
        <th rowspan="2"> Productivity(%)</th>
      @foreach($catalog_product as $ckey=>$cdata)
        <?php  $null[] = 'null'; ?>

        <th colspan="<?=$myColsp?>">{{$cdata->name}}</th>
      @endforeach

    </tr>

      <tr>
    <?php
    for($i=0; $i<$count; $i++) 
    {
    ?>

    @foreach($finalProductTypeOut as $fptokey=>$fptodata)
        <th>{{$fptodata}}</th>
    @endforeach

    <th>Unique Productive Call (as per SKU placed)</th>
    <th>Productivity (as per SKU placed)</th>
    <th>Total Sale Value (as per SKU placed)</th>
     <?php
    }
    ?>
    </tr>


    </thead>
    <tbody>
        <?php 
        $i=1; 
        $r=0; 
        $total_qty = array();
        $total_amt = array(); 
        $amt =0; 
        $senior = App\CommonFilter::senior_name('person');

        ?>
        @if(!empty($person) && count($person)>0)
            @foreach($person as $key=>$data)
            <?php
              $user_id = Crypt::encryptString($data->user_id); 
              $person_id_senior = Crypt::encryptString($data->person_id_senior); 

              $totCounter = !empty($totalCounter[$data->user_id])?$totalCounter[$data->user_id]:'0';

              $uniqueTC = !empty($uniqueTotalCall[$data->user_id])?$uniqueTotalCall[$data->user_id]:'0';
              $uniquePC = !empty($uniqueProductiveCall[$data->user_id])?$uniqueProductiveCall[$data->user_id]:'0';

              $notVisitCounter = $totCounter-$uniqueTC;

              if($uniquePC == '0'){
                $productivity = '0.00';
              }else{
                $productivity = ROUND((($uniquePC/$uniqueTC)*100),2);
              }

              $totalCounterArray[] = !empty($totalCounter[$data->user_id])?$totalCounter[$data->user_id]:'0';
              $uniqueTotalCallArray[] = !empty($uniqueTotalCall[$data->user_id])?$uniqueTotalCall[$data->user_id]:'0';
              $uniqueProductiveCallArray[] = !empty($uniqueProductiveCall[$data->user_id])?$uniqueProductiveCall[$data->user_id]:'0';

            ?>
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$data->l3_name}}</td>
                    <td>{{$data->l4_name}}</td>
                    <td>{{$data->l5_name}}</td>
                    <td>{{$data->l6_name}}</td>
                    <td>{{$data->emp_code}}</td>
                    <td><a href="{{url('user/'.$user_id)}}">{{$data->person_fullname}}</a></td>
                    <td>{{$data->rolename}}</td>
                    <td>{{$data->mobile}}</td>
                    <td><a href="{{url('user/'.$person_id_senior)}}">{{!empty($senior[$data->person_id_senior])?$senior[$data->person_id_senior]:''}}</a></td>
                    <td>{{!empty($totalCounter[$data->user_id])?$totalCounter[$data->user_id]:'0'}}</td>

                    <td onclick="outlet_details('{{$data->user_id}}','{{$from_date}}','{{$to_date}}','{{$company_id}}');">
                    <a class="outlet_modal_details" title="Outlet details" data-toggle="modal" data-target="#outlet_modal_details" >
                        {{$notVisitCounter}}
                    </a>
                    </td>



                    <td>{{!empty($uniqueTotalCall[$data->user_id])?$uniqueTotalCall[$data->user_id]:'0'}}</td>
                    <td>{{!empty($uniqueProductiveCall[$data->user_id])?$uniqueProductiveCall[$data->user_id]:'0'}}</td>
                    <td>{{$productivity}} %</td>
                        @foreach($catalog_product as $ckey=>$cdata)
                        <?php 
                        $finalSaleArray[$cdata->id]['uniqueProductiveCall'][] = !empty($finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall'])?$finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall']:'0';

                        $finalSaleArray[$cdata->id]['sale_value'][] = !empty($finalSaleOutDsr[$data->user_id.$cdata->id]['sale_value'])?$finalSaleOutDsr[$data->user_id.$cdata->id]['sale_value']:'0';
                        ?>
                        @foreach($finalProductTypeOut as $fptokey=>$fptodata)
                            <?php
                                $finalValues = !empty($finalOutDsr[$data->user_id.$cdata->id.$fptokey]['quantity'])?$finalOutDsr[$data->user_id.$cdata->id.$fptokey]['quantity']:'0';
                                $finalValuesArray[$cdata->id.$fptokey][] = !empty($finalOutDsr[$data->user_id.$cdata->id.$fptokey]['quantity'])?$finalOutDsr[$data->user_id.$cdata->id.$fptokey]['quantity']:'0';
                            ?>
                            <td>{{$finalValues}}</td>
                        @endforeach
                  
                      <td>{{!empty($finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall'])?$finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall']:'0'}}</td>

                      <?php 

                      $prduniqueProductiveCall = !empty($finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall'])?$finalSaleOutDsr[$data->user_id.$cdata->id]['uniqueProductiveCall']:'0';

                      ?>

                      @if($prduniqueProductiveCall == 0)
                      <td>0</td>
                      @else
                      <?php
                      $prdProductivity = ROUND($uniquePC/$prduniqueProductiveCall);
                      ?>
                      <td>{{$prdProductivity}}</td>
                      @endif




                      <td>{{!empty($finalSaleOutDsr[$data->user_id.$cdata->id]['sale_value'])?$finalSaleOutDsr[$data->user_id.$cdata->id]['sale_value']:'0'}}</td>
                    @endforeach
                  


                  

                </tr>
           @endforeach
        
    @endif
    </tbody>
    <tfoot>
        <tr>
          <th colspan="10"> {{Lang::get('common.grand')}} {{Lang::get('common.total')}} </th>

            <th></th>
            <th></th>
            <th>{{$ftc = array_sum($uniqueTotalCallArray)}}</th>
            <th>{{$fpc = array_sum($uniqueProductiveCallArray)}}</th>

            @if($fpc == 0)
            <th>0 %</th>
            @else
            <th>{{ROUND((($fpc/$ftc)*100),2)}} %</th>
            @endif

          
          @foreach($catalog_product as $ckey1=>$cdata1)
                    @foreach($finalProductTypeOut as $fptokey1=>$fptodata1)
                        <th>{{array_sum($finalValuesArray[$cdata1->id.$fptokey1])}}</th>
                    @endforeach

          <th>{{round(array_sum($finalSaleArray[$cdata1->id]['uniqueProductiveCall']))}}</th>
          <th>0</th>
          <th>{{round(array_sum($finalSaleArray[$cdata1->id]['sale_value']))}}</th>

          @endforeach
       

        </tr>
    </tfoot>
</table>
<?php
 $implode = implode(",",$null);
  ?>



<div class="modal fade" id="outlet_modal_details" role="dialog">
    <div class="modal-dialog modal-lg3" style="width:900px;">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > Outlet Details</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    
                    <div class="col-xs-12 col-md-12">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>Sr.No</th>
                                <th>Retailer State</th>
                                <th>Retailer Town</th>
                                <th>Retailer Name</th>
                                <th>Retailer Contact</th>
                                <th>Dealer Name</th>
                                <th>Beat Name</th>
                                <th>Created By</th>
                                <th>Created On</th>
                            </thead>
                          
                                <tbody class="mytbody_outlet_details">
                                </tbody>
                    
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>





<script>
     function outlet_details(user_id,from_date,to_date,company_id) {
        // alert(to_date);
                // var user_id = dealer_id;
                // var dealer_id = user_id;
              // alert(user_id);
               
                $('.mytbody_outlet_details').html('');
              
                if (user_id != '') 
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: domain + '/get_outlet_details',
                        dataType: 'json',
                        data: {'user_id': user_id, 'from_date': from_date, 'to_date': to_date, 'company_id': company_id},
                        success: function (data) 
                        {
                            var template_beat ='';

                            if (data.code == 401) 
                            {
                               
                            }
                            else if (data.code == 200) 
                            {
                                var Sno_beat = 1;
                                                          
                                $.each(data.result_data, function (b_key, b_value) {
                                    // console.log(b_value);
                                  
                                        template_beat += ("<tr><td>"+Sno_beat+"</td><td>"+b_value.state+"</td><td>"+b_value.town+"</td><td><a href=retailer/"+b_value.retailer_n+">"+b_value.retailer_name+"</a></td><td>"+b_value.landline+"</td><td><a href=distributor/"+b_value.dealer_n+">"+b_value.dealer_name+"</a></td><td>"+b_value.beat+"</td><td><a href=user/"+b_value.user_n+">"+b_value.user_name+"</a></td><td>"+b_value.created_on+"</td></tr>");
                                    Sno_beat++;
                                });   
                                
                                $('.mytbody_outlet_details').append(template_beat);

                                
                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });
                }       
            };
    </script>





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
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                {"bSortable": false},
                                                  null,null,null,null, null, null,null,null,null,null, null, null,null,null,<?= $implode ?>,
                                                  
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            "bScrollCollapse": true,
                                            "iDisplayLength": 50,


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
                                            "text": "<i class='fa fa-database bigger-110 orange'></i> <span class=''>CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "excel",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "pdf",
                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "print",
                                            "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            autoPrint: false,
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
                                        if (div.length == 1)
                                            div.tooltip({container: 'body', title: div.parent().text()});
                                        else
                                            $(this).tooltip({container: 'body', title: $(this).text()});
                                    });
                                }, 500);





                         /*       myTable.on('select', function (e, dt, type, index) {
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
                                        if (th_checked)
                                            myTable.row(row).select();
                                        else
                                            myTable.row(row).deselect();
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                                    var row = $(this).closest('tr').get(0);
                                    if (this.checked)
                                        myTable.row(row).deselect();
                                    else
                                        myTable.row(row).select();
                                });
*/


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
                                        if (th_checked)
                                            $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                                        else
                                            $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                                    var $row = $(this).closest('tr');
                                    if ($row.is('.detail-row '))
                                        return;
                                    if (this.checked)
                                        $row.addClass(active_class);
                                    else
                                        $row.removeClass(active_class);
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

                                    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
                                        return 'right';
                                    return 'left';
                                }




                                /***************/
                                $('.show-details-btn').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).closest('tr').next().toggleClass('open');
                                    $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
                                });
                                

                            })
        </script>