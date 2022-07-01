<div class="clearfix">
</div>
<div class="table-header center">
   {{Lang::get('common.dailyAttendanceEditReport')}}
    <div class="pull-right tableTools-container"></div>
   
</div>
<table id="dynamic-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
   <thead>
    <tr class="info" style="color: black;">
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.date')}}</th>

     <!--    <th>{{Lang::get('common.location1')}}</th>
        <th>{{Lang::get('common.location2')}}</th> -->
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
       
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>

        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.time')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.address')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.address')}}</th>
        <th>{{Lang::get('common.attendance')}} {{Lang::get('common.remarks')}}</th>
        <th>{{Lang::get('common.check_out')}} {{Lang::get('common.remarks')}}</th>

        <th>{{Lang::get('common.action')}}</th>



    </tr>
</thead>
    <tbody>
    <?php 
    $i=0; 
    $senior_name = App\CommonFilter::senior_name('person');
    ?>
    @if(!empty($records) && count($records)>0)
        @foreach($records as $key=>$data)
        <?php 
        $encid = Crypt::encryptString($data["user_id"]); 
        $sencid = Crypt::encryptString($data["person_id_senior"]); 
        ?>
                <tr  class="">
                    <td>{{$i+1}}</td>
                    <td>{{$date}}</td>
                    
                  <!--   <td>{{$data['l1_name']}}</td>
                    <td>{{$data['l2_name']}}</td> -->
                    <td>{{$data['l3_name']}}</td>
                    <td>{{$data['l4_name']}}</td>
                    <td>{{$data['l5_name']}}</td>
                    <td>{{$data['l6_name']}}</td>
                    
                    <td>{{$data['emp_code']}}</td>
                    <td><a href="{{url('user/'.$encid)}}">{{$data['user_name']}}</a></td>
                    <td>{{$data['role_name']}}</td>
                    <td>{{$data['mobile']}}</td>
                    <td><a href="{{url('user/'.$sencid)}}">{{$data['senior_name']}}</a></td>

                    <td>{{$data['checkin_time']}}</td>
                    <td>{{$data['checkout_time']}}</td>
                    <td>{{$data['checkin_location']}}</td>
                    <td>{{$data['checkout_location']}}</td>
                    <td>{{$data['checkin_remarks']}}</td>
                    <td>{{$data['checkout_remarks']}}</td>

                    @if(!empty($data['checkin_time']) && !empty($data['checkout_time']))
                     <td>
                      <a href="#" date="{{$date}}" user_id="{{$data['user_id']}}" flag="AttendenceCheckoutUpdate"  data-toggle="modal"  data-target="#myModal" class="attUpdation" title="Attendence CheckOut Update">
                            <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div>
                        </a>
                    </td>
                    @elseif(empty($data['checkin_time']) && empty($data['checkout_time']))
                    <td>
                      <a href="#" date="{{$date}}" user_id="{{$data['user_id']}}" flag="AttendenceCheckoutInsert" data-toggle="modal"  data-target="#myModal" class="attUpdation" title="Attendence CheckOut Insert">
                            <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div>
                        </a>
                    </td>
                    @elseif(!empty($data['checkin_time']) && empty($data['checkout_time']))
                    <td>
                      <a href="#" date="{{$date}}" user_id="{{$data['user_id']}}" flag="AttendenceUpdateCheckoutInsert" data-toggle="modal"  data-target="#myModal" class="attUpdation" title="Attendence Update CheckOut Insert">
                            <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div>
                        </a>
                    </td>
                    @elseif(empty($data['checkin_time']) && !empty($data['checkout_time']))
                    <td>
                      <a href="#" date="{{$date}}" user_id="{{$data['user_id']}}" flag="AttendenceInsertCheckoutUpdate" data-toggle="modal"  data-target="#myModal" class="attUpdation" title="Attendence Insert CheckOut Update">
                            <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div>
                        </a>
                    </td>

                    @endif

                   
                </tr>
                <?php $i++; ?>
            @endforeach
    @endif

    </tbody>
</table>


