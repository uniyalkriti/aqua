@if(!empty($records))
<a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
<i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<style>
#simple-table table {
border-collapse: collapse !important;
}

#simple-table table, #simple-table th, #simple-table td {
border: 1px solid black !important;
}
#simple-table th{
/*background-color: #438EB9 !important;*/
background-color: #7BB0FF !important;
color: black;
}
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
<tr><td colspan="39"><h3>{{Lang::get('common.travelling_expenses')}}</h3></td></tr>
<tr>
<th rowspan="2">{{Lang::get('common.s_no')}}</th>
<th rowspan="2" id="remove1">{{Lang::get('common.image')}}</th>
<th rowspan="2">{{Lang::get('common.date')}}</th>
<!-- <th rowspan="2">{{Lang::get('common.location2')}}</th> -->
<th rowspan="2">{{Lang::get('common.location3')}}</th>
<th rowspan="2">{{Lang::get('common.location4')}}</th>
<th rowspan="2">{{Lang::get('common.location5')}}</th>
<th rowspan="2">{{Lang::get('common.location6')}}</th>
<th rowspan="2">{{Lang::get('common.emp_code')}}</th>

<th rowspan="2">{{Lang::get('common.username')}}</th>
<th rowspan="2">{{Lang::get('common.role_key')}}</th>
<th rowspan="2">{{Lang::get('common.user_contact')}}</th>
<th rowspan="2">{{Lang::get('common.senior_name')}}</th>
<th colspan="2">DEPATURE FROM</th>
<th colspan="2">ARRIVAL AT</th>
<th rowspan="2">CLASS & MODE OF TRAVEL</th>
<th rowspan="2">DISTANCE KM</th>

<th>FARE</th>
<th>D.A.</th>
<th>HOTEL</th>
<th>POSTAGE</th>
<th>TELEPHONE EXPENSES</th>
<th>CONVEYANCE</th>
<th>MISC.</th>
<th>{{Lang::get('common.total')}}</th>
<th rowspan="2">Remarks</th>
<th rowspan="2">No. Of {{Lang::get('common.distributor')}} Visited</th>
<th colspan="3">Primary Orders Booked</th>
<th colspan="3">Secondary Orders Booked</th>
<th rowspan="2">Server Date Time </th>
<th rowspan="2">{{Lang::get('common.status')}}</th>
<th rowspan="2">{{Lang::get('common.action')}}</th>
</tr>

<tr>
<th>{{Lang::get('common.location6')}}</th>
<th>{{Lang::get('common.time')}}</th>
<th>{{Lang::get('common.location6')}}</th>
<th>{{Lang::get('common.time')}}</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>
<th>Rs.</th>

<th>No.</th>
<th>Value Rs.</th>
<th>p.</th>
<th>No.</th>
<th>Value Rs.</th>
<th>p.</th>
</tr> 
<tbody>
@if(!empty($records) && count($records)>0)
<?php
$senior_name = App\CommonFilter::senior_name('person');
?>
@foreach($records as $key=>$record)
	
<?php 

$encid = Crypt::encryptString($record->user_id);
$sencid = Crypt::encryptString($record->person_id_senior);
$fare[] = $record->fare;
$da[] = $record->da;
$hotel[] = $record->hotel;
$postage[] = $record->postage;
$te[] = $record->telephoneExpense;
$conv[] = $record->conveyance;
$misc[] = $record->misc;
$total[] = $record->total;
?>
<tr>
<td>{{$key+1}}</td>
<td id="remove">
	<ul class="ace-thumbnails clearfix remove" id="remove">
		<li>
			<a href="{{asset('expense_image/')}}{{!empty($record->image_name1)?'/'.$record->image_name1:'N/A'}}"  data-rel="colorbox">
				<img width="100" height="100"  src="{{asset('expense_image/')}}{{!empty($record->image_name1)?'/'.$record->image_name1:'N/A'}}" alt=" " />
			</a>

		</li>

		<li>
			<a href="{{asset('expense_image/')}}{{!empty($record->image_name2)?'/'.$record->image_name2:'N/A'}}"  data-rel="colorbox">
				<img width="100" height="100"  src="{{asset('expense_image/')}}{{!empty($record->image_name2)?'/'.$record->image_name2:'N/A'}}" alt=" " />
			</a>

		</li>

		<li>
			<a href="{{asset('expense_image/')}}{{!empty($record->image_name3)?'/'.$record->image_name3:'N/A'}}"  data-rel="colorbox">
				<img width="100" height="100"  src="{{asset('expense_image/')}}{{!empty($record->image_name3)?'/'.$record->image_name3:'N/A'}}" alt=" " />
			</a>

		</li>

		
	</ul>
