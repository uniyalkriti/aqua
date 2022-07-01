<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
     {{Lang::get('common.attendance')}} Report 
<title>
    <table class="table table-bordered" style="font-size: 13px;border: 1px black">
        <thead>
            <tr>
                <td colspan="1" class="bg-primary"> {{Lang::get('common.location3')}}</td>
                <td>{{$l3_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.location4')}}</td>
                <td>{{$l4_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.location5')}}</td>
                <td>{{$l5_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.user')}}</td>
                <td>{{$filter_user}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.role_key')}}</td>
                <td>{{$role_name}}</td>

                <td colspan="1" class="bg-primary"> From</td>
                <td>{{$from_date}}</td>

                <td colspan="1" class="bg-primary"> To</td>
                <td>{{$to_date}}</td>

            </tr>
        </thead>
    </table>
</title>
</div>



    <table class="table table-bordered" style="font-size: 13px;border: 1px black">
        <thead>
            <tr>
                <td colspan="1" class="bg-primary"> {{Lang::get('common.location3')}}</td>
                <td>{{$l3_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.location4')}}</td>
                <td>{{$l4_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.location5')}}</td>
                <td>{{$l5_name}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.user')}}</td>
                <td>{{$filter_user}}</td>

                <td colspan="1" class="bg-primary"> {{Lang::get('common.role_key')}}</td>
                <td>{{$role_name}}</td>

                <td colspan="1" class="bg-primary"> From</td>
                <td>{{$from_date}}</td>

                <td colspan="1" class="bg-primary"> To</td>
                <td>{{$to_date}}</td>

            </tr>
        </thead>
    </table>


<table id="dynamic-table" class="table table-bordered" >
    <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.location1')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
      
       
        {{--<th>Status</th>
        <th>Deactivated Date</th>
        <th>Created Date</th>--}}
        @if(!empty($datesDisplayArr))
            @foreach($datesDisplayArr as $keyd=>$datad)
                <?php 
                    $null[] = 'null';
                ?>
                <th>{{date('d',strtotime($datad))}}</th>
            @endforeach
        @endif

        <th>Total Days</th>
        <th>Working Days</th>
        <th>Sunday</th>
        <th>Holiday</th>
        <th>Extra Working</th>
        <th>Absent</th>
        <th>Not Punched</th>
        <th>Actual Paid Days</th>



        
        

    </tr>
</thead>
    <tbody>
    <?php $i = 1;
        $tc=0;
        $pc=0;
        $tq=0;
        $tv=0;
        $total_attd=0;
        $extra_work=0;
        $weekly_off=0;
        $holiday_off =0;
        $absent=0;
    ?>
    @if(!empty($records))
        @foreach($records as $key=>$data)
        <?php $encid = Crypt::encryptString($data["user_id"]); ?>

                <tr  class="">
                    <td style="background-color:#f5d0a9">{{$i}}</td>
                    <td style="background-color:#f5d0a9">{{$data['l1_name']}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($location_4_name[$data['user_id']])?$location_4_name[$data['user_id']]:'-'}}</td>
                    <td style="background-color:#f5d0a9">{{!empty($location_5_name[$data['user_id']])?$location_5_name[$data['user_id']]:'-'}}</td>
                    <td style="background-color:#f5d0a9">{{$data['emp_code']}}</td>
                    <td style="background-color:#f5d0a9"><a href="{{url('user/'.$encid)}}">{{$data['user_name']}}</a></td>
                    <td style="background-color:#f5d0a9">{{$data['role_name']}}</td>
                 
                    @if(!empty($datesArr))
                    <?php 
                    $total_attd=0; 
                    $extra_work=0; 
                    $weekly_off=0; 
                    $holiday_off=0;
                    $absent=0;
                    $punched_absent=0;
                    $count_work_status=array();
                    ?>
                    @foreach($datesArr as $keydd=>$datadd)
                    <?php 
                    $day_name = date('l', strtotime($datadd));

                    if(!empty($data[$keydd]->id)) 
                    {
                        if($day_name == 'Sunday' && !empty($data[$keydd]->work_status)){
                          $extra_work++; 
                        }
                        elseif(array_key_exists($datadd,$holiday) && !empty($data[$keydd]->work_status)){
                           $extra_work++; 
                        }else{
                          // $total_attd++; 
                        }

                    $count_work_status[$data[$keydd]->id][]=$data[$keydd]->id;
                    }    
                    ?>
                    @if(!empty($data[$keydd]->work_status))
                            @if($day_name == 'Sunday') 
                            <?php  
                             if($datadd <= date('Y-m-d')){
                              //  $weekly_off++; 
                                }
                            ?>
                            <td style="background-color:#85f092">EW</td>
                            @elseif(array_key_exists($datadd,$holiday))  
                                @foreach($holiday as $holidaykey => $holidayvalue)
                                        @if($datadd == $holidaykey)
                                        <?php // $holiday_off++; ?>
                                            <td style="background-color:#85f092;">EW</td>    
                                        @endif
                                @endforeach    
                            @elseif($data[$keydd]->work_status == "Absent")
                            <td style="background-color:#f79f81">A</td>
                            <?php  $punched_absent++  ?>
                            @else
                            <td style="background-color:#85f092">P</td>
                            <?php  $total_attd++  ?>
                            @endif

                    @elseif($day_name == 'Sunday') 
                    <?php  
                     if($datadd <= date('Y-m-d')){
                        $weekly_off++; 
                        }
                    ?>
                    <td style="background-color:#ffbf00">S</td>        

                    @elseif(array_key_exists($datadd,$holiday))  
                    @foreach($holiday as $holidaykey => $holidayvalue)
                            @if($datadd == $holidaykey)
                            <?php  $holiday_off++; ?>
                                <td style="background-color:#936c6c; color:white;">H</td>    
                            @endif
                    @endforeach    



                    @else
                    <?php  
                    if($datadd <= date('Y-m-d')){
                    $absent++; 
                    ?>
                    <td style="background-color:#f79f81">{{'A'}}</td>
                    <?php
                    }else{
                    ?>
                    <td style="background-color:#f79f81">{{''}}</td>
                    <?php } ?>
                   
                    @endif
                    @endforeach
                    @endif 
                     <td style="background-color:#f5d0a9">{{$total_days}}</td>
                    <td style="background-color:#f5d0a9">{{$total_attd}}</td>  
                    <td style="background-color:#f5d0a9">{{$weekly_off}}</td>  
                    <td style="background-color:#f5d0a9">{{$holiday_off}}</td>  
                     <td style="background-color:#f5d0a9">{{$extra_work}}</td>  
                     <td style="background-color:#f5d0a9">{{$punched_absent}}</td>  
                    <td style="background-color:#f5d0a9">{{$absent}}</td>  

                    <td style="background-color:#f5d0a9">{{($total_attd+$weekly_off+$holiday_off+$extra_work)}}</td>  





                    
                   
                  
                </tr>
                <?php $i++; ?>
            @endforeach
    @endif
 <?php
 // dd(count($null));
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
                                                 null, null, null,null,null,null,null,null,null,null,null,
                                                  null,null,<?= $implode ?>,
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
                                            autoPrint: true,
                                            // message: 'This print was produced using the Print button for DataTables'
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
