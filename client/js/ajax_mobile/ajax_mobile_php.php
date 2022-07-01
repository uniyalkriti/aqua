<?php
@session_start();
ob_start();
require_once('../../include/config.inc.php');
require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-functions.php');
// This function will prepare the ajax response text which will be send to ajax call ends here
if(isset($_SESSION[SESS.'user']))
{
	//if at some instance we are making a post request
	if(isset($_POST['wcase'])){$_GET['pid'] = $_POST['pid']; $_GET['wcase'] = $_POST['wcase'];}
	if(isset($_GET['pid']) && !empty($_GET['pid']))
	{		
		$id = $_GET['pid'];
		$wcase = $_GET['wcase'];
		switch($wcase)
		{
			case'item-stock-receive': // from the stock-receive pageequiment-master
			{
				//$item = new item();
				list($opt,$row) = run_query($dbc,"SELECT item_name, itemId FROM item INNER JOIN units USING(utId) WHERE CONCAT_WS(' ',item_name, utname) = '$id' LIMIT 1", 'single');
				if($opt){
					//foreach($item_stat as $key=>$value) $id = $key;
					echo 'TRUE<$>'.$row['itemId'].'<$>'.$row['item_name'];					
				}else
					echo'FALSE<$>No item found';
				break;
			}
			case'equiment-master': // from the stock-receive page
			{
				//$item = new item();
				list($opt,$row) = run_query($dbc,$q="SELECT *, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date FROM master_equipment WHERE certno = '$id' LIMIT 1", 'single');
				if($opt){
					//foreach($item_stat as $key=>$value) $id = $key;
					echo 'TRUE<$>'.$row['tmeId'].'<$>'.$row['eqpname'].'<$>'.$row['eqpmake'].'<$>'.$row['certno'].'<$>'.$row['due_date'].'<$>'.$row['traceability'];					
				}else
					echo'FALSE<$>No master equipment found ';
				break;
			}
			case'item-billing': // from the stock-receive page
			{
				$item = new item();
				$order = new order();
				$item_stat = $item->get_item_list("itemcode = '$id'", $records='', $orderby='');
				if(!empty($item_stat)){
					foreach($item_stat as $key=>$value) $id = $key;
					$row = $item_stat[$id];
					echo 'TRUE<$>'.$row['itemId'].'<$>'.(int)$row['purchase_price'].'<$>'.$row['itemcode'].'<$>'.$row['itemname'].'<$>'.$row['brandId_txt'].'<$>'.$row['icId_txt'].'<$>'.$order->get_average_sale_price($row['itemId']).'<$>'.$order->price_display_div($row['itemId']);
					
				}else
					echo'FALSE<$>No item found';
				break;
			}
			case'tax_value': // from the spa load enquiry
			{
				$q = "SELECT * FROM tax WHERE taxId = '$id' LIMIT 1";
				$r = mysqli_query($dbc, $q);
				if($r)
				{
					if(mysqli_num_rows($r)>0)
					{
						echo'TRUE<$>';
						$row = mysqli_fetch_assoc($r);
						//echo $row['price'];
						//echo $row['etc'];
						echo $row['taxvalue'];
	
					}
					else
						echo'FALSE<$>Sorry no record found';
				}
				break;
			}	
			################################ getPendingBillNo ENDS ################################
		}
	}
	
	else
	{
		echo'FALSE<$>Please select a value';
	}
}
else
	echo'FALSE<$$>Sorry please login to complete the deletion request';
$output = ob_get_clean();
echo $output = trim($output);	
?>