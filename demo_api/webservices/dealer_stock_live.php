<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;

if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id']))); else $dealer_id = 0;
$q1 = "SELECT dealer_status from dealer where id = '$dealer_id'";
$q_res = mysqli_query($dbc, $q1)OR die(mysqli_error($dbc)) ;
$q_row = mysqli_fetch_assoc($q_res);
$dealer_status = $q_row['dealer_status'];
if($dealer_status==1){
$final_dealer_stock = array();

//**************************************** _survey_questions  ******************************************************
$qstt = "SELECT `id`,`product_id`, `rate` as retailer_rate, `dealer_rate`, `mrp`,`dealer_id`, `qty`,`mfg`, `expire`, `date` FROM `stock` WHERE dealer_id='$dealer_id' ORDER BY `product_id`,`mrp` ASC";
	//	h1($qstt);
                $srtt = mysqli_query($dbc, $qstt);
                        
                while($rowtt = mysqli_fetch_assoc($srtt))
                {
                   $final_dealer_stock[]=$rowtt;
                }
                

    
if(empty($final_dealer_stock))
{
$stock_data[] = array("response"=>"FALSE"); 
}else
    {
$stock_data[]=array("response"=>"TRUE"
        ,"dealer_stock"=>$final_dealer_stock);
}
$final_array = array("result"=>$stock_data);

$data = json_encode($final_array);

echo $data;
}else{
    echo"N";
}
?>          

