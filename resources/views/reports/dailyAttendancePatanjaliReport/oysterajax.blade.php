<div class="clearfix">
</div>
<div class="table-header center">
   Daily Attendance Report
    <div class="pull-right tableTools-container"></div>
   
</div>
<table class="table table-bordered" style="font-size: 13px;border: 1px black">
    <thead>
    <tr>
    <?php $onleave = 0 ;$half = 0;$present = 0;$total = 0;$withoutleave = 0; $work = 0; $weekly_off = 0;?> @if(!empty($records) && count($records)>0) 
    @foreach($records as $key=>$data) 
    @foreach($users as $uk=>$u)
    <?php
        if (!empty($data[$u['person_id']]->work_date)) {
            if ($data[$u['person_id']]->work_date == 0) {
                $onleave++ ; 
            }
            elseif($data[$u['person_id']]->work=='LEAVE' || $data[$u['person_id']]->work=='Leave'){
                $onleave++; 
            }
            elseif($data[$u['person_id']]->work=='WEEKLY OFF' || $data[$u['person_id']]->work=='Weekly Off'){
                $weekly_off++; 
            }
            elseif(strtotime(date('H:i:s', strtotime($data[$u['person_id']]->work_date))) > strtotime('11:00:00')){
                $half++; 
            } else{
            // Condition check for leave 
            !empty($data[$u['person_id']]->work) && $data[$u['person_id']]->work=='LEAVE'?$onleave++:$present++; } 
        }else{
        $withoutleave++;
     }
    ?> 
    <?php
    if(!empty($data[$u['person_id']]->work)){
        $work++;
    }
    ?>
    @endforeach 
    @endforeach 
    @endif
    <td colspan="2" class="bg-primary"> Total</td>
    <td><?php echo (($present)+($withoutleave)+($half)+($onleave)); ?></td>
    <td colspan="2" class="bg-primary"> Present</td>
    <td><?php echo $present; ?></td>
    <td colspan="2" class="bg-primary"> Half Day</td>
    <td><?php echo $half; ?></td>
    <td colspan="2" class="bg-primary"> Absent</td>
    <td><?php echo $withoutleave; ?></td>
    <td colspan="2" class="bg-primary"> On Leave</td>
    <td><?php echo $onleave; ?></td>
    <td colspan="2" class="bg-primary"> Weekly Off</td>
    <td><?php echo $weekly_off; ?></td>
    <td colspan="2" class="bg-primary"></td> 
    </tr>
</thead>
</table>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
   <thead>
    <tr class="info" style="color: black;">
        <th>S.No.1</th>
        <th>Image</th>
        <th>Zone</th>
        <th>Region</th>
        <th>Belt/Tert/Area/Region</th>
        <th>Date</th>
        <th>Emp. Code</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Mobile</th>
        <th>Senior Name </th>
        <th>Attendance Status</th>
        <th>Status</th>
        <th>Attendance In Time</th>
        <!-- <th>As Per Tour Program Town</th>
        <th>As Per Tour Program Beat</th> -->
        <th>Attendance Location</th>
        <th>Attendance Type</th>
        <th>Check Out Time</th>
        <th>Check Out Location</th>
        <th>Check In Remarks</th>
        <th>Check Out Remarks</th>
        <!-- <th>Working Hours</th>
        <th>Productive Hours</th>
        <th>Productive Calls</th>
        <th>Secondery sales(RV)</th>
        <th>Check Out Total Call</th>
        <th>Check Out Total Productive Call</th>
        <th>Check Out Total RV</th> -->
    </tr>
