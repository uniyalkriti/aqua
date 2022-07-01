<?php

	//For online site
	$dbc = @mysqli_connect('localhost','demo_db','3ZG5!S9sDp','demo_db') OR die ('could not connect:');
	$timezoneset = mysqli_query($dbc, "SET time_zone = '+5:30'");
	mysqli_set_charset($dbc,"utf8");
        mysqli_query($dbc, "SET sql_mode=''");
        //define('SERVER_NAME',"http://".$_SERVER['HTTP_HOST']."/msell");
	if(!$timezoneset)
		die('Sorry time zone could not be set');


?>
