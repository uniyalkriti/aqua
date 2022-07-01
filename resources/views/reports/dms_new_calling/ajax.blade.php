<div class="clearfix">
        <div class="pull-right tableTools-container"></div>
</div>
<div class="table-header center">
{{Lang::get('common.new_calling')}}
   
</div>
<table id="dynamic-table" class="table table-bordered" >
    <thead>
    <tr>
        <th >S.No.</th>
        <th >Date</th>
        <th >Time</th>
        <th >State</th>

        <th >ASM Name</th>
        <th >Distributor Name</th>
        <th >Contact No.</th>
        <th >Support Team Remarks</th>
        <th >Type</th>
       <th >Action</th> 

      
    </tr>
    </thead>
  
    <tbody>
    <?php $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();?>

    @if(!empty($main_query_data))
    
    @foreach($main_query_data as $k=> $r)
   
        <?php 
        // $user_id = Crypt::encryptString($r->user_id); 
        // $retailer_id = Crypt::encryptString($r->retailer_id); 
        $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>

          
            <td>{{!empty($r->date)?$r->date:''}}</td>
            <td>{{!empty($r->time)?$r->time:''}}</td>
            <td>{{$r->dealer_state}}</td>
         
            <td>{{!empty($asm_name[$r->dealer_id])?$asm_name[$r->dealer_id]:'N/A'}}</td>
            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
     
            <td>{{$r->dealer_contact}}</td>
            <td>{{!empty($r->support_team_remarks)?$r->support_team_remarks:''}}</td>
            <td>{{!empty($r->calling_type_name)?$r->calling_type_name:''}}</td>

            <td>
                <button type="button" dealerid="{{$r->dealer_id}}" date="{{!empty($r->date)?$r->date:''}}"
                    data-toggle="modal" data-target="#reciept_modal" class="btn btn-default reciept_modal btn-round btn-white">
                    <i class="ace-icon fa fa-send green"></i>
                    Action 
                </button>
            </td> 
         
        </tr>
        <?php $i++;  ?>
            @endforeach  
 
          
        @endif
    </tbody>
</table>

<div class="modal fade" id="reciept_modal" role="dialog">
    <div class="modal-dialog" style="width:900px;">
    
        <!-- Modal content-->
        <div class="modal-content" id ="modalDiv">
            
            <div class="modal-body" id="qwerty">
                <form action="dms_submit_new_calling" method="post" id="reciept_modal_form" enctype="multipart/form-data">
                    {!! csrf_field() !!}

                    <input type="hidden" id="dealer_id_c" name="dealer_id" value="">
                    <input type="hidden" id="date_c" name="date" value="">
                    
                    <table class="table-bordered" >
                        <th style="background-color:#fcf8e3; color:black; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp mSELL</th>

                    </table>
                    <br>
                   

                    <table border="0" cellspacing="0" cellpadding="0" >

                        <tr>
                            <th >Support Team Remarks</th> &nbsp
                            <td ><textarea style=" height: 85px; width:513px; " type="text" name="team_remarks" id="team_remakrs" required></textarea></td>


                            <th>Calling Type</th>&nbsp
                            <td width="40%">

                                <select name="calling_type" id="calling_type" class="form-control chosen-select"  required>
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($dms_calling_type))
                                        @foreach($dms_calling_type as $k=>$r)
                                            <option value="{{$k}}">{{$r}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>

                 
                    </table>
                    <div class="row">
                        <div class="col-xs-12">
                                <div class="">
                                    <div class="col-xs-2">
                                            <label class="control-label no-padding-right"
                                                   for="head_quarter"> </label>
                                             
                                             
                                    </div>

                                </div>
                        </div>
                    </div>
                   <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-lg-3">
                                <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                            </div>
                            
                            <div class="col-lg-3" id="submit">
                                <button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>

<script type="text/javascript">
    
    $('.reciept_modal').click(function() {
            var dealer_id = $(this).attr('dealerid');
            var date = $(this).attr('date');
            // $('.mytbody').html('');
            

            $('#dealer_id_c').html('');
            $('#date_c').html('');
            
            $('#dealer_id_c').val(dealer_id);
            $('#date_c').val(date);

        });


        $(document).ready(function (e) {
            $('#reciept_modal_form').on('submit',(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type:'POST',
                    url: $(this).attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        alert('Submitted SuccessFully');
                        $('#reciept_modal').modal('toggle');
                       
                    },
                    error: function(data){
                        // console.log("error");
                        // console.log(data);
                    }
                });
            }));

        });
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
                                                {"bSortable": false},
                                                 null,null,null,null,null,null,null,null,
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


