@extends('layouts.master')
 
@section('title')
    <title>Geo fence</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
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
                    <li class="active">Draw Geofence</li>
                </ul><!-- /.breadcrumb -->
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
                         <?php $id = Crypt::encryptString($geofenceData[0]['location_id']);?>
                           <!-- PAGE CONTENT BEGINS --> 
                        {!! Form::open(array('route'=>['geofence.update',$id] , 'method'=>'PUT','enctype'=>'multipart/form-data' ))!!}
                            {{ csrf_field() }}
                               <div class="row">
                                     <div class="col-sm-3">
                                        <label class="control-label">Town</label>
                                        <input  class="form-control" type="town" readonly name="town" value={{$geofenceData[0]['name']}}>
                                     </div> 

                                     <div class="col-sm-3">
                                        <label class="control-label">Latitude</label>
                                        <textArea required="required" class="form-control" readonly name="polylat" id="lat" style="margin: 0px; width: 190px; height: 74px;"></textArea>
                                     </div>
                                     <div class="col-sm-3">
                                        <label class="control-label">Longitude</label>
                                        <textArea required="required" class="form-control" readonly name="polylng" id="lng" style="margin: 0px; width: 190px; height: 74px;"> </textArea>
                                     </div>
                                      <div class="col-sm-3">
                                       <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                            style="margin-top: 28px;">
                                        Submit
                                    </button>
                                     </div>
                                 </div><br>
                                 <div class="br-section-wrapper">
                                      <input type="button"  onclick="document.location.reload(true);" class="btn btn-danger" value="Remove Selected Geofence">
                                      <input id="pac-input" class="controls"  style="width:30%; height:40px" type="text" placeholder="Search Box">
                                <div id="map" style="width:100%; min-height:550px;">
                                </div>
                            </div>
                        </form>
                         <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@endsection

@section('js')
 <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&libraries=geometry,places,drawing">

    </script>
   
    <script>
        //This variable gets all coordinates of polygone and save them. Finally you should use this array because it contains all latitude and longitude coordinates of polygon.
        var coordinates = [];
        //This variable saves polygon.
        var polygons = [];
        var circle;
        var rect;
    </script>

    <script>
        //This function save latitude and longitude to the polygons[] variable after we call it.
        function save_coordinates_to_array(polygon) {
            //Save polygon to 'polygons[]' array to get its coordinate.
            polygons.push(polygon);

            //This variable gets all bounds of polygon.
            var polygonBounds = polygon.getPath();

            for (var i = 0; i < polygonBounds.length; i++) {
                coordinates.push(polygonBounds.getAt(i).lat(), polygonBounds.getAt(i).lng());
                // alert(i);
                var polylat = document.getElementById('lat').value;
                if (polylat == '' || polylat == null) {
                    document.getElementById('lat').value = polygonBounds.getAt(i).lat();
                    document.getElementById('lng').value = polygonBounds.getAt(i).lng();
                }
                else {
                    document.getElementById('lat').value = document.getElementById('lat').value + "," + polygonBounds.getAt(i).lat();
                    document.getElementById('lng').value = document.getElementById('lng').value + "," + polygonBounds.getAt(i).lng();
                }

            }

        }
    </script>
    <script type="text/javascript">

        function initialize() {
            $("#custom-BtnPolygon").click(function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);

            });
            $("#custom-BtnPolyline").click(function () {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYLINE);
            });
            var centerlat = Number('28.562031');
            var centerlng = Number('77.252052');
            //Create a Google maps.
            var map = new google.maps.Map(document.getElementById('map'),
                {zoom: 18, center: new google.maps.LatLng(centerlat, centerlng)});


            //Create a drawing manager panel that lets you select polygon, polyline, circle, rectangle or etc and then draw it.
            //  var drawingManager = new google.maps.drawing.DrawingManager();
            //   drawingManager.setMap(map);

            var drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['']
                    //drawingModes: ['marker', 'circle', 'polygon', 'polyline', 'rectangle']
                },
                //markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
                circleOptions: {
                    fillColor: '#c10d0d',
                    fillOpacity: 0.4,
                    strokeWeight: 2,
                    clickable: false,
                    editable: true,
                    zIndex: 1
                },

                polygonOptions: {
                    fillColor: '#c10d0d',
                    strokeWeight: 2,
                    strokecolor: '#c10d0d',
                    fillOpacity: 0.4,
                    editable: true
                },

            });
            google.maps.event.addListener(drawingManager, 'polygoncomplete', function (r) {
                 drawingManager.setDrawingMode(null);
            });
            drawingManager.setMap(map);

            //This event fires when creation of polygon is completed by user.
            google.maps.event.addDomListener(drawingManager, 'polygoncomplete', function (polygon) {
                //This line make it possible to edit polygon you have drawed.
                polygon.setEditable(true);

                //Call function to pass polygon as parameter to save its coordinated to an array.
                save_coordinates_to_array(polygon);

                //This event is inside 'polygoncomplete' and fires when you edit the polygon by moving one of its anchors.
                google.maps.event.addListener(polygon.getPath(), 'set_at', function () {
                    alert('changed');
                    save_coordinates_to_array(polygon);
                });

                //This event is inside 'polygoncomplete' too and fires when you edit the polygon by moving on one of its anchors.
                google.maps.event.addListener(polygon.getPath(), 'insert_at', function () {
                    alert('also changed');
                    save_coordinates_to_array(polygon);
                });
            });
//google.maps.event.addListener(drawingManager, 'circlecomplete', onCircleComplete);

///google.maps.event.addListener(drawingManager, 'rectanglecomplete', onRect);
// #..................................SearchBox code..........................................#
              // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
              searchBox.setBounds(map.getBounds());
            });

       
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
              var places = searchBox.getPlaces();

              if (places.length == 0) {
                return;
              }

              // For each place, get the icon, name and location.
              var bounds = new google.maps.LatLngBounds();
              places.forEach(function(place) {
                if (!place.geometry) {
                  console.log("Returned place contains no geometry");
                  return;
                }

                if (place.geometry.viewport) {
                  // Only geocodes have viewport.
                  bounds.union(place.geometry.viewport);
                } else {
                  bounds.extend(place.geometry.location);
                }
              });
              map.fitBounds(bounds);
            });
// #..................................SearchBox End Here..........................................#
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
@endsection