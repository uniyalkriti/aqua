@extends('layouts.master')

@section('title')
    <title>Daily Team Report {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>


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

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li class="active">{{Lang::get('common.pending_claim')}}</li>
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
      
      {{-- <div class="col-md-6 col-sm-6 col-lg-2">
          <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
          <select name="region" id="region" class="form-control" required>
              <option value="">select</option>
              @if(!empty($region))
                  @foreach($region as $k=>$r)
                      <option value="{{$k}}">{{$r}}</option>
                  @endforeach
              @endif
          </select>
    </div> --}}
    <div class="col-xs-6 col-sm-6 col-lg-2">
        <div class="">
            <label class="control-label no-padding-right" for="name">{{Lang::get('common.location2')}}</label>
            <select multiple name="region[]" id="region" class="form-control chosen-select">
                <option disabled="disabled" value="">select</option>
                @if(!empty($region))
                    @foreach($region as $k=>$r)
                        <option value="{{$k}}">{{$r}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
   
    <div class="col-xs-6 col-sm-6 col-lg-2">
        <div class="">
            <label class="control-label no-padding-right"
                   for="name">{{Lang::get('common.location3')}}</label>
            <select multiple name="area[]" id="area" class="form-control chosen-select">
                <option disabled="disabled" value="">select</option>
                @if(!empty($state))
                    @foreach($state as $k=>$r)
                        <option value="{{$k}}">{{$r}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-xs-6 col-sm-6 col-lg-2">
        <div class="">
            <label class="control-label no-padding-right"
                   for="name">{{Lang::get('common.location4')}}</label>
            <select multiple name="territory[]" id="territory" class="form-control chosen-select">
                <option disabled="disabled" value="">Select</option>
                @if(!empty($town))
                    @foreach($town as $k=>$r)
                        <option value="{{$k}}">{{$r}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>


    <div class="col-xs-6 col-sm-6 col-lg-2">
        <div class="">
            <label class="control-label no-padding-right"
                   for="name">{{Lang::get('common.dealer_module')}}</label>
            <select multiple name="distributor[]" id="distributor" class="form-control chosen-select">
                <option disabled="disabled"  value="">Select</option>
                @if(!empty($distributor))
                    @foreach($distributor as $k=>$r)
                        <option value="{{$k}}">{{$r}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col-xs-6 col-sm-6 col-lg-2">
        <div class="">
            <label class="control-label no-padding-right"
                   for="name">{{Lang::get('common.location5')}}</label>
            <select multiple name="belt[]" id="belt" class="form-control chosen-select">
                <option disabled="disabled" value="">Select</option>
                @if(!empty($beat))
                    @foreach($beat as $k=>$r)
                        <option value="{{$k}}">{{$r}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
  </div>
  <div class="row">
        <div class="col-xs-6 col-sm-6 col-lg-2">
            <div class="">
                <label class="control-label no-padding-right"
                        for="name">Role</label>
                <select name="role[]" multiple id="role" class="form-control chosen-select">
                    <option disabled="disabled" value="">Select</option>
                    @if(!empty($role))
                        @foreach($role as $k=>$r)
                            <option value="{{$k}}">{{$r}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-2">
            <div class="">
                <label class="control-label no-padding-right"
                        for="name">User</label>
                <select multiple name="user[]" id="user" class="form-control chosen-select">
                    <option disabled="disabled" value="">Select</option>
                    @if(!empty($users))
                        @foreach($users as $k=>$r)
                            <option value="{{$k}}">{{$r}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>



        <div class="col-xs-6 col-sm-6 col-lg-2">
            <div class="">
                <label class="control-label no-padding-right"
                        for="name">From</label>
                <input value="" autocomplete="off" type="text" placeholder="From Date" name="from_date" id="from_date" class="form-control date-picker" required>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-2">
            <div class="">
                <label class="control-label no-padding-right"
                        for="name">To</label>
                <input value="" autocomplete="off" type="text" placeholder="From Date" name="to_date" id="to_date" class="form-control date-picker" required>
            </div>
        </div>  
        {{-- <div class="col-xs-6 col-sm-6 col-lg-1">
                <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                        style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                    Find
                </button>
        </div> --}}
    </div>

  <table width="100%" cellspacing="6"  >
      <tr>
          <br>           
<td><input class="btno btn btn-primary" type="button" value="Distributer Stock" onclick="panel1_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Primary Sale" onclick="panel6_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Payment Collection" onclick="panel2_click();"/></td>
<td><input class="btno btn btn-primary" type="button" value="Return" onclick="panel3_click();"/></td>

</tr>

</table>
</form>

<!-- Filter CONTENT ENDS -->

                          


           


<br>

     <table width="100%" border="1" >
        <tr>
<td>Dealer Stock Report</td>
</tr>

</table>

                 



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
                    console.log(data); 
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
            $('#panel1').show();

            $('#panel4').hide();
            $('#panel5').hide();
            $('#panel6').hide();

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
        var today = moment().format('YYYY-MM-DD');
document.getElementById("from_date").value = today;
    </script>

@endsection
