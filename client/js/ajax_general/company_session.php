<?php
@session_start();
ob_start();
require_once('../../include/config.inc.php');
require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-functions.php');
require_once(DB);
// This function will prepare the ajax response text which will be send to ajax call ends here

if(isset($_GET['pid']))
{
	$id = $_GET['pid'];
        //unset($_SESSION[SESS.'data']['company_id']);
	$_SESSION[SESS.'data']['company_id'] = $id;
        $q = "SELECT * FROM _constant WHERE company_id = '{$_SESSION[SESS.'data']['company_id']}'";
        list($opt,$rs) = run_query($dbc, $q, 'single');
        if($opt) $_SESSION[SESS.'constant'] = $rs;
        echo $id;
	
}
$output = ob_get_clean();
echo $output = trim($output);	
?>