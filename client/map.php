<!DOCTYPE html>
<?php 
require_once ('./include/config.inc.php');
require_once('./include/my-functions.php');

$array="";
$latlng = $_GET['lat_lng'];
$lat_lng=  explode('|',$latlng);

foreach($lat_lng as $value){
    if(trim($value)!=''){ 
        $latlongs[]=$value; 
        
    }
}
//pre($latlongs);
$len_latlong=  sizeof($latlongs); // here we find out lat_long size
// if only one tracking has been found then source and destination will be same.

$origin="$latlongs[0]"; 
$n=$len_latlong-1;
$destination="$latlongs[$n]";

for($i=1;$i< $len_latlong-1; $i++){
    $array.= "{ location : '$latlongs[$i]' }, ";
}
$array=  rtrim($array,',');

?>

<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Draggable directions</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>

var rendererOptions = {
  draggable: true,
  polylineOptions:{ strokeColor: "red" },
};
var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);;
var directionsService = new google.maps.DirectionsService();
var map;

var australia = new google.maps.LatLng(28.708602,77.106961);

function initialize() {

  var mapOptions = {
    zoom: 7,
    center: australia
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  directionsDisplay.setMap(map);
  directionsDisplay.setPanel(document.getElementById('directionsPanel'));

  google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
    computeTotalDistance(directionsDisplay.getDirections());
  });

  calcRoute();
}

function calcRoute() {

  var request = {
    origin: '<?php echo $origin;?>',
    destination: '<?php echo $destination;?>',
    waypoints:[ <?php echo $array;?> ],
    travelMode: google.maps.TravelMode.DRIVING
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    }
  });
}

function computeTotalDistance(result) {
  var total = 0;
  var myroute = result.routes[0];
  for (var i = 0; i < myroute.legs.length; i++) {
    total += myroute.legs[i].distance.value;
  }
  total = total / 1000.0;
  document.getElementById('total').innerHTML = total + ' km';
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas" style="float:left;width:70%; height:100%"></div>
    <div id="directionsPanel" style="float:right;width:30%;height:100%">
    <!--<p>Total Distance: <span id="total"></span></p>
    </div>
  </body>
</html>