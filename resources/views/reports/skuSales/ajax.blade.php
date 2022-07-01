<?php 
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?>
<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
SKU Sales
</div>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
   <thead>
    <tr>
        <th>S.No.</th>
        <th>Product Id</th>
        <th>Name</th>
        <th>Code</th>
        <th>Ordered Quantity</th>
        <th>Ordered Amount</th>
        <th>More</th>

    </tr>
    </thead>
    <tbody>
    @php
    if(empty($retailer))
    $implode = "";
    else
    $implode = implode(",",$retailer);

     if(empty($location_4))
    $location_4_string = "";
    else
    $location_4_string = implode(",",$location_4);

     if(empty($location_5))
    $location_5_string = "";
    else
    $location_5_string = implode(",",$location_5);

    if(empty($location_6))
    $location_6_string = "";
    else
    $location_6_string = implode(",",$location_6);

     if(empty($location_7))
    $location_7_string = "";
    else
    $location_7_string = implode(",",$location_7);

     if(empty($user))
    $user_string = "";
    else
    $user_string = implode(",",$user);


    if(empty($dealer))
    $dealer_string = "";
    else
    $dealer_string = implode(",",$dealer);

    if(empty($role))
    $role_string = "";
    else
    $role_string = implode(",",$role);

    if(empty($product))
    $product_string = "";
    else
    $product_string = implode(",",$product);


    if(empty($location_3))
    $state_string = "";
    else
    $state_string = implode(",",$location_3);

    if(empty($head_quarter))
    $head_quarter_string = "";
    else
    $head_quarter_string = implode(",",$head_quarter);

    if(empty($town))
    $town_string = "";
    else
    $town_string = implode(",",$town);

    if(empty($beat))
    $beat_string = "";
    else
    $beat_string = implode(",",$beat);
  
    @endphp
            @foreach($records as $key=>$value)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$value->product_id}}</td>
                <td>{{$value->product_name}}</td>
                <td>{{$value->itemcode}}</td>
                <td>{{ROUND($value->total_quantity,2)}}</td>
                <td>{{$value->total_value}}</td>
                <td>
                <a href="#" productid="{{ $value->product_id }}" fromdate="{{$from_date}}" todate="{{$to_date}}" retailer_filter="{{$implode}}" state_filter="{{$state_string}}" location_4_string="{{$location_4_string}}" location_5_string="{{$location_5_string}}" location_6_string="{{$location_6_string}}" location_7_string="{{$location_7_string}}" user_string="{{$user_string}}" dealer_string="{{$dealer_string}}" role_string="{{$role_string}}" product_string="{{$product_string}}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="History">
                    <div><i class="fa fa-eye"></i></div>
                </a>
                </td>
           
            </tr>
            @endforeach
    </tbody>
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
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                {"bSortable": false},
                                                  null,null,null,null,null,
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


<!-- details modal starts here  -->

<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg2">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:700px">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- <h4 class="modal-title smaller" > <a href="ExportSkuSales?{{$query_string}}">SKU Details Export  </a></h4> -->
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">SKU Details Export  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">

                 
                    <div class="table-header center">
                        <span>SKU Sales Details </span>
                    </div>
                  

                        <table id="simple-table" class="table table-bordered">
                        
                            <thead>
                                <th>Sr.No</th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Name</th>
                                <th>Qty.</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>View</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
                            </tbody>
                    
                        </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.logs_modal').click(function() {
            var productid = $(this).attr('productid');
            var fromdate = $(this).attr('fromdate');
            var todate = $(this).attr('todate');
            var retailer_filter = $(this).attr('retailer_filter');
            var state_filter = $(this).attr('state_filter');
            var location_4_string = $(this).attr('location_4_string');
            var location_5_string = $(this).attr('location_5_string');
            var location_6_string = $(this).attr('location_6_string');
            var location_7_string = $(this).attr('location_7_string');
            var user_string = $(this).attr('user_string');
            var dealer_string = $(this).attr('dealer_string');
            var role_string = $(this).attr('role_string');
            var product_string = $(this).attr('product_string');
            // var town_filter = $(this).attr('town_filter');
            // var beat_filter = $(this).attr('beat_filter');
            if (productid != '') 
            {
                $('.tbody_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/skuSalesDetails',
                    dataType: 'json',
                    data: "productid=" + productid,
                    // data: {"productid": productid, "fromdate": fromdate, "todate": todate, "retailer_filter": retailer_filter, "state_filter": state_filter, "head_quarter_filter": head_quarter_filter, "town_filter": town_filter, "beat_filter": beat_filter},

                     data: {"productid": productid, "fromdate": fromdate, "todate": todate, "retailer_filter": retailer_filter, "state_filter": state_filter, "location_4_string": location_4_string, "location_5_string": location_5_string, "location_6_string": location_6_string, "location_7_string": location_7_string, "user_string": user_string,"dealer_string": dealer_string,"role_string": role_string,"product_string": product_string},
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            var template = '';
                            var Sno = 1;
                            $.each(data.data_return, function (u_key, u_value) {
                               
                                template += ('<tr class="odd" role="row"><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.retailer_name+'</td><td>'+u_value.product_name+'</td><td>'+u_value.quantity+'</td><td>'+u_value.rate+'</td><td>'+u_value.amount+'</td><td><a href="#" onclick="someFunction('+"'"+u_value.order_id+"'"+');" orderid='+u_value.order_id+' data-toggle="modal"  data-target="#order_modal" class="order_modal" title="Order"> <div><i class="fa fa-eye"></i></div> </a></td></tr>');
                                Sno++;

                                //  template += ('<tr class="odd" role="row"><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.retailer_name+'</td><td>'+u_value.product_name+'</td><td>'+u_value.quantity+'</td><td>'+u_value.rate+'</td><td>'+u_value.amount+'</td></tr>');
                                // Sno++;

                            });   
                            $('.tbody_logs').append(template);
                            
                        }
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });



        
    </script>
     

     <script>

        function someFunction(orderid){
            if (orderid != '') 
            {
                $('.order_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/skuOrderDetails',
                    dataType: 'json',
                    data: "orderid=" + orderid,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            var templatenew = '';
                            var Snon = 1;
                            $.each(data.data_return, function (u_key, u_value) {
                                templatenew += ('<tr class="odd" role="row"><td>'+Snon+'</td><td>'+u_value.order_id+'</td><td>'+u_value.name+'</td><td>'+u_value.qty+'</td><td>'+u_value.rate+'</td></tr>');
                                Snon++;

                            });   
                            $('.order_logs').append(templatenew);
                            
                        }
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            } 

        }




  


        
    </script>




<div class="modal fade" id="order_modal" role="dialog">
    <div class="modal-dialog modal-lg2">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:700px">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- <h4 class="modal-title smaller" > <a href="ExportSkuSales?{{$query_string}}">SKU Details Export  </a></h4> -->
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">SKU Details Export  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">

                 
                    <div class="table-header center">
                        <span>SKU Sales Details </span>
                    </div>
                  

                        <table id="simple-table" class="table table-bordered">
                        
                            <thead>
                                <th>S.No</th>
                                <th>Order No.</th>
                                <th>Product Name</th>
                                <th>Qty.</th>
                                <th>Rate</th>
                                
                            </thead>
                          
                            <tbody class="order_logs">
                            
                            </tbody>
                    
                        </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>