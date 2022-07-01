<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
User Sale Order Report
   
</div>
<table id="dynamic-table" class="table table-bordered" >
    <thead>
    <tr>
        <th>S.No.</th>
        <th>Date</th>
        <th>Order No</th>
        <th>State</th>
        <th>Emp. Code</th>
        <th>User Name</th>
        <th>Senior Name</th>
        <th>Designation</th>
        <th>Mobile</th>
        <th>Distributor Name</th>
        <th>Retailer Name</th>
        <th>Retailer Number</th>
        <th>Beat Name</th>
        <th>Sale Remarks</th>
        <th>No Productive Reason</th>
        <th>Product Name</th> 
        <th>Quantity</th>
        <th>Scheme Qty</th>
        <th>Rate</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=[]; $count_call_0=[];?>


    @if(!empty($records) && count($records)>0)
    
       @foreach($records as $k=> $r)
            <?php 
            $user_id = Crypt::encryptString($r->user_id); 
            $senior_id = Crypt::encryptString($r->senior_id); 
            $retailer_id = Crypt::encryptString($r->retailer_id); 
            $dealer_id = Crypt::encryptString($r->dealer_id); 
            ?>
                 @if(count($details[$r->order_id])>0)

                        <?php  $total=0;
                        $totalqty=0;
                        $totalweight=0; ?>
                            @foreach($details[$r->order_id] as $k1=>$data1)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$r->date}}</td>
                                    <td>{{$r->order_id}}</td>
                                    <td>{{$r->l3_name}}</td>
                                    <td>{{$r->emp_code}}</td>
                                    <td><a href="{{url('user/'.$user_id)}}"> {{$r->user_name}}</a></td>
                                    <td><a href="{{url('user/'.$senior_id)}}"> {{!empty($senior_name[$r->senior_id])?$senior_name[$r->senior_id]:'N/A'}}</a></td>
                                    <td>{{$r->role_name}}</td>
                                    <td>{{$r->mobile}}</td>
                                    <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
                                    <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
                                    <td>{{!empty($r->retailer_other_number)?$r->retailer_other_number:$r->retailer_landline}}    </td>
                                    <td>{{$r->l7_name}}</td>

                                    <td>{{!empty($r->remarks)?$r->remarks:'NA'}}    </td>
                                    @if($r->non_productive_reason_id == 0)
                                        <td>{{''}}</td>
                                        @else
                                            <td>{{!empty($non_productive_reason_name[$r->non_productive_reason_id])?$non_productive_reason_name[$r->non_productive_reason_id]:''}}</td>
                                    @endif
                                
                                        
                                                <?php $value = 0; ?>
                                                        <td>{{$data1->product_name}}</td>
                                                        <td>{{$data1->quantity}}</td>
                                                        <td>{{!empty($product_percentage[$data1->product_id])?$product_percentage[$data1->product_id].'%':''}}</td>
                                                        <td>{{$data1->rate}}</td>
                                                        @if(!empty($product_percentage[$data1->product_id]))
                                                        <?php  $value = ($data1->rate*$data1->quantity)*$product_percentage[$data1->product_id]/100; ?>

                                                        @else
                                                            <?php $value = 0; ?>
                                                        @endif
                                                        <td>{{($data1->rate*$data1->quantity - $value)}}</td> 
                                                    <?php 
                                                    $total+=$data1->rate*$data1->quantity - $value; 
                                                    $totalqty+=$data1->quantity;
                                                    $totalweight+=$data1->weight;

                                                    $gtotal+=$data1->rate*$data1->quantity - $value; 
                                                    $gqty+=$data1->quantity;
                                                    $gweight+=$data1->weight;
                                                    ?>
                                      
                                </tr>
                                <?php $i++; ?>

                             @endforeach
                @else
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$r->date}}</td>
                    <td>{{$r->order_id}}</td>
                    <td>{{$r->l3_name}}</td>
                    <td>{{$r->emp_code}}</td>
                    <td><a href="{{url('user/'.$user_id)}}"> {{$r->user_name}}</a></td>
                    <td><a href="{{url('user/'.$senior_id)}}"> {{!empty($senior_name[$r->senior_id])?$senior_name[$r->senior_id]:'N/A'}}</a></td>
                    <td>{{$r->role_name}}</td>
                    <td>{{$r->mobile}}</td>
                    <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
                    <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
                    <td>{{!empty($r->retailer_other_number)?$r->retailer_other_number:$r->retailer_landline}}    </td>
                    <td>{{$r->l7_name}}</td>

                    <td>{{!empty($r->remarks)?$r->remarks:'NA'}}    </td>
                    @if($r->non_productive_reason_id == 0)
                        <td>{{''}}</td>
                        @else
                            <td>{{!empty($non_productive_reason_name[$r->non_productive_reason_id])?$non_productive_reason_name[$r->non_productive_reason_id]:''}}</td>
                    @endif
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td> 
                </tr>  
                <?php $i++; ?>

            @endif

        @endforeach  
        
    @endif
    </tbody>
    <tr>
    <th colspan="15"><strong>Grand Total</strong></th>

    <td>{{$gqty}}</td>
    <td>-</td>
    <td>-</td>
    <td>{{$gtotal}}</td>

</tr>  
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
                                                 null,null,null,null,null,null,null,null,null,null,
                                                 null,null,null,null,null,null,null,null,
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