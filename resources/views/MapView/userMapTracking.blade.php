@extends('layouts.mapDashboard')

@section('title')
    <title>{{Lang::get('common.user_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('huicalender/css/huicalendar.css')}}" />

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

    


@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
           
            <div class="page-content">

                <form method="get" action="" id="activity" enctype="multipart/form-data">
                {{csrf_field()}}

                    <div class="row">


                       <!--  <div class="myCalendar">
                        </div> -->





                      <!--    <div class="col-md-2" style="display: flex; margin: auto;">

                            <font size="5px">Date </font> &nbsp&nbsp&nbsp
                            <input value="" autocomplete="off" type="text" placeholder="Track Date" name="track_date" id="from_date" class="form-control date-picker input-sm">

                        </div>

                        <div class="col-md-1" style="display: flex; margin: auto;">
                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                    ><i class="fa fa-search mg-r-10"></i>
                                Find
                            </button>
                        </div> -->

                        <input type="hidden" name="user_id" value="{{$userDetails->user_id}}">
                    </div>

                  

                    <div class="row">

                        <div class="col-md-2" style="display: flex; margin: auto;">
                             <div class="myCalendar">
                            </div>
                        </div>

                        <div class="col-md-7" style="display: flex; margin: auto;">

                            <font size="5px">User Name :- {{$userDetails->user_name}} , </font> &nbsp&nbsp&nbsp&nbsp&nbsp
                            <font size="5px">Designation :- {{$userDetails->rolename}} </font> &nbsp&nbsp&nbsp&nbsp&nbsp
                        </div>

                        <div class="col-md-3" style="display: flex; margin: auto;">

                           

                            <font size="5px">Track Date :- {{$track_date}}  </font> &nbsp&nbsp

                              <a onclick="fnExcelReport()" href="javascript:void(0)"
                                class="nav-link"><i class="fa fa-file-excel-o  fa-2x"></i></a>

                        </div>


                    </div>
                </form>

                    
                    <div class="row">
                        <div class="col-md-9" style="display: flex; margin: auto;">
                            <div id="map" style="float:right; width:100vw; height:550px;" ></div>
                        </div>
                        <div class="col-xs-3 col-sm-3">

                          


                        <table id = "simple-table"  class="table table-bordered" style="font-size: 13px;border: 1px black;width: auto;height: 550px;overflow-y: scroll;overflow-x: scroll;display: block;">
                            <tr class="info" style="color: black;">
                                <th>User Name/Track Date</th>
                                <th colspan="2">{{$userDetails->user_name}} / {{$track_date}} </th>
                            </tr>

                            <tr class="info" style="color: black;">
                                <th colspan="3">
                                    TC: {{!empty($totalCall->totalCall)?$totalCall->totalCall:'0'}}<br>
                                    PC: {{!empty($productiveCall->productiveCall)?$productiveCall->productiveCall:'0'}}<br>
                                    Secondary: {{!empty($productiveCall->finalSale)?$productiveCall->finalSale:'0'}}<br>
                                </th>
                            </tr>

                        

                            <tr class="info" style="color: black;">
                               
                                <th>Track {{Lang::get('common.time')}}</th>
                                <th>Geo {{Lang::get('common.address')}}</th>
                                <th>Status</th>
                            </tr>
                            <tbody>
                                <?php $h=1;  $track_count=29; ?>
                                 <?php  ksort($user_work_tracking); ?>
                                @if(!empty($user_work_tracking) && count($user_work_tracking)>0)
                                    @foreach($user_work_tracking as $key=>$data)

                                    <?php  
                                    $latlng = str_replace(' ',',',$data->lat_lng);
                                    $implodecord = explode(',', $latlng);
                                    $lat = $implodecord[0];
                                    $lng = $implodecord[1];

                                    $iconnumber = $key+1;

                                    $datetime = $data->track_date.' '.$data->track_time;
                                    ?>
                                            
                                                   
                                                        <tr>
                                                            
                                             
                                                            <td><a href="#" onclick="addMarker('{{$lat}}','{{$lng}}','{{$iconnumber}}','0','{{$datetime}}','{{$data->status}}','0','0','1'); return false;">
                                                                {{$data->track_time}}</a> <br>
                                                                <font color="red">{{$data->battery_status}}% </font> <i class="fa fa-battery-half" aria-hidden="true"></i>

                                                                @if($data->gps_status == 0)
                                                                <font color="green">ON </font> <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                                @else
                                                                <font color="red">OFF </font> <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                                @endif

                                                            </td>
                                                            <td>{{$data->track_address}}</td>
                                                            <td>{{$data->status}}</td>
                                                            
                                                        </tr>

                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                            
                        </div> 



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
    <script src="{{asset('js/user.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

    <script src="{{asset('huicalender/js/huicalendar.js')}}"></script>

    <!-- <script src="/path/to/cdn/jquery.min.js"></script> -->



    <script>
        var summaryPanel = document.getElementById('directions-panel');
        var total = 0;
        var routeSegment = 0;
        var startindex='<?=$start?>';
        var lastindex='<?=$last?>';
        var locstatusarrays =["Attendance","Tracking","Tracking","Tracking","Tracking","DAILYCLOSURE","BDMCLOSURE","PROJECTCLOSURE","Tracking","Tracking","DAILYCLOSURE","DAILYCLOSURE","DAILYCLOSURE","BDMCLOSURE","Tracking","Tracking","Dealer_Appointment","Tracking","Tracking"];
                function initMap() {
                  var iconnumber = 0;
            bounds = new google.maps.LatLngBounds();
            var mapDiv = document.getElementById('map');
            
            map = new google.maps.Map(mapDiv, {
              zoom: 14,
              center: {lat: 28.6321252, lng: 77.2161135},      
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              suppressMarkers: true
            });
        var checkboxArray = <?php echo $way?>;
        // console.log(checkboxArray);
        var coordinates = new Array();
        var battery_status_array = new Array();
        var gps_status_array = new Array();
        var time_array = new Array();
        var module = new Array();
        // FOR LAT LNG
        // alert(checkboxArray.length);
        var last=checkboxArray.length;
        for (var i = 0; i < checkboxArray.length ; i++) {
          iconnumber = iconnumber+1;
          var startpoint = checkboxArray[i]; 
          var forlatlng = startpoint.split(",");
          // console.log(forlatlng);  
          var starting_address = forlatlng[7];
          var lat = Number(forlatlng[0]);
          var longi = Number(forlatlng[1]);
          var time = forlatlng[2];
          var istatus = forlatlng[3];
          var battery_status = (forlatlng[4]);
          var date_time = (forlatlng[5]);
          var gps_status = (forlatlng[6]);
          // console.log(forlatlng);
          var point = new google.maps.LatLng(lat, longi);
          // alert(battery_status);
                      coordinates.push(point);
                      battery_status_array.push(battery_status);
                      gps_status_array.push(gps_status);
                      time_array.push(date_time);
                      module.push(istatus);

                      addMarker(lat,longi,iconnumber,last,time,istatus,lastindex,startindex);
        }
        
        calculateRoute(coordinates, true,battery_status_array,time_array,gps_status_array,module);
      }
      // ADD MARKER
      function addMarker(lat,lng,iconnumber,last,time,istatus,lastindex,startindex,clickstatus)
      {
         // console.log(istatus); 
         // console.log(iconnumber);
        var color = '1d81c3';
        // if(startindex==1 && iconnumber==1){
        //   color = 'd34941';
        // }           
        // if(lastindex==1 && iconnumber==last){
        //   color = 'ffab38';
        // }
        //alert(istatus);
    // attendence color =red
        if(istatus=='Attendance'){
            color = 'd34941'; 
        }
       // checkout color =yellow
       else if(istatus=='CheckOut'){
            color = 'ffab38';
        }
        // tracking color=white
       else if(istatus=='Tracking'){
            color = 'd580ff';
        }
        // sales color=blue
        
        else if(istatus=='Order Booking'){
            color = '1d81c3';
            
        }
        // outlet_creation color=green
        
        else if(istatus=='Outlet'){
            color = '00cc00';
        }
        else if(istatus=='Daily Reporting'){
            color = '8c8c8c';
        }

       // iconnumber = iconnumber+1;
        var markerSale = new google.maps.Marker({
            position: new google.maps.LatLng(lat,lng),
              // center: {lat: 28.6321252, lng: 77.2161135},      
            zoom: 14,
      map: map,
      icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+iconnumber+'|'+color+'|000000'

    });
    bounds.extend(new google.maps.LatLng(lat,lng));
        map.fitBounds(bounds);


    if(clickstatus==1){
    markerSale.setAnimation(google.maps.Animation.BOUNCE);
    // markerSale.LatLng(lat,lng);
    map.setZoom(20);
    map.panTo(markerSale.position);


    var infoWindow = new google.maps.InfoWindow();
            (function (markerSale, lat,lng,time,istatus) {
                    //Create and open InfoWindow.
                 
                   var geocoder = new google.maps.Geocoder();
                    var latlng = new google.maps.LatLng(lat,lng);

                   geocoder.geocode({'latLng': latlng}, function(results, status) { 
                   var address = '';
                    address =  results[0]['formatted_address'];
                   
                    infoWindow.setContent("<div id='content' style = 'width:200px;min-height:40px'>"+address+"</strong><br><strong>DATE :"+time+"</div>");
                    infoWindow.open(map, markerSale);

                });

            })(markerSale, lat,lng,time,istatus);
            

    } 





// INFO WINDOW
  var infoWindow = new google.maps.InfoWindow();
            (function (markerSale, lat,lng,time,istatus) {
                google.maps.event.addListener(markerSale, "click", function (e) {
                    //Create and open InfoWindow.
                 
                   var geocoder = new google.maps.Geocoder();
                    var latlng = new google.maps.LatLng(lat,lng);

                   geocoder.geocode({'latLng': latlng}, function(results, status) { 
                   var address = '';
                    address =  results[0]['formatted_address'];
                   
                    infoWindow.setContent("<div id='content' style = 'width:200px;min-height:40px'>"+address+"</strong><br><strong>DATE :"+time+"</div>");
                    infoWindow.open(map, markerSale);

                });
                });

            })(markerSale, lat,lng,time,istatus);


        return true;
      }
     
// CALCULATE ROUTE
function calculateRoute(coordinates,displayDistance,battery_status,time,gps_status,module) {
   // console.log(battery_status);
   // alert(battery_status);
        var startpoint = coordinates.shift(); //13.002551,77.634585
       // console.log(startpoint);

        var endpoint = startpoint; //13.002402,77.634293
        var waypts = [];
        // var b_stat = [];
        var len = coordinates.length;
        if(len<=25)
        {
          var length = len;
        }
        else
        {
          var length = 25;
        }
        // console.log(len);
        // var qwerty = waypts.length;
        // console.log(qwerty);
        for (var i = 0; i <= length; i++) {
          var point = coordinates.shift();
          // var b_status = battery_status.shift();
          // b_stat.push(b_status);
          // console.log(time);
          if( point == undefined && len==0)
          {
            // console.log(startpoint);
            endpoint = startpoint;

            drawroute(startpoint,endpoint,waypts,displayDistance,battery_status,time,gps_status,module);

          }
          if( point == undefined )
            continue;
          waypts.push({location:point,stopover:true});
          if(waypts.length == 25 )// Google maps has limitations of 8 waypoints
          {
            if(coordinates.length > 0)
              endpoint = coordinates.shift();

            else
              endpoint = waypts.pop().location;
              drawroute(startpoint,endpoint,waypts,displayDistance,battery_status,time,gps_status,module);
              startpoint = endpoint;
              waypts = [];
          }
        }
        // console.log(waypts.length);
        if( waypts.length > 0 )
        {
          // console.log(endpoint);
          endpoint = waypts.pop().location;
          drawroute(startpoint,endpoint,waypts,displayDistance,battery_status,time,gps_status,module);
        }
 }
      // RUNNING DRAW ROUTE
    function drawroute(startpoint,endpoint,waypts,displayDistance,battery_status,time,gps_status,module){
        // console.log(battery_status);
    if(typeof startpoint == 'undefined' || typeof endpoint == 'undefined')
      return;
    var directionsService = new google.maps.DirectionsService();
    // console.log(directionsService);
    var directionsRequest = {
      origin: startpoint,
      destination: endpoint,
      waypoints:waypts,
      optimizeWaypoints:false,
      travelMode: google.maps.DirectionsTravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC
    };
    directionsService.route(
      directionsRequest,
      function(response, status)
      {
      if (status == google.maps.DirectionsStatus.OK)
      {
        var route = response.routes[0];
        new google.maps.DirectionsRenderer({
          map: map,
          directions: response,
          suppressMarkers: true,
          preserveViewport:true
        });
        if( displayDistance ){
          for (var i = 0; i < route.legs.length; i++) {
            
            var g_status;
            if(gps_status[i]==1)
            {
                g_status = 'OFF';
            }
            else
            {
                g_status = 'ON';
            }
            // console.log(starting_address);
            total = total+route.legs[i].distance.value;
            routeSegment = Number(routeSegment) + 1;
            summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
            '</b><br>';
            summaryPanel.innerHTML += '<b style="background-color:white;">Module: '+ module[i] +'<br> Time: ' + time[i] + ' ' +'<b>Battery Status: ' + battery_status[i] +'% ' + '<b>Gps Status: ' + g_status +'. <br>'  ;
            summaryPanel.innerHTML += '<b>From : </b>'+route.legs[i].start_address + '<br> <b>To :</b>';
            summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
            summaryPanel.innerHTML += (route.legs[i].distance.value/1000) + 'Km. <br>';
            
 
}
print_total(total);
// total = total / 1000;
//  document.getElementById('totalKm').innerHTML = total + ' km';
        }
        map.fitBounds(bounds);
      }
      else
      {
      }
    });

  }
  function print_total(total)
{
    total = total/1000;
document.getElementById('totalKm').innerHTML = total + ' km';
}
      
    </script>
<!-- <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD96suZt39wVDLJ_D1xRmDQ2JA3I5m4Xwg&callback=initMap">
    </script> -->
 <!--    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJHHqymxlSqRqSNm1z6_vWkQ0YuKx3pS8&callback=initMap">
    </script> -->


      <!--  <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDwgjq-uujWa_YlWCj9YiN-1MMMGF2z8w&callback=initMap">
    </script> -->


    <!--   <script async defer-->
    <!--        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZcoCB6RErqBXaAarm3B5LQYFmefWqH5w&callback=initMap">-->
    <!--</script>-->
    
  <!--    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUeJ8H_Cwvz_GdTThA4Ft-OLE0Izr32s&callback=initMap">
    </script> -->


      <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2VaSYlOElhHrnhHWhpFDb6I24HhjyXkI&callback=initMap">
    </script>


    
    <script>
      $("#date").datetimepicker  ( {

    format: 'YYYY-MM-DD'
    });

    </script>
   <!-- <script type="text/javascript">

        var map;
        var arr = 'Array';
        // console.log(arr);
        var personName = '';
        var mobileNumber = '';
        var devices_loc = JSON.parse('');
        // console.log(devices_loc);
        var ank = 0;
        function initMap() {
            var origin = '{{!empty($attendance->lat_lng)?$attendance->lat_lng:''}}'; //attendance
            if(origin == '' || origin == null)
            {
                var centerLatLng = {lat: 28.6321252, lng: 77.2161135};
            }
            else{
                var resCenter = origin.split(",");
                var latCenter = parseFloat(resCenter[0]);
                var lngCenter = parseFloat(resCenter[1]);
                var  centerLatLng = {lat: latCenter, lng: lngCenter};
            }
            var directionsService = new google.maps.DirectionsService;
// var directionsDisplay = new google.maps.DirectionsRenderer;
            directionsDisplay = new google.maps.DirectionsRenderer({
// see more options here: https://developers.google.com/maps/documentation/javascript/reference#DirectionsRendererOptions
                map: map,
                polylineOptions: {
                    strokeColor: '#06117f',

                },

// if you want your own markers (with different image) this should be true
                suppressMarkers: true,
                preserveViewport: true
            });

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: centerLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                heading: 90,
                tilt: 45

            });
// FOR ATTD //
            var color = 'd3463f';
//  var  myLatlng = new google.maps.LatLng(lat,lng);
            var marker = new google.maps.Marker({
                position: centerLatLng,
                zoom: 14,
                icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=1|'+color+'|000000',
                map: map
            });
// END OF ATTD
            var  waypts = <?php echo  $way?>;
            var color='3190d0';

            var iconnumber = 2;
            waypts.forEach(function(feature) {
                var res = feature.location.split(",");
                var lat = Number(res[0]);
                var lng = Number(res[1]);
// console.log(res[0]);
                var  myLatlng = new google.maps.LatLng(lat,lng);
//  console.log(myLatlng.lat);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+iconnumber+'|'+color+'|000000',
                    map: map
                });
                iconnumber = iconnumber+1;
            });