</td>
<td>{{!empty($record->travellingDate)?date('d-M-Y',strtotime($record->travellingDate)):'N/A'}}</td>

<!-- <td>{{$record->l2_name}}</td> -->
<td>{{$record->l3_name}}</td>
<td>{{$record->l4_name}}</td>
<td>{{$record->l5_name}}</td>
<td>{{$record->l6_name}}</td>
<td>{{$record->emp_code}}</td>

<td><a href="{{url('user/'.$encid)}}">{{$record->user_name}}</a></td>
<td>{{!empty($role[$record->role_id])?$role[$record->role_id]:'N/A'}}</td>
<td>{{$record->mobile}}</td>
<td><a href="{{url('user/'.$sencid)}}">{{!empty($senior_name[$record->person_id_senior])?$senior_name[$record->person_id_senior]:'N/A'}}</a></td>


<td>{{!empty($location_6_data[$record->departureID])?$location_6_data[$record->departureID]:'-'}}</td>
<td>{{$record->departureTime}}</td>


<td>{{!empty($location_6_data[$record->arrivalID])?$location_6_data[$record->arrivalID]:'-'}}</td>
<td>{{$record->arrivalTime}}</td>

<td>{{!empty($arr[$record->travelModeID])?$arr[$record->travelModeID]:'N/A'}}</td>
<td>{{$record->distance}}</td>
<td>{{$record->fare}}</td>
<td>{{$record->da}}</td>
<td>{{$record->hotel}}</td>
<td>{{$record->postage}}</td>
<td>{{$record->telephoneExpense	}}</td>
<td>{{$record->conveyance}}</td>
<td>{{$record->misc}}</td>
<td>{{round($record->total)}}</td>

<td>{{!empty($record->remarks)?$record->remarks:'NA'}}</td>


<td>{{!empty($out[$record->user_id.$record->travel_date]['dealer_visit'])?$out[$record->user_id.$record->travel_date]['dealer_visit']:'0'}}</td>
<td>{{!empty($out[$record->user_id.$record->travel_date]['no_of_order'])?$out[$record->user_id.$record->travel_date]['no_of_order']:'0'}}</td>
<td>{{!empty($out[$record->user_id.$record->travel_date]['primary_sale']->primary_sale)?$out[$record->user_id.$record->travel_date]['primary_sale']->primary_sale:'0'}}</td>
<td>{{!empty($out[$record->user_id.$record->travel_date]['primary_qty'])?$out[$record->user_id.$record->travel_date]['primary_qty']:'0'}}</td>

<td>{{!empty($out[$record->user_id.$record->travel_date]['no_of_order_secondary'])?$out[$record->user_id.$record->travel_date]['no_of_order_secondary']:'0'}}</td>
<td>{{!empty($out[$record->user_id.$record->travel_date]['secondary_sale']->secondary_sale)?$out[$record->user_id.$record->travel_date]['secondary_sale']->secondary_sale:'0'}}</td>
<td>{{!empty($out[$record->user_id.$record->travel_date]['secondary_qty'])?$out[$record->user_id.$record->travel_date]['secondary_qty']:'0'}}</td>

<td>{{$record->date_time}}</td>

<!-- status -->
<td id = "status{{$record->id}}">

	@if($record->status==1)
		<span id = "val{{$record->id}}" class="label label-lg label-success arrowed-in arrowed-in-right">{{'Appoved'}}</span>
	@else
		<span id = "value{{$record->id}}" class="label label-lg label-danger arrowed-in arrowed-in-right">{{'Not Approved'}}</span>
	@endif

