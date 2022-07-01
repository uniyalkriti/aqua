@extends('layouts.master')

@section('title')
    <title>Expense</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
     <style>
        .modal-lg2{
            width: 1230px;
        }
        .modal-lg3{
            width: 700px;
        }

    </style>
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('distributor')}}">Advocate </a>
                    </li>

                    <li class="active">Add Details</li>
                </ul>

            </div>

            <div class="page-content">
                <div class="clearfix" style="margin-top: 5px"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        @if(Session::has('message'))
                            <div class="alert alert-block {{ Session::get('alert-class', 'alert-info') }}">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>
                                <i class="ace-icon fa fa-check green"></i>
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        @if(count($errors)>0)
                            @foreach ($errors->all() as $error)
                                <div class="help-block">{{ $error }}</div>
                            @endforeach
                        @endif

                        <form class="form-horizontal" action="{{route('expense.store')}}" method="POST"
                              id="expense-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable"
                                     id="widget-container-col-3">
                                    <div class="widget-box widget-color-blue2 collapsed ui-sortable-handle"
                                         id="widget-box-3">
                                        <div class="widget-header widget-header-small">
                                            <h6 class="widget-title">
                                                <i class="ace-icon fa fa-map-pin"></i>
                                                Add Expense
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table id="mytable" width="100%"  class="table table-bordered table-hover" style="overflow-x: scroll;">

                                <th>S.No</th>
                                <th>Expense Type.</th>
                                <th>Expense Date</th>
                                <th>Fare</th> 
                                <th>Remarks</th>   
                                <th>Image </th>


                               
                                <tbody class="order_body">
                                    <tr>
                                    <td width="200px">1</td>            
                                    <td width="200px">
                                        <select id="expense_type1" name="expense_type[]" >
                                            <option value="">Select Expense</option>
                                            @if(!empty($expense_type))
                                                @foreach($expense_type as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                       
                                    </td>
                                    <td width="200px">
                                        <input  type="text"  name="expense_date[]" id="expense_date1">
                                    </td>            
                                    <td width="200px">
                                        <input  type="text" value=" "   name="fare[]" id="fare1">
                                    </td>            
                                              
                                    
                                    <td width="200px">
                                        <input  type="text" value=" "   name="remarks[]" id="remarks1" >
                                    </td>
                                    <td width="200px">
                                        <input  type="file" value=" "   name="imageFile[]" id="image1">
                                    </td>

                                    
                                    <td width="70px" ><i  title="more" id="sr_no1" class="fa fa-plus" onclick="return addfunction()" ></i></td>  
                                    </tr>                      
                                </tbody>
                               
                            </table>
                            
                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-9">
                                    <button class="btn btn-info btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-120"></i>
                                        Submit
                                    </button>

                                </div>
                            </div>
                        </form>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog ">
    
        <!-- Modal content-->
        <div class="modal-content">
            
            <div class="modal-body ui-dialog-content ui-widget-content">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <table class="table table-bordered table-hover" >
                        
                          <tbody>
                                <tr>
                                       
                                    <td colspan="2" style="font-size: 14px;text-align:center;border: 1px solid #C76E06;border-radius: 3px 3px 3px 3px;background: linear-gradient(to bottom, #A4A4A4 0%, #A4A4A4 60%) ;color: white;font-weight: bold;width: 250px;"><a href="#"  onclick="return secrchData1()" style="color: white;">
                                        सम्पत्ति विलेख विवरण(Property Deed)</a><label style="color: black;font-size: 16px;"></label>
                                     </td>  
                                 </tr>
                                 <tr>
                                  <td colspan="2" style="font-size: 14px;text-align:center;border: 1px solid #C76E06;border-radius: 3px 3px 3px 3px;background: linear-gradient(to bottom, #A4A4A4 0%, #A4A4A4 60%) ;color: white;font-weight: bold;width: 250px;"><a href = "#"  onclick="return secrchData()" style="color: white;">                                  
                                      भूखण्ड /गाटे पर दर्ज़ वादों का विवरण(Revenue court case)</a><label style="color: black;font-size: 16px;"></label>
                                    </td> 
                                </tr>
                                <tr style="margin-top: 60px">  
                                     <td style="font-size: 14px;text-align:center;border: 1px solid #C76E06;border-radius: 3px 3px 3px 3px;background: linear-gradient(to bottom, #A4A4A4 0%, #A4A4A4 60%) ;color: white;font-weight: bold;">                                       
                                        <a href="#" onclick="return secrchData2 ()" style="color: white;">खाता विवरण(Record of Right)</a> <label style="color: black;font-size: 16px;"></label>
                                    </td>
                                     
                                    </tr>
                                
                                 

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

@endsection

@section('js')

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('js/dealer.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
     
    <script>
        $(document).on('keyup', '#plot_no', function () {
            _current_val = $(this).val();
            // alert(_current_val);
             khasra_no = document.getElementsByName('khasra_no[]');
             // alert(khasra_no);
            // $('#khasra_no'+khasra_no).val() = _current_val;
            // alert('khasra_no'+khasra_no);
                document.getElementById("khasra_no"+khasra_no.length).value = _current_val;
            // alert(khasra_no.length);
            // var array = [];
            // var count = 0;
            // for(var i = 0; i < khasra_no.length; ++i){
            //     if(array[i] == 2)
            //         count++;
            // }
            // // alert(count);
        });
       
        $(document).on('change', '#location_2', function () {
            var valu = document.getElementById("location_2");
            _current_val =valu.options[valu.selectedIndex].text;
            // alert(_current_val);

             tehsil = document.getElementsByName('tehsil[]');
             // alert(khasra_no);
            // $('#khasra_no'+khasra_no).val() = _current_val;
            // alert('khasra_no'+khasra_no);
                document.getElementById("tehsil"+tehsil.length).value = _current_val;
            // alert(khasra_no.length);
            // var array = [];
            // var count = 0;
            // for(var i = 0; i < khasra_no.length; ++i){
            //     if(array[i] == 2)
            //         count++;
            // }
            // // alert(count);
        });

        $(document).on('change', '#location_3', function () {
            var valu = document.getElementById("location_3");
            _current_val =valu.options[valu.selectedIndex].text;
            // alert(_current_val);

             loc_vill_dis = document.getElementsByName('loc_vill_dis[]');
             // alert(khasra_no);
            // $('#khasra_no'+khasra_no).val() = _current_val;
            // alert('khasra_no'+khasra_no);
                document.getElementById("loc_vill_dis"+loc_vill_dis.length).value = _current_val;
            // alert(khasra_no.length);
            // var array = [];
            // var count = 0;
            // for(var i = 0; i < khasra_no.length; ++i){
            //     if(array[i] == 2)
            //         count++;
            // }
            // // alert(count);
        });
        $(document).on('change', '#location_1', function () {
            var valu = document.getElementById("location_1");
            _current_val =valu.options[valu.selectedIndex].text;
            // alert(_current_val);

             // final_place = document.getElementsByName('final_place[]');
             // alert(khasra_no);
            // $('#khasra_no'+khasra_no).val() = _current_val;
            // alert('khasra_no'+khasra_no);
                document.getElementById("final_place").value = _current_val;
            // alert(khasra_no.length);
            // var array = [];
            // var count = 0;
            // for(var i = 0; i < khasra_no.length; ++i){
            //     if(array[i] == 2)
            //         count++;
            // }
            // // alert(count);
        });

        $(document).on('change', '#location_1', function () {
            _current_val = $(this).val();
            location_data(_current_val,2);
        });

        $(document).on('change', '#location_2', function () {
            _current_val = $(this).val();
            location_data(_current_val,3);
        });

        $(document).on('change', '#location_3', function () {
            _current_val = $(this).val();
            location_data(_current_val,4);
        });
       function secrchData()
       {
            var dist_code = $('#location_1').val();
            var tehsil_code = $('#location_2').val();
            var village_code = $('#location_3').val();
            var plot_no = $('#plot_no').val();
            window.open('http://vaad.up.nic.in/rcms_metro/Revenue_case_details.aspx?Vill_Cd_Census='+village_code+'&Khasra_Number='+plot_no+'');
        }
        function secrchData2()
       {
            var dist_code = $('#location_1').val();
            var tehsil_code = $('#location_2').val();
            var village_code = $('#location_3').val();
            var plot_no = $('#plot_no').val();
            window.open('http://upbhulekh.gov.in/rorPlotDetail.jsp?dcc='+dist_code+'&tcc='+tehsil_code+'&vcc='+village_code+'&kn='+plot_no+'');
        }
        function secrchData1()
       {
            var dist_code = $('#location_1').val();
            // console.log(dist_code);
            var tehsil_code = $('#location_2').val();
            var village_code = $('#location_3').val();
            var plot_no = $('#plot_no').val();

           // // window.open({method: 'GET', url: 'https://igrsup.gov.in/igrsup/newPropertySearchAction', headers: {
           // //    'districtCode': dist_code,
           // //    'sroCode': dist_code,
           // //    'propertyId': '',
           // //    'propNEWAddress': plot_no,
           // //    'gaonCode1': village_code,

           // //      }
           //  });
           // var dist_code = '123';
           //  // console.log(dist_code);
           //  var tehsil_code = '123';
           //  var village_code = '123';
           //  var plot_no = '123';

           //  url= 'https://igrsup.gov.in/igrsup/newPropertySearchAction';
           //  data= {'districtCode':dist_code,'sroCode':'219','propertyId':'','propNEWAddress':plot_no,'gaonCode1':village_code,'action%3AgetPropertyDeedSearchDetail':'PropertyDeedSearchS:'};
           // $.post(url, function (data) {
           //      // "Access-Control-Allow-Origin": *;
           //     // $.ajaxSetup({
           //     //      headers: {
           //     //          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
           //     //          'Content-Type': 'application/json;charset=UTF-8',
           //     //          "Access-Control-Allow-Origin":true,
           //     //          "Access-Control-Allow-Credentials": true,
           //     //          "Access-Control-Request-Headers": 'content-Type,user-agent,x-hny-team',
           //     //      }
           //     //  });
           //     $.ajaxSetup({
           //          headers: {
           //              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           //              "Access-Control-Allow-Origin":true,
           //          }
           //      });
           //      var w = window.open('about:blank');
           //      w.document.open();
           //      w.document.write(data);
           //      w.document.close();
           //      alert('1');
           //  });

            url= 'https://igrsup.gov.in/igrsup/newPropertySearchAction';
            data= {'districtCode':dist_code,'sroCode':'219','propertyId':'','propNEWAddress':plot_no,'gaonCode1':village_code,'action%3AgetPropertyDeedSearchDetail':'PropertyDeedSearchS:'};
           $.post(url, function (data) {
                // "Access-Control-Allow-Origin": *;
               // $.ajaxSetup({
               //      headers: {
               //          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
               //          'Content-Type': 'application/json;charset=UTF-8',
               //          "Access-Control-Allow-Origin":'*',
               //          "Access-Control-Allow-Credentials": true,
               //          "Access-Control-Request-Headers": 'content-Type,user-agent,x-hny-team',
               //      }
               //  });
               $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        "Access-Control-Allow-Origin":true,
                    }
                });
                var w = window.open('about:blank','windowname');
                w.document.open(url);
                // w.document.write(data);
                w.document.close();
            });
           // alert('1');
              // postData = Encoding.Default.GetBytes("districtCode=" + dist_code + "&sroCode=" + dist_code + "&propertyId=&propNEWAddress=" + plot_no + "&gaonCode1=" + village_code + "&action%3AgetPropertyDeedSearchDetail=PropertyDeedSearchS:");
              //   string vHeaders = "Content-Type: application/x-www-form-urlencoded" + Environment.NewLine;
              //   targetURL = "";
              //   webBrowser1.Navigate(targetURL, "_blank", postData, vHeaders);
                //  
                // $.ajax({
                //     type: "POST",
                //     url: 'https://igrsup.gov.in/igrsup/newPropertySearchAction',
                //     async:true,
                //     crossDomain:true,       
                    
                //     dataType: 'json',
                //     data: {'districtCode':dist_code,'sroCode':'219','propertyId':'','propNEWAddress':plot_no,'gaonCode1':village_code,'action%3AgetPropertyDeedSearchDetail':'PropertyDeedSearchS:'},


                //     // data: {"districtCode":dist_code,"sroCode":"219","propertyId":'',"propNEWAddress":plot_no,"gaonCode1":village_code,"action%3AgetPropertyDeedSearchDetail":"PropertyDeedSearchS:"},
                // });
            // window.open('https://igrsup.gov.in/igrsup/newPropertySearchAction?dcc='+dist_code+'&tcc='+tehsil_code+'&vcc='+village_code+'&kn='+plot_no+'');
        }
       

    </script>

    <script>
        var cust_id = 2;
        var cust_id_s = 1;
        // console.log(cust_id);
        function addfunction()
        {
            // console.log(sr_no);
            str2 = cust_id_s;

            var expense_type = `<select  required="required"   name="expense_type[]"
                                                id="expense_type${cust_id}">
                                <option value="">Select Expense</option>
                                @if(!empty($expense_type))
                                    @foreach($expense_type as $p_key=>$p_value)
                                        <option value="{{$p_key}}">{{$p_value}}</option>
                                    @endforeach
                                @endif
                            </select>`;

            var expense_date = `<input  type="text"  name="expense_date[]" id="expense_date${cust_id}"> `;
            var fare = `<input  type="text"  name="fare[]" id="fare${cust_id}"> `;
            var remarks = `<input  type="text"  name="remarks[]" id="remarks${cust_id}"> `;
            var image = `<input  type="file"  name="imageFile[]" id="image${cust_id}"> `;

            
         

            var template = ('<tr><td>'+cust_id+'</td><td>'+expense_type+'</td><td>'+expense_date+'</td><td>'+fare+'</td><td>'+remarks+'</td><td>'+image+'</td><td width="70px" ><i id=sr_no'+cust_id+' title="more" class="fa fa-plus" aria-hidden="true" onclick="return addfunction()"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></td></tr>');
            $('.order_body').append(template);
            
                
            // var total_hec_grand = 0;
            // var total_share_grand = 0;
            // var total_share = 0;
            // var d=str2;
            // var share_two= document.getElementById("share_two"+d).value;
            // var share_one= document.getElementById("share_one"+d).value;
            // var area= document.getElementById("area_one"+d).value;

            // var formula_value = (share_one/share_two)*area;
            //     document.getElementById("total_area"+d).value = formula_value.toFixed(2);
            
            //  total_share = document.getElementsByName('total_area[]');
            // // console.log(total_share)
            // // var total_hec = document.getElementsByName('total_hec[]');
            
            // for (var po = 0; po < total_share.length; po++)
            // {
            //     // var gtweight = Weight[po].value;
            //     total_share_grand += parseFloat(total_share[po].value);
            //     // total_hec_grand += parseInt(total_hec[po].value);
                
            // }
            // document.getElementById('total_share').value=(total_share_grand/0.405).toFixed(2); 
            // document.getElementById('total_hec').value=total_share_grand; 
            

            
            
           cust_id++;
           cust_id_s++;

            

        }

        $('#mytable').on('click','.removenewrow',function(){

              var table = $(this).closest('table');
              var i = table.find('.mytbody_dispatch7').length;                 

              if(i==1)
              {
                 return false;
              }

             $(this).closest('tr').remove();
           

            
        });
        // function confirmAction(heading, name, action_id, tab, act) {
        //     $.confirm({
        //         title: heading,
        //         content: 'Are you sure want to ' + act + ' ' + name + '?',
        //         buttons: {
        //             confirm: function () {
        //                 takeAction(name, action_id, tab, act);
        //                 $.alert('Done!');
        //                 window.setTimeout(function () {
        //                     location.reload()
        //                 }, 3000);
        //             },
        //             cancel: function () {
        //                 $.alert('Canceled!');
        //             }
        //         }
        //     });
        // }
        function mulfunctionHissa(str)
        {
            var total_hec_grand = 0;
            var total_share_grand = 0;
            var total_share = 0;
            var d=str.substr(5,3);
            // var share_two= document.getElementById("share_two"+d).value;
            // var share_one= document.getElementById("share_one"+d).value;
            var area= document.getElementById("hissa"+d).value;
            // alert(area);
            var formula_value = area;
                document.getElementById("total_area"+d).value = formula_value;
            
             total_share = document.getElementsByName('total_area[]');
            // console.log(total_share)
            // var total_hec = document.getElementsByName('total_hec[]');
            
            for (var po = 0; po < total_share.length; po++)
            {
                // var gtweight = Weight[po].value;
                total_share_grand += parseFloat(total_share[po].value);
                // total_hec_grand += parseInt(total_hec[po].value);
                
            }
            document.getElementById('total_share').value=(total_share_grand/0.405).toFixed(2); 
            document.getElementById('total_hec').value=total_share_grand; 
        }
        function mulfuncOrderConfirm(str2)
        {
            var total_hec_grand = 0;
            var total_share_grand = 0;
            var total_share = 0;
            var d=str2.substr(9,3);

            var hissa = document.getElementById("hissa"+d).value;
            if(hissa > 0)
            {
                // alert(1);
            }
            else
            {

                var share_two= document.getElementById("share_two"+d).value;
                var share_one= document.getElementById("share_one"+d).value;
                var area= document.getElementById("area_one"+d).value;

                var formula_value = (share_one/share_two)*area;
                    document.getElementById("total_area"+d).value = formula_value.toFixed(2);
            }
            
            
             total_share = document.getElementsByName('total_area[]');
            // console.log(total_share);
            // var total_hec = document.getElementsByName('total_hec[]');
            
            for (var po = 0; po < total_share.length; po++)
            {
                // var gtweight = Weight[po].value;
                total_share_grand += parseFloat(total_share[po].value);
                // total_hec_grand += parseInt(total_hec[po].value);
                
            }
            document.getElementById('total_share').value=(total_share_grand/0.405).toFixed(2); 
            document.getElementById('total_hec').value=total_share_grand; 
            
        }

        function takeAction(module, action_id, tab, act) {

            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

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

        jQuery(function ($) {
            $('#filterForm').collapse('hide');
        });
    </script>
    <script type="text/javascript">
    $('.verror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.vnumerror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
      $('.valphaerror').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[0-9`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[0-9`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
      $('.rate').keyup(function()
    {
        var yourInput = $(this).val();
        re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(yourInput);
        if(isSplChar)
        {
            var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
     $('#pin_no').change(function(){
        var pin_no = $(this).val();
        var color = 'red';
        if (pin_no!=''){
        if(pin_no.length<6){
        $('#pin_error').html('PIN No should be 6 Digit');
        $('#pin_error').css('color', color);
        }
       }
      });
       $("#letter_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#date_of_doc").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        $("#final_date").datetimepicker  ( {
            format: 'YYYY-MM-DD'
        });
        // $("#commencement_date").datetimepicker  ( {
        //     format: 'YYYY-MM-DD'
        // });
        // $("#termination_date").datetimepicker  ( {
        //     format: 'YYYY-MM-DD'
        // });
        // $("#certificate_issue_date").datetimepicker  ( {
        //     format: 'YYYY-MM-DD'
        // });
    </script>

@endsection