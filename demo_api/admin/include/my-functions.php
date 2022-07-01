<?php
// echo BASE_URI_A,die();
require_once(DB);
require_once(BASE_URI_A.'functions/db_common_function.php');
require_once(BASE_URI_A.'functions/fileupload.php');
require_once(BASE_URI_A.'functions/common_function.php');
require_once(BASE_URI_A.'functions/mobile.php');
require_once(BASE_URI_A.'functions/date_time.php');
require_once(BASE_URI_A.'include/settings.php');
// require_once(BASE_URI_A.'printouts-format/indian_currency_format1.php');
// require_once(BASE_URI_A.'printouts-format/indian_currency_format.php');
//Calling the autoload functions
function __autoload($class)
{	
	require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/classes/'.strtolower($class) .'.php');
}
?>