</td>
<td id = "block{{$record->id}}">
	<a title="Log" id="{{ $record->id }}" user_id="{{ $record->user_id }}" date="{{ $record->travellingDate }}" data-toggle="modal" data-target="#myModal" class="user-modal"><button><i class="ace-icon fa fa-history  bigger-120"></i></button></a>

	@if($record->status==1)
		<button id ='toggleOn{{$record->id}}' class="btn btn-xs btn-success"
			onclick="status_change('{{"Not Approved"}}','{{"0"}}','{{$record->user_id}}','{{$record->travellingDate}}','{{$record->id}}');" title="Status" >
			<i class="ace-icon fa fa-toggle-on bigger-120"></i>
		</button>
		<a title="Edit" id="{{ $record->id }}" user_id="{{ $record->user_id }}" flag="{{1}}" date="{{ $record->travellingDate }}" data-toggle="modal" data-target="#edit_modal" class="expense-modal-edit"><button><i class="ace-icon fa fa-pencil  bigger-120"></i></button></a>
	@else
		<button id ='toggleOff{{$record->id}}' class="btn btn-xs btn-info"
			onclick="status_change('{{"Approved"}}','{{"1"}}','{{$record->user_id}}','{{$record->travellingDate}}','{{$record->id}}');" title="Status" >
			<i class="ace-icon fa fa-toggle-off bigger-120"></i>
		</button>
		<a title="Edit" id="{{ $record->id }}" user_id="{{ $record->user_id }}" flag="{{2}}" date="{{ $record->travellingDate }}" data-toggle="modal" data-target="#edit_modal" class="expense-modal-edit"><button><i class="ace-icon fa fa-pencil  bigger-120"></i></button></a>
	@endif

	<button class="btn btn-xs btn-danger"
		onclick="status_change_for_delete('{{"Delete"}}','{{$record->user_id}}','{{$record->travellingDate}}','{{$record->id}}');" title="Delete" >
		<i class="ace-icon fa fa-trash bigger-120"></i>
	</button>

	
</td>
<!-- status end -->




</tr>
@endforeach
<tr>
	<td colspan="18"><b>{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</b></td>
	<td><b>{{array_sum($fare)}}</b></td>
	<td><b>{{array_sum($da)}}</b></td>
	<td><b>{{array_sum($hotel)}}</b></td>
	<td><b>{{array_sum($postage)}}</b></td>
	<td><b>{{array_sum($te)}}</b></td>
	<td><b>{{array_sum($conv)}}</b></td>
	<td><b>{{array_sum($misc)}}</b></td>
	<td><b>{{round(array_sum($total))}}</b></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>	
@else
<tr>
<td colspan="37">
<p class="alert alert-danger">No data found</p>
</td>
</tr>
@endif
</tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width:800px;">
    
        <!-- Modal content-->
        <div class="modal-content">
           
            <div class="modal-body">
            			<table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:1160px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp {{ $comp->title }}</th>
                            <th style="background-color:blue; color:white; width:560px; height: 30px; text-align:right;">Modification Log&nbsp&nbsp&nbsp  </th>

                        </table>
                <table class="table table-bordered table-hover">
                        <thead class = "mythead">
                            
                            <tr>
                            	<th>{{Lang::get('common.s_no')}}</th>
                            	<th>{{Lang::get('common.username')}}</th>
                            	<th>{{Lang::get('common.date')}}</th>
                            	<th>{{Lang::get('common.status')}}</th>
                            	<th>Modified By</th>
                            	<th>Modified Time</th>
                            </tr>
                        </thead>
                        <tbody class="mytbody">
                        
                        </tbody>
               </table>
   				<div class="row">
                    <div class="col-xs-12">
                        
                        <div class="col-lg-3">
                            <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal ends here -->
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>

<script>
	function status_change(title,status,user_id,date,id)
	{
		// alert(status);
		// alert(user_id);
		// alert(date);
		// alert(id);
		$.confirm({
	            title: title,
	            content: 'Are you sure want to '+title +'expense ?',
	            buttons: {
	                confirm: function () {
	                    statusAction(status,user_id,date,id);
	                    $.alert({
	                        title: 'Alert!',
	                        content: 'Sccessfully Done!',
	                        buttons: {
	                            ok: function () {
	                            	if(status==1)
	                            	{
	                            		$('#status'+id).append('<span class="label label-lg label-success arrowed-in arrowed-in-right">Approved</span>');
		                    			$('#value'+id).hide();
	                            		$('#block'+id).append('<button><i class="ace-icon fa fa-toggle-on bigger-120"></i></button>')
		                    			$('#toggleOff'+id).hide();

	                            	}
	                            	else
	                            	{
	                            		$('#status'+id).append('<span class="label label-lg label-danger arrowed-in arrowed-in-right">Not Approved</span>');
		                    			$('#val'+id).hide();
		                    			$('#block'+id).append('<button><i class="ace-icon fa fa-toggle-off bigger-120"></i></button>')
		                    			$('#toggleOn'+id).hide();
	                            	}
	                            }
	                        }   
	                    });
	                },
	                cancel: function () {
	                    $.alert('Canceled!');
	                }
	            }
	        });
	}


    function statusAction(status,user_id,date,id)
    {
        if (status != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/changeExpenseStatus',
                dataType: 'json',
                data: {'status': status ,'user_id': user_id,'date':date,'id':id},
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                    	// alert()
                    }

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
        }

    }



    function status_change_for_delete(title,user_id,date,id)
	{
		// alert(status);
		// alert(user_id);
		// alert(date);
		// alert(id);
		$.confirm({
	            title: title,
	            content: 'Are you sure want to '+title +' expense ?',
	            buttons: {
	                confirm: function () {
	                    statusActionForDelete(user_id,date,id);
	                    $.alert({
	                        title: 'Alert!',
	                        content: 'Sccessfully Done!',   
	                    });
	                },
	                cancel: function () {
	                    $.alert('Canceled!');
	                }
	            }
	        });
	}

	   function statusActionForDelete(user_id,date,id)
    {
        if (id != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/deleteExpense',
                dataType: 'json',
                data: {'user_id': user_id,'date':date,'id':id},
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                    	// alert('Successfully Deleted');
                    }

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
        }

    }




