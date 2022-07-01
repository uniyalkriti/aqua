@extends('layouts.master') 
  
@section('title')
    <title>{{Lang::get('common.liveTracking')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}"/>
    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <style>
        .center {
          margin: auto;
          width: 100%;
        }
        #simple-table table {
            border-collapse: collapse !important;
        }

        #simple-table table, #simple-table th, #simple-table td {
            border: 1px solid black !important;
        }

        #simple-table th {
            /*background-color: #438EB9 !important;*/
            background-color: #7BB0FF !important;
            color: black;
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
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.liveTracking')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                  <!--   <a href="#" data-toggle="collapse" data-target="#manualAttandence" class="btn btn-sm btn-default"><i
                        class="fa fa-navicon mg-r-10"></i> Filter</a> -->
                </p>
                <!-- /.nav-search -->
            </div>
            
        </div>
    </div><!-- /.main-content -->

<!--  sms email notification part starts here  -->
@if(!empty($user_data))
    <form method="get" action="" id="activity" enctype="multipart/form-data">
    {{csrf_field()}}
        <div class="main-container ace-save-state" id="main-container">
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="page-content">
                        <div class="row">
                            <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-2 col-sm-2">
                                            <div class="search-area well well-sm">
                                                <div class="search-filter-header bg-primary">
                                                    <h5 class="smaller no-margin-bottom">
                                                        <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; {{Lang::get('common.liveTracking')}}
                                                    </h5>
                                                </div>

                                                 <div class="hr hr-dotted"></div>
                                                <h4 class="blue smaller">
                                                    <i class="fa fa-book"></i>
                                                    {{Lang::get('common.location3')}}
                                                </h4>
                                                <div class="row">
                                                   <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <select name="state" class="form-control chosen-select" id="state">
                                                                     <option value="">Select</option>
                                                            @if(!empty($state))
                                                                @foreach($state as $sk=>$sr)
                                                                    <option @if(!empty($_GET['state'])) @if($sk == $_GET['state']){{"selected"}} @endif @endif value="{{$sk}}" > {{$sr}} 
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="hr hr-dotted"></div>
                                                <h4 class="blue smaller">
                                                    <i class="fa fa-tags"></i>
                                                    Users
                                                </h4>
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <select name="user_id" class="form-control chosen-select" id="user_id">
                                                                     <option value="">Select</option>
                                                            @if(!empty($users))
                                                                @foreach($users as $k=>$r)
                                                                 <option @if(!empty($_GET['user_id'])) @if($k == $_GET['user_id']){{"selected"}} @endif @endif value="{{$k}}" > {{$r}} 
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                              
                                             
                                             
                                               
                                                
                                                <div class="hr hr-dotted"></div>
                                                
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-default btn-round btn-white">
                                                        <i class="ace-icon fa fa-send green"></i>
                                                        Filter
                                                    </button>
                                                </div>
                                            </div> 
                                            
                                        </div> 
                                        <!-- table content starts here  -->
                                        <div class="col-xs-10 col-sm-10">
                                            <div class="row">
                                               
                                                <div id="g_map" style="width:100%;height:450px;"></div>


                                            </div>
                                        </div>
                                        <!-- table content Ends here  -->

                                        <!-- table content start here -->

                                        {{--<div class="col-xs-12 col-sm-2">
                                            <div class="search-area well well-sm">
                                                <div class="search-filter-header bg-primary">
                                                    <h5 class="smaller no-margin-bottom">
                                                        <i class="ace-icon fa fa-sliders light-green bigger-130"></i>&nbsp; {{Lang::get('common.liveTracking')}} Data
                                                    </h5>
                                                </div>

                                                 <div class="hr hr-dotted"></div>
                                                <h4 class="blue smaller">
                                                    <i class="fa fa-book"></i>
                                                    In Field
                                                </h4>
                                                <div class="row">
                                                   <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <input type="text" name="tracking_query_count" value="{{count($tracking_query)}}" class="form-control" readonly>
                                                                  
                                                    </div>
                                                </div>


                                                <div class="hr hr-dotted"></div>
                                                <h4 class="blue smaller">
                                                    <i class="fa fa-tags"></i>
                                                    Not In Field
                                                </h4>
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        <input type="text" name="user_count" value="{{count($users) - count($tracking_query)}}" class="form-control" readonly>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                            
                                        </div> --}}

                                        <!-- table content ends here -->





                                    </div>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->
                            
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->
        </div><!-- /.main-container -->
    </form>
