<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
if(!isset($setlabel)) $setlabel = $p;
$loclevel = $_SESSION[SESS.'constant']['location_level'];
if(isset($mtype) && !empty($mtype)) $p = "$p&mtype=$mtype";
$str = array();
$str['setlabel']= $p;
$first_title = $_SESSION[SESS.'constant']['location_title_1'];
$j = 1;
for($i=1; $i<=$loclevel; $i++)
{
    if($i == 1) {$str["location&mtype=$i"] = "$first_title"; continue; }
    $title = $_SESSION[SESS.'constant']["location_title_$i"];
    $str["location-category&mtype=$i"] =  $title;
   
   
}
//pre($str);
echo breadcumMenu($str);  
?>