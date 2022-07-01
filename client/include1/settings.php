<?php
/*	This function will return the base directory path to which uploading file folder will be created starts here
 	get_upload_basepath($num, $path) accepts two paramater
	1. $num = Number of level to go upward in the directory for ex: C:/wamp/www/ with value 2 mean go to C:\ directory
	2. $path = The path which will be traversed based on $num value
*/
function get_upload_basepaths($num, $path)
{
	while($num != 0)
	{
		$path = dirname($path);
		$num = $num-1;
		//echo $num.' '.$path.'<br/>';
	}
	return $path;
}
//This function will return the base directory path to which uploading file folder will be created ends here


//    For localhost environment
if(stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168') || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'local:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080')
{
	define('SYM','\\'); //use to hold the special symbol for dirctory navigation in windows
	// dirname() will remove the filename and give the parent directory of the script which can then be sent to func for traversing
	// instead of 2 we have used 1 as first parameter because this will be called in index.php which is just above one level not 2
	$ini_path = get_upload_basepaths(0, dirname($_SERVER['SCRIPT_FILENAME']));
	// str_replace() will replace '/' with '\' that is used in the windowns environment for file manipulation
	$ini_path = str_replace('/',SYM, $ini_path); //
	$ini_path .= SYM;
	//define('NEWS_DOWNLOAD_FOLDER','C:\\wamp\\www\\My Dropbox\\cradleindia\\news_downloads'); // to hold news section downloads
}
else
{
	define('SYM','/'); //use to hold the special symbol for directory navigation in linux
	// instead of 2 we have used 1 as first parameter because this will be called in index.php which is just above one level not 2
	$ini_path = get_upload_basepaths(0, dirname($_SERVER['SCRIPT_FILENAME']));
	$ini_path .= SYM;
	//define('NEWS_DOWNLOAD_FOLDER','/home/cradlein/public_html/news_downloads'); // // to hold news section downloads
}

//defining the constants to hold the fileuploading paths starts here
define('ITEM_IMAGES',$ini_path.'item-images'); // // to hold the uploaded images of items
//echo ITEM_IMAGES;
?>