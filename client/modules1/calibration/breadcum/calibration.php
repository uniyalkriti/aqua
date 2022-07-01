<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)){
	if(!isset($setlabel)) $setlabel = $p;
	echo breadcumMenu(array('setlabel'=>$setlabel, 'calibration-order'=>'Calibration Order', 'certificate'=>'Certificate'));
}
?>