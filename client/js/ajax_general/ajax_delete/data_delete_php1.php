<?php
// this code will handle the deletion request from all the pages, which will be tracked by a swithch case
@session_start();
ob_start();
require_once('../../include/config.inc.php');
// Including of the variable to make the correct value for BASE_URI AND bASE_URL in config file ends here
require_once(BASE_URI.'include'.MSYM.'my-functions.php');
/*function __autoload($class)
{
	require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/classes/'.strtolower($class) .'.php');
}*/
if(isset($_SESSION[SESS.'user']))
{
	require_once(DB); // Database inclusion	
	$pageId = $_GET['pageId']; // fetching the details of the page from which search is being performed
        $level_type = $_GET['levelType'];
	$id = $_GET['deleteId']; // fetching the id to be deleted
        $id = $id.'<$>'.$level_type;
	switch($pageId)// this switch case will decide from which page ajax request is being made 
	{
		
                case 'Location Delete':
		{
                        $wdelete = 'Location Delete.'; // what we are deleting
			$cobj = new location();
			$action = $cobj->location_delete($id,$filter='',$records='',$orderby='');		
                      
			ob_clean(); // to get rid off any excess white space			
			echo $msg = $action['status'] ? 'TRUE<$$>'.$action['myreason'] : 'FALSE<$$>'.$action['myreason'];
			break;
		}
                case 'Category Delete':
		{
                        $wdelete = 'Category Delete.'; // what we are deleting
			$cobj = new catalog();
			$action = $cobj->category_delete($id,$filter='',$records='',$orderby='');                   
			ob_clean(); // to get rid off any excess white space
                        
			echo $msg = $action['status'] ? 'TRUE<$$>'.$action['myreason'] : 'FALSE<$$>'.$action['myreason'];
			break;
		}
                case 'Challan Item Delete':
		{
                        $wdelete = 'Challan Item Delete.'; // what we are deleting
			$cobj = new dealer_sale();
			$action = $cobj->dealer_sale_delete($id,$filter='',$records='',$orderby='');                   
			ob_clean(); // to get rid off any excess white space
                        
			echo $msg = $action['status'] ? 'TRUE<$$>'.$action['myreason'] : 'FALSE<$$>'.$action['myreason'];
			break;
		}
                //working status delete
                
	}
}
else
	echo'FALSE<$$>Sorry please login to complete the deletion request';
$output = ob_get_clean();
echo $output = trim($output);	
?>