<?php #  login.php
/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL'))
	require_once('../../page_not_direct_allow.php');
?>

<?php
$url = BASE_URL . 'index.php';
if(!isset($_SESSION[SESS.'user']))
{
	header ("Location: $url");
	exit();
}
else
{
	// storing user logout time and system in status in database starts here
	$q = "UPDATE dealer_person_login SET lastlogout  = NOW(), ipaddress = '{$_SERVER['REMOTE_ADDR']}' WHERE dpId = '".$_SESSION[SESS.'data']['id']."' LIMIT 1";
	$r = mysqli_query($dbc,$q);
	// storing user logout time and system in status in database ends here
			
	//echo'<span class="warn">You have successfully Logged out, <strong>'.$_SESSION['data']['name'].'</strong></span>';
	if($local)
	{
		foreach($_SESSION as $key => $value)
		{
			$pos = substr_count($key,SESS);
			if($pos == 1)
				unset($_SESSION[$key]);
		}
	}
	else
	{
		$_SESSION = array();
		session_destroy();
		setcookie('PHPHSESSID','',time()-3600,'/','',0,0);
	}
	//setcookie('first_name',$data['first_name'],time()-3600,'/','',0,0);
	header ("Location: $url");
	exit();
}
?>