// FOR CHECKOUT //
            var destination = '{{!empty($checkout->lat_lng)?$checkout->lat_lng:''}}';
            if(destination == '')
            {

            }
            else{

                var color = 'ff9f00';
                var destinationCenter = destination.split(",");
                var deslatCenter = parseFloat(destinationCenter[0]);
                var deslngCenter = parseFloat(destinationCenter[1]);
                var  myLatlngdes = new google.maps.LatLng(deslatCenter,deslngCenter);
                var marker = new google.maps.Marker({
                    position: myLatlngdes,
                    zoom: 14,
                    icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+iconnumber+'|'+color+'|000000',
                    map: map
                });
            }


// END OF CHECKOUT
// 28.6321252,77.2161135
            directionsDisplay.setMap(map);

            calculateAndDisplayRoute(directionsService, directionsDisplay);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay)     {
            console.log();
            var origin = '{{!empty($attendance->lat_lng)?$attendance->lat_lng:''}}'; //attendance
       //     console.log(origin);
            var destination2 = '{{!empty($checkout2->lat_lng)?$checkout2->lat_lng:''}}';
             var destination1 = '{{!empty($checkout->lat_lng)?$checkout->lat_lng:''}}';
            if(destination1 == '' )
            {
                var destination = destination2;
            }
            else{
                var destination = destination1;
            }
//console.log(destination);
 //console.log(<?php echo  $way?>);
                directionsService.route({
                origin: origin,
                destination: destination,
                waypoints: <?php echo  $way?>,
                optimizeWaypoints: true,
                provideRouteAlternatives : true,
                travelMode: 'DRIVING'
            }, function(response, status) {
                if (status === 'OK') {
                    console.log(response);
                    directionsDisplay.setDirections(response);
                    var route = response.routes[0];
                    var summaryPanel = document.getElementById('directions-panel');
                    summaryPanel.innerHTML = '';
// For each route, display summary information.
                    var total = 0;
                    var loctimearrays =["11:58:53","12:00:35","12:45:14","13:00:34","13:00:47","13:10:43","13:19:05","13:22:17","14:00:25","14:00:34","14:37:33","14:42:27","14:42:38","14:43:45","15:00:24","15:00:34","15:35:28","16:00:32","16:13:36"];
                    var locstatusarrays =["Attendance","Tracking","Tracking","Tracking","Tracking","DAILYCLOSURE","BDMCLOSURE","PROJECTCLOSURE","Tracking","Tracking","DAILYCLOSURE","DAILYCLOSURE","DAILYCLOSURE","BDMCLOSURE","Tracking","Tracking","Dealer_Appointment","Tracking","Tracking"];
                    for (var i = 0; i < route.legs.length; i++) {
                       
                        total = total+route.legs[i].distance.value;
                        routeSegment = Number(routeSegment) + 1;
                        summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                        '</b><br>';
                        summaryPanel.innerHTML += route.legs[i].start_address + '<br> <b>To :</b>';
                        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                        summaryPanel.innerHTML += (route.legs[i].distance.value/1000) + 'Km. <br>';
                        // var routeSegment = i + 1;
                        // summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                        //     '</b><br>';
                        // summaryPanel.innerHTML += route.legs[i].start_address + '<br> <b>To :</b>';
                        // summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                        // summaryPanel.innerHTML += (route.legs[i].distance.value/1000) + 'Km. <br> <b>Status:</b>';
                        // summaryPanel.innerHTML += locstatusarrays[i] + '</b><br><br>';
                    }
                    print_total(total);
                    // total = total / 1000.0;
                    // document.getElementById('totalKm').innerHTML = total + ' km';
// alert(total);
                } else {
// window.alert('Directions request failed due to ' + status);
                }
            });
        }
        
