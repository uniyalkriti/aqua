<?php
error_reporting(0);
require_once('../client/include/config.inc.php');
require_once '../client/include/conectdb.php';
require_once('functions.php');

$url =BASE_URL."client_sync.php?dealer_id=39";
$json= getHtml($url, $post);
$data= json_decode($json);
//echo '<pre>';
//print_r($json);
//echo '</pre>';
$dealer_status=$data->response->dealer_status->dealer_status;
if($dealer_status==1){
    
    $retailer=$data->response->retailer;
   
if(!empty($retailer)){
	$m=0;
	$retailer_count=count($retailer);
	while($m<$retailer_count){
		$retailer_id=$retailer[$m]->id;
                $retailer_cat=$retailer[$m]->category;
                $track_address=$retailer[$m]->track_add;
		
                $avg_pur=$retailer[$m]->r_avg_pur;
		$mobile=$retailer[$m]->r_contact_no;
		$location=$retailer[$m]->l_code;
		$mcc_mnc_lac_cellId=$retailer[$m]->mccmnclaccellid;
		
		$address=$retailer[$m]->r_address;
                $created_date=$retailer[$m]->cr_date;
                $long=$retailer[$m]->long;
                $lat=$retailer[$m]->lat;
                $tin=$retailer[$m]->r_tin;
                $email=$retailer[$m]->r_email;
                $created_time=$retailer[$m]->cr_time;
                $dealer=$retailer[$m]->d_code;
		$image=$retailer[$m]->image_name;
		$retailer_name=$retailer[$m]->r_name;
		$contact_person=$retailer[$m]->cont_name;
	        $lat_long=$retailer[$m]->lat.','.$retailer[$m]->long;
                $date_time=$retailer[$m]->cr_date.' '.$retailer[$m]->cr_time;
                $ll=explode(",",$lat_long);
                if($track_address == '$$'){
                $taddress =  getLocationByLatLng($ll[0],$ll[1]);
                 }  else {
                $taddress = $track_address;    
                }
                
                $retailer_store="CALL retailer_procedure('$retailer_id','$retailer_name','$image','$dealer','$location','$address','$email','$contact_person','$mobile','$lat_long','$mcc_mnc_lac_cellId','$taddress','$pin','$tin','$avg_pur','$retailer_cat','$user_id','$date_time')";
              $retailer_store_run=mysqli_query($dbc, $retailer_store);
              $dealer_retailer="INSERT INTO `dealer_retailer` (`dealer_id`,`retailer_id`) VALUES ('$dealer','$retailer_id')";
	      $dealer_retailer_run=mysqli_query($dbc, $dealer_retailer);
              $m++;
	}
}
}else{
    
}



function getHtml($url, $post = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if(!empty($post)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    } 
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>