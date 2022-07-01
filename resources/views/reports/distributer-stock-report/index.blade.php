@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.distributor_detail_report')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />


 <style type="text/css">
 .btno {
    border:1px solid #438EB9;
    background-color:#438EB9;
    color:white;
    margin-left:10px;
}

 .table-head-span-half-column {border:0px solid gray; width:360px;text-align:center;margin-left:150px;}
 .table-head-span-full-column {border:0px solid gray; width:800px;text-align:center;
    margin-left:350px;}


</style>



@endsection

@section('body')

    <div class="main-content" style="overflow-x: scroll;">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.distributor_detail_report')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#daily_team_report" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <style>
                            .loader {
                                position: fixed;
                                left: 0px;
                                top: 0px;
                                width: 100%;
                                height: 100%;
                                z-index: 9999;
                                background: url('/msell/images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
                                opacity: .8;
                            }
                            </style>
                            <div class="loader"  style="display:none"></div>  
  <!-- Filter CONTENT BEGINS -->
  <form class="form-horizontal open" action="" method="POST" id="daily_team_report" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}
  <div class="row">
      
      
    

                                            @include('common_filter.filter_sale')

            <div class="col-xs-6 col-sm-6 col-lg-2">
              <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                                   
                     <div class="input-group">
                         <span class="input-group-addon">
                             <i class="fa fa-calendar bigger-110"></i>
                        </span>
                         <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                     </div>
                 </div>

      
         
    </div>

  <table width="100%" cellspacing="6"  >
      <tr>
          <br>      
@if($company_id == 62)     
<td><input class="btno btn btn-primary" type="button" value="Distributer Opening Stock" onclick="panel1_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Stock Inwards" onclick="panel6_click();"/></td>
@else
<td><input class="btno btn btn-primary" type="button" value="Distributer Stock Report" onclick="panel1_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Primary Sale Report" onclick="panel6_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Payment Collection Report" onclick="panel2_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Return Report" onclick="panel3_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Secondary Sale Report" onclick="panel7_click();"/></td>

<td><input class="btno btn btn-primary" type="button" value="Distributor Closing Stock Report" onclick="panel8_click();"/></td>


@endif
</tr>

</table>
</form>

<!-- Filter CONTENT ENDS -->

                          


           


<br>

 
                 



<div id="panel1" style="margin-top: 20px">

</div>  <!--panel 1 ends here !-->


 <!--START OF SIXTH PAGE AND FIRST ROW -->
 <div id="panel6" style="display: none" >

    </div>
     <!-- END OF SIXTH PAGE -->
<br>
<!-- START SECOND PAGE -->

<div id="panel2" style="display:none">

</div>
  

<!-- END OF SECON PAGE -->



<!-- START OF THIRD PAGE-->
<div id="panel3" style="display:none">

  
</div> 
<!-- END OF THIRD PAGE -->

<!-- START OF FOURTH PAGE -->

<div id="panel4" style="display:none">

</div>
<!-- END OF FOURTH PAGE  -->

<!--START OF fifth PAGE AND FIRST ROW -->
<div id="panel5" style="display: none" >

</div>
 <!-- END OF FIFTH PAGE -->

  <!-- START OF seven PAGE-->
<div id="panel7" style="display:none; overflow-x: scroll;">

  
</div> 
<!-- END OF seven PAGE -->


