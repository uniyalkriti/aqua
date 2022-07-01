<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
if(!isset($setlabel)) $setlabel = $p;
echo breadcumMenu(array('setlabel'=>$setlabel, 'order-details'=>'Order Details','mtp'=>'Monthly Tour Plan'));  
?>
