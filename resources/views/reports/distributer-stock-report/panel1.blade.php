<?php 
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?>
@if(!empty($records))
  <!--   <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a> -->
        @if($company_id == 62)
        <button onclick="window.location.href='makeOpeningStock?{{$query_string}}'" href="javascript:void(0)" class="nav-link pull-right">
        <i class="fa fa-bar-chart"></i> Make Opening Stock</button>
        @endif
@endif
<div class="clearfix">
</div>
<div class="table-header center">
  {{Lang::get('common.distributor')}} {{Lang::get('common.stock')}}
    <div class="pull-right tableTools-container"></div>
   
</div>
<table id="dynamic-table" class="table table-bordered" >
   <thead>
            <th>{{Lang::get('common.s_no')}}</th>
            <th>{{Lang::get('common.date')}}</th>

            <th>{{Lang::get('common.order_id')}}</th>
            <th>{{Lang::get('common.location3')}}</th>
            <th>{{Lang::get('common.location4')}}</th>
            <th>{{Lang::get('common.location5')}}</th>
            <th>{{Lang::get('common.location6')}}</th>
            <th>{{Lang::get('common.emp_code')}}</th>
            <th>{{Lang::get('common.username')}}</th>
            <th>{{Lang::get('common.role_key')}}</th>
            <th>{{Lang::get('common.user_contact')}}</th>
            <th>{{Lang::get('common.distributor')}} Name</th>
            <th>{{Lang::get('common.catalog_4')}}</th>
            <th>Landing Cost</th>
            <th>{{Lang::get('common.piece')}} Cost</th>
            <th>{{Lang::get('common.stock')}} QTY({{Lang::get('common.case')}})</th>
            <th>{{Lang::get('common.stock')}} Qty({{Lang::get('common.piece')}})</th>
            <th>{{Lang::get('common.total')}} Amount</th>
            <th>Mfg {{Lang::get('common.date')}}</th>
            <th>Exp {{Lang::get('common.date')}}</th>
            <th>Action</th>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
        <?php $i = 1 ?>
        @foreach($records as $record)
           <?php 
            $did = Crypt::encryptString($record->did); 
            $uid = Crypt::encryptString($record->uid); 
            ?>

            <tr>
                <td>{{$i++}}</td>
                <td>{{!empty($record->submit_date_time)?date('d-M-Y',strtotime($record->submit_date_time)):'NA'}}</td>
                
                <td>{{$record->order_id}}</td>
                <td>{{$record->l3_name}}</td>
                <td>{{$record->l4_name}}</td>
                <td>{{$record->l5_name}}</td>
                <td>{{$record->l6_name}}</td>
                <td>{{!empty($record->emp_code)?$record->emp_code:''}}</td>
                <td><a href="{{url('user/'.$uid)}}">{{$record->user_name}}</a></td>
                <td>{{$record->rolename}}</td>
                <td>{{$record->mobile}}</td>
                <td><a href="{{url('distributor/'.$did)}}">{{$record->dealer_name}}</a></td>
                <td>{{$record->product_name}}</td>
                <td>{{$a=$record->mrp}}</td>
                <td>{{$b=$record->pcs_mrp}}</td>
                <td>{{$n1[]=$bb=$record->cases}}</td>
                <td>{{$n2[]=$aa=$record->stock_qty}}</td>
                <td>{{$total[]=($b*$aa)+($a*$bb)}}</td>
                <td>{{!empty($record->mfg_date) && $record->mfg_date!='0000-00-00'?date('d-M-Y',strtotime($record->mfg_date)):'NA'}}</td>
                <td>{{!empty($record->exp_date) && $record->exp_date!='0000-00-00'?date('d-M-Y',strtotime($record->exp_date)):'NA'}}</td>

                <td>
                  <a href="#" orderid="{{ $record->order_id }}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="Update">
                        <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div><br>
                    </a>
                </td>

            </tr>
        @endforeach

    @endif
    </tbody>
    <tfoot>
         @if(!empty($total))
            <tr>
                <td  style="background: #2D5DAC;color: white">{{Lang::get('common.total')}}</td>
               
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td >{{array_sum($n1)}}</td>
                <td >{{array_sum($n2)}}</td>
                <td >{{array_sum($total)}}</td>
                <td ></td>
                <td ></td>
                <td ></td>
            </tr>
        @endif
   
    </tfoot>
</table>




<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:1300px;">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReportDetails()" href="javascript:void(0)" class="nav-link">Stock Details  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="get" id="filter_distributor" action="primaryStockUpdate" enctype="multipart/form-data">

                 
                    <div class="table-header center">
                        <span>Stock Details </span>
                    </div>
                  

                        <table id="simple-table-details" class="table table-bordered">
                        
                            <thead>
                                <th>Sr.No</th>
                                <th>Order No.</th>
                                <th>Product Name</th>
                                <th>Pcs</th>
                                <th>Pcs Rate</th>

                                  <th>Cases</th>
                                <th>Cases Rate</th>


                                <th>Amount</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
                            </tbody>
                    
                        </table>

                        <div class="col-lg-4">
                            <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Update
                                </button>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>







<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.colVis.min.js')}}"></script>
  
    <script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>




<script>
    $('.logs_modal').click(function() {
            var orderid = $(this).attr('orderid');
            if (orderid != '') 
            {
                $('.tbody_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/stockOrderDetails',
                    dataType: 'json',
                    data: "orderid=" + orderid,
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
                               

                                  template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.pcs+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.rate+'></td><td><input type="text" class="case_qty_val" required="required" name="cases[]" value='+u_value.cases+'></td><td><input readonly type="text" class="case_rate_val" required="required" name="pr_rate[]" value='+u_value.pr_rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');


                                Sno++;
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



<script type="text/javascript">
          $('#simple-table-details').on('keyup','.qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        var case_qty_val   =tr.find('.case_qty_val').val();
        var case_rate_val   =tr.find('.case_rate_val').val();

        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        stk_val.val(tval);
  })
  </script>



  <script type="text/javascript">
          $('#simple-table-details').on('keyup','.case_qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        var case_qty_val   =tr.find('.case_qty_val').val();
        var case_rate_val   =tr.find('.case_rate_val').val();

        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        stk_val.val(tval);
  })
  </script>















    <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,
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