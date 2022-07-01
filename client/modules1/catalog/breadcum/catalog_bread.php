<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
if(!isset($setlabel)) $setlabel = $p;
$v = $p;
$setlabel = $v."&mtype=$_GET[mtype]";
$catalog = $_SESSION[SESS.'constant']['catalog_level'];
$str = array();
$str['setlabel'] = $setlabel;
$j = 1;
for($i=1; $i<=$catalog; $i++)
{
    if($i >= 2) $j = 2;
    $title = $_SESSION[SESS.'constant']["catalog_title_$i"];
    $str["catalog_$j&mtype=$i"] =  $title;
    
}
//pre($str);
echo breadcumMenu($str);  
?>