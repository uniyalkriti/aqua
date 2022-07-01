<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center" style="width:100%;">
   {{Lang::get('common.module_check_satus')}}
</div>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <thead>
    <tr class="info" style="color: black;">
        <th rowspan="2" style="width: 200px;">{{Lang::get('common.s_no')}}</th>
        <th rowspan="2" style="width: 200px;">{{Lang::get('common.user_name')}}</th>
        <th rowspan="2" style="width: 200px;">{{Lang::get('common.role')}}</th>
        <th rowspan="2" style="width: 200px;">{{Lang::get('common.mobile')}}</th>
        <th rowspan="2" style="width: 200px;">{{Lang::get('common.last_sync_date')}}</th>
        @if(!empty($app_module))
            @foreach($app_module as $key => $value)
                <?php 
                    $count_span = !empty($sub_module[$value->module_id])?$sub_module[$value->module_id]:array();
                ?>
                @if(COUNT($count_span) == 0)
                <th colspan="{{COUNT($count_span)}}" rowspan="2" >{{$value->module_name}}</th>
                @else
                <th colspan="{{COUNT($count_span)}}" >{{$value->module_name}}</th>
                @endif
                @if(!empty($count_span))
                    @if(COUNT($count_span) != 0)
                    
                    @endif
                @endif
            @endforeach
        @endif
    </tr>
    <tr class="info">
        @if(!empty($app_module))
            @foreach($app_module as $key => $value)
                <?php 
                    $count_span = !empty($sub_module[$value->module_id])?$sub_module[$value->module_id]:array();
                ?>
                
                @if(!empty($count_span))
                    @if(COUNT($count_span) != 0)
                    
                        @foreach($count_span as $s_key => $s_value)
                                <th style="width: 200px;">{{!empty($s_value['sub_modules_name'])?$s_value['sub_modules_name']:''}}</th>

                        @endforeach
                   
                    @endif
                @endif
            @endforeach
        @endif
         </tr>
       
       
    <!-- </tr> -->
</thead>
    <tbody>
        <?php
            $null = array();
        ?>
        @if(!empty($user_details))
            @foreach($user_details as $u_key => $u_value)
            <tr>
                <td>{{$u_key+1}}</td>
                <td>{{$u_value->first_name.' '.$u_value->middle_name.' '.$u_value->last_name}}</td>
                <td>{{$u_value->rolename}}</td>
                <td>{{$u_value->mobile}}</td>
                <td>{{!empty($u_value->last_sync_date)?$u_value->last_sync_date:'-'}}</td>
                @foreach($app_module as $key => $value)
                    <?php 
                    $count_span = !empty($sub_module[$value->module_id])?$sub_module[$value->module_id]:array();
                ?>
                    @if(COUNT($count_span) == 0)
                    <td style="width:200px;"><i class="fa fa-times"></i></td>
                    <?php
                        $null[] = 'null,'; 
                    ?>
                    @else
                    @endif
                    @if(!empty($count_span))
                        @if(COUNT($count_span) != 0)
                        
                            @foreach($count_span as $s_key => $s_value)
                               <td style="width:200px;"><i class="fa fa-check"></i></td>
                            <?php
                                $null[] = 'null,'; 
                            ?>
                            @endforeach
                       
                        @endif
                    @endif
                @endforeach
            </tr>
            @endforeach
        @endif

        
    <?php
        $implode = implode(",",$null);
      ?>

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
                                                  null,null,null,null,null,null,null,null,<?= $implode ?>,
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