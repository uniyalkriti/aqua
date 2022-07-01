@extends('layouts.master')
 
@section('title')
    <title>Geo fence</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <li class="active">Show Geofence</li>
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
                            {{ csrf_field() }}
                               <div class="row">
                                     <div class="col-sm-3">
                                        <label class="control-label">Town</label>
                                        <input  class="form-control" type="town" readonly name="town" value={{$geofenceData[0]['name']}}>
                                     </div> 

                                    <div class="col-sm-3">
                                        <label class="control-label">Latitude</label>
                                        <textArea required="required" class="form-control" readonly name="polylat" id="lat" style="margin: 0px; width: 190px; height: 74px;">{{$lat}}</textArea>
                                     </div>
                                     <div class="col-sm-3">
                                        <label class="control-label">Longitude</label>
                                        <textArea required="required" class="form-control" readonly name="polylng" id="lng" style="margin: 0px; width: 190px; height: 74px;">{{$lng}}</textArea>
                                     </div>
                                 </div><br>
                                 <div class="br-section-wrapper">
                                <div id="map" style="width:100%; min-height:550px;">
                                </div>
                            </div>
                         <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@endsection
@section('js')
    <script>
var styleShiftWorker = [{
                'featureType': 'administrative',
                'elementType': 'all',
                'stylers': [{
                    'visibility': 'on'
                }, {
                    'lightness': 33
                }]
            }, {
                'featureType': 'administrative',
                'elementType': 'labels',
                'stylers': [{
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'administrative',
                'elementType': 'labels.text',
                'stylers': [{
                    'gamma': '0.75'
                }]
            }, {
                'featureType': 'administrative.neighborhood',
                'elementType': 'labels.text.fill',
                'stylers': [{
                    'lightness': '-37'
                }]
            }, {
                'featureType': 'landscape',
                'elementType': 'geometry',
                'stylers': [{
                    'color': '#f9f9f9'
                }]
            }, {
                'featureType': 'landscape.man_made',
                'elementType': 'geometry',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '40'
                }, {
                    'visibility': 'off'
                }]
            }, {
                'featureType': 'landscape.natural',
                'elementType': 'labels.text.fill',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '-37'
                }]
            }, {
                'featureType': 'landscape.natural',
                'elementType': 'labels.text.stroke',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '100'
                }, {
                    'weight': '2'
                }]
            }, {
                'featureType': 'landscape.natural',
                'elementType': 'labels.icon',
                'stylers': [{
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'poi',
                'elementType': 'geometry',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '80'
                }]
            }, {
                'featureType': 'poi',
                'elementType': 'labels',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '0'
                }]
            }, {
                'featureType': 'poi.attraction',
                'elementType': 'geometry',
                'stylers': [{
                    'lightness': '-4'
                }, {
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'poi.park',
                'elementType': 'geometry',
                'stylers': [{
                    'color': '#c5dac6'
                }, {
                    'visibility': 'on'
                }, {
                    'saturation': '-95'
                }, {
                    'lightness': '62'
                }]
            }, {
                'featureType': 'poi.park',
                'elementType': 'labels',
                'stylers': [{
                    'visibility': 'on'
                }, {
                    'lightness': 20
                }]
            }, {
                'featureType': 'road',
                'elementType': 'all',
                'stylers': [{
                    'lightness': 20
                }]
            }, {
                'featureType': 'road',
                'elementType': 'labels',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'gamma': '1.00'
                }]
            }, {
                'featureType': 'road',
                'elementType': 'labels.text',
                'stylers': [{
                    'gamma': '0.50'
                }]
            }, {
                'featureType': 'road',
                'elementType': 'labels.icon',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'gamma': '0.50'
                }]
            }, {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [{
                    'color': '#c5c6c6'
                }, {
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'road.highway',
                'elementType': 'geometry.stroke',
                'stylers': [{
                    'lightness': '-13'
                }]
            }, {
                'featureType': 'road.highway',
                'elementType': 'labels.icon',
                'stylers': [{
                    'lightness': '0'
                }, {
                    'gamma': '1.09'
                }]
            }, {
                'featureType': 'road.arterial',
                'elementType': 'geometry',
                'stylers': [{
                    'color': '#e4d7c6'
                }, {
                    'saturation': '-100'
                }, {
                    'lightness': '47'
                }]
            }, {
                'featureType': 'road.arterial',
                'elementType': 'geometry.stroke',
                'stylers': [{
                    'lightness': '-12'
                }]
            }, {
                'featureType': 'road.arterial',
                'elementType': 'labels.icon',
                'stylers': [{
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'road.local',
                'elementType': 'geometry',
                'stylers': [{
                    'color': '#fbfaf7'
                }, {
                    'lightness': '77'
                }]
            }, {
                'featureType': 'road.local',
                'elementType': 'geometry.fill',
                'stylers': [{
                    'lightness': '-5'
                }, {
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'road.local',
                'elementType': 'geometry.stroke',
                'stylers': [{
                    'saturation': '-100'
                }, {
                    'lightness': '-15'
                }]
            }, {
                'featureType': 'transit.station.airport',
                'elementType': 'geometry',
                'stylers': [{
                    'lightness': '47'
                }, {
                    'saturation': '-100'
                }]
            }, {
                'featureType': 'water',
                'elementType': 'all',
                'stylers': [{
                    'visibility': 'on'
                }, {
                    'color': '#14a0c1'
                }]
            }, {
                'featureType': 'water',
                'elementType': 'geometry',
                'stylers': [{
                    'saturation': '53'
                }]
            }, {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [{
                    'lightness': '-42'
                }, {
                    'saturation': '17'
                }]
            }, {
                'featureType': 'water',
                'elementType': 'labels.text.stroke',
                'stylers': [{
                    'lightness': '61'
                }]
            }];
      // This example creates a simple polygon representing the Bermuda Triangle.
      // When the user clicks on the polygon an info window opens, showing
      // information about the polygon's coordinates.
      var path = [];
      var map;
      var infoWindow;

       <?php
          foreach($geofenceData as $row)
       { 
         $path['lat'] = $row->lat;
         $path['lng'] = $row->lng;
         $path['zone_id'] = $row->location_id;
       
         $pathresult[] = $path;
       }
       $json_data = json_encode($pathresult);
           ?>
      var resultArray = JSON.parse('<?=$json_data ?>');
        var lat = resultArray[0]['lat'];
        var lng = resultArray[0]['lng'];
  
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 18,
          center: { lat:(Number(lat)),lng:(Number(lng))},
          styles: styleShiftWorker,
          mapTypeId: 'terrain'
        });

       var path = []
       var zone = []
       for (var i=0; i<resultArray.length; i++) {
      
           path[i] = new google.maps.LatLng(resultArray[i].lat, resultArray[i].lng);
           zone[i] = resultArray[i].zone_id;
           
       }

        // Construct the polygon.
        var bermudaTriangle = new google.maps.Polygon({
          paths: path,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 3,
          fillColor: '#FF0000',
          fillOpacity: 0.35
        });
        bermudaTriangle.setMap(map);

        // Add a listener for the click event.
        bermudaTriangle.addListener('click', showArrays);

        infoWindow = new google.maps.InfoWindow;
      }

      /** @this {google.maps.Polygon} */
      function showArrays(event) {
        // Since this polygon has only one path, we can call getPath() to return the
        // MVCArray of LatLngs.
        

        var contentString = '<b>Geo Fence Area</b><br>' +
            'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +
            '<br>';

        // Iterate over the vertices.
        

        // Replace the info window's content and position.
        infoWindow.setContent(contentString);
        infoWindow.setPosition(event.latLng);

        infoWindow.open(map);
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDRh65mFUb_M6gFDKnrBFJulYABgamRqE4&callback=initMap">
    </script>

@endsection