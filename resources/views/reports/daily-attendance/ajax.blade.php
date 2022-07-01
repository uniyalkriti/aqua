<div class="clearfix">
</div>
<div class="table-header center">
   {{Lang::get('common.daily_attendance')}}
    <div class="pull-right tableTools-container"></div>
   
</div>
        <!-- <link rel="stylesheet" href="{{asset('assets/css/dataTables.min.css')}}" /> -->
        <style type="text/css">
            #dynamic-table table tr{
                height: 100px;

            }
            #dynamic-table table th{
                width: 300px;
                
            }
        </style>

<table class="table table-bordered" style="font-size: 13px;border: 1px black;">
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
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
   <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>Check In {{Lang::get('common.image')}}</th>
        <th>{{Lang::get('common.date')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        <?php  if($company_id == 43){
            echo "<th>DGM/RSM </th>";
            $btw_null = "null,";
        }else{
            $btw_null = " ";
        }
        ?>        
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.status')}}</th>
        <th>{{Lang::get('common.status')}}</th>

        <th>Task Of The Day</th>



        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.as_per_tour')}} {{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.as_per_tour')}} {{Lang::get('common.location7')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.location')}}</th>

        <th>{{Lang::get('common.attendance')}} Co-ordinates</th>


        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.type')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.location')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.remarks')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.remarks')}}</th>
        <th>{{Lang::get('common.working')}} {{Lang::get('common.hour')}}</th>
        <th>{{Lang::get('common.productive')}} {{Lang::get('common.hour')}}</th>

        <th>Daily Reporting</th>


        <th> {{Lang::get('common.total_call')}}</th>



        <th>{{Lang::get('common.productive_call')}}</th>
        <th>{{Lang::get('common.secondary_sale')}}</th>

         <th>First Call Location</th>
        <th>Last Call Location</th>

        
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.total_call')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.total')}} {{Lang::get('common.productive_call')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th>


       



    </tr>
</thead>
    <tbody>
    <?php $i = 1;?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
            @foreach($users as $uk=>$u)
            <?php 
            $distance = '0';
            if(!empty($u['residential_lat_lng']) && !empty($data[$u['person_id']]->att_lat_lng)){

                $unit = 'Mi';
                $explodeResidential = explode(',',$u['residential_lat_lng']);
                $latitude1 = !empty($explodeResidential[0])?$explodeResidential[0]:'0.0';
                $longitude1 = !empty($explodeResidential[1])?$explodeResidential[1]:'0.0';

                $explodeAttendnce = explode(',',$data[$u['person_id']]->att_lat_lng);
                $latitude2 = !empty($explodeAttendnce[0])?$explodeAttendnce[0]:'0.0';
                $longitude2 = !empty($explodeAttendnce[1])?$explodeAttendnce[1]:'0.0';

                if($latitude1 == '0.0' || $longitude1 == '0.0' || $latitude2 == '0.0' || $longitude2 == '0.0')
                {
                    $distance = 0;
                }
                else
                {
                    // dd($longitude2);
                    // if($longitude1 && )
                    $theta = (double)($longitude1) - $longitude2;
                    // dd($theta);
                    $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = rad2deg($distance);
                    $distance = $distance * 60 * 1.1515; switch($unit) {
                      case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
                     }
                }

                 



            }




            
            $encid = Crypt::encryptString($u['person_id']); 
            $senior_id = Crypt::encryptString($u['senior_id']); 
            $check = !empty($data[$u['person_id']]->work)?$data[$u['person_id']]->work:'';
            
            if($check == 'LEAVE' || $check == 'Leave')
            {
                if($company_id == 87){
                $color_code = '#FFA5A5';
                }else{
                $color_code = '#dfbf9f';
                }


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


                    <td class="profile-activity clearfix">
                        @if(!empty($data[$u['person_id']]->image_name))
                        <a id="user_image" class="“cboxElement”" href="#user<?=$i?>" data-toggle="modal" data-rel="“colorbox”">
                            <img id="user_image" width="50" height="50"  src="{{asset('attendance_images/')}}{{!empty($data[$u['person_id']]->image_name)?'/'.$data[$u['person_id']]->image_name:'N/A'}}" alt=" " />
                        </a>
                        @endif
                    </td>


                
                    <td>{{!empty($key)?date('d-M-Y',strtotime($key)):'N/A'}}</td>
                    <td>{{$u['l3_name']}}</td>
                    <td>{{$u['l4_name']}}</td>
                    <td>{{$u['l5_name']}}</td>
                    <td>{{$u['l6_name']}}</td>
                    <td>{{$u['emp_code']}}</td>
                    <td><a href="{{url('user/'.$encid)}}">{{$u['uname']}}</a></td>
                    <td>{{$u['role']}}</td>
                    <td>{{$u['mobile']}}</td>
                    <td><a href="{{url('user/'.$senior_id)}}">{{$u['senior_name']}}</a></td>
                    <?php  
                    if($company_id == 43){
                        echo "<td>";
                        foreach($btwdatasenior as $skey => $svalue){
                            if(in_array($u['person_id'],(array)$svalue)){
                                echo !empty($dgm_rsm_for_btw[$skey])?$dgm_rsm_for_btw[$skey]:'NA';
                            }
                        }
                        echo "</td>";
                    }
                        //     if($company_id == 43)
                        // echo "<td>-</td>";
                        ?>      
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


                    <?php
                       $wsid =  !empty($mtp_task_of_the_day[$user_id.$key])?$mtp_task_of_the_day[$user_id.$key]:'0';
                    ?>
                    <td>{{!empty($work_status[$wsid])?$work_status[$wsid]:'N/A'}}</td>



                    <td>{{ !empty($data[$u['person_id']]->work_date)?date('d-M-Y H:i:s',strtotime($data[$u['person_id']]->work_date)):'00:00:00' }}</td>
                    <!-- <td>{{ !empty($data[$u['person_id']]->work_date)?$data[$u['person_id']]->work_date:'N/A' }}</td> -->
                    <td>{{!empty($mtp_towm[$user_id.$key])?$mtp_towm[$user_id.$key]:'N/A'}}</td>
                    <td>{{!empty($mtp_beat[$user_id.$key])?$mtp_beat[$user_id.$key]:'N/A'}}</td>
                    <td>{{!empty($data[$u['person_id']]->track_addrs)?$data[$u['person_id']]->track_addrs:'N/A'}}</td>


                    @if($distance > 1.000)
                    <td><font color="green">{{!empty($data[$u['person_id']]->att_lat_lng)?$data[$u['person_id']]->att_lat_lng:''}}</font></td>
                    @else
                    <td><font color="red">{{!empty($data[$u['person_id']]->att_lat_lng)?$data[$u['person_id']]->att_lat_lng:''}}</font></td>
                    @endif




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
                    <td>
                        @if (!empty($data[$u['person_id']]->work_date) && !empty($data[$u['person_id']]->check_out_date))
                            @php
                                $c11 = new DateTime($data[$u['person_id']]->work_date);
                                $c21 = new DateTime($data[$u['person_id']]->check_out_date);
                                $interval1 = $c11->diff($c21);
                                echo $interval1->format('%h') . " Hours " . $interval1->format('%i'). " Minutes";
                            @endphp
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @if (!empty($first_call[$key.$user_id]) && !empty($last_call[$key.$user_id]))
                            @php
                                $c1 = new DateTime(!empty($first_call[$key.$user_id])?$first_call[$key.$user_id]:'');
                                $c2 = new DateTime(!empty($last_call[$key.$user_id])?$last_call[$key.$user_id]:'');
                                $interval = $c1->diff($c2);
                                echo $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
                            @endphp
                        @else
                            0
                        @endif
                    </td>

                <td>{{ !empty($daily_reporting[$user_id.$key])?'YES':'NO' }}</td>



                <td>{{ !empty($total_calls[$user_id.$key])?$total_calls[$user_id.$key]:'0' }}</td>

                <td>{{ !empty($productive_calls[$user_id.$key])?$productive_calls[$user_id.$key]:'0' }}</td>
                    <td>{{!empty($sale_value[$user_id.$key])?$sale_value[$user_id.$key]:'0'}}</td>



                    @if(!empty($first_call[$key.$user_id]))
                        <?php $fsttime = $first_call[$key.$user_id]; ?>
                        <td>{{!empty($call_location[$key.$user_id.$fsttime])?$call_location[$key.$user_id.$fsttime]:'NA'}}</td>
                    @else
                        <td></td>
                    @endif




                     @if(!empty($last_call[$key.$user_id]))
                        <?php $lsttime = $last_call[$key.$user_id]; ?>
                        <td>{{!empty($call_location[$key.$user_id.$lsttime])?$call_location[$key.$user_id.$lsttime]:'NA'}}</td>
                    @else
                        <td></td>
                    @endif




                    <td bgcolor="#f7bd52">{{ !empty($checkoutarr[$user_id.$key]['tc'])?$checkoutarr[$user_id.$key]['tc']:'0' }}</td>
                    <td bgcolor="#f7bd52">{{ !empty($checkoutarr[$user_id.$key]['tpc'])?$checkoutarr[$user_id.$key]['tpc']:'0' }}</td>
                    <td bgcolor="#f7bd52">{{ !empty($checkoutarr[$user_id.$key]['tsv'])?$checkoutarr[$user_id.$key]['tsv']:'0' }}</td>

                </tr>


<!-- START MODEL BOX -->
<div id="user<?=$i?>" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Check In Image</h4>
            </div>

            <div class="modal-body">

                <img class="center19" id="user_image" style="height: 500px;width: 500px;" class="nav-user-photo" 
                src="{{asset('attendance_images/')}}{{!empty($data[$u['person_id']]->image_name)?'/'.$data[$u['person_id']]->image_name:'N/A'}}" 
                onerror="this.onerror=null;this.src='{{asset('attendance_images/')}}{{!empty($data[$u['person_id']]->image_name)?'/'.$data[$u['person_id']]->image_name:'N/A'}}';" /> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- END MODEL BOX -->


                <?php $i++?>





            @endforeach
        @endforeach
    @endif

    </tbody>
</table>

    <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
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
                                        .wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                {"bSortable": false},
                                                  null,null,null,null,null,null, null, null, null, null,
                                                  null,null,null, null, null, null,null,null,null,null,null,null,null,null,null,null,
                                                  null,null,null,null,null,null, null,null,<?=$btw_null?> 
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],
                                            // "sScrollY": "300px",
                                            //"bPaginate": false,
                                            "autoWidth": true,
                                            "autoheight": false,

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