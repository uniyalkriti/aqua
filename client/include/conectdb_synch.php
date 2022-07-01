<?php
//This file contains the database access information. It will included on every file requiring database access.
if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080' || substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168' || substr($_SERVER['HTTP_HOST'], 0, 7) == '172.168')
{
	//For localhost checking
	$new_dbc=@mysqli_connect('14.141.28.7','root','Dcatch','msell-dsgroup1') OR die ('could not connect:' .mysqli_connect_error());

	mysqli_set_charset($new_dbc,"utf8");
        mysqli_query($new_dbc, "SET sql_mode=''");
}

?>