@endif
<!--  sms email notification part ends here  -->
@endsection

@section('js')



<script>
        var locations = <?php echo $records?>;
        // var mylocations = <?php echo $records?>;
        // var locations = JSON.parse('<?= $records ?>');  
        // console.log(locations[0]); 

        var splt = locations[0].split(',');

        // console.log(splt[0]); 

        var llt = Number(splt[0]);
        var lltng = Number(splt[1]);


               function initMap() {
                var iconnumber = 0;
            //     var mapr={lat:28.6310235, lng:77.2129054};
            // var mapr={lat:28.6310235, lng:77.2129054};
            var mapr={lat:llt, lng:lltng};
                 var map = new google.maps.Map(document.getElementById('g_map'), {
                   zoom: 12,
                   center: mapr,
                 });

                 var infoWin = new google.maps.InfoWindow();
         
                 // Create an array of alphabetical characters used to label the markers.
                // var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         
                 // Add some markers to the map.
                 // Note: The code uses the JavaScript Array.prototype.map() method to
                 // create an array of markers based on a given "locations" array.
                 // The map() method here has nothing to do with the Google Maps API.
                 var markers = locations.map(function(location, i) {
                   var marker =  new google.maps.Marker({
                     position: location,
                     // click : onclickMarker,
                    // label: labels[i % labels.length]
                   });


                     google.maps.event.addListener(marker, 'mouseover', function(evt) {
                    var latlng = new google.maps.LatLng(Number(location.lat),Number(location.lng));
                    var geocoder= new google.maps.Geocoder();
                     geocoder.geocode({'location': latlng}, function(results, status) 
                        {
                             if (status === 'OK') 
                                {
                                    if (results[0]) 
                                    {
                                        var contents="<div style='color:blue; width:200px;' ><strong>"+location.username+"</strong><br>"+results[0].formatted_address+"<br><strong>Last Track Time : </strong>"+location.tracktime+" </div>";

                                        infoWin.setContent(contents);
                                        infoWin.open(map, marker);


                                        } else {
                                      window.alert('No results found');
                                    }
                                } else {
                                    window.alert('Geocoder failed due to: ' + status);
                                }
                        });

                    
                    })





                   google.maps.event.addListener(marker, 'click', function(evt) {
                        var url = "userMapTracking?user_id="+location.userid+"&track_date="+location.trackdate;
                        window.open(url, "_blank");
                    })
                   return marker;

                 });

                 // alert(markers);
         
                 // Add a marker clusterer to manage the markers.
                 var markerCluster = new MarkerClusterer(map, markers,
                     {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

                 // alert(MarkerClusterer);

               }
              // init function close


               // loop paste cluster starts
               var latlng = new Array();
               for (var i = 0; i < locations.length; i++) {
                var data = locations[i].split(',');
          
                   var latt = Number(data[0]);
                   // alert(latt);
                   var lngg = Number(data[1]);
                   var username = data[2];
                   var tracktime = data[4];
                   var userid = Number(data[5]);
                   var trackdate = data[6];

                   locations[i] = {lat: latt, lng: lngg, userid: userid, username: username,tracktime: tracktime,trackdate: trackdate };
               }
               // loop paste cluster ends



               function setMarkers()
                {
                    alert('1');
                }




             </script>
             <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
             </script>
           <!--   <script async defer
                   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCHKZzcHdoESnXYiFr3W_ggCQlflGIn_Es&callback=initMap">
               </script> -->
            
               <!--<script async defer-->
               <!--         src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZcoCB6RErqBXaAarm3B5LQYFmefWqH5w&callback=initMap">-->
               <!-- </script>-->
                
                <!--  <script async defer
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUeJ8H_Cwvz_GdTThA4Ft-OLE0Izr32s&callback=initMap">
                </script> -->


  <script async defer
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2VaSYlOElhHrnhHWhpFDb6I24HhjyXkI&callback=initMap">
                </script>




    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>
    @include('common_filter.filter_script_sale')
    <script type="text/javascript">
        function checkAll(ele) 
        {
            var checkboxes = document.getElementsByTagName('input');
            if (ele.checked) 
            {
                 for (var i = 0; i < checkboxes.length; i++) 
                {
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = true;
                    }
                }
            } 
            else 
            {
                for (var i = 0; i < checkboxes.length; i++) 
                {
                     console.log(i)
                    if (checkboxes[i].type == 'checkbox') 
                    {
                         checkboxes[i].checked = false;
                    }
                }
            }
        }
    </script>
    <script>
        $('#timepicker1').timepicker({
                        minuteStep: 1,
                        showSeconds: true,
                        showMeridian: false,
                        disableFocus: true,
                        icons: {
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down'
                        }
                    }).on('focus', function() {
                        $('#timepicker1').timepicker('showWidget');
                    }).next().on(ace.click_event, function(){
                        $(this).prev().focus();
                    });
    $(".chosen-select").chosen();
    $('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
    });
    </script>
    <script>
    $(document).ready(function () {
        $('#smstr').show('fast');
        $('#emailtr').hide('fast');
        $('#notitr').hide('fast');
        $('#notiimg').hide('fast');
    });
        $(document).on('change', '#category', function () {
        _current_val = $(this).val();
        get_category(_current_val);
        });

        function get_category(val) 
        {
            if(val=='sms')
            {
                $('#smstr').show('fast');
                $('#emailtr').hide('fast');
                $('#notitr').hide('fast');
                $('#notiimg').hide('fast');
            }
            else if(val=='email')
            {
                $('#subtr').show('fast');
                $('#emailtr').show('fast');
                $('#smstr').hide('fast');
                $('#notitr').hide('fast');
                $('#notiimg').hide('fast');
            }
            else
            {
                $('#notitr').show('fast');
                $('#emailtr').hide('fast');
                $('#smstr').hide('fast');
                $('#notiimg').show('fast');
            }
        }
    </script>

       <script>
     $(document).on('change', '#state', function () {
        val = $(this).val();
        // headQuarter = $('#location_3').val();
        _hq = $('#user_id');
        // alert(headQuarter);
        if(val != '')
       {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: domain + '/get_user_name_new',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    
                  
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                          
                            // console.log(value);
                            if (value.name != '') {
                               
                                template += '<option value="' + key + '" >' + value + '</option>';
                                
                            }
                        });
                        // console.log(template);
                      //  alert(_hq.val());
                        _hq.empty();
                        _hq.append(template).trigger("chosen:updated");
               

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

    <script>
    //  $(document).on('load', '#state', function () {
    //     val = $(this).val();
    //     // headQuarter = $('#location_3').val();
    //     _hq = $('#user_id');
    //     // alert(headQuarter);
    //     if(val != '')
    //    {
    //     $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });
    //         $.ajax({
    //             type: "GET",
    //             url: domain + '/get_user_name_new',
    //             dataType: 'json',
    //             data: "id=" + val,
    //             success: function (data) {
                    
                  
    //                     template = '<option value="" >Select</option>';

    //                     $.each(data.result, function (key, value) {
                          
    //                         // console.log(value);
    //                         if (value.name != '') {
                               
    //                             template += '<option value="' + key + '" >' + value + '</option>';
                                
    //                         }
    //                     });
    //                     // console.log(template);
    //                   //  alert(_hq.val());
    //                     _hq.empty();
    //                     _hq.append(template).trigger("chosen:updated");
               

    //             },
    //             complete: function () {
    //                 // $('#loading-image').hide();
    //             },
    //             error: function () {
    //             }
    //         });  
    //    }
        
    // });
    </script>
@endsection