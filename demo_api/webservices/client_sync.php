<?php
require_once('../client/functions/common_function.php');
require_once('../client/include/conectdb.php');
require_once('../client/include/config.inc.php');
require_once('../client/include/my-functions.php');

$dealer_id=$_GET['dealer_id'];

$retailer_info = array();
$final_retailer_details = array();

$query_retailer_access_on = "SELECT * FROM retailer WHERE dealer_id='$dealer_id' AND sync_status=1 GROUP BY id ORDER BY id ASC"; 
$run_retailer = mysqli_query($dbc, $query_retailer_access_on);
        if (mysqli_num_rows($run_retailer) > 0) {
            while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                
                $retailer_info['id'] = $retailer_fetch['id'];
                $retailer_info['name'] = $retailer_fetch['name'];
                $retailer_info['dealer_id'] = $retailer_fetch['dealer_id'];
                $retailer_info['location_id'] = $retailer_fetch['location_id'];
                $retailer_info['company_id'] = $retailer_fetch['company_id'];
                $retailer_info['address'] = $retailer_fetch['address'];
                $retailer_info['email'] = $retailer_fetch['email'];
                $retailer_info['contact_per_name'] = $retailer_fetch['contact_per_name'];
                $retailer_info['landline'] = $retailer_fetch['landline'];
                $retailer_info['other_numbers'] = $retailer_fetch['other_numbers'];
                $retailer_info['tin_no'] = $retailer_fetch['tin_no'];
                $retailer_info['pin_no'] = $retailer_fetch['pin_no'];
                $retailer_info['outlet_type_id'] = $retailer_fetch['outlet_type_id'];
                $retailer_info['created_by_person_id'] = $retailer_fetch['created_by_person_id'];
                $retailer_info['status'] = $retailer_fetch['status'];
                $retailer_info['sync_status'] = $retailer_fetch['sync_status'];
                $final_retailer_details[] = $retailer_info;
            }
        }
$query_dealer_access_on = "SELECT id,dealer_status FROM dealer WHERE id='$dealer_id' GROUP BY id"; 
$run_dealer = mysqli_query($dbc, $query_dealer_access_on);
        if (mysqli_num_rows($run_dealer) > 0) {
            while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                
                $dealer_info['dealer_status'] = $dealer_fetch['dealer_status'];
                $final_dealer_details = $dealer_info;
            }
        }        
        
       $essential = array("dealer_status" => $final_dealer_details,
                          "sale_order" => $final_sale_order,
                          "sale_order_details" => $final_sale_order_details,
                          "retailer" => $final_retailer_details);
      
       $final_array = array("response"=>$essential); 
      $data = json_encode($final_array);
echo $data;
?>

