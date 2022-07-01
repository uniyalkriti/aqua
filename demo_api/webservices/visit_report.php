<?php

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

if(isset($_GET['user_id'])) $user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id']))); 
else $user_id = 0;
if (isset($_GET['from_date']))
    $from_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
else
   $from_date = 0;
if (isset($_GET['to_date']))
    $to_date = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
else
   $to_date = 0;

  $visit_data_query = "SELECT `daily_visit`.`*`,`dealer`.`name` as dealer_name from `daily_visit` INNER JOIN `dealer` on `dealer`.`id` = `daily_visit`.`dealer_id` where  (DATE_FORMAT(daily_visit.date,'%Y-%m-%d')>='$from_date' and DATE_FORMAT(daily_visit.date,'%Y-%m-%d')<='$to_date')"."and user_id='$user_id' GROUP BY time";
    list($opt, $rs) = run_query($dbc, $visit_data_query, $mode = 'multi', $msg = '');
    $out = [];
    $exp = [];
    while ($row = mysqli_fetch_assoc($rs)) 
    {
    	$out[] = $row; 
    }

	if(empty($out))
	{
		$exp[] = array("response"=>"FALSE");
	}
	else
	{
		$exp=array("response"=>"TRUE","data"=>$out);
	}



$data = json_encode($exp);

echo $data;
