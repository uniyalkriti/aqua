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
	$id = $_GET['deleteId']; // fetching the id to be deleted
	switch($pageId)// this switch case will decide from which page ajax request is being made 
	{
		case 'Dealer Ownership Delete':
		{
			$wdelete = 'Dealer Ownership.'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_dealer_ownership_type',$id,$field_name='id',$cls_fun_str='dealer_ownership',$checking_array=array('dealer'=>'ownership_type_id'),$filter='',$wdelete,$wfalse=FALSE,$obj_create='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Retailer Market Gift':
		{
			$wdelete = 'Retailer Market Gift.'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_retailer_mkt_gift',$id,$field_name='id',$cls_fun_str='retailer_market',$checking_array=array('user_retailer_gift_details'=>'gift_id'),$filter='',$wdelete,$wfalse=FALSE,$obj_create='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Outlet Type Delete':
		{
			$wdelete = 'Outlet Type.'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_retailer_outlet_type',$id,$field_name='id',$cls_fun_str='outlet_type',$checking_array=array('retailer'=>'outlet_type_id'),$filter='',$wdelete,$wfalse=FALSE,$obj_create='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Dealer Delete':
		{
			$wdelete = 'Dealer'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='dealer',$id,$field_name='id',$cls_fun_str='dealer',$checking_array=array('retailer'=>'dealer_id','user_sales_order'=>'dealer_id','monthly_tour_program'=>'dealer_id'),$filter='',$wdelete,$wfalse=TRUE,$obj_create='dealer');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Retailer Delete':
		{
			$wdelete = 'Retailer'; // what we are deleting
			$myobj = new retailer();
			$mystat = $myobj->retailer_delete($id, $filter='', $records='', $orderby='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
               
                case 'Working Status Delete':
		{
			$wdelete = 'Working Status Type.'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_working_status',$id,$field_name='id',$cls_fun_str='working_status',$checking_array=array('monthly_tour_program'=>'working_status_id'),$filter='',$wdelete,$wfalse=TRUE,$obj_create='dealer');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Travelling Mode Delete':
		{
			$wdelete = 'Working Status Type.'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_travelling_mode',$id,$field_name='id',$cls_fun_str='travelling_mode',$checking_array=array('user_expense_report'=>'travelling_mode_id'),$filter='',$wdelete,$wfalse=FALSE, $obj_create='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                 case 'Field Experiance Delete':
		{
			$wdelete = 'Field Experiance'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='_field_experience',$id,$field_name='id',$cls_fun_str='field_experience',$checking_array=array(),$filter='',$wdelete,$wfalse=FALSE, $obj_create='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Catalog Product Delete':
		{
			$wdelete = 'Catalog Product'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='catalog_product',$id,$field_name='id',$cls_fun_str='catalog_product',$checking_array=array('user_sales_order_details'=>'product_id'),$filter='',$wdelete,$wfalse = TRUE, $obj_create='catalog_product');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Company Delete':
		{
			$wdelete = 'Company'; // what we are deleting
			$myobj = new settings();
			$mystat = $myobj->delete_common_table_data($table='company',$id,$field_name='company.id',$cls_fun_str='company',$checking_array=array(),$filter='',$wdelete,$wfalse = TRUE, $obj_create='company');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                 case 'Scheme Delete':
		{
			$wdelete = 'Scheme.'; // what we are deleting
			$myobj = new user();
			$mystat = $myobj->scheme_delete($id, $filter='', $records='', $orderby='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
               
		
                case 'Person Delete':
		{
			$wdelete = 'Person.'; // what we are deleting
			$myobj = new user();
			$mystat = $myobj->user_delete($id, $filter='', $records='', $orderby='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                case 'Challan Delete':
		{
			$wdelete = 'Challan.'; // what we are deleting
			$myobj = new dealer_sale();
			$mystat = $myobj->challan_delete($id, $filter='', $records='', $orderby='');
			if($mystat['status'])
				echo'TRUE<$$>'.$mystat['myreason'];
			else
				echo'FALSE<$$>'.$mystat['myreason'];
			break;
		}
                
                  case 'Location Delete':
		{
                        $wdelete = 'Location Delete.'; // what we are deleting
			$cobj = new location();
			$action = $cobj->location_delete($id,$filter='',$records='',$orderby='');		
                      
			ob_clean(); // to get rid off any excess white space			
			echo $msg = $action['status'] ? 'TRUE<$$>'.$action['myreason'] : 'FALSE<$$>'.$action['myreason'];
			break;
		}

		case 'Stock Delete':
		{
		$wdelete = 'Stock.'; // what we are deleting
		$myobj = new report();
		$mystat = $myobj->stock_delete($id, $filter='', $records='', $orderby='');
		if($mystat['status'])
		echo $mystat['myreason'];
		else
		echo $mystat['myreason'];
		break;
		}
		break;
		
		
		################################### Case Service Provider ends here#############################################
	}
}
else
	echo'FALSE<$$>Sorry please login to complete the deletion request';
$output = ob_get_clean();
echo $output = trim($output);	
?>