</thead>
    <tbody>
    <?php $i = 1;?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
            @foreach($users as $uk=>$u)
            <?php 
            
            $encid = Crypt::encryptString($u['person_id']); 
            $senior_id = Crypt::encryptString($u['senior_id']); 
            $check = !empty($data[$u['person_id']]->work)?$data[$u['person_id']]->work:'';
            
            if($check == 'LEAVE' || $check == 'Leave')
            {
                $color_code = '#dfbf9f';

            }

            elseif($check == 'WEEKLY OFF' || $check == 'Weekly Off')
            {
                $color_code = '#ffff00';

            }
            elseif(!empty($data[$u['person_id']]->work_date))
            {
                $color_code = '#81FF5B';

            }
            
            else
            {
                $color_code = '#FFA5A5';

            }
            ?>

               
                <tr style="background: {{ $color_code }}"  class="">
                    <td>{{$i}}</td>
                    <td>
                        {{-- @if(!empty(http://demo.msell.in/demo_api/webservices/mobile_images/Attendance/{{$data[$u['person_id']]->image_name}}))
                        <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                            src="http://demo.msell.in/demo_api/webservices/mobile_images/Attendance/{{!empty($data[$u['person_id']]->image_name)?$data[$u['person_id']]->image_name:'N/A'}}" alt= " "/>
                        @else --}}
                        <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                            src="{{asset('attendance_images/')}}{{!empty($data[$u['person_id']]->image_name)?'/'.$data[$u['person_id']]->image_name:'N/A'}}" alt= " "/>
                        {{-- @endif --}}
                    
                    </td>
                    <td>{{$u['zone']}}</td>
                    <td>{{$u['region']}}</td>
                    <td>{{$u['region_txt']}}</td>
                    <td>{{!empty($key)?date('d-M-Y',strtotime($key)):'N/A'}}</td>
                    <td>{{$u['emp_code']}}</td>
                    <td><a href="{{url('user/'.$encid)}}">{{$u['uname']}}</a></td>
                    <td>{{$u['role']}}</td>
                    <td>{{$u['mobile']}}</td>
                    <td><a href="{{url('user/'.$senior_id)}}">{{$u['senior_name']}}</a></td>
                                

                    <td><?php
                            $user_id = $u['person_id'];
                            if (!empty($data[$u['person_id']]->work_date))
                            {
                                // if ($data[$u['person_id']]->work_date == 0) 
                                // {
                                //     echo "Absent";
                                // } 
        //                         elseif($data[$u['person_id']]->work=='Leave')
        //                         {
        //                             echo "Leave";
        //                         } 
        //                         elseif (strtotime(date('H:i:s', strtotime($data[$u['person_id']]->work_date))) > strtotime('11:00:00'))     // time changed as discussed with bhoopender sir 
        //                         {
        //                             echo "Half Day";
        //                         }
                                
        //                         else 
        //                         {
        // //                            Condition check for leave
        //                             echo !empty($data[$u['person_id']]->work) && $data[$u['person_id']]->work=='Leave'?'Leave':'Present';
        //                         }
                               
                                echo "Marked";
                                
                            }
                            else
                            {
                                echo "Not Marked";

                                // dd($user_id.$key);
                                if(!empty($weekly_off_query[$user_id.$key]))
                                {
                                    echo "weekly off";
                                }
                                else
                                {
                                    echo "";
                                }
                            }
                        ?>
                    </td>
                
                    <td>{{!empty($data[$u['person_id']]->work)?$data[$u['person_id']]->work:'N/A'}}</td>
                    <td>{{ !empty($data[$u['person_id']]->work_date)?date('d-M-Y H:i:s',strtotime($data[$u['person_id']]->work_date)):'00:00:00' }}</td>
                    <!-- <td>{{ !empty($data[$u['person_id']]->work_date)?$data[$u['person_id']]->work_date:'N/A' }}</td> -->
                 
                    <td>{{!empty($data[$u['person_id']]->track_addrs)?$data[$u['person_id']]->track_addrs:'N/A'}}</td>
                    @if(!empty($data[$u['person_id']]->att_status) && $data[$u['person_id']]->att_status == 1)
                    <td>By SFA</td>
                    @elseif(!empty($data[$u['person_id']]->att_status) && $data[$u['person_id']]->att_status == 2)
                    <td>Manually</td>
                    @else
                    <td>Not Marked</td>
                    @endif
                    <td>{{ !empty($data[$u['person_id']]->check_out_date)?date('d-M-Y H:i:s',strtotime($data[$u['person_id']]->check_out_date)):'00:00:00' }}</td>
                    <td>{{!empty($data[$u['person_id']]->check_out_address)?$data[$u['person_id']]->check_out_address:'NA'}}</td>
                    <td>{{!empty($data[$u['person_id']]->check_in_remarks)?$data[$u['person_id']]->check_in_remarks:'NA'}}</td> <!-- added by rohit -->
                    <td>{{!empty($data[$u['person_id']]->check_out_remarks)?$data[$u['person_id']]->check_out_remarks:'NA'}}</td> <!-- //added by rohit  -->
                  

                </tr>
                <?php $i++?>
            @endforeach
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
                                                  null,null,null, null, null, null,null,null,
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