<!-- START OF seven PAGE-->
<div id="panel8" style="display:none; overflow-x: scroll;">

  
</div> 
<!-- END OF seven PAGE -->



 

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report5.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>	
	<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>



    <script type="text/javascript">
        function panel1_click()
        {
           
            var from_date =$('#from_date').val();
          
            if(from_date =='')
            {
            alert("Please Select Date");
            return false;
            }
          $.ajax({
                type: "GET",
                url: domain + '/distributer-stock',
              //  dataType: 'html',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                  //  alert("hello");
                    $('#panel1').html(data);
                    // console.log(data); 
                },
                beforeSend: function () {
                // $(".loader").fadeIn("slow");
             },
            complete: function () {
                // $(".loader").fadeOut("slow");
             },
              });
            $('#panel2').hide();
            $('#panel3').hide();
            $('#panel1').show();

            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();
            $('#panel7').hide();
            $('#panel8').hide();


        }

         function panel2_click()
        {
            
            var from_date =$('#from_date').val();
            var dateAsm =$('#date').val();
            if(from_date == '')
            {
                alert("Please Select Date");
                return false; 
            }
            // else if( dateAsm == '')
            // {
            //     alert("Please Select Date");
            //     $('#panel1').html('');
            // }
          $.ajax({
                type: "GET",
                url: domain + '/payment-collection-report',
                // dataType: 'json',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                    $('#panel2').html(data);
                   // console.log(data); 
                },
                beforeSend: function () {
                $(".loader").fadeIn("slow");
             },
            complete: function () {
                $(".loader").fadeOut("slow");
             },
              });
            $('#panel2').show();
            $('#panel3').hide();
            $('#panel1').hide();
            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();
            $('#panel7').hide();
            $('#panel8').hide();

 
        }

         function panel3_click()
        {
            var from_date =$('#from_date').val();
            var dateAsm =$('#date').val();
           
         if( from_date == '')
            {
                alert("Please Select Date");
                return false;
            }
          $.ajax({
                type: "GET",
                url: domain + '/return-report',
                // dataType: 'json',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                    $('#panel3').html(data);
                   // console.log(data); 
                },
                beforeSend: function () {
                $(".loader").fadeIn("slow");
             },
            complete: function () {
                $(".loader").fadeOut("slow");
             },
              });
            $('#panel2').hide();
            $('#panel3').show();
            $('#panel1').hide();
            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();
            $('#panel7').hide();
            $('#panel8').hide();


        } 

        function panel7_click()
        {
            var from_date =$('#from_date').val();
            var dateAsm =$('#date').val();
           
         if( from_date == '')
            {
                alert("Please Select Date");
                return false;
            }
          $.ajax({
                type: "GET",
                url: domain + '/distributor-secondary-sale',
                // dataType: 'json',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                    $('#panel7').html(data);
                   // console.log(data); 
                },
                beforeSend: function () {
                $(".loader").fadeIn("slow");
             },
            complete: function () {
                $(".loader").fadeOut("slow");
             },
              });
            $('#panel2').hide();
            $('#panel7').show();
            $('#panel1').hide();
            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();
            $('#panel3').hide();
            $('#panel8').hide();

        }


          function panel8_click()
        {
            var from_date =$('#from_date').val();
            var dateAsm =$('#date').val();
           
         if( from_date == '')
            {
                alert("Please Select Date");
                return false;
            }
          $.ajax({
                type: "GET",
                url: domain + '/distributorClosingStock',
                // dataType: 'json',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                    $('#panel8').html(data);
                   // console.log(data); 
                },
                beforeSend: function () {
                $(".loader").fadeIn("slow");
             },
            complete: function () {
                $(".loader").fadeOut("slow");
             },
              });
            $('#panel2').hide();
            $('#panel7').hide();
            $('#panel1').hide();
            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();
            $('#panel3').hide();
            $('#panel8').show();

        }

       
       
        
        function panel6_click()
        {
         // alert(domain);
           var from_date =$('#from_date').val();
            var to_date =$('#to_date').val();
        //    alert(userAsm);
            if(from_date =='')
            {
                alert("Please Select From Date");
                $('#panel6').html('');
                return false;
            }
           
          $.ajax({
                type: "GET",
               // url: domain + '/dtrpanel6',
               url: domain + '/dealerprimarysale',
                // dataType: 'json',
                data : $("#daily_team_report").serialize(),
                success: function (data) {
                  //  alert("ewe");
                    $('#panel6').html(data);
                   // console.log(data); 
                },
                beforeSend: function () {
                $(".loader").fadeIn("slow");
             },
            complete: function () {
                $(".loader").fadeOut("slow");
             },
              });
        
            $('#panel2').hide();
            $('#panel3').hide();
            $('#panel1').hide();
            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').show();
            $('#panel7').hide();
            $('#panel8').hide();



        }  
// Ganesh ADD for date Picker
$(function () {
    $('#date').datetimepicker({
        viewMode: 'days',
        format: 'YYYY-MM-DD',
        // useCurrent: true,
        // maxDate: moment()
    });

});
function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

$(document).on('change', '#region', function () {
        _current_val = $(this).val();
      //  alert(_current_val);
        get_region_user(_current_val);
    });

    function get_region_user(val) {
        _region=$('#region');
        _state=$('#state');
        _position=$('#position');
        _user=$('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_region_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                   
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.user, function (key3, value3) {
                            if (value3.name != '') {
                             //   alert("shdfhafhh");
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _user.empty();
                      //  _user.append(template3).trigger("chosen:updated");
                      _user.append(template3).trigger();

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
            //datepicker plugin
				//link
				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
				//or change it into a date range picker
				$('.input-daterange').datepicker({autoclose:true});
			
			
				//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
				$('input[name=date_range_picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
                     showDropdowns: true,
                    // showWeekNumbers: true,             
                    minDate: '2017-01-01',
                    maxDate: moment().add(2, 'years').format('YYYY-01-01'),
					locale: {
                        format: 'YYYY/MM/DD',
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
					},
                    ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            },
                            dateLimit: {
                                                "month": 1
                                            },

				})
				.prev().on(ace.click_event, function()
                {
					$(this).next().focus();
				});
		
    </script>

@endsection
