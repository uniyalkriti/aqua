<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
{{Lang::get('common.attendance_time_report')}}
   
</div>
<table id="dynamic-table" class="table table-bordered" >
    <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        <th>Total No. Of Days</th>

        <th>Upto 09:30</th>
        <th>After 09:30</th>


        @foreach($datearray as $datekey=>$datedata)
                <?php 
                    $null[] = 'null';
                ?>
        <th>{{$datedata}}</th>
        @endforeach

        <th>Total {{Lang::get('common.attendance')}}</th>
        <th>Total Working Day</th>
        <th>Total Weekly Off</th>
        <th>Total Absent</th>
        <th>Total Half Day Present</th>
        <th>Total Holidays</th>
        <th>Total Leave</th>
        <th>Total Working Hour</th>



     

    </tr>
    </thead>
    <tbody>
    <?php $i=0;  ?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
        <?php $encid = Crypt::encryptString($data->user_id);
         $pid = Crypt::encryptString($data->person_id_senior);
         $weekly_off = 0;
         $absent = 0;
         $attendance = 0;
         $total_holidays = 0;
         $total_leave = 0;
         $halfday = 0;
         $present = 0;
         $whrs = array();

        
        ?>
                <tr  class="">
                    <td>{{$i+1}}</td>
                    <td>{{$data->state}}</td>
                    <td>{{$data->l4_name}}</td>
                    <td>{{$data->l5_name}}</td>
                    <td>{{$data->l6_name}}</td>
                    <td><a href="{{url('user/'.$encid)}}">{{$data->user_name}}</a></td>
                    <td>  {{$data->rolename}}</td>
                    <td>{{$data->mobile}}</td>
                    <td> <a href="{{url('user/'.$pid)}}">  {{!empty($senior_name_data[$data->person_id_senior])?$senior_name_data[$data->person_id_senior]:'N/A'}} </a> </td>
                    <td>{{$datediff+1}}</td>
                       
                    <td>
                        {{!empty($upto_check_in[$data->user_id])?$upto_check_in[$data->user_id]:0}}
                    </td>
                      <td>
                        {{!empty($after_check_in[$data->user_id])?$after_check_in[$data->user_id]:0}}
                    </td>
                    <?php 
                     // $absent = array();
                      ?>
                    @foreach ($datearray as $dkey => $dvalue)
                    <?php
                      $date_number = date('N', strtotime($dvalue));
                    ?>


                    @if(!empty($att_time[$data->user_id.$dvalue]) && !empty($checkOutTime[$data->user_id.$dvalue]))
                        @php
                        $c1 = new DateTime(!empty($att_time[$data->user_id.$dvalue])?$att_time[$data->user_id.$dvalue]:'');
                        $c2 = new DateTime(!empty($checkOutTime[$data->user_id.$dvalue])?$checkOutTime[$data->user_id.$dvalue]:'');
                        $interval = $c1->diff($c2);
                        $difference = $interval->format('%H:%i:%s');

                        $time = strtotime($checkOutTime[$data->user_id.$dvalue])-strtotime($att_time[$data->user_id.$dvalue]);
                        $workingHours = $time / 3600; 
                        $whrs[] = $time;
                        @endphp
                    @else
                        @php
                        $difference = "00:00:00";
                        $workingHours = 0; 
                        @endphp
                    @endif


                       <!-- conditions starts here -->
                        @if(isset($working_status[$data->user_id.$dvalue]))
                            @if($working_status[$data->user_id.$dvalue] == "Leave")
                            <td style="background:#FFFF00;text-align:center">
                                <font color="black"> Leave  </font> <br>
                            </td>
                             <?php $total_leave++;  ?>   

                            @elseif(!empty($att_time[$data->user_id.$dvalue]) && empty($checkOutTime[$data->user_id.$dvalue]))
                              <td style="background:#fa0a0a;text-align:center">  
                                <font color="black"> {{!empty($att_time[$data->user_id.$dvalue])?$att_time[$data->user_id.$dvalue]:'00:00:00'}}  </font> <br>
                                <font color="black"> {{!empty($checkOutTime[$data->user_id.$dvalue])?$checkOutTime[$data->user_id.$dvalue]:''}}  </font> <br>
                                <font color="black">{{$difference}}</font>
                             </td> 
                             <?php $absent++;  ?>   


                            @else
                                        @if($workingHours <= 4)
                                            @php $absent++;  @endphp
                                       @elseif($workingHours >4 && $workingHours <8)
                                            @php $halfday++; @endphp
                                       @elseif($workingHours >= 8)
                                            @php $present++;  @endphp
                                       @endif
                             <td style="background:#85F092;text-align:center">  
                                <font color="black"> {{!empty($att_time[$data->user_id.$dvalue])?$att_time[$data->user_id.$dvalue]:'00:00:00'}}  </font> <br>
                                <font color="black"> {{!empty($checkOutTime[$data->user_id.$dvalue])?$checkOutTime[$data->user_id.$dvalue]:'00:00:00'}}  </font> <br>
                                <font color="black">{{$difference}}</font>
                             </td> 
                             <?php $attendance++;   ?>   

                             @endif
                        @elseif($data->weekly_off_data == $date_number) 
                            <?php  $weekly_off++; ?>
                            <td style="background:#0099FF;text-align:center"> <font color="black"> WEEKLY/OFF </font></td>
                        @elseif(isset($holiday[$dvalue]))
                        <td style="background:#ff0099;text-align:center"> <font color="black">  {{$holiday[$dvalue]}} </font>
                        </td>
                             <?php $total_holidays++;  ?>   

                        @else
                             <td style="background:#F79F81;text-align:center">  -  </td>  
                             <?php $absent++;  ?>   
                        @endif     
                        <!-- conditions ends here -->
                    @endforeach



                    @php
                    $alltime=array_sum($whrs);
                    $allduration = intval($alltime/60);
                    $alldurationhr = intval($allduration/60);
                    $alldurationmin = intval($allduration%60);
                    $alldurationsec = intval($alltime%60); 
                    @endphp


                    <td>
                        {{COUNT($datearray)}}
                    </td>
                    <td>{{$present}}</td>
                    <td>{{$weekly_off}}</td>
                    <td>{{$absent}}</td>
                    <td>{{$halfday}}</td>
                    <td>{{$total_holidays}}</td>
                    <td>{{$total_leave}}</td>
                    <td>{{$alldurationhr.':'.$alldurationmin.':'.$alldurationsec}}</td>


                </tr>
                <?php $i++;  ?>
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
                                                 null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,<?= $implode ?>,
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