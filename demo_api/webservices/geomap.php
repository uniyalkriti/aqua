<?php
$lat=$_GET['lat'];
$lng=$_GET['lng'];
//$mmc=$_GET['mmc'];
//$mnc=$_GET['mnc'];
//$lac=$_GET['lac'];
//$cellid=$_GET['cellid'];
//include_once("../include/functions.php");
/*
if(($lat==0) && ($lng==0)){
    $latlng=getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cellid);
    $lat_lng=explode(",",$latlng);
    $lat=$lat_lng[0];
    $lng=$lat_lng[1];
}
*/
?>
<html>
    <head>
        <style type="text/css">
		body{
			margin:0px;
			padding:0px;
		}
            div#map {
                position: relative;
            }

            div#crosshair {
                position: absolute;
                top: 192px;
                height: 200px;
                width: 200px;
                left: 50%;
                margin-left: -8px;
                display: block;
                background: url(crosshair.gif);
                background-position: center center;
                background-repeat: no-repeat;
            }
        </style>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">
            var map;
            var geocoder;
            var centerChangedLast;
            var reverseGeocodedLast;
            var currentReverseGeocodeResponse;

            function initialize() {
                var latlng = new google.maps.LatLng(<?php echo $lat ?>,<?php echo $lng ?>);
                var myOptions = {
                    zoom: 17,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                geocoder = new google.maps.Geocoder();

                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: "<?php echo $address; ?>"
                });

            }

        </script>
    </head>
    <body onload="initialize()">
        <div id="map" style="width:500px; height:500px">
            <div id="map_canvas" style="width:100%; height:500px"></div>
            <div id="crosshair"></div>
        </div>


    </body>
</html>