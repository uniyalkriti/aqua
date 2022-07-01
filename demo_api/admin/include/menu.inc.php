<?php
if (!defined('BASE_URL')) die('Script direct access not allowed');
//Based on the user logged in we will change the top menu
$logurole = isset($_SESSION[SESS.'data']['role_group_id']) ? $_SESSION[SESS.'data']['role_group_id']:0;
//h1($logurole);
switch($logurole)
{
	case'11'://if admin loggs in
		require_once('menu-by-role/admin-menu.inc.php');
		break;
	
	default://if admin loggs in
		require_once('menu-by-role/dealer-menu.inc.php');
		break;
}
?>
