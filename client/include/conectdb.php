<?php
//This file contains the database access information. It will included on every file requiring database access.
if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080' || substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168' || substr($_SERVER['HTTP_HOST'], 0, 7) == '172.168')
{
	//For localhost checking
	$dbc = @mysqli_connect('162.222.39.114','demo_db','','demo_db') OR die ('could not connect:' .mysqli_connect_error());

	mysqli_set_charset($dbc,"utf8");
        mysqli_query($dbc, "SET sql_mode=''");
        //define('SERVER_NAME',"http://".$_SERVER['HTTP_HOST']."/msell");
	//mysqli_set_charset($dbc,"utf8"); 
	//putenv("TZ=Australia/Brisbane");
}
else
{
	//For online site
	$dbc = @mysqli_connect('localhost','demo_db','3ZG5!S9sDp','demo_db') OR die ('could not connect:');
	$timezoneset = mysqli_query($dbc, "SET time_zone = '+5:30'");
	mysqli_set_charset($dbc,"utf8");
        mysqli_query($dbc, "SET sql_mode=''");
        //define('SERVER_NAME',"http://".$_SERVER['HTTP_HOST']."/msell");
	if(!$timezoneset)
		die('Sorry time zone could not be set');
}
?>