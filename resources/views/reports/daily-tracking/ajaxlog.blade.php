
<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
   {{Lang::get('common.daily_tracking')}} Log
   
</div>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
<thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.date')}}</th>

        <!-- <th>{{Lang::get('common.location2')}}</th> -->
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>

        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        
    <?php 
        for($i = 1; $i <= $constant[0];$i++)
        {
            echo '<td>TRACK - '.($i).'</td>';
                             
        }
    ?>
       <th>View Your route</th>
    </tr>
</thead>
    <tbody>
        <?php $h=1; $count=0; $track_count=29; $test = [];
        $emp_code = App\CommonFilter::emp_code('person');
        $senior_name = App\CommonFilter::senior_name('person');

         ?>
        @if(!empty($records) && count($records)>0)
            @foreach($records as $key=>$data)
             <?php
              $encid = Crypt::encryptString($data->user_id);
              $sencid = Crypt::encryptString($data->person_id_senior);
              ?>
                <tr>
                    <td>{{$h++}}</td>
                    <td>{{$data->track_date}}</td>

                    <!-- <td>{{$data->l2_name}}</td> -->
                    <td>{{$data->l3_name}}</td>
                    <td>{{$data->l4_name}}</td>
                    <td>{{$data->l5_name}}</td>
                    <td>{{$data->l6_name}}</td>
                    <td>{{!empty($emp_code[$data->user_id])?$emp_code[$data->user_id]:''}}</td>
                    <td><a href="{{url('user/'.$encid)}}">{{$data->person_fullname}}</a></td>
                    <td>{{$data->role_name}}</td>
                    <td>{{$data->mobile}}</td>
                    <td><a href="{{url('user/'.$sencid)}}">{{!empty($senior_name[$data->person_id_senior])?$senior_name[$data->person_id_senior]:''}}</a></td>

                    <?php ksort($track_time); ?>
                    @if(!empty($track_time[$data->user_id][$data->track_date]))
                        @foreach($track_time[$data->user_id][$data->track_date] as $Tkey => $Tvalue)
                            @php
                            $count=count($Tvalue);
                            $test = array();
                            $exit=0;
                            @endphp

                            @foreach($Tvalue as $T1key => $T1value)
                                @if( $T1value->status == 'Attendance' || $T1value->status == 'CheckOut' || $T1value->track_time <='19:00:00' && $T1value->track_time >='09:00:00' )

                                    <td>
                                        <?php 
                                        echo '<b> Module : </b> '.$T1value->status.'<br><strong>Track Time: </strong>'.$T1value->track_time.'<br><strong>Track Address: </strong>'.$T1value->location.'<br> <strong>Battery Status : '.$T1value->battery_status.'%</strong>';
                                        ?>
                                    </td>
                                    <?php  
                                    // $test = array();
                                    $test[] = $T1value->status; 
                                        $exit++;
                                        if($exit==$constant[0])  break;  
                                    ?>
                                @endif
                                
                                
                            @endforeach
                        @endforeach

                    @endif
                    <?php 
                    $count = count($test);
                    // dd($count);/
                        for($k=0; $k<=$track_count-($count); $k++)
                        {
                            echo'<td>NA</td>';
                        }    


                    ?>
                    <td>   <a href="{{url('user_tracking/'.$encid.'?date='.$data->track_date)}}">click</a> </td>

                </tr>
            @endforeach
        @endif
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
                                                  null,null,null,null,null,null, null, null, null, null,
                                                  null,null,null,null,null,null, null, null, null, null,
                                                  null,null,null,null,null,null, null, null, null, null,
                                                  null,null,null,null,null,null,null,null,null,null,
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            "sScrollY": "1300px",
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