<!-- details modal starts here  -->

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:1000px;">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReportDetails()" href="javascript:void(0)" class="nav-link">{{Lang::get('common.user')}} {{Lang::get('common.attendance')}}  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="get" id="submitAttendance" action="attendanceDetailsUpdate" enctype="multipart/form-data">

                    <input readonly type="hidden" placeholder="" name="user_id" id="user_id"  class="form-control input-sm" >
                    <input readonly type="hidden" placeholder="" name="return_date" id="return_date"  class="form-control input-sm" >
                    <input readonly type="hidden" placeholder="" name="return_flag" id="return_flag"  class="form-control input-sm" >
                    
                    <div class="table-header center">
                        <span>{{Lang::get('common.attendance')}} {{Lang::get('common.details')}} </span>
                    </div><br>
                    <div class="row">
                         <div class="col-lg-2">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.username')}} <b style="color: red;"></b></label>
                                <input readonly type="text" placeholder="" name="user_name" id="user_name"  class="form-control input-sm" >
                            </div>
                        </div>
                         <div class="col-xs-6 col-sm-6 col-lg-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="name">{{Lang::get('common.working')}} {{Lang::get('common.status')}}</label>
                                <select name="work_status" id="work_status" class="form-control" required="required">
                                    <option value="">select</option>
                                    @if(!empty($work_status_drop))
                                        @foreach($work_status_drop as $k=>$r)
                                            <option value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.attendance')}} {{Lang::get('common.time')}}<b style="color: red;"></b></label>
                                <input type="text" placeholder="hh:mm:ss" name="checkin_time" id="checkin_time" class="form-control input-sm" >
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.attendance')}} {{Lang::get('common.address')}}<b style="color: red;"></b></label>
                                <input type="text" placeholder="" name="checkin_location" id="checkin_location" class="form-control input-sm" >
                            </div>
                        </div>
                         <div class="col-lg-2">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.attendance')}} {{Lang::get('common.remarks')}}<b style="color: red;"></b></label>
                                <input type="text" placeholder="" name="checkin_remarks" id="checkin_remarks" class="form-control input-sm" >
                            </div>
                        </div>
                    </div><br>

                     <div class="table-header center">
                        <span>{{Lang::get('common.check_out')}} {{Lang::get('common.details')}} </span>
                    </div><br>

                    <div class="row">
                        <div class="col-lg-3">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.check_out')}}  {{Lang::get('common.time')}} <b style="color: red;"></b></label>
                                <input type="text" placeholder="hh:mm:ss" name="checkout_time" id="checkout_time"  class="form-control input-sm" >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.check_out')}}  {{Lang::get('common.address')}} <b style="color: red;"></b></label>
                                <input type="text" placeholder="" name="checkout_location" id="checkout_location"  class="form-control input-sm" >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="">
                               <label class="control-label no-padding-right" for="joining_date"> {{Lang::get('common.check_out')}}  {{Lang::get('common.remarks')}} <b style="color: red;"></b></label>
                                <input type="text" placeholder="" name="checkout_remarks" id="checkout_remarks"  class="form-control input-sm" >
                            </div>
                        </div>
                    </div>

                  
                  <!--   <div class="tbodyData">    
                    </div> -->
                       

                        <div class="col-lg-4">
                            <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Update
                                </button>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- modal ends -->



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
          $(document).ready(function (e) {
            $('#submitAttendance').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                // $('#submit_invoice').html('');
                // $('#submit_invoice').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
                 $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        alert('Operation Performed');
                        $('#myModal').modal('toggle');
                        window.location = "dailyAttendanceEdit";

                                               
                    },
                    complete: function (data) {
                    $('#m-spinner').remove();
                    },
                    error: function (data) {
                        alert('Error Found Refresh And try Again Or Contact To Administrator');
                        $('#myModal').modal('toggle');
                        $('#m-spinner').remove();
                        window.location = "dailyAttendanceEdit";

                    }
                });
            }));

        });
    </script>

<script>
    $('.attUpdation').click(function() {
            var date = $(this).attr('date');
            var user_id = $(this).attr('user_id');
            var flag = $(this).attr('flag');
            if (date != '') 
            {
                $('.tbodyData').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/attendanceDetails',
                    dataType: 'json',
                    data: {"date": date, "user_id": user_id, "flag": flag},
                    success: function (data) 
                    {   
                        $('#user_name').html('');
                        $('#att_work_status').html('');
                        $('#checkin_time').html('');
                        $('#checkin_location').html('');
                        $('#checkin_remarks').html('');
                        $('#checkout_time').html('');
                        $('#checkout_location').html('');
                        $('#checkout_remarks').html('');
                        $('#user_id').html('');
                        $('#return_flag').html('');
                        $('#return_date').html('');

                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            // var template = '';
                            var Sno = 1;
                            $.each(data.final_array, function (u_key, u_value) {
                               
                                // template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.quantity+'></td><td><input type="text" class="rate_val" required="required" name="rate[]" value='+u_value.rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');
                                $("#user_name").val(u_value.user_name);
                                $("#att_work_status").val(u_value.att_work_status);
                                $("#checkin_time").val(u_value.checkin_time);
                                $("#checkin_location").val(u_value.checkin_location);
                                $("#checkin_remarks").val(u_value.checkin_remarks);
                                $("#checkout_time").val(u_value.checkout_time);
                                $("#checkout_location").val(u_value.checkout_location);
                                $("#checkout_remarks").val(u_value.checkout_remarks);
                                $("#user_id").val(u_value.user_id);
                                $("#return_flag").val(u_value.flag);
                                $("#return_date").val(u_value.date);

                                        
                                // template += ('<div class="row"><div class="col-xs-6 col-sm-6 col-lg-2"><label class="control-label no-padding-right" for="name">User Name</label></div></div>');


                                Sno++;
                            });   
                            // $('.tbodyData').append(template);

                            
                        }
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });
        
    </script>




    <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                null,
                                                  null,null,null,null,null,null, null, null,null,null,null,null,
                                                  null,null,null,null,
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