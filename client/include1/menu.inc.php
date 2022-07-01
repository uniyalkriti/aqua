<?php
if (!defined('BASE_URL')) die('Script direct access not allowed');
//Based on the user logged in we will change the top menu
$logurole = 2;//isset($_SESSION[SESS.'data']['urole']) ? $_SESSION[SESS.'data']['urole']:0;
switch($logurole)
{
	case'1'://if admin loggs in
		require_once('menu-by-role/admin-menu.inc.php');
		break;
	case'2'://if admin loggs in
		require_once('menu-by-role/menu-'.$logurole.'.inc.php');
		break;
}
?>