</script>
<script>
	$('.user-modal').click(function(){
		var user_id = $(this).attr('user_id'); 
		var date = $(this).attr('date'); 
		var id = $(this).attr('id'); 
	

		$('.mytbody').html('');
		if (state != '') 
		{
		    $.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		    });
		    $.ajax({
		        type: "POST",
		        url: domain + '/show_expense_log_data',
		        dataType: 'json',
		        data: "id=" + id+"&date="+date+"&user_id="+user_id,
		        success: function (data) 
		        {
		            
		            if (data.code == 401) 
		            {
		                //  $('#loading-image').hide();
		            }
		            else if (data.code == 200) 
		            {
		                
	                        
		               		var Sno = 1;

	                        $.each(data.user_data, function (key, value) {
	                        	console.log(value);
	                            $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.user_name+"</td><td>"+value.date+"</td><td>"+value.status+"</td><td>"+value.modified_by+"</td><td>"+value.modified_time+"</td></tr>");
	                            Sno++;
	                        });
	                   
		            }

		        },
		        complete: function () 
		        {
		            // $('#loading-image').hide();
		        },
		        error: function () 
		        {
		        }
		    });
		}       
	});
</script>

<!-- modal for edit starts here -->
<!-- Modal -->
    <div class="modal fade" id="edit_modal" role="dialog">
        <div class="modal-dialog" style="width:1500px;">
        
            <!-- Modal content-->
            <div class="modal-content" id ="modalDiv">
                
                <div class="modal-body" id="qwerty">
                    <form action="submit_expense_edit" method="get">
                        
                        <table class="table-bordered" >
                            <th style="background-color:blue; color:white; width:860px; height: 30px; text-align:left;">&nbsp&nbsp&nbsp {{ $comp->title }}</th>
                            <th style="background-color:blue; color:white; width:600px; height: 30px; text-align:right;">Edit Expense&nbsp&nbsp&nbsp  </th>

                        </table>
                     
                        <table  class=" table-bordered ">
                            <thead class = "mythead_edit">

                            </thead>
                            <tbody class="mytbody_edit">

                            </tbody>
                        </table>
                        
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                
                                <div class="col-lg-3">
                                    <button class="btn btn-danger form-control" data-dismiss="modal" type="button" name="cancel"><b>Cancel</b></button>
                                </div>
                                
                                <div class="col-lg-3" id="submit">
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>



                $('.expense-modal-edit').click(function() {
                      var user_id = $(this).attr('user_id'); 
                      var date = $(this).attr('date'); 
                      var id = $(this).attr('id'); 
                      var flag = $(this).attr('flag'); 
                      $('.mytbody_edit').html('');
                      $('.mythead_edit').html('');
                      $('#submit').html('');
                      if (id != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/edit_travelling_expense',
                dataType: 'json',
                data: "id=" + id+"&date="+date+"&user_id="+user_id,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                        // alert('qwertyh');
                    }
                    else if (data.code == 200) {
                        
                            var Sno = 1;
                            if(flag ==1)
                            {
                            	$('.mythead_edit').append("<tr><td style='width:140px;'>"+"<b>Sno</b></td><td style='width:140px;'>"+"<b>Distancte</b>"+"</td><td style='width:140px;'>"+"<b>FARE</b>"+"</td><td style='width:140px;'>"+"<b>D.A.</b>"+"</td><td style='width:140px;'>"+"<b>HOTEl</b>"+"</td><td style='width:140px;'>"+"<b>POSTAGE</b>"+"</td><td style='width:140px;'>"+"<b>TELEPHONE EXPENSES</b>"+"</td><td style='width:140px;'>"+"<b>CONVEYANCE</b>"+"</td><td style='width:140px;'>"+"<b>MISC.</b>"+"</td></tr>");
                                console.log(data.result);
                                $.each(data.result, function (key, value){
                                    $('.mytbody_edit').append("<tr><td>"+Sno+"</td><td><input readonly type='text' id='distance' name='distance' value="+value.distance+"></td><td><input  readonly  type='text' id='fare' name='fare' value="+value.fare+"></td><td><input readonly  type='text' id='da' name='da' value="+value.da+"></td><td><input type='text' id='hotel' readonly name='hotel' value="+value.hotel+"></td><td><input  readonly type='text' id='postage' name='postage' value="+value.postage+"></td><td><input type='text' readonly id='telephoneExpense' name='telephoneExpense' value="+value.telephoneExpense+"></td><td><input readonly  type='text' id='conveyance' name='conveyance' value="+value.conveyance+"></td><td><input readonly  type='text' id='msc' name='misc' value="+value.misc+"></td><input type='hidden' name='user_id' value="+value.user_id+"><input readonly  type='hidden' name='id' value="+value.id+"><input  readonly type='hidden' name='date' value="+value.date+"></tr>");
                                    Sno++;
                                });
                                $('#submit').append('<button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>');
                            }
                            else
                            {
                            	$('.mythead_edit').append("<tr><td style='width:140px;'>"+"<b>Sno</b></td><td style='width:140px;'>"+"<b>Distancte</b>"+"</td><td style='width:140px;'>"+"<b>FARE</b>"+"</td><td style='width:140px;'>"+"<b>D.A.</b>"+"</td><td style='width:140px;'>"+"<b>HOTEl</b>"+"</td><td style='width:140px;'>"+"<b>POSTAGE</b>"+"</td><td style='width:140px;'>"+"<b>TELEPHONE EXPENSES</b>"+"</td><td style='width:140px;'>"+"<b>CONVEYANCE</b>"+"</td><td style='width:140px;'>"+"<b>MISC.</b>"+"</td></tr>");
                                console.log(data.result);
                                $.each(data.result, function (key, value){
                                    $('.mytbody_edit').append("<tr><td>"+Sno+"</td><td><input type='text' id='distance' name='distance' value="+value.distance+"></td><td><input style='width:140px;' type='text' id='fare' name='fare' value="+value.fare+"></td><td><input type='text' id='da' name='da' value="+value.da+"></td><td><input type='text' id='hotel' name='hotel' value="+value.hotel+"></td><td><input type='text' id='postage' name='postage' value="+value.postage+"></td><td><input type='text' id='telephoneExpense' name='telephoneExpense' value="+value.telephoneExpense+"></td><td><input type='text' id='conveyance' name='conveyance' value="+value.conveyance+"></td><td><input type='text' id='msc' name='misc' value="+value.misc+"></td><input type='hidden' name='user_id' value="+value.user_id+"><input type='hidden' name='id' value="+value.id+"><input type='hidden' name='date' value="+value.date+"></tr>");
                                    Sno++;
                                });
                                $('#submit').append('<button class="btn btn-success form-control" id="submit" type="submit" name="submit"><b>Submit</b></button>');
                            }

                                
                        
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
<!-- modal for edit ends here  -->
