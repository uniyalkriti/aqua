<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
   {{Lang::get('common.mothly_attendance')}}
   
</div>
<table id="dynamic-table" class="table table-bordered" >
    <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.location1')}}</th>
        <th>{{Lang::get('common.location2')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.email')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        <th>Senior {{Lang::get('common.role_key')}}</th>
          <th>{{Lang::get('common.status')}}</th>
        <th>{{Lang::get('common.deactivate_date')}}</th>
        @if(!empty($datesDisplayArr))
            @foreach($datesDisplayArr as $keyd=>$datad)
                <?php 
                    $null[] = 'null';
                ?>
                <th>{{$datad}}</th>
            @endforeach
        @endif
        <th>{{Lang::get('common.total')}} Days</th>
        <th>{{Lang::get('common.total')}} Attd</th>
        <th>{{Lang::get('common.total')}} present</th>


         @if(!empty($working_status))
          @foreach($working_status as $keyw=>$dataw)
              <?php
              $null[] = 'null';
              ?>
              <th>{{$dataw}}</th>
          @endforeach
      @endif 
      <th>Attendence Not Marked</th>

      <th>{{Lang::get('common.total')}} Half Day</th>
       <!--  <th>Total leave</th>
        <th>Total Holiday</th>
        <th>Total meeting</th>
        <th>Total WeekOff</th> -->
    </tr>
</thead>
    <tbody>
    <?php $i = 1;
        $tc=0;
        $pc=0;
        $tq=0;
        $tv=0;
        $total_attd=0;
    ?>
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php 
        
        $encid = Crypt::encryptString($data["user_id"]);
        $person_id_senior = Crypt::encryptString($data["person_id_senior"]);
        
        ?>

                <tr  class="">
                    <td style="background-color:#f5d0a9">{{$i}}</td>
                    <td style="background-color:#f5d0a9">{{$data['l2_name']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['l3_name']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['emp_code']}}</td>
                    <td style="background-color:#f5d0a9"><a href="{{url('user/'.$encid)}}">{{$data['user_name']}}</a></td>
                    <td style="background-color:#f5d0a9">{{$data['role_name']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['mobile']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['email']}}</td>
                    <td style="background-color:#f5d0a9"><a href="{{url('user/'.$person_id_senior)}}">{{!empty($senior[$data['person_id_senior']])?$senior[$data['person_id_senior']]:'NA'}}</a></td>
                    <td style="background-color:#f5d0a9">{{!empty($senior_role[$data['person_id_senior']])?$senior_role[$data['person_id_senior']]:'NA'}}</td>

                     <td style="background-color:#f5d0a9">{{$data['status']}}</td>
                    <td style="background-color:#f5d0a9">{{$data['date_de']}}</td>


                    @if(!empty($datesArr))
                    <?php 
                    $total_attd=0; 
                    $count_work_status=array();
                    $pr_count = array();
                    $half = array();
                    ?>
                    @foreach($datesArr as $keydd=>$datadd)
                    <?php 
                    $pr_array = array(89,91,92,94,97);
                    if(!empty($data[$keydd]->id)) 
                    {
                        $total_attd++; 
                        if(!empty($data[$keydd]->check_out))
                        {
                            if(in_array($data[$keydd]->id,$pr_array))
                            {
                                 $same_date = date('Y-m-d');
                                if($data[$keydd]->check_in_date == $same_date && ($data[$keydd]->check_in  >= '10:30:00' ) )
                                {
                                    $half[]=$data[$keydd]->id;
                                    $td = "<td style='background-color:#ffc433'>Half Day</td>";
                                }    
                                elseif(($data[$keydd]->check_in  >= '10:30:00' ) || ($data[$keydd]->check_out  <= '17:30:00' )  )
                                {
                                    $half[] = $data[$keydd]->id;
                                    $td = "<td style='background-color:#ffc433'>Half Day</td>";
                                }
                                else
                                {
                                     $pr_count[]=$data[$keydd]->id;
                                    $td = "<td style='background-color:#acff33'>Present</td>";

                                }

                            }

                            else
                            {
                                $count_work_status[$data[$keydd]->id][]=$data[$keydd]->id;
                                    $td = "<td style='background-color:#33ffc7'>{{$data[$keydd]->work_status}}</td>";

                            }
                        }
                        else
                        {

                            if(in_array($data[$keydd]->id,$pr_array))
                            {
                                $half[]=$data[$keydd]->id;
                                $td = "<td style='background-color:#ffc433'>Half Day</td>";

                            }
                            else
                            {
                                $count_work_status[$data[$keydd]->id][]=$data[$keydd]->id;
                                $td = "<td style='background-color:#33ffc7'>{{$data[$keydd]->work_status}}</td>";

                            }

                        }




                    }  
                    ?>
                    @if(!empty($data[$keydd]->work_status))
                         <?php echo $td;  ?>
                    @else
                    <td style="background-color:#f79f81">{{'-'}}</td>
                    @endif
                    @endforeach

                    @endif 
                    <td style="background-color:#f5d0a9">{{$total_days}}</td>
                    <td style="background-color:#f5d0a9">{{$total_attd}}</td>  
                     <td style="background-color:#f5d0a9">@if(!empty($pr_count)){{count($pr_count)}} @endif</td>
                     @foreach($working_status as $keyw=>$dataw)
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[$keyw])){{count($count_work_status[$keyw])}} @endif</td>
                     @endforeach
                     <td style="background-color:#f5d0a9">{{$total_days-$total_attd}}</td>
                     <td style="background-color:#f5d0a9">@if(!empty($half)){{count($half)}} @endif</td>
                     




                  <!--   <td style="background-color:#f5d0a9">@if(!empty($count_work_status[1])){{count($count_work_status[1])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[12])){{count($count_work_status[12])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[14])){{count($count_work_status[14])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[19])){{count($count_work_status[19])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[17])){{count($count_work_status[17])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[5])){{count($count_work_status[5])}} @endif</td>
                    <td style="background-color:#f5d0a9">@if(!empty($count_work_status[13])){{count($count_work_status[13])}} @endif</td> -->

                </tr>
                <?php $i++; ?>
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
                                                 null, null, null,null,
                                                  null,null,null,null,null,null,null,null,null,null,null,<?= $implode ?>,
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
