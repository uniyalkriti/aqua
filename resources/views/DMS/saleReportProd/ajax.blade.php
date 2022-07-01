
<div class="table-header center" style="background-color: white; color: black; text-align: center; font-size: 40px; font-weight:100px;">
     <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;">Sale Details Report</b>
    <div class="pull-left">
        
    </div>
    <div class="pull-right tableTools-container1"></div>
   
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
@if(!empty($status)?$status == '1' :'0' )
<table id="dynamic-table" class=" table table-bordered ">
@else
<table id="dynamic-table" class="table  table-bordered ">
@endif
   
    <thead >
        <tr>
            <th rowspan="3" style="background-color: #539639; color: black; font-weight: bold; text-align: left;">S.NO</th>
            <th rowspan="3" style="background-color: #539639; color: black; font-weight: bold; text-align: left;">TERRITORY</th>
            <th rowspan="3" style="background-color: #539639; color: black; font-weight: bold; text-align: left;">PARTY NAME</th>
            @if(!empty($mktg_catg))
                @foreach($mktg_catg as $key => $value)
                    <th colspan="{{$value->count_prod*2}}" style="background-color: #539639; color: black; font-weight: bold; text-align: center;" >{{$value->name}}</th>
                    <th rowspan="3" style="background-color: #539639; color: black; font-weight: bold; text-align: left;">T.Value</th>

                @endforeach

            @endif

            @if(!empty($prod_catg))
                <tr>
                @foreach($prod_catg as $key => $pvalue)
                    <th colspan="2" style="background-color: #90d781; color: black; font-weight: bold; text-align: left;">{{$pvalue}}</th>
                @endforeach

                </tr>
            @endif

            @if(!empty($prod_catg))
                <tr>
                @foreach($prod_catg as $key => $pvalue)
                    <th>QTY</th>
                    <th>VALUE</th>
                @endforeach
                </tr>
            @endif


            

            
        </tr>
    </thead>
    <tbody>
        
    </tbody>
    
</table>
            
 <!-- modal starts here  -->
 <div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >Status</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="table-header center">
                            Status
                           
                        </div>
                        <table class="table table-bordered table-hover" >
                            <thead class = "mythead_distibutor_list">
                                <th>Sr.No</th>
                                <th>ITEM CODE</th>
                                <th>ITEM NAME</th>
                                <th>QTY</th>
                                <th>AMT</th>
                                <th>Status</th>
                                <th>Re-Book Order No</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
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
 <!-- modal ends here -->
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
    // $('.logs_modal').click(function() {
    //         var order_no = $(this).attr('order_no');
    //         var order_date = $(this).attr('order_date');
    //         var sl_no = $(this).attr('sl_no');
    //         var status_f = '';
    //         var tt = 0;
    //         var tt_am = 0;
    //         var rvrno = '';
    //         if (order_no != '') 
    //         {
    //             $('.tbody_logs').html('');
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 }
    //             });
    //             $.ajax({
    //                 type: "POST",
    //                 url: 'https://baidyanathjhansi.msell.in/public/item_detailer_short_item',
    //                 dataType: 'json',
    //                 data: "order_no=" + order_no+"&sl_no=" + sl_no+"&order_date=" + order_date,
    //                 success: function (data) 
    //                 {
    //                     if (data.code == 401) 
    //                     {
                           
    //                     }
    //                     else if (data.code == 200) 
    //                     {
    //                         var template = '';
    //                         var Sno = 1;
    //                         $.each(data.data_return, function (u_key, u_value) {
                               
                               
    //                            if(u_value.qty_cancel != '0')
    //                            {
    //                                 if(u_value.QTYCANCELLED_FLAG == 'R')
    //                                 {
    //                                     status_f = 'Re-Book';
    //                                 }
    //                                 else
    //                                 {
    //                                     status_f = 'CANCELLED';
    //                                 }
    //                                 tt += parseInt(u_value.qty_cancel);
    //                                 var ord_amt = parseFloat(u_value.order_amt)-parseFloat((u_value.order_amt*7)/100);
    //                                 tt_am += ord_amt;

    //                                 if(u_value.r_vrno == null)
    //                                 {
    //                                     rvrno = '-'; 
    //                                 }
    //                                 else
    //                                 {
    //                                     rvrno = u_value.r_vrno;
    //                                 }

    //                                 template += ('<tr><td style="text-align:left;">'+Sno+'</td><td style="text-align:left;">'+u_value.ITEM_CODE+'</td><td style="text-align:left;">'+u_value.ITEM_NAME+'</td><td style="text-align:right;">'+u_value.qty_cancel+'</td><td style="text-align:right;">'+(ord_amt.toFixed(2))+'</td><td style="text-align:left;">'+status_f+'</td><td style="text-align:left;">'+rvrno+'</td></tr>');
    //                                 Sno++;
    //                            }
                                
    //                         });   
    //                         template += ('<tr><td colspan="3">Total</td><td>'+tt+'</td><td>'+tt_am.toFixed(2)+'</td><td></td><td></td></tr>');
    //                         $('.tbody_logs').append(template);

                            
    //                     }
    //                 },
    //                 complete: function () {
    //                     // $('#loading-image').hide();
    //                 },
    //                 error: function () {
    //                 }
    //             });
    //         }       
    //     });
        
    </script>
    <script type="text/javascript">
         jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table1')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": true},
                            null,null, null, null, null,
                            null,null, null, null,null,
                            
                            {"bSortable": true}
                        ],
                        "aaSorting": [],
                        // "sScrollY": "1000px",
                        //"bPaginate": false,

                        // "sScrollX": "100%",
                        "sScrollXInner": "120%",
                        "bScrollCollapse": true,
                        "iDisplayLength": 10,


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
            myTable.buttons().container().appendTo($('.tableTools-container1'));

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
                $('.dt-button-collection').appendTo('.tableTools-container1 .dt-buttons')
            });

            ////

            


            myTable.on('select', function (e, dt, type, index) {
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