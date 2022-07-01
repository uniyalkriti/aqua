<?php
//This function will convert the date from javascript calender to a format that can be used with mysql query
function format_date_time($date)
{
	//15-Oct-2010 06:55:54 PM
	$ndate0 = explode(' ',$date);
	$months = array('JAN'=>'01', 'FEB'=>'02', 'MAR'=>'03', 'APR'=>'04', 'MAY'=>'05', 'JUN'=>'06', 'JUL'=>'07', 'AUG'=>'08', 'SEP'=>'09', 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
	//echo '<h1>'.$ndate[1].'</h1>';
	$ndate = explode('-',$ndate0[0]); // seperating the date field which is in format 15-Oct-2010
	$ndate[1] = $months[strtoupper($ndate[1])]; // changing the month to uppercase to be able to match the month array
	$time_12_hour = $ndate0[1].' '.$ndate0[2]; // concatenating the $ndate0 array to get 12hr time in format 06:55:54 PM
	$time_24_hour = date("H:i:s", strtotime("$time_12_hour")); // converting the time to 24 hours format
	$rdate = $ndate[2].'-'.$ndate[1].'-'.$ndate[0].' '.$time_24_hour; // got the time which can be used in mysql query
	return ($rdate);
}
//The date conversion function ends here
?>