function print_total(total)
{
    total = total/1000;
document.getElementById('totalKm').innerHTML = total + ' km';
}
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&callback=initMap">
    </script>-->

    <script type="text/javascript">
    function fnExcelReport() {
        var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
        var textRange;
        var j = 0;
        tab = document.getElementById('simple-table'); // id of table

        for (j = 0; j < tab.rows.length; j++) {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            //tab_text=tab_text+"</tr>";
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
        }
        else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

        return (sa);
    }
    </script>



    <!-- reference website https://www.jqueryscript.net/time-clock/hui-month-calendar.html -->
    <script type="text/javascript">
        


        $('.myCalendar').huicalendar({
              enabledDay: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
              selectable: true,
        }).on('changeDate', function(e){

            var user_id = '<?php echo $userDetails->user_id; ?>';
            var year = e.year;
            var month = ("0" + (e.month)).slice(-2);
            var date = ("0" + e.date).slice(-2);
            var finddate = year+'-'+month+'-'+date;


            // console.log(date);

            window.location = "http://aqualabindia.in/public/userMapTracking?user_id="+user_id+"&track_date="+finddate;

            // viewDay: new Date(e.year+'/'+month+'/'+date)
            // viewDay: new Date('2021/07/01')


          // console.log(year)
          // console.log(month)
          // console.log(date)
        })


    </script>

   

@endsection
