
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<div class="table-header center">
  {{Lang::get('common.state_wise_secondary')}}
    <div class="pull-right tableTools-container"></div>
</div>
<table id="dynamic-table"  class="table table-bordered" style="width:100%; font-size: 13px;border: 1px black">
    <thead>
        <tr class="info" style="color: black;">
            <th>{{Lang::get('common.s_no')}}</th>
            <th>{{Lang::get('common.location1')}}</th>
            <th>{{Lang::get('common.location2')}}</th>
            <th>{{Lang::get('common.location3')}}</th>
            <th>{{Lang::get('common.total')}} {{Lang::get('common.retailer')}} Till ({{ date('Y-m-d') }})</th>
            <th>New {{Lang::get('common.retailer')}} Added</th>
            <th>{{Lang::get('common.productive_call')}}</th>
            <th>{{Lang::get('common.secondary_sale')}}</th>
            @foreach($work_status as $key => $value)
            <?php  $null[] = 'null'; ?>
                <th>{{Lang::get('common.total')}} {{Lang::get('common.user')}} {{' On '.$value}}</th>
            @endforeach
            
            <th>{{Lang::get('common.total')}} {{Lang::get('common.user')}} Till ({{ date('Y-m-d') }})</th>
            <th>{{Lang::get('common.total')}} Active {{Lang::get('common.user')}}</th>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1;
        $tc=0;
        $pc=0;
        $tq=0;
        $tv=0;
        $no=0;
        $au=0;
        $w_s_l_count=array();
        $w_s_p_count=0;
           
    ?>
    @if(!empty($dsr))
        @foreach($dsr as $key=>$data)
        <?php
        if(isset($data['active_user'])){
          $active_user = $data['active_user'];
        }
        else {
          $active_user = 0; 
        }
        $stateId = $data['l3_id'];
        // $activeUser = "<a href="http://localhost/Test/Learning/plantedit.php?id='.$id.'" class="btn btn-warning" name = "edit" value ="edit" data-toggle="modal" data-target="#myModal4" >&#9998</a>";
        ?>
                <tr  class="">
                    <td>{{$i}}</td>
                    <td>{{$data['l1_name']}}</td>
                    <td>{{$data['l2_name']}}</td>
                    <td>{{$data['l3_name']}}</td>
                    <td>{{ $data['total_outlet']}}</td>
                    <td>{{$data['new_outlet']}}</td>
                    <td>{{$data['pc']}}</td>
                    
                    <td>{{isset($data['total_sale_value'])?$data['total_sale_value']:'0'}}</td>
                    

                    
                    
                    @foreach($work_status as $key => $value)
                    <?php 
                        $w_s_l_count[$key][] = isset($total_work_status_leave[$stateId.$key])?$total_work_status_leave[$stateId.$key]:0;
                    ?>
                        <td><a title="work_status_leave" state_id="{{ $stateId }}" to_date="{{ $to_date }}" from_date="{{ $from_date }}" data-toggle="modal" flag="{{$key}}" data-target="#myModal" class="user-modal">{{ isset($total_work_status_leave[$stateId.$key])?$total_work_status_leave[$stateId.$key]:0}}</a></td>
                    @endforeach
                    
                    
                    <td><a title="Total Users" state_id="{{ $stateId }}" to_date="{{ $to_date }}" from_date="{{ $from_date }}" data-toggle="modal" flag="T" data-target="#myModal" class="user-modal">{{ isset($data['total_user']) ? $data['total_user']:0}}</a></td>
                   
                    <td><a title="Active Users" flag="A" state_id="{{ $stateId }}" to_date="{{ $to_date }}" from_date="{{ $from_date }}" data-toggle="modal" data-target="#myModal" class="user-modal">{{ $active_user }}</a></td>
                   
                   
                </tr>
                <?php 
                $tc+=$data['total_outlet'];
                $no+=$data['new_outlet'];
                $pc+=$data['pc'];
                $tq+=$data['total_user'];
                $au+=$active_user;
                $tv+=$data['total_sale_value'];
                // $w_s_l_count+=isset($data['work_status_leave'])?$data['work_status_leave']:0;
                // $w_s_p_count+= isset($data['work_status_present'])?$data['work_status_present']:0;
                $i++?>
            @endforeach
            
    @endif

    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</th>
            <th>{{$tc}}</th>
            <th>{{$no}}</th>
            <th>{{$pc}}</th>
            <th>{{$tv}}</th>
            @foreach($work_status as $key => $value)
                <th>{{(array_sum($w_s_l_count[$key]))}}</th>
            @endforeach

            
            
            <th>{{$tq}}</th>
            <th>{{$au}}</th>
        </tr>
    </tfoot>
</table>
<?php
    $implode = implode(",",$null);
?>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width:800px;">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" >{{Lang::get('common.user')}} {{Lang::get('common.details')}}</h4>
  
      </div>
      <div class="modal-body">
            <table class="table table-bordered table-hover">
                    <thead class = "mythead">
                        
                    </thead>
                    <tbody class="mytbody">
                    
                    </tbody>
                       </table>
    </div>
        </div>
    
    </div>
        
      </div>
      <script>
            $('.user-modal').click(function() {
                  var state = $(this).attr('state_id'); 
                  var from_date = $(this).attr('from_date'); 
                  var to_date = $(this).attr('to_date'); 
                  var flag = $(this).attr('flag'); 
                  $('.mytbody').html('');
                  $('.mythead').html('');
                  if (state != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/get_active_user_sales',
            dataType: 'json',
            data: "id=" + state+"&from_date="+from_date+"&to_date="+to_date+"&flag="+flag,
            success: function (data) {
                
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    

                    if(flag=='P' || flag=='L' || flag=='T')
                    {
                        var s_no = "{{Lang::get('common.s_no')}}"; 
                        var username = "{{Lang::get('common.username')}}"; 
                        var role_key = "{{Lang::get('common.role_key')}}"; 
                        var user_contact = "{{Lang::get('common.user_contact')}}"; 
                        $('.mythead').append("<tr><td>"+s_no+"</td><td>"+username+"</td><td>"+role_key+"</td><td>"+user_contact+"</td></tr>");
                        var Sno = 1;
                        $.each(data.user_data, function (key, value) {
                            $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.user_name+"</td><td>"+value.role_name+"</td><td>"+value.mobile+"</td></tr>");
                            Sno++;
                        });
                    }
                    else
                    {
                        var s_no = "{{Lang::get('common.s_no')}}"; 
                        var username = "{{Lang::get('common.username')}}"; 
                        var role_key = "{{Lang::get('common.role_key')}}"; 
                        var user_contact = "{{Lang::get('common.user_contact')}}"; 
                        var productive_call = "{{Lang::get('common.productive_call')}}"; 
                        var secondary_sale = "{{Lang::get('common.secondary_sale')}}"; 
                        $('.mythead').append("<tr><td>"+s_no+"</td><td>"+username+"</td><td>"+role_key+"</td><td>"+user_contact+"</td><td>"+productive_call+"</td><td>"+secondary_sale+"</td></tr>");
                        var Sno = 1;
                        $.each(data.user_data, function (key, value) {
                            $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.user_name+"</td><td>"+value.role_name+"</td><td>"+value.mobile+"</td><td>"+value.call_status+"</td><td>"+value.total_sale_value+"</td></tr>");
                            Sno++;
                        });
                    }

                       
                   
                    // _user.empty();
                    
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
      <!-- END MODAL -->

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