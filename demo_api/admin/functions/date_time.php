<?php
//function to dd/mm/yyyy to mysqldate if $func = 1 AND mysql date to dd/mm/yyyy
function dateconvert($date,$func)
{
	if ($func == 1)
	{
		 //insert conversion
		$d = explode('/', $date);
		$year = $d[2];
		$month = $d[1];
		$day = $d[0];
		$date = "$year-$month-$day";
		return $date;
	}
	if ($func == 2)
	{
		 //output conversion
		$d = explode('-', $date);
		$year = $d[0];
		$month = $d[1];
		$day = $d[2];
		$date = "$day/$month/$year";
		return $date;
	}
} 
?>
