<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
{{Lang::get('common.payment_recieved')}}
   
</div>
<form action="submit_payment_adjusment" id="submit_payment_adjusment_form" method="POST" enctype="multipart/form-data" >
                            {!! csrf_field() !!}

    <table id="dynamic-table" class="table table-bordered">
     
        <thead>
            <th >S.No.</th>
            <th >Code</th>
            <th >Distributor Name</th>
            <th >Invoice No.</th>
            <th >Order Value</th>
            <th >Recieved Payment</th>
            <th >Balance</th>
            <th >Payment</th>
            <th >Claim adjusmaint</th>
            <th >Type</th>
            <th >Amount</th>
            <th >Date</th>
        </thead>
      
        <tbody>
            <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();?>

            @if(!empty($records))
            
                @foreach($records as $k=> $r)

                    
                
                    <?php 
               
                    $dealer_id = Crypt::encryptString($r->dealer_id); 
                    ?>
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$r->dealer_code}}</td>
                        <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
                        <td>{{$r->invoice_no}}</td>
                        <td>{{$x = !empty($order_value[$r->order_id])?$order_value[$r->order_id]:'0'}}</td>
                        <td>{{$y = !empty($r->amount)?$r->amount:'0'}}</td>
                        <td>{{$x-$y}}</td>
                        <td>{{$r->payment_mode}}</td>
                        
                        <td>
                            <input type="hidden" name="dealer_id[]" class="form-control" value="{{$r->dealer_id}}">
                            <input type="hidden" name="order_id[]" value="{{$r->order_id}}"><input type="text" name="claim_adjustment[]" id="claim_adjustment" value="{{!empty($r->claim_adjustment)?$r->claim_adjustment:'0'}}" autocomplete="off">    
                        </td>
                        <td>
                            <select  name="claim_adjustment_mode[]" id="claim_adjustment_mode" class="form-control input-sm">
                                <option value="">Select</option>
                                @if(!empty($mode))
                                    @foreach($mode as $l6_key=>$l6_data)
                                        <option value="{{$l6_key}}">{{$l6_data}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            <input type="text" name="claim_adjustment_amount[]" id="claim_adjustment_amount"  autocomplete="off">    
                        </td>
                        <td>
                            <input type="text" name="claim_adjustment_date[]" id="claim_adjustment_date"  autocomplete="off">    
                        </td>
              
                        
                    </tr>
                @endforeach  

                   
            @endif
        </tbody>
        <tfoot>
            <td><input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary"></td>
            
        </tfoot>
    </table>

</form>
<script type="text/javascript">
    



        $(document).ready(function (e) {
            $('#submit_payment_adjusment_form').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(data){

                        // console.log(data);
                        // console.log(data.code);
                        // if(data.code == 200)
                        // {
                            $.alert('Claim SuccessFully Submitted Againt Order No: '+data.order_id+' !!');
                            setTimeout("window.parent.location = ''", 150);                           
                        // }
                        // else
                        // {
                        //     $.alert('Enter Any value which must be greater the zero !!');

                        // }
                        
                    },
                    error: function(data){
                        // console.log("error");
                        // console.log(data.status);
                        $.alert('Somethig Went Wrong Error-Code : '+data.status+' !!');

                    }
                });
            }));

        });
</script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>

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
                                                 null, null, null,null,  null, null, null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "300px",
                                            //"bPaginate": false,

                                            "sScrollX": "100%",
                                            "sScrollXInner": "120%",
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


