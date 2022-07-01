<div class="clearfix">
</div>
<div class="table-header center">
   {{Lang::get('common.daily_attendance')}}
    <div class="pull-right tableTools-container"></div>
   
</div>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
   <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.date')}}</th>
        <th>{{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.working')}} {{Lang::get('common.status')}}</th>
        <th>{{Lang::get('common.working')}} With</th>
        <th>{{Lang::get('common.user_address')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.location')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.remarks')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.location')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.remarks')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.image')}}</th>
   

    </tr>
</thead>
    <tbody>
    <?php
     $i = 1;
     $senior_name = App\CommonFilter::senior_name('person');
     // dd($users);
     ?>
    @foreach($users as $uk=>$u)
            <?php 
            $user_id = $u->user_id;
            $encid = Crypt::encryptString($u->user_id); 
            $senior_id = Crypt::encryptString($u->senior_id); 
            // dd($users);
            ?>

               
                <tr>
                    <td>{{$i}}</td>
                    
                    <td>{{$u->l3_name}}</td>
                    <td>{{$u->l4_name}}</td>
                    <td>{{$u->l5_name}}</td>
                    <td>{{$u->l6_name}}</td>
                    <td>{{!empty($u->work_date)?date('d-M-Y',strtotime($u->work_date)):'N/A'}}</td>
                    <td>{{!empty($u->work_time)?$u->work_time:''}}</td>
                  
                    <td><a href="{{url('user/'.$encid)}}">{{$u->uname}}</a></td>
                    <td>{{$u->rolename}}</td>
                    <td>{{$u->work_status}}</td>
                    <td>{{!empty($senior_name[$u->working_with])?$senior_name[$u->working_with]:''}}</td>
                    <td>{{$u->address}}</td>
                    <td>{{$u->track_addrs}}</td>
                    <td>{{$u->remarks}}</td>

                    <td>{{ !empty($checkoutarr[$user_id.$u->work_date]['work_time'])?$checkoutarr[$user_id.$u->work_date]['work_time']:'00:00:00' }}</td>
                    <td>{{ !empty($checkoutarr[$user_id.$u->work_date]['attn_address'])?$checkoutarr[$user_id.$u->work_date]['attn_address']:'' }}</td>
                    <td>{{ !empty($checkoutarr[$user_id.$u->work_date]['remarks'])?$checkoutarr[$user_id.$u->work_date]['remarks']:'' }}</td>

                    <td>
                        <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                            src="{{asset('attendance_images/')}}{{!empty($u->image_name)?'/'.$u->image_name:'N/A'}}" alt= " "/>
                    </td>

               



                </tr>
                <?php $i++?>
    @endforeach
     
    </tbody>
</table>



<div class="modal fade" id="outlet_modal_details" role="dialog">
    <div class="modal-dialog modal-lg3" style="width:850px;">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" > {{Lang::get('common.mtp')}} {{Lang::get('common.details')}}</h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    
                    <div class="col-xs-12 col-md-12">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <th>{{Lang::get('common.username')}}</th>
                                <th>{{Lang::get('common.date')}}</th>
                                <th>{{Lang::get('common.day')}}</th>
                                <th>{{Lang::get('common.location6')}} From</th>
                                <th>{{Lang::get('common.working')}} From</th>
                                <th>{{Lang::get('common.location7')}}</th>
                            </thead>
                          
                                <tbody class="mytbody_outlet_details">
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






<script>
     function outlet_details(date,user_id) {
        // alert(outlet_type);
                // var user_id = dealer_id;
                // var dealer_id = user_id;
              // alert(user_id);
               
                $('.mytbody_outlet_details').html('');
              
                if (date != '') 
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: domain + '/get_mtp_details',
                        dataType: 'json',
                        data: {'date': date, 'user_id': user_id},
                        success: function (data) 
                        {
                            var template_beat ='';

                            if (data.code == 401) 
                            {
                               
                            }
                            else if (data.code == 200) 
                            {
                                var Sno_beat = 1;
                                                          
                                $.each(data.result_data, function (b_key, b_value) {
                                    // console.log(b_value);
                                  
                                        template_beat += ("<tr><td>"+Sno_beat+"</td><td><a href=retailer/"+b_value.retailer_n+">"+b_value.retailer_name+"</a></td><td><a href=distributor/"+b_value.dealer_n+">"+b_value.dealer_name+"</a></td><td>"+b_value.beat+"</td><td><a href=user/"+b_value.user_n+">"+b_value.user_name+"</a></td><td>"+b_value.created_on+"</td></tr>");
                                    Sno_beat++;
                                });   
                                
                                $('.mytbody_outlet_details').append(template_beat);

                                
                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });
                }       
            };
    </script>


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
                                                {"bSortable": true},
                                                  null,null,null,null,null,null, null, null, null, null,
                                                  null,null,null, null, null, null,
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